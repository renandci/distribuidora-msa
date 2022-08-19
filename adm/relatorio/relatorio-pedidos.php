<?php
include '../topo.php';
?>

<style>
	body{ background-color: #f1f1f1 }
</style>

<div class="row">
	<form action="/adm/relatorio/relatorio-pedidos-print.php" method="post" target="_blank" class="col-lg-8 col-lg-offset-2 col-md-8 col-md-offset-2 col-sm-12 col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading panel-store text-uppercase">RELATÓRIOS DE PEDIDOS</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 mb15">
						<div>
							<span class="show bold-3">Data Inicial:</span>
							<span class="show"><input type="text" name="data_ini" class="datepicker w100 black-10" autocomplete="off"/></span>
						</div>
					</div>
					<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 mb15">
						<div>
							<span class="show bold-3">Data Final:</span>
							<span class="show"><input type="text" name="data_fin" class="datepicker w100" autocomplete="off"/></span>
						</div>
					</div>
					<!--
					<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 mb15">
						<div>
							<span class="show bold-3">Estado:</span>
						</div>
						<div class="ft13px bold-3">
							<select name="estados" class="w100">
								<option value="">Todos</option>                                        
								<option value="AC">Acre</option>
								<option value="AL">Alagoas</option>
								<option value="AP">Amapá</option>
								<option value="AM">Amazonas</option>
								<option value="BA">Bahia</option>
								<option value="CE">Ceará</option>
								<option value="DF">Distrito Federal</option>
								<option value="ES">Espírito Santo</option>
								<option value="GO">Goiás</option>
								<option value="MA">Maranhão</option>
								<option value="MT">Mato Grosso</option>
								<option value="MS">Mato Grosso do Sul</option>
								<option value="MG">Minas Gerais</option>
								<option value="PA">Pará</option>
								<option value="PB">Paraíba</option>
								<option value="PR">Paraná</option>
								<option value="PE">Pernambuco</option>
								<option value="PI">Piauí</option>
								<option value="RJ">Rio de Janeiro</option>
								<option value="RN">Rio Grande do Norte</option>
								<option value="RS">Rio Grande do Sul</option>
								<option value="RO">Rondônia</option>
								<option value="RR">Roraima</option>
								<option value="SC">Santa Catarina</option>
								<option value="SP">São Paulo</option>
								<option value="SE">Sergipe</option>
								<option value="TO">Tocantins</option>
							</select>
						</div>
					</div>
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"></div>
					<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 mb15">
						<div>
							<span class="show bold-3">Forma pagamento:</span>
						</div>
						<div class="ft13px bold-3">
							<select name="forma_pagamento" class="w100">
								<option value="">Todos</option>
								<?php 
								// $Pedidos = Pedidos::all(['select' => 'upper(forma_pagamento) as forma_pagamento', 'group' => 'forma_pagamento']);
								$Pedidos = Pedidos::all(['select' => 'upper(forma_pagamento) as forma_pagamento', 'group' => 'forma_pagamento', 'conditions' => ['loja_id=?', $CONFIG['loja_id']]]);
								foreach($Pedidos as $pg ) {
									echo sprintf('<option value="%s">%s</option>', $pg->forma_pagamento, $pg->forma_pagamento);
								}
								?>
							</select>
						</div>
					</div>
					-->
					<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 mb15">
						<span class="show bold-3">Marca:</span> 
						<select name="id_marca" class="w100">
							<option value="0">Todas</option>
							<?php
							$Marcas = Marcas::all( [ 'conditions' => ['excluir = 0 and loja_id = ? ', $CONFIG['loja_id']] ] );
							foreach( $Marcas as $rs_m )
							{
								echo "<option value='".$rs_m->id."'>".$rs_m->marcas."</option>";
							}
							?>
						</select>
					</div>
					
					<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 mb15">
						<span class="show bold-3">Ordem:</span> 
						<select name="ordem" class="w100">
							<option value="marca_asc">Marcas (A-Z)</option>
							<option value="marca_desc">Marcas (Z-A)</option>
						</select>
					</div>
					<!--
					<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 mb15">
						<span class="show bold-3">Exibir</span>
						<span class="pull-left w100 mt5">
							<input type="radio" id="14" name="tipo_relatorio" value="V" checked />
							<label for="14" class="input-radio"></label> RELATÓRIO DE VENDAS
						</span>

						<span class="pull-left w100 mt5">
							<input type="radio" id="15" name="tipo_relatorio" value="I"/>
							<label for="15" class="input-radio"></label> COMO NOS CONHECEU
						</span>

						<span class="pull-left w100 mt5">
							<input type="radio" id="16" name="tipo_relatorio" value="F"/>
							<label for="16" class="input-radio"></label> POR FORMA DE PAGAMENTO
						</span>
					</div>
					-->
				</div>
			</div>
		</div>
		<div class="panel panel-default">
		<div class="panel-heading panel-store text-uppercase">STATUS DOS PEDIDOS</div>
			<div class="panel-body">
				<div class="row ft12px">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 borde-3 ft18px">
						<span class="show mb5">
							<input type="checkbox" id="0" name="pedidos[]" value="20"/>
							<label for="0" class="input-checkbox"></label> IMPRIMIR TODOS PEDIDOS
						</span>
						<hr/>
					</div>
					<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 selecionarTodos mb15">
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
							<input type="checkbox" id="12" name="pedidos[]" value="12"/>
							<label for="12" class="input-checkbox"></label> AGUARDANDO RETIRADA
						</div>
						<div class="mb5">
							<input type="checkbox" id="8" name="pedidos[]" value="8"/>
							<label for="8" class="input-checkbox"></label> PEDIDO EM TRANSPORTE
						</div>
						<div class="mb5">
							<input type="checkbox" id="9" name="pedidos[]" value="9"/>
							<label for="9" class="input-checkbox"></label> PEDIDO ENTREGUE
						</div>

					</div>
					<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 selecionarTodos mb15">
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
					<div class="mt20 mb20 pull-left w100 text-center  mb15">
						<button type="submit" class="btn btn-primary w30">
							<i class="fa fa-print ft25px"></i> 
							<span class="ft25px">imprimir</span>
						</button>
						<input type="hidden" name="acao" value="imprimir"/>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>
	
<?php ob_start(); ?>
<script>
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
</script>
<?php 
$SCRIPT['script_manual'] .= ob_get_clean();

include '../rodape.php';