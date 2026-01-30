<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/custom.css" />
<script src="<?php echo base_url() ?>assets/js/sweetalert2.all.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/jquery.mask.min.js"></script>

<div class="new122">
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon">
            <i class="fas fa-door-open"></i>
        </span>
        <h5>Fechar Caixa</h5>
    </div>

    <!-- Estatísticas do Turno -->
    <?php if (isset($estatisticas)): ?>
    <div class="span12" style="margin-left: 0; margin-top: 20px;">
        <div class="widget-box">
            <div class="widget-title">
                <h5>Resumo do Turno</h5>
            </div>
            <div class="widget-content">
                <div class="span12" style="margin-left: 0;">
                    <div class="span3" style="text-align: center; padding: 20px; background: #E3F2FD; border-radius: 5px; margin: 5px;">
                        <h3 style="margin: 0; color: #2196F3;"><?php echo number_format($estatisticas['vendas']->total_vendas, 0, ',', '.'); ?></h3>
                        <p style="margin: 5px 0 0 0;">Total de Vendas</p>
                    </div>
                    <div class="span3" style="text-align: center; padding: 20px; background: #E8F5E9; border-radius: 5px; margin: 5px;">
                        <h3 style="margin: 0; color: #4CAF50;">R$ <?php echo number_format($estatisticas['vendas']->total_vendido, 2, ',', '.'); ?></h3>
                        <p style="margin: 5px 0 0 0;">Total Vendido</p>
                    </div>
                    <div class="span3" style="text-align: center; padding: 20px; background: #FFF3E0; border-radius: 5px; margin: 5px;">
                        <h3 style="margin: 0; color: #FF9800;">R$ <?php echo number_format($caixa_aberto->valor_abertura, 2, ',', '.'); ?></h3>
                        <p style="margin: 5px 0 0 0;">Valor de Abertura</p>
                    </div>
                    <div class="span3" style="text-align: center; padding: 20px; background: #F3E5F5; border-radius: 5px; margin: 5px;">
                        <h3 style="margin: 0; color: #9C27B0;">R$ <?php echo number_format($caixa_aberto->valor_abertura + $estatisticas['vendas']->total_vendido, 2, ',', '.'); ?></h3>
                        <p style="margin: 5px 0 0 0;">Valor Esperado</p>
                    </div>
                </div>

                <?php if (!empty($estatisticas['pagamentos'])): ?>
                <div class="span12" style="margin-left: 0; margin-top: 20px;">
                    <h5>Pagamentos por Forma:</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Forma de Pagamento</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($estatisticas['pagamentos'] as $pagamento): ?>
                            <tr>
                                <td><?php echo $pagamento->nome; ?></td>
                                <td>R$ <?php echo number_format($pagamento->total, 2, ',', '.'); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Formulário de Fechamento -->
    <div class="span12" style="margin-left: 0; margin-top: 20px;">
        <div class="widget-box">
            <div class="widget-title">
                <h5>Fechamento do Caixa</h5>
            </div>
            <div class="widget-content">
                <?php if (isset($custom_error) && $custom_error): ?>
                <div class="alert alert-danger">
                    <?php echo $custom_error; ?>
                </div>
                <?php endif; ?>

                <form action="<?php echo current_url(); ?>" method="post" id="formFecharCaixa">
                    <div class="span12" style="margin-left: 0;">
                        <div class="span6" style="margin-left: 0;">
                            <label for="valor_fechamento">Valor de Fechamento (R$) *</label>
                            <input type="text" 
                                   name="valor_fechamento" 
                                   id="valor_fechamento" 
                                   class="span12 money" 
                                   placeholder="0,00"
                                   required
                                   autofocus>
                            <span class="help-inline">Digite o valor total encontrado no caixa</span>
                        </div>
                        <div class="span6">
                            <label for="observacoes">Observações</label>
                            <textarea name="observacoes" 
                                      id="observacoes" 
                                      class="span12" 
                                      rows="4"
                                      placeholder="Observações sobre o fechamento..."></textarea>
                        </div>
                    </div>

                    <div class="span12" style="margin-left: 0; margin-top: 20px;">
                        <div class="span12" style="margin-left: 0;">
                            <button type="submit" class="button btn btn-danger">
                                <span class="button__icon"><i class="fas fa-door-open"></i></span>
                                <span class="button__text2">Fechar Caixa</span>
                            </button>
                            <a href="<?php echo base_url(); ?>index.php/vendas/pdv" class="button btn btn-warning">
                                <span class="button__icon"><i class="fas fa-times"></i></span>
                                <span class="button__text2">Cancelar</span>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.money').mask('#.##0,00', {reverse: true});
    
    // Sugerir valor esperado
    const valorEsperado = <?php echo $caixa_aberto->valor_abertura + ($estatisticas['vendas']->total_vendido ?? 0); ?>;
    $('#valor_fechamento').val(valorEsperado.toFixed(2).replace('.', ','));
    
    $('#formFecharCaixa').submit(function(e) {
        e.preventDefault();
        
        const valorFechamento = parseFloat($('#valor_fechamento').val().replace(',', '.')) || 0;
        const diferenca = valorFechamento - valorEsperado;
        
        let mensagem = 'Valor de fechamento: R$ ' + valorFechamento.toFixed(2).replace('.', ',');
        if (diferenca != 0) {
            mensagem += '\nDiferença: R$ ' + Math.abs(diferenca).toFixed(2).replace('.', ',');
            if (diferenca > 0) {
                mensagem += ' (Sobra)';
            } else {
                mensagem += ' (Falta)';
            }
        }
        
        Swal.fire({
            title: 'Fechar Caixa?',
            text: mensagem,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sim, fechar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });
});
</script>
