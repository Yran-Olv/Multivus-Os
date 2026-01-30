<?php
$totalServico  = 0;
$totalProdutos = 0;
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <title><?= $this->config->item('app_name') ?> - <?= $result->idOs ?> - <?= $result->nomeCliente ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/bootstrap5.3.2.min.css" />
    <link rel="stylesheet" href="<?= base_url() ?>assets/font-awesome/css/font-awesome.css" />
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/imprimir.css">
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
                    <div class="imgLogo" class="align-middle">
                        <img src="<?= $emitente->url_logo ?>" class="img-fluid" style="width:140px;">
                    </div>
                    <div class="emitente">
                        <span style="font-size: 16px;"><b><?= $emitente->nome ?></b></span></br>
                        <?php 
                        $this->load->helper('validation');
                        if ($emitente->cnpj != "00.000.000/0000-00" && $emitente->cnpj != "000.000.000-00") : 
                            $doc_formatado = formatar_documento_emitente($emitente->cnpj);
                            if (!empty($doc_formatado)) :
                        ?>
                            <span class="align-middle"><?= $doc_formatado ?></span></br>
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
                        <span style="font-weight: bold;">Tel: <?= $emitente->telefone ?></span></br>
                        <span style="font-weight: bold;"><?= $emitente->email ?></span></br>
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

        <?php if ($configuration['control_2vias']) : ?>
            <div class="sub-page novaPagina">
                <header>
                    <?php if ($emitente == null) : ?>
                        <div class="alert alert-danger" role="alert">
                            Você precisa configurar os dados do emitente. >>> <a href="<?=base_url()?>index.php/mapos/emitente">Configurar</a>
                        </div>
                    <?php else : ?>
                        <div class="imgLogo" class="align-middle">
                            <img src="<?= $emitente->url_logo ?>" class="img-fluid" style="width:140px;">
                        </div>
                        <div class="emitente">
                            <span style="font-size: 16px;"><b><?= $emitente->nome ?></b></span></br>
                            <?php 
                            if ($emitente->cnpj != "00.000.000/0000-00" && $emitente->cnpj != "000.000.000-00") : 
                                $doc_formatado = formatar_documento_emitente($emitente->cnpj);
                                if (!empty($doc_formatado)) :
                            ?>
                                <span class="align-middle"><?= $doc_formatado ?></span></br>
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
                            <span style="font-weight: bold;">Tel: <?= $emitente->telefone ?></span></br>
                            <span style="font-weight: bold;"><?= $emitente->email ?></span></br>
                            <span style="word-break: break-word;">Responsável: <b><?= $result->nome ?></b></span>
                        </div>
                    <?php endif; ?>
                </header>
                <section>
                    <div class="title">
                        <!-- VIA EMPRESA  -->
                        <?php $totalServico = 0;
$totalProdutos = 0; ?>
                        <?php if ($configuration['control_2vias']) : ?><span class="via">Via Empresa</span><?php endif; ?>
                        ORDEM DE SERVIÇO #<?= str_pad($result->idOs, 4, 0, STR_PAD_LEFT) ?>
                        <span class="emissao">Emissão: <?= date('d/m/Y') ?></span>
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
                            <div>
                                <?= printSafeHtml($result->descricaoProduto) ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($result->defeito) : ?>
                        <div class="subtitle">DEFEITO APRESENTADO</div>
                        <div class="dados">
                            <div>
                                <?= printSafeHtml($result->defeito) ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($result->observacoes) : ?>
                        <div class="subtitle">OBSERVAÇÕES</div>
                        <div class="dados">
                            <div>
                                <?= printSafeHtml($result->observacoes) ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($result->laudoTecnico) : ?>
                        <div class="subtitle">PARECER TÉCNICO</div>
                        <div class="dados">
                            <div>
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
        <?php endif; ?>

        <?php if ($anexos && $imprimirAnexo) : ?>
            <div class="sub-page" id="anexos">
                <header style="border: 1px solid #cdcdcd">
                    <?php if ($emitente == null) : ?>
                        <div class="alert alert-danger" role="alert">
                            Você precisa configurar os dados do emitente. >>> <a href="<?= base_url() ?>index.php/mapos/emitente">Configurar</a>
                        </div>
                    <?php else : ?>
                        <div id="imgLogo" class="align-middle">
                            <img src="<?= $emitente->url_logo ?>" class="img-fluid" style="width:140px;">
                        </div>
                        <div style="padding-left: 10px; padding-right: 10px; margin-top: 3px;">
                            <span style="font-size: 16px;"><b><?= $emitente->nome ?></b></span></br>
                            <?php 
                            if ($emitente->cnpj != "00.000.000/0000-00" && $emitente->cnpj != "000.000.000-00") : 
                                $doc_formatado = formatar_documento_emitente($emitente->cnpj);
                                if (!empty($doc_formatado)) :
                            ?>
                                <span class="align-middle"><?= $doc_formatado ?></span></br>
                            <?php 
                                endif;
                            endif; 
                            ?>
                            <span class="align-middle">
                                <?= $emitente->rua.', '.$emitente->numero.', '.$emitente->bairro ?><br>
                                <?= $emitente->cidade.' - '.$emitente->uf.' - '.$emitente->cep ?>
                            </span>
                        </div>
                        <div style="text-align: right; max-width: 230px; margin-top: 10px;">
                            <span style="font-weight: bold;">Tel: <?= $emitente->telefone ?></span></br>
                            <span style="font-weight: bold;"><?= $emitente->email ?></span></br>
                            <span style="word-break: break-word;">Responsável: <b><?= $result->nome ?></b></span>
                        </div>
                    <?php endif; ?>
                </header>
                <section>
                    <div class="title">
                        ORDEM DE SERVIÇO #<?= str_pad($result->idOs, 4, 0, STR_PAD_LEFT) ?>
                        <span class="emissao">Emissão: <?= date('d/m/Y') ?></span>
                    </div>
                    <div class="subtitle">ANEXO(S)</div>
                    <div class="dados">
                        <div style="width: 100%; display: flex; justify-content: space-around; align-items: center; flex-wrap: wrap;">
                            <?php
                                $contaAnexos = 0;
foreach ($anexos as $a) :
    if ($a->thumb) :
        $thumb = $a->url.'/thumbs/'.$a->thumb;
        $link  = $a->url.'/'.$a->anexo;
        ?>
                                        <img src="<?= $link ?>" alt="">
                            <?php
    endif;
endforeach;
?>
                        </div>
                    </div>
                <section>
            </div>
        <?php endif; ?>
    </div>
    <!-- Botão e Modal WhatsApp -->
    <?php 
    $this->load->helper('whatsapp');
    $idOs = $result->idOs;
    // A configuração está disponível via $this->data['configuration'] do MY_Controller
    $CI = &get_instance();
    $configWhatsapp = isset($CI->data['configuration']) ? $CI->data['configuration'] : [];
    $enabled = isset($configWhatsapp['whatsapp_enabled']) && $configWhatsapp['whatsapp_enabled'] == '1';
    $hasToken = isset($configWhatsapp['whatsapp_api_token']) && !empty($configWhatsapp['whatsapp_api_token']);
    $urlWhatsapp = base_url() . 'index.php/whatsapp_os/enviar/' . $idOs;
    ?>
    
    <div id="whatsapp-controls" style="position: fixed; top: 20px; right: 20px; z-index: 9999; display: flex; gap: 10px;">
        <?php if ($enabled && $hasToken) { ?>
            <button type="button" class="btn btn-success" onclick="abrirModalWhatsApp()" style="padding: 10px 20px; font-size: 14px; background: #25D366; border: none; color: white; border-radius: 5px; cursor: pointer;">
                <i class="bx bxl-whatsapp"></i> Enviar via WhatsApp
            </button>
        <?php } else { ?>
            <button type="button" class="btn btn-success" onclick="alert('Configure o WhatsApp em Mapos > Configurações > API')" style="padding: 10px 20px; font-size: 14px; opacity: 0.7; background: #25D366; border: none; color: white; border-radius: 5px; cursor: pointer;">
                <i class="bx bxl-whatsapp"></i> Enviar via WhatsApp
            </button>
        <?php } ?>
        <button type="button" class="btn btn-primary" onclick="window.print()" style="padding: 10px 20px; font-size: 14px; background: #007bff; border: none; color: white; border-radius: 5px; cursor: pointer;">
            <i class="bx bx-printer"></i> Imprimir
        </button>
    </div>

    <!-- Modal WhatsApp -->
    <div id="modal-whatsapp" style="display: none; position: fixed; z-index: 10000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5);">
        <div style="background-color: #fefefe; margin: 15% auto; padding: 30px; border: none; width: 450px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 15px;">
                <h3 style="margin: 0; color: #25D366;">
                    <i class="bx bxl-whatsapp"></i> Enviar via WhatsApp
                </h3>
                <span onclick="fecharModalWhatsApp()" style="color: #aaa; font-size: 28px; font-weight: bold; cursor: pointer; line-height: 1;">&times;</span>
            </div>
            <p style="margin-bottom: 20px; color: #666;">
                Deseja enviar a <strong>Ordem de Serviço #<?= str_pad($idOs, 4, 0, STR_PAD_LEFT) ?></strong> para o cliente <strong><?= $result->nomeCliente ?></strong> via WhatsApp?
            </p>
            <div style="margin-top: 25px; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="fecharModalWhatsApp()" style="padding: 10px 20px; background: #6c757d; border: none; color: white; border-radius: 5px; cursor: pointer;">Cancelar</button>
                <button type="button" onclick="enviarWhatsApp()" id="btn-enviar-whatsapp" style="padding: 10px 20px; background: #25D366; border: none; color: white; border-radius: 5px; cursor: pointer;">
                    <i class="bx bxl-whatsapp"></i> Enviar
                </button>
            </div>
        </div>
    </div>

    <style>
        /* Ocultar controles na impressão */
        @media print {
            #whatsapp-controls, #modal-whatsapp {
                display: none !important;
            }
        }
        
        /* Estilos para os botões */
        #whatsapp-controls button:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }
        
        #whatsapp-controls button:active {
            transform: translateY(0);
        }
    </style>

    <script src="<?= base_url() ?>assets/js/sweetalert2.all.min.js"></script>
    <script type="text/javascript">
        function abrirModalWhatsApp() {
            document.getElementById('modal-whatsapp').style.display = 'block';
        }
        
        function fecharModalWhatsApp() {
            document.getElementById('modal-whatsapp').style.display = 'none';
        }
        
        function enviarWhatsApp() {
            var btn = document.getElementById('btn-enviar-whatsapp');
            btn.disabled = true;
            btn.innerHTML = '<i class="bx bx-loader bx-spin"></i> Enviando...';
            
            var url = '<?= $urlWhatsapp ?>';
            
            // Redirecionar para a URL de envio
            Swal.fire({
                icon: 'info',
                title: 'Enviando...',
                text: 'A ordem de serviço está sendo enviada via WhatsApp.',
                showConfirmButton: false,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Redirecionar após um breve delay
            setTimeout(function() {
                window.location.href = url;
            }, 500);
        }
        
        // Fechar modal ao clicar fora
        window.onclick = function(event) {
            var modal = document.getElementById('modal-whatsapp');
            if (event.target == modal) {
                fecharModalWhatsApp();
            }
        }
        
        // Fechar modal com ESC
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                fecharModalWhatsApp();
            }
        });
        
        // Auto-print após 2 segundos (para dar tempo de ver os botões)
        setTimeout(function() {
            // Não fazer print automático se o modal estiver aberto
            if (document.getElementById('modal-whatsapp').style.display !== 'block') {
        window.print();
            }
        }, 2000);
    </script>
</body>
</html>
