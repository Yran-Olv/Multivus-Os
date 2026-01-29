# 📘 Guia Completo de Instalação - MapOS via Docker

Este projeto suporta **apenas instalação via Docker**. Este guia irá te levar passo a passo desde a instalação do Docker até o sistema funcionando completamente.

## 📋 Índice

1. [Pré-requisitos](#pré-requisitos)
2. [Instalação do Docker](#instalação-do-docker)
3. [Download do Projeto](#download-do-projeto)
4. [Configuração Inicial](#configuração-inicial)
5. [Instalação do Sistema](#instalação-do-sistema)
6. [Configuração Pós-Instalação](#configuração-pós-instalação)
7. [Configuração para Produção](#configuração-para-produção)
8. [Troubleshooting](#troubleshooting)
9. [Comandos Úteis](#comandos-úteis)

---

## 🌐 Suporte a Qualquer Domínio

O sistema aceita **qualquer domínio** que você possua. Você pode usar:
- Domínios com extensão `.com`, `.com.br`, `.net`, `.org`, etc.
- Subdomínios (ex: `sistema.empresa.com`)
- Qualquer outro domínio válido que você possua

Basta configurar o domínio no arquivo `.env` e apontar o DNS para o servidor.

---

## 📋 Pré-requisitos

Antes de começar, você precisa ter:

- **Sistema Operacional:** Linux, macOS ou Windows (com WSL2)
- **Memória RAM:** Mínimo 2GB (recomendado 4GB ou mais)
- **Espaço em Disco:** Mínimo 5GB livres
- **Acesso à Internet:** Para baixar imagens Docker e dependências

---

## 🐳 Instalação do Docker

### Linux (Ubuntu/Debian)

#### Passo 1: Atualizar o sistema
```bash
sudo apt-get update
sudo apt-get upgrade -y
```

#### Passo 2: Instalar dependências
```bash
sudo apt-get install -y \
    ca-certificates \
    curl \
    gnupg \
    lsb-release
```

#### Passo 3: Adicionar chave GPG do Docker
```bash
sudo mkdir -p /etc/apt/keyrings
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /etc/apt/keyrings/docker.gpg
```

#### Passo 4: Adicionar repositório Docker
```bash
echo \
  "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/ubuntu \
  $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null
```

#### Passo 5: Instalar Docker e Docker Compose
```bash
sudo apt-get update
sudo apt-get install -y docker-ce docker-ce-cli containerd.io docker-compose-plugin
```

#### Passo 6: Adicionar seu usuário ao grupo docker (para não precisar usar sudo)
```bash
sudo usermod -aG docker $USER
newgrp docker
```

#### Passo 7: Verificar instalação
```bash
docker --version
docker compose version
```

Você deve ver algo como:
```
Docker version 24.0.0, build ...
Docker Compose version v2.20.0
```

### Windows

1. Baixe o [Docker Desktop para Windows](https://www.docker.com/products/docker-desktop/)
2. Execute o instalador e siga as instruções
3. Reinicie o computador quando solicitado
4. Abra o Docker Desktop e aguarde a inicialização
5. Verifique a instalação abrindo PowerShell ou CMD:
```powershell
docker --version
docker compose version
```

### macOS

1. Baixe o [Docker Desktop para Mac](https://www.docker.com/products/docker-desktop/)
2. Execute o instalador e arraste o Docker para a pasta Applications
3. Abra o Docker Desktop
4. Verifique a instalação:
```bash
docker --version
docker compose version
```

---

## 📥 Download do Projeto

### Opção 1: Via Git (Recomendado)

```bash
# Clone o repositório
git clone https://github.com/RamonSilva20/mapos.git

# Entre na pasta do projeto
cd mapos
```

### Opção 2: Download ZIP

1. Acesse: https://github.com/RamonSilva20/mapos/releases
2. Baixe a versão mais recente (arquivo `.zip`)
3. Extraia o arquivo
4. Entre na pasta extraída:
```bash
cd mapos
```

---

## ⚙️ Configuração Inicial

### Passo 1: Navegar para a pasta docker

```bash
cd docker
```

### Passo 2: Criar arquivo .env

Crie um arquivo chamado `.env` na pasta `docker` com o seguinte conteúdo:

**Para desenvolvimento/teste (localhost):**
```env
# Configurações do Nginx
NGINX_HOST=localhost
NGINX_PORT=8000

# Configurações do MySQL
MYSQL_MAPOS_VERSION=8.0
MYSQL_MAPOS_HOST=mysql
MYSQL_MAPOS_DATABASE=mapos
MYSQL_MAPOS_USER=mapos
MYSQL_MAPOS_PASSWORD=mapos
MYSQL_MAPOS_ROOT_PASSWORD=root
MYSQL_MAPOS_PORT=3306

# Configurações do phpMyAdmin
PHP_MY_ADMIN_PORT=8080
```

**Como criar o arquivo:**

**Linux/macOS:**
```bash
nano .env
# Cole o conteúdo acima, pressione Ctrl+X, depois Y, depois Enter para salvar
```

**Windows (PowerShell):**
```powershell
New-Item -Path .env -ItemType File
notepad .env
# Cole o conteúdo e salve
```

### Passo 3: Verificar se o arquivo foi criado corretamente

```bash
# Linux/macOS
cat .env

# Windows (PowerShell)
Get-Content .env
```

Você deve ver todas as variáveis listadas acima.

---

## 🚀 Instalação do Sistema

### Passo 1: Iniciar os containers Docker

```bash
docker compose up -d --force-recreate
```

**O que este comando faz:**
- `up`: Inicia os containers
- `-d`: Executa em modo detached (background)
- `--force-recreate`: Recria os containers mesmo se já existirem

**Aguarde alguns minutos** enquanto o Docker:
- Baixa as imagens necessárias (nginx, mysql, php-fpm, phpmyadmin)
- Constrói as imagens personalizadas
- Inicia todos os serviços

### Passo 2: Verificar se os containers estão rodando

```bash
docker compose ps
```

Você deve ver algo como:
```
NAME          STATUS          PORTS
nginx         Up 2 minutes     0.0.0.0:8000->8000/tcp
php-fpm       Up 2 minutes     
mysql         Up 2 minutes     0.0.0.0:3306->3306/tcp
phpmyadmin    Up 2 minutes     0.0.0.0:8080->80/tcp
composer      Exited (0)       
```

Todos os containers principais devem estar com status "Up".

### Passo 3: Verificar logs (se houver problemas)

```bash
# Ver todos os logs
docker compose logs

# Ver logs de um serviço específico
docker compose logs nginx
docker compose logs mysql
docker compose logs php-fpm

# Acompanhar logs em tempo real
docker compose logs -f
```

### Passo 4: Acessar o sistema no navegador

1. Abra seu navegador
2. Acesse: `http://localhost:8000/`
3. Você deve ver a tela de instalação do MapOS

**Se não conseguir acessar:**
- Verifique se os containers estão rodando: `docker compose ps`
- Verifique se a porta 8000 está livre: `netstat -tuln | grep 8000` (Linux) ou `netstat -an | findstr 8000` (Windows)
- Verifique os logs: `docker compose logs nginx`

---

## 🎯 Instalação do Sistema (Assistente Web)

### Passo 1: Iniciar a instalação

1. No navegador, acesse `http://localhost:8000/`
2. Você será redirecionado para `/install`
3. Clique em **"Próximo"** ou **"Next"**

### Passo 2: Configuração do Banco de Dados

Preencha os campos com as seguintes informações:

```
Host do Banco de Dados: mysql
Nome de Usuário: mapos
Senha: mapos
Nome do Banco de Dados: mapos
```

**⚠️ Importante:** Use exatamente essas informações, pois são as configuradas no arquivo `.env`.

### Passo 3: Configuração do Administrador

Preencha os dados do primeiro usuário administrador:

```
Nome Completo: [Seu Nome]
E-mail: [Seu E-mail]
Senha: [Sua Senha]
Confirmar Senha: [Confirme a Senha]
```

**⚠️ Anote essas informações!** Você precisará delas para fazer login.

### Passo 4: Configuração da URL

Para desenvolvimento local, use:
```
URL: http://localhost:8000/
```

**Para produção com domínio próprio**, use:
```
URL: http://seudominio.com.br/
```

### Passo 5: Finalizar instalação

1. Clique em **"Instalar"** ou **"Install"**
2. Aguarde o processo de instalação (pode levar alguns minutos)
3. Quando concluir, você verá uma mensagem de sucesso
4. Clique em **"Ir para página de login"** ou **"Go to login page"**

### Passo 6: Primeiro Login

1. Use o e-mail e senha que você configurou no Passo 3
2. Faça login no sistema
3. Parabéns! O sistema está instalado e funcionando! 🎉

---

## 🔧 Configuração Pós-Instalação

### 1. Configurar E-mail

1. Faça login no sistema
2. Vá em **Configurações > Sistema > E-mail**
3. Preencha as informações do seu servidor SMTP:

```
Protocolo: SMTP
Host SMTP: smtp.seudominio.com.br (ou seu servidor SMTP)
Criptografia: TLS ou SSL
Porta: 587 (TLS) ou 465 (SSL)
Usuário: seu-email@seudominio.com.br
Senha: sua-senha-de-email
```

4. Clique em **Salvar**

**Teste o envio de e-mail:**
- Vá em **E-mail > Enviar E-mail de Teste**
- Digite um e-mail de teste
- Clique em **Enviar**

### 2. Verificar Cron Jobs (Envio Automático de E-mails)

Os cron jobs já estão configurados automaticamente no Docker. Eles executam:
- A cada 2 minutos: Processa e envia e-mails pendentes
- A cada 5 minutos: Tenta reenviar e-mails com falha

**Para verificar se estão funcionando:**
```bash
docker compose exec php-fpm crontab -l
```

Você deve ver os cron jobs listados.

### 3. Acessar phpMyAdmin (Opcional)

1. Acesse: `http://localhost:8080/`
2. Use as credenciais:
   - **Servidor:** mysql
   - **Usuário:** mapos
   - **Senha:** mapos

---

## 🌐 Configuração para Produção

### Passo 1: Configurar Domínio no .env

Edite o arquivo `.env` na pasta `docker`:

```env
# Configurações do Nginx - Produção
# Substitua SEU_DOMINIO_AQUI pelo seu domínio real
NGINX_HOST=seudominio.com.br
NGINX_PORT=80

# Configurações do MySQL (use senhas fortes em produção!)
MYSQL_MAPOS_VERSION=8.0
MYSQL_MAPOS_HOST=mysql
MYSQL_MAPOS_DATABASE=mapos
MYSQL_MAPOS_USER=mapos
MYSQL_MAPOS_PASSWORD=senha_forte_aqui
MYSQL_MAPOS_ROOT_PASSWORD=senha_root_forte_aqui
MYSQL_MAPOS_PORT=3306

# Configurações do phpMyAdmin (considere remover em produção)
PHP_MY_ADMIN_PORT=8080
```

**⚠️ Importante em Produção:**
- Use senhas fortes e únicas
- Considere remover o phpMyAdmin do `docker-compose.yml` em produção
- Configure HTTPS/SSL (veja seção abaixo)

### Passo 2: Configurar DNS

No painel de controle do seu provedor de domínio:

1. Acesse as configurações de DNS
2. Adicione um registro do tipo **A**:
   - **Nome/Host:** @ (ou deixe em branco)
   - **Valor/IP:** IP do seu servidor
   - **TTL:** 3600 (ou padrão)

3. (Opcional) Adicione registro para www:
   - **Nome/Host:** www
   - **Valor/IP:** IP do seu servidor
   - **TTL:** 3600

**Aguarde a propagação DNS** (pode levar de alguns minutos a 48 horas).

### Passo 3: Reiniciar Containers

```bash
docker compose down
docker compose up -d
```

### Passo 4: Configurar HTTPS (Recomendado)

#### Opção A: Usar Proxy Reverso (Mais Fácil)

Use **Nginx Proxy Manager** ou **Traefik** que gerencia SSL automaticamente:

1. Instale o proxy reverso
2. Configure para apontar para `http://localhost:8000`
3. O proxy gerencia o SSL com Let's Encrypt automaticamente

#### Opção B: SSL Direto no Nginx

1. **Instalar Certbot:**
```bash
sudo apt-get update
sudo apt-get install certbot
```

2. **Obter Certificado:**
```bash
sudo certbot certonly --standalone -d seudominio.com.br
```

3. **Copiar Certificados:**
```bash
sudo mkdir -p docker/certs
sudo cp /etc/letsencrypt/live/seudominio.com.br/fullchain.pem docker/certs/
sudo cp /etc/letsencrypt/live/seudominio.com.br/privkey.pem docker/certs/
sudo chown -R $USER:$USER docker/certs/
```

4. **Atualizar docker-compose.yml** para mapear certificados e porta 443 (veja seção avançada abaixo)

### Passo 5: Atualizar URL no Sistema

Se você já instalou o sistema com localhost, precisa atualizar a URL:

1. Acesse o sistema como administrador
2. Vá em **Configurações > Sistema**
3. Atualize a URL base para: `https://seudominio.com.br/`

---

## 🔍 Troubleshooting

### Problema: Containers não iniciam

**Solução:**
```bash
# Ver logs detalhados
docker compose logs

# Verificar se as portas estão em uso
# Linux
netstat -tuln | grep 8000
# Windows
netstat -an | findstr 8000

# Parar containers e reiniciar
docker compose down
docker compose up -d
```

### Problema: Erro de conexão com banco de dados

**Solução:**
1. Verifique se o MySQL está rodando:
```bash
docker compose ps mysql
```

2. Verifique os logs do MySQL:
```bash
docker compose logs mysql
```

3. Aguarde alguns segundos após iniciar os containers (MySQL precisa de tempo para inicializar)

### Problema: Página em branco ou erro 502

**Solução:**
```bash
# Verificar logs do PHP-FPM
docker compose logs php-fpm

# Verificar permissões
docker compose exec php-fpm ls -la /var/www/html

# Reiniciar containers
docker compose restart
```

### Problema: Não consigo acessar http://localhost:8000

**Soluções:**
1. Verifique se os containers estão rodando: `docker compose ps`
2. Verifique se a porta está correta no `.env`: `NGINX_PORT=8000`
3. Tente acessar pelo IP: `http://127.0.0.1:8000/`
4. Verifique firewall:
```bash
# Linux
sudo ufw status
sudo ufw allow 8000
```

### Problema: Erro ao instalar dependências do Composer

**Solução:**
```bash
# Executar composer manualmente
docker compose exec php-fpm composer install --no-dev --ignore-platform-reqs
```

### Problema: Domínio não funciona em produção

**Soluções:**
1. Verifique se o DNS está propagado: `nslookup seudominio.com.br`
2. Verifique se o firewall permite portas 80 e 443
3. Verifique se o `.env` tem o domínio correto
4. Reinicie os containers: `docker compose restart`

---

## 🛠️ Comandos Úteis

### Gerenciamento de Containers

```bash
# Iniciar containers
docker compose up -d

# Parar containers
docker compose down

# Parar e remover volumes (⚠️ apaga banco de dados!)
docker compose down -v

# Reiniciar containers
docker compose restart

# Ver status dos containers
docker compose ps

# Ver logs
docker compose logs -f

# Ver logs de um serviço específico
docker compose logs -f nginx
```

### Acesso aos Containers

```bash
# Acessar container PHP-FPM
docker compose exec php-fpm bash

# Acessar container MySQL
docker compose exec mysql bash

# Executar comandos PHP
docker compose exec php-fpm php index.php tools

# Executar Composer
docker compose exec php-fpm composer install
```

### Backup e Restore

```bash
# Backup do banco de dados
docker compose exec mysql mysqldump -u mapos -pmapos mapos > backup.sql

# Restore do banco de dados
docker compose exec -T mysql mysql -u mapos -pmapos mapos < backup.sql

# Backup da pasta de dados
tar -czf backup-dados.tar.gz docker/data/
```

### Limpeza

```bash
# Remover containers parados
docker compose rm

# Limpar imagens não utilizadas
docker image prune

# Limpar tudo (⚠️ cuidado!)
docker system prune -a
```

---

## ⚠️ Importante

### Segurança em Produção

- ✅ Use senhas fortes para MySQL
- ✅ Configure HTTPS/SSL
- ✅ Configure firewall (permitir apenas 80, 443, SSH)
- ✅ Faça backups regulares
- ✅ Remova ou restrinja acesso ao phpMyAdmin
- ✅ Mantenha o Docker e imagens atualizadas
- ✅ Configure logs e monitoramento

### Backup

**⚠️ SEMPRE faça backup antes de atualizar!**

A pasta `docker/data/db/mysql` contém todos os dados do banco de dados. Faça backup regularmente:

```bash
# Backup completo
tar -czf backup-$(date +%Y%m%d).tar.gz docker/data/
```

### Atualização

Consulte a seção de atualização no README principal do projeto.

---

## 📞 Suporte

- **Documentação:** Consulte o README principal
- **Issues:** https://github.com/RamonSilva20/mapos/issues
- **Discussions:** https://github.com/RamonSilva20/mapos/discussions

---

**Pronto!** Seu sistema MapOS está instalado e funcionando via Docker! 🎉
