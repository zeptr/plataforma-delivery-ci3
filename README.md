# Plataforma de Delivery — CodeIgniter 3

Sistema web completo de gestão de entregas (delivery) desenvolvido em **PHP /
CodeIgniter 3**, com **MySQL**, **Bootstrap 5** e **OpenStreetMap (Leaflet)**.
Toda a aplicação está em **Português de Moçambique** e foi construída seguindo o
padrão **MVC** e uma abordagem de **Test Driven Development (TDD)** para a lógica
de domínio.

Resolve a dificuldade no acompanhamento operacional das entregas, coordenando
**clientes**, **lojas/restaurantes/supermercados**, **entregadores** e
**administradores** numa única plataforma.

---

## Começar em 30 segundos (para quem recebe o código)

A aplicação corre **localmente** — não precisa de estar publicada online nem de
trazer qualquer base de dados. O instalador cria tudo.

1. Coloque a pasta em `C:\xampp\htdocs\delivery` e inicie **Apache + MySQL** no XAMPP.
2. Abra **`http://localhost/delivery/instalar`** e clique em **«Instalar agora»**.
3. Inicie sessão em **`http://localhost/delivery/login`** com qualquer conta:

   | Perfil | E-mail | Palavra-passe |
   |--------|--------|---------------|
   | Administrador | `admin@delivery.mz` | `senha123` |
   | Loja | `sabores@delivery.mz` | `senha123` |
   | Entregador | `joao@delivery.mz` | `senha123` |
   | Cliente | `ana@delivery.mz` | `senha123` |

   *(Lista completa de contas e respectivas permissões na [secção 5](#5-tipos-de-conta-e-contas-de-demonstração); detalhes de instalação na [secção 4](#4-instalação).)*

---

## 1. Funcionalidades por perfil

### Administrador
Painel com indicadores, gestão de clientes, lojas, entregadores (com **aprovação
de documentos**), pedidos, pagamentos, categorias, relatórios estatísticos e
**bloqueio/desbloqueio** de utilizadores.

### Cliente
Registo e início de sessão, recuperação de palavra-passe, gestão de perfil e de
**múltiplos endereços** (com GPS), pesquisa e filtragem de lojas por categoria,
visualização de produtos, **carrinho** (AJAX), escolha de **forma de pagamento**,
criação de pedidos, **acompanhamento em tempo real** com mapa, histórico,
**avaliação** de loja/entregador e **favoritos**.

### Loja / Restaurante / Supermercado
Registo e perfil empresarial (NUIT, horário, taxa de entrega, logótipo, GPS),
**CRUD de produtos** com imagem e stock, categorias de produto, gestão de pedidos
com **alteração de estado**, promoções e relatórios de vendas.

### Entregador
Registo com dados pessoais, do veículo e **documentação**, upload de documentos
(aprovados pelo administrador), lista de **pedidos disponíveis**, aceitação de
entregas, **actualização do estado da entrega**, visualização de **rota** e
histórico.

---

## 2. Módulos do sistema

| Módulo | Descrição |
|--------|-----------|
| 1 — Autenticação | Login, registo, logout, recuperação de senha, sessão e permissões |
| 2 — Dashboards | Painéis distintos para Admin, Cliente, Loja e Entregador |
| 3 — Produtos | CRUD, categorias, imagem, preços, stock |
| 4 — Carrinho | Adicionar/remover, actualizar quantidade, total automático |
| 5 — Pedidos | Fluxo de 8 estados (ver abaixo) |
| 6 — Geolocalização | Localização do cliente/loja, entregador no mapa, rota e tempo |
| 7 — Pagamentos | M-Pesa, e-Mola, Dinheiro, Cartão |
| 8 — Relatórios | Vendas, entregas, cancelados, lucros, estatísticas mensais |

### Fluxo do pedido (máquina de estados)

```
criado → recebido → confirmado → preparacao →
entregador_aceite → saiu_entrega → entregue → finalizado
```
O pedido pode ser **cancelado** enquanto não tiver saído para entrega.
As transições são validadas centralmente em
`application/libraries/Estado_pedido.php`.

---

## 3. Tecnologias

- **Backend:** PHP 8.1 + CodeIgniter 3.1.13
- **Base de dados:** MySQL (utf8mb4)
- **Frontend:** HTML5, CSS3, Bootstrap 5, JavaScript, AJAX
- **Design System:** direção própria *"Calor moçambicano com precisão operacional"* —
  paleta terracota/teal sobre base creme, tipografia *Bricolage Grotesque* + *Inter*,
  componentes refinados e micro-interações (`assets/css/app.css`)
- **Mapas:** Leaflet + OpenStreetMap
- **Gráficos:** Chart.js
- **Segurança:** `password_hash` (bcrypt), sessões, CSRF, validação de formulários

---

## 4. Instalação

> **Esta aplicação NÃO precisa de estar publicada online.** Corre localmente em
> qualquer máquina com XAMPP (ou PHP + MySQL) — **Windows, macOS ou Linux**.
> Quem receber o código não precisa de nenhuma base de dados pré-existente nem
> de editar configurações: o `base_url` é detectado automaticamente e o
> instalador cria toda a base de dados.

### 4.1 Instalação rápida — 1 clique (recomendado)

1. Copie a pasta do projecto para `C:\xampp\htdocs\delivery` (ou equivalente).
2. Inicie o **Apache** e o **MySQL** no painel do XAMPP.
3. (Se necessário) confirme as credenciais do MySQL em
   `application/config/database.php` — por omissão XAMPP usa `root` sem palavra-passe.
4. Abra no navegador: **`http://localhost/delivery/instalar`**
5. Clique em **«Instalar agora»**.

O assistente cria automaticamente a **base de dados**, todas as **tabelas** e as
**contas + dados de demonstração** — não é preciso mexer no phpMyAdmin. No fim,
mostra as contas disponíveis e uma ligação para o início de sessão. Pode
**reinstalar** a qualquer momento para repor um estado limpo.

> A página `/instalar` é uma ferramenta de configuração para ambiente local
> (fica desactivada em produção) e pode ser removida após a instalação
> (`application/controllers/Instalar.php`).

### 4.2 Instalação manual (alternativa, via phpMyAdmin)

1. No **phpMyAdmin**, importe `application/sql/schema.sql` (cria a base de dados e as tabelas).
2. Importe `application/sql/seed.sql` para os dados de demonstração
   (selecione a codificação **utf8mb4** para manter a acentuação correcta).
3. Aceda a `http://localhost/delivery/`.

### 4.3 Sem XAMPP (servidor embutido do PHP)

```bash
# Arrancar a aplicação (basta ter PHP e MySQL a correr)
php -S localhost:8080 server.php
```
Depois abra **`http://localhost:8080/instalar`** e clique em «Instalar agora» —
não precisa de criar a base de dados manualmente.

<details><summary>Em alternativa, criar a base de dados pela linha de comandos</summary>

```bash
mysql -u root -e "CREATE DATABASE delivery_mz CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
mysql --default-character-set=utf8mb4 -u root delivery_mz < application/sql/schema.sql
mysql --default-character-set=utf8mb4 -u root delivery_mz < application/sql/seed.sql
```
</details>

> **Nota sobre acentuação:** importe sempre o `seed.sql` com
> `--default-character-set=utf8mb4` (ou via phpMyAdmin com codificação UTF-8)
> para evitar caracteres corrompidos.

---

## 5. Tipos de conta e contas de demonstração

Existem **quatro tipos de conta (perfis)**. Após a instalação, todas as contas
abaixo ficam disponíveis com a palavra-passe **`senha123`**.

| Perfil | E-mail (demo) | O que pode fazer |
|--------|---------------|------------------|
| **Administrador** | `admin@delivery.mz` | Gere todo o sistema: clientes, lojas, entregadores, aprovação de documentos, pedidos, pagamentos, categorias, relatórios e bloqueio de utilizadores |
| **Loja** | `sabores@delivery.mz` (Restaurante) · `poupa@delivery.mz` (Supermercado) | Gere o perfil empresarial, produtos, categorias, stock, promoções, recebe e confirma pedidos, altera o estado do pedido e vê relatórios de vendas |
| **Entregador** | `joao@delivery.mz` (documentos já aprovados) | Vê pedidos disponíveis, aceita entregas, actualiza o estado da entrega, vê a rota no mapa, carrega documentos e gere o perfil/veículo |
| **Cliente** | `ana@delivery.mz` (com endereço e GPS) | Pesquisa lojas, adiciona ao carrinho, faz pedidos, escolhe pagamento, acompanha em tempo real, avalia e marca favoritos |

### Criar novas contas
Além das contas demo, é possível **registar novas contas** dos perfis Cliente,
Loja e Entregador em `http://localhost/delivery/registo` — o administrador é o
único perfil que não se auto-regista (já vem criado pelo instalador). Os
entregadores registados ficam pendentes até o administrador **aprovar os
documentos**.

---

## 6. Estrutura do projecto

```
application/
├── controllers/   Auth, Dashboard, Admin, Cliente, Loja, Entregador
├── core/          MY_Controller (base + controladores por perfil)
├── models/        Usuario, Cliente, Loja, Produto, Entregador, Pedido,
│                  Pagamento, Relatorio, Avaliacao
├── libraries/     Autenticacao, Estado_pedido, Carrinho_calc, Pagamento_proc
├── helpers/       seguranca_helper, formato_helper
├── views/         auth/ cliente/ loja/ entregador/ admin/ templates/
└── sql/           schema.sql, seed.sql
assets/            css/ js/ uploads/
tests/             TestCase, run.php, unidade/*Test.php
server.php         Router para o servidor embutido do PHP
```

---

## 7. Testes (TDD)

A lógica de domínio (máquina de estados do pedido, cálculo do carrinho e
processamento de pagamentos) é coberta por testes unitários, escritos **antes**
da implementação.

```bash
php tests/run.php
```
Resultado esperado: **27 testes, 68 asserções — tudo passou.**

A aplicação foi adicionalmente validada de ponta a ponta no navegador:
registo, início de sessão nos quatro perfis, carrinho, checkout e o ciclo
completo do pedido (`criado → … → entregue`), incluindo a confirmação automática
do pagamento em dinheiro na entrega.

---

## 8. Notas de segurança / produção

- As palavras-passe são guardadas com `password_hash` (bcrypt). **Nunca** em texto simples.
- A protecção **CSRF** está activada (`application/config/config.php`).
- Em produção: definir `ENVIRONMENT` como `production` no `index.php`, usar uma
  `encryption_key` forte e credenciais de base de dados próprias.
- Os scripts de CDN (Bootstrap, Leaflet, Chart.js) podem ser reforçados com
  atributos **Subresource Integrity** (`integrity` + `crossorigin`) ou servidos
  localmente para ambientes sensíveis.
