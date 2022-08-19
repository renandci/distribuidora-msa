<?php

/**
 * Verifica se existe a FinalizarPagamentoo
 */
if (isset($POST['pagamento']['acao']) && $POST['pagamento']['acao'] == 'FinalizarPagamento') {

  /**
   * Verificar se o Frete está selecionado
   */
  if (isset($POST['pagamento']['Frete']) && $POST['pagamento']['Frete'] == '') {
    $string['error'] = 1;
    $string['mensagem'] = 'Selecione a forma de envio!';
    exit(json_encode($string, JSON_UNESCAPED_UNICODE));
  }

  /**
   * Verificar a Forma de Pagamento
   */
  if (!isset($POST['pagamento']['FormaPagamento']) && $POST['pagamento']['FormaPagamento'] == '') {
    $string['error'] = 1;
    $string['mensagem'] = 'Selecione uma forma de pagamento!';
    exit(json_encode($string, JSON_UNESCAPED_UNICODE));
  }

  /**
   * Uma previa de vericação de estoque antes de finalizar
   */
  $Carrinho = Carrinho::all(['conditions' => ['id_session=?', SESSION_ID]]);
  foreach ($Carrinho as $rws) {
    if ($rws->prod->estoque < $rws->quantidade) {
      exit(json_encode([
        'error' => 1,
        'mensagem' => 'Desculpe, mas não temos mais estoque para alguns produtos, <a href="/identificacao/carrinho">clique aqui</a> para voltar ao carrinho de compras.'
      ], JSON_UNESCAPED_UNICODE));
    }
  }
  unset($Carrinho);

  switch ($POST['pagamento']['FormaPagamento']) {
      /**
     * Pagamento com cartão via cielo
     */
    case 'Mp Cartão':
      include PATH_ROOT . 'app/includes/ajax-confirma-compra-mp-cartao.php';
      break;

      /**
       * Pagamento com cartão via cielo
       */
    case 'Cartão':
      include PATH_ROOT . 'app/includes/ajax-confirma-compra-cielo-3.0.php';
      break;

      /**
       * Pagamento com ambiente da cielo (sem homologação)
       */
    case 'Ambiente Cielo':
      include PATH_ROOT . 'app/includes/ajax-confirma-compra-cielo.php';
      break;

      /**
       * Pagamento Transferencia
       */
    case 'Transferência':
      include PATH_ROOT . 'app/includes/ajax-confirma-compra-transferencia.php';
      break;

      /**
       * Pagamento Boleto
       */
    case 'Boleto':
      if (!empty($CONFIG['pagamentos']['mp_boleto']) && empty($CONFIG['pagamentos']['pagarme_boleto'])) {
        include PATH_ROOT . 'app/includes/ajax-confirma-compra-mp-boleto.php';
      }

      if (empty($CONFIG['pagamentos']['mp_boleto']) && !empty($CONFIG['pagamentos']['pagarme_boleto'])) {
        include PATH_ROOT . 'app/includes/ajax-confirma-compra-pagarme-boleto.php';
      }

      if (empty($CONFIG['pagamentos']['mp_boleto']) && empty($CONFIG['pagamentos']['pagarme_boleto'])) {
        include PATH_ROOT . 'app/includes/ajax-confirma-compra-boleto.php';
      }
      break;

      /**
       * Pagamento PIX
       */
    case 'Pix':
      include PATH_ROOT . 'app/includes/ajax-confirma-compra-pix.php';
      break;

      /**
       * Pagamento PicPay
       */
    case 'PicPay':
      include PATH_ROOT . 'app/includes/ajax-confirma-compra-picpay.php';
      break;

      /**
       * Pagamento PagSeguro
       */
    case 'PagSeguro':
      include PATH_ROOT . 'app/includes/ajax-confirma-compra-pagseguro.php';
      break;

      /**
       * Pagamento Pagar Me
       */
    case 'Pagar Me':
      include PATH_ROOT . 'app/includes/ajax-confirma-compra-pagarme.php';
      break;
  }
}