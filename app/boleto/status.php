<?php
include '../settings.php';
include '../vendor/autoload.php';
include '../settings-config.php';
include '../includes/bibli-funcoes.php';
include '../includes/ajax-emails.php';

try {
  $str = [];
  $status = 1;
  $pay_token   = filter_input(INPUT_POST, 'paymentToken');
  $cod_refer   = filter_input(INPUT_POST, 'chargeReference');

  if (empty($pay_token) || empty($cod_refer)) {
    throw new Exception('Não Autorizado');
  }

  // API 1.0
  $url = ($CONFIG['pagamentos']['boleto_mode'] == 0 ? 'https://sandbox.boletobancario.com/boletofacil/integration/api/v1' : 'https://www.boletobancario.com/boletofacil/integration/api/v1');

  // Gerar o Pagamento
  $ch_charges = curl_init();
  curl_setopt_array($ch_charges, [
    CURLOPT_URL => $url . '/fetch-payment-details',
    CURLOPT_RETURNTRANSFER => true,
    // CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_HTTPHEADER => array(
      "cache-control: no-cache",
      "content-type: application/x-www-form-urlencoded",
    ),
    CURLOPT_POSTFIELDS => http_build_query([
      'token'         => $CONFIG['pagamentos']['boleto_token'],
      'paymentToken'  => $pay_token,
      'responseType'  => "JSON"
    ])
  ]);

  $info = curl_getinfo($ch_charges);
  $err = curl_error($ch_charges);
  $response = curl_exec($ch_charges);
  curl_close($ch_charges);

  if ($err)
    throw new Exception('Não foi possivel gerar o boleto');

  $charge = json_decode($response);

  $status = $charge->data->payment->status;

  $Pedidos = Pedidos::first(['conditions' => ['codigo like ? and forma_pagamento = "Boleto"',  (string)$cod_refer]]);

  switch ($status) {
    case 'CONFIRMED':
      $str['status'] = 3;
      $str['mensagem'] = 'O pagamento foi aprovado e acreditado.';
      break;

    case 'AUTHORIZED':
      $str['status'] = 11;
      $str['mensagem'] = 'Pagamento autorizado (Aguardando confirmação)';
      break;

    case 'FAILED':
    case 'DECLINED':
    case 'NOT_AUTHORIZED':
      $str['status'] = 5;
      $str['mensagem'] = 'O pagamento não foi efetuado dentro da data prevista. Mas caso você ainda tenha interesse na compra, clique na opção pagar novamente.';
      break;

    default:
      $str['status'] = 5;
      $str['mensagem'] = 'O pagamento não foi efetuado dentro da data prevista. Mas caso você ainda tenha interesse na compra, clique na opção pagar novamente.';
      break;
  }

  $Pedidos->status = $str['status'];
  $Pedidos->save();

  PedidosLogs::logs($Pedidos->id, 0, $str['mensagem'], $str['status']);
} catch (\Exception $e) {
  echo $e->getMessage();
  // printf('<pre>%s</pre>', print_r($e, 1));
  //throw $th;
}
