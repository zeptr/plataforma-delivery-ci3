<?php defined('BASEPATH') OR exit('Acesso directo ao script não permitido'); ?>
<h4 class="mb-4">O meu carrinho</h4>
<?php if (empty($itens)): ?>
    <div class="card"><div class="card-body text-center py-5">
        <i class="bi bi-cart-x fs-1 text-muted"></i>
        <p class="mt-3 mb-3">O seu carrinho está vazio.</p>
        <a href="<?= site_url('cliente/lojas') ?>" class="btn btn-principal">Explorar lojas</a>
    </div></div>
<?php else: ?>
    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card"><div class="card-body">
                <h6 class="text-muted mb-3"><i class="bi bi-shop"></i> <?= e($loja['nome_empresa']) ?></h6>
                <table class="table align-middle">
                    <thead class="table-light"><tr><th>Produto</th><th>Preço</th><th width="140">Quantidade</th><th class="text-end">Subtotal</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach ($itens as $it): ?>
                        <tr>
                            <td><?= e($it['nome']) ?></td>
                            <td><?= moeda($it['preco']) ?></td>
                            <td>
                                <?= form_open('cliente/carrinho_actualizar', ['class' => 'd-flex gap-1']) ?>
                                    <input type="hidden" name="produto_id" value="<?= $it['produto_id'] ?>">
                                    <input type="number" name="quantidade" value="<?= $it['quantidade'] ?>" min="0" class="form-control form-control-sm" style="width:70px">
                                    <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-repeat"></i></button>
                                <?= form_close() ?>
                            </td>
                            <td class="text-end"><?= moeda($it['preco'] * $it['quantidade']) ?></td>
                            <td><a href="<?= site_url('cliente/carrinho_remover/' . $it['produto_id']) ?>" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></a></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div></div>
        </div>
        <div class="col-lg-4">
            <div class="card"><div class="card-body">
                <h6 class="mb-3">Resumo</h6>
                <div class="d-flex justify-content-between mb-1"><span>Subtotal</span><span><?= moeda($subtotal) ?></span></div>
                <div class="d-flex justify-content-between mb-1"><span>Taxa de entrega</span><span><?= moeda($taxa) ?></span></div>
                <hr>
                <div class="d-flex justify-content-between fw-bold fs-5"><span>Total</span><span class="text-principal"><?= moeda($total) ?></span></div>
                <a href="<?= site_url('cliente/checkout') ?>" class="btn btn-principal w-100 mt-3">Finalizar pedido</a>
            </div></div>
        </div>
    </div>
<?php endif; ?>
