# 📊 Análise Completa do Projeto MapOS/Multivus-OS

**Documento de Referência para Desenvolvimento e Melhorias**

> Este documento serve como guia de referência para entender a arquitetura, estrutura e padrões do projeto, facilitando futuras melhorias e manutenção.

---

## 📋 Índice

1. [Visão Geral](#visão-geral)
2. [Arquitetura do Sistema](#arquitetura-do-sistema)
3. [Stack Tecnológico](#stack-tecnológico)
4. [Estrutura de Diretórios](#estrutura-de-diretórios)
5. [Padrões e Convenções](#padrões-e-convenções)
6. [Banco de Dados](#banco-de-dados)
7. [Componentes Principais](#componentes-principais)
8. [API REST](#api-rest)
9. [Sistema de Autenticação](#sistema-de-autenticação)
10. [Integrações e Gateways](#integrações-e-gateways)
11. [Sistema de Instalação](#sistema-de-instalação)
12. [Docker e Infraestrutura](#docker-e-infraestrutura)
13. [Áreas de Melhoria Identificadas](#áreas-de-melhoria-identificadas)
14. [Guia de Desenvolvimento](#guia-de-desenvolvimento)

---

## 🎯 Visão Geral

### Informações do Projeto

- **Nome:** MapOS / Multivus-OS
- **Versão Atual:** 4.53.0
- **Tipo:** Sistema de Controle de Ordens de Serviço (OS)
- **Framework Base:** CodeIgniter 3.1.13
- **Licença:** Apache 2.0
- **Linguagem:** PHP 8.4+
- **Banco de Dados:** MySQL 8.0+

### Funcionalidades Principais

- ✅ Gestão de Ordens de Serviço (OS)
- ✅ Cadastro de Clientes e Fornecedores
- ✅ Controle de Produtos e Serviços
- ✅ Gestão Financeira (Contas a Pagar/Receber)
- ✅ Sistema de Vendas
- ✅ Gestão de Garantias
- ✅ Relatórios e Gráficos
- ✅ Área do Cliente (Portal)
- ✅ API REST para integrações
- ✅ Sistema de E-mail em Fila
- ✅ Integração com Gateways de Pagamento
- ✅ Sistema de Permissões e Auditoria

---

## 🏗️ Arquitetura do Sistema

### Padrão Arquitetural

O projeto utiliza o padrão **MVC (Model-View-Controller)** do CodeIgniter:

```
┌─────────────┐
│   Browser   │
└──────┬──────┘
       │
       ▼
┌─────────────┐
│  index.php  │ (Front Controller)
└──────┬──────┘
       │
       ▼
┌─────────────┐
│  Controller  │ ←──┐
└──────┬──────┘    │
       │           │
       ▼           │
┌─────────────┐    │
│    Model    │────┘
└──────┬──────┘
       │
       ▼
┌─────────────┐
│    View     │
└─────────────┘
```

### Fluxo de Requisição

1. **Front Controller** (`index.php`) recebe a requisição
2. Verifica ambiente e carrega autoload do Composer
3. Carrega configurações do `.env`
4. Redireciona para instalação se necessário
5. **Router** (`routes.php`) determina o controller
6. **Controller** processa a requisição
7. **Model** acessa o banco de dados
8. **View** renderiza a resposta

### Estrutura de Camadas

```
┌─────────────────────────────────────┐
│         Presentation Layer           │
│  (Views, Assets, JavaScript)         │
└─────────────────────────────────────┘
┌─────────────────────────────────────┐
│        Application Layer            │
│  (Controllers, Libraries, Helpers)  │
└─────────────────────────────────────┘
┌─────────────────────────────────────┐
│          Business Layer              │
│  (Models, Business Logic)            │
└─────────────────────────────────────┘
┌─────────────────────────────────────┐
│          Data Layer                 │
│  (MySQL Database)                   │
└─────────────────────────────────────┘
```

---

## 💻 Stack Tecnológico

### Backend

- **PHP:** 8.4+ (requerido), 8.5 (Docker)
- **Framework:** CodeIgniter 3.1.13
- **Banco de Dados:** MySQL 8.0+ / MariaDB
- **Composer:** Gerenciador de dependências PHP

### Frontend

- **JavaScript:** jQuery, jQuery UI
- **CSS Framework:** Bootstrap (Matrix Admin Theme)
- **Editor WYSIWYG:** Trumbowyg
- **Gráficos:** Biblioteca de gráficos JavaScript
- **DataTables:** Para tabelas interativas

### Bibliotecas e Dependências Principais

```json
{
  "mercadopago/dx-php": "^3.8.0",        // Gateway Mercado Pago
  "efipay/sdk-php-apis-efi": "^1.17.0",  // Gateway Gerencianet/Efí
  "mpdf/mpdf": "^8.2.7",                 // Geração de PDFs
  "phpoffice/phpword": "^0.18.3",        // Manipulação de Word
  "piggly/php-pix": "^2.0.2",            // Geração de QR Code PIX
  "codephix/asaas-sdk": "^2.0.12",       // Gateway Asaas
  "vlucas/phpdotenv": "^5.6.3",          // Gerenciamento de .env
  "filp/whoops": "^2.18.4",              // Tratamento de erros
  "ezyang/htmlpurifier": "^4.19"         // Sanitização HTML
}
```

### Infraestrutura (Docker)

- **Nginx:** Servidor web
- **PHP-FPM:** Processador PHP
- **MySQL:** Banco de dados
- **phpMyAdmin:** Interface de gerenciamento do banco
- **Composer:** Container para instalação de dependências

---

## 📁 Estrutura de Diretórios

```
Multivus-Os/
├── application/              # Código da aplicação
│   ├── cache/               # Cache da aplicação
│   ├── config/              # Arquivos de configuração
│   │   ├── config.php       # Configurações gerais
│   │   ├── database.php     # Configuração do banco
│   │   ├── routes.php       # Rotas da aplicação
│   │   └── routes_api.php   # Rotas da API
│   ├── controllers/         # Controllers (lógica de controle)
│   │   ├── api/             # Controllers da API REST
│   │   │   └── v1/          # Versão 1 da API
│   │   ├── Clientes.php
│   │   ├── Os.php
│   │   ├── Login.php
│   │   └── ...
│   ├── core/                # Extensões do core
│   │   └── MY_Controller.php # Controller base customizado
│   ├── database/            # Migrations e seeds
│   │   ├── migrations/      # Migrações do banco
│   │   └── seeds/           # Dados iniciais
│   ├── helpers/             # Funções auxiliares
│   ├── hooks/               # Hooks do CodeIgniter
│   ├── language/            # Arquivos de idioma
│   ├── libraries/           # Bibliotecas customizadas
│   │   ├── Gateways/        # Gateways de pagamento
│   │   ├── Authorization_Token.php
│   │   ├── Permission.php
│   │   └── REST_Controller.php
│   ├── models/              # Models (acesso a dados)
│   ├── third_party/         # Bibliotecas de terceiros
│   ├── views/               # Templates/Views
│   └── vendor/              # Dependências do Composer
├── assets/                  # Arquivos estáticos
│   ├── css/                 # Estilos CSS
│   ├── js/                  # Scripts JavaScript
│   ├── img/                 # Imagens
│   └── ...
├── docker/                   # Configuração Docker
│   ├── docker-compose.yml
│   ├── etc/                 # Configurações dos containers
│   └── data/                # Dados persistentes
├── install/                 # Sistema de instalação
├── updates/                 # Scripts de atualização SQL
├── index.php                # Front Controller
├── composer.json            # Dependências PHP
└── banco.sql               # Schema inicial do banco
```

### Convenções de Nomenclatura

- **Controllers:** PascalCase, singular (ex: `Clientes.php`, `Os.php`)
- **Models:** PascalCase + sufixo `_model` (ex: `Clientes_model.php`)
- **Views:** lowercase, correspondem aos controllers
- **Libraries:** PascalCase (ex: `Permission.php`)
- **Helpers:** snake_case + sufixo `_helper` (ex: `audit_helper.php`)

---

## 📐 Padrões e Convenções

### Padrão MVC do CodeIgniter

#### Controllers

```php
class Clientes extends MY_Controller {
    public function __construct() {
        parent::__construct();
        // MY_Controller já verifica autenticação
    }
    
    public function index() {
        // Listagem
    }
    
    public function adicionar() {
        // Formulário de adição
    }
}
```

#### Models

```php
class Clientes_model extends CI_Model {
    public function get($table, $fields, $where = '', $perpage = 0, $start = 0, $one = false, $array = 'array') {
        // Padrão de consulta
    }
}
```

#### Views

- Localizadas em `application/views/`
- Estruturadas com tema (topo, menu, conteúdo, rodapé)
- Usam helpers do CodeIgniter para formulários

### Controller Base Customizado

**MY_Controller** (`application/core/MY_Controller.php`):

- **Função:** Controller base que estende `CI_Controller`
- **Responsabilidades:**
  - Verificação de autenticação (sessão)
  - Carregamento de configurações do banco
  - Inicialização de dados comuns para views
  - Método `layout()` para renderização padrão

**Características:**
```php
- Verifica se usuário está logado
- Carrega configurações da tabela 'configuracoes'
- Disponibiliza $this->data para todas as views
- Redireciona para login se não autenticado
```

### Sistema de Configurações

- Configurações armazenadas na tabela `configuracoes`
- Acessadas via `$this->data['configuration']` nos controllers
- Carregadas automaticamente pelo `MY_Controller`

### Sistema de Permissões

- Baseado em roles (perfis de usuário)
- Tabela `permissoes` define perfis
- Usuários vinculados a perfis via `permissoes_id`
- Biblioteca `Permission.php` gerencia verificações

---

## 🗄️ Banco de Dados

### Principais Tabelas

#### Tabelas de Negócio

1. **`os`** - Ordens de Serviço
   - Campos principais: `idOs`, `dataInicial`, `dataFinal`, `status`, `valorTotal`
   - Relacionamentos: `clientes`, `usuarios`, `servicos`, `produtos`

2. **`clientes`** - Clientes e Fornecedores
   - Campos: `idClientes`, `nomeCliente`, `documento`, `email`, `telefone`
   - Suporta pessoa física/jurídica
   - Integração com Asaas (`asaas_id`)

3. **`produtos`** - Produtos
   - Campos: `idProdutos`, `descricao`, `preco`, `estoque`, `categoria`
   - Controle de estoque

4. **`servicos`** - Serviços
   - Campos: `idServicos`, `nome`, `descricao`, `preco`

5. **`vendas`** - Vendas
   - Campos: `idVendas`, `clientes_id`, `dataVenda`, `valorTotal`
   - Relacionada com `vendas_produtos` e `vendas_servicos`

6. **`lancamentos`** - Lançamentos Financeiros
   - Campos: `idLancamentos`, `tipo`, `descricao`, `valor`, `dataVencimento`
   - Contas a pagar/receber

#### Tabelas de Sistema

1. **`usuarios`** - Usuários do Sistema
   - Campos: `idUsuarios`, `nome`, `email`, `senha`, `permissoes_id`
   - Senhas hashadas

2. **`permissoes`** - Perfis de Permissão
   - Campos: `idPermissao`, `nome`, `permissoes` (JSON)

3. **`configuracoes`** - Configurações do Sistema
   - Campos: `idConfig`, `config`, `valor`
   - Chave-valor simples

4. **`email_queue`** - Fila de E-mails
   - Campos: `id`, `to`, `message`, `status`, `date`
   - Status: pending, sending, sent, failed

5. **`ci_sessions`** - Sessões do CodeIgniter
   - Armazena dados de sessão

#### Tabelas de Relacionamento

- `servicos_os` - Serviços vinculados a OS
- `produtos_os` - Produtos vinculados a OS
- `vendas_produtos` - Produtos de vendas
- `vendas_servicos` - Serviços de vendas
- `equipamentos_os` - Equipamentos de OS

### Estrutura de Relacionamentos

```
usuarios ──┐
           ├──> os ──> servicos_os ──> servicos
clientes ──┘     │
                 ├──> produtos_os ──> produtos
                 └──> equipamentos_os ──> equipamentos

clientes ──> vendas ──> vendas_produtos ──> produtos
                    └──> vendas_servicos ──> servicos

usuarios ──> permissoes
usuarios ──> lancamentos
```

### Migrations

- Localizadas em `application/database/migrations/`
- Sistema de versionamento de schema
- Executadas via: `php index.php tools migrate`

---

## 🧩 Componentes Principais

### Controllers Principais

#### 1. **Mapos** (Dashboard)
- Controller padrão (`default_controller`)
- Exibe dashboard principal
- Métodos: `index()`, `dashboard()`

#### 2. **Os** (Ordens de Serviço)
- CRUD completo de OS
- Métodos principais:
  - `index()` - Listagem
  - `adicionar()` - Formulário de criação
  - `editar($id)` - Edição
  - `visualizar($id)` - Visualização
  - `excluir($id)` - Exclusão
  - `imprimir($id)` - Impressão

#### 3. **Clientes**
- Gestão de clientes e fornecedores
- Suporta pessoa física/jurídica
- Integração com gateways de pagamento

#### 4. **Login**
- Autenticação de usuários
- Gerenciamento de sessão
- Recuperação de senha

#### 5. **Email**
- Processamento de fila de e-mails
- Métodos: `process()`, `retry()`
- Executado via cron

#### 6. **Tools**
- Comandos CLI
- Migrations
- Utilitários do sistema

### Models Principais

#### Padrão de Models

Todos os models seguem padrão similar:

```php
class Clientes_model extends CI_Model {
    // Métodos comuns:
    - get() - Buscar registros
    - add() - Adicionar
    - edit() - Editar
    - delete() - Excluir
    - count() - Contar registros
}
```

#### Models Específicos

1. **Os_model** - Lógica de negócio de OS
2. **Clientes_model** - Gestão de clientes
3. **Email_model** - Fila de e-mails
4. **Financeiro_model** - Lançamentos financeiros
5. **Vendas_model** - Processamento de vendas
6. **Audit_model** - Auditoria de ações

### Libraries Customizadas

#### 1. **Permission.php**
- Sistema de permissões
- Verificação de acesso
- Métodos: `checkPermission()`, `hasPermission()`

#### 2. **Authorization_Token.php**
- Autenticação via token JWT
- Usado na API REST

#### 3. **REST_Controller.php**
- Controller base para API
- Tratamento de requisições REST
- Respostas JSON padronizadas

#### 4. **Gateways/** (Gateways de Pagamento)
- **BasePaymentGateway.php** - Classe base
- **MercadoPago.php** - Integração Mercado Pago
- **GerencianetSdk.php** - Integração Gerencianet/Efí
- **Asaas.php** - Integração Asaas

#### 5. **Format.php**
- Formatação de dados
- Datas, valores monetários, etc.

### Helpers

1. **audit_helper.php** - Funções de auditoria
2. **date_helper.php** - Manipulação de datas
3. **validation_helper.php** - Validações customizadas
4. **mpdf_helper.php** - Geração de PDFs
5. **security_helper.php** - Segurança
6. **general_helper.php** - Funções gerais

---

## 🔌 API REST

### Estrutura

- Localizada em `application/controllers/api/v1/`
- Baseada em `REST_Controller`
- Autenticação via token JWT

### Endpoints Principais

#### Autenticação
- `POST /api/v1/login` - Login e obtenção de token

#### Clientes
- `GET /api/v1/clientes` - Listar clientes
- `GET /api/v1/clientes/{id}` - Obter cliente
- `POST /api/v1/clientes` - Criar cliente
- `PUT /api/v1/clientes/{id}` - Atualizar cliente
- `DELETE /api/v1/clientes/{id}` - Excluir cliente

#### Ordens de Serviço
- `GET /api/v1/os` - Listar OS
- `GET /api/v1/os/{id}` - Obter OS
- `POST /api/v1/os` - Criar OS
- `PUT /api/v1/os/{id}` - Atualizar OS
- `DELETE /api/v1/os/{id}` - Excluir OS

#### Produtos e Serviços
- Endpoints similares para produtos e serviços

### Área do Cliente (API)

Controllers em `api/v1/client/`:
- **ClientLoginController** - Login do cliente
- **ClientOsController** - OS do cliente
- **ClientCobrancasController** - Cobranças
- **ClientComprasController** - Compras

### Configuração da API

- Habilitada via variável de ambiente: `API_ENABLED=true`
- Rotas definidas em `application/config/routes_api.php`
- Autenticação obrigatória (exceto login)

---

## 🔐 Sistema de Autenticação

### Autenticação Web

1. **Login** (`Login.php` controller)
   - Validação de credenciais
   - Criação de sessão
   - Redirecionamento baseado em permissões

2. **Sessão**
   - Armazenada em `ci_sessions`
   - Dados do usuário em `$this->session->userdata()`
   - Verificada em `MY_Controller`

3. **Proteção de Rotas**
   - `MY_Controller` verifica autenticação
   - Redireciona para login se não autenticado

### Autenticação API

- Token JWT via `Authorization_Token`
- Header: `Authorization: Bearer {token}`
- Token obtido via endpoint `/api/v1/login`

### Recuperação de Senha

- Tabela `resets_de_senha`
- Token único com expiração
- Envio de e-mail com link de reset

---

## 💳 Integrações e Gateways

### Gateways de Pagamento

#### 1. **Mercado Pago**
- SDK: `mercadopago/dx-php`
- Classe: `Gateways/MercadoPago.php`
- Métodos: criar pagamento, webhook, cancelar

#### 2. **Gerencianet/Efí**
- SDK: `efipay/sdk-php-apis-efi`
- Classe: `Gateways/GerencianetSdk.php`
- Suporte a PIX, boleto, cartão

#### 3. **Asaas**
- SDK: `codephix/asaas-sdk`
- Classe: `Gateways/Asaas.php`
- Integração com clientes (`asaas_id`)

### Padrão de Gateway

Todos implementam interface `PaymentGateway`:
```php
- createPayment()
- getPayment()
- cancelPayment()
- processWebhook()
```

### Geração de PIX

- Biblioteca: `piggly/php-pix`
- Geração de QR Code PIX
- Integração com gateways

---

## 🚀 Sistema de Instalação

### Fluxo de Instalação

1. **Verificação de Ambiente**
   - `index.php` verifica `.env`
   - Se `APP_ENVIRONMENT=pre_installation`, redireciona para `/install`

2. **Instalador Web** (`install/`)
   - Interface web de instalação
   - Passos:
     - Verificação de requisitos
     - Configuração de banco de dados
     - Criação de usuário administrador
     - Configuração de URL

3. **Migrations**
   - Execução automática de migrations
   - Criação de schema inicial

4. **Configuração Final**
   - Atualização de `.env`
   - Criação de arquivos de configuração

### Arquivos de Instalação

- `install/index.php` - Interface principal
- `install/do_install.php` - Processamento
- `install/view/` - Views do instalador
- `install/settings.json` - Configurações

---

## 🐳 Docker e Infraestrutura

### Estrutura Docker

```
docker/
├── docker-compose.yml      # Orquestração
├── etc/
│   ├── nginx/              # Configuração Nginx
│   ├── php/                # PHP-FPM e configurações
│   └── composer/           # Container Composer
└── data/                   # Dados persistentes
    └── db/mysql/           # Dados do MySQL
```

### Containers

1. **nginx** - Servidor web
2. **php-fpm** - Processador PHP
3. **mysql** - Banco de dados
4. **phpmyadmin** - Interface do banco
5. **composer** - Instalação de dependências

### Configuração via .env

Variáveis principais:
- `NGINX_HOST` - Domínio/host
- `NGINX_PORT` - Porta do Nginx
- `MYSQL_MAPOS_*` - Configurações MySQL
- `PHP_MY_ADMIN_PORT` - Porta phpMyAdmin

### Cron Jobs

Configurados no container PHP-FPM:
- A cada 2 minutos: `email/process`
- A cada 5 minutos: `email/retry`

---

## 🔍 Áreas de Melhoria Identificadas

### 1. Arquitetura e Código

#### Pontos Fortes
- ✅ Estrutura MVC bem definida
- ✅ Separação de responsabilidades
- ✅ Uso de migrations
- ✅ Sistema de configurações flexível

#### Oportunidades de Melhoria

1. **Migração para CodeIgniter 4**
   - CI3 está em manutenção
   - CI4 oferece melhorias significativas
   - Requer refatoração extensiva

2. **Padronização de Respostas**
   - Padronizar respostas JSON da API
   - Implementar DTOs/Resources
   - Melhorar tratamento de erros

3. **Testes Automatizados**
   - Adicionar testes unitários
   - Testes de integração
   - CI/CD pipeline

4. **Documentação de Código**
   - PHPDoc mais completo
   - Documentação de API (Swagger/OpenAPI)
   - Guias de desenvolvimento

### 2. Segurança

#### Melhorias Sugeridas

1. **Validação de Entrada**
   - Validar todos os inputs
   - Sanitização mais rigorosa
   - CSRF protection em todos os forms

2. **Autenticação**
   - Implementar 2FA
   - Rate limiting
   - Sessões mais seguras

3. **SQL Injection**
   - Usar Query Builder sempre
   - Evitar queries diretas
   - Prepared statements

4. **XSS Protection**
   - Escapar outputs
   - Content Security Policy
   - Usar `htmlpurifier` consistentemente

### 3. Performance

1. **Cache**
   - Implementar cache de queries
   - Cache de views
   - Redis para sessões

2. **Otimização de Banco**
   - Índices adequados
   - Queries otimizadas
   - Connection pooling

3. **Assets**
   - Minificação de CSS/JS
   - CDN para assets estáticos
   - Lazy loading de imagens

### 4. Frontend

1. **Modernização**
   - Migrar para framework moderno (Vue.js/React)
   - Componentização
   - Estado gerenciado

2. **Responsividade**
   - Melhorar mobile
   - PWA capabilities
   - Offline support

3. **UX/UI**
   - Design system
   - Acessibilidade (WCAG)
   - Feedback visual melhorado

### 5. DevOps

1. **CI/CD**
   - GitHub Actions
   - Testes automáticos
   - Deploy automatizado

2. **Monitoramento**
   - Logs centralizados
   - APM (Application Performance Monitoring)
   - Alertas

3. **Backup**
   - Backup automatizado
   - Restore testing
   - Disaster recovery

---

## 📚 Guia de Desenvolvimento

### Como Adicionar um Novo Controller

1. Criar arquivo em `application/controllers/`
2. Estender `MY_Controller` (ou `CI_Controller` se não precisar auth)
3. Implementar métodos necessários
4. Criar views correspondentes
5. Adicionar rotas se necessário

**Exemplo:**
```php
class MeuController extends MY_Controller {
    public function index() {
        $this->data['dados'] = $this->MeuModel->getAll();
        $this->load->view('meu/index', $this->data);
    }
}
```

### Como Adicionar um Novo Model

1. Criar arquivo em `application/models/`
2. Estender `CI_Model`
3. Implementar métodos CRUD
4. Usar Query Builder do CodeIgniter

**Exemplo:**
```php
class MeuModel extends CI_Model {
    public function getAll() {
        return $this->db->get('minha_tabela')->result();
    }
}
```

### Como Adicionar uma Nova Biblioteca

1. Criar arquivo em `application/libraries/`
2. Seguir convenção de nomenclatura
3. Carregar via `$this->load->library()`

### Como Adicionar um Novo Helper

1. Criar arquivo em `application/helpers/`
2. Nome: `meu_helper.php`
3. Funções globais
4. Carregar via `$this->load->helper()`

### Como Criar uma Migration

1. Criar arquivo em `application/database/migrations/`
2. Nome: `YYYYMMDDHHMMSS_nome_da_migration.php`
3. Implementar `up()` e `down()`
4. Executar: `php index.php tools migrate`

### Padrões de Código

1. **PSR-12** para estilo de código
2. **PSR-4** para autoloading
3. Comentários PHPDoc
4. Validação de entrada
5. Tratamento de erros

### Checklist para Novas Features

- [ ] Controller criado
- [ ] Model criado
- [ ] Views criadas
- [ ] Rotas configuradas
- [ ] Validações implementadas
- [ ] Permissões verificadas
- [ ] Testes escritos (se aplicável)
- [ ] Documentação atualizada
- [ ] Migrations criadas (se necessário)

---

## 📝 Notas Importantes

### Variáveis de Ambiente

Principais variáveis no `.env`:
- `APP_ENVIRONMENT` - Ambiente (development/production)
- `APP_BASEURL` - URL base
- `DB_*` - Configurações do banco
- `API_ENABLED` - Habilitar/desabilitar API

### Sistema de Versões

- Segue Semantic Versioning
- Changelog em `CHANGELOG.md`
- Versão atual: 4.53.0

### Contribuição

- Código em português (comentários e variáveis)
- Interface em português brasileiro
- Documentação em português

### Suporte

- Issues no GitHub
- Discussions para dúvidas
- Email: contato@mapos.com.br

---

## 🔗 Referências Úteis

- **CodeIgniter 3 Docs:** https://codeigniter.com/userguide3/
- **Composer:** https://getcomposer.org/doc/
- **Docker:** https://docs.docker.com/
- **MySQL:** https://dev.mysql.com/doc/

---

**Última Atualização:** 2026-01-29
**Versão do Documento:** 1.0.0

---

> Este documento deve ser atualizado conforme o projeto evolui. Mantenha-o sempre atualizado para facilitar o desenvolvimento e manutenção.
