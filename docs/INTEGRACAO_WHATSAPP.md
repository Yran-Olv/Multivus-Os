# 📱 Integração WhatsApp - Envio de Ordem de Serviço

## 📋 Índice

1. [Visão Geral](#visão-geral)
2. [Requisitos](#requisitos)
3. [Instalação](#instalação)
4. [Configuração](#configuração)
5. [Como Usar](#como-usar)
6. [Estrutura de Arquivos](#estrutura-de-arquivos)
7. [API do Whaticket](#api-do-whaticket)
8. [Troubleshooting](#troubleshooting)
9. [Desenvolvimento](#desenvolvimento)

---

## 🎯 Visão Geral

A integração WhatsApp permite o envio automático de Ordens de Serviço em formato PDF diretamente para o WhatsApp do cliente através da API do Whaticket.

### Funcionalidades

- ✅ Envio de OS em PDF via WhatsApp
- ✅ Geração automática de PDF otimizado (1 página)
- ✅ Busca inteligente do número do cliente (Contato > Telefone > Celular)
- ✅ Formatação automática do número (adiciona código do país 55)
- ✅ Configuração centralizada no sistema
- ✅ Interface separada dos arquivos originais

---

## 📦 Requisitos

### Sistema

- PHP 8.4+
- CodeIgniter 3.1.13+
- mPDF 8.2.7+ (já incluído no projeto)
- cURL habilitado
- Extensão CURLFile (PHP 5.5+)

### Whaticket

- Conta ativa no Whaticket
- Token de API configurado na conexão
- Conexão WhatsApp ativa e funcionando

---

## 🚀 Instalação

### 1. Executar Migration

A migration cria as configurações necessárias no banco de dados:

```bash
cd docker
docker compose exec php-fpm php index.php tools migrate
```

**Ou via interface web:**
- Acesse: `Mapos > Configurações > Sistema`
- Clique em "Atualizar Banco de Dados"

### 2. Verificar Arquivos

Certifique-se de que os seguintes arquivos foram criados:

```
application/
├── controllers/
│   └── Whatsapp_os.php                    ✅ Controller separado
├── helpers/
│   └── whatsapp_helper.php                ✅ Helper de funções
├── views/
│   ├── mapos/
│   │   └── partials/
│   │       └── whatsapp_config.php        ✅ View de configurações
│   └── os/
│       └── imprimirOsWhatsapp.php         ✅ View de PDF WhatsApp
└── database/
    └── migrations/
        └── 20250101000000_add_whatsapp_config.php  ✅ Migration
```

---

## ⚙️ Configuração

### Passo 1: Obter Token da API

1. Acesse seu painel do Whaticket
2. Vá em **Conexões**
3. Clique em **Editar** na conexão que deseja usar
4. Copie o **Token** da API
5. Guarde este token para o próximo passo

### Passo 2: Configurar no Sistema

1. Acesse: `Mapos > Configurações > API`
2. Role até a seção **"Integração WhatsApp - Whaticket"**
3. Preencha os campos:

   | Campo | Descrição | Valor Padrão |
   |-------|-----------|--------------|
   | **Ativar Envio de OS via WhatsApp** | Ativa/desativa a funcionalidade | Desativar |
   | **Token da API** | Token obtido no Whaticket | - |
   | **URL da API** | Endpoint da API | `https://api.multivus.com.br/api/messages/send` |
   | **Assinar Mensagem** | Assina a mensagem enviada | Sim |
   | **Encerrar Ticket** | Encerra ticket após envio | Não |

4. Clique em **Salvar Alterações**

### Passo 3: Verificar Configuração

Após salvar, verifique se:
- ✅ O botão WhatsApp aparece na edição de OS
- ✅ As configurações foram salvas corretamente

---

## 📖 Como Usar

### Enviar OS via WhatsApp

1. Acesse uma Ordem de Serviço: `Os > Editar > [ID da OS]`
2. Localize o botão **WhatsApp** ao lado do botão **Imprimir**
3. Clique no botão **WhatsApp**
4. O sistema irá:
   - Gerar o PDF da OS (otimizado para 1 página)
   - Buscar o número do cliente automaticamente
   - Enviar via API do Whaticket
   - Exibir mensagem de sucesso/erro

### Busca Automática do Número

O sistema busca o número do cliente na seguinte ordem de prioridade:

1. **Contato** (`contato_cliente`)
2. **Telefone** (`telefone_cliente`)
3. **Celular** (`celular_cliente`)

Se nenhum número for encontrado, uma mensagem de erro será exibida.

### Formatação do Número

O sistema formata automaticamente o número:
- Remove caracteres especiais (parênteses, traços, espaços)
- Adiciona código do país **55** (Brasil) se necessário
- Exemplo: `(34) 99999-9999` → `5534999999999`

---

## 📁 Estrutura de Arquivos

### Arquivos Criados

#### 1. Controller: `Whatsapp_os.php`

**Localização:** `application/controllers/Whatsapp_os.php`

**Responsabilidade:**
- Processar envio de OS via WhatsApp
- Gerar PDF usando mPDF
- Comunicar com API do Whaticket
- Validar permissões e configurações

**Métodos:**
- `enviar($idOs)` - Envia OS via WhatsApp

#### 2. Helper: `whatsapp_helper.php`

**Localização:** `application/helpers/whatsapp_helper.php`

**Funções:**
- `whatsapp_render_button($idOs, $configuration)` - Renderiza botão WhatsApp
- `whatsapp_save_config($postData)` - Salva configurações
- `whatsapp_get_cliente_numero($cliente)` - Obtém número do cliente
- `whatsapp_format_numero($numero)` - Formata número para envio

#### 3. View PDF: `imprimirOsWhatsapp.php`

**Localização:** `application/views/os/imprimirOsWhatsapp.php`

**Características:**
- CSS separado e otimizado
- Conteúdo compacto para 1 página
- Mesma estrutura visual da impressão A4
- Inclui: dados do emitente, cliente, produtos, serviços, valores

#### 4. View Configurações: `whatsapp_config.php`

**Localização:** `application/views/mapos/partials/whatsapp_config.php`

**Responsabilidade:**
- Interface de configuração do WhatsApp
- Incluída na página de configurações do sistema

#### 5. Migration: `20250101000000_add_whatsapp_config.php`

**Localização:** `application/database/migrations/20250101000000_add_whatsapp_config.php`

**Cria as configurações:**
- `whatsapp_api_token` - Token da API
- `whatsapp_api_url` - URL do endpoint
- `whatsapp_enabled` - Ativar/desativar
- `whatsapp_send_signature` - Assinar mensagem
- `whatsapp_close_ticket` - Encerrar ticket

### Arquivos Modificados (Mínimas Alterações)

#### 1. `application/controllers/Mapos.php`

**Alteração:** 3 linhas adicionadas para salvar configurações WhatsApp

```php
// Carregar helper de WhatsApp para salvar configurações
$this->load->helper('whatsapp');
$whatsappData = whatsapp_save_config($this->input->post());
if ($whatsappData) {
    $data = array_merge($data, $whatsappData);
}
```

#### 2. `application/views/mapos/configurar.php`

**Alteração:** Include da view parcial de configurações

```php
<?php 
// Incluir configurações do WhatsApp via view parcial (arquivo separado)
if (file_exists(APPPATH . 'views/mapos/partials/whatsapp_config.php')) {
    $this->load->view('mapos/partials/whatsapp_config', ['configuration' => $configuration]);
}
?>
```

#### 3. `application/views/os/editarOs.php`

**Alteração:** 2 linhas para renderizar botão WhatsApp

```php
<?php 
// Incluir botão WhatsApp via helper (arquivo separado)
$this->load->helper('whatsapp');
echo whatsapp_render_button($result->idOs, $configuration);
?>
```

---

## 🔌 API do Whaticket

### Endpoint

```
POST https://api.multivus.com.br/api/messages/send
```

### Headers

```
Authorization: Bearer {TOKEN}
Content-Type: multipart/form-data
```

### Body (FormData)

| Campo | Tipo | Obrigatório | Descrição |
|-------|------|-------------|-----------|
| `number` | String | Sim | Número do destinatário (com código do país) |
| `body` | String | Sim | Mensagem de texto |
| `userId` | String | Não | ID do usuário (vazio por padrão) |
| `queueId` | String | Não | ID da fila (vazio por padrão) |
| `medias` | File | Sim | Arquivo PDF da OS |
| `sendSignature` | Boolean | Não | Assinar mensagem (true/false) |
| `closeTicket` | Boolean | Não | Encerrar ticket (true/false) |

### Exemplo de Requisição

```php
$postData = [
    'number' => '5534999999999',
    'body' => 'Olá Cliente!\n\nSegue em anexo a Ordem de Serviço #0001.',
    'userId' => '',
    'queueId' => '',
    'sendSignature' => 'true',
    'closeTicket' => 'false',
    'medias' => new CURLFile($pdfPath, 'application/pdf', 'OS_0001.pdf')
];
```

### Resposta de Sucesso

```json
{
    "status": "success",
    "message": "Mensagem enviada com sucesso"
}
```

### Resposta de Erro

```json
{
    "status": "error",
    "message": "Descrição do erro"
}
```

### Documentação Oficial

Para mais detalhes, consulte a [documentação oficial do Whaticket](https://app.multivus.com.br/messages-api).

---

## 🔧 Troubleshooting

### Botão WhatsApp não aparece

**Problema:** O botão não aparece na edição da OS.

**Soluções:**
1. Verifique se a migration foi executada
2. Verifique se o usuário tem permissão `eOs`
3. Limpe o cache do navegador
4. Verifique se o helper está sendo carregado corretamente

### Erro: "Token da API WhatsApp não configurado"

**Problema:** Mensagem de erro ao tentar enviar.

**Soluções:**
1. Acesse `Mapos > Configurações > API`
2. Preencha o campo **Token da API**
3. Salve as alterações
4. Tente novamente

### Erro: "Cliente não possui número cadastrado"

**Problema:** Não encontra número do cliente.

**Soluções:**
1. Verifique se o cliente tem pelo menos um dos campos preenchidos:
   - Contato
   - Telefone
   - Celular
2. Edite o cliente e preencha um dos campos
3. Tente novamente

### Erro: "Erro ao gerar PDF"

**Problema:** Falha na geração do PDF.

**Soluções:**
1. Verifique permissões da pasta `assets/uploads/temp/`
2. Verifique se mPDF está instalado: `composer show mpdf/mpdf`
3. Verifique logs de erro do PHP
4. Verifique espaço em disco

### Erro na API (HTTP 401, 403, 500)

**Problema:** Erro ao comunicar com API do Whaticket.

**Soluções:**
1. Verifique se o token está correto
2. Verifique se a conexão WhatsApp está ativa no Whaticket
3. Verifique se a URL da API está correta
4. Verifique logs do Whaticket
5. Teste o token diretamente na API

### PDF muito grande ou não cabe em 1 página

**Problema:** PDF gerado não cabe em uma página.

**Soluções:**
1. A view `imprimirOsWhatsapp.php` já está otimizada
2. Se necessário, ajuste o CSS na view
3. Reduza tamanho de fontes ou espaçamentos
4. Considere remover seções menos importantes

---

## 💻 Desenvolvimento

### Adicionar Novas Funcionalidades

#### 1. Adicionar Campo na Configuração

1. Adicione na migration:
```php
('novo_campo', 'valor_padrao')
```

2. Adicione na view `whatsapp_config.php`:
```php
<div class="control-group">
    <label for="novo_campo" class="control-label">Novo Campo</label>
    <div class="controls">
        <input type="text" name="novo_campo" value="<?= $configuration['novo_campo'] ?>">
    </div>
</div>
```

3. Adicione no helper `whatsapp_save_config()`:
```php
if (isset($postData['novo_campo'])) {
    $data['novo_campo'] = $postData['novo_campo'];
}
```

#### 2. Modificar Mensagem Enviada

Edite o método `enviar()` em `Whatsapp_os.php`:

```php
$mensagem = "Sua mensagem personalizada aqui";
```

#### 3. Adicionar Validações

No método `enviar()`, adicione validações antes do envio:

```php
// Exemplo: Validar se OS está finalizada
if ($this->data['result']->status != 'Finalizado') {
    $this->session->set_flashdata('error', 'OS deve estar finalizada para enviar.');
    redirect(site_url('os/editar/' . $idOs));
}
```

### Testes

#### Teste Manual

1. Configure o WhatsApp nas configurações
2. Crie uma OS de teste
3. Adicione número do cliente
4. Clique no botão WhatsApp
5. Verifique se o PDF foi gerado
6. Verifique se a mensagem chegou no WhatsApp

#### Teste da API

Use cURL para testar a API diretamente:

```bash
curl -X POST https://api.multivus.com.br/api/messages/send \
  -H "Authorization: Bearer SEU_TOKEN" \
  -F "number=5534999999999" \
  -F "body=Teste" \
  -F "medias=@/caminho/para/arquivo.pdf"
```

### Logs

Os erros são exibidos via flashdata do CodeIgniter. Para debug:

1. Ative logs do PHP
2. Verifique `application/logs/`
3. Use `var_dump()` ou `error_log()` para debug

---

## 📝 Notas Importantes

### Segurança

- ⚠️ **Nunca** compartilhe o token da API
- ⚠️ Mantenha o token seguro e não o versione no Git
- ⚠️ Use HTTPS em produção
- ⚠️ Valide sempre os dados de entrada

### Performance

- O PDF é gerado sob demanda (não é cacheado)
- Arquivos temporários são removidos após envio
- O processo pode levar alguns segundos dependendo do tamanho da OS

### Limitações

- PDF limitado a 1 página (otimizado)
- Requer número válido do cliente
- Depende da API do Whaticket estar online
- Requer conexão WhatsApp ativa no Whaticket

### Compatibilidade

- ✅ PHP 8.4+
- ✅ CodeIgniter 3.1.13+
- ✅ mPDF 8.2.7+
- ✅ Whaticket API v1

---

## 📞 Suporte

### Problemas Comuns

Consulte a seção [Troubleshooting](#troubleshooting) acima.

### Documentação Adicional

- [Documentação do Whaticket](https://app.multivus.com.br/messages-api)
- [Documentação do mPDF](https://mpdf.github.io/)
- [Documentação do CodeIgniter 3](https://codeigniter.com/userguide3/)

### Contato

Para suporte técnico, consulte a documentação do projeto ou abra uma issue no repositório.

---

## 📄 Licença

Esta integração segue a mesma licença do projeto principal.

---

**Última atualização:** Janeiro 2025  
**Versão:** 1.0.0
