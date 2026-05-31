<?php defined('BASEPATH') OR exit('Acesso directo ao script não permitido'); ?>
<h4 class="mb-4">Olá, <?= e($nome_actual) ?> 👋</h4>
<div class="row g-3 mb-4">
    <?php foreach ([
        ['Pedidos feitos', $total_pedidos, 'box-seam', 'primary', 'cliente/pedidos'],
        ['Pedidos em curso', $em_curso, 'hourglass-split', 'warning', 'cliente/pedidos'],
        ['Lojas favoritas', $favoritos, 'heart', 'danger', 'cliente/favoritos'],
        ['Explorar lojas', '<i class="bi bi-arrow-right"></i>', 'shop', 'success', 'cliente/lojas'],
    ] as [$r, $v, $i, $cor, $url]): ?>
        <div class="col-6 col-md-3"><a href="<?= site_url($url) ?>" class="text-decoration-none">
            <div class="card cartao-estatistica h-100"><div class="card-body d-flex justify-content-between align-items-center">
                <div><div class="text-muted small"><?= $r ?></div><div class="valor text-<?= $cor ?>"><?= $v ?></div></div>
                <i class="bi bi-<?= $i ?> icone text-<?= $cor ?>"></i>
            </div></div>
        </a></div>
    <?php endforeach; ?>
</div>

<div class="card"><div class="card-header bg-white fw-semibold">Últimos pedidos</div>
<div class="table-responsive"><table class="table table-hover align-middle mb-0">
    <thead class="table-light"><tr><th>#</th><th>Loja</th><th>Total</th><th>Estado</th><th>Data</th><th></th></tr></thead>
    <tbody>
    <?php if (empty($ultimos)): ?>
        <tr><td colspan="6" class="text-center text-muted py-4">Ainda não fez pedidos. <a href="<?= site_url('cliente/lojas') ?>">Explorar lojas</a></td></tr>
    <?php else: foreach ($ultimos as $p): ?>
        <tr><td>#<?= $p['id'] ?></td><td><?= e($p['nome_empresa']) ?></td><td><?= moeda($p['total']) ?></td>
            <td><span class="badge bg-<?= estado_badge($p['estado']) ?>"><?= Estado_pedido::rotulo($p['estado']) ?></span></td>
            <td class="small text-muted"><?= data_pt($p['criado_em']) ?></td>
            <td><a href="<?= site_url('cliente/acompanhar/' . $p['id']) ?>" class="btn btn-sm btn-outline-primary">Acompanhar</a></td></tr>
    <?php endforeach; endif; ?>
    </tbody>
</table></div></div>
