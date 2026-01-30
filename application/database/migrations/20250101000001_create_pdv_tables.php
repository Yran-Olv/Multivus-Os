<?php
/**
 * Migration: Criação de tabelas para Sistema PDV (Ponto de Venda)
 * 
 * Tabelas criadas:
 * - formas_pagamento: Formas de pagamento disponíveis
 * - pagamentos_venda: Pagamentos de cada venda
 * - caixas: Caixas do estabelecimento
 * - turnos_caixa: Turnos de trabalho (abertura/fechamento)
 * - cancelamentos_venda: Cancelamentos com motivo
 * - cupons_desconto: Sistema de cupons de desconto
 */

class Migration_create_pdv_tables extends CI_Migration
{
    public function up()
    {
        // ============================================
        // Tabela: formas_pagamento
        // ============================================
        $this->dbforge->add_field([
            'idFormaPagamento' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'nome' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false
            ],
            'tipo' => [
                'type' => 'ENUM',
                'constraint' => ['dinheiro', 'cartao_debito', 'cartao_credito', 'pix', 'boleto', 'cheque', 'vale', 'outros'],
                'default' => 'outros'
            ],
            'ativo' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1
            ],
            'exige_troco' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0
            ],
            'exige_parcelas' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0
            ],
            'taxa' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'default' => 0.00
            ],
            'ordem' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0
            ]
        ]);
        $this->dbforge->add_key('idFormaPagamento', true);
        $this->dbforge->create_table('formas_pagamento', true);

        // Inserir formas de pagamento padrão
        $formas_padrao = [
            [
                'nome' => 'Dinheiro',
                'tipo' => 'dinheiro',
                'ativo' => 1,
                'exige_troco' => 1,
                'exige_parcelas' => 0,
                'taxa' => 0.00,
                'ordem' => 1
            ],
            [
                'nome' => 'Cartão de Débito',
                'tipo' => 'cartao_debito',
                'ativo' => 1,
                'exige_troco' => 0,
                'exige_parcelas' => 0,
                'taxa' => 0.00,
                'ordem' => 2
            ],
            [
                'nome' => 'Cartão de Crédito',
                'tipo' => 'cartao_credito',
                'ativo' => 1,
                'exige_troco' => 0,
                'exige_parcelas' => 1,
                'taxa' => 0.00,
                'ordem' => 3
            ],
            [
                'nome' => 'PIX',
                'tipo' => 'pix',
                'ativo' => 1,
                'exige_troco' => 0,
                'exige_parcelas' => 0,
                'taxa' => 0.00,
                'ordem' => 4
            ],
            [
                'nome' => 'Vale',
                'tipo' => 'vale',
                'ativo' => 1,
                'exige_troco' => 0,
                'exige_parcelas' => 0,
                'taxa' => 0.00,
                'ordem' => 5
            ]
        ];
        $this->db->insert_batch('formas_pagamento', $formas_padrao);

        // ============================================
        // Tabela: pagamentos_venda
        // ============================================
        $this->dbforge->add_field([
            'idPagamentoVenda' => [
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
            'formas_pagamento_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false
            ],
            'valor' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => false
            ],
            'troco' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00
            ],
            'parcelas' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 1
            ],
            'data_pagamento' => [
                'type' => 'DATETIME',
                'null' => false,
                'default' => 'CURRENT_TIMESTAMP'
            ]
        ]);
        $this->dbforge->add_key('idPagamentoVenda', true);
        $this->dbforge->add_key('vendas_id');
        $this->dbforge->add_key('formas_pagamento_id');
        $this->dbforge->create_table('pagamentos_venda', true);

        // Foreign keys
        $this->db->query('ALTER TABLE `pagamentos_venda` 
            ADD CONSTRAINT `fk_pagamentos_venda_vendas` 
            FOREIGN KEY (`vendas_id`) REFERENCES `vendas` (`idVendas`) 
            ON DELETE CASCADE ON UPDATE CASCADE');
        
        $this->db->query('ALTER TABLE `pagamentos_venda` 
            ADD CONSTRAINT `fk_pagamentos_venda_formas` 
            FOREIGN KEY (`formas_pagamento_id`) REFERENCES `formas_pagamento` (`idFormaPagamento`) 
            ON DELETE RESTRICT ON UPDATE CASCADE');

        // ============================================
        // Tabela: caixas
        // ============================================
        $this->dbforge->add_field([
            'idCaixa' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'nome' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false
            ],
            'descricao' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'saldo_inicial' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00
            ],
            'ativo' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1
            ]
        ]);
        $this->dbforge->add_key('idCaixa', true);
        $this->dbforge->create_table('caixas', true);

        // Inserir caixa padrão
        $this->db->insert('caixas', [
            'nome' => 'Caixa Principal',
            'descricao' => 'Caixa principal do estabelecimento',
            'saldo_inicial' => 0.00,
            'ativo' => 1
        ]);

        // ============================================
        // Tabela: turnos_caixa
        // ============================================
        $this->dbforge->add_field([
            'idTurno' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'caixas_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false
            ],
            'usuarios_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false
            ],
            'data_abertura' => [
                'type' => 'DATETIME',
                'null' => false
            ],
            'data_fechamento' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'valor_abertura' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => false
            ],
            'valor_fechamento' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true
            ],
            'valor_esperado' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true
            ],
            'diferenca' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true
            ],
            'observacoes' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['aberto', 'fechado', 'cancelado'],
                'default' => 'aberto'
            ]
        ]);
        $this->dbforge->add_key('idTurno', true);
        $this->dbforge->add_key('caixas_id');
        $this->dbforge->add_key('usuarios_id');
        $this->dbforge->create_table('turnos_caixa', true);

        // Foreign keys
        $this->db->query('ALTER TABLE `turnos_caixa` 
            ADD CONSTRAINT `fk_turnos_caixa_caixas` 
            FOREIGN KEY (`caixas_id`) REFERENCES `caixas` (`idCaixa`) 
            ON DELETE RESTRICT ON UPDATE CASCADE');
        
        $this->db->query('ALTER TABLE `turnos_caixa` 
            ADD CONSTRAINT `fk_turnos_caixa_usuarios` 
            FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios` (`idUsuarios`) 
            ON DELETE RESTRICT ON UPDATE CASCADE');

        // ============================================
        // Tabela: cancelamentos_venda
        // ============================================
        $this->dbforge->add_field([
            'idCancelamento' => [
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
            'usuarios_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false
            ],
            'motivo' => [
                'type' => 'TEXT',
                'null' => false
            ],
            'data_cancelamento' => [
                'type' => 'DATETIME',
                'null' => false,
                'default' => 'CURRENT_TIMESTAMP'
            ],
            'estornar_estoque' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1
            ]
        ]);
        $this->dbforge->add_key('idCancelamento', true);
        $this->dbforge->add_key('vendas_id');
        $this->dbforge->add_key('usuarios_id');
        $this->dbforge->create_table('cancelamentos_venda', true);

        // Foreign keys
        $this->db->query('ALTER TABLE `cancelamentos_venda` 
            ADD CONSTRAINT `fk_cancelamentos_venda_vendas` 
            FOREIGN KEY (`vendas_id`) REFERENCES `vendas` (`idVendas`) 
            ON DELETE CASCADE ON UPDATE CASCADE');
        
        $this->db->query('ALTER TABLE `cancelamentos_venda` 
            ADD CONSTRAINT `fk_cancelamentos_venda_usuarios` 
            FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios` (`idUsuarios`) 
            ON DELETE RESTRICT ON UPDATE CASCADE');

        // ============================================
        // Tabela: cupons_desconto
        // ============================================
        $this->dbforge->add_field([
            'idCupom' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'codigo' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
                'unique' => true
            ],
            'tipo' => [
                'type' => 'ENUM',
                'constraint' => ['percentual', 'valor_fixo'],
                'null' => false
            ],
            'valor' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => false
            ],
            'valor_minimo' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00
            ],
            'data_inicio' => [
                'type' => 'DATE',
                'null' => false
            ],
            'data_fim' => [
                'type' => 'DATE',
                'null' => false
            ],
            'quantidade_usos' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0
            ],
            'quantidade_maxima' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true
            ],
            'ativo' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1
            ]
        ]);
        $this->dbforge->add_key('idCupom', true);
        $this->dbforge->add_key('codigo');
        $this->dbforge->create_table('cupons_desconto', true);

        // ============================================
        // Modificar tabela: vendas
        // ============================================
        $fields = [
            'turnos_caixa_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'after' => 'usuarios_id'
            ],
            'caixas_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'after' => 'turnos_caixa_id'
            ],
            'numero_cupom' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'after' => 'garantia'
            ],
            'serie_cupom' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => true,
                'after' => 'numero_cupom'
            ],
            'chave_nfce' => [
                'type' => 'VARCHAR',
                'constraint' => 44,
                'null' => true,
                'after' => 'serie_cupom'
            ],
            'xml_nfce' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'chave_nfce'
            ],
            'url_nfce' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
                'after' => 'xml_nfce'
            ],
            'status_nfce' => [
                'type' => 'ENUM',
                'constraint' => ['pendente', 'autorizada', 'cancelada', 'rejeitada'],
                'null' => true,
                'after' => 'url_nfce'
            ],
            'cupons_desconto_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'after' => 'status_nfce'
            ]
        ];

        foreach ($fields as $field => $attributes) {
            $this->dbforge->add_column('vendas', [$field => $attributes]);
        }

        // Foreign keys para vendas
        $this->db->query('ALTER TABLE `vendas` 
            ADD CONSTRAINT `fk_vendas_turnos_caixa` 
            FOREIGN KEY (`turnos_caixa_id`) REFERENCES `turnos_caixa` (`idTurno`) 
            ON DELETE SET NULL ON UPDATE CASCADE');
        
        $this->db->query('ALTER TABLE `vendas` 
            ADD CONSTRAINT `fk_vendas_caixas` 
            FOREIGN KEY (`caixas_id`) REFERENCES `caixas` (`idCaixa`) 
            ON DELETE SET NULL ON UPDATE CASCADE');
        
        $this->db->query('ALTER TABLE `vendas` 
            ADD CONSTRAINT `fk_vendas_cupons_desconto` 
            FOREIGN KEY (`cupons_desconto_id`) REFERENCES `cupons_desconto` (`idCupom`) 
            ON DELETE SET NULL ON UPDATE CASCADE');
    }

    public function down()
    {
        // Remover foreign keys primeiro
        $this->db->query('ALTER TABLE `vendas` DROP FOREIGN KEY `fk_vendas_turnos_caixa`');
        $this->db->query('ALTER TABLE `vendas` DROP FOREIGN KEY `fk_vendas_caixas`');
        $this->db->query('ALTER TABLE `vendas` DROP FOREIGN KEY `fk_vendas_cupons_desconto`');
        $this->db->query('ALTER TABLE `pagamentos_venda` DROP FOREIGN KEY `fk_pagamentos_venda_vendas`');
        $this->db->query('ALTER TABLE `pagamentos_venda` DROP FOREIGN KEY `fk_pagamentos_venda_formas`');
        $this->db->query('ALTER TABLE `turnos_caixa` DROP FOREIGN KEY `fk_turnos_caixa_caixas`');
        $this->db->query('ALTER TABLE `turnos_caixa` DROP FOREIGN KEY `fk_turnos_caixa_usuarios`');
        $this->db->query('ALTER TABLE `cancelamentos_venda` DROP FOREIGN KEY `fk_cancelamentos_venda_vendas`');
        $this->db->query('ALTER TABLE `cancelamentos_venda` DROP FOREIGN KEY `fk_cancelamentos_venda_usuarios`');

        // Remover colunas da tabela vendas
        $this->dbforge->drop_column('vendas', 'turnos_caixa_id');
        $this->dbforge->drop_column('vendas', 'caixas_id');
        $this->dbforge->drop_column('vendas', 'numero_cupom');
        $this->dbforge->drop_column('vendas', 'serie_cupom');
        $this->dbforge->drop_column('vendas', 'chave_nfce');
        $this->dbforge->drop_column('vendas', 'xml_nfce');
        $this->dbforge->drop_column('vendas', 'url_nfce');
        $this->dbforge->drop_column('vendas', 'status_nfce');
        $this->dbforge->drop_column('vendas', 'cupons_desconto_id');

        // Remover tabelas
        $this->dbforge->drop_table('cupons_desconto', true);
        $this->dbforge->drop_table('cancelamentos_venda', true);
        $this->dbforge->drop_table('turnos_caixa', true);
        $this->dbforge->drop_table('caixas', true);
        $this->dbforge->drop_table('pagamentos_venda', true);
        $this->dbforge->drop_table('formas_pagamento', true);
    }
}
