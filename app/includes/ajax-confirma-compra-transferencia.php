<?php

/**
 * Finaliza pagamaneto somente para transferencia
 */
// Pedidos::transaction(function(){
// // Globals
// global $LOJA, $STORE, $CONFIG, $UA_INFO, $MobileDetect, $WebService, $settings, $str;
$connection = ActiveRecord\ConnectionManager::get_connection();
$connection->transaction();
try {

  // // $Carrinho = Carrinho::cart();
  // $Carrinho = $CONFIG['carrinho_all'];
  // $Cart = current($Carrinho);
  // $Clientes = Clientes::first(['conditions' => ['md5(id) = ?', $_SESSION['cliente']['id_cliente']]]);

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

  $TOTAL = valor_pagamento($Cart->valorcompra, $Cart->frete_valor, $Cart->cupom_value, $Cart->cupom_desconto, 0.00);

  $PagamentoData = date('Y-m-d H:i:s');
  $PagamentoCodVenda = Pedidos::getCodidoVenda('ALF');

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
    $CONFIG['desconto_boleto'],
    'Transferência',
    '',
    '',
    $UA_INFO['platform'],
    $UA_INFO['browser'],
    $UA_INFO['version'],
    $Cart->id_cupom,
    (int)$Cart->pedidos_id,
    $Cart->jadlog_pudoid
  );

  foreach ($Carrinho as $rs) {
    PedidosVendas::gerarVendas($InserirPedido->id, $rs->id_produto, $rs->preco_venda, $rs->preco_promo, $rs->quantidade, $rs->personalizado);

    // Adiciona items aos kits se houver
    if (count($rs->carrinho_prod->grid_kits)) {
      foreach ($rs->carrinho_prod->grid_kits as $rws) {
        PedidosVendas::gerarVendas($InserirPedido->id, $rws->produto->id, $rws->produto->preco_venda, $rws->produto->preco_promo, $rws->qtde, '');
      }
    }
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

  // // Inserir a indicacao do cliente, nesse caso e ele é um cliente novo
  /*
    // try{
       // $indicacao = current(ClientesIndicacoes::find('all', array('conditions'=>array('id_session=?', session_id()))));
       // $indicacao->id_pedido = $InserirPedido->id;
       // $indicacao->save();
    // } catch (Exception $ex) {

    // }
	*/

  Carrinho::delete_all(['conditions' => ['id_session=?', session_id()]]);

  $ID = (int)$InserirPedido->id;
  $UpStatus = Pedidos::find($ID);
  $UpStatus->status = 1;
  $UpStatus->obs = $Cart->cliente_obs;
  $UpStatus->save();

  PedidosLogs::logs($InserirPedido->id, 0, 'Pedido realizado', 1);

  $str['mensagem'] = ''
    . 'Pedido finalizado com sucesso!'
    . '<script>window.location.href="/identificacao/finalizado?pedidos_id=' . $InserirPedido->id . '"</script>'
    . '';

  $connection->commit();
  // $connection->rollback();
} catch (\Exception $exception) {
  $str['mensagem'] = $exception->getMessage(); // . @var_export($ItemsClearSale, true);
  // $str['mensagem'] = 'Erro ao tentar finalizar o pagamento';
  $connection->rollback();
}
// return true;
// });

exit(json_encode($str, JSON_UNESCAPED_UNICODE));