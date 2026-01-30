<?php

class Migration_add_whatsapp_config extends CI_Migration
{
    public function up()
    {
        // Adiciona configurações do WhatsApp
        $sql = "INSERT INTO `configuracoes` (`config`, `valor`) VALUES 
                ('whatsapp_api_token', ''),
                ('whatsapp_api_url', 'https://api.multivus.com.br/api/messages/send'),
                ('whatsapp_enabled', '0'),
                ('whatsapp_send_signature', '1'),
                ('whatsapp_close_ticket', '0')";
        $this->db->query($sql);
    }

    public function down()
    {
        $this->db->query("DELETE FROM `configuracoes` WHERE `config` IN ('whatsapp_api_token', 'whatsapp_api_url', 'whatsapp_enabled', 'whatsapp_send_signature', 'whatsapp_close_ticket')");
    }
}
