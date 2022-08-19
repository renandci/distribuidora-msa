<?php

$ARQUIVO_INCLUDE = PATH_ROOT . '/assets/' . ASSETS . '/assets.php';

if ( file_exists( $ARQUIVO_INCLUDE ) ) {
    $ARQUIVO_INCLUDE = include $ARQUIVO_INCLUDE;
}

$GZIPIT_ASSETS_TEMP = [	
	'adm/css' => [
		'type' => 'css',
		'files' => [
			'/public/bootstrap/css/bootstrap.css',
			'/public/css/adm/css.css',
			'/public/colorpicker/css/colorpicker.css',
			// '/public/lazyload/lazyload.js',
			// '/public/js/respond.js',
		]
	],
	
	'jquery-datetimepicker.css' => [
		'type' => 'css',
		'files' => [
			'/public/jquery-datetimepicker/jquery.datetimepicker.css',
		]
	],
	'jquery-datetimepicker.js' => [
		'type' => 'javascript',
		'files' => [
			'/public/jquery-datetimepicker/jquery.datetimepicker.js',
		]
	],
	
	'jquery_1.11.2' => [
		'type' => 'javascript',
		'files' => [
			'/public/js/respond.js',
			'/public/js/jquery-1.11.2.min.js',
			'/public/jqvalidate/jquery.validate.js',
			'/public/colorpicker/js/colorpicker.js',
			'/public/js/jquery.form.js',
			'/public/mask/jquery.mask.js',
		]
	],
	
	'dialogextend' => [
		'type' => 'javascript',
		'files' => [	
            '/public/jquery-ui/jquery.dialogextend.js',
		]
	],
	
	'full' => [
		'type' => 'javascript',
		'files' => [	
			'/public/js/adm/jquery.adm.js',
			'/public/js/adm/jquery.banners.js',
			// '/adm/produtos/js/jquery.produtos.menus-submenus.js',
			// '/adm/produtos/js/jquery.produtos.js',
			// '/adm/produtos/js/jquery.produtos.cores.js',
			// '/adm/produtos/js/jquery.produtos.tamanhos.js',
			// '/adm/produtos/js/jquery.produtos.cores-tamanhos.js',
			// '/adm/produtos/js/jquery.produtos.fotos.js',
			// '/adm/vendas/js/jquery.vendas.js',
			// '/adm/vendas/js/jquery.vendas.detalhes.js',
		]
	],
	
	'jqzoom-core' => [
		'type' => 'javascript',
		'files' => [
			'public/jqzoom/jquery-1.6.js',
			'public/jqzoom/jquery.jqzoom-core.js',
		]
	],
	
	'jquery3.2.1' => [
		'type' => 'javascript',
		'files' => [
			'/public/js/respond.js',
			'/public/js/jquery3.2.1.js',
			'/public/mask/jquery.mask.js',
			'/public/js/jquery.form.js',
		]
	],
	
	'checkout-new' => [
		'type' => 'javascript',
		'files' => [
			'/public/js/checkout-new.js',
		]
	],
	
	// /**
	 // * VERSOES DE CSS
	 // * Css definido para o template do site
	 // */
	// 'version-css.1.0' => [
		// 'type' => 'css',
		// 'files' => [
		
			// 'public/css/awesome.css',
			// 'public/bootstrap/css/bootstrap.css',
			// 'public/jquery-card/card.css',
			// 'public/jqzoom/jquery.jqzoom.css',
			// 'public/owl-carousel-v1.3.3/owl.carousel.css',
            // 'public/owl-carousel-v1.3.3/owl.theme.css',
            // 'public/owl-carousel-v1.3.3/owl.transitions.css',
			// 'public/fancybox/jquery.fancybox.css',
			
			// 'assets/' . ASSETS . '/css/css.min.css',
			// 'assets/' . ASSETS . '/css/index.css',
			// 'assets/' . ASSETS . '/css/produtos.css',
			// 'assets/' . ASSETS . '/css/produto.css',
			
			// 'public/css/identificacao.css',
			
		// ]
	// ],
	
	// /**
	 // * VERSOES DE JAVASCRIPT
	 // * Javascript definido para o template do site
	 // */
	// 'version-javascript.1.0' => [
		// 'type' => 'javascript',
		// 'files' => [
			// 'public/js/respond.js',
			// 'public/js/jquery-1.9.1.min.js',
			// 'public/jquery-card/card.js',
			// 'public/lazyload/lazyload.js',
			// 'public/fancybox/jquery.fancybox.pack.js',
			// 'public/mask/jquery.mask.min.js',
			// 'public/owl-carousel-v1.3.3/owl.carousel.js',
			// 'public/jqvalidate/jquery.validate.js',
			// 'public/js/aviseme.js',
			// 'public/js/acoes-site.js',
			// 'public/js/index.js',
			// 'public/js/produtos.js',
			
			// // '/public/js/checkout-new.js',
		// ]
	// ],	
	
	'produto-1.0' => [
		'type' => 'javascript',
		'files' => [			
			'public/js/produto.js',
		]
	],
	
	// ----------------------------------------------------------------------------- //
	// Scripts do sistema
    // Dividir os arquivos
	'javascript-1.0' => [
		'type' => 'javascript',
		'files' => [
			'public/js/jquery-1.11.2.min.js',
			'public/lazyload/lazyload.js',
			'public/bootstrap/js/bootstrap.js',
			'public/fancybox/jquery.fancybox.pack.js',
			'public/jqvalidate/jquery.validate.js',
			// 'public/jqvalidate/jquery.validate.additional-methods.js',
			'public/jquery-card/jquery.card.js',
			'public/owl-carousel-v1.3.3/owl.carousel.js',
			'public/mask/jquery.mask.js',
		]
	],
	
	'mustache' => [
		'type' => 'javascript',
		'files' => [
			'public/js/mustache.js'
		]
	],
	
	ASSETS . '-1.0' => [
		'type' => 'javascript',
		'files' => [
			'public/js/respond.js',
			'public/js/goodshare.min.js',
			'public/js/instagram-feed.js',
			'public/elevatezoom/jquery.elevatezoom.js',
			'public/js/aviseme.js',
			'public/js/acoes-site.js',
			'public/js/index.js',
			'public/js/produtos.js',
			'public/js/produto.js',
		]
	],
	
	'stylesheet-1.0' => [
		'type' => 'css',
		'files' => [
			'public/bootstrap/css/bootstrap.css',
			'public/jquery-card/card.css',
            'public/owl-carousel-v1.3.3/owl.carousel.css',
            'public/owl-carousel-v1.3.3/owl.theme.css',
			'public/fancybox/jquery.fancybox.css',
			'public/css/aviseme.css',
			'public/css/identificacao.css',
		]
	],
	// ----------------------------------------------------------------------------- //
];

$GZIPIT_ASSETS = array_merge_recursive ( $GZIPIT_ASSETS_TEMP, $ARQUIVO_INCLUDE);
