<?php

/**
 * @author Renan Henrique <renan@dcisuporte.com.br>
 * @company Data Control Infomatica
 */

use
  CieloCheckout\Order,
  CieloCheckout\Item,
  CieloCheckout\Discount,
  CieloCheckout\Cart,
  CieloCheckout\Address,
  CieloCheckout\Services,
  CieloCheckout\Shipping,
  CieloCheckout\Payment,
  CieloCheckout\Customer,
  CieloCheckout\Options,
  CieloCheckout\Transaction,
  Cielo\Merchant;

// Pedidos::transaction(function() {
// global $UA_INFO, $CONFIG, $str, $settings;
$connection = ActiveRecord\ConnectionManager::get_connection();
$connection->transaction();
try {
  // $Carrinho = Carrinho::cart();
  // $Carrinho = $CONFIG['carrinho_all'];
  // $Cart = current($Carrinho);
  // $Clientes = Clientes::first(['conditions' => ['md5(id)=?', $_SESSION['cliente']['id_cliente']]]);

  $Clientes = Clientes::first(['conditions' => ['md5(id)=?', $_SESSION['cliente']['id_cliente']]]);

  $Cart = new stdClass();
  $Carrinho = $CONFIG['carrinho_all'];
  foreach ($Carrinho as $cart) {
    $Cart->valorcompra += $cart->preco_promo * $cart->quantidade;
    $Cart->frete_valor = $cart->frete_valor;
    $Cart->cupom_value = $cart->cupom_value;
    $Cart->cupom_desconto = $cart->cupom_desconto;
    $Cart->frete_tipo = $cart->frete_tipo;
    $Cart->id_cupom = $cart->id_cupom;
    $Cart->pedidos_id = $cart->pedidos_id;
    $Cart->jadlog_pudoid = $cart->jadlog_pudoid;
    $Cart->frete_prazo = $cart->frete_prazo;
  }


  $PagamentoData = date('Y-m-d H:i:s');
  $PagamentoCodVenda = Pedidos::getCodidoVenda('ALF');
  $TOTAL = valor_pagamento($Cart->valorcompra, $Cart->frete_valor, $Cart->cupom_value, $Cart->cupom_desconto, 0.00);

  try {
    // Instantiate cart's item object and set it to an array of product items.
    $CarrinhoItens = [
      'Name' => 'Pedido #' . $PagamentoCodVenda,
      'Description' => 'Pagamento do Ped.: #' . $PagamentoCodVenda,
      'UnitPrice' => (int)number_format($TOTAL['TOTAL_COMPRA'], 2, '', ''),
      'Quantity' => 1,
      'Type' => 'Asset',
      'Sku' => $PagamentoCodVenda,
      'Weight' => 0,
    ];
    $Items = [
      new Item($CarrinhoItens),
    ];

    // Instantiate cart discount object.
    $properties = [
      'Type' => 'Percent',
      'Value' => 0,
    ];
    $Discount = new Discount($properties);

    // Instantiate shipping address' object.
    $Address = [
      'Street' => $Clientes->endereco->nome,
      'Number' => $Clientes->endereco->numero,
      'Complement' => $Clientes->endereco->complemento,
      'District' => $Clientes->endereco->bairro,
      'City' => $Clientes->endereco->cidade,
      'State' => $Clientes->endereco->uf,
    ];
    $Address = new Address($Address);

    $properties = [
      'Name' => 'Entrega gratuita',
      'Price' => 0,
      'DeadLine' => 15,
    ];

    $Services = [
      new Services($properties),
    ];

    // Instantiate shipping's object.
    $properties = [
      'Type' => 'FixedAmount',
      'SourceZipCode' => soNumero($CONFIG['cep']),
      'TargetZipCode' => soNumero($Clientes->endereco->cep),
      'Address' => $Address,
      'Services' => $Services,
    ];
    $Shipping = new Shipping($properties);

    // Instantiate payment's object.
    $properties = [
      'BoletoDiscount' => 0,
      'DebitDiscount' => 0,
    ];
    $Payment = new Payment($properties);

    // Instantiate customer's object.
    $properties = [
      'Identity' => soNumero($Clientes->cpfcnpj),
      'FullName' => $Clientes->nome,
      'Email' => $Clientes->email,
      'Phone' => soNumero($Clientes->telefone),
    ];
    $Customer = new Customer($properties);

    // Instantiate options' object.
    $properties = [
      'AntifraudEnabled' => FALSE,
    ];
    $Options = new Options($properties);

    // Instantiate order's object.
    $properties = [
      'OrderNumber' => $PagamentoCodVenda,
      'SoftDescriptor' => substr(preg_replace('/\s/', '', $CONFIG['nome_fantasia']), 0, 13),
      // Instantiate cart's object.
      'Cart' => new Cart(['Discount' => $Discount, 'Items' => $Items]),
      'Shipping' => $Shipping,
      'Payment' => $Payment,
      'Customer' => $Customer,
      'Options' => $Options,
    ];
    $Order = new Order($properties);

    // Instantiate merchant's object.
    $Merchant = new Merchant($CONFIG['pagamentos']['cielo_merchantid'], $CONFIG['pagamentos']['cielo_mid']);

    // Instantiate transaction's object.
    $Transaction = new Transaction($Merchant, $Order);
    $Transaction->request_new_transaction();

    /**
     * Cria um novo pedido
     */
    $InserirPedido = Pedidos::gerarPedido(
      $PagamentoData,
      $PagamentoCodVenda,
      $Clientes->id,
      retornaIpReal(),
      $Cart->frete_tipo,
      $Cart->frete_valor,
      $Cart->frete_prazo,
      $Cart->valorcompra,
      $TOTAL['TOTAL_CUPOM_REAL'],
      '',
      'Cartão',
      '',
      '',
      $UA_INFO['platform'],
      $UA_INFO['browser'],
      $UA_INFO['version'],
      $Cart->id_cupom,
      $Cart->pedidos_id,
      $Cart->jadlog_pudoid
    );

    foreach ($Carrinho as $rs) {
      PedidosVendas::gerarVendas($InserirPedido->id, $rs->id_produto, $rs->preco_venda, $rs->preco_promo, $rs->quantidade, $rs->personalizado);
    }

    // verificar se existe a necessidade de endereco para o sistema
    if ($STORE['config']['endereco']['configure']['status'] == true) {
      PedidosEnderecos::gerarEnderecos(
        $InserirPedido->id,
        $Clientes->id,
        $Clientes->endereco->nome,
        $Clientes->endereco->endereco,
        $Clientes->endereco->numero,
        $Clientes->endereco->bairro,
        $Clientes->endereco->complemento,
        $Clientes->endereco->referencia,
        $Clientes->endereco->cidade,
        $Clientes->endereco->uf,
        $Clientes->endereco->cep
      );
    }

    $str['mensagem'] .= ''
      . 'Gerando pedido na Cielo!'
      . '<script>window.location.href="' . $Transaction->response->settings->checkoutUrl . '"</script>'
      . '';

    Carrinho::delete_all(array('conditions' => array('id_session=?', session_id())));

    $UpStatus = Pedidos::find($InserirPedido->id);
    $UpStatus->status = 1;
    $UpStatus->obs = $Cart->cliente_obs;
    $UpStatus->save();

    PedidosLogs::logs($InserirPedido->id, 0, 'Pedido realizado', 1);

    // $name = 'Transaction.txt';
    // $file = fopen($name, 'a');
    // $text = @var_export( $Transaction, true);
    // fwrite($file, $text);
    // fclose($file);

    // Inseri dados das transações dos pedidos
    // $PedidosTransacoes = new PedidosTransacoes();
    // $PedidosTransacoes->pedidos_id = $InserirPedido->id;
    // $PedidosTransacoes->cielo_tid = ! empty( $getTid ) ? $getTid : null;
    // $PedidosTransacoes->cielo_paymentid = ! empty( $getPaymentId ) ? $getPaymentId : null;
    // $PedidosTransacoes->save();

  } catch (Exception $e) {
    $str['error'] = 1;
    $str['mensagem'] = $e->getMessage();
  }

  // Check out the Transaction class at src/Checkout/Transaction.php
  // There you will find a constant for each of the transaction status codes that
  // will eventually be sent by Cielo to your app via POST method.

  // There is also a static method for retrieving an array of all transaction
  // status codes available.

  //$str['mensagem'] = json_encode($msg, JSON_UNESCAPED_UNICODE);

  /**
   * Inserir a indicacao do cliente, nesse caso e ele é um cliente novo
   */
  // $indicacao = current(ClientesIndicacoes::find('all', array('conditions'=>array('id_session=?', session_id()))));
  // $indicacao->id_pedido = $InserirPedido->id;
  // $indicacao->save();

  // return true;
  // });
  $connection->commit();
} catch (\Exception $exception) {
  $str['mensagem'] = $exception->getMessage(); // . @var_export($ItemsClearSale, true);
  // $str['mensagem'] = 'Erro ao tentar finalizar o pagamento';
  $connection->rollback();
}

exit(json_encode($str, JSON_UNESCAPED_UNICODE));