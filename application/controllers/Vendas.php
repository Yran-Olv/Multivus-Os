<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Vendas extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->helper('form');
        $this->load->model('vendas_model');
        $this->load->model('pdv_model');
        $this->load->model('produtos_model');
        $this->data['menuVendas'] = 'Vendas';
    }

    public function index()
    {
        $this->gerenciar();
    }

    public function gerenciar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vVenda')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar vendas.');
            redirect(base_url());
        }

        $this->load->library('pagination');

        $where_array = [];

        $pesquisa = $this->input->get('pesquisa');
        $status = $this->input->get('status');
        $de = $this->input->get('data');
        $ate = $this->input->get('data2');

        if ($pesquisa) {
            $where_array['pesquisa'] = $pesquisa;
        }
        if ($status) {
            $where_array['status'] = $status;
        }
        if ($de) {
            $where_array['de'] = $de;
        }
        if ($ate) {
            $where_array['ate'] = $ate;
        }

        $this->data['configuration']['base_url'] = site_url('vendas/gerenciar/');
        $this->data['configuration']['total_rows'] = $this->vendas_model->count('vendas');
        
        if (count($where_array) > 0) {
            $this->data['configuration']['suffix'] = "?pesquisa={$pesquisa}&status={$status}&data={$de}&data2={$ate}";
            $this->data['configuration']['first_url'] = base_url("index.php/vendas/gerenciar")."?pesquisa={$pesquisa}&status={$status}&data={$de}&data2={$ate}";
        }

        $this->pagination->initialize($this->data['configuration']);

        $this->data['results'] = $this->vendas_model->get('vendas', '*', $where_array, $this->data['configuration']['per_page'], $this->uri->segment(3));

        foreach ($this->data['results'] as $key => $venda) {
            $this->data['results'][$key]->totalProdutos = $this->vendas_model->getTotalVendas($venda->idVendas);
        }

        $this->data['view'] = 'vendas/vendas';

        return $this->layout();
    }

    public function adicionar()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'aVenda')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para adicionar Vendas.');
            redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        if ($this->form_validation->run('vendas') == false) {
            $this->data['custom_error'] = (validation_errors() ? true : false);
        } else {
            $dataVenda = $this->input->post('dataVenda');

            try {
                $dataVenda = explode('/', $dataVenda);
                $dataVenda = $dataVenda[2] . '-' . $dataVenda[1] . '-' . $dataVenda[0];
            } catch (Exception $e) {
                $dataVenda = date('Y-m-d');
            }

            $data = [
                'dataVenda' => $dataVenda,
                'observacoes' => $this->input->post('observacoes'),
                'observacoes_cliente' => $this->input->post('observacoes_cliente'),
                'clientes_id' => $this->input->post('clientes_id'),
                'usuarios_id' => $this->input->post('usuarios_id'),
                'faturado' => 0,
                'status' => $this->input->post('status'),
                'garantia' => $this->input->post('garantia')
            ];

            $id = $this->vendas_model->add('vendas', $data, true);

            if (is_numeric($id)) {
                $this->session->set_flashdata('success', 'Venda iniciada com sucesso, adicione os produtos.');
                log_info('Adicionou uma venda. ID: ' . $id);
                redirect(site_url('vendas/editar/') . $id);
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro.</p></div>';
            }
        }

        $this->data['view'] = 'vendas/adicionarVenda';

        return $this->layout();
    }

    public function editar()
    {
        if (! $this->uri->segment(3) || ! is_numeric($this->uri->segment(3)) || ! $this->vendas_model->getById($this->uri->segment(3))) {
            $this->session->set_flashdata('error', 'Venda não encontrada ou parâmetro inválido.');
            redirect('vendas/gerenciar');
        }

        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'eVenda')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para editar vendas');
            redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        $this->data['editavel'] = $this->vendas_model->isEditable($this->input->post('idVendas'));
        if (! $this->data['editavel']) {
            $this->session->set_flashdata('error', 'Essa Venda já tem seu status Faturada e não pode ser alterado e nem suas informações atualizadas. Por favor abrir uma nova Venda.');

            redirect(site_url('vendas'));
        }

        if ($this->form_validation->run('vendas') == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="form_error">' . validation_errors() . '</div>' : false);
        } else {
            $dataVenda = $this->input->post('dataVenda');

            try {
                $dataVenda = explode('/', $dataVenda);
                $dataVenda = $dataVenda[2] . '-' . $dataVenda[1] . '-' . $dataVenda[0];
            } catch (Exception $e) {
                $dataVenda = date('Y/m/d');
            }

            $data = [
                'dataVenda' => $dataVenda,
                'observacoes' => $this->input->post('observacoes'),
                'observacoes_cliente' => $this->input->post('observacoes_cliente'),
                'usuarios_id' => $this->input->post('usuarios_id'),
                'clientes_id' => $this->input->post('clientes_id'),
                'status' => $this->input->post('status'),
                'garantia' => $this->input->post('garantia')
            ];

            if ($this->vendas_model->edit('vendas', $data, 'idVendas', $this->input->post('idVendas')) == true) {
                $this->session->set_flashdata('success', 'Venda editada com sucesso!');
                log_info('Alterou uma venda. ID: ' . $this->input->post('idVendas'));
                redirect(site_url('vendas/editar/') . $this->input->post('idVendas'));
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro</p></div>';
            }
        }

        $this->data['result'] = $this->vendas_model->getById($this->uri->segment(3));
        $this->data['produtos'] = $this->vendas_model->getProdutos($this->uri->segment(3));
        $this->data['view'] = 'vendas/editarVenda';

        return $this->layout();
    }

    public function visualizar()
    {
        if (! $this->uri->segment(3) || ! is_numeric($this->uri->segment(3))) {
            $this->session->set_flashdata('error', 'Item não pode ser encontrado, parâmetro não foi passado corretamente.');
            redirect('mapos');
        }

        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'vVenda')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar vendas.');
            redirect(base_url());
        }

        $this->data['custom_error'] = '';
        $this->load->model('mapos_model');
        $this->data['result'] = $this->vendas_model->getById($this->uri->segment(3));
        $this->data['produtos'] = $this->vendas_model->getProdutos($this->uri->segment(3));
        $this->data['emitente'] = $this->mapos_model->getEmitente();
        $this->data['qrCode'] = $this->vendas_model->getQrCode(
            $this->uri->segment(3),
            $this->data['configuration']['pix_key'],
            $this->data['emitente']
        );
        $this->data['chaveFormatada'] = $this->formatarChave($this->data['configuration']['pix_key']);
        $this->data['modalGerarPagamento'] = $this->load->view(
            'cobrancas/modalGerarPagamento',
            [
                'id' => $this->uri->segment(3),
                'tipo' => 'venda',
            ],
            true
        );

        $clienteId = $this->data['result']->clientes_id;
        $this->load->model('clientes_model');
        $cliente = $this->clientes_model->getById($clienteId);

        $zapnumber = preg_replace('/[^0-9]/', '', $cliente->telefone ?? '');
        $this->data['zapnumber'] = $zapnumber;
        $this->data['view'] = 'vendas/visualizarVenda';

        return $this->layout();
    }

    public function imprimir()
    {
        if (! $this->uri->segment(3) || ! is_numeric($this->uri->segment(3))) {
            $this->session->set_flashdata('error', 'Item não pode ser encontrado, parâmetro não foi passado corretamente.');
            redirect('mapos');
        }

        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'vVenda')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar vendas.');
            redirect(base_url());
        }

        $this->data['custom_error'] = '';
        $this->load->model('mapos_model');
        $this->data['result'] = $this->vendas_model->getById($this->uri->segment(3));
        $this->data['produtos'] = $this->vendas_model->getProdutos($this->uri->segment(3));
        $this->data['emitente'] = $this->mapos_model->getEmitente();
        $this->data['qrCode'] = $this->vendas_model->getQrCode(
            $this->uri->segment(3),
            $this->data['configuration']['pix_key'],
            $this->data['emitente']
        );
        $this->data['chaveFormatada'] = $this->formatarChave($this->data['configuration']['pix_key']);

        $this->load->view('vendas/imprimirVenda', $this->data);
    }

    public function imprimirTermica()
    {
        if (! $this->uri->segment(3) || ! is_numeric($this->uri->segment(3))) {
            $this->session->set_flashdata('error', 'Item não pode ser encontrado, parâmetro não foi passado corretamente.');
            redirect('mapos');
        }

        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'vVenda')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar vendas.');
            redirect(base_url());
        }

        $this->data['custom_error'] = '';
        $this->load->model('mapos_model');
        $this->data['result'] = $this->vendas_model->getById($this->uri->segment(3));
        $this->data['produtos'] = $this->vendas_model->getProdutos($this->uri->segment(3));
        $this->data['emitente'] = $this->mapos_model->getEmitente();
        $this->data['qrCode'] = $this->vendas_model->getQrCode(
            $this->uri->segment(3),
            $this->data['configuration']['pix_key'],
            $this->data['emitente']
        );
        
        $this->data['chaveFormatada'] = $this->formatarChave($this->data['configuration']['pix_key']);

        $this->load->view('vendas/imprimirVendaTermica', $this->data);
    }

    public function imprimirVendaOrcamento()
    {
        if (! $this->uri->segment(3) || ! is_numeric($this->uri->segment(3))) {
            $this->session->set_flashdata('error', 'Item não pode ser encontrado, parâmetro não foi passado corretamente.');
            redirect('mapos');
        }

        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'vVenda')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar vendas.');
            redirect(base_url());
        }

        $this->data['custom_error'] = '';
        $this->load->model('mapos_model');
        $this->data['result'] = $this->vendas_model->getById($this->uri->segment(3));
        $this->data['produtos'] = $this->vendas_model->getProdutos($this->uri->segment(3));
        $this->data['emitente'] = $this->mapos_model->getEmitente();
        $this->data['qrCode'] = $this->vendas_model->getQrCode(
            $this->uri->segment(3),
            $this->data['configuration']['pix_key'],
            $this->data['emitente']
        );
        
        $this->data['chaveFormatada'] = $this->formatarChave($this->data['configuration']['pix_key']);
        $this->load->view('vendas/imprimirVendaOrcamento', $this->data);
    }

    public function excluir()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'dVenda')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para excluir vendas');
            redirect(base_url());
        }

        $this->load->model('vendas_model');

        $id = $this->input->post('id');

        $editavel = $this->vendas_model->isEditable($id);
        if (! $editavel) {
            $this->session->set_flashdata('error', 'Erro ao tentar excluir. Venda já faturada');
            redirect(site_url('vendas/gerenciar/'));
        }

        $venda = $this->vendas_model->getByIdCobrancas($id);
        if ($venda == null) {
            $venda = $this->vendas_model->getById($id);
            if ($venda == null) {
                $this->session->set_flashdata('error', 'Erro ao tentar excluir venda.');
                redirect(site_url('vendas/gerenciar/'));
            }
        }

        if (isset($venda->idCobranca) != null) {
            if ($venda->status == 'canceled') {
                $this->vendas_model->delete('cobrancas', 'vendas_id', $id);
            } else {
                $this->session->set_flashdata('error', 'Existe uma cobrança associada a esta venda, deve cancelar e/ou excluir a cobrança primeiro!');
                redirect(site_url('vendas/gerenciar/'));
            }
        }

        $this->vendas_model->delete('itens_de_vendas', 'vendas_id', $id);
        $this->vendas_model->delete('vendas', 'idVendas', $id);
        if ((int) $venda->faturado === 1) {
            $this->vendas_model->delete('lancamentos', 'descricao', "Fatura de Venda - #${id}");
        }

        log_info('Removeu uma venda. ID: ' . $id);

        $this->session->set_flashdata('success', 'Venda excluída com sucesso!');
        redirect(site_url('vendas/gerenciar/'));
    }

    public function autoCompleteProduto()
    {
        if (isset($_GET['term'])) {
            $q = strtolower($_GET['term']);
            $this->vendas_model->autoCompleteProduto($q);
        }
    }

    public function autoCompleteCliente()
    {
        if (isset($_GET['term'])) {
            $q = strtolower($_GET['term']);
            $this->vendas_model->autoCompleteCliente($q);
        }
    }

    public function autoCompleteUsuario()
    {
        if (isset($_GET['term'])) {
            $q = strtolower($_GET['term']);
            $this->vendas_model->autoCompleteUsuario($q);
        }
    }

    public function adicionarProduto()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eVenda')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para editar vendas.');
            redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->form_validation->set_rules('quantidade', 'Quantidade', 'trim|required');
        $this->form_validation->set_rules('idProduto', 'Produto', 'trim|required');
        $this->form_validation->set_rules('idVendasProduto', 'Vendas', 'trim|required');

        $idVenda = $this->input->post('idVendasProduto');
        $editavel = $this->vendas_model->isEditable($idVenda);
        if (!$editavel) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(422)
                ->set_output(json_encode(['result' => false, 'messages' => '<br /><br /> <strong>Motivo:</strong> Venda já faturada']));
        }

        if ($this->form_validation->run() == false) {
            echo json_encode(['result' => false]);
        } else {
            $preco = $this->input->post('preco');
            $quantidade = $this->input->post('quantidade');
            $subtotal = $preco * $quantidade;
            $produto = $this->input->post('idProduto');
            $data = [
                'quantidade' => $quantidade,
                'subTotal' => $subtotal,
                'produtos_id' => $produto,
                'preco' => $preco,
                'vendas_id' => $idVenda,
            ];

            if ($this->vendas_model->add('itens_de_vendas', $data) == true) {
                $this->load->model('produtos_model');

                if ($this->data['configuration']['control_estoque']) {
                    $this->produtos_model->updateEstoque($produto, $quantidade, '-');
                }

                // Atualiza o desconto da venda
                $this->db->set('desconto', 0.00);
                $this->db->set('valor_desconto', 0.00);
                $this->db->set('tipo_desconto', null);
                $this->db->where('idVendas', $idVenda);
                $this->db->update('vendas');

                // Registra a ação nos logs com o ID da venda
                log_info('Adicionou produto à venda com ID: ' . $idVenda);

                echo json_encode(['result' => true]);
            } else {
                echo json_encode(['result' => false]);
            }
        }
    }

    public function excluirProduto()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eVenda')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para editar Vendas.');
            redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->form_validation->set_rules('idProduto', 'Produto', 'trim|required');
        $this->form_validation->set_rules('idVendas', 'Venda', 'trim|required');
        $this->form_validation->set_rules('quantidade', 'Quantidade', 'trim|required');
        $this->form_validation->set_rules('produto', 'Produto', 'trim|required');

        if ($this->form_validation->run() == false) {
            echo json_encode(['result' => false, 'messages' => 'Dados inválidos']);
            return;
        }

        $idProduto = $this->input->post('idProduto');
        $idVendas = $this->input->post('idVendas');
        $quantidade = $this->input->post('quantidade');
        $produto = $this->input->post('produto');

        $editavel = $this->vendas_model->isEditable($idVendas);
        if (!$editavel) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(422)
                ->set_output(json_encode(['result' => false, 'messages' => '<br /><br /> <strong>Motivo:</strong> Venda já faturada']));
        }

        $this->db->trans_start();

        $this->vendas_model->delete('itens_de_vendas', 'idItens', $idProduto);

        if ($this->data['configuration']['control_estoque']) {
            $this->load->model('produtos_model');
            $this->produtos_model->updateEstoque($produto, $quantidade, '+');
        }

        $this->db->set('desconto', 0.00);
        $this->db->set('valor_desconto', 0.00);
        $this->db->set('tipo_desconto', null);
        $this->db->where('idVendas', $idVendas);
        $this->db->update('vendas');

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            echo json_encode(['result' => false, 'messages' => 'Erro ao excluir o produto']);
        } else {
            $this->db->trans_complete();
            log_info('Removeu produto da venda. ID da Venda: ' . $idVendas . ', ID do Produto: ' . $idProduto);
            echo json_encode(['result' => true, 'messages' => 'Produto removido com sucesso']);
        }
    }

    public function adicionarDesconto()
    {
        if ($this->input->post('desconto') == '') {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(['messages' => 'Campo desconto vazio']));
        } else {
            $idVendas = $this->input->post('idVendas');
            $data = [
                'desconto' => $this->input->post('desconto'),
                'tipo_desconto' => $this->input->post('tipoDesconto'),
                'valor_desconto' => $this->input->post('resultado'),
            ];
            $editavel = $this->vendas_model->isEditable($idVendas);
            if (! $editavel) {
                return $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(400)
                    ->set_output(json_encode(['result' => false, 'messages', 'Desconto não pode ser adiciona. Venda não ja Faturada/Cancelada']));
            }
            if ($this->vendas_model->edit('vendas', $data, 'idVendas', $idVendas) == true) {
                log_info('Adicionou um desconto na Venda. ID: ' . $idVendas);

                return $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(201)
                    ->set_output(json_encode(['result' => true, 'messages' => 'Desconto adicionado com sucesso!']));
            } else {
                log_info('Ocorreu um erro ao tentar adiciona desconto a Venda: ' . $idVendas);

                return $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(400)
                    ->set_output(json_encode(['result' => false, 'messages', 'Ocorreu um erro ao tentar adiciona desconto a Venda.']));
            }
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(400)
            ->set_output(json_encode(['result' => false, 'messages', 'Ocorreu um erro ao tentar adiciona desconto a OS.']));
    }

    public function faturar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eVenda')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para editar Vendas');
            redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->load->model('vendas_prazo_model');
        $this->data['custom_error'] = '';

        if ($this->form_validation->run('receita') == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="form_error">' . validation_errors() . '</div>' : false);
        } else {
            $venda_id = $this->input->post('vendas_id');
            $vencimento = $this->input->post('vencimento');
            $recebimento = $this->input->post('recebimento');
            $tipoVenda = $this->input->post('tipo_venda'); // 'avista' ou 'aprazo'
            $numeroParcelas = $this->input->post('numero_parcelas') ?: 1;
            $intervaloParcelas = $this->input->post('intervalo_parcelas') ?: 30;
            $taxaJuros = $this->input->post('taxa_juros') ? str_replace(',', '.', $this->input->post('taxa_juros')) : 0;
            $taxaMulta = $this->input->post('taxa_multa') ? str_replace(',', '.', $this->input->post('taxa_multa')) : 0;
            $dataPrimeiroVencimento = $this->input->post('data_primeiro_vencimento');

            try {
                $vencimento = explode('/', $vencimento);
                $vencimento = $vencimento[2] . '-' . $vencimento[1] . '-' . $vencimento[0];

                if ($recebimento != null) {
                    $recebimento = explode('/', $recebimento);
                    $recebimento = $recebimento[2] . '-' . $recebimento[1] . '-' . $recebimento[0];
                }

                if ($dataPrimeiroVencimento) {
                    $dataPrimeiroVencimento = explode('/', $dataPrimeiroVencimento);
                    $dataPrimeiroVencimento = $dataPrimeiroVencimento[2] . '-' . $dataPrimeiroVencimento[1] . '-' . $dataPrimeiroVencimento[0];
                }
            } catch (Exception $e) {
                $vencimento = date('Y-m-d');
                if (!$dataPrimeiroVencimento) {
                    $dataPrimeiroVencimento = date('Y-m-d', strtotime('+' . $intervaloParcelas . ' days'));
                }
            }

            $vendas = $this->vendas_model->getById($venda_id);

            $valorTotal = getAmount($this->input->post('valor'));
            $tipoDesconto = $vendas->tipo_desconto;
            $valorDesconto = $vendas->desconto;

            if ($tipoDesconto == 'percentual') {
                $valorDesconto = $valorTotal * ($valorDesconto / 100);
            } elseif ($tipoDesconto == 'real') {
                $valorDesconto = $valorDesconto;
            } else {
                $valorDesconto = 0;
            }

            $valorDesconto = min($valorTotal, $valorDesconto);
            $valorComDesconto = $valorTotal - $valorDesconto;

            $this->db->trans_start();

            // Se for venda à vista
            if ($tipoVenda == 'avista' || $numeroParcelas <= 1) {
                $data = [
                    'vendas_id' => $venda_id,
                    'descricao' => set_value('descricao'),
                    'valor' => $valorTotal,
                    'desconto' => $vendas->desconto,
                    'tipo_desconto' => 'real',
                    'valor_desconto' => $valorComDesconto,
                    'clientes_id' => $this->input->post('clientes_id'),
                    'data_vencimento' => $vencimento,
                    'data_pagamento' => $recebimento,
                    'baixado' => $this->input->post('recebido') == 1 ? true : false,
                    'cliente_fornecedor' => set_value('cliente'),
                    'forma_pgto' => $this->input->post('formaPgto'),
                    'tipo' => 'receita',
                    'usuarios_id' => $this->session->userdata('id_admin'),
                ];

                $this->db->insert('lancamentos', $data);
                $idLancamentos = $this->db->insert_id();

                if ($idLancamentos) {
                    $updateData = [
                        'faturado' => 1,
                        'valorTotal' => $valorTotal,
                        'desconto' => $vendas->desconto,
                        'valor_desconto' => $valorComDesconto,
                        'lancamentos_id' => $idLancamentos,
                        'status' => 'Faturado',
                        'tipo_venda' => 'avista',
                        'numero_parcelas' => 1,
                        'valor_pago_total' => $valorComDesconto,
                        'valor_pendente' => 0
                    ];
                }
            } else {
                // Venda a prazo - criar parcelas
                if (!$dataPrimeiroVencimento) {
                    $dataPrimeiroVencimento = date('Y-m-d', strtotime('+' . $intervaloParcelas . ' days'));
                }

                $updateData = [
                    'faturado' => 1,
                    'valorTotal' => $valorTotal,
                    'desconto' => $vendas->desconto,
                    'valor_desconto' => $valorComDesconto,
                    'status' => 'Faturado',
                    'tipo_venda' => 'aprazo',
                    'numero_parcelas' => $numeroParcelas,
                    'intervalo_parcelas' => $intervaloParcelas,
                    'taxa_juros' => $taxaJuros,
                    'taxa_multa' => $taxaMulta,
                    'data_primeiro_vencimento' => $dataPrimeiroVencimento,
                    'notificar_atraso' => 1,
                    'dias_antes_notificar' => 3
                ];

                // Criar parcelas
                $parcelasCriadas = $this->vendas_prazo_model->criarParcelas(
                    $venda_id,
                    $valorComDesconto,
                    $numeroParcelas,
                    $intervaloParcelas,
                    $dataPrimeiroVencimento,
                    $taxaJuros
                );

                if (!$parcelasCriadas) {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('error', 'Erro ao criar parcelas da venda.');
                    $json = ['result' => false, 'message' => 'Erro ao criar parcelas'];
                    echo json_encode($json);
                    exit();
                }
            }

            // Atualizar venda
            $this->db->where('idVendas', $venda_id);
            $this->db->update('vendas', $updateData);

            log_info('Faturou a venda com ID: ' . $venda_id . ' - Tipo: ' . ($tipoVenda == 'aprazo' ? 'A Prazo' : 'À Vista'));

            $this->db->trans_complete();

            if ($this->db->trans_status() === false) {
                $this->session->set_flashdata('error', 'Ocorreu um erro ao tentar faturar venda.');
                $json = ['result' => false, 'message' => 'Erro na transação'];
            } else {
                if ($tipoVenda == 'aprazo') {
                    $this->session->set_flashdata('success', 'Venda faturada a prazo com sucesso! ' . $numeroParcelas . ' parcela(s) criada(s).');
                } else {
                    $this->session->set_flashdata('success', 'Venda faturada com sucesso!');
                }
                $json = ['result' => true, 'message' => 'Venda faturada com sucesso'];
            }

            echo json_encode($json);
            exit();
        }

        $this->session->set_flashdata('error', 'Ocorreu um erro ao tentar faturar venda.');
        $json = ['result' => false, 'message' => 'Erro de validação'];
        echo json_encode($json);
    }

    public function validarCPF($cpf)
    {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        if (strlen($cpf) !== 11 || preg_match('/^(\d)\1+$/', $cpf)) {
            return false;
        }
        $soma1 = 0;
        for ($i = 0; $i < 9; $i++) {
            $soma1 += $cpf[$i] * (10 - $i);
        }
        $resto1 = $soma1 % 11;
        $dv1 = ($resto1 < 2) ? 0 : 11 - $resto1;
        if ($dv1 != $cpf[9]) {
            return false;
        }
        $soma2 = 0;
        for ($i = 0; $i < 10; $i++) {
            $soma2 += $cpf[$i] * (11 - $i);
        }
        $resto2 = $soma2 % 11;
        $dv2 = ($resto2 < 2) ? 0 : 11 - $resto2;
    
        return $dv2 == $cpf[10];
    }
    
    public function validarCNPJ($cnpj)
    {
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
        if (strlen($cnpj) !== 14 || preg_match('/^(\d)\1+$/', $cnpj)) {
            return false;
        }
        $soma1 = 0;
        for ($i = 0, $pos = 5; $i < 12; $i++, $pos--) {
            $pos = ($pos < 2) ? 9 : $pos;
            $soma1 += $cnpj[$i] * $pos;
        }
        $dv1 = ($soma1 % 11 < 2) ? 0 : 11 - ($soma1 % 11);
        if ($dv1 != $cnpj[12]) {
            return false;
        }
        $soma2 = 0;
        for ($i = 0, $pos = 6; $i < 13; $i++, $pos--) {
            $pos = ($pos < 2) ? 9 : $pos;
            $soma2 += $cnpj[$i] * $pos;
        }
        $dv2 = ($soma2 % 11 < 2) ? 0 : 11 - ($soma2 % 11);
    
        return $dv2 == $cnpj[13];
    }
    
    public function formatarChave($chave)
    {
        if ($this->validarCPF($chave)) {
            return substr($chave, 0, 3) . '.' . substr($chave, 3, 3) . '.' . substr($chave, 6, 3) . '-' . substr($chave, 9);
        } elseif ($this->validarCNPJ($chave)) {
            return substr($chave, 0, 2) . '.' . substr($chave, 2, 3) . '.' . substr($chave, 5, 3) . '/' . substr($chave, 8, 4) . '-' . substr($chave, 12);
        } elseif (strlen($chave) === 11) {
            return '(' . substr($chave, 0, 2) . ') ' . substr($chave, 2, 5) . '-' . substr($chave, 7);
        }
        return $chave;
    }

    public function visualizarVenda($id)
    {
        $venda = $this->Vendas_model->getById($id);
        $produtos = $this->Vendas_model->getProdutos($id);
        $total = $this->Vendas_model->getTotalVendas($id);
        
        $data['venda'] = $venda;
        $data['produtos'] = $produtos;
        $data['total'] = $total;

        $this->load->view('vendas/vendas', $data);
    }

    /**
     * ============================================
     * MÉTODOS DO PDV (Ponto de Venda)
     * ============================================
     */

    /**
     * Tela principal do PDV
     */
    public function pdv()
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
            redirect('vendas/abrirCaixa');
        }

        // Buscar produtos ativos
        $this->data['produtos'] = $this->pdv_model->buscarProdutos('', 100);
        
        // Buscar formas de pagamento ativas
        if ($this->db->table_exists('formas_pagamento')) {
            $this->data['formas_pagamento'] = $this->db->where('ativo', 1)
                ->order_by('ordem', 'ASC')
                ->get('formas_pagamento')
                ->result();
        } else {
            $this->data['formas_pagamento'] = [];
        }
        
        // Cliente consumidor final
        $this->data['cliente_consumidor_final'] = $this->pdv_model->getClienteConsumidorFinal();
        
        // Caixa atual
        $this->data['caixa_aberto'] = $caixaAberto;
        
        $this->data['view'] = 'pdv/index';
        return $this->layout();
    }

    /**
     * Buscar produtos para PDV (AJAX)
     */
    public function pdvBuscarProdutos()
    {
        $termo = $this->input->get('termo');
        $produtos = $this->pdv_model->buscarProdutos($termo, 50);
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['success' => true, 'produtos' => $produtos]));
    }

    /**
     * Buscar produto por código de barras para PDV (AJAX)
     */
    public function pdvBuscarProdutoCodigoBarras()
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
     * Criar venda rápida no PDV (AJAX)
     */
    public function pdvCriarVenda()
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
     * Adicionar produto à venda no PDV (AJAX)
     */
    public function pdvAdicionarProduto()
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
     * Remover produto da venda no PDV (AJAX)
     */
    public function pdvRemoverProduto()
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
     * Finalizar venda no PDV (AJAX)
     */
    public function pdvFinalizarVenda()
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
     * Cancelar venda no PDV (AJAX)
     */
    public function pdvCancelarVenda()
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
     * Abertura de caixa para PDV
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
            redirect('vendas/pdv');
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
                    redirect('vendas/pdv');
                } else {
                    $this->data['custom_error'] = $result['message'];
                }
            }
        }

        // Buscar caixas ativos
        if ($this->db->table_exists('caixas')) {
            $query = $this->db->where('ativo', 1)->get('caixas');
            if ($query !== false) {
                $this->data['caixas'] = $query->result();
            } else {
                $this->data['caixas'] = [];
            }
        } else {
            $this->data['caixas'] = [];
            $this->data['custom_error'] = '<div class="alert alert-error">As tabelas do PDV não foram criadas. Por favor, execute as migrations primeiro através de <a href="' . base_url('index.php/mapos/configurar') . '">Configurações > Atualizar Banco de Dados</a>.</div>';
        }

        $this->data['view'] = 'pdv/abrir_caixa';
        return $this->layout();
    }

    /**
     * Fechamento de caixa do PDV
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
            redirect('vendas/pdv');
        }

        if ($this->input->post()) {
            $valorFechamento = str_replace(',', '.', str_replace('.', '', $this->input->post('valor_fechamento')));
            $observacoes = $this->input->post('observacoes');

            $result = $this->pdv_model->fecharCaixa($caixaAberto->idTurno, $valorFechamento, $observacoes);

            if ($result['success']) {
                $this->session->set_flashdata('success', 'Caixa fechado com sucesso!');
                redirect('vendas/relatorioFechamento/' . $caixaAberto->idTurno);
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
     * Relatório de fechamento do PDV
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
            redirect('vendas/pdv');
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
     * Dashboard de vendas do PDV
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
