var emitentes = [];
<?php 
$NfeEmitentes = NfeEmitentes::all(['conditions' => ['loja_id=?', $CONFIG['loja_id']], 'order' => 'id asc']);
foreach( $NfeEmitentes as $emit ) { ?>
	emitentes.push({
		value: "<?php echo (!empty($emit->id) && $emit->id > 0 ? $emit->id : '');?>", 
		text: "<?php echo $emit->razaosocial?>",
		selected: <?php echo (count($NfeEmitentes) == 1 ? 'true':'null')?>,
		nrnfe: "<?php echo $emit->nrnfe?>"
	});
<?php } ?>

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
}).css({"overflow-x": "hidden"}),

JanelaEtiquetaCorreio = $("<form/>", {id: "janela-etiqueta"}).dialog({
	width: 455, 
	height: "auto",
	autoOpen: false,
	modal: true
}).html([
	$("<div/>", {
		id: "form_etiqueta_correios",
		class: "row",
		method: "post",
		action: "/adm/jadlog/jadlog-etiquetas-action.php?acao=gerar_etiqueta_jadlog",
		html: [
			$("<div/>", {
				class: "col-sm-3 form-group",
				html: [
					$("<label/>", { for: "frete_qtde_correios", html: "QTDE:" }),
					$("<input/>", {
						id: "frete_qtde_correios",
						name: "frete_qtde",
						class: "form-control text-right",
						value: 1,
						css: {width: "100%"}
					})
				]
			}),
			$("<div/>", {
				class: "col-sm-9 form-group",
				html: [
					$("<label/>", { for: "frete_seguro_correios", html: "Seguro:" }),
					$("<select/>", {
						id: "frete_seguro_correios",
						name: "frete_seguro",
						class: "form-control",
						value: 1,
						css: {width: "100%"},
						html: [
							$("<option/>", { value: 0, text: "Não" }),
							$("<option/>", { value: 1, text: "Sim" }),
						]
					})
				]
			}),
			$("<div/>", {
				class: "col-sm-12 form-group",
				html: [
					$("<label/>", { for: "frete_servico_correios", html: "Serviço de postagem:" }),
					$("<select/>", {
						id: "frete_servico_correios",
						name: "frete_servico",
						class: "form-control",
						value: 1,
						css: {width: "100%"},
						html: [
							$("<option/>", { value: 0, text: "" }),
							$("<option/>", { value: 3, text: "Carregando dados..." }),
						]
					})
				]
			}),
			
			$("<div/>", {
				class: "col-sm-12 form-group text-center",
				html: [
					$("<button/>", { html: "gerar etiqueta", class: "btn btn-primary", type: "submit", name: "acao", value: "gerar_etiqueta_jadlog" }),
					$("<input/>", { type: "hidden", name: "id_pedido", value: "<?php echo $Pedido->id?>" })
				]
			})
		]
	})
]).css({"overflow-x": "hidden"}), 
// gera o txt para o sistema 
JanelaNfeTxt = $("<form/>", {
	css: { "overflow-x": "hidden" },
	target: "_blank",
	action: "/adm/vendas/vendas-txt.php",
	html: [
		$("<div/>", {
			class: "form-group",
			html: [
				$("<label/>", { html: "Emitente" }),
				$("<select/>", { value: "100", class: "form-control", name: "id_emitente", id: "id_emitente" }),
			]
		}), 
		$("<div/>",{
			class: "row",
			html: [
				$("<div/>", {
					class: "form-group col-sm-6",
					html: [
						$("<label/>", { html: "Nro da Nota" }),
						$("<input/>", { value: "100", class: "form-control text-right", name: "nfe_nrnota", id: "nfe_nrnota", type: "number" }),
					]
				}),
				$("<div/>", {
					class: "form-group col-sm-6",
					html: [
						$("<label/>", { html: "Porcentagem da Nota" }),
						$("<input/>", { value: "100", class: "form-control text-right", name: "porc_nota", id: "porc_nota", type: "number" }),
					]
				}),
			]
		}),
		$("<input/>", { value: "<?php echo $PEDIDO_ID?>", class: "hidden", name: "id_pedido", id: "id_pedido", type: "hidden" }),
		$("<button/>", { class: "btn btn-primary", html: "gerar", type: "submit" })
	]
}).dialog({
	width: 455, 
	height: "auto",
	autoOpen: false,
	modal: true,
	title: "Gerar TXT para NF-e",
	open: function() {
		var id_id_emitente = $(this).find("#id_emitente"),
			id_nfe_nrnota = $(this).find("#nfe_nrnota")
		if(emitentes.length > 0) {
			id_nfe_nrnota.val(emitentes[0].nrnfe);
			id_id_emitente.html($("<option/>",{ text: "Selecione o emitente" }));
			for(var e = 0; e < emitentes.length; e++) {
				id_id_emitente.append($("<option/>", { text: emitentes[e].text, value: emitentes[e].value, attr: { selected: emitentes[e].selected } }));
			}
		}
	}
});

$("#div-edicao").on("click", ".btn-txt", function(e){ 
	e.preventDefault(); 
	JanelaNfeTxt.dialog("open"); 
});

JanelaNfeTxt.submit(function(){
	$.ajax({
		url: window.location.href,
		success: function( str ){
			var list = $("<div/>", { html: str });
			$("#div-edicao").html(list.find("#div-edicao").html());
			JanelaNfeTxt.dialog("close"); 
		}
	});
});

JanelaEtiquetaJadLog = $("<div/>", { id: "janela-etiqueta-jadlog" }).dialog({
	modal: true,
	title: 'Etiquetas JADLOG',
	autoOpen: false,
	width: 500,
	height: 396,
}).html([
	$("<form/>", {
		id: "form_etiqueta_jadlog",
		class: "row",
		method: "post",
		action: "/adm/jadlog/jadlog-etiquetas-action.php?acao=gerar_etiqueta_jadlog",
		html: [
			$("<div/>", {
				class: "col-sm-3 form-group",
				html: [
					$("<label/>", { for: "frete_qtde", html: "QTDE" }),
					$("<input/>", {
						id: "frete_qtde",
						name: "frete_qtde",
						class: "form-control",
						value: 1,
						css: {width: "100%"}
					})
				]
			}),
			$("<div/>", {
				class: "col-sm-5 form-group",
				html: [
					$("<label/>", { for: "frete_tipo", html: "Modalidade" }),
					$("<select/>", {
						id: "frete_tipo",
						class: "form-control",
						name: "frete_tipo",
						css: {width: "100%"},
						html: [
							$("<option/>", { value: 0, text: "Tipo do frete" }),
							$("<option/>", { value: 3, text: "Packpage" }),
							$("<option/>", { value: 4, text: "Rodoviário" }),
							$("<option/>", { value: 9, text: ".COM" }),
							$("<option/>", { value: 40, text: "PICKUP", <?php echo !empty($Pedido->frete_pudoid) ? 'attr:{selected: "selected"}' : null?> }),
						]
					})
				]
			}),
			$("<div/>", {
				class: "col-sm-4 form-group",
				html: [
					$("<label/>", { for: "frete_tp_doc", html: "Nota" }),
					$("<select/>", {
						id: "frete_tp_doc",
						class: "form-control",
						name: "frete_tp_doc",
						css: {width: "100%"},
						html: [
							$("<option/>", { value: 0, text: "Declaração" }),
							$("<option/>", { value: 1, text: "NF" }),
							$("<option/>", { value: 2, text: "NFE" }),
							$("<option/>", { value: 4, text: "CTE" }),
						]
					})
				]
			}),
			$("<div/>", {
				class: "col-sm-3 form-group",
				html: [
					$("<label/>", { for: "frete_nr_serie", html: "Série Nfe" }),
					$("<input/>", {
						id: "frete_nr_serie",
						name: "frete_nr_serie",
						class: "form-control",
						css: {width: "100%"}
					})
				]
			}),
			$("<div/>", {
				class: "col-sm-4 form-group",
				html: [
					$("<label/>", { for: "frete_cfop", html: "CFOP" }),
					$("<input/>", {
						id: "frete_cfop",
						name: "frete_cfop",
						class: "form-control",
						css: {width: "100%"}
					})
				]
			}),
			$("<div/>", {
				class: "col-sm-5 form-group",
				html: [
					$("<label/>", { for: "frete_nr_nfe", html: "Nr Nota Fiscal" }),
					$("<input/>", {
						id: "frete_nr_nfe",
						name: "frete_nr_nfe",
						class: "form-control",
						css: {width: "100%"}
					})
				]
			}),
			$("<div/>", {
				class: "col-sm-12 form-group",
				html: [
					$("<label/>", { for: "frete_nr_danfe", html: "Nr da chave DANFE" }),
					$("<input/>", {
						id: "frete_nr_danfe",
						name: "frete_nr_danfe",
						class: "form-control",
						css: {width: "100%"}
					})
				]
			}),
			$("<div/>", {
				class: "col-sm-12 form-group text-center",
				html: [
					$("<button/>", { html: "gerar etiqueta", class: "btn btn-primary", type: "submit", name: "acao", value: "gerar_etiqueta_jadlog" }),
					$("<input/>", { type: "hidden", name: "id_pedido", value: "<?php echo $Pedido->id?>" })
				]
			})
		]
	})
]).css({ "overflow-x": "hidden" });

JanelaEtiquetaCorreio.dialog({
	open: function() {
		$.ajax({
			url: "/adm/correios/correios.php",
			data: {
				acao: "FreteCalcular", 
				altura: "<?php echo $cubagem['altura']?>",
				largura: "<?php echo $cubagem['largura']?>",
				comprimento: "<?php echo $cubagem['comprimento']?>",
				peso: "<?php echo $cubagem['peso']?>",
				cep: "<?php echo soNumero($Pedido->pedido_endereco->cep)?>"
			},
			retries: 3,
			timeout: 15000,
			retryInterval: 5000,
			dataType: "html",
			beforeSend: function(){
				$("select[name=frete_servico]").html([ 
					$("<option/>", { text: "" }), 
					$("<option/>", { text: "Carregando informações..." }) 
				]);
			},
			success: function(correios) {
				var count_correios = 0,
					list_correios = $("<div/>", { html: correios });
				
				$("select[name=frete_servico]").html(list_correios.find("select[name='servicos[]']").html());
			}
		})
	}
});

JanelaEtiquetaJadLog.dialog({
	open: function(){
		$.ajax({
			url: "/adm/jadlog/jadlog-etiquetas-action.php",
			data: { acao: "jadlog_get_servicos", id_pedido: "<?php echo $Pedido->id?>" },
			retries: 3,
			timeout: 15000,
			retryInterval: 5000,
			dataType: "html",
			beforeSend: function() {
				$("select[name=frete_tipo]").html([ 
					$("<option/>", { text: "" }), 
					$("<option/>", { text: "Carregando informações..." }) 
				]);
			},
			success: function(jadlog) {
				var count_jadlog = 0, 
					list_jadlog = $("<div/>", { html: jadlog });
				var obj = JSON.parse(list_jadlog.find("#jadlog_get_servicos").html());
				
				$("select[name=frete_tipo]").html([ $("<option/>", { text: "Selecione", value: 0 })]);
				
				// if( obj.length === 0 && count_jadlog < 5 ){
				// 	count_jadlog++;
				// 	console.log("count_jadlog", count_jadlog);
				// 	// return setTimeout(reload_servicos_jadlog, 2500);
				// }
				
				for( var i = 0; i < obj.length; i++ ) {
					$("select[name=frete_tipo]").append([ $('<option/>', { value: obj[i].modalidade, text: obj[i].text + " - R$: " + obj[i].vltotal }) ]);
				}
			}
		})
	}
});
/**
 * Ver informações do produto se haja personalização
 * @type @call;$|@call;$
 */
$(document).on("click", "a[href]", function(e){
	var $this = $(e.target),
		href = this.href || e.target.href,
		ModalId = href.split("#")[1],
		Modal = $("#" + ModalId);
	if( href.search('personalizado_') === '0') return false;
	
	if( Modal.is( ":visible" ) ) {
		Modal.dialog("open");
	}
	else {
		Modal.dialog({
			title: "Detalhes da Personalização",
			width: 800,
			height: 600,
			modal: true,
			autoOpen: true,
			hide: { effect: "explode", duration: 440 },
			open: function(event, ui) {                
				$("html").css("overflow", "hidden");
			},
			close: function(event, ui) {
				$("html").css("overflow", "auto");
			}
		});
	}
});

/**
 * Formulario para upload das imagens personalizadas
 */
$("._modal_personalizado").on("change", "._form_personalizado", function( e ) {
	e.preventDefault();
	var Form = $(this)||$(e.target),
		FormAction = Form.attr("action"),
		FormDataSerialize = Form.serializeArray();
	
	Form.ajaxSubmit({
		url: FormAction,
		type: "post",
		data: FormDataSerialize,
		cache: false,
		resetForm: true,
		uploadProgress	: function(event,position,total,percentComplete){
			$("#status-alteracao").fadeIn(0).html('Enviando imagem '+percentComplete+'%');
		},
		success: function( str ) {
			var list = $("<div/>", { html: str });
			if( list.find("#status").html() === "true" ){
				$.ajax({
					url: window.location.href,
					success: function(str){
						var list = $("<div/>", { html: str });
						$( window.location.hash ).html( list.find(window.location.hash).html() );
					}
				})
			}
		},
		error: function(x,t,m) { 
			alert("Não consegui enviar a imagem!\nTente recarregar a página.");
			console.log( x.responseText + '\n' + t + '\n' + m ); 
		}
	});
});

/**
 * Definir os status do pedido
 * @type @call;$
 */
var AvancarStatusHml = $("<form/>", {
	append: [
		$("<h4/>", { html: "Deseja avançar para qual status?" }),
		$("<div/>", { class: "selecao-status clearfix mt15", html: ['<?php echo status_imgs()?>'] }),
		$("<div/>",{ 
			id: "div-status-8",
			class: "esconder-status mt15",
			append: [
				$("<p/>", { html: "Digite o código de rastreio" }),
				$("<input/>", { name: "rastreio", class: "w30", type: "text" }),
			]
		}),
		$("<div/>",{ 
			id: "div-status",
			class: "esconder-status mt15",
			append: [
				$("<p/>", { html: "Digite um motivo", append:[ $("<span/>",{ html: "(Opcional)", class: "ft10px" }) ] }),
				$("<textarea/>", { name: "motivos", class: "w70", rows: 5 }),
			]
		}),
		$("<input/>", { name: "status", class: "hidden", type: "radio", attr: { "checked": "checked" } })
		// // carregar imagens personalizadas
		// $("<div/>", { 
			// id: "div-status9",
			// class: "esconder-status mt15",
			// append: [
				// $("<label>", { "class": "btn btn-primary-default", "for": "files", html: "carregar fotos" })
			// ]
		// })
	]
});

JanelaModal.on("mouseout", "img[data-status]", function() {
	var src = $(this).attr("src"),
		src = src.replace("status-", "off-");

	if( ! $(this).data("at") )
		$(this).attr({"src": src}).css({"cursor": "inherit"});
	
})
.on("mouseover", "img[data-status]", function() {
	var src = $(this).attr("src"),
		src = src.replace("off-", "status-");
	$(this).attr({"src": src}).css({"cursor": "pointer"});
})
.on("click", "img[data-status]", function(event) {
	var src = $(this).attr("src"),
		src = src.replace("off-", "status-"),
		src_all = JanelaModal.find("img[data-status]"),
		status = $(event.target).data("status");

		$.each(src_all, function(a, b) {
			src_new = $(b).attr("src").replace("status-", "off-");
			$(b).attr({"src": src_new, "data-at": ""});
		});
	
	$(this).attr({"src": src, "data-at": "true"}).css({"cursor": "pointer"});

	$("input[name=rastreio]").val("");
	JanelaModal.find(".esconder-status").hide(0);
	JanelaModal.find("input[name=status]").val(status||1);

	switch( status ) 
	{
		case 4 :
		case 5 :
		case 10 :
		case 12 :
			$("#div-status").show(0);
		break;
		case 8:
			$("#div-status-"+ status).show(0);
			$("input[name=rastreio]").val( $("[data-etiqueta]").attr("data-etiqueta") );
		break;
		// dados para personalização
		// case 9:
		// 	if( ! $("#conteudos-recarregar").hasClass("files-plus") )
		// 		$("#div-"+ status +"").show(0);
		// break;
	}
});

/**
 * Avançar status do pagamento
 */	
$("#div-edicao").on("click", ".btn-acao-status", function(e) {
	var $this = $(this),
		$Acao = $this.data("acao"),
		$Status = $this.data("status");
		
	switch( $Acao )
	{
		case "status-avancar" :
			switch( $Status )
			{
				case 0:
				case 1:
				case 2:
				case 3:
				case 4:
				case 5:
				case 6:
				case 7:
				case 8:
				case 9:
				case 10 :
				case 11 :
				case 12 :
					console.log("Alterar Status do Pedido");
					JanelaModal.dialog({ 
						title: "Alterar Status do Pedido", 
						autoOpen: true,
						width: 800,
						height: 532,
						open: function(e, ui) {
							$(e.target).parents().find(".ui-dialog-buttonset").find("button").removeClass("ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only ui-state-hover");
						},
						buttons: [{				
							text: "Avançar",
							class: "btn btn-primary",
							click: function() {
								$.ajax({
									url: window.location.href,
									type: "POST",
									data: {
										acao: "STATUS",
										status: $("input[type=radio][name=status]:checked").val(),
										motivos: $("textarea[name=motivos]").val(),
										rastreio: $("input[name=rastreio]").val()
									},
									dataType: "html",
									success: function( str ){ 
										var list = $("<div/>",{ html: str });
										$("#div-edicao").html(list.find("#div-edicao").html());
									},
									beforeSend: function() { 
										JanelaModal.html([
											$("<h3/>", {
												class: "text-center",
												html: [
													"Enviando e-mail e alterando status do pedido, aguarde",
													$("<i/>", { class: "fa fa-spinner fa-pulse fa-fw"})        
												]
											})
										]).parents().find(".ui-dialog-buttonset").fadeOut(0);
									},
									complete: function() { 
										JanelaModal.html("").dialog("close").parents().find(".ui-dialog-buttonset").fadeIn(0);
									},
									error: function(x,t,m){ 
										console.log("Error Vendas Detalhes\n"+x.responseText+"\n"+t+"\n"+m);
									}
								});
							}
						}, {
							text: "Cancelar",
							class: "btn btn-danger",
							click: function() { 
								JanelaModal.dialog("close"); 
								$(".esconder-status").hide(0);
							}
						}],
						close: function() { 
							JanelaModal.dialog("close"); 
							$(".esconder-status").hide(0); 
						}
					}).html( AvancarStatusHml );
				break;
			}
		break;
		case 'status-telemarketing' :
			$.ajax({
				url: window.location.href,
				dataType: "html",
				cache: false,
				success: function( str ) { 
					var list = $("<div/>",{ html: str });
					JanelaModal.dialog({ 
						title: "Anotações de Telemarketing", 
						autoOpen: true,
						buttons: {
							'Avançar': function() {
								$.ajax({
									url: window.location.href,
									type: 'post',
									data: {
										acao: 'TELEMARKETING',
										descricao: JanelaModal.find('textarea[name=descricao]').val()
									},
									success: function( str ) { 
										var list = $("<div/>",{ html: str });
										JanelaModal.find("#telemarketing-reload").html( list.find("#telemarketing-reload").html() );
										JanelaModal.find('textarea[name=descricao]').val("");
									},
									error: function(x,t,m){ 
										console.log("Error Vendas Detalhes\n"+x.responseText+"\n"+t+"\n"+m);
									}
								});
							},
							'Cancelar': function() { 
								JanelaModal.dialog('close'); 
							}
						},
						close: function() { 
							JanelaModal.dialog('close'); 
						}
					}).html( list.find("#telemarketing").fadeIn().html() );
				},
				complete: function() {
					
					var TextArea = JanelaModal.find('textarea[name=descricao]');
					if (tinyMCE.activeEditor !== null)
						tinymce.EditorManager.execCommand('mceRemoveEditor', true, TextArea);
					
					JanelaModal.find('textarea[name=descricao]').tinymce({
						entity_encoding: "raw",
						language: "pt_BR",                        
						toolbar_items_size: "small",
						menubar: false,
						toolbar1: "newdocument cut copy paste | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify",
						toolbar2: "undo redo | bullist numlist | outdent indent blockquote | link unlink anchor code | forecolor backcolor | insertdatetime",
						plugins: [
							"advlist autolink autosave link charmap print preview hr anchor pagebreak spellchecker",
							"searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime nonbreaking",
							"table contextmenu directionality emoticons template textcolor paste textcolor colorpicker textpattern"
						]
					});
					
				},
				error: function(x,t,m){ 
					console.log("Error Telemarketing\n"+x.responseText+"\n"+t+"\n"+m);
				}
			});
		break;
		case 'status-pedidos-logs':
			$.ajax({
				url: window.location.href,
				cache: false,
				success: function( str ){ 
					var list = $("<div/>", { html: str });
					JanelaModal.html(list.find('#recarregar-pedidos-logs').fadeIn().html()).dialog({
						title: "Logs Pedidos",
						autoOpen: true,
						width: 775,
						height: 405
					});
				},
				error: function(x,t,m){ 
					console.log("Error Telemarketing\n"+x.responseText+"\n"+t+"\n"+m);
				}
			});
		break;
		case 'cielo':
			$.ajax({
				url: e.target.href||this.href,
				cache: false,
				success: function( str ){ 
					var list = $("<div/>", { html: str });
					JanelaModal.html( list.find("#checkout-cielo").fadeIn().html() ).dialog({
						title: "Cielo",
						autoOpen: true,
						width: 775,
						height: 405
					});
				},
				error: function(x,t,m){ 
					console.log("Error Cielo\n"+x.responseText+"\n"+t+"\n"+m);
				}
			});
		break;
	}
	e.preventDefault();
});

JanelaModal.on("click", "a.cielo", function(e){
	e.preventDefault();
	$.ajax({
		url: e.target.href||this.href,
		success: function( str ){ 
			var list = $("<div/>", { html: str });
			JanelaModal.html( list.find("#checkout-cielo").fadeIn().html() ).dialog({
				title: "Cielo",
				autoOpen: true,
				width: 775,
				height: 405
			});
		},
		error: function(x,t,m){ 
			console.log("Error Cielo 2\n"+x.responseText+"\n"+t+"\n"+m);
		}
	});                
});	


// add the rule here
$.validator.addMethod("valueNotEquals", function(value, element, arg){
	return arg !== value;
}, "Value must not equal arg.");

$("#form_etiqueta_jadlog").validate({
	debug: true,
	rules: {
		"frete_tipo": { required: true, valueNotEquals: "0" },
		// "frete_nr_serie": { required: true },
		// "frete_nr_nfe": { required: true },
		// "frete_nr_danfe": { required: true },
	},
	messages: {
		"frete_tipo": { required: "Campo obrigatório", valueNotEquals: "Selecione a modalidade do frete." },
		// "frete_nr_serie": { required: "Nr série Nfe." },
		// "frete_nr_nfe": { required: "Digite o nr da nfe." },
		// "frete_nr_danfe": { required: "Digite o nr da danfe da nfe" },
	},
	errorClass: "small text-danger",
	errorElement: "span",
	highlight: function(element, errorClass, validClass) {
		$( element ).parent().addClass("has-error");
	},
	unhighlight: function(element, errorClass, validClass) {
		$( element ).parent().removeClass("has-error");
	},
	errorPlacement: function(error, element) {
		error.insertAfter(element);
	},
	submitHandler: function( form ) {
		// return false;
		form.submit();
		
	}
});

$("#conteudos-recarregar").on("click", "#nfe", function( e ) {
	$.ajax({
		url: e.target.href || this.href,
		success: function( str ){
			var list = $("<div/>", { html: str })
			JanelaNFe
			.html(list.find("#conteudos-recarregar-filho").html())
			.dialog({ 
				autoOpen: true, 
				open: function() {
					setTimeout(() => { JanelaNFe.find("select[name=id_emitente]").trigger("change"); }, 10);
				} 
			});
		}
	});
	e.preventDefault();
});

$("#conteudos-recarregar").on("change", "select[name=id_servicos]", function(e){
	var ethis = $(e.target),
		value = ethis.val(),
		hide = $("tr._hide");
		
	if( value !== "" ) {
		hide.fadeOut(0);
	}
	
	if( value === "PAC" || value === "SEDEX" ) {
		$("tr." + value).fadeIn();
	}
	
	e.preventDefault();
});

// Remove a etiqueta gerada CORREIOS
$("#conteudos-recarregar").on("click", ".btn_remover_etiquetas", function(e){
	e.preventDefault();
	if( ! confirm('Deseja realmente remover a etiqueta!') ) return false;
	$.ajax({
		url: e.target.href||this.href,
		success: function( str ) {
			var list = $("<div/>", { html: str });
			$("#reload_buttons").html( list.find("#reload_buttons").html() );
			$("input[name=rastreio]").val( list.find("input[name=rastreio]").val() );
			console.log( list.find("#errors").html() );
		}
	});
});


// Gera um nova etiqueta CORREIOS
$("#conteudos-recarregar").on("click", ".btn_gerar_etiquetas", function(e){
	e.preventDefault();
	var NrPed = $(e.target).attr("data-nr"),
		HrefPed = $(e.target).attr("href");
	JanelaEtiquetaCorreio.attr({ "action": HrefPed }).dialog({"title": "Gerar etiqueta - Ped.: " + NrPed}).dialog("open");
});


// Remove a etiqueta gerada JadLog
$("#conteudos-recarregar").on("click", ".btn_remover_etiquetas_jadlog", function(e){
	e.preventDefault();
	if( ! confirm("Deseja realmente remover a etiqueta!") ) return false;
	$.ajax({
		url: e.target.href||this.href,
		success: function( str ) {
			var list = $("<div/>", { html: str });
			$("#reload_buttons").html( list.find("#reload_buttons").html() );
			$("input[name=rastreio]").val( list.find("input[name=rastreio]").val() );
			console.log( list.find("#errors").html() );
		}
	});
});

// Gera um nova etiqueta JadLog
$('#conteudos-recarregar').on("click", ".btn_gerar_etiquetas_jadlog", function(e){
	e.preventDefault();
	var NrPed = $(e.target).attr("data-nr"),
		HrefPed = $(e.target).attr("href");
	JanelaEtiquetaJadLog.dialog({"title": "Gerar etiqueta - Ped.: " + NrPed}).dialog("open");
});

JanelaEtiquetaCorreio.on("submit", function(e) {
	e.preventDefault();
	var ActionPed = $(e.target).attr("action")||window.location.href,
		frete_qtde = $(e.target).find("input[name=frete_qtde]").val(),
		frete_servico = $(e.target).find("select[name=frete_servico]").val(),
		frete_seguro = $(e.target).find("select[name=frete_seguro]").val(),
		input_data = $(e.target).serialize();
	
	if( frete_qtde === 0 || frete_qtde === "" ){
		alert("Digite a quantidade de pacotes do pedido!");
		return false;
	}
	if( frete_servico === "" ) {
		alert("Selecione o serviço de envio do pedido!");
		return false;
	}
	
	// if( frete_seguro === "" ) {
	// 	alert("Selecione o seguro do pedido!");
	// 	return false;
	// }
	
	console.log(input_data);
	// return;
	$.ajax({
		url: ActionPed,
		type: "POST",
		data: input_data,
		success: function( str ) {
			var list = $("<div/>", { html: str });
			$("#reload_buttons").html( list.find("#reload_buttons").html() );
			$("input[name=rastreio]").val( list.find("input[name=rastreio]").val() );
		},
		beforeSend: function() {
			JanelaEtiquetaCorreio.find('button[type="submit"]').prop("disabled", "disable").toggleClass("btn-primary btn-orange").html([
				$("<i/>",{
					class: "fa fa-spinner fa-spin mr5",
				}),
				$("<span/>",{
					html: "Gerando etiqueta..."
				})
			]);
		},
		complete: function() {
			JanelaEtiquetaCorreio.dialog("close").find('button[type="submit"]').prop("disabled", "false").toggleClass("btn-primary btn-orange").html("gerar etiqueta");
		}
	});
});

/**
 * Cancelamento do pagamento
 */
CieloCancela = $("<div/>", {
	id: "tela-cancela-cielo"
}).dialog({
	autoOpen: false,
	width: 475,
	height: 355,
	modal: true,
	title: "Cancelamento - Ped.: <?php echo $Pedido->codigo?>",
	open: function(){
		$(this).html([
			$("<form/>",{
				id: "form-motivos",
				html: [
					$("<small>",{ html: "Digite o motivo do cancelamento." }),
					$("<textarea/>",{
						name: "motivos",
						class: "w100",
						rows: 9
					}),
					$("<div/>",{
						class: "mt15 clearfix text-center",
						html: [
							$("<hr/>"),
							$("<button/>",{
								type: "submit",
								text: "enviar",
								class: "btn btn-danger mr5"
							})
						]
					})
				]
			})
		]);                
	},
	close: function() { 
		$(this).find("form")[0].reset();
	}
});

/**
 * Capturar o pagamento
 */
JanelaModal.on("click", "#cielo-capture", function(){
	$.ajax({
		cache: false,
		url: "/adm/vendas/vendas-cielo.php",
		data: { acao: "CapitureSale", pedido_id: "<?php echo $Pedido->id?>", status: 2 },
		beforeSend: function(){
			$("#tela-cielo").find("button").addClass("disabled").attr("disabled","true");
		},
		success: function(str) {
			var list = $("<div/>",{ html: str });
			$("#checkout-cielo").html( list.find("#checkout-cielo").html() ).fadeIn(100);
		},
		error: function(a,b,c){
			console.log(a.responseText+"\n"+b+"\n"+c);
		}
	});
});

CieloCancela.on("submit", "#form-motivos", function(e){
	e.preventDefault();
	if( $(this).find("textarea").val() === "" ) {
		alert("Digite o motivo do cancelamento!");
		$(this).find("textarea").focus();
		return false;
	}

	$.when(
		$.ajax({
			url: "/adm/vendas/vendas-cielo.php",
			data: { acao: "CancelSale", pedido_id: "<?php echo $Pedido->id?>", motivos: $(this).find("textarea").val(), status: 4 },
			beforeSend: function(){
				CieloCancela.find("*").fadeOut(0);
				CieloCancela.append([ $("<h4/>", { class: "text-center mt35", html: "Cancelando pagamento..." }) ]);
				$("#tela-cielo").find("button").addClass("disabled").attr("disabled","true");
			}
		}),
		
		/**
		 * Apenas carregar informações da tela
		 */
		$.ajax({
			url: "/adm/vendas/vendas-detalhes.php",
			data: { id: "<?php echo $Pedido->id?>" }
		})
	).then(function(Cielo, Vendas) {
		var ListCielo = $("<div/>",{ html: Cielo });
		// JanelaModal.html(ListCielo.find("#checkout-cielo").html()).fadeIn(100);
		
		var ListVendas = $("<div/>",{ html: Vendas });
		$("#recarrega-status").html(ListVendas.find("#recarrega-status").html());
		JanelaModal.dialog("close");
		CieloCancela.dialog("close");
	});

});

JanelaModal.on("click", "#cielo-cancela", function(){
	CieloCancela.dialog("open");
});

// Tenta editar o endereco
$("#div-edicao").on("click", "#pedido_alterar_endereco", function(e){
	e.preventDefault();

	var NrPed = $(this).data("nr");

	$.ajax({
		url: this.href,
		success: function(str){
			var list = $("<div/>", { html: str });
			JanelaModal.html( list.find("#endereco_alterar").html() ).dialog({"title": "Editar Endereço - Ped.: " + NrPed}).dialog("open");
		}
	});
});


var $sidebar   = $("#flutue"), 
	$window    = $(window),
	offset     = $sidebar.offset(),
	total	   = $window.height(),
	topPadding = 65;

$window.scroll(function() {

	if($window.scrollTop() + offset.top > total) {
		$sidebar.stop().animate({
			marginTop: $window.scrollTop() + $sidebar.height()
		});
	} 
	else if ($window.scrollTop() > offset.top) {
		$sidebar.stop().animate({
			marginTop: $window.scrollTop() - offset.top + topPadding
		});
	} 
	else {
		$sidebar.stop().animate({
			marginTop: 0
		});
	}
});

imprimir_dados = function ( divId ) {
	var mywindow = window.open('/adm/vendas/vendas-impressao.php?id=<?php echo $Pedido->id?>', 'PRINT', 'height=400,width=600');
	mywindow.onload = function() {
		mywindow.imprimir_dados_impressao( divId );
	};
};