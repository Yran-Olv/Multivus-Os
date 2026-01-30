<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/custom.css" />
<script src="<?php echo base_url() ?>assets/js/sweetalert2.all.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/jquery.mask.min.js"></script>

<div class="new122">
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon">
            <i class="fas fa-file-invoice-dollar"></i>
        </span>
        <h5>Venda a Prazo #<?php echo $venda->idVendas; ?></h5>
    </div>

    <div class="span12" style="margin-left: 0; margin-top: 20px;">
        <!-- Informações da Venda -->
        <div class="widget-box">
            <div class="widget-title">
                <h5>Informações da Venda</h5>
            </div>
            <div class="widget-content">
                <div class="span6">
                    <p><strong>Cliente:</strong> <?php echo $venda->nomeCliente; ?></p>
                    <p><strong>Data da Venda:</strong> <?php echo date('d/m/Y', strtotime($venda->dataVenda)); ?></p>
                    <p><strong>Vendedor:</strong> <?php echo $venda->nome; ?></p>
                    <p><strong>Número de Parcelas:</strong> <?php echo $venda->numero_parcelas; ?></p>
                </div>
                <div class="span6">
                    <p><strong>Valor Total:</strong> R$ <?php echo number_format($venda->valorTotal, 2, ',', '.'); ?></p>
                    <p><strong>Valor Pago:</strong> R$ <?php echo number_format($venda->valor_pago_total, 2, ',', '.'); ?></p>
                    <p><strong>Valor Pendente:</strong> R$ <?php echo number_format($venda->valor_pendente, 2, ',', '.'); ?></p>
                    <p><strong>Taxa de Juros:</strong> <?php echo number_format($venda->taxa_juros, 2, ',', '.'); ?>%</p>
                </div>
            </div>
        </div>

        <!-- Produtos -->
        <?php if (!empty($produtos)): ?>
        <div class="widget-box" style="margin-top: 20px;">
            <div class="widget-title">
                <h5>Produtos</h5>
            </div>
            <div class="widget-content nopadding">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Quantidade</th>
                            <th>Preço Unit.</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($produtos as $produto): ?>
                        <tr>
                            <td><?php echo $produto->descricao; ?></td>
                            <td><?php echo $produto->quantidade; ?></td>
                            <td>R$ <?php echo number_format($produto->preco, 2, ',', '.'); ?></td>
                            <td>R$ <?php echo number_format($produto->subTotal, 2, ',', '.'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Parcelas -->
        <div class="widget-box" style="margin-top: 20px;">
            <div class="widget-title">
                <h5>Parcelas</h5>
            </div>
            <div class="widget-content nopadding">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Valor</th>
                            <th>Vencimento</th>
                            <th>Pago</th>
                            <th>Status</th>
                            <th>Dias Atraso</th>
                            <th>Juros/Multa</th>
                            <th>Total</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($parcelas as $parcela): ?>
                        <tr class="<?php echo $parcela->status == 'atrasada' ? 'danger' : ($parcela->status == 'paga' ? 'success' : ''); ?>">
                            <td><?php echo $parcela->numero_parcela; ?></td>
                            <td>R$ <?php echo number_format($parcela->valor_parcela, 2, ',', '.'); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($parcela->data_vencimento)); ?></td>
                            <td>R$ <?php echo number_format($parcela->valor_pago, 2, ',', '.'); ?></td>
                            <td>
                                <?php if ($parcela->status == 'paga'): ?>
                                    <span class="label label-success">Paga</span>
                                <?php elseif ($parcela->status == 'atrasada'): ?>
                                    <span class="label label-important">Atrasada</span>
                                <?php else: ?>
                                    <span class="label label-warning">Pendente</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($parcela->dias_atraso > 0): ?>
                                    <span class="badge badge-danger"><?php echo $parcela->dias_atraso; ?> dias</span>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($parcela->juros > 0 || $parcela->multa > 0): ?>
                                    J: R$ <?php echo number_format($parcela->juros, 2, ',', '.'); ?><br>
                                    M: R$ <?php echo number_format($parcela->multa, 2, ',', '.'); ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td><strong>R$ <?php echo number_format($parcela->valor_total, 2, ',', '.'); ?></strong></td>
                            <td>
                                <?php if ($parcela->status != 'paga'): ?>
                                <button class="btn btn-success btn-mini btnPagarParcela" data-parcela-id="<?php echo $parcela->idParcela; ?>" data-valor="<?php echo $parcela->valor_total; ?>">
                                    <i class="fas fa-money-bill-wave"></i> Pagar
                                </button>
                                <?php else: ?>
                                <span class="label label-success">Paga em <?php echo date('d/m/Y', strtotime($parcela->data_pagamento)); ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Pagamento -->
<div id="modalPagarParcela" class="modal hide fade" tabindex="-1" role="dialog">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3>Registrar Pagamento</h3>
    </div>
    <form id="formPagarParcela">
        <div class="modal-body">
            <input type="hidden" id="parcela_id" name="parcela_id">
            <div class="span12" style="margin-left: 0;">
                <div class="span6">
                    <label>Valor da Parcela</label>
                    <input type="text" id="valor_parcela" class="span12" readonly style="background: #f5f5f5;">
                </div>
                <div class="span6">
                    <label>Valor Pago *</label>
                    <input type="text" id="valor_pago" name="valor_pago" class="span12 money" required>
                </div>
            </div>
            <div class="span12" style="margin-left: 0;">
                <div class="span6">
                    <label>Data Pagamento *</label>
                    <input type="date" id="data_pagamento" name="data_pagamento" class="span12" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="span6">
                    <label>Forma de Pagamento</label>
                    <select id="forma_pagamento_id" name="forma_pagamento_id" class="span12">
                        <option value="">Selecione...</option>
                        <?php
                        // Buscar formas de pagamento
                        $formas = $this->db->get('formas_pagamento')->result();
                        foreach ($formas as $forma):
                        ?>
                        <option value="<?php echo $forma->idFormaPagamento; ?>"><?php echo $forma->nome; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="span12" style="margin-left: 0;">
                <div class="span6">
                    <label>Desconto</label>
                    <input type="text" id="desconto" name="desconto" class="span12 money" value="0,00">
                </div>
                <div class="span6">
                    <label>Observações</label>
                    <textarea id="observacoes" name="observacoes" class="span12" rows="3"></textarea>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-success">Registrar Pagamento</button>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    // Máscara de dinheiro
    $('.money').mask('#.##0,00', {reverse: true});

    // Abrir modal de pagamento
    $('.btnPagarParcela').click(function() {
        var parcelaId = $(this).data('parcela-id');
        var valor = $(this).data('valor');
        
        $('#parcela_id').val(parcelaId);
        $('#valor_parcela').val('R$ ' + valor.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.'));
        $('#valor_pago').val(valor.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.'));
        $('#data_pagamento').val('<?php echo date('Y-m-d'); ?>');
        $('#desconto').val('0,00');
        $('#observacoes').val('');
        
        $('#modalPagarParcela').modal('show');
    });

    // Submeter pagamento
    $('#formPagarParcela').submit(function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        
        $.ajax({
            url: '<?php echo base_url(); ?>index.php/vendas_prazo/registrarPagamento',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.result) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sucesso!',
                        text: response.message || 'Pagamento registrado com sucesso!',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(function() {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro!',
                        text: response.message || 'Erro ao registrar pagamento.'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: 'Erro ao comunicar com o servidor.'
                });
            }
        });
    });
});
</script>

<style>
.label {
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 11px;
}
.label-important {
    background: #f44336;
    color: white;
}
.label-warning {
    background: #FF9800;
    color: white;
}
.label-success {
    background: #4CAF50;
    color: white;
}
.badge {
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 11px;
}
.badge-danger {
    background: #f44336;
    color: white;
}
</style>
