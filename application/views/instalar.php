<?php defined('BASEPATH') OR exit('Acesso directo ao script não permitido'); ?>
<div class="cartao-auth" style="max-width: 480px;">
    <div class="card"><div class="card-body p-4">
        <div class="text-center mb-4">
            <h3 class="fw-bold mb-1"><i class="bi bi-gear-wide-connected text-principal"></i> Instalação</h3>
            <p class="text-muted small mb-0">Configure a base de dados e os dados de demonstração</p>
        </div>

        <!-- Estado actual -->
        <ul class="list-group mb-3">
            <li class="list-group-item d-flex justify-content-between align-items-center">
                Ligação ao MySQL
                <?= $estado['ligado']
                    ? '<span class="badge bg-success">OK</span>'
                    : '<span class="badge bg-danger">Falhou</span>' ?>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                Base de dados <code><?= e($nome_bd) ?></code>
                <?= $estado['bd_existe']
                    ? '<span class="badge bg-success">Existe</span>'
                    : '<span class="badge bg-secondary">Em falta</span>' ?>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                Tabelas e dados
                <?= $estado['instalado']
                    ? '<span class="badge bg-success">' . (int) $estado['contas'] . ' contas</span>'
                    : '<span class="badge bg-secondary">Por instalar</span>' ?>
            </li>
        </ul>

        <?php if (!$estado['ligado']): ?>
            <div class="alert alert-danger small">
                Sem ligação ao MySQL. Inicie o MySQL (XAMPP) e confirme as credenciais em
                <code>application/config/database.php</code>.
                <?php if (!empty($estado['mensagem'])): ?><br><strong>Detalhe:</strong> <?= e($estado['mensagem']) ?><?php endif; ?>
            </div>
        <?php else: ?>
            <?= form_open('instalar/executar') ?>
                <button class="btn btn-principal w-100">
                    <i class="bi bi-download"></i>
                    <?= $estado['instalado'] ? 'Reinstalar (repõe os dados de demonstração)' : 'Instalar agora' ?>
                </button>
            <?= form_close() ?>
            <p class="text-muted small mt-2 mb-0">
                Cria a base de dados, todas as tabelas e as contas de demonstração.
                <?= $estado['instalado'] ? 'Atenção: reinstalar apaga os dados actuais.' : '' ?>
            </p>
        <?php endif; ?>

        <?php if ($estado['instalado']): ?>
            <hr>
            <h6 class="mb-2">Contas de demonstração <span class="text-muted fw-normal">(palavra-passe: <code>senha123</code>)</span></h6>
            <div class="table-responsive">
                <table class="table table-sm mb-3">
                    <tbody>
                        <tr><td><span class="badge bg-primary">Admin</span></td><td>admin@delivery.mz</td></tr>
                        <tr><td><span class="badge bg-info">Loja</span></td><td>sabores@delivery.mz</td></tr>
                        <tr><td><span class="badge bg-info">Loja</span></td><td>poupa@delivery.mz</td></tr>
                        <tr><td><span class="badge bg-warning">Entregador</span></td><td>joao@delivery.mz</td></tr>
                        <tr><td><span class="badge bg-secondary">Cliente</span></td><td>ana@delivery.mz</td></tr>
                    </tbody>
                </table>
            </div>
            <a href="<?= site_url('login') ?>" class="btn btn-dark w-100"><i class="bi bi-box-arrow-in-right"></i> Ir para o início de sessão</a>
        <?php endif; ?>
    </div></div>
    <p class="text-center text-muted small mt-3 mb-0">Ferramenta de configuração — pode ser removida após a instalação.</p>
</div>
