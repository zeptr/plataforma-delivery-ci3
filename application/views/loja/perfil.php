<?php defined('BASEPATH') OR exit('Acesso directo ao script não permitido'); ?>
<h4 class="mb-4">Perfil da loja</h4>
<div class="card"><div class="card-body">
    <?= form_open_multipart('loja/perfil') ?>
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Nome da empresa</label>
                <input type="text" name="nome_empresa" class="form-control" value="<?= e($loja['nome_empresa']) ?>" required></div>
            <div class="col-md-3"><label class="form-label">Categoria</label>
                <input type="text" class="form-control" value="<?= e($loja['categoria_nome']) ?>" disabled></div>
            <div class="col-md-3"><label class="form-label">NUIT</label>
                <input type="text" class="form-control" value="<?= e($loja['nuit']) ?>" disabled></div>
            <div class="col-md-4"><label class="form-label">Telefone</label>
                <input type="text" name="telefone" class="form-control" value="<?= e($loja['telefone']) ?>"></div>
            <div class="col-md-4"><label class="form-label">Horário</label>
                <input type="text" name="horario" class="form-control" value="<?= e($loja['horario']) ?>" placeholder="08:00 - 22:00"></div>
            <div class="col-md-4"><label class="form-label">Taxa de entrega (MT)</label>
                <input type="number" step="0.01" name="taxa_entrega" class="form-control" value="<?= e($loja['taxa_entrega']) ?>"></div>
            <div class="col-12"><label class="form-label">Endereço</label>
                <input type="text" name="endereco" class="form-control" value="<?= e($loja['endereco']) ?>"></div>
            <div class="col-md-6"><label class="form-label">Logotipo</label>
                <input type="file" name="logotipo" class="form-control" accept="image/*">
                <?php if (!empty($loja['logotipo'])): ?><small class="text-muted">Actual: <?= e($loja['logotipo']) ?></small><?php endif; ?></div>
            <div class="col-12">
                <label class="form-label d-flex justify-content-between">Localização GPS
                    <button type="button" class="btn btn-sm btn-outline-secondary py-0" onclick="capturarLocalizacao('lat','lng',function(la,ln){if(mp){mp.marcador.setLatLng([la,ln]);mp.mapa.setView([la,ln],15);}})"><i class="bi bi-geo-alt"></i> Minha localização</button>
                </label>
                <div id="mapa-loja" class="mapa" style="height:260px"></div>
                <input type="hidden" name="latitude" id="lat" value="<?= e($loja['latitude']) ?>">
                <input type="hidden" name="longitude" id="lng" value="<?= e($loja['longitude']) ?>">
            </div>
        </div>
        <button class="btn btn-principal mt-4">Guardar alterações</button>
    <?= form_close() ?>
</div></div>
<script>
let mp = iniciarMapa('mapa-loja', document.getElementById('lat').value, document.getElementById('lng').value, true,
    function(la,ln){document.getElementById('lat').value=la;document.getElementById('lng').value=ln;});
</script>
