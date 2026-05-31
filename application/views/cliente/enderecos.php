<?php defined('BASEPATH') OR exit('Acesso directo ao script não permitido'); ?>
<h4 class="mb-4">Os meus endereços</h4>
<div class="row g-3">
    <div class="col-lg-5">
        <div class="card"><div class="card-body">
            <h6 class="mb-3">Novo endereço</h6>
            <?= validation_errors('<div class="alert alert-danger py-2 small">', '</div>') ?>
            <?= form_open('cliente/enderecos') ?>
                <div class="mb-2"><label class="form-label small">Etiqueta</label>
                    <input type="text" name="etiqueta" class="form-control" placeholder="Casa, Trabalho…"></div>
                <div class="mb-2"><label class="form-label small">Endereço *</label>
                    <input type="text" name="endereco" class="form-control" value="<?= set_value('endereco') ?>" required></div>
                <div class="mb-2">
                    <label class="form-label small d-flex justify-content-between">Localização GPS
                        <button type="button" class="btn btn-sm btn-outline-secondary py-0" onclick="capturarLocalizacao('lat','lng',function(la,ln){if(mp){mp.marcador.setLatLng([la,ln]);mp.mapa.setView([la,ln],15);}})"><i class="bi bi-geo-alt"></i></button>
                    </label>
                    <div id="mapa-end" class="mapa" style="height:200px"></div>
                    <input type="hidden" name="latitude" id="lat"><input type="hidden" name="longitude" id="lng">
                </div>
                <div class="form-check mb-3"><input class="form-check-input" type="checkbox" name="principal" id="pp" value="1"><label class="form-check-label small" for="pp">Definir como principal</label></div>
                <button class="btn btn-principal w-100">Guardar endereço</button>
            <?= form_close() ?>
        </div></div>
    </div>
    <div class="col-lg-7">
        <?php if (empty($enderecos)): ?>
            <p class="text-muted">Sem endereços guardados.</p>
        <?php else: foreach ($enderecos as $en): ?>
            <div class="card mb-2"><div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <strong><?= e($en['etiqueta']) ?></strong>
                    <?php if ($en['principal']): ?><span class="badge bg-success">Principal</span><?php endif; ?>
                    <div class="small text-muted"><?= e($en['endereco']) ?></div>
                </div>
                <a href="<?= site_url('cliente/endereco_remover/' . $en['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Remover endereço?')"><i class="bi bi-trash"></i></a>
            </div></div>
        <?php endforeach; endif; ?>
    </div>
</div>
<script>
let mp = iniciarMapa('mapa-end', null, null, true, function(la,ln){document.getElementById('lat').value=la;document.getElementById('lng').value=ln;});
</script>
