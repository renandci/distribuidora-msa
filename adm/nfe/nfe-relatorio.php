<?php

use NFePHP\NFe\Common\Standardize;

include_once dirname(__DIR__) . '/topo.php';

$emitentes = NfeEmitentes::all(['conditions'=>['loja_id=?',$CONFIG['loja_id']]]);
?>
<style>
	body{ 
		background-color: #f1f1f1 
	}
	fieldset {
		-webkit-border-radius: 5px;
		-moz-border-radius: 5px;
		border-radius: 5px;
		border-color: #cdcdcd;
		border-style: solid;
		border-width: 1px;
	}
</style>
<div class="row">
	<div class="container">
		<form action="/adm/nfe/nfe-relatorio-imprimir.php" method="post" class="panel panel-default mt50" target="_blank">
			<div class="panel-heading panel-store text-uppercase">Relatórios de Notas</div>
			<div class="panel-body">
				<div class="col-md-12 mb15">
					<fieldset>
						<legend class="bold">Dados do Emitente</legend>
						<div class="clearfix">
							<label>Emitente:</label>
							<select name="id_emitentes" class="w100">
							<?php foreach( $emitentes as $r ) { ?>
								<option value="<?php echo $r->id;?>">
									<?php echo $r->razaosocial?>
								</option>
							<?php } ?>
							</select>
						</div>
					</fieldset>
				</div>
				<div class="col-md-4">
					<fieldset>
						<legend class="bold">NF-e do Emitidas/Canceladas</legend>
						<div class="clearfix">
							<label>Selecione:</label>
							<select name="status" class="w100">
								<option value="">Selecione...</option>
								<option value="1" selected>Emitidas</option>
								<option value="2">Canceladas</option>
							</select>
						</div>
					</fieldset>
				</div>
				<div class="col-md-4">
					<fieldset>
						<legend class="bold">NF-e Detalhada</legend>
						<div class="clearfix">
							<label>Mostrar Detalhes:</label>
							<select name="is_detalhed" class="w100">
								<option value="">Selecione...</option>
								<option value="1">Mostar produtos</option>
								<option value="0" selected>Mostar sem produtos</option>
							</select>
						</div>
					</fieldset>
				</div>
				<div class="col-md-4">
					<fieldset>
						<legend class="bold">Periodo</legend>
						<div class="row">
							<div class="col-md-6">
								<label>Data inicial:</label>
								<input type="text" name="date_ini" class="w100 text-right datepicker" value="<?php echo date('01/m/Y')?>"/>
							</div>
							<div class="col-md-6">
								<label>Data final:</label>
								<input type="text" name="date_fin" class="w100 text-right datepicker" value="<?php echo date('t/m/Y')?>"/>
							</div>
						</div>
					</fieldset>
				</div>
				<div class="col-md-12 text-center mt15">
					<button type="submit" class="btn btn-primary">imprimir</button>
				</div>
			</div>
		</form>
	</div>
</div>
<?php
include_once dirname(__DIR__) . '/rodape.php';