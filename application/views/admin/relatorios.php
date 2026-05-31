<?php defined('BASEPATH') OR exit('Acesso directo ao script não permitido'); ?>
<h4 class="mb-4">Relatórios estatísticos</h4>

<div class="row g-3 mb-4">
    <?php foreach ([
        ['Vendas totais', moeda($resumo['total_vendas']), 'success'],
        ['Entregas concluídas', $resumo['entregas'], 'info'],
        ['Pedidos cancelados', $resumo['canceladas'], 'danger'],
        ['Total recebido', moeda($recebido), 'primary'],
    ] as [$r, $v, $cor]): ?>
        <div class="col-6 col-md-3"><div class="card cartao-estatistica"><div class="card-body">
            <div class="text-muted small"><?= $r ?></div><div class="valor text-<?= $cor ?>"><?= $v ?></div>
        </div></div></div>
    <?php endforeach; ?>
</div>

<div class="row g-3">
    <div class="col-lg-7"><div class="card"><div class="card-header bg-white fw-semibold">Vendas mensais (últimos 6 meses)</div>
        <div class="card-body"><canvas id="grafico-mensal" height="120"></canvas></div></div></div>
    <div class="col-lg-5"><div class="card"><div class="card-header bg-white fw-semibold">Pedidos por estado</div>
        <div class="card-body"><canvas id="grafico-estado" height="120"></canvas></div></div></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
const mensais = <?= json_encode(array_reverse($mensais)) ?>;
const porEstado = <?= json_encode($por_estado) ?>;

new Chart(document.getElementById('grafico-mensal'), {
    type: 'bar',
    data: {
        labels: mensais.map(m => m.mes),
        datasets: [{ label: 'Vendas (MT)', data: mensais.map(m => parseFloat(m.total)), backgroundColor: '#e63946' }]
    },
    options: { plugins: { legend: { display: false } } }
});
new Chart(document.getElementById('grafico-estado'), {
    type: 'doughnut',
    data: {
        labels: porEstado.map(e => e.estado),
        datasets: [{ data: porEstado.map(e => parseInt(e.total)),
            backgroundColor: ['#1d3557','#457b9d','#a8dadc','#e63946','#f4a261','#2a9d8f','#e76f51','#264653','#6c757d'] }]
    }
});
</script>
