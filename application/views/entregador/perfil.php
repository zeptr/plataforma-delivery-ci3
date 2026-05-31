<?php defined('BASEPATH') OR exit('Acesso directo ao script não permitido'); ?>
<h4 class="mb-4">O meu perfil</h4>
<div class="card"><div class="card-body">
    <?= form_open('entregador/perfil') ?>
        <h6 class="text-secondary">Dados pessoais</h6>
        <div class="row g-3 mb-3">
            <div class="col-md-6"><label class="form-label">Nome</label>
                <input type="text" name="nome" class="form-control" value="<?= e($entregador['nome']) ?>" required></div>
            <div class="col-md-6"><label class="form-label">Telefone</label>
                <input type="text" name="telefone" class="form-control" value="<?= e($entregador['telefone']) ?>"></div>
        </div>
        <h6 class="text-secondary">Veículo</h6>
        <div class="row g-3">
            <div class="col-md-4"><label class="form-label">Tipo</label>
                <select name="tipo_veiculo" class="form-select">
                    <?php foreach (['mota' => 'Mota', 'bicicleta' => 'Bicicleta', 'carro' => 'Carro'] as $v => $r): ?>
                        <option value="<?= $v ?>" <?= $entregador['tipo_veiculo'] === $v ? 'selected' : '' ?>><?= $r ?></option>
                    <?php endforeach; ?>
                </select></div>
            <div class="col-md-4"><label class="form-label">Marca</label><input type="text" name="marca" class="form-control" value="<?= e($entregador['marca']) ?>"></div>
            <div class="col-md-4"><label class="form-label">Modelo</label><input type="text" name="modelo" class="form-control" value="<?= e($entregador['modelo']) ?>"></div>
            <div class="col-md-4"><label class="form-label">Cor</label><input type="text" name="cor" class="form-control" value="<?= e($entregador['cor']) ?>"></div>
            <div class="col-md-4"><label class="form-label">Matrícula</label><input type="text" name="matricula" class="form-control" value="<?= e($entregador['matricula']) ?>"></div>
            <div class="col-md-4"><label class="form-label">Ano de fabrico</label><input type="number" name="ano_fabrico" class="form-control" value="<?= e($entregador['ano_fabrico']) ?>"></div>
        </div>
        <button class="btn btn-principal mt-4">Guardar alterações</button>
    <?= form_close() ?>
</div></div>
