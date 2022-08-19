<?php
include '../topo.php';	

// unset($_SESSION);

AcessoML($_SESSION, $PgAt);

$params = array('access_token' => $_SESSION['access_token']);

$VerificarApp = $meli->get('users/' . $CONFIG_MELI['app'], $params);

if( isset($_SESSION['access_token_id'], $VerificarApp['body']->id) && $VerificarApp['body']->id != $_SESSION['access_token_id'] ) { ?>
<div class="text-center">
    <h2>Atenção: usuario não cadastrado para o marketingplace do mercado livre</h2>
    <p>Clique no link a seguir, e faça o login com seu usuário <b><?php echo $VerificarApp['body']->nickname?></b></p>
    <a href="/adm/sair.php?acao=LogOutML&_u=<?php echo URL_BASE_HTTPS?>adm/mercadolivre/ml-auth.php" class="btn btn-danger">sair</a>
</div>
<?php 
include '../rodape.php';
return;
}

/**
 * Obtem a lista cadastrada de produtos no mercado livree
 */
if( isset($GET['acao']) && $GET['acao'] == 'ObterListagem' ) 
{
	$count = 0;
	$maximo = 50;
    $date = date('Y-m-d H:i:s');
	$ListagemAll = $meli->get('users/' . $CONFIG_MELI['app'] . '/items/search', array('access_token' => $_SESSION['access_token']) );

	$total = ceil( (INT)current( $ListagemAll )->paging->total / $maximo );
	/**
	 * Gera uma nova paginacao simples de produtos
	 */
	for( $offset = 0; $offset < $total; $offset++ )
	{
		/**
		 * Faz uma simples verificação do resultado, obtendo os ids dos produtos
		 */
		$params = array(
			'offset' => $maximo * $offset,
			'access_token' => $_SESSION['access_token'],
		);

		$listagem = $meli->get('users/' . $CONFIG_MELI['app'] . '/items/search', $params );

		/**
		 * Faz uma simples verificação do resultado, obtendo os ids dos produtos
		 */
		if( count( $listagem['body']->results ) > 0 )
		{
			foreach ( $listagem['body']->results as $key => $val )
			{
				if( MercadoLivreProdutos::count(['conditions'=>['produtos_ml_id=?', $val] ]) == 0 )
				{
					$new = current($meli->get('items/' . $val, $params));
					if( $new->status != 'closed' )
					{    
						$MercadoLivreProdutos = MercadoLivreProdutos::action_cadastrar_editar([ 
							'MercadoLivreProdutos' => [ 0 => [
								'mercadolivre_id' => $CONFIG_MELI['id'],
								'produtos_codigo_id' => 0,
								'produtos_ml_id' => $new->id,
								'produtos_ml_title' => $new->title, 
								'produtos_ml_preco' => $new->price,
								'produtos_ml_status' => $new->status,
								'produtos_ml_estoque' => $new->initial_quantity,
								] ] ], 'cadastrar', 'id');
						if( $MercadoLivreProdutos > 0 ) {
							$count++;
						}
					}
				}
			}
			if( $count > 0 ) {
				header('location: /adm/mercadolivre/ml-produtos.php?_ml=');
				return;
			}
		}
	}
}

/**
 * Vinculação do produto via ML ao site
 */
if( isset($GET['acao'], $GET['codigo_id'], $GET['produto_vincular_id']) && $GET['acao'] == 'VincularProdutoSite' ) {
	$MercadoLivreProdutos = MercadoLivreProdutos::find($GET['produto_vincular_id']);
	$MercadoLivreProdutos->produtos_codigo_id = $GET['codigo_id'];
	$MercadoLivreProdutos->save();

	Logs::create_logs('Veinculou produto ao mercadolivre: ' . $GET['produto_vincular_id'] . ' a '. $GET['codigo_id'], $_SESSION['admin']['id_usuario']);

	header('Location: /adm/mercadolivre/ml-produtos.php');
	return;
   
}

/**
 * Altualiza as informações via mercado livre
 */
if( isset( $POST['acao'] ) && $POST['acao'] == 'VeincularProduto' ) 
{
    if( isset($POST['produto_ml_id']) && $POST['produto_ml_id'] != '' ) {
		
		$ml = $meli->get('users/' . $CONFIG_MELI['app'], $params);
		
		$MercadoLivreProdutos = MercadoLivreProdutos::find([ 'conditions' => ['produtos_codigo_id=?', $GET['produto_vincular_id']]]);
		$MercadoLivreProdutos->produtos_ml_id = $ml['body']->site_id . $POST['produto_ml_id'];
		$MercadoLivreProdutos->save();
		
		Logs::create_logs('Veinculou produto ao mercadolivre: ' . $ml['body']->site_id . $POST['produto_ml_id'] . ' a '. $GET['produto_vincular_id'], $_SESSION['admin']['id_usuario']);

		header('Location: /adm/mercadolivre/ml-produtos.php?_ml='.$GET['_ml']);
		return;

	} 
	else {
        echo '<div class="text-center alert alert-danger">Atenção você deve informar o id do produto no mercado livre!</div>';
    }
}

/**
 * Finaliza e delete o anuncio no mercado livre
 */
if( isset($GET['status_ml']) && $GET['status_ml'] == 'closed' )
{
    $item = array("status" => "closed");
    $response = $meli->put('items/' . $GET['produtos_ml_id'], $item, $params);
    if ($response['body']->error == '') {
		header('Location: /adm/mercadolivre/ml-produtos.php?_ml='.$GET['_ml'].'&status_ml=deleted&produtos_ml_id=' . $GET['produtos_ml_id']);
		return;
    } else {
        $error = urlencode($response['body']->error);
        $cause = urldecode($response['body']->cause[0]->message);
        echo '<div class="alert alert-danger">'.$cause.'</div>';
    }
}
if( isset($GET['status_ml']) && $GET['status_ml'] == 'deleted' )
{
    $item = array("deleted" => "true");
    $response = $meli->put('items/' . $GET['produtos_ml_id'], $item, $params);
    if ($response['body']->error == '') {

		$MercadoLivreProdutos = MercadoLivreProdutos::find([ 'conditions' => ['produtos_ml_id=?', $response['body']->id]]);
		$MercadoLivreProdutos->delete();
		
		Logs::create_logs('Deletou produto mercadolivre: ' . $response['body']->id, $_SESSION['admin']['id_usuario']);

		header('Location: /adm/mercadolivre/ml-produtos.php?_ml='.$GET['_ml']);
		return;

    } else {
        $error = urlencode($response['body']->error);
        $cause = urlcode($response['body']->cause[0]->message);
        echo '<div class="alert alert-danger">'.$cause.'</div>';
    }
}

/**
 * Pausa o anúncio no mercado livre
 */
if( isset($GET['status_ml']) && $GET['status_ml'] == 'paused' )
{
    $item = array("status" => "paused");
	$response = $meli->put('items/' . $GET['produtos_ml_id'], $item, $params);
	
    if ($response['body']->error == ''){

		$MercadoLivreProdutos = MercadoLivreProdutos::find([ 'conditions' => ['produtos_ml_id=?', $response['body']->id]]);
		$MercadoLivreProdutos->produtos_ml_status = $response['body']->status;
		$MercadoLivreProdutos->save();
		
		Logs::create_logs('Alterou produto mercadolivre: ' . $response['body']->id, $_SESSION['admin']['id_usuario']);

        if( $response['body']->id != '' ) {
            header('Location: /adm/mercadolivre/ml-produtos.php?_ml='.$GET['_ml']);
            return;
        }
	} 
	else {
        $error = urlencode($response['body']->error);
        $cause = urlencode($response['body']->cause[0]->message);
        echo '<div class="alert alert-danger">'.$cause.'</div>';
    }
}

/**
 * Ativar o anúncio no mercado livre
 */
if( isset($GET['status_ml']) && $GET['status_ml'] == 'active' )
{
	$item = array("status" => "active");
	
	$response = $meli->put('items/' . $GET['produtos_ml_id'], $item, $params);

	// $response = $meli->get('items/' . $GET['produtos_ml_id']);
	// $MercadoLivreProdutos = MercadoLivreProdutos::find([ 'conditions' => ['produtos_ml_id=?', $response['body']->id]]);
	// header('Location: /adm/mercadolivre/ml-categorias.php?_ml=' . $GET['produtos_ml_id'] . '&codigo_id=' . $MercadoLivreProdutos->produtos_codigo_id);
	// return;

	// // Percorrer todos os produtos para alterar estoque/;
	// printf('<pre>%s</pre>', print_r($response, true));
	// return;

    if ($response['body']->error == '') {
       	$MercadoLivreProdutos = MercadoLivreProdutos::find([ 'conditions' => ['produtos_ml_id=?', $response['body']->id]]);
		$MercadoLivreProdutos->produtos_ml_status = $response['body']->status;
		$MercadoLivreProdutos->save();
		
		Logs::create_logs('Alterou produtos_ml_status '.$response['body']->status.' mercadolivre: ' . $response['body']->id, $_SESSION['admin']['id_usuario']);

        if( $response['body']->id != '' ) {
            header('Location: /adm/mercadolivre/ml-produtos.php?_ml=' . $GET['_ml']);
            return;
        }
    } else {
        $error = urlencode($response['body']->error);
        $cause = urlencode($response['body']->cause[0]->message);
        echo '<div class="alert alert-danger">' . $cause . '</div>';
    }
}

/**
 * Altualiza as informações via mercado livre
 */
if( isset( $POST['produtos'] ) && count($POST['produtos']) > 0  ) {
    
	foreach ($POST['produtos'] as $k => $v ) 
	{
		$ml = $meli->get('items/' . $v['id'], $params);
		if( $ml['body']->id ) {
			$MercadoLivreProdutos = MercadoLivreProdutos::find([ 'conditions' => ['produtos_ml_id=?', $v['id']]]);
			$MercadoLivreProdutos->produtos_ml_status = $ml['body']->status;
			$MercadoLivreProdutos->produtos_ml_title = $ml['body']->title;
			$MercadoLivreProdutos->produtos_ml_preco = $ml['body']->price;
			$MercadoLivreProdutos->produtos_ml_estoque = $ml['body']->initial_quantity;
			$MercadoLivreProdutos->save();
			
			Logs::create_logs('Alterou produto mercadolivre: ' . $response['body']->id, $_SESSION['admin']['id_usuario']);
		}		
	}
	header('Location: /adm/mercadolivre/ml-produtos.php?_ml='.$GET['_ml']);
	return;
}

/**
 * Deleta dados em massa.
 */
if( !empty($POST) && $POST['campos'] != '' )
{
	if( isset( $POST['campos'] ) && count($POST['campos']) > 0 )
	{
		$values = '';
		foreach( $POST as $k => $v )
		{
			foreach( $v as $key => $value )
			{
				MercadoLivreProdutos::delete_all(array('conditions' => array('produtos_codigo_id = ?', (int)$value['value'] )));
				Logs::create_logs('DELETOU PRODUTOS ML '.$values, $_SESSION['admin']['id_usuario']);
			}
		}
		
		header("Location: /adm/mercadolivre/ml-produtos.php");
		return;
	}
}

/**
 * Excluir apenas produto selecionado
 */
if( isset( $GET['acao'], $GET['id'] ) && ( 'excluir' == $GET['acao'] && $GET['id'] > '0' ) ) {	
	MercadoLivreProdutos::delete_all(array('conditions' => array('id = ?', (int)$GET['id'] )));
	Logs::create_logs('DELETOU PRODUTOS ML '.$values, $_SESSION['admin']['id_usuario']);

	header("Location: /adm/mercadolivre/ml-produtos.php");
	return;
}

/**
 * Ativar/Desativar produtos
 */
if( isset( $GET['acao'], $GET['status'] ) && ( 'status' == $GET['acao'] && $GET['status'] != '' ) ) {
	
	$STATUS = $GET['status'] == 0 ? 1 : 0;
	
	$MercadoLivreProdutos = MercadoLivreProdutos::find(array('conditions' => array('codigo_id = ?', (int)$GET['codigo_id'] )));
	$MercadoLivreProdutos->status = $STATUS;
	$MercadoLivreProdutos->save();
	
	$logs = "ATIVOU/DESTIVOU PRODUTOS DO MARKETING ML: {$GET['codigo_id']}";
	Logs::create_logs($logs, $_SESSION['admin']['id_usuario']);

	header("Location: /adm/mercadolivre/ml-produtos.php");
	return;
}

$TOTAL_PROD_ML_ACTIVE = $MercadoLivre->ml_active();
$TOTAL_PROD_ML_PAUSED = $MercadoLivre->ml_paused();
$TOTAL_PROD_ML = $MercadoLivre->ml_closed();

$GET_STATUS = isset( $POST['status'] ) && $POST['status'] != '' ? $POST['status'] : ( isset( $GET['status'] ) && $GET['status'] != '' ? $GET['status'] : '' );
$GET_PESQUISAR = isset( $GET['pesquisar'] ) && $GET['pesquisar'] != '' ? $GET['pesquisar'] : ( isset( $POST['pesquisar'] ) && $POST['pesquisar'] != '' ? $POST['pesquisar'] : '' );

$pesquisar = isset($POST['pesquisar']) && $POST['pesquisar'] != '' ? filter_input(INPUT_POST, 'pesquisar', FILTER_SANITIZE_STRING|FILTER_SANITIZE_MAGIC_QUOTES) : '';
$pesquisar .= isset($GET['pesquisar']) && $GET['pesquisar'] != '' ? filter_input(INPUT_GET, 'pesquisar', FILTER_SANITIZE_STRING|FILTER_SANITIZE_MAGIC_QUOTES) : '';

$id_marca = isset($POST['id_marca']) && $POST['id_marca'] != '' ? filter_input(INPUT_POST, 'id_marca', FILTER_SANITIZE_NUMBER_INT ) : '';
$id_marca .= isset($GET['id_marca']) && $GET['id_marca'] != '' ? filter_input(INPUT_GET, 'id_marca', FILTER_SANITIZE_NUMBER_INT) : '';

$statusML = '';
$statusML .= isset($POST['ml_status']) && $POST['ml_status'] != '' ? filter_input(INPUT_POST, 'ml_status', FILTER_SANITIZE_STRING ) : '';
$statusML .= isset($GET['ml_status']) && $GET['ml_status'] != '' ? filter_input(INPUT_GET, 'ml_status', FILTER_SANITIZE_STRING) : '';

$whereCodPalavarasChave = '';
$whereCodPalavarasChave .= $pesquisar != '' ? '' 
			. substr_replace(" AND (P.nome_produto like '%" . implode("%' AND P.nome_produto like '%", explode(' ', $pesquisar)) . "%' OR "
			. "P.codigo_referencia like '%" . implode("%' AND P.codigo_referencia like '%", explode(' ', $pesquisar)) . "%' OR "
			. "P.codigo_produto like '%" . implode("%' AND P.codigo_produto like '%", explode(' ', $pesquisar)) . "%' OR ", ")", -4, 3) : ' ';

$whereCodPalavarasChave .= $id_marca != '' ? queryInjection(" AND P.id_marca= %u ", $id_marca) : ' ';
$whereCodPalavarasChave .= $statusML != '' ? queryInjection(" AND P.produtos_ml_status='%s' ", $statusML) : ' ';

//				echo 
$buscaSql   = ''
	. 'select ' . PHP_EOL
		. 'P.estoque, ' . PHP_EOL
		. 'P.id, ' . PHP_EOL
		. 'P.codigo_id, ' . PHP_EOL
		. 'P.nome_produto, ' . PHP_EOL
		. 'P.produtos_ml_title, ' . PHP_EOL
		. 'P.preco_venda, ' . PHP_EOL
		. 'P.preco_promo, ' . PHP_EOL
		. 'P.produtos_ml_preco, ' . PHP_EOL
		. 'P.produtos_ml_estoque, ' . PHP_EOL
		. 'P.status, ' . PHP_EOL
//						. 'P.id_tamanho, ' . PHP_EOL
//						. 'P.id_cor, ' . PHP_EOL
		. 'P.id_marca, ' . PHP_EOL
		. 'P.nometamanho, ' . PHP_EOL
		. 'P.nomecor, ' . PHP_EOL
		. 'P.marcas, ' . PHP_EOL
		// . "P.cortipo, " . PHP_EOL
		// . "P.tamanhotipo, " . PHP_EOL
		. 'P.codigo_referencia, ' . PHP_EOL
		. 'P.codigo_produto, ' . PHP_EOL
		. 'P.produtos_ml_status, ' . PHP_EOL
		. 'P.produtos_ml_id, ' . PHP_EOL
		. 'P.produtos_codigo_id ' . PHP_EOL
		. 'FROM (' . PHP_EOL
			. 'select ' . PHP_EOL
				. 'p.estoque, ' . PHP_EOL
				. "pml.id, " . PHP_EOL
				. "p.codigo_id, " . PHP_EOL
				. "p.nome_produto, " . PHP_EOL
				. "pml.produtos_ml_title, " . PHP_EOL
				. "p.preco_venda, " . PHP_EOL
				. "p.preco_promo, " . PHP_EOL
				. "pml.produtos_ml_preco, " . PHP_EOL
				. "pml.produtos_ml_estoque, " . PHP_EOL
				. "p.status, " . PHP_EOL
//								. "t.id as id_tamanho, " . PHP_EOL
//								. "c.id as id_cor, " . PHP_EOL
				. "p.id_marca, " . PHP_EOL
				. "t.nometamanho, " . PHP_EOL
				. "c.nomecor, " . PHP_EOL
				. "m.marcas, " . PHP_EOL
				// . "COR.tipo AS cortipo, " . PHP_EOL
				// . "TAM.tipo AS tamanhotipo, " . PHP_EOL
				. 'p.codigo_referencia, ' . PHP_EOL
				. 'p.codigo_produto, ' . PHP_EOL
				. 'pml.produtos_ml_status, ' . PHP_EOL
				. 'pml.produtos_ml_id, ' . PHP_EOL
				. 'pml.produtos_codigo_id ' . PHP_EOL
			. 'FROM mercadolivre_produtos pml ' . PHP_EOL
			. 'INNER JOIN produtos p on p.codigo_id = pml.produtos_codigo_id ' . PHP_EOL
			. 'INNER JOIN tamanhos t on t.id = p.id_tamanho ' . PHP_EOL
			. 'INNER JOIN cores c on c.id = p.id_cor ' . PHP_EOL
			. 'INNER JOIN marcas m on m.id = p.id_marca ' . PHP_EOL
			
			. 'WHERE '
			. 'p.excluir = 0 '
			. ( isset($GET['_ml']) && $GET['_ml'] != '' ? ' AND ( pml.produtos_ml_id IS NOT NULL AND (pml.produtos_ml_status!="closed" OR pml.produtos_ml_status IS NULL) ) ' : 'AND pml.produtos_ml_id IS NULL ')
			. 'GROUP BY p.codigo_id, pml.produtos_ml_id ) as P WHERE 0 = 0 ' . PHP_EOL
	. $whereCodPalavarasChave . PHP_EOL
	. ' GROUP BY P.produtos_ml_id, P.codigo_id '
	. ' ORDER BY P.produtos_ml_id DESC ';

$i 			= 0;
$maximo 	= 50;	
$pag 		= isset( $GET['pag'] ) &&  $GET['pag'] != '' ? $GET['pag'] : 1; 
$inicio 	= ( $pag * $maximo ) - $maximo;

$result_num = MercadoLivre::find_by_sql( $buscaSql );
$total 		= ceil( count($result_num) / $maximo );

$buscaSql 	.= " limit {$inicio}, {$maximo}";
$result_sql = MercadoLivre::find_by_sql( $buscaSql );

?>

	<div id="div-edicao">
        <h2 class="neo-sans-medium">
            MARKETINGPLACE MERCADO LIVRE 
            <small><?php if( isset($GET['_ml']) && $GET['_ml'] != '' ) { ?>produtos enviados<?php } else { ?>produtos ñ enviados<?php }?></small>
        </h2>
        <p>Usuário Mercado Livre <?php echo $VerificarApp['body']->nickname?></p>
        <span>TOTAL PRODUTOS: <?php echo $TOTAL_PROD_ML?></span> |
        <span>PRODUTOS ATIVOS: <?php echo $TOTAL_PROD_ML_ACTIVE?></span> |
        <span>PRODUTOS PAUSADOS: <?php echo $TOTAL_PROD_ML_PAUSED?></span>
		<table width="100%" border="0" cellpadding="8" cellspacing="0" class="lista-comuns">
			<tbody id="ml-produtos">
				<tr class="ocultar">
					<td colspan="8">
                        <form action="/adm/mercadolivre/ml-produtos.php?_ml=<?php echo $GET['_ml']?>" method="post" class="formulario-produtos">
							<input name="pesquisar" type="text" class="" size="50"/>
							<select name="id_marca" class="ml15">
								<option value="">Selecione uma marca</option>
								<?php
								$result_marcas = $MercadoLivre->ml_marcas_rows();
								foreach( $result_marcas as $marcas_rs) {
									extract($marcas_rs->to_array());
									if( $LETRA != $letra ) { ?> 
									<optgroup label="<?php echo $letra?>"> <?php $LETRA = $letra; } ?>
										<option value="<?php echo $id?>"<?php echo ((INT)$GET['id_marca'] == $id) || ((INT)$POST['id_marca'] == $id) ? ' selected':''?>>
											<?php echo $marcas?>
										</option>
									<?php if( $LETRA != $letra ) { ?>
									</optgroup>
									<?php } 
								}?>
							</select>
							<select name="ml_status" class="ml15 mr5" style="width: 145px">
								<option value="">Todos</option>
								<?php
								$result_produtos_status = $MercadoLivre->ml_produtos_status_rows();
								foreach( $result_produtos_status as $produtos_rs) {
									extract($produtos_rs->to_array()); ?>
										<option value="<?php echo $status?>"<?php echo ($GET['ml_status'] == $status) || ($POST['ml_status'] == $status) ? ' selected':''?>>
											<?php echo ucwords(StatusML($status))?>
										</option>
                                <?php } ?>
							</select>
							<button type="submit" class="btn btn-primary">
								<i class="fa fa-search"></i>
							</button>
                            
                            <?php if( isset($GET['_ml']) && $GET['_ml'] != '' ) { ?>
                            <button type="button" class="btn btn-primary" data-id="btn-atualizacao">
                                atualizar informações 
                            </button>
                            <?php } /* else { ?>
                                <button type="button" class="btn btn-warning" data-id="btn-listagem">
                                    buscar listagem no mercado livre
                                </button>
                            <?php } */ ?>
							<!--
							<a href="/adm/mercadolivre/ml-produtos.php?acao=ObterListagem" class="btn btn-warning">
								buscar listagem no mercado livre
							</a>
							<button type="button" <?php echo _P(  $PgAt, $_SESSION['admin']['id_usuario'], 'excluir' )?> class="btn btn-danger" data-id="btn-excluir-varios" data-href="ml-produtos.php?codigo_id=<?php echo $GET['codigo_id']?>&_ml=<?php echo $GET['_ml']?>">
                                excluir seleção
                            </button>
							-->
						</form>
					</td>
				</tr>
                
				<tr class="plano-fundo-adm-003 ocultar">
					<td bgcolor="#ffffff" align="center" nowrap="nowrap" width="1%">
						<input type="checkbox" name="seleciona-all" class="seleciona-all" id="label" value=""/>
						<label for="label" class="input-checkbox"></label>
					</td>
					<td>Cód.</td>
					<td>Produto</td>
					<td align="center">Estoque</td>
					<td align="center" id="recarregar-categorias">Status</td>
					<td align="center" id="recarregar-categorias">Pr.Venda</td>
					<td align="center">Ações</td>
				</tr>
				<?php foreach( $result_sql as $rs ) { ?>
				
				<tr id="<?php echo $rs->codigo_id?>" class="in-hover lista-zebrada">
					<td nowrap="nowrap" width="1%">
						<input type="checkbox" name="produtos[<?php echo $rs->id?>][id]" data-name="produtos" id="label<?php echo $rs->id?>" value="<?php echo ( $rs->produtos_ml_id != '' ? $rs->produtos_ml_id : $rs->id )?>"/>
						<label for="label<?php echo $rs->id?>" class="input-checkbox"></label>
					</td>
					
					<td align="center" nowrap="nowrap" width="1%">
						<?php echo ( $rs->produtos_ml_id != '' ? ''
                                . '<a href="/adm/mercadolivre/ml-link.php?ml_id=' . $rs->produtos_ml_id . '" target="_blank">'
                                . '#' . substr($rs->produtos_ml_id, 3)
                                . '</a>' : $rs->codigo_produto )?>
					</td>
					<td>
						<?php echo (($rs->produtos_ml_title && ($rs->produtos_ml_title != $rs->nome_produto)) ? $rs->produtos_ml_title : $rs->nome_produto);?> 
						<?php //echo $rs->estoque > 0 ? '<span class="btn btn-danger btn-xs"> estoque baixo! </span>':'';?>
					</td>
                    
                    <td align="center" nowrap="nowrap" width="1%">
                        <?php if( !isset($GET['_ml']) || $GET['_ml'] == '' ) { ?>
                        Produto ñ enviado
                        <?php } else { 
                            echo $rs->produtos_ml_estoque;
                        } ?>
                    </td>

                    <td align="center" nowrap="nowrap" width="1%">
                        <?php if( !isset($GET['_ml']) || $GET['_ml'] == '' ) { ?>
                        Produto ñ enviado
                        <?php } else { echo StatusML( $rs->produtos_ml_status ); } ?>
                    </td>
                    
                    <td align="center" nowrap="nowrap" width="1%">
                        <?php if( !isset($GET['_ml']) || $GET['_ml'] == '' ) { ?>
                        Produto ñ enviado
                        <?php } else { ?>
                        R$: <?php echo number_format((($rs->preco_promo != $rs->produtos_ml_preco) && $rs->produtos_ml_preco > 0
                                                       ? $rs->produtos_ml_preco : $rs->preco_promo), 2, ',', '.')?>
                        <?php } ?>
                    </td>
                    
					<td align="center" nowrap="nowrap" width="1%">
						<?php if( !isset($GET['_ml']) || $GET['_ml'] == '' ) { ?>
							<?php if( $rs->produtos_codigo_id > '1111111111'  ) { ?>
								<!--
								<a class="btn btn-warning btn-sm" href="/adm/mercadolivre/ml-produtos.php?produto_id=<?php echo $rs->codigo_id?>" data-id="btn-vincular">
									vincular via ml
								</a>
								-->
								<a href="/adm/mercadolivre/ml-categorias.php?codigo_id=<?php echo $rs->codigo_id;?>&_ml=<?php echo $GET['_ml']?>" class="ml5 btn btn-primary btn-sm" data-id="btn-envio-alterar">
									enviar p/ mercado livre
								</a>
								<a href="/adm/mercadolivre/ml-produtos.php?acao=excluir&id=<?php echo $rs->id;?>&_ml=<?php echo $GET['_ml']?>" class="ml5 btn btn-danger btn-sm data-acao-excluir">
									remover desta lista
								</a>
							<?php } else { ?>
								<a class="btn btn-success btn-sm" href="/adm/produtos/produtos.php?produto_vincular_id=<?php echo $rs->id?>&pesquisar=<?php echo (($rs->produtos_ml_title && ($rs->produtos_ml_title != $rs->nome_produto)) ? $rs->produtos_ml_title : $rs->nome_produto)?>" data-id="btn-vincularsite">
									vincular ao site
								</a>
							<?php } ?>
							
						<?php } else { ?>                        
							<a href="/adm/mercadolivre/ml-produtos.php?_ml=<?php echo $GET['_ml']?>&status_ml=<?php echo $rs->produtos_ml_status=='paused' ? 'active':'paused'?>&produtos_ml_id=<?php echo $rs->produtos_ml_id?>" class="ml5 btn btn-primary btn-sm" data-id="btn-envio-alterar">
								<?php echo $rs->produtos_ml_status != 'paused' ? 'pausar anúncio' : 'ativar anúncio'?>
							</a>

							<a href="/adm/mercadolivre/ml-produtos.php?acao=excluir&produtos_ml_id=<?php echo $rs->produtos_ml_id;?>&status_ml=closed&_ml=<?php echo $GET['_ml']?>" class="ml5 btn btn-danger btn-sm data-acao-excluir">
								finalizar anúncio
							</a>
                        <?php } ?>
					</td>
				</tr>
				<?php ++$i; } ?>
				<tr class="ocultos ocultar">
					<td colspan="8">
						<div class="paginacao clearfix">
						<?php
						if( $total > 0 )
						{
							if( $pag != 1 )
							{ 
								echo "<a href=\"/adm/mercadolivre/ml-produtos.php?pesquisar={$pesquisar}&_ml={$GET['_ml']}&status={$status}&id_marca={$id_marca}&pag=1\">Primeira página</a>";
							}
							
							for( $i = $pag - 10, $limiteDeLinks = $i + 20; $i <= $limiteDeLinks; ++$i )
							{
								if($i < 1)
								{
									$i = 1;
									$limiteDeLinks = 19;
								}
							
								if($limiteDeLinks > $total)
								{
									$limiteDeLinks = $total; 
									$i = $limiteDeLinks - 20;
								}

								if( $i < 1 )
								{
									$i = 1;
									$limiteDeLinks = $total;
								}
								
								if($i == $pag)
								{
									echo "<span class=\"at plano-fundo-adm-001\">{$i}</span>";
								}
								else
								{							
									echo "<a href=\"/adm/mercadolivre/ml-produtos.php?pesquisar={$pesquisar}&_ml={$GET['_ml']}&status={$status}&id_marca={$id_marca}&pag={$i}\">{$i}</a>";
								}
							} 

							if( $pag != $total )
							{  
								if( $pag == $i && $total > 0 )
								{ 
									echo "<span class=\"lipg\">Última página</span>";
								}
								else
								{ 
									echo "<a href=\"/adm/mercadolivre/ml-produtos.php?pesquisar={$pesquisar}&_ml={$GET['_ml']}&status={$status}&id_marca={$id_marca}&pag={$total}\">Última página</a>"; 
								}
							}
						}
						?>
					</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<?php ob_start(); ?>
	<script>
		$(document).on("click", "a", function(e){
			var href = this.href || e.target.href;		
			if( href.search("excluir") > "0")
				if( ! confirm("Deseja realmente excluir!") ) return false;
		
		});
        
        $(document).on("change", "select[name]", function(){
            var $this = $(this),
                $DataName = $this.attr("data-name"),
                $DataText = $this.find("option:selected").text(),
                $DataHref = $this.attr("data-href");
            
            $("tr[me-use=true]").find("input[data-name="+ $DataName +"]").next().remove();
            if( ! $this.val() ){
                $("tr[me-use=true]").find("input[data-name="+ $DataName +"]").val( "" );
//                return false;
            }

            $("tr[me-use=true]").find("input[data-name="+ $DataName +"]").val( $this.val() ).after([
                $("<span/>",{ html: $DataText.trim() })
            ]);
            
            if( $DataName === 'categoria-id' ){ 
                $.ajax({
                    url: $DataHref||window.location.href,
                    type: "get",
                    data: { categoria_id: $this.val() },
                    success: function(str){
                        var list = $("<span/>", { html: str });
                        $("#recarregar-categorias").html( list.find("#recarregar-categorias").html() );
                        
                        if( JanelaModal.is(":visible") )
                            JanelaModal.find("#conteudos-recarregar").html( list.find("#conteudos-recarregar").html() );
                        
                    }
                });
            }            
        });
        
        /**-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*
         SELECIONA TODOS INPUTS
         **-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**/
        $(document).on("click", "input[name=seleciona-all]", function(e){
            var $this = $(this).parents(), 
               $all = $this.find("input[data-name='produtos']").serialize();

            if( ! $all.length )
                $this.find("input[data-name='produtos']").prop({ "checked": true }).parent().parent().attr({"me-use": true });
            else
                $this.find("input[data-name='produtos']").prop({ "checked": false }).parent().parent().attr({"me-use": false });
            
            $("[data-change]").trigger("change");
        });
        
        /**
         * ATUALIZA OS DADOS A PARTIR DO MERCADO LIVRE
         */
        $("#div-edicao").on("click", "button[data-id=btn-atualizacao]", function(e){
            e.preventDefault();
            var $this = $(this).parents(),
                $all = $this.find("input[data-name='produtos']").serialize();
                
            if( ! $all.length )
                return confirm("Selecione ao menos um produto!");
            
            console.log( $all ); 
                
            $.ajax({
                url: window.location.href,
                data: $all,
                type: "post",
                success: function(str){
                    var list = $("<div/>", { html: str });
                    $("#div-edicao").html(list.find("#div-edicao").html());
                },
                complete: function(){
                    JanelaModal.dialog({
                        "title": "Marketingplace - Produtos",
                        "width": "300",
                        "height": "175",
                        "autoOpen": true
                    }).html([
                        $("<span/>",{ 
                            html: "Dados atualizados...",
                            class: "text-center neo-sans-light"
                        })
                    ]).delay(1550).queue(function(d){
                        $(this).dialog("close");
                        d();
                    });
                },
                beforeSend: function(){
                    JanelaModal.dialog({
                        "title": "Marketingplace - Produtos",
                        "width": "300",
                        "height": "175",
                        "autoOpen": true
                    }).html([
                        $("<span/>",{ 
                            html: "Aguarde, estamos altualizando dados...",
                            class: "text-center neo-sans-light"
                        }).delay(5000).queue(function(a){
                            $(this).html("Estamos quase lá, altualizando dados...");
                            a();
                        }).delay(10000).queue(function(b){
                            $(this).html("Mais um momento, ainda estamos altualizando dados...");
                            b();
                        }).delay(15000).queue(function(c){
                            $(this).html("Só mais uns intantes, altualizando dados...");
                            c();
                        })
                    ]);
                },
                error: function(){
                }
            });
        });
        
        /**
         * Gera vinculação do produto cadastrado no mercado livre
         */
       $("#div-edicao").on("click", "[data-id=btn-vincular]", function(e){
			JanelaModal.dialog({
				"title": "Marketingplace - Vincular produtos",
				"width": "375",
				"height": "205",
				"autoOpen": true
			}).html([
				$("<p/>", { html: "Copie e cole o id do produto no mercado livre<br/>ex: #123456789", style: "margin-top:0px" }),
				$("<form/>", {
					id: "form-vincular",
					action: this.href||e.target.href,
					method: "post",
					class: "text-center no-action",
					html: [
						$("<input/>",{ name: "produto_ml_id", type: "text", placeholder:"Digite apenas números.", class: "mb5", css:{ width: "300", height: "45" } }),
						$("<button/>",{ html:"Vincular Produto", type: "submit", class: "mt5 btn btn-primary", css: { width: "300", height: "45" } }),
						$("<input/>",{ name:"acao", value: "VeincularProduto", type: "hidden" })
					]
				})
			]);
			e.preventDefault();
		}); 
		
		/**
         * BUSCA E CADASTRA OS DADOS A PARTIR DO MERCADO LIVRE
         */
        $("#div-edicao").on("click", "button[data-id=btn-listagem]", function(e){
            e.preventDefault();
            $.ajax({
                url: window.location.href,
                data: { acao: "ObterListagem" },
                success: function(str){
                    var list = $("<div/>", { html: str });
                    $("#div-edicao").html( list.find("#div-edicao").html() );
                },
                complete: function(){
                    JanelaModal.dialog({
                        "title": "Marketingplace - Produtos",
                        "width": "300",
                        "height": "175",
                        "autoOpen": true
                    }).html([
                        $("<span/>",{ 
                            html: "Dados atualizados...",
                            class: "text-center neo-sans-light"
                        })
                    ]).delay(1550).queue(function(d){
                        $(this).dialog("close");
                        d();
                    });
                },
                beforeSend: function(){
                    JanelaModal.dialog({
                        "title": "Marketingplace - Produtos",
                        "width": "300",
                        "height": "175",
                        "autoOpen": true
                    }).html([
                        $("<span/>",{ 
                            html: "Aguarde, buscando produtos...",
                            class: "text-center neo-sans-light"
                        }).delay(5000).queue(function(a){
                            $(this).html("Estamos quase lá, cadastrando os produtos no site...");
                            a();
                        }).delay(15000).queue(function(b){
                            $(this).html("Mais um momento, estamos altualizando os produtos...");
                            b();
                        }).delay(20000).queue(function(c){
                            $(this).html("Só mais uns intantes, altualizando dados...");
                            c();
                        })
                    ]);
                },
                error: function(a,b,c){
					console.log(a.responseText+"\n"+b+"\n"+c);
                }
            });
        });
        
        /**
         * Gera vinculação do produto cadastrado no mercado livre
         */
		$("#div-edicao").on("click", "[data-id=btn-vincular]", function(e){
			JanelaModal.dialog({
				"title": "Marketingplace - Vincular produtos",
				"width": "375",
				"height": "205",
				"autoOpen": true
			}).html([
				$("<p/>", { html: "Copie e cole o id do produto no mercado livre<br/>ex: #123456789", style: "margin-top:0px" }),
				$("<form/>", {
					id: "form-vincular",
					action: this.href||e.target.href,
					method: "post",
					class: "text-center",
					html: [
						$("<input/>",{ name: "produto_ml_id", type: "text", placeholder:"Digite apenas números.", class: "mb5", css:{ width: "300", height: "45" } }),
						$("<button/>",{ html:"Vincular Produto", type: "submit", class: "mt5 btn btn-primary", css: { width: "300", height: "45" } }),
						$("<input/>",{ name:"acao", value: "VeincularProduto", type: "hidden" })
					]
				})
			]);
			e.preventDefault();
		});
	</script>
	<?php
	$SCRIPT['script_manual'] .= ob_get_clean();
	
include '../rodape.php';