<?php defined('BASEPATH') OR exit('Acesso directo ao script não permitido'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <a href="<?= site_url('loja/pedidos') ?>" class="btn btn-sm btn-light"><i class="bi bi-arrow-left"></i> Voltar</a>
    <?php if (!empty($proximos)): ?>
        <div class="d-flex gap-2">
            <?php foreach ($proximos as $estado): ?>
                <?= form_open('loja/mudar_estado/' . $pedido['id'], ['class' => 'd-inline']) ?>
                    <input type="hidden" name="estado" value="<?= $estado ?>">
                    <button class="btn btn-sm btn-<?= $estado === 'cancelado' ? 'outline-danger' : 'principal' ?>"
                        <?= $estado === 'cancelado' ? 'onclick="return confirm(\'Cancelar este pedido?\')"' : '' ?>>
                        <?php if ($estado === 'cancelado'): ?>Cancelar<?php else: ?>Marcar: <?= Estado_pedido::rotulo($estado) ?><?php endif; ?>
                    </button>
                <?= form_close() ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php $this->load->view('templates/_detalhe_pedido', compact('pedido', 'itens', 'historico', 'pagamento')); ?>
