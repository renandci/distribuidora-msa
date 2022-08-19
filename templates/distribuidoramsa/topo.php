<div class="topo" <?php echo $modulo == 'identificacao'
                    ? ' style="border-bottom-color: #000; border-bottom-width: 1px; border-bottom-style: dashed;"'
                    : ' id="topo-movel"'; ?>>
  <div class="container">
    <div class="row">
      <div class="hidden-xs new-topo <?php echo $modulo == 'identificacao' ? 'hidden' : ''; ?>">
        <a class="show text-center" href="<?php echo URL_BASE; ?>">
          <img src="/assets/distribuidoramsa/imgs/<?= $CONFIG['logo_desktop'] ?>" class="logo-site" />
          <!-- <img src="<?php echo Imgs::src($CONFIG['logo_desktop'], 'imgs'); ?>" class="logo-site" /> -->
        </a>
        <nav class="mt15 navegacao">
          <ul class="new-menu">
            <li class="item-menu">
              <a href="/">
                HOME
              </a>
            </li>
            <li class="item-menu">
              <a href="loja/empresa/2">
                EMPRESA
              </a>
            </li>
            <li class="item-menu">
              <a href="/produtos">
                PRODUTOS
              </a>
            </li>
            <li class="item-menu">
              <a href="loja/lojas/3">
                LOJAS
              </a>
            </li>
            <li class="item-menu">
              <a href="loja/marcas/4">
                MARCAS
              </a>
            </li>
            <li class="item-menu">
              <a href="contato">
                CONTATO
              </a>
            </li>
          </ul>
        </nav>
        <div>
          <form action="/produtos" class="cx-pesquisa-topo clearfix">
            <span class="desenho-campo-busca pull-left model-radius model-border">
              <input autocomplete=off type="text" name="pesquisar" id="pesquisar" placeholder="O que voce procura?" class="input-pesquisar" />
              <button type="submit"><i class="fa fa-search"></i></button>
              <ul class="pesquisa-rapida retornar-pesquisa"></ul>
            </span>
          </form>
        </div>
      </div>
    </div>
  </div>

</div>
<?php if ('index' == $modulo) {
  include dirname(__DIR__) . '/' . ASSETS . '/layout-banner-personalizado.php';
}
?>
<div class="corpo">
  <div class="<?php echo $modulo == 'identificacao' ? 'bg-branco' : 'bg-transparent'; ?> is-init">
    <div class="centro-corpo clearfix container" id="recarregar-html">
