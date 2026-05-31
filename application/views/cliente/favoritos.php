<?php defined('BASEPATH') OR exit('Acesso directo ao script não permitido'); ?>
<h4 class="mb-4">Lojas favoritas</h4>
<div class="row g-3">
    <?php if (empty($lojas)): ?>
        <p class="text-muted">Ainda não tem lojas favoritas. <a href="<?= site_url('cliente/lojas') ?>">Explorar</a></p>
    <?php else: foreach ($lojas as $l): ?>
        <div class="col-md-6 col-lg-4"><div class="card h-100"><div class="card-body">
            <h6><?= e($l['nome_empresa']) ?></h6>
            <p class="small text-muted mb-2"><i class="bi bi-geo-alt"></i> <?= e($l['endereco']) ?></p>
            <div class="d-flex gap-2">
                <a href="<?= site_url('cliente/loja/' . $l['id']) ?>" class="btn btn-principal btn-sm flex-grow-1">Ver produtos</a>
                <a href="<?= site_url('cliente/favorito_alternar/' . $l['id']) ?>" class="btn btn-outline-danger btn-sm"><i class="bi bi-heart-fill"></i></a>
            </div>
        </div></div></div>
    <?php endforeach; endif; ?>
</div>
