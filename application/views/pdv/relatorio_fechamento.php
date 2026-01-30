<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/custom.css" />

<div class="new122">
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon">
            <i class="fas fa-file-invoice"></i>
        </span>
        <h5>Relatório de Fechamento - Turno #<?php echo $turno->idTurno; ?></h5>
    </div>

    <!-- Informações do Turno -->
    <div class="span12" style="margin-left: 0; margin-top: 20px;">
        <div class="widget-box">
            <div class="widget-title">
                <h5>Informações do Turno</h5>
            </div>
            <div class="widget-content">
                <div class="span6">
                    <p><strong>Caixa:</strong> <?php echo $turno->caixa_nome; ?></p>
                    <p><strong>Operador:</strong> <?php echo $turno->usuario_nome; ?></p>
                    <p><strong>Data Abertura:</strong> <?php echo date('d/m/Y H:i:s', strtotime($turno->data_abertura)); ?></p>
                    <p><strong>Data Fechamento:</strong> <?php echo date('d/m/Y H:i:s', strtotime($turno->data_fechamento)); ?></p>
                </div>
                <div class="span6">
                    <p><strong>Valor de Abertura:</strong> R$ <?php echo number_format($turno->valor_abertura, 2, ',', '.'); ?></p>
                    <p><strong>Valor de Fechamento:</strong> R$ <?php echo number_format($turno->valor_fechamento, 2, ',', '.'); ?></p>
                    <p><strong>Valor Esperado:</strong> R$ <?php echo number_format($turno->valor_esperado, 2, ',', '.'); ?></p>
                    <p><strong>Diferença:</strong> 
                        <span style="color: <?php echo $turno->diferenca >= 0 ? '#4CAF50' : '#f44336'; ?>; font-weight: bold;">
                            R$ <?php echo number_format($turno->diferenca, 2, ',', '.'); ?>
                            <?php echo $turno->diferenca >= 0 ? '(Sobra)' : '(Falta)'; ?>
                        </span>
                    </p>
                </div>
                <?php if ($turno->observacoes): ?>
                <div class="span12" style="margin-left: 0; margin-top: 15px;">
                    <p><strong>Observações:</strong></p>
                    <p><?php echo nl2br($turno->observacoes); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Estatísticas -->
    <?php if (isset($estatisticas)): ?>
    <div class="span12" style="margin-left: 0; margin-top: 20px;">
        <div class="widget-box">
            <div class="widget-title">
                <h5>Estatísticas do Turno</h5>
            </div>
            <div class="widget-content">
                <div class="span12" style="margin-left: 0;">
                    <div class="span4" style="text-align: center; padding: 20px; background: #E3F2FD; border-radius: 5px; margin: 5px;">
                        <h3 style="margin: 0; color: #2196F3;"><?php echo number_format($estatisticas['vendas']->total_vendas, 0, ',', '.'); ?></h3>
                        <p style="margin: 5px 0 0 0;">Total de Vendas</p>
                    </div>
                    <div class="span4" style="text-align: center; padding: 20px; background: #E8F5E9; border-radius: 5px; margin: 5px;">
                        <h3 style="margin: 0; color: #4CAF50;">R$ <?php echo number_format($estatisticas['vendas']->total_vendido, 2, ',', '.'); ?></h3>
                        <p style="margin: 5px 0 0 0;">Total Vendido</p>
                    </div>
                    <div class="span4" style="text-align: center; padding: 20px; background: #FFF3E0; border-radius: 5px; margin: 5px;">
                        <h3 style="margin: 0; color: #FF9800;">
                            R$ <?php echo number_format($estatisticas['vendas']->total_vendido > 0 ? ($estatisticas['vendas']->total_vendido / $estatisticas['vendas']->total_vendas) : 0, 2, ',', '.'); ?>
                        </h3>
                        <p style="margin: 5px 0 0 0;">Ticket Médio</p>
                    </div>
                </div>

                <?php if (!empty($estatisticas['pagamentos'])): ?>
                <div class="span12" style="margin-left: 0; margin-top: 20px;">
                    <h5>Pagamentos por Forma:</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Forma de Pagamento</th>
                                <th>Quantidade</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($estatisticas['pagamentos'] as $pagamento): ?>
                            <tr>
                                <td><?php echo $pagamento->nome; ?></td>
                                <td><?php echo number_format($pagamento->quantidade, 0, ',', '.'); ?></td>
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

    <!-- Vendas do Turno -->
    <?php if (!empty($vendas)): ?>
    <div class="span12" style="margin-left: 0; margin-top: 20px;">
        <div class="widget-box">
            <div class="widget-title">
                <h5>Vendas do Turno</h5>
            </div>
            <div class="widget-content nopadding">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Data/Hora</th>
                            <th>Valor</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($vendas as $venda): ?>
                        <tr>
                            <td><?php echo $venda->idVendas; ?></td>
                            <td><?php echo $venda->nomeCliente; ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($venda->dataVenda)); ?></td>
                            <td>R$ <?php echo number_format($venda->valor_desconto, 2, ',', '.'); ?></td>
                            <td>
                                <a href="<?php echo base_url(); ?>index.php/vendas/visualizar/<?php echo $venda->idVendas; ?>" 
                                   class="btn btn-info btn-mini" target="_blank">
                                    <i class="fas fa-eye"></i> Ver
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="span12" style="margin-left: 0; margin-top: 20px; text-align: center;">
        <button onclick="window.print()" class="button btn btn-primary">
            <span class="button__icon"><i class="fas fa-print"></i></span>
            <span class="button__text2">Imprimir Relatório</span>
        </button>
        <a href="<?php echo base_url(); ?>index.php/vendas/pdv" class="button btn btn-success">
            <span class="button__icon"><i class="fas fa-arrow-left"></i></span>
            <span class="button__text2">Voltar ao PDV</span>
        </a>
    </div>
</div>

<style>
@media print {
    .widget-title, .button, a {
        display: none !important;
    }
}
</style>
