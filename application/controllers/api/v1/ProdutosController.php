<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class ProdutosController extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('produtos_model');
        $this->load->helper('validation_helper');
    }

    public function index_get($id = '')
    {
        $this->logged_user();
        if (! $this->permission->checkPermission($this->logged_user()->level, 'vProduto')) {
            $this->response([
                'status' => false,
                'message' => 'Você não está autorizado a Visualizar Produtos',
            ], REST_Controller::HTTP_UNAUTHORIZED);
        }

        if (! $id) {
            $search = trim($this->get('search', true));
            // Verificar se campo nome existe
            $fields = $this->db->list_fields('produtos');
            $nomeCondition = in_array('nome', $fields) ? " OR nome LIKE '%{$search}%'" : '';
            $where = $search ? "codDeBarra LIKE '%{$search}%' OR descricao LIKE '%{$search}%'{$nomeCondition}" : '';

            $perPage = $this->get('perPage', true) ?: 20;
            $page = $this->get('page', true) ?: 0;
            $start = $page ? ($perPage * $page) : 0;

            $produtos = $this->produtos_model->get('produtos', '*', $where, $perPage, $start);

            $this->response([
                'status' => true,
                'message' => 'Listando Produtos',
                'result' => $produtos,
            ], REST_Controller::HTTP_OK);
        }

        if ($id && is_numeric($id)) {
            $produto = $this->produtos_model->getById($id);

            $this->response([
                'status' => true,
                'message' => 'Detalhes do Produto',
                'result' => $produto,
            ], REST_Controller::HTTP_OK);
        }

        $this->response([
            'status' => false,
            'message' => 'Nenhum Produto localizado.',
            'result' => null,
        ], REST_Controller::HTTP_OK);
    }

    public function index_post()
    {
        $this->logged_user();
        if (! $this->permission->checkPermission($this->logged_user()->level, 'aProduto')) {
            $this->response([
                'status' => false,
                'message' => 'Você não está autorizado a Adicionar Produtos!',
            ], REST_Controller::HTTP_UNAUTHORIZED);
        }

        $_POST = (array) json_decode(file_get_contents('php://input'), true);

        $this->load->library('form_validation');

        if ($this->form_validation->run('produtos') == false) {
            $this->response([
                'status' => false,
                'message' => validation_errors(),
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $precoCompra = $this->post('precoCompra', true);
        $precoCompra = str_replace(',', '', $precoCompra);
        $precoVenda = $this->post('precoVenda', true);
        $precoVenda = str_replace(',', '', $precoVenda);
        $nome = $this->post('nome', true);
        $descricao = $this->post('descricao', true);
        // Se descrição estiver vazia, usar o nome (limitado a 80 caracteres)
        if (empty($descricao) && !empty($nome)) {
            $descricao = mb_substr($nome, 0, 80);
        }
        // Se nome estiver vazio, usar a descrição
        if (empty($nome) && !empty($descricao)) {
            $nome = $descricao;
        }
        
        $data = [
            'codDeBarra' => $this->post('codDeBarra', true),
            'nome' => $nome,
            'descricao' => $descricao,
            'descricao_completa' => $this->post('descricao_completa', true),
            'unidade' => $this->post('unidade', true),
            'precoCompra' => $precoCompra,
            'precoVenda' => $precoVenda,
            'estoque' => $this->post('estoque', true),
            'estoqueMinimo' => $this->post('estoqueMinimo', true) ? $this->post('estoqueMinimo', true) : 0,
            'saida' => $this->post('saida', true) ? $this->post('saida', true) : 0,
            'entrada' => $this->post('entrada', true) ? $this->post('entrada', true) : 0,
        ];

        if ($this->produtos_model->add('produtos', $data)) {
            $this->response([
                'status' => true,
                'message' => 'Produto adicionado com sucesso!',
                'result' => $this->produtos_model->get('produtos', '*', "descricao = '{$data['descricao']}'", 1, 0, true),
            ], REST_Controller::HTTP_CREATED);
        }

        $this->response([
            'status' => false,
            'message' => 'Não foi possível adicionar o Produto. Avise ao Administrador.',
        ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function index_put($id)
    {
        $this->logged_user();

        if (! $this->permission->checkPermission($this->logged_user()->level, 'eProduto')) {
            $this->response([
                'status' => false,
                'message' => 'Você não está autorizado a Editar Produtos!',
            ], REST_Controller::HTTP_UNAUTHORIZED);
        }

        if (! $id) {
            $this->response([
                'status' => false,
                'message' => 'Informe o ID do produto!',
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        if (! $this->put('descricao', true) ||
        ! $this->put('unidade', true) ||
        ! $this->put('precoCompra', true) ||
        ! $this->put('precoVenda', true) ||
        ! $this->put('estoque', true)) {
            $this->response([
                'status' => false,
                'message' => 'Preencha todos os campos obrigatórios!',
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $precoCompra = $this->put('precoCompra', true);
        $precoCompra = str_replace(',', '', $precoCompra);
        $precoVenda = $this->put('precoVenda', true);
        $precoVenda = str_replace(',', '', $precoVenda);
        
        $nome = $this->put('nome', true);
        $descricao = $this->put('descricao', true);
        // Se descrição estiver vazia, usar o nome (limitado a 80 caracteres)
        if (empty($descricao) && !empty($nome)) {
            $descricao = mb_substr($nome, 0, 80);
        }
        // Se nome estiver vazio, usar a descrição
        if (empty($nome) && !empty($descricao)) {
            $nome = $descricao;
        }
        
        $data = [
            'codDeBarra' => $this->put('codDeBarra', true) ?: 0,
            'nome' => $nome,
            'descricao' => $descricao,
            'descricao_completa' => $this->put('descricao_completa', true),
            'unidade' => $this->put('unidade', true),
            'precoCompra' => $precoCompra,
            'precoVenda' => $precoVenda,
            'estoque' => $this->put('estoque', true),
            'estoqueMinimo' => $this->put('estoqueMinimo', true) ?: 0,
            'saida' => $this->put('saida', true) ?: 0,
            'entrada' => $this->put('entrada', true) ?: 0
        ];

        if ($this->produtos_model->edit('produtos', $data, 'idProdutos', $id)) {
            $this->response([
                'status' => true,
                'message' => 'Produto editado com sucesso!',
                'result' => $this->produtos_model->getById($id),
            ], REST_Controller::HTTP_OK);
        }

        $this->response([
            'status' => false,
            'message' => 'Não foi possível editar o Produto. Avise ao Administrador.',
        ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function index_delete($id)
    {
        $this->logged_user();
        if (! $this->permission->checkPermission($this->logged_user()->level, 'dProduto')) {
            $this->response([
                'status' => false,
                'message' => 'Você não está autorizado a Apagar Produtos!',
            ], REST_Controller::HTTP_UNAUTHORIZED);
        }

        if (! $id) {
            $this->response([
                'status' => false,
                'message' => 'Informe o ID do Produto!',
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $this->produtos_model->delete('produtos_os', 'produtos_id', $id);
        $this->produtos_model->delete('itens_de_vendas', 'produtos_id', $id);

        if ($this->produtos_model->delete('produtos', 'idProdutos', $id)) {
            $this->log_app('Removeu um Produto. ID' . $id);
            $this->response([
                'status' => true,
                'message' => 'Produto excluído com sucesso!',
            ], REST_Controller::HTTP_OK);
        }

        $this->response([
            'status' => false,
            'message' => 'Não foi possível excluir o Produto. Avise ao Administrador.',
        ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
    }
}
