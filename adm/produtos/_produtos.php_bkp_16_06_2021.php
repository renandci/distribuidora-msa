<?php

include '../topo.php';	

// /**
//  * Adiciona o produto ao mercado livre em massa
//  */
// if( isset($POST['mlactive']) && $POST['mlactive'] == true ) {
	
// 	unset($POST['mlactive']);

// 	foreach( $POST as $k => $v ) {
// 		foreach( $v as $key => $value ) {
// 			MercadoLivreProdutos::action_cadastrar_editar([
// 				'MercadoLivreProdutos' => [
// 					0 => ['mercadolivre_id' => $CONFIG_MELI['id'], 'produtos_codigo_id' => $value['value']] 
// 				]
// 			], 'cadastrar', 'mercadolivre_id');
// 		}
// 	}

// 	$data = http_build_query($GET);
//     header(sprintf('Location: /adm/produtos/produtos.php?%s', $data));
//     return;
// }

/**
 * Ediçao de dados em massa.
 */
if( isset( $POST['edit'] ) && count($POST['edit']) > 0 ) :
    foreach( $POST as $k => $v ) :
        foreach( $v as $key => $value ) :
			Produtos::action_cadastrar_editar([
                    'Produtos' => [
                        $value['id'] => [ 'estoque' => $value['estoque'], 'preco_promo' => $value['preco_promo'] ] 
                    ]
			], 'alterar', 'nome_produto');
			
        endforeach;
    endforeach;
	
	$data = http_build_query($GET);
    header(sprintf('Location: /adm/produtos/produtos.php?%s', $data));
    return;
endif;

/**
 * Deleta dados em massa.
 */
if( isset( $POST['campos'] ) && count($POST['campos']) > 0 ) :
    foreach( $POST as $k => $v ) :
        foreach( $v as $key => $value ) :
            $Each = Produtos::all(['conditions' => ['codigo_id=?', $value['value']] ]);
            foreach ( $Each as $r ) :
                Produtos::action_cadastrar_editar([
                    'Produtos' => [
                        $r->id => [
                            'excluir' => '1'
                        ] 
                    ]
                ], 'excluir', 'nome_produto');
            endforeach;
        endforeach;
    endforeach;
    header("Location: /adm/produtos/produtos.php?codigo_id={$CODIGO_ID}");
    return;
endif;

/**
 * Excluir apenas produto selecionado
 */
if( isset( $GET['acao'], $GET['codigo_id'] ) && ( 'excluir' == $GET['acao'] && $GET['codigo_id'] > '0' ) ) :
    $Each = Produtos::all(['conditions' => ['codigo_id=?', $GET['codigo_id']] ]);
    foreach ( $Each as $r ) :
        Produtos::action_cadastrar_editar([
            'Produtos' => [
                $r->id => [
                    'excluir' => 1
                ] 
            ]
        ], 'excluir', 'nome_produto');
    endforeach;
    header("Location: /adm/produtos/produtos.php?codigo_id={$CODIGO_ID}");
    return;
endif;

/**
 * Ativar/Desativar produtos
 */
if( isset( $GET['acao'], $GET['status'] ) && ( 'status' == $GET['acao'] && $GET['status'] != '' ) ) :
	$STATUS = $GET['status'] > 0 ? '0' : '1';
    $Each = Produtos::all(['conditions' => ['codigo_id=?', $GET['codigo_id']] ]);
    foreach ( $Each as $r ) :
        Produtos::action_cadastrar_editar([
            'Produtos' => [
                $r->id => [
                    'status' => (STRING)$STATUS
                ] 
            ]
        ], 'alterar', 'id');
    endforeach;
    header("Location: /adm/produtos/produtos.php?codigo_id={$CODIGO_ID}");
    return;
endif;

if( !empty( $GET ) && $GET['acao'] === 'copiar' ) :
	/**
	 * Seleciona e copia os produtos
	 */
    $CODIGO_ID = time();
    
    /**
     * Gera um novo produto a partir de uma copia do mesmo adicionado cores|etc...
     */
    $Produtos = Produtos::find('first', ['conditions' => ['codigo_id=? and excluir=0', $GET['codigo_id']], 'limit' => 1 ]);
    $ArrayFields = $Produtos->to_array();
    foreach ( $ArrayFields as $fields => $values ) :
        if( ! in_array($fields, ['id', 'id_cor', 'id_tamanho', 'codigo_produto', 'codigo_id']) ) :
            $campos[$fields] = $Produtos->{$fields};
        else :
            $campos['codigo_id'] = $CODIGO_ID;
            $campos['codigo_produto'] = CodProduto($Produtos->nome_produto, $Produtos->id);
            $campos['id_cor'] = 0;
            $campos['id_tamanho'] = 0;
        endif;
    endforeach;
    
    Produtos::action_cadastrar_editar([ 'Produtos' => [ 0 => $campos ] ], 'cadastrar', 'id');
    header("Location: /adm/produtos/produtos.php?codigo_id={$CODIGO_ID}");
    return;
endif;

$conditions = null;
$TOTAL_CADASTROS_GERAL = Produtos::find_num_rows('select * from produtos where excluir=0 and loja_id=? group by codigo_id', [ $CONFIG['loja_id'] ]);
$TOTAL_CADASTROS_ATIVOS = Produtos::find_num_rows('select * from produtos where excluir=0 and status=0 and loja_id=? group by codigo_id', [ $CONFIG['loja_id'] ]);
$TOTAL_CADASTROS_DESATIVOS = Produtos::find_num_rows('select * from produtos where excluir=0 and status=1 and loja_id=? group by codigo_id', [ $CONFIG['loja_id'] ]);

$GET_STATUS = isset( $POST['status'] ) && $POST['status'] != '' ? $POST['status'] : ( isset( $GET['status'] ) && $GET['status'] != '' ? $GET['status'] : '' );
$GET_PESQUISAR = isset( $GET['pesquisar'] ) && $GET['pesquisar'] != '' ? $GET['pesquisar'] : ( isset( $POST['pesquisar'] ) && $POST['pesquisar'] != '' ? $POST['pesquisar'] : '' );
$PRODUTO_VINCULAR_ID = isset( $GET['produto_vincular_id'] ) && $GET['produto_vincular_id'] != '' ? $GET['produto_vincular_id'] : ( isset( $POST['produto_vincular_id'] ) && $POST['produto_vincular_id'] != '' ? $POST['produto_vincular_id'] : '' );

$HIDDEN_TAG_VINCULAPROD = isset($PRODUTO_VINCULAR_ID) && $PRODUTO_VINCULAR_ID > 0 ? ' tag-hidden ':'';

$pesquisar = isset($POST['pesquisar']) && $POST['pesquisar'] != '' ? filter_input(INPUT_POST, 'pesquisar', FILTER_SANITIZE_STRING) : '';
$pesquisar .= isset($GET['pesquisar']) && $GET['pesquisar'] != '' ? filter_input(INPUT_GET, 'pesquisar', FILTER_SANITIZE_STRING) : '';

$id_marca = isset($POST['id_marca']) && $POST['id_marca'] != '' ? filter_input(INPUT_POST, 'id_marca', FILTER_SANITIZE_NUMBER_INT ) : '';
$id_marca .= isset($GET['id_marca']) && $GET['id_marca'] != '' ? filter_input(INPUT_GET, 'id_marca', FILTER_SANITIZE_NUMBER_INT) : '';

$status = '';
$status .= isset($POST['status']) && $POST['status'] != '' ? filter_input(INPUT_POST, 'status', FILTER_SANITIZE_NUMBER_INT ) : '';
$status .= isset($GET['status']) && $GET['status'] != '' ? filter_input(INPUT_GET, 'status', FILTER_SANITIZE_NUMBER_INT) : '';

$whereCodPalavarasChave = '';
$whereCodPalavarasChave .= $pesquisar != "" ? "AND (P.nome_produto like '%{$pesquisar}%' OR (P.codigo_referencia like '{$pesquisar}' OR (P.codigo_produto like '{$pesquisar}'))) " :'';
// $whereCodPalavarasChave .= $pesquisar != '' ? '' 
			// . substr_replace(" AND (P.nome_produto like '%" . implode("%' AND P.nome_produto like '%", explode(' ', $pesquisar)) . "%' OR "
			// . "P.codigo_referencia like '%" . implode("%' AND P.codigo_referencia like '%", explode(' ', $pesquisar)) . "%' OR "
			// . "P.codigo_produto like '%" . implode("%' AND P.codigo_produto like '%", explode(' ', $pesquisar)) . "%' OR ", ")", -4, 3) : ' ';

$whereCodPalavarasChave .= $id_marca != '' ? sprintf("AND P.id_marca= %u ", $id_marca) : ' ';
$whereCodPalavarasChave .= $status != '' ? sprintf("AND P.status = %u ", $status) : ' ';

$sql   = ''
	. 'SELECT ' . PHP_EOL
		. 'P.id, ' . PHP_EOL
		. 'P.codigo_id, ' . PHP_EOL
		. 'P.nome_produto, ' . PHP_EOL
		. 'P.status, ' . PHP_EOL
		. 'P.id_tamanho, ' . PHP_EOL
		. 'P.id_cor, ' . PHP_EOL
		. 'P.id_marca, ' . PHP_EOL
		. 'P.codigo_referencia, ' . PHP_EOL
		. 'P.codigo_produto, '
		. 'P.excluir, ' . PHP_EOL
		. 'P.loja_id, ' . PHP_EOL
		. 'P.IF_ML, ' . PHP_EOL
		. 'P.estoque ' . PHP_EOL
		. 'FROM (' . PHP_EOL
			. 'SELECT ' . PHP_EOL
				. 'p.id, ' . PHP_EOL
				. 'p.codigo_id, ' . PHP_EOL
				. 'p.nome_produto, ' . PHP_EOL
				. 'p.status, ' . PHP_EOL
				. 't.id as id_tamanho, ' . PHP_EOL
				. 'c.id as id_cor, ' . PHP_EOL
				. 'p.id_marca, ' . PHP_EOL
				. 'p.codigo_referencia, ' . PHP_EOL
				. 'p.codigo_produto, '
				. 'p.excluir, ' . PHP_EOL
				. 'p.loja_id, ' . PHP_EOL
				. '( select count(*) from mercadolivre_produtos ml where ml.produtos_codigo_id = p.codigo_id ) as IF_ML, ' . PHP_EOL
				. '( select count(produtos.id) from produtos where produtos.codigo_id = p.codigo_id and (produtos.estoque < 0 or produtos.estoque <= 1) and produtos.excluir=0 ) as estoque ' . PHP_EOL
			. 'FROM produtos p ' . PHP_EOL
			. 'INNER JOIN tamanhos t on t.id = p.id_tamanho ' . PHP_EOL
			. 'INNER JOIN cores c on c.id = p.id_cor ' . PHP_EOL
		. sprintf(') as P WHERE P.excluir = 0 and P.loja_id=%u ', $CONFIG['loja_id']) . PHP_EOL
	. $whereCodPalavarasChave . PHP_EOL
	. 'GROUP BY P.codigo_id '
	. 'ORDER BY P.nome_produto ASC ';

$i = 0;
$maximo = 50;	
$pag = isset( $GET['pag'] ) &&  $GET['pag'] != '' ? $GET['pag'] : 1; 
$inicio = (( $pag * $maximo ) - $maximo);

$total = (ceil(Produtos::connection()->query($sql)->rowCount() / $maximo));

// echo 
$sql .= sprintf("LIMIT %u, %u", $inicio, $maximo);

$result = Produtos::find_by_sql($sql);


$LETRA = '';
$Marcas = Marcas::all([
	'select' => 'id, marcas, UPPER(SUBSTRING(marcas, 1, 1)) as letra', 
	'conditions' => [ 'excluir = 0 and loja_id=?', $CONFIG['loja_id'], ], 
	'group' => 'marcas', 
	'order' => 'marcas asc' 
]);
?>
<style>
	body{ background-color: #f1f1f1 }
	.cx { float: left; font-size: 10px; padding: 0px 2px; margin: 0 1px; border-style: solid; border-width: 1px; border-color: #999; }
	.cx-danger { border-style: solid; border-width: 1px; border-color: #d29494; background-color: #eadada; color: #c77676 }
	.cx > input { border-radius: 0; -moz-border-radius: 0; -webkit-border-radius: 0; width: 55px; padding: 0 5px; height: 18px; margin-right: 5px; display: inline-block; border-style: solid; border-width: 1px; border-color: #999; margin: 2px; background-color: #fff; }
	.cx > input:nth-child(2) { width: 35px }
</style>
<div id="div-edicao" class="panel panel-default">
	<div class="panel-heading panel-store text-uppercase">PRODUTOS</div>
	<div class="panel-body">
		<div class="mb15 clearfix">
			<button class="pull-right btn btn-danger<?php echo $HIDDEN_TAG_VINCULAPROD?>" type="button" data-id="btn-excluir-varios" data-href="/adm/produtos/produtos.php?codigo_id=<?php echo $GET['codigo_id']?>" <?php echo _P($PgAt, $_SESSION['admin']['id_usuario'], 'excluir')?>>
				<i class="fa fa-trash"></i> excluir seleção
			</button>
			<a href="/adm/produtos/produtos-cadastrar.php?acao=CriarNovoProduto" class="pull-right mr5 btn btn-primary<?php echo $HIDDEN_TAG_VINCULAPROD?>" <?php echo _P($PgAt, $_SESSION['admin']['id_usuario'], 'incluir')?>>
				<i class="fa fa-edit"></i> cadastrar
			</a> 
		</div>
		<table width="100%" border="0" cellpadding="8" cellspacing="0" class="table">
			<tbody>
				<tr class="ocultar">
					<td colspan="5">
						<form action="/adm/produtos/produtos.php" method="get" class="formulario-produtos">
							<div class="clearfix mb15">
								<input type="radio" id="ativo" name="status" value="0" <?php echo isset($GET_STATUS) && $GET_STATUS == '0' ? 'checked' : ''?>/>
								<label for="ativo" class="input-radio"></label>
								Ativos 
								<input type="radio" id="desativar" name="status" value="1" <?php echo isset( $GET_STATUS ) && $GET_STATUS == '1' ? 'checked' : ''?>/>
								<label for="desativar" class="input-radio"></label>
								Desativados	| 
								<span class="cor-001">Total:</span> <span class="ft18px"><?php echo $TOTAL_CADASTROS_GERAL?></span> | 
								<span class="cor-001">Total Ativos:</span> <span class="ft18px"><?php echo $TOTAL_CADASTROS_ATIVOS?></span> | 
								<span class="cor-001">Total Desativados:</span> <span class="ft18px"><?php echo $TOTAL_CADASTROS_DESATIVOS?></span> 
							</div>
							<input name="pesquisar" type="text" class="form-control pull-left mr15" style="max-width: 450px"/>
							<input type="hidden" name="produto_vincular_id" value="<?php echo $GET['produto_vincular_id']?>" />
							<select name="id_marca">
								<option value="">Selecione uma marca</option>
								<?php foreach( $Marcas as $m ) {
									if( $LETRA != $m->letra ) { ?> 
									<optgroup label="<?php echo $m->letra?>"> <?php $LETRA = $m->letra; } ?>
										<option value="<?php echo $m->id?>"<?php echo ((INT)$GET['id_marca'] == $m->id) || ((INT)$POST['id_marca'] == $m->id) ? ' selected':''?>>
											<?php echo $m->marcas?>
										</option>
									<?php if( $LETRA != $m->letra ) { ?>
									</optgroup>
									<?php } ?>
								<?php } ?>
							</select>
							<button type="submit" class="btn"><i class="fa fa-search"></i> pesquisar</button>
							<button type="button" class="btn btn-info pull-right" id="save_massa"><i class="fa fa-save"></i> salvar em massa</button>
							<!-- <button type="button" class="btn btn-info pull-right mr5" id="ml_massa"><i class="fa fa-save"></i> enviar mercado livre</button> -->
						</form>
					</td>
				</tr>
				<tr class="plano-fundo-adm-003 ocultar bold">
					<td bgcolor="#ffffff" align="center" nowrap="nowrap" width="1%">
						<input type="checkbox" name="selecionados-exclusao-all" class="selecionados-exclusao-all" id="label" value=""/>
						<label for="label" class="input-checkbox"></label>
					</td>
					<!--<td>Cód.</td>-->
					<td>Produto</td>
					<td align="center">Ações</td>
				</tr>
				<?php foreach( $result as $rs ) { ?>
				<tr id="<?php echo $rs->codigo_id?>" class="in-hover lista-zebrada">
					<td nowrap="nowrap" width="1%">
						<input type="checkbox" name="selecionados-exclusao" id="label<?php echo $rs->codigo_id?>" value="<?php echo $rs->codigo_id?>"/>
						<label for="label<?php echo $rs->codigo_id?>" class="input-checkbox"></label>
					</td>
					<!--
					<td align="center" nowrap="nowrap" width="1%">
						<?php echo $rs->new_cod?>
					</td>
					-->
					<td>
						<?php echo $rs->nome_produto;?> 
						
						<div class="clearfix">
						<?php
						$Estoques = $rs->produtos_all;
						if( count($Estoques) > 0 ) :
							foreach($Estoques as $rwes) :
								echo sprintf('<span class="cx%s" style="min-width: 24%%; text-align: right; margin-bottom: 2px">', ($rwes->estoque <= 1 ? ' cx-danger':null));
								echo ($rwes->nomecor);
								echo ($rwes->nomecor != '' && $rwes->nometamanho != '' ? ' | ' : null);
								echo ($rwes->nometamanho);
								echo ($rwes->nomecor != '' || $rwes->nometamanho != '' ? ' ' : null);
								echo sprintf('<input type="tel" id="edit[%s][id]" class="hidden" value="%s" data-edit="true"/>', $rwes->id, $rwes->id);
								echo sprintf('<input type="tel" id="edit[%s][estoque]" class="text-center" autocomplete="off" value="%s" data-edit="true"/>', $rwes->id, $rwes->estoque);
								echo sprintf('<input type="tel" id="edit[%s][preco_promo]" class="text-right" data-price="true" autocomplete="off" value="%s" data-edit="true"/>', $rwes->id, number_format($rwes->preco_promo, 2, ',', '.'));
								echo '</span>';
							endforeach;
						endif;
						?>
						</div>
					</td>
					<td align="center" nowrap="nowrap" width="1%">
						
						<i class="fa fa-<?php echo ($rs->status==2?'desktop':'globe')?> fa-2x" style="vertical-align: inherit;"></i> 
						
                        <?php /*if( !empty($CONFIG_MELI['app']) && $CONFIG_MELI['app'] != '' && !$HIDDEN_TAG_VINCULAPROD ) { ?>
							
							<?php echo !empty($rs->if_ml) ? sprintf('<img src="%s">', Imgs::src('Mercado-Livre-logo.png', 'public')) : null;?>

                            <?php if( $rs->if_ml == '0' ) { ?>
                                <a href="/adm/produtos/produtos.php?codigo_id=<?php echo $rs->codigo_id?>&mlactive=true" class="btn<?php echo $rs->status == 1 ? ' btn-info-default ' : ' btn-info-default ';?>btn-sm" data-id="btn-ativa-ml">
                                    ativar no mercado livre
                                </a>
                            <?php } else { ?>
                                <img src="<?php echo Imgs::src('Mercado-Livre-logo.png', 'public')?>"/>
                            <?php } ?>
                        <?php } */?> 

						<?php /*if( $HIDDEN_TAG_VINCULAPROD ) { ?>
                            <a href="/adm/mercadolivre/ml-produtos.php?acao=VincularProdutoSite&codigo_id=<?php echo $rs->codigo_id?>&produto_vincular_id=<?php echo $GET['produto_vincular_id']?>" class="btn btn-secundary-default btn-sm">vincular ao site</a>
						<?php } */?> 
						
						<a <?php echo _P($PgAt, $_SESSION['admin']['id_usuario'], 'excluir')?> href="/adm/produtos/produtos.php?acao=status&codigo_id=<?php echo $rs->codigo_id;?>&status=<?php echo $rs->status;?>" class="ml5 btn btn-<?php echo $rs->status == 1 ? 'warning-default':'info-default';?> btn-sm data-acao<?php echo $HIDDEN_TAG_VINCULAPROD?>">
							<?php echo $rs->status == 1 ? 'ativar' : 'desativar';?>
						</a> 
						<a <?php echo _P($PgAt, $_SESSION['admin']['id_usuario'], 'incluir')?> href="/adm/produtos/produtos.php?acao=copiar&codigo_id=<?php echo $rs->codigo_id;?>" class="ml5 btn btn-success btn-sm produtos-copiar<?php echo $HIDDEN_TAG_VINCULAPROD?>">
							copiar
						</a>							
						<a <?php echo _P($PgAt, $_SESSION['admin']['id_usuario'], 'alterar')?> href="/adm/produtos/produtos-cadastrar.php?acao=ProdutosEditar&codigo_id=<?php echo $rs->codigo_id;?>" class="ml5 btn btn-warning btn-sm<?php echo $HIDDEN_TAG_VINCULAPROD?>">
							alterar
						</a>
						<a <?php echo _P($PgAt, $_SESSION['admin']['id_usuario'], 'excluir')?> href="/adm/produtos/produtos.php?acao=excluir&codigo_id=<?php echo $rs->codigo_id;?>" class="ml5 btn btn-danger-default btn-sm data-acao-excluir<?php echo $HIDDEN_TAG_VINCULAPROD?>">
							excluir
						</a>
					</td>
				</tr>
				<?php ++$i; } ?>
				<tr class="ocultos ocultar">
					<td colspan="4">
							<div class="paginacao clearfix">
							<?php
							$i = 0;
							if( $total > 0 )
							{
								for( $i = $pag - 19, $limiteDeLinks = $i + 20; $i <= $limiteDeLinks; ++$i )
								{
									if($i < 1)
									{
										$i = 1;
										$limiteDeLinks = 19;
									}
								
									if($limiteDeLinks > $total)
									{
										$limiteDeLinks = $total; 
										$i = $limiteDeLinks - 18;
									}

									if($i < 1)
									{
										$i = 1;
										$limiteDeLinks = $total;
									}
									
									if($i == $pag) {
										echo sprintf('<span class="at plano-fundo-adm-001">%s</span>', $i);
									}
									else {	
										$data = http_build_query(array_replace($GET, ['pag' => $i]));
										echo sprintf('<a href="/adm/produtos/produtos.php?%s">%s</a>', $data, $i);
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
</div>
<?php ob_start(); ?>
<script>
	<?php
	$data_uri = http_build_query($GET);
	$new_uri = sprintf('/adm/produtos/produtos.php?%s', $data_uri);
	?>
	$(document).on("click", "a", function(){
		var href = this.href || e.target.href;		
		if( href.search("excluir") > "0")
			if( ! confirm("Deseja realmente excluir!") ) return false;
	
	});
	
	$("[data-price=true]").mask("#,##0,00", {reverse: true, autoUnmask:true});
	
	$("#div-edicao").on("click", "#save_massa", function(e) {
		var formToSubmit = e.target;
		var serialisedFormArrayObject = $(formToSubmit).serializeArray();
		var $contentEditableItems = $('[data-edit="true"]');
		$contentEditableItems.each(function(index) {
			serialisedFormArrayObject.push({
				name: $contentEditableItems[index].id,
				value: $contentEditableItems[index].value
			});
		});
		$.ajax({
			type: "post",
			url: "<?php echo $new_uri?>",
			data: serialisedFormArrayObject,
			beforeSend: function() {
				$("#status-alteracao").html("Alterando dados em massa, aguarde...").fadeIn(10);
			},
			success: function(result) {
				var list = $("<div/>", {html: result});
				$("#div-edicao").html(list.find("#div-edicao").html());
			},
			done: function(result) {
			  // tell user its done!
			},
			error: function(a,b,c) {
				alert("An error has occured.");
				console.log(a.responseText, b, c);
			}
		});
	});

	$("#div-edicao").on("click", "#ml_massa", function(e) {
		var formToSubmit = e.target;
		var serialisedFormArrayObject = $('input[name="selecionados-exclusao"]').serializeArray();
		
		if( ! serialisedFormArrayObject.length )
			return confirm("Selecione os produtos para enviar!");

		// console.log(serialisedFormArrayObject);
		// return;

		$.ajax({
			type: "post",
			url: "<?php echo $new_uri?>",
			data: { campos: serialisedFormArrayObject, mlactive: "true" },
			beforeSend: function() {
				$("#status-alteracao").html("Alterando dados em massa, aguarde...").fadeIn(10);
			},
			success: function(result) {
				var list = $("<div/>", {html: result});
				$("#div-edicao").html(list.find("#div-edicao").html());
			},
			done: function(result) {
			  // tell user its done!
			},
			error: function(a,b,c) {
				alert("An error has occured.");
				console.log(a.responseText, b, c);
			}
		});
	});

</script>
<?php
$SCRIPT['script_manual'] .= ob_get_clean();

include '../rodape.php';