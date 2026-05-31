<?php defined('BASEPATH') OR exit('Acesso directo ao script não permitido'); ?>
<div class="cartao-auth">
    <div class="card"><div class="card-body p-4">
        <h4 class="fw-bold text-center mb-3">Recuperar palavra-passe</h4>
        <p class="text-muted small text-center">Indique o e-mail associado à sua conta.</p>
        <?= validation_errors('<div class="alert alert-danger py-2 small">', '</div>') ?>
        <?= form_open('recuperar') ?>
            <div class="mb-3">
                <label class="form-label">E-mail</label>
                <input type="email" name="email" class="form-control" value="<?= set_value('email') ?>" required>
            </div>
            <button class="btn btn-principal w-100">Enviar instruções</button>
        <?= form_close() ?>
        <p class="text-center small mt-3 mb-0"><a href="<?= site_url('login') ?>">Voltar ao início de sessão</a></p>
    </div></div>
</div>
