<?php defined('BASEPATH') OR exit('Acesso directo ao script não permitido'); ?>
<h4 class="mb-3">Lojas</h4>
<form method="get" class="row g-2 mb-4">
    <div class="col-md-6"><input type="text" name="pesquisa" class="form-control" placeholder="Pesquisar loja…" value="<?= e($pesquisa) ?>"></div>
    <div class="col-md-4">
        <select name="categoria_id" class="form-select">
            <option value="">Todas as categorias</option>
            <?php foreach ($categorias as $c): ?>
                <option value="<?= $c['id'] ?>" <?= (string)$categoria_sel === (string)$c['id'] ? 'selected' : '' ?>><?= e($c['nome']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2 d-grid"><button class="btn btn-principal"><i class="bi bi-search"></i> Filtrar</button></div>
</form>

<div class="row g-3">
    <?php if (empty($lojas)): ?>
        <p class="text-muted">Nenhuma loja encontrada.</p>
    <?php else: foreach ($lojas as $l): ?>
        <div class="col-md-6 col-lg-4">
            <div class="card h-100"><div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="logo-loja d-flex align-items-center justify-content-center me-3"><i class="bi bi-shop fs-4 text-secondary"></i></div>
                    <div>
                        <h6 class="mb-0"><?= e($l['nome_empresa']) ?></h6>
                        <span class="badge bg-light text-dark"><?= e($l['categoria_nome']) ?></span>
                    </div>
                </div>
                <p class="small text-muted mb-2"><i class="bi bi-geo-alt"></i> <?= e($l['endereco']) ?></p>
                <p class="small mb-3"><i class="bi bi-truck"></i> Taxa: <?= moeda($l['taxa_entrega']) ?> · <i class="bi bi-clock"></i> <?= e($l['horario'] ?: 'N/D') ?></p>
                <a href="<?= site_url('cliente/loja/' . $l['id']) ?>" class="btn btn-principal btn-sm w-100">Ver produtos</a>
            </div></div>
        </div>
    <?php endforeach; endif; ?>
</div>
