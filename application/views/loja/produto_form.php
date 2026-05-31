<?php defined('BASEPATH') OR exit('Acesso directo ao script não permitido'); ?>
<h4 class="mb-4"><?= $produto ? 'Editar produto' : 'Novo produto' ?></h4>
<div class="row"><div class="col-lg-8">
    <div class="card"><div class="card-body">
        <?= form_open_multipart('loja/produto_guardar') ?>
            <?php if ($produto): ?><input type="hidden" name="id" value="<?= $produto['id'] ?>"><?php endif; ?>
            <div class="row g-3">
                <div class="col-md-8"><label class="form-label">Nome *</label>
                    <input type="text" name="nome" class="form-control" value="<?= e($produto['nome'] ?? '') ?>" required></div>
                <div class="col-md-4"><label class="form-label">Categoria</label>
                    <select name="categoria_produto_id" class="form-select">
                        <option value="">— sem categoria —</option>
                        <?php foreach ($categorias as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= ($produto['categoria_produto_id'] ?? null) == $c['id'] ? 'selected' : '' ?>><?= e($c['nome']) ?></option>
                        <?php endforeach; ?>
                    </select></div>
                <div class="col-12"><label class="form-label">Descrição</label>
                    <textarea name="descricao" class="form-control" rows="2"><?= e($produto['descricao'] ?? '') ?></textarea></div>
                <div class="col-md-4"><label class="form-label">Preço (MT) *</label>
                    <input type="number" step="0.01" name="preco" class="form-control" value="<?= e($produto['preco'] ?? '') ?>" required></div>
                <div class="col-md-4"><label class="form-label">Preço promocional</label>
                    <input type="number" step="0.01" name="preco_promocional" class="form-control" value="<?= e($produto['preco_promocional'] ?? '') ?>"></div>
                <div class="col-md-4"><label class="form-label">Stock</label>
                    <input type="number" name="stock" class="form-control" value="<?= e($produto['stock'] ?? 0) ?>"></div>
                <div class="col-md-8"><label class="form-label">Imagem</label>
                    <input type="file" name="imagem" class="form-control" accept="image/*">
                    <?php if (!empty($produto['imagem'])): ?><small class="text-muted">Actual: <?= e($produto['imagem']) ?></small><?php endif; ?></div>
                <div class="col-md-4 d-flex align-items-end">
                    <div class="form-check"><input class="form-check-input" type="checkbox" name="ativo" id="at" value="1" <?= (!$produto || $produto['ativo']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="at">Produto activo</label></div></div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button class="btn btn-principal">Guardar</button>
                <a href="<?= site_url('loja/produtos') ?>" class="btn btn-light">Cancelar</a>
            </div>
        <?= form_close() ?>
    </div></div>
</div></div>
