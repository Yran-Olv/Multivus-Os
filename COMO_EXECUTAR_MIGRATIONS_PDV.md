# 🚀 Como Executar as Migrations do PDV

## 📋 Passo a Passo

### Opção 1: Via Interface Web (Recomendado)

1. **Acesse o sistema como administrador**
   - URL: `http://localhost:8000/index.php/mapos/configurar`

2. **Navegue até a aba "Atualização"**
   - Clique na aba "Atualização" no menu de configurações

3. **Clique no botão "Banco de Dados"**
   - Você verá um botão amarelo com o ícone de sincronização
   - Isso abrirá um modal de confirmação

4. **Confirme a atualização**
   - O modal pedirá confirmação
   - **Recomendação:** Faça um backup antes (há um link no modal)
   - Clique em "Atualizar"

5. **Aguarde a conclusão**
   - O sistema executará todas as migrations pendentes
   - Você verá uma mensagem de sucesso ou erro

### Opção 2: Via Terminal (Docker)

Se você estiver usando Docker, pode executar via terminal:

```bash
cd docker
docker-compose exec php-fpm php index.php tools migrate
```

### Opção 3: Via Terminal (Sem Docker)

Se não estiver usando Docker:

```bash
php index.php tools migrate
```

---

## ✅ Verificação

Após executar as migrations, você pode verificar se as tabelas foram criadas:

### Tabelas que devem ser criadas:

1. **`formas_pagamento`** - Formas de pagamento disponíveis
2. **`pagamentos_venda`** - Pagamentos de cada venda
3. **`caixas`** - Caixas do estabelecimento
4. **`turnos_caixa`** - Turnos de trabalho (abertura/fechamento)
5. **`cancelamentos_venda`** - Cancelamentos com motivo
6. **`cupons_desconto`** - Sistema de cupons de desconto

### Verificar via SQL:

```sql
-- Verificar se as tabelas existem
SHOW TABLES LIKE '%caixas%';
SHOW TABLES LIKE '%formas_pagamento%';
SHOW TABLES LIKE '%turnos_caixa%';

-- Verificar migrations executadas
SELECT * FROM migrations ORDER BY version DESC;
```

---

## 🔧 Troubleshooting

### Erro: "Migration failed"

1. **Verifique os logs:**
   - Arquivo: `application/logs/log-YYYY-MM-DD.php`
   - Procure por erros relacionados às migrations

2. **Verifique permissões:**
   - A pasta `application/database/migrations/` deve ter permissão de leitura
   - O banco de dados deve ter permissão para criar tabelas

3. **Verifique se há conflitos:**
   - Algumas colunas podem já existir na tabela `vendas`
   - A migration verifica antes de adicionar colunas

### Erro: "Table already exists"

Se alguma tabela já existir, você pode:
1. Fazer backup da tabela
2. Dropar a tabela manualmente
3. Executar a migration novamente

**⚠️ CUIDADO:** Sempre faça backup antes de dropar tabelas!

---

## 📝 Próximos Passos

Após executar as migrations com sucesso:

1. **Criar um caixa:**
   - Acesse `/index.php/vendas/abrirCaixa`
   - Crie pelo menos um caixa no sistema

2. **Configurar formas de pagamento:**
   - As formas de pagamento padrão serão criadas automaticamente
   - Você pode adicionar mais em: `formas_pagamento`

3. **Abrir um turno de caixa:**
   - Acesse `/index.php/vendas/abrirCaixa`
   - Selecione um caixa e informe o valor de abertura

4. **Usar o PDV:**
   - Acesse `/index.php/vendas/pdv`
   - Comece a fazer vendas!

---

## 📞 Suporte

Se encontrar problemas, verifique:
- Logs do sistema em `application/logs/`
- Mensagens de erro no navegador (F12 > Console)
- Status das migrations na tabela `migrations`

---

**Última atualização:** <?= date('d/m/Y H:i:s') ?>
