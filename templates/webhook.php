<?php
http_response_code(200);

// $text = PHP_EOL;
// $text .= date('d/m/Y H:i:s');
// $text .= PHP_EOL;
// $text .= @var_export($_POST, true);
// $text .= PHP_EOL;

// $name = 'webhook.txt';
// $file = fopen($name, 'a');
// fwrite($file, $text);
// fclose($file);

try {
	
	$str = [];
	
	$params = json_decode(file_get_contents("php://input"), true);

	$ClearSale = new ClearSale\Service\Orders(
		(empty($CONFIG['clearsale']['ambient']) ? new \ClearSale\Ambient\Sandbox() : new \ClearSale\Ambient\Production()),
		new \ClearSale\Auth\Login($CONFIG['clearsale']['login'], $CONFIG['clearsale']['pass'])
	);

	$PagarMe = new PagarMe\Client($CONFIG['pagarme_api_key']);

	$return = $ClearSale->statusCheck($params['code']);
	
	$Pedidos = Pedidos::first(['conditions' => ['codigo=?', $params['code']]]);
	
	$TOTAL_CAP = valor_pagamento($Pedidos->valor_compra, $Pedidos->frete_valor, $Pedidos->desconto_cupom, '$', $Pedidos->desconto_boleto);
	
	print_r($return);
	
	$status = $return['status'];
	
	// Já existindo status capturado, bloqueia o mesmo
	foreach( $Pedidos->pedidos_logs as $status )
		if( 3 == $status->status )
			throw new Exception('Pedido já foi capturado e aprovado');

	// return;
	
	switch($status) 
	{
		case 'APA':
		case 'APP':
		case 'APM':			
			
			$str['status'] = 3;
			
			if($status == 'APA')
				$str['mensagem'] = 'Pedido foi aprovado automaticamente segundo parâmetros definidos na regra de aprovação automática.';

			if($status == 'APP')
				$str['mensagem'] = 'Pedido aprovado automaticamente por política estabelecida pelo cliente ou Clearsale.';

			if($status == 'APM')
				$str['mensagem'] = 'Pedido aprovado manualmente por tomada de decisão de um analista.';
			
			$capturedTransaction = $PagarMe->transactions()->capture([
			  'id' => $Pedidos->pedido_transacao->pagarme_id,
			  'amount' => (number_format($TOTAL_CAP['TOTAL_COMPRA'], 2, '', '') * 1)
			]);
			
		break;
		
		case 'RPM':
		case 'AMA':
		case 'NVO':
			
			$str['status'] = 11;
			
			if($status == 'RPM')
				$str['mensagem'] = 'Pedido Reprovado sem Suspeita por falta de contato com o cliente dentro do período acordado e/ou políticas restritivas de CPF.';
			
			if($status == 'AMA')
				$str['mensagem'] = 'Pedido está em fila para análise.';
			
			if($status == 'NVO')
				$str['mensagem'] = 'Pedido importado e não classificado Score pela analisadora (processo que roda o Score de cada pedido).';
			
		break;
		
		case 'SUS':
		case 'CAN':
		case 'FRD':
		case 'RPA':
		case 'RPP':
		
			$str['status'] = 10;
		
			if($status == 'SUS')
				$str['mensagem'] = 'Pedido Suspenso por suspeita de fraude baseado no contato com o “cliente” ou ainda na base ClearSale.';
			
			if($status == 'CAN')
				$str['mensagem'] = 'Cancelado por solicitação do cliente ou duplicidade do pedido.';
			
			if($status == 'FRD')
				$str['mensagem'] = 'Pedido imputado como Fraude Confirmada por contato com a administradora de cartão e/ou contato com titular do cartão ou CPF do cadastro que desconhecem a compra.';
			
			if($status == 'RPA')
				$str['mensagem'] = 'Pedido Reprovado Automaticamente por algum tipo de Regra de Negócio que necessite aplicá-la.';
			
			if($status == 'RPP')
				$str['mensagem'] = 'Pedido reprovado automaticamente por política estabelecida pelo cliente ou Clearsale.';
			
		break;
	}
	
	// Adiciona um novo logs de pedidos
	PedidosLogs::logs($Pedidos->id, 0, $str['mensagem'], $str['status']);
	
	$Pedidos->status = $str['status'];
	$Pedidos->motivos = $str['mensagem'];
	$Pedidos->save();
	
} catch (\ClearSale\Service\ServiceResponseException $e) {
    echo $e->getMessage();
} catch (Exception $e) {
    echo $e->getMessage();
}