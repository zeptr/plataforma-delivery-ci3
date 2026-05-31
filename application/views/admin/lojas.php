<?php defined('BASEPATH') OR exit('Acesso directo ao script não permitido'); ?>
<h4 class="mb-4">Gestão de lojas</h4>
<div class="card"><div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr><th>#</th><th>Empresa</th><th>Categoria</th><th>NUIT</th><th>Taxa</th><th>Estado</th><th></th></tr></thead>
        <tbody>
        <?php if (empty($lojas)): ?>
            <tr><td colspan="7" class="text-center text-muted py-4">Sem lojas registadas.</td></tr>
        <?php else: foreach ($lojas as $l): ?>
            <tr>
                <td><?= $l['id'] ?></td>
                <td><?= e($l['nome_empresa']) ?></td>
                <td><span class="badge bg-light text-dark"><?= e($l['categoria_nome']) ?></span></td>
                <td><?= e($l['nuit']) ?></td>
                <td><?= moeda($l['taxa_entrega']) ?></td>
                <td><?= $l['ativo'] ? '<span class="badge bg-success">Activa</span>' : '<span class="badge bg-secondary">Inactiva</span>' ?></td>
                <td>
                    <a href="<?= site_url('admin/alternar_loja/' . $l['id']) ?>" class="btn btn-sm btn-outline-<?= $l['ativo'] ? 'secondary' : 'success' ?>">
                        <?= $l['ativo'] ? 'Desactivar' : 'Activar' ?>
                    </a>
                </td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
</div></div>
