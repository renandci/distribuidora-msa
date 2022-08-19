<?php
require_once '../topo.php';

// Remove a etiqueta do pedido 
if( ! empty( $GET['acao'] ) && $GET['acao'] == 'remover_etiquetas' ) {
	
	$CorreiosEtiqutas = CorreiosEtiquetas::find(['conditions' => ['id_pedidos=?', (int)$GET['id']]]);
	$CorreiosEtiqutas->delete_log(['id' => $CorreiosEtiqutas->id]);

	header('location: /adm/vendas/vendas-detalhes.php?id=' . $GET['id']);
	return;
}

// Gera a etiqueta para o pedido
if( ! empty( $GET['acao'] ) && $GET['acao'] == 'gerar_etiquetas' ) {

	echo '<pre>';
	
	$date = date('Y-m-d H:i:s');
	try {
		// Inicio da requisicao
		// Tambem inicia uma quantidade para as etiquetas
		$SolicitaEtiquetas = new \PhpSigep\Model\SolicitaEtiquetas();
		
		
		// Gera a quantidade de etiqueta
		$SolicitaEtiquetas->setQtdEtiquetas((!empty($POST['frete_qtde']) ? (int)$POST['frete_qtde'] : 1));
		$SolicitaEtiquetas->setAccessData($AccessDataCorreios);
		$SolicitaEtiquetas->setServicoDePostagem((string)$POST['frete_servico']);
		
		$phpSigep = new PhpSigep\Services\SoapClient\Real();
		$EtiquetasResult = $phpSigep->solicitaEtiquetas($SolicitaEtiquetas);

		$errorMsg = $EtiquetasResult->getErrorMsg();

		if( !empty($errorMsg) && $errorMsg != null ) {
			throw new Exception($errorMsg);
		}
		
		$PhpSigepParamsDv = new \PhpSigep\Model\GeraDigitoVerificadorEtiquetas();
		$PhpSigepParamsDv->setAccessData($AccessDataCorreios);
		$PhpSigepParamsDv->setEtiquetas($EtiquetasResult->getResult());
		
		$EtiquetasDv = $phpSigep->geraDigitoVerificadorEtiquetas($PhpSigepParamsDv);
		
		$errorMsg2 = $EtiquetasDv->getErrorMsg();

		if( !empty($errorMsg2) && $errorMsg2 != null ) {
			throw new Exception($errorMsg2);
		}
		
		foreach ($EtiquetasDv->getResult() as $etiqueta) {
			$CorreiosEtiquetas = new CorreiosEtiquetas();
			$CorreiosEtiquetas->id_pedidos = $GET['id']; 
			$CorreiosEtiquetas->servico = $POST['frete_servico']; 
			$CorreiosEtiquetas->etiqueta = $etiqueta->getEtiquetaSemDv(); 
			$CorreiosEtiquetas->dv = $etiqueta->getDv(); 
			$CorreiosEtiquetas->seguro = $POST['frete_seguro'];		
			$CorreiosEtiquetas->save_log();
			
			$status = (Pedidos::find($GET['id']))->status;
			$status_text = text_status_vendas($status);
			PedidosLogs::logs($GET['id'], $_SESSION['admin']['id_usuario'], $status_text, $status);
		}
	} 
	catch( Exception $e ) {
		$logs = 'Não foi possivel gerar as etiquetas' . PHP_EOL;
		$logs .= $e->getMessage();
		$status = (Pedidos::find($GET['id']))->status;
		PedidosLogs::logs($id_pedido, $_SESSION['admin']['id_usuario'], $logs, $status);
	}			
	echo '</pre>';
	header('location: /adm/vendas/vendas-detalhes.php?id=' . $GET['id']);
	return;
}

?>
<form method="post" id="janela_etiqueta">
	<div class="clearfix">
		<div class="col-md-4">
			<p class="mb5">QTDE Volume:</p>
			<input name="frete_qtde" class="text-right" value="1" style="width: 55px;">
		</div>
		<div class="col-md-12 mb15"></div>
		<div class="col-md-6">
			<p class="mb5">Serviço de postagem:</p>
			<select name="frete_servico" style="height: 36px; width: 100%;">
				<option value="">Serviço de postagem</option>
				<?php
				$PhpSigepServicoDePostagem = \PhpSigep\Model\ServicoDePostagem::getAll();
				$Servicos = Lojas::find_by_sql('select * from correios_servicos where loja_id=? order by id desc', [ $CONFIG['loja_id'] ]);
				
				foreach($Servicos as $sr) { 
					foreach( $PhpSigepServicoDePostagem as $servicos ) { 
						if(  $servicos->getCodigo() == $sr->servico_int ) {
							$SERVICO = $sr->servico_text;
							$SERVICO_ID = $sr->servico_int;
						}
					} 
					?>
				<option value="<?php echo $SERVICO_ID ?>">
					<?php echo $SERVICO?>
				</option>
				<?php } ?>
			</select>
		</div>
		<div class="col-md-6">
			<p class="mb5">Seguro Adicional:</p>
			<select name="frete_seguro" style="height: 36px; width: 100%;">
				<option value="">Seguro Adicional</option>
				<option value="0" selected>NÃO</option>
				<option value="1">SIM</option>
			</select>
		</div>
		<div class="col-md-12 text-center mb15">
			<hr/>
			<button type="submit" class="btn btn-success">
				gerar etiqueta
			</button>
		</div>
	</div>
</form>
<?php ob_start(); ?>
<script>
	var janela_etiqueta = $("#janela_etiqueta").dialog({
		width: 455, 
		height: "auto",
		autoOpen: false,
		modal: true
	});
	
	// Remove a etiqueta gerada
	$("#correios_reload_buttons").on("click", ".btn_remover_etiquetas", function(e){
		e.preventDefault();
		if( ! confirm('Deseja realmente remover a etiqueta!') ) return false;
		$.ajax({
			url: e.target.href||this.href,
			success: function( str ) {
				var list = $("<div/>", { html: str });
				$("#correios_reload_buttons").html( list.find("#correios_reload_buttons").html() );
			}
		});
	});
	
	
	// Gera um nova etiqueta
	$("#correios_reload_buttons").on("click", ".btn_gerar_etiquetas", function(e){
		e.preventDefault();
		var NrPed = $(e.target).attr("data-nr"),
			HrefPed = $(e.target).attr("href");
		janela_etiqueta
			.dialog({"title": "Gerar etiqueta - Ped.: " + NrPed})
			.dialog("open")
			.attr({ "action": HrefPed });
	});
	
	janela_etiqueta.on("submit", function(e) {
		e.preventDefault();
		var ActionPed = $(e.target).attr("action"),
			frete_qtde = $(e.target).find("input[name=frete_qtde]").val(),
			frete_servico = $(e.target).find("select[name=frete_servico]").val(),
			frete_seguro = $(e.target).find("select[name=frete_seguro]").val(),
			input_data = $(e.target).serialize();
		
		if( frete_qtde === 0 || frete_qtde === "" ){
			alert("Digite a quantidade de pacotes do pedido!");
			return false;
		}
		if( frete_servico === "" ){
			alert("Selecione o serviço de envio do pedido!");
			return false;
		}
		if( frete_seguro === "" ){
			alert("Selecione o seguro do pedido!");
			return false;
		}
		
		$.ajax({
			url: ActionPed,
			type: "POST",
			data: input_data,
			success: function( str ) {
				var list = $("<div/>", { html: str });
				$("#correios_reload_buttons").html( list.find("#correios_reload_buttons").html() );
				console.log( list.find("pre").html() );
			}, 
			error: function(a,b,c){
				console.log(a.resonseText+"\n"+b+"\n"+c);
			},
			complete: function( ) {
				janela_etiqueta.dialog("close");
			}
		});
	});
</script>
<?php
$SCRIPT['script_manual'] .= ob_get_clean();

require_once '../rodape.php';