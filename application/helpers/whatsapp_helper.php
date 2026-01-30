<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Helper para funcionalidades do WhatsApp
 */

/**
 * Renderiza o botão de envio via WhatsApp
 */
if (!function_exists('whatsapp_render_button')) {
    function whatsapp_render_button($idOs, $configuration)
    {
        $CI = &get_instance();
        
        // Verifica permissão para visualizar OS (vOs) - necessário para enviar
        if (!$CI->permission->checkPermission($CI->session->userdata('permissao'), 'vOs')) {
            return '';
        }
        
        // Sempre mostra o botão apontando para o controller de envio
        // O controller fará as validações necessárias e mostrará mensagens de erro apropriadas
        // URL completa com index.php para garantir que funcione
        $url = base_url() . 'index.php/whatsapp_os/enviar/' . $idOs;
        
        // Verificar se está configurado para mostrar tooltip diferente
        $enabled = isset($configuration['whatsapp_enabled']) && $configuration['whatsapp_enabled'] == '1';
        $hasToken = isset($configuration['whatsapp_api_token']) && !empty($configuration['whatsapp_api_token']);
        
        if (!$enabled || !$hasToken) {
            // Botão com estilo diferente indicando que precisa configurar
            return '<a title="Configure o WhatsApp em Mapos > Configurações > API" class="button btn btn-mini btn-success" href="' . $url . '" style="opacity: 0.7;" id="enviarWhatsappOS">
                <span class="button__icon"><i class="bx bxl-whatsapp"></i></span> <span class="button__text">WhatsApp</span>
            </a>';
        }
        
        return '<a title="Enviar OS via WhatsApp" class="button btn btn-mini btn-success" href="' . $url . '" id="enviarWhatsappOS">
            <span class="button__icon"><i class="bx bxl-whatsapp"></i></span> <span class="button__text">WhatsApp</span>
        </a>';
    }
}

/**
 * Salva configurações do WhatsApp
 */
if (!function_exists('whatsapp_save_config')) {
    function whatsapp_save_config($postData)
    {
        $data = [];
        
        // Sempre incluir todos os campos, mesmo que vazios, para garantir que sejam salvos
        $data['whatsapp_api_token'] = isset($postData['whatsapp_api_token']) ? $postData['whatsapp_api_token'] : '';
        $data['whatsapp_api_url'] = isset($postData['whatsapp_api_url']) && !empty($postData['whatsapp_api_url']) 
            ? $postData['whatsapp_api_url'] 
            : 'https://api.multivus.com.br/api/messages/send';
        $data['whatsapp_enabled'] = isset($postData['whatsapp_enabled']) ? $postData['whatsapp_enabled'] : '0';
        $data['whatsapp_send_signature'] = isset($postData['whatsapp_send_signature']) ? $postData['whatsapp_send_signature'] : '1';
        $data['whatsapp_close_ticket'] = isset($postData['whatsapp_close_ticket']) ? $postData['whatsapp_close_ticket'] : '0';
        
        return $data;
    }
}

/**
 * Obtém número do cliente (prioridade: contato > telefone > celular)
 */
if (!function_exists('whatsapp_get_cliente_numero')) {
    function whatsapp_get_cliente_numero($cliente)
    {
        if (!empty($cliente->contato_cliente)) {
            return $cliente->contato_cliente;
        } elseif (!empty($cliente->telefone_cliente)) {
            return $cliente->telefone_cliente;
        } elseif (!empty($cliente->celular_cliente)) {
            return $cliente->celular_cliente;
        }
        
        return '';
    }
}

/**
 * Formata número para envio (adiciona código do país se necessário)
 */
if (!function_exists('whatsapp_format_numero')) {
    function whatsapp_format_numero($numero)
    {
        // Limpar número (remover caracteres especiais)
        $numeroLimpo = preg_replace('/[^0-9]/', '', $numero);
        
        // Verificar se tem código do país (55 para Brasil)
        if (strlen($numeroLimpo) == 11 || strlen($numeroLimpo) == 10) {
            // Adicionar código do país 55 se não tiver
            $numeroLimpo = '55' . $numeroLimpo;
        }
        
        return $numeroLimpo;
    }
}
