<?php

$r = 0;
$Produtos = [];

$GET_NOME_GRUPO = (string)Url::getURL(1);
$GET_ID_GRUPO   = (int)Url::getURL(2);

$GET_NOME_SUB_GRUPO = (string)Url::getURL(3);
$GET_ID_SUB_GRUPO   = (int)Url::getURL(4);

$GET_NOME_TETRA_GRUPO = (string)Url::getURL(5);
$GET_ID_TETRA_GRUPO   = (int)Url::getURL(6);

$GET_PAGINACAO = $GET_ID_GRUPO > 0 ? '/' . $GET_NOME_GRUPO . '/' . $GET_ID_GRUPO : '';
$GET_PAGINACAO = $GET_ID_SUB_GRUPO > 0 ? '/' . $GET_NOME_GRUPO . '/' . $GET_ID_GRUPO . '/' . $GET_NOME_SUB_GRUPO . '/' . $GET_ID_SUB_GRUPO : $GET_PAGINACAO;
$GET_PAGINACAO = $GET_ID_SUB_GRUPO > 0 ? '/' . $GET_NOME_GRUPO . '/' . $GET_ID_GRUPO . '/' . $GET_NOME_SUB_GRUPO . '/' . $GET_ID_SUB_GRUPO : $GET_PAGINACAO;
$GET_PAGINACAO = $GET_ID_TETRA_GRUPO > 0 ? '/' . $GET_NOME_GRUPO . '/' . $GET_ID_GRUPO . '/' . $GET_NOME_SUB_GRUPO . '/' . $GET_ID_SUB_GRUPO . '/' . $GET_NOME_TETRA_GRUPO . '/' . $GET_ID_TETRA_GRUPO : $GET_PAGINACAO;

/**
 * Converter a pesquisa dos filtro do sistema de busca
 */
$key_filter = null;
$GET_FILTER = null;

if (is_array($GET['filtro'])) {
  foreach ($GET['filtro'] as $key => $values) {
    if ($key_filter != $key)
      $GET[$key] .= '[';

    $GET[$key] .= implode(',', $values);

    if ($key_filter != $key)
      $GET[$key] .= ']';
  }
  unset($GET['filtro']);
  unset($GET['pag']);
  unset($GET['_']);
}

if (is_array($GET)) {
  foreach ($GET as $k => $v) {
    if (!in_array($k, $GET)) {
      $GET[$k] = sprintf('%s', $v);
      $GET_FILTER .= sprintf('%s=%s&', $k, $v);
    } else {
      $GET[$k] = sprintf('%s', $v);
      $GET_FILTER = sprintf('%s=%s&', $k, $v);
    }
  }
}

$Conditions = [];
$ConditionsFilters = [];

$Conditions['select'] = ''
  . 'SQL_CACHE produtos.id, '
  . 'produtos.loja_id, '
  . 'produtos.id_cor, '
  . 'produtos.id_marca, '
  . 'produtos.id_tamanho, '
  . 'produtos.codigo_id,'
  . 'produtos.codigo_produto, '
  . 'produtos.nome_produto, '
  // . 'produtos.subnome_produto, '
  // . 'produtos.postagem, '
  . 'produtos.estoque, '
  // . 'produtos.subnome_produto as descricao, '
  . 'produtos.placastatus, '
  . 'produtos.categoria, '
  // . 'produtos.utilidades,'
  // . 'produtos.frete, '
  . 'produtos.status, '
  . 'produtos.excluir, '
  . 'produtos.ordem,'
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
  . 'grupos.id AS id_grupo, '
  . 'grupos.grupo, '
  . 'subgrupos.id AS id_subgrupo, '
  . 'subgrupos.subgrupo, '
  . 'produtos_imagens.imagem ';

$ConditionsFilters['select'] = $Conditions['select'];

$Conditions['joins'] = ''
  . 'INNER JOIN produtos ON produtos_menus.codigo_id = produtos.codigo_id '
  . 'INNER JOIN marcas ON produtos.id_marca = marcas.id '
  . 'INNER JOIN cores ON produtos.id_cor = cores.id '
  . 'INNER JOIN tamanhos ON produtos.id_tamanho = tamanhos.id '
  . 'INNER JOIN opcoes_tipo opca ON opca.id = cores.opcoes_id '
  . 'INNER JOIN opcoes_tipo opcb ON opcb.id = tamanhos.opcoes_id '
  . 'INNER JOIN grupos ON produtos_menus.id_grupo = grupos.id '
  . 'INNER JOIN subgrupos ON produtos_menus.id_subgrupo = subgrupos.id '
  . 'INNER JOIN produtos_imagens ON produtos_imagens.codigo_id = produtos.codigo_id ';

$ConditionsFilters['joins'] = $Conditions['joins'];

$Conditions['conditions'] = ''
  . 'produtos.status = 0 '
  . 'AND produtos.excluir = 0 '
  . 'AND marcas.excluir = 0 '
  . 'AND produtos.id_cor = produtos_imagens.cor_id '
  . 'AND produtos_imagens.capa = 1 ';

$Conditions['conditions'] .= sprintf('AND produtos.loja_id=%u ', $CONFIG['loja_id']);

$ConditionsFilters['conditions'] = ''
  . 'produtos.status = 0 '
  . 'AND produtos.excluir = 0 '
  . 'AND marcas.excluir = 0 '
  . 'AND produtos.id_cor = produtos_imagens.cor_id '
  . 'AND produtos_imagens.capa = 1 ';

$ConditionsFilters['conditions'] .= sprintf('AND produtos.loja_id=%u ', $CONFIG['loja_id']);

/**
 * conditions de Pesquisa no site
 */
if (!empty($GET['pesquisar']) && $GET['pesquisar'] != '') {
  $A = sprintf('%%%s%%', $GET['pesquisar']);
  $B = implode('%" AND produtos.nome_produto like "%', explode(' ', sprintf('%%%s%%', str_replace([' de', ' para', ' com', ' a', ' o', ' da'], "", $GET['pesquisar']))));

  $Conditions['conditions'] .= 'AND(produtos.nome_produto like "%s" OR(produtos.nome_produto like %s OR (produtos.codigo_produto like "%s"))) ';
  $Conditions['conditions'] = sprintf($Conditions['conditions'], $A, "\"{$B}\"", $A);

  $ConditionsFilters['conditions'] .= 'AND(produtos.nome_produto like "%s" OR(produtos.nome_produto like %s OR (produtos.codigo_produto like "%s"))) ';
  $ConditionsFilters['conditions'] = sprintf($ConditionsFilters['conditions'], $A, "\"{$B}\"", $A);
}

/**
 * Conditions Grupos e SubGrupos e TretaGrupos
 */
if (isset($GET_ID_GRUPO, $GET_ID_SUB_GRUPO, $GET_ID_TETRA_GRUPO) && $GET_ID_TETRA_GRUPO > 0) {
  $Conditions['conditions'] .= 'AND produtos_menus.id_subgrupo=%u ';
  $Conditions['conditions'] = sprintf($Conditions['conditions'], $GET_ID_TETRA_GRUPO);

  $ConditionsFilters['conditions'] .= 'AND produtos._menus.id_subgrupo=%u ';
  $ConditionsFilters['conditions'] = sprintf($ConditionsFilters['conditions'], $GET_ID_TETRA_GRUPO);
}

/**
 * Conditions Grupos e SubGrupos
 */
if (isset($GET_ID_GRUPO, $GET_ID_SUB_GRUPO) && ($GET_ID_SUB_GRUPO > 0 && $GET_ID_TETRA_GRUPO == 0)) {
  $Conditions['conditions'] .= 'AND produtos_menus.id_subgrupo=%u ';
  $Conditions['conditions'] = sprintf($Conditions['conditions'], $GET_ID_SUB_GRUPO);

  $ConditionsFilters['conditions'] .= 'AND produtos_menus.id_subgrupo=%u ';
  $ConditionsFilters['conditions'] = sprintf($ConditionsFilters['conditions'], $GET_ID_SUB_GRUPO);
}

/**
 * Conditions Grupos
 */
if (isset($GET_ID_GRUPO) && $GET_ID_GRUPO > 0) {
  $Conditions['conditions'] .= ' AND produtos_menus.id_grupo=%u ';
  $Conditions['conditions'] = sprintf($Conditions['conditions'], $GET_ID_GRUPO);

  $ConditionsFilters['conditions'] .= ' AND produtos_menus.id_grupo=%u ';
  $ConditionsFilters['conditions'] = sprintf($ConditionsFilters['conditions'], $GET_ID_GRUPO);
}

/**
 * conditions para Grupos ou SubGrupos
 */
if (!empty($GET['grupo']) && $GET['grupo'] != '') {
  $Conditions['conditions'] .= sprintf('AND produtos_menus.id_grupo IN(%s)', implode('", "', explode(',', str_replace(['[', ']'], '"', $GET['grupo']))));
  $ConditionsFilters['conditions'] .= sprintf('AND produtos_menus.id_grupo IN(%s)', implode('", "', explode(',', str_replace(['[', ']'], '"', $GET['grupo']))));
  // $Conditions['conditions'] .= sprintf('AND id_grupo = %s', implode('" OR id_grupo = "', explode(',', str_replace(['[', ']'], '"', $GET['grupo']))));
  // $ConditionsFilters['conditions'] .= sprintf('AND id_grupo = %s', implode('" OR id_grupo = "', explode(',', str_replace(['[', ']'], '"', $GET['grupo']))));
}

if (!empty($GET['subgrupo']) && $GET['subgrupo'] != '') {
  $Conditions['conditions'] .= sprintf('AND produtos_menus.id_subgrupo IN(%s)', implode('", "', explode(',', str_replace(['[', ']'], '"', $GET['subgrupo']))));
  $ConditionsFilters['conditions'] .= sprintf('AND produtos_menus.id_subgrupo IN(%s)', implode('", "', explode(',', str_replace(['[', ']'], '"', $GET['subgrupo']))));
  // $Conditions['conditions'] .= sprintf('AND id_subgrupo = %s', implode('" OR id_subgrupo = "', explode(',', str_replace(['[', ']'], '"', $GET['subgrupo']))));
  // $ConditionsFilters['conditions'] .= sprintf('AND id_subgrupo = %s', implode('" OR id_subgrupo = "', explode(',', str_replace(['[', ']'], '"', $GET['subgrupo']))));
}

/**
 * conditions para Categoria (Generos)
 */
if (!empty($GET['genero']) && $GET['genero'] != '') {
  $loop_genero = null;
  $GET_GENERO = explode(',', str_replace(['[', ']'], "", $GET['genero']));
  foreach ($GET_GENERO as $V_GET_GENERO) {
    $loop_genero[] = checkCategoria($V_GET_GENERO);
  }
  // New genero
  $GET_GENERO = '[' . implode(',', $loop_genero) . ']';

  $Conditions['conditions'] .= sprintf('AND produtos.categoria IN(%s)', implode('", "', explode(',', str_replace(['[', ']'], '"', $GET_GENERO))));
  $ConditionsFilters['conditions'] .= sprintf('AND produtos.categoria IN(%s)', implode('", "', explode(',', str_replace(['[', ']'], '"', $GET_GENERO))));
}

/**
 * conditions para Busca de Cores
 */
// if (!empty($GET['cores']) && $GET['cores'] != '') {
//   $Conditions['conditions'] .= sprintf('AND produtos.id_cor IN(%s)', implode('","', explode(',', str_replace(['[', ']'], '"', $GET['cores']))));
//   // $Conditions['conditions'] .= sprintf('AND id_cor like %s', implode('" OR id_cor like "', explode(',', str_replace(['[', ']'], '"', $GET['cores']))));
// }

// /**
//  * conditions para Busca de Tamanhos
//  */
// if (!empty($GET['tamanhos']) && $GET['tamanhos'] != '') {
//   $Conditions['conditions'] .= sprintf('AND produtos.id_tamanho IN(%s)', implode('", "', explode(',', str_replace(['[', ']'], '"', $GET['tamanhos']))));
//   // $Conditions['conditions'] .= sprintf('AND id_tamanho like %s', implode('" OR id_tamanho like "', explode(',', str_replace(['[', ']'], '"', $GET['tamanhos']))));
// }


$Conditions['order'] = '';
$Conditions['order'] .= 'produtos.nome_produto ASC';
// if (!empty($STORE['config']['sql']['order'])) {
//   $Conditions['order'] .= $STORE['config']['sql']['order'];
// } else {
//   $Conditions['order'] .= 'produtos.estoque DESC, id DESC';
// }

if (!empty($GET['preco']) && ($GET['preco'] === 'asc' || $GET['preco'] === 'desc')) {
  $Conditions['order'] = sprintf('produtos.preco_promo %s, id desc, produtos.estoque desc ', $GET['preco']);
}
if (!empty($GET['preco']) && (($GET['preco'] !== 'asc' || $GET['preco'] !== 'desc') && $GET['preco'] === 'data')) {
  $Conditions['order'] = sprintf('produtos.id desc ', $GET['preco']);
}

// Configuração direto no arquivo para cada loja
$Conditions['group'] = 'produtos.codigo_id';
if (!$STORE['product']['group'] || $STORE['config']['sql']['group']) {
  $Conditions['group'] = 'produtos.codigo_id, produtos.id_cor';
}

$maximo = 12;

$pag = !empty($GET['pag']) && $GET['pag'] > 0 ? (int)$GET['pag'] : 1;

$inicio = (($pag * $maximo) - $maximo);
$TotalProdutos = (int)count(ProdutosMenus::all($Conditions));

$ProdutosTotal = ceil($TotalProdutos / $maximo);

$Conditions['limit'] = $maximo;
$Conditions['offset'] = ($maximo * ($pag - 1));

/**
 * Produtos
 * @description Gerado uma camada no mysql com uma view
 * @bkp $Produtos = Produtos::all( $Conditions );
 */
$Produtos = ProdutosMenus::all($Conditions);
/**
 * Filtros
 * CORES|TAMANHOS|MARCAS|CATEGORIA
 */
$filtros = [];
$codigo_id_array = [0];

$ConditionsFilters['group'] = 'produtos.codigo_id, produtos.id_cor, produtos.id_tamanho, produtos.categoria';
$ConditionsFilters['order'] = 'produtos.codigo_id asc, produtos.categoria asc, produtos.id_cor asc, produtos.id_tamanho asc';
$ProdutosFiltersGeneros = ProdutosMenus::all($ConditionsFilters);

foreach ($ProdutosFiltersGeneros as $rsFiltros) {
  $codigo_id_array[$rsFiltros->codigo_id] = $rsFiltros->codigo_id;

  if (!empty($rsFiltros->categoria)) {
    $filtros['genero']['generos'][$rsFiltros->categoria] = [
      'id_filtro' => checkCategoria($rsFiltros->categoria, false),
      'categoria' => checkCategoria($rsFiltros->categoria, true)
    ];
  }

  if ($rsFiltros->id_cor > 0) {
    $filtros['cores'][$rsFiltros->opc_tipo_a][$rsFiltros->id_cor] = [
      'id_cor' => $rsFiltros->id_cor,
      'cor' => $rsFiltros->nomecor,
      'hex1' => $rsFiltros->cor1,
      'hex2' => $rsFiltros->cor2
    ];
  }

  if ($rsFiltros->id_tamanho > 0) {
    $filtros['tamanhos'][$rsFiltros->opc_tipo_b][$rsFiltros->id_tamanho] = [
      'id_tamanho' => $rsFiltros->id_tamanho,
      'tamanhos' => $rsFiltros->nometamanho,
    ];
  }
}

$ConditionsMetasTags = ['conditions' => ['codigo_id IN(?) AND id_grupo=? AND id_subgrupo=?', $codigo_id_array, $GET_ID_GRUPO, ($GET_ID_TETRA_GRUPO > 0 ? $GET_ID_TETRA_GRUPO : $GET_ID_SUB_GRUPO)]];
$ConditionsMetasTags['group'] = 'codigo_id';
//$ConditionsMetasTags['order'] = 'rand()';
$ProdutosMenus = ProdutosMenus::all($ConditionsMetasTags);
$MetasTags = current($ProdutosMenus);

$STORE_KEYWORDS = $GET_ID_GRUPO > 0 && $GET_ID_SUB_GRUPO > 0 ? $MetasTags->subgrupo->subgrupo_keywords : $MetasTags->grupo->grupo_keywords;
$STORE_DESCRIPTION = $GET_ID_GRUPO > 0 && $GET_ID_SUB_GRUPO > 0 ? $MetasTags->subgrupo->subgrupo_description : $MetasTags->grupo->grupo_description;
$STORE_TITULO_PAGINA = $GET_ID_GRUPO > 0 && $GET_ID_SUB_GRUPO > 0 ? $MetasTags->grupo->grupo . ' - ' . $MetasTags->subgrupo->subgrupo : $MetasTags->grupo->grupo;

$BREACRUMB_PESQUISAR = !empty($GET['pesquisar']) ? ['pesquisar' => $GET['pesquisar']] : null;

$BREACRUMB = array_merge([
  'grupo_id' => $MetasTags->grupo->id,
  'grupo' => $MetasTags->grupo->grupo,
  'subgrupo_id' => $MetasTags->subgrupo->id,
  'subgrupo' => $MetasTags->subgrupo->subgrupo
], (array)$BREACRUMB_PESQUISAR);

$STORE['TITULO_PAGINA'] = !empty($STORE_TITULO_PAGINA) ? $STORE_TITULO_PAGINA  . ' | ' . $STORE['TITULO_PAGINA'] : $STORE['TITULO_PAGINA'];
$STORE['keywords'] = $STORE_KEYWORDS;
$STORE['description'] = $STORE_DESCRIPTION;

$STORE['cs:page'] = !empty($GET['pesquisar']) ? 'search' : 'category';
$STORE['cs:description'] = !empty($GET['pesquisar']) ? sprintf('key-words=%s', $GET['pesquisar']) : '';

/**
 * Verificar se é um dispositivo móvel que está sendo acessado
 */
include dirname(__DIR__) . '/_layout/layout-header.php';
if ($MobileDetect->isMobile() || $MobileDetect->isTablet()) {
  include dirname(__DIR__) . '/_layout/layout-header-mobile-topo.php';
} else {
  include sprintf('%stopo.php', URL_VIEWS_BASE);
}
// include dirname(__DIR__) . '/'.ASSETS.'/layout-banner-personalizado.php';
?>
<form class="row">
  <?php
  /**
   * Adicionar a pesquisa na checagem dos filtros
   */
  echo !empty($GET['pesquisar']) ? "<input type='checkbox' name='pesquisar' value='{$GET['pesquisar']}' checked/>" : '';
  ?>
  <div class="clearfix<?php echo empty($GET['pesquisar']) ? ' hidden' : ''; ?> mb5" style="border-bottom: 1px solid #ddd;">
    <div class="mt15 mb15 ml35 clearfix">
      <span class="fa-stack fa-lg pull-left">
        <i class="fa fa-circle fa-stack-2x color-001-forte"></i>
        <i class="fa fa-search fa-stack-1x fa-inverse"></i>
      </span>
      <span class="pull-left ml5 ft18px" style="margin-top: 8px">
        Você buscou: <b><?php echo sprintf('%s', $GET['pesquisar']) ?></b>
      </span>
    </div>
  </div>

  <?php if (!$MobileDetect->isMobile()) { ?>
    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 menus-lateral" id="menus-lateral" visible="false">
      <ul class="clearfix">
        <?php include dirname(__DIR__) . '/_all/produtos-menus-lateral.php'; ?>
      </ul>
    </div>
  <?php } ?>

  <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12" id="recarregar-filtros">
    <ul class="row">
      <?php
      include dirname(__DIR__) . '/_all/produtos-paginacao.php';

      $div = 1;
      foreach ($Produtos as $rIndex) { ?>
        <li class="col-lg-3 col-xs-2 mb15">
          <?php // echo $STORE['personalize_class']::template_blackfriday($rIndex); ?>
          <a href="/<?php echo converter_texto($rIndex->nome_produto) ?>/<?php echo $rIndex->id ?>/p" class="cx-lista-produtos" btn-hovers>
            <div class="lista-centro-produtos black-70">
              <img src="<?php echo $STORE['PRE_LOADED']; ?>" data-original="<?php echo Imgs::src($rIndex->imagem, 'smalls'); ?>" alt="<?php echo $rIndex->nome_produto ?>" class="lazy img-responsive" />
              <!-- <font size="1" class="black-30">CÓD: <?php echo CodProduto($rIndex->nome_produto, $rIndex->id, $rIndex->codigo_produto); ?></font> -->
              <font size="1" class="black-30"><?php echo $rIndex->marcas; ?></font>
              <h3 class="mt5 mb15 nome-produto"><?php echo $rIndex->nome_produto; ?></h3>
            </div>
          </a>
        </li>

        <?php echo (($div % 4) == 0) ? '<li class="col-lg-12 hidden-md hidden-sm hidden-xs"><hr/></li>' : '' ?>
        <?php // echo (($div % $STORE['lg_line']) == 0) ? '<li class="col-lg-12 hidden-md hidden-sm hidden-xs"><hr/></li>' : '' ?>
        <?php // echo (($div % $STORE['xs_line']) == 0) ? '<li class="hidden-lg hidden-md hidden-sm col-xs-12"><hr/></li>' : '' ?>

      <?php
        $div++;
      }

      include dirname(__DIR__) . '/_all/produtos-paginacao.php';
      ?>
    </ul>
  </div>
</form>
<?php ob_start(); ?>
<script>
  $("[data-store]").on("change", "select[name=filter_price]", function(e) {
    var select_value = $(e.target).val(),
      select_url = $(e.target).replace_url_params({
        url: window.location.href,
        name: 'preco',
        value: select_value
      });

    window.location.href = select_url;
    console.log(select_url);
  });
</script>
<?php
$str['script_manual'] .= ob_get_clean();


include sprintf('%srodape.php', URL_VIEWS_BASE);
