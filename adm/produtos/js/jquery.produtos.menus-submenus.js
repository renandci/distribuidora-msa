/**-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**
 menus e submenus
	**-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**/
CarregarMenusSubMenus = function(){
	$.ajax({
		url: "/adm/produtos/produtos-grupos-subgrupos.php?codigo_id=" + GLOBALS.codigo_id,
		success: function( str ){ 
			var list = $("<div/>", { html: str });
			$("#aba3").html( list.find("#aba3").html() ); 
		},
		error: function(x,t,m){ 
			console.log(x.responseText+'\n'+t+'\n'+m);
		}
	}); 
};
// CarregarMenusSubMenus();

$("a[href=#aba3]").on("click", CarregarMenusSubMenus);

$("#aba3").on("click", "a.deletar_grupos", function(e){
	if( ! confirm("Deseja realmente excluir!") ) return false;
	e.preventDefault();
	
	$.ajax({
		url: this.href,
		type: "post",
		data: { acao : "btnacoes" },
		cache: false,
		success: function( str ){ 
			var list = $("<div/>", { html: str });
			$("#aba3").html( list.find("#aba3").html() ); 
		},
		complete: function(){
			CarregarMenusSubMenus();
		},
		error: function(x,t,m){ 
			console.log(x.responseText+"\n"+t+"\n"+m);
		}
	});
});

$("#aba3").on("click", "#btn-adicionar-novoGrupo", function(e){
	e.preventDefault();
	$.ajax({
		url: "/adm/produtos/produtos-grupos-subgrupos.php?codigo_id="+GLOBALS.codigo_id,
		type: "post",
		data: { 
			acao: "adicionar-grupos", 
			id_grupo: $("#id_grupo :selected").val(), 
			codigo_id : GLOBALS.codigo_id
		},
		success: function( str ){ 
			CarregarMenusSubMenus();
		},
		error: function(x,t,m){ 
			console.log(x.responseText+"\n"+t+"\n"+m);
		}
	});
});


$("#aba3").on("click", "#btn-cadastrar-grupos", function(e){
	e.preventDefault();
	$.ajax({
		url: this.href,
		cache: false,
		success: function( str ){ 
			var list = $("<div/>", { html: str });
			JanelaModal
				.dialog({ title: "Cadastrar/Editar - Grupos", autoOpen: true })
				.html( list.find("#div-edicao").html() );
		},
		error: function(x,t,m) { 
			console.log(x.responseText+"\n"+t+"\n"+m);
		}
	});
});

/**-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**
 Excluir|Paginar os menus|submenus dentro da tela modal
	**-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**/
JanelaModal.on("click", "a.btn-cadastros-grupos, a.btn-paginacao", function(e){
	e.preventDefault();
	$.ajax({
		url: this.href,
		cache: false,
		success: function( str ){ 
			var list = $("<div/>", { html: str });
			JanelaModal
				.html( list.find("#div-edicao").html() );
		},
		complete: function(){
			CarregarMenusSubMenus();
		},
		error: function(x,t,m){ 
			console.log(x.responseText+"\n"+t+"\n"+m);
		}
	});
});

/**-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**
 Excluir|Paginar os sub menus dentro da tela modal
	**-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**/
$("#aba3").on("click", "a.btn-adicionar-subgrupos", function(e){
	e.preventDefault();
	$.ajax({
		url: this.href,
		success: function( str ){ 
			var list = $("<div/>", { html: str });
			JanelaModal
				.dialog({ title: "Cadastrar/Editar - SubGrupos", autoOpen: true })
				.html( list.find("#div-edicao").html() );
		},
		error: function(x,t,m){ 
			console.log(x.responseText+"\n"+t+"\n"+m);
		}
	});
});

/**-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**
 Adicionar novo submenus no menu
	**-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**/
JanelaModal.on("click", "a.btn-adicionar-novo-sub-grupo", function(e){
	e.preventDefault();
	
	var HrefSerialize = e.target.href||$(this).attr("href"),
		DataSerialize = HrefSerialize.split("?")[1]+"&acao=adicionar-subgrupos";
		
		console.log(DataSerialize);
		
	$.ajax({
		url: "/adm/produtos/produtos-grupos-subgrupos.php?codigo_id=" + GLOBALS.codigo_id,
		type: "post",
		data : DataSerialize,
		success: function( str ) {
			var list = $("<div/>", { html: str });
			$("#aba3").html( list.find("#aba3").html() );
		},
		complete: function() {
			$.ajax({
				url: HrefSerialize,
				success: function( str ){ 
					var list = $("<div/>", { html: str });
					JanelaModal
						.html( list.find("#div-edicao").html() );
				},
				error: function(x,t,m){ 
					console.log(x.responseText+"\n"+t+"\n"+m);
				}
			});
		},
		error: function(x,t,m){ 
			console.log(x.responseText+"\n"+t+"\n"+m);
		}
	});
});
/**-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**
 Adicionar novo menu
	**-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**/
JanelaModal.on("click", "a.btn-adicionar-novo-grupo", function(e){
	var href = e.target.href||this.href,
		href = href.split("grupoid=")[1];
	$("#aba3")
		.find("#id_grupo")
		.children("[value=" + href + "]")
		.attr("selected", true);
		
	$("#aba3")
		.find("a#btn-adicionar-novoGrupo")
		.delay(2000)
		.trigger("click");
	
	
	$(this).parent().parent().next().remove();
	$(this).parent().parent().remove();
	
	e.preventDefault();
});