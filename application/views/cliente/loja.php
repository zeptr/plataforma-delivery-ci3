<?php defined('BASEPATH') OR exit('Acesso directo ao script não permitido'); ?>
<div class="d-flex justify-content-between align-items-start mb-3">
    <div>
        <h4 class="mb-1"><?= e($loja['nome_empresa']) ?></h4>
        <p class="text-muted small mb-0">
            <span class="badge bg-light text-dark"><?= e($loja['categoria_nome']) ?></span>
            <i class="bi bi-geo-alt"></i> <?= e($loja['endereco']) ?> ·
            <i class="bi bi-truck"></i> <?= moeda($loja['taxa_entrega']) ?> ·
            <i class="bi bi-star-fill text-warning"></i> <?= $avaliacoes['media'] ?: '—' ?> (<?= $avaliacoes['total'] ?>)
        </p>
    </div>
    <button class="btn btn-outline-danger" id="btn-favorito" data-loja="<?= $loja['id'] ?>" onclick="alternarFavorito(this)">
        <i class="bi bi-heart<?= $e_favorito ? '-fill' : '' ?>"></i>
    </button>
</div>

<div class="d-flex justify-content-end mb-3">
    <a href="<?= site_url('cliente/ver_carrinho') ?>" class="btn btn-dark position-relative">
        <i class="bi bi-cart"></i> Carrinho <span class="badge bg-danger" id="contador-carrinho"></span>
    </a>
</div>

<?php
$por_categoria = [];
foreach ($produtos as $p) { $por_categoria[$p['categoria_produto_id'] ?: 0][] = $p; }
$nomes_cat = []; foreach ($categorias as $c) { $nomes_cat[$c['id']] = $c['nome']; }
?>

<?php if (empty($produtos)): ?>
    <p class="text-muted">Esta loja ainda não tem produtos disponíveis.</p>
<?php else: foreach ($por_categoria as $cat_id => $lista): ?>
    <h6 class="mt-4 mb-2 text-secondary text-uppercase small fw-bold"><?= e($nomes_cat[$cat_id] ?? 'Outros') ?></h6>
    <div class="row g-3">
        <?php foreach ($lista as $p): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100"><div class="card-body d-flex flex-column">
                    <h6 class="mb-1"><?= e($p['nome']) ?></h6>
                    <p class="small text-muted flex-grow-1"><?= e($p['descricao']) ?></p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-principal">
                            <?php if ($p['preco_promocional']): ?>
                                <s class="text-muted small"><?= moeda($p['preco']) ?></s> <?= moeda($p['preco_promocional']) ?>
                            <?php else: ?><?= moeda($p['preco']) ?><?php endif; ?>
                        </span>
                        <?php if ($p['stock'] > 0): ?>
                            <button class="btn btn-sm btn-principal" onclick="adicionarCarrinho(<?= $p['id'] ?>)"><i class="bi bi-plus-lg"></i> Adicionar</button>
                        <?php else: ?>
                            <span class="badge bg-secondary">Esgotado</span>
                        <?php endif; ?>
                    </div>
                </div></div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endforeach; endif; ?>

<script>
async function adicionarCarrinho(id) {
    const r = await postAjax(BASE_URL + 'cliente/carrinho_adicionar', { produto_id: id, quantidade: 1 });
    if (r.ok) {
        document.getElementById('contador-carrinho').textContent = r.artigos;
        const t = document.createElement('div');
        t.className = 'toast-mini';
        t.textContent = r.msg;
        document.body.appendChild(t);
        setTimeout(() => t.remove(), 1800);
    } else { alert(r.msg); }
}
async function alternarFavorito(btn) {
    const r = await postAjax(BASE_URL + 'cliente/favorito_alternar/' + btn.dataset.loja, {});
    btn.querySelector('i').className = 'bi bi-heart' + (r.favorito ? '-fill' : '');
}
</script>
<style>.toast-mini{position:fixed;bottom:20px;left:50%;transform:translateX(-50%);background:#1d3557;color:#fff;padding:.6rem 1.2rem;border-radius:2rem;z-index:2000;box-shadow:0 4px 12px rgba(0,0,0,.2)}</style>
