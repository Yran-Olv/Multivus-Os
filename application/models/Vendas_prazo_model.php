<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Vendas_prazo_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Criar parcelas para uma venda a prazo
     */
    public function criarParcelas($vendaId, $valorTotal, $numeroParcelas, $intervaloDias = 30, $dataPrimeiroVencimento = null, $taxaJuros = 0)
    {
        if ($numeroParcelas <= 1) {
            return false;
        }

        if ($dataPrimeiroVencimento === null) {
            $dataPrimeiroVencimento = date('Y-m-d', strtotime('+' . $intervaloDias . ' days'));
        }

        $valorParcela = $valorTotal / $numeroParcelas;
        $parcelas = [];

        $this->db->trans_start();

        for ($i = 1; $i <= $numeroParcelas; $i++) {
            $dataVencimento = date('Y-m-d', strtotime($dataPrimeiroVencimento . ' +' . (($i - 1) * $intervaloDias) . ' days'));
            
            // Calcular juros se aplicável
            $valorComJuros = $valorParcela;
            if ($taxaJuros > 0 && $i > 1) {
                $meses = ($i - 1) * ($intervaloDias / 30);
                $valorComJuros = $valorParcela * pow(1 + ($taxaJuros / 100), $meses);
            }

            $parcela = [
                'vendas_id' => $vendaId,
                'numero_parcela' => $i,
                'valor_parcela' => $valorParcela,
                'valor_pago' => 0.00,
                'data_vencimento' => $dataVencimento,
                'data_pagamento' => null,
                'status' => 'pendente',
                'dias_atraso' => 0,
                'juros' => 0.00,
                'multa' => 0.00,
                'desconto' => 0.00,
                'valor_total' => $valorComJuros,
                'observacoes' => null,
                'formas_pagamento_id' => null,
                'usuarios_id' => null
            ];

            $this->db->insert('parcelas_venda', $parcela);
            $parcelas[] = $parcela;
        }

        // Atualizar venda
        $this->db->where('idVendas', $vendaId);
        $this->db->update('vendas', [
            'tipo_venda' => 'aprazo',
            'numero_parcelas' => $numeroParcelas,
            'intervalo_parcelas' => $intervaloDias,
            'taxa_juros' => $taxaJuros,
            'valor_total_parcelado' => array_sum(array_column($parcelas, 'valor_total')),
            'valor_pendente' => array_sum(array_column($parcelas, 'valor_total')),
            'data_primeiro_vencimento' => $dataPrimeiroVencimento
        ]);

        $this->db->trans_complete();

        return $this->db->trans_status() !== false;
    }

    /**
     * Obter parcelas de uma venda
     */
    public function getParcelas($vendaId)
    {
        $this->db->select('parcelas_venda.*, formas_pagamento.nome as forma_pagamento_nome, usuarios.nome as usuario_nome');
        $this->db->from('parcelas_venda');
        $this->db->join('formas_pagamento', 'formas_pagamento.idFormaPagamento = parcelas_venda.formas_pagamento_id', 'left');
        $this->db->join('usuarios', 'usuarios.idUsuarios = parcelas_venda.usuarios_id', 'left');
        $this->db->where('vendas_id', $vendaId);
        $this->db->order_by('numero_parcela', 'ASC');

        return $this->db->get()->result();
    }

    /**
     * Obter parcela por ID
     */
    public function getParcelaById($parcelaId)
    {
        $this->db->select('parcelas_venda.*, vendas.*, clientes.nomeCliente, clientes.telefone, clientes.email');
        $this->db->from('parcelas_venda');
        $this->db->join('vendas', 'vendas.idVendas = parcelas_venda.vendas_id');
        $this->db->join('clientes', 'clientes.idClientes = vendas.clientes_id');
        $this->db->where('parcelas_venda.idParcela', $parcelaId);
        $this->db->limit(1);

        return $this->db->get()->row();
    }

    /**
     * Registrar pagamento de parcela
     */
    public function registrarPagamento($parcelaId, $valorPago, $dataPagamento = null, $formaPagamentoId = null, $usuarioId = null, $observacoes = null, $desconto = 0)
    {
        if ($dataPagamento === null) {
            $dataPagamento = date('Y-m-d');
        }

        $parcela = $this->db->get_where('parcelas_venda', ['idParcela' => $parcelaId])->row();
        
        if (!$parcela) {
            return false;
        }

        $this->db->trans_start();

        $valorPagoTotal = $parcela->valor_pago + $valorPago;
        $valorTotal = $parcela->valor_total - $desconto;
        
        $status = 'pendente';
        if ($valorPagoTotal >= $valorTotal) {
            $status = 'paga';
            $valorPagoTotal = $valorTotal; // Não pode pagar mais que o total
        }

        // Atualizar parcela
        $this->db->where('idParcela', $parcelaId);
        $this->db->update('parcelas_venda', [
            'valor_pago' => $valorPagoTotal,
            'data_pagamento' => $status === 'paga' ? $dataPagamento : null,
            'status' => $status,
            'desconto' => $desconto,
            'valor_total' => $valorTotal,
            'formas_pagamento_id' => $formaPagamentoId,
            'usuarios_id' => $usuarioId,
            'observacoes' => $observacoes,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // Registrar no histórico
        $this->db->insert('historico_pagamentos', [
            'parcelas_venda_id' => $parcelaId,
            'vendas_id' => $parcela->vendas_id,
            'valor_pago' => $valorPago,
            'data_pagamento' => $dataPagamento,
            'formas_pagamento_id' => $formaPagamentoId,
            'usuarios_id' => $usuarioId,
            'observacoes' => $observacoes
        ]);

        // Atualizar valores da venda
        $this->atualizarValoresVenda($parcela->vendas_id);

        // Se parcela foi paga, criar notificação
        if ($status === 'paga') {
            $this->criarNotificacao($parcela->vendas_id, $parcelaId, 'pagamento_recebido', 
                'Parcela Paga', 
                "Parcela #{$parcela->numero_parcela} da venda #{$parcela->vendas_id} foi paga no valor de R$ " . number_format($valorPago, 2, ',', '.'),
                'baixa'
            );
        }

        $this->db->trans_complete();

        return $this->db->trans_status() !== false;
    }

    /**
     * Atualizar valores totais da venda
     */
    public function atualizarValoresVenda($vendaId)
    {
        $parcelas = $this->getParcelas($vendaId);
        
        $valorPagoTotal = 0;
        $valorPendente = 0;
        
        foreach ($parcelas as $parcela) {
            $valorPagoTotal += $parcela->valor_pago;
            if ($parcela->status !== 'paga') {
                $valorPendente += ($parcela->valor_total - $parcela->valor_pago);
            }
        }

        $this->db->where('idVendas', $vendaId);
        $this->db->update('vendas', [
            'valor_pago_total' => $valorPagoTotal,
            'valor_pendente' => $valorPendente
        ]);
    }

    /**
     * Atualizar status de parcelas atrasadas
     */
    public function atualizarParcelasAtrasadas()
    {
        $hoje = date('Y-m-d');
        
        // Buscar parcelas pendentes com vencimento passado
        $this->db->where('status', 'pendente');
        $this->db->where('data_vencimento <', $hoje);
        $parcelas = $this->db->get('parcelas_venda')->result();

        foreach ($parcelas as $parcela) {
            $diasAtraso = (strtotime($hoje) - strtotime($parcela->data_vencimento)) / 86400;
            
            // Buscar configurações da venda para calcular multa e juros
            $venda = $this->db->get_where('vendas', ['idVendas' => $parcela->vendas_id])->row();
            
            $multa = 0;
            $juros = 0;
            
            if ($venda && $venda->taxa_multa > 0) {
                $multa = $parcela->valor_parcela * ($venda->taxa_multa / 100);
            }
            
            if ($venda && $venda->taxa_juros > 0) {
                $mesesAtraso = $diasAtraso / 30;
                $juros = $parcela->valor_parcela * ($venda->taxa_juros / 100) * $mesesAtraso;
            }

            $valorTotal = $parcela->valor_parcela + $multa + $juros - $parcela->desconto;

            // Atualizar parcela
            $this->db->where('idParcela', $parcela->idParcela);
            $this->db->update('parcelas_venda', [
                'status' => 'atrasada',
                'dias_atraso' => $diasAtraso,
                'multa' => $multa,
                'juros' => $juros,
                'valor_total' => $valorTotal
            ]);

            // Criar notificação de atraso
            $this->criarNotificacao(
                $parcela->vendas_id,
                $parcela->idParcela,
                'atraso',
                'Parcela em Atraso',
                "Parcela #{$parcela->numero_parcela} da venda #{$parcela->vendas_id} está em atraso há {$diasAtraso} dia(s). Valor: R$ " . number_format($valorTotal, 2, ',', '.'),
                $diasAtraso > 30 ? 'urgente' : ($diasAtraso > 15 ? 'alta' : 'media')
            );
        }

        return count($parcelas);
    }

    /**
     * Criar notificação
     */
    public function criarNotificacao($vendaId, $parcelaId = null, $tipo, $titulo, $mensagem, $prioridade = 'media', $usuarioId = null)
    {
        $data = [
            'vendas_id' => $vendaId,
            'parcelas_venda_id' => $parcelaId,
            'tipo' => $tipo,
            'titulo' => $titulo,
            'mensagem' => $mensagem,
            'prioridade' => $prioridade,
            'lida' => 0,
            'usuarios_id' => $usuarioId
        ];

        return $this->db->insert('notificacoes_venda', $data);
    }

    /**
     * Obter notificações
     */
    public function getNotificacoes($usuarioId = null, $lidas = null, $tipo = null, $limit = null)
    {
        $this->db->select('notificacoes_venda.*, vendas.idVendas, clientes.nomeCliente, parcelas_venda.numero_parcela');
        $this->db->from('notificacoes_venda');
        $this->db->join('vendas', 'vendas.idVendas = notificacoes_venda.vendas_id');
        $this->db->join('clientes', 'clientes.idClientes = vendas.clientes_id');
        $this->db->join('parcelas_venda', 'parcelas_venda.idParcela = notificacoes_venda.parcelas_venda_id', 'left');
        
        if ($usuarioId !== null) {
            $this->db->where('(notificacoes_venda.usuarios_id IS NULL OR notificacoes_venda.usuarios_id = ' . $usuarioId . ')');
        }
        
        if ($lidas !== null) {
            $this->db->where('notificacoes_venda.lida', $lidas);
        }
        
        if ($tipo !== null) {
            $this->db->where('notificacoes_venda.tipo', $tipo);
        }
        
        $this->db->order_by('notificacoes_venda.created_at', 'DESC');
        
        if ($limit !== null) {
            $this->db->limit($limit);
        }

        return $this->db->get()->result();
    }

    /**
     * Marcar notificação como lida
     */
    public function marcarNotificacaoLida($notificacaoId, $usuarioId = null)
    {
        $data = [
            'lida' => 1,
            'data_leitura' => date('Y-m-d H:i:s')
        ];

        $this->db->where('idNotificacao', $notificacaoId);
        if ($usuarioId !== null) {
            $this->db->where('(usuarios_id IS NULL OR usuarios_id = ' . $usuarioId . ')');
        }
        
        return $this->db->update('notificacoes_venda', $data);
    }

    /**
     * Contar notificações não lidas
     */
    public function countNotificacoesNaoLidas($usuarioId = null)
    {
        $this->db->where('lida', 0);
        if ($usuarioId !== null) {
            $this->db->where('(usuarios_id IS NULL OR usuarios_id = ' . $usuarioId . ')');
        }
        
        return $this->db->count_all_results('notificacoes_venda');
    }

    /**
     * Buscar vendas a prazo com filtros
     */
    public function buscarVendasPrazo($filtros = [])
    {
        $this->db->select('vendas.*, clientes.nomeCliente, clientes.telefone, clientes.email, usuarios.nome as vendedor_nome');
        $this->db->from('vendas');
        $this->db->join('clientes', 'clientes.idClientes = vendas.clientes_id');
        $this->db->join('usuarios', 'usuarios.idUsuarios = vendas.usuarios_id', 'left');
        $this->db->where('vendas.tipo_venda', 'aprazo');

        // Filtros
        if (isset($filtros['status_parcela'])) {
            if ($filtros['status_parcela'] === 'atrasadas') {
                $this->db->where('vendas.valor_pendente >', 0);
                $this->db->where('EXISTS (SELECT 1 FROM parcelas_venda WHERE parcelas_venda.vendas_id = vendas.idVendas AND parcelas_venda.status = "atrasada")');
            } elseif ($filtros['status_parcela'] === 'pendentes') {
                $this->db->where('vendas.valor_pendente >', 0);
            } elseif ($filtros['status_parcela'] === 'pagas') {
                $this->db->where('vendas.valor_pendente', 0);
            }
        }

        if (isset($filtros['cliente'])) {
            $this->db->like('clientes.nomeCliente', $filtros['cliente']);
        }

        if (isset($filtros['data_inicio'])) {
            $this->db->where('vendas.dataVenda >=', $filtros['data_inicio']);
        }

        if (isset($filtros['data_fim'])) {
            $this->db->where('vendas.dataVenda <=', $filtros['data_fim']);
        }

        if (isset($filtros['vencimento_inicio'])) {
            $this->db->where('vendas.data_primeiro_vencimento >=', $filtros['vencimento_inicio']);
        }

        if (isset($filtros['vencimento_fim'])) {
            $this->db->where('vendas.data_primeiro_vencimento <=', $filtros['vencimento_fim']);
        }

        if (isset($filtros['valor_minimo'])) {
            $this->db->where('vendas.valorTotal >=', $filtros['valor_minimo']);
        }

        if (isset($filtros['valor_maximo'])) {
            $this->db->where('vendas.valorTotal <=', $filtros['valor_maximo']);
        }

        $this->db->order_by('vendas.dataVenda', 'DESC');

        return $this->db->get()->result();
    }

    /**
     * Obter estatísticas de vendas a prazo
     */
    public function getEstatisticas($dataInicio = null, $dataFim = null)
    {
        if ($dataInicio === null) {
            $dataInicio = date('Y-m-01'); // Primeiro dia do mês
        }
        if ($dataFim === null) {
            $dataFim = date('Y-m-t'); // Último dia do mês
        }

        $this->db->select('
            COUNT(*) as total_vendas,
            SUM(valorTotal) as valor_total_vendas,
            SUM(valor_pago_total) as valor_pago,
            SUM(valor_pendente) as valor_pendente,
            COUNT(CASE WHEN valor_pendente > 0 THEN 1 END) as vendas_pendentes,
            COUNT(CASE WHEN EXISTS (SELECT 1 FROM parcelas_venda WHERE parcelas_venda.vendas_id = vendas.idVendas AND parcelas_venda.status = "atrasada") THEN 1 END) as vendas_atrasadas
        ');
        $this->db->from('vendas');
        $this->db->where('tipo_venda', 'aprazo');
        $this->db->where('dataVenda >=', $dataInicio);
        $this->db->where('dataVenda <=', $dataFim);

        return $this->db->get()->row();
    }

    /**
     * Obter histórico de pagamentos de uma parcela
     */
    public function getHistoricoPagamentos($parcelaId)
    {
        $this->db->select('historico_pagamentos.*, formas_pagamento.nome as forma_pagamento_nome, usuarios.nome as usuario_nome');
        $this->db->from('historico_pagamentos');
        $this->db->join('formas_pagamento', 'formas_pagamento.idFormaPagamento = historico_pagamentos.formas_pagamento_id', 'left');
        $this->db->join('usuarios', 'usuarios.idUsuarios = historico_pagamentos.usuarios_id', 'left');
        $this->db->where('historico_pagamentos.parcelas_venda_id', $parcelaId);
        $this->db->order_by('historico_pagamentos.data_pagamento', 'DESC');

        return $this->db->get()->result();
    }
}
