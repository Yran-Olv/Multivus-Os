# 🔍 Verificação de Status das Migrations

## 📋 Migrations Criadas

### Migrations do Sistema Base
1. ✅ `20121031100537_create_base.php` - Criação da base do sistema
2. ✅ `20200306012421_add_cep_to_usuarios_table.php`
3. ✅ `20200428012421_add_contato_and_complemento_to_clientes_table.php`
4. ✅ `20200921012421_add_observacoes_to_vendas_table.php`
5. ✅ `20200921012422_add_observacoes_cliente_to_vendas_table.php`
6. ✅ `20200921012423_add_observacoes_to_lancamentos_table.php`
7. ✅ `20201224012424_add_cep_to_emitente_table.php`
8. ✅ `20201230231550_add_controle_cobrancas.php`
9. ✅ `20210105223548_add_cobrancas_cliente.php`
10. ✅ `20210107190526_fix_table_cobrancas.php`
11. ✅ `20210108201419_add_usuarios_lancamentos.php`
12. ✅ `20210110153941_feature_notificawhats.php`
13. ✅ `20210114151942_feature_control_baixaretroativa.php`
14. ✅ `20210114151943_drop_table_pagamento.php`
15. ✅ `20210114151944_add_payment_gateway_to_cobrancas.php`
16. ✅ `20210125023104_controle_editar_os.php`
17. ✅ `20210125151515_add_clientefornecedor.php`
18. ✅ `20210125173737_add_control_datatable.php`
19. ✅ `20210125173738_add_pix_key.php`
20. ✅ `20210125173739_add_os_status_list.php`
21. ✅ `20210125173740_add_aprovado_to_status_list.php`
22. ✅ `20210125173741_asaas_payment_gateway.php`
23. ✅ `20220216173741_upload_image_user.php`
24. ✅ `20220307173741_add_password_client.php`
25. ✅ `20220313023104_controle_editar_vendas.php`
26. ✅ `20220320173741_add_desconto_lancamentos_os_vendas.php`
27. ✅ `20221112173741_add_tipo_desconto_os_vendas.php`
28. ✅ `20221119210810_add_asaas_id_clientes.php`
29. ✅ `20221130180810_add_config_control_print_2ways_os.php`
30. ✅ `20230428110810_alter_charset_configuracoes.php`
31. ✅ `20240503170400_add_garantia_status_to_vendas_table.php`

### Migrations Novas (Criadas Recentemente)
32. ⚠️ `20250101000000_add_whatsapp_config.php` - **Configurações WhatsApp**
33. ⚠️ `20250101000001_create_pdv_tables.php` - **Tabelas do PDV**
34. ⚠️ `20250101000002_create_vendas_prazo_tables.php` - **Tabelas de Vendas a Prazo**

---

## 🔍 Como Verificar o Status

### Método 1: Via Interface Web (Recomendado)

1. Acesse: `/index.php/mapos/configurar`
2. Procure pela seção "Atualizar Banco de Dados"
3. Clique em "Atualizar Banco de Dados"
4. O sistema executará todas as migrations pendentes

### Método 2: Via Terminal/CLI

```bash
# Executar todas as migrations pendentes
php index.php tools migrate

# Ou se tiver acesso direto ao CodeIgniter
php index.php migrate
```

### Método 3: Via SQL (Verificação Manual)

Execute no banco de dados:

```sql
-- Verificar migrations executadas
SELECT * FROM migrations ORDER BY version;

-- Verificar se as novas tabelas existem
SHOW TABLES LIKE 'formas_pagamento';
SHOW TABLES LIKE 'pagamentos_venda';
SHOW TABLES LIKE 'caixas';
SHOW TABLES LIKE 'turnos_caixa';
SHOW TABLES LIKE 'cancelamentos_venda';
SHOW TABLES LIKE 'cupons_desconto';
SHOW TABLES LIKE 'parcelas_venda';
SHOW TABLES LIKE 'notificacoes_venda';
SHOW TABLES LIKE 'historico_pagamentos';

-- Verificar se as colunas foram adicionadas na tabela vendas
DESCRIBE vendas;
-- Procurar por: tipo_venda, numero_parcelas, turnos_caixa_id, caixas_id, etc.

-- Verificar se as configurações WhatsApp existem
SELECT * FROM configuracoes WHERE config LIKE 'whatsapp%';
```

### Método 4: Script PHP de Verificação

Execute o script criado:

```bash
php verificar_migrations.php
```

---

## ✅ Checklist de Verificação

### Tabelas do PDV
- [ ] `formas_pagamento` existe?
- [ ] `pagamentos_venda` existe?
- [ ] `caixas` existe?
- [ ] `turnos_caixa` existe?
- [ ] `cancelamentos_venda` existe?
- [ ] `cupons_desconto` existe?

### Tabelas de Vendas a Prazo
- [ ] `parcelas_venda` existe?
- [ ] `notificacoes_venda` existe?
- [ ] `historico_pagamentos` existe?

### Modificações na Tabela `vendas`
- [ ] Coluna `tipo_venda` existe?
- [ ] Coluna `numero_parcelas` existe?
- [ ] Coluna `intervalo_parcelas` existe?
- [ ] Coluna `taxa_juros` existe?
- [ ] Coluna `taxa_multa` existe?
- [ ] Coluna `valor_total_parcelado` existe?
- [ ] Coluna `valor_pago_total` existe?
- [ ] Coluna `valor_pendente` existe?
- [ ] Coluna `data_primeiro_vencimento` existe?
- [ ] Coluna `notificar_atraso` existe?
- [ ] Coluna `dias_antes_notificar` existe?
- [ ] Coluna `turnos_caixa_id` existe?
- [ ] Coluna `caixas_id` existe?

### Configurações WhatsApp
- [ ] `whatsapp_api_token` existe em `configuracoes`?
- [ ] `whatsapp_api_url` existe em `configuracoes`?
- [ ] `whatsapp_enabled` existe em `configuracoes`?
- [ ] `whatsapp_send_signature` existe em `configuracoes`?
- [ ] `whatsapp_close_ticket` existe em `configuracoes`?

---

## 🚨 Se as Migrations Não Foram Executadas

### Opção 1: Executar via Interface Web
1. Acesse `/index.php/mapos/configurar`
2. Clique em "Atualizar Banco de Dados"

### Opção 2: Executar via Terminal
```bash
php index.php tools migrate
```

### Opção 3: Executar Manualmente via SQL
Se não conseguir executar via CodeIgniter, você pode executar o SQL manualmente:

1. Abra cada arquivo de migration em `application/database/migrations/`
2. Copie o conteúdo do método `up()`
3. Execute no banco de dados

**⚠️ ATENÇÃO:** Execute apenas as migrations que ainda não foram executadas!

---

## 📊 Query SQL para Verificar Tudo

```sql
-- Ver todas as migrations executadas
SELECT version FROM migrations ORDER BY version;

-- Verificar todas as tabelas criadas pelas novas migrations
SELECT 
    TABLE_NAME,
    CASE 
        WHEN TABLE_NAME IN ('formas_pagamento', 'pagamentos_venda', 'caixas', 'turnos_caixa', 'cancelamentos_venda', 'cupons_desconto') THEN 'PDV'
        WHEN TABLE_NAME IN ('parcelas_venda', 'notificacoes_venda', 'historico_pagamentos') THEN 'Vendas a Prazo'
        ELSE 'Outras'
    END as categoria
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME IN (
    'formas_pagamento', 'pagamentos_venda', 'caixas', 'turnos_caixa', 
    'cancelamentos_venda', 'cupons_desconto', 'parcelas_venda', 
    'notificacoes_venda', 'historico_pagamentos'
)
ORDER BY categoria, TABLE_NAME;

-- Verificar colunas adicionadas na tabela vendas
SELECT 
    COLUMN_NAME,
    COLUMN_TYPE,
    IS_NULLABLE,
    COLUMN_DEFAULT
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'vendas'
AND COLUMN_NAME IN (
    'tipo_venda', 'numero_parcelas', 'intervalo_parcelas', 'taxa_juros',
    'taxa_multa', 'valor_total_parcelado', 'valor_pago_total', 'valor_pendente',
    'data_primeiro_vencimento', 'notificar_atraso', 'dias_antes_notificar',
    'turnos_caixa_id', 'caixas_id'
)
ORDER BY COLUMN_NAME;

-- Verificar configurações WhatsApp
SELECT config, valor FROM configuracoes WHERE config LIKE 'whatsapp%';
```

---

## 🎯 Resumo das Migrations Pendentes

### Migration 1: WhatsApp Config
**Arquivo:** `20250101000000_add_whatsapp_config.php`
**O que faz:** Adiciona configurações do WhatsApp na tabela `configuracoes`
**Tabelas afetadas:** `configuracoes`

### Migration 2: PDV Tables
**Arquivo:** `20250101000001_create_pdv_tables.php`
**O que faz:** Cria tabelas para o sistema PDV
**Tabelas criadas:**
- `formas_pagamento`
- `pagamentos_venda`
- `caixas`
- `turnos_caixa`
- `cancelamentos_venda`
- `cupons_desconto`
**Tabelas modificadas:** `vendas` (adiciona `turnos_caixa_id`, `caixas_id`)

### Migration 3: Vendas a Prazo
**Arquivo:** `20250101000002_create_vendas_prazo_tables.php`
**O que faz:** Cria tabelas para vendas a prazo
**Tabelas criadas:**
- `parcelas_venda`
- `notificacoes_venda`
- `historico_pagamentos`
**Tabelas modificadas:** `vendas` (adiciona campos de vendas a prazo)

---

**Última atualização:** <?= date('d/m/Y H:i:s') ?>
