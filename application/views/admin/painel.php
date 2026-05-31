<?php defined('BASEPATH') OR exit('Acesso directo ao script não permitido'); ?>
<h4 class="mb-4">Painel administrativo</h4>

<div class="row g-3 mb-4">
    <?php
    $cartoes = [
        ['Vendas totais', moeda($resumo['total_vendas']), 'cash-stack', 'success'],
        ['Pedidos', $resumo['total_pedidos'], 'box-seam', 'primary'],
        ['Entregas concluídas', $resumo['entregas'], 'check2-circle', 'info'],
        ['Cancelados', $resumo['canceladas'], 'x-circle', 'danger'],
        ['Clientes', $total_clientes, 'people', 'secondary'],
        ['Lojas', $total_lojas, 'shop', 'secondary'],
        ['Entregadores', $total_entregadores, 'bicycle', 'secondary'],
        ['Total recebido', moeda($recebido), 'wallet2', 'success'],
    ];
    foreach ($cartoes as [$rotulo, $valor, $icone, $cor]): ?>
        <div class="col-6 col-md-3">
            <div class="card cartao-estatistica h-100"><div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small"><?= $rotulo ?></div>
                    <div class="valor text-<?= $cor ?>"><?= $valor ?></div>
                </div>
                <i class="bi bi-<?= $icone ?> icone text-<?= $cor ?>"></i>
            </div></div>
        </div>
    <?php endforeach; ?>
</div>

<div class="card">
    <div class="card-header bg-white fw-semibold">Últimos pedidos</div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr>
                <th>#</th><th>Cliente</th><th>Loja</th><th>Total</th><th>Estado</th><th>Data</th><th></th>
            </tr></thead>
            <tbody>
            <?php if (empty($ultimos)): ?>
                <tr><td colspan="7" class="text-center text-muted py-4">Ainda não há pedidos.</td></tr>
            <?php else: foreach ($ultimos as $p): ?>
                <tr>
                    <td>#<?= $p['id'] ?></td>
                    <td><?= e($p['cliente_nome']) ?></td>
                    <td><?= e($p['nome_empresa']) ?></td>
                    <td><?= moeda($p['total']) ?></td>
                    <td><span class="badge bg-<?= estado_badge($p['estado']) ?>"><?= Estado_pedido::rotulo($p['estado']) ?></span></td>
                    <td class="small text-muted"><?= data_pt($p['criado_em']) ?></td>
                    <td><a href="<?= site_url('admin/pedido/' . $p['id']) ?>" class="btn btn-sm btn-outline-primary">Ver</a></td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
