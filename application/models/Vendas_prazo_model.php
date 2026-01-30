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
        // Verificar se a tabela existe
        if (!$this->db->table_exists('parcelas_venda')) {
            log_message('error', 'Tabela parcelas_venda não existe no banco de dados');
            return [];
        }

        try {
            $this->db->select('parcelas_venda.*, formas_pagamento.nome as forma_pagamento_nome, usuarios.nome as usuario_nome');
            $this->db->from('parcelas_venda');
            $this->db->join('formas_pagamento', 'formas_pagamento.idFormaPagamento = parcelas_venda.formas_pagamento_id', 'left');
            $this->db->join('usuarios', 'usuarios.idUsuarios = parcelas_venda.usuarios_id', 'left');
            $this->db->where('vendas_id', $vendaId);
            $this->db->order_by('numero_parcela', 'ASC');

            $query = $this->db->get();
            
            // Verificar se houve erro na query
            $error = $this->db->error();
            if ($error['code'] != 0) {
                log_message('error', 'Erro na query getParcelas: ' . $error['message']);
                return [];
            }
            
            return $query ? $query->result() : [];
        } catch (Exception $e) {
            log_message('error', 'Exceção em getParcelas: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Obter parcela por ID
     */
    public function getParcelaById($parcelaId)
    {
        // Verificar se a tabela existe
        if (!$this->db->table_exists('parcelas_venda')) {
            log_message('error', 'Tabela parcelas_venda não existe no banco de dados');
            return null;
        }

        try {
            $this->db->select('parcelas_venda.*, vendas.*, clientes.nomeCliente, clientes.telefone, clientes.email');
            $this->db->from('parcelas_venda');
            $this->db->join('vendas', 'vendas.idVendas = parcelas_venda.vendas_id');
            $this->db->join('clientes', 'clientes.idClientes = vendas.clientes_id');
            $this->db->where('parcelas_venda.idParcela', $parcelaId);
            $this->db->limit(1);

            $query = $this->db->get();
            
            // Verificar se houve erro na query
            $error = $this->db->error();
            if ($error['code'] != 0) {
                log_message('error', 'Erro na query getParcelaById: ' . $error['message']);
                return null;
            }
            
            return $query ? $query->row() : null;
        } catch (Exception $e) {
            log_message('error', 'Exceção em getParcelaById: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Registrar pagamento de parcela
     */
    public function registrarPagamento($parcelaId, $valorPago, $dataPagamento = null, $formaPagamentoId = null, $usuarioId = null, $observacoes = null, $desconto = 0)
    {
        // Verificar se a tabela existe
        if (!$this->db->table_exists('parcelas_venda')) {
            log_message('error', 'Tabela parcelas_venda não existe no banco de dados');
            return false;
        }

        try {
            if ($dataPagamento === null) {
                $dataPagamento = date('Y-m-d');
            }

            $query = $this->db->get_where('parcelas_venda', ['idParcela' => $parcelaId]);
            
            // Verificar se houve erro na query
            $error = $this->db->error();
            if ($error['code'] != 0) {
                log_message('error', 'Erro na query registrarPagamento: ' . $error['message']);
                return false;
            }
            
            $parcela = $query ? $query->row() : null;
            
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
        } catch (Exception $e) {
            log_message('error', 'Exceção em registrarPagamento: ' . $e->getMessage());
            if ($this->db->trans_status() !== false) {
                $this->db->trans_rollback();
            }
            return false;
        }
    }

    /**
     * Atualizar valores totais da venda
     */
    public function atualizarValoresVenda($vendaId)
    {
        // Verificar se a tabela vendas existe
        if (!$this->db->table_exists('vendas')) {
            log_message('error', 'Tabela vendas não existe no banco de dados');
            return;
        }

        try {
            $parcelas = $this->getParcelas($vendaId);
            
            $valorPagoTotal = 0;
            $valorPendente = 0;
            
            foreach ($parcelas as $parcela) {
                $valorPagoTotal += $parcela->valor_pago;
                if ($parcela->status !== 'paga') {
                    $valorPendente += ($parcela->valor_total - $parcela->valor_pago);
                }
            }

            $updateData = [];
            
            // Verificar se as colunas existem antes de atualizar
            if ($this->db->field_exists('valor_pago_total', 'vendas')) {
                $updateData['valor_pago_total'] = $valorPagoTotal;
            }
            
            if ($this->db->field_exists('valor_pendente', 'vendas')) {
                $updateData['valor_pendente'] = $valorPendente;
            }
            
            if (!empty($updateData)) {
                $this->db->where('idVendas', $vendaId);
                $this->db->update('vendas', $updateData);
                
                // Verificar se houve erro na query
                $error = $this->db->error();
                if ($error['code'] != 0) {
                    log_message('error', 'Erro na query atualizarValoresVenda: ' . $error['message']);
                }
            }
        } catch (Exception $e) {
            log_message('error', 'Exceção em atualizarValoresVenda: ' . $e->getMessage());
        }
    }

    /**
     * Atualizar status de parcelas atrasadas
     */
    public function atualizarParcelasAtrasadas()
    {
        // Verificar se a tabela existe
        if (!$this->db->table_exists('parcelas_venda')) {
            log_message('error', 'Tabela parcelas_venda não existe no banco de dados');
            return 0;
        }

        try {
            $hoje = date('Y-m-d');
            
            // Buscar parcelas pendentes com vencimento passado
            $this->db->where('status', 'pendente');
            $this->db->where('data_vencimento <', $hoje);
            $query = $this->db->get('parcelas_venda');
            
            // Verificar se houve erro na query
            $error = $this->db->error();
            if ($error['code'] != 0) {
                log_message('error', 'Erro na query atualizarParcelasAtrasadas: ' . $error['message']);
                return 0;
            }
            
            $parcelas = $query ? $query->result() : [];

            foreach ($parcelas as $parcela) {
                $diasAtraso = (strtotime($hoje) - strtotime($parcela->data_vencimento)) / 86400;
                
                // Buscar configurações da venda para calcular multa e juros
                $vendaQuery = $this->db->get_where('vendas', ['idVendas' => $parcela->vendas_id]);
                $venda = $vendaQuery ? $vendaQuery->row() : null;
                
                $multa = 0;
                $juros = 0;
                
                if ($venda && isset($venda->taxa_multa) && $venda->taxa_multa > 0) {
                    $multa = $parcela->valor_parcela * ($venda->taxa_multa / 100);
                }
                
                if ($venda && isset($venda->taxa_juros) && $venda->taxa_juros > 0) {
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
        } catch (Exception $e) {
            log_message('error', 'Exceção em atualizarParcelasAtrasadas: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Criar notificação
     */
    public function criarNotificacao($vendaId, $parcelaId = null, $tipo, $titulo, $mensagem, $prioridade = 'media', $usuarioId = null)
    {
        // Verificar se a tabela existe
        if (!$this->db->table_exists('notificacoes_venda')) {
            log_message('error', 'Tabela notificacoes_venda não existe no banco de dados');
            return false;
        }

        try {
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

            $result = $this->db->insert('notificacoes_venda', $data);
            
            // Verificar se houve erro na query
            $error = $this->db->error();
            if ($error['code'] != 0) {
                log_message('error', 'Erro na query criarNotificacao: ' . $error['message']);
                return false;
            }
            
            return $result;
        } catch (Exception $e) {
            log_message('error', 'Exceção em criarNotificacao: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Obter notificações
     */
    public function getNotificacoes($usuarioId = null, $lidas = null, $tipo = null, $limit = null)
    {
        // Verificar se a tabela existe
        if (!$this->db->table_exists('notificacoes_venda')) {
            log_message('error', 'Tabela notificacoes_venda não existe no banco de dados');
            return [];
        }

        try {
            $this->db->select('notificacoes_venda.*, vendas.idVendas, clientes.nomeCliente, parcelas_venda.numero_parcela');
            $this->db->from('notificacoes_venda');
            $this->db->join('vendas', 'vendas.idVendas = notificacoes_venda.vendas_id', 'inner');
            $this->db->join('clientes', 'clientes.idClientes = vendas.clientes_id', 'inner');
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

            $query = $this->db->get();
            
            // Verificar se houve erro na query
            $error = $this->db->error();
            if ($error['code'] != 0) {
                log_message('error', 'Erro na query getNotificacoes: ' . $error['message']);
                return [];
            }
            
            return $query ? $query->result() : [];
        } catch (Exception $e) {
            log_message('error', 'Exceção em getNotificacoes: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Marcar notificação como lida
     */
    public function marcarNotificacaoLida($notificacaoId, $usuarioId = null)
    {
        // Verificar se a tabela existe
        if (!$this->db->table_exists('notificacoes_venda')) {
            log_message('error', 'Tabela notificacoes_venda não existe no banco de dados');
            return false;
        }

        try {
            $data = [
                'lida' => 1,
                'data_leitura' => date('Y-m-d H:i:s')
            ];

            $this->db->where('idNotificacao', $notificacaoId);
            if ($usuarioId !== null) {
                $this->db->where('(usuarios_id IS NULL OR usuarios_id = ' . $usuarioId . ')');
            }
            
            $result = $this->db->update('notificacoes_venda', $data);
            
            // Verificar se houve erro na query
            $error = $this->db->error();
            if ($error['code'] != 0) {
                log_message('error', 'Erro na query marcarNotificacaoLida: ' . $error['message']);
                return false;
            }
            
            return $result;
        } catch (Exception $e) {
            log_message('error', 'Exceção em marcarNotificacaoLida: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Contar notificações não lidas
     */
    public function countNotificacoesNaoLidas($usuarioId = null)
    {
        // Verificar se a tabela existe
        if (!$this->db->table_exists('notificacoes_venda')) {
            log_message('error', 'Tabela notificacoes_venda não existe no banco de dados');
            return 0;
        }

        try {
            $this->db->where('lida', 0);
            if ($usuarioId !== null) {
                $this->db->where('(usuarios_id IS NULL OR usuarios_id = ' . $usuarioId . ')');
            }
            
            $count = $this->db->count_all_results('notificacoes_venda');
            
            // Verificar se houve erro na query
            $error = $this->db->error();
            if ($error['code'] != 0) {
                log_message('error', 'Erro na query countNotificacoesNaoLidas: ' . $error['message']);
                return 0;
            }
            
            return $count;
        } catch (Exception $e) {
            log_message('error', 'Exceção em countNotificacoesNaoLidas: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Buscar vendas a prazo com filtros
     */
    public function buscarVendasPrazo($filtros = [])
    {
        // Verificar se a tabela vendas existe
        if (!$this->db->table_exists('vendas')) {
            log_message('error', 'Tabela vendas não existe no banco de dados');
            return [];
        }

        // Verificar se a coluna tipo_venda existe
        if (!$this->db->field_exists('tipo_venda', 'vendas')) {
            log_message('error', 'Coluna tipo_venda não existe na tabela vendas. Execute a migration.');
            return [];
        }

        try {
            $this->db->select('vendas.*, clientes.nomeCliente, clientes.telefone, clientes.email, usuarios.nome as vendedor_nome');
            $this->db->from('vendas');
            $this->db->join('clientes', 'clientes.idClientes = vendas.clientes_id');
            $this->db->join('usuarios', 'usuarios.idUsuarios = vendas.usuarios_id', 'left');
            $this->db->where('vendas.tipo_venda', 'aprazo');

            // Filtros
            if (isset($filtros['status_parcela'])) {
                if ($filtros['status_parcela'] === 'atrasadas') {
                    if ($this->db->field_exists('valor_pendente', 'vendas')) {
                        $this->db->where('vendas.valor_pendente >', 0);
                    }
                    if ($this->db->table_exists('parcelas_venda')) {
                        $this->db->where('EXISTS (SELECT 1 FROM parcelas_venda WHERE parcelas_venda.vendas_id = vendas.idVendas AND parcelas_venda.status = "atrasada")');
                    }
                } elseif ($filtros['status_parcela'] === 'pendentes') {
                    if ($this->db->field_exists('valor_pendente', 'vendas')) {
                        $this->db->where('vendas.valor_pendente >', 0);
                    }
                } elseif ($filtros['status_parcela'] === 'pagas') {
                    if ($this->db->field_exists('valor_pendente', 'vendas')) {
                        $this->db->where('vendas.valor_pendente', 0);
                    }
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
                if ($this->db->field_exists('data_primeiro_vencimento', 'vendas')) {
                    $this->db->where('vendas.data_primeiro_vencimento >=', $filtros['vencimento_inicio']);
                }
            }

            if (isset($filtros['vencimento_fim'])) {
                if ($this->db->field_exists('data_primeiro_vencimento', 'vendas')) {
                    $this->db->where('vendas.data_primeiro_vencimento <=', $filtros['vencimento_fim']);
                }
            }

            if (isset($filtros['valor_minimo'])) {
                $this->db->where('vendas.valorTotal >=', $filtros['valor_minimo']);
            }

            if (isset($filtros['valor_maximo'])) {
                $this->db->where('vendas.valorTotal <=', $filtros['valor_maximo']);
            }

            $this->db->order_by('vendas.dataVenda', 'DESC');

            $query = $this->db->get();
            
            // Verificar se houve erro na query
            $error = $this->db->error();
            if ($error['code'] != 0) {
                log_message('error', 'Erro na query buscarVendasPrazo: ' . $error['message']);
                return [];
            }
            
            return $query ? $query->result() : [];
        } catch (Exception $e) {
            log_message('error', 'Exceção em buscarVendasPrazo: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Obter estatísticas de vendas a prazo
     */
    public function getEstatisticas($dataInicio = null, $dataFim = null)
    {
        // Verificar se a tabela vendas existe e se tem as colunas necessárias
        if (!$this->db->table_exists('vendas')) {
            log_message('error', 'Tabela vendas não existe no banco de dados');
            return $this->getEstatisticasVazias();
        }

        // Verificar se a coluna tipo_venda existe
        if (!$this->db->field_exists('tipo_venda', 'vendas')) {
            log_message('error', 'Coluna tipo_venda não existe na tabela vendas. Execute a migration.');
            return $this->getEstatisticasVazias();
        }

        try {
            if ($dataInicio === null) {
                $dataInicio = date('Y-m-01'); // Primeiro dia do mês
            }
            if ($dataFim === null) {
                $dataFim = date('Y-m-t'); // Último dia do mês
            }

            // Verificar se as colunas existem antes de usar
            $colunasExistentes = [];
            $campos = $this->db->list_fields('vendas');
            
            $select = 'COUNT(*) as total_vendas, SUM(valorTotal) as valor_total_vendas';
            
            if (in_array('valor_pago_total', $campos)) {
                $select .= ', SUM(valor_pago_total) as valor_pago';
            } else {
                $select .= ', 0 as valor_pago';
            }
            
            if (in_array('valor_pendente', $campos)) {
                $select .= ', SUM(valor_pendente) as valor_pendente';
                $select .= ', COUNT(CASE WHEN valor_pendente > 0 THEN 1 END) as vendas_pendentes';
            } else {
                $select .= ', 0 as valor_pendente, 0 as vendas_pendentes';
            }
            
            // Verificar se a tabela parcelas_venda existe para a subquery
            if ($this->db->table_exists('parcelas_venda')) {
                $select .= ', COUNT(CASE WHEN EXISTS (SELECT 1 FROM parcelas_venda WHERE parcelas_venda.vendas_id = vendas.idVendas AND parcelas_venda.status = "atrasada") THEN 1 END) as vendas_atrasadas';
            } else {
                $select .= ', 0 as vendas_atrasadas';
            }

            $this->db->select($select);
            $this->db->from('vendas');
            $this->db->where('tipo_venda', 'aprazo');
            $this->db->where('dataVenda >=', $dataInicio);
            $this->db->where('dataVenda <=', $dataFim);

            $query = $this->db->get();
            
            // Verificar se houve erro na query
            $error = $this->db->error();
            if ($error['code'] != 0) {
                log_message('error', 'Erro na query getEstatisticas: ' . $error['message']);
                return $this->getEstatisticasVazias();
            }
            
            return $query ? $query->row() : $this->getEstatisticasVazias();
        } catch (Exception $e) {
            log_message('error', 'Exceção em getEstatisticas: ' . $e->getMessage());
            return $this->getEstatisticasVazias();
        }
    }

    /**
     * Retornar estatísticas vazias quando há erro
     */
    private function getEstatisticasVazias()
    {
        return (object) [
            'total_vendas' => 0,
            'valor_total_vendas' => 0,
            'valor_pago' => 0,
            'valor_pendente' => 0,
            'vendas_pendentes' => 0,
            'vendas_atrasadas' => 0
        ];
    }

    /**
     * Obter histórico de pagamentos de uma parcela
     */
    public function getHistoricoPagamentos($parcelaId)
    {
        // Verificar se a tabela existe
        if (!$this->db->table_exists('historico_pagamentos')) {
            log_message('error', 'Tabela historico_pagamentos não existe no banco de dados');
            return [];
        }

        try {
            $this->db->select('historico_pagamentos.*, formas_pagamento.nome as forma_pagamento_nome, usuarios.nome as usuario_nome');
            $this->db->from('historico_pagamentos');
            $this->db->join('formas_pagamento', 'formas_pagamento.idFormaPagamento = historico_pagamentos.formas_pagamento_id', 'left');
            $this->db->join('usuarios', 'usuarios.idUsuarios = historico_pagamentos.usuarios_id', 'left');
            $this->db->where('historico_pagamentos.parcelas_venda_id', $parcelaId);
            $this->db->order_by('historico_pagamentos.data_pagamento', 'DESC');

            $query = $this->db->get();
            
            // Verificar se houve erro na query
            $error = $this->db->error();
            if ($error['code'] != 0) {
                log_message('error', 'Erro na query getHistoricoPagamentos: ' . $error['message']);
                return [];
            }
            
            return $query ? $query->result() : [];
        } catch (Exception $e) {
            log_message('error', 'Exceção em getHistoricoPagamentos: ' . $e->getMessage());
            return [];
        }
    }
}
