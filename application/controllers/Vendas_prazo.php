<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Vendas_prazo extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('vendas_prazo_model');
        $this->load->model('vendas_model');
        $this->load->model('clientes_model');
        $this->load->helper('form');
        $this->data['menuVendas'] = 'Vendas';
    }

    /**
     * Listar vendas a prazo
     */
    public function index()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vVenda')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar vendas a prazo.');
            redirect(base_url());
        }

        // Processar filtros
        $filtros = [];
        
        if ($this->input->get('cliente')) {
            $filtros['cliente'] = $this->input->get('cliente');
        }
        
        if ($this->input->get('status_parcela')) {
            $filtros['status_parcela'] = $this->input->get('status_parcela');
        }
        
        if ($this->input->get('data_inicio')) {
            $filtros['data_inicio'] = $this->input->get('data_inicio');
        }
        
        if ($this->input->get('data_fim')) {
            $filtros['data_fim'] = $this->input->get('data_fim');
        }
        
        if ($this->input->get('vencimento_inicio')) {
            $filtros['vencimento_inicio'] = $this->input->get('vencimento_inicio');
        }
        
        if ($this->input->get('vencimento_fim')) {
            $filtros['vencimento_fim'] = $this->input->get('vencimento_fim');
        }

        // Buscar vendas
        $this->data['vendas'] = $this->vendas_prazo_model->buscarVendasPrazo($filtros);
        
        // Para cada venda, buscar parcelas
        foreach ($this->data['vendas'] as $venda) {
            $venda->parcelas = $this->vendas_prazo_model->getParcelas($venda->idVendas);
            
            // Contar parcelas por status
            $venda->parcelas_pendentes = 0;
            $venda->parcelas_atrasadas = 0;
            $venda->parcelas_pagas = 0;
            
            foreach ($venda->parcelas as $parcela) {
                if ($parcela->status === 'pendente') {
                    $venda->parcelas_pendentes++;
                } elseif ($parcela->status === 'atrasada') {
                    $venda->parcelas_atrasadas++;
                } elseif ($parcela->status === 'paga') {
                    $venda->parcelas_pagas++;
                }
            }
        }

        // Estatísticas
        $this->data['estatisticas'] = $this->vendas_prazo_model->getEstatisticas();

        // Notificações não lidas
        $this->data['notificacoes_nao_lidas'] = $this->vendas_prazo_model->countNotificacoesNaoLidas($this->session->userdata('id_admin'));

        $this->data['view'] = 'vendas_prazo/listar';
        return $this->layout();
    }

    /**
     * Visualizar detalhes de uma venda a prazo
     */
    public function visualizar($vendaId = null)
    {
        if (!$vendaId || !is_numeric($vendaId)) {
            $this->session->set_flashdata('error', 'Venda não encontrada.');
            redirect('vendas_prazo');
        }

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vVenda')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar vendas.');
            redirect(base_url());
        }

        $venda = $this->vendas_model->getById($vendaId);
        
        if (!$venda || $venda->tipo_venda !== 'aprazo') {
            $this->session->set_flashdata('error', 'Venda não encontrada ou não é uma venda a prazo.');
            redirect('vendas_prazo');
        }

        $this->data['venda'] = $venda;
        $this->data['parcelas'] = $this->vendas_prazo_model->getParcelas($vendaId);
        $this->data['produtos'] = $this->vendas_model->getProdutos($vendaId);

        $this->data['view'] = 'vendas_prazo/visualizar';
        return $this->layout();
    }

    /**
     * Registrar pagamento de parcela
     */
    public function registrarPagamento()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eVenda')) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(403)
                ->set_output(json_encode(['result' => false, 'message' => 'Você não tem permissão para registrar pagamentos.']));
            return;
        }

        $this->load->library('form_validation');
        $this->form_validation->set_rules('parcela_id', 'Parcela', 'required|numeric');
        $this->form_validation->set_rules('valor_pago', 'Valor Pago', 'required|numeric|greater_than[0]');
        $this->form_validation->set_rules('data_pagamento', 'Data Pagamento', 'required');

        if ($this->form_validation->run() == false) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(['result' => false, 'message' => validation_errors()]));
            return;
        }

        $parcelaId = $this->input->post('parcela_id');
        $valorPago = str_replace(',', '.', str_replace('.', '', $this->input->post('valor_pago')));
        $dataPagamento = $this->input->post('data_pagamento');
        $formaPagamentoId = $this->input->post('forma_pagamento_id') ?: null;
        $desconto = $this->input->post('desconto') ? str_replace(',', '.', str_replace('.', '', $this->input->post('desconto'))) : 0;
        $observacoes = $this->input->post('observacoes');

        // Converter data
        try {
            $dataPagamento = date('Y-m-d', strtotime(str_replace('/', '-', $dataPagamento)));
        } catch (Exception $e) {
            $dataPagamento = date('Y-m-d');
        }

        $result = $this->vendas_prazo_model->registrarPagamento(
            $parcelaId,
            $valorPago,
            $dataPagamento,
            $formaPagamentoId,
            $this->session->userdata('id_admin'),
            $observacoes,
            $desconto
        );

        if ($result) {
            log_info('Registrou pagamento de parcela. Parcela ID: ' . $parcelaId);
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode(['result' => true, 'message' => 'Pagamento registrado com sucesso!']));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode(['result' => false, 'message' => 'Erro ao registrar pagamento.']));
        }
    }

    /**
     * Obter parcelas de uma venda (AJAX)
     */
    public function getParcelas($vendaId)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vVenda')) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(403)
                ->set_output(json_encode(['result' => false, 'message' => 'Sem permissão']));
            return;
        }

        $parcelas = $this->vendas_prazo_model->getParcelas($vendaId);
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['result' => true, 'parcelas' => $parcelas]));
    }

    /**
     * Obter notificações (AJAX)
     */
    public function getNotificacoes()
    {
        $lidas = $this->input->get('lidas');
        $tipo = $this->input->get('tipo');
        $limit = $this->input->get('limit') ?: 10;

        $notificacoes = $this->vendas_prazo_model->getNotificacoes(
            $this->session->userdata('id_admin'),
            $lidas === 'true' ? 1 : ($lidas === 'false' ? 0 : null),
            $tipo,
            $limit
        );

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['result' => true, 'notificacoes' => $notificacoes]));
    }

    /**
     * Marcar notificação como lida
     */
    public function marcarNotificacaoLida($notificacaoId)
    {
        $result = $this->vendas_prazo_model->marcarNotificacaoLida($notificacaoId, $this->session->userdata('id_admin'));

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['result' => $result]));
    }

    /**
     * Contar notificações não lidas (AJAX)
     */
    public function countNotificacoesNaoLidas()
    {
        $count = $this->vendas_prazo_model->countNotificacoesNaoLidas($this->session->userdata('id_admin'));

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['result' => true, 'count' => $count]));
    }

    /**
     * Atualizar parcelas atrasadas (pode ser chamado via cron)
     */
    public function atualizarParcelasAtrasadas()
    {
        // Verificar se é chamado via CLI ou tem permissão
        if (!$this->input->is_cli_request() && !$this->permission->checkPermission($this->session->userdata('permissao'), 'eVenda')) {
            $this->session->set_flashdata('error', 'Você não tem permissão.');
            redirect(base_url());
        }

        $atualizadas = $this->vendas_prazo_model->atualizarParcelasAtrasadas();

        if ($this->input->is_cli_request()) {
            echo "Parcelas atualizadas: {$atualizadas}\n";
        } else {
            $this->session->set_flashdata('success', "{$atualizadas} parcela(s) atualizada(s).");
            redirect('vendas_prazo');
        }
    }

    /**
     * Criar notificações de vencimento próximo
     */
    public function criarNotificacoesVencimento()
    {
        // Verificar se é chamado via CLI ou tem permissão
        if (!$this->input->is_cli_request() && !$this->permission->checkPermission($this->session->userdata('permissao'), 'eVenda')) {
            $this->session->set_flashdata('error', 'Você não tem permissão.');
            redirect(base_url());
        }

        $hoje = date('Y-m-d');
        $diasNotificar = 3; // Padrão: 3 dias antes

        // Buscar parcelas que vencem nos próximos dias
        $this->db->select('parcelas_venda.*, vendas.*, clientes.nomeCliente');
        $this->db->from('parcelas_venda');
        $this->db->join('vendas', 'vendas.idVendas = parcelas_venda.vendas_id');
        $this->db->join('clientes', 'clientes.idClientes = vendas.clientes_id');
        $this->db->where('parcelas_venda.status', 'pendente');
        $this->db->where('parcelas_venda.data_vencimento >=', $hoje);
        $this->db->where('parcelas_venda.data_vencimento <=', date('Y-m-d', strtotime("+{$diasNotificar} days")));
        $this->db->where('vendas.notificar_atraso', 1);
        
        // Verificar se já existe notificação para esta parcela
        $this->db->where('NOT EXISTS (SELECT 1 FROM notificacoes_venda WHERE notificacoes_venda.parcelas_venda_id = parcelas_venda.idParcela AND notificacoes_venda.tipo = "vencendo_proximo")');
        
        $parcelas = $this->db->get()->result();

        $criadas = 0;
        foreach ($parcelas as $parcela) {
            $diasRestantes = (strtotime($parcela->data_vencimento) - strtotime($hoje)) / 86400;
            
            $this->vendas_prazo_model->criarNotificacao(
                $parcela->vendas_id,
                $parcela->idParcela,
                'vencendo_proximo',
                'Parcela Vencendo em Breve',
                "Parcela #{$parcela->numero_parcela} da venda #{$parcela->vendas_id} (Cliente: {$parcela->nomeCliente}) vence em {$diasRestantes} dia(s). Valor: R$ " . number_format($parcela->valor_total, 2, ',', '.'),
                $diasRestantes <= 1 ? 'alta' : 'media'
            );
            $criadas++;
        }

        if ($this->input->is_cli_request()) {
            echo "Notificações criadas: {$criadas}\n";
        } else {
            $this->session->set_flashdata('success', "{$criadas} notificação(ões) criada(s).");
            redirect('vendas_prazo');
        }
    }

    /**
     * Relatório de vendas a prazo
     */
    public function relatorio()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vVenda')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar relatórios.');
            redirect(base_url());
        }

        $dataInicio = $this->input->get('data_inicio') ?: date('Y-m-01');
        $dataFim = $this->input->get('data_fim') ?: date('Y-m-t');

        $this->data['estatisticas'] = $this->vendas_prazo_model->getEstatisticas($dataInicio, $dataFim);
        $this->data['data_inicio'] = $dataInicio;
        $this->data['data_fim'] = $dataFim;

        // Vendas com parcelas atrasadas
        $this->db->select('vendas.*, clientes.nomeCliente');
        $this->db->from('vendas');
        $this->db->join('clientes', 'clientes.idClientes = vendas.clientes_id');
        $this->db->where('vendas.tipo_venda', 'aprazo');
        $this->db->where('vendas.dataVenda >=', $dataInicio);
        $this->db->where('vendas.dataVenda <=', $dataFim);
        $this->db->where('EXISTS (SELECT 1 FROM parcelas_venda WHERE parcelas_venda.vendas_id = vendas.idVendas AND parcelas_venda.status = "atrasada")');
        $this->data['vendas_atrasadas'] = $this->db->get()->result();

        $this->data['view'] = 'vendas_prazo/relatorio';
        return $this->layout();
    }
}
