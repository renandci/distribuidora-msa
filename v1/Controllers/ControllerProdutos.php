<?php

class ControllerProdutos extends Controller
{
	/**
	 * Somenete oculta o registro
	 */
	public static function delete() {
		global $CONFIG, $_DELETE;

		//  Busca o id de cada registro
		$stmp = Produtos::find((int)$_DELETE['id']);
		$stmp->excluir = 1;
		$stmp->save_log();
		$return['msg'] = 'Status OK';
		$return['id'] = $stmp->id;

		return $return;
	}

	/**
	 * Somenete cria o um registro novo ou edita o proprio
	 */
	public static function create_or_edit() {

		global $CONFIG, $_POST, $_PUT;

		$codigo_id = time();

		$return = ['msg' => 'Status OK'];

		$REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];

		// Somenete para cadastrar novos dados
		if( $REQUEST_METHOD == 'POST' )
			$params = $_POST;

		// Somenete para editar novos dados
		if( $REQUEST_METHOD == 'PUT' )
			$params = $_PUT;

		$array[] = $params;
		if(isset($params['variations']) && $params['variations'] != '')
		{
			$variations = $params['variations'];

			unset($array, $params['variations']);

			foreach( $variations as $str )
			{
				$array[] = $params + $str;
			}
		}

		foreach($array as $array_rws)
		{
			if( $REQUEST_METHOD == 'POST' ) {
				$stmp = new Produtos();
				$array_rws['codigo_id'] = $codigo_id;
			}
			if( $REQUEST_METHOD == 'PUT' ) {
				if( ! isset( $array_rws['id'] ) && (int)$array_rws['id'] == 0 )
					return ['msg' => 'Fail', 'msg_text' => 'Method requer os id dos produtos'];

				$stmp = Produtos::find( (int)$array_rws['id'] );
			}

			foreach($array_rws as $k => $vl)
				$stmp->{$k} = $vl;

			$log = $stmp->save_log();

			if( empty( $log['id'] ) ) {
				$return['msg'] = 'Fail';
				$return[$k][] = $log[$k];
			}
			else {
				$return['id'][] = $log['id'];
			}
		}

		return $return;

		// // Created variations
		// if( $REQUEST_METHOD == 'PUT' ) {

		// 	if(!isset($params['codigo_id']) && (isset($params['variations']) && $params['variations'] != '')) {

		// 		foreach( $params['variations'] as $str ) {

		// 			//  Busca o id de cada registro
		// 			$stmp = Produtos::find($str['id']);

		// 			foreach( $str as $name => $values ) {
		// 				$stmp->{$name} = $values;
		// 			}

		// 			$log = $stmp->save_log();
		// 			if( empty( $log['id'] ) ) {
		// 				$return['msg'] = 'Fail';
		// 				$k = current(array_keys($log));
		// 				$return[$k][] = current($log);
		// 			}

		// 			$return['id'][] = $stmp->id;
		// 		}
		// 	}
		// 	else if(!isset($params['variations']) && (isset($params['codigo_id']) && $params['codigo_id'] != '')) {

		// 		$stmp_tmp = Produtos::all(['conditions' => ['codigo_id=?', (int)$params['codigo_id']]]);

		// 		foreach( $stmp_tmp as $key => $val ) {

		// 			//  Busca o id de cada registro
		// 			$stmp = Produtos::find($val->id);

		// 			foreach( $params as $name => $values ) {
		// 				$stmp->{$name} = $values;
		// 			}

		// 			$log = $stmp->save_log();
		// 			if( empty( $log['id'] ) ) {
		// 				$return['msg'] = 'Fail';
		// 				$k = current(array_keys($log));
		// 				$return[$k][] = current($log);
		// 			}

		// 			$return['id'][] = $stmp->id;
		// 		}
		// 	}

		// 	return $return;
		// }

		// if( isset( $params['codigo_id'] ) && $params['codigo_id'] > 0 && $REQUEST_METHOD != 'POST' )
		// {
		// 	// Busca todos do grupo se há necessidade
		// 	$stmp_tmp = Produtos::all(['conditions' => ['codigo_id=?', (int)$params['codigo_id']]]);

		// 	// Percorre os dados
		// 	foreach( $stmp_tmp as $key => $val )
		// 	{
		// 		//  Busca o id de cada registro
		// 		$stmp = Produtos::find($val->id);

		// 		if( $REQUEST_METHOD == 'POST' ) $stmp::$validates_presence_of[0] = ['nome_produto', 'message' => 'Digite o nome do produto!'];

		// 		if( $REQUEST_METHOD == 'POST' ) $stmp::$validates_numericality_of[0] = ['id_marca', 'greater_than' => 0, 'message' => 'Selecione uma marca!'];

		// 		foreach( $params as $name => $values )
		// 		{
		// 			try {
		// 				self::$array_before[$name] = $stmp->{$name};

		// 				self::$array_after[$name] = addslashes($values);

		// 				if(static::test_float(static::moeda($values)))
		// 					$stmp->{$name} = static::moeda($values);
		// 				else
		// 					$stmp->{$name} = addslashes($values);
		// 			}
		// 			catch (Exception $e) {
		// 			}
		// 		}

		// 		$stmp->save();

		// 		if( ! $stmp->is_valid() ) {
		// 			$return['msg'] = 'Fail';
		// 			foreach ( $stmp->errors->get_raw_errors() as $column_name => $error ) {
		// 				$return[$column_name] = current( $error );
		// 			}
		// 			return $return;
		// 		}
		// 		$return['id'][] = $stmp->id;
		// 	}

		// 	Logs::my_logs(self::$array_before, self::$array_after, (int)$CONFIG['tkn']['iduser'], 'Produtos');

		// 	return $return;
		// }
		// else if( isset( $params['id'] ) && $params['id'] > 0 && $REQUEST_METHOD != 'POST' ) {
		// 	$stmp = Produtos::find((int)$params['id']);
		// }
		// else {
		// 	$stmp = new Produtos();
		// 	$params['codigo_id'] = isset($params['codigo_id']) && $params['codigo_id'] > 0 ? $params['codigo_id'] : time();
		// }

		// if( $REQUEST_METHOD == 'POST' ) $stmp::$validates_presence_of[0] = ['nome_produto', 'message' => 'Digite o nome do produto!'];

		// if( $REQUEST_METHOD == 'POST' ) $stmp::$validates_numericality_of[0] = ['id_marca', 'greater_than' => 0, 'message' => 'Selecione uma marca!'];

		// foreach( $params as $name => $values )
		// {
		// 	try {
		// 		self::$array_before[$name] = $stmp->{$name};

		// 		self::$array_after[$name] = addslashes($values);

		// 		if(static::test_float(static::moeda($values)))
		// 			$stmp->{$name} = static::moeda($values);
		// 		else
		// 			$stmp->{$name} = addslashes($values);
		// 	}
		// 	catch (Exception $e) { }
		// }

		// $stmp->save();

		// if( ! $stmp->is_valid() ) {
		// 	$return['msg'] = 'Fail';
		// 	foreach ( $stmp->errors->get_raw_errors() as $column_name => $error ) {
		// 		$return[$column_name] = current( $error );
		// 	}
		// 	return $return;
		// }

		// $return['id'][] = $stmp->id;

		// Logs::my_logs(self::$array_before, self::$array_after, (int)$CONFIG['tkn']['iduser'], 'Produtos');

		// return $return;
	}

	/**
	 * Retorna todos os registro ou um especifico
	 */
	public static function find_by_all( $id = 0 ) {

		global $CONFIG, $_GET;

		$str = array();

		$conditions = array();

		$str['pag'] = ! empty( $_GET['pag'] ) && $_GET['pag'] > 0 ? (int)$_GET['pag'] : 1;

		$str['limit'] = ! empty( $_GET['limit'] ) && ($_GET['limit'] >= 0 && $_GET['limit'] <= 500 ) ? (int)$_GET['limit'] : 50;

		// $str['conditions'] = null;

		$conditions['conditions'] = sprintf('loja_id = %u and excluir = 0 ', $CONFIG['loja_id']);

		if( ! empty( $_GET['codigo_id'] ) && $_GET['codigo_id'] > 0 ) {
			$conditions['conditions'] .= sprintf('and codigo_id = %u ', (int)$_GET['codigo_id']);
		}

		if( ! empty( $id ) && $id > 0 ) {
			$conditions['conditions'] .= sprintf('and id = %u ', (int)$id);
		}

		if( ! empty( $_GET['q'] ) && $_GET['q'] != '' ) {
			$conditions['conditions'] .= sprintf('and nome_produto like "%%%s%%" ', addslashes($_GET['q']));
		}

		$str['limit_rows'] = (int)Produtos::count($conditions);

		$conditions['limit'] = $str['limit'];

		$conditions['offset'] = ($str['limit'] * ($str['pag'] - 1));

		$conditions['order'] = 'id desc';

		$loop = Produtos::all($conditions);

		$str['limit'] = ($str['limit'] >= $str['limit_rows'] ? $str['limit_rows'] : $str['limit']);

		$str['pag_first'] = 1;

		$str['pag_last'] = ceil($str['limit_rows'] / $str['limit']);

		$str['pag'] = $str['pag'];

		$prod = null;

		$str['produtos'] = null;

		// Produtos
		foreach ( $loop as $ky => $rw )
		{
			$prod['id'] = $rw->id;
			$prod['codigo_id'] = $rw->codigo_id;
			$prod['codigo_produto'] = CodProduto($rw->nome_produto, $rw->id, $rw->codigo_produto);
			$prod['nome_produto'] = $rw->nome_produto;
			$prod['preco_custo'] = number_format($rw->preco_custo, 2, '.', '');
			$prod['preco_venda'] = number_format($rw->preco_venda, 2, '.', '');
			$prod['preco_promo'] = number_format($rw->preco_promo, 2, '.', '');
			$prod['estoque'] = $rw->estoque;
			$prod['ncm'] = $rw->ncm;
			$prod['cest'] = $rw->cest;
			$prod['status'] = $rw->status;
			$prod['excluir'] = $rw->excluir;
			$prod['marca'] = [
				'id_marca' => $rw->marca->id,
				'marca' => $rw->marca->marcas,
				'created_at' => !empty($rw->marca->created_at) ? $rw->marca->created_at->format('d/m/Y H:i:s') : null,
				'updated_at' => !empty($rw->marca->updated_at) ? $rw->marca->updated_at->format('d/m/Y H:i:s') : null,
			];
			$prod['cor'] = [
				'id_cor' => $rw->cor->id,
				'cor' => $rw->cor->nomecor,
				'created_at' => !empty($rw->cor->created_at) ? $rw->cor->created_at->format('d/m/Y H:i:s') : null,
				'updated_at' => !empty($rw->cor->updated_at) ? $rw->cor->updated_at->format('d/m/Y H:i:s') : null,
			];
			$prod['tamanho'] = [
				'id_tamanhos' => $rw->tamanho->id,
				'tam' => $rw->tamanho->nometamanho,
				'created_at' => !empty($rw->tamanho->created_at) ? $rw->tamanho->created_at->format('d/m/Y H:i:s') : null,
				'updated_at' => !empty($rw->tamanho->updated_at) ? $rw->tamanho->updated_at->format('d/m/Y H:i:s') : null,
			];
			$prod['descricao'] = [
				'id_descricao' => $rw->descricao->id,
				'descricao' => $rw->descricao->nome,
				'descricao_text' => $rw->descricao->descricao,
				'created_at' => !empty($rw->descricao->created_at) ? $rw->descricao->created_at->format('d/m/Y H:i:s') : null,
				'updated_at' => !empty($rw->descricao->updated_at) ? $rw->descricao->updated_at->format('d/m/Y H:i:s') : null,
			];
			$prod['frete'] = [
				'id_frete' => $rw->freteproduto->id,
				'descricao' => $rw->freteproduto->nome_frete,
				'peso' => $rw->freteproduto->peso,
				'altura' => $rw->freteproduto->altura,
				'largura' => $rw->freteproduto->largura,
				'comprimento' => $rw->freteproduto->comprimento,
				'created_at' => !empty($rw->freteproduto->created_at) ? $rw->freteproduto->created_at->format('d/m/Y H:i:s') : null,
				'updated_at' => !empty($rw->freteproduto->updated_at) ? $rw->freteproduto->updated_at->format('d/m/Y H:i:s') : null,
			];
			$prod['fotos'] = [];

			// Imagens
			foreach( $rw->fotos as $f ) {
				array_push($prod['fotos'], [
					'id' => $f->id,
					'codigo_id' => $f->codigo_id,
					'capa' => $f->capa,
					'imagem_xs' => Imgs::src($f->imagem, 'smalls'),
					'imagem_md' => Imgs::src($f->imagem, 'medium'),
					'imagem_lg' => Imgs::src($f->imagem, 'large'),
					'created_at' => !empty($f->created_at) ? $f->created_at->format('d/m/Y H:i:s') : null,
					'updated_at' => !empty($f->updated_at) ? $f->updated_at->format('d/m/Y H:i:s') : null,
				]);
			}
			$rws['created_at'] = !empty($rw->created_at) ? $rw->created_at->format('d/m/Y H:i:s') : null;
			$rws['updated_at'] = !empty($rw->updated_at) ? $rw->updated_at->format('d/m/Y H:i:s') : null;
			$str['produtos'][] = $prod;
		}

		return $str;
	}
}
