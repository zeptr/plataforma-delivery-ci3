<?php defined('BASEPATH') OR exit('Acesso directo ao script não permitido'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Pedidos</h4>
    <form method="get"><select name="estado" class="form-select form-select-sm" onchange="this.form.submit()">
        <option value="">Todos os estados</option>
        <?php foreach ($estados as $val => $rot): ?>
            <option value="<?= $val ?>" <?= $estado_sel === $val ? 'selected' : '' ?>><?= $rot ?></option>
        <?php endforeach; ?>
    </select></form>
</div>
<div class="card"><div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr><th>#</th><th>Cliente</th><th>Total</th><th>Pagamento</th><th>Estado</th><th>Data</th><th></th></tr></thead>
        <tbody>
        <?php if (empty($pedidos)): ?>
            <tr><td colspan="7" class="text-center text-muted py-4">Sem pedidos.</td></tr>
        <?php else: foreach ($pedidos as $p): ?>
            <tr><td>#<?= $p['id'] ?></td><td><?= e($p['cliente_nome']) ?></td><td><?= moeda($p['total']) ?></td>
                <td class="small"><?= Pagamento_proc::rotulo($p['metodo_pagamento']) ?></td>
                <td><span class="badge bg-<?= estado_badge($p['estado']) ?>"><?= Estado_pedido::rotulo($p['estado']) ?></span></td>
                <td class="small text-muted"><?= data_pt($p['criado_em']) ?></td>
                <td><a href="<?= site_url('loja/pedido/' . $p['id']) ?>" class="btn btn-sm btn-outline-primary">Gerir</a></td></tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
</div></div>
