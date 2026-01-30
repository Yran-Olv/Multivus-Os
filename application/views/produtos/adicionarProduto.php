<style>
    /* Hiding the checkbox, but allowing it to be focused */
    .badgebox {
        opacity: 0;
    }

    .badgebox+.badge {
        /* Move the check mark away when unchecked */
        text-indent: -999999px;
        /* Makes the badge's width stay the same checked and unchecked */
        width: 27px;
    }

    .badgebox:focus+.badge {
        /* Set something to make the badge looks focused */
        /* This really depends on the application, in my case it was: */

        /* Adding a light border */
        box-shadow: inset 0px 0px 5px;
        /* Taking the difference out of the padding */
    }

    .badgebox:checked+.badge {
        /* Move the check mark back when checked */
        text-indent: 0;
    }
    
    /* Estilos para especificações técnicas */
    .especificacoes-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin-top: 10px;
    }
    
    .especificacoes-grid > div {
        display: flex;
        flex-direction: column;
    }
    
    .especificacoes-grid label {
        font-weight: normal;
        font-size: 12px;
        margin-bottom: 5px;
        color: #555;
    }
    
    .especificacoes-grid input {
        width: 100%;
        padding: 6px 10px;
        border: 1px solid #ddd;
        border-radius: 3px;
    }
    
    @media (max-width: 768px) {
        .especificacoes-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-shopping-bag"></i>
                </span>
                <h5>Cadastro de Produto</h5>
            </div>
            <div class="widget-content nopadding tab-content">
                <?php echo $custom_error; ?>
                <form action="<?php echo current_url(); ?>" id="formProduto" method="post" class="form-horizontal" enctype="multipart/form-data" novalidate>
                    <div class="control-group">
                        <label for="imagem" class="control-label">Imagem do Produto</label>
                        <div class="controls">
                            <input id="imagem" type="file" name="imagem" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" />
                            <small style="color: #666;">Formatos aceitos: JPG, PNG, GIF, WEBP (máx. 40MB)</small>
                            <div id="preview-imagem" style="margin-top: 10px; display: none;">
                                <img id="img-preview" src="" alt="Preview" style="max-width: 200px; max-height: 200px; border: 1px solid #ddd; border-radius: 5px; padding: 5px;" />
                            </div>
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="nome" class="control-label">Nome do Produto<span class="required">*</span></label>
                        <div class="controls">
                            <input id="nome" type="text" name="nome" value="<?php echo set_value('nome'); ?>" placeholder="Ex: Notebook Sony Vaio" maxlength="100" />
                            <small style="color: #666;">Nome curto para exibição em cards do PDV, listagens e relatórios (máx. 100 caracteres)</small>
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="codDeBarra" class="control-label">Código de Barra<span class=""></span></label>
                        <div class="controls">
                            <input id="codDeBarra" type="text" name="codDeBarra" value="<?php echo set_value('codDeBarra'); ?>" />
                        </div>
                    </div>
                    
                    <!-- Especificações Técnicas -->
                    <div class="control-group">
                        <label class="control-label"><strong>Especificações Técnicas</strong></label>
                        <div class="controls">
                            <small style="color: #666; display: block; margin-bottom: 10px;">Preencha os campos abaixo conforme o tipo de produto (computadores, celulares, periféricos, etc.)</small>
                            
                            <div class="especificacoes-grid">
                                <div>
                                    <label for="marca">Marca</label>
                                    <input id="marca" type="text" name="marca" value="<?php echo set_value('marca'); ?>" placeholder="Ex: Sony, Samsung, Dell" maxlength="100" />
                                </div>
                                <div>
                                    <label for="modelo">Modelo</label>
                                    <input id="modelo" type="text" name="modelo" value="<?php echo set_value('modelo'); ?>" placeholder="Ex: Vaio, Galaxy S23" maxlength="100" />
                                </div>
                                <div>
                                    <label for="processador">Processador</label>
                                    <input id="processador" type="text" name="processador" value="<?php echo set_value('processador'); ?>" placeholder="Ex: Intel i5, Snapdragon 888" maxlength="100" />
                                </div>
                                <div>
                                    <label for="memoria_ram">Memória RAM</label>
                                    <input id="memoria_ram" type="text" name="memoria_ram" value="<?php echo set_value('memoria_ram'); ?>" placeholder="Ex: 8GB, 16GB DDR4" maxlength="50" />
                                </div>
                                <div>
                                    <label for="armazenamento">Armazenamento</label>
                                    <input id="armazenamento" type="text" name="armazenamento" value="<?php echo set_value('armazenamento'); ?>" placeholder="Ex: 256GB SSD, 1TB HDD" maxlength="50" />
                                </div>
                                <div>
                                    <label for="tela">Tela</label>
                                    <input id="tela" type="text" name="tela" value="<?php echo set_value('tela'); ?>" placeholder="Ex: 15.6&quot;, 6.1&quot; Full HD" maxlength="50" />
                                </div>
                                <div>
                                    <label for="sistema_operacional">Sistema Operacional</label>
                                    <input id="sistema_operacional" type="text" name="sistema_operacional" value="<?php echo set_value('sistema_operacional'); ?>" placeholder="Ex: Windows 11, Android 13" maxlength="50" />
                                </div>
                                <div>
                                    <label for="cor">Cor</label>
                                    <input id="cor" type="text" name="cor" value="<?php echo set_value('cor'); ?>" placeholder="Ex: Preto, Prata, Azul" maxlength="50" />
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="control-group">
                        <label for="descricao_completa" class="control-label">Descrição Completa</label>
                        <div class="controls">
                            <textarea id="descricao_completa" name="descricao_completa" rows="4" style="width: 100%;"><?php echo set_value('descricao_completa'); ?></textarea>
                            <small style="color: #666;">Descrição detalhada adicional, observações, características especiais, etc. (opcional)</small>
                        </div>
                    </div>
                    
                    <!-- Campo descricao oculto (para compatibilidade) -->
                    <input type="hidden" id="descricao" name="descricao" value="" />
                    <div class="control-group">
                        <label class="control-label">Tipo de Movimento</label>
                        <div class="controls">
                            <label for="entrada" class="btn btn-default" style="margin-top: 5px;">Entrada
                                <input type="checkbox" id="entrada" name="entrada" class="badgebox" value="1" checked>
                                <span class="badge">&check;</span>
                            </label>
                            <label for="saida" class="btn btn-default" style="margin-top: 5px;">Saída
                                <input type="checkbox" id="saida" name="saida" class="badgebox" value="1" checked>
                                <span class="badge">&check;</span>
                            </label>
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="precoCompra" class="control-label">Preço de Compra<span class="required">*</span></label>
                        <div class="controls">
                            <input id="precoCompra" class="money" data-affixes-stay="true" data-thousands="" data-decimal="." type="text" name="precoCompra" value="<?php echo set_value('precoCompra'); ?>" />
                            <strong><span style="color: red" id="errorAlert"></span><strong>
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="Lucro" class="control-label">Lucro</label>
                        <div class="controls">
                            <select id="selectLucro" name="selectLucro" style="width: 10.5em;">
                              <option value="markup">Markup</option>
                              <option value="margemLucro">Margem de Lucro</option>
                            </select>
                            <input style="width: 4em;" id="Lucro" name="Lucro" type="text" placeholder="%" maxlength="3" size="2" />
                            <i class="icon-info-sign tip-left" title="Markup: Porcentagem aplicada ao valor de compra | Margem de Lucro: Porcentagem aplicada ao valor de venda"></i>
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="precoVenda" class="control-label">Preço de Venda<span class="required">*</span></label>
                        <div class="controls">
                            <input id="precoVenda" class="money" data-affixes-stay="true" data-thousands="" data-decimal="." type="text" name="precoVenda" value="<?php echo set_value('precoVenda'); ?>" />
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="unidade" class="control-label">Unidade<span class="required">*</span></label>
                        <div class="controls">
                            <select id="unidade" name="unidade"></select>
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="estoque" class="control-label">Estoque<span class="required">*</span></label>
                        <div class="controls">
                            <input id="estoque" type="text" name="estoque" value="<?php echo set_value('estoque'); ?>" />
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="estoqueMinimo" class="control-label">Estoque Mínimo</label>
                        <div class="controls">
                            <input id="estoqueMinimo" type="text" name="estoqueMinimo" value="<?php echo set_value('estoqueMinimo'); ?>" />
                        </div>
                    </div>
                    <div class="form-actions">
                        <div class="span12">
                            <div class="span6 offset3" style="display: flex;justify-content: center">
                                <button type="submit" class="button btn btn-mini btn-success" style="max-width: 160px"><span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">Adicionar</span></button>
                                <a href="<?php echo base_url() ?>index.php/produtos" id="" class="button btn btn-mini btn-warning"><span class="button__icon"><i class="bx bx-undo"></i></span><span class="button__text2">Voltar</span></a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url() ?>assets/js/jquery.validate.js"></script>
<script src="<?php echo base_url(); ?>assets/js/maskmoney.js"></script>
<script type="text/javascript">
    function calcLucro(precoCompra, Lucro) {
        var lucroTipo = $('#selectLucro').val();
        var precoVenda;
        
        if (lucroTipo === 'markup') {
            precoVenda = (precoCompra * (1 + Lucro / 100)).toFixed(2);
        } else if (lucroTipo === 'margemLucro') {
            precoVenda = (precoCompra / (1 - (Lucro / 100))).toFixed(2);
        }
        
        return precoVenda;
    }
    
    function atualizarPrecoVenda() {
        var precoCompra = Number($("#precoCompra").val());
        var lucro = Number($("#Lucro").val());
        
        if (precoCompra > 0 && lucro >= 0) {
            $('#precoVenda').val(calcLucro(precoCompra, lucro));
        }
    }
    
    $("#precoCompra, #Lucro, #selectLucro").on('input change', atualizarPrecoVenda);

    $("#precoCompra, #Lucro").on('input change', function() {
        if ($("#precoCompra").val() == '0.00' && $('#precoVenda').val() != '') {
            $('#errorAlert').text('Você não pode preencher valor de compra e depois apagar.').css("display", "inline").fadeOut(6000);
            $('#precoVenda').val('');
            $("#precoCompra").focus();
        } else if ($("#precoCompra").val() != '' && $("#Lucro").val() != '') {
            atualizarPrecoVenda();
        }
    });

    $("#Lucro").keyup(function() {
        this.value = this.value.replace(/[^0-9.]/g, '');
        if ($("#precoCompra").val() == null || $("#precoCompra").val() == '') {
            $('#errorAlert').text('Preencher valor da compra primeiro.').css("display", "inline").fadeOut(5000);
            $('#Lucro').val('');
            $('#precoVenda').val('');
            $("#precoCompra").focus();

        } else if (Number($("#Lucro").val()) >= 0) {
            $('#precoVenda').val(calcLucro(Number($("#precoCompra").val()), Number($("#Lucro").val())));
        } else {
            $('#errorAlert').text('Não é permitido número negativo.').css("display", "inline").fadeOut(5000);
            $('#Lucro').val('');
            $('#precoVenda').val('');
        }
    });

    $('#precoVenda').focusout(function () {
        if (Number($('#precoVenda').val()) < Number($("#precoCompra").val())) {
            $('#errorAlert').text('Preço de venda não pode ser menor que o preço de compra.').css("display", "inline").fadeOut(6000);
            $('#precoVenda').val('');
        }
    });

    // Preview de imagem
    $('#imagem').on('change', function(e) {
        var file = e.target.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#img-preview').attr('src', e.target.result);
                $('#preview-imagem').show();
            }
            reader.readAsDataURL(file);
        } else {
            $('#preview-imagem').hide();
        }
    });

    // Gerar descrição automaticamente com base no nome e especificações
    function gerarDescricao() {
        var nome = $('#nome').val();
        var marca = $('#marca').val();
        var modelo = $('#modelo').val();
        var processador = $('#processador').val();
        var memoriaRam = $('#memoria_ram').val();
        var armazenamento = $('#armazenamento').val();
        
        var descricao = nome;
        var especificacoes = [];
        
        if (marca) especificacoes.push(marca);
        if (modelo) especificacoes.push(modelo);
        if (processador) especificacoes.push(processador);
        if (memoriaRam) especificacoes.push(memoriaRam + ' RAM');
        if (armazenamento) especificacoes.push(armazenamento);
        
        if (especificacoes.length > 0) {
            descricao += ' ' + especificacoes.join(' ');
        }
        
        // Limitar a 80 caracteres para compatibilidade
        if (descricao.length > 80) {
            descricao = descricao.substring(0, 77) + '...';
        }
        
        $('#descricao').val(descricao);
    }
    
    // Atualizar descrição quando campos mudarem
    $('#nome, #marca, #modelo, #processador, #memoria_ram, #armazenamento').on('blur change', gerarDescricao);

    $(document).ready(function() {
        $(".money").maskMoney();
        $.getJSON('<?php echo base_url() ?>assets/json/tabela_medidas.json', function(data) {
            for (i in data.medidas) {
                $('#unidade').append(new Option(data.medidas[i].descricao, data.medidas[i].sigla));
            }
        });
        $('#formProduto').validate({
            ignore: ":hidden, [type='file']", // Ignorar campos ocultos e arquivos
            rules: {
                nome: {
                    required: true
                },
                unidade: {
                    required: true
                },
                precoCompra: {
                    required: true
                },
                precoVenda: {
                    required: true
                },
                estoque: {
                    required: true
                }
            },
            messages: {
                nome: {
                    required: 'Campo Requerido.'
                },
                unidade: {
                    required: 'Campo Requerido.'
                },
                precoCompra: {
                    required: 'Campo Requerido.'
                },
                precoVenda: {
                    required: 'Campo Requerido.'
                },
                estoque: {
                    required: 'Campo Requerido.'
                }
            },
            errorClass: "help-inline",
            errorElement: "span",
            highlight: function(element, errorClass, validClass) {
                $(element).parents('.control-group').addClass('error');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).parents('.control-group').removeClass('error');
                $(element).parents('.control-group').addClass('success');
            }
        });
    });
</script>
