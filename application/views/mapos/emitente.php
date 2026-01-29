<script src="<?php echo base_url() ?>assets/js/jquery.mask.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/funcoes.js"></script>
<script src="<?php echo base_url() ?>assets/js/sweetalert2.all.min.js"></script>

<style>
    /* Garante que o SweetAlert apareça acima do modal */
    /* O modal Bootstrap tem z-index: 99999, então precisamos maior */
    .swal2-container {
        z-index: 100000 !important;
    }
    
    .swal2-popup {
        z-index: 100001 !important;
    }
    
    /* Garante que o SweetAlert seja renderizado no body, não dentro do modal */
    body > .swal2-container {
        z-index: 100000 !important;
    }

    .modal-body {
        padding: 20px;
        overflow-y: inherit !important;
    }

    .form-horizontal .controls {
        margin-left: 20px;
    }

    .form-horizontal .control-label {
        padding-top: 9px;
        width: 160px;
    }

    h5 {
        padding-bottom: 15px;
        font-size: 1.5em;
        font-weight: 500;
    }

    .form-horizontal .control-group {
        border-top: 0 solid #ffffff;
        border-bottom: 0 solid #eeeeee;
        margin-bottom: 0;
    }

    .widget-content {
        padding: 0 16px 15px;
    }

    @media (max-width: 480px) {
        .modal-body {
            padding: 20px;
            overflow-x: hidden !important;
            grid-template-columns: 1fr !important;
        }

        form {
            display: block !important;
        }

        .form-horizontal .control-label {
            margin-bottom: -6px;
        }

        .btn-xs {
            position: initial !important;
        }
    }
</style>

<?php if (!isset($dados) || $dados == null) { ?>
    <div class="row-fluid" style="margin-top:0">
        <div class="span12">
            <div class="widget-box">
                <div class="widget-title">
                    <h5>Dados do Emitente</h5>
                </div>
                <div class="widget-content ">
                    <div class="alert alert-danger">Nenhum dado foi cadastrado até o momento. Essas informações estarão disponíveis na tela de impressão de OS.</div>
                    <a href="#modalCadastrar" data-toggle="modal" role="button" class="button btn btn-success" style="max-width: 150px"> <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">Cadastrar Dados</span></a>
                </div>
            </div>
        </div>
    </div>

    <div id="modalCadastrar" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <form action="<?= site_url('mapos/cadastrarEmitente'); ?>" id="formCadastrar" enctype="multipart/form-data" method="post" class="form-horizontal">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h5 id="myModalLabel" style="text-align-last:center">Cadastrar Dados do Emitente</h5>
            </div>
            <div class="modal-body" style="display: grid;grid-template-columns: 1fr 1fr">
                <div class="control-group">
                    <label for="nome" class="control-label"><span class="required"></span></label>
                    <div class="controls">
                        <input id="nomeEmitente" placeholder="Razão Social/Nome*" type="text" name="nome" value="" />
                    </div>
                </div>
                <div class="control-group">
                    <label for="cnpj" class="control-label"><span class="required">CPF/CNPJ*</span></label>
                    <div class="controls">
                        <input class="documentoEmitente" placeholder="CPF ou CNPJ*" id="documento" type="text" name="cnpj" value="" title="Digite CPF (11 dígitos) ou CNPJ (14 dígitos). Para ocultar digite 00.000.000/0000-00" />
                        <button style="top:34px;right:40px;position:absolute" id="buscar_info_documento" class="btn btn-xs" type="button"><i class="fas fa-search"></i></button>
                        <input type="hidden" name="confirmar_documento_invalido" id="confirmar_documento_invalido" value="0" />
                    </div>
                </div>
                <div class="control-group">
                    <label for="descricao" class="control-label"></label>
                    <div class="controls">
                        <input type="text" placeholder="IE" name="ie" value="" />
                    </div>
                </div>
                <div class="control-group">
                    <label for="cep" class="control-label"><span class="required"></span></label>
                    <div class="controls">
                        <input id="cep" type="text" placeholder="CEP*" name="cep" value="" />
                    </div>
                </div>
                <div class="control-group">
                    <label for="descricao" class="control-label"><span class="required"></span></label>
                    <div class="controls">
                        <input id="rua" type="text" placeholder="Logradouro*" name="logradouro" value="" />
                    </div>
                </div>
                <div class="control-group">
                    <label for="descricao" class="control-label"><span class="required"></span></label>
                    <div class="controls">
                        <input type="text" id="numero" placeholder="Número*" name="numero" value="" />
                    </div>
                </div>
                <div class="control-group">
                    <label for="descricao" class="control-label"><span class="required"></span></label>
                    <div class="controls">
                        <input id="bairro" type="text" placeholder="Bairro*" name="bairro" value="" />
                    </div>
                </div>
                <div class="control-group">
                    <label for="descricao" class="control-label"><span class="required"></span></label>
                    <div class="controls">
                        <input id="cidade" type="text" placeholder="Cidade*" name="cidade" value="" />
                    </div>
                </div>
                <div class="control-group">
                    <label for="descricao" class="control-label"><span class="required"></span></label>
                    <div class="controls">
                        <input id="estado" type="text" placeholder="UF*" name="uf" value="" />
                    </div>
                </div>
                <div class="control-group">
                    <label for="descricao" class="control-label"><span class="required"></span></label>
                    <div class="controls">
                        <input id="telefone" type="text" placeholder="Telefone*" name="telefone" value="" />
                    </div>
                </div>
                <div class="control-group">
                    <label for="descricao" class="control-label"><span class="required"></span></label>
                    <div class="controls">
                        <input id="email" type="text" placeholder="E-mail*" name="email" value="" />
                    </div>
                </div>
                <div class="control-group">
                    <label for="logo" class="control-label"><span class="required">Logotipo*</span></label>
                    <div class="controls">
                        <input type="file" name="userfile" value="" />
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="display:flex;justify-content: center">
                <button class="button btn btn-warning" data-dismiss="modal" aria-hidden="true" id="btnCancelExcluir"><span class="button__icon"><i class="bx bx-x"></i></span><span class="button__text2">Cancelar</span></button>
                <button class="button btn btn-success"><span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">Cadastrar</span></button>
            </div>
        </form>
    </div>

    <?php } else { ?>
    <div class="row-fluid" style="margin-top:0">
        <div class="span12">
            <div class="widget-box">
                <div class="widget-title" style="margin: -20px 0 0">
                    <span class="icon">
                        <i class="fas fa-align-justify"></i>
                    </span>
                    <h5>Dados do Emitente</h5>
                </div>
                <div class="widget-content ">
                    <div class="alert alert-info">Os dados abaixo serão utilizados no cabeçalho das telas de impressão.</div>
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td style="width: 25%"><img src="<?= $dados->url_logo; ?>"></td>
                                <td>
                                    <span style="font-size: 20px; "><b><?= $dados->nome; ?></b></span></br>
                                    <i class="fas fa-fingerprint" style="margin:5px 1px"></i> <?php 
                                        $documento = preg_replace('/[^0-9]/', '', $dados->cnpj);
                                        if (strlen($documento) == 11) {
                                            echo 'CPF: ' . $dados->cnpj;
                                        } else {
                                            echo 'CNPJ: ' . $dados->cnpj;
                                        }
                                    ?> <?php if (!empty($dados->ie)) {
                                        echo ' - IE:' . $dados->ie;
                                    } ?></br>
                                    <i class="fas fa-map-marker-alt" style="margin:4px 3px"></i> <?= $dados->rua . ', ' . $dados->numero . ', ' . $dados->bairro . ' - ' . $dados->cep . ', ' . $dados->cidade . '/' . $dados->uf; ?></br>
                                    <i class="fas fa-phone" style="margin:5px 1px"></i> <?= $dados->telefone; ?></br>
                                    <i class="fas fa-envelope" style="margin:5px 1px"></i> <?= $dados->email; ?></br>
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div style="display:flex">
                        <a href="#modalAlterar" data-toggle="modal" role="button" class="button btn btn-success"><span class="button__icon"><i class='bx bx-edit'></i></span><span class="button__text2">Atualizar Dados</span></a>
                        <a href="#modalLogo" data-toggle="modal" role="button" class="button btn btn-inverse"><span class="button__icon"><i class='bx bx-upload'></i></span> <span class="button__text2">Alterar Logo</span></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="modalAlterar" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <form action="<?= site_url('mapos/editarEmitente'); ?>" id="formAlterar" enctype="multipart/form-data" method="post" class="form-horizontal">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h3 id="">Editar Dados do Emitente</h3>
            </div>
            <div class="modal-body" style="display: grid;grid-template-columns: 1fr 1fr">
                <div class="control-group">
                    <label for="nome" class="control-label"><span class="required"></span></label>
                    <div class="controls">
                        <input id="nomeEmitente" type="text" name="nome" value="<?= $dados->nome; ?>" placeholder="Razão Social/Nome*" />
                        <input id="nome" type="hidden" name="id" value="<?= $dados->id; ?>" />
                    </div>
                </div>
                <div class="control-group">
                    <label for="cnpj" class="control-label"><span class="required">CPF/CNPJ*</span></label>
                    <div class="controls">
                        <input class="documentoEmitente" type="text" id="documento" name="cnpj" value="<?= $dados->cnpj; ?>" placeholder="CPF ou CNPJ*" title="Digite CPF (11 dígitos) ou CNPJ (14 dígitos). Para ocultar digite 00.000.000/0000-00" />
                        <button style="top:34px;right:40px;position:absolute" id="buscar_info_documento" class="btn btn-xs" type="button"><i class="fas fa-search"></i></button>
                        <input type="hidden" name="confirmar_documento_invalido" id="confirmar_documento_invalido" value="0" />
                    </div>
                </div>
                <div class="control-group">
                    <label for="descricao" class="control-label"></label>
                    <div class="controls">
                        <input type="text" name="ie" value="<?= $dados->ie; ?>" placeholder="IE" />
                    </div>
                </div>
                <div class="control-group">
                    <label for="cep" class="control-label"><span class="required"></span></label>
                    <div class="controls">
                        <input id="cep" type="text" name="cep" value="<?= $dados->cep; ?>" placeholder="CEP*" />
                    </div>
                </div>
                <div class="control-group">
                    <label for="descricao" class="control-label"><span class="required"></span></label>
                    <div class="controls">
                        <input type="text" id="rua" name="logradouro" value="<?= $dados->rua; ?>"
                            placeholder="Logradouro*" />
                    </div>
                </div>
                <div class="control-group">
                    <label for="descricao" class="control-label"><span class="required"></span></label>
                    <div class="controls">
                        <input type="text" id="numero" name="numero" value="<?= $dados->numero; ?>" placeholder="Número*" />
                    </div>
                </div>
                <div class="control-group">
                    <label for="descricao" class="control-label"><span class="required"></span></label>
                    <div class="controls">
                        <input type="text" id="bairro" name="bairro" value="<?= $dados->bairro; ?>" placeholder="Bairro*" />
                    </div>
                </div>
                <div class="control-group">
                    <label for="descricao" class="control-label"><span class="required"></span></label>
                    <div class="controls">
                        <input type="text" id="cidade" name="cidade" value="<?= $dados->cidade; ?>" placeholder="Cidade*" />
                    </div>
                </div>
                <div class="control-group">
                    <label for="descricao" class="control-label"><span class="required"></span></label>
                    <div class="controls">
                        <input type="text" id="estado" name="uf" value="<?= $dados->uf; ?>" placeholder="UF*" />
                    </div>
                </div>
                <div class="control-group">
                    <label for="descricao" class="control-label"><span class="required"></span></label>
                    <div class="controls">
                        <input type="text" id="telefone" name="telefone" value="<?= $dados->telefone; ?>"
                            placeholder="Telefone*" />
                    </div>
                </div>
                <div class="control-group">
                    <label for="descricao" class="control-label"><span class="required"></span></label>
                    <div class="controls">
                        <input id="email" type="text" name="email" value="<?= $dados->email; ?>" placeholder="E-mail*" />
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="display:flex;justify-content: center">
                <button class="button btn btn-mini btn-danger" data-dismiss="modal" aria-hidden="true" id="btnCancelExcluir"><span class="button__icon"><i class='bx bx-x'></i></span> <span class="button__text2">Cancelar</span></button>
                <button class="button btn btn-primary"><span class="button__icon"><i class="bx bx-sync"></i></span><span class="button__text2">Atualizar</span></button>
            </div>
        </form>
    </div>

    <div id="modalLogo" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <form action="<?= site_url('mapos/editarLogo'); ?>" id="formLogo" enctype="multipart/form-data" method="post" class="form-horizontal">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h3 id="">MapOS - Atualizar Logotipo</h3>
            </div>
            <div class="modal-body">
                <div class="span12 alert alert-info">Selecione uma nova imagem da logotipo. Tamanho indicado (130 X 130).</div>
                <div class="control-group">
                    <label for="logo" class="control-label"><span class="required">Logotipo*</span></label>
                    <div class="controls">
                        <input type="file" name="userfile" value="" />
                        <input id="nome" type="hidden" name="id" value="<?= $dados->id; ?>" />
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="display:flex;justify-content: center">
                <button class="button btn btn-mini btn-danger" data-dismiss="modal" aria-hidden="true" id="btnCancelExcluir"><span class="button__icon"><i class='bx bx-x'></i></span> <span class="button__text2">Cancelar</span></button>
                <button class="button btn btn-primary"><span class="button__icon"><i class="bx bx-sync"></i></span><span class="button__text2">Atualizar</span></button>
            </div>
        </form>
    </div>
<?php } ?>

<script type="text/javascript" src="<?= base_url() ?>assets/js/jquery.validate.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $("#formLogo").validate({
            rules: {
                userfile: {
                    required: true
                }
            },
            messages: {
                userfile: {
                    required: 'Campo Requerido.'
                }
            },

            errorClass: "help-inline",
            errorElement: "span",
            highlight: function (element, errorClass, validClass) {
                $(element).parents('.control-group').addClass('error');
                $(element).parents('.control-group').removeClass('success');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).parents('.control-group').removeClass('error');
                $(element).parents('.control-group').addClass('success');
            }
        });

        $("#formCadastrar").validate({
            rules: {
                userfile: {
                    required: true
                },
                nome: {
                    required: true
                },
                cnpj: {
                    required: true
                },
                logradouro: {
                    required: true
                },
                numero: {
                    required: true
                },
                bairro: {
                    required: true
                },
                cidade: {
                    required: true
                },
                uf: {
                    required: true
                },
                telefone: {
                    required: true
                },
                email: {
                    required: true
                }
            },
            messages: {
                userfile: {
                    required: 'Campo Requerido.'
                },
                nome: {
                    required: 'Campo Requerido.'
                },
                cnpj: {
                    required: 'Campo Requerido.'
                },
                logradouro: {
                    required: 'Campo Requerido.'
                },
                numero: {
                    required: 'Campo Requerido.'
                },
                bairro: {
                    required: 'Campo Requerido.'
                },
                cidade: {
                    required: 'Campo Requerido.'
                },
                uf: {
                    required: 'Campo Requerido.'
                },
                telefone: {
                    required: 'Campo Requerido.'
                },
                email: {
                    required: 'Campo Requerido.'
                }
            },

            errorClass: "help-inline",
            errorElement: "span",
            highlight: function (element, errorClass, validClass) {
                $(element).parents('.control-group').addClass('error');
                $(element).parents('.control-group').removeClass('success');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).parents('.control-group').removeClass('error');
                $(element).parents('.control-group').addClass('success');
            }
        });

        $("#formAlterar").validate({
            rules: {
                userfile: {
                    required: true
                },
                nome: {
                    required: true
                },
                cnpj: {
                    required: true
                },
                logradouro: {
                    required: true
                },
                numero: {
                    required: true
                },
                bairro: {
                    required: true
                },
                cidade: {
                    required: true
                },
                uf: {
                    required: true
                },
                telefone: {
                    required: true
                },
                email: {
                    required: true
                }
            },
            messages: {
                userfile: {
                    required: 'Campo Requerido.'
                },
                nome: {
                    required: 'Campo Requerido.'
                },
                cnpj: {
                    required: 'Campo Requerido.'
                },
                logradouro: {
                    required: 'Campo Requerido.'
                },
                numero: {
                    required: 'Campo Requerido.'
                },
                bairro: {
                    required: 'Campo Requerido.'
                },
                cidade: {
                    required: 'Campo Requerido.'
                },
                uf: {
                    required: 'Campo Requerido.'
                },
                telefone: {
                    required: 'Campo Requerido.'
                },
                email: {
                    required: 'Campo Requerido.'
                }
            },

            errorClass: "help-inline",
            errorElement: "span",
            highlight: function (element, errorClass, validClass) {
                $(element).parents('.control-group').addClass('error');
                $(element).parents('.control-group').removeClass('success');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).parents('.control-group').removeClass('error');
                $(element).parents('.control-group').addClass('success');
            }
        });

        // Máscara dinâmica para CPF/CNPJ no emitente
        $('#documento').on('input', function () {
            let v = $(this).val().replace(/[^a-zA-Z0-9]/g, '').toUpperCase();
            let result = '';
            // CPF: 11 dígitos numéricos
            if (/^\d{0,11}$/.test(v)) {
                for (let i = 0; i < v.length && i < 11; i++) {
                    if (i === 3 || i === 6) result += '.';
                    if (i === 9) result += '-';
                    result += v[i];
                }
                $(this).val(result);
            }
            // CNPJ tradicional: 14 dígitos numéricos
            else if (/^\d{12,14}$/.test(v) && !/[A-Z]/.test(v)) {
                for (let i = 0; i < v.length && i < 14; i++) {
                    if (i === 2 || i === 5) result += '.';
                    if (i === 8) result += '/';
                    if (i === 12) result += '-';
                    result += v[i];
                }
                $(this).val(result);
            }
            // CNPJ alfanumérico: 14 caracteres (letras e números)
            else {
                for (let i = 0; i < v.length && i < 14; i++) {
                    if (i === 2 || i === 5) result += '.';
                    if (i === 8) result += '/';
                    if (i === 12) result += '-';
                    result += v[i];
                }
                $(this).val(result);
            }
        });

        // Função para validar CPF
        function validarCPF(cpf) {
            cpf = cpf.replace(/[^\d]/g, '');
            if (cpf.length !== 11) return false;
            if (/^(\d)\1{10}$/.test(cpf)) return false;
            
            let soma = 0;
            for (let i = 0; i < 9; i++) {
                soma += parseInt(cpf.charAt(i)) * (10 - i);
            }
            let resto = (soma * 10) % 11;
            if (resto === 10 || resto === 11) resto = 0;
            if (resto !== parseInt(cpf.charAt(9))) return false;
            
            soma = 0;
            for (let i = 0; i < 10; i++) {
                soma += parseInt(cpf.charAt(i)) * (11 - i);
            }
            resto = (soma * 10) % 11;
            if (resto === 10 || resto === 11) resto = 0;
            if (resto !== parseInt(cpf.charAt(10))) return false;
            
            return true;
        }

        // Função para validar CNPJ (já existe no funcoes.js, mas vamos usar aqui também)
        function validarCNPJEmitente(cnpj) {
            cnpj = cnpj.replace(/[^\w]/g, '').toUpperCase();
            
            // CNPJ numérico tradicional
            if (/^\d{14}$/.test(cnpj)) {
                if (/^(\d)\1{13}$/.test(cnpj)) return false;
                
                let tamanho = cnpj.length - 2;
                let numeros = cnpj.substring(0, tamanho);
                let digitos = cnpj.substring(tamanho);
                
                let soma = 0;
                let pos = tamanho - 7;
                for (let i = tamanho; i >= 1; i--) {
                    soma += parseInt(numeros.charAt(tamanho - i)) * pos--;
                    if (pos < 2) pos = 9;
                }
                
                let resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
                if (resultado != parseInt(digitos.charAt(0))) return false;
                
                tamanho = tamanho + 1;
                numeros = cnpj.substring(0, tamanho);
                soma = 0;
                pos = tamanho - 7;
                for (let i = tamanho; i >= 1; i--) {
                    soma += parseInt(numeros.charAt(tamanho - i)) * pos--;
                    if (pos < 2) pos = 9;
                }
                resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
                
                return resultado == parseInt(digitos.charAt(1));
            }
            
            // CNPJ alfanumérico - aceita formato válido
            if (/^[A-Z0-9]{14}$/.test(cnpj)) {
                return true;
            }
            
            return false;
        }

        // Função para verificar se é CPF ou CNPJ
        function verificarTipoDocumento(documento) {
            let doc = documento.replace(/[^\d]/g, '');
            if (doc.length === 11) return 'CPF';
            if (doc.length === 14) return 'CNPJ';
            return null;
        }

        // Função helper para garantir que SweetAlert apareça acima do modal
        function configurarSweetAlertAcimaModal() {
            setTimeout(() => {
                const swalContainer = document.querySelector('.swal2-container');
                const swalPopup = document.querySelector('.swal2-popup');
                
                if (swalContainer) {
                    swalContainer.style.zIndex = '100000';
                    // Move para o body se estiver dentro do modal
                    if (swalContainer.closest('.modal')) {
                        document.body.appendChild(swalContainer);
                    }
                }
                if (swalPopup) {
                    swalPopup.style.zIndex = '100001';
                }
            }, 10);
        }

        // Função para processar busca automática quando documento é válido
        function processarBuscaAutomatica() {
            let documento = $('#documento').val().trim();
            let docLimpo = documento.replace(/[^\d]/g, '');
            let tipo = verificarTipoDocumento(documento);
            
            if (!tipo) {
                return;
            }

            // Validação
            let valido = false;
            if (tipo === 'CPF') {
                valido = validarCPF(documento);
            } else {
                valido = validarCNPJEmitente(documento);
            }

            // Se válido, busca automaticamente
            if (valido) {
                if (tipo === 'CNPJ') {
                    buscarCNPJ(docLimpo);
                } else {
                    // Para CPF, tenta buscar via API alternativa ou informa
                    buscarCPF(docLimpo);
                }
            } else {
                // Se inválido, mostra aviso mas não bloqueia
                $('#confirmar_documento_invalido').val('0');
            }
        }

        // Buscar informações do documento (botão manual)
        $('#buscar_info_documento').on('click', function () {
            let documento = $('#documento').val().trim();
            let docLimpo = documento.replace(/[^\d]/g, '');
            let tipo = verificarTipoDocumento(documento);
            
            if (!tipo) {
                Swal.fire({
                    icon: "warning",
                    title: "Atenção",
                    text: "Por favor, digite um CPF (11 dígitos) ou CNPJ (14 dígitos) válido."
                });
                return;
            }

            // Validação
            let valido = false;
            if (tipo === 'CPF') {
                valido = validarCPF(documento);
            } else {
                valido = validarCNPJEmitente(documento);
            }

            if (!valido) {
                Swal.fire({
                    icon: "warning",
                    title: "Documento Inválido",
                    text: "O " + tipo + " informado não é válido. Deseja continuar mesmo assim?",
                    showCancelButton: true,
                    confirmButtonText: "Sim, continuar",
                    cancelButtonText: "Cancelar"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#confirmar_documento_invalido').val('1');
                        // Se for CNPJ, tenta buscar mesmo assim
                        if (tipo === 'CNPJ') {
                            buscarCNPJ(docLimpo);
                        } else {
                            buscarCPF(docLimpo);
                        }
                    }
                });
                return;
            }

            // Se válido, busca automaticamente
            if (tipo === 'CPF') {
                buscarCPF(docLimpo);
            } else {
                buscarCNPJ(docLimpo);
            }
        });

        // Busca automática quando documento é válido e campo perde foco
        $('#documento').on('blur', function() {
            let documento = $(this).val().trim();
            let docLimpo = documento.replace(/[^\d]/g, '');
            
            // Só busca se o documento estiver completo
            if (docLimpo.length === 11 || docLimpo.length === 14) {
                processarBuscaAutomatica();
            }
        });

        // Busca automática quando documento é digitado e está completo e válido
        let timeoutBusca;
        $('#documento').on('input', function() {
            clearTimeout(timeoutBusca);
            let documento = $(this).val().trim();
            let docLimpo = documento.replace(/[^\d]/g, '');
            
            // Aguarda 1 segundo após parar de digitar e verifica se está completo e válido
            if (docLimpo.length === 11 || docLimpo.length === 14) {
                timeoutBusca = setTimeout(function() {
                    let tipo = verificarTipoDocumento(documento);
                    if (tipo) {
                        let valido = false;
                        if (tipo === 'CPF') {
                            valido = validarCPF(documento);
                        } else {
                            valido = validarCNPJEmitente(documento);
                        }
                        
                        // Se válido e campos ainda não foram preenchidos, busca automaticamente
                        if (valido && (!$("#nomeEmitente").val() || $("#nomeEmitente").val().trim() === "")) {
                            processarBuscaAutomatica();
                        }
                    }
                }, 1000); // Aguarda 1 segundo após parar de digitar
            }
        });

        // Função para buscar CPF
        function buscarCPF(cpf) {
            // Valida o CPF novamente
            let cpfFormatado = $('#documento').val().trim();
            if (!validarCPF(cpfFormatado)) {
                return;
            }

            // IMPORTANTE: Não há API pública confiável para consultar dados completos de CPF
            // por questões de privacidade e LGPD. No entanto, vamos tentar algumas alternativas:
            
            // 1. Não limpa campos existentes (importante!)
            // 2. Foca no campo nome para facilitar preenchimento
            // 3. Mostra mensagem informativa
            
            // Garante que os campos não sejam limpos
            // Se os campos já tiverem valores, mantém
            // Se estiverem vazios, apenas foca no nome
            
            // Foca no campo de nome para facilitar preenchimento manual
            setTimeout(function() {
                if (!$("#nomeEmitente").val() || $("#nomeEmitente").val().trim() === "") {
                    $("#nomeEmitente").focus();
                }
            }, 200);
            
            // Mostra mensagem de sucesso (não bloqueante, permite continuar preenchendo)
            // Posiciona acima do modal usando z-index muito alto
            Swal.fire({
                icon: "success",
                title: "CPF Válido!",
                html: "O CPF informado é válido.<br><br><small>Por questões de privacidade e segurança (LGPD), a consulta automática de dados de CPF não está disponível. Por favor, preencha os dados manualmente.</small>",
                confirmButtonText: "Entendi",
                allowOutsideClick: true,
                allowEscapeKey: true,
                timer: 5000,
                timerProgressBar: true,
                target: 'body', // Renderiza no body, não dentro do modal
                didOpen: () => {
                    configurarSweetAlertAcimaModal();
                }
            }).then(() => {
                // Após fechar a mensagem, foca no campo nome se estiver vazio
                if (!$("#nomeEmitente").val() || $("#nomeEmitente").val().trim() === "") {
                    $("#nomeEmitente").focus();
                }
            });
        }

        // Função para buscar CNPJ
        function buscarCNPJ(cnpj) {
            // Se for CNPJ alfanumérico, não busca
            if (/^[A-Z0-9]{14}$/.test(cnpj.replace(/[^\w]/g, '').toUpperCase()) && /[A-Z]/.test(cnpj)) {
                Swal.fire({
                    icon: "info",
                    title: "Atenção",
                    text: "A consulta automática ainda não está disponível para o novo formato de CNPJ alfanumérico. Preencha os dados manualmente."
                });
                return;
            }

            // Preenche campos com "..." enquanto consulta
            $("#nomeEmitente").val("...");
            $("#email").val("...");
            $("#cep").val("...");
            $("#rua").val("...");
            $("#numero").val("...");
            $("#bairro").val("...");
            $("#cidade").val("...");
            $("#estado").val("...");
            $("#telefone").val("...");

            // Consulta webservice
            $.ajax({
                url: "https://www.receitaws.com.br/v1/cnpj/" + cnpj,
                dataType: 'jsonp',
                crossDomain: true,
                contentType: "text/javascript",
                timeout: 10000, // Timeout de 10 segundos
                success: function (dados) {
                    if (dados.status == "OK") {
                        // Preenche automaticamente todos os campos
                        $("#nomeEmitente").val(capital_letter(dados.nome));
                        $("#cep").val(dados.cep ? dados.cep.replace(/\./g, '') : "");
                        $("#email").val(dados.email ? dados.email.toLocaleLowerCase() : "");
                        $("#telefone").val(dados.telefone ? dados.telefone.split("/")[0].replace(/\ /g, '') : "");
                        $("#rua").val(capital_letter(dados.logradouro || ""));
                        $("#numero").val(dados.numero || "");
                        $("#bairro").val(capital_letter(dados.bairro || ""));
                        $("#cidade").val(capital_letter(dados.municipio || ""));
                        $("#estado").val(dados.uf || "");
                        
                        // Se tiver IE, preenche também
                        if (dados.inscricao_estadual) {
                            $("input[name='ie']").val(dados.inscricao_estadual);
                        }
                        
                        // Foca no campo nome para permitir edição se necessário
                        $("#nomeEmitente").focus();
                        
                        // Mostra mensagem de sucesso (acima do modal)
                        Swal.fire({
                            icon: "success",
                            title: "Dados encontrados!",
                            text: "Os dados foram preenchidos automaticamente. Verifique e ajuste se necessário.",
                            timer: 2000,
                            showConfirmButton: false,
                            target: 'body', // Renderiza no body, não dentro do modal
                            didOpen: () => {
                                configurarSweetAlertAcimaModal();
                            }
                        });
                    } else {
                        // Limpa campos
                        $("#nomeEmitente").val("");
                        $("#cep").val("");
                        $("#email").val("");
                        $("#numero").val("");
                        $("#telefone").val("");
                        Swal.fire({
                            icon: "warning",
                            title: "Atenção",
                            text: "CNPJ não encontrado na base de dados. Preencha os dados manualmente."
                        });
                    }
                },
                error: function (xhr, status, error) {
                    // Limpa campos
                    $("#nomeEmitente").val("");
                    $("#cep").val("");
                    $("#email").val("");
                    $("#numero").val("");
                    $("#telefone").val("");
                    
                    if (status === "timeout") {
                        Swal.fire({
                            icon: "error",
                            title: "Timeout",
                            text: "A consulta demorou muito. Tente novamente ou preencha os dados manualmente."
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Erro",
                            text: "Erro ao consultar CNPJ. Tente novamente ou preencha os dados manualmente."
                        });
                    }
                }
            });
        }

        // Validação antes de submeter formulário
        $('#formCadastrar, #formAlterar').on('submit', function(e) {
            let documento = $('#documento').val().trim();
            let docLimpo = documento.replace(/[^\d]/g, '');
            let tipo = verificarTipoDocumento(documento);
            
            if (!tipo) {
                e.preventDefault();
                Swal.fire({
                    icon: "error",
                    title: "Erro",
                    text: "Por favor, digite um CPF (11 dígitos) ou CNPJ (14 dígitos) válido."
                });
                return false;
            }

            let valido = false;
            if (tipo === 'CPF') {
                valido = validarCPF(documento);
            } else {
                valido = validarCNPJEmitente(documento);
            }

            if (!valido && $('#confirmar_documento_invalido').val() !== '1') {
                e.preventDefault();
                Swal.fire({
                    icon: "warning",
                    title: "Documento Inválido",
                    text: "O " + tipo + " informado não é válido. Deseja continuar mesmo assim?",
                    showCancelButton: true,
                    confirmButtonText: "Sim, continuar",
                    cancelButtonText: "Cancelar"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#confirmar_documento_invalido').val('1');
                        $('#formCadastrar, #formAlterar').off('submit').submit();
                    }
                });
                return false;
            }
        });

        function capital_letter(str) {
            if (typeof str === 'undefined') { return; }
            str = str.toLocaleLowerCase().split(" ");
            for (var i = 0, x = str.length; i < x; i++) {
                str[i] = str[i][0].toUpperCase() + str[i].substr(1);
            }
            return str.join(" ");
        }
    });
</script>
