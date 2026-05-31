<?php defined('BASEPATH') OR exit('Acesso directo ao script não permitido'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <a href="<?= site_url('cliente/pedidos') ?>" class="btn btn-sm btn-light"><i class="bi bi-arrow-left"></i> Os meus pedidos</a>
    <?php if (in_array($pedido['estado'], ['criado', 'recebido', 'confirmado', 'preparacao'], true)): ?>
        <a href="<?= site_url('cliente/cancelar/' . $pedido['id']) ?>" class="btn btn-sm btn-outline-danger"
           onclick="return confirm('Tem a certeza que deseja cancelar este pedido?')">Cancelar pedido</a>
    <?php endif; ?>
</div>

<?php $this->load->view('templates/_detalhe_pedido', compact('pedido', 'itens', 'historico', 'pagamento')); ?>

<?php if (in_array($pedido['estado'], ['entregue', 'finalizado'], true)): ?>
    <div class="card mt-3"><div class="card-body">
        <h6 class="mb-3"><i class="bi bi-star"></i> Avaliação</h6>
        <?php if ($avaliacao): ?>
            <p class="mb-1">Loja: <?= str_repeat('★', (int)$avaliacao['nota_loja']) ?><span class="text-muted"><?= str_repeat('☆', 5 - (int)$avaliacao['nota_loja']) ?></span></p>
            <?php if ($avaliacao['nota_entregador']): ?><p class="mb-1">Entregador: <?= str_repeat('★', (int)$avaliacao['nota_entregador']) ?></p><?php endif; ?>
            <?php if ($avaliacao['comentario']): ?><p class="text-muted small mb-0">"<?= e($avaliacao['comentario']) ?>"</p><?php endif; ?>
        <?php else: ?>
            <?= form_open('cliente/avaliar/' . $pedido['id']) ?>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small">Nota à loja</label>
                        <select name="nota_loja" class="form-select"><?php for ($i = 5; $i >= 1; $i--) echo "<option value='$i'>$i ★</option>"; ?></select>
                    </div>
                    <?php if ($pedido['entregador_id']): ?>
                    <div class="col-md-6">
                        <label class="form-label small">Nota ao entregador</label>
                        <select name="nota_entregador" class="form-select"><?php for ($i = 5; $i >= 1; $i--) echo "<option value='$i'>$i ★</option>"; ?></select>
                    </div>
                    <?php endif; ?>
                    <div class="col-12">
                        <textarea name="comentario" class="form-control" rows="2" placeholder="Comentário (opcional)"></textarea>
                    </div>
                </div>
                <button class="btn btn-principal mt-3">Enviar avaliação</button>
            <?= form_close() ?>
        <?php endif; ?>
    </div></div>
<?php endif; ?>
