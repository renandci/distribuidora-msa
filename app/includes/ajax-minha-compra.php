<?php
switch ($POST['acao']) {
  case 'AtualizarCarrinho':

    $POST['tipofrete'] = $POST['tipofrete'] ?? 'GRÁTIS';
    $POST['valorfrete'] = dinheiro($POST['valorfrete']);
    $POST['valorfrete'] = $POST['valorfrete'] > 0 ? $POST['valorfrete'] : '0.00';

    $set = [
      'frete_prazo' => trim($POST['prazosfrete']),
      'frete_tipo' => trim($POST['tipofrete']),
      'frete_valor' => $POST['valorfrete'],
      'cep' => trim($POST['cep'])
    ];

    if ($POST['tipofrete'] != 'JADLOG-ECONOMICO') {
      $set = ($set + ['jadlog_pudoid' => null]);
    }

    Carrinho::update_all([
      'set' => $set,
      'conditions' => ['id_session=?', session_id()]
    ]);

    $i = 1;
    $TIPO_FRETE = 0;
    $TOTAL_ITENS = 0;
    $TOTAL_FRETE = 0;
    $TOTAL_ESTOQUE = 0;
    $TOTAL_DESCONTO = 0;
    $TOTAL_CARRINHO = 0;
    $TOTAL_CARRINHO_FRETE  = 0;
    $TOTAL_FRETE_SOMA = $POST['valorfrete'];

    foreach ($CONFIG['carrinho_all'] as $r) {
      $TOTAL_CARRINHO += ($r->preco_promo * $r->quantidade);

      $ID_CUPOM = $r->id_cupom;
      $CUPOM = $r->cupom_codigo;
      $CUPOM_TIPO = $r->cupom_desconto;
      $CUPOM_VALOR = $r->cupom_value;
    }

    $TOTAL = valor_pagamento($TOTAL_CARRINHO, $TOTAL_FRETE_SOMA, $CUPOM_VALOR, $CUPOM_TIPO, $CONFIG['desconto_boleto']);

    $str['total_frete'] = 'R$: ' . number_format($TOTAL['TOTAL_FRETE'], 2, ',', '.');
    $str['total_desconto'] = $TOTAL['TOTAL_CUPOM'] ? $TOTAL['TOTAL_CUPOM'] : 'R$: 0,00';
    $str['total_boleto'] = 'R$: ' . number_format($TOTAL['TOTAL_COMPRA_C_BOLETO'], 2, ',', '.');
    $str['total_transferencia'] = 'R$: ' . number_format($TOTAL['TOTAL_COMPRA_C_BOLETO'], 2, ',', '.');
    $str['total_carrinho'] = 'R$: ' . number_format($TOTAL['TOTAL_COMPRA'], 2, ',', '.');
    $str['total_carrinho_frete'] = 'R$: ' . number_format($TOTAL['TOTAL_COMPRA_C_BOLETO'], 2, ',', '.');
    $str['quantidade_parcela'] = parcelamento($TOTAL['TOTAL_COMPRA'], $CONFIG['qtde_parcelas'], $CONFIG['parcela_minima']);

    $str['installments_html'] = ''
      . '<div class="row ft12px mb5">'
      . '<div class="col-xs-12">'
      . 'Parcelamento via cartão'
      . '</div>';
    for ($p = 1; $p <= ($str['quantidade_parcela']); ++$p) {
      $total = $TOTAL['TOTAL_COMPRA'];

      if ($p == 1 && isset($STORE['cartao_em_1x']) && $STORE['cartao_em_1x'])
        $total = $TOTAL['TOTAL_COMPRA_C_BOLETO'];

      $str['installments_html'] .= ''
        . '<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">'
        . "{$p}x de <span class='color-004 ft14px'>R$: " . number_format(($total / $p), 2, ',', '.') . "</span>"
        . '</div>';
    }
    $str['installments_html'] .= '</div>';


    $taxa  = 2 / 100;
    $str['selecione'] = 'Selecione...';
    $str['installments'] = '<option value="-1">Selecione...</option>';

    for ($p = 1; $p <= ($str['quantidade_parcela']); ++$p) {
      $total = $TOTAL['TOTAL_COMPRA'];
      $text = sprintf('R$: %s', number_format($total, 2, ',', '.'));

      if ($p == 1 && isset($STORE['cartao_em_1x']) && $STORE['cartao_em_1x']) {
        $total = $TOTAL['TOTAL_COMPRA_C_BOLETO'];
        $text = sprintf('%s%% de desconto', $CONFIG['desconto_boleto']);
      }

      $str['installments'] .= sprintf('<option value="%u" data-parcela="%u" data-total="%s">%sx de R$: %s (%s)</option>', $p, $p, $total, $p, number_format($total / $p, 2, ',', '.'), $text);
    }

    exit(json_encode($str, JSON_UNESCAPED_UNICODE));
    break;
}
