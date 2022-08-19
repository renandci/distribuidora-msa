<?php
include '../topo.php';
?>

<style>
	body{ background-color: #f1f1f1 }
</style>

<div class="row">
	<form action="/adm/relatorio/relatorio-mais-vendidos-print.php" method="post" target="_blank" class="col-lg-8 col-lg-offset-2 col-md-8 col-md-offset-2 col-sm-12 col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading panel-store text-uppercase">Relatórios de Produtos mais Vendidos</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
						<div>
							<span class="show bold-3">Data Inicial:</span>
							<span class="show"><input type="text" name="dataInicial" class="datepicker w80" autocomplete="off"/></span>
						</div>
						<div class="mt5 ft13px bold-3">
							<input type="checkbox" id="11" name="produtos" value="true" CHECKED />
							<label for="11" class="input-checkbox"></label> MOSTRAR PRODUTOS
						</div>
					</div>
					<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
						<div>
							<span class="show bold-3">Data Final:</span>
							<span class="show"><input type="text" name="dataFinal" class="datepicker w80" autocomplete="off"/></span>
						</div>
						<div class="mt5 ft13px bold-3">
							<input type="checkbox" id="12" name="cores" value="true" CHECKED />
							<label for="12" class="input-checkbox"></label> MOSTRAR CORES
						</div>
					</div>
					<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
						<div>
							<span class="show bold-3">Exibir Por:</span>
						</div>
						<div class="ft13px bold-3">
							<select name="exibir" class="w80">
								<option value="M">Marca</option>
								<option value="P">Produto</option>										
							</select>
						</div>
						<div class="mt5 ft13px bold-3">
							<input type="checkbox" id="13" name="tamanhos" value="true" CHECKED />
							<label for="13" class="input-checkbox"></label> MOSTRAR TAMANHOS
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading panel-store text-uppercase">
				<input type="checkbox" id="0" name="pedidos[]" value="20"/>
				<label for="0" class="input-checkbox"></label> IMPRIMIR TODOS PEDIDOS
			</div>
			<div class="panel-body">
				<div class="row ft12px">
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 selecionarTodos">
						<div class="mb5">
							<input type="checkbox" id="3" name="pedidos[]" value="3"/>
							<label for="3" class="input-checkbox"></label> PEDIDO PAGAMENTO APROVADO
						</div>
						<div class="mb5">
							<input type="checkbox" id="6" name="pedidos[]" value="6"/>
							<label for="6" class="input-checkbox"></label> PEDIDO EM PRODUÇÃO
						</div>
						<div class="mb5">
							<input type="checkbox" id="7" name="pedidos[]" value="7"/>
							<label for="7" class="input-checkbox"></label> PEDIDO EM SEPARAÇÃO DE ESTOQUE
						</div>
						<div class="mb5">
							<input type="checkbox" id="8" name="pedidos[]" value="8"/>
							<label for="8" class="input-checkbox"></label> PEDIDO EM TRANSPORTE
						</div>
						<div class="mb5">
							<input type="checkbox" id="12" name="pedidos[]" value="12"/>
							<label for="12" class="input-checkbox"></label> AGUARDANDO RETIRADA
						</div>
						<div class="mb5">
							<input type="checkbox" id="9" name="pedidos[]" value="9"/>
							<label for="9" class="input-checkbox"></label> PEDIDO ENTREGUE
						</div>
					</div>
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 selecionarTodos">				
						<div class="mb5">
							<input type="checkbox" id="1" name="pedidos[]" value="1"/>
							<label for="1" class="input-checkbox"></label> PEDIDO REALIZADO 
						</div>
						<div class="mb5">
							<input type="checkbox" id="11" name="pedidos[]" value="11"/>
							<label for="11" class="input-checkbox"></label> PAGAMENTO EM ANÁLISE
						</div>
						<div class="mb5">
							<input type="checkbox" id="2" name="pedidos[]" value="2"/>
							<label for="2" class="input-checkbox"></label> PEDIDO AGUARDANDO PAGAMENTO
						</div>
						
						<div class="mb5">
							<input type="checkbox" id="4" name="pedidos[]" value="4"/>
							<label for="4" class="input-checkbox"></label> PEDIDO PAGAMENTO NÃO APROVADO
						</div>
						<div class="mb5">
							<input type="checkbox" id="5" name="pedidos[]" value="5"/>
							<label for="5" class="input-checkbox"></label> PEDIDO PAGAMENTO NÃO EFETUADO
						</div>
						
						<div class="mb5">
							<input type="checkbox" id="10" name="pedidos[]" value="10"/>
							<label for="10" class="input-checkbox"></label> PEDIDO CANCELADO
						</div>
					</div>
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 selecionarTodos mt15">
						<button type="submit" class="btn btn-primary center-block col-lg-3" style="float: none">
							<i class="fa fa-print fa-2x"></i> 
							<span class="ft25px">imprimir</span>
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
	// $( '.datas' ).datepicker({
	   // dateFormat: 'dd/mm/yy',
	   // dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado','Domingo'
		   // ],
	   // dayNamesMin: [
	   // 'D','S','T','Q','Q','S','S','D'
	   // ],
	   // dayNamesShort: [
	   // 'Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'
	   // ],
	   // monthNames: [  'Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro',
	   // 'Outubro','Novembro','Dezembro'
	   // ],
	   // monthNamesShort: [
	   // 'Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set',
	   // 'Out','Nov','Dez'
	   // ],
	   // nextText: 'Próximo',
	   // prevText: 'Anterior'
	// });
	$('.selecionarTodos input[type=checkbox]').click(function(){
		$('input[type=checkbox][id=0]').attr({'checked':false});
		$('label[for=0]').css({'background-position':'-2px -2px'});
		if($(this).attr('checked'))
		{
			$('input[type=checkbox][id='+this.id+']').attr({'checked':false});
			$('label[for='+this.id+']').css({'background-position':'-2px -2px'});
		}
		else
		{
			$('input[type=checkbox][id='+this.id+']').attr({'checked':true});
			$('label[for='+this.id+']').css({'background-position':'-50px -2px'});
		}
	});

	$('input[type=checkbox][id=0]').click(function(){
		if($(this).attr('checked'))
		{
			$(this).attr({'checked':false});
			$('label[for='+this.id+']').css({'background-position':'-2px -2px'});
			$('.selecionarTodos input[type=checkbox]').attr({'checked':false});
			$('.selecionarTodos label').css({'background-position':'-2px -2px'});
		}
		else
		{
			$(this).attr({'checked':true});
			$('label[for='+this.id+']').css({'background-position':'-50px -2px'});
			$('.selecionarTodos input[type=checkbox]').attr({'checked':true});
			$('.selecionarTodos label').css({'background-position':'-50px -2px'});
		}
	});
	<?php 
	$SCRIPT['script_manual'] .= ob_get_clean(); 
	?>
</script>
<?php
	include '../rodape.php';