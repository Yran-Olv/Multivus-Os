# Instalação via Docker

Este projeto agora suporta **apenas instalação via Docker**.

## 🌐 Suporte a Qualquer Domínio

O sistema aceita **qualquer domínio** que você possua. Você pode usar:
- Domínios com extensão `.com`, `.com.br`, `.net`, `.org`, etc.
- Subdomínios (ex: `sistema.empresa.com`)
- Qualquer outro domínio válido que você possua

Basta configurar o domínio no arquivo `.env` e apontar o DNS para o servidor.

## Pré-requisitos

- [Docker](https://docs.docker.com/install/) instalado
- [Docker Compose](https://docs.docker.com/compose/install/) instalado

## Configuração Inicial

1. Entre na pasta `docker`:
```bash
cd docker
```

2. Crie um arquivo `.env` baseado nas configurações abaixo:
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

3. Execute o Docker Compose:
```bash
docker-compose up --force-recreate -d
```

## Acessando o Sistema

1. Acesse a URL `http://localhost:8000/` no navegador e inicie a instalação.

2. Na etapa de configuração use as seguintes configurações:
   - **Host:** mysql
   - **Usuário:** mapos
   - **Senha:** mapos
   - **Banco de Dados:** mapos

3. Configure o email de envio em Configurações > Sistema > E-mail.

## Acessos

- **Aplicação:** http://localhost:8000/
- **phpMyAdmin:** http://localhost:8080/

## Configuração para Produção com Domínio Próprio

Você pode usar **qualquer domínio** que possua em produção. O sistema aceita qualquer domínio válido (ex: `meudominio.com.br`, `sistema.empresa.com`, `app.exemplo.net`, etc.).

### 1. Configuração do arquivo .env

Atualize o arquivo `.env` na pasta `docker` substituindo `SEU_DOMINIO_AQUI` pelo seu domínio real:

```env
# Configurações do Nginx - Produção
# Substitua SEU_DOMINIO_AQUI pelo seu domínio (ex: meudominio.com.br, sistema.empresa.com, etc.)
NGINX_HOST=SEU_DOMINIO_AQUI
NGINX_PORT=80

# Para HTTPS (recomendado em produção), use:
# NGINX_PORT=443

# Configurações do MySQL
MYSQL_MAPOS_VERSION=8.0
MYSQL_MAPOS_HOST=mysql
MYSQL_MAPOS_DATABASE=mapos
MYSQL_MAPOS_USER=mapos
MYSQL_MAPOS_PASSWORD=mapos
MYSQL_MAPOS_ROOT_PASSWORD=root
MYSQL_MAPOS_PORT=3306

# Configurações do phpMyAdmin (opcional em produção)
PHP_MY_ADMIN_PORT=8080
```

### 2. Configuração do DNS

Configure o DNS do seu domínio para apontar para o IP do servidor:
- **Tipo A:** Apontar seu domínio (ex: `meudominio.com.br`) para o IP do servidor
- **Tipo A:** Apontar `www.seudominio.com.br` para o IP do servidor (opcional, se quiser suportar www)

**Exemplos de configuração DNS:**
- `meudominio.com.br` → IP do servidor
- `sistema.empresa.com` → IP do servidor
- `app.exemplo.net` → IP do servidor

### 3. Configuração da URL no Sistema

Durante a instalação do sistema, use a URL completa do seu domínio:
- **URL:** `http://SEU_DOMINIO_AQUI/` (ou `https://SEU_DOMINIO_AQUI/` se usar SSL)

**Exemplos:**
- `http://meudominio.com.br/`
- `https://sistema.empresa.com/`
- `http://app.exemplo.net/`

### 4. Configuração de HTTPS (Recomendado para Produção)

Para usar HTTPS em produção, você tem algumas opções:

#### Opção 1: Usar um Proxy Reverso (Recomendado)

A forma mais simples é usar um proxy reverso como **Nginx Proxy Manager** ou **Traefik** que gerencia SSL automaticamente com Let's Encrypt. Nesse caso:
- Configure o proxy para apontar para `http://localhost:8000` (ou a porta interna do container)
- O proxy gerencia o SSL e redireciona para o container

#### Opção 2: Configurar SSL diretamente no Nginx

1. **Obter certificado SSL** (Let's Encrypt recomendado):
```bash
# Instale o certbot
sudo apt-get install certbot

# Obtenha o certificado (substitua SEU_DOMINIO_AQUI pelo seu domínio)
sudo certbot certonly --standalone -d SEU_DOMINIO_AQUI
```

2. **Copiar certificados para a pasta docker**:
```bash
# Substitua SEU_DOMINIO_AQUI pelo seu domínio
sudo cp /etc/letsencrypt/live/SEU_DOMINIO_AQUI/fullchain.pem docker/certs/
sudo cp /etc/letsencrypt/live/SEU_DOMINIO_AQUI/privkey.pem docker/certs/
sudo chown -R $USER:$USER docker/certs/
```

3. **Atualizar o template do Nginx** (`docker/etc/nginx/default.template.conf`) para incluir SSL:
```nginx
server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name ${NGINX_HOST};
    
    ssl_certificate /etc/nginx/ssl/fullchain.pem;
    ssl_certificate_key /etc/nginx/ssl/privkey.pem;
    
    # ... resto da configuração
}

# Redirecionar HTTP para HTTPS
server {
    listen 80;
    listen [::]:80;
    server_name ${NGINX_HOST};
    return 301 https://$server_name$request_uri;
}
```

4. **Atualizar docker-compose.yml** para mapear volumes dos certificados e porta 443:
```yaml
nginx:
  volumes:
    - "./certs:/etc/nginx/ssl:ro"
  ports:
    - "80:80"
    - "443:443"
```

5. **Atualizar o .env** com `NGINX_PORT=443` (ou manter 80 se usar proxy reverso)

### 5. Reiniciar os Containers

Após alterar o arquivo `.env`, reinicie os containers:
```bash
docker-compose down
docker-compose up -d
```

## Importante

⚠️ **Cuide da pasta `docker/data`**, onde o MySQL do Docker salva os arquivos. Se for deletada você perderá seu banco de dados.

⚠️ **Em produção**, considere:
- Usar senhas fortes para o MySQL (altere `MYSQL_MAPOS_PASSWORD` e `MYSQL_MAPOS_ROOT_PASSWORD`)
- Configurar HTTPS/SSL
- Configurar firewall adequadamente (permitir apenas portas 80, 443 e SSH)
- Fazer backups regulares da pasta `docker/data/db/mysql`
- Restringir acesso ao phpMyAdmin (ou removê-lo completamente do docker-compose.yml em produção)
- Usar um proxy reverso (como Traefik ou Nginx Proxy Manager) para gerenciar SSL automaticamente
- Configurar logs e monitoramento

## Comandos Úteis

- **Iniciar os containers:** `docker-compose up -d`
- **Parar os containers:** `docker-compose down`
- **Ver logs:** `docker-compose logs -f`
- **Reconstruir containers:** `docker-compose up --force-recreate -d`
