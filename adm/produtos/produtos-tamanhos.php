<?php
include '../topo.php';

/**
 * Adiciona Tamanhos nos Produtos
 */
if( isset( $POST['acao'] ) && 'adicionar-tamanhos' == $POST['acao'] ) :
	
	$tamanho_id = $POST['tamanho_id'];
    $codigo_id = (int)$POST['codigo_id'];
	
    // Gera um novo produto a partir de uma copia do mesmo adicionado cores|etc...
    $Produtos = Produtos::first(['conditions' => ['codigo_id=? and excluir=0 and status=0', $codigo_id], 'limit' => 1 ]);
    $ArrayFields = $Produtos->to_array();
	
	foreach($tamanho_id as $id_tamanho)
	{
		foreach ( $ArrayFields as $fields => $values ) 
		{		
			if( ! in_array($fields, ['id', 'id_tamanho', 'codigo_produto']) ) {
				$campos[$fields] = $Produtos->{$fields};
			}
			else {
				$campos['codigo_produto'] = CodProduto($Produtos->nome_produto, $Produtos->id);
				$campos['id_tamanho'] = $id_tamanho;
			}
		}
		unset($campos['created_at'], $campos['updated_at']);
		
		// Verifica cor nula
		$ProdutosNulo = Produtos::first(['conditions' => ['codigo_id=? and id_tamanho=?', $codigo_id, 0]]);

		// Verifica se há cores existentes e excluidas
		$ProdutosExcluida = Produtos::first(['conditions' => ['codigo_id=? and id_tamanho=? and excluir=1', $codigo_id, $id_tamanho]]);

		if( count($ProdutosNulo) ) 
			Produtos::action_cadastrar_editar(['Produtos' => [$ProdutosNulo->id => ['id_tamanho' => $id_tamanho]]], 'alterar', 'nome_produto');
		else if( count($ProdutosExcluida) )
			Produtos::action_cadastrar_editar(['Produtos' => [$ProdutosExcluida->id => ['id_tamanho' => $id_tamanho, 'excluir' => '0']]], 'alterar', 'nome_produto');
		else  // Gera um cadastro novo
			Produtos::action_cadastrar_editar([ 'Produtos' => [ 0 => $campos ] ], 'cadastrar', 'nome_produto');
	}
	
	
	// $tamanho_id = (INT)$POST['tamanho_id'];;
	// $tam_id = (INT)$POST['tamanho_id'];
	// $cod_id = isset($GET['codigo_id']) ? (INT)$GET['codigo_id'] : (INT)$POST['codigo_id'];
	
    // /**
	 // * Verifica tamanho nula e gera um novo cadastro
	 // */
	// if( Produtos::count(['conditions' => ['codigo_id=? and id_tamanho=0', $cod_id]]) > 0 ) :
        // $Produtos = Produtos::all([ 'conditions' => ['codigo_id=? and id_tamanho=?', $cod_id, 0] ]);
        // foreach ( $Produtos as $r ) {
            // Produtos::action_cadastrar_editar([ 'Produtos' => [ $r->id => [ 'id_tamanho' => $tam_id ] ] ], 'alterar', 'id');            
        // }
        // header("Location: /adm/produtos/produtos-tamanhos.php?codigo_id={$GET['codigo_id']}");
        // return;
	// endif;

	// /**
	 // * Verifica se há tamanhos existentes e excluidas
	 // */
    // if( Produtos::count(['conditions' => ['codigo_id=? and id_tamanho=? and excluir=1', $cod_id, $tam_id]]) > 0 ) :
        // $Produtos = Produtos::all([ 'conditions' => ['codigo_id=? and id_tamanho=? and excluir=1', $cod_id, $tam_id] ]);
        // foreach ( $Produtos as $r ) :
            // Produtos::action_cadastrar_editar([ 'Produtos' => [ $r->id => [ 'excluir' => '0' ] ] ], 'alterar', 'id');            
        // endforeach;
        // header("Location: /adm/produtos/produtos-tamanhos.php?codigo_id={$GET['codigo_id']}");
        // return;
	// endif;
	
	// /**
     // * Gera um novo produto a partir de uma copia do mesmo adicionado cores|etc...
     // */
    // $Produtos = Produtos::find('first', ['conditions' => ['codigo_id=? and excluir=0 and status=0', $cod_id], 'limit' => 1 ]);
    // $ArrayFields = $Produtos->to_array();
    // foreach ( $ArrayFields as $fields => $values ) :
        // if( ! in_array($fields, ['id', 'id_cor', 'id_tamanho', 'codigo_produto']) ) :
            // $campos[$fields] = $Produtos->{$fields};
        // else :
            // $campos['codigo_produto'] = CodProduto($Produtos->nome_produto, $Produtos->id);
            // $campos['id_cor'] = 0;
            // $campos['id_tamanho'] = $tam_id;
        // endif;
    // endforeach;
    
    // Produtos::action_cadastrar_editar([ 'Produtos' => [ 0 => $campos ] ], 'cadastrar', 'nome_produto');
	// header("Location: /adm/produtos/produtos-tamanhos.php?codigo_id={$GET['codigo_id']}");
	return;
endif;

/**
 * Deleta as Tamanhos dos Produtos
 */
if( isset( $GET['acao'], $GET['produto_id'] ) && ( 'ExcluirTamanhos' == $GET['acao'] && $GET['produto_id'] > 0 ) ) :
    Produtos::action_cadastrar_editar([ 'Produtos' => [ $GET['produto_id'] => [ 'excluir' => 1 ] ] ], 'excluir', 'id');
    header("Location: /adm/produtos/produtos-tamanhos.php?codigo_id={$GET['codigo_id']}");
    return;
endif;

/**
 * Edita os dados dos produtos
 */
if( isset( $POST['Produtos'] ) && count($POST['Produtos']) > 0 ) :
    foreach ( $POST['Produtos'] as $key => $values ) :
        foreach ( $values as $key1 => $values1 ) :
            if ( $key1 !== 'id' ) :
                $campos[ $key1 ] = ( preg_replace('/[^0-9.]*/', 'B', $values1) ? dinheiro($values1) : $values1 );
                Produtos::action_cadastrar_editar([ 'Produtos' => [ $key => $campos ] ], 'alterar', 'nome_produto');
            endif;
        endforeach;
    endforeach;
    
    header("Location: /adm/produtos/produtos-tamanhos.php?codigo_id={$GET['codigo_id']}");
    return;
endif;
?>
<div class="clearfix" id="aba6">
	<span class="pull-left w90 mb15">
		<p>OPÇÕES:</p> 

		<select id="id_tamanho" class="w90" multiple="multiple">
			<optgroup label="Nenhum">;
			<option value="0">Selecione uma opção</option>
			<?php
			$arr_c = [];
			$group = null;
			
			$conditions['conditions'] = sprintf('excluir = 0 and loja_id=%u', $CONFIG['loja_id']);
			$Tamanhos = Tamanhos::all($conditions);

            foreach ( $Tamanhos as $Tam ) {
				$arr_c[] = $Tam->id;
				if( $group != $Tam->opcoes->tipo ) {
					$group = $Tam->opcoes->tipo; ?>
                    <optgroup label="<?php echo $group?>">
                <?php } ?>
				<option value="<?php echo $Tam->id?>" hex1="<?php echo $Tam->hex1?>" hex2="<?php echo $Tam->hex2?>"><?php echo $Tam->nometamanho?></option>
                <?php if( $group != $Tam->opcoes->tipo ) { ?>
                    </optgroup>
                <?php } ?>
            <?php } ?>
		</select>
		<button type="button" class="btn fa fa-plus-square fa-1x" id="btn-adicionar-tamanhos" <?php echo _P('produtos-cadastrar', $_SESSION['admin']['id_usuario'], 'incluir')?>></button>
		<a href="/adm/tamanhos.php?codigo_id=<?php echo $GET['codigo_id']?>" class="btn fa fa-folder-open fa-1x" id="btn-cadastrar-tamanhos" <?php echo _P('tamanhos', $_SESSION['admin']['id_usuario'], 'acessar')?>></a>
	</span>
	
	<table width="100%" cellpadding="8" cellspacing="0" border="0" bgcolor="#bbbbbb" class="mt15">
		<tbody>
			<tr bgcolor="#f3f3f3" class="formulario-produto-tamanhos">
				<td colspan="9" class="clearfix">					
					<a href="/adm/produtos/produtos-tamanhos.php?acao=SalvarTam&codigo_id=<?php echo $GET['codigo_id']?>" class="pull-right btn btn-primary btn-sm ml15 btn-salvar-cor-tamanho" data-id="formulario-<?php echo $GET['codigo_id']?>" <?php echo _P('produtos-tamanhos', $_SESSION['admin']['id_usuario'], 'alterar')?>>
						Salvar
						<i class="fa fa-edit"></i>
					</a>
					<a href="/adm/fotos.php?codigo_id=<?php echo $GET['codigo_id']?>" class="pull-right btn btn-warning btn-sm btn-fotos" <?php echo _P('fotos', $_SESSION['admin']['id_usuario'], 'acessar')?>>
						<i class="fa fa-camera"></i> 
						<?php echo (int)ProdutosImagens::count(['conditions' => ['codigo_id=?', $GET['codigo_id']]])?> fotos
					</a>
				</td>
			</tr>
			<?php
			$conditions['select'] = ''
				. 'produtos.id, '
				. 'produtos.codigo_id, '
				. 'produtos.id_cor, '
				. 'produtos.id_tamanho, '
				. 'produtos.id_frete, '
				. 'produtos.codigo_referencia, '
				. 'produtos.codigo_produto, '
				. 'produtos.nome_produto, '
				. 'produtos.estoque, '
				. 'produtos.preco_custo, '
				. 'produtos.preco_venda, '
				. 'produtos.preco_promo, '
				. 'produtos.placastatus, '
				. 'produtos.categoria, '
				. '(SELECT SUM(pedidos_vendas.quantidade) as total '
				. 'FROM pedidos_vendas '
				. 'JOIN pedidos ON pedidos.id = pedidos_vendas.id_pedido '
				. 'WHERE pedidos.status in(1,2,3,6,7,11) AND pedidos_vendas.id_produto = produtos.id) as pendentes';

			$conditions['joins'] = ['tamanho', 'freteproduto'];
			$conditions['conditions'] = sprintf('produtos.codigo_id=%u and produtos.excluir=0', (int)$GET['codigo_id']);
			$conditions['order'] = 'tamanhos.ordem asc, tamanhos.nometamanho asc';
			
			$ProdutosTamanhos = Produtos::all($conditions);

			foreach( $ProdutosTamanhos as $rws ) { ?>
				<!--[ TAMANHOS ]-->
                <?php if($group_ordem != $rws->tamanho->opcoes->tipo) { $group_ordem = $rws->tamanho->opcoes->tipo; ?>
                <tr bgcolor="f3f3f3" class="plano-fundo-adm-001 cor-branco bold">
                    <td align="center"><?php echo $group_ordem;?></td>
                    <td align="center">Refer/Cód: <small>(opcional)</small></td>
                    <td align="right">Estoque</td>
                    <td align="center">Pr. Custo:</td>
                    <td align="center">Pr. de:</td>
                    <td align="center">Pr. Site:</td>
                    <td align="left">Categoria <small>(opcional)</small></td>
                    <td align="center">Frete</td>
                    <td align="center">Ações</td>
                </tr>
                <?php } ?>
                
				<tr bgcolor="#<?php echo (($i % 2) == 0) ? 'ffffff' : 'f3f3f3';?>" id="formulario-<?php echo $rws->codigo_id?>" class="in-hover formulario-produto-tamanhos<?php echo !$rws->id_tamanho ? ' hidden':''?>" style="border-bottom: solid 1px #aaa">
					<input name="Produtos[<?php echo $rws->id?>][id]" type="hidden" value="<?php echo $rws->id?>"/>
					<td nowrap="nowrap" width="1%" align="center" class="text-maiusculo">
						<?php echo $rws->nometamanho?>
					</td>
					<td nowrap="nowrap" width="155px">
						<input type="text" class="w100" name="Produtos[<?php echo $rws->id?>][codigo_produto]" value="<?php echo CodProduto($rws->nome_produto, $rws->id, $rws->codigo_produto)?>"/>
					</td>
					<td>
                        <input name="Produtos[<?php echo $rws->id?>][estoque]" class="pull-right text-right" value="<?php echo $rws->estoque?>" style="width: 110px;"/>
                        <span class="show ft12px bold pull-right mr15 mt10" style="color: #dc4e4e">PENDENTES: <?php echo (INT)$rws->pendentes?></span>
                    </td>
					<td nowrap="nowrap" width="100px" data-disabled>
						<input name="Produtos[<?php echo $rws->id?>][preco_custo]" type="text" value="<?php echo number_format($rws->preco_custo, 2, ',', '.')?>" class="text-right w100 preco-mask"/>
					</td>
					<td nowrap="nowrap" width="100px" data-disabled>
						<input name="Produtos[<?php echo $rws->id?>][preco_venda]" type="text" value="<?php echo number_format($rws->preco_venda, 2, ',', '.')?>" class="text-right w100 preco-mask"/>
					</td>
					<td nowrap="nowrap" width="100px" data-disabled>
						<input name="Produtos[<?php echo $rws->id?>][preco_promo]" type="text" value="<?php echo number_format($rws->preco_promo, 2, ',', '.')?>" class="text-right w100 preco-mask"/>
					</td>
					<td>
						<select name="Produtos[<?php echo $rws->id?>][categoria]" style="width: 200px">
							<option value="">Selecione</option>
							<option value="F"<?php echo $rws->categoria=="F" ? " selected" : ""?>>Feminino</option>
							<option value="M"<?php echo $rws->categoria=="M" ? " selected" : ""?>>Masculino</option>
							<option value="N"<?php echo $rws->categoria=="N" ? " selected" : ""?>>Neutro</option>
						</select>
					</td>
					<td align="center" id="id_frete<?php echo $rws->id?>">
                        A.: <span id="altura<?php echo $rws->id?>"><?php echo $rws->freteproduto->altura?></span><br />
                        L.: <span id="largura<?php echo $rws->id?>"><?php echo $rws->freteproduto->largura?></span><br />
                        C.: <span id="comprimento<?php echo $rws->id?>"><?php echo $rws->freteproduto->comprimento?></span><br />
                        Kg: <span id="peso<?php echo $rws->id?>"><?php echo $rws->freteproduto->peso?></span><br />
                    </td>
					<td align="center" nowrap="nowrap" width="1%">
						<a href="/adm/frete.php?produto_id=<?php echo $rws->id?>&codigo_id=<?php echo $GET['codigo_id']?>&id_frete=<?php echo $rws->id_frete?>" class="mt5 btn btn-info btn-sm show btn-dados-frete" <?php echo _P( 'frete', $_SESSION['admin']['id_usuario'], 'acessar' )?>>
							<i class="fa fa-truck"></i> frete
						</a>
						<a href="/adm/produtos/produtos-tamanhos.php?acao=ExcluirTamanhos&produto_id=<?php echo $rws->id?>&codigo_id=<?php echo $GET['codigo_id']?>" class="mt5 btn btn-danger btn-sm btn-excluir-tamanhos" <?php echo _P( 'produtos-tamanhos', $_SESSION['admin']['id_usuario'], 'excluir' )?>>
							<i class="fa fa-trash"></i> excluir
						</a>
					</td>
				</tr>
				<!--[END TAMANHOS]-->
            <?php $i++; } ?>
		</tbody>
	</table>
</div>
<?php
include '../rodape.php';