<?php
	if(count($POST) > 0 or count($GET) > 0)
	{
		session_start();
		include "../db.php";
		include "bibli-funcoes.php";
		
		$buscaComentarios = "
				select 
					comen.titulocomentario,
					comen.comentario,
					date_format( comen.data, '%d-%m-%Y') as data,
					cli.nome,
					cliend.cidade,
					cliend.uf,
					comen.nota
				from produtos_comentarios comen 
					left join clientes cli on cli.id = comen.id_cliente
					left join clientes_enderecos cliend on cli.id = cliend.id_cliente
				where comen.id_produto = {$POST['ID']} and comen.ativo = 1 group by comen.id order by comen.id desc limit 0, 20
			";
			
			$SqlComentarios = mysqli_query($conexao, $buscaComentarios );
			
			$TotalComentarios = mysqli_fetch_assoc( mysqli_query($conexao, "select count(id) as total from produtos_comentarios where id_produto = {$POST['ID']} and ativo = 1" ) );
			
			$MediaNotaProduto = mysqli_fetch_assoc( mysqli_query($conexao, "select round( avg( nota ),1 ) as media from produtos_comentarios where id_produto = {$POST['ID']} and ativo = 1" ) );
			$MEDIA = ($MediaNotaProduto['media']) ? $MediaNotaProduto['media'] : 0;
			?>		
			<div class="tag-centro">
				<h2><?php echo $TotalComentarios['total'];?> Avaliações</h2>
				<div style="font-size: 2em; line-height: 20px;" class="clearfix">
					<span class="pull-left">Nota <?php echo $MEDIA; ?></span> <span class="notas-estrelas pull-left" style="<?php	media_produto( $MEDIA )?>"></span>
				</div>
				<div class="mt20">
					<button type="button" class="btn btn-criar-comentario white" onclick="javascript:$('.form-comentario,.abre-absoluta').fadeIn(0);">CRIAR UM COMENTÁRIO</button>
				</div>
				<ul class="tag-comentario mt20">
				<?php
				while( $rCom = mysqli_fetch_assoc( $SqlComentarios ) )
				{
				?>				
					<li class="mt5 mb5 clearfix model-radius" style='border-bottom: dotted 1px #aaa;'>
						<div class="pull-left ids-clientes">
							<span class="show notas-estrelas" style="<?php	media_produto( $rCom['nota'] )?>"></span>
							<span class="show">por <span class="bold"><?php echo $rCom['nome'];?></span></span>
							<span class="show">de <?php echo $rCom['cidade'];?> - <?php echo $rCom['uf'];?></span>
							<span class="show"><?php echo $rCom['data'];?></span>
						</div>
						
						<div class="pull-right ids-comentarios">
							<p class="bold"><?php echo $rCom['titulocomentario'];?></p>
							<div class="textarea-comentario model-radius"><?php echo nl2br( $rCom['comentario'] );?></div>
						</div>
					</li>
				<?php
				}
				mysqli_free_result( $SqlComentarios );
				?>
				</ul>
			</div>
		<?php
		die;
	}