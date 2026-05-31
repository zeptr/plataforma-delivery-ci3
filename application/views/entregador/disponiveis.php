<?php defined('BASEPATH') OR exit('Acesso directo ao script não permitido'); ?>
<h4 class="mb-4">Pedidos disponíveis para entrega</h4>
<?php if (!$aprovado): ?>
    <div class="alert alert-warning"><i class="bi bi-lock"></i> Precisa de ter os documentos aprovados para aceitar entregas.</div>
<?php endif; ?>
<div class="row g-3">
    <?php if (empty($pedidos)): ?>
        <p class="text-muted">Não há pedidos disponíveis de momento. Volte mais tarde.</p>
    <?php else: foreach ($pedidos as $p): ?>
        <div class="col-md-6 col-lg-4"><div class="card h-100"><div class="card-body">
            <div class="d-flex justify-content-between mb-2">
                <span class="fw-semibold">Pedido #<?= $p['id'] ?></span>
                <span class="badge bg-success"><?= moeda($p['total']) ?></span>
            </div>
            <p class="small mb-1"><i class="bi bi-shop"></i> <?= e($p['nome_empresa']) ?></p>
            <p class="small mb-1"><i class="bi bi-person"></i> <?= e($p['cliente_nome']) ?></p>
            <p class="small text-muted mb-3"><i class="bi bi-geo-alt"></i> <?= e($p['endereco_entrega']) ?></p>
            <a href="<?= site_url('entregador/aceitar/' . $p['id']) ?>" class="btn btn-principal btn-sm w-100 <?= $aprovado ? '' : 'disabled' ?>"
               onclick="return confirm('Aceitar esta entrega?')"><i class="bi bi-check-lg"></i> Aceitar entrega</a>
        </div></div></div>
    <?php endforeach; endif; ?>
</div>
