	<div class="panel panel-default">
		<div class="panel-heading panel-store neo-sans-medium text-uppercase">Cliente</div>
		<div class="panel-body">
			<form class="row" id="search_clientes">
				<input type="hidden" name="acao" value="cli_add"/>
				<div class="form-group col-sm-9 col-xs-12">
					<label>Pesquisar Produtos</label>
					<select name="cli_id" class="form-control select_no_init" id="input_select_cli" tabindex="0"></select>
				</div>
				<div class="form-group col-sm-2 col-xs-12">
					<button type="submit" class="btn btn-primary mt25 btn-block" tabindex="2" id="btn_adicionar_cli"><i class="fa fa-plus"></i> adicionar</button>
				</div>
			</form>
			<div class="table-responsive" id="search_cli">
				<?php
				$Cliente = Carrinho::first(['conditions' => ['id_session = ? ', sha1($_SESSION['admin']['id_usuario'])], 'order' => 'id desc']);
				if( !empty($Cliente->id_cliente) && $Cliente->id_cliente > 0 ) { ?>
					<table class="table table-striped table-hover table-condensed text-uppercase">
						<thead>
							<tr>
								<th nowrap="nowrap" width="1%">Nome</th>
								<th>E-mail</th>
								<th nowrap="nowrap" width="1%" align="center">Tel</th>
							</tr>
						</thead>
						<body>
							<tr class="in-hover" data-dblclick="cliente_editar" data="{
								id_cliente: '<?php echo $Cliente->carrinho_cli->id?>',
								nome: '<?php echo $Cliente->carrinho_cli->nome?>',
								email: '<?php echo $Cliente->carrinho_cli->email?>',
								cpfcnpj: '<?php echo $Cliente->carrinho_cli->cpfcnpj?>',
								rg: '<?php echo $Cliente->carrinho_cli->rg?>',
								telefone: '<?php echo $Cliente->carrinho_cli->telefone?>',
								celular: '<?php echo $Cliente->carrinho_cli->celular?>',
								data_nascimento: '<?php echo $Cliente->carrinho_cli->data_nascimento?>',
								operadora: '<?php echo $Cliente->carrinho_cli->operadora?>',
								id_endereco: '<?php echo $Cliente->carrinho_cli->endereco->id?>',
								endereco: '<?php echo $Cliente->carrinho_cli->endereco->endereco?>',
								numero: '<?php echo $Cliente->carrinho_cli->endereco->numero?>',
								bairro: '<?php echo $Cliente->carrinho_cli->endereco->bairro?>',
								complemento: '<?php echo $Cliente->carrinho_cli->endereco->complemento?>',
								referencia: '<?php echo $Cliente->carrinho_cli->endereco->referencia?>',
								cidade: '<?php echo $Cliente->carrinho_cli->endereco->cidade?>',
								uf: '<?php echo $Cliente->carrinho_cli->endereco->uf?>',
								cep: '<?php echo $Cliente->carrinho_cli->endereco->cep?>',
							}">
								<td nowrap="nowrap" width="1%">
									<?php echo $Cliente->carrinho_cli->nome?>
								</td>
								<td>
									<?php echo $Cliente->carrinho_cli->email?>
								</td>
								<td nowrap="nowrap" width="1%" align="center">
									<?php echo $Cliente->carrinho_cli->telefone?>
								</td>
							</tr>
						</body>
					</table>
					<?php 
				} ?>
			</div>
		</div>
		<?php ob_start(); ?>
		<script>
			// $("input[name=cli_price],input.preco-promo").mask("#.##0,00", { reverse: true });
			var ModalGridCli = $("#cli_form").dialog({
				dialogClass: "classe-ui",
				autoOpen: false,
				width: 800,
				height: 532,
				modal: true
			}).css({ 
				"overflow-x": "hidden" 
			});
			
			$("#input_select_cli").select2({
				language: "pt-BR",
				placeholder: "Buscar, nome, email, telefone...",
				minimumInputLength: 3,
				ajax: {
					url:"/adm/pdv/pdv-clientes-json.php",
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
			
			// adciona cliente ao pdv
			$("#search_clientes").on("click", "#btn_adicionar_cli", function(e){
				e.preventDefault();
				var data = $("#search_clientes").serializeArray();
				console.log(data);
				$.ajax({
					url: "/adm/pdv/pdv.php",
					data: data,
					success: function(str) {
						var list = $("<div/>", {html: str});
						$("#search_cli").html( list.find("#search_cli").html() );
						$("#input_select_cli").val(null).trigger('change');
					}
				})
			});
			
			// edita cliente na grid
			$("#search_cli").on("dblclick", "[data-dblclick='cliente_editar']", function(e){
				var elem = $(this),
					data = elem.attr("data"),
					json = JSON.stringify(eval('(' + data + ')')),
					str = JSON.parse(json);
				
				$.each(str, function(i, e) {
					ModalGridCli.find("#" + i).val(str[i]);
				});

				ModalGridCli.dialog({title: "Editar Clientes - Grid"}).dialog("open");
			});
			
			ModalGridCli.on("submit", function(e){
				e.preventDefault();
				var elem = $(this),
					dataSerialize = elem.serializeArray();
					// dataSerialize.push({ name: "q", value: $("#search_cli").find("input[name=q]").val() });
					
				$.ajax({
					url: "/adm/pdv/pdv.php",
					type: "POST",
					data: dataSerialize,
					success: function(str) {
						var list = $("<div/>", { html: str });
						console.log(list.find("#search_cli").html());
						$("#search_cli").html( list.find("#search_cli").html() );
						ModalGridCli.dialog("close");
					}
				});
			});
		</script>
		<?php $SCRIPT['script_manual'] .= ob_get_clean();?>
	</div>
	
	<form id="cli_form">
		<input type="hidden" name="cli[id_cliente]" id="id_cliente"/>
		<input type="hidden" name="end[id_endereco]" id="id_endereco"/>
		<div class="panel panel-default">
			<div class="panel-heading panel-store neo-sans-medium text-uppercase">
				Dados - Clientes
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-sm-9 col-xs-12 from-group mb15">
						<label>Nome</label>
						<input type="text" name="cli[nome]" id="nome" class="form-control"/>
					</div>
					<div class="col-sm-7 col-xs-12 from-group mb15">
						<label>E-mail</label>
						<input type="text" name="cli[email]" id="email" class="form-control"/>
					</div>
					<div class="clearfix"></div>
					<div class="col-sm-4 col-xs-12 from-group mb15">
						<label>CPF/CNPJ</label>
						<input type="text" name="cli[cpfcnpj]" id="cpfcnpj" class="form-control"/>
					</div>
					<div class="col-sm-4 col-xs-12 from-group mb15">
						<label>RG/IE</label>
						<input type="text" name="cli[rg]" id="rg" class="form-control"/>
					</div>
					<div class="clearfix"></div>
					<div class="col-sm-3 col-xs-12 from-group mb15">
						<label>Telefone</label>
						<input type="text" name="cli[telefone]" id="telefone" class="form-control"/>
					</div>
					<div class="col-sm-3 col-xs-12 from-group mb15">
						<label>Celular</label>
						<input type="text" name="cli[celular]" id="celular" class="form-control"/>
					</div>
					<div class="col-sm-4 col-xs-12 from-group mb15">
						<label>Operadora</label>
						<input type="text" name="cli[operadora]" id="operadora" class="form-control"/>
					</div>
					<div class="clearfix"></div>
					<div class="col-sm-4 col-xs-12 from-group mb15">
						<label>Data Nascimento</label>
						<input type="text" name="cli[data_nascimento]" id="data_nascimento" class="form-control"/>
					</div>
				</div>
			</div>
		</div>
		
		<div class="panel panel-default">
			<div class="panel-heading panel-store neo-sans-medium text-uppercase">
				Endereço - Clientes
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-sm-9 col-xs-12 from-group mb15">
						<label>Endereço</label>
						<input type="text" name="end[endereco]" id="endereco" class="form-control"/>
					</div>
					<div class="col-sm-3 col-xs-12 from-group mb15">
						<label>Número</label>
						<input type="text" name="end[numero]" id="numero" class="form-control"/>
					</div>
					<div class="clearfix"></div>
					<div class="col-sm-5 col-xs-12 from-group mb15">
						<label>Bairro</label>
						<input type="text" name="end[bairro]" id="bairro" class="form-control"/>
					</div>
					<div class="col-sm-6 col-xs-12 from-group mb15">
						<label>Complemento</label>
						<input type="text" name="end[complemento]" id="complemento" class="form-control"/>
					</div>
					<div class="clearfix"></div>
					<div class="col-sm-6 col-xs-12 from-group mb15">
						<label>Referência</label>
						<input type="text" name="end[referencia]" id="referencia" class="form-control"/>
					</div>
					<div class="col-sm-4 col-xs-12 from-group mb15">
						<label>Cidade</label>
						<input type="text" name="end[cidade]" id="cidade" class="form-control"/>
					</div>
					<div class="col-sm-2 col-xs-12 from-group mb15">
						<label>UF</label>
						<input type="text" name="end[uf]" id="uf" class="form-control"/>
					</div>
					<div class="col-sm-4 col-xs-12 from-group mb15">
						<label>CEP</label>
						<input type="text" name="end[cep]" id="cep" class="form-control"/>
					</div>
					<input type="hidden" name="end[status]" id="status" value="ativo" class="hidden"/>
				</div>
			</div>
		</div>
		<button type="submit" class="btn btn-primary">salvar</button>
	</form>
	<style>
		body{padding-bottom: 55px}
	</style>