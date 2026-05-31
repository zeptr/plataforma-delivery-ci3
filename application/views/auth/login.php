<?php defined('BASEPATH') OR exit('Acesso directo ao script não permitido'); ?>
<div class="cartao-auth">
    <div class="card">
        <div class="card-body p-4">
            <div class="text-center mb-4">
                <h3 class="fw-bold mb-1"><i class="bi bi-box-seam-fill text-principal"></i> Delivery MZ</h3>
                <p class="text-muted small mb-0">Plataforma de Gestão de Entregas</p>
            </div>

            <?= validation_errors('<div class="alert alert-danger py-2 small">', '</div>') ?>

            <?= form_open('login') ?>
                <div class="mb-3">
                    <label class="form-label">E-mail</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="email" class="form-control" value="<?= set_value('email') ?>" required autofocus>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Palavra-passe</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-principal w-100">Entrar</button>
            <?= form_close() ?>

            <div class="d-flex justify-content-between mt-3 small">
                <a href="<?= site_url('recuperar') ?>" class="text-decoration-none">Esqueceu a palavra-passe?</a>
                <a href="<?= site_url('registo') ?>" class="text-decoration-none">Criar conta</a>
            </div>
        </div>
    </div>
    <p class="text-center text-white-50 small mt-3 mb-0">© <?= date('Y') ?> Delivery MZ — Moçambique</p>
</div>
