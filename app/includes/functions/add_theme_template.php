<?php
/**
 * Função para gerar um popup de demostração do frete que há no site
 * @param $image string Nome da imagem a ser carregado
 * @param $w int Largura da imagem
 * @param $h int Altura da imagem
 * @param $r int Borda radius
 * @return Html em forma de popup
 */

function popup_frete( $image = '', $w = '616', $h = '281', $r = '10' ) {
	if( ! $image ) return false;
	$html_popup = null;
	ob_start(); 
	?>
	<script>
		$("[data-frete=regras]").click(function(){
			$("<div/>", {
				style : "position: fixed; z-index: 998; width: 100%; height: 100%; top: 0; left: 0; margin: 0; background-image: url(<?=Imgs::src("overlay-box.png", "imgs")?>); background-repeat: repeat;",
				append:[
					$("<div/>", {
						style: "-webkit-border-radius: <?=$r?>px; -moz-border-radius: <?=$r?>px; border-radius: <?=$r?>px; overflow: hidden; position: fixed; z-index: 999; width: <?=$w?>px; height: <?=$h?>px; top: 50%; left: 50%; margin: -<?=str_replace(',', '.', ($h/2))?>px 0 0 -<?=str_replace(',', '.',($w/2))?>px; text-align: center;",
						append:[
							$("<img/>", {
								src: "<?=Imgs::src($image, "imgs")?>",
								alt: "Regras de frete grátis"
							}),
							$("<a/>",{
								style : "position: absolute; z-index: 1001; top: 0; right: 0; margin: 3px 7px; width: 42px; height: 40px;",
								href : "javascript:void(0)",
								click : function(){
									$(this).parent().parent().remove();
								}
							})
						]
					})
				]
			}).appendTo("body");
		})
	</script>
	<?php
	$html_popup .= ob_get_contents();
	ob_end_clean();
	return $html_popup;
}