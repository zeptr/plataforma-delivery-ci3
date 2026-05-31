<?php defined('BASEPATH') OR exit('Acesso directo ao script não permitido'); ?>
<h4 class="mb-4">Gestão de entregadores</h4>
<div class="card"><div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr><th>#</th><th>Nome</th><th>Veículo</th><th>Matrícula</th><th>Documentos</th><th>Conta</th><th class="text-end">Acções</th></tr></thead>
        <tbody>
        <?php if (empty($entregadores)): ?>
            <tr><td colspan="7" class="text-center text-muted py-4">Sem entregadores registados.</td></tr>
        <?php else: foreach ($entregadores as $en): ?>
            <tr>
                <td><?= $en['id'] ?></td>
                <td><?= e($en['nome']) ?><br><span class="small text-muted"><?= e($en['telefone']) ?></span></td>
                <td><?= e(ucfirst($en['tipo_veiculo'])) ?> <?= e($en['marca']) ?> <?= e($en['modelo']) ?></td>
                <td><?= e($en['matricula']) ?></td>
                <td><?= $en['documentos_aprovados'] ? '<span class="badge bg-success">Aprovados</span>' : '<span class="badge bg-warning text-dark">Pendentes</span>' ?></td>
                <td><?= $en['bloqueado'] ? '<span class="badge bg-danger">Bloqueado</span>' : '<span class="badge bg-success">Activo</span>' ?></td>
                <td class="text-end">
                    <a href="<?= site_url('admin/aprovar_entregador/' . $en['id']) ?>" class="btn btn-sm btn-outline-<?= $en['documentos_aprovados'] ? 'warning' : 'success' ?>">
                        <?= $en['documentos_aprovados'] ? 'Revogar' : 'Aprovar' ?>
                    </a>
                    <a href="<?= site_url('admin/alternar_bloqueio/' . $en['usuario_id']) ?>" class="btn btn-sm btn-outline-<?= $en['bloqueado'] ? 'success' : 'danger' ?>">
                        <?= $en['bloqueado'] ? 'Desbloquear' : 'Bloquear' ?>
                    </a>
                </td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
</div></div>
