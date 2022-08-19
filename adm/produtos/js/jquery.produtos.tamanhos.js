/**-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**
 CarregarProdutoCoresTamanhos
	**-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**/
CarregarProdutoTamanhos = function(){
	$.ajax({
		url: "/adm/produtos/produtos-tamanhos.php?codigo_id=" + GLOBALS.codigo_id,
		success: function( str ){ 
			var list = $("<div/>", { html: str });
			$("#aba6").html( list.find("#aba6").html() ); 
		},
		error: function(x,t,m){ 
			console.log(x.responseText+'\n'+t+'\n'+m);
		}
	}); 
};

$("a[href=#aba6]").on("click", CarregarProdutoTamanhos);

$("#aba6").on("click", "a.btn-salvar-cor-tamanho", function(e){
	e.preventDefault();
	var $this = $(this),
		$href = e.target.href,
		$dataId = $this.attr("data-id"),
		$form = $("tr#" + $dataId).find('input[name], select[name], textarea[name]').serialize();
		
	console.log($form);
	
	$.ajax({
		url: this.href || $href,
		type: "post",
		cache: false,
		data: $form,
		success: function( str ){ 
			var list = $("<div/>", { html: str });
			$("#aba6").html( list.find("#aba6").html() ); 
		},
		error: function(x,t,m){ 
			console.log(x.responseText+'\n'+t+'\n'+m); 
		}
	});
});

/**-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*
	Adicionar Cores Novas
	*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**/
$("#aba6").on("click", "button#btn-adicionar-tamanhos", function(e){
	e.preventDefault();
	
	var SelectVal = $("#aba6").find("select").val();
	
	$.ajax({
		url: "/adm/produtos/produtos-tamanhos.php?codigo_id=" + GLOBALS.codigo_id,
		type: "POST",
		data: { 
			acao: "adicionar-tamanhos", 
			tamanho_id: SelectVal, 
			codigo_id: GLOBALS.codigo_id
		},
		success: function( str ){
			var list = $("<div/>", { html: str });
			$("#aba6").html( list.find("#aba6").html() ); 
		},
		complete: function(){
			if( $("#aba6").is(":visible") )
				$("a[href=#aba6]").trigger("click");
		},
		error: function(x,t,m){ 
			console.log(x.responseText+"\n"+t+"\n"+m);
		}
	});
});

/**-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*
	Remover Cores
	*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**/
$("#aba6").on("click", "a.btn-excluir-tamanhos, a.excluir-all-cor-tam", function(e){
	if( ! confirm("Deseja realmente excluir!") ) return false;
	e.preventDefault();
	
	$.ajax({
		url: this.href,
		data: { 
			codigo_id: GLOBALS.codigo_id
		},
		cache: false,
		success: function( str ){ 
			var list = $("<div/>", { html: str });
			$("#aba6").html( list.find("#aba6").html() ); 
		},
		complete: function(){
			if( $("#aba6").is(":visible") )
				$("a[href=#aba6]").trigger("click");
		},
		error: function(x,t,m){ 
			console.log(x.responseText+"\n"+t+"\n"+m);
		}
	});
});


$("#aba6").on("click", '#btn-cadastrar-tamanhos', function(e){
	e.preventDefault();
	$.ajax({
		url: this.href,
		cache: false,
		success: function( str ){ 
			var list = $("<div/>", { html: str });
			JanelaModal
				.dialog({ title: "Cadastrar/Editar - Cores", autoOpen: true })
				.html( list.find("#div-edicao").html() );
		},
		error: function(x,t,m){ 
			console.log(x.responseText+'\n'+t+'\n'+m); 
		}
	});
});

/**-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**
 Abre a Janela com os Tamanhos
	**-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**/
$("#aba6").on("click", 'a.btn-tamanhos', function(e){
	e.preventDefault();
	$.ajax({
		url: this.href,
		cache: false,
		success: function( str ){ 
			var list = $("<div/>", { html: str });
			JanelaModal
				.dialog({ title: "Cadastrar/Editar - Tamanhos", autoOpen: true })
				.html( list.find("#div-edicao").html() );
		},
		complete: function(){
			if( $("#aba6").is(":visible") )
				$("a[href=#aba6]").trigger("click");
		},
		error: function(x,t,m){ 
			console.log(x.responseText+'\n'+t+'\n'+m); 
		}
	});
});