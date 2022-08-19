<?php

class CchicCom extends HelperHtml 
{	
	/**
	 * @return template_set_price_01 padrao lojas
	 */
	public static function template_set_price_01( $pr_venda = array(), $pr_promo = array(), $pr_parcelas = array() ) {
		$html = $pr_venda['preco_venda'] > 0 
			? sprintf('<span class="price_venda show %s"><s class="%s">DE R$: %s</s> por </span>', $pr_venda['preco_size'], $pr_venda['preco_color'], $pr_venda['preco_venda']) : '';
			
		$html .= $pr_promo['preco_promo'] > 0 
			? sprintf('<span class="price_promo %s %s show">R$: %s</span>', $pr_promo['preco_size'], $pr_promo['preco_color'], (!empty($pr_promo['preco_boleto']) ? $pr_promo['preco_boleto']:$pr_promo['preco_promo'])) : '';
		
		$html .= $pr_parcelas['preco_promo'] > 0 
			? sprintf('<span class="price_parcelamento mt5 mb15 show">ou em <span class="%s"> %sx de R$: %s </span> sem juros</span>', $pr_parcelas['preco_color'], $pr_parcelas['preco_parcela_x'], $pr_parcelas['preco_parcela_price']) : '';
		return $html;
	}
	
	/**
	 * @return template_set_view_product_price padrao lojas
	 */
	public static function template_set_view_product_price( $pr_venda = array(), $pr_promo = array(), $pr_parcelas = array() ) {
		$html = $pr_venda['preco_venda'] > 0 
			? sprintf('<span class="show %s"><s class="%s">DE R$: %s</s> por </span>', $pr_venda['preco_size'], $pr_venda['preco_color'], $pr_venda['preco_venda']) : '';
			
		$html .= $pr_promo['preco_promo'] > 0 
			? sprintf('<span class="%s %s show">R$: %s</span>', $pr_promo['preco_size'], $pr_promo['preco_color'], (!empty($pr_promo['preco_boleto']) ? $pr_promo['preco_boleto']:$pr_promo['preco_promo'])) : '';
		
		$html .= $pr_parcelas['preco_promo'] > 0 
			? sprintf('<span class="mt5 mb15 show">ou em <span class="%s"> %sx de R$: %s </span> sem juros</span>', $pr_parcelas['preco_color'], $pr_parcelas['preco_parcela_x'], $pr_parcelas['preco_parcela_price']) : '';
		return $html;
	}	
}