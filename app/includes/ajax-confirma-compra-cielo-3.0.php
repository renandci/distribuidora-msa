<?php

use Cielo\API30\Merchant;
use Cielo\API30\Ecommerce\Environment;
use Cielo\API30\Ecommerce\Sale;
use Cielo\API30\Ecommerce\CieloEcommerce;
use Cielo\API30\Ecommerce\Payment;
use Cielo\API30\Ecommerce\CreditCard;
use Cielo\API30\Ecommerce\Request\CieloRequestException;

// Pedidos::transaction(function() {

// global $UA_INFO, $CONFIG, $POST, $str, $settings;

$connection = ActiveRecord\ConnectionManager::get_connection();
$connection->transaction();
try {
  // // $Carrinho = Carrinho::cart();
  // $Carrinho = $CONFIG['carrinho_all'];
  // $Cart = current($Carrinho);

  // /**
  //  * Nota: O endereco de push do cadastro de cliente
  //  * Retorna o endereco em que o status esteja ativo
  //  */
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

  $TOTAL = valor_pagamento($Cart->valorcompra, $Cart->frete_valor, $Cart->cupom_value, $Cart->cupom_desconto, 0);

  $TOTAL_CIELO = (int)number_format($TOTAL['TOTAL_COMPRA'], 2, '', '');

  $PagamentoData = date('Y-m-d H:i:s');
  $Pagamento = $POST['pagamento'];
  $PagamentoCodVenda = Pedidos::getCodidoVenda('ALF');

  $HolderCardName = trim(substr($Pagamento['cardholderName'], 0, 50));
  $HolderCardNumber = soNumero($Pagamento['cardNumber']);
  $HolderCardExpireMes = substr(soNumero($Pagamento['cardExpiration']), 0, 2);
  $HolderCardExpireAno = substr(date('Y'), 0, 2) . substr(soNumero($Pagamento['cardExpiration']), 2);
  $HolderCardSecurityCode = soNumero($Pagamento['securityCode']);
  $HolderCardInstallments = soNumero($Pagamento['installments']);

  // Get Brand
  $HolderCardBrand = CardBrand::test($HolderCardNumber);

  // Configure o ambiente
  $environment = $CONFIG['pagamentos']['cielo_mode'] == '1' ? Environment::production() : Environment::sandbox();

  // Configure seu merchant
  $merchant = new Merchant($CONFIG['pagamentos']['cielo_merchantid'], $CONFIG['pagamentos']['cielo_merchantkey']);

  // Crie uma instância de Sale informando o ID do pedido na loja
  $Sale = new Sale($PagamentoCodVenda);

  // Crie uma instância de Customer informando o nome do cliente
  $Sale->customer($Clientes->nome)
    ->setEmail($Clientes->email)
    ->setBirthdate(
      implode(
        '-',
        array_reverse(
          explode(
            '/',
            str_replace(
              ' ',
              '',
              !empty($Clientes->data_nascimento)
                ? $Clientes->data_nascimento : null
            )
          )
        )
      )
    )
    ->address()
    ->setZipCode(soNumero($Clientes->endereco->cep))
    ->setCountry('BRA')
    ->setCity($Clientes->endereco->cidade)
    ->setStreet($Clientes->endereco->endereco)
    ->setNumber($Clientes->endereco->numero)
    ->setState($Clientes->endereco->uf);

  // Crie uma instância de Payment informando o valor do pagamento
  $Payment = $Sale->payment($TOTAL_CIELO)
    ->setInstallments($HolderCardInstallments)
    ->setReturnUrl(URL_BASE . 'identificacao/finalizado')
    // Crie uma instância de Credit Card utilizando os dados de teste
    // esses dados estão disponíveis no manual de integração
    ->setType(Payment::PAYMENTTYPE_CREDITCARD)
    ->creditCard((int)$HolderCardSecurityCode, (string)$HolderCardBrand)
    ->setExpirationDate("{$HolderCardExpireMes}/{$HolderCardExpireAno}")
    ->setCardNumber($HolderCardNumber)
    ->setHolder($HolderCardName)
    ->setBrand($HolderCardBrand);


  // Crie o pagamento na Cielo
  try {
    // Configure o SDK com seu merchant e o ambiente apropriado para criar a venda
    $Buy = (new CieloEcommerce($merchant, $environment))->createSale($Sale);

    // Com o ID do pagamento, podemos fazer sua captura, se ela não tiver sido capturada ainda
    // Não está ativo
    $PaymentId = $Buy->getPayment()->getPaymentId();
    $finalize = (new CieloEcommerce($merchant, $environment))->captureSale($PaymentId, $TOTAL_CIELO, 0);

    $getTid = $Buy->getPayment()->getTid();
    $getPaymentId = $Buy->getPayment()->getPaymentId();
  } catch (CieloRequestException $e) {
    $str['error'] = 1;
    $str['mensagem'] = 'Opps!<br/>';
    $str['mensagem'] .= $e->getCieloError()->getMessage();

    $GetTrace = $e->getTrace();
    $GetTrace = $GetTrace[3];
    $GetTraceArgs = $GetTrace['args'];

    $getPaymentId = $GetTraceArgs[0];
  }

  // Cria um novo pedido
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
    $HolderCardBrand,
    $HolderCardInstallments,
    $UA_INFO['platform'],
    $UA_INFO['browser'],
    $UA_INFO['version'],
    $Cart->id_cupom,
    $Cart->pedidos_id,
    $Cart->jadlog_pudoid
  );

  // retorna os dados do carrinho de compras
  foreach ($Carrinho as $rs) :
    PedidosVendas::gerarVendas($InserirPedido->id, $rs->id_produto, $rs->preco_venda, $rs->preco_promo, $rs->quantidade, $rs->personalizado);
  endforeach;

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

  // Inseri dados das transações dos pedidos
  $PedidosTransacoes = new PedidosTransacoes();
  $PedidosTransacoes->pedidos_id = $InserirPedido->id;
  $PedidosTransacoes->cielo_tid = !empty($getTid) ? $getTid : null;
  $PedidosTransacoes->cielo_paymentid = !empty($getPaymentId) ? $getPaymentId : null;
  $PedidosTransacoes->save();

  // Inserir a indicacao do cliente, nesse caso e ele é um cliente novo
  $indicacao = ClientesIndicacoes::find('first', ['conditions' => ['id_session=?', session_id()]]);
  if (isset($indicacao->id) && $indicacao->id > 0) {
    $indicacao->id_session = '';
    $indicacao->id_pedido = $InserirPedido->id;
    $indicacao->save();
  }

  // Alterar o status do pedido
  $PedidosStatus = Pedidos::find($InserirPedido->id);
  $PedidosStatus->status = 1;
  $PedidosStatus->obs = $Cart->cliente_obs;
  $PedidosStatus->save();

  // adiciona um novo logs de pedidos
  PedidosLogs::logs($InserirPedido->id, 0, 'Pedido realizado', 1);

  try {
    // Pega e verifica o novo statu do pagamento na api da cielo
    $CieloEcommerceStatus = (new CieloEcommerce($merchant, $environment))->getSale($getPaymentId);
    switch ($CieloEcommerceStatus->getPayment()->getStatus()) {
      case 1:
        $NewCieloEcommerceStatus = ['status' => '4', 'descricao' => 'Pagamento não aprovado'];
        break;
      case 2:
        $NewCieloEcommerceStatus = ['status' => '3', 'descricao' => 'Pagamento aprovado'];
        break;
      case 3:
        $NewCieloEcommerceStatus = ['status' => '4', 'descricao' => 'Pagamento não aprovado'];
        break;
      default:
        $NewCieloEcommerceStatus = ['status' => '10', 'descricao' => 'Pagamento cancelado'];
        break;
    }

    // Alterar o status do pedido apos o status da cielo
    $PedidosStatusAfter = Pedidos::find($InserirPedido->id);
    $PedidosStatusAfter->status = $NewCieloEcommerceStatus['status'];
    $PedidosStatusAfter->save();

    // gerar um novo log
    $PedidosLogs = new PedidosLogs();
    $PedidosLogs->id_adm = 0;
    $PedidosLogs->id_pedido = $InserirPedido->id;
    $PedidosLogs->status = $NewCieloEcommerceStatus['status'];
    $PedidosLogs->descricao = $NewCieloEcommerceStatus['descricao'];
    $PedidosLogs->data_envio = date('Y-m-d H:i:s');
    $PedidosLogs->save();

    // Limpa o carrinho
    Carrinho::delete_all(array('conditions' => array('id_session=?', session_id())));

    $str['mensagem'] = ''
      . 'Pedido finalizado com sucesso!'
      . sprintf('<script>window.location.href="/identificacao/finalizado?pedidos_id=%s"</script>', $InserirPedido->id)
      . '';
  } catch (Exception $e) {
    $str['error'] = 1;
    $str['mensagem'] = $e->getCieloError()->getMessage();
  }

  // return true;
  // });
  $connection->commit();
} catch (\Exception $exception) {
  $str['mensagem'] = $exception->getMessage(); // . @var_export($ItemsClearSale, true);
  // $str['mensagem'] = 'Erro ao tentar finalizar o pagamento';
  $connection->rollback();
}

exit(json_encode($str, JSON_UNESCAPED_UNICODE));