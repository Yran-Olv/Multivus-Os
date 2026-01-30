<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/custom.css" />

<div class="new122">
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon">
            <i class="fas fa-chart-bar"></i>
        </span>
        <h5>Relatório de Vendas a Prazo</h5>
    </div>

    <!-- Filtros -->
    <div class="span12" style="margin-left: 0; margin-top: 20px;">
        <form method="get" action="<?php echo base_url(); ?>index.php/vendas_prazo/relatorio">
            <div class="span12" style="margin-left: 0;">
                <div class="span4">
                    <label>Data Início</label>
                    <input type="date" name="data_inicio" class="span12" value="<?php echo $data_inicio; ?>">
                </div>
                <div class="span4">
                    <label>Data Fim</label>
                    <input type="date" name="data_fim" class="span12" value="<?php echo $data_fim; ?>">
                </div>
                <div class="span2">
                    <label>&nbsp;</label>
                    <button type="submit" class="button btn btn-primary span12">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                </div>
                <div class="span2">
                    <label>&nbsp;</label>
                    <a href="<?php echo base_url(); ?>index.php/vendas_prazo/relatorio?data_inicio=<?php echo date('Y-m-01'); ?>&data_fim=<?php echo date('Y-m-t'); ?>" class="button btn btn-info span12">
                        <i class="fas fa-calendar"></i> Este Mês
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Estatísticas -->
    <?php if (isset($estatisticas)): ?>
    <div class="span12" style="margin-left: 0; margin-top: 20px;">
        <div class="widget-box">
            <div class="widget-title">
                <h5>Estatísticas do Período</h5>
            </div>
            <div class="widget-content">
                <div class="span12" style="margin-left: 0;">
                    <div class="span3" style="text-align: center; padding: 20px; background: #E3F2FD; border-radius: 5px; margin: 5px;">
                        <h2 style="margin: 0; color: #2196F3;"><?php echo number_format($estatisticas->total_vendas, 0, ',', '.'); ?></h2>
                        <p style="margin: 5px 0 0 0;">Total de Vendas</p>
                    </div>
                    <div class="span3" style="text-align: center; padding: 20px; background: #FFF3E0; border-radius: 5px; margin: 5px;">
                        <h2 style="margin: 0; color: #FF9800;">R$ <?php echo number_format($estatisticas->valor_pendente, 2, ',', '.'); ?></h2>
                        <p style="margin: 5px 0 0 0;">Valor Pendente</p>
                    </div>
                    <div class="span3" style="text-align: center; padding: 20px; background: #FFEBEE; border-radius: 5px; margin: 5px;">
                        <h2 style="margin: 0; color: #f44336;"><?php echo number_format($estatisticas->vendas_atrasadas, 0, ',', '.'); ?></h2>
                        <p style="margin: 5px 0 0 0;">Vendas Atrasadas</p>
                    </div>
                    <div class="span3" style="text-align: center; padding: 20px; background: #E8F5E9; border-radius: 5px; margin: 5px;">
                        <h2 style="margin: 0; color: #4CAF50;">R$ <?php echo number_format($estatisticas->valor_pago, 2, ',', '.'); ?></h2>
                        <p style="margin: 5px 0 0 0;">Valor Pago</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Vendas Atrasadas -->
    <?php if (!empty($vendas_atrasadas)): ?>
    <div class="span12" style="margin-left: 0; margin-top: 20px;">
        <div class="widget-box">
            <div class="widget-title" style="background: #f44336; color: white;">
                <h5>Vendas com Parcelas Atrasadas</h5>
            </div>
            <div class="widget-content nopadding">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Data Venda</th>
                            <th>Valor Total</th>
                            <th>Valor Pendente</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($vendas_atrasadas as $venda): ?>
                        <tr class="danger">
                            <td><?php echo $venda->idVendas; ?></td>
                            <td><?php echo $venda->nomeCliente; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($venda->dataVenda)); ?></td>
                            <td>R$ <?php echo number_format($venda->valorTotal, 2, ',', '.'); ?></td>
                            <td><strong>R$ <?php echo number_format($venda->valor_pendente, 2, ',', '.'); ?></strong></td>
                            <td>
                                <a href="<?php echo base_url(); ?>index.php/vendas_prazo/visualizar/<?php echo $venda->idVendas; ?>" class="btn btn-info btn-mini">
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
</div>

<style>
.danger {
    background-color: #ffebee !important;
}
</style>
