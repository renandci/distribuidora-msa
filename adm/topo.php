<?php
defined('PATH_ROOT') || define('PATH_ROOT', realpath($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR);
require_once PATH_ROOT . '/app/settings.php';
require_once PATH_ROOT . '/app/vendor/autoload.php';
require_once PATH_ROOT . '/app/settings-config.php';
require_once PATH_ROOT . '/app/includes/bibli-funcoes.php';

// AS CONFIGURAÇÕES SETADAS AQUI, APENAS FAZEM MOSTRA CORES DE EMAIL, MOBILE THEMA, CALCULO DE FRETE COMPRA DIRETA NA LOJA
require_once sprintf('%s/assets/%s/settings.php', PATH_ROOT, ASSETS);
$REQUIRE_ONCE = sprintf('%s/assets/%s/settings_store.inc', PATH_ROOT, ASSETS);
if (file_exists($REQUIRE_ONCE)) {
  $STORE_PLACE = require_once $REQUIRE_ONCE;
  $STORE = (test_array_replace($STORE, $STORE_PLACE));
}

require_once PATH_ROOT . '/app/includes/ajax-emails.php';

if (isset($CONFIG['correios']) && $CONFIG['correios']['diretoria'] > 0) {
  require_once PATH_ROOT . '/adm/correios/correios-bootstrap.php';
}

$PgAt = basename($_SERVER['PHP_SELF'], '.php');

AcessoPagSession($PgAt, $_SESSION['admin']['id_usuario']);

// AdicionarVerificaPermissao($PgAt, $_SESSION['admin']['id_usuario'], 0);

// Mercado Livre API
// global $CONFIG_MELI;
// $MercadoLivre = MercadoLivre::find(['conditions' => ['loja_id=?', $CONFIG['loja_id']]]);
// if ( isset($MercadoLivre) && count( $MercadoLivre ) > 0 ) {
// 	$CONFIG_MELI = $MercadoLivre->to_array();
//     $meli = new Meli($CONFIG_MELI['app_id'], $CONFIG_MELI['app_key'], $_SESSION['access_token'], $_SESSION['refresh_token']);
// }
$SCRIPT = array();
ob_start();
?>
<!DOCTYPE html>
<html lang="pt-br" id="<?php echo ASSETS ?>">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />

  <link rel="shortcut icon" href="<?php echo Imgs::src($CONFIG['logo_favicon_ico'], 'imgs'); ?>" />
  <link rel="icon" type="image/png" href="<?php echo Imgs::src($CONFIG['logo_favicon_png'], 'imgs'); ?>" />
  <link rel="apple-touch-icon-precomposed" sizes="57x57" href="<?php echo Imgs::src($CONFIG['logo_favicon_png'], 'imgs'); ?>" />
  <link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo Imgs::src($CONFIG['logo_favicon_png'], 'imgs'); ?>" />
  <link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo Imgs::src($CONFIG['logo_favicon_png'], 'imgs'); ?>" />

  <?php if (!strstr(URL_BASE, '.test')) { ?>
    <link rel="dns-prefetch" href="//datacontrolinformatica.com.br">
  <?php } else { ?>
    <link rel="dns-prefetch" href="//server.dcisuporte.test">
  <?php } ?>
  <?php if (!strstr(URL_BASE, 'www.')) { ?>
    <link rel="dns-prefetch" href="<?php echo str_replace('://', '://static.', URL_STATIC) ?>">
    <link rel="dns-prefetch" href="<?php echo str_replace('://', '://imagens.', URL_IMAGENS) ?>">
  <?php } else { ?>
    <link rel="dns-prefetch" href="<?php echo URL_STATIC ?>">
    <link rel="dns-prefetch" href="<?php echo URL_IMAGENS ?>">
  <?php } ?>
  <title><?php echo isset($TITLE) && $TITLE ? $TITLE : tituloNomes(str_replace('-', ' ', $PgAt)) . ' - ' ?>Administrativo</title>
  <base href="<?php echo rtrim(dirname($_SERVER['PHP_SELF']), '/\\') ?>/" />
  <?php
  $fontes = array();
  $fontes['awesome']     = PATH_ROOT . '/public/css/awesome.css';
  $fontes['titillium']   = PATH_ROOT . '/public/css/fonte-titillium-web.css';
  $fontes['neosans']     = PATH_ROOT . '/public/css/fonte-neo-sans.css';

  if (count($fontes) > 0) {
    $puts_font = '';
    foreach ($fontes as $font) {
      $puts_font .= file_get_contents($font);
    }
    $minifier = new MatthiasMullie\Minify\CSS($puts_font);
    $font_style = $minifier->minify();
    echo "<style>{$font_style}</style>";
  }
  ?>
  <link href="<?php echo URL_STATIC ?>public/css/adm/css.min.css" rel="stylesheet" type="text/css" media="all" />
  <!--<link href="/public/select2-4.0.3/css/select2.min.css" rel="stylesheet"/>-->
  <link href="/public/select2-4.0.7/css/select2.min.css" rel="stylesheet" />
  <link href="/public/jquery-datetimepicker/jquery.datetimepicker.min.css" rel="stylesheet" type="text/css" />
  <link href="/public/jquery-ui/jquery-ui.min.css" rel="stylesheet" type="text/css" />

  <script src="/public/js/<?php echo ($PgAt == 'configuracoes-pagemantos' ? 'jquery3.2.1.' : 'jquery_1.11.2.') ?>min.js"></script>
  <!-- <script src="/public/select2-4.0.3/js/select2.full.min.js"></script> -->
  <script src="/public/select2-4.0.7/js/select2.full.min.js"></script>
  <script src="/public/select2-4.0.7/js/i18n/pt-BR.js" type="text/javascript"></script>
  <script src="/public/js/full.min.js"></script>
  <script src="/public/jquery-ui/jquery-ui.min.js"></script>
  <script src="/public/js/dialogextend.min.js"></script>
  <script src="<?php echo URL_BASE ?>public/tinymce/jquery.tinymce.min.js" type="text/javascript"></script>
  <script src="<?php echo URL_BASE ?>public/tinymce/tinymce.min.js" type="text/javascript"></script>
  <script src="/public/jquery-datetimepicker/jquery.datetimepicker.full.min.js"></script>
  <!--
        <script src="/public/mask/jquery.mask.min.js"></script>
		<script src="/public/js/maskprecos/jquery.maskPrecos.js"></script>
        <script src="/public/js/jquery.form.js"></script>
		<script src="/public/tinymce/tinymce.min.js" ></script>
		-->

  <link href="<?php echo URL_BASE ?>public/chart/dist/Chart.min.css" rel="stylesheet" type="text/css" />
  <script src="<?php echo URL_BASE ?>public/chart/dist/Chart.min.js" type="text/javascript"></script>

  <style rel="stylesheet" type="text/css" media="all">
    <?php ob_start(); ?>@media print {
      @page {
        margin: 0 0.5cm;
        padding: 0 !important;
      }

      * {
        background: transparent !important;
        color: #000 !important;
        text-shadow: none !important;
        filter: none !important;
        -ms-filter: none !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
      }

      body {
        margin: 0 !important;
        padding: 0 !important;
        line-height: 1.4em !important;
        font-size: 12px !important;
      }

      .plano-fundo-adm-001 {
        background-color: #10416c !important;
        color: #fff !important;
        width: 100% !important;
        visibility: visible !important;
      }

      .topo {
        display: none !important;
      }

      .conteudos {
        margin: 0 !important;
        padding: 0 !important;
      }
    }

    html {
      padding-top: 0 !important;
    }

    body {
      padding-top: 55px;
    }

    .in-hover:hover {
      background-color: #ffdfdf !important;
      cursor: pointer;
    }

    .in-hover:focus {
      outline: none;
      background-color: #ffdfdf !important;
    }

    .ui-tooltip {
      font-size: 12px;
    }

    .ui-dialog,
    #ui-datepicker-div {
      font-size: 14px;
    }

    .ui-widget-header,
    .ui-dialog-titlebar {
      background-image: none;
      background-color: #10416c;
      color: #fff;
    }

    .ui-button,
    .ui-button-text {
      outline: 0 !important;
      /*display: none !important;*/
    }

    fieldset,
    .fieldset {
      border: solid 1px #ddd;
      padding: 15px;
      -webkit-border-radius: 3px;
      -moz-border-radius: 3px;
      border-radius: 3px;
    }

    fieldset legend,
    .fieldset legend {
      display: inline;
      border: none;
      width: auto;
      padding: 5px;
      font-size: 12px;
      margin-bottom: 0;
    }

    [incluir='0'],
    [alterar='0'],
    [excluir='0'],
    [acessar='0'] {
      display: none;
    }

    .select2-container .select2-selection--single,
    .select2-container--default .select2-selection--multiple {
      height: 34px;
      /*border-bottom-width: 3px;*/
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
      line-height: 32px;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
      height: 34px;
    }

    /* bootstrap */
    .col-no-gutters {
      padding: 0
    }

    label span,
    label font,
    label small {
      font-weight: normal;
    }

    .fixed-footer {
      position: fixed;
      bottom: 0;
      left: 0;
      margin: 0;
      width: 100%;
      z-index: 999;
      border-left-width: 0;
      border-right-width: 0;
      border-bottom-width: 0;
      -webkit-border-radius: 0;
      -moz-border-radius: 0;
      border-radius: 0;
    }

    .cx-cor {
      position: relative;
      overflow: hidden;
      width: 15px;
      height: 15px;
      display: block;
      border-color: #444;
      border-width: 1px;
      border-style: solid;
    }

    .cx-cor .cx-cor-001 {
      position: absolute;
      z-index: 0;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      overflow: hidden;
    }

    .cx-cor .cx-cor-002 {
      position: absolute;
      width: 0;
      height: 0;
      font-size: 0;
      overflow: hidden;
      top: 50%;
      left: 50%;
      border-bottom: 200px solid transparent;
      border-left: 200px solid transparent;
      margin-left: -100px;
      margin-top: -100px;
    }

    /**
			 * Novo modo do menus
			 */
    #topo {
      overflow: visible;
      position: fixed;
      top: 0;
      left: 0;
      margin: 0;
      z-index: 99;
      height: 100%;
      width: 235px;
      /* overflow-y: scroll; */
    }

    #topo>.container-fluid,
    #topo>.container-fluid>.row {
      height: 100%;
    }

    /* .container {
				max-width: ;
			} */
    /* width */
    /* #topo::-webkit-scrollbar {
				width: 8px;
			} */

    /* Track */
    /* #topo::-webkit-scrollbar-track {
				background: #f1f1f1;
			} */

    /* Handle */
    /* #topo::-webkit-scrollbar-thumb {
				background: #dedede;
			} */

    /* Handle on hover */
    /* #topo::-webkit-scrollbar-thumb:hover {
				background: #fefefe;
			} */

    #topo>div>div>div.menus {
      width: 100%;
      height: 100%;
      height: calc(100% - 55px);
      padding-top: 5px;
      padding-bottom: 15px;
      overflow-y: scroll;
    }

    #topo>div>div>div.menus::-webkit-scrollbar {
      width: 8px;
    }

    #topo>div>div>div.menus::-webkit-scrollbar-track {
      background: #f1f1f1;
    }

    #topo>div>div>div.menus::-webkit-scrollbar-thumb {
      background: #dedede;
    }

    #topo>div>div>div.menus::-webkit-scrollbar-thumb:hover {
      background: #fefefe;
    }

    #topo>div>div>div.menus.pull-right {
      position: fixed;
      top: 0;
      right: 0;
      margin: 0;
      width: 100%;
      background-color: #3b718c;
      z-index: -11;
    }

    #topo>div>div>div.menus ul .navs-menus-aticve>.menu-nivel-1 {
      display: block !important;
      background-color: #fff;
    }

    #topo>div>div>div>ul {
      width: 100%;
      padding: 0;
    }

    #topo>div>div>div>ul>li {
      width: 100%;
      float: none;
      height: auto;
      border-radius: 1px;
    }

    #topo>div>div>div>ul>li::after {
      content: '';
      display: table;
      clear: both;
    }

    #topo>div>div>div>ul>li>.menu-nivel-1 {
      position: relative;
      min-width: 100%;
      width: 100%;
    }

    #topo>div>div>div.menus>ul>li>.menu-nivel-1 a {
      font-size: 11px;
      padding: 8px;
    }

    /* #topo > div > div > div.menus>ul>li>.menu-nivel-1 a.links-ativo::after {
				font-size: 22px;
				content: '\25C4';
				display: table;
				color: #fff;
				float: right;
				margin: 0;
				margin-top: -8px;
				margin-right: -11px;
			} */
    #topo>div>div>div>ul>li.pull-right {
      position: fixed;
      top: 0;
      right: 0;
      min-width: 100%;
      width: 100%;
      height: 55px;
      background-color: #0d3456;
    }

    #topo>div>div>div>ul>li.pull-right>span {
      height: 55px;
      width: 175px;
      float: right;
      z-index: -1;
    }

    #topo>div>div>div>ul>li.pull-right>span+div {
      top: 0;
      left: auto;
      right: 0px;
      background-color: #fff;
      z-index: 0;
      position: absolute;
      width: 175px;
      min-width: auto;
      margin: 0;
      margin-top: 55px;
    }

    #conteudos-recarregar {
      position: relative;
      margin-left: 235px;
    }

    <?php
    $minifier = new MatthiasMullie\Minify\CSS(ob_get_clean());
    echo $minifier->minify();
    ?>
  </style>
  <?php ob_start(); ?>
  <script>
    $("a.links-ativo").each(function(a, b) {
      $(b).parent().parent().parent().addClass("navs-menus-aticve");
    });
  </script>
  <?php $SCRIPT['script_manual'] .= ob_get_clean(); ?>
</head>

<body>
  <!--<a href="https://chart.googleapis.com/chart?cht=qr&chs=150x150&chl=<?php echo $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"] ?>&choe=UTF-8">ssssss</a>-->

  <div class="topo  clearfix plano-fundo-adm-001" id="topo">
    <div class="container-fluid">
      <div class="row">
        <div class="col-lg-12 col-md-12 co-sm-12" style="z-index: 1; height: 55px;">
          <a href="/adm/" style="line-height: 45px;" class="show">
            <img src="<?php echo Imgs::src('rubrica.png', 'public'); ?>" />
          </a>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 menus">
          <ul class="clearfix">
            <?php

            function searcharray($value, $key, $array)
            {
              foreach ($array as $k => $val) {
                if ($val[$key] == $value) {
                  return true;
                }
              }
              return null;
            }

            function _define_dir($pg = null)
            {
              // Encontra a posição da string
              $i = strpos($pg, '-');

              $append = ($i > 0 ? '/' : '');

              switch ($pg) {
                case 'produtos':
                case 'boletos':
                case 'clientes':
                case 'configuracoes':
                case 'jadlog':
                case 'correios':
                case 'cupons':
                case 'marketing':
                case 'mercadolivre':
                case 'newsletter':
                case 'promocoes':
                case 'relatorio':
                case 'vendas':
                case 'nfe':
                case 'ml':
                case 'skyhub':
                  return implode('/', [(str_replace(['ml'], ['mercadolivre'], $pg)), $pg]) . '.php';
                  break;
              }

              // Define o diretorio do arquivo
              $dir = substr($pg, 0, $i);

              return implode($append, [(str_replace(['ml', 'sub'], ['mercadolivre', null], $dir)), $pg]) . '.php';
            }

            $MenusTopo = Adm::find_by_sql(''
              . 'select SQL_CACHE adm_permissoes.pagina, adm_permissoes.pagina_rename, adm_permissoes.ordem, adm_grupos.grupo, adm.foto '
              . 'from adm_permissoes '
              . 'inner join adm on adm.id = adm_permissoes.id_adm '
              . 'inner join adm_grupos on adm_grupos.id = adm_permissoes.id_adm_grupos '
              . 'where adm_permissoes.status=? and adm_permissoes.id_adm=? and adm_grupos.id > ?'
              . 'group by adm_permissoes.pagina '
              . 'order by adm_grupos.ordem asc, adm_permissoes.ordem asc', [1, $_SESSION['admin']['id_usuario'], 0]);

            $menus = [];
            $usuario_foto = null;
            foreach ($MenusTopo as $loop) {
              $id = $loop->grupo;
              $menus[$id][] = [
                'pagina' => $loop->pagina,
                'pagina_rename' => $loop->pagina_rename
              ];
              $usuario_foto = $loop->foto;
            }

            $divs = 0;
            foreach ($menus as $key => $array) {
              usort($MenusTopo, function ($a, $b) {
                if ($a->ordem == $b->ordem && $a->grupo == $b->grupo) return 0;
                return (($a->ordem > $b->ordem && $a->grupo > $b->grupo) ? -1 : 1);
              });

              // rack
              $PgAt = $PgAt == 'ml-editar'         ? 'ml-produtos' : $PgAt;
              $PgAt = $PgAt == 'ml-categorias'       ? 'ml-produtos' : $PgAt;
              $PgAt = $PgAt == 'produtos-cadastrar'     ? 'produtos' : $PgAt;
              $PgAt = $PgAt == 'vendas-detalhes'       ? 'vendas' : $PgAt;
              $PgAt = $PgAt == 'newsletter-lista'      ? 'newsletter' : $PgAt;
              $PgAt = $PgAt == 'newsletter-criar-emails'  ? 'newsletter' : $PgAt;
              $PgAt = $PgAt == 'newsletter-criar-envios'  ? 'newsletter' : $PgAt;
              // $PgAt = $PgAt == 'clientes-exportar'	    ? 'clientes' : $PgAt;

              $test_pag = searcharray($PgAt, 'pagina', $array);

            ?>
              <li class="navs-menus">
                <a href="javascript:void(0);" class="menu-principal">
                  <?php echo $key ?>
                  <i class="fa fa-chevron-<?php echo $test_pag ? 'down' : 'right' ?> pull-right ft8px mt5"></i>
                </a>
                <div class="menu-nivel-1">
                  <div class="clearfix" style="width: 100%; padding: 5px;">
                    <?php foreach ($array as $loop) { ?>
                      <a href="/adm/<?php echo _define_dir($loop['pagina']) ?>" class="links-lateral show<?php echo ($PgAt == $loop['pagina'] ? ' links-ativo' : ''); ?>" <?php echo _P($loop['pagina'], $_SESSION['admin']['id_usuario'], 'acessar') ?>>
                        <?php echo str_replace('-', ' ', (!empty($loop['pagina_rename']) ? $loop['pagina_rename'] : $loop['pagina'])); ?>
                      </a>
                    <?php } ?>
                  </div>
                </div>
              </li>
            <?php
              $divs++;
            }
            ?>
            <!--[ end loop ]-->
            <li class="navs-menus<?php echo ('nfe-config' == $PgAt || 'nfe-relatorio' == $PgAt || 'nfe-reenvio' == $PgAt) ? ' navs-menus-aticve' : '' ?>">
								<a href="javascript:void(0);" class="menu-principal">NF-<span class="text-lowercase">e</span></a>
								<div class="menu-nivel-1">
									<a href="/adm/nfe/nfe-config.php" class="links-lateral pull-left <?php echo $PgAt == 'nfe-config' ? 'links-ativo' : ''; ?>"<?php echo _P('nfe-config', $_SESSION['admin']['id_usuario'], 'acessar') ?>>
										configurações
									</a>
									<a href="/adm/nfe/nfe-relatorio.php" class="links-lateral pull-left <?php echo $PgAt == 'nfe-relatorio' ? 'links-ativo' : ''; ?>"<?php echo _P('nfe-relatorio', $_SESSION['admin']['id_usuario'], 'acessar') ?>>
										relatórios
									</a>
									<a href="/adm/nfe/nfe-reenvio.php" class="links-lateral pull-left <?php echo $PgAt == 'nfe-reenvio' ? 'links-ativo' : ''; ?>"<?php echo _P('nfe-reenvio', $_SESSION['admin']['id_usuario'], 'acessar') ?>>
										reenvio xml/e-mail
									</a>
								</div>
							</li>
            <li class="navs-menus pull-right<?php // =($PgAt=='lojas' || $PgAt == 'permissao'|| $PgAt == 'backup') ?' navs-menus-aticve':''
                                            ?>">
              <span class="menu-principal">
                <img src="<?php echo isset($usuario_foto) && $usuario_foto != null ? Imgs::src(sprintf('usuarios-%s', $usuario_foto), 'public') : Imgs::src('sem-foto-produto.png', 'public') ?>" class="img-circle" width="35" height="35" style="margin-top: -7px" />
                <?php echo $_SESSION['admin']['apelido']; ?>
              </span>
              <div class="menu-nivel-1" style="right:0;left:auto;">
                <a href="/adm/permissao.php" class="links-lateral pull-left <?php echo $PgAt == 'permissao' ? 'links-ativo' : ''; ?>" <?php echo _P('permissao', $_SESSION['admin']['id_usuario'], 'acessar') ?>>
                  <i class="fa fa-user"></i> usuários/permissões
                </a>

                <a href="/adm/lojas.php?acao=Boletos&loja_id=<?php echo $CONFIG['loja_id'] ?>" class="links-lateral pull-left <?php echo $GET['acao'] == 'Boletos' ? 'links-ativo' : ''; ?>" <?php echo _P('lojas', $_SESSION['admin']['id_usuario'], 'acessar') ?>>
                  <i class="fa fa-gear"></i> faturas/boletos
                </a>

                <a href="/adm/lojas.php?acao=Planos&loja_id=<?php echo $CONFIG['loja_id'] ?>" class="links-lateral pull-left <?php echo $GET['acao'] == 'Planos' ? 'links-ativo' : ''; ?>" <?php echo _P('lojas', $_SESSION['admin']['id_usuario'], 'acessar') ?>>
                  <i class="fa fa-gear"></i> planos
                </a>

                <a href="/adm/backup/backup.php" class="links-lateral pull-left <?php echo $PgAt == 'backup' ? 'links-ativo' : ''; ?>" <?php echo _P('backup', $_SESSION['admin']['id_usuario'], 'acessar') ?>>
                  <i class="fa fa-archive"></i> backup
                </a>

                <a href="/adm/logs.php" class="links-lateral pull-left <?php echo $PgAt == 'logs' ? 'links-ativo' : ''; ?>" <?php echo _P('logs', $_SESSION['admin']['id_usuario'], 'acessar') ?>>
                  <i class="fa fa-bug"></i> logs
                </a>
                <a href="/adm/sair.php?acao=sair" class="links-lateral pull-left">
                  <i class="fa fa-power-off"></i> sair
                </a>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <div class="status-alteracao" id="status-alteracao">
    <div class="container-fluid">-</div>
  </div>
  <div class="text-uppercase container-fluid">
    <p class="text-right"><?php echo (boas_vindas() . ', <strong>' . $CONFIG['nome_fantasia'] . '</strong>') ?></p>
  </div>
  <style>
    .container-fluid .container {
      width: 100%;
    }
  </style>
  <div class="container-fluid mb50" id="conteudos-recarregar">
