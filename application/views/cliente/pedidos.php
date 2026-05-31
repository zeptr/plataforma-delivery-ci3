<?php defined('BASEPATH') OR exit('Acesso directo ao script não permitido'); ?>
<h4 class="mb-4">Os meus pedidos</h4>
<div class="card"><div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr><th>#</th><th>Loja</th><th>Total</th><th>Pagamento</th><th>Estado</th><th>Data</th><th></th></tr></thead>
        <tbody>
        <?php if (empty($pedidos)): ?>
            <tr><td colspan="7" class="text-center text-muted py-4">Ainda não fez pedidos.</td></tr>
        <?php else: foreach ($pedidos as $p): ?>
            <tr>
                <td>#<?= $p['id'] ?></td><td><?= e($p['nome_empresa']) ?></td><td><?= moeda($p['total']) ?></td>
                <td class="small"><?= Pagamento_proc::rotulo($p['metodo_pagamento']) ?></td>
                <td><span class="badge bg-<?= estado_badge($p['estado']) ?>"><?= Estado_pedido::rotulo($p['estado']) ?></span></td>
                <td class="small text-muted"><?= data_pt($p['criado_em']) ?></td>
                <td><a href="<?= site_url('cliente/acompanhar/' . $p['id']) ?>" class="btn btn-sm btn-outline-primary">Acompanhar</a></td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
</div></div>
