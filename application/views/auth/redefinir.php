<?php defined('BASEPATH') OR exit('Acesso directo ao script não permitido'); ?>
<div class="cartao-auth">
    <div class="card"><div class="card-body p-4">
        <h4 class="fw-bold text-center mb-3">Definir nova palavra-passe</h4>
        <?= validation_errors('<div class="alert alert-danger py-2 small">', '</div>') ?>
        <?= form_open('recuperar/' . $token) ?>
            <div class="mb-3">
                <label class="form-label">Nova palavra-passe</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Confirmar palavra-passe</label>
                <input type="password" name="password2" class="form-control" required>
            </div>
            <button class="btn btn-principal w-100">Guardar</button>
        <?= form_close() ?>
    </div></div>
</div>
