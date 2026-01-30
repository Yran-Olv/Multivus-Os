<div class="accordion" id="collapse-group">
    <div class="accordion-group widget-box">
        <div class="accordion-heading">
            <div class="widget-title" style="margin: -20px 0 0">
                <a data-parent="#collapse-group" href="#collapseGOne" data-toggle="collapse">
                    <span class="icon"><i class="fas fa-shopping-bag"></i></span>
                    <h5>Dados do Produto</h5>
                </a>
            </div>
        </div>
        <div class="collapse in accordion-body">
            <div class="widget-content">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td style="text-align: center; width: 30%"><strong>Código de Barra</strong></td>
                            <td>
                                <?php echo $result->codDeBarra ?>
                            </td>
                        </tr>
                        <?php if (!empty($result->imagem)): ?>
                        <tr>
                            <td style="text-align: center; width: 30%"><strong>Imagem</strong></td>
                            <td>
                                <img src="<?php echo base_url('assets/produtos/' . $result->imagem); ?>" alt="<?php echo htmlspecialchars(getNomeProduto($result)); ?>" style="max-width: 200px; max-height: 200px; border: 1px solid #ddd; border-radius: 5px; padding: 5px;" />
                            </td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <td style="text-align: right; width: 30%"><strong>Nome do Produto</strong></td>
                            <td>
                                <?php echo htmlspecialchars(getNomeProduto($result)); ?>
                            </td>
                        </tr>
                        <?php 
                        // Verificar se há especificações técnicas
                        $temEspecificacoes = !empty($result->marca) || !empty($result->modelo) || 
                                            !empty($result->processador) || !empty($result->memoria_ram) || 
                                            !empty($result->armazenamento) || !empty($result->tela) || 
                                            !empty($result->sistema_operacional) || !empty($result->cor);
                        ?>
                        <?php if ($temEspecificacoes): ?>
                        <tr>
                            <td style="text-align: right; width: 30%"><strong>Especificações Técnicas</strong></td>
                            <td>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                                    <?php if (!empty($result->marca)): ?>
                                    <div><strong>Marca:</strong> <?php echo htmlspecialchars($result->marca); ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($result->modelo)): ?>
                                    <div><strong>Modelo:</strong> <?php echo htmlspecialchars($result->modelo); ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($result->processador)): ?>
                                    <div><strong>Processador:</strong> <?php echo htmlspecialchars($result->processador); ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($result->memoria_ram)): ?>
                                    <div><strong>Memória RAM:</strong> <?php echo htmlspecialchars($result->memoria_ram); ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($result->armazenamento)): ?>
                                    <div><strong>Armazenamento:</strong> <?php echo htmlspecialchars($result->armazenamento); ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($result->tela)): ?>
                                    <div><strong>Tela:</strong> <?php echo htmlspecialchars($result->tela); ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($result->sistema_operacional)): ?>
                                    <div><strong>Sistema Operacional:</strong> <?php echo htmlspecialchars($result->sistema_operacional); ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($result->cor)): ?>
                                    <div><strong>Cor:</strong> <?php echo htmlspecialchars($result->cor); ?></div>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <td style="text-align: right; width: 30%"><strong>Descrição</strong></td>
                            <td>
                                <?php echo htmlspecialchars($result->descricao); ?>
                            </td>
                        </tr>
                        <?php if (!empty($result->descricao_completa)): ?>
                        <tr>
                            <td style="text-align: right; width: 30%"><strong>Descrição Completa</strong></td>
                            <td>
                                <?php echo nl2br(htmlspecialchars($result->descricao_completa)); ?>
                            </td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <td style="text-align: right"><strong>Unidade</strong></td>
                            <td>
                                <?php echo $result->unidade ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: right"><strong>Preço de Compra</strong></td>
                            <td>R$
                                <?php echo $result->precoCompra; ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: right"><strong>Preço de Venda</strong></td>
                            <td>R$
                                <?php echo $result->precoVenda; ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: right"><strong>Estoque</strong></td>
                            <td>
                                <?php echo $result->estoque; ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: right"><strong>Estoque Mínimo</strong></td>
                            <td>
                                <?php echo $result->estoqueMinimo; ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
