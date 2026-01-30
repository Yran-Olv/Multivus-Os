<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_add_imagem_nome_descricao_to_produtos extends CI_Migration
{
    public function up()
    {
        // Adicionar campo de imagem
        $this->dbforge->add_column('produtos', [
            'imagem' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'idProdutos',
                'comment' => 'Caminho da imagem do produto'
            ],
        ]);

        // Adicionar campo de nome (separado da descrição)
        $this->dbforge->add_column('produtos', [
            'nome' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'imagem',
                'comment' => 'Nome do produto (para exibição)'
            ],
        ]);

        // Adicionar campo de descrição completa
        $this->dbforge->add_column('produtos', [
            'descricao_completa' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'descricao',
                'comment' => 'Descrição completa e detalhada do produto'
            ],
        ]);

        // Atualizar produtos existentes: copiar descricao para nome se nome estiver vazio
        $this->db->query("UPDATE produtos SET nome = descricao WHERE nome IS NULL OR nome = ''");
    }

    public function down()
    {
        // Remover colunas
        $this->dbforge->drop_column('produtos', 'imagem');
        $this->dbforge->drop_column('produtos', 'nome');
        $this->dbforge->drop_column('produtos', 'descricao_completa');
    }
}
