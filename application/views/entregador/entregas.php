<?php defined('BASEPATH') OR exit('Acesso directo ao script não permitido'); ?>
<h4 class="mb-4">As minhas entregas</h4>
<div class="card"><div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr><th>#</th><th>Loja</th><th>Cliente</th><th>Endereço</th><th>Total</th><th>Estado</th><th></th></tr></thead>
        <tbody>
        <?php if (empty($pedidos)): ?>
            <tr><td colspan="7" class="text-center text-muted py-4">Ainda não tem entregas.</td></tr>
        <?php else: foreach ($pedidos as $p): ?>
            <tr><td>#<?= $p['id'] ?></td><td><?= e($p['nome_empresa']) ?></td><td><?= e($p['cliente_nome']) ?></td>
                <td class="small text-muted"><?= e($p['endereco_entrega']) ?></td><td><?= moeda($p['total']) ?></td>
                <td><span class="badge bg-<?= estado_badge($p['estado']) ?>"><?= Estado_pedido::rotulo($p['estado']) ?></span></td>
                <td><a href="<?= site_url('entregador/entrega/' . $p['id']) ?>" class="btn btn-sm btn-outline-primary">Ver</a></td></tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
</div></div>
