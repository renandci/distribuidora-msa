<?php
include '../topo.php';
?>

	<div class="panel panel-default">
		<div class="panel-body">
			<h2>Relatório de Estoque</h2>
			
			<form action="/adm/produtos/produtos-relatorio-print.php" method="get" target="_blank">
				Ordenar por: 
				<div class="checkbox">
					<label>
						<input type="radio" name="order" value="nome_produto" id="nome_produto" checked>
						<label for="nome_produto" class="input-radio pull-left"></label>
						<span class="pull-left ml5" style="margin-top: 1px">Nome</span>
					</label>
					<label>
						<input type="radio" name="order" value="codigo_produto" id="codigo_produto">
						<label for="codigo_produto" class="input-radio pull-left"></label>
						<span class="pull-left ml5" style="margin-top: 1px">Código</span>
					</label>
					<label>
						<input type="radio" name="order" value="estoque" id="estoque">
						<label for="estoque" class="input-radio pull-left"></label>
						<span class="pull-left ml5" style="margin-top: 1px">Quantidade</span>
					</label>
				</div>
				<div class="text-center mt10 mb25">
					<button type="submit" class="btn btn-primary">imprimir</button>
				</div>
			</form>
			
		</div>
	</div>
	
	<style>
		body{
			background-color: #efefef;
		}
		.radio label, .checkbox label{
			padding-left: 0;
		}
	</style>
<?php
include '../rodape.php';