-- ============================================
-- Script SQL para Verificar Status das Migrations
-- Execute este script no seu banco de dados
-- ============================================

-- 1. Verificar migrations executadas
SELECT 
    'MIGRATIONS EXECUTADAS' as tipo,
    version as versao
FROM migrations 
ORDER BY version;

-- 2. Verificar se as tabelas do PDV existem
SELECT 
    'TABELAS PDV' as categoria,
    TABLE_NAME as tabela,
    CASE 
        WHEN TABLE_NAME IS NOT NULL THEN '✅ EXISTE'
        ELSE '❌ NÃO EXISTE'
    END as status
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME IN (
    'formas_pagamento', 
    'pagamentos_venda', 
    'caixas', 
    'turnos_caixa', 
    'cancelamentos_venda', 
    'cupons_desconto'
)
ORDER BY TABLE_NAME;

-- 3. Verificar se as tabelas de Vendas a Prazo existem
SELECT 
    'TABELAS VENDAS A PRAZO' as categoria,
    TABLE_NAME as tabela,
    CASE 
        WHEN TABLE_NAME IS NOT NULL THEN '✅ EXISTE'
        ELSE '❌ NÃO EXISTE'
    END as status
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME IN (
    'parcelas_venda', 
    'notificacoes_venda', 
    'historico_pagamentos'
)
ORDER BY TABLE_NAME;

-- 4. Verificar colunas adicionadas na tabela vendas (PDV)
SELECT 
    'COLUNAS PDV EM VENDAS' as categoria,
    COLUMN_NAME as coluna,
    COLUMN_TYPE as tipo,
    CASE 
        WHEN COLUMN_NAME IS NOT NULL THEN '✅ EXISTE'
        ELSE '❌ NÃO EXISTE'
    END as status
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'vendas'
AND COLUMN_NAME IN (
    'turnos_caixa_id',
    'caixas_id'
)
ORDER BY COLUMN_NAME;

-- 5. Verificar colunas adicionadas na tabela vendas (Vendas a Prazo)
SELECT 
    'COLUNAS VENDAS A PRAZO EM VENDAS' as categoria,
    COLUMN_NAME as coluna,
    COLUMN_TYPE as tipo,
    CASE 
        WHEN COLUMN_NAME IS NOT NULL THEN '✅ EXISTE'
        ELSE '❌ NÃO EXISTE'
    END as status
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'vendas'
AND COLUMN_NAME IN (
    'tipo_venda',
    'numero_parcelas',
    'intervalo_parcelas',
    'taxa_juros',
    'taxa_multa',
    'valor_total_parcelado',
    'valor_pago_total',
    'valor_pendente',
    'data_primeiro_vencimento',
    'notificar_atraso',
    'dias_antes_notificar'
)
ORDER BY COLUMN_NAME;

-- 6. Verificar configurações WhatsApp
SELECT 
    'CONFIGURAÇÕES WHATSAPP' as categoria,
    config as configuracao,
    CASE 
        WHEN valor IS NOT NULL AND valor != '' THEN '✅ CONFIGURADO'
        ELSE '⚠️ VAZIO'
    END as status,
    valor as valor_atual
FROM configuracoes 
WHERE config LIKE 'whatsapp%'
ORDER BY config;

-- 7. Resumo Geral
SELECT 
    'RESUMO' as tipo,
    CONCAT(
        'Migrations executadas: ', 
        (SELECT COUNT(*) FROM migrations),
        ' | ',
        'Tabelas PDV: ',
        (SELECT COUNT(*) FROM information_schema.TABLES 
         WHERE TABLE_SCHEMA = DATABASE() 
         AND TABLE_NAME IN ('formas_pagamento', 'pagamentos_venda', 'caixas', 'turnos_caixa', 'cancelamentos_venda', 'cupons_desconto')),
        ' | ',
        'Tabelas Vendas a Prazo: ',
        (SELECT COUNT(*) FROM information_schema.TABLES 
         WHERE TABLE_SCHEMA = DATABASE() 
         AND TABLE_NAME IN ('parcelas_venda', 'notificacoes_venda', 'historico_pagamentos'))
    ) as informacao;
