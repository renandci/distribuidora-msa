<?php
include '../topo.php';
?>

<style>
	body{ background-color: #f1f1f1 }
</style>

<div class="row">
	<form action="/adm/relatorio/relatorio-vendas-mensal-print.php" method="post" target="_blank" class="col-lg-8 col-lg-offset-2 col-md-8 col-md-offset-2 col-sm-12 col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading panel-store text-uppercase">RELATÓRIOS DE VENDAS MENSAL - AGRUPADO POR MÊS</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-lg-3 col-md-3 col-sm-6 col-xs-6 mb15">
						<div>
							<span class="show bold-3">Selecione o ano:</span>
							<input type="text" name="data_ini" min="2015" max="2099" step="1" value="2021" style="width: 150px;" />
						</div>
					</div>
					<div class="mt20 mb20 pull-left w100 text-center  mb15">
						<button type="submit" class="btn btn-primary w30">
							<i class="fa fa-print ft20px"></i> 
							<span class="ft20px">imprimir</span>
						</button>
						<input type="hidden" name="acao" value="imprimir"/>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>
	
<script>
    <?php ob_start(); ?>
	// $( ".datepicker" ).click(function() {
	// 	alert( "teste." );
	// });
	$( ".teste" ).datepicker({
     changeMonth: false,
     changeYear: true,
     yearRange: "1930:2022"
	});

   <?php
   $SCRIPT['script_manual'] .= ob_get_clean();
   ?> 
</script>
<?php
include '../rodape.php';