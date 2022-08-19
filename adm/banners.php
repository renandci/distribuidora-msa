<?php
include 'topo.php';

/**
 * Adicionar link ao banner
 */
switch( $POST['acao'] )
{
	case 'Banners' :
		$ID_BANNER = isset( $GET['id_banner'] ) && $GET['id_banner'] != '' ? $GET['id_banner'] : '';
		$produto = addslashes($POST['produto']);
		$ordem 	= (INT)$POST['ordem'];
		switch( $GET['acaoLink'] )
		{
			case 'inserirLink' :
			case 'removerLink' :
                if( Banners::action_cadastrar_editar(['Banners' => [$ID_BANNER => [ 'produto' => $produto, 'ordem' => $ordem, ] ] ], 'alterar', 'banner') ) {
                    header('Location: /adm/banners.php');
                    return;
                }
			break;
		}
	break;
}

/**
 * Exclui o banner
 */
if( isset( $GET['acao'], $GET['id_banner'] ) && $GET['acao'] == 'excluirBanner' )
{
    $Banner = Banners::find($GET['id_banner']);
	$rsExcluir = $Banner->to_array();
	$CAMINHO = '../assets/' . ASSETS . '/imgs/banners/';
	if( is_file( $CAMINHO . $rsExcluir['banner'] ) ) {
		if( unlink( $CAMINHO . $rsExcluir['banner'] ) ) {
			Banners::action_cadastrar_editar(['Banners' => [$rsExcluir['id'] => [ 'produto' => '' ] ] ], 'delete', 'banner');
			header('Location: /adm/banners.php');
			return;
		}
	}
    else {
        Banners::action_cadastrar_editar(['Banners' => [$rsExcluir['id'] => [ 'produto' => '' ] ] ], 'delete', 'banner');
        header('Location: /adm/banners.php');
        return;
    }
}
?>

<style>
	body{ background-color: #f1f1f1 }
</style>
<div id="div-edicao" class="panel panel-default">
	<div class="panel-heading panel-store">BANNERS</div>
	<div class="panel-body">
		<div class="clearfix">
			<a href="/adm/banners-enviar.php" class="btn btn-primary mb5 mt5 btn-cadastros-banners pull-right" <?php echo _P( 'banners', $_SESSION['admin']['id_usuario'], 'incluir' )?>>cadastrar</a>
		</div>
		<table width="100%" border="0" cellpadding="10" cellspacing="0" class="table table-striped">
			<tbody>
				<tr class='text-uppercase'>
					<th>Imagem</th>
					<th>Opções</th>
					<th class='text-center'>Ações</th>
				</tr>
				<?php
				$i		= 0;
				$maximo = 15;
				$pag 	= isset( $GET['pag'] ) && $GET['pag'] != '' ? $GET['pag'] : 1;
				$inicio = ( $pag * $maximo ) - $maximo;
				$busca 	= sprintf('select * from banners where loja_id=%u order by id desc ', $CONFIG['loja_id']);
				$total 	= ceil( Banners::find_num_rows( $busca ) / $maximo );
				$busca .= sprintf('limit %u, %u', $inicio, $maximo);
				$sql = Banners::find_by_sql( $busca );
				foreach( $sql as $rs ) { $rs = $rs->to_array(); ?>
				<tr class="in-hover">
					<td align="center" width='1%' nowrap="nowrap">
						<img src="<?php echo !empty($rs['banner']) && $rs['banner'] != '' ? Imgs::src("mobile-{$rs['banner']}", 'banners') : Imgs::src('banner-null.gif', 'imgs')?>" class="imagem-carregar" width="450"/>
					</td>
					<td>
						<input type='hidden' name='id' value='<?php echo $rs['id'];?>'/>
						<input type='hidden' name='acao' value='Banners'/>
						<div class="row">
							<div class="form-group col-lg-10 col-md-10 col-sm-10">
								<label for="produto">Link</label>
								<input type="text" name="produto" class="form-control" id="produto" placeholder="Cadastrar link do Banner" value="<?php echo $rs['produto'];?>"/>
							</div>
							<div class="form-group col-lg-2 col-md-2 col-sm-2">
								<label for="Ordem">Ordem</label>
								<input type="text" name="ordem" class="form-control text-right" id="ordem" placeholder="Ordenar banner" value="<?php echo $rs['ordem'];?>">
							</div>
						</div>
						<a href='/adm/banners.php?id_banner=<?php echo $rs['id'];?>&acaoLink=<?php echo $rs['produto'] == '' ? 'inserirLink' : 'removerLink';?>' class='btn btn-success btn-edicao-bannner' <?php echo _P( 'banners', $_SESSION['admin']['id_usuario'], 'incluir|alterar' )?>>
							salvar
						</a>
					</td>
					<td align="center" width='1%' nowrap="nowrap">
						<a href='/adm/banners-enviar.php?id_banner=<?php echo $rs['id'];?>' class='btn btn-primary btn-xs btn-cadastros-banners btn-block' <?php echo _P( 'banners', $_SESSION['admin']['id_usuario'], 'incluir' )?>>
							editar imagem
						</a>
						<a href='/adm/banners.php?acao=excluirBanner&id_banner=<?php echo $rs['id'];?>' class='btn btn-danger btn-xs btn-block mt15' <?php echo _P( 'banners', $_SESSION['admin']['id_usuario'], 'excluir' )?>>
							excluir
						</a>
					</td>
				</tr>
				<?php
				++$i;
				}
				?>
				<tr>
					<td colspan="3">
						<div class="paginacao paginacao-add">
							<?php
							if( $total > 0 )
							{
								for( $i = $pag - 1, $limiteDeLinks = $i + 5; $i <= $limiteDeLinks; ++$i )
								{
									if($i < 1)
									{
										$i = 1;
										$limiteDeLinks = 5;
									}

									if($limiteDeLinks > $total)
									{
										$limiteDeLinks = $total;
										$i = $limiteDeLinks - 4;
									}

									if($i < 1)
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
										echo "<a href=\"/adm/banners.php?pag={$i}\">{$i}</a>";
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
<script>
	$(document).on("click", "a", function(){
		var href = this.href || e.target.href;
		if( href.search('excluir') > '0')
			if( ! confirm("Deseja realmente excluir!") ) return false;

	});
</script>
<?php
include 'rodape.php';
