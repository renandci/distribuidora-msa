<!--[
 **
 * @store: <?php echo $CONFIG['nome_fantasia']?> 
 * @link: <?php echo URL_BASE?> 
 * @copyright: Data Control Informatica - (16) 3262-1365 
 * @author: Renan Henrique <renan@dcisuporte.com.br/>
 * 
]-->
<!DOCTYPE html>
<html lang="pt-BR" data-store="<?php echo ASSETS?>" id="<?php echo ASSETS?>">
	<head>
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
		<?php if( ! strstr( URL_BASE, '.test' ) ) { ?> 
		<link rel="dns-prefetch" href="//ns1.dcisuporte.com.br"/>
		<?php } else { ?> 
		<link rel="dns-prefetch" href="//server.dcisuporte.test">
		<?php } ?>
		<?php if( ! strstr( URL_BASE, 'www.' ) ) { ?> 
		<link rel="dns-prefetch" href="<?php echo str_replace( '://', '://static.', URL_STATIC )?>">
		<link rel="dns-prefetch" href="<?php echo str_replace( '://', '://imagens.', URL_IMAGENS )?>">
		<?php } else { ?> 
		<link rel="dns-prefetch" href="<?php echo URL_STATIC?>">
		<link rel="dns-prefetch" href="<?php echo URL_IMAGENS?>">
		<?php } ?> 		
		<link rel="dns-prefetch" href="//www.facebook.com">
		<link rel="dns-prefetch" href="//connect.facebook.net">
		<link rel="dns-prefetch" href="//static.ak.facebook.com">
		<link rel="dns-prefetch" href="//static.ak.fbcdn.net">
		<link rel="dns-prefetch" href="//s-static.ak.facebook.com">
		<link rel="dns-prefetch" href="//google-analytics.com">
		<link rel="dns-prefetch" href="//www.google-analytics.com">
		<!--<link rel="dns-prefetch" href="//fonts.googleapis.com">-->
		<!--<link rel="dns-prefetch" href="//platform.twitter.com">-->
		
        <meta name="theme-color" content="<?php echo $STORE['theme-color']?>"/>
        <meta name="msapplication-navbutton-color" content="<?php echo $STORE['msapplication-navbutton-color']?>"/>
        <meta name="apple-mobile-web-app-capable" content="<?php echo $STORE['apple-mobile-web-app-capable']?>"/>
        <meta name="apple-mobile-web-app-status-bar-style" content="<?php echo $STORE['apple-mobile-web-app-status-bar-style']?>"/>

        <meta name="description" content="<?php echo $STORE['description'];?>" />
        <meta name="keywords" content="<?php echo $STORE['keywords'];?>" />
        <meta name="viewport" content="<?php echo $STORE['viewport'];?>" />
        <meta name="robots" content="<?php echo $STORE['robots'];?>" />		

        <meta name="twitter:title" content="<?php echo $STORE['TITULO_PAGINA'];?>" />
        <meta name="twitter:description" content="<?php echo $STORE['description'];?>" />
        <meta name="twitter:image" content="<?php echo $STORE['image'];?>" />
        <meta name="twitter:url" content="<?php echo $STORE['url'];?>" />
        <meta name="twitter:card" content="summary" />

        <meta property="og:title" content="<?php echo $STORE['TITULO_PAGINA'];?>" />
        <meta property="og:type" content="<?php echo $STORE['type'];?>" />
        <meta property="og:description" content="<?php echo $STORE['description'];?>" />
        <meta property="og:image" content="<?php echo $STORE['image'];?>" />
        <meta property="og:url" content="<?php echo $STORE['url'];?>" />
        <meta property="og:site_name" content="<?php echo $CONFIG['nome_fantasia'];?>" />

        <title><?php echo $STORE['TITULO_PAGINA'];?></title>

        <link href="<?php echo $STORE['canonical'];?>" rel="canonical"/>
        <link rel="shortcut icon" href="<?php echo Imgs::src('favicon.ico', 'imgs');?>" />
        <link rel="icon" type="image/png" href="<?php echo Imgs::src('favicon.png', 'imgs');?>" />
        <link rel="apple-touch-icon-precomposed" sizes="57x57" href="<?php echo Imgs::src('favicon.png', 'imgs');?>"/>
        <link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo Imgs::src('favicon.png', 'imgs');?>"/>
        <link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo Imgs::src('favicon.png', 'imgs');?>"/>

        <?php
        if (isset($STORE['fontes']) && is_array($STORE['fontes'])) :
			$STORE['fontes'][] = 'public/css/fonte-titillium-web.css';
            echo init_fontface($STORE['fontes']);
        endif;
        
		$Fretes = ConfiguracoesFretesGratis::all([ 
			'conditions' => [ 'loja_id = ?', $CONFIG['loja_id']], 
			'select' => 'id, descricao, uf, frete_valor', 
			'group' => 'uf, frete_valor' 
		]);
		
		$frete_array = [];
		
		foreach ($Fretes as $frete) 
		{
			$STORE['descricao'][] = $frete->descricao ? $frete->descricao : $frete->uf;
			$STORE['frete_valor'][] = number_format($frete->frete_valor, 2, '.', '');
			
			if( ! empty( $frete->frete_valor ) ) 
			{
				$frete_array[ $frete->frete_valor ]['frete_valor'] = '<b>R$: ' . number_format($frete->frete_valor, 2, ',', '.') . '</b><br/>';
			}
			
			if( ! empty( $frete->uf ) ) 
			{
				$frete_array[ $frete->frete_valor ]['frete_uf'][] = '<b>' . $frete->uf . '</b>';
			}
			
			if( !empty($frete->descricao) ) {
				$frete_array[ $frete->frete_valor ]['frete_descricao'] = $frete->descricao;
			}
		}
		
		// Gera um pre texto
		foreach( $frete_array as $group => $frt_txt ) 
		{
			if ( count( $frt_txt['frete_uf'] ) ) 
			{
				if( strstr($frt_txt['frete_descricao'], '*valor*') || strstr($frt_txt['frete_descricao'], '(*uf*)') )
				{
					$STORE['frete_descricao'][] = str_replace(['*valor*', '*uf*'], [$frt_txt['frete_valor'], implode(',', $frt_txt['frete_uf'])], $frt_txt['frete_descricao']);
				} 
				else 
				{
					$frt_txt['frete_descricao'] = $frt_txt['frete_descricao'] . ' *valor* (*uf*)';
					$STORE['frete_descricao'][] = str_replace(['*valor*', '*uf*'], [$frt_txt['frete_valor'], implode(',', $frt_txt['frete_uf'])], $frt_txt['frete_descricao']);
				}
			} else {
				$STORE['frete_descricao'][] = $frt_txt['frete_descricao'];
			}
		}
		
		// Seta na conf do site
		// Todo o conteudo sera convertido dentro da HelperHtml::popup_frete();
		if( ! empty( $STORE['frete_descricao'] ) ) {
			$CONFIG['frete_text'][$CONFIG['loja_id']] = super_unique($STORE['frete_descricao']);
		}
		
		// print_r($CONFIG['frete_text'][$CONFIG['loja_id']]);
		
		?>
		<style>
			@media(max-width: 768px) {
				.table-responsive{
					overflow-x: auto;
				}
				.btn{ 
					white-space: initial !important; 
				}
				<?php if($modulo != 'identificacao') { ?>
				.is-init > div {
					padding-top: 5px;
					padding-bottom: 25px;
				}
				<?php } ?>
			}
			.banner-index > .owl-controls {
				position: absolute;
			}
		</style>
		<link href="https://cdndci.dcisuporte.com.br/css/version-css.1.0.min.css" rel="stylesheet" type="text/css"/>
		<link href="<?php echo URL_STATIC?>public/css/stylesheet-1.0.min.css" rel="stylesheet" type="text/css"/>
        <script type="text/javascript">
            var settings = settings || {}, 
				AviseMe = AviseMe || {},
				Checkout = Checkout || {};
            settings = {
                store: "<?php echo ASSETS?>",
                lazyplaceholder:"<?php echo $STORE['PRE_LOADED']?>",
                frete: {
                    texto: ["<?php // echo is_array($STORE['descricao']) ? implode('","', $STORE['descricao']) : ''?>"],
                    price: ["<?php // echo is_array($STORE['frete_valor']) ? implode('","', $STORE['frete_valor']) : ''?>"]
                }
            };
        </script>
        <base href="<?php echo URL_BASE?>"/>
		<?php
		// Com excessao do checkout-new, todas as paginas estarão os scripts abaixo do html
        $BIBLIOTECAS = null;
        if( Url::getURL(1) != 'checkout-new' ) : 
            ob_start();
        endif;
        ?>
        <!--
        <script src="https://cdndci.dcisuporte.com.br/js/version-javascript.1.0.min.js"></script>
		-->
		<script src="<?php echo URL_STATIC?>public/js/javascript-1.0.min.js"></script>
        <script src="<?php echo URL_STATIC?>public/js/<?php echo ASSETS?>-1.0.min.js"></script>
        <?php 
        if( Url::getURL(1) != 'checkout-new' ) :
            $BIBLIOTECAS = ob_get_clean(); 
            
        endif;
		?>
	</head>
    <body>
        <?php
        /**
         * Busca os menus do sistema
		 * @bkp ProdutosMenus::getMenus();
         */
        $menus = ProdutosMenus::prod_get_grupos();
        
        /**
         * Gera o carrinho de compras para usuarios
         */
        $c['quantidade'] = '0';
        $c['preco_carrinho'] = '0.00';
		
        $Carrinho = Carrinho::find([
            'select' => ''
                      . 'case when carrinho.quantidade > 0 then sum(carrinho.quantidade) else 0 end as quantidade, '
                      . 'case when produtos.preco_promo > 0 then sum( produtos.preco_promo * carrinho.quantidade ) else 0.00 end as preco_carrinho ',
            'joins' => ['inner join produtos on produtos.id = carrinho.id_produto'],
            'conditions' => ['carrinho.id_session=?', session_id()]
        ]);
		
		$c = $Carrinho->to_array();
		
        $c['preco_carrinho'] = number_format($c['preco_carrinho'], 2, ',', '.');
		