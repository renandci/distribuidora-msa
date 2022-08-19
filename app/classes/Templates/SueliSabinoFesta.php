<?php

class SueliSabinoFesta extends HelperHtml 
{
	public static function template_set_price_01( $pr_venda = array(), $pr_promo = array(), $pr_parcelas = array() ) {
		$html = $pr_venda['preco_venda'] > 0 
			? sprintf('<span class="show"><s class="%s">DE R$ %s POR</s></span>', $pr_venda['preco_color'], $pr_venda['preco_venda']) : '';
			
		$html .= $pr_promo['preco_boleto'] > 0 
			? sprintf('<span class="font-extra %s %s show">R$ %s a Vista</span>', $pr_promo['preco_size'], $pr_promo['preco_color'], $pr_promo['preco_boleto']) : '';
		
		$html .= $pr_parcelas['preco_promo'] > 0 
			? sprintf('<span class="mt5 %s show">ou %sx de R$ %s</span>', $pr_parcelas['preco_color'], $pr_parcelas['preco_parcela_x'], $pr_parcelas['preco_parcela_price']) : '';
			
		// $html .= $pr_promo['preco_promo'] > 0 
		// 	? sprintf('<span class="mt5 mb15 show text-uppercase" style="font-weight: bold; color: #b5b5b7 !important;">R$ %s</span>', $pr_promo['preco_promo']) : '';
			
		return $html;
	}
	
	public static function personalize_price_view_product( $pr_venda = array(), $pr_promo = array(), $pr_parcelas = array() ) {
		return parent::template_set_price_01( $pr_venda, $pr_promo, $pr_parcelas );
	}
}