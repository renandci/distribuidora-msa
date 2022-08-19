<?php
include '../topo.php';

// Envio de lembrete via api web whatsapp
if(isset($POST['phone'], $POST['text']) && strlen(soNumero($POST['phone'])) === 13 ) {
	$uri = '' 
		 . 'https://' . ($MobileDetect->isMobile() ? 'api':'web') . '.whatsapp.com/send?' 
		 . urldecode(http_build_query($POST, '', '&'));
		 
	header('location: ' . $uri);
	return;
}

// Envia um lembretinho para o cliente se a forma de pagamento for boleto
if( isset( $GET['acao'] ) && $GET['acao'] == 'EnviarLembrete' ) {

    $rws = Pedidos::connection()->query(sprintf('select id, data_venda, codigo, status from pedidos where id=%u', (INT)$GET['id']))->fetch();

    $text_status = text_status_vendas($rws['status']);

    $descricao 	= "Lembrete de pagamento Via {$rws['forma_pagamento']} do pedido: {$rws['codigo']}<br />Status atual: $text_status";
    
    PedidosLogs::logs($rws->id, $_SESSION['admin']['id_usuario'], $descricao, $rws['status']);   

	LembretePagamentoBoletoTransferencia($GET['id']);
	header('location: /adm/vendas/vendas.php');
	return;
}

?>
<style>
	body{ background-color: #f1f1f1 }
</style>
<div id="div-edicao" class="panel panel-default">
	<div class="panel-heading panel-store text-uppercase">Vendas</div>
	<div class="panel-body">
		<form id="buscar-infomacoes-vendas" action="/adm/vendas/vendas.php" class="row">
			<div class="form-group col-lg-1 col-md-2 col-sm-6 col-xs-6">
				<label>Valor:</label>
				<input type="text" name="valorEstimado" class="form-control preco-mask text-right" value="<?php echo $valor_estimado != '' ? number_format($valor_estimado,2,',','.') : '0,00';?>" />
			</div>
			<div class="form-group col-lg-2 col-md-2 col-sm-6 col-xs-6">
				<label>Digite o número do pedido:</label>
				<input type="text" name="codigoVenda" class="form-control" value="<?php echo $codigo;?>"/>
			</div>
			<div class="form-group col-lg-2 col-md-2 col-sm-6 col-xs-6">
				<label>Data Inicial:</label>
				<input type="text" name="dataInicial" class="datepicker form-control" value="<?php echo $data_inicial;?>"/>
			</div>
			<div class="form-group col-lg-2 col-md-2 col-sm-6 col-xs-6">
				<label>Data Final:</label>
				<input type="text" name="dataFinal" class="datepicker form-control" value="<?php echo $data_final;?>"/>
			</div>
			<div class="form-group col-lg-3 col-md-3 col-sm-6 col-xs-6">
				<label>Status:</label>
				<select name="status[]" id="status" class="form-control" multiple="">
					<?php
					$Status = Pedidos::all(['conditions' => ['excluir=? and loja_id=?', 0, $CONFIG['loja_id']], 'group' => 'status' ]);
					foreach ( $Status as $stts ) { ?>
						<option value="<?php echo $stts->status?>">
							<?php echo text_status_vendas( $stts->status )?>
						</option>
					<?php } ?>
				</select>
			</div>
			<div class="form-group col-lg-2 col-md-2 col-sm-6 col-xs-6">
				<label>Formas de Envios:</label>				
				<select name="envios[]" id="Envios" class="form-control" multiple="" size="1">
					<?php
					$Frete = Pedidos::all(['conditions' => ['excluir=? and loja_id=?', 0, $CONFIG['loja_id']], 'group' => 'frete_tipo' ]);
					foreach ( $Frete as $frete_tipo ) { ?>
						<option value="<?php echo $frete_tipo->frete_tipo?>">
							<?php echo $frete_tipo->frete_tipo?>
						</option>
					<?php } ?>
				</select> 
			</div>
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"></div>
			<div class="form-group col-lg-5 col-md-5 col-sm-6 col-xs-6">
				<label for="clientesId">Clientes:</label>
				<select name="clientesId[]" id="clientesId" class="form-control" multiple="">
					<?php
					$CATCOR = 'A';
					echo '<optgroup label="'.$CATCOR.'">';
					$resultClientes = Pedidos::all([
						'conditions' => ['pedidos.excluir=? and pedidos.loja_id=?', 0, $CONFIG['loja_id']], 
						'group' => 'pedidos.id_cliente', 
						'order' => 'clientes.nome asc', 
						'joins' => ['cliente'] 
					]);
					foreach ( $resultClientes as $rsClientes ) {
						if( $CATCOR != StringLetra($rsClientes->cliente->nome) ){
							$CATCOR = StringLetra($rsClientes->cliente->nome);
							
							echo '</optgroup><optgroup label="'.$CATCOR.'">';
						}
						echo '<option value="' . $rsClientes->id_cliente . '">' . $rsClientes->cliente->nome .  '</option>';
					}
					echo '</optgroup>';
					?>
				</select>
			</div>
			<div class="form-group col-lg-2 col-md-2 col-sm-6 col-xs-6">
				<label>Formas de Pgto:</label>				
				<select name="pgto[]" id="Pgto" class="form-control" multiple="" size="1">
					<?php
					$result_pgto = Pedidos::all(['conditions' => ['excluir=? and loja_id=?', 0, $CONFIG['loja_id']], 'group' => 'forma_pagamento' ]);
					foreach ( $result_pgto as $forma_pagamento ) { ?>
						<option value="<?php echo $forma_pagamento->forma_pagamento?>">
							<?php echo $forma_pagamento->forma_pagamento?>
						</option>
					<?php } ?>
				</select> 
			</div>
			<div class="form-group col-lg-2 col-md-2 col-sm-6 col-xs-6">
				<label>Nr NF-e:</label>
				<input type="text" name="chavenfe" class="form-control" value="<?php echo $chavenfe;?>"/>
			</div>
			<button type="submit" class="btn btn-primary pull-left mt25 ml15">pesquisar</button>
			<?php echo !empty( $GET['acao'] ) ? '<a href="/adm/vendas/vendas.php" class=" btn btn-danger mt25 white ml5">limpar <i class="fa fa-close"></i></a>' : ''?>
			<input type="hidden" value="PesquisarVenda" name="acao">
		</form>
		<style>.table>thead>tr>th, .table>tbody>tr>th, .table>tfoot>tr>th, .table>thead>tr>td, .table>tbody>tr>td, .table>tfoot>tr>td { vertical-align: middle; }</style>
		<table class="table table-striped">
			<thead>
				<tr class="plano-fundo-adm-001 cor-branco">
					<!-- <th nowrap="nowrap" width="1%">Info.</th> -->
					<th nowrap="nowrap" width="1%">Nr. Pedidos</th>
					<th align="center">Data compra</th>
					<th>Clientes</th>
					<th nowrap="nowrap" width="1%">Nr. NF-e</th>
					<th align="center">Valor compra</th>
					<th align="center">Envio - Valor</th>
					<th align="center" nowrap="nowrap" width="1%">Pgto</th>
					<th align="center" colspan="2">Status</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$i		= 0;
				$maximo = 25;
				$pag 	= isset($GET['pag']) && $GET['pag'] != '' ? (int)$GET['pag'] : 1;

				$conditions['select'] = 'pedidos.*, clientes.nome, clientes.email, clientes.telefone, clientes.celular, nfe_notas.chavenfe, nfe_notas.status as nfestatus ';
				$conditions['order'] = 'pedidos.id desc';
				$conditions['group'] = 'pedidos.id';
				$conditions['joins'] = ['cliente', 'LEFT JOIN nfe_notas ON (nfe_notas.id_pedido = pedidos.id and (nfe_notas.status in(1, 3)))'];
				$conditions['conditions'] = sprintf('pedidos.loja_id=%u and pedidos.excluir=0 ', $CONFIG['loja_id']);
				
				if(!empty( $GET['codigoVenda'] ) && $GET['codigoVenda'] != '') {
					$conditions['conditions'] .= sprintf('and pedidos.codigo like "%%%s%%" ', $GET['codigoVenda']);
				}
				
				if(!empty( $GET['clientesId'] ) && count($GET['clientesId']) > 0 ) {
					$array = [];
					foreach($GET['clientesId'] as $clientesId)
						if( (int)$clientesId > 0 )
							$array[] = $clientesId;
					
					if(!empty($array))
						$conditions['conditions'] .= sprintf('and pedidos.id_cliente IN(%s) ', implode(',', $array));
				}
				
				if(!empty( $GET['status'] ) && $GET['status'] != '') {
					$conditions['conditions'] .= sprintf('and pedidos.status IN("%s") ', implode('", "', $GET['status']));
				}
				
				if(!empty( $GET['envios'] ) && $GET['envios'] != '') {
					$conditions['conditions'] .= sprintf('and pedidos.frete_tipo IN("%s") ', implode('", "', $GET['envios']));
				}
				
				if(!empty( $GET['chavenfe'] ) && $GET['chavenfe'] != '') {
					$conditions['conditions'] .= sprintf('and nfe_notas.chavenfe like "%s" ', $GET['chavenfe']);
				}
				
				if(!empty( $GET['pgto'] ) && $GET['pgto'] != '') {
					$conditions['conditions'] .= sprintf('and pedidos.forma_pagamento IN("%s") ', implode('", "', $GET['pgto']));	
				}
				
				if((!empty( $GET['dataInicial'] ) && $GET['dataInicial'] != '') || !empty( $GET['dataFinal'] ) && $GET['dataFinal'] != '') {
					$GET['dataInicial'] = !empty($GET['dataInicial']) ? sprintf("%s 00:00:00", converterDatas($GET['dataInicial'])) : date('Y-m-01 00:00:00');
					$GET['dataFinal'] = !empty($GET['dataFinal']) ? sprintf("%s 23:59:59", converterDatas($GET['dataFinal'])) : date('Y-m-t 23:59:59');
					
					$conditions['conditions'] .= sprintf('and pedidos.data_venda between "%s" and "%s" ', $GET['dataInicial'], $GET['dataFinal']);
				}
				
				if(!empty( $GET['valorEstimado'] ) && dinheiro($GET['valorEstimado']) > 0 ) {
					$conditions['conditions'] .= sprintf('and pedidos.valor_compra like "%%%s%%" ', dinheiro($GET['valorEstimado']));
				}
				
				$Pedidos = Pedidos::all($conditions);
				
				$total = ceil(count($Pedidos) / $maximo);
				
				$conditions['limit'] = $maximo;
				$conditions['offset'] = ($maximo * ($pag - 1));

				if( $total > 0 ) {
					$result = Pedidos::all($conditions);
					foreach ( $result as $rs ) 
					{ 					
						$TOTAL = valor_pagamento($rs->valor_compra, $rs->frete_valor, $rs->desconto_cupom, '$', $rs->desconto_boleto);
						?>
						<tr style="<?php 
							if (!empty($rs->chavenfe) && $rs->nfestatus == 3 ) {
								echo('background-color: #fcf8e3!important;');
							}
							if (!empty($rs->chavenfe) && $rs->nfestatus == 1 ) {
								echo('background-color: #dff0d8!important;');
							}
							else{
								if (($i % 2) == 0){
								 echo('background-color: #f3f3f3');
								}
							}
							?>" class="formulario<?php echo $rs->id;?>">
							
							<td nowrap="nowrap" width="1%" align="center" class="bold">
								<?php echo $rs->codigo;?>
							</td>
							<td align="center" nowrap="nowrap" width="1%">
								<?php echo $rs->data_venda->format('d/m/Y H:i');?>
							</td>
							<td>
								<a class="analisar-cliente"data-toggle="tooltip" title="Histórico do Cliente" href="/adm/clientes/clientes-analisar.php?acao=analisar&id=<?php echo $rs->id_cliente?>"><?php echo $rs->nome;?></a>
							</td>
							<td nowrap="nowrap" width="1%" align="center">
								<?php echo ($rs->chavenfe != '' ? substr($rs->chavenfe, -18, 8) : '--');?>
							</td>
							<td align="center" nowrap="nowrap" width="1%">
								<font color='#a20000' class="ft22px bold">R$ <?php echo number_format($TOTAL['TOTAL_COMPRA_C_BOLETO'],2,',','.');?></font>
							</td>
							<td align="center" nowrap="nowrap" width="1%">
								<?php echo implode(' | ', [$rs->frete_tipo, '<font color="#a20000">R$ ' . number_format($rs->frete_valor,2,',','.'). '</font>'])?>
							</td>
							<td align="center" nowrap="nowrap" width="1%">
								<?php
								$data = array_filter([$rs->forma_pagamento, $rs->cartao, $rs->parcelas]);
								echo implode(' | ', $data);
								if($rs->forma_pagamento == 'Boleto') {

									$LogCount = count($rs->pedidos_logs);

									$DateNow = date('Y-m-d H:i:s');
									$DateVen = date('Y-m-d H:i:s', strtotime(sprintf("+%u days", $CONFIG['pagamentos']['boleto_venc']), strtotime($rs->data_venda)));
									$DateCurrent = floor((strtotime($DateVen) - strtotime($DateNow)) / (60 * 60 * 24));
									
									if( $DateCurrent >= 0 && in_array($rs->status, [1,2,4,5,10]) ) {
										echo $DateCurrent == 0 
											? '<br/><span class="badge" style="background-color: #be00ff">vence hoje</span>' 
												: sprintf(" | Faltam %s dia(s)", $DateCurrent); ?>
										<a href='/adm/vendas/vendas.php?acao=EnviarLembrete&id=<?php echo $rs->id?>' class='mt5 btn btn-xs btn-lembrete btn-block btn<?php echo ($LogCount > 1 ? '-danger':'-primary')?>' id='lembrete<?php echo $rs->id?>' data-tel="55<?php echo strlen(soNumero($rs->telefone)) == 11 ? soNumero($rs->telefone) : soNumero($rs->celular) ?>">
											enviar lembrete!
										</a>
										<?php
									} 
									else if( $DateCurrent <= 0 && in_array($rs->status, [1,2,4,5,10]) ) {
										echo '<br/><span class="badge" style="background-color: #ab0000">vencido</span>';
									}
								}
								?>							
							</td>
							<td align="center" nowrap="nowrap" width="1%"><img src="<?php echo Imgs::src("status-{$rs->status}", 'status')?>.png" width="55"/></td>
							<td align="center" nowrap="nowrap" width="1%">
								<a href='/adm/vendas/vendas-detalhes.php?id=<?php echo $rs->id?>' <?php echo _P('vendas-detalhes', $_SESSION['admin']['id_usuario'], 'acessar')?> class="btn btn-success btn-xs"><i class="fa fa-globe ft16px"></i> mais<br/>detalhes</a>
								<?php 
								echo isset($rs->pedido_off) && $rs->pedido_off > 0 ? 
										' <a '
										. 'href="/adm/vendas/vendas-detalhes.php?id=' . $rs->pedido_off . '&off=true" '
										. 'class="fa fa-external-link fa-2x ml5"'
										. 'title="Produto com Nova Tentativa de Pagamento" '
										. 'target="_blank"'
										. _P('vendas-detalhes', $_SESSION['admin']['id_usuario'], 'acessar')
										.'></a>':'';
								?>
							</td>
						</tr>
						<?php
						++$i;
					}
					?>
					<tr>
						<td colspan="10">
							<div class="paginacao paginacao-add">
								<?php
                                for($i = $initial = max(1, $pag - 4), $l = min($initial + 8, $total); $i <= $l; $i++) :
                                    if($pag == $i) :
                                        echo sprintf('<span class="at plano-fundo-adm-001">%s</span>', $i); 
                                    else :
                                        $data = http_build_query(array_replace($GET, ['pag' => $i]));
                                        echo sprintf('<a href="/adm/vendas/vendas.php?%s">%s</a>', $data, $i);
                                    endif;
                                endfor;
								?>
							</div>
						</td>
					</tr>
				<?php
				}
				else
				{
				?>
				<tr>
					<td colspan="7">
						<h2>Não há resultado para sua pesquisa.</h2>
					</td>
				</tr>
				<?php
				}
				?>
			</tbody>
		</table>
	</div>
</div>
<?php ob_start(); ?>
<script src='https://maps.googleapis.com/maps/api/js?v=3&key=AIzaSyCarGFF_WSsunQec6-H-yF9dPgh2kCL_dM'></script>
<?php $SCRIPT['bibliotecas'] .= ob_get_clean()?>
<?php ob_start(); ?>
<script>
	$.widget('custom.catcomplete', $.ui.autocomplete, {
        _create: function() {
          this._super();
          this.widget().menu( 'option', 'items', '> :not(.ui-autocomplete-category)' );
        },
        _renderMenu: function( ul, items ) {
          var that = this,
            currentCategory = '';
          $.each( items, function( index, item ) {
            var li;
            if ( item.category != currentCategory ) {
              ul.append( '<li class=\"ui-autocomplete-category\">' + item.category + '</li>' );
              currentCategory = item.category;
            }
            li = that._renderItemData( ul, item );
            if ( item.category ) {
              li.attr( 'aria-label', item.category + ' : ' + item.label );
            }
          });
        }
    });
	
    $("#div-edicao").on("click", ".btn-lembrete", function(e){
        e.preventDefault();
		var data_tel = $(this).attr("data-tel");
        $.ajax({
            url: this.href,
            cache: false,
			complete: function(){},
            beforeSend : function(){
				JanelaModal
					.dialog({title:"Envio de Lembrete",autoOpen: true, width: 400, height: 275})
					.html("Aguarde...<br/>Enviando um lembrete de pagamento via Boleto.");
			},
            success: function(str){ 
                var list = $("<div/>", { html: str });
				$("#div-edicao").html( list.find("#div-edicao").html() );
				JanelaModal.dialog({title: "Lembrete via Whatsapp Web", height: "auto"}).html([
					$("<form/>", {
						method: "post",
						class: "no-action",
						action: "/adm/vendas/vendas.php",
						target: "_blank",
						html: [
							$("<div/>", {
								class: "form-group",
								html: [
									$("<label>", { html: "Número do cliente", class: "ft11px" }),
									$("<input/>", { type: "tel", class: "form-control", name: "phone", value: data_tel})
								]
							}),
							$("<div/>", {
								class: "form-group",
								html: [
									$("<label>", { html: "Digite sua mensagem!", class: "ft11px" }),
									$("<input/>", { type: "text", class: "form-control", name: "text"})
								]
							}),
							$("<button/>", { 
								class: "btn btn-primary", 
								html: "Enviar", 
								click: function(){
									$(this).delay(500).queue(function(e){
										JanelaModal.dialog("close").html("");
										e();
									})
								} 
							})
						]
					})
				]);
            },
            error: function(x,t,m){ 
				console.log(x.responseText+'\\n'+t+'\\n'+m); 
			}
        });
    });
	
	$("#div-edicao").on("click", ".analisar-cliente", function(e){
        e.preventDefault();
        $.ajax({
            url: this.href||e.target.href,
            dataType: "html",
            success: function( str ){ 
                var list = $("<div/>", { html: str });

                JanelaModal.html( list.find("#div-edicao").html() ).dialog({ 
					autoOpen: true,
					title: "Clientes Analisar/Alterar"
				}).css({ "background-color": "#f1f1f1" });
			},
			complete: function() {
				$("a.ui-dialog-titlebar-maximize").trigger("click");
			},
            error: function(x,t,m){ 
                console.log( x.responseText+"\n"+t+"\n"+m ); 
            }
        }); 
    });

    var SPMaskBehavior = function (val) {
        return val.replace(/\D/g, '').length === 11 ? "(00) 00000-0000" : "(00) 0000-00009";
    };
    var spOptions = {
        onKeyPress: function(val, e, field, options) {
            field.mask(SPMaskBehavior.apply({}, arguments), options);
        }
	};
	
	function busca_cidade( a, b ) {
        var cep = a, 
            id=b.target.id;

        $.ajax({
            url: "../../",
            type: "post",
            data: { acao: "BuscaCidade", cep: cep },
            dataType: "json",
            beforeSend: function() {
                JanelaModal.find("#cidade"+id).val( "Carregando..." );
                JanelaModal.find("#uf"+id).val( "" );
            }, 
            success: function( str ) {
                JanelaModal.find("#cidade"+id).val( str.cidade );
                JanelaModal.find("#uf"+id).val( str.uf );
            }, 
            error: function( x,m,t ){ 
                alert( x.responseText ); 
            }
        });
    }
    JanelaModal.find("input[name=cep]").mask("00000-000", { onComplete : busca_cidade });
    JanelaModal.find('input[name=data_nascimento]').mask('99 / 99 / 9999');
    JanelaModal.find('input[name=telefone]').mask(SPMaskBehavior, spOptions);
    JanelaModal.find('input[name=celular]').mask(SPMaskBehavior, spOptions);

    JanelaModal.on('click', 'input[type=radio]', function(e) {
        if( $(this).val() === 'true' ) {
            $( '#conteudos-recarregar' ).find('input[name=q]').mask('999.999.999-99');
        } else {
            $( '#conteudos-recarregar' ).find('input[name=q]').unmask('');
        }

        /**
         * Nesse if vai passar somente datas
         */
        if($(this).val() === 'data') {
            $('#ocultar-datas').fadeIn(10);
        } else {
            $('#ocultar-datas').fadeOut(0);
        }			
    });
	$('[data-toggle="tooltip"]').tooltip();
</script>
<?php
$SCRIPT['script_manual'] .= ob_get_clean();

include '../rodape.php';