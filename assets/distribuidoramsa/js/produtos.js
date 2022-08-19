/*!
 * @author Renan Henrique <renan@dcisuporte.com.br>
 * @company Data Control Informatica - 16 3262 1365
 * @return Funcoes para a pagina de produtos - WWW.TUDOENXOVAIS.COM.BR
 */
$(document).ready(function(){
	
	$.ajaxSetup({ cache : false });
	
	// $( "#slider" ).slider({
		// value:100,
		// min: 0,
		// max: 500,
		// step: 5,
		// slide: function( event, ui ) {
			// $( "#amount" ).val( "$" + ui.value );
		// }
	// });
	// $( "#amount" ).val( "$" + $( "#slider" ).slider( "value" ) );
	
	function showValues() {
		var fields = $( ":input" ).serializeArray();
		$( "#results" ).empty();
		jQuery.each( fields, function( i, field ) {
		  $( "#results" ).append( field.value + " " );
		});
	}
	
	
	var $recarregar = $("#recarregar-html").parent(), $form = $("#filtros").parent();
	$(document).on("click", "#filtros input[type=checkbox]", function(e){
		
		var $filtros = $.param( $("#filtros input:checked") );
        
		
		console.log( $filtros );
		
		$.get(window.location.href, $filtros, function(str){
//			var $html = $(str).find("#pegar-filtros");
//			$("#recarregar-filtros").html($html.html());
//            console.log($html.html());
//            console.log($html);
			
            var $html = $(str).find("#recarregar-html").parent().html();
			$("#recarregar-html").html($html);
		}).fail(function(x,m,t){
			console.log(x.responseText+'\n'+m+'\n'+t);
		}).done(CarregarImagens);		
	});
	
});