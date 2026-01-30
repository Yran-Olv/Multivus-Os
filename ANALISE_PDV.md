# 📊 Análise: Transformação do Sistema de Vendas em PDV (Ponto de Venda)

## 🎯 Objetivo
Analisar o sistema atual de vendas (`/index.php/vendas`) e identificar o que falta para transformá-lo em um **Sistema Frente de Caixa (PDV)** completo e profissional.

---

## 📋 Situação Atual do Sistema de Vendas

### ✅ O que já existe:

1. **CRUD Básico de Vendas**
   - Criar, editar, visualizar e excluir vendas
   - Listagem com paginação e filtros
   - Status de vendas (Aberto, Faturado, Cancelado, etc.)

2. **Gestão de Produtos na Venda**
   - Adicionar produtos à venda
   - Remover produtos da venda
   - Cálculo automático de subtotais
   - Controle de estoque (quando habilitado)

3. **Sistema de Descontos**
   - Desconto percentual
   - Desconto em valor (real)
   - Cálculo automático do valor final

4. **Faturamento**
   - Faturar venda
   - Integração com lançamentos financeiros
   - Geração de QR Code PIX

5. **Impressão**
   - Impressão de venda (A4)
   - Impressão térmica
   - Impressão de orçamento

6. **Autocomplete**
   - Busca de produtos
   - Busca de clientes
   - Busca de usuários/vendedores

### 📊 Estrutura do Banco de Dados Atual

#### Tabela `vendas`
```sql
- idVendas (PK)
- dataVenda
- valorTotal
- desconto
- valor_desconto
- tipo_desconto (percentual/real)
- faturado (0/1)
- observacoes
- observacoes_cliente
- clientes_id (FK)
- usuarios_id (FK)
- lancamentos_id (FK)
- status
- garantia
```

#### Tabela `itens_de_vendas`
```sql
- idItens (PK)
- subTotal
- quantidade
- preco
- vendas_id (FK)
- produtos_id (FK)
```

---

## ❌ O que FALTA para um PDV Completo

### 1. 🖥️ **Interface de PDV (Tela de Venda Rápida)**

#### Problema Atual:
- O sistema exige criar uma venda primeiro, depois adicionar produtos
- Não há uma tela dedicada para venda rápida no balcão
- Interface não é otimizada para uso em touchscreen/tablet

#### O que precisa:
- **Nova rota:** `/vendas/pdv` ou `/pdv`
- **Tela fullscreen** otimizada para touch
- **Layout em duas colunas:**
  - Esquerda: Lista de produtos (grid com imagens)
  - Direita: Carrinho de compras + total + botões de ação
- **Busca rápida** de produtos (barra de pesquisa sempre visível)
- **Teclado numérico virtual** para valores
- **Botões grandes** para ações principais

### 2. 💰 **Múltiplas Formas de Pagamento**

#### Problema Atual:
- Só permite faturar depois (processo em duas etapas)
- Não há seleção de forma de pagamento na hora da venda
- Não há suporte para pagamento parcial

#### O que precisa:
- **Tabela `formas_pagamento`:**
```sql
CREATE TABLE formas_pagamento (
    idFormaPagamento INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(50) NOT NULL,
    tipo ENUM('dinheiro', 'cartao_debito', 'cartao_credito', 'pix', 'boleto', 'cheque', 'vale', 'outros'),
    ativo TINYINT(1) DEFAULT 1,
    exige_troco TINYINT(1) DEFAULT 0,
    exige_parcelas TINYINT(1) DEFAULT 0,
    taxa DECIMAL(5,2) DEFAULT 0.00,
    ordem INT DEFAULT 0
);
```

- **Tabela `pagamentos_venda`:**
```sql
CREATE TABLE pagamentos_venda (
    idPagamentoVenda INT PRIMARY KEY AUTO_INCREMENT,
    vendas_id INT NOT NULL,
    formas_pagamento_id INT NOT NULL,
    valor DECIMAL(10,2) NOT NULL,
    troco DECIMAL(10,2) DEFAULT 0.00,
    parcelas INT DEFAULT 1,
    data_pagamento DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vendas_id) REFERENCES vendas(idVendas),
    FOREIGN KEY (formas_pagamento_id) REFERENCES formas_pagamento(idFormaPagamento)
);
```

- **Interface de seleção de pagamento:**
  - Modal com botões grandes para cada forma
  - Campo para valor recebido (quando necessário)
  - Cálculo automático de troco
  - Suporte a pagamento parcial (múltiplas formas)

### 3. 🏪 **Caixa/Turno de Trabalho**

#### Problema Atual:
- Não há controle de caixa aberto/fechado
- Não há controle de turno de trabalho
- Não há fechamento de caixa com relatório

#### O que precisa:
- **Tabela `caixas`:**
```sql
CREATE TABLE caixas (
    idCaixa INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    saldo_inicial DECIMAL(10,2) DEFAULT 0.00,
    ativo TINYINT(1) DEFAULT 1
);
```

- **Tabela `turnos_caixa`:**
```sql
CREATE TABLE turnos_caixa (
    idTurno INT PRIMARY KEY AUTO_INCREMENT,
    caixas_id INT NOT NULL,
    usuarios_id INT NOT NULL,
    data_abertura DATETIME NOT NULL,
    data_fechamento DATETIME NULL,
    valor_abertura DECIMAL(10,2) NOT NULL,
    valor_fechamento DECIMAL(10,2) NULL,
    valor_esperado DECIMAL(10,2) NULL,
    diferenca DECIMAL(10,2) NULL,
    observacoes TEXT,
    status ENUM('aberto', 'fechado', 'cancelado') DEFAULT 'aberto',
    FOREIGN KEY (caixas_id) REFERENCES caixas(idCaixa),
    FOREIGN KEY (usuarios_id) REFERENCES usuarios(idUsuarios)
);
```

- **Tabela `vendas` - Adicionar campos:**
```sql
ALTER TABLE vendas ADD COLUMN turnos_caixa_id INT NULL;
ALTER TABLE vendas ADD COLUMN caixas_id INT NULL;
ALTER TABLE vendas ADD FOREIGN KEY (turnos_caixa_id) REFERENCES turnos_caixa(idTurno);
ALTER TABLE vendas ADD FOREIGN KEY (caixas_id) REFERENCES caixas(idCaixa);
```

- **Funcionalidades:**
  - Abrir caixa (com valor inicial)
  - Fechar caixa (com relatório de movimentação)
  - Listar vendas do turno
  - Relatório de fechamento (dinheiro, cartão, PIX, etc.)

### 4. 🧾 **Cupom Fiscal / NFC-e**

#### Problema Atual:
- Não há integração com emissor de cupom fiscal
- Não há geração de XML para NFC-e
- Não há controle de numeração de cupom

#### O que precisa:
- **Tabela `vendas` - Adicionar campos:**
```sql
ALTER TABLE vendas ADD COLUMN numero_cupom INT NULL;
ALTER TABLE vendas ADD COLUMN serie_cupom VARCHAR(10) NULL;
ALTER TABLE vendas ADD COLUMN chave_nfce VARCHAR(44) NULL;
ALTER TABLE vendas ADD COLUMN xml_nfce TEXT NULL;
ALTER TABLE vendas ADD COLUMN url_nfce VARCHAR(500) NULL;
ALTER TABLE vendas ADD COLUMN status_nfce ENUM('pendente', 'autorizada', 'cancelada', 'rejeitada') NULL;
```

- **Integração com API Fiscal:**
  - Classe para comunicação com API (ex: Focus NFe, NFe.io, etc.)
  - Geração de XML
  - Envio para SEFAZ
  - Consulta de status

### 5. 📱 **Venda Rápida sem Cliente (Consumidor Final)**

#### Problema Atual:
- É obrigatório selecionar um cliente
- Não há cliente padrão "Consumidor Final"

#### O que precisa:
- **Cliente padrão "Consumidor Final":**
  - Criar cliente automático no banco
  - Permitir venda sem selecionar cliente (usa consumidor final)
  - Opção de cadastrar cliente rápido na hora da venda

### 6. 🔢 **Código de Barras / Leitor**

#### Problema Atual:
- Não há suporte a leitura de código de barras
- Não há busca por código de barras

#### O que precisa:
- **Campo de busca por código de barras:**
  - Campo sempre focado para leitura rápida
  - Busca automática ao ler código
  - Adiciona produto automaticamente ao carrinho
  - Suporte a teclado virtual e leitor físico

### 7. 📊 **Dashboard de Vendas em Tempo Real**

#### Problema Atual:
- Não há visão geral das vendas do dia
- Não há métricas em tempo real

#### O que precisa:
- **Nova view:** `/vendas/dashboard`
- **Métricas:**
  - Total vendido hoje
  - Quantidade de vendas
  - Ticket médio
  - Formas de pagamento mais usadas
  - Produtos mais vendidos
  - Gráficos em tempo real

### 8. 🖨️ **Impressão Automática de Cupom**

#### Problema Atual:
- Impressão é manual
- Não há impressão automática após venda

#### O que precisa:
- **Configuração de impressora:**
  - Seleção de impressora padrão
  - Configuração de tamanho de papel
  - Impressão automática após finalizar venda
  - Reimpressão de cupom

### 9. 💳 **Integração com Máquina de Cartão**

#### Problema Atual:
- Não há integração com TEF (Transferência Eletrônica de Fundos)
- Não há comunicação com máquinas de cartão

#### O que precisa:
- **Integração TEF:**
  - Classe para comunicação com TEF
  - Suporte a múltiplas adquirentes (Cielo, Rede, GetNet, etc.)
  - Processamento de pagamento com cartão
  - Confirmação automática na venda

### 10. 📦 **Gestão de Estoque em Tempo Real**

#### Problema Atual:
- Controle de estoque existe, mas não é em tempo real
- Não há alertas de estoque baixo durante a venda

#### O que precisa:
- **Alertas visuais:**
  - Aviso quando produto está com estoque baixo
  - Bloqueio de venda se estoque = 0
  - Sugestão de produtos similares

### 11. 🔐 **Controle de Acesso por Usuário**

#### Problema Atual:
- Permissões existem, mas não há controle específico de PDV

#### O que precisa:
- **Permissões específicas:**
  - `vPDV` - Visualizar PDV
  - `aPDV` - Adicionar venda no PDV
  - `ePDV` - Editar venda no PDV
  - `dPDV` - Cancelar venda no PDV
  - `fPDV` - Fechar caixa
  - `aCaixa` - Abrir caixa

### 12. 📈 **Relatórios de PDV**

#### Problema Atual:
- Relatórios existem, mas não são específicos para PDV

#### O que precisa:
- **Relatórios:**
  - Relatório de fechamento de caixa
  - Relatório de vendas por período
  - Relatório de formas de pagamento
  - Relatório de produtos mais vendidos
  - Relatório de vendedores
  - Relatório de cancelamentos

### 13. 🔄 **Cancelamento de Venda**

#### Problema Atual:
- Cancelamento existe, mas não há controle adequado

#### O que precisa:
- **Tabela `cancelamentos_venda`:**
```sql
CREATE TABLE cancelamentos_venda (
    idCancelamento INT PRIMARY KEY AUTO_INCREMENT,
    vendas_id INT NOT NULL,
    usuarios_id INT NOT NULL,
    motivo TEXT NOT NULL,
    data_cancelamento DATETIME DEFAULT CURRENT_TIMESTAMP,
    estornar_estoque TINYINT(1) DEFAULT 1,
    FOREIGN KEY (vendas_id) REFERENCES vendas(idVendas),
    FOREIGN KEY (usuarios_id) REFERENCES usuarios(idUsuarios)
);
```

- **Funcionalidades:**
  - Cancelar venda com motivo obrigatório
  - Estornar estoque automaticamente
  - Estornar pagamento (se aplicável)
  - Relatório de cancelamentos

### 14. 🎫 **Cupons de Desconto / Promoções**

#### Problema Atual:
- Desconto manual existe, mas não há sistema de cupons

#### O que precisa:
- **Tabela `cupons_desconto`:**
```sql
CREATE TABLE cupons_desconto (
    idCupom INT PRIMARY KEY AUTO_INCREMENT,
    codigo VARCHAR(50) UNIQUE NOT NULL,
    tipo ENUM('percentual', 'valor_fixo') NOT NULL,
    valor DECIMAL(10,2) NOT NULL,
    valor_minimo DECIMAL(10,2) DEFAULT 0.00,
    data_inicio DATE NOT NULL,
    data_fim DATE NOT NULL,
    quantidade_usos INT DEFAULT 0,
    quantidade_maxima INT NULL,
    ativo TINYINT(1) DEFAULT 1
);
```

- **Tabela `vendas` - Adicionar campo:**
```sql
ALTER TABLE vendas ADD COLUMN cupons_desconto_id INT NULL;
ALTER TABLE vendas ADD FOREIGN KEY (cupons_desconto_id) REFERENCES cupons_desconto(idCupom);
```

### 15. 📱 **Modo Offline / Sincronização**

#### Problema Atual:
- Sistema depende de conexão com internet

#### O que precisa:
- **Armazenamento local:**
  - Salvar vendas localmente (LocalStorage/IndexedDB)
  - Sincronizar quando conexão voltar
  - Indicador de status de conexão

---

## 🗄️ Resumo das Tabelas a Criar/Modificar

### Novas Tabelas:
1. `formas_pagamento` - Formas de pagamento disponíveis
2. `pagamentos_venda` - Pagamentos de cada venda
3. `caixas` - Caixas do estabelecimento
4. `turnos_caixa` - Turnos de trabalho (abertura/fechamento)
5. `cancelamentos_venda` - Cancelamentos com motivo
6. `cupons_desconto` - Sistema de cupons

### Tabelas a Modificar:
1. `vendas` - Adicionar campos:
   - `turnos_caixa_id`
   - `caixas_id`
   - `numero_cupom`
   - `serie_cupom`
   - `chave_nfce`
   - `xml_nfce`
   - `url_nfce`
   - `status_nfce`
   - `cupons_desconto_id`

---

## 🎨 Interface do PDV (Proposta)

### Layout Principal:
```
┌─────────────────────────────────────────────────────────┐
│  [LOGO]  PDV - Venda Rápida          [Usuário] [Caixa] │
├─────────────────────────────────────────────────────────┤
│                                                         │
│  ┌──────────────────┐  ┌──────────────────────────┐  │
│  │                  │  │  CARRINHO                 │  │
│  │                  │  │  ┌────────────────────┐  │  │
│  │   PRODUTOS       │  │  │ Produto 1    R$ 50 │  │  │
│  │   (Grid)         │  │  │ Produto 2    R$ 30 │  │  │
│  │                  │  │  └────────────────────┘  │  │
│  │  [Busca: _____]  │  │                          │  │
│  │                  │  │  SUBTOTAL:    R$ 80,00  │  │
│  │  [Img] [Img]     │  │  DESCONTO:    R$  0,00  │  │
│  │  [Img] [Img]     │  │  ─────────────────────  │  │
│  │  [Img] [Img]     │  │  TOTAL:       R$ 80,00  │  │
│  │                  │  │                          │  │
│  │                  │  │  [FINALIZAR VENDA]      │  │
│  │                  │  │  [CANCELAR]             │  │
│  └──────────────────┘  └──────────────────────────┘  │
│                                                         │
└─────────────────────────────────────────────────────────┘
```

### Modal de Pagamento:
```
┌─────────────────────────────────────┐
│  FORMA DE PAGAMENTO                 │
├─────────────────────────────────────┤
│  Total: R$ 80,00                    │
│                                     │
│  [DINHEIRO]  [CARTÃO DÉBITO]       │
│  [CARTÃO CRÉDITO]  [PIX]            │
│  [VALE]  [OUTROS]                   │
│                                     │
│  Valor Recebido: R$ [_____]         │
│  Troco: R$ 0,00                     │
│                                     │
│  [CONFIRMAR]  [CANCELAR]            │
└─────────────────────────────────────┘
```

---

## 📝 Checklist de Implementação

### Fase 1: Estrutura Base
- [ ] Criar migrations para novas tabelas
- [ ] Criar controller `PDV.php`
- [ ] Criar model `PDV_model.php`
- [ ] Criar view `pdv/index.php` (tela principal)

### Fase 2: Funcionalidades Básicas
- [ ] Interface de PDV (grid de produtos + carrinho)
- [ ] Busca de produtos
- [ ] Adicionar/remover produtos do carrinho
- [ ] Cálculo de totais
- [ ] Venda rápida (sem cliente obrigatório)

### Fase 3: Sistema de Caixa
- [ ] CRUD de caixas
- [ ] Abertura de caixa
- [ ] Fechamento de caixa
- [ ] Relatório de fechamento
- [ ] Vinculação de venda ao turno

### Fase 4: Formas de Pagamento
- [ ] CRUD de formas de pagamento
- [ ] Modal de seleção de pagamento
- [ ] Cálculo de troco
- [ ] Pagamento parcial (múltiplas formas)
- [ ] Tabela de pagamentos da venda

### Fase 5: Funcionalidades Avançadas
- [ ] Leitor de código de barras
- [ ] Impressão automática
- [ ] Cancelamento de venda
- [ ] Sistema de cupons
- [ ] Dashboard de vendas

### Fase 6: Integrações
- [ ] Integração TEF (máquina de cartão)
- [ ] Integração NFC-e (se aplicável)
- [ ] Sincronização offline

---

## 🚀 Prioridades de Implementação

### Alta Prioridade (MVP):
1. Interface de PDV básica
2. Sistema de caixa (abertura/fechamento)
3. Múltiplas formas de pagamento
4. Venda rápida sem cliente obrigatório
5. Leitor de código de barras

### Média Prioridade:
6. Dashboard de vendas
7. Cancelamento de venda
8. Relatórios de PDV
9. Impressão automática

### Baixa Prioridade (Futuro):
10. Integração TEF
11. Integração NFC-e
12. Sistema de cupons
13. Modo offline

---

## 📚 Referências e Padrões

### Padrões de PDV Comerciais:
- **Tiny ERP / Odoo** - Sistema open-source com PDV
- **iFood PDV** - Interface moderna e touch-friendly
- **Stone Pagamentos** - Integração com máquinas

### APIs Fiscais:
- **Focus NFe** - API para NFC-e
- **NFe.io** - API para documentos fiscais
- **Brasil API** - Validação de CPF/CNPJ

### TEF:
- **Cielo** - API TEF
- **Rede** - API TEF
- **GetNet** - API TEF

---

## ✅ Conclusão

O sistema atual de vendas tem uma **base sólida**, mas precisa de **muitas funcionalidades** para se tornar um PDV completo. As principais lacunas são:

1. **Interface dedicada** para venda rápida
2. **Sistema de caixa** (abertura/fechamento)
3. **Múltiplas formas de pagamento** na hora da venda
4. **Integrações** (TEF, NFC-e)
5. **Funcionalidades de PDV** (código de barras, impressão automática, etc.)

A implementação deve ser feita em **fases**, começando pelo MVP (interface + caixa + pagamentos) e depois adicionando funcionalidades avançadas.

---

**Documento criado em:** <?= date('d/m/Y H:i:s') ?>
**Versão:** 1.0
