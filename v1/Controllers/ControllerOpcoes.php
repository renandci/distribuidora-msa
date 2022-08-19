<?php

class ControllerOpcoes extends Controller
{
	
	/**
	 * Somenete oculta o registro
	 */
	public static function delete() {
		global $CONFIG, $_DELETE;
		
		//  Busca o id de cada registro
		$stmp = OpcoesTipo::find((INT)$_DELETE['id']);
		$stmp->excluir = 1;
		$stmp->save();
		$return['msg'] = 'Status OK';
		$return['id'] = $stmp->id;
		
		Logs::my_logs(['tipo' => $stmp->tipo, 'excluir' => 0], ['tipo' => $stmp->tipo, 'excluir' => 1], (INT)$CONFIG['tkn']['iduser'], 'OpcoesTipo. Exclusão');
		
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
			$stmp = OpcoesTipo::find((INT)$params['id']);
		} 
		else {
			$stmp = new OpcoesTipo();
		}
		
		$stmp::$validates_presence_of[0] = ['tipo', 'message' => 'Digite um tipo de opção!'];
		
		foreach( $params as $name => $values ) 
		{
			try {
				self::$array_before[$name] = $stmp->{$name};
				
				self::$array_after[$name] = addslashes($values);
				
				$stmp->{$name} = addslashes($values);
			} 
			catch (Exception $e) {}
		} 
		
		if( ! $stmp->is_valid() ) {
			$return['msg'] = 'Fail';
			foreach ( $stmp->errors->get_raw_errors() as $column_name => $error ) {
				$return[$column_name] = current( $error );
			}
			return $return;
		}
		
		$stmp->save();
		
		$return['id'] = $stmp->id;
		
		Logs::my_logs(self::$array_before, self::$array_after, (INT)$CONFIG['tkn']['iduser'], 'OpcoesTipo');
		
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
			$conditions['conditions'] .= sprintf('and tipo like %%%s%% ', addslashes($_GET['q']));
		}
		
		$str['limit_rows'] = OpcoesTipo::count($conditions);
		
		$conditions['limit'] = $str['limit'];
		
		$conditions['offset'] = ($str['limit'] * ($str['pag'] - 1));
		
		$conditions['order'] = 'id desc';
		
		$loop = OpcoesTipo::all($conditions);

		$str['limit'] = ($str['limit'] <= $str['limit_rows'] ? $str['limit_rows'] : $str['limit']);
		
		$str['pag_first'] = 1;
		
		$str['pag_last'] = ceil($str['limit_rows'] / $str['limit']);
		
		$str['pag'] = $str['pag'];
		
		$rws = null;
		
		$str['opcoes'] = null;
		
		// OpcoesTipo
		foreach ( $loop as $ky => $rw ) 
		{
			$rws['id'] = $rw->id;
			$rws['tipo'] = $rw->tipo;
			$rws['ordem'] = $rw->ordem;
			$rws['excluir'] = $rw->excluir;
			$rws['created_at'] = !empty($rw->created_at) ? $rw->created_at->format('d/m/Y H:i:s') : null;
			$rws['updated_at'] = !empty($rw->updated_at) ? $rw->updated_at->format('d/m/Y H:i:s') : null;
			$str['opcoes'][] = $rws;
		}
		
		if( ! empty( $id ) ) {
			unset($str['pag'], $str['limit'], $str['conditions']);
			$str = current($str['opcoes']);
		}
		
		return $str;
	}
}