<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/custom.css" />
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>

<div class="new122">
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon">
            <i class="fas fa-chart-line"></i>
        </span>
        <h5>Dashboard de Vendas - PDV</h5>
    </div>

    <!-- Filtro de Data -->
    <div class="span12" style="margin-left: 0; margin-top: 20px;">
        <form method="get" action="<?php echo base_url(); ?>index.php/vendas/dashboard">
            <div class="span3">
                <label>Data</label>
                <input type="date" name="data" class="span12" value="<?php echo $data; ?>">
            </div>
            <div class="span2">
                <label>&nbsp;</label>
                <button type="submit" class="button btn btn-primary span12">
                    <i class="fas fa-search"></i> Filtrar
                </button>
            </div>
            <div class="span2">
                <label>&nbsp;</label>
                <a href="<?php echo base_url(); ?>index.php/vendas/dashboard?data=<?php echo date('Y-m-d'); ?>" class="button btn btn-info span12">
                    <i class="fas fa-calendar-day"></i> Hoje
                </a>
            </div>
        </form>
    </div>

    <!-- Estatísticas do Dia -->
    <?php if (isset($vendas_dia)): ?>
    <div class="span12" style="margin-left: 0; margin-top: 20px;">
        <div class="widget-box">
            <div class="widget-title">
                <h5>Estatísticas do Dia - <?php echo date('d/m/Y', strtotime($data)); ?></h5>
            </div>
            <div class="widget-content">
                <div class="span12" style="margin-left: 0;">
                    <div class="span3" style="text-align: center; padding: 20px; background: #E3F2FD; border-radius: 5px; margin: 5px;">
                        <h2 style="margin: 0; color: #2196F3;"><?php echo number_format($vendas_dia->total_vendas, 0, ',', '.'); ?></h2>
                        <p style="margin: 5px 0 0 0;">Total de Vendas</p>
                    </div>
                    <div class="span3" style="text-align: center; padding: 20px; background: #E8F5E9; border-radius: 5px; margin: 5px;">
                        <h2 style="margin: 0; color: #4CAF50;">R$ <?php echo number_format($vendas_dia->total_vendido, 2, ',', '.'); ?></h2>
                        <p style="margin: 5px 0 0 0;">Total Vendido</p>
                    </div>
                    <div class="span3" style="text-align: center; padding: 20px; background: #FFF3E0; border-radius: 5px; margin: 5px;">
                        <h2 style="margin: 0; color: #FF9800;">R$ <?php echo number_format($vendas_dia->ticket_medio, 2, ',', '.'); ?></h2>
                        <p style="margin: 5px 0 0 0;">Ticket Médio</p>
                    </div>
                    <div class="span3" style="text-align: center; padding: 20px; background: #F3E5F5; border-radius: 5px; margin: 5px;">
                        <h2 style="margin: 0; color: #9C27B0;">
                            <?php 
                            $horas = date('H');
                            $vendasPorHora = $horas > 0 ? ($vendas_dia->total_vendas / $horas) : 0;
                            echo number_format($vendasPorHora, 1, ',', '.');
                            ?>
                        </h2>
                        <p style="margin: 5px 0 0 0;">Vendas/Hora</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Formas de Pagamento -->
    <?php if (!empty($formas_pagamento_dia)): ?>
    <div class="span12" style="margin-left: 0; margin-top: 20px;">
        <div class="widget-box">
            <div class="widget-title">
                <h5>Formas de Pagamento</h5>
            </div>
            <div class="widget-content">
                <canvas id="chartFormasPagamento" style="max-height: 300px;"></canvas>
                <table class="table table-bordered" style="margin-top: 20px;">
                    <thead>
                        <tr>
                            <th>Forma de Pagamento</th>
                            <th>Quantidade</th>
                            <th>Total</th>
                            <th>Percentual</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $totalGeral = array_sum(array_column($formas_pagamento_dia, 'total'));
                        foreach ($formas_pagamento_dia as $forma): 
                            $percentual = $totalGeral > 0 ? ($forma->total / $totalGeral) * 100 : 0;
                        ?>
                        <tr>
                            <td><?php echo $forma->nome; ?></td>
                            <td><?php echo number_format($forma->quantidade, 0, ',', '.'); ?></td>
                            <td>R$ <?php echo number_format($forma->total, 2, ',', '.'); ?></td>
                            <td><?php echo number_format($percentual, 2, ',', '.'); ?>%</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Produtos Mais Vendidos -->
    <?php if (!empty($produtos_mais_vendidos)): ?>
    <div class="span12" style="margin-left: 0; margin-top: 20px;">
        <div class="widget-box">
            <div class="widget-title">
                <h5>Produtos Mais Vendidos</h5>
            </div>
            <div class="widget-content nopadding">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Produto</th>
                            <th>Quantidade</th>
                            <th>Total Vendido</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $posicao = 1; ?>
                        <?php foreach ($produtos_mais_vendidos as $produto): ?>
                        <tr>
                            <td><?php echo $posicao++; ?></td>
                            <td><?php echo $produto->descricao; ?></td>
                            <td><?php echo number_format($produto->quantidade_vendida, 0, ',', '.'); ?></td>
                            <td>R$ <?php echo number_format($produto->total_vendido, 2, ',', '.'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
<?php if (!empty($formas_pagamento_dia)): ?>
const ctxFormasPagamento = document.getElementById('chartFormasPagamento').getContext('2d');
new Chart(ctxFormasPagamento, {
    type: 'doughnut',
    data: {
        labels: [<?php echo implode(',', array_map(function($f) { return "'" . $f->nome . "'"; }, $formas_pagamento_dia)); ?>],
        datasets: [{
            data: [<?php echo implode(',', array_column($formas_pagamento_dia, 'total')); ?>],
            backgroundColor: [
                '#3498db',
                '#2ecc71',
                '#f39c12',
                '#e74c3c',
                '#9b59b6',
                '#1abc9c',
                '#34495e'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
<?php endif; ?>
</script>
