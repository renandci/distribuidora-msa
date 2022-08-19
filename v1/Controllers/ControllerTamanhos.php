<?php

class ControllerTamanhos extends Controller
{
	
	/**
	 * Somenete oculta o registro
	 */
	public static function delete() {
		global $CONFIG, $_DELETE;
		
		//  Busca o id de cada registro
		$stmp = Tamanhos::find((int)$_DELETE['id']);
		$stmp->excluir = 1;
		$stmp->save_log();
		$return['msg'] = 'Status OK';
		$return['id'] = $_DELETE['id'];
		
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
		
			Tamanhos::$validates_presence_of[0] = ['nometamanho', 'message' => 'Campo nome obrigatório!'];

			Tamanhos::$validates_numericality_of[0] = ['opcoes_id', 'greater_than' => 0, 'message' => 'Selecione a opção de grid!'];

			$stmp = new Tamanhos();
		}

		if( $REQUEST_METHOD == 'PUT' ) {
			if( ! isset( $params['id'] ) && (int)$params['id'] == 0 ) 
				return ['msg' => 'Fail', 'msg_text' => 'Method requer os id'];

			$stmp = Tamanhos::find( (int)$params['id'] );
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

		return $return;
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
		
		// $str['conditions'] = [];
		
		$conditions['conditions'] = sprintf('loja_id = %u and excluir = 0 ', $CONFIG['loja_id']);
		
		if( ! empty( $id ) && $id > 0 ) {
			$conditions['conditions'] .= sprintf('and id = %u ', $id);
		}
		
		if( ! empty( $_GET['q'] ) && $_GET['q'] != '' ) {
			$conditions['conditions'] .= sprintf('and nometamanho like %%%s%% ', addslashes($_GET['q']));
		}
		
		$str['limit_rows'] = Tamanhos::count($conditions);
		
		$conditions['limit'] = $str['limit'];
		
		$conditions['offset'] = ($str['limit'] * ($str['pag'] - 1));
		
		$conditions['order'] = 'id desc';
		
		$loop = Tamanhos::all($conditions);

		$str['limit'] = ($str['limit'] <= $str['limit_rows'] ? $str['limit_rows'] : $str['limit']);
		
		$str['pag_first'] = 1;
		
		$str['pag_last'] = ceil($str['limit_rows'] / $str['limit']);
		
		$str['pag'] = $str['pag'];
		
		$rws = null;
		
		$str['tamanhos'] = null;
		
		// Tamanhos
		foreach ( $loop as $ky => $rw ) 
		{
			$rws['id'] = $rw->id;
			$rws['grid'] = [
				'opcoes_id' => $rw->opcoes_id,
				'opcao' => $rw->opcoes->tipo,
				'filtro' => $rw->opcoes->filtro,
				'ordem' => $rw->opcoes->ordem,
			];
			$rws['nometamanho'] = $rw->nometamanho;
			$rws['ordem'] = $rw->ordem;
			$rws['excluir'] = $rw->excluir;
			$rws['created_at'] = !empty($rw->created_at) ? $rw->created_at->format('d/m/Y H:i:s') : null;
			$rws['updated_at'] = !empty($rw->updated_at) ? $rw->updated_at->format('d/m/Y H:i:s') : null;
			$str['tamanhos'][] = $rws;
		}

		return $str;
	}
}