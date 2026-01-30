<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Whatsapp_os extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('os_model');
        $this->load->model('mapos_model');
        $this->load->helper('mpdf');
        $this->load->helper('whatsapp');
        $this->load->helper('date'); // Para função dateInterval
        
        // Garantir que as configurações do WhatsApp existam no banco
        $this->ensureWhatsappConfigExists();
    }
    
    /**
     * Garante que as configurações do WhatsApp existam no banco de dados
     */
    private function ensureWhatsappConfigExists()
    {
        $configs = [
            'whatsapp_api_token' => '',
            'whatsapp_api_url' => 'https://api.multivus.com.br/api/messages/send',
            'whatsapp_enabled' => '0',
            'whatsapp_send_signature' => '1',
            'whatsapp_close_ticket' => '0'
        ];
        
        foreach ($configs as $config => $defaultValue) {
            $this->db->where('config', $config);
            $exists = $this->db->get('configuracoes')->row();
            
            if (!$exists) {
                $this->db->insert('configuracoes', [
                    'config' => $config,
                    'valor' => $defaultValue
                ]);
            }
        }
        
        // Recarregar configurações após garantir que existem
        // O MY_Controller já carrega, mas vamos garantir que as novas estejam disponíveis
        $configuracoes = $this->db->get('configuracoes')->result();
        foreach ($configuracoes as $c) {
            $this->data['configuration'][$c->config] = $c->valor;
        }
    }

    /**
     * Envia ordem de serviço via WhatsApp
     */
    public function enviar()
    {
        if (!$this->uri->segment(3) || !is_numeric($this->uri->segment(3))) {
            $this->session->set_flashdata('error', 'Item não pode ser encontrado, parâmetro não foi passado corretamente.');
            redirect('os');
        }

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vOs')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para enviar O.S. por WhatsApp.');
            redirect(base_url());
        }

        // Verificar se WhatsApp está habilitado
        if (!isset($this->data['configuration']['whatsapp_enabled']) || $this->data['configuration']['whatsapp_enabled'] != '1') {
            $this->session->set_flashdata('error', 'O envio de OS via WhatsApp está desabilitado. Ative nas configurações do sistema (Mapos > Configurações > API).');
            // Redireciona para a página de onde veio (editar ou visualizar)
            $referer = $this->input->server('HTTP_REFERER');
            if ($referer && strpos($referer, 'os/') !== false) {
                redirect($referer);
            } else {
                redirect(site_url('os/visualizar/' . $this->uri->segment(3)));
            }
        }

        // Verificar se token está configurado
        if (empty($this->data['configuration']['whatsapp_api_token'])) {
            $this->session->set_flashdata('error', 'Token da API WhatsApp não configurado. Configure em Mapos > Configurações > API.');
            // Redireciona para a página de onde veio (editar ou visualizar)
            $referer = $this->input->server('HTTP_REFERER');
            if ($referer && strpos($referer, 'os/') !== false) {
                redirect($referer);
            } else {
                redirect(site_url('os/visualizar/' . $this->uri->segment(3)));
            }
        }

        $idOs = $this->uri->segment(3);
        $this->data['result'] = $this->os_model->getById($idOs);
        $this->data['produtos'] = $this->os_model->getProdutos($idOs);
        $this->data['servicos'] = $this->os_model->getServicos($idOs);
        $this->data['emitente'] = $this->mapos_model->getEmitente();
        
        if ($this->data['configuration']['pix_key']) {
            $this->data['qrCode'] = $this->os_model->getQrCode(
                $idOs,
                $this->data['configuration']['pix_key'],
                $this->data['emitente']
            );
            $this->data['chaveFormatada'] = $this->formatarChave($this->data['configuration']['pix_key']);
        }

        // Obter número do cliente usando helper
        $numeroCliente = whatsapp_get_cliente_numero($this->data['result']);

        if (empty($numeroCliente)) {
            $this->session->set_flashdata('error', 'Cliente não possui número de telefone cadastrado (Contato, Telefone ou Celular).');
            // Redireciona para a página de onde veio (editar ou visualizar)
            $referer = $this->input->server('HTTP_REFERER');
            if ($referer) {
                redirect($referer);
            } else {
                redirect(site_url('os/editar/' . $idOs));
            }
        }

        // Formatar número usando helper
        $numeroLimpo = whatsapp_format_numero($numeroCliente);

        // Gerar HTML do PDF
        $html = $this->load->view('os/imprimirOsWhatsapp', $this->data, true);

        // Converter URLs HTTP para caminhos de arquivo locais (mPDF não consegue acessar URLs HTTP)
        $baseUrl = rtrim(base_url(), '/');
        $basePath = rtrim(FCPATH, '/');
        
        // Função auxiliar para converter URL em caminho local
        $convertUrlToPath = function($url) use ($baseUrl, $basePath) {
            // Remover protocolo e domínio
            $url = str_replace($baseUrl, '', $url);
            // Remover query strings
            $url = strtok($url, '?');
            // Converter para caminho de arquivo local
            $filePath = $basePath . '/' . ltrim($url, '/');
            // Normalizar caminho (remover barras duplas, etc.)
            $filePath = realpath($filePath) ?: $filePath;
            return $filePath;
        };
        
        // Converter URLs de imagens e links (src e href)
        $html = preg_replace_callback(
            '/(src|href)=["\']([^"\']+)["\']/i',
            function($matches) use ($baseUrl, $convertUrlToPath) {
                $url = $matches[2];
                // Se for URL do próprio site (base_url)
                if (strpos($url, $baseUrl) === 0 || preg_match('/^https?:\/\/localhost/i', $url)) {
                    $filePath = $convertUrlToPath($url);
                    // Verificar se o arquivo existe
                    if (file_exists($filePath) && is_file($filePath)) {
                        return $matches[1] . '="' . $filePath . '"';
                    }
                }
                // Se não for URL local ou arquivo não existe, retornar original
                return $matches[0];
            },
            $html
        );

        // Contar produtos e serviços para decidir se limita a uma página
        $totalItens = (count($this->data['produtos']) ?: 0) + (count($this->data['servicos']) ?: 0);
        
        // Gerar PDF usando mPDF com configuração otimizada para uma página
        $filename = 'OS_' . str_pad($idOs, 4, 0, STR_PAD_LEFT) . '_' . date('YmdHis');
        
        // Se tiver muitos itens, permitir mais páginas, senão limitar a uma
        if ($totalItens > 10) {
            // Muitos itens - permitir múltiplas páginas
            $pdfPath = pdf_create($html, $filename, false, false);
        } else {
            // Poucos itens - limitar a uma página
            $pdfPath = pdf_create_whatsapp($html, $filename);
        }

        if (!$pdfPath || !file_exists($pdfPath)) {
            $this->session->set_flashdata('error', 'Erro ao gerar PDF da ordem de serviço.');
            // Redireciona para a página de onde veio (editar ou visualizar)
            $referer = $this->input->server('HTTP_REFERER');
            if ($referer) {
                redirect($referer);
            } else {
                redirect(site_url('os/editar/' . $idOs));
            }
        }

        // Preparar dados para envio via API
        $apiUrl = isset($this->data['configuration']['whatsapp_api_url']) && !empty($this->data['configuration']['whatsapp_api_url']) 
            ? $this->data['configuration']['whatsapp_api_url'] 
            : 'https://api.multivus.com.br/api/messages/send';
        $token = isset($this->data['configuration']['whatsapp_api_token']) ? $this->data['configuration']['whatsapp_api_token'] : '';
        
        // Verificar novamente se o token está configurado (após garantir que existe no banco)
        if (empty($token)) {
            $this->session->set_flashdata('error', 'Token da API WhatsApp não configurado ou não foi salvo corretamente. Verifique em Mapos > Configurações > API e salve novamente.');
            $referer = $this->input->server('HTTP_REFERER');
            if ($referer && strpos($referer, 'os/') !== false) {
                redirect($referer);
            } else {
                redirect(site_url('os/visualizar/' . $idOs));
            }
        }
        
        // Converter para booleanos (true/false) ou inteiros (1/0) conforme esperado pela API
        $sendSignature = (isset($this->data['configuration']['whatsapp_send_signature']) && $this->data['configuration']['whatsapp_send_signature'] == '1') ? true : false;
        $closeTicket = (isset($this->data['configuration']['whatsapp_close_ticket']) && $this->data['configuration']['whatsapp_close_ticket'] == '1') ? true : false;

        // Mensagem de texto
        $mensagem = "Olá " . $this->data['result']->nomeCliente . "!\n\n";
        $mensagem .= "Segue em anexo a Ordem de Serviço #" . str_pad($idOs, 4, 0, STR_PAD_LEFT) . ".\n\n";
        $mensagem .= "Atenciosamente,\n" . ($this->data['emitente']->nome ?: 'Equipe');

        // Preparar FormData para envio
        if (class_exists('CURLFile')) {
            $mediaFile = new CURLFile($pdfPath, 'application/pdf', basename($pdfPath));
        } else {
            $mediaFile = '@' . $pdfPath;
        }
        
        // Preparar dados para envio
        // Para multipart/form-data, todos os valores são enviados como strings
        // A API pode esperar strings '1'/'0' ou 'true'/'false' para booleanos
        // userId e queueId: não enviar se vazios (a API pode esperar inteiros e não aceitar strings vazias)
        $postData = [
            'number' => $numeroLimpo,
            'body' => $mensagem,
            'sendSignature' => $sendSignature ? '1' : '0', // String '1' ou '0' (multipart/form-data converte tudo para string)
            'closeTicket' => $closeTicket ? '1' : '0', // String '1' ou '0'
            'medias' => $mediaFile
        ];
        
        // Não incluir userId e queueId - a API pode esperar inteiros e não aceitar strings vazias
        // Se a documentação indicar que são obrigatórios, podemos adicionar campos de configuração para eles

        // Log de debug (remover em produção)
        // log_message('debug', 'WhatsApp API - Dados enviados: ' . print_r($postData, true));
        // log_message('debug', 'WhatsApp API - URL: ' . $apiUrl);
        
        // Enviar via cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $token
            // Não definir Content-Type manualmente - cURL define automaticamente para multipart/form-data
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Desabilitar verificação SSL se necessário
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_VERBOSE, false); // Desabilitar output verbose

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        $curlInfo = curl_getinfo($ch);
        curl_close($ch);

        // Remover arquivo temporário
        if (file_exists($pdfPath)) {
            @unlink($pdfPath);
        }

        // Verificar resposta
        if ($error) {
            $this->session->set_flashdata('error', 'Erro ao enviar via WhatsApp: ' . $error);
            // Redireciona para a página de onde veio (editar ou visualizar)
            $referer = $this->input->server('HTTP_REFERER');
            if ($referer && strpos($referer, 'os/') !== false) {
                redirect($referer);
            } else {
                redirect(site_url('os/visualizar/' . $idOs));
            }
        }

        if ($httpCode >= 200 && $httpCode < 300) {
            $this->session->set_flashdata('success', 'Ordem de serviço enviada com sucesso via WhatsApp para ' . $numeroCliente . '!');
        } else {
            // Melhorar tratamento de erros - mostrar resposta completa da API
            $responseData = json_decode($response, true);
            $errorMsg = 'Erro desconhecido';
            
            // Log de debug (remover em produção)
            // log_message('error', 'WhatsApp API - HTTP Code: ' . $httpCode);
            // log_message('error', 'WhatsApp API - Response: ' . $response);
            
            if ($responseData) {
                // Se a resposta for JSON, tentar extrair mensagem de erro
                if (isset($responseData['message'])) {
                    $errorMsg = $responseData['message'];
                } elseif (isset($responseData['error'])) {
                    $errorMsg = is_array($responseData['error']) ? implode(', ', $responseData['error']) : $responseData['error'];
                } elseif (isset($responseData['errors'])) {
                    if (is_array($responseData['errors'])) {
                        // Se for array de erros, formatar melhor
                        $errors = [];
                        foreach ($responseData['errors'] as $key => $value) {
                            if (is_array($value)) {
                                $errors[] = $key . ': ' . implode(', ', $value);
                            } else {
                                $errors[] = $key . ': ' . $value;
                            }
                        }
                        $errorMsg = implode(' | ', $errors);
                    } else {
                        $errorMsg = $responseData['errors'];
                    }
                } else {
                    // Mostrar resposta completa se não tiver campos padrão
                    $errorMsg = 'Resposta da API: ' . substr(strip_tags($response), 0, 300);
                }
            } elseif (!empty($response)) {
                // Se não for JSON, mostrar parte da resposta
                $errorMsg = 'Resposta: ' . substr(strip_tags($response), 0, 300);
            }
            
            $this->session->set_flashdata('error', 'Erro ao enviar via WhatsApp (HTTP ' . $httpCode . '): ' . $errorMsg);
        }

        // Redireciona para a página de onde veio (editar ou visualizar)
        $referer = $this->input->server('HTTP_REFERER');
        if ($referer) {
            redirect($referer);
        } else {
            redirect(site_url('os/editar/' . $idOs));
        }
    }

    /**
     * Formata chave PIX (método auxiliar)
     */
    private function formatarChave($chave)
    {
        if (strlen($chave) == 11) {
            return substr($chave, 0, 3) . '.' . substr($chave, 3, 3) . '.' . substr($chave, 6, 3) . '-' . substr($chave, 9, 2);
        } elseif (strlen($chave) == 14) {
            return substr($chave, 0, 2) . '.' . substr($chave, 2, 3) . '.' . substr($chave, 5, 3) . '/' . substr($chave, 8, 4) . '-' . substr($chave, 12, 2);
        }
        return $chave;
    }

    /**
     * Método index - redireciona para enviar se ID for passado
     */
    public function index()
    {
        if ($this->uri->segment(2) && is_numeric($this->uri->segment(2))) {
            redirect('whatsapp_os/enviar/' . $this->uri->segment(2));
        } else {
            $this->session->set_flashdata('error', 'ID da OS não informado.');
            redirect('os');
        }
    }
}
