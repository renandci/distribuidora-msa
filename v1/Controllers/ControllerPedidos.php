<?php

class ControllerPedidos extends Controller
{

	/**
	 * Somenete oculta o registro
	 */
	public static function delete() {
		global $CONFIG, $_DELETE;

		//  Busca o id de cada registro
		$stmp = Pedidos::find((INT)$_DELETE['id']);
		$stmp->excluir = 1;
		$stmp->save_log();
		$return['msg'] = 'Status OK';
		$return['id'] = $stmp->id;

		Logs::my_logs(['codigo' => $stmp->codigo, 'excluir' => 0], ['codigo' => $stmp->codigo, 'excluir' => 1], (INT)$CONFIG['tkn']['iduser'], 'Pedidos. Exclusão');

		return $return;
	}


	/**
	 * Somenete cria o um registro novo ou edita o proprio
	 */
	public static function create_or_edit() {
		global $CONFIG, $_POST, $_PUT;

		$return = ['msg' => 'Status OK'];

		$REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];

		// Somenete para cadastrar novos dados
		if( $REQUEST_METHOD == 'POST' )
			$params = $_POST;

		// Somenete para editar novos dados
		if( $REQUEST_METHOD == 'PUT' )
			$params = $_PUT;

		if( $REQUEST_METHOD == 'POST' ) {
			$stmp = new Pedidos();
		}

		if( $REQUEST_METHOD == 'PUT' ) {
			if( ! isset( $params['id'] ) && (int)$params['id'] == 0 )
				return ['msg' => 'Fail', 'msg_text' => 'Method requer os id'];

			$stmp = Pedidos::find( (int)$params['id'] );
		}

		foreach($params as $k => $vl)
			$stmp->{$k} = $vl;

		$log = $stmp->save_log();

		if( empty( $log['id'] ) ) {
			$return['msg'] = 'Fail';
			foreach ( $log as $column_name => $error )
				$return[$column_name] = $error;
		}
		else {
			$return['id'][] = $log['id'];
		}

		$text_status  = text_status_vendas($stmp->status);
		$text_status .= $stmp->rastreio ? PHP_EOL . "Cod. rastreio:{$stmp->rastreio}": '';
		$text_status .= $stmp->motivos ? PHP_EOL . "Motivos: {$stmp->motivos}": '';

		PedidosLogs::logs($stmp->id, (INT)$CONFIG['tkn']['iduser'], $text_status, $stmp->status);

		return $return;
	}

	/**
	 * Retorna todos os registro ou um especifico
	 */
	public static function find_by_all( $id = 0 ) {

		global $CONFIG, $_GET;

		$str = array();

		$conditions = array();

		$str['pag'] = ! empty( $_GET['pag'] ) && $_GET['pag'] > 0 ? (INT)$_GET['pag'] : 1;

		$str['limit'] = ! empty( $_GET['limit'] ) && ($_GET['limit'] >= 0 && $_GET['limit'] <= 500 ) ? (INT)$_GET['limit'] : 50;

		// $str['conditions'] = [];

		$conditions['conditions'] = sprintf('loja_id = %u and excluir = 0 ', $CONFIG['loja_id']);

		if( ! empty( $id ) && $id > 0 ) {
			$conditions['conditions'] .= sprintf('and id = %u ', $id);
		}

		if( ! empty( $_GET['q'] ) && $_GET['q'] != '' ) {
			$conditions['conditions'] .= sprintf('and codigo like %%%s%% ', addslashes($_GET['q']));
		}

		$str['limit_rows'] = Pedidos::count($conditions);

		$conditions['limit'] = $str['limit'];

		$conditions['offset'] = ($str['limit'] * ($str['pag'] - 1));

		$conditions['order'] = 'id desc';

		$loop = Pedidos::all($conditions);

		$str['limit'] = ($str['limit'] >= $str['limit_rows'] ? $str['limit_rows'] : $str['limit']);

		$str['pag_first'] = 1;

		$str['pag_last'] = ceil($str['limit_rows'] / $str['limit']);

		$str['pag'] = $str['pag'];

		$rws = null;

		$str['pedidos'] = null;

		// Pedidos
		foreach ( $loop as $ky => $rw )
		{
			$rws['id'] = $rw->id;
			$rws['codigo'] = $rw->codigo;
			$rws['data_venda'] = $rw->data_venda->format('d/m/Y H:i');
			$rws['frete_tipo'] = $rw->frete_tipo;
			$rws['frete_valor'] = number_format($rw->frete_valor, 2, '.', '');
			$rws['valor_compra'] = number_format($rw->valor_compra, 2, '.', '');
			$rws['desconto_cupom'] = $rw->desconto_cupom;
			$rws['desconto_boleto'] = $rw->desconto_boleto;
			$rws['forma_pagamento'] = $rw->forma_pagamento;
			$rws['status'] = $rw->status;
			$rws['status_text'] = text_status_vendas($rw->status);
			$rws['cartao'] = $rw->cartao;
			$rws['parcelas'] = $rw->parcelas;
			$rws['ip'] = $rw->ip;
			$rws['excluir'] = $rw->excluir;
			$rws['nfe'] = [
				'nfe_chave' => $rw->nfes_notas[0]->chavenfe,
				'nfe_nr' => substr($rw->nfes_notas[0]->chavenfe, -18, 8),
			];
			$rws['cliente'] = [
				// 'dados' => [
					'id' => $rw->pedido_cliente->id,
					'nome' => $rw->pedido_cliente->nome,
					'email' => $rw->pedido_cliente->email,
					'cpfcnpj' => $rw->pedido_cliente->cpfcnpj,
					'created_at' => !empty($rw->pedido_cliente->created_at) ? $rw->pedido_cliente->created_at->format('d/m/Y H:i:s') : null,
					'updated_at' => !empty($rw->pedido_cliente->updated_at) ? $rw->pedido_cliente->updated_at->format('d/m/Y H:i:s') : null,
				// ],
			];
			$rws['endereco'] = [
				'id' => $rw->pedido_endereco->id,
				'endereco' => $rw->pedido_endereco->endereco,
				'numero' => $rw->pedido_endereco->numero,
				'bairro' => $rw->pedido_endereco->bairro,
				'complemento' => $rw->pedido_endereco->complemento,
				'referencia' => $rw->pedido_endereco->referencia,
				'cidade' => $rw->pedido_endereco->cidade,
				'uf' => $rw->pedido_endereco->uf,
				'cep' => $rw->pedido_endereco->cep,
				'created_at' => !empty($rw->pedido_endereco->created_at) ? $rw->pedido_endereco->created_at->format('d/m/Y H:i:s') : null,
				'updated_at' => !empty($rw->pedido_endereco->updated_at) ? $rw->pedido_endereco->updated_at->format('d/m/Y H:i:s') : null,
			];

			$rws['pedidos_vendas'] = [];

			$rws['status_logs'] = [];

			// Pedidos Vendas
			foreach( $rw->pedidos_vendas as $f )
			{
				array_push($rws['pedidos_vendas'], [
					'id' => $f->produto->id,
					'codigo_produto' => CodProduto($f->produto->nome_produto, $f->produto->id, $f->produto->codigo_produto),
					'nome_produto' => $f->produto->nome_produto,
					'valor_pago' => number_format($f->valor_pago, 2, '.', ''),
					'grid' => [
						'marca' => [
							'id' => $f->produto->marca->id,
							'marca' => $f->produto->marca->marcas,
							'created_at' => !empty($f->produto->marca->created_at) ? $f->produto->marca->created_at->format('d/m/Y H:i:s') : null,
							'updated_at' => !empty($f->produto->marca->updated_at) ? $f->produto->marca->updated_at->format('d/m/Y H:i:s') : null,
						],
						'cor' => [
							'id' => $f->produto->cor->id,
							'cor' => $f->produto->cor->nomecor,
							'created_at' => !empty($f->produto->cor->created_at) ? $f->produto->cor->created_at->format('d/m/Y H:i:s') : null,
							'updated_at' => !empty($f->produto->cor->updated_at) ? $f->produto->cor->updated_at->format('d/m/Y H:i:s') : null,
						],
						'tamanho' => [
							'id' => $f->produto->tamanho->id,
							'tam' => $f->produto->tamanho->nometamanho,
							'created_at' => !empty($f->produto->tamanho->created_at) ? $f->produto->tamanho->created_at->format('d/m/Y H:i:s') : null,
							'updated_at' => !empty($f->produto->tamanho->updated_at) ? $f->produto->tamanho->updated_at->format('d/m/Y H:i:s') : null,
						],
						// 'descricao' => [
						// 	'id' => $f->produto->descricao->id,
						// 	'descricao' => $f->produto->descricao->nome,
						// 	'descricao_text' => $f->produto->descricao->descricao,
						// 	'created_at' => !empty($f->produto->descricao->created_at) ? $f->produto->descricao->created_at->format('d/m/Y H:i:s') : null,
						// 	'updated_at' => !empty($f->produto->descricao->updated_at) ? $f->produto->descricao->updated_at->format('d/m/Y H:i:s') : null,
						// ],
						// 'frete' => [
						// 	'id' => $f->produto->freteproduto->id,
						// 	'descricao' => $f->produto->freteproduto->nome_frete,
						// 	'peso' => $f->produto->freteproduto->peso,
						// 	'altura' => $f->produto->freteproduto->altura,
						// 	'largura' => $f->produto->freteproduto->largura,
						// 	'comprimento' => $f->produto->freteproduto->comprimento,
						// 	'created_at' => !empty($f->produto->freteproduto->created_at) ? $f->produto->freteproduto->created_at->format('d/m/Y H:i:s') : null,
						// 	'updated_at' => !empty($f->produto->freteproduto->updated_at) ? $f->produto->freteproduto->updated_at->format('d/m/Y H:i:s') : null,
						// ],
						'fotos' => [
							'imagem_xs' => Imgs::src($f->produto->capa->imagem, 'smalls'),
							'imagem_md' => Imgs::src($f->produto->capa->imagem, 'medium'),
							'imagem_lg' => Imgs::src($f->produto->capa->imagem, 'large'),
							'created_at' => !empty($f->produto->capa->updated_at) ? $f->produto->capa->created_at->format('d/m/Y H:i:s') : null,
							'updated_at' => !empty($f->produto->capa->updated_at) ? $f->produto->capa->updated_at->format('d/m/Y H:i:s') : null,
						],
					]
				]);

			}

			// Pedidos Logs
			foreach( $rw->pedidos_logs as $f ) {
				array_push($rws['status_logs'], [
					'status' => $f->status,
					'status_text' => text_status_vendas($f->status),
					'descricao' => $f->descricao,
					'data_envio' => $f->data_envio->format('d/m/Y H:i:s'),
					'created_at' => $f->created_at->format('d/m/Y H:i:s'),
					'updated_at' => $f->updated_at->format('d/m/Y H:i:s')
				]);
			}
			$rws['created_at'] = $rw->created_at->format('d/m/Y H:i:s');
			$rws['updated_at'] = $rw->updated_at->format('d/m/Y H:i:s');
			$str['pedidos'][] = $rws;
		}

		return $str;
	}
}
