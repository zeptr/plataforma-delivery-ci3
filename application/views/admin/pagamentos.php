<?php defined('BASEPATH') OR exit('Acesso directo ao script não permitido'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Pagamentos</h4>
    <span class="badge bg-success fs-6">Total recebido: <?= moeda($total) ?></span>
</div>
<div class="card"><div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr><th>Referência</th><th>Pedido</th><th>Loja</th><th>Método</th><th>Valor</th><th>Estado</th><th>Data</th></tr></thead>
        <tbody>
        <?php if (empty($pagamentos)): ?>
            <tr><td colspan="7" class="text-center text-muted py-4">Sem pagamentos.</td></tr>
        <?php else: foreach ($pagamentos as $pg): ?>
            <tr>
                <td class="small"><?= e($pg['referencia']) ?></td>
                <td><a href="<?= site_url('admin/pedido/' . $pg['pedido_id']) ?>">#<?= $pg['pedido_id'] ?></a></td>
                <td><?= e($pg['nome_empresa']) ?></td>
                <td><?= Pagamento_proc::rotulo($pg['metodo']) ?></td>
                <td><?= moeda($pg['valor']) ?></td>
                <td><span class="badge bg-<?= $pg['estado'] === 'pago' ? 'success' : ($pg['estado'] === 'pendente' ? 'warning text-dark' : 'secondary') ?>"><?= ucfirst($pg['estado']) ?></span></td>
                <td class="small text-muted"><?= data_pt($pg['criado_em']) ?></td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
</div></div>
