<?php
include_once '../topo.php';
/**
 *  @Author: Renan - Data Control Informatica.
 *  @Mail: renan@dcisuporte.com.br
 *  @Date: 19/02/2016 
 *  @Time: 10:24:47
 */

$JadLogNew = new JadLogNew($CONFIG['jadlog']['token']);
 
$ACAO = isset($_GET['acao']) && $_GET['acao'] != '' ? $_GET['acao'] : null;
switch ($ACAO) 
{
	case 'jadlog_get_servicos':
	
		$id_pedido = (isset($_GET['id_pedido']) && $_GET['id_pedido'] != '' ? $_GET['id_pedido'] : 0);
		
		$rws = Pedidos::find($id_pedido);
		
		$cepLocal = soNumero($CONFIG['cep']);
		$cepDestino = soNumero($rws->pedido_endereco->cep);
		$pesoCubagem = 0;
		foreach($rws->pedidos_vendas as $r) {
			$pesoCubagem += $r->produto->freteproduto->peso * $r->quantidade;
		}
		
		$fretea = [
			"frete" => [ 
				[
					"cepori" => $cepLocal,
					"cepdes" => $cepDestino,
					"frap" => null,
					"peso" => $pesoCubagem,
					"conta" => $CONFIG['jadlog']['contacorrente'],
					"contrato" => $CONFIG['jadlog']['nrcontrato'],
					"modalidade" => 3,
					"tpentrega" => "D",
					"tpseguro" => "N",
					"vldeclarado" => 0,
					"vlcoleta" => 1.50
				], [
					"cepori" => $cepLocal,
					"cepdes" => $cepDestino,
					"frap" => null,
					"peso" => $pesoCubagem,
					"conta" => $CONFIG['jadlog']['contacorrente'],
					"contrato" => $CONFIG['jadlog']['nrcontrato'],
					"modalidade" => 4,
					"tpentrega" => "D",
					"tpseguro" => "N",
					"vldeclarado" => 0,
					"vlcoleta" => 1.50
				], [
					"cepori" => $cepLocal,
					"cepdes" => $cepDestino,
					"frap" => null,
					"peso" => $pesoCubagem,
					"conta" => $CONFIG['jadlog']['contacorrente'],
					"contrato" => $CONFIG['jadlog']['nrcontrato'],
					"modalidade" => 9,
					"tpentrega" => "D",
					"tpseguro" => "N",
					"vldeclarado" => 0,
					"vlcoleta" => 1.50
				]
			]
		];
		
		$freteb = [
			"frete" => [[
					"cepori" => $cepLocal,
					"cepdes" => $cepDestino,
					"frap" => null,
					"peso" => $pesoCubagem,
					"conta" => $CONFIG['jadlog']['contacorrente'],
					"contrato" => $CONFIG['jadlog']['nrcontrato'],
					"modalidade" => 40,
					"tpentrega" => "R",
					"tpseguro" => "N",
					"vldeclarado" => 0,
					"vlcoleta" => 1.50			
				]
			]
		];
		
		$json = [];
		
		try {
			$vlfrete_a = $JadLogNew->post('/frete/valor', $fretea);
			
			$vlfrete_b = $JadLogNew->post('/frete/valor', $freteb);
			
			$json = [ [
					'text' => 'Packpage',
					'modalidade' => $vlfrete_a['body']->frete[0]->modalidade,
					'vltotal' => number_format(($CONFIG['fretes_tipo'] == '%' ? desconto_boleto($vlfrete_a['body']->frete[0]->vltotal, $CONFIG['fretes_valor']) : ($vlfrete_a['body']->frete[0]->vltotal - $CONFIG['fretes_valor'])), 2, ',', '.')
				], [
					'text' => 'Rodoviário',
					'modalidade' => $vlfrete_a['body']->frete[1]->modalidade,
					'vltotal' => number_format(($CONFIG['fretes_tipo'] == '%' ? desconto_boleto($vlfrete_a['body']->frete[1]->vltotal, $CONFIG['fretes_valor']) : ($vlfrete_a['body']->frete[1]->vltotal - $CONFIG['fretes_valor'])), 2, ',', '.')
				], [
					'text' => '.COM',
					'modalidade' => $vlfrete_a['body']->frete[2]->modalidade,
					'vltotal' => number_format(($CONFIG['fretes_tipo'] == '%' ? desconto_boleto($vlfrete_a['body']->frete[2]->vltotal, $CONFIG['fretes_valor']) : ($vlfrete_a['body']->frete[2]->vltotal - $CONFIG['fretes_valor'])), 2, ',', '.')
				], [
					'text' => 'PICKUP',
					'modalidade' => $vlfrete_b['body']->frete[0]->modalidade,
					'vltotal' => number_format(($CONFIG['fretes_tipo'] == '%' ? desconto_boleto($vlfrete_b['body']->frete[0]->vltotal, $CONFIG['fretes_valor']) : ($vlfrete_b['body']->frete[0]->vltotal - $CONFIG['fretes_valor'])), 2, ',', '.')
				], 
			];
			
			echo sprintf('<div id="jadlog_get_servicos">%s</div>', json_encode($json, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
		} 
		catch(Exception $e) {
			print_r( $e->getMessage() );
		}
		
	break;
	
	case 'remover_etiquetas':
	case 'remover_etiqueta_jadlog':
		
		$id_pedido = (isset($_GET['id_pedido']) && $_GET['id_pedido'] != '' ? $_GET['id_pedido'] : 0);
		$etiquetas_id = (isset($_GET['etiquetas_id']) && $_GET['etiquetas_id'] != '' ? $_GET['etiquetas_id'] : 0);
		
		try {
			$JadLogEtiqueta = JadLogEtiqueta::find($etiquetas_id);
			$JadLogEtiqueta->excluir = 1;
			$JadLogEtiqueta->save_log();

			$ReturnJadLogNew = $JadLogNew->post('/pedido/cancelar', ['codigo' => $JadLogEtiqueta->codigo]); 
			
			PedidosLogs::logs($id_pedido, $_SESSION['admin']['id_usuario'], 'Etiqueta removida com sucesso', $JadLogEtiqueta->status);
		} 
		catch (exception $e) {
			PedidosLogs::logs($id_pedido, $_SESSION['admin']['id_usuario'], 'Etiqueta removida com sucesso', $JadLogEtiqueta->status);
		}

		header('location: /adm/vendas/vendas-detalhes.php?id=' . $id_pedido);
		return;

	break;
	
	case 'gerar_etiquetas':
	case 'gerar_etiqueta_jadlog':
		
		$frete_qtde = (isset($_POST['frete_qtde']) && $_POST['frete_qtde'] != '' ? $_POST['frete_qtde'] : 1);
		$modalidade = (isset($_POST['frete_tipo']) && $_POST['frete_tipo'] != '' ? $_POST['frete_tipo'] : '');
		$nrDoc = (isset($_POST['frete_nr_nfe']) && $_POST['frete_nr_nfe'] != '' ? $_POST['frete_nr_nfe'] : 'DEC-' . date('is'));
		$serie = (isset($_POST['frete_nr_serie']) && $_POST['frete_nr_serie'] != '' ? $_POST['frete_nr_serie'] : '');
		$danfeCte = (isset($_POST['frete_nr_danfe']) && $_POST['frete_nr_danfe'] != '' ? $_POST['frete_nr_danfe'] : '');
		$tpDocumento = (isset($_POST['frete_tp_doc']) && $_POST['frete_tp_doc'] != '' ? $_POST['frete_tp_doc'] : '');
		$cfop = (isset($_POST['frete_cfop']) && $_POST['frete_cfop'] != '' ? $_POST['frete_cfop'] : null);
		$id_pedido = (isset($_POST['id_pedido']) && $_POST['id_pedido'] != '' ? $_POST['id_pedido'] : 0);
		
		$sql = '' 
			  . 'select ' 

			  . 'pedidos.codigo as codigo_venda, ' 
			  . 'pedidos.frete_tipo, ' 
			  . 'pedidos.frete_valor, ' 
			  . 'pedidos.valor_compra, ' 
			  . 'pedidos.desconto_cupom, ' 
			  . 'pedidos.desconto_boleto, ' 
			  . 'pedidos.frete_pudoid, ' 
			  . 'pedidos.status, ' 
			   
			  . 'clientes.nome, '
			  . 'clientes.cpfcnpj, '
			  . 'clientes.telefone, '
			  . 'clientes.celular, '
			  . 'clientes.email, '

			  . 'pedidos_enderecos.endereco, '
			  . 'pedidos_enderecos.numero, '
			  . 'pedidos_enderecos.bairro, '
			  . 'pedidos_enderecos.complemento, '
			  . 'pedidos_enderecos.referencia, '
			  . 'pedidos_enderecos.cidade, '
			  . 'pedidos_enderecos.uf, '
			  . 'pedidos_enderecos.cep, ' 
			  
			  . '(max(dados_frete.altura) / sum(pedidos_vendas.quantidade)) as altura, ' 
			  . 'max(dados_frete.largura) as largura, ' 
			  . 'max(dados_frete.comprimento) as comprimento, ' 
			  . 'sum(pedidos_vendas.quantidade * dados_frete.peso) as peso ' 

			  . 'from (((((pedidos '
			  . 'join pedidos_enderecos on pedidos_enderecos.id_pedido = pedidos.id) '
			  . 'join pedidos_vendas on pedidos_vendas.id_pedido = pedidos.id) '
			  . 'join produtos on pedidos_vendas.id_produto = produtos.id) '
			  . 'join dados_frete on produtos.id_frete = dados_frete.id) '
			  . 'join clientes on pedidos.id_cliente = clientes.id) '
			  . sprintf('where pedidos.id=%u', $id_pedido);
			  
		$rws = Lojas::connection()->query($sql)->fetch(PDO::FETCH_OBJ);
		
		$TOTAL = valor_pagamento($rws->valor_compra, $rws->frete_valor, $rws->desconto_cupom, '$', $rws->desconto_boleto);
		
		$json = [
			'conteudo' => $CONFIG['jadlog']['fantasia'],
			'pedido' => [ $rws->codigo_venda ],
			'totPeso' => $rws->peso,
			'totValor' => number_format($TOTAL['TOTAL_COMPRA_C_BOLETO'], 2, '.', ''),
			'obs' => '',
			'modalidade' => $modalidade,
			'contaCorrente' => $CONFIG['jadlog']['contacorrente'],
			'tpColeta' => 'K',
			'tipoFrete' => 0,
			'cdUnidadeOri' => $CONFIG['jadlog']['ponto'],
			'cdUnidadeDes' => null,
			'cdPickupOri' => null,
			'cdPickupDes' => $rws->frete_pudoid,
			'nrContrato' => null,
			'shipmentId' => null,
			'vlColeta' => null,
			'servico' => 1,
			'rem' => [
				'nome' => $CONFIG['jadlog']['remet'],
				'cnpjCpf' => soNumero($CONFIG['jadlog']['cnpjcpf']),
				'ie' => soNumero($CONFIG['jadlog']['ie']),
				'endereco' => $CONFIG['jadlog']['endereco'],
				'numero' => $CONFIG['jadlog']['numero'],
				'compl' => null,
				'bairro' => $CONFIG['jadlog']['bairro'],
				'cidade' => $CONFIG['jadlog']['cidade'],
				'uf' => $CONFIG['jadlog']['uf'],
				'cep' => soNumero($CONFIG['jadlog']['cep']),
				'fone' => soNumero($CONFIG['jadlog']['fone']),
				'cel' => null,
				'email' => $CONFIG['jadlog']['email'],
				'contato' => $CONFIG['jadlog']['contato']
			],
			'des' => [
				'nome' => $rws->nome,
				'cnpjCpf' => soNumero($rws->cpfcnpj),
				'ie' => null,
				'endereco' => $rws->endereco,
				'numero' => $rws->numero,
				'compl' => $rws->complemento,
				'bairro' => $rws->bairro,
				'cidade' => $rws->cidade,
				'uf' => $rws->uf,
				'cep' => soNumero($rws->cep),
				'fone' => soNumero($rws->telefone),
				'cel' => soNumero($rws->celular),
				'email' => $rws->email, 
				'contato' => $rws->nome
			],
			'dfe' => [ [
					'danfeCte' => $danfeCte,
					'cfop' => $cfop,
					'serie' => $serie,
					'nrDoc' => $nrDoc,
					'tpDocumento' => $tpDocumento,
					// 'cfop' => null,
					// 'danfeCte' => null,
					// 'serie' => null,
					// 'nrDoc' => null,
					// 'tpDocumento' => null,
					'valor' => number_format($TOTAL['TOTAL_COMPRA_C_BOLETO'], 2, '.', '')
				]
			],
			'volume' => [ [
					'identificador' => soNumero($rws->codigo_venda),
					'altura' => round($rws->altura),
					'largura' => round($rws->largura),
					'comprimento' => round($rws->comprimento),
					'peso' => $rws->peso
				]
			]
		];
		
		try {
			$ReturnJadLogNew = $JadLogNew->post('/pedido/incluir', $json);
			$json = $ReturnJadLogNew['body'];
			
			if( !empty($json->erro->descricao) ){
				throw new Exception($json->erro->descricao);
			}
			
			$JadLogEtiqueta = new JadLogEtiqueta();
			$JadLogEtiqueta->id_pedido = $id_pedido;
			$JadLogEtiqueta->nrnfe = $nrDoc;
			$JadLogEtiqueta->codigo = $json->codigo;
			$JadLogEtiqueta->shipment_id = $json->shipmentId;
			$JadLogEtiqueta->modalidade = $modalidade;
			$JadLogEtiqueta->volumes = $frete_qtde;
			$JadLogEtiqueta->save_log();

			PedidosLogs::logs($id_pedido, $_SESSION['admin']['id_usuario'], 'Etiqueta Gerada com sucesso', $rws->status);
			header('location: /adm/vendas/vendas-detalhes.php?id=' . $id_pedido);
			return;
		} 
		catch(exception $e) {
			// apenas tenta salvar alguma coisa nos erros se houver
			PedidosLogs::logs($id_pedido, $_SESSION['admin']['id_usuario'], $e->getMessage(), $rws->status);
			header('location: /adm/vendas/vendas-detalhes.php?id=' . $id_pedido);
			return;
		}
	break;
}

include_once '../rodape.php';