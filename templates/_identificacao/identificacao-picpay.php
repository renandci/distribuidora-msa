<?php

use Picpay\Seller;
use Picpay\Exception\RequestException;
use Picpay\Request\StatusRequest;

$connection = ActiveRecord\ConnectionManager::get_connection();
$connection->transaction();

// Dados da loja (PicPay Token e Seller Token)
$Seller = new Seller($CONFIG['picpay_token'], $CONFIG['picpay_seller']);

// STATUS
try {
  http_response_code(200);
  $referenceId = filter_input(INPUT_POST, 'referenceId');

  $authorizationId = filter_input(INPUT_POST, 'authorizationId');

  if (empty($referenceId)) {
    throw new Exception('Teste');
  }

  // Cria uma nova requisição de status do pagamento com os dados da loja e id do pedido
  $StatusRequest = new StatusRequest($Seller, $referenceId);
  // $StatusRequest = new StatusRequest($Seller, '0000000197');

  // Faze a requisição. O retorno contém o status do pagamento, seu id do pedido e numero de autorizaçao caso esteja pago
  $StatusResponse = $StatusRequest->execute();
  $referenceId = $StatusResponse->referenceId;
  $status = $StatusResponse->status;
  $str = [];

  $Pedidos = Pedidos::first(['conditions' => ['codigo=? and forma_pagamento="PicPay"', $referenceId]]);

  // print_r($Pedidos);
  switch ($status) {
    case 'paid':
      $str['status'] = 3;
      $str['mensagem'] = 'Pagamento aprovado';
      break;
    case 'analysis':
      $str['status'] = 11;
      $str['mensagem'] = 'Em processo de análise';
      break;
    case 'completed':
      $str['status'] = 3;
      $str['mensagem'] = 'Pago e saldo disponível';
      break;
    case 'refunded':
      $str['status'] = 10;
      $str['mensagem'] = 'Pagamento pago porém devolvido para o títular do cartão';
      break;
    case 'chargeback':
      $str['status'] = 11;
      $str['mensagem'] = 'pago e com chargeback';
      break;
    case 'expired':
      $str['status'] = 10;
      $str['mensagem'] = 'Prazo para pagamento expirado';
      break;
  }

  if (!in_array($str['status'], [3, 10, 11])) {
    throw new Exception('Teste');
  }

  $Pedidos->status = $str['status'];
  $Pedidos->motivos = $str['mensagem'];
  $Pedidos->save();

  PedidosLogs::logs($Pedidos->id, 0, $str['mensagem'], $str['status']);

  // "expired": prazo para pagamento expirado
  // "analysis": pago e em processo de análise anti-fraude
  // "paid": pago
  // "completed": pago e saldo disponível
  // "refunded": pago e devolvido
  // "chargeback": pago e com chargeback

  // print_r($StatusResponse->status);

  $connection->commit();
} catch (RequestException $e) {
  $connection->rollback();
  // Tratar os erros da requisição aqui
  $errorMessage = $e->getMessage();
  $statusCode = $e->getCode();
  $errors = $e->getErrors();
}
