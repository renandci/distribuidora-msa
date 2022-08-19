<?php

class BebeChegando extends HelperHtml 
{
	public static function template_set_price_01( $pr_venda = array(), $pr_promo = array(), $pr_parcelas = array() ) {
		
		$html = $pr_venda['preco_venda'] > 0 
			? sprintf('<span class="show %s"><s class="%s">DE R$: %s</s> por </span>', $pr_venda['preco_size'], $pr_venda['preco_color'], $pr_venda['preco_venda']) : '';
			
		$html .= $pr_promo['preco_promo'] > 0 
			? sprintf('<span class="font-extra %s %s show"><span class="ft20px show">R$: %s A VISTA</span></span>', $pr_promo['preco_size'], $pr_promo['preco_color'], $pr_promo['preco_promo']) : '';
			
		$html .= $pr_parcelas['preco_promo'] > 0 
			? sprintf('<span class="mt5 mb15 show">ou em <span class="color-004"> %sx de R$: %s</span> sem juros</span>', $pr_parcelas['preco_parcela_x'], $pr_parcelas['preco_parcela_price']) : '';
			
		$html .= $pr_promo['preco_boleto'] > 0 && $pr_promo['preco_promo'] != $pr_promo['preco_boleto']
			? sprintf('<span class="font-extra color-004 ft14px show">OU R$ %s NO BOLETO</span>', $pr_promo['preco_boleto']) : '';
			
		return $html;
	}
}