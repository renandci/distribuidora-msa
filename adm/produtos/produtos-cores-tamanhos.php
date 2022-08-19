<?php
include '../topo.php';

/**
 * Criar novas cores
 */
$count = 0;
$mensagem = '';
if( isset( $POST['acao'], $POST['cor_id'], $POST['codigo_id'] ) && ($POST['acao'] === 'AdicionarCores' && $POST['cor_id'] > 0) ) {
    $cor_id = $POST['cor_id'];
    $codigo_id = (int)$POST['codigo_id'];
	
    // Gera um novo produto a partir de uma copia do mesmo adicionado cores|etc...
    $Produtos = Produtos::first(['conditions' => ['codigo_id=? and excluir=0 and status=0', $codigo_id], 'limit' => 1 ]);
    $ArrayFields = $Produtos->to_array();
	
	foreach($cor_id as $id_cor)
	{
		foreach ( $ArrayFields as $fields => $values ) 
		{		
			if( ! in_array($fields, ['id', 'id_cor', 'id_tamanho', 'codigo_produto']) ) {
				$campos[$fields] = $Produtos->{$fields};
			}
			else {
				$campos['codigo_produto'] = CodProduto($Produtos->nome_produto, $Produtos->id);
				$campos['id_tamanho'] = 0;
				$campos['id_cor'] = $id_cor;
			}
		}
		unset($campos['created_at'], $campos['updated_at']);
		
		// Verifica cor nula
		$ProdutosNulo = Produtos::first(['conditions' => ['codigo_id=? and id_cor=?', $codigo_id, 0]]);

		// Verifica se há cores existentes e excluidas
		$ProdutosExcluida = Produtos::first(['conditions' => ['codigo_id=? and id_cor=? and excluir=1', $codigo_id, $id_cor]]);

		if( count($ProdutosNulo) ) 
			Produtos::action_cadastrar_editar(['Produtos' => [$ProdutosNulo->id => ['id_cor' => $id_cor]]], 'alterar', 'nome_produto');
		else if( count($ProdutosExcluida) )
			Produtos::action_cadastrar_editar(['Produtos' => [$ProdutosExcluida->id => ['id_cor' => $cor_id, 'excluir' => '0']]], 'alterar', 'nome_produto');
		else  // Gera um cadastro novo
			Produtos::action_cadastrar_editar([ 'Produtos' => [ 0 => $campos ] ], 'cadastrar', 'nome_produto');
	}
	
	header("Location: /adm/produtos/produtos-cores-tamanhos.php?codigo_id={$codigo_id}");
    return;
}
/**
 * Adiciona tamanhos nas cores nos produtos
 */
if( isset( $POST['acao'], $GET['codigo_id'], $GET['cor_id'] ) && ($POST['acao'] == 'AdicionarTamanhos') ) {
	$cor_id = (int)$GET['cor_id'];
	$tam_id = $POST['tamanho_id'];
	$codigo_id = (int)$GET['codigo_id'];
    
	// Gera um novo produto a partir de uma copia do mesmo adicionado cores|etc...
    $Produtos = Produtos::first(['conditions' => [ 'codigo_id=? and id_cor=? and excluir=0', $codigo_id, $cor_id], 'limit' => 1 ]);
    $ArrayFields = $Produtos->to_array();
	
	// echo '<div id="aba7">';
	
	foreach($tam_id as $id_tam)
	{
		foreach ( $ArrayFields as $fields => $values ) 
		{		
			if( ! in_array($fields, ['id', 'id_tamanho', 'codigo_produto']) ) {
				$campos[$fields] = $Produtos->{$fields};
			}
			else {
				$campos['codigo_produto'] = CodProduto($Produtos->nome_produto, $Produtos->id);
				$campos['id_tamanho'] = $id_tam;
			}
		}
		unset($campos['created_at'], $campos['updated_at']);
		
		// Verifica cor nula
		$ProdutosNulo = Produtos::first(['conditions' => ['codigo_id=? and id_cor=? and id_tamanho=0', $codigo_id, $cor_id]]);

		// Verifica se há cores existentes e excluidas
		$ProdutosExcluida = Produtos::first(['conditions' => ['codigo_id=? and id_cor=? and id_tamanho=? and excluir=1', $codigo_id, $cor_id, $id_tam]]);

		if( count($ProdutosNulo) ) 
			Produtos::action_cadastrar_editar(['Produtos' => [$ProdutosNulo->id => ['id_tamanho' => $id_tam]]], 'alterar', 'nome_produto');
		else if( count($ProdutosExcluida) )
			Produtos::action_cadastrar_editar(['Produtos' => [$ProdutosExcluida->id => ['id_tamanho' => $id_tam, 'excluir' => '0']]], 'alterar', 'nome_produto');
		else  // Gera um cadastro novo
			Produtos::action_cadastrar_editar([ 'Produtos' => [ 0 => $campos ] ], 'cadastrar', 'nome_produto');
		
		// echo '<pre>';
		// print_r($campos);
		// echo '</pre>';
	}
	// echo '</div>';
    
    header("Location: /adm/produtos/produtos-cores-tamanhos.php?codigo_id={$codigo_id}");
    return;
}

/**
 * Deleta os tamanhos
 */
if( isset( $GET['acao'], $GET['produto_id'], $GET['codigo_id'] ) && ( 'ExcluirTamanhos' == $GET['acao'] && $GET['produto_id'] > 0 ) ) {
    Produtos::action_cadastrar_editar([ 'Produtos' => [ $GET['produto_id'] => [ 'excluir' => 1 ] ] ], 'excluir', 'nome_produto');
    header("Location: /adm/produtos/produtos-cores-tamanhos.php?codigo_id={$GET['codigo_id']}");
    return;
}

/**
 * Delete as cores
 */
if( isset( $GET['acao'], $GET['cor_id'], $GET['codigo_id'] ) && ( 'ExcluirCores' == $GET['acao'] ) ) {
    $count = 0;
    $Produtos = Produtos::all([ 'conditions' => ['codigo_id=? and id_cor=?',$GET['codigo_id'], $GET['cor_id']] ]);
    foreach ( $Produtos as $r ) {
        Produtos::action_cadastrar_editar([ 'Produtos' => [ $r->id => [ 'excluir' => 1 ] ] ], 'excluir', 'nome_produto');
        $count++;
    }
    if( $count > 0 ) {
        header("Location: /adm/produtos/produtos-cores-tamanhos.php?codigo_id={$GET['codigo_id']}");
        return;
    }
}

/**
 * Edita os dados dos produtos
 */
if( isset( $POST['Produtos'] ) && count($POST['Produtos']) > 0 ) {
	$count = 0;
    foreach ( $POST['Produtos'] as $key => $values ) {
        foreach ( $values as $key1 => $values1 ) {
            if ( $key1 !== 'id' ) {
                $campos[ $key1 ] = ( preg_replace('/[^0-9.]*/', 'B', $values1) ? dinheiro($values1) : $values1 );
                Produtos::action_cadastrar_editar([ 'Produtos' => [ $key => $campos ] ], 'alterar', 'nome_produto');
				$count++;
            }
        }
    }
	if( $count > 0 ) {
        header("Location: /adm/produtos/produtos-cores-tamanhos.php?codigo_id={$GET['codigo_id']}");
        return;
    }
}
?>
<div class="clearfix" id="aba7">
	<div class="mb15">
		<p>OPÇÕES:</p>
		<select id="id_cor" class="w90" multiple="multiple">
			<optgroup label="Nenhum">;
			<option value="0">Selecione uma opção</option>
			<?php
			$arr_c = [];
			$CATCOR = null;
			
			$conditions['conditions'] = sprintf('excluir = 0 and loja_id=%u', $CONFIG['loja_id']);
			$Cores = Cores::all($conditions);

            foreach ( $Cores as $rwsCor ) {
				$arr_c[] = $rwsCor->id;
				if( $CATCOR != $rwsCor->opcoes->tipo ) {
					$CATCOR = $rwsCor->opcoes->tipo; ?>
                    <optgroup label="<?php echo $CATCOR?>">
                <?php } ?>
				<option value="<?php echo $rwsCor->id?>" hex1="<?php echo $rwsCor->cor1?>" hex2="<?php echo $rwsCor->cor2?>"><?php echo $rwsCor->nomecor?></option>
                <?php if( $CATCOR != $rwsCor->opcoes->tipo ) { ?>
                    </optgroup>
                <?php } ?>
            <?php } ?>
		</select>
		<button type="button" class="btn fa fa-plus-square fa-1x" id="btn-adicionar-cores" <?php echo _P('produtos-cores-tamanhos', $_SESSION['admin']['id_usuario'], 'incluir')?>></button>
		<a href="/adm/cores.php?codigo_id=<?php echo $GET['codigo_id']?>" class="btn fa fa-folder-open fa-1x" id="btn-cadastrar-cores" <?php echo _P('cores', $_SESSION['admin']['id_usuario'], 'acessar' )?>></a>
	</div>

	<table width="100%" cellpadding="8" cellspacing="0" border="0" bgcolor="#bbbbbb" class="mt15">
		<tbody>
			<tr bgcolor="f3f3f3" class="plano-fundo-adm-001 cor-branco bold-3">
				<td align="center">Tam.</td>
				<td align="center">Ref./Cód.: <small>(opcional)</small></td>
				<td align="right">Estoque</td>
				<td align="center">Pr. Custo:</td>
				<td align="center">Pr. de:</td>
				<td align="center">Pr. Site:</td>
				<td align="left">Categoria <small>(opcional)</small></td>
				<td align="center">Frete</td>
				<td align="center">Ações</td>
			</tr>
			<?php
			$group_cor = 0;
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

			$conditions['joins'] = ['cor', 'tamanho', 'freteproduto'];
			$conditions['conditions'] = sprintf('produtos.codigo_id=%u and produtos.excluir=0', (int)$GET['codigo_id']);
			$conditions['order'] = 'cores.ordem asc, cores.nomecor asc, tamanhos.ordem asc, tamanhos.nometamanho asc';
			
			$ProdutosCores = Produtos::all($conditions);

			foreach( $ProdutosCores as $rws ) { ?>
				<?php if( $group_cor != $rws->id_cor ) { $group_cor = $rws->id_cor; ?>
				<tr bgcolor="#ebebeb" class="formulario-produto-cores">
					<td colspan="9" class="clearfix text-right">
						<span class="cx-cor-relativa <?php echo !empty($rws->cor->icon) ? 'is_icon':null?> pull-left" style="width: 32px; height: 32px;">
							<span class="cor-style-1" style="background: <?php echo !empty($rws->cor->icon) ? sprintf('url(%s)', Imgs::src($rws->capa->imagem, 'xs')) : "#{$rws->cor->cor1}"?>">
								<span class="cor-style-2" style="border-bottom-color: #<?php echo $rws->cor->cor2?>"></span>
							</span>
						</span>
						<b class="pull-left mt5 ml15 mr15 text-maiusculo"><?php echo $rws->cor->nomecor?></b>
						<a href="/adm/fotos.php?codigo_id=<?php echo $GET['codigo_id']?>&cor_id=<?php echo $rws->id_cor?>" class="btn btn-sm btn-warning btn-fotos" <?php echo _P('fotos', $_SESSION['admin']['id_usuario'], 'acessar')?>>
							<?php echo count($rws->fotos)?> fotos cadastradas
							<i class="fa fa-camera"></i>
						</a> 
						<a href="/adm/tamanhos.php?cor_id=<?php echo $rws->id_cor?>&codigo_id=<?php echo $GET['codigo_id']?>" class="btn btn-sm btn-info btn-tamanhos ml5 mr5" <?php echo _P('tamanhos', $_SESSION['admin']['id_usuario'], 'acessar' )?>>
							adicionar <?php echo $rws->tamanho->opcoes->tipo?> <i class="fa fa-plus"></i>
						</a> 
						<a href="/adm/produtos/produtos-cores-tamanhos.php?acao=SalvarCorTam&codigo_id=<?php echo $GET['codigo_id']?>" class="btn btn-sm btn-primary ml5 mr5 btn-salvar-cor-tamanho" data-id="formulario-<?php echo $rws->codigo_id?>-<?php echo $rws->id_cor?>" <?php echo _P('produtos-cores-tamanhos', $_SESSION['admin']['id_usuario'], 'alterar')?>>
							Salvar
							<i class="fa fa-edit"></i>
						</a> 
						<a href="/adm/produtos/produtos-cores-tamanhos.php?acao=ExcluirCores&codigo_id=<?php echo $GET['codigo_id']?>&cor_id=<?php echo $rws->id_cor?>" class="btn btn-sm btn btn-danger excluir-all-cor-tam" <?php echo _P('produtos-cores-tamanhos', $_SESSION['admin']['id_usuario'], 'excluir')?>>
							Excluir Cor
							<i class="fa fa-trash"></i>
						</a> 
					</td>
				</tr>
				<?php } ?>
				<!--[ TAMANHOS ]-->
				<tr bgcolor="#<?php echo (($i % 2) == 0) ? 'ffffff' : 'f3f3f3';?>" id="formulario-<?php echo $rws->codigo_id?>-<?php echo $rws->id_cor?>" class="in-hover formulario-produto-cores<?php echo !$rws->id_tamanho ? ' hidden':''?>">
					<input name="Produtos[<?php echo $rws->id?>][id]" type="hidden" value="<?php echo $rws->id?>"/>
					<td nowrap="nowrap" width="1%" align="center" class="text-maiusculo">
						<?php echo $rws->nometamanho?>
					</td>
					<td nowrap="nowrap" width="155px">
						<input type="text" class="w100" name="Produtos[<?php echo $rws->id?>][codigo_produto]" value="<?php echo $rws->codigo_produto == '' ? CodProduto($rws->nome_produto, $rws->id) : $rws->codigo_produto?>"/>
					</td>
					<td>
						<input name="Produtos[<?php echo $rws->id?>][estoque]" class="text-right pull-right" value="<?php echo $rws->estoque?>" style="width: 110px;"/>
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
						A.: <span><?php echo $rws->freteproduto->altura?></span><br/>
						L.: <span><?php echo $rws->freteproduto->largura?></span><br/>
						C.: <span><?php echo $rws->freteproduto->comprimento?></span><br/>
						Kg: <span><?php echo $rws->freteproduto->peso?></span>
					</td>
					<td align="center" nowrap="nowrap" width="1%">
						<a href="/adm/frete.php?produto_id=<?php echo $rws->id?>&codigo_id=<?php echo $GET['codigo_id']?>&id_frete=<?php echo $rws->id_frete?>" class="btn btn-info btn-sm btn-block btn-dados-frete" <?php echo _P('frete', $_SESSION['admin']['id_usuario'], 'acessar' )?>>
							<i class="fa fa-truck"></i> frete
						</a> 
						<a href="/adm/produtos/produtos-cores-tamanhos.php?acao=ExcluirTamanhos&produto_id=<?php echo $rws->id?>&id_frete=<?php echo $rws->id_frete?>&codigo_id=<?php echo $GET['codigo_id']?>" class="mt5 btn btn-danger btn-block btn-sm btn-excluir-tamanhos" <?php echo _P('produtos-cores-tamanhos', $_SESSION['admin']['id_usuario'], 'excluir')?>>
							<i class="fa fa-trash"></i> excluir
						</a>
					</td>
				</tr>
				<tr><td colspan="9" bgcolor="#ffffff" style="border-bottom: solid 1px #aaa"></td></tr>
				<!--[END TAMANHOS]-->
			<?php $i++; } ?>
		</tbody>
	</table>
</div>
<?php
include '../rodape.php';