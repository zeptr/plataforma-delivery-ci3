<?php defined('BASEPATH') OR exit('Acesso directo ao script não permitido'); ?>
<h4 class="mb-4"><?= e($loja['nome_empresa']) ?></h4>
<div class="row g-3 mb-4">
    <?php foreach ([
        ['Vendas', moeda($resumo['total_vendas']), 'cash-stack', 'success'],
        ['Pedidos pendentes', $pendentes, 'hourglass-split', 'warning'],
        ['Entregas concluídas', $resumo['entregas'], 'check2-circle', 'info'],
        ['Produtos', $total_produtos, 'box', 'primary'],
    ] as [$r, $v, $i, $cor]): ?>
        <div class="col-6 col-md-3"><div class="card cartao-estatistica h-100"><div class="card-body d-flex justify-content-between align-items-center">
            <div><div class="text-muted small"><?= $r ?></div><div class="valor text-<?= $cor ?>"><?= $v ?></div></div>
            <i class="bi bi-<?= $i ?> icone text-<?= $cor ?>"></i>
        </div></div></div>
    <?php endforeach; ?>
</div>

<div class="card"><div class="card-header bg-white fw-semibold d-flex justify-content-between">
    <span>Pedidos recentes</span><a href="<?= site_url('loja/pedidos') ?>" class="small">Ver todos</a></div>
<div class="table-responsive"><table class="table table-hover align-middle mb-0">
    <thead class="table-light"><tr><th>#</th><th>Cliente</th><th>Total</th><th>Estado</th><th>Data</th><th></th></tr></thead>
    <tbody>
    <?php if (empty($ultimos)): ?>
        <tr><td colspan="6" class="text-center text-muted py-4">Ainda não recebeu pedidos.</td></tr>
    <?php else: foreach ($ultimos as $p): ?>
        <tr><td>#<?= $p['id'] ?></td><td><?= e($p['cliente_nome']) ?></td><td><?= moeda($p['total']) ?></td>
            <td><span class="badge bg-<?= estado_badge($p['estado']) ?>"><?= Estado_pedido::rotulo($p['estado']) ?></span></td>
            <td class="small text-muted"><?= data_pt($p['criado_em']) ?></td>
            <td><a href="<?= site_url('loja/pedido/' . $p['id']) ?>" class="btn btn-sm btn-outline-primary">Gerir</a></td></tr>
    <?php endforeach; endif; ?>
    </tbody>
</table></div></div>
