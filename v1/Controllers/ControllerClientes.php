<?php

class ControllerClientes extends Controller
{
	
	/**
	 * Somenete oculta o registro
	 */
	public static function delete() {
		global $CONFIG, $_DELETE;
		
		//  Busca o id de cada registro
		$stmp = Clientes::find((INT)$_DELETE['id']);
		$stmp->excluir = 1;
		$stmp->save();
		$return['msg'] = 'Status OK';
		$return['id'] = $stmp->id;
		Logs::my_logs(['nome' => $stmp->nome, 'excluir' => 0], ['nome' => $stmp->nome, 'excluir' => 1], $CONFIG['tkn']['iduser'], null);
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
		
		if( isset( $params['id'] ) && $params['id'] > 0 && $REQUEST_METHOD != 'POST' ) {
			$stmp = Clientes::find((INT)$params['id']);
		} 
		else {
			$stmp = new Clientes();
		}
		
		foreach( $params as $name => $values ) 
		{
			try {
				self::$array_before[$name] = $stmp->{$name};
				
				self::$array_after[$name] = addslashes($values);
				
				if(static::test_float(static::moeda($values)))
					$stmp->{$name} = static::moeda($values);
				else
					$stmp->{$name} = addslashes($values);
			} 
			catch (Exception $e) {}
		} 
		
		$stmp->save();
		
		$return['id'] = $stmp->id;
		
		Logs::my_logs(self::$array_before, self::$array_after, $CONFIG['tkn']['iduser'], null);
		
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
			$conditions['conditions'] .= sprintf('and nome like %%%s%% or email like %%%s%% or ', addslashes($_GET['q']), addslashes($_GET['q']));
		}
		
		$conditions['limit'] = $str['limit'];
		
		$conditions['offset'] = ($str['limit'] * ($str['pag'] - 1));
		
		$conditions['order'] = 'id desc';
		
		$loop = Clientes::all($conditions);
		
		$rws = null;
		
		$str['clientes'] = null;
		
		// Clientes
		foreach ( $loop as $ky => $rw ) 
		{
			$rws['id'] = $rw->id;
			$rws['nome'] = $rw->nome;
			$rws['email'] = $rw->email;
			$rws['cpfcnpj'] = $rw->cpfcnpj;
			$rws['excluir'] = $rw->excluir;
			$rws['enderecos'] = [];

			foreach( $rw->enderecos as $f ) 
			{
				array_push($rws['enderecos'], [ 
					'id' => $f->id, 
					'status' => $f->status,
					'endereco' => $f->endereco,
					'numero' => $f->numero,
					'bairro' => $f->bairro,
					'complemento' => $f->complemento,
					'referencia' => $f->referencia,
					'cidade' => $f->cidade,
					'uf' => $f->uf,
					'cep' => $f->cep,
					'created_at' => !empty($rw->created_at) ? $rw->created_at->format('d/m/Y H:i:s') : null,
					'updated_at' => !empty($rw->updated_at) ? $rw->updated_at->format('d/m/Y H:i:s') : null,
				]);	
			}
			$rws['created_at'] = !empty($rw->created_at) ? $rw->created_at->format('d/m/Y H:i:s') : null;
			$rws['updated_at'] = !empty($rw->updated_at) ? $rw->updated_at->format('d/m/Y H:i:s') : null;
			$str['clientes'][] = $rws;
		}

		return $str;
	}
}