# 📋 Implementação: Sistema de Vendas a Prazo

## ✅ O que foi implementado

### 1. **Banco de Dados**

#### Migration: `20250101000002_create_vendas_prazo_tables.php`
- **Tabela `parcelas_venda`**: Armazena todas as parcelas das vendas a prazo
  - Campos: valor, vencimento, status, juros, multa, desconto, etc.
- **Tabela `notificacoes_venda`**: Sistema de notificações
  - Tipos: atraso, vencendo_hoje, vencendo_proximo, pagamento_recebido
  - Prioridades: baixa, media, alta, urgente
- **Tabela `historico_pagamentos`**: Histórico de todos os pagamentos
- **Modificações na tabela `vendas`**:
  - `tipo_venda` (avista/aprazo)
  - `numero_parcelas`
  - `intervalo_parcelas`
  - `taxa_juros`, `taxa_multa`
  - `valor_total_parcelado`, `valor_pago_total`, `valor_pendente`
  - `data_primeiro_vencimento`
  - `notificar_atraso`, `dias_antes_notificar`

### 2. **Model: `Vendas_prazo_model.php`**

#### Funcionalidades:
- ✅ `criarParcelas()` - Cria parcelas automaticamente ao faturar venda a prazo
- ✅ `getParcelas()` - Busca parcelas de uma venda
- ✅ `registrarPagamento()` - Registra pagamento de parcela
- ✅ `atualizarParcelasAtrasadas()` - Atualiza status e calcula juros/multa
- ✅ `criarNotificacao()` - Cria notificações automáticas
- ✅ `getNotificacoes()` - Busca notificações com filtros
- ✅ `marcarNotificacaoLida()` - Marca notificação como lida
- ✅ `countNotificacoesNaoLidas()` - Conta notificações não lidas
- ✅ `buscarVendasPrazo()` - Busca vendas com filtros avançados
- ✅ `getEstatisticas()` - Estatísticas de vendas a prazo
- ✅ `getHistoricoPagamentos()` - Histórico de pagamentos de uma parcela

### 3. **Controller: `Vendas_prazo.php`**

#### Rotas:
- ✅ `/vendas_prazo` - Listagem de vendas a prazo com filtros
- ✅ `/vendas_prazo/visualizar/{id}` - Detalhes da venda e parcelas
- ✅ `/vendas_prazo/registrarPagamento` - Registrar pagamento (AJAX)
- ✅ `/vendas_prazo/getParcelas/{id}` - Obter parcelas (AJAX)
- ✅ `/vendas_prazo/getNotificacoes` - Obter notificações (AJAX)
- ✅ `/vendas_prazo/marcarNotificacaoLida/{id}` - Marcar como lida
- ✅ `/vendas_prazo/countNotificacoesNaoLidas` - Contar não lidas
- ✅ `/vendas_prazo/atualizarParcelasAtrasadas` - Atualizar parcelas (pode ser cron)
- ✅ `/vendas_prazo/criarNotificacoesVencimento` - Criar notificações de vencimento
- ✅ `/vendas_prazo/relatorio` - Relatório de vendas a prazo

### 4. **Modificações no Controller `Vendas.php`**

#### Método `faturar()` atualizado:
- ✅ Suporte a venda à vista (comportamento original)
- ✅ Suporte a venda a prazo com criação automática de parcelas
- ✅ Configuração de número de parcelas, intervalo, juros e multa
- ✅ Integração com `Vendas_prazo_model` para criar parcelas

### 5. **Views Criadas**

#### `vendas_prazo/listar.php`:
- ✅ Listagem de vendas a prazo
- ✅ Filtros: cliente, status, datas, vencimento
- ✅ Estatísticas em cards
- ✅ Indicadores visuais (badges) para status das parcelas
- ✅ Links para visualizar vendas

#### `vendas_prazo/visualizar.php`:
- ✅ Informações completas da venda
- ✅ Lista de produtos
- ✅ Tabela de parcelas com status
- ✅ Modal para registrar pagamento
- ✅ Cálculo automático de juros e multa
- ✅ Histórico de pagamentos

#### `vendas_prazo/relatorio.php`:
- ✅ Relatório com estatísticas do período
- ✅ Filtros por data
- ✅ Lista de vendas atrasadas
- ✅ Cards com métricas principais

### 6. **Modificações nas Views Existentes**

#### `vendas/editarVenda.php`:
- ✅ Modal de faturar atualizado
- ✅ Opção de escolher "À Vista" ou "A Prazo"
- ✅ Campos para configurar parcelas (número, intervalo, juros, multa)
- ✅ JavaScript para mostrar/ocultar opções de prazo
- ✅ Validação de campos

#### `mapos/painel.php` (Dashboard):
- ✅ Widget de notificações de vendas a prazo
- ✅ Badge com contador de notificações não lidas
- ✅ Lista de notificações recentes
- ✅ Botões para marcar como lida e ver venda
- ✅ Widget de estatísticas de vendas a prazo
- ✅ JavaScript para atualizar notificações automaticamente

### 7. **Sistema de Notificações**

#### Tipos de Notificação:
- ✅ **Atraso**: Parcela em atraso (criada automaticamente)
- ✅ **Vencendo Próximo**: Parcela vencendo em breve (configurável)
- ✅ **Pagamento Recebido**: Quando parcela é paga
- ✅ **Outros**: Notificações customizadas

#### Prioridades:
- ✅ **Urgente**: Mais de 30 dias de atraso
- ✅ **Alta**: 15-30 dias de atraso ou vencendo hoje
- ✅ **Média**: 1-15 dias de atraso ou vencendo em breve
- ✅ **Baixa**: Pagamentos recebidos

### 8. **Funcionalidades de Pesquisa e Filtros**

#### Filtros Disponíveis:
- ✅ Por cliente (nome)
- ✅ Por status (pendentes, atrasadas, pagas)
- ✅ Por data de venda (início/fim)
- ✅ Por data de vencimento (início/fim)
- ✅ Por valor (mínimo/máximo)

### 9. **Cálculo Automático**

#### Juros e Multa:
- ✅ Juros calculados mensalmente (taxa configurável)
- ✅ Multa calculada por atraso (taxa configurável)
- ✅ Atualização automática ao verificar parcelas atrasadas
- ✅ Desconto aplicável em cada parcela

---

## 🚀 Como Usar

### 1. **Executar Migration**

```bash
php index.php tools migrate
```

### 2. **Faturar Venda a Prazo**

1. Acesse `/vendas/editar/{id}`
2. Clique em "Faturar"
3. Selecione "A Prazo"
4. Configure:
   - Número de parcelas
   - Intervalo entre parcelas (dias)
   - Taxa de juros (% ao mês)
   - Taxa de multa (% por atraso)
   - Data do primeiro vencimento
5. Clique em "Faturar"

### 3. **Registrar Pagamento**

1. Acesse `/vendas_prazo/visualizar/{id}`
2. Clique em "Pagar" na parcela desejada
3. Preencha:
   - Valor pago
   - Data do pagamento
   - Forma de pagamento
   - Desconto (opcional)
   - Observações
4. Clique em "Registrar Pagamento"

### 4. **Visualizar Notificações**

- No dashboard (`/mapos`), há um widget com notificações
- Acesse `/vendas_prazo` para ver todas as vendas
- Notificações são atualizadas automaticamente a cada 30 segundos

### 5. **Atualizar Parcelas Atrasadas (Cron)**

Configure um cron job para atualizar parcelas automaticamente:

```bash
# Executar diariamente às 00:00
0 0 * * * curl http://seusite.com/index.php/vendas_prazo/atualizarParcelasAtrasadas

# Ou criar notificações de vencimento (executar diariamente às 08:00)
0 8 * * * curl http://seusite.com/index.php/vendas_prazo/criarNotificacoesVencimento
```

---

## 📊 Estrutura de Dados

### Relacionamentos:
```
vendas (1) ──> (N) parcelas_venda
parcelas_venda (1) ──> (N) historico_pagamentos
vendas (1) ──> (N) notificacoes_venda
parcelas_venda (1) ──> (N) notificacoes_venda
```

### Fluxo de Venda a Prazo:
1. Venda criada → Status: "Aberto"
2. Venda faturada → Tipo: "A Prazo" → Parcelas criadas
3. Parcelas geradas → Status: "Pendente"
4. Parcela vencida → Status: "Atrasada" → Notificação criada
5. Pagamento registrado → Status: "Paga" → Notificação criada

---

## 🔧 Configurações

### Taxas Padrão:
- Juros: 0% (configurável por venda)
- Multa: 0% (configurável por venda)
- Intervalo padrão: 30 dias
- Notificação: 3 dias antes do vencimento

### Permissões Necessárias:
- `vVenda` - Visualizar vendas
- `eVenda` - Editar vendas (registrar pagamentos)

---

## 📝 Próximas Melhorias Sugeridas

1. **Relatórios Avançados**:
   - Gráficos de inadimplência
   - Previsão de recebimento
   - Análise de clientes

2. **Integração com E-mail**:
   - Enviar e-mail quando parcela vence
   - Enviar boleto/recibo por e-mail

3. **Integração com WhatsApp**:
   - Notificar cliente via WhatsApp
   - Enviar lembretes de vencimento

4. **Relatórios em PDF**:
   - Relatório de vendas a prazo
   - Extrato de parcelas do cliente

5. **Dashboard Avançado**:
   - Gráficos de recebimento
   - Previsão de fluxo de caixa

---

## ✅ Checklist de Testes

- [ ] Criar venda a prazo com 3 parcelas
- [ ] Verificar se parcelas foram criadas corretamente
- [ ] Registrar pagamento de parcela
- [ ] Verificar se notificação foi criada
- [ ] Testar filtros de pesquisa
- [ ] Verificar atualização de parcelas atrasadas
- [ ] Testar notificações no dashboard
- [ ] Verificar cálculo de juros e multa
- [ ] Testar relatório

---

**Implementação concluída em:** <?= date('d/m/Y H:i:s') ?>
**Versão:** 1.0
