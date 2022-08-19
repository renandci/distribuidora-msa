// var cancelar_nfe_xml = $("<form/>", { id: "#cancelar_nfe_xml" }).dialog({
		// title: "Cancelamento de NF-e",
		// autoOpen: false,
		// modal: true,
		// width: 757,
		// height: 576
	// }),
	// init_date_validade = (function(e) {
		// var text = $(e.target).find("option:selected").attr("data-validade"),
			// text_is_cancel = $(e.target).find("option:selected").attr("data-validade-cancel");
		
		// cancelar_nfe_xml.find("#data-validade").removeClass("hidden").html(text);
		
		// if( text_is_cancel > "0" )
			// cancelar_nfe_xml.find("button").fadeOut(0);
		// else
			// cancelar_nfe_xml.find("button").fadeIn(0);
	// });
// // Criar um novo form para o cancelamento
// // $( "#button_cancelar_nfe_xml" ).click( function (  ) {
// $( "#div-edicao" ).on("click", "#button_cancelar_nfe_xml", function (  ) {
	// cancelar_nfe_xml.html([
		// $("<div/>", {
			// class: "row",
			// html: [
				// $( "<div/>", { 
					// class: "col-md-12", 
					// html: [
						// $( "<div/>", {
							// class: "alert alert-danger ft12px text-center", 
							// html: [
								// $("<i/>", { class: "fa fa-exclamation-triangle mr5"}),
								// "As notas podem ser canceladas no prazo máximo de até 24h após a data de emissão."
							// ] 
						// } ),
					// ] 
				// } ),
				// $( "<div/>", { 
					// class: "col-md-12", 
					// html: [
						// $( "<div/>", { 
							// id: "data-validade",
							// class: "alert alert-info hidden ft12px text-center", 
							// html: "--"
						// } ),
					// ] 
				// } ),
				// $( "<div/>" , {
					// class: "col-md-6",
					// html: [
						// $( "<fieldset/>" , {
							// html: [
								// $( "<legend/>", {
									// class: "bold",
									// html: "Dados do Emitente" 
								// } ),
								// $( "<div/>",{
									// class: "clearfix",
									// html: [
										// $( "<label/>", {
											// html: "Emitente *" 
										// } ),
										// $( "<select/>", {
											// class: "w100",
											// name: "id_emitente",
											// html: [
												// $("<option/>", { value: "", text: "Selecione um emitente" }),
												// <?php /*
												// $emitentes = NfeEmitentes::connection()->query(sprintf('select * from nfe_emitentes where loja_id=%u order by id asc', $CONFIG['loja_id']));
												// while( $emit = $emitentes->fetch() ) { ?>
												// $("<option/>", {
													// "value": "<?php echo (!empty($emit['id']) && $emit['id'] > 0 ? $emit['id'] : '');?>", "text": "<?php echo $emit['razaosocial']?>",
													// <?php echo ($emitentes->rowCount() == 1?'"selected": "selected"':'')?>
												// }),
												// <?php } */?>
											// ]
										// })
									// ]
								// } )
							// ]
						// })
					// ]
				// }),
				// $( "<div/>" , {
					// class: "col-md-6",
					// html: [
						// $( "<fieldset/>" , {
							// html: [
								// $( "<legend/>", {
									// class: "bold",
									// html: "Dados do Emitente" 
								// } ),
								// $( "<div/>",{
									// class: "clearfix",
									// html: [
										// $( "<label/>", {
											// html: "NF-e *" 
										// } ),
										// $( "<select/>", {
											// class: "w100",
											// name: "id_nota",
											// change: function(e){ return init_date_validade(e); },
											// html: [
												// <?php /* 
												// $notas = NfeNotas::connection()->query(sprintf('select * from nfe_notas where status != 3 and id_pedido=%u order by id desc', $PEDIDO_ID)); 
												// while( $rNfe = $notas->fetch() ) { ?>
													// $("<option/>", {
														// "value": "<?php echo $rNfe['id'];?>", 
														// "data-validade": "NF-e <?php echo substr($rNfe['chavenfe'], -18, 8)?> gerada <?php echo date('d/m/Y H:i', strtotime($rNfe['created_at']));?>",
														// "data-validade-cancel": "<?php echo (strtotime($rNfe['created_at']) > strtotime(date('Y-m-d H:i')) ? 1 : 0 )?>",
														// "text": "<?php echo (substr($rNfe['chavenfe'], -18, 8)).($rNfe['status']==2?' - Nota Cancelada':'').(!empty($rNfe['nrreccan'])?' recibo ' . $rNfe['nrreccan']:'')?>",
														// "style": {<?php echo ($rNfe['status'] == 2 ? '"background-color":"#ffecef"':'') . ($rNfe['status'] == 1 ? '"background-color":"#c4efae"':'')?>}
													// }),
												// <?php } */ ?>
											// ]
										// })
									// ]
								// })
							// ]
						// })
					// ]
				// }),
				// $( "<div/>" , {
					// class: "col-md-12",
					// html: [
						// $( "<fieldset/>" , {
							// html: [
								// $( "<legend/>", {
									// class: "bold",
									// html: "Digite o motivo do cancelamento" 
								// } ),
								// $( "<div/>",{
									// class: "clearfix",
									// html: [
										// $( "<label/>", {
											// html: "Descrição *" 
										// } ),
										// $("<input/>",{
											// type: "text",
											// name: "xMotivo",
											// class: "w100 mb15",
											// maxlenght: 255
										// }),
										// $( "<button/>", {
											// class: "btn btn-danger",
											// type: "submit",
											// html: "Cancelar NF-e"
										// } ),
									// ]
								// })
							// ]
						// } )
					// ]
				// })
			// ]
		// })
	// ]).dialog("open").attr({"method": "post", "action": "/adm/nfe/nfe-cancelar.php"});
	
	// cancelar_nfe_xml.find("select[name=id_nota]").change();
// } );

// // Form para o cancelamento ajax
// cancelar_nfe_xml.ajaxForm({
	// beforeSubmit: function( formData, jqForm, options ) {
		
		// var empty = cancelar_nfe_xml.find( "select[name] :selected, input[name]" ).filter( function(  ) {
			// return this.value === "";
		// } );

		// if( empty.length ) {
			// alert("Preenchar todos os campos (*)!");
			// return false;
		// }
		// else {
			// return true;
		// };
	// },
	// type: "post",
	// success: function( str ) {
		// var list = $( "<div/>", { html: str } );
		// $( "#div-edicao" ).html( list.find("#div-edicao").html() );
		// $.ajax({
			// // url: "/adm/vendas/vendas-detalhes.php?id=<?php echo $PEDIDO_ID?>",
			// url: "/adm/vendas/vendas-detalhes.php?id=<?php echo $PEDIDO_ID?>",
			// success: function(str){
				// var list = $( "<div/>", { html: str } );
				// $( "#div-edicao" ).html( list.find("#div-edicao").html() );
			// }
		// });
	// },
	// beforeSend: function(){
		// cancelar_nfe_xml.find("button[type]").append([ $("<i/>", {class: "mr5 fa fa-spinner fa-spin"}) ]);
		// $("#status-alteracao").fadeIn(0).html("Cancelando NF-e aguarde...");
	// },
	// complete: function(){
		// cancelar_nfe_xml.dialog("close");
	// },
	// resetForm: true
// });