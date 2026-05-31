<?php defined('BASEPATH') OR exit('Acesso directo ao script não permitido'); ?>
<div class="cartao-auth" style="max-width: 640px;">
    <div class="card">
        <div class="card-body p-4">
            <h4 class="fw-bold text-center mb-1">Criar conta</h4>
            <p class="text-muted text-center small">Escolha o tipo de conta e preencha os dados</p>

            <?= validation_errors('<div class="alert alert-danger py-2 small">', '</div>') ?>

            <div class="btn-group w-100 mb-4" role="group">
                <?php foreach (['cliente' => 'Cliente', 'loja' => 'Loja', 'entregador' => 'Entregador'] as $val => $rot): ?>
                    <input type="radio" class="btn-check sel-perfil" name="sel_perfil" id="p_<?= $val ?>"
                           value="<?= $val ?>" <?= $perfil_sel === $val ? 'checked' : '' ?>>
                    <label class="btn btn-outline-danger" for="p_<?= $val ?>"><?= $rot ?></label>
                <?php endforeach; ?>
            </div>

            <?= form_open('registo', ['id' => 'form-registo']) ?>
                <input type="hidden" name="perfil" id="campo-perfil" value="<?= e($perfil_sel) ?>">

                <!-- Campos comuns -->
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nome completo *</label>
                        <input type="text" name="nome" class="form-control" value="<?= set_value('nome') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Telefone *</label>
                        <input type="text" name="telefone" class="form-control" value="<?= set_value('telefone') ?>" placeholder="84xxxxxxx" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">E-mail *</label>
                        <input type="email" name="email" class="form-control" value="<?= set_value('email') ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Palavra-passe *</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Confirmar *</label>
                        <input type="password" name="password2" class="form-control" required>
                    </div>
                </div>

                <!-- CLIENTE -->
                <div class="grupo-perfil" data-perfil="cliente">
                    <hr>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Género *</label>
                            <select name="genero" class="form-select">
                                <option value="masculino">Masculino</option>
                                <option value="feminino">Feminino</option>
                                <option value="outro">Outro</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Endereço *</label>
                            <input type="text" name="endereco" class="form-control" value="<?= set_value('endereco') ?>">
                        </div>
                    </div>
                </div>

                <!-- LOJA -->
                <div class="grupo-perfil" data-perfil="loja">
                    <hr>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nome da empresa *</label>
                            <input type="text" name="nome_empresa" class="form-control" value="<?= set_value('nome_empresa') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Categoria *</label>
                            <select name="categoria_id" class="form-select">
                                <option value="">— escolher —</option>
                                <?php foreach ($categorias as $c): ?>
                                    <option value="<?= $c['id'] ?>"><?= e($c['nome']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">NUIT *</label>
                            <input type="text" name="nuit" class="form-control" value="<?= set_value('nuit') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Taxa de entrega (MT)</label>
                            <input type="number" step="0.01" name="taxa_entrega" class="form-control" value="50">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Horário</label>
                            <input type="text" name="horario" class="form-control" placeholder="08:00 - 22:00">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Endereço *</label>
                            <input type="text" name="endereco" class="form-control" value="<?= set_value('endereco') ?>">
                        </div>
                    </div>
                </div>

                <!-- ENTREGADOR -->
                <div class="grupo-perfil" data-perfil="entregador">
                    <hr>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">BI / Passaporte *</label>
                            <input type="text" name="bi_passaporte" class="form-control" value="<?= set_value('bi_passaporte') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tipo de veículo *</label>
                            <select name="tipo_veiculo" class="form-select">
                                <option value="mota">Mota</option>
                                <option value="bicicleta">Bicicleta</option>
                                <option value="carro">Carro</option>
                            </select>
                        </div>
                        <div class="col-md-4"><label class="form-label">Marca</label><input type="text" name="marca" class="form-control"></div>
                        <div class="col-md-4"><label class="form-label">Modelo</label><input type="text" name="modelo" class="form-control"></div>
                        <div class="col-md-4"><label class="form-label">Cor</label><input type="text" name="cor" class="form-control"></div>
                        <div class="col-md-4"><label class="form-label">Matrícula *</label><input type="text" name="matricula" class="form-control" value="<?= set_value('matricula') ?>"></div>
                        <div class="col-md-4"><label class="form-label">Ano de fabrico</label><input type="number" name="ano_fabrico" class="form-control"></div>
                        <div class="col-md-4"><label class="form-label">Nº carta de condução</label><input type="text" name="carta_numero" class="form-control"></div>
                        <div class="col-md-4"><label class="form-label">Categoria da carta</label><input type="text" name="carta_categoria" class="form-control" placeholder="B"></div>
                        <div class="col-md-4"><label class="form-label">Validade da carta</label><input type="date" name="carta_validade" class="form-control"></div>
                        <p class="small text-muted mb-0">As fotografias dos documentos são carregadas após o registo, na área «Documentos».</p>
                    </div>
                </div>

                <!-- Localização GPS (cliente/loja) -->
                <div class="grupo-perfil" data-perfil="cliente loja">
                    <hr>
                    <label class="form-label d-flex justify-content-between">
                        <span>Localização (GPS)</span>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="capturarLocalizacao('lat','lng', function(lat,lng){ if(mapaReg){ mapaReg.marcador.setLatLng([lat,lng]); mapaReg.mapa.setView([lat,lng],15); } })">
                            <i class="bi bi-geo-alt"></i> Usar a minha localização
                        </button>
                    </label>
                    <div id="mapa-registo" class="mapa" style="height:220px;"></div>
                    <input type="hidden" name="latitude" id="lat" value="<?= set_value('latitude') ?>">
                    <input type="hidden" name="longitude" id="lng" value="<?= set_value('longitude') ?>">
                </div>

                <button type="submit" class="btn btn-principal w-100 mt-4">Criar conta</button>
            <?= form_close() ?>

            <p class="text-center small mt-3 mb-0">Já tem conta? <a href="<?= site_url('login') ?>">Iniciar sessão</a></p>
        </div>
    </div>
</div>

<script>
let mapaReg = null;
function mostrarGrupos(perfil) {
    document.getElementById('campo-perfil').value = perfil;
    document.querySelectorAll('.grupo-perfil').forEach(g => {
        g.style.display = g.dataset.perfil.split(' ').includes(perfil) ? 'block' : 'none';
        // Desactiva campos escondidos para não interferirem na validação obrigatória.
        g.querySelectorAll('input,select').forEach(i => i.disabled = (g.style.display === 'none'));
    });
    if (perfil !== 'entregador' && !mapaReg) {
        mapaReg = iniciarMapa('mapa-registo',
            document.getElementById('lat').value, document.getElementById('lng').value, true,
            (lat, lng) => { document.getElementById('lat').value = lat; document.getElementById('lng').value = lng; });
    }
    setTimeout(() => mapaReg && mapaReg.mapa.invalidateSize(), 250);
}
document.querySelectorAll('.sel-perfil').forEach(r => r.addEventListener('change', e => mostrarGrupos(e.target.value)));
mostrarGrupos(document.querySelector('.sel-perfil:checked').value);
</script>
