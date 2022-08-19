<?php
// use NFePHP\NFe\Tools;
// use NFePHP\NFe\Convert;
// use NFePHP\NFe\Complements;
// use NFePHP\Common\Certificate;
use NFePHP\NFe\Common\Standardize;
// use NFePHP\Common\Soap\SoapCurl;

include_once dirname(__DIR__) . '/topo.php';

$id_usuario = $_SESSION['admin']['id_usuario'];

// Data extendida pra o nr lote
$idLote = substr(str_pad(date('YmdHi0s'), 15, '0', STR_PAD_BOTH), 0, 15);
$NfeEmitentes = NfeEmitentes::first(['conditions' => ['loja_id=?', $CONFIG['loja_id']]]);
$dir = sprintf('%sassets/%s/xml/', PATH_ROOT, ASSETS);
$filename = '%s%s.xml';
?>
<style>body{ background-color: #f1f1f1 }</style>
<div id="reload_nfe" class="panel panel-default mt50">
		<?php if( empty($GET['acao']) ) { ?>
		<div class="panel-heading panel-store text-uppercase clearfix">
			Enviar NFE <a href="/adm/nfe/nfe-emitir.php?acao=lista_nfes" class="btn btn-success pull-right ml5" id="nfe_enviar">enviar nfe</a>
		</div>
		<?php } ?>
		<?php if( isset( $GET['error'] ) && $GET['error'] != null ) { ?>
			<div class="alert alert-info ft12px"><?php echo $GET['error'];?></div>
		<?php } ?>
		<table width="100%" border="0" cellpadding="10" cellspacing="0">
			<tbody>
				<?php if(!empty($GET['acao']) && $GET['acao'] == 'lista_imprimir') { ?>
					<tr class="lista-zebrada in-hover" id="nfe_<?php echo $rs->id?>" data-each="loop">
						<td nowrap="nowrap" width="1%">Cod. Venda</td>
						<td>Chave</td>
						<td nowrap="nowrap" width="1%">Ações</td>
					</tr>
					<?php 
					$NfeNotas = NfeNotas::all(['conditions' => ['id_lote=? AND nrprot IS NOT NULL OR nrprot = "" AND nrreccan IS NULL AND status in(1, 3)', $GET['id_lote']], 'order'=>'id ASC']);
					foreach( $NfeNotas as $rs ) { ?>
						<tr class="lista-zebrada in-hover" id="nfe_<?php echo $rs->id?>" data-each="loop">
							<td><?php echo !empty($rs->skyhub->cod_venda) ? soNumero($rs->skyhub->cod_venda) : $rs->pedido->codigo?></td>
							<td><?php echo $rs->chavenfe?></td>
							<td nowrap="nowrap" width="1%">
								<a href="/adm/nfe/nfe-imprimir.php?id_nota=<?php echo $rs->id?>" target="_blank" class="btn-xs btn btn-warning">
									<i class="fa fa-print"></i> 
									imprimir
								</a>
							</td>
						</tr>
					<?php } ?>
				<?php } elseif( ! empty( $GET['acao'] ) && $GET['acao'] == 'lista_nfes' ) { ?>
					<tr class="plano-fundo-adm-001 cor-branco">
						<th nowrap="nowrap" width="1%" bgcolor="ffffff">
							<input type="checkbox" name="idnfes_all" id="e_all"/>
							<label for="e_all" class="input-checkbox"></label>
						</th>
						<th>Chave</th>
						<th nowrap="nowrap" width="1%">Nr Nota</th>
						<th>Cliente</th>
						<th nowrap="nowrap" width="1%">Cód. Venda</th>
						<!-- <th nowrap="nowrap" width="1%">Data Venda</th> -->
					</tr>
					<?php 
					$NfeNotas = NfeNotas::all(['conditions' => ['loja_id=? AND status in(1, 3) AND nrprot = ""', $CONFIG['loja_id'] ], 'order' => 'id asc', 'group' => 'DATE_FORMAT(created_at, "%m.%Y"), id']);
					foreach( $NfeNotas as $rs ) { ?>
						<tr class="lista-zebrada in-hover" id="nfe_<?php echo $rs->id?>" data-each="loop">
							<td nowrap="nowrap" width="1%">
								<input type="checkbox" name="idnfes[]" id="e_<?php echo $rs->id?>" value="<?php echo $rs->id?>"/>
								<label for="e_<?php echo $rs->id?>" class="input-checkbox"></label>
							</td>
							<td nowrap="nowrap" width="1%">
								<?php echo $rs->chavenfe?> <?php echo $rs->status == 3 ? '<span class="badge">nota de devolução</span>':null?>
								<span class="show ft11px" style="white-space: normal;">
									<?php echo $rs->motivo?>
								</span>
							</td>
							<td align="center"><?php echo (substr($rs->chavenfe, -18, 8) * 1)?></td>
							<td><?php echo !empty($rs->skyhub->nome_cliente) ? $rs->skyhub->nome_cliente : $rs->pedido->cliente->nome?></td>
							<td><?php echo !empty($rs->skyhub->cod_venda) ? soNumero($rs->skyhub->cod_venda) : $rs->pedido->codigo?></td>
						</tr>
						<?php if( ! empty ( $rs->motivo ) ) { ?>
						<tr>
							<td colspan="10">
								<form id="formulario_<?php echo $rs->id?>" method="post" enctype="multipart/form-data" action="/adm/nfe/nfe-upload.php">
									<a href="/adm/nfe/nfe.php?id_pedido=<?php echo $rs->id_pedido?>" target="_blank" class="btn btn-xs btn-warning btn-nfe">
										<i class="fa fa-edit"></i> corrigir nf-e
									</a>
									<a href="/adm/nfe/nfe-download.php?f=<?php echo sprintf($filename, $dir, $rs->chavenfe)?>" target="_blank" class="btn btn-xs btn-info">
										<i class="fa fa-download"></i> Fazer Download do XML
									</a>
									<input type="hidden" name="id" value="<?php echo $rs->id?>"/>
									<!-- <input name="xml" type="file" id="xml_<?php echo $rs->id?>" style="display: none;" accept="text/xml"/> -->
									<!-- <label for="xml_<?php echo $rs->id?>" class="btn btn-xs btn-success">Fazer upload do XML</label> -->
									<span class="visualizar show"></span>
								</form>
							</td>
						</tr>
						<?php } ?>
						<!-- <textarea data-init="0" id="textarea-<?php echo $rs->id?>"><?php echo (file_get_contents(sprintf($filename, $dir, $rs->chavenfe)))?></textarea> -->
					<?php } ?>
				<?php } else { ?>
					<!--[ MOSTRA NFE ]-->
					<tr class="plano-fundo-adm-001 cor-branco">
						<td>Nr Lote</td>
						<td class="text-center">Data Envio</td>
						<td nowrap="nowrap" width="1%" class="text-center">Total de Nfe</td>
						<td nowrap="nowrap" width="1%" class="text-center">Ações</td>
					</tr>
					<?php
					$maximo = 50;
					$pag = ! empty( $GET['pag'] ) && $GET['pag'] > 0 ? (int)$GET['pag'] : 1;

					$inicio = (($pag * $maximo) - $maximo);
					
					$sql = '' 
						. 'select nfe_notas.*, (select count(b.id) from nfe_notas b where b.status IN(1,3) and b.id_lote = nfe_notas.id_lote group by b.id_lote) as total ' 
						. 'from nfe_notas ' 
						. sprintf('where nfe_notas.id_lote > 0 and nfe_notas.loja_id=%u and nfe_notas.status IN(1,3) ', $CONFIG['loja_id'])
						. 'group by nfe_notas.id_lote '
						. 'order by nfe_notas.id_lote desc ';
					
                    $totalAll = (int)Lojas::query( $sql )->rowCount();
                    
					$totalPages = ceil($totalAll / $maximo);

					// $sql .= '' 
					// 	. sprintf('limit %u ', $maximo) 
					// 	. sprintf('offset %u', ( ($maximo * ($pag - 1)) )); 
					$sql .= '' 
						. sprintf('limit %u, %u ', $inicio, $maximo); 

					if( $totalPages == 0 ) { ?>
					<tr class="text-center">
						<td class="ft20px" colspan="4">Nenhuma NFE gerada ate o momento</td>
					</tr>
					<?php }
					
					$result = Lojas::query( $sql );
					
					while( $rs = $result->fetch() ) { ?>
						<?php
						// Y    m  d  H  i  0 s
						// 0123 45 67 89 01 2 34
						// 2021 01 12 18 01 0 19
						$str = $rs['id_lote'];
						$data = sprintf('%s/%s/%s %s:%s', 
							$str[6].$str[7], 
								$str[4].$str[5], 
									$str[0].$str[1].$str[2].$str[3], 
										$str[8].$str[9], 
											$str[10].$str[11]);
						
						?>
						<tr class="lista-zebrada in-hover">
							<td><?php echo $rs['nrrec']?></td>
							<td nowrap="nowrap" width="1%" class="text-center"><?php echo $data?></td>
							<td nowrap="nowrap" width="1%" class="text-center"><?php echo $rs['total']?></td>
							<td nowrap="nowrap" width="1%" class="text-center">
								<a href="/adm/nfe/nfe-emitir.php?id_lote=<?php echo $rs['id_lote']?>&acao=lista_imprimir" data-btn="btn-print" class="btn btn-info btn-xs" target="_blank">
									<i class="fa fa-eye"></i> 
									visualizar nfes
								</a>
							</td>
						</tr>
					<?php } ?>
					<!--[ END MOSTRA NFE ]-->
				<?php } ?>
				<tr>
					<td colspan="10">
						<div class="paginacao paginacao-add">
							<?php  if( $totalPages > 0 ) { ?>
								
								<?php if( $pag > 1 ) { ?>
									<a href="/adm/nfe/nfe-emitir.php?pag=<?php echo ($pag - 1)?>" class="fa fa-chevron-left" ajax></a>
								<?php } ?>
								
								<?php for( $i = $pag - 1, $limiteDeLinks = $i + 2; $i <= $limiteDeLinks; ++$i ) { 
									if($i < 1) {
										$i = 1;
										$limiteDeLinks = 2;
									}

									if($limiteDeLinks > $totalPages) {
										$limiteDeLinks = $totalPages; 
										$i = $limiteDeLinks - 2;
									}

									if($i < 1) {
										$i = 1;
										$limiteDeLinks = $totalPages;
									}
									?>
									
									<?php if($i == $pag) { ?>
										<span class="at plano-fundo-adm-001"><?php echo $i?></span>
									<?php } else { ?>
										<a href="/adm/nfe/nfe-emitir.php?pag=<?php echo $i?>" ajax><?php echo $i?></a>
									<?php } ?>
									
								<?php } ?>
								
								<?php if( $pag != $totalPages ) { ?>
									<a href="/adm/nfe/nfe-emitir.php?pag=<?php echo ($pag + 1)?>" class="fa fa-chevron-right" ajax></a>
								<?php } ?>
								
							<?php } ?>	
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<?php
	ob_start();
	?>
	<script>
		// JanelaNfe = $("<form/>", {
		// 	id: "reload_nfe"
		// });
		JanelaNFe = $("<div/>", { id: "janela-nfe" }).dialog({
			title: "Gerar Nota Fiscal (NFE-e)",
			autoOpen: false,
			width: 800,
			height: 532,
			modal: true,        
		}).dialogExtend({
			"maximizable" : true,
			"dblclick" : "maximize",
			"icons" : { "maximize" : "ui-icon-arrow-4-diag" }
		}).css({"overflow-x": "hidden"});
		
		$.ajaxSetup({
			error: function(x, t, m) {
				if(x && x.status !== 200) {
					this.retries = 0;
					x.abort();
					$("#status-alteracao")
						.stop()
							.html("Erro de comunicação com o servidor.")
									.fadeIn(10)
										.delay(3000)
											.queue(function(ex){
												$(this).fadeOut(0);
								ex();
					});
				}

				if( m === 'timeout' ) {
					console.log("Conexão cancel", this.timeout, this.data);
					$("#status-alteracao")
						.stop()
							.html("Estabelecendo um nova conexão, aguarde.")
									.fadeIn(10)
										.delay(3000)
											.queue(function(ex){
												$(this).fadeOut(0);
								ex();
					});
				}
			}
		});
		
		func_gerar_nfe = function( e ) {
			var checkbox = $.param(JanelaModal.find("input[name=\"idnfes[]\"]:checked"));
			if(!checkbox){ alert("selecione ao menos um pedido!"); return false; }
			
			$.when(
				$.ajax({
					url: "/adm/nfe/nfe-assinar.php",
					type: "post",
					data: checkbox + "&acao=EmitirNfe",
					retries: 3,
					timeout: 15000,
					retryInterval: 15000,
					beforeSend: function() {
						JanelaModal.html([
							$("<h3/>", {html: ["Enviando NF-e, aguarde", $("<i/>", { class: "fa fa-spinner fa-spin fa-1x fa-fw" })], class: "text-center"}),
						]).parents().find(".ui-dialog-buttonset").fadeOut(0)
					}
				}),
				$.ajax({
					url: "/adm/nfe/nfe-emitir.php",
					retries: 3,
					timeout: 20000,
					retryInterval: 20000
				})
			).then(function(EmissaoNfe, ReloadPage) {
				var ListEmissaoNfe = $("<div/>", { html : EmissaoNfe });
				var ListReloadPage = $("<div/>", { html : ReloadPage });
				JanelaModal.html(ListEmissaoNfe.find("#reload_nfe").html()).parents().find(".ui-dialog-buttonset").fadeIn(110);
				JanelaModal.dialog("close");
				$("#reload_nfe").html(ListReloadPage.find("#reload_nfe").html());
			});
		};
		
		JanelaModal.on("click", "#e_all", function() {
			if( ! $(this).is(":checked") ) {
				JanelaModal.find("input[name=\"idnfes[]\"]").prop("checked", false);
			} else {
				JanelaModal.find("input[name=\"idnfes[]\"]").prop("checked", true);
			}
		});
		
		$(document).on("click", "#nfe_enviar", function(e) {
			e.preventDefault();
			JanelaModal.dialog({
				open: function(e, ui) {
					$(e.target).parents().find(".ui-dialog-buttonset").find("button").removeClass("ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only ui-state-hover");
				},
				title: "Enviar NFEs",
				buttons: [{
						text: "Enviar Xmls Sefaz",
						class: "btn btn-primary",
						click: func_gerar_nfe
					}, {
						text: "Cancela",
						class: "btn btn-danger",
						click: function() {
							$(this).dialog("close").find("input[name=\"idnfes[]\"]").prop("checked", "false")
						}
					}
				],
			});

			$.ajax({
				url: this.href||e.target.href,
				success: function( str ) { 
					var list = $("<div/>", { html : str });
					JanelaModal.html(list.find("#reload_nfe").html()).dialog("open");
				}
			});
		});
		
		$("#reload_nfe").on("click", "[data-btn='btn-print']", function(e){
			e.preventDefault();
			
			JanelaModal.dialog({title: "Imprimir NFEs",buttons: {}});

			$.ajax({
				url: this.href||e.target.href,
				success: function( str ) { 
					var list = $("<div/>", { html : str });
					JanelaModal.html(list.find("#reload_nfe").html()).dialog("open");
				}
			});
		});

		JanelaModal.on("change", "input[name=xml]", function(e) {
			var form_id = $([null, e.target.id||this.id].join("#")).parent();
			
			$(form_id).ajaxForm({
				// o callback será no elemento com o id #visualizar
				// target: JanelaModal 
				success: function(str) {
					var list = $("<div/>", { html : str });
					JanelaModal.html(list.find("#reload_nfe").html());
				}
			}).submit();
		});

		JanelaModal.on("click", "tr", function(e) {
			var elem = $(this),
				elem_id = elem.next().find("input[name=xml]").attr("id");
				elem.next().fadeIn();
		});

		JanelaModal.on("click", ".btn-nfe", function(e) {
			$.ajax({
				url: e.target.href||this.href,
				success: function( str ){
					var list = $("<div/>", { html: str })
					JanelaNFe.html(list.find("#conteudos-recarregar-filho").html()).dialog({ 
						autoOpen: true,
						open: function() {
							setTimeout(() => { JanelaNFe.find("select[name=id_emitente]").trigger("change"); }, 10);
						} 
					});
				}
			});
			e.preventDefault();
		});

	</script>
	<?php $SCRIPT['script_manual'] .= ob_get_clean();
include_once dirname(__DIR__) . '/rodape.php';