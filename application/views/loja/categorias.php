<?php defined('BASEPATH') OR exit('Acesso directo ao script não permitido'); ?>
<h4 class="mb-4">Categorias de produto</h4>
<div class="row g-3">
    <div class="col-md-4"><div class="card"><div class="card-body">
        <h6>Nova categoria</h6>
        <?= form_open('loja/categorias') ?>
            <div class="mb-2"><input type="text" name="nome" class="form-control" placeholder="Ex.: Bebidas" required></div>
            <button class="btn btn-principal w-100">Adicionar</button>
        <?= form_close() ?>
    </div></div></div>
    <div class="col-md-8"><div class="card"><ul class="list-group list-group-flush">
        <?php if (empty($categorias)): ?>
            <li class="list-group-item text-muted">Sem categorias.</li>
        <?php else: foreach ($categorias as $c): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span><i class="bi bi-tag"></i> <?= e($c['nome']) ?></span>
                <a href="<?= site_url('loja/categoria_remover/' . $c['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Remover categoria?')"><i class="bi bi-trash"></i></a>
            </li>
        <?php endforeach; endif; ?>
    </ul></div></div>
</div>
