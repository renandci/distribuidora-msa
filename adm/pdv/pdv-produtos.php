	<div class="panel panel-default">
		<div class="panel-heading panel-store neo-sans-medium text-uppercase">
			Venda Rápida
		</div>
		<div class="panel-body">
			<form class="row" id="search_produtos">
				<input type="hidden" name="acao" value="prod_add"/>
				<div class="form-group col-sm-9 col-xs-12">
					<label>Pesquisar Produtos</label>
					<select name="prod_id" class="form-control select_no_init" id="input_select_prod" tabindex="0"></select>
				</div>
				<div class="form-group col-sm-1 col-xs-12">
					<label>QTDE</label>
					<input name="prod_qtde" type="number" class="form-control text-right" value="1" tabindex="1" id="prod_qtde">
				</div>
				<div class="form-group col-sm-2 col-xs-12">
					<button type="submit" class="btn btn-primary mt25 btn-block" tabindex="2" id="btn_adicionar_prod"><i class="fa fa-plus"></i> adicionar</button>
				</div>
			</form>
			<div class="table-responsive bg-warning" style="overflow-y: auto; min-height: 375px" id="result_products">
			<?php
			$Cart = Carrinho::all(['conditions' => ['id_session = ? ', sha1($_SESSION['admin']['id_usuario'])], 'order' => 'id desc']);
			if( (is_array($Cart) ? count($Cart) : 0) > 0 ) { ?>
				<table class="table table-condensed text-uppercase table-hover ft12px">
					<thead>
						<tr>
							<th>Descrição</th>
							<th>QTDE</th>
							<th colspan="2" class="text-center">Valor</th>
						</tr>
					</thead>
					<body>
						<?php 
						$count_qtde = 0;
						$count_price = 0;
						foreach($Cart as $sess) { ?>
						<tr data-dblclick="produtos_editar" data-id="<?php echo $sess->id?>" 
							data="{
								id: '<?php echo $sess->id?>', 
								prod_id: '<?php echo $sess->carrinho_prod->id?>', 
								prod_nome: '<?php echo ($sess->carrinho_prod->new_cod . ' - ' . trim($sess->carrinho_prod->nome_produto) . opc_prod($sess->carrinho_prod->cor->nomecor) . opc_prod($sess->carrinho_prod->tamanho->nometamanho))?>', 
								prod_estoque: '<?php echo $sess->quantidade?>', 
								prod_preco: '<?php echo number_format(($sess->prod_valor > 0 ? $sess->prod_valor : $sess->carrinho_prod->preco_promo), 2, ',', '.')?>'}">
							<td><?php echo ($sess->carrinho_prod->new_cod . ' - ' . trim($sess->carrinho_prod->nome_produto) . opc_prod($sess->carrinho_prod->cor->nomecor) . opc_prod($sess->carrinho_prod->tamanho->nometamanho))?></td>
							<td nowrap="nowrap" width="1%" align="center"><?php echo $sess->quantidade?></td>
							<td nowrap="nowrap" width="1%" align="center">R$: <?php echo number_format(($sess->prod_valor > 0 ? $sess->prod_valor : $sess->carrinho_prod->preco_promo), 2, ',', '.')?></td>
							<td nowrap="nowrap" width="1%" align="center"><a href="/adm/pdv/pdv.php?acao=prod_remove&prod_id=<?php echo $sess->id?>" class="fa fa-trash products_remove"></a></td>
						</tr>
						<?php
						$count_qtde += $sess->quantidade;
						$count_price += $sess->prod_valor;
						$i++;
						?>
						<?php } ?>
					</body>
				</table>
			<?php } ?>
			</div>
		</div>
		<div class="panel panel-default fixed-footer">
			<div class="panel-body">
				<div class="row">
					<div class="col-sm-10 col-xs-12">
						a
					</div>
					<div class="col-sm-2 col-xs-12 text-right pull-right">
						<span class="show neo-sans ft12px">Total Item(s): <span id="result_itens"><?php echo $count_qtde?></span></span>
						<span class="show ft14px neo-sans-medium">Sub Total <span id="result_total_sub" class="cor-ad0707">R$: <?php echo number_format($count_price, 2, ',', '.')?></span></span>
						<hr class="mt0 mb5"/>
						<span class="show ft18px neo-sans-medium">Total <span id="result_total_geral" class="cor-ad0707">R$: <?php echo number_format($count_price*$count_qtde, 2, ',', '.')?></span></span>
					</div>
				</div>
			</div>
		</div>
		<?php ob_start(); ?>
		<script>
			
			var ModalGridProd = $("<form/>", {
				id: "prod_form",
				html: [
					$("<input/>", { id: "id", type: "hidden", name: "id", class: "hidden", value: "0" }),
					$("<input/>", { id: "prod_id", type: "hidden", name: "prod_id", class: "hidden", value: "0" }),
					$("<h4/>", { id: "prod_h4" }),
					$("<div/>", { class: "row", html: [
							$("<div/>", { 
								class: "col-sm-3 col-xs-12 form-group",
								html: [
									$("<label/>", { html: "QTDE" }),
									$("<input/>", { id: "prod_qtde", tabindex: "1", type: "number", name: "prod_qtde", class: "form-control text-right", value: "0", })
								]
							}),
							$("<div/>", { 
								class: "col-sm-9 col-xs-12 form-group",
								html: [ $("<label/>", { html: "Valor" }), $("<input/>", { tabindex: "2", id: "prod_price", name: "prod_price", type: "text", class: "form-control text-right", value: "0,00", }) ]
							}),
						]
					}),
					$("<button/>", { type: "submit", html: "salvar", tabindex: "3", class: "pull-right btn btn-success" })
				]
			}).dialog({
				dialogClass: "classe-ui",
				autoOpen: false,
				width: 395,
				height: 255,
				modal: true,
			}).css({
				"overflow-x": "hidden"
			});
			
			$("input[name=prod_price],input.preco-promo").mask("#.##0,00", { reverse: true });
			
			$("#input_select_prod").select2({
				language: "pt-BR",
				placeholder: "Buscar, codigo, descriçao, cor, tamanho...",
				minimumInputLength: 3,
				ajax: {
					url:"/adm/pdv/pdv-produtos-json.php",
					type: "GET",
					delay: 255,
					dataType: "json",
					complete: function(){},
					beforeSend: function(){},
					data: function (params, page) {
						return {
							q: params.term || "",
							page: params.page || 1
						};
					}
				},
				escapeMarkup: function (markup) { 
					return markup; 
				}
			});
			
			// adciona produtos ao pdv
			$("#search_produtos").on("click", "#btn_adicionar_prod", function(e){
				e.preventDefault();
				var data = $("#search_produtos").serialize();
				
				$.ajax({
					url: "/adm/pdv/pdv.php",
					data: data,
					success: function(str) {
						var list = $("<div/>", {html: str});
						$("#result_products").html( list.find("#result_products").html() );
						$("#input_select_prod").val(null).trigger('change');
						$("#prod_qtde").val(1);
					}
				})
			});
			
			// remove produtos na grid
			$("#result_products").on("dblclick", "[data-dblclick='produtos_editar']", function(e){
				var elem = $(this),
					data = elem.attr("data"),
					json = JSON.stringify(eval('(' + data + ')')),
					str = JSON.parse(json);
				
				ModalGridProd.find("#id").val(str.id);
				ModalGridProd.find("#prod_h4").html(str.prod_nome);
				ModalGridProd.find("#prod_id").val(str.prod_id);
				ModalGridProd.find("#prod_qtde").val(str.prod_estoque);
				ModalGridProd.find("#prod_price").val(str.prod_preco);
				ModalGridProd.dialog({title: "Editar Produtos - Grid"}).dialog("open");
			});
			
			// remove produtos na grid
			$("#result_products").on("click", "a.products_remove", function(e){
				e.preventDefault();
				
				var elem = $(this),
					data_href = elem.attr("href")||this.href;
				
				if( !confirm('Deseja realmente excluir!') )
					return;
				
				$.ajax({
					url: data_href,
					success: function( str ) {
						var list = $("<div/>", {html: str});
						$("#result_itens").html( list.find("#result_itens").html() );
						$("#result_total_sub").html( list.find("#result_total_sub").html() );
						$("#result_total_geral").html( list.find("#result_total_geral").html() );
						$("#result_products").html( list.find("#result_products").html() );
					}
				});
			});
			
			ModalGridProd.on("submit", function(e){
				e.preventDefault();
				var elem = $(this),
					dataSerialize = elem.serializeArray();
					dataSerialize.push({name: "acao", value: "prod_add"});
					
				console.log(dataSerialize);
					
				$.ajax({
					url: "/adm/pdv/pdv.php",
					data: dataSerialize,
					success: function(str) {
						var list = $("<div/>", {html: str});
						$("#result_itens").html( list.find("#result_itens").html() );
						$("#result_total_sub").html( list.find("#result_total_sub").html() );
						$("#result_total_geral").html( list.find("#result_total_geral").html() );
						$("#result_products").html( list.find("#result_products").html() );
						ModalGridProd.dialog("close");
					}
				});
			});
		</script>
		<?php $SCRIPT['script_manual'] .= ob_get_clean();?>
	</div>