<?php defined('BASEPATH') OR exit('Acesso directo ao script não permitido'); ?>
<h4 class="mb-2">Os meus documentos</h4>
<p class="text-muted small mb-4">Estado:
    <?= $entregador['documentos_aprovados']
        ? '<span class="badge bg-success">Aprovados</span>'
        : '<span class="badge bg-warning text-dark">Pendentes de aprovação</span>' ?>
</p>
<div class="card"><div class="card-body">
    <?= form_open_multipart('entregador/documentos') ?>
        <div class="row g-3">
            <?php
            $campos = [
                'foto_perfil' => 'Foto de perfil',
                'selfie' => 'Selfie de verificação',
                'carta_frente' => 'Carta de condução (frente)',
                'carta_verso' => 'Carta de condução (verso)',
                'seguro' => 'Seguro do veículo',
            ];
            foreach ($campos as $campo => $rotulo): ?>
                <div class="col-md-6">
                    <label class="form-label"><?= $rotulo ?>
                        <?php if (!empty($entregador[$campo])): ?><span class="badge bg-success">Carregado</span><?php endif; ?>
                    </label>
                    <input type="file" name="<?= $campo ?>" class="form-control" accept="image/*,application/pdf">
                </div>
            <?php endforeach; ?>
        </div>
        <button class="btn btn-principal mt-4">Carregar documentos</button>
    <?= form_close() ?>
</div></div>
<div class="card mt-3"><div class="card-body">
    <h6>Dados do veículo</h6>
    <dl class="row small mb-0">
        <dt class="col-4">BI/Passaporte</dt><dd class="col-8"><?= e($entregador['bi_passaporte']) ?></dd>
        <dt class="col-4">Veículo</dt><dd class="col-8"><?= e(ucfirst($entregador['tipo_veiculo'])) ?> <?= e($entregador['marca']) ?> <?= e($entregador['modelo']) ?> (<?= e($entregador['cor']) ?>)</dd>
        <dt class="col-4">Matrícula</dt><dd class="col-8"><?= e($entregador['matricula']) ?></dd>
        <dt class="col-4">Carta n.º</dt><dd class="col-8"><?= e($entregador['carta_numero']) ?> · Cat. <?= e($entregador['carta_categoria']) ?> · Válida até <?= data_pt($entregador['carta_validade'], false) ?></dd>
    </dl>
</div></div>
