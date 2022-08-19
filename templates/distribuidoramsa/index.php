<?php
$Conditions = [];
$Conditions['select'] = ''
  . 'produtos.id, '
  . 'produtos.loja_id, '
  . 'produtos.id_cor, '
  // . 'produtos.id_marca, '
  // . 'produtos.id_tamanho, '
  . 'produtos.codigo_id,'
  . 'produtos.codigo_produto, '
  . 'produtos.nome_produto, '
  // . 'produtos.subnome_produto, '
  // . 'produtos.postagem, '
  . 'produtos.estoque, '
  . 'produtos.preco_venda, '
  . 'produtos.preco_promo, '
  . 'produtos.placastatus, '
  // . 'produtos.categoria, '
  // . 'produtos.utilidades,'
  . 'produtos.frete, '
  . 'produtos.subnome_produto as descricao, '
  // . 'produtos.status, '
  // . 'produtos.excluir, '
  // . 'produtos.ordem,'
  . 'marcas.marcas, '
  // . 'marcas.disponib_entrega, '
  // . 'cores.nomecor, '
  // . 'cores.cor1, '
  // . 'cores.cor2, '
  // . 'tamanhos.nometamanho, '
  // . 'tamanhos.hex1, '
  // . 'tamanhos.hex2,'
  // . 'opca.tipo AS opc_tipo_a, '
  // . 'opcb.tipo AS opc_tipo_b, 	'
  // . 'grupos.id AS id_grupo, '
  // . 'grupos.grupo, '
  // . 'subgrupos.id AS id_subgrupo, '
  // . 'subgrupos.subgrupo, '
  . 'produtos_imagens.imagem ';

$Conditions['joins'] = ''
  . 'INNER JOIN produtos ON produtos_menus.codigo_id = produtos.codigo_id '
  . 'INNER JOIN marcas ON produtos.id_marca = marcas.id '
  . 'INNER JOIN cores ON produtos.id_cor = cores.id '
  // . 'INNER JOIN tamanhos ON produtos.id_tamanho = tamanhos.id '
  // . 'INNER JOIN opcoes_tipo opca ON opca.id = cores.opcoes_id '
  // . 'INNER JOIN opcoes_tipo opcb ON opcb.id = tamanhos.opcoes_id '
  // . 'INNER JOIN grupos ON produtos_menus.id_grupo = grupos.id '
  // . 'INNER JOIN subgrupos ON produtos_menus.id_subgrupo = subgrupos.id '
  . 'INNER JOIN produtos_imagens ON produtos_imagens.codigo_id = produtos.codigo_id ';

$Conditions['conditions'] = ''
  . 'produtos.status = 0 '
  . 'AND produtos.excluir = 0 '
  . 'AND marcas.excluir = 0 '
  . 'AND produtos.placastatus LIKE (SELECT id FROM plaquinha_status WHERE ativo = 1) '
  . 'AND produtos.id_cor = produtos_imagens.cor_id '
  . 'AND produtos_imagens.capa=1 ';
$Conditions['conditions'] .= sprintf('AND produtos.loja_id=%u ', $CONFIG['loja_id']);

$Conditions['order'] = 'produtos.nome_produto ASC';
$Conditions['group'] = 'produtos.codigo_id, produtos.id_cor';
$Conditions['limit'] = '8';

$CONFIG['produtos_index'] = ProdutosMenus::all($Conditions);

// $Conditions['select'] .= ', count(pedidos_vendas.id_produto) as teste ';
// $Conditions['joins'] .= 'INNER JOIN pedidos_vendas ON pedidos_vendas.id_produto=produtos.id ';
// $Conditions['order'] = 'produtos.nome_produto ASC';
// $CONFIG['produtos_index_sale'] = ProdutosMenus::all($Conditions);

/**
 * Verificar se é um dispositivo móvel que está sendo acessado
 */
include dirname(__DIR__) . '/_layout/layout-header.php';
if ($MobileDetect->isMobile() || $MobileDetect->isTablet()) {
  include dirname(__DIR__) . '/_layout/layout-header-mobile-topo.php';
} else {
  include sprintf('%stopo.php', URL_VIEWS_BASE);
}
?>

<br>
<ul class="row">
  <li class="col-lg-12 col-md-12 col-sm-12 col-xs-12 featured-products">
    <h1>PRODUTOS EM DESTAQUE</h1>
  </li>
  <?php
  $div = 1;
  foreach ($CONFIG['produtos_index'] as $rIndex) { ?>
    <?php if ($rIndex->estoque > 0) ?>
    <li class="<?= $STORE['personalize_cols_index'] ?> mb15">
      <a href="/<?= converter_texto($rIndex->nome_produto) ?>/<?= $rIndex->id ?>/p" class="cx-lista-produtos" btn-hovers>
        <div class="lista-centro-produtos black-70">
          <!-- <img data-original="<?= Imgs::src($rIndex->imagem, 'medium'); ?>" alt="<?= $rIndex->nome_produto ?>" class="lazy img-responsive" /> -->
          <img data-original="/assets/distribuidoramsa/imgs/produtos/smalls/<?= $rIndex->imagem ?>" style="width: 230px;height: 230px;" alt="<?= $rIndex->nome_produto ?>" class="lazy img-responsive text-center" />
          <font size="1" class="black-30">CÓD: <?= CodProduto($rIndex->nome_produto, $rIndex->id, $rIndex->codigo_produto); ?></font>
          <font size="1" class="black-30"><?= $rIndex->marcas; ?></font>
          <h3 class="mt5 mb15 nome-produto"><?= $rIndex->nome_produto; ?></h3>
          <!-- <h4 class="mt5 mb15 nome-produto"><?= $rIndex->descricao; ?></h4> -->
        </div>
      </a>
    </li>
    <?php echo (($div % 4) == 0) ? '<li class="col-lg-12 col-md-12 hidden-sm hidden-xs"><hr/></li>' : '' ?>
    <?php echo (($div % 2) == 0) ? '<li class="hidden-lg hidden-md hidden-md col-xs-12"><hr/></li>' : '' ?>
  <?php
    ++$div;
  }
  unset($rIndex, $div);
  ?>
  <?php
  $div = 1;
  ?>
</ul>
<hr>
<div class="col-md-12 text-center">
  <a href="/produtos" class="btn btn-info ">Ver mais Produtos</a>
</div>

<?php
include sprintf('%srodape.php', URL_VIEWS_BASE);
