<?php defined('BASEPATH') OR exit('Acesso directo ao script não permitido'); ?>
<h4 class="mb-4">Finalizar pedido</h4>
<?= validation_errors('<div class="alert alert-danger py-2 small">', '</div>') ?>
<?= form_open('cliente/checkout', ['id' => 'form-checkout']) ?>
<div class="row g-3">
    <div class="col-lg-7">
        <div class="card mb-3"><div class="card-body">
            <h6 class="mb-3"><i class="bi bi-geo-alt"></i> Endereço de entrega</h6>
            <?php if (empty($enderecos)): ?>
                <p class="text-muted small">Não tem endereços. <a href="<?= site_url('cliente/enderecos') ?>">Adicionar endereço</a></p>
            <?php else: foreach ($enderecos as $en): ?>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="endereco_id" value="<?= $en['id'] ?>" id="end<?= $en['id'] ?>" <?= $en['principal'] ? 'checked' : '' ?>>
                    <label class="form-check-label" for="end<?= $en['id'] ?>">
                        <strong><?= e($en['etiqueta']) ?></strong> — <?= e($en['endereco']) ?>
                    </label>
                </div>
            <?php endforeach; endif; ?>
        </div></div>

        <div class="card mb-3"><div class="card-body">
            <h6 class="mb-3"><i class="bi bi-credit-card"></i> Método de pagamento</h6>
            <?php foreach ([
                'mpesa' => 'M-Pesa', 'emola' => 'e-Mola', 'dinheiro' => 'Dinheiro na entrega', 'cartao' => 'Cartão bancário'
            ] as $val => $rot): ?>
                <div class="form-check mb-2">
                    <input class="form-check-input metodo-pg" type="radio" name="metodo_pagamento" value="<?= $val ?>" id="pg<?= $val ?>" <?= $val === 'dinheiro' ? 'checked' : '' ?>>
                    <label class="form-check-label" for="pg<?= $val ?>"><?= $rot ?></label>
                </div>
            <?php endforeach; ?>
            <div class="mt-2" id="campo-numero" style="display:none">
                <label class="form-label small">Número de telemóvel (carteira)</label>
                <input type="text" name="numero_movel" class="form-control" placeholder="84xxxxxxx / 86xxxxxxx">
            </div>
        </div></div>

        <div class="card"><div class="card-body">
            <label class="form-label">Observações (opcional)</label>
            <textarea name="observacoes" class="form-control" rows="2" placeholder="Ex.: deixar na portaria"></textarea>
        </div></div>
    </div>

    <div class="col-lg-5">
        <div class="card"><div class="card-body">
            <h6 class="mb-3"><i class="bi bi-shop"></i> <?= e($loja['nome_empresa']) ?></h6>
            <?php foreach ($itens as $it): ?>
                <div class="d-flex justify-content-between small mb-1">
                    <span><?= $it['quantidade'] ?>× <?= e($it['nome']) ?></span>
                    <span><?= moeda($it['preco'] * $it['quantidade']) ?></span>
                </div>
            <?php endforeach; ?>
            <hr>
            <div class="d-flex justify-content-between mb-1"><span>Subtotal</span><span><?= moeda($subtotal) ?></span></div>
            <div class="d-flex justify-content-between mb-1"><span>Taxa de entrega</span><span><?= moeda($taxa) ?></span></div>
            <div class="d-flex justify-content-between fw-bold fs-5 mt-2"><span>Total</span><span class="text-principal"><?= moeda($total) ?></span></div>
            <button type="submit" class="btn btn-principal w-100 mt-3" <?= empty($enderecos) ? 'disabled' : '' ?>>Confirmar pedido</button>
        </div></div>
    </div>
</div>
<?= form_close() ?>
<script>
function toggleNumero() {
    const m = document.querySelector('.metodo-pg:checked').value;
    document.getElementById('campo-numero').style.display = (m === 'mpesa' || m === 'emola') ? 'block' : 'none';
}
document.querySelectorAll('.metodo-pg').forEach(r => r.addEventListener('change', toggleNumero));
toggleNumero();
</script>
