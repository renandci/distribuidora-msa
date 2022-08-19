<div class="mt15 container">
	<div class="row">
		<ol class="breadcrumb">
			<li>
				<a href="/" <?php echo $modulo != 'index' ? ' class="active"':''?>>
					<i class="fa fa-home"></i> Inicial
				</a>
			</li>

			<?php if( ! empty( $BREACRUMB['grupo_id'] ) ){ ?>
			<li>
				<a href="/produtos/<?php echo converter_texto($BREACRUMB['grupo']) . '/' . $BREACRUMB['grupo_id']?>"
					<?php echo !empty($BREACRUMB['subgrupo_id']) > 0 ? ' class="active"':''?>>
					<?php echo $BREACRUMB['grupo']?>
				</a>
			</li>
			<?php } ?>
			
			<?php if( ! empty( $BREACRUMB['subgrupo_id'] ) && empty( $BREACRUMB['nome_produto'] ) ){ ?>
			<li>
				<a href="#" <?php echo !empty($BREACRUMB['subgrupo_id']) > 0 ? ' class="active"':''?>>
					<?php echo $BREACRUMB['subgrupo']?>
				</a>
			</li>
			<?php } ?>
			
			<?php if( ! empty( $BREACRUMB['subgrupo_id'] ) && ! empty( $BREACRUMB['nome_produto'] ) ) { ?>
			<li>
				<a href="<?php echo ((!empty($BREACRUMB['subgrupo_id']) > 0) && $BREACRUMB['nome_produto']) ? '/produtos/' .converter_texto($BREACRUMB['grupo']) . '/' . $BREACRUMB['grupo_id'] . '/' . converter_texto($BREACRUMB['subgrupo']) . '/' . $BREACRUMB['subgrupo_id'] :'javascript:void(0);';?>"
					<?php echo ((!empty($BREACRUMB['subgrupo_id']) > 0) && $BREACRUMB['nome_produto']) ? ' class="active"':''?>>
					<?php echo $BREACRUMB['subgrupo']?>
				</a>
			</li>                            
			<?php } ?>

			<?php if(!empty($BREACRUMB['pesquisar'])){ ?>
			<li>
				<a href="#">
					<i class="fa fa-search"></i> <?php echo $BREACRUMB['pesquisar']?> 
				</a>
			</li>
			<?php } ?>

			<?php if(!empty($BREACRUMB['nome_produto'])){ ?>
			<li>
				<?php echo $BREACRUMB['nome_produto']?> 
			</li>
			<?php } ?>
		</ol>
	</div>
</div>