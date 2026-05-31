<?php defined('BASEPATH') OR exit('Acesso directo ao script não permitido'); ?>
<h4 class="mb-4">Gestão de clientes</h4>
<div class="card"><div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr><th>#</th><th>Nome</th><th>E-mail</th><th>Telefone</th><th>Registo</th><th>Estado</th><th></th></tr></thead>
        <tbody>
        <?php if (empty($clientes)): ?>
            <tr><td colspan="7" class="text-center text-muted py-4">Sem clientes registados.</td></tr>
        <?php else: foreach ($clientes as $c): ?>
            <tr>
                <td><?= $c['id'] ?></td>
                <td><?= e($c['nome']) ?></td>
                <td><?= e($c['email']) ?></td>
                <td><?= e($c['telefone']) ?></td>
                <td class="small text-muted"><?= data_pt($c['criado_em'], false) ?></td>
                <td><?= $c['bloqueado'] ? '<span class="badge bg-danger">Bloqueado</span>' : '<span class="badge bg-success">Activo</span>' ?></td>
                <td>
                    <a href="<?= site_url('admin/alternar_bloqueio/' . $c['usuario_id']) ?>"
                       class="btn btn-sm btn-outline-<?= $c['bloqueado'] ? 'success' : 'danger' ?>"
                       onclick="return confirm('Confirmar alteração de estado?')">
                        <?= $c['bloqueado'] ? 'Desbloquear' : 'Bloquear' ?>
                    </a>
                </td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
</div></div>
