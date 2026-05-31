<?php defined('BASEPATH') OR exit('Acesso directo ao script não permitido');
/** Parcial reutilizável: detalhe de um pedido + linha do tempo + mapa. */
$feitos = array_column($historico, 'estado');
?>
<div class="row g-3">
    <div class="col-lg-7">
        <div class="card mb-3"><div class="card-body">
            <div class="d-flex justify-content-between">
                <h5 class="mb-1">Pedido #<?= $pedido['id'] ?></h5>
                <span class="badge bg-<?= estado_badge($pedido['estado']) ?> align-self-start"><?= Estado_pedido::rotulo($pedido['estado']) ?></span>
            </div>
            <p class="text-muted small mb-3"><?= data_pt($pedido['criado_em']) ?></p>
            <dl class="row mb-0 small">
                <dt class="col-4">Loja</dt><dd class="col-8"><?= e($pedido['nome_empresa']) ?></dd>
                <dt class="col-4">Cliente</dt><dd class="col-8"><?= e($pedido['cliente_nome']) ?> · <?= e($pedido['cliente_telefone']) ?></dd>
                <dt class="col-4">Entrega em</dt><dd class="col-8"><?= e($pedido['endereco_entrega']) ?></dd>
                <dt class="col-4">Entregador</dt><dd class="col-8"><?= $pedido['entregador_nome'] ? e($pedido['entregador_nome']) . ' · ' . e($pedido['entregador_telefone']) : '<span class="text-muted">por atribuir</span>' ?></dd>
                <dt class="col-4">Pagamento</dt><dd class="col-8"><?= Pagamento_proc::rotulo($pedido['metodo_pagamento']) ?>
                    <?php if (!empty($pagamento)): ?><span class="badge bg-<?= $pagamento['estado'] === 'pago' ? 'success' : 'warning text-dark' ?>"><?= ucfirst($pagamento['estado']) ?></span> <span class="text-muted"><?= e($pagamento['referencia']) ?></span><?php endif; ?>
                </dd>
            </dl>
        </div></div>

        <div class="card"><div class="card-header bg-white fw-semibold">Itens</div>
            <table class="table mb-0">
                <thead class="table-light"><tr><th>Produto</th><th class="text-center">Qt</th><th class="text-end">Preço</th><th class="text-end">Subtotal</th></tr></thead>
                <tbody>
                <?php foreach ($itens as $it): ?>
                    <tr><td><?= e($it['nome']) ?></td><td class="text-center"><?= $it['quantidade'] ?></td>
                        <td class="text-end"><?= moeda($it['preco_unitario']) ?></td>
                        <td class="text-end"><?= moeda($it['subtotal']) ?></td></tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr><td colspan="3" class="text-end">Subtotal</td><td class="text-end"><?= moeda($pedido['subtotal']) ?></td></tr>
                    <tr><td colspan="3" class="text-end">Taxa de entrega</td><td class="text-end"><?= moeda($pedido['taxa_entrega']) ?></td></tr>
                    <tr class="fw-bold"><td colspan="3" class="text-end">Total</td><td class="text-end text-principal"><?= moeda($pedido['total']) ?></td></tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card mb-3"><div class="card-header bg-white fw-semibold">Seguimento</div>
            <div class="card-body">
                <ul class="linha-tempo mb-0">
                    <?php foreach (Estado_pedido::fluxo() as $estado):
                        $feito = in_array($estado, $feitos, true) || $pedido['estado'] === $estado; ?>
                        <li class="<?= $feito ? 'feito' : '' ?>">
                            <div class="fw-<?= $feito ? 'semibold' : 'normal' ?>"><?= Estado_pedido::rotulo($estado) ?></div>
                        </li>
                    <?php endforeach; ?>
                    <?php if ($pedido['estado'] === 'cancelado'): ?>
                        <li class="feito"><div class="fw-semibold text-danger">Cancelado</div></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        <?php if (!empty($pedido['latitude']) && !empty($pedido['loja_lat'])): ?>
        <div class="card"><div class="card-header bg-white fw-semibold">Rota de entrega</div>
            <div class="card-body p-2"><div id="mapa-rota" class="mapa"></div></div>
        </div>
        <script>
            tracarRota('mapa-rota',
                { lat: <?= (float)$pedido['loja_lat'] ?>, lng: <?= (float)$pedido['loja_lng'] ?>, rotulo: 'Loja' },
                { lat: <?= (float)$pedido['latitude'] ?>, lng: <?= (float)$pedido['longitude'] ?>, rotulo: 'Entrega' });
        </script>
        <?php endif; ?>
    </div>
</div>
