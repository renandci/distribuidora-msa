<?php
require_once '../topo.php';

// Fecha os Pedidos para a plp
if( !empty($POST['acao']) && $POST['acao'] == 'fechar_plp' ) 
{
	echo '<span id="pre">';
	$a = 0;
	$b = 0;
	$c = 0;
	$CorreiosEtiquetas = CorreiosEtiquetas::all(['conditions' => ['id in(?)', $POST['etiquetas']]]);
	foreach(  $CorreiosEtiquetas as $etiquetas )
	{
		// Pedidos Vendas
		if( $etiquetas->pedido->id > 0 )
		{
			$a_alt = 0;
			$a_lar = 0;
			$a_com = 0;
			foreach($etiquetas->pedido->pedidos_vendas as $rs) 
			{
				if( $a_alt < $rs->produto->freteproduto->altura )
					$a_alt = $rs->produto->freteproduto->altura;

				if( $a_lar < $rs->produto->freteproduto->largura )
					$a_lar = $rs->produto->freteproduto->largura;

				if( $a_com < $rs->produto->freteproduto->comprimento )
					$a_com = $rs->produto->freteproduto->comprimento;

				$ResultAll[$a]['a'] = $rs->produto->nome_produto;
				$ResultAll[$a]['codigo'] = $etiquetas->pedido->codigo;
				$ResultAll[$a]['nome'] = $etiquetas->pedido->cliente->nome;
				$ResultAll[$a]['email'] = $etiquetas->pedido->cliente->email;
				$ResultAll[$a]['telefone'] = $etiquetas->pedido->cliente->telefone;
				
				$ResultAll[$a]['endereco'] = $etiquetas->pedido->pedido_endereco->endereco;
				$ResultAll[$a]['numero'] = $etiquetas->pedido->pedido_endereco->numero;
				$ResultAll[$a]['bairro'] = $etiquetas->pedido->pedido_endereco->bairro;
				$ResultAll[$a]['complemento'] = $etiquetas->pedido->pedido_endereco->complemento;
				$ResultAll[$a]['cidade'] = $etiquetas->pedido->pedido_endereco->cidade;
				$ResultAll[$a]['uf'] = $etiquetas->pedido->pedido_endereco->uf;
				$ResultAll[$a]['cep'] = $etiquetas->pedido->pedido_endereco->cep;
				
				$ResultAll[$a]['nrnfe'] = substr((!empty($etiquetas->pedido->nfe_notas->chavenfe) ? $etiquetas->pedido->nfe_notas->chavenfe:null), -18, 8);
				$ResultAll[$a]['quantidade'] = $rs->quantidade;
				$ResultAll[$a]['valor_pago'] = $rs->valor_pago;

				$ResultAll[$a]['altura'] += (float)($rs->produto->freteproduto->altura > 0 ? $rs->produto->freteproduto->altura / $rs->quantidade : 0);
				$ResultAll[$a]['largura'] = $a_lar;
				$ResultAll[$a]['comprimento'] = $a_com;			
				$ResultAll[$a]['peso'] += (float)($rs->produto->freteproduto->peso > 0 ? $rs->produto->freteproduto->peso * $rs->quantidade:0);
				
				$ResultAll[$a]['seguro'] = $etiquetas->seguro;
				$ResultAll[$a]['servico'] = $etiquetas->servico;
				$ResultAll[$a]['etiqueta'] = $etiquetas->etiqueta;
				$ResultAll[$a]['dv'] = $etiquetas->dv;
				$ResultAll[$a]['plp_nr'] = $CorreiosPlp->plp_nr;

				if( ! empty($rs->produto->grid_kits) )
				{
					$b_alt = 0;
					$b_lar = 0;
					$b_com = 0;
					unset($ResultAll[$a]);
					foreach ($rs->produto->grid_kits as $pr ) 
					{
						$c++;
						$ResultAll[$b]['b'] = $pr->produto->nome_produto;
						$ResultAll[$b]['codigo'] = $etiquetas->pedido->codigo;
						$ResultAll[$b]['nome'] = $etiquetas->pedido->cliente->nome;
						$ResultAll[$b]['email'] = $etiquetas->pedido->cliente->email;
						$ResultAll[$b]['telefone'] = $etiquetas->pedido->cliente->telefone;
						
						$ResultAll[$b]['endereco'] = $etiquetas->pedido->pedido_endereco->endereco;
						$ResultAll[$b]['numero'] = $etiquetas->pedido->pedido_endereco->numero;
						$ResultAll[$b]['bairro'] = $etiquetas->pedido->pedido_endereco->bairro;
						$ResultAll[$b]['complemento'] = $etiquetas->pedido->pedido_endereco->complemento;
						$ResultAll[$b]['cidade'] = $etiquetas->pedido->pedido_endereco->cidade;
						$ResultAll[$b]['uf'] = $etiquetas->pedido->pedido_endereco->uf;
						$ResultAll[$b]['cep'] = $etiquetas->pedido->pedido_endereco->cep;
						
						$ResultAll[$b]['nrnfe'] = substr((!empty($etiquetas->pedido->nfe_notas->chavenfe) ? $etiquetas->pedido->nfe_notas->chavenfe:null), -18, 8);
						$ResultAll[$b]['quantidade'] = $rs->quantidade;
						$ResultAll[$b]['valor_pago'] = $rs->produto->preco_promo;
						
						$ResultAll[$b]['altura'] += (float)($pr->produto->freteproduto->altura > 0 ? $pr->produto->freteproduto->altura / $rs->quantidade:0);
						$ResultAll[$b]['largura'] = $b_lar;
						$ResultAll[$b]['comprimento'] = $b_com;					
						$ResultAll[$b]['peso'] += (float)($pr->produto->freteproduto->peso > 0 ? $pr->produto->freteproduto->peso * $rs->quantidade:0);
						
						$ResultAll[$b]['seguro'] = $etiquetas->seguro;
						$ResultAll[$b]['servico'] = $etiquetas->servico;
						$ResultAll[$b]['etiqueta'] = $etiquetas->etiqueta;
						$ResultAll[$b]['dv'] = $etiquetas->dv;
						$ResultAll[$b]['plp_nr'] = $CorreiosPlp->plp_nr;
						$b++;
					}	
				}
				$a++;
			}
		}

		// Pedidos SkyHub
		if( $etiquetas->skyhub_order->id > 0 )
		{
			$c_alt = 0;
			$c_lar = 0;
			$c_com = 0;
			$c = $a;
			foreach($etiquetas->skyhub_order->skyhub_produto as $rs) 
			{
				if( $c_alt < $rs->altura )
					$c_alt = $rs->altura;

				if( $c_lar < $rs->largura )
					$c_lar = $rs->largura;

				if( $c_com < $rs->comprimento )
					$c_com = $rs->comprimento;

				$ResultAll[$c]['c'] = $rs->nome;
				$ResultAll[$c]['codigo'] = $etiquetas->skyhub_order->cod_venda;
				$ResultAll[$c]['nome'] = $etiquetas->skyhub_order->nome_cliente;
				$ResultAll[$c]['email'] = $etiquetas->skyhub_order->email;
				$ResultAll[$c]['telefone'] = $etiquetas->skyhub_order->telefone;
				
				$ResultAll[$c]['endereco'] = $etiquetas->skyhub_order->endereco;
				$ResultAll[$c]['numero'] = $etiquetas->skyhub_order->numero;
				$ResultAll[$c]['bairro'] = $etiquetas->skyhub_order->bairro;
				$ResultAll[$c]['complemento'] = $etiquetas->skyhub_order->complemento;
				$ResultAll[$c]['cidade'] = $etiquetas->skyhub_order->cidade;
				$ResultAll[$c]['uf'] = $etiquetas->skyhub_order->uf;
				$ResultAll[$c]['cep'] = $etiquetas->skyhub_order->cep;
				
				$ResultAll[$c]['nrnfe'] = substr((!empty($etiquetas->skyhub_order->chave_nfe) ? $etiquetas->skyhub_order->chave_nfe:null), -18, 8);
				$ResultAll[$c]['quantidade'] = $rs->quantidade;
				$ResultAll[$c]['valor_pago'] = $rs->valor;

				$ResultAll[$c]['altura'] += (float)($rs->altura > 0 ? $rs->altura / $rs->quantidade : 0);
				$ResultAll[$c]['largura'] = $a_lar;
				$ResultAll[$c]['comprimento'] = $a_com;			
				$ResultAll[$c]['peso'] += (float)($rs->peso > 0 ? $rs->peso * $rs->quantidade:0);
				
				$ResultAll[$c]['seguro'] = $etiquetas->seguro;
				$ResultAll[$c]['servico'] = $etiquetas->servico;
				$ResultAll[$c]['etiqueta'] = $etiquetas->etiqueta;
				$ResultAll[$c]['dv'] = $etiquetas->dv;
				$ResultAll[$c]['plp_nr'] = $CorreiosPlp->plp_nr;
				$c++;
			}
		}
	}
	
	usort($ResultAll, function($a, $b) {
		return $a['codigo'] > $b['codigo'];
	});

	// printf('<pre>%s</pre>', print_r($ResultAll, 1));
	// return;

	foreach( $ResultAll as $rs )
	{
		// DADOS DA ENCOMENDA QUE SERÁ DESPACHADA
		$dimensao = new \PhpSigep\Model\Dimensao();
		$dimensao->setAltura((float)($rs['altura'] > 0 ? $rs['altura'] / $rs['quantidade'] : 0));
		$dimensao->setLargura($rs['largura']);
		$dimensao->setComprimento($rs['comprimento']);
		$dimensao->setDiametro(0);
		$dimensao->setTipo(\PhpSigep\Model\Dimensao::TIPO_PACOTE_CAIXA);
		
		$destinatario = new \PhpSigep\Model\Destinatario();
		$destinatario->setNome($rs['nome']);
		$destinatario->setEmail($rs['email']);
		$destinatario->setTelefone(soNumero($rs['telefone']));
		$destinatario->setLogradouro($rs['endereco']);
		$destinatario->setNumero($rs['numero']);
		if( ! empty( $rs['complemento'] ) )
			$destinatario->setComplemento($rs['complemento']);
		
		$destino = new \PhpSigep\Model\DestinoNacional();
		$destino->setNumeroPedido($rs['codigo']);
		$destino->setBairro($rs['bairro']);
		$destino->setCidade($rs['cidade']);
		$destino->setUf($rs['uf']);
		$destino->setCep(soNumero($rs['cep']));
		if( ! empty( $rs['nrnfe'] ) && $rs['nrnfe'] != null ) {
			$destino->setNumeroNotaFiscal(str_pad($rs['nrnfe'], 9, '0', STR_PAD_LEFT) );
		}		
		
		// Estamos criando uma etique falsa, mas em um ambiente real voçê deve usar o método
		// {@link \PhpSigep\Services\SoapClient\Real::solicitaEtiquetas() } para gerar o número das etiquetas
		$etiqueta = new \PhpSigep\Model\Etiqueta();
		$etiqueta->setEtiquetaSemDv($rs['etiqueta']);
		$etiqueta->setDv($rs['dv']);
		
		// Se não tiver valor declarado informar 0 (zero)
		$servicoAdicional = new \PhpSigep\Model\ServicoAdicional();
		$servicoAdicional->setCodigoServicoAdicional(\PhpSigep\Model\ServicoAdicional::SERVICE_REGISTRO);
		$servicoAdicional->setValorDeclarado(!empty($rs['seguro']) ? $rs['valor_pago'] : 0);
		
		$encomenda = new \PhpSigep\Model\ObjetoPostal();
		$encomenda->setServicosAdicionais(array($servicoAdicional));
		$encomenda->setDestinatario($destinatario);
		$encomenda->setDestino($destino);
		$encomenda->setDimensao($dimensao);
		$encomenda->setEtiqueta($etiqueta);
		$encomenda->setPeso((float)($rs['peso'] > 0 ? $rs['peso'] * $rs['quantidade']:0));
		// $encomenda->setServicoDePostagem(new \PhpSigep\Model\ServicoDePostagem(\PhpSigep\Model\ServicoDePostagem::SERVICE_SEDEX_40096));	
		$encomenda->setServicoDePostagem(new \PhpSigep\Model\ServicoDePostagem( $rs['servico'] ));	
		
		$encomendas[$rs['etiqueta']] = $encomenda;
	}
		
	$date = date('y-m-d H:i:s');
	
	// DADOS DO REMETENTE
	$remetente = new \PhpSigep\Model\Remetente();
	$remetente->setNome($CONFIG['nome_fantasia']);
	$remetente->setTelefone(soNumero($CONFIG['telefone']));
	$remetente->setEmail($CONFIG['email_contato']);
	$remetente->setLogradouro($CONFIG['endereco']);
	$remetente->setNumero($CONFIG['numero']);
	
	if( ! empty( $CONFIG['bairro'] ) )
		$remetente->setBairro($CONFIG['bairro']);
	
	$remetente->setCep(soNumero($CONFIG['cep']));
	$remetente->setCidade($CONFIG['cidade']);
	$remetente->setUf($CONFIG['uf']);

	$remetente->setNumeroContrato( $CONFIG['correios']['numeroContrato'] );;
	$remetente->setCodigoAdministrativo( $CONFIG['correios']['codAdministrativo'] );
	$remetente->setDiretoria((int)$CONFIG['correios']['diretoria']);

	$plp = new \PhpSigep\Model\PreListaDePostagem();
	$plp->setAccessData( $AccessDataCorreios );
	$plp->setEncomendas( $encomendas );
	$plp->setRemetente( $remetente );
	
	$phpSigep = new PhpSigep\Services\SoapClient\Real();
	$ResultPhpSigep = $phpSigep->fechaPlpVariosServicos($plp);
	
	if( is_object($ResultPhpSigep) && $ResultPhpSigep->getErrorMsg() == null ) {
		$ResultPlp = $ResultPhpSigep->getResult();
		
		$ResultIdPlp = $ResultPlp->getIdPlp();
		
		$CorreiosPlp = new CorreiosPlp();
		$CorreiosPlp->id_correios = $CONFIG['correios']['id'];
		$CorreiosPlp->plp_nr = $ResultIdPlp;
		$CorreiosPlpId = $CorreiosPlp->save_log();

		foreach($POST['etiquetas'] as $id_plp) {
			$CorreiosEtiquetas = CorreiosEtiquetas::find($id_plp);
			$CorreiosEtiquetas->id_plp = $CorreiosPlpId['id'];
			$CorreiosEtiquetas->save_log();
		}
		
		header('location: /adm/correios/correios-fechar-plp.php?zuzim=');
		return;		
	} 
	else {
		printf('<h2>Não foi possivel fechar a PLP!</h2><p>%s</p>', $ResultPhpSigep->getErrorMsg());
	}
	echo '</span>';

}
?>
	<style>body{ background-color: #f1f1f1; }</style>	
	<div id="reload_plp" class="panel panel-default">
		<div class="panel-heading panel-store">
			Fechar PLP 
			<a href="/adm/correios/correios-fechar-plp.php?acao=lista_etiquetas" class="btn btn-danger btn-xs pull-right" id="plp_fechar">
				<i class="fa fa-edit"></i> fechar plp
			</a>
		</div>
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
						$conditions['conditions'] = 'correios_etiquetas.id_plp = 0 and correios_etiquetas.id > 0';
						$CorreiosEtiquetas = CorreiosEtiquetas::all($conditions);					
						foreach( $CorreiosEtiquetas as $rs ) { ?>
							<tr class="lista-zebrada in-hover">
								<td nowrap="nowrap" width="1%">
									<input type="checkbox" name="etiquetas[]" id="e_<?php echo $rs->id?>" value="<?php echo $rs->id?>"/>
									<label for="e_<?php echo $rs->id?>" class="input-checkbox"></label>
								</td>
								<td><?php echo !empty($rs->skyhub_order->id) ? $rs->skyhub_order->nome_cliente : $rs->pedido->pedido_cliente->nome?></td>
								<td nowrap="nowrap" width="1%">
									<?php echo !empty($rs->skyhub_order->id) ? $rs->skyhub_order->cod_venda : $rs->pedido->codigo?>
								</td>
								<td nowrap="nowrap" width="1%">
									<?php echo !empty($rs->skyhub_order->id) ? $rs->skyhub_order->created_at->format('d/m/Y') : $rs->pedido->data_venda->format('d/m/Y')?>
								</td>
								<td nowrap="nowrap" width="1%"><?php echo $rs->pedido->frete_tipo?></td>
								<td nowrap="nowrap" width="1%"><?php echo mask($rs->etiqueta, '##########'. $rs->dv .'##');?></td>
							</tr>
						<?php } ?>
					<?php } else { ?>
						<!--[ MOSTRA PLP ]-->
						<tr class="plano-fundo-adm-001 cor-branco">
							<td nowrap="nowrap" width="1%">Cód. etiqueta</td>
							<td align="center">Data</td>
							<td align="center">Total de etiqueta</td>
							<td nowrap="nowrap" width="1%" align="center">Ações</td>
						</tr>
						<?php
						$maximo = 25;
						$pag = ! empty( $GET['pag'] ) && $GET['pag'] > 0 ? (int)$GET['pag'] : 1;
						$inicio = (($pag * $maximo) - $maximo);
						
						$sql = '' 
							. 'select *, (select count(*) from correios_etiquetas where correios_etiquetas.id_plp = correios_plp.id) as total ' 
							. 'from correios_plp ' 
							. 'where id > 0 '
							. 'order by id desc ';
						
						$conditions['order'] = 'id desc';
						$conditions['conditions'] = 'id > 0';
						$CorreiosPlp = CorreiosPlp::all($conditions);

						$CorreiosPlpCount = count($CorreiosPlp);
						$total = ceil($CorreiosPlpCount / $maximo);
		
						$sql .= '' 
							. sprintf('limit %u ', $maximo) 
							. sprintf('offset %u', ( ($maximo * ($pag - 1)) )); 
						
						$conditions['limit'] = $maximo;
						$conditions['offset'] = ($pag - 1) * $maximo;

						if( $total == 0 ) { ?>
						<tr class="text-center">
							<td class="ft20px" colspan="4">Nenhuma PLP gerada ate o momento</td>
						</tr>
						<?php }
						
						$result = CorreiosPlp::all($conditions);
						
						foreach( $result as $rs ) { ?>
							<tr class="lista-zebrada in-hover">
								<td><?php echo $rs->plp_nr?></td>
								<td align="center"><?php echo $rs->created_at->format('d/m/Y')?></td>
								<td align="center"><?php echo count($rs->etiquetas)?></td>
								<td nowrap="nowrap" width="1%">
									<a href="/adm/correios/correios-print.php?id_plp=<?php echo $rs->id?>&imprimir_tipo=etiquetas_a4" class="btn btn-warning btn-sm" target="_blank">
										<i class="fa fa-print"></i>
										imprimir as etiquetas
									</a> 
									<a href="/adm/correios/correios-print.php?id_plp=<?php echo $rs->id?>&imprimir_tipo=plp" class="btn btn-primary btn-sm" target="_blank">
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
										<a href="/adm/correios/correios-fechar-plp.php?pag=<?php echo ($pag - 1)?>" class="fa fa-chevron-left" ajax></a>
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
					JanelaModal.html(list.find("#pre").html());
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
		
		$("#reload_plp").on("click", "#plp_fechar", function( e ){
			e.preventDefault();
			$.ajax({
				url: this.href||e.target.href,
				beforeSend: function() {
					JanelaModal.dialog({
						title: "Fechar PLPs",
						buttons: {
							"Fechar os Pedidos": func_gerar_plp,
							"Cancela" : function() {
								$(this).dialog("close").find("input[name=\"etiquetas[]\"]").prop("checked", "false");
							}
						}
					});
				},
				success: function( str ) { 
					var list = $("<div/>", { html : str });
					JanelaModal.dialog("open").html(list.find("#reload_plp .panel-body").html());
					JanelaModal.find(".panel-store").remove();
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