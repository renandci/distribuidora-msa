<?php

class Sauin extends HelperHtml 
{
	public static function template_set_price_01( $pr_venda = array(), $pr_promo = array(), $pr_parcelas = array() ) {
		
		$html = $pr_venda['preco_venda'] > 0 
			? sprintf('<span class="price_de"><s class="price_color">DE R$ %s</s></span> ', $pr_venda['preco_venda']) : '';
			
		$html .= $pr_promo['preco_promo'] > 0 
			? sprintf('<span class="price_por price_color_2">POR R$ %s</span>', $pr_promo['preco_promo']) : '';
		
		$html .= $pr_parcelas['preco_promo'] > 0 
			? sprintf('<span class="show mt5 price_parcela">EM ATÉ %sx de R$ %s sem juros</span>', $pr_parcelas['preco_parcela_x'], $pr_parcelas['preco_parcela_price']) : '';
			
		// $html .= $pr_promo['preco_boleto'] > 0 
			// ? sprintf('<span class="mt5 mb15 show"><span class="%s">R$ %s </span> no boleto</span>', $pr_parcelas['preco_color'], $pr_promo['preco_boleto']) : '';
			
		return $html;
	}
}