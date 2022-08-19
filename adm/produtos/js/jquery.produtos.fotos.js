/**
 * O milagra acontece aqui
 * Se por ventura a tela do produto estiver visivel, tenta fazer update dos dados
 * @returns {undefined}
 */
ProdutosCadastrarEditar = function() {
	var DataStr = $("#aba1").parent().serialize();
	
	$.ajax({
		url: window.location.href,
		type: "post",
		data: DataStr,
		success: function(str) {
			var list = $("<div/>", { html: str });
			$("#aba1").html( list.find("#aba1").html() );
		},
		error: function(a,b,c){
			console.log(a.responseText+"\n"+b+"\n"+c);
		}
	});
};

$(".edicao-imagens").on("click", ".open-foto", function(){
	var IdClickFoto = $(this).attr("data-id");
	$("input#"+IdClickFoto+"").click();
});


/**
 * Envia uma foto para upload
 */
$(".edicao-imagens").on("change", "input[type=file]", function() {
	// valida a extensao da imagem
	if( !validaExtensao(  this.id  ) ) { 
		return false;
	}
	
	var FormFoto = $(this).parents(), 
		FormAction = $(FormFoto).attr("action"),
		FormDataSerialize = $(FormFoto).serializeArray();
		console.log(FormDataSerialize);
		// return false;
		
	$(FormFoto).ajaxSubmit({
		url: FormAction,
		data: FormDataSerialize,
		resetForm: true,
		cache: false,
		type: "post",
		uploadProgress: function(event,position,total,percentComplete){
			$("#status-alteracao").fadeIn(0).html('Enviando imagem '+percentComplete+'%');
		},
		success: function( str ) {
			var list = $("<div/>", { html: str });
			$(".edicao-imagens").html( list.find(".edicao-imagens").html() );
		},
		// complete: function(){
		// 	if( $("#aba7").is(":visible") )
		// 		CarregarProdutoCoresTamanhos();
			
		// 	if( $("#aba6").is(":visible") )
		// 		CarregarProdutoTamanhos();
			
		// 	if( $("#aba5").is(":visible") )
		// 		CarregarProdutoCores();
			
		// 	if( $("#aba1").is(":visible") )
		// 		ProdutosCadastrarEditar();
		// },
		error: function(x,t,m){ 
			alert("Não consegui enviar a imagem!\nTente recarregar a página.");
			console.log( x.responseText + '\n' + t + '\n' + m ); 
		}
	});
});

/**-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*
 define acoes a parti de dentro do JanelaModal
	**-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**/
JanelaModal.on("click", ".open-foto", function(){
	var IdClickFoto = $(this).attr("data-id");
	$("input#"+IdClickFoto+"").click();
});

JanelaModal.on("change", "input[type=file].fotos-produtos", function() {
	// valida a extensao da imagem
	if( !validaExtensao(  this.id  ) ) { 
		return false;
	}
	
	var FormFoto = $(this).parents(), 
		FormAction = $(FormFoto).attr("action"),
		FormDataSerialize = $(FormFoto).serializeArray();
		console.log(FormDataSerialize);
		// return false;
		
	$(FormFoto).ajaxSubmit({
		url: FormAction,
		data: FormDataSerialize,
		resetForm: true,
		cache: false,
		type: "post",
		uploadProgress: function(event,position,total,percentComplete){
			$("#status-alteracao").fadeIn(0).html('Enviando imagem '+percentComplete+'%');
		},
		complete: function(){
			if( $("#aba7").is(":visible") )
				CarregarProdutoCoresTamanhos();
			
			if( $("#aba6").is(":visible") )
				CarregarProdutoTamanhos();
			
			if( $("#aba5").is(":visible") )
				CarregarProdutoCores();
			
			if( $("#aba1").is(":visible") )
				ProdutosCadastrarEditar();
		},
		success: function( str ) {
			var list = $("<div/>", { html: str });
			JanelaModal.html( list.find("#div-edicao").html() );
		},
		error: function(x,t,m){ 
			alert("Não consegui enviar a imagem!\nTente recarregar a página.");
			console.log( x.responseText + '\n' + t + '\n' + m ); 
		}
	});
});

/**
 * Aplica a capa na foto
 */
JanelaModal.on("click", ".add-capa", function(e){
	e.preventDefault();
	var href = e.target.href||this.href;
	$.ajax({
		url: href,
		cache: false,
		success: function( str ){ 
			console.log(str);
			var list = $("<div/>", { html: str });
			JanelaModal
				.html( list.find("#div-edicao").html() );
		},
		error: function(x,t,m){ 
			console.log(x.responseText+"\n"+t+"\n"+m);
		}
	});
});

/**
 * Altera a ordem das fotos
 */
JanelaModal.on("change", "input[name=ordem]", function() {
		var FormFoto = $(this).parent().parent().parent(), 
		FormAction = $(FormFoto).attr("action"),
		FormDataOrdem = $(this).val(),
		FormDataFotoId = $(FormFoto).find("input[name=id]").val();
		
	$.ajax({
		url: FormAction,
		data: { acao: "ordem_foto", ordem: FormDataOrdem, capa_id: FormDataFotoId },
		resetForm: true,
		cache: false,
		type: "post",
		success: function( str ) {
			var list = $("<div/>", { html: str });
			$(".edicao-imagens").html( list.find(".edicao-imagens").html() );
		},
		error: function(x,t,m){ 
			alert("Não consegui altera a ordem da imagem!\nTente recarregar a página.");
			console.log( x.responseText + '\n' + t + '\n' + m ); 
		}
	});
	return;
});

JanelaModal.on("click", ".remove-foto", function(e){
	e.preventDefault();
	var href = e.target.href||this.href;
	$.ajax({
		url: href,
		cache: false,
		success: function( str ){ 
			console.log(str);
			var list = $("<div/>", { html: str });
			JanelaModal
				.html( list.find("#div-edicao").html() );
		},
		complete: function(){
			if( $("#aba7").is(":visible") )
				CarregarProdutoCoresTamanhos();
			
			if( $("#aba6").is(":visible") )
				CarregarProdutoTamanhos();
			
			if( $("#aba5").is(":visible") )
				CarregarProdutoCores();
			
			if( $("#aba1").is(":visible") )
				ProdutosCadastrarEditar();
//					$("#aba1").parent().trigger("submit");
		},
		error: function(x,t,m){ 
			console.log(x.responseText+"\n"+t+"\n"+m);
		}
	});
});