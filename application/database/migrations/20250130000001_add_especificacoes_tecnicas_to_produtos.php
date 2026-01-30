<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_add_especificacoes_tecnicas_to_produtos extends CI_Migration
{
    public function up()
    {
        // Adicionar campos de especificações técnicas
        $this->dbforge->add_column('produtos', [
            'processador' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'descricao_completa',
                'comment' => 'Processador (ex: Intel i5, Snapdragon 888)'
            ],
            'memoria_ram' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'processador',
                'comment' => 'Memória RAM (ex: 8GB, 16GB)'
            ],
            'armazenamento' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'memoria_ram',
                'comment' => 'Armazenamento (ex: 256GB SSD, 1TB HDD)'
            ],
            'tela' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'armazenamento',
                'comment' => 'Tela (ex: 15.6", 6.1")'
            ],
            'sistema_operacional' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'tela',
                'comment' => 'Sistema Operacional (ex: Windows 11, Android 13)'
            ],
            'cor' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'sistema_operacional',
                'comment' => 'Cor do produto'
            ],
            'marca' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'cor',
                'comment' => 'Marca do produto'
            ],
            'modelo' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'marca',
                'comment' => 'Modelo do produto'
            ],
        ]);
    }

    public function down()
    {
        // Remover colunas
        $this->dbforge->drop_column('produtos', 'processador');
        $this->dbforge->drop_column('produtos', 'memoria_ram');
        $this->dbforge->drop_column('produtos', 'armazenamento');
        $this->dbforge->drop_column('produtos', 'tela');
        $this->dbforge->drop_column('produtos', 'sistema_operacional');
        $this->dbforge->drop_column('produtos', 'cor');
        $this->dbforge->drop_column('produtos', 'marca');
        $this->dbforge->drop_column('produtos', 'modelo');
    }
}
