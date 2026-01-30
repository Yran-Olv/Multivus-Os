<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/custom.css" />
<script src="<?php echo base_url() ?>assets/js/sweetalert2.all.min.js"></script>

<div class="new122">
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon">
            <i class="fas fa-calendar-alt"></i>
        </span>
        <h5>Vendas a Prazo</h5>
    </div>

    <!-- Filtros -->
    <div class="span12" style="margin-left: 0; margin-top: 20px;">
        <form method="get" action="<?php echo base_url(); ?>index.php/vendas_prazo" id="formFiltros">
            <div class="span12" style="margin-left: 0;">
                <div class="span3">
                    <label>Cliente</label>
                    <input type="text" name="cliente" class="span12" value="<?php echo $this->input->get('cliente'); ?>" placeholder="Nome do cliente">
                </div>
                <div class="span2">
                    <label>Status</label>
                    <select name="status_parcela" class="span12">
                        <option value="">Todos</option>
                        <option value="pendentes" <?php echo $this->input->get('status_parcela') == 'pendentes' ? 'selected' : ''; ?>>Pendentes</option>
                        <option value="atrasadas" <?php echo $this->input->get('status_parcela') == 'atrasadas' ? 'selected' : ''; ?>>Atrasadas</option>
                        <option value="pagas" <?php echo $this->input->get('status_parcela') == 'pagas' ? 'selected' : ''; ?>>Pagas</option>
                    </select>
                </div>
                <div class="span2">
                    <label>Data Venda (Início)</label>
                    <input type="date" name="data_inicio" class="span12" value="<?php echo $this->input->get('data_inicio'); ?>">
                </div>
                <div class="span2">
                    <label>Data Venda (Fim)</label>
                    <input type="date" name="data_fim" class="span12" value="<?php echo $this->input->get('data_fim'); ?>">
                </div>
                <div class="span2">
                    <label>Vencimento (Início)</label>
                    <input type="date" name="vencimento_inicio" class="span12" value="<?php echo $this->input->get('vencimento_inicio'); ?>">
                </div>
                <div class="span1">
                    <label>&nbsp;</label>
                    <button type="submit" class="button btn btn-primary span12">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Estatísticas -->
    <?php if (isset($estatisticas)): ?>
    <div class="span12" style="margin-left: 0; margin-top: 20px;">
        <div class="widget-box">
            <div class="widget-content">
                <div class="span3">
                    <div class="stat-box" style="background: #4CAF50; color: white; padding: 15px; border-radius: 5px; text-align: center;">
                        <h3 style="margin: 0; font-size: 24px;"><?php echo number_format($estatisticas->total_vendas, 0, ',', '.'); ?></h3>
                        <p style="margin: 5px 0 0 0;">Total de Vendas</p>
                    </div>
                </div>
                <div class="span3">
                    <div class="stat-box" style="background: #2196F3; color: white; padding: 15px; border-radius: 5px; text-align: center;">
                        <h3 style="margin: 0; font-size: 24px;">R$ <?php echo number_format($estatisticas->valor_pendente, 2, ',', '.'); ?></h3>
                        <p style="margin: 5px 0 0 0;">Valor Pendente</p>
                    </div>
                </div>
                <div class="span3">
                    <div class="stat-box" style="background: #FF9800; color: white; padding: 15px; border-radius: 5px; text-align: center;">
                        <h3 style="margin: 0; font-size: 24px;"><?php echo number_format($estatisticas->vendas_atrasadas, 0, ',', '.'); ?></h3>
                        <p style="margin: 5px 0 0 0;">Vendas Atrasadas</p>
                    </div>
                </div>
                <div class="span3">
                    <div class="stat-box" style="background: #9C27B0; color: white; padding: 15px; border-radius: 5px; text-align: center;">
                        <h3 style="margin: 0; font-size: 24px;">R$ <?php echo number_format($estatisticas->valor_pago, 2, ',', '.'); ?></h3>
                        <p style="margin: 5px 0 0 0;">Valor Pago</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Tabela de Vendas -->
    <div class="span12" style="margin-left: 0; margin-top: 20px;">
        <div class="widget-box">
            <div class="widget-content nopadding">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Data Venda</th>
                            <th>Valor Total</th>
                            <th>Parcelas</th>
                            <th>Pendente</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($vendas)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center;">Nenhuma venda a prazo encontrada.</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($vendas as $venda): ?>
                        <tr class="<?php echo $venda->parcelas_atrasadas > 0 ? 'danger' : ''; ?>">
                            <td><?php echo $venda->idVendas; ?></td>
                            <td><?php echo $venda->nomeCliente; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($venda->dataVenda)); ?></td>
                            <td>R$ <?php echo number_format($venda->valorTotal, 2, ',', '.'); ?></td>
                            <td>
                                <span class="badge badge-info"><?php echo $venda->parcelas_pagas; ?> Paga(s)</span>
                                <span class="badge badge-warning"><?php echo $venda->parcelas_pendentes; ?> Pendente(s)</span>
                                <?php if ($venda->parcelas_atrasadas > 0): ?>
                                <span class="badge badge-danger"><?php echo $venda->parcelas_atrasadas; ?> Atrasada(s)</span>
                                <?php endif; ?>
                            </td>
                            <td>R$ <?php echo number_format($venda->valor_pendente, 2, ',', '.'); ?></td>
                            <td>
                                <?php if ($venda->parcelas_atrasadas > 0): ?>
                                    <span class="label label-important">Atrasada</span>
                                <?php elseif ($venda->valor_pendente > 0): ?>
                                    <span class="label label-warning">Pendente</span>
                                <?php else: ?>
                                    <span class="label label-success">Paga</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?php echo base_url(); ?>index.php/vendas_prazo/visualizar/<?php echo $venda->idVendas; ?>" class="btn btn-info btn-mini">
                                    <i class="fas fa-eye"></i> Ver
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.stat-box {
    transition: transform 0.2s;
}
.stat-box:hover {
    transform: scale(1.05);
}
.badge {
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 11px;
    margin-right: 3px;
}
.badge-info {
    background: #2196F3;
    color: white;
}
.badge-warning {
    background: #FF9800;
    color: white;
}
.badge-danger {
    background: #f44336;
    color: white;
}
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
</style>
