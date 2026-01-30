<?php
$totalServico  = 0;
$totalProdutos = 0;

$osNum = str_pad($result->idOs, 4, '0', STR_PAD_LEFT);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title><?= $this->config->item('app_name') ?> - OS #<?= $osNum ?> - <?= $result->nomeCliente ?></title>

    <style>
        /* ==========================
           mPDF SAFE CSS (A4)
           - sem bootstrap
           - sem ::after/::before
           - sem variáveis CSS
           - sem @supports
           ========================== */

        @page { margin: 8mm; }

        body{
            margin:0;
            padding:0;
            font-family: Arial, sans-serif;
            font-size: 10.5pt;
            color:#111827;
        }

        .page{
            width:100%;
            background:#fff;
        }

        /* Topo marca */
        .brandbar{
            border: 1px solid #e5e7eb;
            background:#0f172a;
            color:#fff;
            padding:8px 10px;
            border-radius:6px;
        }
        .brand-left{ float:left; width:70%; }
        .brand-right{ float:right; width:30%; text-align:right; }
        .appname{ margin:0; font-size:12pt; font-weight:bold; }
        .subtitle{ margin:2px 0 0 0; font-size:9.5pt; color:#e2e8f0; }
        .badge{
            display:inline-block;
            padding:2px 8px;
            border-radius:999px;
            background:#f8fafc;
            color:#0f172a;
            border:1px solid #e5e7eb;
            font-size:9pt;
            font-weight:bold;
        }
        .clear{ clear:both; height:0; line-height:0; }

        /* Header emitente */
        .header{
            margin-top:8mm;
            border:1px solid #e5e7eb;
            border-radius:6px;
            padding:10px;
        }
        .header-table{
            width:100%;
            border-collapse:collapse;
        }
        .header-table td{ vertical-align:top; }

        .logo-box{ width:45mm; padding-right:6mm; }
        .logo{ width:45mm; height:auto; display:block; }

        .emitente-name{ margin:0; font-size:13pt; font-weight:bold; color:#0f172a; }
        .muted{ color:#64748b; font-size:9.2pt; }
        .strong{ font-weight:bold; color:#0f172a; }

        .contact-box{
            width:65mm;
            text-align:right;
            font-size:9.5pt;
            line-height:1.35;
        }

        /* Blocos */
        .block{
            margin-top:6mm;
            border:1px solid #e5e7eb;
            border-radius:6px;
        }
        .block-h{
            background:#f8fafc;
            border-bottom:1px solid #e5e7eb;
            padding:7px 10px;
            font-weight:bold;
            color:#0f172a;
            font-size:10pt;
        }
        .block-b{
            padding:9px 10px;
            font-size:9.8pt;
            line-height:1.45;
        }

        /* Título OS */
        .os-title{
            margin-top:8mm;
            border:1px solid #e5e7eb;
            border-radius:6px;
            overflow:hidden;
        }
        .os-top{
            background:#f1f5f9;
            padding:8px 10px;
            border-bottom:1px solid #e5e7eb;
        }
        .osnum{
            font-size:12pt;
            font-weight:bold;
            color:#0f172a;
        }
        .osmeta{
            float:right;
            font-size:9.5pt;
            color:#334155;
            margin-top:2px;
        }
        .pill{
            display:inline-block;
            padding:2px 8px;
            border-radius:999px;
            border:1px solid #e5e7eb;
            background:#fff;
            font-size:9pt;
            font-weight:bold;
        }
        .os-bottom{
            padding:8px 10px;
            font-size:9.5pt;
            color:#334155;
        }

        /* Grid (tabela) */
        .grid{
            width:100%;
            border-collapse:collapse;
        }
        .grid td{ width:50%; vertical-align:top; }
        .pad-r{ padding-right:6mm; }
        .pad-l{ padding-left:6mm; text-align:right; }

        /* Tabelas */
        .tbl{
            width:100%;
            border-collapse:collapse;
            font-size:9.6pt;
        }
        .tbl th, .tbl td{
            border:1px solid #e5e7eb;
            padding:6px 7px;
        }
        .tbl thead th{
            background:#0f172a;
            color:#fff;
            font-weight:bold;
            font-size:9.2pt;
            text-transform:uppercase;
        }
        .center{ text-align:center; white-space:nowrap; }
        .num{ text-align:right; white-space:nowrap; }
        .totrow td{ background:#f8fafc; font-weight:bold; }

        /* Pagamento */
        .pay-table{ width:100%; border-collapse:collapse; margin-top:6mm; }
        .qrbox{
            border:1px solid #e5e7eb;
            border-radius:6px;
            padding:8px;
            width:55mm;
        }
        .qrbox img{ width:40mm; height:auto; display:block; margin:0 auto; }
        .qrhint{ text-align:center; margin-top:6px; font-size:8.8pt; color:#334155; }
        .pixkey{ margin-top:6px; font-size:8.8pt; text-align:center; word-break:break-word; }

        .footer{
            margin-top:8mm;
            border:1px solid #e5e7eb;
            border-radius:6px;
            padding:10px;
        }
        .signs{ width:100%; border-collapse:collapse; margin-top:10mm; }
        .signs td{ width:50%; text-align:center; vertical-align:bottom; }
        .signline{ border-top:1px solid #111827; padding-top:4px; font-size:9.5pt; }

        .alert-danger{
            border:1px solid #fecaca;
            background:#fef2f2;
            color:#7f1d1d;
            padding:10px;
            border-radius:6px;
            font-weight:bold;
        }

        /* evita quebras feias */
        .avoid-break{ page-break-inside: avoid; }
    </style>
</head>

<body>
<div class="page">

    <!-- Brandbar -->
    <div class="brandbar">
        <div class="brand-left">
            <p class="appname"><?= $this->config->item('app_name') ?></p>
            <p class="subtitle">Ordem de Serviço - documento de atendimento</p>
        </div>
        <div class="brand-right">
            <span class="badge">OS #<?= $osNum ?></span><br>
            <span style="font-size:9.5pt;color:#e2e8f0;">Emissão: <?= date('d/m/Y H:i') ?></span>
        </div>
        <div class="clear"></div>
    </div>

    <!-- Header Emitente -->
    <div class="header avoid-break">
        <?php if ($emitente == null) : ?>
            <div class="alert-danger">
                Você precisa configurar os dados do emitente.
                <div style="margin-top:6px;">
                    <a href="<?= base_url() ?>index.php/mapos/emitente">Configurar emitente</a>
                </div>
            </div>
        <?php else : ?>
            <table class="header-table">
                <tr>
                    <td class="logo-box">
                        <?php if (!empty($emitente->url_logo)) : ?>
                            <img class="logo" src="<?= $emitente->url_logo ?>" alt="Logo">
                        <?php endif; ?>
                    </td>
                    <td>
                        <p class="emitente-name"><?= $emitente->nome ?></p>

                        <?php
                        $this->load->helper('validation');
                        $doc_formatado = '';
                        if ($emitente->cnpj != "00.000.000/0000-00" && $emitente->cnpj != "000.000.000-00") {
                            $doc_formatado = formatar_documento_emitente($emitente->cnpj);
                        }
                        ?>
                        <?php if (!empty($doc_formatado)) : ?>
                            <div class="muted">CPF/CNPJ: <span class="strong"><?= $doc_formatado ?></span></div>
                        <?php endif; ?>

                        <div class="muted" style="margin-top:3px; line-height:1.35;">
                            <?= $emitente->rua . ', ' . $emitente->numero . ' - ' . $emitente->bairro ?><br>
                            <?= $emitente->cidade . ' - ' . $emitente->uf . ' - CEP: ' . $emitente->cep ?>
                        </div>
                    </td>
                    <td class="contact-box">
                        <div><span class="muted">Telefone:</span> <span class="strong"><?= $emitente->telefone ?></span></div>
                        <div><span class="muted">E-mail:</span> <span class="strong"><?= $emitente->email ?></span></div>
                        <div style="margin-top:4px;">
                            <span class="muted">Responsável:</span> <span class="strong"><?= $result->nome ?></span>
                        </div>
                    </td>
                </tr>
            </table>
        <?php endif; ?>
    </div>

    <!-- Título OS -->
    <div class="os-title avoid-break">
        <div class="os-top">
            <span class="osnum">ORDEM DE SERVIÇO</span>
            <span class="osmeta">
                Status: <span class="pill"><?= $result->status ?></span>
            </span>
            <div class="clear"></div>
        </div>
        <div class="os-bottom">
            <?php if (!empty($result->dataInicial)) : ?>
                Entrada: <b><?= date('d/m/Y', strtotime($result->dataInicial)) ?></b>
            <?php endif; ?>
            <?php if (!empty($result->dataFinal)) : ?>
                &nbsp;&nbsp;|&nbsp;&nbsp; Saída: <b><?= date('d/m/Y', strtotime($result->dataFinal)) ?></b>
            <?php endif; ?>
            <?php if (!empty($result->garantia)) : ?>
                &nbsp;&nbsp;|&nbsp;&nbsp; Garantia: <b><?= $result->garantia ?> dia(s)</b>
            <?php endif; ?>
            <?php if (!empty($result->dataFinal) && !empty($result->garantia) && in_array($result->status, ['Finalizado','Faturado'])) : ?>
                &nbsp;&nbsp;|&nbsp;&nbsp; Venc.: <b><?= dateInterval($result->dataFinal, $result->garantia) ?></b>
            <?php endif; ?>
        </div>
    </div>

    <!-- Cliente -->
    <div class="block avoid-break">
        <div class="block-h">DADOS DO CLIENTE</div>
        <div class="block-b">
            <table class="grid">
                <tr>
                    <td class="pad-r">
                        <div class="strong" style="font-size:11pt;"><?= $result->nomeCliente ?></div>
                        <div class="muted">CPF/CNPJ: <span class="strong"><?= $result->documento ?></span></div>
                        <div class="muted">
                            Contato: <span class="strong"><?= $result->contato_cliente ?></span>
                            <?= $result->telefone ? ' - ' . $result->telefone : '' ?>
                            <?= ($result->telefone && $result->celular) ? ' / ' : '' ?>
                            <?= $result->celular ?>
                        </div>
                        <div class="muted">E-mail: <span class="strong"><?= $result->email ?></span></div>
                    </td>
                    <td class="pad-l">
                        <div><?= $result->rua . ', ' . $result->numero . ' - ' . $result->bairro ?></div>
                        <div><?= $result->complemento ? ($result->complemento . ' - ') : '' ?><?= $result->cidade . ' - ' . $result->estado ?></div>
                        <div class="muted">CEP: <span class="strong"><?= $result->cep ?></span></div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <?php if ($result->descricaoProduto) : ?>
        <div class="block avoid-break">
            <div class="block-h">DESCRIÇÃO</div>
            <div class="block-b"><?= printSafeHtml($result->descricaoProduto) ?></div>
        </div>
    <?php endif; ?>

    <?php if ($result->defeito) : ?>
        <div class="block avoid-break">
            <div class="block-h">DEFEITO APRESENTADO</div>
            <div class="block-b"><?= printSafeHtml($result->defeito) ?></div>
        </div>
    <?php endif; ?>

    <?php if ($result->observacoes) : ?>
        <div class="block avoid-break">
            <div class="block-h">OBSERVAÇÕES</div>
            <div class="block-b"><?= printSafeHtml($result->observacoes) ?></div>
        </div>
    <?php endif; ?>

    <?php if ($result->laudoTecnico) : ?>
        <div class="block avoid-break">
            <div class="block-h">PARECER TÉCNICO</div>
            <div class="block-b"><?= printSafeHtml($result->laudoTecnico) ?></div>
        </div>
    <?php endif; ?>

    <?php if ($result->garantias_id) : ?>
        <div class="block avoid-break">
            <div class="block-h">TERMO DE GARANTIA</div>
            <div class="block-b"><?= printSafeHtml($result->textoGarantia) ?></div>
        </div>
    <?php endif; ?>

    <!-- Produtos -->
    <?php if ($produtos) : ?>
        <div class="block avoid-break">
            <div class="block-h">PRODUTOS</div>
            <div class="block-b">
                <table class="tbl">
                    <thead>
                    <tr>
                        <th>Produto</th>
                        <th style="width:14mm;">Qtd</th>
                        <th style="width:24mm;">Unit.</th>
                        <th style="width:28mm;">Subtotal</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($produtos as $p) :
                        $totalProdutos += $p->subTotal;
                        $unit = $p->preco ?: $p->precoVenda;
                        ?>
                        <tr>
                            <td><?= getNomeProduto($p) ?></td>
                            <td class="center"><?= $p->quantidade ?></td>
                            <td class="num">R$ <?= number_format($unit, 2, ',', '.') ?></td>
                            <td class="num">R$ <?= number_format($p->subTotal, 2, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="totrow">
                        <td colspan="3" class="num">TOTAL PRODUTOS</td>
                        <td class="num">R$ <?= number_format($totalProdutos, 2, ',', '.') ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

    <!-- Serviços -->
    <?php if ($servicos) : ?>
        <div class="block avoid-break">
            <div class="block-h">SERVIÇOS</div>
            <div class="block-b">
                <table class="tbl">
                    <thead>
                    <tr>
                        <th>Serviço</th>
                        <th style="width:14mm;">Qtd</th>
                        <th style="width:24mm;">Unit.</th>
                        <th style="width:28mm;">Subtotal</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($servicos as $s) :
                        $preco = $s->preco ?: $s->precoVenda;
                        $qtd = ($s->quantidade ?: 1);
                        $subtotal = $preco * $qtd;
                        $totalServico += $subtotal;
                        ?>
                        <tr>
                            <td><?= $s->nome ?></td>
                            <td class="center"><?= $qtd ?></td>
                            <td class="num">R$ <?= number_format($preco, 2, ',', '.') ?></td>
                            <td class="num">R$ <?= number_format($subtotal, 2, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="totrow">
                        <td colspan="3" class="num">TOTAL SERVIÇOS</td>
                        <td class="num">R$ <?= number_format($totalServico, 2, ',', '.') ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

    <!-- Pagamento -->
    <?php if ($totalProdutos != 0 || $totalServico != 0) : ?>
        <table class="pay-table avoid-break">
            <tr>
                <td style="width:60mm;">
                    <?php if (!empty($this->data['configuration']['pix_key'])) : ?>
                        <div class="qrbox">
                            <img src="<?= $qrCode ?>" alt="QR Code Pix">
                            <div class="qrhint">Escaneie o QR Code para pagar via Pix</div>
                            <div class="pixkey">Chave Pix:<br><b><?= $chaveFormatada ?></b></div>
                        </div>
                    <?php endif; ?>
                </td>
                <td style="padding-left:6mm;">
                    <div class="block" style="margin-top:0;">
                        <div class="block-h">RESUMO DOS VALORES</div>
                        <div class="block-b">
                            <?php $subtotalGeral = ($totalProdutos + $totalServico); ?>

                            <table class="tbl">
                                <tbody>
                                <?php if (!empty($result->valor_desconto) && (float)$result->valor_desconto != 0) : ?>
                                    <tr>
                                        <td>SUBTOTAL</td>
                                        <td class="num"><b>R$ <?= number_format($subtotalGeral, 2, ',', '.') ?></b></td>
                                    </tr>
                                    <tr>
                                        <td>DESCONTO</td>
                                        <td class="num">
                                            <b>R$ <?= number_format(($result->valor_desconto - $subtotalGeral), 2, ',', '.') ?></b>
                                        </td>
                                    </tr>
                                    <tr class="totrow">
                                        <td><b>TOTAL</b></td>
                                        <td class="num"><b>R$ <?= number_format($result->valor_desconto, 2, ',', '.') ?></b></td>
                                    </tr>
                                <?php else : ?>
                                    <tr class="totrow">
                                        <td><b>TOTAL</b></td>
                                        <td class="num"><b>R$ <?= number_format($subtotalGeral, 2, ',', '.') ?></b></td>
                                    </tr>
                                <?php endif; ?>
                                </tbody>
                            </table>

                            <div class="muted" style="margin-top:6px;">
                                Valores sujeitos a validação conforme itens e serviços executados.
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    <?php endif; ?>

    <!-- Rodapé -->
    <div class="footer avoid-break">
        <div class="muted">
            Entrada: <b><?= !empty($result->dataInicial) ? date('d/m/Y', strtotime($result->dataInicial)) : '' ?></b>
            &nbsp;&nbsp;|&nbsp;&nbsp;
            OS: <b>#<?= $osNum ?></b>
            &nbsp;&nbsp;|&nbsp;&nbsp;
            Saída: <b><?= !empty($result->dataFinal) ? date('d/m/Y', strtotime($result->dataFinal)) : '' ?></b>
        </div>

        <table class="signs">
            <tr>
                <td><div class="signline">Assinatura do cliente</div></td>
                <td><div class="signline">Assinatura do técnico</div></td>
            </tr>
        </table>

        <div class="muted" style="margin-top:6px; text-align:center;">
            Documento gerado por <?= $this->config->item('app_name') ?> • <?= date('d/m/Y H:i') ?>
        </div>
    </div>

</div>
</body>
</html>
