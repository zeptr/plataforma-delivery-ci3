<?php defined('BASEPATH') OR exit('Acesso directo ao script não permitido'); ?>
<a href="<?= site_url('admin/pedidos') ?>" class="btn btn-sm btn-light mb-3"><i class="bi bi-arrow-left"></i> Voltar</a>
<?php $this->load->view('templates/_detalhe_pedido', compact('pedido', 'itens', 'historico', 'pagamento')); ?>
