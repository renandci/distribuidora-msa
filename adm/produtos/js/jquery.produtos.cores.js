/**-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**
 CarregarProdutoCores
	**-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**/
CarregarProdutoCores = function(){
	$.ajax({
		url: "/adm/produtos/produtos-cores.php?codigo_id=" + GLOBALS.codigo_id,
		success: function( str ){ 
			var list = $("<div/>", { html: str });
			$("#aba5").html( list.find("#aba5").html() ); 
		},
		error: function(x,t,m){ 
			console.log(x.responseText+'\n'+t+'\n'+m);
		}
	}); 
};
// CarregarProdutoCores();

$("a[href=#aba5]").on("click", CarregarProdutoCores);


/**-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*
	Adicionar Cores Novas
	*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**/
$("#aba5").on("click", "button#btn-adicionar-cores", function(e){
	e.preventDefault();
	
	var SelectVal = $("#aba5").find("select").val();
	
	$.ajax({
		url: "/adm/produtos/produtos-cores.php?codigo_id=" + GLOBALS.codigo_id,
		type: "POST",
		data: { 
			acao: "AdicionarCores", 
			cor_id: SelectVal, 
			codigo_id: GLOBALS.codigo_id
		},
		success: function( str ){
			var list = $("<div/>", { html: str });
			$("#aba5").html( list.find("#aba5").html() ); 
		},
		error: function(x,t,m){ 
			console.log(x.responseText+"\n"+t+"\n"+m);
		}
	});
});

/**-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*
	Remover Cores
	*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**/
$("#aba5").on("click", "a.btn-remover-cor", function(e){
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
			$("#aba5").html( list.find("#aba5").html() ); 
		},
		complete: function(){
			CarregarProdutoCores();
		},
		error: function(x,t,m){ 
			console.log(x.responseText+"\n"+t+"\n"+m);
		}
	});
});


$("#aba5").on("click", '#btn-cadastrar-cores', function(e){
	e.preventDefault();
	$.ajax({
		url: this.href,
		cache: false,
		success: function( str ){ 
			var list = $("<div/>", { html: str });
			JanelaModal.html(list.find("#div-edicao").html()).dialog({ title: "Cadastrar/Editar - Cores", autoOpen: true });
		},
		error: function(x,t,m){ 
			console.log(x.responseText+'\n'+t+'\n'+m); 
		}
	});
});

/**-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**
 Editar variantes das cores
	**-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**/
$("#aba5").on("click", '#submit-cores', function(e){
	e.preventDefault();
	
	var DataSerialize = $("tr.formulario-produto-cores").find('input[name], select[name]').serialize();
	
	$.ajax({
		url: this.href,
		type: "post",
		data: DataSerialize,
		cache: false,
		success: function( str ){ 
			var list = $("<div/>", { html: str });
			$("#aba5").html( list.find("#aba5").html() ); 
		},
		error: function(x,t,m){ 
			console.log(x.responseText+'\n'+t+'\n'+m); 
		}
	});
});