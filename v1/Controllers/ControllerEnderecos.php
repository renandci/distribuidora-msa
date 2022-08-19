<?php

class ControllerEnderecos extends Controller
{
	
	/**
	 * Somenete oculta o registro
	 */
	public static function delete() {
		global $CONFIG, $_DELETE;
		
		//  Busca o id de cada registro
		$stmp = ClientesEnderecos::find((INT)$_DELETE['id']);
		$return['msg'] = 'Status OK';
		$return['id'] = $stmp->id;
		Logs::my_logs(
			['endereco' => $stmp->endereco, 'cidade' => $stmp->cidade, 'uf' => $stmp->uf, 'cep' => $stmp->cep], 
			['endereco' => $stmp->endereco, 'cidade' => $stmp->cidade, 'uf' => $stmp->uf, 'cep' => $stmp->cep], (INT)$CONFIG['tkn']['iduser'], 'ClientesEnderecos. Exclusão');
		$stmp->delete();
		
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
			$stmp = ClientesEnderecos::find((INT)$params['id']);
		} 
		else {
			$stmp = new ClientesEnderecos();
		}
		
		foreach( $params as $name => $values ) 
		{
			try {
				self::$array_before[$name] = $stmp->{$name};
				
				self::$array_after[$name] = addslashes($values);
				
				$stmp->{$name} = addslashes($values);
			} 
			catch (Exception $e) {
			}
		} 
		
		$stmp->save();
		
		$return['id'] = $stmp->id;
		
		Logs::my_logs(self::$array_before, self::$array_after, $CONFIG['tkn']['iduser'], 'ClientesEnderecos');
		
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
		
		$conditions['conditions'] = sprintf('loja_id = %u ', $CONFIG['loja_id']);
		
		if( ! empty( $id ) && $id > 0 ) {
			$conditions['conditions'] .= sprintf('and id = %u ', $id);
		}
		
		if( ! empty( $_GET['q'] ) && $_GET['q'] != '' ) {
			$conditions['conditions'] .= sprintf('and endereco like %%%s%% or cidade like %%%s%% or ', addslashes($_GET['q']), addslashes($_GET['q']));
		}
		
		$conditions['limit'] = $str['limit'];
		
		$conditions['offset'] = ($str['limit'] * ($str['pag'] - 1));
		
		$conditions['order'] = 'id desc';
		
		$loop = ClientesEnderecos::all($conditions);
		
		$rws = null;
		
		$str['enderecos'] = null;
		
		// ClientesEnderecos
		foreach ( $loop as $ky => $rw ) 
		{
			$rws['id'] = $rw->id;
			$rws['id_cliente'] = $rw->id_cliente;
			$rws['cep'] = $rw->cep;
			$rws['status'] = $rw->status;
			$rws['endereco'] = $rw->endereco;
			$rws['bairro'] = $rw->bairro;
			$rws['numero'] = $rw->numero;
			$rws['complemento'] = $rw->complemento;
			$rws['referencia'] = $rw->referencia;
			$rws['cidade'] = $rw->cidade;
			$rws['uf'] = $rw->uf;
			$rws['created_at'] = !empty($rw->created_at) ? $rw->created_at->format('d/m/Y H:i:s') : null;
			$rws['updated_at'] = !empty($rw->updated_at) ? $rw->updated_at->format('d/m/Y H:i:s') : null;
			$str['enderecos'][] = $rws;
		}

		return $str;
	}
}