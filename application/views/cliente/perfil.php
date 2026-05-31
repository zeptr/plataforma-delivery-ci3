<?php defined('BASEPATH') OR exit('Acesso directo ao script não permitido'); ?>
<h4 class="mb-4">O meu perfil</h4>
<div class="row"><div class="col-lg-6">
    <div class="card"><div class="card-body">
        <?= form_open('cliente/perfil') ?>
            <div class="mb-3"><label class="form-label">Nome completo</label>
                <input type="text" name="nome" class="form-control" value="<?= e($usuario['nome']) ?>" required></div>
            <div class="mb-3"><label class="form-label">E-mail</label>
                <input type="email" class="form-control" value="<?= e($usuario['email']) ?>" disabled></div>
            <div class="mb-3"><label class="form-label">Telefone</label>
                <input type="text" name="telefone" class="form-control" value="<?= e($usuario['telefone']) ?>"></div>
            <div class="mb-3"><label class="form-label">Género</label>
                <select name="genero" class="form-select">
                    <?php foreach (['masculino' => 'Masculino', 'feminino' => 'Feminino', 'outro' => 'Outro'] as $v => $r): ?>
                        <option value="<?= $v ?>" <?= $usuario['genero'] === $v ? 'selected' : '' ?>><?= $r ?></option>
                    <?php endforeach; ?>
                </select></div>
            <div class="mb-3"><label class="form-label">Nova palavra-passe <span class="text-muted small">(deixe em branco para manter)</span></label>
                <input type="password" name="password" class="form-control"></div>
            <button class="btn btn-principal">Guardar alterações</button>
        <?= form_close() ?>
    </div></div>
</div></div>
