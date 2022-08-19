<?php
$BIBLIOTECAS = null;

/**
 * Busca os menus do sistema
 * @bkp ProdutosMenus::getMenus();
 */
$menus = HelperHtml::make_menus($CONFIG['menus']);
// // printf('<!--[<pre>%s</pre>]-->', print_r($menus, 1));
// printf('<pre>%s</pre>', print_r($menus, 1));
// exit;

$sum_qtde = 0;
$sum_price = 0;
$c['produtos'] = null;
if (!empty($CONFIG['carrinho_all']) && $modulo != 'identificacao') foreach ($CONFIG['carrinho_all'] as $car) {

  $sum_qtde += $car->quantidade;
  $sum_price += ($car->preco_promo * $car->quantidade);

  $c['produtos'][] = [
    'imagem' => Imgs::src($car->imagem, 'smalls'),
    'nome_produto' => $car->nome_produto,
    'quantidade' => $car->quantidade,
    'preco_promo' => number_format($car->preco_promo, 2, ',', '.'),
  ];
}

$c['quantidade'] = $sum_qtde;
$c['preco_carrinho'] = number_format($sum_price, 2, ',', '.');

// printf('<pre>%s</pre>', print_r(json_encode($c), 1));
?>
<!DOCTYPE html>
<html lang="pt-BR" data-store="<?php echo ASSETS ?>" id="<?php echo ASSETS ?>">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="x-ua-compatible" content="ie=edge,chrome=1" />
  <title><?php echo $STORE['TITULO_PAGINA']; ?></title>

  <link rel="dns-prefetch" href="<?php echo URL_STATIC ?>" />
  <link rel="dns-prefetch" href="//imgx.datacontrolinformatica.com.br" />
  <link rel="preconnect" href="//imgx.datacontrolinformatica.com.br" crossorigin />

  <link rel="dns-prefetch" href="//www.facebook.com" />
  <link rel="dns-prefetch" href="//connect.facebook.net" />
  <link rel="dns-prefetch" href="//static.ak.facebook.com" />
  <link rel="dns-prefetch" href="//static.ak.fbcdn.net" />
  <link rel="dns-prefetch" href="//s-static.ak.facebook.com" />
  <link rel="dns-prefetch" href="//google-analytics.com" />
  <link rel="dns-prefetch" href="//www.google-analytics.com" />
  <link rel="dns-prefetch" href="//platform.twitter.com" />
  <!--<link rel="dns-prefetch" href="//fonts.googleapis.com">-->

  <meta name="theme-color" content="<?php echo $STORE['theme-color'] ?>" />
  <meta name="msapplication-navbutton-color" content="<?php echo $STORE['msapplication-navbutton-color'] ?>" />
  <meta name="apple-mobile-web-app-capable" content="<?php echo $STORE['apple-mobile-web-app-capable'] ?>" />
  <meta name="apple-mobile-web-app-status-bar-style" content="<?php echo $STORE['apple-mobile-web-app-status-bar-style'] ?>" />

  <meta name="description" content="<?php echo $CONFIG['description']; ?>" />
  <meta name="keywords" content="<?php echo $CONFIG['keywords']; ?>" />
  <meta name="viewport" content="<?php echo $STORE['viewport']; ?>" />
  <meta name="robots" content="<?php echo $STORE['robots']; ?>" />

  <meta name="google-tag-verification" content="<?php echo !empty($CONFIG['google_tag_verification']) ? $CONFIG['google_tag_verification'] : '' ?>" />
  <meta name="facebook-domain-verification" content="<?php echo !empty($CONFIG['fb_verification']) ? $CONFIG['fb_verification'] : '' ?>" />

  <meta name="twitter:title" content="<?php echo $STORE['TITULO_PAGINA']; ?>" />
  <meta name="twitter:description" content="<?php echo $STORE['description']; ?>" />
  <meta name="twitter:image" content="<?php echo $STORE['image']; ?>" />
  <meta name="twitter:url" content="<?php echo $STORE['url']; ?>" />
  <meta name="twitter:card" content="summary" />

  <meta property="og:title" content="<?php echo $STORE['TITULO_PAGINA']; ?>" />
  <meta property="og:type" content="<?php echo $STORE['type']; ?>" />
  <meta property="og:description" content="<?php echo $STORE['description']; ?>" />
  <meta property="og:image" content="<?php echo $STORE['image']; ?>" />
  <meta property="og:url" content="<?php echo $STORE['url']; ?>" />
  <meta property="og:site_name" content="<?php echo $CONFIG['nome_fantasia']; ?>" />

  <meta name="cs:page" content="<?php echo !empty($STORE['cs:page']) ? $STORE['cs:page'] : ($modulo == 'index' ? 'home' : '') ?>" />
  <meta name="cs:description" content="<?php echo !empty($STORE['cs:description']) ? $STORE['cs:description'] : '' ?>" />

  <link href="<?php echo $STORE['canonical']; ?>" rel="canonical" />
  <link rel="shortcut icon" href="<?php echo Imgs::src($CONFIG['logo_favicon_ico'], 'imgs'); ?>" />
  <link rel="icon" type="image/png" href="<?php echo Imgs::src($CONFIG['logo_favicon_png'], 'imgs'); ?>" />
  <link rel="apple-touch-icon-precomposed" sizes="57x57" href="<?php echo Imgs::src($CONFIG['logo_favicon_png'], 'imgs'); ?>" />
  <link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo Imgs::src($CONFIG['logo_favicon_png'], 'imgs'); ?>" />
  <link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo Imgs::src($CONFIG['logo_favicon_png'], 'imgs'); ?>" />

  <?php
  if (isset($STORE['fontes']) && is_array($STORE['fontes'])) {
    $STORE['fontes'][] = 'public/css/fontawesome-personalize.css';
    $STORE['fontes'][] = 'public/css/fonte-titillium-web.css';
  }
  $STORE['fontes'][] = '@media(max-width: 768px) {';
  $STORE['fontes'][] = 'html, body{ overflow-x: hidden; }';
  $STORE['fontes'][] = '.btn{ white-space: initial !important; }';
  $STORE['fontes'][] = ($modulo != 'identificacao' ? '.is-init > div { padding-top: 5px; padding-bottom: 25px; }' : '');
  $STORE['fontes'][] = '.banner-index > .owl-controls { position: absolute; }';
  $STORE['fontes'][] = '.owl-controls{ bottom: 0; }';
  $STORE['fontes'][] = '}';
  echo init_fontface($STORE['fontes']);
  ?>

  <?php if (strstr(URL_BASE, '.lojasecommerce.')) { ?>
    <link href="http://cdndci.dcisuporte.test/css/version-css.1.0.min.css" rel="stylesheet" type="text/css" />
  <?php } else { ?>
    <link href="<?php echo URL_STATIC ?>public/css/stylesheet-1.0.min.css" rel="stylesheet" type="text/css" />
  <?php } ?>
  <script>
    <?php ob_start(); ?>
    var settings = settings || {},
      AviseMe = AviseMe || {},
      Checkout = Checkout || {},
      Button = Button || {},
      Produto = Produto || {};
    settings = {
      store: "<?php echo ASSETS ?>",
      lazyplaceholder: "<?php echo $STORE['PRE_LOADED'] ?>",
      frete: {
        texto: [""],
        price: [""]
      }
    };

    <?php
    // instancia e joga o javascript na tela
    $JSqueeze = new Patchwork\JSqueeze();
    $content = $JSqueeze->squeeze(ob_get_clean(), true, false, false);
    echo $content;
    ?>
  </script>

  <?php if (!empty($CONFIG['fb_link'])) { ?>
    <!-- Facebook Code -->
    <script>
      (function(e, t, a) {
        var n, o = e.getElementsByTagName(t)[0];
        if (e.getElementById(a)) return;
        n = e.createElement(t);
        n.id = a;
        n.src = '//connect.facebook.net/pt_BR/sdk.js#xfbml=1&version=v2.4';
        o.parentNode.insertBefore(n, o)
      }(document, 'script', 'facebook-jssdk'));
    </script>
    <!-- End Facebook Code -->
  <?php } ?>

  <?php if (!empty($CONFIG['fb_id'])) { ?>
    <!-- Facebook Pixel Code -->
    <script>
      ! function(f, b, e, v, n, t, s) {
        if (f.fbq) return;
        n = f.fbq = function() {
          n.callMethod ?
            n.callMethod.apply(n, arguments) : n.queue.push(arguments)
        };
        if (!f._fbq) f._fbq = n;
        n.push = n;
        n.loaded = !0;
        n.version = '2.0';
        n.queue = [];
        t = b.createElement(e);
        t.async = !0;
        t.src = v;
        s = b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t, s)
      }(window, document, 'script',
        'https://connect.facebook.net/en_US/fbevents.js');
      fbq('init', '<?php echo $CONFIG['fb_id'] ?>');
      fbq('track', 'PageView');
    </script>
    <!-- End Facebook Pixel Code -->
  <?php } ?>

  <?php if (!empty($CONFIG['google_tag_analytics'])) { ?>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $CONFIG['google_tag_analytics'] ?>"></script>
    <script>
      window.dataLayer = window.dataLayer || [];

      function gtag() {
        dataLayer.push(arguments);
      }
      gtag('js', new Date());
      gtag('config', '<?php echo $CONFIG['google_tag_analytics'] ?>');
    </script>
    <!-- Global site tag (gtag.js) - Google Analytics -->
  <?php } ?>

  <?php if (!empty($CONFIG['google_tag_manager'])) { ?>
    <!-- Google Tag Manager -->
    <script>
      <?php
      // Include Tags dataLayer Google
      if (!empty($CONFIG['google_tag_manager'])) {
        include sprintf('%s/templates/_modules/datalayer.php', PATH_ROOT);
      }
      ?>
        (function(w, d, s, l, i) {
          w[l] = w[l] || [];
          w[l].push({
            'gtm.start': new Date().getTime(),
            event: 'gtm.js'
          });
          var f = d.getElementsByTagName(s)[0],
            j = d.createElement(s),
            dl = l != 'dataLayer' ? '&l=' + l : '';
          j.async = true;
          j.src =
            'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
          f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', '<?php echo $CONFIG['google_tag_manager'] ?>');
    </script>
    <!-- End Google Tag Manager -->
  <?php } ?>

  <?php
  // Com excessao do checkout-new, todas as paginas estarão os scripts abaixo do html
  if ($GET_ACAO != 'checkout-new') ob_start();

  if (strstr(URL_BASE, '.lojasecommerce.')) { ?>
    <script src="http://cdndci.dcisuporte.test/js/version-javascript.1.0.min.js"></script>
    <script src="http://cdndci.dcisuporte.test/js/version-javascript.1.1.min.js"></script>
    <script src="http://cdndci.dcisuporte.test/js/version-javascript.layout.1.0.min.js"></script>
    <script src="<?php echo URL_STATIC ?>public/js/<?php echo ASSETS ?>-1.0.min.js"></script>
  <?php } else { ?>
    <script src="<?php echo URL_STATIC ?>public/js/javascript-1.0.min.js"></script>
    <script src="<?php echo URL_STATIC ?>public/js/<?php echo ASSETS ?>-1.0.min.js"></script>
    <!-- <script src="<?php echo URL_STATIC ?>public/js/mustache.min.js"></script> -->
  <?php }

  if ($GET_ACAO != 'checkout-new') $BIBLIOTECAS .= ob_get_clean();

  ?>
  <base href="<?php echo URL_BASE ?>" />
</head>

<body>
  <?php if (!empty($CONFIG['google_tag_manager'])) { ?>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo $CONFIG['google_tag_manager'] ?>" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
  <?php } ?>

  <?php if (!empty($CONFIG['fb_id'])) { ?>
    <!-- Facebook Pixel Code -->
    <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=<?php echo $CONFIG['fb_id'] ?>&ev=PageView&noscript=1" /></noscript>
    <!-- End Facebook Pixel Code -->
  <?php } ?>
