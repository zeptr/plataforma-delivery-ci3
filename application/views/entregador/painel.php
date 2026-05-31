<?php defined('BASEPATH') OR exit('Acesso directo ao script não permitido'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Olá, <?= e($nome_actual) ?></h4>
    <a href="<?= site_url('entregador/disponibilidade') ?>" class="btn btn-<?= $entregador['disponivel'] ? 'success' : 'outline-secondary' ?>">
        <i class="bi bi-<?= $entregador['disponivel'] ? 'toggle-on' : 'toggle-off' ?>"></i>
        <?= $entregador['disponivel'] ? 'Disponível' : 'Indisponível' ?>
    </a>
</div>

<?php if (!$entregador['documentos_aprovados']): ?>
    <div class="alert alert-warning"><i class="bi bi-exclamation-triangle"></i> Os seus documentos ainda não foram aprovados. Não poderá aceitar entregas até à aprovação. <a href="<?= site_url('entregador/documentos') ?>">Carregar documentos</a></div>
<?php endif; ?>

<div class="row g-3 mb-4">
    <?php foreach ([
        ['Entregas em curso', $em_curso, 'truck', 'warning', 'entregador/entregas'],
        ['Entregas concluídas', $concluidas, 'check2-circle', 'success', 'entregador/entregas'],
        ['Pedidos disponíveis', $disponiveis, 'inbox', 'primary', 'entregador/disponiveis'],
    ] as [$r, $v, $i, $cor, $url]): ?>
        <div class="col-md-4"><a href="<?= site_url($url) ?>" class="text-decoration-none">
            <div class="card cartao-estatistica h-100"><div class="card-body d-flex justify-content-between align-items-center">
                <div><div class="text-muted small"><?= $r ?></div><div class="valor text-<?= $cor ?>"><?= $v ?></div></div>
                <i class="bi bi-<?= $i ?> icone text-<?= $cor ?>"></i>
            </div></div>
        </a></div>
    <?php endforeach; ?>
</div>

<div class="card"><div class="card-header bg-white fw-semibold">Entregas recentes</div>
<div class="table-responsive"><table class="table table-hover align-middle mb-0">
    <thead class="table-light"><tr><th>#</th><th>Loja</th><th>Cliente</th><th>Estado</th><th></th></tr></thead>
    <tbody>
    <?php if (empty($ultimos)): ?>
        <tr><td colspan="5" class="text-center text-muted py-4">Sem entregas ainda.</td></tr>
    <?php else: foreach ($ultimos as $p): ?>
        <tr><td>#<?= $p['id'] ?></td><td><?= e($p['nome_empresa']) ?></td><td><?= e($p['cliente_nome']) ?></td>
            <td><span class="badge bg-<?= estado_badge($p['estado']) ?>"><?= Estado_pedido::rotulo($p['estado']) ?></span></td>
            <td><a href="<?= site_url('entregador/entrega/' . $p['id']) ?>" class="btn btn-sm btn-outline-primary">Ver</a></td></tr>
    <?php endforeach; endif; ?>
    </tbody>
</table></div></div>
