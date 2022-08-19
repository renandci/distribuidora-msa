<?php

class ControllerMenus extends Controller
{
	
	/**
	 * Somenete oculta o registro
	 */
	public static function delete() {
		global $CONFIG, $_DELETE;
		//  Busca o id de cada registro
		$stmp = ProdutosMenus::find((INT)$_DELETE['id']);
		// $stmp->delete();
		$return['msg'] = 'Status OK';
		$return['id'] = $stmp->id;		
		Logs::my_logs(['grupo' => $stmp->grupo->grupo, 'excluir' => 0], ['grupo' => $stmp->grupo->grupo, 'excluir' => 1], (INT)$CONFIG['tkn']['iduser'], 'ProdutosMenus. Exclusão');
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
		
			ProdutosMenus::$validates_numericality_of[0] = ['id_grupo', 'greater_than' => 0, 'message' => 'Selecione um grupo!'];
		
			ProdutosMenus::$validates_numericality_of[0] = ['codigo_id', 'greater_than' => 0, 'message' => 'Selecione um produto!'];

			$stmp = new ProdutosMenus();
		}

		if( $REQUEST_METHOD == 'PUT' ) {
			if( ! isset( $params['id'] ) && (int)$params['id'] == 0 ) 
				return ['msg' => 'Fail', 'msg_text' => 'Method requer os id'];

			$stmp = ProdutosMenus::find( (int)$params['id'] );
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
		
		$str = [];
		$conditions = [];
		$conditions['conditions'] = sprintf('loja_id = %u and grupo_id > 0 ', $CONFIG['loja_id']);
		
		// if( ! empty( $id ) && $id > 0 ) {
		// 	$conditions['conditions'] .= sprintf('and id = %u ', $id);
		// }
		
		$loop = ProdutosMenusViewsTemp::all($conditions);
		
		$str['menus'] = null;
		$grupos = null;
		$subgrupos = null;
		// ProdutosMenus
		foreach ( $loop as $ky => $rws ) 
		{
			// $rws['id'] = $rw->id;
			// $rws['codigo_id'] = $rw->codigo_id;
			// $rws['grupo'] = $rw->grupo->grupo;
			// $rws['id_grupo'] = $rw->id_grupo;
			// $rws['id_subgrupo'] = $rw->id_subgrupo;
			// $rws['subgrupo'] = $rw->subgrupo->subgrupo;
			// $rws['id_subgrupo'] = $rw->id_subgrupo;
			// $rws['created_at'] = !empty($rw->created_at) ? $rw->created_at->format('d/m/Y H:i:s') : null;
			// $rws['updated_at'] = !empty($rw->updated_at) ? $rw->updated_at->format('d/m/Y H:i:s') : null;
			// $str['menus'][$rw->codigo_id][] = $rws;

			$subgrupos[$rws->subgrupos_id][$rws->subgrupos_id] = [
				'subgrupos_id' 		    => $rws->subgrupos_id,
				'subgrupos' 			=> $rws->sbgp->subgrupo,
				'subgrupos_description' => $rws->sbgp->subgrupo_description,
				'subgrupos_keywords'	=> $rws->sbgp->subgrupo_keywords,
				'subgrupos_icon'		=> null,
				'subgrupos_ordem'		=> $rws->sbgp->ordem,
				'subgrupos_excluir'		=> $rws->sbgp->excluir,
				'produto_subgrupo_id'	=> $rws->sbgp->produto_subgrupo_id,
				'created_at' => !empty($rws->sbgp->created_at) ? $rws->sbgp->created_at->format('d/m/Y H:i:s') : null,
				'updated_at' => !empty($rws->sbgp->updated_at) ? $rws->sbgp->updated_at->format('d/m/Y H:i:s') : null
			];

			$grupos[$rws->grupo_id] = [
                'grupo_id' 			=> $rws->grupo_id,
				'grupo' 			=> $rws->gp->grupo,
                'grupo_description' => $rws->gp->grupo_description,
                'grupo_keywords'	=> $rws->gp->grupo_keywords,
                'grupo_icon'		=> $rws->gp->grupo_icon,
                'grupo_ordem'		=> $rws->gp->ordem,
                'grupo_excluir'		=> $rws->gp->excluir,
                'produto_grupo_id'	=> $rws->gp->produto_grupo_id,
                'subgrupos' 		=> $subgrupos[$rws->subgrupos_id],
				'created_at' => !empty($rws->gp->created_at) ? $rws->gp->created_at->format('d/m/Y H:i:s') : null,
				'updated_at' => !empty($rws->gp->updated_at) ? $rws->gp->updated_at->format('d/m/Y H:i:s') : null
			];

			$str['menus'] = $grupos;
		}

		return $str;
	}
}