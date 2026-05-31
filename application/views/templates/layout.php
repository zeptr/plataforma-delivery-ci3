<?php
defined('BASEPATH') OR exit('Acesso directo ao script não permitido');

/** Menus por perfil: [url, ícone, rótulo] */
$menus = [
    'admin' => [
        ['admin', 'speedometer2', 'Painel'],
        ['admin/clientes', 'people', 'Clientes'],
        ['admin/lojas', 'shop', 'Lojas'],
        ['admin/entregadores', 'bicycle', 'Entregadores'],
        ['admin/pedidos', 'box-seam', 'Pedidos'],
        ['admin/pagamentos', 'cash-coin', 'Pagamentos'],
        ['admin/categorias', 'tags', 'Categorias'],
        ['admin/relatorios', 'graph-up', 'Relatórios'],
    ],
    'cliente' => [
        ['cliente', 'speedometer2', 'Painel'],
        ['cliente/lojas', 'shop', 'Lojas'],
        ['cliente/ver_carrinho', 'cart', 'Carrinho'],
        ['cliente/pedidos', 'box-seam', 'Os meus pedidos'],
        ['cliente/favoritos', 'heart', 'Favoritos'],
        ['cliente/enderecos', 'geo-alt', 'Endereços'],
        ['cliente/perfil', 'person', 'Perfil'],
    ],
    'loja' => [
        ['loja', 'speedometer2', 'Painel'],
        ['loja/produtos', 'box', 'Produtos'],
        ['loja/categorias', 'tags', 'Categorias'],
        ['loja/pedidos', 'box-seam', 'Pedidos'],
        ['loja/relatorios', 'graph-up', 'Relatórios'],
        ['loja/perfil', 'shop', 'Perfil da loja'],
    ],
    'entregador' => [
        ['entregador', 'speedometer2', 'Painel'],
        ['entregador/disponiveis', 'inbox', 'Pedidos disponíveis'],
        ['entregador/entregas', 'truck', 'As minhas entregas'],
        ['entregador/documentos', 'file-earmark-text', 'Documentos'],
        ['entregador/perfil', 'person', 'Perfil'],
    ],
];
$perfil = $perfil_actual ?? null;
$menu = $menus[$perfil] ?? [];
$segmentos = $this->uri->segment_array();
$rota_actual = implode('/', $segmentos);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($titulo_pagina ?? 'Plataforma de Delivery') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600;12..96,700;12..96,800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/app.css') ?>" rel="stylesheet">
    <!-- Leaflet + funções de mapa carregados no <head> para estarem disponíveis
         quando os scripts de inicialização nas views forem interpretados. -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="<?= base_url('assets/js/app.js') ?>"></script>
</head>
<body class="<?= $perfil ? 'tem-sidebar' : 'sem-sidebar' ?>">
<?php if ($perfil): ?>
    <nav class="navbar navbar-dark navbar-topo px-3">
        <button class="btn btn-sm btn-outline-light d-md-none me-2" id="btn-menu"><i class="bi bi-list"></i></button>
        <a class="navbar-brand fw-bold" href="<?= site_url($perfil) ?>"><i class="bi bi-box-seam-fill"></i> Delivery MZ</a>
        <div class="ms-auto d-flex align-items-center text-white">
            <span class="me-3 d-none d-sm-inline"><i class="bi bi-person-circle"></i> <?= e($nome_actual) ?>
                <span class="badge bg-light text-dark text-capitalize"><?= e($perfil) ?></span></span>
            <a href="<?= site_url('logout') ?>" class="btn btn-sm btn-outline-light"><i class="bi bi-box-arrow-right"></i> Sair</a>
        </div>
    </nav>

    <aside class="sidebar" id="sidebar">
        <ul class="nav flex-column">
            <?php foreach ($menu as [$url, $icone, $rotulo]):
                $activo = ($rota_actual === $url || ($url !== $perfil && strpos($rota_actual, $url) === 0)); ?>
                <li class="nav-item">
                    <a class="nav-link <?= $activo ? 'activo' : '' ?>" href="<?= site_url($url) ?>">
                        <i class="bi bi-<?= $icone ?>"></i> <span><?= $rotulo ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </aside>

    <main class="conteudo">
<?php else: ?>
    <main class="conteudo-publico">
        <aside class="auth-marca">
            <div class="marca-topo"><i class="bi bi-box-seam-fill"></i> Delivery MZ</div>
            <div class="marca-centro">
                <h1>Entregas sob controlo, do pedido à porta.</h1>
                <p>A plataforma que liga clientes, lojas e entregadores em Moçambique — com acompanhamento em tempo real.</p>
            </div>
            <div class="marca-features">
                <span><i class="bi bi-shop"></i> Restaurantes, supermercados, farmácias e mais</span>
                <span><i class="bi bi-geo-alt"></i> Rastreamento por GPS e rota de entrega</span>
                <span><i class="bi bi-wallet2"></i> M-Pesa, e-Mola, cartão ou dinheiro</span>
            </div>
        </aside>
        <section class="auth-form">
<?php endif; ?>

        <?php foreach (['sucesso' => 'success', 'erro' => 'danger', 'aviso' => 'warning', 'info' => 'info'] as $chave => $cor):
            if ($this->session->flashdata($chave)): ?>
            <div class="alert alert-<?= $cor ?> alert-dismissible fade show" role="alert">
                <?= e($this->session->flashdata($chave)) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; endforeach; ?>

        <?= $conteudo ?>
    <?php if (!$perfil): ?></section><?php endif; ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.BASE_URL = '<?= site_url() ?>';
        window.CSRF = { nome: '<?= $this->security->get_csrf_token_name() ?>', hash: '<?= $this->security->get_csrf_hash() ?>' };
        // Alterna a sidebar em ecrãs pequenos.
        document.getElementById('btn-menu')?.addEventListener('click', function () {
            document.getElementById('sidebar')?.classList.toggle('aberta');
        });
    </script>
</body>
</html>
