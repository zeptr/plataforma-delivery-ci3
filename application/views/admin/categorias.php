<?php defined('BASEPATH') OR exit('Acesso directo ao script não permitido'); ?>
<h4 class="mb-4">Categorias de loja</h4>
<div class="row g-3">
    <div class="col-md-4">
        <div class="card"><div class="card-body">
            <h6>Nova categoria</h6>
            <?= form_open('admin/categorias') ?>
                <div class="mb-2"><input type="text" name="nome" class="form-control" placeholder="Nome da categoria" required></div>
                <button class="btn btn-principal w-100">Adicionar</button>
            <?= form_close() ?>
        </div></div>
    </div>
    <div class="col-md-8">
        <div class="card"><ul class="list-group list-group-flush">
            <?php foreach ($categorias as $c): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-<?= e($c['icone'] ?: 'tag') ?>"></i> <?= e($c['nome']) ?></span>
                    <?= $c['ativo'] ? '<span class="badge bg-success">Activa</span>' : '<span class="badge bg-secondary">Inactiva</span>' ?>
                </li>
            <?php endforeach; ?>
        </ul></div>
    </div>
</div>
