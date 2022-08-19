$(document).ready(function(){
	
	$("#div-edicao").on("click", ".btn-cadastros-banners", function(e){
		e.preventDefault();
		$.ajax({
			url: this.href,
			cache: false,
			success: function( str ) {
				var list = $("<div/>", { html: str });
				JanelaModal
					.dialog({ title : "Edição de Banners", autoOpen : true })
					.html( list.find("#div-edicao").html() );
			}
		}); 
	});
	
	$("#div-edicao").on("click", ".btn-edicao-bannner", function(e){
		e.preventDefault();
		var $this = $(this),
			$action = $this.attr("href")||e.target.href,
			$FormDataSerialize = $( $this.parent() ).find('input[name], select[name], textarea[name]').serialize();

		$.ajax({
			url: $action,
			type: "post",
			data: $FormDataSerialize,
			cache: false,
			success: function( str ){ 
				var list = $("<div/>", { html: str });
				$("#div-edicao").html( list.find("#div-edicao").html() );
			}
		});
	});
	
	JanelaModal.on("change", "input[type=file].banners", function() {
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
			uploadProgress	: function(event,position,total,percentComplete){
				$("#status-alteracao").fadeIn(0).html("Enviando imagem "+percentComplete+"%");
			},
			complete: function(){
				JanelaModal.dialog("close");
			},
			success: function( str ) {
				var list = $("<div/>", { html: str });
				JanelaModal
					.html( list.find("#div-edicao").html() );
				
				$.ajax({
					url: window.location.href,
					success: function( str ) {
						var list = $("<div/>", { html: str });
						$("#div-edicao").html( list.find("#div-edicao").html() );
					}
				});
			},
			error: function(x,t,m){ 
				alert("Não consegui enviar a imagem!\nTente recarregar a página.");
				console.log( x.responseText + "\n" + t + "\n" + m ); 
			}
		});
	});
});