<?php
include 'topo.php';

if( !isset( $GET['codigo_id'] ) OR $GET['codigo_id'] == '') {
	header('Location: /adm/login.php');
	return;
}

switch($POST['acao'])
{
	case 'cadastrar_editar_imagens':

		$CODIGO_ID = $POST['codigo_id'];
		$COR_ID = (int)$POST['cor_id']??0;
		$ID = (int)$POST['id']??0;

		$CAMINHO = URL_VIEWS_BASE_PUBLIC_UPLOAD . '/imgs/produtos/';

		$IMG = $_FILES['foto'];

		$Produto = Produtos::first(['conditions' => ['codigo_id=? and id_cor=?', $CODIGO_ID, $COR_ID]]);

		$ProdutosImagens = null;
		foreach($IMG['error'] as $k => $v)
		{
			$ProdutosImagens = $ID > 0 ? ProdutosImagens::find($ID) : null;

			$ext = pathinfo($IMG['name'][$k], PATHINFO_EXTENSION);
			$IMG['tmp_name'][$k];

			// $basename = bin2hex(random_bytes(8));
			$basename = sprintf('%s-%s', $CODIGO_ID, (time() * ($k + 1)));
			$filename = $ID > 0 ? $ProdutosImagens->imagem : sprintf('%s.%0.8s', $basename, $ext);

			// // Tenta remover a imagem do servidor
			// if( file_exists($CAMINHO . $ProdutosImagens->imagem) && $ID > 0) {
			// 	if( ! unlink($CAMINHO . $ProdutosImagens->imagem) ) {
			// 	}
			// }

			$capa = $ID == 0 && $Produto->capa->id == 0 && $k == 0 ? 1 : 0;
			ProdutosImagens::action_cadastrar_editar([
				'ProdutosImagens' => [
					$ID => [
						'codigo_id' => $CODIGO_ID,
						'cor_id' => $COR_ID,
						'imagem' => $filename,
						'capa' => $capa
					]
				]
			]);

			/**
			 * Carregar a imagem no upload
			 */
			$WideImageTmpName = WideImage\WideImage::load($IMG['tmp_name'][$k]);
			$WideImage960x960 = $WideImageTmpName->resize(960, 960);
			$WideImage280x280 = $WideImageTmpName->resize(280, 280);
			// $WideImage480x480 = $WideImageTmpName->resize(480, 480);
			// $WideImage315x315 = $WideImageTmpName->resize(240, 240);

			/**
			 * Carregar quadro da imagem
			 */
			$WideImageSquare = WideImage\WideImage::load( '../public/imgs/_quadro.jpg' );
			$WideImageSquare960x960 = $WideImageSquare->resize(960, 960);
			$WideImageSquare280x280 = $WideImageSquare->resize(280, 280);
			// $WideImageSquare480x480 = $WideImageSquare->resize(480, 480);
			// $WideImageSquare315x315 = $WideImageSquare->resize(240, 240);

			$WideImageSquare960x960->merge($WideImage960x960, 'center', 'center', 92)->saveToFile( $CAMINHO . $filename );
			$WideImageSquare280x280->merge($WideImage280x280, 'center', 'center', 86)->saveToFile( $CAMINHO . 'smalls/' . $filename );
			// $WideImageSquare480x480->merge($WideImage480x480, 'center', 'center', 93)->saveToFile( $CAMINHO . 'medium/' . $NOVO_NOME_IMAGEM );
			// $WideImageSquare315x315->merge($WideImage315x315, 'center', 'center', 89)->saveToFile( $CAMINHO . 'smalls/' . $NOVO_NOME_IMAGEM );

			$WideImageSquare960x960->destroy();
			$WideImageSquare280x280->destroy();
			// $WideImageSquare480x480->destroy();
			// $WideImageSquare315x315->destroy();
			$WideImageSquare->destroy();
		}

		header("Location: /adm/fotos.php?codigo_id={$CODIGO_ID}&cor_id={$COR_ID}");
        return;

	break;
    /**
     * Editar ordens das fotos se necessario
     */
    case 'ordem_foto' :
        ProdutosImagens::action_cadastrar_editar([
            'ProdutosImagens' => [
                $POST['capa_id'] => [
                    'ordem' => (string)$POST['ordem']
                ]
            ]
        ], 'alterar', 'imagem');
        header("Location: /adm/fotos.php?codigo_id={$GET['codigo_id']}&cor_id={$GET['cor_id']}");
		return;
	break;
}

/**
 * Seta uma capa
 */
if( isset( $GET['capa_id'] ) || (int)$GET['capa_id'] > 0 ) {
    ProdutosImagens::query('update produtos_imagens set capa = NULL where codigo_id=? AND cor_id=?', [ (int)$GET['codigo_id'], (int)$GET['cor_id']]);
	ProdutosImagens::action_cadastrar_editar([
        'ProdutosImagens' => [
            $GET['capa_id'] => [
                'capa' => 1
            ]
        ]
    ], 'alterar', 'imagem');
	header("Location: /adm/fotos.php?codigo_id={$GET['codigo_id']}&cor_id={$GET['cor_id']}");
	return;
}

if( isset( $GET['remove_id'] ) && (int)$GET['remove_id'] > 0) {
	$CAMINHO = URL_VIEWS_BASE_PUBLIC_UPLOAD . '/imgs/produtos/';

	$ProdutosImagens = ProdutosImagens::find($GET['remove_id']);

	$map = $CAMINHO . $ProdutosImagens->imagem;

	if( file_exists( $map ) )
		if( unlink( $map ) )
			ProdutosImagens::action_cadastrar_editar(['ProdutosImagens' => [$GET['remove_id'] => ['imagem' => $ProdutosImagens->imagem]]], 'delete', 'imagem');

	header("Location: /adm/fotos.php?codigo_id={$GET['codigo_id']}&cor_id={$GET['cor_id']}");
	return;
}

// if( isset( $GET['remove_id'] ) && (int)$GET['remove_id'] > 0) {
// 	$CAMINHO = URL_VIEWS_BASE_PUBLIC_UPLOAD . '/imgs/produtos/';

// 	$ProdutosImagens = ProdutosImagens::find($GET['remove_id']);

// 	$map = $CAMINHO . $ProdutosImagens->imagem;

// 	if( file_exists( $map ) ) {
// 		if( array_map('unlink', [
// 			$CAMINHO . 'smalls/' . $ProdutosImagens->imagem,
// 			$CAMINHO . 'medium/' . $ProdutosImagens->imagem,
// 			$CAMINHO . $ProdutosImagens->imagem,
// 		]) ) {
// 			ProdutosImagens::action_cadastrar_editar(['ProdutosImagens' => [$GET['remove_id'] => ['imagem' => $ProdutosImagens->imagem]]], 'delete', 'imagem');
// 		}
// 	}

// 	header("Location: /adm/fotos.php?codigo_id={$GET['codigo_id']}&cor_id={$GET['cor_id']}");
// 	return;
// }

$rs = Produtos::first(['conditions' => [ 'codigo_id=? and id_cor=?', (int)$GET['codigo_id'], (int)$GET['cor_id'] ] ]);
// echo $basename = bin2hex(random_bytes(8));
?>

<div class="tag-opcoes clearfix edicao-imagens" id="div-edicao">
	<style>
		.ocultar {
			display: none;
		}
		.form-caixa-imagens {
			border: solid 1px #dedede;
		}
		input[type=file]{
			display: none;
		}
	</style>

	<h2>EDITAR IMAGEM DO PRODUTO</h2>
	<p>
		<?php echo $rs->nome_produto?>
        <?php echo ( $rs->cor->nomecor ? ($rs->cor->opcoes->tipo ? "{$rs->cor->opcoes->tipo}: ": '') . $rs->cor->nomecor : '' )?>
        <?php echo ( $rs->tamanho->nometamanho ? ($rs->tamanho->opcoes->tipo ? "{$rs->tamanho->opcoes->tipo}: ": '') . $rs->tamanho->nometamanho : '' )?>
	</p>
	<small>Aconselhável que o tamanho das fotos seja de até 1200x1200px pixels com até máximo de até 400 Kb</small>
	<div class="clearfix">
		<form class="form-caixa-imagens text-center mt15 mb15 col-md-12 col-sm-12" method="post" enctype="multipart/form-data" action="/adm/fotos.php?codigo_id=<?php echo $GET['codigo_id']?>&cor_id=<?php echo $GET['cor_id']?>">
			<input type="hidden" name="acao" value="cadastrar_editar_imagens">
			<input type="hidden" name="codigo_id" value="<?php echo $GET['codigo_id']?>">
			<input type="hidden" name="cor_id" value="<?php echo $GET['cor_id']?>">
			<input type="hidden" name="id" value="">
			<input type="file" name="foto[]" id="foto-nova" class="fotos-produtos" multiple="multiple"/>
			<label class="btn btn-primary mt15 mb15 open-foto" data-id="foto-nova" <?php echo _P('fotos', $_SESSION['admin']['id_usuario'], 'incluir')?>>adicionar foto</label>
		</form>
		<?php
		$result = ProdutosImagens::all(['conditions' => [ 'codigo_id=? and cor_id=?', (int)$GET['codigo_id'], (int)$GET['cor_id'] ] ]);
		foreach( $result as $f )
		{
			$IMAGEM = file_exists(URL_VIEWS_BASE_PUBLIC_UPLOAD . '/imgs/produtos/' . $f->imagem) ? Imgs::src(sprintf('%s?v=%s', $f->imagem, substr(time(), -3)), 'medium') : Imgs::src('sem-foto-produto.png', 'public');
			?>
			<div class="col-md-3 col-sm-4 mt15 mb15">
				<form class="clearfix text-center form-caixa-imagens" method="post" enctype="multipart/form-data" action="/adm/fotos.php?codigo_id=<?php echo $GET['codigo_id']?>&cor_id=<?php echo $GET['cor_id']?>">
					<input type="hidden" name="acao" value="cadastrar_editar_imagens">
					<input type="hidden" name="codigo_id" value="<?php echo $GET['codigo_id']?>">
					<input type="hidden" name="cor_id" value="<?php echo $GET['cor_id']?>">
					<input type="hidden" name="id" value="<?php echo $f->id?>">
					<img src="<?php echo $IMAGEM?>" class="w100"/>
					<input type="file" name="foto[]" id="foto<?php echo $f->id?>" class="fotos-produtos"/>
					<div style="background-color: #dedede; padding: 5px 0;" class="clearfix ft10px">
						<div class="row">
							<span class="col-md-4" <?php echo _P( 'fotos', $_SESSION['admin']['id_usuario'], 'alterar' )?>>
								CAPA<br/>
								<a href="/adm/fotos.php?capa_id=<?php echo $f->id?>&codigo_id=<?php echo $GET['codigo_id']?>&cor_id=<?php echo (int)$GET['cor_id']?>" class="fa fa-toggle-<?php echo $f->capa ? 'on' : 'off'?> cor-001 fa-2x add-capa"></a>
							</span>
							<span class="col-md-4" <?php echo _P( 'fotos', $_SESSION['admin']['id_usuario'], 'alterar' )?>>
								EDITAR<br/>
								<a href="javascript:void(0);" class="fa fa-folder-open cor-001 fa-2x open-foto" data-id="foto<?php echo $f->id?>"></a>
							</span>
							<span class="col-md-4" <?php echo _P( 'fotos', $_SESSION['admin']['id_usuario'], 'excluir' )?>>
								EXCLUIR<br/>
								<a href="/adm/fotos.php?remove_id=<?php echo $f->id?>&codigo_id=<?php echo $GET['codigo_id']?>&cor_id=<?php echo (int)$GET['cor_id']?>" class="fa fa-times-circle cor-001 fa-2x remove-foto"></a>
							</span>
							<!--
							<span class="col-md-12 mt5 text-left">
								ORDEM<br/>
								<input type="text" name="ordem" value="<?php echo $f->ordem?>" style="width: 55px"/>
							</span>
							-->
						</div>
					</div>
				</form>
			</div>
		<?php } ?>
	</div>
</div>
<?php
include 'rodape.php';
