<?php

define( 'background001', 	"#dedede" );
define( 'background002', 	"#f3f3f3" );
define( 'background003', 	"#ffffff" );
define( 'background004', 	"#cbc18e" );
define( 'color001', 		"#ffffff" );
define( 'color002', 		"#999999" );
define( 'color003', 		"#000000" );

$STORE['config']['btn-compra'] = array(
    true,
    'text' => 'Comprar',
    'class' => 'btn btn-compra btn-compra-rapida btn-block',
    'class-icon' => 'fa-shopping-cart'
);

$STORE['config']['btn-espiar'] = false;
$CONFIG['fb_link'] = '';
$STORE['config']['facebook']['api'] = '';

global $STORE;
$STORE['TITULO_PAGINA']                          = $CONFIG['nome_fantasia'];
$STORE['title']                                  = $CONFIG['nome_fantasia'];
$STORE['description']                            = $CONFIG['description'];
$STORE['keywords']                               = $CONFIG['keywords'];
$STORE['type']                                   = 'website';
$STORE['image']                                  = Imgs::src( 'logo.gif', 'imgs');
$STORE['url']                                    = $HTTP_HTTPS . $_SERVER['SERVER_NAME'] . $_SERVER ['REQUEST_URI'];
$STORE['canonical']                              = $STORE['url'];
$STORE['viewport']                               = 'width=device-width, initial-scale=1.0';
$STORE['robots']                                 = 'index,follow';
$STORE['theme-color']                            = '#dedede';
$STORE['msapplication-navbutton-color']          = '#dedede';
$STORE['apple-mobile-web-app-capable']           = 'yes';
$STORE['apple-mobile-web-app-status-bar-style']  = 'black-translucent';
$STORE['script_manual']         = '';
$STORE['BIBLIOTECAS']           = '';
$STORE['BIBLIOTECAS_RODAPE']    = '';
$STORE['PRE_LOADED']            = Imgs::src( 'carregar-imagens.gif', 'imgs' );
$STORE['fontes'] = array(
    'public/css/awesome.css',
    'assets/' . ASSETS . '/css/fontello.css',
    'assets/' . ASSETS . '/css/fonte-helveticas.css',
   'public/css/fonte-titillium-web.css',
//    'assets/' . ASSETS . '/css/fonte-dosis.css',
//    'assets/' . ASSETS . '/css/fonte-neo-sans.css',
    'assets/' . ASSETS . '/css/bootstrap-mobile.css',
);
$STORE['frete_prod'] = true;
