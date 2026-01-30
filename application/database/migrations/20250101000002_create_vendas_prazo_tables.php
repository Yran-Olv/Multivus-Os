<?php
/**
 * Migration: Sistema de Vendas a Prazo
 * 
 * Tabelas criadas:
 * - parcelas_venda: Parcelas das vendas a prazo
 * - notificacoes_venda: Notificações de vendas em atraso
 * - historico_pagamentos: Histórico de pagamentos das parcelas
 */

class Migration_create_vendas_prazo_tables extends CI_Migration
{
    public function up()
    {
        // ============================================
        // Tabela: parcelas_venda
        // ============================================
        $this->dbforge->add_field([
            'idParcela' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'vendas_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false
            ],
            'numero_parcela' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
                'comment' => 'Número da parcela (1, 2, 3, ...)'
            ],
            'valor_parcela' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => false,
                'comment' => 'Valor da parcela'
            ],
            'valor_pago' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
                'comment' => 'Valor já pago desta parcela'
            ],
            'data_vencimento' => [
                'type' => 'DATE',
                'null' => false,
                'comment' => 'Data de vencimento da parcela'
            ],
            'data_pagamento' => [
                'type' => 'DATE',
                'null' => true,
                'comment' => 'Data em que foi paga'
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pendente', 'paga', 'atrasada', 'cancelada'],
                'default' => 'pendente',
                'comment' => 'Status da parcela'
            ],
            'dias_atraso' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'comment' => 'Dias em atraso (calculado)'
            ],
            'juros' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
                'comment' => 'Juros aplicados'
            ],
            'multa' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
                'comment' => 'Multa aplicada'
            ],
            'desconto' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
                'comment' => 'Desconto aplicado'
            ],
            'valor_total' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => false,
                'comment' => 'Valor total (parcela + juros + multa - desconto)'
            ],
            'observacoes' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Observações sobre a parcela'
            ],
            'formas_pagamento_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'comment' => 'Forma de pagamento utilizada'
            ],
            'usuarios_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'comment' => 'Usuário que recebeu o pagamento'
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
                'default' => 'CURRENT_TIMESTAMP'
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'on_update' => 'CURRENT_TIMESTAMP'
            ]
        ]);
        $this->dbforge->add_key('idParcela', true);
        $this->dbforge->add_key('vendas_id');
        $this->dbforge->add_key('status');
        $this->dbforge->add_key('data_vencimento');
        $this->dbforge->create_table('parcelas_venda', true);

        // Foreign keys
        $this->db->query('ALTER TABLE `parcelas_venda` 
            ADD CONSTRAINT `fk_parcelas_venda_vendas` 
            FOREIGN KEY (`vendas_id`) REFERENCES `vendas` (`idVendas`) 
            ON DELETE CASCADE ON UPDATE CASCADE');
        
        $this->db->query('ALTER TABLE `parcelas_venda` 
            ADD CONSTRAINT `fk_parcelas_venda_formas_pagamento` 
            FOREIGN KEY (`formas_pagamento_id`) REFERENCES `formas_pagamento` (`idFormaPagamento`) 
            ON DELETE SET NULL ON UPDATE CASCADE');
        
        $this->db->query('ALTER TABLE `parcelas_venda` 
            ADD CONSTRAINT `fk_parcelas_venda_usuarios` 
            FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios` (`idUsuarios`) 
            ON DELETE SET NULL ON UPDATE CASCADE');

        // ============================================
        // Tabela: notificacoes_venda
        // ============================================
        $this->dbforge->add_field([
            'idNotificacao' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'vendas_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false
            ],
            'parcelas_venda_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'comment' => 'Parcela específica (NULL = notificação geral da venda)'
            ],
            'tipo' => [
                'type' => 'ENUM',
                'constraint' => ['atraso', 'vencendo_hoje', 'vencendo_proximo', 'pagamento_recebido', 'outros'],
                'null' => false,
                'comment' => 'Tipo de notificação'
            ],
            'titulo' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
                'comment' => 'Título da notificação'
            ],
            'mensagem' => [
                'type' => 'TEXT',
                'null' => false,
                'comment' => 'Mensagem da notificação'
            ],
            'prioridade' => [
                'type' => 'ENUM',
                'constraint' => ['baixa', 'media', 'alta', 'urgente'],
                'default' => 'media',
                'comment' => 'Prioridade da notificação'
            ],
            'lida' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => 'Se a notificação foi lida'
            ],
            'data_leitura' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'Data em que foi lida'
            ],
            'usuarios_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'comment' => 'Usuário que deve receber (NULL = todos)'
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
                'default' => 'CURRENT_TIMESTAMP'
            ]
        ]);
        $this->dbforge->add_key('idNotificacao', true);
        $this->dbforge->add_key('vendas_id');
        $this->dbforge->add_key('parcelas_venda_id');
        $this->dbforge->add_key('lida');
        $this->dbforge->add_key('tipo');
        $this->dbforge->create_table('notificacoes_venda', true);

        // Foreign keys
        $this->db->query('ALTER TABLE `notificacoes_venda` 
            ADD CONSTRAINT `fk_notificacoes_venda_vendas` 
            FOREIGN KEY (`vendas_id`) REFERENCES `vendas` (`idVendas`) 
            ON DELETE CASCADE ON UPDATE CASCADE');
        
        $this->db->query('ALTER TABLE `notificacoes_venda` 
            ADD CONSTRAINT `fk_notificacoes_venda_parcelas` 
            FOREIGN KEY (`parcelas_venda_id`) REFERENCES `parcelas_venda` (`idParcela`) 
            ON DELETE CASCADE ON UPDATE CASCADE');
        
        $this->db->query('ALTER TABLE `notificacoes_venda` 
            ADD CONSTRAINT `fk_notificacoes_venda_usuarios` 
            FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios` (`idUsuarios`) 
            ON DELETE SET NULL ON UPDATE CASCADE');

        // ============================================
        // Tabela: historico_pagamentos
        // ============================================
        $this->dbforge->add_field([
            'idHistorico' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'parcelas_venda_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false
            ],
            'vendas_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false
            ],
            'valor_pago' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => false,
                'comment' => 'Valor pago neste pagamento'
            ],
            'data_pagamento' => [
                'type' => 'DATETIME',
                'null' => false,
                'default' => 'CURRENT_TIMESTAMP',
                'comment' => 'Data do pagamento'
            ],
            'formas_pagamento_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'comment' => 'Forma de pagamento'
            ],
            'usuarios_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'comment' => 'Usuário que recebeu'
            ],
            'observacoes' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Observações do pagamento'
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
                'default' => 'CURRENT_TIMESTAMP'
            ]
        ]);
        $this->dbforge->add_key('idHistorico', true);
        $this->dbforge->add_key('parcelas_venda_id');
        $this->dbforge->add_key('vendas_id');
        $this->dbforge->create_table('historico_pagamentos', true);

        // Foreign keys
        $this->db->query('ALTER TABLE `historico_pagamentos` 
            ADD CONSTRAINT `fk_historico_parcelas_venda` 
            FOREIGN KEY (`parcelas_venda_id`) REFERENCES `parcelas_venda` (`idParcela`) 
            ON DELETE CASCADE ON UPDATE CASCADE');
        
        $this->db->query('ALTER TABLE `historico_pagamentos` 
            ADD CONSTRAINT `fk_historico_vendas` 
            FOREIGN KEY (`vendas_id`) REFERENCES `vendas` (`idVendas`) 
            ON DELETE CASCADE ON UPDATE CASCADE');
        
        $this->db->query('ALTER TABLE `historico_pagamentos` 
            ADD CONSTRAINT `fk_historico_formas_pagamento` 
            FOREIGN KEY (`formas_pagamento_id`) REFERENCES `formas_pagamento` (`idFormaPagamento`) 
            ON DELETE SET NULL ON UPDATE CASCADE');
        
        $this->db->query('ALTER TABLE `historico_pagamentos` 
            ADD CONSTRAINT `fk_historico_usuarios` 
            FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios` (`idUsuarios`) 
            ON DELETE SET NULL ON UPDATE CASCADE');

        // ============================================
        // Modificar tabela: vendas
        // ============================================
        $fields = [
            'tipo_venda' => [
                'type' => 'ENUM',
                'constraint' => ['avista', 'aprazo'],
                'default' => 'avista',
                'after' => 'status',
                'comment' => 'Tipo de venda: à vista ou a prazo'
            ],
            'numero_parcelas' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 1,
                'after' => 'tipo_venda',
                'comment' => 'Número de parcelas (se a prazo)'
            ],
            'intervalo_parcelas' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 30,
                'after' => 'numero_parcelas',
                'comment' => 'Intervalo em dias entre parcelas'
            ],
            'taxa_juros' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'default' => 0.00,
                'after' => 'intervalo_parcelas',
                'comment' => 'Taxa de juros mensal (%)'
            ],
            'taxa_multa' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'default' => 0.00,
                'after' => 'taxa_juros',
                'comment' => 'Taxa de multa por atraso (%)'
            ],
            'valor_total_parcelado' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
                'after' => 'taxa_multa',
                'comment' => 'Valor total com juros (se parcelado)'
            ],
            'valor_pago_total' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
                'after' => 'valor_total_parcelado',
                'comment' => 'Valor total já pago'
            ],
            'valor_pendente' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
                'after' => 'valor_pago_total',
                'comment' => 'Valor ainda pendente'
            ],
            'data_primeiro_vencimento' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'valor_pendente',
                'comment' => 'Data do primeiro vencimento'
            ],
            'notificar_atraso' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
                'after' => 'data_primeiro_vencimento',
                'comment' => 'Se deve notificar sobre atrasos'
            ],
            'dias_antes_notificar' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 3,
                'after' => 'notificar_atraso',
                'comment' => 'Dias antes do vencimento para notificar'
            ]
        ];

        foreach ($fields as $field => $attributes) {
            $this->dbforge->add_column('vendas', [$field => $attributes]);
        }

        // Índices para melhor performance nas consultas
        $this->db->query('CREATE INDEX idx_vendas_tipo_venda ON vendas(tipo_venda)');
        $this->db->query('CREATE INDEX idx_vendas_data_primeiro_vencimento ON vendas(data_primeiro_vencimento)');
    }

    public function down()
    {
        // Remover índices
        $this->db->query('DROP INDEX idx_vendas_tipo_venda ON vendas');
        $this->db->query('DROP INDEX idx_vendas_data_primeiro_vencimento ON vendas');

        // Remover foreign keys
        $this->db->query('ALTER TABLE `historico_pagamentos` DROP FOREIGN KEY `fk_historico_parcelas_venda`');
        $this->db->query('ALTER TABLE `historico_pagamentos` DROP FOREIGN KEY `fk_historico_vendas`');
        $this->db->query('ALTER TABLE `historico_pagamentos` DROP FOREIGN KEY `fk_historico_formas_pagamento`');
        $this->db->query('ALTER TABLE `historico_pagamentos` DROP FOREIGN KEY `fk_historico_usuarios`');
        $this->db->query('ALTER TABLE `notificacoes_venda` DROP FOREIGN KEY `fk_notificacoes_venda_vendas`');
        $this->db->query('ALTER TABLE `notificacoes_venda` DROP FOREIGN KEY `fk_notificacoes_venda_parcelas`');
        $this->db->query('ALTER TABLE `notificacoes_venda` DROP FOREIGN KEY `fk_notificacoes_venda_usuarios`');
        $this->db->query('ALTER TABLE `parcelas_venda` DROP FOREIGN KEY `fk_parcelas_venda_vendas`');
        $this->db->query('ALTER TABLE `parcelas_venda` DROP FOREIGN KEY `fk_parcelas_venda_formas_pagamento`');
        $this->db->query('ALTER TABLE `parcelas_venda` DROP FOREIGN KEY `fk_parcelas_venda_usuarios`');

        // Remover colunas da tabela vendas
        $this->dbforge->drop_column('vendas', 'tipo_venda');
        $this->dbforge->drop_column('vendas', 'numero_parcelas');
        $this->dbforge->drop_column('vendas', 'intervalo_parcelas');
        $this->dbforge->drop_column('vendas', 'taxa_juros');
        $this->dbforge->drop_column('vendas', 'taxa_multa');
        $this->dbforge->drop_column('vendas', 'valor_total_parcelado');
        $this->dbforge->drop_column('vendas', 'valor_pago_total');
        $this->dbforge->drop_column('vendas', 'valor_pendente');
        $this->dbforge->drop_column('vendas', 'data_primeiro_vencimento');
        $this->dbforge->drop_column('vendas', 'notificar_atraso');
        $this->dbforge->drop_column('vendas', 'dias_antes_notificar');

        // Remover tabelas
        $this->dbforge->drop_table('historico_pagamentos', true);
        $this->dbforge->drop_table('notificacoes_venda', true);
        $this->dbforge->drop_table('parcelas_venda', true);
    }
}
