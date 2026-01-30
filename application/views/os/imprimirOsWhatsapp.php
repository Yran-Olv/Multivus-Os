<?php
$totalServico  = 0;
$totalProdutos = 0;
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <title><?= $this->config->item('app_name') ?> - OS #<?= $result->idOs ?> - <?= $result->nomeCliente ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/bootstrap5.3.2.min.css" />
    <link rel="stylesheet" href="<?= base_url() ?>assets/font-awesome/css/font-awesome.css" />
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/imprimir.css">
    <style>
        /* ============================================
           CSS OTIMIZADO PARA mPDF - PDF WHATSAPP
           ============================================ */
        
        /* ============================================
           CONFIGURAÇÕES GERAIS DO DOCUMENTO
           ============================================ */
        body {
            width: 210mm;              /* Largura padrão A4 (210mm) */
            margin: 0;                  /* Remove margens padrão do navegador */
            padding: 0;                 /* Remove padding padrão */
            font-size: 11px;            /* Tamanho da fonte base (compacto para WhatsApp) */
            background: white;          /* Fundo branco */
            font-family: Arial, sans-serif; /* Fonte Arial (compatível com mPDF) */
            line-height: 1.3;           /* Altura da linha (espaçamento entre linhas) */
        }
        
        /* ============================================
           CONTAINER PRINCIPAL DA PÁGINA
           ============================================ */
        .main-page {
            width: 210mm;               /* Largura A4 */
            margin: 0;                  /* Sem margem externa */
            padding: 5mm;               /* Padding interno de 5mm */
            background: white;          /* Fundo branco */
        }
        
        /* ============================================
           CONTAINER DA SUB-PÁGINA
           ============================================ */
        .sub-page {
            width: 100%;                /* Ocupa 100% da largura disponível */
        }
        
        /* ============================================
           CABEÇALHO (HEADER) - LOGO, EMITENTE E CONTATO
           ============================================ */
        header {
            position: relative;         /* Permite posicionamento absoluto de elementos filhos */
            width: 100%;               /* Ocupa toda a largura */
            padding: 10px;              /* Espaçamento interno de 10px */
            border: 1px solid #cdcdcd; /* Borda cinza clara */
            overflow: hidden;           /* Esconde conteúdo que ultrapassa o container (limpa floats) */
            min-height: 100px;          /* Altura mínima para garantir espaço */
        }
        
        /* Mensagem de alerta quando emitente não está configurado */
        header .alert {
            width: 100%;               /* Ocupa toda a largura */
            clear: both;                /* Limpa os floats acima */
        }
        
        /* ============================================
           LOGO DO EMITENTE (LADO ESQUERDO)
           ============================================ */
        header .imgLogo,
        #imgLogo {
            float: left;                /* Flutua à esquerda (permite elementos ao lado) */
            width: 150px;               /* Largura fixa de 150px */
            vertical-align: top;        /* Alinha ao topo verticalmente */
            margin-right: 0;            /* Sem margem à direita */
        }
        
        /* Imagem da logo */
        header .imgLogo img,
        #imgLogo img {
            width: 140px;               /* Largura da imagem 140px */
            height: auto;               /* Altura automática (mantém proporção) */
            display: block;             /* Exibe como bloco (remove espaço abaixo) */
        }
        
        /* ============================================
           DADOS DO EMITENTE (CENTRO)
           ============================================ */
        header .emitente {
            float: left;                /* Flutua à esquerda (fica ao lado da logo) */
            padding-left: 15px;         /* Espaçamento interno à esquerda */
            padding-right: 15px;        /* Espaçamento interno à direita */
            margin-top: 0;              /* Sem margem superior */
            width: auto;                /* Largura automática (ajusta ao conteúdo) */
            min-width: 250px;          /* Largura mínima de 250px */
            vertical-align: top;        /* Alinha ao topo */
        }
        
        /* Cada linha de texto do emitente */
        header .emitente span {
            display: block;             /* Exibe como bloco (cada span em uma linha) */
            line-height: 1.6;           /* Altura da linha (espaçamento entre linhas) */
            margin-bottom: 2px;         /* Margem inferior pequena entre linhas */
        }
        
        /* ============================================
           CONTATO DO EMITENTE (LADO DIREITO)
           ============================================ */
        header .contatoEmitente {
            float: right;               /* Flutua à direita */
            text-align: right;          /* Alinha texto à direita */
            max-width: 230px;          /* Largura máxima de 230px */
            margin-top: 0;              /* Sem margem superior */
            vertical-align: top;        /* Alinha ao topo */
        }
        
        /* Cada linha de contato */
        header .contatoEmitente span {
            display: block;             /* Exibe como bloco (cada span em uma linha) */
            line-height: 1.6;           /* Altura da linha */
            margin-bottom: 2px;         /* Margem inferior entre linhas */
        }
        
        /* Ícones no header */
        header .icon {
            width: 20px;               /* Largura dos ícones */
        }
        
        /* ============================================
           SEÇÕES DO DOCUMENTO
           ============================================ */
        section {
            width: 100%;               /* Ocupa toda a largura */
            padding: 8px 0;             /* Padding vertical de 8px (topo e fundo) */
        }
        
        /* ============================================
           TÍTULO DA ORDEM DE SERVIÇO
           ============================================ */
        section .title {
            width: 100%;               /* Ocupa toda a largura */
            border: 1px solid #cdcdcd;  /* Borda cinza clara */
            padding: 5px;               /* Espaçamento interno */
            font-weight: bold;          /* Texto em negrito */
            font-size: 1.2em;           /* Tamanho 1.2x maior que o padrão */
            text-align: center;         /* Texto centralizado */
            background-color: #e8e8e8;  /* Fundo cinza claro */
            position: relative;         /* Permite posicionamento absoluto de elementos filhos */
        }
        
        /* Texto "Via cliente" ou "Via empresa" (canto esquerdo) */
        section .title .via {
            font-size: 11px;            /* Tamanho menor */
            position: absolute;          /* Posicionamento absoluto */
            left: 5px;                  /* 5px da esquerda */
            top: 5px;                   /* 5px do topo */
        }
        
        /* Esconde "via" por padrão (só aparece na impressão) */
        .via {
            display: none;
        }
        
        /* Data de emissão (canto direito) */
        section .title .emissao {
            font-size: 11px;            /* Tamanho menor */
            position: absolute;         /* Posicionamento absoluto */
            right: 5px;                 /* 5px da direita */
            top: 5px;                   /* 5px do topo */
        }
        
        /* ============================================
           SUBTÍTULOS DAS SEÇÕES (DADOS DO CLIENTE, ETC)
           ============================================ */
        section .subtitle {
            margin-top: 8px;           /* Espaço superior de 8px */
            width: 100%;                /* Ocupa toda a largura */
            border: 1px solid #cdcdcd; /* Borda cinza clara */
            padding: 4px;               /* Espaçamento interno */
            font-weight: bold;          /* Texto em negrito */
            text-align: left;           /* Alinhamento à esquerda */
            background-color: #e8e8e8; /* Fundo cinza claro */
            font-size: 1em;             /* Tamanho padrão */
        }
        
        /* ============================================
           CAIXA DE DADOS (DADOS DO CLIENTE, DESCRIÇÃO, ETC)
           ============================================ */
        section .dados {
            width: 100%;               /* Ocupa toda a largura */
            border: 1px solid #cdcdcd;  /* Borda cinza clara */
            padding: 6px;               /* Espaçamento interno */
            font-weight: normal;        /* Peso da fonte normal (não negrito) */
            line-height: 1.5;           /* Altura da linha */
            overflow: hidden;           /* Esconde conteúdo que ultrapassa */
            page-break-inside: avoid;   /* Evita quebra de página dentro desta seção */
        }
        
        /* Primeira div dentro de .dados (lado esquerdo) */
        section .dados > div {
            float: left;                /* Flutua à esquerda */
            width: 48%;                 /* Ocupa 48% da largura */
        }
        
        /* Última div dentro de .dados (lado direito) */
        section .dados > div:last-child {
            float: right;               /* Flutua à direita */
            text-align: right;          /* Alinha texto à direita */
        }
        
        /* Parágrafos dentro de .dados */
        section .dados p {
            margin-bottom: 0.3em;       /* Margem inferior pequena */
        }
        
        /* Spans dentro de .dados */
        section .dados span {
            display: block;             /* Exibe como bloco (cada span em uma linha) */
            margin-bottom: 2px;         /* Margem inferior entre linhas */
        }
        
        /* ============================================
           TABELAS (PRODUTOS, SERVIÇOS)
           ============================================ */
        section .tabela {
            padding-top: 8px;           /* Espaço superior antes da tabela */
            width: 100%;                /* Ocupa toda a largura */
        }
        
        /* Tabela */
        section .tabela table {
            width: 100%;                /* Ocupa toda a largura */
            margin-bottom: 0;           /* Sem margem inferior */
            border-collapse: collapse;  /* Bordas colapsadas (sem espaços entre células) */
            font-size: 10px;            /* Tamanho da fonte menor (compacto) */
        }
        
        /* Cabeçalho e células da tabela */
        section .tabela table th,
        section .tabela table td {
            padding: 4px;                /* Espaçamento interno das células */
            border: 1px solid #cdcdcd;  /* Borda cinza clara */
        }
        
        /* Linha do cabeçalho da tabela (fundo cinza) */
        section .tabela table .table-secondary {
            background-color: #e8e8e8;  /* Fundo cinza claro */
            border-color: #cdcdcd;      /* Borda cinza clara */
        }
        
        /* Corpo da tabela */
        section .tabela table tbody {
            border-color: #cdcdcd;      /* Cor da borda */
        }
        
        /* ============================================
           SEÇÃO DE PAGAMENTO (QR CODE E RESUMO)
           ============================================ */
        section .pagamento {
            margin-top: 10px;           /* Espaço superior */
            overflow: hidden;           /* Limpa floats */
        }
        
        /* Container do QR Code (lado esquerdo) */
        section .pagamento .qrcode {
            float: left;                /* Flutua à esquerda */
            width: 150px;               /* Largura fixa de 150px */
            margin-right: 10px;         /* Espaço à direita */
        }
        
        /* Imagem do QR Code */
        section .pagamento .qrcode img {
            width: 130px;               /* Largura da imagem */
            height: auto;               /* Altura automática (mantém proporção) */
        }
        
        /* Texto da chave PIX */
        section .pagamento .qrcode .chavePix {
            width: 100%;                /* Ocupa toda a largura do container */
            word-break: break-word;     /* Quebra palavras longas se necessário */
            margin-top: 5px;            /* Espaço superior */
            text-align: center;         /* Texto centralizado */
            font-size: 9px;             /* Fonte pequena */
        }
        
        /* Ícone do QR Code */
        section .pagamento .qrcode i {
            font-size: 16px;            /* Tamanho do ícone */
            color: #989898;             /* Cor cinza */
        }
        
        /* Tabela de resumo de valores (lado direito) */
        section .pagamento .tabela {
            float: right;               /* Flutua à direita */
            width: 400px;               /* Largura fixa de 400px */
        }
        
        /* Tabela dentro da seção de pagamento */
        section .pagamento .tabela table {
            width: 100%;                /* Ocupa toda a largura */
            text-align: right;          /* Alinha números à direita */
        }
        
        /* Cabeçalho da tabela de pagamento */
        section .pagamento .tabela table th {
            text-align: center;         /* Texto centralizado */
        }
        
        /* Linhas do corpo da tabela de pagamento */
        section .pagamento .tabela table tbody tr {
            font-weight: bold;          /* Texto em negrito */
        }
        
        /* ============================================
           RODAPÉ (FOOTER)
           ============================================ */
        footer {
            width: 100%;                /* Ocupa toda a largura */
            padding: 8px;               /* Espaçamento interno */
            border: 1px solid #cdcdcd;  /* Borda cinza clara */
            margin-top: 10px;           /* Espaço superior */
            page-break-before: avoid;    /* Evita quebra de página antes do rodapé */
            page-break-inside: avoid;   /* Evita quebra de página dentro do rodapé */
        }
        
        /* Seção de detalhes (datas, número da OS) */
        footer .detalhes {
            width: 100%;                /* Ocupa toda a largura */
            overflow: hidden;           /* Limpa floats */
            margin-bottom: 10px;        /* Espaço inferior */
        }
        
        /* Cada detalhe (data inicial, número OS, data final) */
        footer .detalhes span {
            display: inline-block;      /* Exibe em linha mas permite largura */
            margin-right: 15px;         /* Espaço à direita entre elementos */
        }
        
        /* Seção de assinaturas */
        footer .assinaturas {
            width: 100%;                /* Ocupa toda a largura */
            overflow: hidden;           /* Limpa floats */
            margin-top: 40px;           /* Espaço superior grande */
        }
        
        /* Cada assinatura */
        footer .assinaturas span {
            display: inline-block;      /* Exibe em linha mas permite largura */
            width: 45%;                 /* Ocupa 45% da largura */
            text-align: center;         /* Texto centralizado */
            border-top: 1px solid #000; /* Linha preta acima (linha de assinatura) */
            padding-top: 5px;           /* Espaço acima da linha */
        }
        
        /* Primeira assinatura (cliente) - lado esquerdo */
        footer .assinaturas span:first-child {
            float: left;                /* Flutua à esquerda */
        }
        
        /* Segunda assinatura (técnico) - lado direito */
        footer .assinaturas span:last-child {
            float: right;               /* Flutua à direita */
        }
        
        /* ============================================
           LIMPEZA DE FLOATS (CLEARFIX)
           ============================================ */
        /* Remove o efeito de float dos elementos filhos */
        header::after,
        section .dados::after,
        section .pagamento::after,
        footer .assinaturas::after {
            content: "";                /* Conteúdo vazio (pseudo-elemento) */
            display: table;             /* Exibe como tabela */
            clear: both;                /* Limpa floats de ambos os lados */
        }
    </style>
</head>
<body>
    <div class="main-page">
        <div class="sub-page">
            <header>
                <?php if ($emitente == null) : ?>
                    <div class="alert alert-danger" role="alert">
                        Você precisa configurar os dados do emitente. >>> <a href="<?=base_url()?>index.php/mapos/emitente">Configurar</a>
                    </div>
                <?php else : ?>
                    <div class="imgLogo align-middle">
                        <img src="<?= $emitente->url_logo ?>" class="img-fluid" style="width:140px;">
                    </div>
                    <div class="emitente">
                        <span style="font-size: 16px;"><b><?= $emitente->nome ?></b></span><br>
                        <?php 
                        $this->load->helper('validation');
                        if ($emitente->cnpj != "00.000.000/0000-00" && $emitente->cnpj != "000.000.000-00") : 
                            $doc_formatado = formatar_documento_emitente($emitente->cnpj);
                            if (!empty($doc_formatado)) :
                        ?>
                            <span class="align-middle"><?= $doc_formatado ?></span><br>
                        <?php 
                            endif;
                        endif; 
                        ?>
                        <span class="align-middle">
                            <?= $emitente->rua.', '.$emitente->numero.', '.$emitente->bairro ?><br>
                            <?= $emitente->cidade.' - '.$emitente->uf.' - '.$emitente->cep ?>
                        </span>
                    </div>
                    <div class="contatoEmitente">
                        <span style="font-weight: bold;">Tel: <?= $emitente->telefone ?></span><br>
                        <span style="font-weight: bold;"><?= $emitente->email ?></span><br>
                        <span style="word-break: break-word;">Responsável: <b><?= $result->nome ?></b></span>
                    </div>
                <?php endif; ?>
            </header>
            <section>
                <div class="title">
                    <?php if ($configuration['control_2vias']) : ?><span class="via">Via cliente</span><?php endif; ?>
                    ORDEM DE SERVIÇO #<?= str_pad($result->idOs, 4, 0, STR_PAD_LEFT) ?>
                    <span class="emissao">Emissão: <?= date('d/m/Y H:i:s') ?></span>
                </div>

                <?php if ($result->dataInicial != null): ?>
                    <div class="tabela">
                        <table class="table table-bordered">
                            <thead>
                                <tr class="table-secondary">
                                    <th class="text-center">STATUS</th>
                                    <th class="text-center">DATA INICIAL</th>
                                    <th class="text-center">DATA FINAL</th>
                                    <?php if ($result->garantia) : ?>
                                        <th class="text-center">GARANTIA</th>
                                    <?php endif; ?>
                                    <?php if (in_array($result->status, ['Finalizado', 'Faturado'])) : ?>
                                        <th class="text-center">VENC. GARANTIA</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center"><?= $result->status ?></td>
                                    <td class="text-center"><?= date('d/m/Y', strtotime($result->dataInicial)) ?></td>
                                    <td class="text-center"><?= $result->dataFinal ? date('d/m/Y', strtotime($result->dataFinal)) : '' ?></td>
                                    <?php if ($result->garantia) : ?>
                                        <td class="text-center"><?= $result->garantia . ' dia(s)' ?></td>
                                    <?php endif; ?>
                                    <?php if (in_array($result->status, ['Finalizado', 'Faturado'])) : ?>
                                        <td class="text-center"><?= dateInterval($result->dataFinal, $result->garantia) ?></td>
                                    <?php endif; ?>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <div class="subtitle">DADOS DO CLIENTE</div>
                <div class="dados">
                    <div>
                        <span><b><?= $result->nomeCliente ?></b></span><br />
                        <span>CPF/CNPJ: <?= $result->documento ?></span><br />
                        <span><?= $result->contato_cliente.' '.$result->telefone ?><?= $result->telefone && $result->celular ? ' / '.$result->celular : $result->celular ?></span><br />
                        <span><?= $result->email ?></span><br />
                    </div>
                    <div style="text-align: right;">
                        <span><?= $result->rua.', '.$result->numero.', '.$result->bairro ?></span><br />
                        <span><?= $result->complemento.' - '.$result->cidade.' - '.$result->estado ?></span><br />
                        <span>CEP: <?= $result->cep ?></span><br />
                    </div>
                </div>

                <?php if ($result->descricaoProduto) : ?>
                    <div class="subtitle">DESCRIÇÃO</div>
                    <div class="dados">
                        <div style="text-align: justify;">
                            <?= printSafeHtml($result->descricaoProduto) ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($result->defeito) : ?>
                    <div class="subtitle">DEFEITO APRESENTADO</div>
                    <div class="dados">
                        <div style="text-align: justify;">
                            <?= printSafeHtml($result->defeito) ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($result->observacoes) : ?>
                    <div class="subtitle">OBSERVAÇÕES</div>
                    <div class="dados">
                        <div style="text-align: justify;">
                            <?= printSafeHtml($result->observacoes) ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($result->laudoTecnico) : ?>
                    <div class="subtitle">PARECER TÉCNICO</div>
                    <div class="dados">
                        <div style="text-align: justify;">
                            <?= printSafeHtml($result->laudoTecnico) ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($result->garantias_id) : ?>
                    <div class="subtitle">TERMO DE GARANTIA</div>
                    <div class="dados">
                        <div style="text-align: justify;"><?= printSafeHtml($result->textoGarantia) ?></div>
                    </div>
                <?php endif; ?>

                <?php if ($produtos) : ?>
                    <div class="tabela">
                        <table class="table table-bordered">
                            <thead>
                                <tr class="table-secondary">
                                    <th>PRODUTO(S)</th>
                                    <th class="text-center" width="10%">QTD</th>
                                    <th class="text-center" width="10%">UNT</th>
                                    <th class="text-end" width="15%" >SUBTOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($produtos as $p) :
                                    $totalProdutos = $totalProdutos + $p->subTotal;
                                    echo '<tr>';
                                    echo '  <td>' . $p->descricao . '</td>';
                                    echo '  <td class="text-center">' . $p->quantidade . '</td>';
                                    echo '  <td class="text-center">' . number_format($p->preco ?: $p->precoVenda, 2, ',', '.') . '</td>';
                                    echo '  <td class="text-end">R$ ' . number_format($p->subTotal, 2, ',', '.') . '</td>';
                                    echo '</tr>';
                                endforeach; ?>
                                <tr>
                                    <td colspan="3" class="text-end"><b>TOTAL PRODUTOS:</b></td>
                                    <td class="text-end"><b>R$ <?= number_format($totalProdutos, 2, ',', '.') ?></b></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <?php if ($servicos) : ?>
                    <div class="tabela">
                        <table class="table table-bordered">
                            <thead>
                                <tr class="table-secondary">
                                    <th>SERVIÇO(S)</th>
                                    <th class="text-center" width="10%">QTD</th>
                                    <th class="text-center" width="10%">UNT</th>
                                    <th class="text-end" width="15%" >SUBTOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    setlocale(LC_MONETARY, 'en_US');
                    foreach ($servicos as $s) :
                        $preco = $s->preco ?: $s->precoVenda;
                        $subtotal = $preco * ($s->quantidade ?: 1);
                        $totalServico = $totalServico + $subtotal;
                        echo '<tr>';
                        echo '  <td>' . $s->nome . '</td>';
                        echo '  <td class="text-center">' . ($s->quantidade ?: 1) . '</td>';
                        echo '  <td class="text-center">' . number_format($preco, 2, ',', '.') . '</td>';
                        echo '  <td class="text-end">R$ ' . number_format($subtotal, 2, ',', '.') . '</td>';
                        echo '</tr>';
                    endforeach; ?>
                                <tr>
                                    <td colspan="3" class="text-end"><b>TOTAL SERVIÇOS:</b></td>
                                    <td class="text-end"><b>R$ <?= number_format($totalServico, 2, ',', '.') ?></b></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <?php if ($totalProdutos != 0 || $totalServico != 0) : ?>
                    <div class="pagamento">
                        <div class="qrcode">
                            <?php if ($this->data['configuration']['pix_key']) : ?>
                                <div><img width="130px" src="<?= $qrCode ?>" alt="QR Code de Pagamento" /></div>
                                <div style="display: flex; flex-wrap: wrap; align-content: center;">
                                    <div style="width: 100%; text-align:center;"><i class="fas fa-camera"></i><br />Escaneie o QRCode ao lado para pagar por Pix</div>
                                    <div class="chavePix">Chave Pix: <b><?= $chaveFormatada ?></b></div>
                                </div>
                            <?php else: ?>
                                <div></div>
                                <div></div>
                            <?php endif; ?>
                        </div>
                        <div>
                            <div class="tabela">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr class="table-secondary">
                                            <th colspan="2">RESUMO DOS VALORES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($result->valor_desconto != 0) : ?>
                                            <tr>
                                                <td width="65%">SUBTOTAL</td>
                                                <td>R$ <b><?= number_format($totalProdutos + $totalServico, 2, ',', '.') ?></b></td>
                                            </tr>
                                            <tr>
                                                <td>DESCONTO</td>
                                                <td>R$ <b><?= number_format($result->valor_desconto != 0 ? $result->valor_desconto - ($totalProdutos + $totalServico) : 0.00, 2, ',', '.') ?></b></td>
                                            </tr>
                                            <tr>
                                                <td>TOTAL</td>
                                                <td>R$ <?= number_format($result->valor_desconto, 2, ',', '.') ?></td>
                                            </tr>
                                        <?php else : ?>
                                            <tr>
                                                <td style="width:290px">TOTAL</td>
                                                <td>R$ <?= number_format($totalProdutos + $totalServico, 2, ',', '.') ?></td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </section>
            <footer>
                <div class="detalhes">
                    <span>Data inicial: <b><?= date('d/m/Y', strtotime($result->dataInicial)) ?></b></span>
                    <span>ORDEM DE SERVIÇO <b>#<?= str_pad($result->idOs, 4, 0, STR_PAD_LEFT) ?></b></span>
                    <span>Data final: <b><?= $result->dataFinal ? date('d/m/Y', strtotime($result->dataFinal)) : '' ?></b></span>
                </div>
                <div class="assinaturas">
                    <span>Assinatura do cliente</span>
                    <span>Assinatura do técnico</span>
                </div>
            </footer>
        </div>
    </div>
</body>
</html>
