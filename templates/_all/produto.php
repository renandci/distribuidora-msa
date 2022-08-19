<?php
$URL_PRODUTO_GET = Url::getURL(2);
$ID_PRODUTO_GET = !empty($URL_PRODUTO_GET) && $URL_PRODUTO_GET > 0 ? $URL_PRODUTO_GET : 0;
$ID_PRODUTO_GET = !empty($URL_PRODUTO_GET) && $URL_PRODUTO_GET === 'p' ? (int)Url::getURL(1) : $ID_PRODUTO_GET;

$Produto = new stdClass();
$Produto = Produtos::find($ID_PRODUTO_GET);

$ProdutoTamCoun = count($Produto->produtos_all);

$imgs = array();
foreach ($Produto->fotos as $f) {
  // ativa a imagem na tab
  if ($f->ordem < 0) {
    $imgs_tab[$f->id] = $f->imagem;
  }
  // ativa uma capa para o produto
  else if ($f->capa == '1') {
    $imgs['capa'] = $f->imagem;
  }
  // listgem de fotos
  else {
    $imgs[$f->id] = $f->imagem;
  }
}

$STORE['dataLayer']['produto'] = [
  'sku' => CodProduto($Produto->nome_produto, $Produto->id, $Produto->codigo_produto),
  'name' => $Produto->nome_produto,
  'price' => $Produto->preco_promo,
  'quantity' => 1
];
$STORE['TITULO_PAGINA'] = $Produto->nome_produto . ' | ' . $STORE['TITULO_PAGINA'];
$STORE['description'] = $Produto->subnome_produto;
$STORE['keywords'] = $Produto->produtos_menus[0]->id_subgrupo > 0 ? $Produto->produtos_menus[0]->subgrupo->subgrupo_keywords : $Produto->produtos_menus[0]->grupo->grupo_keywords;
$STORE['image'] = Imgs::src($imgs['capa'], 'medium');

$tamanhoSmall = '63px;';
$BREACRUMB_GRUPO = $Produto->produtos_menus[0]->id_grupo > 0 ? array('grupo_id' => $Produto->produtos_menus[0]->id_grupo, 'grupo' => $Produto->produtos_menus[0]->grupo->grupo) : null;
$BREACRUMB_SUBGRUPO = $Produto->produtos_menus[0]->id_subgrupo > 0 ? array('id' => $Produto->produtos_menus[0]->id_subgrupo, 'subgrupo' => $Produto->produtos_menus[0]->subgrupo->subgrupo) : null;
$BREACRUMB_NOMEPRODUTO = $Produto->nome_produto ? array('nome_produto' => $Produto->nome_produto) : null;

$BREACRUMB_PESQUISAR = !empty($GET['pesquisar']) ? array('pesquisar' => $GET['pesquisar']) : null;

$BREACRUMB = array_merge([
  'grupo_id' => $Produto->produtos_menus[0]->id_grupo,
  'grupo' => $Produto->produtos_menus[0]->grupo->grupo,
  'id' => $Produto->produtos_menus[0]->id_subgrupo,
  'subgrupo' => $Produto->produtos_menus[0]->subgrupo->subgrupo,
  'nome_produto' => $Produto->nome_produto
], (array)$BREACRUMB_PESQUISAR);

// ClearSale
$STORE['cs:page'] = 'product';
$STORE['cs:description'] = sprintf('name=%s, sku=%s', $Produto->nome_produto, CodProduto($Produto->nome_produto, $Produto->id, $Produto->codigo_produto));

/**
 * Verificar se é um dispositivo móvel que está sendo acessado
 */
include dirname(__DIR__) . '/_layout/layout-header.php';
if ($MobileDetect->isMobile() || $MobileDetect->isTablet()) {
  include dirname(__DIR__) . '/_layout/layout-header-mobile-topo.php';
} else {
  include sprintf('%stopo.php', URL_VIEWS_BASE);
}

function atacadista_qtde($id_produto = 0)
{
  if (isset($_SESSION['atacadista']) && $id_produto > 0) {
    return ($_SESSION['atacadista'][$id_produto]);
  }
  return 0;
}

// Javascript para carrinho de compras
if (isset($CONFIG['atacadista']) && $CONFIG['atacadista'] > '0') {

  if (isset($POST['acao']) && $POST['acao'] == 'AddToAtacado') {

    if (!isset($_SESSION['atacadista'])) {
      $_SESSION['atacadista'] = [null];
    }

    $Carrinho = Carrinho::all(['conditions' => ['id_session=?', session_id()]]);

    if (count($Carrinho) > 0) {
      unset($_SESSION['atacadista']);
      foreach ($Carrinho as $rws) {
        $_SESSION['atacadista'][$rws->id_produto] = $rws->quantidade;
      }
    }

    if (!empty($POST['id_value'])) {
      $_SESSION['atacadista'][(int)$POST['id_produto']] = (int)$POST['id_value'];
    } else {
      unset($_SESSION['atacadista'][(int)$POST['id_produto']]);
    }

    unset($_SESSION['atacadista'][0]);

    header(sprintf("location: /produto/%s/%u", $URL_PRODUTO_GET, $ID_PRODUTO_GET));
    return;
  }

  ob_start();
?>
  <script>
    // // Quando sair do input
    // // Deve sempre fazer uma acao de input
    // $("#conteudo-html").on("change", "[data-type=qtde]", function( e ) {
    // var eThis = e.target,
    // this_id = eThis.id,
    // this_value = eThis.value;

    // $.ajax({
    // url: window.location.href,
    // type: "POST",
    // data: { acao: "AddToAtacado", id_produto: this_id, id_value: this_value },
    // beforeSend: function(){},
    // complete: function(){},
    // success: function( str ) {
    // var list = $("<div/>", { html: str });
    // $( "#" + this_id ).val( list.find( "#" + this_id ).val() );
    // }
    // });
    // });
  </script>
<?php
  $str['script_manual'] .= ob_get_clean();
}

$ldJson = null;

$ldJson['@context'] = 'https://schema.org/';
$ldJson['@type'] = 'Product';
$ldJson['name'] = $Produto->nome_produto;

$ldJson['image'] = [];
foreach ($imgs as $x => $img) {
  array_push($ldJson['image'], Imgs::src($img, 'medium'));
}

$ldJson['description'] = htmlspecialchars_decode($Produto->descricao->descricao);
$ldJson['sku'] = CodProduto($Produto->nome_produto, $Produto->id, $Produto->codigo_produto);
$ldJson['mpn'] = '';
$ldJson['brand'] = [
  '@type' => "Brand",
  'name' => ASSETS
];

$ldJson['review'] = [
  '@type' => 'Review',
  'reviewRating' => [
    '@type' => 'Rating',
    'ratingValue' => $Produto->comentarios_media->media ? $Produto->comentarios_media->media : 0,
    'bestRating' => '5'
  ],
  // 'author' => [
  //   '@type' => 'Person',
  //   'name' => 'Fred Benson'
  // ]
];

$ldJson['aggregateRating'] = [
  '@type' => 'AggregateRating',
  'ratingValue' => $Produto->comentarios_media->media ? $Produto->comentarios_media->media : 0,
  // 'reviewCount' => '89'
];

$ldJson['offers'] = [
  '@type' => 'AggregateOffer',
  'highPrice' => number_format($Produto->preco_promo, 2, '.', ''),
  'lowPrice' => number_format(desconto_boleto($Produto->preco_promo, $CONFIG["desconto_boleto"]), 2, '.', ''),
  'priceCurrency' => 'BRL'
];


?>
<script type="application/ld+json">
  <?php echo json_encode($ldJson, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?>
</script>
<div id="conteudo-html" class="row">
  <!--
	<div class="clearfix div-produto-001 parametro-id row" id="div-produto" datavalue="<?php echo $Produto->id_tamanho == '0' ? $Produto->id : ''; ?>">
	<input type="hidden" name="produto_id" value="<?php echo $Produto->id_tamanho == '0' ? $Produto->id : ''; ?>"/>
	-->
  <div class="clearfix div-produto-001 parametro-id" id="div-produto" datavalue="<?php echo $Produto->id; ?>">
    <input type="hidden" name="acao" value="InserirCarrinho" />
    <input type="hidden" name="produto_id" value="<?php echo $Produto->id; ?>" />
    <div class="col-lg-7 col-md-7 col-sm-6 col-xs-12">
      <?php
      //Somente mobile
      if ($MobileDetect->isMobile()) { ?>
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" id="carregar_thumblist">
          <div id="owl_carousel">
            <img src="<?php echo Imgs::src($imgs['capa'], 'medium'); ?>" class="elevate_zoom img-responsive" id="elevate_zoom" data-zoom-image="<?php echo Imgs::src($imgs['capa'], 'large'); ?>" title="<?php echo $Produto->nome_produto ?>" />
            <?php
            $teste = 0;
            foreach ($imgs as $x => $img) { ?>
              <?PHP if ($x != 'capa') { ?>
                <div><img data-src="<?php echo Imgs::src($img, 'medium') ?>" class="img-responsive center-block lazyOwl" /></div>
            <?php }
            } ?>
          </div>
        </div>
      <?php } else { ?>
        <?php echo $STORE['personalize_class']::template_blackfriday($Produto); ?>
        <div class="clearfix" id="carregar-gallery" style="position: relative">
          <?php
          // implantação de contador regressivo para promoções
          $setup_ini = $Produto->setup_ini;
          $setup_ini = !empty($setup_ini) ? strtotime($setup_ini->format('Y-m-d H:i:s')) : $CONFIG['timestamp'] + 1;
          $setup_fin = $Produto->setup_fin;
          $setup_fin = !empty($setup_fin) ? strtotime($setup_fin->format('Y-m-d H:i:s')) : $CONFIG['timestamp'] + 1;
          $codigo_a = $Produto->codigo_id;
          $codigo_b = $Produto->promo_codigo_id;
          $id = $Produto->promo_id;

          $ini = ($setup_fin >= $timestamp);
          $fin = ($setup_fin >= $timestamp);
          $uni = ($codigo_a == $codigo_b && ($codigo_b > 0));

          if ($id > 0 && ($ini && ($ini && $fin) && $uni)) {
            echo $STORE['personalize_class']::template_countdown($Produto->setup_fin->format('Y-m-d H:i:s'), $Produto->setup_text, "#{$Produto->setup_color}", "#{$Produto->setup_hex}");
          }
          ?>
          <img src="<?php echo Imgs::src($imgs['capa'], 'medium'); ?>" class="elevate_zoom img-responsive" id="elevate_zoom" data-zoom-image="<?php echo Imgs::src($imgs['capa'], 'large'); ?>" title="<?php echo $Produto->nome_produto ?>" />
        </div>
        <?php // $str['script_manual'] .= 'AviseMe.produto("'.$ID_PRODUTO_GET.'");';
        ?>
        <div class="clearfix mt10" id="carregar_thumblist">
          <?php
          foreach ($imgs as $x => $img) {
            echo $img != '' ? ''
              . '<a href="' . $Produto->nome_produto . '" class="mr5 elevatezoom-gallery' . ($x == 'capa' ? ' active' : '') . '" '
              . 'data-image="' . Imgs::src($img, 'medium') . '" '
              . 'data-zoom-image="' . Imgs::src($img, 'large') . '">'
              . '<img src="' . Imgs::src($img, 'smalls') . '" width="75"/>'
              . '</a>' : '';
          }
          ?>
        </div>
      <?php } ?>
    </div>

    <div class="col-lg-5 col-md-5 col-sm-6 col-xs-12">
      <h1 data-nome-produto class="mt0 title-produto"><?php echo $Produto->nome_produto; ?></h1>
      <p class="mb25"><?php echo nl2br($Produto->subnome_produto) ?></p>

      <div class="div-centro-descricao-produto-002">
        <div class="clearfix">
          <div class="clearfix" id="carregar-descricao-texto">
            <div class="row">
              <div class="<?php echo (empty($CONFIG['atacadista']) && (!empty($Produto->id_tamanho) && !empty($Produto->id_cor))) ? 'col-lg-9 col-md-9' : 'col-lg-12 col-md-12' ?> col-sm-12 col-xs-12">
                <span class="black-30 show mt5 ft12px">
                  CÓD:
                  <span id="cod-produto" class="cod-produto">
                    <?php echo CodProduto($Produto->nome_produto, $Produto->id, $Produto->codigo_produto) ?>
                  </span>
                </span>

                <?php if (in_array('envio imediato', array_map(function ($s) {
                  $s = strip_tags($s);
                  $s = strtolower($s);
                  return $s;
                }, explode(',', $Produto->placastatus)))) { ?>
                  <div class="row hidden-xs">
                    <div class="col-sm-9 mb5">
                      <strong>Disponibilidade:</strong> Envio imediato após a confirmação de pagamento.
                    </div>
                    <div class="col-sm-4 mb15">
                      <span class="btn btn-success">
                        <i class="fa fa-send"></i> Envio Imediato
                      </span>
                    </div>
                  </div>
                <?php } ?>
                <div class="clearfix">
                  <?php
                  echo ($STORE['personalize_class']::{$STORE['personalize_price_view_product']}([
                    'preco_venda' => number_format($Produto->preco_venda, 2, ',', '.'),
                    'preco_size' => 'ft14px',
                    'preco_color' => 'black-40 price-venda',
                  ], [
                    'preco_promo' => number_format($Produto->preco_promo, 2, ',', '.'),
                    'preco_size' => 'ft22px',
                    'preco_color' => 'color-004 price-promo',
                    'preco_boleto' => number_format(desconto_boleto($Produto->preco_promo, $CONFIG['desconto_boleto']), 2, ',', '.')
                  ], [
                    'preco_promo' => $Produto->preco_promo,
                    'preco_size' => 'ft14px',
                    'preco_color' => 'color-004 price-parcelamento',
                    'preco_parcela_x' => parcelamento($Produto->preco_promo, $CONFIG['qtde_parcelas'], $CONFIG['parcela_minima']),
                    'preco_parcela_price' => number_format(($Produto->preco_promo / parcelamento($Produto->preco_promo, $CONFIG['qtde_parcelas'], $CONFIG['parcela_minima'])), 2, ',', '.'),
                    'qtde_parcelas' => $CONFIG['qtde_parcelas'],
                    'parcela_minima' => $CONFIG['parcela_minima']
                  ]));
                  ?>

                  <ul class="parcelamento-produto black-60" style='display: none; top: auto;' onmouseover="$(this).fadeIn(0).stop();" onmouseout="$(this).fadeOut(0);">
                    <li class="font-extra ft14px">Parcelamento no cartão de crédito</li>
                    <?php
                    for ($i = 1; $i <= parcelamento($Produto->preco_promo, $CONFIG['qtde_parcelas'], $CONFIG['parcela_minima']); $i++) {
                      $preco_promo = $Produto->preco_promo;
                      $text = sprintf('R$: %s', number_format($preco_promo, 2, ',', '.'));

                      if ($i == 1 && isset($STORE['cartao_em_1x']) && $STORE['cartao_em_1x']) {
                        $preco_promo = desconto_boleto($Produto->preco_promo, $CONFIG['desconto_boleto']);
                        $text = sprintf('%s%% de desconto', $CONFIG['desconto_boleto']);
                      }

                      echo sprintf('<li class="ft13px">%u %s de R$ %s (%s)</li>', $i, $i > 1 ? "parcelas" : "parcela", number_format($preco_promo / $i, 2, ',', '.'), $text);
                    }
                    ?>
                  </ul>

                </div>
              </div>
              <?php if (!empty($CONFIG['atacadista']) && (empty($Produto->id_tamanho) && empty($Produto->id_cor))) { ?>
                <!--[COMPRAS PARA ATACADO]-->
                <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                  <div class="form-group">
                    <label>QTDE:</label>
                    <input type="tel" name="qtde[<?php echo $Produto->id ?>]" id="<?php echo $Produto->id ?>" class="text-right form-control" value="<?php echo atacadista_qtde($Produto->id) ?>" onchange="Produto.acao_atacado('<?php echo $Produto->id ?>', this.value, '/produto/<?php echo Url::getURL(1) ?>/<?php echo Url::getURL(2) ?>')" />
                  </div>
                </div>
                <!--[END COMPRAS PARA ATACADO]-->
              <?php } ?>
            </div>
          </div>
          <!--[ A DISPONIBILIDADE ESTÁ NAS MARCAS OU ESPECIFICO EM CADA PRODUTO
                    <div class="pull-left w100 ft15px">
                        <span class="pull-left mr5">Disponibilidade para postagem: </span> <span class="light pull-left"><?php echo $Produto->postagem ?></span>
                        <span class="mt15 pull-left w100">Somar os dias de disponibilidade acima com os dias do transporte.</span>
                    </div>
                    ]-->
        </div>
        <?php
        $ResultPersonalizado = ProdutosPersonalizados::all(['conditions' => ['codigo_id=?', $Produto->codigo_id], 'order' => 'input_type desc']);
        if (count($ResultPersonalizado) > 0) { ?>
          <div class="clearfix border-bottom-dotted"></div>
          <!--[ PRODUTOS PERSONALIZADO ]-->
          <div class="clearfix personalizado">
            <h4 class="text-center mb0">PRODUTO PERSONALIZADO</h4>
            <small class="text-center show mb15">*Produtos personalizados não serão aceitos trocas ou devoluções.</small>
            <?php
            $input_type_select_open = '';
            $input_type_select_close = '';
            $input_type_select_option = '';

            $input_type = '';
            $input_type_text = '';

            $input_type_select_option .= "<option value=''>Selecione</option>";
            foreach ($ResultPersonalizado as $personalizado) {
              /**
               * Campos com opções
               */
              if ($personalizado->input_type == 'select') {
                $input_type_select_option .= "<option value='{$personalizado->input_value}'>{$personalizado->input_value}</option>";
                if ($input_type != $personalizado->input_type) {
                  $input_type = $personalizado->input_type;
                  $input_type_select_open .= "<label>{$personalizado->input_description}</label>";
                  $input_type_select_open .= "<{$input_type} name='{$personalizado->input_name}' class='personalizado-required'>";
                  $input_type_select_close .= "</{$input_type}>";
                }
              }

              /**
               * Campos com edição de texto
               */
              if ($personalizado->input_type == 'input') {
                $input_type_text .= "<label class='show'>{$personalizado->input_description}</label>";
                $input_type_text .= "<{$personalizado->input_type} name='{$personalizado->input_name}' "
                  . "type='{$personalizado->input_value}' class='personalizado-required'/>";
                $input_type_text .= "<input name='personalizado_id' type='hidden' value='{$personalizado->id}'/>";
              }
            }

            echo $input_type_text;

            echo $input_type_select_open;
            echo $input_type_select_open ? $input_type_select_option : '';
            echo $input_type_select_close;
            ?>
          </div>
          <!--[ END PRODUTOS PERSONALIZADO ]-->
        <?php } ?>

        <div id="carregar-variacao-produto">
          <?php // if( isset( $Produto->id_cor ) && $Produto->id_cor > 0 ) {
          ?>
          <?php
          $group_corid = 0;
          if ($ProdutoTamCoun >= 1 && $Produto->id_cor > 0) { ?>
            <div class="clearfix border-bottom-dotted mt5 mb5"></div>
            <p class="mb5" id="cx-cores-text"><?php echo tituloNomes($Produto->cor->opcoes->id ? implode(': ', [$Produto->cor->opcoes->tipo, $Produto->nomecor]) : '') ?></p>
            <div class="mostra-carregamento text-center mt10 mb10" style="display:none;"><img src="<?php echo Imgs::src('spinner.gif', 'public') ?>"></div>
            <ul class="<?php echo (!empty($CONFIG['atacadista']) && empty($Produto->id_tamanho) ? 'row' : 'clearfix') ?> produtos-variacoes" id="cx-cores">
              <?php foreach ($Produto->produtos_all as $cor) {
                if ($group_corid != $cor->id_cor) {
                  $group_corid = $cor->id_cor; ?>

                  <?php if (!empty($CONFIG['atacadista']) && empty($Produto->id_tamanho)) { ?>
                    <!--[COMPRAS PARA ATACADO]-->
                    <li class="text-input-comum-atacadista col-lg-3">
                      <div>
                        <div class="hex-colors">
                          <span style="background-color: #<?php echo $cor->cor->cor1 ?>">
                            <span style="border-bottom-color: #<?php echo $cor->cor->cor2 ?>"></span>
                          </span>
                        </div>
                        <div class="form-group">
                          <small>QTDE:</small>
                          <input type="tel" name="qtde[<?php echo $cor->id ?>]" id="<?php echo $cor->id ?>" class="text-right form-control" value="<?php echo atacadista_qtde($cor->id) ?>" onchange="Produto.acao_atacado('<?php echo $cor->id ?>', this.value, '/<?php echo Url::getURL(1) ?>/<?php echo Url::getURL(2) ?>/p')">
                        </div>
                      </div>
                    </li>
                    <!--[END COMPRAS PARA ATACADO]-->
                  <?php } else { ?>
                    <?php if (!empty($cor->cor->cor1) && empty($cor->cor->icon)) { ?>
                      <li class="hex-colors">
                        <a href="/<?php echo converter_texto($Produto->nome_produto) ?>/<?php echo $cor->id ?>/p" class="<?php echo $cor->id_cor == $Produto->id_cor ? 'hex-colors-active' : ''; ?><?php echo $cor->estoque == 0 && $Produto->id_tamanho == 0 ? ' not-estoque' : '' ?>">
                          <span style="background-color: #<?php echo $cor->cor->cor1 ?>">
                            <span style="border-bottom-color: #<?php echo $cor->cor->cor2 ?>"></span>
                          </span>
                        </a>
                      </li>
                    <?php } else if (!empty($cor->cor->icon)) { ?>
                      <li class="hex-colors<?php echo !empty($cor->cor->icon) ? ' is_icon' : null ?>">
                        <a href="/<?php echo converter_texto($Produto->nome_produto) ?>/<?php echo $cor->id ?>/p" class="<?php echo $cor->id_cor == $Produto->id_cor ? 'hex-colors-active' : ''; ?><?php echo $cor->estoque == 0 && $Produto->id_tamanho == 0 ? ' not-estoque' : '' ?>">
                          <span style="background: <?php echo !empty($cor->cor->icon) ? sprintf('url(%s)', Imgs::src($cor->capa->imagem, 'xs')) : "#{$cor->cor->cor1}" ?>">
                            <span style="border-bottom-color: #<?php echo $cor->cor->cor2 ?>"></span>
                          </span>
                        </a>
                      </li>
                    <?php } else { ?>
                      <li class="text-input-comum">
                        <a href="/<?php echo converter_texto($Produto->nome_produto) ?>/<?php echo $cor->id ?>/p" class="<?php echo $cor->id_cor == $Produto->id_cor ? 'hex-colors-active' : ''; ?><?php echo $cor->estoque == 0 && $Produto->id_tamanho == 0 ? ' not-estoque' : '' ?>">
                          <?php echo $cor->cor->nomecor ?>
                        </a>
                      </li>
                    <?php } ?>
                  <?php } ?>
              <?php }
              } ?>
            </ul>
          <?php } ?>

          <?php // if( isset( $Produto->id_tamanho ) && $Produto->id_tamanho > 0 ) {
          ?>
          <?php if ($ProdutoTamCoun >= 1 && $Produto->id_tamanho > 0) { ?>
            <div class="clearfix border-bottom-dotted mt5 mb5"></div>
            <div class="mostra-carregamento text-center mt10 mb10" style="display:none;"><img src="<?php echo Imgs::src('spinner.gif', 'public') ?>"></div>
            <div class="tag-tamanhos">
              <p class="mb5 tipo-variacao" id="trocar-tamanhos-text"><?php echo tituloNomes($Produto->tamanho->opcoes->id ? implode(': ', [$Produto->tamanho->opcoes->tipo, $Produto->tamanho->nometamanho]) : '') ?></p>
              <ul class="<?php echo (!empty($CONFIG['atacadista']) && !empty($Produto->id_cor) ? 'row row-no-gutters' : 'clearfix') ?> produtos-variacoes" id="trocar-tamanhos">

                <?php foreach ($Produto->produtos_all as $tam) {
                  if ($Produto->id_cor == $tam->id_cor) {
                    $check = ($tam->id_tamanho == $Produto->id_tamanho && $ProdutoTamCoun == 1);
                ?>

                    <?php if (!empty($CONFIG['atacadista'])) { ?>
                      <!--[COMPRAS PARA ATACADO]-->
                      <li class="text-input-comum-atacadista col-lg-3 mb5">
                        <div class="mr5">
                          <strong><?php echo $tam->tamanho->nometamanho ?></strong>
                          <div class="form-group">
                            <small>QTDE:</small>
                            <input type="tel" name="qtde[<?php echo $tam->id ?>]" id="<?php echo $tam->id ?>" class="text-right form-control" value="<?php echo atacadista_qtde($tam->id) ?>" onchange="Produto.acao_atacado('<?php echo $tam->id ?>', this.value, '/<?php echo Url::getURL(1) ?>/<?php echo Url::getURL(2) ?>/p')" />
                          </div>
                        </div>
                      </li>
                      <!--[END COMPRAS PARA ATACADO]-->
                    <?php } else { ?>
                      <?php if (!empty($tam->hex1)) { ?>
                        <li class="hex-colors">
                          <a href="/<?php echo converter_texto($Produto->nome_produto) ?>/<?php echo $tam->id ?>/p" class="<?php echo $check ? 'text-input-comum-active' : ''; ?><?php echo $tam->estoque == 0 ? ' not-estoque' : '' ?>">
                            <span style="background-color: #<?php echo $tam->tamanho->hex1 ?>">
                              <span style="border-bottom-color: #<?php echo $tam->tamanho->hex2 ?>"></span>
                            </span>
                            <input type="radio" name="tamanho" value="<?php echo $tam->id ?>" id="tam<?php echo $tam->id ?>" <?php echo $check ? ' checked' : null ?> />
                            <label for="tam<?php echo $tam->id ?>"><?php echo $tam->tamanho->nometamanho ?></label>
                          </a>
                        </li>
                      <?php } else if (!empty($tam->tamanho->icon)) { ?>
                        <li class="hex-colors<?php echo !empty($tam->tamanho->icon) ? ' is_icon' : null ?>">
                          <a href="/<?php echo converter_texto($Produto->nome_produto) ?>/<?php echo $tam->id ?>/p" class="<?php echo $check ? 'hex-colors-active' : ''; ?><?php echo $tam->estoque == 0 ? ' not-estoque' : '' ?>">
                            <span style="background: <?php echo !empty($tam->tamanho->icon) ? sprintf('url(%s)', Imgs::src("icon/{$tam->tamanho->icon}", 'imgs')) : "#{$tam->tamanho->hex1}" ?>">
                              <span style="border-bottom-color: #<?php echo $tam->tamanho->hex2 ?>"></span>
                            </span>
                          </a>
                        </li>
                      <?php } else { ?>
                        <li class="text-input-comum">
                          <!--
											<a href="/<?php echo converter_texto($Produto->nome_produto) ?>/<?php echo $tam->id ?>/p" class="<?php echo $check ? 'text-input-comum-active' : ''; ?><?php echo $tam->estoque == 0 ? ' not-estoque' : '' ?>">
											-->
                          <a class="<?php echo $check ? 'text-input-comum-active' : ''; ?><?php echo $tam->estoque == 0 ? ' not-estoque' : '' ?>" data-href="/<?php echo converter_texto($Produto->nome_produto) ?>/<?php echo $tam->id ?>/p">
                            <input type="radio" name="tamanho" value="<?php echo $tam->id ?>" id="tam<?php echo $tam->id ?>" <?php echo $check ? ' checked' : null ?> data-estoque="<?php echo $tam->estoque == 0 ? 'false' : 'true' ?>" />
                            <label for="tam<?php echo $tam->id ?>" data-text="<?php echo $Produto->tamanho->opcoes->tipo ?>: <?php echo $tam->tamanho->nometamanho ?>">
                              <?php echo $tam->tamanho->nometamanho ?>
                            </label>
                          </a>
                        </li>
                      <?php } ?>
                    <?php } ?>

                <?php }
                } ?>

              </ul>
            </div>
            <div class="clearfix border-bottom-dotted mt15 mb15"></div>
          <?php } ?>
        </div>
        <?php if ($Produto->status == 0) { ?>
          <div class="clearfix btn-comprar-mobile text-center" id="btn-comprar-mobile">
            <?php if (!empty($STORE['config']['product']['buy']) || !empty($Produto->orcamento)) { ?>
              <div class="text-left solicite-orcamento">
                <div>
                  <p class="alert">Solicite orçamento pelos meios de comunição abaixo</p>
                </div>
                <div style="cursor:pointer;">
                  <span class="fa-stack fa-lg">
                    <i class="fa fa-circle fa-stack-2x to-phone"></i>
                    <i class="fa fa-phone fa-stack-1x fa-inverse"></i>
                  </span> <strong><?php echo ($CONFIG['telefone']) ?></strong>
                </div>
                <div onclick="window.open('https://<?php echo $MobileDetect->isMobile() ? 'api' : 'web' ?>.whatsapp.com/send?phone=<?php echo soNumero('55' . ($CONFIG['celular'] ? $CONFIG['celular'] : $CONFIG['telefone'])) ?>&text=Oi! Estou entrando em contato pelo chat Whatsapp da <?php echo $CONFIG['nome_fantasia'] ?>. Poderia me ajudar?', 'new')" style="cursor:pointer;">
                  <span class="fa-stack fa-lg">
                    <i class="fa fa-circle fa-stack-2x to-whatsapp"></i>
                    <i class="fa fa-whatsapp fa-stack-1x fa-inverse"></i>
                  </span> <strong><?php echo ($CONFIG['celular'] ? $CONFIG['celular'] : $CONFIG['telefone']) ?></strong>
                </div>

                <div style="cursor:pointer;" onclick="AviseMe.tela();">
                  <span class="fa-stack fa-lg">
                    <i class="fa fa-circle fa-stack-2x to-email"></i>
                    <i class="fa fa-send fa-stack-1x fa-inverse"></i>
                  </span> <strong>solicitar orçamento por e-mail</strong>
                </div>

                <div>
                  <!--<p>Consulte nossos preços e quantidades para compras no varejo e no atacado.</p>-->
                  <p>Solicite orçamento para compras no varejo e no atacado pelos meios de comunicação abaixo.</p>
                </div>
                <!--<button type="button" class="btn btn-orcamento btn-lg btn-block center-block" >solicitar orçameto por e-mail</button>-->
              </div>
            <?php } else { ?>
              <?php if ($Produto->estoque_min > 0) { ?>
                <div class="mb15" id="estoque-min-init">
                  <p>Produto com quantidade mínima para compra de <?php echo $Produto->estoque_min ?></p>
                  <select name="estoque_min" style="width: 120px;" class="estoque-min-init">
                    <?php for ($i = $Produto->estoque_min; $i <= $Produto->estoque; ++$i) {
                      echo "<option value='{$i}'>{$i}</option>";
                    } ?>
                  </select>
                </div>
              <?php } ?>

              <span class="hidden-lg hidden-md hidden-sm price" id="price-mobile">
                <?php
                echo $Produto->preco_venda > 0 ? sprintf('<span class="show"><s class="black-40">DE R$: %s</s> por </span>', number_format($Produto->preco_venda, 2, ',', '.')) : '';

                echo $Produto->preco_promo > 0 ? sprintf('<span class="color-004 show"><span class="ft16px show">R$: %s à vista</span>no Boleto/Transferência</span>', number_format(desconto_boleto($Produto->preco_promo, $CONFIG['desconto_boleto']), 2, ',', '.')) : '';

                // echo $Produto->preco_promo > 0 ? ''
                // . '<span class="mt5 mb15 show">ou em <span class="color-004">'
                // . parcelamento($Produto->preco_promo, $CONFIG['qtde_parcelas'], $CONFIG['parcela_minima'])
                // . 'x de R$: '
                // . number_format($Produto->preco_promo / parcelamento($Produto->preco_promo, $CONFIG['qtde_parcelas'], $CONFIG['parcela_minima']),2,',','.')
                // . '</span> sem juros</span>' : '';
                ?>
                <!--
						<span class="black-50">POR</span>
						<span class="color-004 ft18px">
							R$: <span class="preco-venda"><?php echo number_format($Produto->preco_promo, 2, ',', '.'); ?></span>
						</span>
						<span class="black-50 show">
							ou em
							<span class="qtde-parcela color-004">
								<?php echo parcelamento($Produto->preco_promo, $CONFIG['qtde_parcelas'], $CONFIG['parcela_minima']); ?>x de R$:
							</span>
							<span class='valor-parcela color-004'>
								<?php echo number_format($Produto->preco_promo / parcelamento($Produto->preco_promo, $CONFIG['qtde_parcelas'], $CONFIG['parcela_minima']), 2, ',', '.'); ?>
							</span> sem juros no cartão
						</span>
						-->
              </span>

              <?php if (empty($STORE['config']['cart']['direct'])) { ?>
                <button type="button" class="btn btn-comprar btn-lg" onclick="Button.comprar_false()" data-estoque="<?php echo $Produto->estoque == 0 ? 'false' : 'true' ?>">
                  <i class="fa fa-2x fa-shopping-cart"></i>
                  <span class="ft28px">COMPRAR</span>
                </button>
              <?php } else { ?>
                <input type="hidden" name="cart_direct" value="1" />
                <button type="button" class="btn btn-comprar btn-lg btn-comprar-define" id="btn-comprar" data-estoque="<?php echo $Produto->estoque == 0 ? 'false' : 'true' ?>">
                  <i class="fa fa-2x fa-shopping-cart"></i>
                  <span class="ft28px">COMPRAR</span>
                </button>
              <?php } ?>
              <button type="button" class="btn btn-aviseme btn-lg" id="btn-aviseme" onclick="AviseMe.tela();" style="display: none;">
                <i class="fa fa-2x fa-paper-plane-o"></i>
                <span class="ft38px">AVISE-ME!</span>
              </button>

              <?php /* if( $Produto->estoque > 0 ) { ?>
						<?php if( empty( $STORE['config']['cart']['direct'] ) ) { ?>
							<button type="button" class="btn btn-comprar btn-lg" onclick="Button.comprar_false()">
								<i class="fa fa-2x fa-shopping-cart"></i>
								<span class="ft28px">COMPRAR</span>
							</button>
						<?php } else { ?>
							<input type="hidden" name="cart_direct" value="1"/>
							<button type="button" class="btn btn-comprar btn-lg btn-comprar-define" id="btn-comprar">
								<i class="fa fa-2x fa-shopping-cart"></i>
								<span class="ft28px">COMPRAR</span>
							</button>
						<?php } ?>
					<?php } else { ?>
						<p>Produto indisponível!</p>
						<?php } */ ?>
            <?php } ?>
          </div>
        <?php } else { ?>
          <h4 class="bg-danger ">Produto Indisponível</h4>
        <?php } ?>

        <?php if (!empty($STORE['frete_prod']) && empty($Produto->orcamento)) { ?>
          <form id="formulario-frete" class="form-frete text-center">
            <?php if ($Produto->estoque > 0) { ?>
              <?php if (!$MobileDetect->isMobile() && !$MobileDetect->isTablet()) { ?>
                <div class="clearfix border-bottom-dotted mt15 mb15"></div>
              <?php } ?>
              <p><i class="fa fa-truck"></i> CALCULE SEU FRETE</p>
              <div class="mt5 mb15 form-frete">
                <input autocomplete="off" placeholder="DIGITE SEU CEP" type="text" name="frete" id="campo-frete" class="model-border model-radius" />
                <button type="submit" id="button-frete" class="btn btn-button-frete model-radius">calcular</button>
                <img src="<?php echo Imgs::src('ajax-loader.gif', 'public'); ?>" width="25" style="display:none;" class="mt15" id="calcular-frete" />
              </div>
              <div id="info-frete"></div>
            <?php } ?>
          </form>
        <?php } ?>
      </div>
    </div>
  </div>



  <?php if (isset($STORE['ABAS_SELETIVAS']) && $STORE['ABAS_SELETIVAS'] === true) {  ?>
    <!--[ ABAS SELETIVAS ]-->
    <div class="plano-fundo-f5f5f5">
      <div class="tabs-descricao" id="tabs-descricao">
        <div class="container">
          <div class="tabs-links">
            <a href="#aba1" class="active">DESCRIÇÃO</a>
            <a href="#aba2">SUGESTÃO DE USO</a>
            <a href="#aba3">TABELA NUTRICIONAL</a>
          </div>

          <ul class="tabs-caixa">
            <li class="active" id='aba1'>
              <?php echo htmlspecialchars_decode($Produto->descricao); ?>
            </li>
            <li id='aba2'>
              <?php echo htmlspecialchars_decode($Produto->descricao_produto); ?>
            </li>
            <li id='aba3'>
              <?php echo !empty($imgs_tab) ? sprintf('<img src="%s" class="img-responsive"/>', Imgs::src(end($imgs_tab), 'smalls')) : 'Não informado.'; ?>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <!--[ END ABAS SELETIVAS ]-->
    <?php ob_start(); ?>
    <script>
      $("#tabs-descricao").on("click", "a[href]", function(e) {
        e.preventDefault();
        $(".tabs-links").find("a").removeClass("active");
        $(".tabs-caixa").find("li").removeClass("active");
        $(this).addClass("active");
        $(".tabs-caixa").find($(this).attr("href")).addClass('active');
      });
    </script>
    <?php $str['script_manual'] .= isset($str['script_manual']) ? ob_get_clean() : $str['script_manual']; ?>
  <?php } else { ?>
    <!--[ DESCRIÇÃO COMUM ]-->
    <div class="plano-fundo-f5f5f5">
      <div style="padding: 0 20px;">
        <h2 class="hidden">DESCRIÇÃO</h2>
        <div class="clearfix">
          <?php echo htmlspecialchars_decode($Produto->descricao->descricao); ?>
        </div>
        <?php if ($Produto->descricao_produto) { ?>
          <h2>SUGESTÃO DE USO</h2>
          <div class="clearfix">
            <?php echo htmlspecialchars_decode($Produto->descricao_produto); ?>
          </div>
        <?php } ?>
        <p class="ft12px mt35">* Todas as informações de descrição dos produtos são de responsabilidade dos fabricantes/fornecedores.</p>
      </div>
    </div>
    <!--[ END DESCRIÇÃO COMUM ]-->
  <?php } ?>

  <?php
  // $ProdutosDescricoesAbasCount = (int)ProdutosDescricoesAbas::count([ 'conditions' => [ 'codigo_id=?', $Produto->codigo_id ] ]);
  if (!empty($Produto->produtos_abas)) { ?>
    <div class="mt15 mb25">
      <!-- Nav tabs -->
      <ul class="nav nav-tabs" role="tablist">
        <?php
        $aAbas = 1;
        $liAbas = 1;
        $CATGRUPO = '';
        $CATGRUPO2 = '';
        // $ProdAbasArray = ProdutosDescricoesAbas::all([ 'conditions' => [ 'codigo_id=?', $Produto->codigo_id ], 'order' => 'ordem asc' ]);
        foreach ($Produto->produtos_abas as $abas) { ?>
          <?php if ($CATGRUPO != $abas->aba) {
            $CATGRUPO = $abas->aba; ?>
            <li class="<?php echo $aAbas == 1 ? 'active' : '' ?>">
              <a href="#tab-<?php echo $aAbas ?>" aria-controls="tab-<?php echo $aAbas ?>" role="tab" data-toggle="tab">
                <?php echo $CATGRUPO ?>
              </a>
            </li>
          <?php $aAbas++;
          } ?>
        <?php } ?>
      </ul>

      <!-- Tab panes -->
      <div class="tab-content class-test-image">
        <?php foreach ($Produto->produtos_abas as $descricao) { ?>
          <?php if ($CATGRUPO2 != $descricao->aba) {
            $CATGRUPO2 = $descricao->aba; ?>
            <!--[init]-->
            <div class="tab-pane<?php echo $liAbas == 1 ? ' active' : '' ?>" id="tab-<?php echo $liAbas ?>">
            <?php $liAbas++;
          } ?>

            <?php echo htmlspecialchars_decode($descricao->descricao); ?>

            <?php if ($CATGRUPO2 != '') { ?>
            </div>
            <!--[end]-->
          <?php } ?>
        <?php } ?>
      </div>
    </div>
    <style>
      .class-test-image img {
        max-width: 100%;
        height: auto;
      }
    </style>
  <?php } ?>

  <div id="recarregar-produtos-relacionado">
    <?php

    // $ProdutoRelacionadoGrupo = ProdutosRelacionados::find(array('conditions' => array('produtos_id=?', $ID_PRODUTO_GET)));
    // $GrupoRelacionado = $ProdutoRelacionadoGrupo->grupos_id;
    // ($Produto->produto_relacionado->grupos_relacionados);
    // echo Produtos::connection()->last_query;
    // echo '<hr/>';
    // // print_r($Produto->produto_relacionado->grupos_relacionados[0]->produto_relacao);
    // return;

    if (!empty($Produto->produto_relacionado)) {



      // $ResultRelacionados = Produtos::all(
      //     array(
      //         'select' => ''
      //                     . 'produtos.*, '
      //                     . 'produtos_imagens.imagem, '
      //                     . 'marcas.marcas, '
      //                     . 'tamanhos.nometamanho, '
      //                     . 'cores.nomecor, '
      //                     . 'tamanhos.nometamanho, '
      //                     . 'COR.tipo AS cortipo, '
      //                     . 'TAM.tipo AS tamanhotipo '
      //                     . '',
      //         'joins' => array(
      //             'JOIN produtos_imagens ON produtos_imagens.codigo_id = produtos.codigo_id ',
      //             'JOIN marcas ON produtos.id_marca = marcas.id ',
      //             'JOIN cores ON produtos.id_cor = cores.id ',
      //             'JOIN tamanhos ON produtos.id_tamanho = tamanhos.id ',
      //             'JOIN opcoes_tipo COR ON (COR.id = cores.opcoes_id) ',
      //             'JOIN opcoes_tipo TAM ON (TAM.id = tamanhos.opcoes_id) '
      //         ),
      //         'conditions' => array(''
      //             . 'produtos.estoque >= ? and produtos.excluir=? and produtos.status=? and '
      //                 . 'exists( '
      //                     . 'select 1 from produtos_relacionados where produtos_relacionados.produtos_id = produtos.id and produtos_relacionados.grupos_id=?) ',
      //                             0, 0, 0, $GrupoRelacionado
      //         ),
      //         'order' => 'rand(), produtos.estoque desc',
      //         'limit' => 12
      //     )
      // );

      // if( count( $ResultRelacionados ) > 1 ) {
    ?>

      <h2 class="mt15 mb20 text-center">COMPRE TAMBÉM</h2>
      <ul class="produtos-site bg-branco clearfix" id="produtos-relacionado">
        <?php foreach ($Produto->produto_relacionado->grupos_relacionados as $rIndex) { ?>

          <li id="produto<?php echo $rIndex->produto_relacao->id ?>" class="<?php echo $Produto->id == $rIndex->produto_relacao->id ? 'hidden' : '' ?> ml5 mr5 mt25">
            <a href="/<?php echo converter_texto($rIndex->produto_relacao->nome_produto) ?>/<?php echo $rIndex->produto_relacao->id ?>/p" class="cx-lista-produtos" btn-hovers style="position: relative">
              <?php echo $STORE['personalize_class']::template_blackfriday($rIndex->produto_relacao); ?>
              <div class="lista-centro-produtos black-70" style="">

                <img src="<?php echo Imgs::src($rIndex->produto_relacao->capa->imagem, 'medium'); ?>" alt="<?php echo $rIndex->produto_relacao->nome_produto ?>" class="lazy img-responsive" />

                <font size="1" class="black-30">CÓD: <?php echo CodProduto($rIndex->produto_relacao->nome_produto, $rIndex->produto_relacao->id, $rIndex->produto_relacao->codigo_produto); ?></font>

                <h3 class="mt5 mb15 nome-produto"><?php echo $rIndex->produto_relacao->nome_produto; ?></h3>
                <?php
                echo (!empty($STORE['personalize_box_color_top']) ? $STORE['personalize_class']::caixa_cores($rIndex->produto_relacao->id) : null);

                echo ($STORE['personalize_class']::{$STORE['personalize_price']}([
                  'preco_venda' => number_format($rIndex->produto_relacao->preco_venda, 2, ',', '.'),
                  'preco_size' => 'ft14px',
                  'preco_color' => 'black-40 price-venda',
                ], [
                  'preco_promo' => number_format($rIndex->produto_relacao->preco_promo, 2, ',', '.'),
                  'preco_size' => 'ft20px',
                  'preco_color' => 'color-004 price-promo',
                  'preco_boleto' => number_format(desconto_boleto($rIndex->produto_relacao->preco_promo, $CONFIG['desconto_boleto']), 2, ',', '.')
                ], [
                  'preco_promo' => $rIndex->produto_relacao->preco_promo,
                  'preco_size' => 'ft12px',
                  'preco_color' => 'color-004 price-parcelamento',
                  'preco_parcela_x' => parcelamento($rIndex->produto_relacao->preco_promo, $CONFIG['qtde_parcelas'], $CONFIG['parcela_minima']),
                  'preco_parcela_price' => number_format($rIndex->produto_relacao->preco_promo / parcelamento($rIndex->produto_relacao->preco_promo, $CONFIG['qtde_parcelas'], $CONFIG['parcela_minima']), 2, ',', '.'),
                ]));
                ?>
                <?php if (!empty($STORE['config']['btn_frete']) && CheckFreteGratis($rIndex->produto_relacao->preco_promo, $STORE['frete_valor']) || $rIndex->produto_relacao->frete > 0) { ?>
                  <span class='btn btn-frete' data-frete='frete' data-texto-1='FRETE' data-texto-2='GRÁTIS'>
                    <?php echo $STORE['text_frete'] ? $STORE['text_frete'] : 'FRETE GRÁTIS'; ?>
                  </span>
                <?php } ?>

                <?php
                /**
                 * Botao de Espiar
                 */
                echo !empty($STORE['config']['btn-espiar'][0]) && $rIndex->produto_relacao->estoque > 0 ?
                  HelperHtml::button_espiar(
                    '/' . converter_texto($rIndex->produto_relacao->nome_produto) . '/' . $rIndex->produto_relacao->id . '/p',
                    $STORE['config']['btn-espiar']['text'],
                    $STORE['config']['btn-espiar']['class'],
                    $STORE['config']['btn-espiar']['class-icon']
                  ) : '';
                /**
                 * Botao de Comprar
                 */
                echo !empty($STORE['config']['btn-compra'][0]) && $rIndex->produto_relacao->estoque > 0 ?
                  HelperHtml::button_comprar(
                    '/' . converter_texto($rIndex->produto_relacao->nome_produto) . '/' . $rIndex->produto_relacao->id . '/p',
                    $STORE['config']['btn-compra']['text'],
                    $STORE['config']['btn-compra']['class'],
                    $STORE['config']['btn-compra']['class-icon']
                  ) : '';
                /**
                 * Botao de AviseMe
                 */
                echo !empty($STORE['config']['btn-compra'][0]) && $rIndex->produto_relacao->estoque == 0 ? ''
                  . HelperHtml::button_avise_me(
                    '/' . converter_texto($rIndex->produto_relacao->nome_produto) . '/' . $rIndex->produto_relacao->id . '/p',
                    $STORE['config']['btn-aviseme']['text'],
                    $STORE['config']['btn-aviseme']['class'],
                    $STORE['config']['btn-aviseme']['class-icon']
                  ) : '';
                echo (!empty($STORE['personalize_box_color_bottom']) ? $STORE['personalize_class']::caixa_cores($rIndex->produto_relacao->id) : null);
                ?>
              </div>
            </a>
          </li>
        <?php } ?>
      </ul>
      <ul id='carousel-custom-dots' class='carousel-custom-dots'>
        <li class='owl-arrow owl-prev fa fa-chevron-left color-001 ft32px'></li>
        <li class='owl-arrow owl-next fa fa-chevron-right color-001 ft32px'></li>
      </ul>
      <style type="text/css">
        .carousel-custom-dots {
          position: relative;
        }

        .carousel-custom-dots .owl-arrow {
          cursor: pointer;
          position: absolute;
          height: 30px;
          width: 30px;
          color: red;
        }

        .carousel-custom-dots .owl-prev {
          margin-left: -30px;
          left: 0;
        }

        .carousel-custom-dots .owl-next {
          margin-right: -30px;
          right: 0;
        }
      </style>
      <?php // }
      ?>
    <?php } ?>
  </div>

  <div id="carregar-comentarios" class="clearfix">
    <div class="bg-branco clearfix">
      <style>
        .btn-grey {
          background-color: #D8D8D8;
          color: #FFF;
        }

        .rating-block {
          background-color: #FAFAFA;
          border: 1px solid #EFEFEF;
          padding: 15px 15px 20px 15px;
          border-radius: 3px;
        }

        .bold {
          font-weight: 700;
        }

        .padding-bottom-7 {
          padding-bottom: 7px;
        }

        .review-block {
          background-color: #FAFAFA;
          border: 1px solid #EFEFEF;
          padding: 15px;
          border-radius: 3px;
          margin-bottom: 15px;
        }

        .review-block-name {
          font-size: 12px;
          margin: 10px 0;
        }

        .review-block-date {
          font-size: 12px;
        }

        .review-block-rate {
          font-size: 13px;
          margin-bottom: 15px;
        }

        .review-block-title {
          font-size: 15px;
          font-weight: 700;
          margin-bottom: 10px;
        }

        .review-block-description {
          font-size: 13px;
        }
      </style>
      <div class="mt20 text-center bg-branco">
        <button type="button" class="btn btn-primary btn-comentario mt15" data-toggle="modal" data-target="#form-comentario">CRIAR UM COMENTÁRIO</button>
        <hr />
      </div>
      <?php if (count($Produto->comentarios) > 0) {
        $MEDIA = ($Produto->comentarios_media->media) ? $Produto->comentarios_media->media : 0;
      ?>

        <div class="row">
          <div class="col-sm-9">
            <div class="rating-block">
              <h4>Avaliação dos Clientes</h4>
              <h2 class="bold padding-bottom-7"><?php echo $MEDIA ?> <small>/ 5</small></h2>
              <?php for ($z = 1; $z <= 5; $z++) { ?>
                <span class="fa fa-star ft28px" <?php echo ($MEDIA >= $z ? ' style="color:#f0ad4e"' : '') ?> aria-hidden="true"></span>
              <?php } ?>
            </div>
          </div>
          <div class="col-sm-3">
            <h4>Nível de Classificação</h4>
            <?php
            $i = 1;
            $bgs = [1 => 'danger', 2 => 'warning', 3 => 'info', 4 => 'primary', 5 => 'success'];
            foreach ($Produto->comentarios_rating as $k => $val) { ?>
              <div class="pull-left">
                <div class="pull-left" style="width:35px; line-height:1;">
                  <div style="height:9px; margin:5px 0;"><?php echo ($val->rating) ?> <span class="fa fa-star"></span></div>
                </div>
                <div class="pull-left" style="width:180px;">
                  <div class="progress" style="height:9px; margin:8px 0;">
                    <div class="progress-bar progress-bar-<?php echo $bgs[$i] ?>" role="progressbar" aria-valuenow="<?php echo $val->total_rates * 10 ?>" aria-valuemin="0" aria-valuemax="5" style="width: <?php echo $val->total_rates * 10 ?>%">
                      <span class="sr-only">0% Complete (danger)</span>
                    </div>
                  </div>
                </div>
                <div class="pull-right" style="margin-left:10px;"><?php echo @$val->total_rates ?></div>
              </div>
            <?php $i++;
            } ?>
          </div>
        </div>

        <div class="row">
          <div class="col-sm-12">
            <hr />
            <div class="review-block">
              <?php $i = 0;
              foreach ($Produto->comentarios as $rsCli) {
                if ($i >= 5) continue; ?>
                <div class="row">
                  <div class="col-sm-3">
                    <?php
                    $image = glob(sprintf('%simgs/users/user-%s{.*}', URL_VIEWS_BASE_PUBLIC_UPLOAD, md5($rsCli->cliente->id)), GLOB_BRACE);
                    if (count($image) == 1) {
                      $image = $image[0];
                    } else {
                      $image = Imgs::src('icon-users.gif', 'public');
                    }
                    ?>
                    <img src="<?php echo $image ?>" class="img-rounded" />
                    <div class="review-block-name"><a href="#"><?php echo $rsCli->cliente->nome ?></a></div>
                    <div class="review-block-date"><?php echo utf8_encode(strftime('%A, %d de %B de %Y', strtotime((!empty($rsCli->data) ? $rsCli->data : $rsCli->created_at)))) ?></div>
                  </div>
                  <div class="col-sm-9">
                    <div class="review-block-rate">
                      <?php for ($z = 1; $z <= 5; $z++) { ?>
                        <span class="fa fa-star" <?php echo ($rsCli->nota >= $z ? ' style="color:#f0ad4e"' : '') ?> aria-hidden="true"></span>
                      <?php } ?>
                    </div>
                    <div class="review-block-title"><?php echo $rsCli->titulocomentario ?></div>
                    <div class="review-block-description"><?php echo $rsCli->comentario ?></div>
                  </div>
                </div>
                <hr />
              <?php $i++;
              } ?>
            </div>
          </div>
        </div>
      <?php } ?>
    </div>
  </div>

  <div class="model-radius cx-frete-produto cx-frete-erro cx-erro-carrinho" style="display: none" id="cx-erro-carrinho">
    <div class="show clearfix">
      <a href="javascript:void(0);" class="close-modal" onclick="$('.div-absoluta,.cx-frete-produto').fadeOut(0);">X</a>
      <h4 id="cidade-uf" class="font-bold">ATENÇÃO</h4>
      <span class="show font-bold mensagem-erro-frete mensagem-erro-carrinho"></span>
    </div>
  </div>

  <!--[COMENTÁRIOS DO PRODUTO]-->

  <div class="modal fade" tabindex="1" role="dialog" id="form-comentario">
    <form class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Avaliação</h4>
        </div>
        <div class="modal-body" id="modal-body">
          <div class="row">
            <div class="col-md-6 col-xs-12">
              <div class="form-group" id="rating-ability-wrapper">
                <label class="control-label" for="rating">
                  <input type="hidden" id="selected_rating" name="selected_rating" value="" required="required" />
                </label>
                <h2 class="bold rating-header" style="margin-top: -10px; margin-bottom: 10px;"><span class="selected-rating">0</span><small> / 5</small></h2>
                <button type="button" class="btnrating btn btn-default " data-attr="1" id="rating-star-1">
                  <i class="fa fa-star" aria-hidden="true"></i>
                </button>
                <button type="button" class="btnrating btn btn-default " data-attr="2" id="rating-star-2">
                  <i class="fa fa-star" aria-hidden="true"></i>
                </button>
                <button type="button" class="btnrating btn btn-default " data-attr="3" id="rating-star-3">
                  <i class="fa fa-star" aria-hidden="true"></i>
                </button>
                <button type="button" class="btnrating btn btn-default " data-attr="4" id="rating-star-4">
                  <i class="fa fa-star" aria-hidden="true"></i>
                </button>
                <button type="button" class="btnrating btn btn-default " data-attr="5" id="rating-star-5">
                  <i class="fa fa-star" aria-hidden="true"></i>
                </button>
              </div>
            </div>
          </div>
          <div class="form-horizontal">
            <div class="form-group">
              <label for="rating_titulo" class="col-sm-2 control-label">Título</label>
              <div class="col-sm-10">
                <input type="text" name="rating_titulo" class="form-control" id="rating_titulo" placeholder="Digite aqui o titulo do seu comentário" autocomplete="off" />
              </div>
            </div>
            <div class="form-group">
              <label for="rating_comentario" class="col-sm-2 control-label">Comentário</label>
              <div class="col-sm-10">
                <textarea id="rating_comentario" name="rating_comentario" class="form-control" placeholder="Digite aqui o seu comentário" style="height: 150px;"></textarea>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Enviar avaliação</button>
        </div>
      </div><!-- /.modal-content -->
    </form><!-- /.modal-dialog -->
  </div><!-- /.modal -->
  <?php ob_start() ?>
  <script>
    // ENVIA COMENTARIO
    $("#conteudo-html").on("submit", "#form-comentario", function(e) {
      e.preventDefault();
      var id = $("#div-produto").attr("datavalue");
      $.ajax({
        url: window.location.href,
        type: "post",
        dataType: "json",
        error: function(x, m, t) {
          console.log(x.responseText + "\\n" + m + "\\n" + t);
        },
        data: {
          acao: "CriarComentario",
          titulo: $("#rating_titulo").val(),
          comentario: $("#rating_comentario").val(),
          nota: $("#selected_rating").val(),
          produto: id,
          _u: "<?php echo implode('/', [substr(URL_BASE, 0, -1), 'produto', Url::getURL(1), Url::getURL(2)]) ?>"
        },
        beforeSend: function() {
          $(".btn-enviar-comentario").val("Enviando...");
        },
        success: function(data) {
          if (data.logado) {
            $("#form-comentario").modal("hide");
          } else {
            $("#modal-body").html(data.msg);
          }
        }
      });
    });

    $(".btnrating").mouseenter(function() {

      var previous_value = $("#selected_rating").val();

      var selected_value = $(this).attr("data-attr");

      $("#selected_rating").val(selected_value);

      $(".selected-rating").empty();
      $(".selected-rating").html(selected_value);

      for (i = 1; i <= selected_value; ++i) {
        $("#rating-star-" + i).toggleClass('btn-warning');
        $("#rating-star-" + i).toggleClass('btn-default');
      }

      for (ix = 1; ix <= previous_value; ++ix) {
        $("#rating-star-" + ix).toggleClass('btn-warning');
        $("#rating-star-" + ix).toggleClass('btn-default');
      }
    });

    $(".btnrating").on('click', (function(e) {

      var previous_value = $("#selected_rating").val();

      var selected_value = $(this).attr("data-attr");
      $("#selected_rating").val(selected_value);

      $(".selected-rating").empty();
      $(".selected-rating").html(selected_value);

      for (i = 1; i <= selected_value; ++i) {
        $("#rating-star-" + i).toggleClass('btn-warning');
        $("#rating-star-" + i).toggleClass('btn-default');
      }

      for (ix = 1; ix <= previous_value; ++ix) {
        $("#rating-star-" + ix).toggleClass('btn-warning');
        $("#rating-star-" + ix).toggleClass('btn-default');
      }
    }));
  </script>
  <?php $str['script_manual'] .= ob_get_clean(); ?>
  <!--[\\END COMENTÁRIOS DO PRODUTO]-->
</div>
<?php
include sprintf('%srodape.php', URL_VIEWS_BASE);
