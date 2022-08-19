<?php

class ControllerFotos extends Controller
{
	/**
	 * Somenete oculta o registro
	 */
	public static function delete() {
		global $CONFIG, $_DELETE;
		
		//  Busca o id de cada registro
		$stmp = ProdutosImagens::find((INT)$_DELETE['id']);

		$map = [
			self::$CAMINHO . $stmp->imagem,
			self::$CAMINHO . 'medium/' . $stmp->imagem,
			self::$CAMINHO . 'smalls/' . $stmp->imagem
		];
		
		$return = array_map(function($data){
			if( file_exists( $data ) )
				if(unlink($data))
					$str = $data . ' - Removido';

			return $str;
		}, $map);
		
		Logs::my_logs([
				'nome_produto' => $stmp->produto->nome_produto, 
				'imagem' => $stmp->imagem
			], [
				'nome_produto' => $stmp->produto->nome_produto, 
				'imagem' => $stmp->imagem], (INT)$CONFIG['tkn']['iduser'], 'ProdutosImagens. Exclusão');
				
		$return['id'] = $stmp->id;
		$return['msg'] = 'Status OK';
		
		$stmp->delete();
	}
	
	/**
	 * Somenete cria o um registro novo ou edita o proprio
	 * Upload se encontra em controlle *
	 */
	public static function create_or_edit() {
		
		global $CONFIG, $_POST;
		$return = ['msg' => 'Status OK'];

		$REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];
		
		$params = [];
		
		// Somenete para cadastrar novos dados
		if( $REQUEST_METHOD == 'POST' )
			$params = array_merge((count($_POST) > 0 ? $_POST : []), $params);
		
		// // Somenete para editar novos dados
		// if( $REQUEST_METHOD == 'PUT' )
			// $params = array_merge((count($_PUT) > 0 ? $_PUT : []), $params);
		
		if( count($_FILES) > 0 )
			$params = array_merge(current($_FILES), $params);


		
		if(isset($params['id']) && $params['id'] > 0) {
			$stmp = ProdutosImagens::find((INT)$params['id']);
		}
		else {
			$temp = new ProdutosImagens();
			
			$temp->codigo_id = (int)$params['codigo_id'];
			
			$temp->cor_id = (int)$params['cor_id'];
			
			$temp->capa = (int)$params['capa'];
			
			try {
				$temp->save();
				$stmp = ProdutosImagens::find((INT)$temp->id);				
			} catch (Exception $e){				
				return $e;
			}			
		}
		
		$ext = pathinfo($params['name']);
		
		$ext = $ext['extension'];
		
		$stmp->imagem = (is_object($stmp) && (string)$stmp->imagem != '' ? $stmp->imagem : substr(converter_texto($stmp->produto->nome_produto),0,(100-strlen('-'.uniqid(time()).'.'.$ext))).'-'.uniqid(time()).'.'.$ext);
		$stmp->codigo_id = (is_object($stmp) && $stmp->codigo_id != 0 ? $stmp->codigo_id : (int)$params['codigo_id']);
		$stmp->cor_id = (is_object($stmp) && $stmp->cor_id != 0 ? $stmp->cor_id : (int)$params['cor_id']);
		$stmp->capa = (is_object($stmp) && $stmp->capa != 0 ? $stmp->capa : (int)$params['capa']);
		
		$stmp->save();
		
		if( ! $stmp->is_valid() ) {
			$return['msg'] = 'Fail';
			foreach ( $stmp->errors->get_raw_errors() as $column_name => $error ) {
				$return[$column_name] = current( $error );
			}
			return $return;
		}
		
		// Verificar se existe imagem para possivel substituição da mesma
		if ( $params['size'] > 0 )
			static::up_image($params['tmp_name'], $stmp->imagem);
		
		$return['id'] = $stmp->id;

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
		
		$str['limit'] = ! empty( $_GET['limit'] ) && ($_GET['limit'] >= 0 && $_GET['limit'] <= 500 ) ? (INT)$_GET['limit'] : 30;
		
		// $str['conditions'] = [];
		
		$conditions['conditions'] = sprintf('loja_id = %u ', $CONFIG['loja_id']);
		
		if( ! empty( $id ) && $id > 0 ) {
			$conditions['conditions'] .= sprintf('and id = %u ', $id);
		}
		
		if( ! empty( $_GET['q'] ) && $_GET['q'] != '' ) {
			$conditions['conditions'] .= sprintf('and nomecor like %%%s%% ', addslashes($_GET['q']));
		}
		
		$str['limit_rows'] = ProdutosImagens::count($conditions);
		
		$conditions['limit'] = $str['limit'];
		
		$conditions['offset'] = ($str['limit'] * ($str['pag'] - 1));
		
		$conditions['order'] = 'id desc';
		
		$loop = ProdutosImagens::all($conditions);

		$str['limit'] = ($str['limit'] <= $str['limit_rows'] ? $str['limit_rows'] : $str['limit']);
		
		$str['pag_first'] = 1;
		
		$str['pag_last'] = ceil($str['limit_rows'] / $str['limit']);
		
		$str['pag'] = $str['pag'];
		
		$rws = null;
		
		$str['fotos'] = null;
		
		// ProdutosImagens
		foreach ( $loop as $ky => $rw ) 
		{
			$rws['id'] = $rw->id;
			$rws['capa'] = $rw->capa;
			$rws['codigo_id'] = $rw->codigo_id;
			$rws['cor_id'] = $rw->cor_id;
			$rws['ordem'] = $rw->ordem;
			$rws['imagem'] = $rw->imagem;
			$rws['imagem_xs'] = Imgs::src($rw->imagem, 'smalls');
			$rws['imagem_sm'] = Imgs::src($rw->imagem, 'medium');
			$rws['imagem_lg'] = Imgs::src($rw->imagem, 'large');
			$rws['created_at'] = !empty($rw->created_at) ? $rw->created_at->format('d/m/Y H:i:s') : null;
			$rws['updated_at'] = !empty($rw->updated_at) ? $rw->updated_at->format('d/m/Y H:i:s') : null;
			$str['fotos'][] = $rws;
		}
		
		if( ! empty( $id ) ) {
			unset($str['pag'], $str['limit'], $str['conditions']);
			$str = current($str['fotos']);
		}
		
		return $str;
	}
}