<?php if ($emitente): ?>
    <div>
        <br>
        <div style="width: 50%; float: left" class="float-left col-md-3">
            <?php if (file_exists(convertUrlToUploadsPath($emitente->url_logo))) { ?>
                <img style="width: 150px" src="<?= convertUrlToUploadsPath($emitente->url_logo) ?>" alt="<?= $emitente->nome ?>"><br><br>
            <?php } else { ?>
                <div style="width: 150px;"><p></p></div>
            <?php } ?>
        </div>
        <div style="float: right">
            <?php 
            $this->load->helper('validation');
            $doc_formatado = formatar_documento_emitente($emitente->cnpj);
            ?>
            <b>EMPRESA: </b> <?= $emitente->nome ?> <b><?= !empty($doc_formatado) ? $doc_formatado : 'CNPJ: ' . $emitente->cnpj ?></b><br>
            <b>ENDEREÇO: </b> <?= $emitente->rua ?>, <?= $emitente->numero ?>, <?= $emitente->bairro ?>, <?= $emitente->cidade ?> - <?= $emitente->uf ?> <br>

            <?php if (isset($title)): ?>
                <b>RELATÓRIO: </b> <?= $title ?> <br>
            <?php endif ?>

            <?php if (isset($dataInicial)): ?>
                <b>DATA INICIAL: </b> <?= $dataInicial ?>
            <?php endif ?>

            <?php if (isset($dataFinal)): ?>
                <b>DATA FINAL: </b> <?= $dataFinal ?>
            <?php endif ?>
        </div>
    </div>
<?php endif ?>
