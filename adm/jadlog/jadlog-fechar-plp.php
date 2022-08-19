<?php
require_once '../topo.php';
?>
	<style>body{ background-color: #f1f1f1; }</style>
	<div id="reload_plp" class="panel panel-default">
		<div class="panel-heading panel-store">Fechar PLP JadLog</div>
		<div class="panel-body">
			<table width="100%" border="0" cellpadding="10" cellspacing="0">
				<tbody>
					<?php if( ! empty( $GET['acao'] ) && $GET['acao'] == 'lista_etiquetas' ) { ?>
					<tr class="plano-fundo-adm-001 cor-branco">
						<td nowrap="nowrap" width="1%" bgcolor="ffffff">
							<input type="checkbox" name="etiquetas_all" id="e_all"/>
							<label for="e_all" class="input-checkbox"></label>
						</td>
						<td>Cliente</td>
						<td>Código venda</td>
						<td>Data venda</td>
						<td>Frete</td>
						<td nowrap="nowrap" width="1%">Código etiqueta</td>
					</tr>
					<?php
					$sql = '' 
						. 'select correios_etiquetas.*, pedidos.codigo, pedidos.data_venda, pedidos.frete_tipo, clientes.nome ' 
						. 'from correios_etiquetas ' 
						. 'inner join pedidos on correios_etiquetas.id_pedidos = pedidos.id ' 
						. 'inner join clientes on clientes.id = pedidos.id_cliente ' 
						. 'where correios_etiquetas.id_plp = 0 and correios_etiquetas.id > 0';
						
					$result = Lojas::find_by_sql( $sql );
					
					foreach( $result as $rs ) { $rs = $rs->to_array(); ?>
					<tr class="lista-zebrada in-hover">
						<td nowrap="nowrap" width="1%">
							<input type="checkbox" name="etiquetas[]" id="e_<?php echo $rs['id']?>" value="<?php echo $rs['id']?>"/>
							<label for="e_<?php echo $rs['id']?>" class="input-checkbox"></label>
						</td>
						<td nowrap="nowrap" width="1%">
							<?php echo $rs['nome']?>
						</td>
						<td>
							<?php echo $rs['codigo']?>
						</td>
						<td>
							<?php echo date('d/m/Y', strtotime($rs['data_venda']))?>
						</td>
						<td nowrap="nowrap" width="1%">
							<?php echo $rs['tipo_frete']?>
						</td>
						<td>
							<?php echo mask( $rs['etiqueta'], '##########'. $rs['dv'] .'##' ); ?>
						</td>
					</tr>
					<?php } ?>
					<?php } else { ?>
					<!--[ MOSTRA PLP ]-->
					<tr class="plano-fundo-adm-001 cor-branco">
						<td align="center">Data</td>
						<td>Total</td>
						<td nowrap="nowrap" width="1%" align="center">Ações</td>
					</tr>
					<?php
					$maximo = 25;	

					$pag = ! empty( $GET['pag'] ) && $GET['pag'] > 0 ? (INT)$GET['pag'] : 1;

					$inicio = (($pag * $maximo) - $maximo);
					
					$sql = '' 
						. 'select *, (select count(id) from jadlog_etiqueta a where date_format(a.created_at, "%Y%m%d")=date_format(jadlog_etiqueta.created_at, "%Y%m%d")) as total ' 
						. 'from jadlog_etiqueta ' 
						. 'where id > 0 '
						. 'group by date_format(created_at, "%Y%m%d") '
						. 'order by id desc ';
					
					$total = ceil(Lojas::find_num_rows($sql) / $maximo);

					$sql .= '' 
						. sprintf('limit %u ', $maximo) 
						. sprintf('offset %u', ( ($maximo * ($pag - 1)) )); 

					if( $total == 0 ) { ?>
					<tr class="text-center">
						<td class="ft20px" colspan="4">Nenhuma PLP gerada ate o momento</td>
					</tr>
					<?php }
					
					$result = Lojas::query( $sql );
					
					while( $rs = $result->fetch() ) { ?>
						<tr class="lista-zebrada in-hover">
							<td nowrap="nowrap" width="1%"><?php echo date('d/m/Y', strtotime($rs['created_at']))?></td>
							<td><?php echo $rs['total']?></td>
							<td nowrap="nowrap" width="1%">
								<a href="/adm/jadlog/jadlog-print.php?date_group=<?php echo date('Ymd', strtotime($rs['created_at']))?>&imprimir_tipo=etiquetas_a4" class="btn btn-warning btn-sm" target="_blank">
									<i class="fa fa-print"></i>
									imprimir as etiquetas
								</a> 
								<a href="/adm/jadlog/jadlog-imprimir.php?date_group=<?php echo date('Ymd', strtotime($rs['created_at']))?>&imprimir_tipo=plp" class="btn btn-primary btn-sm" target="_blank">
									<i class="fa fa-print"></i>
									imprimir a plp
								</a>
							</td>
						</tr>
					<?php } ?>
					<!--[ END MOSTRA PLP ]-->
					<?php } ?>
					<tr>
						<td colspan="4">
							<div class="paginacao paginacao-add">
								<?php  if( $total > 0 ) { ?>
									
									<?php if( $pag > 1 ) { ?>
										<a href="/adm/jadlog/jadlog-fechar-plp.php?pag=<?php echo ($pag - 1)?>" class="fa fa-chevron-left" ajax></a>
									<?php } ?>
									
									<?php for( $i = $pag - 2, $limiteDeLinks = $i + 4; $i <= $limiteDeLinks; ++$i ) { 
										if($i < 1) {
											$i = 1;
											$limiteDeLinks = 3;
										}

										if($limiteDeLinks > $total) {
											$limiteDeLinks = $total; 
											$i = $limiteDeLinks - 4;
										}

										if($i < 1) {
											$i = 1;
											$limiteDeLinks = $total;
										}
										?>
										
										<?php if($i == $pag) { ?>
											<span class="at plano-fundo-adm-001"><?php echo $i?></span>
										<?php } else { ?>
											<a href="/adm/correios/correios-fechar-plp.php?pag=<?php echo $i?>" ajax><?php echo $i?></a>
										<?php } ?>
										
									<?php } ?>
									
									<?php if( $pag != $total ) { ?>
										<a href="/adm/correios/correios-fechar-plp.php?pag=<?php echo ($pag + 1)?>" class="fa fa-chevron-right" ajax></a>
									<?php } ?>
									
								<?php } ?>	
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<?php
	ob_start();
	?>
	<script>
	
		$.ajaxSetup({
			error: function(x,t,m) {
				console.log(x.resonseText+"\n"+t+"\n"+m); 
			}
		});
		
		func_gerar_plp = function(  ){
			var checkbox = $.param(JanelaModal.find("input[name=\"etiquetas[]\"]:checked"));
			if(!checkbox){ alert("selecione ao menos um pedido!"); return false; }
			
			$.ajax({
				url: window.location.href,
				type: "post",
				data: checkbox + "&acao=fechar_plp",
				success: function( str ) { 
					var list = $("<div/>", { html : str });
					JanelaModal.html( list.find("#pre").html() );
					$("#reload_plp").html( list.find("#reload_plp").html() );
				}
			});
			
			console.log(checkbox);
			return false;
		};
		
		JanelaModal.on("click", "#e_all", function() {
			if( ! $(this).is(":checked") ) {
				JanelaModal.find("input[name=\"etiquetas[]\"]").prop("checked", false);
			} else {
				JanelaModal.find("input[name=\"etiquetas[]\"]").prop("checked", true);
			}
			console.log( ($(this).is(":checked") !== false) );
		});
		
		// $("#plp_fechar").click(function(){
			// var checkbox = $.param($("#reload_plp input[name=\"etiquetas[]\"]:checked"));
			// if(!checkbox){ alert("selecione ao menos um pedido!"); return false; }
			
			// $("#reload_plp input[name=\"etiquetas[]\"]:checked").each( function(a,b) {
				// var link_open = $( b ).val();
			// });
			// console.log(link_open);
			// return false;
		// });
		
		$("#plp_fechar").click(function( e ){
			e.preventDefault();
			$.ajax({
				url: this.href||e.target.href,
				beforeSend: function() {
					JanelaModal.dialog({
						title: "Fechar PLPs",
						buttons: {
							"Fechar os Pedidos": func_gerar_plp,
							"Cancela" : function(){
								$(this).dialog("close").find("input[name=\"etiquetas[]\"]").prop("checked", "false");
							}
						}
					});
				},
				success: function( str ) { 
					var list = $("<div/>", { html : str });
					JanelaModal.dialog("open").html( list.find("#reload_plp").html() );
				}
			});
		});
		
		$("#reload_plp").on("click", "", function(){
			
		});
	</script>
	<?php
	$SCRIPT['script_manual'] .= ob_get_clean();
	
	?>
<?php
require_once '../rodape.php';