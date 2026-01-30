<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Pdv_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Buscar produtos para PDV (com estoque e imagem)
     */
    public function buscarProdutos($termo = '', $limit = 50)
    {
        $this->db->select('produtos.*');
        $this->db->from('produtos');
        
        // Verificar se existe campo categorias_id antes de fazer join
        $fields = $this->db->list_fields('produtos');
        if (in_array('categorias_id', $fields)) {
            $this->db->select('categorias.nome as categoria_nome');
            $this->db->join('categorias', 'categorias.idCategorias = produtos.categorias_id', 'left');
        }
        
        if (!empty($termo)) {
            $this->db->group_start();
            $this->db->like('produtos.descricao', $termo);
            // Verificar se campo nome existe antes de usar
            $fields = $this->db->list_fields('produtos');
            if (in_array('nome', $fields)) {
                $this->db->or_like('produtos.nome', $termo);
            }
            $this->db->or_like('produtos.codDeBarra', $termo);
            $this->db->group_end();
        }
        
        $this->db->order_by('produtos.descricao', 'ASC');
        $this->db->limit($limit);
        
        return $this->db->get()->result();
    }

    /**
     * Buscar produto por código de barras
     */
    public function buscarProdutoPorCodigoBarras($codigo)
    {
        $this->db->select('produtos.*');
        $this->db->from('produtos');
        
        // Verificar se existe campo categorias_id antes de fazer join
        $fields = $this->db->list_fields('produtos');
        if (in_array('categorias_id', $fields)) {
            $this->db->select('categorias.nome as categoria_nome');
            $this->db->join('categorias', 'categorias.idCategorias = produtos.categorias_id', 'left');
        }
        
        $this->db->where('produtos.codDeBarra', $codigo);
        $this->db->limit(1);
        
        return $this->db->get()->row();
    }

    /**
     * Obter cliente "Consumidor Final"
     */
    public function getClienteConsumidorFinal()
    {
        $this->db->where('nomeCliente', 'Consumidor Final');
        $this->db->or_where('documento', '00000000000');
        $cliente = $this->db->get('clientes')->row();
        
        if (!$cliente) {
            // Criar cliente consumidor final se não existir
            $data = [
                'nomeCliente' => 'Consumidor Final',
                'documento' => '00000000000',
                'telefone' => '',
                'celular' => '',
                'email' => '',
                'rua' => '',
                'numero' => '',
                'bairro' => '',
                'cidade' => '',
                'estado' => '',
                'cep' => '',
                'dataCadastro' => date('Y-m-d')
            ];
            $this->db->insert('clientes', $data);
            $cliente = $this->db->get_where('clientes', ['idClientes' => $this->db->insert_id()])->row();
        }
        
        return $cliente;
    }

    /**
     * Criar venda rápida no PDV
     */
    public function criarVendaRapida($dados)
    {
        $this->db->trans_start();
        
        // Criar venda
        $venda = [
            'dataVenda' => date('Y-m-d'),
            'valorTotal' => 0,
            'desconto' => 0,
            'valor_desconto' => 0,
            'tipo_desconto' => null,
            'faturado' => 0,
            'observacoes' => isset($dados['observacoes']) ? $dados['observacoes'] : null,
            'observacoes_cliente' => null,
            'clientes_id' => $dados['clientes_id'],
            'usuarios_id' => $dados['usuarios_id'],
            'status' => 'Aberto',
            'garantia' => null,
            'tipo_venda' => 'avista',
            'turnos_caixa_id' => isset($dados['turnos_caixa_id']) ? $dados['turnos_caixa_id'] : null,
            'caixas_id' => isset($dados['caixas_id']) ? $dados['caixas_id'] : null
        ];
        
        $this->db->insert('vendas', $venda);
        $vendaId = $this->db->insert_id();
        
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === false) {
            return false;
        }
        
        return $vendaId;
    }

    /**
     * Finalizar venda do PDV (faturar e registrar pagamentos)
     */
    public function finalizarVendaPDV($vendaId, $pagamentos, $desconto = 0, $tipoDesconto = null)
    {
        $this->db->trans_start();
        
        // Calcular total dos produtos
        $this->db->select_sum('subTotal');
        $this->db->where('vendas_id', $vendaId);
        $totalProdutos = $this->db->get('itens_de_vendas')->row()->subTotal ?: 0;
        
        // Aplicar desconto
        $valorDesconto = 0;
        if ($desconto > 0) {
            if ($tipoDesconto == 'percentual') {
                $valorDesconto = $totalProdutos * ($desconto / 100);
            } else {
                $valorDesconto = $desconto;
            }
        }
        
        $valorTotal = $totalProdutos - $valorDesconto;
        $valorTotalPagamentos = array_sum(array_column($pagamentos, 'valor'));
        
        // Verificar se o valor dos pagamentos confere
        if (abs($valorTotalPagamentos - $valorTotal) > 0.01) {
            $this->db->trans_rollback();
            return ['success' => false, 'message' => 'Valor dos pagamentos não confere com o total da venda.'];
        }
        
        // Atualizar venda
        $this->db->where('idVendas', $vendaId);
        $this->db->update('vendas', [
            'valorTotal' => $totalProdutos,
            'desconto' => $desconto,
            'valor_desconto' => $valorTotal,
            'tipo_desconto' => $tipoDesconto,
            'faturado' => 1,
            'status' => 'Faturado'
        ]);
        
        // Registrar pagamentos
        foreach ($pagamentos as $pagamento) {
            $this->db->insert('pagamentos_venda', [
                'vendas_id' => $vendaId,
                'formas_pagamento_id' => $pagamento['forma_pagamento_id'],
                'valor' => $pagamento['valor'],
                'troco' => isset($pagamento['troco']) ? $pagamento['troco'] : 0,
                'parcelas' => isset($pagamento['parcelas']) ? $pagamento['parcelas'] : 1,
                'data_pagamento' => date('Y-m-d H:i:s')
            ]);
        }
        
        // Criar lançamento financeiro
        $this->db->insert('lancamentos', [
            'vendas_id' => $vendaId,
            'descricao' => 'Venda PDV #' . $vendaId,
            'valor' => $valorTotal,
            'desconto' => $desconto,
            'tipo_desconto' => $tipoDesconto ?: 'real',
            'valor_desconto' => $valorTotal,
            'clientes_id' => $this->db->get_where('vendas', ['idVendas' => $vendaId])->row()->clientes_id,
            'data_vencimento' => date('Y-m-d'),
            'data_pagamento' => date('Y-m-d'),
            'baixado' => 1,
            'forma_pgto' => implode(', ', array_column($pagamentos, 'forma_nome')),
            'tipo' => 'receita',
            'usuarios_id' => $this->session->userdata('id_admin')
        ]);
        
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === false) {
            return ['success' => false, 'message' => 'Erro ao finalizar venda.'];
        }
        
        return ['success' => true, 'venda_id' => $vendaId];
    }

    /**
     * Verificar se há caixa aberto para o usuário
     */
    public function getCaixaAberto($usuarioId, $caixaId = null)
    {
        // Verificar se as tabelas existem
        if (!$this->db->table_exists('turnos_caixa') || !$this->db->table_exists('caixas')) {
            return null;
        }
        
        $this->db->select('turnos_caixa.*, caixas.nome as caixa_nome, usuarios.nome as usuario_nome');
        $this->db->from('turnos_caixa');
        $this->db->join('caixas', 'caixas.idCaixa = turnos_caixa.caixas_id', 'left');
        $this->db->join('usuarios', 'usuarios.idUsuarios = turnos_caixa.usuarios_id', 'left');
        $this->db->where('turnos_caixa.usuarios_id', $usuarioId);
        $this->db->where('turnos_caixa.status', 'aberto');
        
        if ($caixaId) {
            $this->db->where('turnos_caixa.caixas_id', $caixaId);
        }
        
        $this->db->order_by('turnos_caixa.data_abertura', 'DESC');
        $this->db->limit(1);
        
        $query = $this->db->get();
        
        if ($query === false) {
            return null;
        }
        
        return $query->row();
    }

    /**
     * Abrir caixa
     */
    public function abrirCaixa($caixaId, $usuarioId, $valorAbertura)
    {
        // Verificar se as tabelas existem
        if (!$this->db->table_exists('turnos_caixa') || !$this->db->table_exists('caixas')) {
            return ['success' => false, 'message' => 'Tabelas do PDV não foram criadas. Execute as migrations primeiro.'];
        }
        
        // Verificar se já existe caixa aberto
        $caixaAberto = $this->getCaixaAberto($usuarioId, $caixaId);
        if ($caixaAberto) {
            return ['success' => false, 'message' => 'Já existe um caixa aberto para este usuário.'];
        }
        
        $data = [
            'caixas_id' => $caixaId,
            'usuarios_id' => $usuarioId,
            'data_abertura' => date('Y-m-d H:i:s'),
            'valor_abertura' => $valorAbertura,
            'status' => 'aberto'
        ];
        
        $this->db->insert('turnos_caixa', $data);
        $turnoId = $this->db->insert_id();
        
        if ($turnoId) {
            return ['success' => true, 'turno_id' => $turnoId];
        }
        
        return ['success' => false, 'message' => 'Erro ao abrir caixa.'];
    }

    /**
     * Fechar caixa
     */
    public function fecharCaixa($turnoId, $valorFechamento, $observacoes = null)
    {
        $turno = $this->db->get_where('turnos_caixa', ['idTurno' => $turnoId])->row();
        
        if (!$turno || $turno->status != 'aberto') {
            return ['success' => false, 'message' => 'Caixa não encontrado ou já fechado.'];
        }
        
        // Calcular valor esperado (abertura + vendas - saídas)
        $this->db->select_sum('valor_desconto');
        $this->db->where('turnos_caixa_id', $turnoId);
        $this->db->where('faturado', 1);
        $totalVendas = $this->db->get('vendas')->row()->valor_desconto ?: 0;
        
        $valorEsperado = $turno->valor_abertura + $totalVendas;
        $diferenca = $valorFechamento - $valorEsperado;
        
        $this->db->where('idTurno', $turnoId);
        $this->db->update('turnos_caixa', [
            'data_fechamento' => date('Y-m-d H:i:s'),
            'valor_fechamento' => $valorFechamento,
            'valor_esperado' => $valorEsperado,
            'diferenca' => $diferenca,
            'observacoes' => $observacoes,
            'status' => 'fechado'
        ]);
        
        return ['success' => true, 'diferenca' => $diferenca];
    }

    /**
     * Obter estatísticas do turno
     */
    public function getEstatisticasTurno($turnoId)
    {
        // Vendas do turno
        $this->db->select('COUNT(*) as total_vendas, SUM(valor_desconto) as total_vendido');
        $this->db->from('vendas');
        $this->db->where('turnos_caixa_id', $turnoId);
        $this->db->where('faturado', 1);
        $vendas = $this->db->get()->row();
        
        // Pagamentos por forma
        $this->db->select('formas_pagamento.nome, SUM(pagamentos_venda.valor) as total');
        $this->db->from('pagamentos_venda');
        $this->db->join('vendas', 'vendas.idVendas = pagamentos_venda.vendas_id');
        $this->db->join('formas_pagamento', 'formas_pagamento.idFormaPagamento = pagamentos_venda.formas_pagamento_id');
        $this->db->where('vendas.turnos_caixa_id', $turnoId);
        $this->db->group_by('formas_pagamento.idFormaPagamento');
        $pagamentos = $this->db->get()->result();
        
        return [
            'vendas' => $vendas,
            'pagamentos' => $pagamentos
        ];
    }

    /**
     * Obter vendas do dia (para dashboard)
     */
    public function getVendasDia($data = null)
    {
        if ($data === null) {
            $data = date('Y-m-d');
        }
        
        $this->db->select('COUNT(*) as total_vendas, SUM(valor_desconto) as total_vendido, AVG(valor_desconto) as ticket_medio');
        $this->db->from('vendas');
        $this->db->where('dataVenda', $data);
        $this->db->where('faturado', 1);
        
        return $this->db->get()->row();
    }

    /**
     * Obter produtos mais vendidos
     */
    public function getProdutosMaisVendidos($data = null, $limit = 10)
    {
        if ($data === null) {
            $data = date('Y-m-d');
        }
        
        // Verificar se campo nome existe
        $fields = $this->db->list_fields('produtos');
        $nomeField = in_array('nome', $fields) ? 'COALESCE(produtos.nome, produtos.descricao) as descricao' : 'produtos.descricao';
        $this->db->select($nomeField . ', SUM(itens_de_vendas.quantidade) as quantidade_vendida, SUM(itens_de_vendas.subTotal) as total_vendido');
        $this->db->from('itens_de_vendas');
        $this->db->join('vendas', 'vendas.idVendas = itens_de_vendas.vendas_id');
        $this->db->join('produtos', 'produtos.idProdutos = itens_de_vendas.produtos_id');
        $this->db->where('vendas.dataVenda', $data);
        $this->db->where('vendas.faturado', 1);
        $this->db->group_by('produtos.idProdutos');
        $this->db->order_by('quantidade_vendida', 'DESC');
        $this->db->limit($limit);
        
        return $this->db->get()->result();
    }

    /**
     * Cancelar venda do PDV
     */
    public function cancelarVenda($vendaId, $usuarioId, $motivo, $estornarEstoque = true)
    {
        $venda = $this->db->get_where('vendas', ['idVendas' => $vendaId])->row();
        
        if (!$venda) {
            return ['success' => false, 'message' => 'Venda não encontrada.'];
        }
        
        if ($venda->faturado == 1) {
            return ['success' => false, 'message' => 'Não é possível cancelar venda já faturada.'];
        }
        
        $this->db->trans_start();
        
        // Registrar cancelamento
        $this->db->insert('cancelamentos_venda', [
            'vendas_id' => $vendaId,
            'usuarios_id' => $usuarioId,
            'motivo' => $motivo,
            'estornar_estoque' => $estornarEstoque ? 1 : 0
        ]);
        
        // Estornar estoque se necessário
        if ($estornarEstoque) {
            $itens = $this->db->get_where('itens_de_vendas', ['vendas_id' => $vendaId])->result();
            $this->load->model('produtos_model');
            
            foreach ($itens as $item) {
                $this->produtos_model->updateEstoque($item->produtos_id, $item->quantidade, '+');
            }
        }
        
        // Deletar itens da venda
        $this->db->delete('itens_de_vendas', ['vendas_id' => $vendaId]);
        
        // Deletar venda
        $this->db->delete('vendas', ['idVendas' => $vendaId]);
        
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === false) {
            return ['success' => false, 'message' => 'Erro ao cancelar venda.'];
        }
        
        return ['success' => true];
    }
}
