<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDV - Ponto de Venda</title>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap5.3.2.min.css" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/font-awesome/css/font-awesome.css" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/custom.css" />
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            overflow: hidden;
        }
        .pdv-container {
            display: flex;
            height: 100vh;
            flex-direction: column;
        }
        .pdv-header {
            background: #2c3e50;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .pdv-header h2 {
            margin: 0;
            font-size: 24px;
        }
        .pdv-header-info {
            display: flex;
            gap: 20px;
            align-items: center;
        }
        .pdv-main {
            display: flex;
            flex: 1;
            overflow: hidden;
        }
        .pdv-produtos {
            flex: 1;
            background: white;
            padding: 20px;
            overflow-y: auto;
            border-right: 2px solid #e0e0e0;
        }
        .pdv-busca {
            margin-bottom: 20px;
        }
        .pdv-busca input {
            width: 100%;
            padding: 15px;
            font-size: 18px;
            border: 2px solid #3498db;
            border-radius: 5px;
        }
        .pdv-busca input:focus {
            outline: none;
            border-color: #2980b9;
        }
        .pdv-grid-produtos {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
        }
        .pdv-produto-card {
            background: white;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        .pdv-produto-card:hover {
            border-color: #3498db;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .pdv-produto-card img {
            width: 100%;
            height: 120px;
            object-fit: cover;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .pdv-produto-card .nome {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 5px;
            color: #333;
        }
        .pdv-produto-card .preco {
            color: #27ae60;
            font-size: 16px;
            font-weight: bold;
        }
        .pdv-produto-card .estoque {
            font-size: 12px;
            color: #999;
            margin-top: 5px;
        }
        .pdv-carrinho {
            width: 400px;
            background: white;
            display: flex;
            flex-direction: column;
            border-left: 2px solid #e0e0e0;
        }
        .pdv-carrinho-header {
            background: #34495e;
            color: white;
            padding: 15px;
            font-size: 18px;
            font-weight: bold;
        }
        .pdv-carrinho-itens {
            flex: 1;
            overflow-y: auto;
            padding: 15px;
        }
        .pdv-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #e0e0e0;
            margin-bottom: 10px;
        }
        .pdv-item-info {
            flex: 1;
        }
        .pdv-item-nome {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .pdv-item-detalhes {
            font-size: 12px;
            color: #666;
        }
        .pdv-item-valor {
            font-weight: bold;
            color: #27ae60;
            font-size: 16px;
        }
        .pdv-item-acoes {
            display: flex;
            gap: 5px;
        }
        .pdv-item-acoes button {
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 12px;
        }
        .pdv-resumo {
            padding: 20px;
            background: #f8f9fa;
            border-top: 2px solid #e0e0e0;
        }
        .pdv-resumo-linha {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 16px;
        }
        .pdv-resumo-total {
            font-size: 24px;
            font-weight: bold;
            color: #27ae60;
            border-top: 2px solid #27ae60;
            padding-top: 10px;
            margin-top: 10px;
        }
        .pdv-botoes {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 15px;
        }
        .pdv-btn {
            padding: 15px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
        }
        .pdv-btn-primary {
            background: #3498db;
            color: white;
        }
        .pdv-btn-primary:hover {
            background: #2980b9;
        }
        .pdv-btn-danger {
            background: #e74c3c;
            color: white;
        }
        .pdv-btn-danger:hover {
            background: #c0392b;
        }
        .pdv-btn-success {
            background: #27ae60;
            color: white;
        }
        .pdv-btn-success:hover {
            background: #229954;
        }
        .pdv-btn-warning {
            background: #f39c12;
            color: white;
        }
        .pdv-btn-warning:hover {
            background: #d68910;
        }
        .pdv-btn-full {
            grid-column: 1 / -1;
        }
        .badge-estoque {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }
        .badge-estoque-baixo {
            background: #e74c3c;
            color: white;
        }
        .badge-estoque-ok {
            background: #27ae60;
            color: white;
        }
        .badge-estoque-zero {
            background: #7f8c8d;
            color: white;
        }
        .modal-pagamento {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        .modal-pagamento.active {
            display: flex;
        }
        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 10px;
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }
        .modal-header {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #2c3e50;
        }
        .formas-pagamento-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        .forma-pagamento-btn {
            padding: 20px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            background: white;
            cursor: pointer;
            text-align: center;
            transition: all 0.3s;
            font-size: 16px;
            font-weight: bold;
        }
        .forma-pagamento-btn:hover {
            border-color: #3498db;
            background: #ecf0f1;
        }
        .forma-pagamento-btn.active {
            border-color: #27ae60;
            background: #d5f4e6;
        }
        .input-valor {
            width: 100%;
            padding: 15px;
            font-size: 24px;
            text-align: center;
            border: 2px solid #3498db;
            border-radius: 5px;
            margin: 10px 0;
        }
        .teclado-numerico {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-top: 15px;
        }
        .tecla {
            padding: 20px;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            background: white;
            cursor: pointer;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            transition: all 0.2s;
        }
        .tecla:hover {
            background: #ecf0f1;
            border-color: #3498db;
        }
        .tecla.zero {
            grid-column: 1 / 3;
        }
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .alert-success {
            background: #d5f4e6;
            color: #27ae60;
            border: 1px solid #27ae60;
        }
        .alert-danger {
            background: #fadbd8;
            color: #e74c3c;
            border: 1px solid #e74c3c;
        }
        .alert-warning {
            background: #fef5e7;
            color: #f39c12;
            border: 1px solid #f39c12;
        }
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #999;
        }
        .empty-state i {
            font-size: 64px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="pdv-container">
        <!-- Header -->
        <div class="pdv-header">
            <div>
                <h2><i class="fas fa-cash-register"></i> PDV - Ponto de Venda</h2>
            </div>
            <div class="pdv-header-info">
                <span><i class="fas fa-user"></i> <?php echo $this->session->userdata('nome_admin'); ?></span>
                <span><i class="fas fa-cash-register"></i> Caixa: <?php echo $caixa_aberto->caixa_nome; ?></span>
                <span><i class="fas fa-clock"></i> <?php echo date('d/m/Y H:i'); ?></span>
                <a href="<?php echo base_url(); ?>index.php/vendas/fecharCaixa" class="btn btn-warning btn-sm" style="color: white;">
                    <i class="fas fa-door-open"></i> Fechar Caixa
                </a>
                <a href="<?php echo base_url(); ?>index.php/mapos" class="btn btn-danger btn-sm" style="color: white;">
                    <i class="fas fa-times"></i> Sair
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="pdv-main">
            <!-- Produtos -->
            <div class="pdv-produtos">
                <div class="pdv-busca">
                    <input type="text" 
                           id="buscaProduto" 
                           placeholder="🔍 Buscar produto ou código de barras..." 
                           autofocus
                           autocomplete="off">
                </div>
                <div id="gridProdutos" class="pdv-grid-produtos">
                    <?php foreach ($produtos as $produto): ?>
                    <div class="pdv-produto-card" 
                         data-produto-id="<?php echo $produto->idProdutos; ?>"
                         data-produto-preco="<?php echo $produto->precoVenda; ?>"
                         data-produto-estoque="<?php echo $produto->estoque; ?>">
                        <?php if (!empty($produto->url_image_thumb) && file_exists(FCPATH . $produto->url_image_thumb)): ?>
                        <img src="<?php echo base_url() . $produto->url_image_thumb; ?>" alt="<?php echo $produto->descricao; ?>">
                        <?php else: ?>
                        <div style="width: 100%; height: 120px; background: #ecf0f1; display: flex; align-items: center; justify-content: center; border-radius: 5px;">
                            <i class="fas fa-box" style="font-size: 48px; color: #bdc3c7;"></i>
                        </div>
                        <?php endif; ?>
                        <div class="nome"><?php echo $produto->descricao; ?></div>
                        <div class="preco">R$ <?php echo number_format($produto->precoVenda, 2, ',', '.'); ?></div>
                        <div class="estoque">
                            <?php if ($produto->estoque <= 0): ?>
                                <span class="badge-estoque badge-estoque-zero">Sem Estoque</span>
                            <?php elseif ($produto->estoque < 10): ?>
                                <span class="badge-estoque badge-estoque-baixo">Estoque: <?php echo $produto->estoque; ?></span>
                            <?php else: ?>
                                <span class="badge-estoque badge-estoque-ok">Estoque: <?php echo $produto->estoque; ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Carrinho -->
            <div class="pdv-carrinho">
                <div class="pdv-carrinho-header">
                    <i class="fas fa-shopping-cart"></i> Carrinho
                </div>
                <div class="pdv-carrinho-itens" id="carrinhoItens">
                    <div class="empty-state">
                        <i class="fas fa-shopping-cart"></i>
                        <p>Carrinho vazio</p>
                        <p style="font-size: 12px;">Adicione produtos clicando nos cards</p>
                    </div>
                </div>
                <div class="pdv-resumo" id="resumoVenda" style="display: none;">
                    <div class="pdv-resumo-linha">
                        <span>Subtotal:</span>
                        <span id="subtotal">R$ 0,00</span>
                    </div>
                    <div class="pdv-resumo-linha">
                        <span>Desconto:</span>
                        <span id="desconto">R$ 0,00</span>
                    </div>
                    <div class="pdv-resumo-linha pdv-resumo-total">
                        <span>TOTAL:</span>
                        <span id="total">R$ 0,00</span>
                    </div>
                    <div class="pdv-botoes">
                        <button class="pdv-btn pdv-btn-warning" onclick="aplicarDesconto()">
                            <i class="fas fa-tag"></i> Desconto
                        </button>
                        <button class="pdv-btn pdv-btn-danger" onclick="cancelarVenda()">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button class="pdv-btn pdv-btn-success pdv-btn-full" onclick="finalizarVenda()">
                            <i class="fas fa-check"></i> Finalizar Venda
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Pagamento -->
    <div class="modal-pagamento" id="modalPagamento">
        <div class="modal-content">
            <div class="modal-header">Forma de Pagamento</div>
            <div id="alertPagamento"></div>
            <div>
                <div style="text-align: center; font-size: 24px; font-weight: bold; margin-bottom: 20px; color: #27ae60;">
                    Total: <span id="totalPagamento">R$ 0,00</span>
                </div>
                <div class="formas-pagamento-grid" id="formasPagamentoGrid">
                    <?php foreach ($formas_pagamento as $forma): ?>
                    <div class="forma-pagamento-btn" 
                         data-forma-id="<?php echo $forma->idFormaPagamento; ?>"
                         data-forma-nome="<?php echo $forma->nome; ?>"
                         data-exige-troco="<?php echo $forma->exige_troco; ?>"
                         onclick="selecionarFormaPagamento(this)">
                        <?php
                        $icones = [
                            'dinheiro' => 'fa-money-bill-wave',
                            'cartao_debito' => 'fa-credit-card',
                            'cartao_credito' => 'fa-credit-card',
                            'pix' => 'fa-qrcode',
                            'vale' => 'fa-ticket-alt',
                            'outros' => 'fa-wallet'
                        ];
                        $icone = isset($icones[$forma->tipo]) ? $icones[$forma->tipo] : 'fa-wallet';
                        ?>
                        <i class="fas <?php echo $icone; ?>" style="font-size: 32px; display: block; margin-bottom: 10px;"></i>
                        <?php echo $forma->nome; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div id="campoValorRecebido" style="display: none;">
                    <label>Valor Recebido:</label>
                    <input type="text" 
                           id="valorRecebido" 
                           class="input-valor" 
                           placeholder="0,00"
                           onkeyup="calcularTroco()">
                    <div class="teclado-numerico">
                        <div class="tecla" onclick="adicionarNumero('1')">1</div>
                        <div class="tecla" onclick="adicionarNumero('2')">2</div>
                        <div class="tecla" onclick="adicionarNumero('3')">3</div>
                        <div class="tecla" onclick="adicionarNumero('4')">4</div>
                        <div class="tecla" onclick="adicionarNumero('5')">5</div>
                        <div class="tecla" onclick="adicionarNumero('6')">6</div>
                        <div class="tecla" onclick="adicionarNumero('7')">7</div>
                        <div class="tecla" onclick="adicionarNumero('8')">8</div>
                        <div class="tecla" onclick="adicionarNumero('9')">9</div>
                        <div class="tecla" onclick="adicionarNumero('0')">0</div>
                        <div class="tecla zero" onclick="adicionarNumero('00')">00</div>
                        <div class="tecla" onclick="adicionarNumero('.')">.</div>
                        <div class="tecla" onclick="limparValor()" style="background: #e74c3c; color: white;">⌫</div>
                    </div>
                    <div style="text-align: center; font-size: 20px; font-weight: bold; margin-top: 15px; color: #3498db;">
                        Troco: <span id="troco">R$ 0,00</span>
                    </div>
                </div>
                <div style="margin-top: 20px; display: flex; gap: 10px;">
                    <button class="pdv-btn pdv-btn-danger" style="flex: 1;" onclick="fecharModalPagamento()">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button class="pdv-btn pdv-btn-success" style="flex: 1;" onclick="confirmarPagamento()" id="btnConfirmarPagamento" disabled>
                        <i class="fas fa-check"></i> Confirmar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo base_url(); ?>assets/js/jquery.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/sweetalert2.all.min.js"></script>
    <script>
        let vendaId = null;
        let carrinho = [];
        let formasPagamentoSelecionadas = [];
        let formaPagamentoAtual = null;
        let totalVenda = 0;
        let desconto = 0;

        $(document).ready(function() {
            // Criar venda ao carregar
            criarVenda();

            // Busca de produtos
            $('#buscaProduto').on('keyup', function() {
                const termo = $(this).val();
                if (termo.length >= 2) {
                    buscarProdutos(termo);
                } else if (termo.length === 0) {
                    carregarProdutos();
                }
            });

            // Busca por código de barras (Enter)
            $('#buscaProduto').on('keypress', function(e) {
                if (e.which === 13) {
                    const codigo = $(this).val().trim();
                    if (codigo.length > 0) {
                        buscarProdutoCodigoBarras(codigo);
                        $(this).val('');
                    }
                }
            });

            // Adicionar produto ao clicar no card
            $(document).on('click', '.pdv-produto-card', function() {
                const produtoId = $(this).data('produto-id');
                const preco = parseFloat($(this).data('produto-preco'));
                const estoque = parseInt($(this).data('produto-estoque'));

                if (estoque <= 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Sem Estoque',
                        text: 'Este produto não possui estoque disponível.'
                    });
                    return;
                }

                adicionarProduto(produtoId, preco);
            });
        });

        function criarVenda() {
            $.ajax({
                url: '<?php echo base_url(); ?>index.php/vendas/pdvCriarVenda',
                type: 'POST',
                data: {
                    cliente_id: <?php echo $cliente_consumidor_final->idClientes; ?>
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        vendaId = response.venda_id;
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: response.message || 'Erro ao criar venda'
                        });
                    }
                }
            });
        }

        function buscarProdutos(termo) {
            $.ajax({
                url: '<?php echo base_url(); ?>index.php/vendas/pdvBuscarProdutos',
                type: 'GET',
                data: { termo: termo },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        renderizarProdutos(response.produtos);
                    }
                }
            });
        }

        function buscarProdutoCodigoBarras(codigo) {
            $.ajax({
                url: '<?php echo base_url(); ?>index.php/vendas/pdvBuscarProdutoCodigoBarras',
                type: 'POST',
                data: { codigo: codigo },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        adicionarProduto(response.produto.idProdutos, parseFloat(response.produto.precoVenda));
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Produto não encontrado',
                            text: 'Código de barras não encontrado no sistema.'
                        });
                    }
                }
            });
        }

        function renderizarProdutos(produtos) {
            let html = '';
            produtos.forEach(function(produto) {
                const estoqueBadge = produto.estoque <= 0 
                    ? '<span class="badge-estoque badge-estoque-zero">Sem Estoque</span>'
                    : produto.estoque < 10
                    ? `<span class="badge-estoque badge-estoque-baixo">Estoque: ${produto.estoque}</span>`
                    : `<span class="badge-estoque badge-estoque-ok">Estoque: ${produto.estoque}</span>`;
                
                const imagem = (produto.url_image_thumb && produto.url_image_thumb.trim() !== '')
                    ? `<img src="<?php echo base_url(); ?>${produto.url_image_thumb}" alt="${produto.descricao}">`
                    : `<div style="width: 100%; height: 120px; background: #ecf0f1; display: flex; align-items: center; justify-content: center; border-radius: 5px;"><i class="fas fa-box" style="font-size: 48px; color: #bdc3c7;"></i></div>`;

                html += `
                    <div class="pdv-produto-card" 
                         data-produto-id="${produto.idProdutos}"
                         data-produto-preco="${produto.precoVenda}"
                         data-produto-estoque="${produto.estoque}">
                        ${imagem}
                        <div class="nome">${produto.descricao}</div>
                        <div class="preco">R$ ${parseFloat(produto.precoVenda).toFixed(2).replace('.', ',')}</div>
                        <div class="estoque">${estoqueBadge}</div>
                    </div>
                `;
            });
            $('#gridProdutos').html(html);
        }

        function carregarProdutos() {
            buscarProdutos('');
        }

        function adicionarProduto(produtoId, preco) {
            if (!vendaId) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro',
                    text: 'Venda não foi criada. Recarregue a página.'
                });
                return;
            }

            $.ajax({
                url: '<?php echo base_url(); ?>index.php/vendas/pdvAdicionarProduto',
                type: 'POST',
                data: {
                    venda_id: vendaId,
                    produto_id: produtoId,
                    quantidade: 1,
                    preco: preco
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        atualizarCarrinho(response.itens, response.total);
                        $('#buscaProduto').val('').focus();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: response.message || 'Erro ao adicionar produto'
                        });
                    }
                }
            });
        }

        function removerProduto(itemId) {
            $.ajax({
                url: '<?php echo base_url(); ?>index.php/vendas/pdvRemoverProduto',
                type: 'POST',
                data: { item_id: itemId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        atualizarCarrinho(response.itens, response.total);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: response.message || 'Erro ao remover produto'
                        });
                    }
                }
            });
        }

        function atualizarCarrinho(itens, total) {
            carrinho = itens;
            totalVenda = total;
            
            if (itens.length === 0) {
                $('#carrinhoItens').html(`
                    <div class="empty-state">
                        <i class="fas fa-shopping-cart"></i>
                        <p>Carrinho vazio</p>
                        <p style="font-size: 12px;">Adicione produtos clicando nos cards</p>
                    </div>
                `);
                $('#resumoVenda').hide();
            } else {
                let html = '';
                itens.forEach(function(item) {
                    html += `
                        <div class="pdv-item">
                            <div class="pdv-item-info">
                                <div class="pdv-item-nome">${item.descricao}</div>
                                <div class="pdv-item-detalhes">
                                    ${item.quantidade}x R$ ${parseFloat(item.preco).toFixed(2).replace('.', ',')}
                                </div>
                            </div>
                            <div class="pdv-item-valor">
                                R$ ${parseFloat(item.subTotal).toFixed(2).replace('.', ',')}
                            </div>
                            <div class="pdv-item-acoes">
                                <button onclick="removerProduto(${item.idItens})" style="background: #e74c3c; color: white;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    `;
                });
                $('#carrinhoItens').html(html);
                
                atualizarResumo();
                $('#resumoVenda').show();
            }
        }

        function atualizarResumo() {
            const subtotal = totalVenda;
            const totalComDesconto = subtotal - desconto;
            
            $('#subtotal').text('R$ ' + subtotal.toFixed(2).replace('.', ','));
            $('#desconto').text('R$ ' + desconto.toFixed(2).replace('.', ','));
            $('#total').text('R$ ' + totalComDesconto.toFixed(2).replace('.', ','));
        }

        function aplicarDesconto() {
            Swal.fire({
                title: 'Aplicar Desconto',
                html: `
                    <div style="text-align: left; margin: 20px 0;">
                        <label>Tipo:</label>
                        <select id="tipoDesconto" class="swal2-input" style="width: 100%; margin-bottom: 10px;">
                            <option value="real">Valor (R$)</option>
                            <option value="percentual">Percentual (%)</option>
                        </select>
                        <label>Valor:</label>
                        <input type="text" id="valorDesconto" class="swal2-input" placeholder="0,00">
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Aplicar',
                cancelButtonText: 'Cancelar',
                preConfirm: () => {
                    const tipo = document.getElementById('tipoDesconto').value;
                    const valor = document.getElementById('valorDesconto').value.replace(',', '.');
                    return { tipo, valor: parseFloat(valor) || 0 };
                }
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    const { tipo, valor } = result.value;
                    if (tipo === 'percentual') {
                        desconto = (totalVenda * valor) / 100;
                    } else {
                        desconto = Math.min(valor, totalVenda);
                    }
                    atualizarResumo();
                }
            });
        }

        function finalizarVenda() {
            if (carrinho.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Carrinho Vazio',
                    text: 'Adicione produtos antes de finalizar a venda.'
                });
                return;
            }

            formasPagamentoSelecionadas = [];
            formaPagamentoAtual = null;
            $('#totalPagamento').text('R$ ' + (totalVenda - desconto).toFixed(2).replace('.', ','));
            $('#modalPagamento').addClass('active');
            $('#campoValorRecebido').hide();
            $('#btnConfirmarPagamento').prop('disabled', true);
        }

        function selecionarFormaPagamento(element) {
            $('.forma-pagamento-btn').removeClass('active');
            $(element).addClass('active');
            
            formaPagamentoAtual = {
                id: $(element).data('forma-id'),
                nome: $(element).data('forma-nome'),
                exige_troco: $(element).data('exige-troco') == 1
            };

            if (formaPagamentoAtual.exige_troco) {
                $('#campoValorRecebido').show();
                $('#valorRecebido').val('').focus();
            } else {
                $('#campoValorRecebido').hide();
                $('#btnConfirmarPagamento').prop('disabled', false);
            }
        }

        function adicionarNumero(numero) {
            const campo = $('#valorRecebido');
            let valor = campo.val().replace(/[^0-9,]/g, '');
            
            if (numero === '.') {
                if (!valor.includes(',')) {
                    valor += ',';
                }
            } else {
                valor += numero;
            }
            
            campo.val(valor);
            calcularTroco();
        }

        function limparValor() {
            $('#valorRecebido').val('');
            calcularTroco();
        }

        function calcularTroco() {
            const valorRecebido = parseFloat($('#valorRecebido').val().replace(',', '.')) || 0;
            const total = totalVenda - desconto;
            const troco = Math.max(0, valorRecebido - total);
            
            $('#troco').text('R$ ' + troco.toFixed(2).replace('.', ','));
            
            if (valorRecebido >= total) {
                $('#btnConfirmarPagamento').prop('disabled', false);
            } else {
                $('#btnConfirmarPagamento').prop('disabled', true);
            }
        }

        function confirmarPagamento() {
            if (!formaPagamentoAtual) {
                mostrarAlerta('Selecione uma forma de pagamento', 'warning');
                return;
            }

            const total = totalVenda - desconto;
            let valorPagamento = total;

            if (formaPagamentoAtual.exige_troco) {
                const valorRecebido = parseFloat($('#valorRecebido').val().replace(',', '.')) || 0;
                if (valorRecebido < total) {
                    mostrarAlerta('Valor recebido é menor que o total', 'danger');
                    return;
                }
                valorPagamento = valorRecebido;
            }

            formasPagamentoSelecionadas.push({
                forma_pagamento_id: formaPagamentoAtual.id,
                valor: total.toFixed(2),
                troco: formaPagamentoAtual.exige_troco ? (valorPagamento - total).toFixed(2) : '0.00'
            });

            // Se ainda falta pagar, continuar selecionando formas
            // Por enquanto, vamos finalizar com uma forma apenas
            processarFinalizacao();
        }

        function processarFinalizacao() {
            $.ajax({
                url: '<?php echo base_url(); ?>index.php/vendas/pdvFinalizarVenda',
                type: 'POST',
                data: {
                    venda_id: vendaId,
                    pagamentos: JSON.stringify(formasPagamentoSelecionadas),
                    desconto: desconto,
                    tipo_desconto: desconto > 0 ? 'real' : null
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Venda Finalizada!',
                            text: 'Venda #' + response.venda_id + ' finalizada com sucesso.',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: response.message || 'Erro ao finalizar venda'
                        });
                    }
                }
            });
        }

        function cancelarVenda() {
            if (carrinho.length === 0) {
                location.reload();
                return;
            }

            Swal.fire({
                title: 'Cancelar Venda?',
                text: 'Esta ação não pode ser desfeita.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sim, cancelar',
                cancelButtonText: 'Não',
                input: 'text',
                inputPlaceholder: 'Motivo do cancelamento',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Informe o motivo do cancelamento';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?php echo base_url(); ?>index.php/vendas/pdvCancelarVenda',
                        type: 'POST',
                        data: {
                            venda_id: vendaId,
                            motivo: result.value
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Venda Cancelada',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Erro',
                                    text: response.message || 'Erro ao cancelar venda'
                                });
                            }
                        }
                    });
                }
            });
        }

        function fecharModalPagamento() {
            $('#modalPagamento').removeClass('active');
            formasPagamentoSelecionadas = [];
            formaPagamentoAtual = null;
        }

        function mostrarAlerta(mensagem, tipo) {
            const alertClass = tipo === 'danger' ? 'alert-danger' : 'alert-warning';
            $('#alertPagamento').html(`<div class="alert ${alertClass}">${mensagem}</div>`);
            setTimeout(() => {
                $('#alertPagamento').html('');
            }, 3000);
        }

        // Focar no campo de busca ao carregar
        $(document).ready(function() {
            $('#buscaProduto').focus();
        });
    </script>
</body>
</html>
