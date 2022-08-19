var GLOBALS = GLOBALS || {}, 
    JanelaModal = JanelaModal || $("#janela-cadastros");

// Automatically cancel unfinished ajax requests 
// when the user navigates elsewhere.
(function($) {
	var xhrPool = [];
	$(document).ajaxSend(function(e, jqXHR, options){
		xhrPool.push(jqXHR);
	});
	$(document).ajaxComplete(function(e, jqXHR, options) {
		xhrPool = $.grep(xhrPool, function(x){ return x !== jqXHR});
	});
	var abort = function() {
		console.log("aaa");
		$.each(xhrPool, function(idx, jqXHR) {
			jqXHR.abort();
		});
	};

	var oldbeforeunload = window.onbeforeunload;
	window.onbeforeunload = function() {
		var r = oldbeforeunload ? oldbeforeunload() : undefined;
		if (r === undefined) {
			// only cancel requests if there is no prompt to stay on the page
			// if there is a prompt, it will likely give the requests enough time to finish
			abort();
		}
		return r;
	};
})(jQuery);

Object.defineProperty(Array.prototype, 'chunk', {
	value: function(chunkSize) {
		return this.reduce(function(previous, current) {
			var chunk;
			if (previous.length === 0 || 
					previous[previous.length -1].length === chunkSize) {
				chunk = [];
				previous.push(chunk);
			}
			else {
				chunk = previous[previous.length -1];
			}
			chunk.push(current);
			return previous;
		}, []);
	}
});

resize_image = (function(file, size, callback) {
	var fileTracker = new FileReader;
	fileTracker.onload = function() {
		var image = new Image();
		image.onload = function(){
			var canvas = document.createElement("canvas");
			
			// if(image.height > size) 
			// {
				// image.width *= size / image.height;
				// image.height = size;
			// }
			
			if(image.width > size) 
			{
				image.height *= size / image.width;
				image.width = size;
			}
			
			var ctx = canvas.getContext("2d");
			ctx.clearRect(0, 0, canvas.width, canvas.height);
			canvas.width = image.width;
			canvas.height = image.height;
			ctx.drawImage(image, 0, 0, image.width, image.height);
			callback(canvas.toDataURL("image/jpg"));
		};
		image.src = this.result;
	}

	fileTracker.readAsDataURL(file);

	fileTracker.onabort = function() {
		alert("The upload was aborted.");
	}

	fileTracker.onerror = function() {
		alert("An error occured while reading the file.");
	}
});

validaExtensao = function( id ) {
	console.log( $( "#" + id + "" ).val() );
	var extPermitidas = ["jpeg", "jpg", "png", "gif", "JPEG", "JPG", "PNG", "GIF"];
	var extArquivo = $( "#" + id + "" ).val().split(".").pop();

	if(typeof extPermitidas.find(function(ext){ return extArquivo == ext; }) == 'undefined') {
		alert('Extensão "' + extArquivo + '" não permitida!');
		$("#" + id + "").val("").empty();
		return false;
	}
	return true;
};

/**
 * Carrega e clona as imagens dinamicamente
 */
jQuery.fn.preview_img_clone = function( files, options) {
	
	var zuzim = $(this);
	
	zuzim.parent().parent().find("div").first().html("");

	$( files ).each(function (i, file) {
		if ( file.type.match("image.*") ) {
			resize_image(file, options.width, function( result ) {
				zuzim.parent().parent().find("div").first().append([
					$("<a/>", {
						class: "img-preview",
						html: [
							$("<img/>", { src: result }) 
						]
					})
				]);
			});
		}
		else {
			alert("Erro ao ler os arquivos.");
		}
	});
};

// carregar a imagem antes do envio
jQuery.fn.preview_img = function(options) {
    var $this = this;
	var defaults = {
		img: "imagemnull",
		width: 95,
	};
	settings = $.extend( {}, defaults, options );
	
	console.log($this);

    if (typeof (FileReader) !== "undefined") {

        var image_holder = $this.parents().find((settings.img.indexOf("#") != -1 ?settings.img : "#" + settings.img));
			image_holder.empty();
			
        var reader = new FileReader();
        reader.onload = function (e) {
            image_holder.attr({ "src": e.target.result, width: settings.width });
        };
        
        image_holder.show();
        reader.readAsDataURL($(this)[0].files[0]);
    } 
    else {    
        alert("This browser does not support FileReader.");
    }
};

jQuery.fn.my_search = function( options ) {
	var settings, input, filter, table, tr, td, i;
  
	var defaults = {
		target: "#table",
	};
	settings = $.extend( {}, defaults, options );
	
	return $( document ).on("keyup", $( this ), function ( a, b ) {
		
		var value = $( a.target ).val().toLowerCase().trim();
		
		$.each( $( settings.target ).find( "tr" ), function ( index ) {
			
			if ( !index ) return;
			
			$(this).find("td").each(function () {
				
				var id = $(this).text().toLowerCase().trim();
				
				var not_found = (id.indexOf(value) == -1);
				
				$(this).closest('tr').toggle(!not_found);
				
				return not_found;
			});
		});
	});
};
	
jQuery.fn.replace_url_params= function ( parans ) {
	var pattern = new RegExp('(\\?|\\&)('+parans.name+'=).*?(&|$)'),
		newUrl = parans.url;
		
	if( newUrl.search(pattern) >= 0 ) 
		newUrl = newUrl.replace(pattern,'$1$2' + parans.value + '$3');
	else 
		newUrl = newUrl + (newUrl.indexOf('?')>0 ? '&' : '?') + parans.name + '=' + parans.value
	
	return newUrl
};

// contador de caracteres
jQuery.fn.counter = function() {
	
	$( this ).each(function() {
		var 
			max = $(this).attr("maxlength"), 
			val = $(this).val(), 
			cur = 0,
			counter = $("<font>", { class: "counter show" });
			
		if(val) //value="", or no value at all will cause an error
			cur = val.length;
		
		var left = max-cur;
		if( val.length ) {
			$(this).after([ counter.html(left.toString() + " caracteres digitados") ]);
		}
		
		$(this).on("keyup", function(i) {
			var max = $(this).attr('maxlength');
			var val = $(this).val();
			var cur = 0;
			
			if(val)
				cur = val.length;
			var left = max-cur;
			
			
			if( $( this ).next(".counter").length === 0) {
				$(this).after([ $(this).after([ counter.html(left.toString() + " caracteres digitados") ]) ]);
			} else if( $( this ).next(".counter").length && cur === 0 ) {
				$(this).next(".counter").remove();
			}
			
			$(this).next(".counter").html(left.toString() + " caracteres restantes");
			if(left <= 3)
			{
				$(this).next(".counter").css('color', '#ff0000');
			} 
			else 
			{
				$(this).next(".counter").css('color', '#666666');
			}
			return this;
		});
	});
  return this;
};

$(document).ready(function(){
    
    $.ajaxSetup({ cache: false });
    
	$("input.peso").mask("#.##0", { reverse: true });
    
    // SELECIONA/EXCLUIR TODOS DADOS SELLECIONADOS
	$(document).on("click", "input[data-action=selecionados-exclusao-all]", function(e){
		var $this = $(this).parents(), 
			$all = $this.find("input[data-action=selecionados-exclusao]").serialize();
			
			if( ! $all.length )
				$this.find("input[data-action=selecionados-exclusao]").prop({ "checked": true });
			else
				$this.find("input[data-action=selecionados-exclusao]").prop({ "checked": false });
	});
    
    // SELECIONA/EXCLUIR TODOS DADOS SELLECIONADOS
    $(document).on("click", "[data-action=btn-excluir-varios]", function(e) {
        e.preventDefault();
		var $this = $(this).parents(), 
			$link = $this.find("[data-href]").attr("data-href"),
			$all = $this.find("input[data-action=selecionados-exclusao]").serialize(),
			$IsLoaded = JanelaModal.is( ":visible" ) ? true : false ;
		
		if( ! $all.length )
			return confirm("Selecione ao menos um para excluir!");
		
		if( ! confirm("Deseja realmente excluir!") )
			return false;
		
		$.ajax({
			url: $link,
			type: "post",
			data: $all,
			success: function( str ) {
				var list = $("<div/>", { html: str });
				if( ! $IsLoaded ) {
					$("#div-edicao").html( list.find("#div-edicao").html() );
				} else {
					JanelaModal.html( list.find("#div-edicao").html() );
				}
			}, 
			error: function(a,b,c){
				console.log( a.responseText+"\n"+b+"\n"+c );
			}
		});
	});

	// SELECIONA/EXCLUIR TODOS DADOS SELLECIONADOS
    $(document).on("click", "[data-action='btn-add-varios'], [data-action='btn-alterar-varios']", function(e) {
        e.preventDefault();
		var $this = $(this).parents(), 
			$link = $this.find("[data-href]").attr("data-href"),
			$all = $this.find("input[data-action=selecionados-exclusao]").serialize(),
			$IsLoaded = JanelaModal.is(":visible") ? true : false ;
		
		if( ! $all.length )
			return confirm("Selecione ao menos um para adicionar!");
				
		$.ajax({
			url: $link,
			type: "post",
			data: $all,
			success: function( str ) {
				var list = $("<div/>", { html: str });
				if( ! $IsLoaded )
					$("#div-edicao").html( list.find("#div-edicao").html() );
				else
					JanelaModal.html( list.find("#div-edicao").html() );
			}, 
			error: function(a,b,c){
				console.log( a.responseText+"\n"+b+"\n"+c );
			}
		});
	});
    
    // remove os icones dos tamanhos e cores
    $(document).on("click", "[data-remove-imgs]", function(e) {
        e.preventDefault();
        var href = this.href||e.target.href,
            icon = "/plataformaimgs/65x65/square/imgs_sem-foto-produto.png",
            img_id = $(e.target).attr("data-remove-imgs");
		
        // apenas limpa o input
        if( href.search('#') > '0') {
            $("img#"+img_id).attr({"src": icon });
            $(e.target).parents("form").get(0).reset();
        } 
//        else {
//            $("img#"+img_id).attr({"src": icon });
//            $(e.target).parents("form").get(0).reset();
//            $.ajax({
//                url: href,
//                success: function( str ) {
//                    var list = $("<div/>", { html: str });
//                    $("#div-edicao").html( list.find("#div-edicao").html() );
//                }, 
//                error: function(a,b,c){
//                    console.log( a.responseText+"\n"+b+"\n"+c );
//                }
//            });
//        }
    });
    
	// $(document).on("click", "a", function(){
		// var href = this.href || e.target.href;		
		// if( href.search('excluir') > '0')
			// if( ! confirm("Deseja realmente excluir!") ) return false;
	
	// });
});