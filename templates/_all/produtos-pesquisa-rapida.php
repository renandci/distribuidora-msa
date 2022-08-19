<?php

/**
 * conditions de Pesquisa no site
 */
$str['results'] = null;
if (!empty($GET['pesquisar']) && $GET['pesquisar'] != '') {
  $A = queryInjection('%%%s%%', $GET['pesquisar']);
  $B = implode('%" OR nome_produto like "%', explode(' ', queryInjection('%%%s%%', str_replace([' de', ' para', ' com', ' a', ' o', ' da'], null, $GET['pesquisar']))));

  $Conditions = null;
  $Conditions['conditions'] = '(loja_id=%u AND(nome_produto like "%s" AND(nome_produto like %s OR (codigo_produto like "%s")))) ';
  $Conditions['conditions'] = sprintf($Conditions['conditions'], $CONFIG['loja_id'], $A, "\"{$B}\"", $A);
  $Conditions['limit'] = 5;

  $Produtos = Produtos::all($Conditions);

  foreach ($Produtos as $r) {
    $str['results'][] = [
      'id' => $r->codigo_id,
      'text' => $r->nome_produto,
      'image' => Imgs::src($r->capa->imagem, 'smalls'),
      'uri' => sprintf('%s%s/%u/p', URL_BASE, converter_texto($r->nome_produto), $r->id),
      'preco_venda' => $r->preco_venda > 0 ? 'R$: ' . number_format($r->preco_venda, 2, ',', '.') : null,
      'preco_promo' => $r->preco_promo > 0 ? 'R$: ' . number_format($r->preco_promo, 2, ',', '.') : null
    ];
  }
}

exit(json_encode($str, JSON_UNESCAPED_UNICODE));
