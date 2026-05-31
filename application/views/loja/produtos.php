<?php defined('BASEPATH') OR exit('Acesso directo ao script não permitido'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Produtos</h4>
    <a href="<?= site_url('loja/produto_form') ?>" class="btn btn-principal"><i class="bi bi-plus-lg"></i> Novo produto</a>
</div>
<div class="card"><div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr><th>Produto</th><th>Categoria</th><th>Preço</th><th>Stock</th><th>Estado</th><th class="text-end">Acções</th></tr></thead>
        <tbody>
        <?php
        $nomes_cat = []; foreach ($categorias as $c) { $nomes_cat[$c['id']] = $c['nome']; }
        if (empty($produtos)): ?>
            <tr><td colspan="6" class="text-center text-muted py-4">Sem produtos. Adicione o primeiro.</td></tr>
        <?php else: foreach ($produtos as $p): ?>
            <tr>
                <td><?= e($p['nome']) ?></td>
                <td class="small text-muted"><?= e($nomes_cat[$p['categoria_produto_id']] ?? '—') ?></td>
                <td><?= moeda($p['preco_promocional'] ?: $p['preco']) ?></td>
                <td><?= $p['stock'] > 0 ? $p['stock'] : '<span class="text-danger">0</span>' ?></td>
                <td><?= $p['ativo'] ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-secondary">Inactivo</span>' ?></td>
                <td class="text-end">
                    <a href="<?= site_url('loja/produto_form/' . $p['id']) ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                    <a href="<?= site_url('loja/produto_remover/' . $p['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Remover produto?')"><i class="bi bi-trash"></i></a>
                </td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
</div></div>
