    <!--[TOPO NOVO]-->
    <div class="cx-usuario-topo clearfix" id="cx-usuario-topo">
      <div class="container">
        <div class="row">
          <div class="col-xs-3 open-close">
            <a href="javascript:void(0);" class="fa fa-bars pull-left ft32px mt5"></a>
          </div>
          <div class="col-xs-6 mt5">
            <a class="show logo-site text-center" href="<?php echo URL_BASE; ?>">
              <img src="<?php echo Imgs::src($CONFIG['logo_mobile'], 'imgs'); ?>" class="img-responsive center-block" />
            </a>
          </div>
          <div class="col-xs-3">
            <!-- <a href="/identificacao/carrinho" class="show text-right mt5 carrinho" id="carrinho-mobile">
              <div><?php echo $c['quantidade'] ?></div>
              <i class="fa fa-shopping-basket ft28px" aria-hidden="true"></i>
            </a> -->
          </div>
        </div>

        <div id="meutopo" class="meutopo" visible="false">
          <font id="menutopo-voltar" class="open-close"><i class="fa fa-close"></i></font>
          <span>
            <i class="fa fa-user"></i>
            <span class="text-uppercase"><?php echo sprintf('Olá %s', !empty($_SESSION['cliente']['nome']) ? $_SESSION['cliente']['nome'] : boas_vindas())  ?></span>
            <!-- <ul class="row">
              <li class="col-xs-4">
                <a href="/identificacao/meus-pedidos">
                  <i class="fa fa-gift"></i> Pedidos
                </a>
              </li>
              <li class="col-xs-4">
                <a href="/identificacao/meus-dados">
                  <i class="fa fa-user"></i> Dados
                </a>
              </li              </li>
            </ul> -->
            <hr>
            <form action="/produtos" class="cx-pesquisa-topo LabelProdutos">
              <span class="desenho-campo-busca ">
                <input type="text" name="pesquisar" id="pesquisar" placeholder="Procurar" class="input-pesquisar" autocomplete="off" />
                <button type="submit"><i class="fa fa-search"></i></button>
                <ul class='pesquisa-rapida retornar-pesquisa'></ul>
              </span>
            </form>
          </span>
          <form class="menus-lateral" id="menus-lateral">
            <ul class="clearfix">
              <li class="produtos-menus">
                <div class="menus-lateral-title">
                  <a href="/">home </a>
                </div>
              </li>
              <li class="produtos-menus">
                <div class="menus-lateral-title">
                  <a href="/loja/empresa/2">empresa </a>
                </div>
              </li>
              <li class="produtos-menus">
                <div class="menus-lateral-title">
                  <a href="/produtos">produtos </a>
                </div>
              </li>
              <li class="produtos-menus">
                <div class="menus-lateral-title">
                  <a href="/loja/lojas/3">lojas </a>
                </div>
              </li>
              <li class="produtos-menus">
                <div class="menus-lateral-title">
                  <a href="/loja/marcas/4">marcas </a>
                </div>
              </li>
              <li class="produtos-menus">
                <div class="menus-lateral-title">
                  <a href="/contato">contato </a>
                </div>
              </li>
              <?php // include PATH_ROOT . '/templates/_all/produtos-menus-lateral.php';
              ?>
              <?php // include PATH_ROOT . '/templates/_all/produtos-filtros-lateral.php';
              ?>
            </ul>
          </form>
          <div class="col-xs-12 text-center">
            <a class="show logo-site text-center" href="<?php echo URL_BASE; ?>">
              <img src="<?php echo Imgs::src($CONFIG['logo_desktop'], 'imgs'); ?>" class="img-responsive center-block" />
            </a>
          </div>
        </div>
      </div>
    </div>

    <!--[END TOPO NOVO]-->
    <?php
    // if ('index' == $modulo) {
    //   include PATH_ROOT . '/templates/_layout/layout-banner-mobile.php';
    // }

    if ('index' == $modulo) {
      include sprintf('%s/templates/%s/layout-banner-personalizado.php', PATH_ROOT, ASSETS);
    }

    $info_pagamentos_personalize = sprintf('%s/templates/%s/info-pagamentos.php', PATH_ROOT, ASSETS);
    if ($modulo != 'identificacao') {
      if (file_exists($info_pagamentos_personalize)) {
        require $info_pagamentos_personalize;
      } else {
        $str['script_manual'] .= HelperHtml::popup_frete();
      }
    }

    // include PATH_ROOT . '/templates/_all/menu-mobile-index.php';
    ?>
    <div class="corpo">
      <div class="<?php echo $modulo == 'identificacao' ? 'bg-branco' : 'bg-transparent'; ?> is-init">
        <div class="container" id="recarregar-html">
