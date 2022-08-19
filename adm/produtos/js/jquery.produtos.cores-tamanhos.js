/**-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**
 CarregarProdutoCoresTamanhos
	**-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**/
CarregarProdutoCoresTamanhos = function(){
	$.ajax({
		url: "/adm/produtos/produtos-cores-tamanhos.php?codigo_id=" + GLOBALS.codigo_id,
		success: function( str ){ 
			var list = $("<div/>", { html: str });
			$("#aba7").html(list.find("#aba7").html()); 
		},
		error: function(x,t,m){ 
			console.log(x.responseText+'\n'+t+'\n'+m);
		}
	}); 
};

$("a[href=#aba7]").on("click", CarregarProdutoCoresTamanhos);

$("#aba7").on("click", "a.btn-salvar-cor-tamanho", function(e){
	e.preventDefault();
	var $this = $(this),
		$href = e.target.href,
		$dataId = $this.attr("data-id"),
		$form = $("tr#" + $dataId).find('input[name], select[name], textarea[name]').serialize();
	
	$.ajax({
		url: this.href || $href,
		type: "post",
		data: $form,
		success: function( str ){ 
			var list = $("<div/>", { html: str });
			$("#aba7").html( list.find("#aba7").html() ); 
		},
		error: function(x,t,m){ 
			console.log(x.responseText+'\n'+t+'\n'+m); 
		}
	});
});

/**-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*
	Adicionar Cores Novas
	*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**/
$("#aba7").on("click", "#btn-adicionar-cores", function(e){
	e.preventDefault();
	
	var SelectVal = $("#aba7").find("select").val();
	
	$.ajax({
		url: "/adm/produtos/produtos-cores-tamanhos.php?codigo_id=" + GLOBALS.codigo_id,
		type: "post",
		data: { 
			acao: "AdicionarCores", 
			cor_id: SelectVal, 
			codigo_id: GLOBALS.codigo_id
		},
		success: function( str ){
			var list = $("<div/>", { html: str });
			$("#aba7").html( list.find("#aba7").html() ); 
		},
		error: function(x,t,m){ 
			console.log(x.responseText+"\n"+t+"\n"+m);
		}
	});
});

/**-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*
	Remover Cores
	*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**/
$("#aba7").on("click", "a.btn-excluir-tamanhos, a.excluir-all-cor-tam", function(e){
	e.preventDefault();
	
	if( ! confirm("Deseja realmente excluir!") ) return false;
	
	$.ajax({
		url: this.href,
		data: { codigo_id: GLOBALS.codigo_id },
		cache: false,
		success: function( str ){ 
			var list = $("<div/>", { html: str });
			$("#aba7").html( list.find("#aba7").html() ); 
		},
		error: function(x,t,m){ 
			console.log(x.responseText+"\n"+t+"\n"+m);
		}
	});
});


/**-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**
 Abre a Janela com as Cores
	**-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**/
$("#aba7").on("click", "#btn-cadastrar-cores", function(e){
	e.preventDefault();

	$.ajax({
		url: this.href,
		cache: false,
		success: function( str ){ 
			var list = $("<div/>", { html: str });
			JanelaModal.dialog({ title: "Cadastrar/Editar - Cores", autoOpen: true }).html( list.find("#div-edicao").html() );
		},
		complete: function(){
			if( $("#aba7").is(":visible") ) CarregarProdutoCoresTamanhos();
		},
		error: function(x,t,m){ 
			console.log(x.responseText+'\n'+t+'\n'+m); 
		}
	});
});

/**-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**
 Abre a Janela com os Tamanhos
	**-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**/
$("#aba7").on("click", 'a.btn-tamanhos', function(e){
	e.preventDefault();
	$.ajax({
		url: this.href,
		cache: false,
		success: function( str ){ 
			var list = $("<div/>", { html: str });
			JanelaModal.dialog({ title: "Cadastrar/Editar - Tamanhos", autoOpen: true }).html( list.find("#div-edicao").html() );
		},
		complete: function(){
			if( $("#aba7").is(":visible") ) CarregarProdutoCoresTamanhos();
		},
		error: function(x,t,m){ 
			console.log(x.responseText+'\n'+t+'\n'+m); 
		}
	});
});