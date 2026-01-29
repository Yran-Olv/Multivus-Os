
![MapOS](https://raw.githubusercontent.com/RamonSilva20/mapos/master/assets/img/logo.png)

![version](https://img.shields.io/badge/version-4.53.0-blue.svg?longCache=true&style=flat-square)
![license](https://img.shields.io/badge/license-Apache-green.svg?longCache=true&style=flat-square)
![theme](https://img.shields.io/badge/theme-Matrix--Admin-lightgrey.svg?longCache=true&style=flat-square)
![issues](https://img.shields.io/github/issues/RamonSilva20/mapos.svg?longCache=true&style=flat-square)
![contributors](https://img.shields.io/github/contributors/RamonSilva20/mapos.svg?longCache=true&style=flat-square)

### Contato: contato@mapos.com.br
### [Feedback](https://github.com/RamonSilva20/mapos/discussions) - Vote ou sugira melhorias

![Map-OS](https://raw.githubusercontent.com/RamonSilva20/mapos/master/docs/dashboard.png)

## ⚠️ IMPORTANTE: Instalação Apenas via Docker

**A partir de agora, este projeto suporta apenas instalação via Docker.** Os métodos de instalação manual foram descontinuados.

## 📖 Guia Completo de Instalação

Para um tutorial passo a passo completo e detalhado, consulte o **[Guia Completo de Instalação](docker/README.md)** na pasta `docker`.

O guia inclui:
- ✅ Instalação do Docker (Linux, Windows, macOS)
- ✅ Download e configuração do projeto
- ✅ Instalação passo a passo do sistema
- ✅ Configuração para produção
- ✅ Troubleshooting e soluções de problemas
- ✅ Comandos úteis e dicas

### Instalação Rápida (Resumo)

1. **Instale o Docker e Docker Compose:**
   - Linux: Siga as instruções no [guia completo](docker/README.md)
   - Windows: [Docker Desktop](https://www.docker.com/products/docker-desktop/)
   - macOS: [Docker Desktop](https://www.docker.com/products/docker-desktop/)

2. **Baixe o projeto:**
```bash
git clone https://github.com/RamonSilva20/mapos.git
cd mapos/docker
```

3. **Crie o arquivo `.env`:**
```env
NGINX_HOST=localhost
NGINX_PORT=8000
MYSQL_MAPOS_VERSION=8.0
MYSQL_MAPOS_HOST=mysql
MYSQL_MAPOS_DATABASE=mapos
MYSQL_MAPOS_USER=mapos
MYSQL_MAPOS_PASSWORD=mapos
MYSQL_MAPOS_ROOT_PASSWORD=root
MYSQL_MAPOS_PORT=3306
PHP_MY_ADMIN_PORT=8080
```

4. **Inicie os containers:**
```bash
docker compose up -d --force-recreate
```

5. **Acesse e instale:**
   - Acesse: `http://localhost:8000/`
   - Configure o banco de dados:
     - Host: `mysql`
     - Usuário: `mapos`
     - Senha: `mapos`
     - Banco: `mapos`

**Acessos:**
- **Aplicação:** http://localhost:8000/
- **phpMyAdmin:** http://localhost:8080/

**⚠️ Importante:** Cuide da pasta `docker/data`, onde o MySQL do Docker salva os arquivos. Se for deletada você perderá seu banco de dados.

**📚 Para instruções detalhadas, consulte o [Guia Completo](docker/README.md)**

### Configuração para Produção com Domínio Próprio

Você pode usar **qualquer domínio** que possua em produção. O sistema aceita qualquer domínio válido.

**Passos para configurar:**

1. Configure o arquivo `.env` na pasta `docker`:
   - Altere `NGINX_HOST=SEU_DOMINIO_AQUI` (substitua pelo seu domínio real)
   - Altere `NGINX_PORT=80` (ou `443` para HTTPS)
   
   **Exemplos de domínios válidos:**
   - `meudominio.com.br`
   - `sistema.empresa.com`
   - `app.exemplo.net`
   - Qualquer outro domínio que você possua

2. Configure o DNS do seu domínio para apontar para o IP do servidor (registro Tipo A)

3. Durante a instalação, use a URL completa do seu domínio: `http://SEU_DOMINIO_AQUI/`

4. Reinicie os containers: `docker-compose down && docker-compose up -d`

Para mais detalhes sobre configuração em produção, consulte o [README da pasta docker](docker/README.md).

### Atualização

1. Pare o docker de rodar:
```bash
cd docker
docker-compose down
```

2. Faça o backup dos arquivos e do banco de dados:
   - Logado como administrador vá em `configurações > backup`.
   - Dentro da pasta `Assets` copie as pastas `anexos`, `arquivos`, `uploads`, `userimage` e qualquer personalização feita dentro da pasta `img`.
   - Dentro da pasta `application` copie o arquivo `.env`.
   - **Importante:** Faça backup da pasta `docker/data/db/mysql` que contém os dados do banco de dados.

3. Substitua os arquivos pelos da nova versão.

4. Entre na pasta `docker` no seu terminal e rode o comando:
```bash
docker-compose up --force-recreate -d
```

5. Logue no sistema como administrador e navegue até Configurações -> Sistema e clique no botão `Atualizar Banco de Dados` para atualizar seu banco de dados.
   - **Alternativa:** Também é possível atualizar o banco de dados via terminal rodando o comando:
   ```bash
   docker-compose exec php-fpm php index.php tools migrate
   ```

6. Restaure os backups para seus locais devidos.

7. Pronto, sua atualização está concluída!

### Atualização via sistema (Docker)

1. Primeiro é necessário atualizar manualmente o sistema para a versão v4.4.0;
2. Quando estiver nessa versão é possível atualizar o sistema clicando no botão "Atualizar Mapos" em Sistema >> Configurações;
3. Serão baixados e atualizados todos os arquivos exceto: `config.php`, `database.php` e `email.php`;
4. Após a atualização, reinicie os containers Docker:
```bash
cd docker
docker-compose restart
```

### Comandos de terminal (Docker)

Para listar todos os comandos de terminal disponíveis, execute o comando:
```bash
cd docker
docker-compose exec php-fpm php index.php tools
```

Para executar comandos específicos, use:
```bash
docker-compose exec php-fpm php index.php tools [comando]
```

### Hospedagem Parceira
Em parceria com o Projeto Map-OS a SysGO oferece hospedagem de qualidade e suporte personalizado para usuários dos Map-OS com custo justo e confiabilidade.
Solicite sua hospedagem agora [Clique Aqui!](https://sysgo.com.br/mapos)

<p><img src="https://sysgo.com.br/img-externo/mapos-github.jpg" alt="SysGO - MAP-OS Cloud Hosting" style="width:50%;"></p>

### Frameworks/Bibliotecas
* [bcit-ci/CodeIgniter](https://github.com/bcit-ci/CodeIgniter)
* [twbs/bootstrap](https://github.com/twbs/bootstrap)
* [jquery/jquery](https://github.com/jquery/jquery)
* [jquery/jquery-ui](https://github.com/jquery/jquery-ui)
* [mpdf/mpdf](https://github.com/mpdf/mpdf)
* [Matrix Admin](http://wrappixel.com/demos/free-admin-templates/matrix-admin/index.html)
* [filp/whoops](https://github.com/filp/whoops)

### Requerimentos
* PHP >= 8.4
* MySQL >= 5.7 ou >= 8.0
* Composer >= 2

### Doações
Gosta do mapos e gostaria de contribuir com seu desenvolvimento?

Doações podem ser realizadas nos links:
* [catarse/mapos](https://www.catarse.me/mapos) - Mensal
* [kofi/mapos](https://ko-fi.com/mapos) - Exporádica

### Estrelas
[![Estrelas](https://api.star-history.com/svg?repos=RamonSilva20/mapos&type=Date)](https://star-history.com/#RamonSilva20/mapos&Date)

### Contribuidores
[![Contribuidores](https://contrib.rocks/image?repo=RamonSilva20/mapos)](https://github.com/RamonSilva20/mapos/graphs/contributors)

## Autor
| [<img src="https://avatars.githubusercontent.com/RamonSilva20?s=115"><br><sub>Ramon Silva</sub>](https://github.com/RamonSilva20) |
| :---: |
