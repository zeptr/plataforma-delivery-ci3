<?php defined('BASEPATH') OR exit('Acesso directo ao script não permitido'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <a href="<?= site_url('entregador/entregas') ?>" class="btn btn-sm btn-light"><i class="bi bi-arrow-left"></i> Voltar</a>
    <?php if (!empty($proximos)): ?>
        <div class="d-flex gap-2">
            <?php foreach ($proximos as $estado): ?>
                <?= form_open('entregador/mudar_estado/' . $pedido['id'], ['class' => 'd-inline']) ?>
                    <input type="hidden" name="estado" value="<?= $estado ?>">
                    <button class="btn btn-sm btn-principal"><i class="bi bi-arrow-right-circle"></i> <?= Estado_pedido::rotulo($estado) ?></button>
                <?= form_close() ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php $this->load->view('templates/_detalhe_pedido', compact('pedido', 'itens', 'historico', 'pagamento')); ?>
