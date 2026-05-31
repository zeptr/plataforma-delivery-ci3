-- =====================================================================
--  DADOS DE DEMONSTRAÇÃO — Plataforma de Delivery
--  Palavra-passe de todas as contas: senha123
-- =====================================================================
USE `delivery_mz`;
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE `avaliacoes`;
TRUNCATE TABLE `pagamentos`;
TRUNCATE TABLE `pedido_historico`;
TRUNCATE TABLE `pedido_itens`;
TRUNCATE TABLE `pedidos`;
TRUNCATE TABLE `favoritos`;
TRUNCATE TABLE `promocoes`;
TRUNCATE TABLE `produtos`;
TRUNCATE TABLE `categorias_produto`;
TRUNCATE TABLE `enderecos`;
TRUNCATE TABLE `entregadores`;
TRUNCATE TABLE `clientes`;
TRUNCATE TABLE `lojas`;
TRUNCATE TABLE `categorias_loja`;
TRUNCATE TABLE `usuarios`;
SET FOREIGN_KEY_CHECKS = 1;

-- Categorias de loja
INSERT INTO `categorias_loja` (`id`,`nome`,`icone`) VALUES
(1,'Restaurante','shop'),(2,'Supermercado','cart4'),(3,'Farmácia','capsule'),
(4,'Fast Food','egg-fried'),(5,'Padaria','cup-hot'),(6,'Bebidas','cup-straw'),
(7,'Papelaria','pencil'),(8,'Cosméticos','flower1'),(9,'Talho','egg');

-- Utilizadores (hash de "senha123")
SET @h := '$2y$10$bV0Y.aqT8LZocxuh3zPGYOcJO7lPTL2xBlqrodLgAUWU1i4Vo8y36';
INSERT INTO `usuarios` (`id`,`nome`,`email`,`telefone`,`password_hash`,`perfil`,`genero`,`criado_em`) VALUES
(1,'Administrador','admin@delivery.mz','840000000',@h,'admin','outro',NOW()),
(2,'Restaurante Sabores','sabores@delivery.mz','841111111',@h,'loja',NULL,NOW()),
(3,'Supermercado Poupa','poupa@delivery.mz','842222222',@h,'loja',NULL,NOW()),
(4,'João Mucavele','joao@delivery.mz','843333333',@h,'entregador','masculino',NOW()),
(5,'Ana Sitoe','ana@delivery.mz','844444444',@h,'cliente','feminino',NOW());

-- Lojas
INSERT INTO `lojas` (`id`,`usuario_id`,`categoria_id`,`nome_empresa`,`nuit`,`telefone`,`email`,`endereco`,`horario`,`taxa_entrega`,`latitude`,`longitude`,`ativo`) VALUES
(1,2,1,'Restaurante Sabores','100000001','841111111','sabores@delivery.mz','Av. Julius Nyerere, Maputo','08:00 - 22:00',60.00,-25.9650000,32.5830000,1),
(2,3,2,'Supermercado Poupa','100000002','842222222','poupa@delivery.mz','Av. 24 de Julho, Maputo','07:00 - 21:00',80.00,-25.9700000,32.5750000,1);

-- Categorias de produto
INSERT INTO `categorias_produto` (`id`,`loja_id`,`nome`) VALUES
(1,1,'Pratos principais'),(2,1,'Bebidas'),(3,2,'Mercearia'),(4,2,'Higiene');

-- Produtos
INSERT INTO `produtos` (`loja_id`,`categoria_produto_id`,`nome`,`descricao`,`preco`,`stock`,`ativo`,`criado_em`) VALUES
(1,1,'Frango à Zambeziana','Frango grelhado com molho de coco e piri-piri',650.00,40,1,NOW()),
(1,1,'Matapa com camarão','Folhas de mandioca com amendoim e camarão',550.00,25,1,NOW()),
(1,2,'Refresco 500ml','Bebida gaseificada',80.00,200,1,NOW()),
(2,3,'Arroz 5kg','Arroz branco de qualidade',520.00,60,1,NOW()),
(2,3,'Óleo alimentar 1L','Óleo vegetal',180.00,80,1,NOW()),
(2,4,'Sabonete','Sabonete hidratante',45.00,150,1,NOW());

-- Entregador (documentos aprovados, disponível)
INSERT INTO `entregadores` (`id`,`usuario_id`,`bi_passaporte`,`tipo_veiculo`,`marca`,`modelo`,`cor`,`matricula`,`ano_fabrico`,`carta_numero`,`carta_categoria`,`carta_validade`,`documentos_aprovados`,`disponivel`,`latitude`,`longitude`) VALUES
(1,4,'110100100110B','mota','Honda','CG 125','Vermelha','ABC-12-34',2020,'MZ123456','A','2030-12-31',1,1,-25.9680000,32.5800000);

-- Cliente + endereço
INSERT INTO `clientes` (`id`,`usuario_id`) VALUES (1,5);
INSERT INTO `enderecos` (`cliente_id`,`etiqueta`,`endereco`,`latitude`,`longitude`,`principal`) VALUES
(1,'Casa','Bairro da Polana, Rua 1234, Maputo',-25.9720000,32.5870000,1);
