<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class PDV extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('pdv_model');
        $this->load->model('vendas_model');
        $this->load->model('produtos_model');
        $this->load->model('clientes_model');
        $this->load->helper('form');
        $this->data['menuVendas'] = 'Vendas';
    }

    /**
     * Tela principal do PDV
     */
    public function index()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vVenda')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para acessar o PDV.');
            redirect(base_url());
        }

        // Verificar se há caixa aberto
        $caixaAberto = $this->pdv_model->getCaixaAberto($this->session->userdata('id_admin'));
        
        if (!$caixaAberto) {
            // Redirecionar para abertura de caixa
            $this->session->set_flashdata('warning', 'É necessário abrir um caixa antes de iniciar as vendas.');
            redirect('pdv/abrirCaixa');
        }

        // Buscar produtos ativos
        $this->data['produtos'] = $this->pdv_model->buscarProdutos('', 100);
        
        // Buscar formas de pagamento ativas
        $this->data['formas_pagamento'] = $this->db->where('ativo', 1)
            ->order_by('ordem', 'ASC')
            ->get('formas_pagamento')
            ->result();
        
        // Cliente consumidor final
        $this->data['cliente_consumidor_final'] = $this->pdv_model->getClienteConsumidorFinal();
        
        // Caixa atual
        $this->data['caixa_aberto'] = $caixaAberto;
        
        $this->data['view'] = 'pdv/index';
        return $this->layout();
    }

    /**
     * Buscar produtos (AJAX)
     */
    public function buscarProdutos()
    {
        $termo = $this->input->get('termo');
        $produtos = $this->pdv_model->buscarProdutos($termo, 50);
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['success' => true, 'produtos' => $produtos]));
    }

    /**
     * Buscar produto por código de barras (AJAX)
     */
    public function buscarProdutoCodigoBarras()
    {
        $codigo = $this->input->post('codigo');
        
        if (empty($codigo)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['success' => false, 'message' => 'Código não informado']));
            return;
        }
        
        $produto = $this->pdv_model->buscarProdutoPorCodigoBarras($codigo);
        
        if ($produto) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['success' => true, 'produto' => $produto]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['success' => false, 'message' => 'Produto não encontrado']));
        }
    }

    /**
     * Criar venda rápida (AJAX)
     */
    public function criarVenda()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aVenda')) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(403)
                ->set_output(json_encode(['success' => false, 'message' => 'Sem permissão']));
            return;
        }

        $caixaAberto = $this->pdv_model->getCaixaAberto($this->session->userdata('id_admin'));
        
        if (!$caixaAberto) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['success' => false, 'message' => 'Caixa não está aberto']));
            return;
        }

        $clienteId = $this->input->post('cliente_id') ?: $this->pdv_model->getClienteConsumidorFinal()->idClientes;
        
        $vendaId = $this->pdv_model->criarVendaRapida([
            'clientes_id' => $clienteId,
            'usuarios_id' => $this->session->userdata('id_admin'),
            'turnos_caixa_id' => $caixaAberto->idTurno,
            'caixas_id' => $caixaAberto->caixas_id
        ]);
        
        if ($vendaId) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['success' => true, 'venda_id' => $vendaId]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['success' => false, 'message' => 'Erro ao criar venda']));
        }
    }

    /**
     * Adicionar produto à venda (AJAX)
     */
    public function adicionarProduto()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eVenda')) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(403)
                ->set_output(json_encode(['success' => false, 'message' => 'Sem permissão']));
            return;
        }

        $vendaId = $this->input->post('venda_id');
        $produtoId = $this->input->post('produto_id');
        $quantidade = $this->input->post('quantidade') ?: 1;
        $preco = $this->input->post('preco');

        // Verificar estoque
        $produto = $this->produtos_model->getById($produtoId);
        if ($this->data['configuration']['control_estoque'] && $produto->estoque < $quantidade) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false, 
                    'message' => 'Estoque insuficiente. Disponível: ' . $produto->estoque
                ]));
            return;
        }

        $subtotal = $preco * $quantidade;

        $this->db->trans_start();

        // Adicionar item
        $this->db->insert('itens_de_vendas', [
            'vendas_id' => $vendaId,
            'produtos_id' => $produtoId,
            'quantidade' => $quantidade,
            'preco' => $preco,
            'subTotal' => $subtotal
        ]);

        // Atualizar estoque
        if ($this->data['configuration']['control_estoque']) {
            $this->produtos_model->updateEstoque($produtoId, $quantidade, '-');
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['success' => false, 'message' => 'Erro ao adicionar produto']));
            return;
        }

        // Buscar itens atualizados
        $itens = $this->vendas_model->getProdutos($vendaId);
        $total = array_sum(array_column($itens, 'subTotal'));

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => true,
                'itens' => $itens,
                'total' => $total
            ]));
    }

    /**
     * Remover produto da venda (AJAX)
     */
    public function removerProduto()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eVenda')) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(403)
                ->set_output(json_encode(['success' => false, 'message' => 'Sem permissão']));
            return;
        }

        $itemId = $this->input->post('item_id');
        
        $item = $this->db->get_where('itens_de_vendas', ['idItens' => $itemId])->row();
        
        if (!$item) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['success' => false, 'message' => 'Item não encontrado']));
            return;
        }

        $this->db->trans_start();

        // Remover item
        $this->db->delete('itens_de_vendas', ['idItens' => $itemId]);

        // Estornar estoque
        if ($this->data['configuration']['control_estoque']) {
            $this->produtos_model->updateEstoque($item->produtos_id, $item->quantidade, '+');
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['success' => false, 'message' => 'Erro ao remover produto']));
            return;
        }

        // Buscar itens atualizados
        $itens = $this->vendas_model->getProdutos($item->vendas_id);
        $total = array_sum(array_column($itens, 'subTotal'));

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => true,
                'itens' => $itens,
                'total' => $total
            ]));
    }

    /**
     * Finalizar venda (AJAX)
     */
    public function finalizarVenda()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eVenda')) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(403)
                ->set_output(json_encode(['success' => false, 'message' => 'Sem permissão']));
            return;
        }

        $vendaId = $this->input->post('venda_id');
        $pagamentos = json_decode($this->input->post('pagamentos'), true);
        $desconto = $this->input->post('desconto') ? str_replace(',', '.', str_replace('.', '', $this->input->post('desconto'))) : 0;
        $tipoDesconto = $this->input->post('tipo_desconto') ?: null;

        if (empty($pagamentos) || !is_array($pagamentos)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['success' => false, 'message' => 'Pagamentos não informados']));
            return;
        }

        // Buscar nomes das formas de pagamento
        foreach ($pagamentos as &$pagamento) {
            $forma = $this->db->get_where('formas_pagamento', ['idFormaPagamento' => $pagamento['forma_pagamento_id']])->row();
            $pagamento['forma_nome'] = $forma ? $forma->nome : 'Desconhecido';
            $pagamento['valor'] = str_replace(',', '.', str_replace('.', '', $pagamento['valor']));
        }

        $result = $this->pdv_model->finalizarVendaPDV($vendaId, $pagamentos, $desconto, $tipoDesconto);

        if ($result['success']) {
            log_info('Venda finalizada no PDV. ID: ' . $vendaId);
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($result));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($result));
        }
    }

    /**
     * Cancelar venda (AJAX)
     */
    public function cancelarVenda()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'dVenda')) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(403)
                ->set_output(json_encode(['success' => false, 'message' => 'Sem permissão']));
            return;
        }

        $vendaId = $this->input->post('venda_id');
        $motivo = $this->input->post('motivo');
        $estornarEstoque = $this->input->post('estornar_estoque') !== 'false';

        if (empty($motivo)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['success' => false, 'message' => 'Motivo do cancelamento é obrigatório']));
            return;
        }

        $result = $this->pdv_model->cancelarVenda($vendaId, $this->session->userdata('id_admin'), $motivo, $estornarEstoque);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($result));
    }

    /**
     * Abertura de caixa
     */
    public function abrirCaixa()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aVenda')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para abrir caixa.');
            redirect(base_url());
        }

        // Verificar se já existe caixa aberto
        $caixaAberto = $this->pdv_model->getCaixaAberto($this->session->userdata('id_admin'));
        if ($caixaAberto) {
            redirect('pdv');
        }

        if ($this->input->post()) {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('caixa_id', 'Caixa', 'required');
            $this->form_validation->set_rules('valor_abertura', 'Valor de Abertura', 'required|numeric');

            if ($this->form_validation->run() == false) {
                $this->data['custom_error'] = validation_errors();
            } else {
                $caixaId = $this->input->post('caixa_id');
                $valorAbertura = str_replace(',', '.', str_replace('.', '', $this->input->post('valor_abertura')));

                $result = $this->pdv_model->abrirCaixa(
                    $caixaId,
                    $this->session->userdata('id_admin'),
                    $valorAbertura
                );

                if ($result['success']) {
                    $this->session->set_flashdata('success', 'Caixa aberto com sucesso!');
                    redirect('pdv');
                } else {
                    $this->data['custom_error'] = $result['message'];
                }
            }
        }

        // Buscar caixas ativos
        $this->data['caixas'] = $this->db->where('ativo', 1)->get('caixas')->result();

        $this->data['view'] = 'pdv/abrir_caixa';
        return $this->layout();
    }

    /**
     * Fechamento de caixa
     */
    public function fecharCaixa()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eVenda')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para fechar caixa.');
            redirect(base_url());
        }

        $caixaAberto = $this->pdv_model->getCaixaAberto($this->session->userdata('id_admin'));
        
        if (!$caixaAberto) {
            $this->session->set_flashdata('error', 'Não há caixa aberto para fechar.');
            redirect('pdv');
        }

        if ($this->input->post()) {
            $valorFechamento = str_replace(',', '.', str_replace('.', '', $this->input->post('valor_fechamento')));
            $observacoes = $this->input->post('observacoes');

            $result = $this->pdv_model->fecharCaixa($caixaAberto->idTurno, $valorFechamento, $observacoes);

            if ($result['success']) {
                $this->session->set_flashdata('success', 'Caixa fechado com sucesso!');
                redirect('pdv/relatorioFechamento/' . $caixaAberto->idTurno);
            } else {
                $this->data['custom_error'] = $result['message'];
            }
        }

        // Estatísticas do turno
        $this->data['estatisticas'] = $this->pdv_model->getEstatisticasTurno($caixaAberto->idTurno);
        $this->data['caixa_aberto'] = $caixaAberto;

        $this->data['view'] = 'pdv/fechar_caixa';
        return $this->layout();
    }

    /**
     * Relatório de fechamento
     */
    public function relatorioFechamento($turnoId)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vVenda')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar relatórios.');
            redirect(base_url());
        }

        $turno = $this->db->select('turnos_caixa.*, caixas.nome as caixa_nome, usuarios.nome as usuario_nome')
            ->from('turnos_caixa')
            ->join('caixas', 'caixas.idCaixa = turnos_caixa.caixas_id')
            ->join('usuarios', 'usuarios.idUsuarios = turnos_caixa.usuarios_id')
            ->where('turnos_caixa.idTurno', $turnoId)
            ->get()
            ->row();

        if (!$turno) {
            $this->session->set_flashdata('error', 'Turno não encontrado.');
            redirect('pdv');
        }

        $this->data['turno'] = $turno;
        $this->data['estatisticas'] = $this->pdv_model->getEstatisticasTurno($turnoId);

        // Vendas do turno
        $this->data['vendas'] = $this->db->select('vendas.*, clientes.nomeCliente')
            ->from('vendas')
            ->join('clientes', 'clientes.idClientes = vendas.clientes_id')
            ->where('vendas.turnos_caixa_id', $turnoId)
            ->where('vendas.faturado', 1)
            ->order_by('vendas.dataVenda', 'DESC')
            ->get()
            ->result();

        $this->data['view'] = 'pdv/relatorio_fechamento';
        return $this->layout();
    }

    /**
     * Dashboard de vendas
     */
    public function dashboard()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vVenda')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar o dashboard.');
            redirect(base_url());
        }

        $data = $this->input->get('data') ?: date('Y-m-d');

        $this->data['vendas_dia'] = $this->pdv_model->getVendasDia($data);
        $this->data['produtos_mais_vendidos'] = $this->pdv_model->getProdutosMaisVendidos($data, 10);
        $this->data['data'] = $data;

        // Formas de pagamento do dia
        $this->db->select('formas_pagamento.nome, COUNT(*) as quantidade, SUM(pagamentos_venda.valor) as total');
        $this->db->from('pagamentos_venda');
        $this->db->join('vendas', 'vendas.idVendas = pagamentos_venda.vendas_id');
        $this->db->join('formas_pagamento', 'formas_pagamento.idFormaPagamento = pagamentos_venda.formas_pagamento_id');
        $this->db->where('DATE(vendas.dataVenda)', $data);
        $this->db->where('vendas.faturado', 1);
        $this->db->group_by('formas_pagamento.idFormaPagamento');
        $this->data['formas_pagamento_dia'] = $this->db->get()->result();

        $this->data['view'] = 'pdv/dashboard';
        return $this->layout();
    }
}
