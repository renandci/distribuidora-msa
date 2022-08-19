<?php

class LidiEnxovais extends HelperHtml
{
  public static function template_set_price_01($pr_venda = array(), $pr_promo = array(), $pr_parcelas = array())
  {
    $html = $pr_venda['preco_venda'] > 0
      ? sprintf('<span class="ml10 show %s"><s class="%s">DE R$ %s por </span></s>', $pr_venda['preco_size'], $pr_venda['preco_color'], $pr_venda['preco_venda']) : '';

    $html .= $pr_promo['preco_boleto'] > 0
      ? sprintf('<span class="ml10 %s %s show"><b>R$ %s</b><span class="ft14px"> <b class="show">Boleto, Pix ou 1x no cartão</b></span></span>', $pr_promo['preco_size'], $pr_promo['preco_color'], $pr_promo['preco_boleto'])
      : sprintf('<span class="ml10 %s %s show"><b>R$ %s</b><span class="ft14px"> <b class="show">Boleto, Pix ou 1x no cartão</b></span></span>', $pr_promo['preco_size'], $pr_promo['preco_color'], $pr_promo['preco_promo']);


    $pr_parcelas['preco_parcela_price'] = $pr_parcelas['preco_parcela_x'] == 1 ? $pr_promo['preco_boleto'] : $pr_parcelas['preco_parcela_price'];

    $html .= $pr_parcelas['preco_promo'] > 0
      ? sprintf('<span class="font-extra ml10 show color-001 ft16px"><span> %sx de R$ %s </span> s/ juros</span>', $pr_parcelas['preco_parcela_x'], $pr_parcelas['preco_parcela_price']) : '';

    return $html;
  }

  public static function template_set_view_product_price($pr_venda = array(), $pr_promo = array(), $pr_parcelas = array())
  {

    $pr_parcelas['preco_parcela_price'] = $pr_parcelas['preco_parcela_x'] == 1 ? $pr_promo['preco_boleto'] : $pr_parcelas['preco_parcela_price'];

    $html = $pr_venda['preco_venda'] > 0
      ? sprintf('<span class="show %s"><s class="%s">de R$: %s por: </s></span>', $pr_venda['preco_size'], $pr_venda['preco_color'], $pr_venda['preco_venda']) : '';

    $html .= $pr_promo['preco_promo'] > 0
      ? sprintf('<span class="font-extra %s %s show ft25px">R$: %s <small class="ft12px">Boleto, Pix ou 1x no cartão</small></span>', $pr_promo['preco_size'], 'price-boleto', (!empty($pr_promo['preco_boleto']) ? $pr_promo['preco_boleto'] : $pr_promo['preco_promo'])) : '';

    $html .= $pr_parcelas['preco_promo'] > 0 && !empty($pr_promo['preco_boleto'])
      ? sprintf('<span class="font-extra mt5 show" onmouseover="$(\'.parcelamento-produto\').stop().fadeIn(110);$(this).css({\'cursor\': \'pointer\'})" onmouseout="$(\'.parcelamento-produto\').fadeOut(550);">ou R$ %s <br/><span class="%s ft25px"> %sx de R$: %s sem juros <i class="fa fa-sort-desc ft18px" aria-hidden="true"></i></span></span>', number_format($pr_parcelas['preco_promo'], 2, ',', '.'), $pr_parcelas['preco_color'], $pr_parcelas['preco_parcela_x'], $pr_parcelas['preco_parcela_price'])
      : sprintf('<span class="font-extra mt5 show" onmouseover="$(\'.parcelamento-produto\').stop().fadeIn(110);$(this).css({\'cursor\': \'pointer\'})" onmouseout="$(\'.parcelamento-produto\').fadeOut(550);">ou em <span class="%s"> %sx de R$: %s </span> sem juros <i class="fa fa-sort-desc ft18px" aria-hidden="true"></i></span>', $pr_parcelas['preco_color'], $pr_parcelas['preco_parcela_x'], $pr_parcelas['preco_parcela_price']);

    $html .= '<ul class="parcelamento-produto black-60" style="display: none; top: 100%;" onmouseover="$(this).fadeIn(0).stop();" onmouseout="$(this).fadeOut(0);">';
    $html .= '<li class="font-extra ft14px">Parcelamento no cartão de crédito</li>';
    for ($i = 1; $i <= parcelamento($pr_promo['preco_promo'], $pr_parcelas['qtde_parcelas'], $pr_parcelas['parcela_minima']); $i++) {
      $html .= sprintf('<li class="ft13px">%u %s de R$ %s</li>', $i, $i > 1 ? 'parcelas' : 'parcela', number_format($pr_promo['preco_promo'] / $i, 2, ',', '.'));
    }
    $html .= '</ul>';
    $porc = $pr_venda['preco_venda'] > 0 ? ($pr_promo['preco_promo'] * 100) / $pr_venda['preco_venda'] : 0;

    $html .= $pr_venda['preco_venda'] > 0 ? sprintf('<span class="display-porcetagem">%s%%</span>', round($porc - 100, 0)) : null;

    return $html;
  }
}
