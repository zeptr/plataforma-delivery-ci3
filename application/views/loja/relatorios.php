<?php defined('BASEPATH') OR exit('Acesso directo ao script não permitido'); ?>
<h4 class="mb-4">Relatórios de vendas</h4>
<div class="row g-3 mb-4">
    <?php foreach ([
        ['Vendas totais', moeda($resumo['total_vendas']), 'success'],
        ['Entregas concluídas', $resumo['entregas'], 'info'],
        ['Pedidos cancelados', $resumo['canceladas'], 'danger'],
        ['Total de pedidos', $resumo['total_pedidos'], 'primary'],
    ] as [$r, $v, $cor]): ?>
        <div class="col-6 col-md-3"><div class="card cartao-estatistica"><div class="card-body">
            <div class="text-muted small"><?= $r ?></div><div class="valor text-<?= $cor ?>"><?= $v ?></div>
        </div></div></div>
    <?php endforeach; ?>
</div>
<div class="row g-3">
    <div class="col-lg-7"><div class="card"><div class="card-header bg-white fw-semibold">Vendas mensais</div>
        <div class="card-body"><canvas id="gm" height="120"></canvas></div></div></div>
    <div class="col-lg-5"><div class="card"><div class="card-header bg-white fw-semibold">Pedidos por estado</div>
        <div class="card-body"><canvas id="ge" height="120"></canvas></div></div></div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
const m = <?= json_encode(array_reverse($mensais)) ?>, pe = <?= json_encode($por_estado) ?>;
new Chart(gm, { type:'bar', data:{ labels:m.map(x=>x.mes), datasets:[{label:'Vendas (MT)',data:m.map(x=>parseFloat(x.total)),backgroundColor:'#e63946'}] }, options:{plugins:{legend:{display:false}}} });
new Chart(ge, { type:'doughnut', data:{ labels:pe.map(x=>x.estado), datasets:[{data:pe.map(x=>parseInt(x.total)),backgroundColor:['#1d3557','#457b9d','#a8dadc','#e63946','#f4a261','#2a9d8f','#e76f51','#264653','#6c757d']}] } });
</script>
