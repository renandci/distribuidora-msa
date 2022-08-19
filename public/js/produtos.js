/*!
 * @author Renan Henrique <renan@dcisuporte.com.br>
 * @company Data Control Informatica - 16 3262 1365
 * @return Funcoes para a pagina de produto
 */
$(document).ready(function(){
	
	var checkeds_filtros = null,
        recarregar_html = $("#recarregar-html, #cx-usuario-topo"),
        produtos_filtros = $("#recarregar-html"),
        produtos_site = $("#produtos_site"),
        filtros_reload = $("#filtros_reload");
	
	$.ajaxSetup({
		success: function( str ) {
			var list = $("<div/>", { html: str });
			$("#menus-lateral").html(list.find("#menus-lateral").html());
			$("#recarregar-html").html(list.find("#recarregar-html").html());
			window.history.pushState({}, list.find("title").text(), list.find("span.paginacao").attr("href"));
		},
		beforeSend: function() {
			$("#aminacao-site").fadeIn(0);
			if( $( window ).width() < 768 && $("#meutopo").attr("visible") === "true" )
				$("#menutopo-voltar").trigger("click");
		},
		complete: function () {
			
			$("#aminacao-site").fadeOut(0);
			
			if( $( window ).width() < 768 )
				$("#recarregar-html").find(".menus-lateral").addClass("menus-lateral-pushindex");
		},
		error: function ( a,b,c ) {
			// console.log(a.responseText+"\n"+b+"\n"+c);
		}
	});
	
	window.onpopstate = function(e){
		var url = e.target.window.location.href||window.location.href;
		$.ajax({
            url: url
		});
	};
	
	// Click inputs filter
    $(document).on("click", "input[type=checkbox]", function(e) {

		var checked_subgrupo = $(e.target),
			checked_subgrupo_name = e.target.name;
			
		// Caso selecione apenas o subgrupo
		// Tenta checar o parent do grupo
		if(checked_subgrupo_name === "filtro[subgrupo][]") {
			var data_check = $(checked_subgrupo).data("check");
			$("label[for='"+ data_check +"']").trigger("click");
		}

		var checkeds_filtros = $.param( $("input[type=checkbox]:checked") ),
			href = window.location.pathname,
			href = href.indexOf("produtos") !== -1 ? href:"/produtos"; 
			// href.split("?")[0] === "/" ? "/produtos" : href;

		console.log(checkeds_filtros);
		// console.log(window.location.pathname);
		// console.log(href);
		// console.log("/produtos".split("?")[0]);
		// console.log(window.location.href.split("?")[0]);
		
        $.ajax({
            url: href, // window.location.href.split("?")[0],
            data: checkeds_filtros
		});
		
		$("html[data-store]").stop().animate({scrollTop: 0}, 550);
		
    });
});