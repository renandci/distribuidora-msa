/*!
 * @author renan henrique <renan@dcisuporte.com.br>
 * @company Data Control Informatica - 16 3262-1365
 * @return Funcoes para o site
 */
$(document).ready(function(){
	$('input.input-pesquisar')
    .blur(function(){
        $('.desenho-campo-busca')
        .removeClass('input-hover');
    })
    .focus(function(){ 
        $('.desenho-campo-busca')
        .addClass('input-hover');
    });
    
	$('.input-pesquisar').keyup(function(){
		if($(this).val() !== '') {
			$.ajax({
				url : '../pesquisa-rapida',
				type : 'post',
				data : { pesquisar : this.value },
				dataType : 'json',
				beforeSend : function(){$('.retornar-pesquisa').css('border-width', '0px 1px 3px 1px','min-height', '0');},
				success : function(str){ $('.retornar-pesquisa').html(str.pesquisa); },
				error : function(x,t,m){ console.log(m); }								
			});
		}  else {
			$('.retornar-pesquisa').empty();
		}
	});
    /*
     * TAG MENU
     */
	$("a#abre-menu").click(function(e){
        $(this).find(".ii").toggleClass("fa-sort-desc fa-sort-asc").parent().next().toggleClass('tag-block');
		e.preventDefault();
	});
	
	$('i.icon-lista-menu-mobile').click(function(){
        $(this).toggleClass("fa-sort-desc fa-sort-asc").next().toggleClass('mostra-menu-topo-mobile');
//		var iconClick = $(this);
//		if( iconClick.attr('value') === 0 ) {
//			iconClick.attr({'value':'1'}).removeClass('fa-sort-desc').addClass('fa-sort-asc');
//			$('ul.mostra-menu-' + iconClick.attr('icon') ).addClass('mostra-menu-topo-mobile');
//		} else {
//			iconClick.attr({'value':'0'}).removeClass('fa-sort-asc').addClass('fa-sort-desc');
//			$('ul.mostra-menu-' + iconClick.attr('icon') ).removeClass('mostra-menu-topo-mobile');
//		}
	});
    
    
    /**
     * Novo modelo do meutopo
     */
    $("#cx-usuario-topo").on("click", "span.open-close, font.open-close", function(e) {
        e.preventDefault();
		
		console.log($(this).find("#produtos-site").length);
		
		// if deve montar outro menu a partir do mesmo click que há dentro do menu de login
		// modificado para burros
		if( $(this).find("#produtos-site").length === 1 ){
			// $(".menus-site").click();
		} 
		else 	
		if($("#meutopo").attr("visible")==="false"){
            $("#meutopo").animate({marginLeft: "0"}, 300).attr({"visible":"true"});
            $("body").css({"overflow-y":"hidden"});
        }
        else { 
            $("#meutopo").animate({marginLeft: "-100%"}, 200).attr({"visible":"false"});
            $("body").css({"overflow-y":"auto"});
        }
    });

    /**
     * Novo menu lateral do site
     */
    $(document).on("click", "font.menus-site, #produtos-site", function(e) {
        
        e.preventDefault();
        // vamos verficar se existe, caso nao, vamos fazer uma copia do menu com ajax
        if( $(document).find(".menus-lateral-pushindex").length === 0 && window.location.pathname.split("/")[1].trim() === "produtos") {
			$.ajax({
                url: "/index",
                success: function(str){
                    var list = $("<div/>", { html: str });
					$(".menus-lateral").html( list.find(".menus-lateral").html() );
                },
                complete: function(){
                    $(document).find(".menus-lateral").addClass("menus-lateral-mobile menus-lateral-pushindex").animate({marginLeft: "0"}, 330).attr({"visible":"true"});
                }
            });
		}
		else
        if( $(document).find(".menus-lateral").length === 0 ) {
		
            $.ajax({
                url: "/index",
                success: function(str){
                    var list = $("<div/>", { html: str });
                    $("body").append([
                        $("<ul/>", { class: "menus-lateral", "visible": "false", html: [
                                list.find(".menus-lateral").html()
                            ] 
                        })
                    ]);
                },
                complete: function(){
                    $(document).find(".menus-lateral").addClass("menus-lateral-mobile").animate({marginLeft: "0"}, 330).attr({"visible":"true"});
                }
            });
            return false;
        } 
        else {

            if($(document).find(".menus-lateral").attr("visible")==="false"){
                $(document).find(".menus-lateral").addClass("menus-lateral-mobile").animate({marginLeft: "0"}, 330).attr({"visible":"true"});
                $("body").css({"overflow-y":"hidden"});
            }
            else { 
                $(document).find(".menus-lateral").addClass("menus-lateral-mobile").animate({marginLeft: "-100%"}, 200).attr({"visible":"false"});
				$("body").css({"overflow-y":"auto"});
            }        
        } 
    });
    
    /*
     * NÃO ADICIONAR NADA APOS ESSA LINHA DE COMANDO
     */
    if ( $( window ).width() < 768 ) 
		return false;
	$('.lista-menu-topo').hover(function(){
        $(this)
        .addClass('add-link-menu-topo')
        .children('.lista-submenus-topo')
        .slideToggle(200);
    }, function () {
        $(this).delay(400).queue(function(ex){
            $(this).removeClass('add-link-menu-topo');
            $(this).children('.lista-submenus-topo').fadeOut(50);
            ex();
        });
    });
	
    var $HeightTopo = $("#topo-movel").height() + $("#bg-topo-costurado").height();
    $( window ).scroll(function (){
		if ( $( window ).width() > 768 ){
			if ( $( this ).scrollTop() >= $HeightTopo ){
                if( $("#topo-movel").hasClass("add-topo-movel") ) return false;
				$("#topo-movel")
                    .addClass("add-topo-movel")
                    .fadeIn(900)
                    .find("#img-new-cart")
                    .fadeOut(0);
                $("body")
                    .css({"padding-top":$HeightTopo});
			} else {
				$("#topo-movel")
                    .removeAttr("style")
                    .removeClass("add-topo-movel")
                    .find("#img-new-cart")
                    .fadeIn();
                $("body")
                    .css({"padding-top":0});
			}
		} else {
			$("#topo-movel")
                .removeAttr("style")
                .removeClass("add-topo-movel")
                .find("#img-new-cart")
                .fadeIn();
            $("body")
                .css({"padding-top":0});
		}
	});

	
   
});