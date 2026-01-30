<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/custom.css" />
<script src="<?php echo base_url() ?>assets/js/sweetalert2.all.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/jquery.mask.min.js"></script>

<div class="new122">
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon">
            <i class="fas fa-cash-register"></i>
        </span>
        <h5>Abrir Caixa</h5>
    </div>

    <div class="span12" style="margin-left: 0; margin-top: 20px;">
        <div class="widget-box">
            <div class="widget-content">
                <?php if (isset($custom_error) && $custom_error): ?>
                <div class="alert alert-danger">
                    <?php echo $custom_error; ?>
                </div>
                <?php endif; ?>

                <form action="<?php echo current_url(); ?>" method="post" id="formAbrirCaixa">
                    <div class="span12" style="margin-left: 0;">
                        <div class="span6" style="margin-left: 0;">
                            <label for="caixa_id">Caixa *</label>
                            <select name="caixa_id" id="caixa_id" class="span12" required>
                                <option value="">Selecione um caixa...</option>
                                <?php foreach ($caixas as $caixa): ?>
                                <option value="<?php echo $caixa->idCaixa; ?>">
                                    <?php echo $caixa->nome; ?>
                                    <?php if ($caixa->descricao): ?>
                                    - <?php echo $caixa->descricao; ?>
                                    <?php endif; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="span6">
                            <label for="valor_abertura">Valor de Abertura (R$) *</label>
                            <input type="text" 
                                   name="valor_abertura" 
                                   id="valor_abertura" 
                                   class="span12 money" 
                                   placeholder="0,00"
                                   required
                                   autofocus>
                        </div>
                    </div>

                    <div class="span12" style="margin-left: 0; margin-top: 20px;">
                        <div class="span12" style="margin-left: 0;">
                            <button type="submit" class="button btn btn-success">
                                <span class="button__icon"><i class="fas fa-check"></i></span>
                                <span class="button__text2">Abrir Caixa</span>
                            </button>
                            <a href="<?php echo base_url(); ?>index.php/mapos" class="button btn btn-warning">
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
    
    $('#formAbrirCaixa').submit(function(e) {
        e.preventDefault();
        
        Swal.fire({
            title: 'Abrir Caixa?',
            text: 'Confirme o valor de abertura',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sim, abrir',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });
});
</script>
