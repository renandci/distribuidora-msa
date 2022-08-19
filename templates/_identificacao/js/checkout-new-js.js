if( "<?php echo $CONFIG['clearsale']['mapper']?>" !== "0" ) {
	(function (a, b, c, d, e, f, g) {
	a['CsdpObject'] = e; a[e] = a[e] || function () {
	(a[e].q = a[e].q || []).push(arguments)
	}, a[e].l = 1 * new Date(); f = b.createElement(c),
	g = b.getElementsByTagName(c)[0]; f.async = 1; f.src = d; g.parentNode.insertBefore(f, g)
	})(window, document, 'script', '//device.clearsale.com.br/p/fp.js', 'csdp');
	csdp("app", "<?php echo $CONFIG['clearsale']['mapper']?>");
	csdp("outputsessionid", "PagamentoSessionIdClearSale");
}

$.ajax = (($oldAjax) => {
	// on fail, retry by creating a new Ajax deferred
	function check( a, b, c ) {
	var shouldRetry = b != 'success' && b != 'parsererror';
	if( shouldRetry && --this.retries > 0 )
		setTimeout(() => { $.ajax(this) }, this.retryInterval || 100);
	}

	return settings => $oldAjax(settings).always(check)
})($.ajax);

if (!String.prototype.trim) {
	String.prototype.trim = function () {
		return this.replace(/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g, '');
	};
}

/**
 * Acoes de retorno ajax
 * @param {type} param
 */
$(document)
.ajaxSend(function(evt, request, settings) {
	// console.log("Starting request at " + settings.url + ".");
})
.ajaxStart(function() {
	console.log("Inicio");
	$("#aminacao-site").fadeIn(0);
	$("button[type=submit]").css({
		"text-indent": "-99999px",
		"background-image": "url(<?php echo Imgs::src('spinner-white.gif', 'imgs');?>)",
		"background-repeat": "no-repeat",
		"background-position": "center center"
	}).attr({"disabled": true });
})
.ajaxComplete(function(a, b, c) {
	console.log("Completo");

	$("button[type=submit]").attr({"disabled": false }).removeAttr("style");

	if( "<?php echo $CONFIG['pagamentos']['mp']?>" !== 0 ) {
		// addEvent(document.querySelector('input[data-checkout="cardNumber"]'), "keyup", guessingPaymentMethod);
		addEvent(document.querySelector('input[data-checkout="cardNumber"]'), "keyup", clearOptions);
		addEvent(document.querySelector('input[data-checkout="cardNumber"]'), "change", guessingPaymentMethod);
	}

})
.ajaxStop(function(event, request, settings) {

	console.log("Fim");

	Checkout.finalcompra();

	var atacadista = $("#new-checkout-reload").find("[data-atacadista]").attr("data-atacadista"),
		atacadista_min = $("#new-checkout-reload").find("[data-min]").attr("data-min"),
		atacadista_max = $("#new-checkout-reload").find("[data-max]").attr("data-max");

	if( atacadista > 0 ) {
		$($("<div/>", {
			id: "alert-info", class: "alert alert-info",
			html: [
				$("<script>", { html: $("#finalizar-pedido").addClass("disabled").attr({ "disabled": "disabled", "type": "button" }).css({"opacity": "0.5"}) }),
				( atacadista_min > 0 ? "Você não atingiu seu limite de compra." : null ),
				( atacadista_max > 0 ? "Você já atingiu seu limite de compra." : null ),
			]
		})).prependTo("#new-checkout-reload");
	}
	else {
		$("#finalizar-pedido").removeClass("disabled").removeAttr("disabled").attr({"type": "submit"}).css({"opacity": "1"});
		$( "#alert-info" ).remove();
	}

	$("#aminacao-site").fadeOut(10);
	$("#new-checkout-reload").find("input[name=TipoPessoa]:checked").trigger("click");

	$("#new-checkout-reload").on("focus", "input[name],select[name]", function(e) {
		$(e.target).parent().addClass("border-in");
	}).on("blur", "input[name]", function(e){
		$(e.target).parent().removeClass("border-in");
	});

	if( "<?php echo $CONFIG['pagamentos']['mp']?>" !== 0 ||
	    "<?php echo $CONFIG['pagamentos']['cielo']?>" !== 0 ||
	    "<?php echo $CONFIG['pagamentos']['pagseguro']?>" !== 0 ||
		"<?php echo $CONFIG['pagamentos']['pagarme']?>" !== 0 ) {

		$("#form-minha-compra").card({
			// width: "100%",
			formatting: true,
			container: "#card-wrapper",
			formSelectors: {
				numberInput: "input[data-checkout=cardNumber]",
				expiryInput: "input[data-checkout=cardExpiration]",
				nameInput: "input[data-checkout=cardholderName]",
				cvcInput: "input[data-checkout=securityCode]"
			},
			cardSelectors: {
				cardContainer: ".jp-card-container",
				card: ".jp-card",
				numberDisplay: ".jp-card-number",
				expiryDisplay: ".jp-card-expiry",
				cvcDisplay: ".jp-card-cvc",
				nameDisplay: ".jp-card-name"
			},
			placeholders: {
				number: "&bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull;",
				cvc: "&bull;&bull;&bull;",
				expiry: "&bull;&bull;/&bull;&bull;",
				name: "titular do cartão"
			}
		});
		// $("#card-wrapper").css("overflow-x", "auto");
	}
});

Mercadopago.setPublishableKey("<?php echo $CONFIG['pagamentos']['mp_public_key']?>");
Mercadopago.getIdentificationTypes();

PagSeguroDirectPayment.setSessionId("<?php echo $PagSeguroSessionId?>");

var ModalSite = $("#modal-site");
$("#new-checkout-reload").find("input,select").attr({"onpaste": "return false;"});

function pagseguro_repostas(str) {

	$("input[data-checkout='cardNumber']").parent().next().remove();
	$("input[data-checkout='cardNumber']").parent().removeClass("has-error");
	$("input[data-checkout='cardholderName']").parent().next().remove();
	$("input[data-checkout='cardholderName']").parent().removeClass("has-error");
	$("input[data-checkout='cardExpiration']").next("p").remove();
	$("input[data-checkout='cardExpiration']").parent("p").removeClass("has-error");
	$("input[data-checkout='securityCode']").parent().next().remove();
	$("input[data-checkout='securityCode']").parent().removeClass("has-error");
	$("select[data-checkout='installments']").parent().next().remove();
	$("select[data-checkout='installments']").parent().removeClass("has-error");

	// begin try javascript
	if(str === "10000") {
		$("input[data-checkout='cardNumber']").parent().addClass("has-error");
		$("input[data-checkout='cardNumber']").parent().after("<p>&#10008; O número do cartão não é válido!</p>");
		return("Digite o número do cartão.");
	}
	if(str === "10001") {
		$("input[data-checkout='cardNumber']").parent().addClass("has-error");
		$("input[data-checkout='cardNumber']").parent().after("<p>&#10008; O número do cartão não é válido!</p>");
		return("Número do cartão de crédito com comprimento inválido.");
	}
	if(str === "10002") {
		$("input[data-checkout='cardExpiration']").parent().addClass("has-error");
		$("input[data-checkout='cardExpiration']").parent().after("<p>&#10008; Data de validede inválida!</p>");
		return("Formato de data inválido.");
	}
	if(str === "30400") {
		$("input[data-checkout='cardExpiration']").parent().addClass("has-error");
		$("input[data-checkout='cardExpiration']").parent().after("<p>&#10008; Verifique a validade!</p>");
		return("Data do cartão inválida.");
	}
	if(str === "10003") {
		$("input[data-checkout='securityCode']").parent().addClass("has-error");
		$("input[data-checkout='securityCode']").parent().after("<p>&#10008; Campo de segurança inválido!</p>");
		return("Campo de segurança inválido.");
	}
	if(str === "10004") {
		$("input[data-checkout='securityCode']").parent().addClass("has-error");
		$("input[data-checkout='securityCode']").parent().after("<p>&#10008; CVV Obrigatório!</p>");
		return("CVV Obrigatório.");
	}
	if(str === "10006")	{
		$("input[data-checkout='securityCode']").parent().addClass("has-error");
		$("input[data-checkout='securityCode']").parent().after("<p>&#10008; Campo de segurança com comprimento inválido!</p>");
		return("Campo de segurança com comprimento inválido.");
	}

	if (str == "53020" || str == "53021") {
		return ("Verifique telefone inserido");
	} else if (str == "53010" || str == "53011" || str == "53012") {
		return ("Verifique o e-mail inserido");
	} else if (str == "53017") {
		return ("Verifique o CPF inserido");
	} else if (str == "53018" || str == "53019") {
		return ("Verifique o DDD inserido");
	} else if (str == "53013" || str == "53014" || str == "53015") {
		return ("Verifique o nome inserido");
	} else if (str == "53029" || str == "53030") {
		return ("Verifique o bairro inserido");
	} else if (str == "53022" || str == "53023") {
		return ("Verifique o CEP inserido");
	} else if (str == "53024" || str == "53025") {
		return ("Verifique a rua inserido");
	} else if (str == "53026" || str == "53027") {
		return ("Verifique o número inserido");
	} else if (str == "53033" || str == "53034") {
		return ("Verifique o estado inserido");
	} else if (str == "53031" || str == "53032") {
		return ("Verifique a cidade informada");
	} else if (str == "10001") {
		return ("Verifique o número do cartão inserido");
	} else if (str == "10002" || str == "30405") {
		return ("Verifique a data de validade do cartão inserido");
	} else if (str == "10004") {
		return ("É obrigatorio informar o código de segurança, que se encontra no verso, do cartão");
	} else if (str == "10006" || str == "10003" || str == "53037") {
		return ("Verifique o código de segurança do cartão informado");
	} else if (str == "30404") {
		return ("Ocorreu um erro. Atualize a página e tente novamente!");
	} else if (str == "53047") {
		return ("Verifique a data de nascimento do titular do cartão informada");
	} else if (str == "53053" || str == "53054") {
		return ("Verifique o CEP inserido");
	} else if (str == "53055" || str == "53056") {
		return ("Verifique a rua inserido");
	} else if (str == "53042" || str == "53043" || str == "53044") {
		return ("Verifique o nome inserido");
	} else if (str == "53057" || str == "53058") {
		return ("Verifique o número inserido");
	} else if (str == "53062" || str == "53063") {
		return ("Verifique a cidade informada");
	} else if (str == "53045" || str == "53046") {
		return ("Verifique o CPF inserido");
	} else if (str == "53060" || str == "53061") {
		return ("Verifique o bairro inserido");
	} else if (str == "53064" || str == "53065") {
		return ("Verifique o estado inserido");
	} else if (str == "53051" || str == "53052") {
		return ("Verifique telefone inserido");
	} else if (str == "53049" || str == "53050") {
		return ("Verifique o código de área informado");
	} else if (str == "53122") {
		return ("Enquanto na sandbox do PagSeguro, o e-mail deve ter o domínio @sandbox.pagseguro.com.br (ex.: comprador@sandbox.pagseguro.com.br)");
	}
};

function meradopago_respostas( s ) {

	$("input[data-checkout='cardNumber']").parent().next().remove();
	$("input[data-checkout='cardNumber']").parent().removeClass("has-error");
	$("input[data-checkout='cardholderName']").parent().next().remove();
	$("input[data-checkout='cardholderName']").parent().removeClass("has-error");
	$("input[data-checkout='cardExpiration']").parent().next().remove();
	$("input[data-checkout='cardExpiration']").parent().removeClass("has-error");
	$("input[data-checkout='securityCode']").parent().next().remove();
	$("input[data-checkout='securityCode']").parent().removeClass("has-error");
	$("input[data-checkout='docNumber']").parent().next().remove();
	$("input[data-checkout='docNumber']").parent().removeClass("has-error");
	$("select[data-checkout='installments']").parent().next().remove();
	$("select[data-checkout='installments']").parent().removeClass("has-error");

	switch( s ) {
		case "205":
			$("input[data-checkout='cardNumber']").parent().addClass("has-error");
			$("input[data-checkout='cardNumber']").parent().after("<p>&#10008; Digite o número do cartão!</p>");
			return "Digite o número do cartão.";

		case "209":
		case "208":
			$("input[data-checkout='cardExpiration']").parent().addClass("has-error");
			$("input[data-checkout='cardExpiration']").parent().after("<p>&#10008; Digite a válidade!</p>");
			return "Digite a válidade.";

		case "212":
		case "213":
		case "214":
			$("input[data-checkout='docNumber']").parent().addClass("has-error");
			$("input[data-checkout='docNumber']").parent().after("<p>&#10008; Insira o seu documento!</p>");
			return "Revise o número de CPF/CNJP.";

		case "220":
			$("input[data-checkout='cardIssuerId']").parent().addClass("has-error");
			$("input[data-checkout='cardIssuerId']").parent().after("<p>&#10008; Digite o seu banco emissor!</p>");
			return "Digite o seu banco emissor.";

		case "221":
			$("input[data-checkout='cardholderName']").parent().addClass("has-error");
			$("input[data-checkout='cardholderName']").parent().after("<p>&#10008; Insira o nome e sobrenome!</p>");
			return "Insira o nome e sobrenome.";

		case "224":
			$("input[data-checkout='securityCode']").parent().addClass("has-error");
			$("input[data-checkout='securityCode']").parent().after("<p>&#10008; Digite o código de segurança!</p>");
			return "Digite o código de segurança.";

		case "E301":
			$("input[data-checkout='cardNumber']").parent().addClass("has-error");
			$("input[data-checkout='cardNumber']").parent().after("<p>&#10008; Digite o número do cartão novamente!</p>");
			return "Há algo errado com o número do seu cartão. Volte a digitá-lo.";

		case "E302":
			$("input[data-checkout='securityCode']").parent().addClass("has-error");
			$("input[data-checkout='securityCode']").parent().after("<p>&#10008; Confira o código de segurança!</p>");
			return "Confira o código de segurança.";

		case "316":
			$("input[data-checkout='cardholderName']").parent().addClass("has-error");
			$("input[data-checkout='cardholderName']").parent().after("<p>&#10008; Confira o nome do titular do cartão!</p>");
			return "Por favor, digite um nome válido.";

		case "322":
		case "323":
		case "324":
			$("input[data-checkout='docNumber']").parent().addClass("has-error");
			$("input[data-checkout='docNumber']").parent().after("<p>&#10008; Confira o nome do titular do cartão!</p>");
			return "Confira o número de CPF/CNPJ.";

		case "325":
		case "326":
		case "E205":
			$("input[data-checkout='cardExpiration']").parent().addClass("has-error");
			$("input[data-checkout='cardExpiration']").parent().after("<p>&#10008; Digite a data de válidade novamente!</p>");
			return "Revise a data de válidade.";

		default: return "Por favor, revise todos os dados e tente novamente."; break;
	}
}

SPMaskBehavior = function (val) {
	return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
};
var spOptions = {
	onKeyPress: function(val, e, field, options) {
		field.mask(SPMaskBehavior.apply({}, arguments), options);
	}
};
CpfCnpjMaskBehavior = function (val) {
	return val.replace(/\D/g, '').length === 14 ? '00.000.000/0000-00' : '000.000.000-00999';
};
var CpfCnpjOptions = {
	onKeyPress: function(val, e, field, options) {
		field.mask(CpfCnpjMaskBehavior.apply({}, arguments), { reverse: false });
	}
};

function addEvent(el, eventName, handler) {
	if (el.addEventListener) {
		   el.addEventListener(eventName, handler);
	} else {
		el.attachEvent("on" + eventName, function(){
		  handler.call(el);
		});
	}
}

function getBin() {
	var cardSelector = document.querySelector("#cardId");
	if (cardSelector && cardSelector[cardSelector.options.selectedIndex].value !== "-1") {
		return cardSelector[cardSelector.options.selectedIndex].getAttribute("first_six_digits");
	}
	var ccNumber = document.querySelector('input[data-checkout="cardNumber"]');
	return ccNumber.value.replace(/[ .-]/g, "").slice(0, 6);
}

function clearOptions() {

	if($("input[data-pgto='pagamento']:checked").val() !== 'Mp Cartão') return;

	var bin = getBin();
	if (bin.length === 0) {
		document.querySelector("#issuer").style.display = "none";
		document.querySelector("#issuer").innerHTML = "";

		var selectorInstallments = document.querySelector("#installments"),
			fragment = document.createDocumentFragment(),
			option = new Option("Selecione...", "-1");

		selectorInstallments.options.length = 0;
		fragment.appendChild(option);
		selectorInstallments.appendChild(fragment);
		selectorInstallments.setAttribute("disabled", "disabled");
	}
}

function guessingPaymentMethod(event) {

	if($("input[data-pgto='pagamento']:checked").val() !== 'Mp Cartão') return;

	var bin = getBin(),
		amount = document.querySelector("#pagamentoAmount").value;
	if (event.type === "keyup") {
		if (bin.length >= 6) {
			Mercadopago.getPaymentMethod({
				"bin": bin
			}, setPaymentMethodInfo);
		}
	} else {
		setTimeout(function() {
			if (bin.length >= 6) {
				Mercadopago.getPaymentMethod({
					"bin": bin
				}, setPaymentMethodInfo);
			}
		}, 100);
	}
};

function setPaymentMethodInfo(status, response) {

	if($("input[data-pgto='pagamento']:checked").val() !== 'Mp Cartão') return;

	if (status === 200) {
		// do somethings ex: show logo of the payment method
		var form = document.querySelector("#form-minha-compra");

		if (document.querySelector("input[name=paymentMethodId]") === null) {
			var paymentMethod = document.createElement("input");
			paymentMethod.setAttribute("name", "paymentMethodId");
			paymentMethod.setAttribute("type", "hidden");
			paymentMethod.setAttribute("value", response[0].id);
			form.appendChild(paymentMethod);
		} else {
			document.querySelector("input[name=paymentMethodId]").value = response[0].id;
		}

		// check if the security code (ex: Tarshop) is required
		var cardConfiguration = response[0].settings,
			bin = getBin(),
			amount = document.querySelector("#pagamentoAmount").value;

		for (var index = 0; index < cardConfiguration.length; index++) {
			if (bin.match(cardConfiguration[index].bin.pattern) !== null && cardConfiguration[index].security_code.length === 0) {
				/*
				* In this case you do not need the Security code. You can hide the input.
				*/
			} else {
				/*
				* In this case you NEED the Security code. You MUST show the input.
				*/
			}
		}

		Mercadopago.getInstallments({
			"bin": bin,
			"amount": amount
		}, setInstallmentInfo);

		// check if the issuer is necessary to form-minha-compra
		var issuerMandatory = false,
			additionalInfo = response[0].additional_info_needed;

		for (var i = 0; i < additionalInfo.length; i++) {
			if (additionalInfo[i] === "issuer_id") {
				issuerMandatory = true;
			}
		}

		if (issuerMandatory) {
			Mercadopago.getIssuers(response[0].id, showCardIssuers);
			addEvent(document.querySelector("#issuer"), "change", setInstallmentsByIssuerId);
		} else {
			document.querySelector("#issuer").style.display = "none";
			document.querySelector("#issuer").options.length = 0;
		}
	}
};

function showCardIssuers(status, issuers) {

	if($("input[data-pgto='pagamento']:checked").val() !== 'Mp Cartão') return;

	var issuersSelector = document.querySelector("#issuer"),
		fragment = document.createDocumentFragment(),
		quantidade_parcela = $("[quantidade_parcela]").attr("quantidade_parcela");

	issuersSelector.options.length = 0;
	var option = new Option("Parcelado via mercado pago...", "-1");
	fragment.appendChild(option);

	// for (var i = 0; i < quantidade_parcela; i++) {
	for (var i = 0; i < issuers.length; i++) {
		if (issuers[i].name !== "default") {
			option = new Option(issuers[i].name, issuers[i].id);
		} else {
			option = new Option("Otro", issuers[i].id);
		}
		fragment.appendChild(option);
	}
	issuersSelector.appendChild(fragment);
	issuersSelector.removeAttribute("disabled");
	document.querySelector("#issuer").removeAttribute("style");
};

function setInstallmentsByIssuerId(status, response) {
	var issuerId = document.querySelector("#issuer").value,
		amount = document.querySelector("#pagamentoAmount").value;

	if (issuerId === "-1") {
		return;
	}

	Mercadopago.getInstallments({
		"bin": getBin(),
		"amount": amount,
		"issuer_id": issuerId
	}, setInstallmentInfo);
};

function setInstallmentInfo(status, response) {

	var installments = response[0].payer_costs;
	var options = ('<option value="">Parcelas via Mercado Pago</option>');
	for (var i = 0; i < installments.length; i++) {

		var optItem     = installments[i];
		var optQuantity = optItem.installments;
		var optAmount   = optItem.installment_amount;
		var totalAmount = optItem.total_amount;
		var optLabel    = (optQuantity + " x R$ " + (optAmount.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,').replace(".", ',')));

		options += ('<option value="' + optQuantity + '" data-parcela="' + optAmount +'" data-total="' + totalAmount + '">'+ optLabel +'</option>');
	};
	$("select[data-checkout=installments]").html(options);

	// var selectorInstallments = document.querySelector("#installments"),
	// 	fragment = document.createDocumentFragment();

	// selectorInstallments.options.length = 0;

	// if (response.length > 0) {
	// 	var option = new Option("Parcelado via mercado pago", "-1"),
	// 		payerCosts = response[0].payer_costs,
	// 		quantidade_parcela = $("[quantidade_parcela]").html();

	// 	fragment.appendChild(option);

	// 	for (var i = 0; i < payerCosts.length; i++) {
	// 		if ( i <= quantidade_parcela ) {
	// 			option = new Option(payerCosts[i].recommended_message || payerCosts[i].installments, payerCosts[i].installments);
	// 			fragment.appendChild(option);
	// 		}
	// 	}
	// 	selectorInstallments.appendChild(fragment);
	// 	selectorInstallments.removeAttribute("disabled");
	// }
};

function cardsHandler() {
	clearOptions();
	var cardSelector = document.querySelector("#cardId"),
		amount = document.querySelector("#pagamentoAmount").value;

	if (cardSelector && cardSelector[cardSelector.options.selectedIndex].value !== "-1") {
		var _bin = cardSelector[cardSelector.options.selectedIndex].getAttribute("first_six_digits");
		Mercadopago.getPaymentMethod({
			"bin": _bin
		}, setPaymentMethodInfo);
	}
}

// Obter a forma de parcelamento
$("#form-minha-compra").on("change", "input[data-checkout=cardBrand]", function(e){

	if($("input[data-pgto='pagamento']:checked").val() !== 'PagSeguro') return;

	var AmountValue = $("#pagamentoAmount").val(),
		CardBrand = $('[data-checkout="cardBrand"]').val();

	PagSeguroDirectPayment.getInstallments({
		amount: AmountValue,
		brand: CardBrand,
		success: function(response) {
			console.log(response.installments);
			var installments = response.installments[CardBrand];
			var options = ('<option value="">Parcelas via PagSeguro</option>');
			for (var i in installments) {

				var optItem     = installments[i];
				var optQuantity = optItem.quantity;
				var optAmount   = optItem.installmentAmount;
				var totalAmount = optItem.totalAmount;
				var optLabel    = (optQuantity + " x R$ " + (optAmount.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,').replace(".", ',')));

				options += ('<option value="' + optItem.quantity + '" data-parcela="' + optAmount +'" data-total="' + totalAmount + '">'+ optLabel +'</option>');
			};
			$("select[data-checkout=installments]").html(options);
		},
		error: function(response) {
			$("select[data-checkout=installments]").trigger("change");
			console.log(response);
		},
		complete: function(response) {
		}
	});
});

// Obter o onSenderHashReady
$("#form-minha-compra").on("change", "input[data-checkout=cardholderName]", function(e) {

	if($("input[data-pgto='pagamento']:checked").val() !== 'PagSeguro') return;

	PagSeguroDirectPayment.onSenderHashReady(function(response){
		if(response.status == 'error') {
			console.log(response.message);
			return false;
		}
		var hash = response.senderHash; //Hash estará disponível nesta variável.
		$("[data-checkout='cardToken']").val(hash);
	});
});

// Recuperar o valor da pracela
$("#form-minha-compra").on("change", "select[data-checkout=installments]", function(e) {

	if( e.currentTarget.value === undefined || e.currentTarget.value === "" ) return;

	InstallmentAmount = $(e.currentTarget).children("option:selected").data("parcela"),
	TotalAmount = $(e.currentTarget).children("option:selected").data("total");

	$("td#total_carrinho_frete").html("R$: " + (TotalAmount.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,').replace(".", ',')));

	if( ! $("input[name=InstallmentsAmount]").length ) {
		$("#form-minha-compra").append([
			$("<input/>", { value: InstallmentAmount, name: "InstallmentsAmount", type: "hidden",  autocomplete: "off" })
		]);
	} else {
		$("input[name=InstallmentsAmount]").val( InstallmentAmount );
	}

	if($("input[data-pgto='pagamento']:checked").val() !== 'PagSeguro') return;
	SendHash = PagSeguroDirectPayment.getSenderHash();

	if( ! $("input[name=HashPagSeguro]").length ) {
		$("#form-minha-compra").append([
			$("<input/>", { value: SendHash, name: "HashPagSeguro", type: "hidden",  autocomplete: "off" })
		]);
	}
	else {
		$("input[name=HashPagSeguro]").val( SendHash );
	}

});

// Obter a forma de parcelamento
$("#form-minha-compra").on("blur", "input[data-checkout=cardNumber]", function(e) {

	if($("input[data-pgto='pagamento']:checked").val() !== 'PagSeguro') return;

	var InputCardNumber = $(e.target),
		InputCardNumber = InputCardNumber.val(),
		InputCardNumber = InputCardNumber.replace(/\D/g, ''),
		InputCardNumber = InputCardNumber.trim();
		InputCardNumberCardBIn = InputCardNumber.substr(0, 6);

	if( InputCardNumberCardBIn <= 6 ) return;

	PagSeguroDirectPayment.getBrand({
		cardBin: InputCardNumber,
		success: function(response) {
			if (typeof response.brand.name === 'undefined') return;
			$('[data-checkout="cardBrand"]').val(response.brand.name).trigger("change");
		},
		error: function(response) {
			// ModalSite.modal("show").find(".modal-dialog").removeClass("modal-lg").addClass("modal-sm").find(".modal-body").find("p").html("Não foi possivel obter a bandeira de pagamento, recarregue a página e tente novamente!");
			console.log("Error getBrand: " + response );
			console.log(response.responseText());
		}
	});
}).trigger("change");

$("#form-minha-compra").on("focus", "input[data-checkout=cardExpiration]", function(e) {
	$(this).val("");
	$("select[id=cardExpirationMonth], select[id=cardExpirationYear]").html([$("<option/>", { value: 0, text: '...' })]);
});

$("#form-minha-compra").on("change", "input[data-checkout=cardExpiration]", function(e){
	var Data = $(e.target).val();
	if( ! Data ) return false;

	Explode = Data.split('/');
	Month = Explode[0].trim();
	Year = Explode[1].trim();

	// Seleciona os select
	setTimeout(function() {
		if( ! Data ) return;
		$("select[id=cardExpirationMonth]").html([ $("<option/>", { value: Month, text: Month }) ]);
		$("select[id=cardExpirationYear]").html([ $("<option/>", { value: "20" + Year, text: Year }) ]);
	}, 110);
});

doSubmit = false;
sdkResponseHandler = (function (status, response) {

	$("input[data-checkout='cardNumber']").next().remove();
	$("input[data-checkout='cardNumber']").parent().removeClass("has-error");
	$("input[data-checkout='docNumber']").next().remove();
	$("input[data-checkout='docNumber']").parent().removeClass("has-error");
	$("input[data-checkout='cardholderName']").next().remove();
	$("input[data-checkout='cardholderName']").parent().removeClass("has-error");
	$("input[data-checkout='cardExpiration']").next().remove();
	$("input[data-checkout='cardExpiration']").parent().removeClass("has-error");
	$("input[data-checkout='securityCode']").next().remove();
	$("input[data-checkout='securityCode']").parent().removeClass("has-error");
	$("select[data-checkout='installments']").next().remove();
	$("select[data-checkout='installments']").parent().removeClass("has-error");

	if (status !== 200 && status !== 201) {
		console.log( response );
		doSubmit = false;
		ModalSite.find(".modal-dialog").addClass("modal-sm").find(".modal-body").find("p").html(meradopago_respostas(response.cause[0].code));
	}
	else {
		var form = document.querySelector("#form-minha-compra"),
			card = document.createElement("input");
		card.setAttribute("name", "token");
		card.setAttribute("type", "hidden");
		card.setAttribute("style", "display:none");
		card.setAttribute("value", response.id);

		form.appendChild(card);
		doSubmit = true;
	}
});

// Obter a forma de parcelamento
$("#form-minha-compra").on("blur", "input[data-checkout=cardNumber]", function(e){

	if($("input[data-pgto='pagamento']:checked").val() !== 'PagSeguro') return;

	var InputCardNumber = $(e.target),
		InputCardNumber = InputCardNumber.val(),
		InputCardNumber = InputCardNumber.replace(/\D/g, ''),
		InputCardNumber = InputCardNumber.trim();
		InputCardNumberCardBIn = InputCardNumber.substr(0, 6);

	if( InputCardNumberCardBIn <= 6 ) return;

	PagSeguroDirectPayment.getBrand({
		cardBin: InputCardNumber,
		success: function(response) {
			if (typeof response.brand.name === 'undefined') return;
			$('[data-checkout="cardBrand"]').val(response.brand.name).trigger("change");
		},
		error: function(response) {
			// ModalSite.modal("show").find(".modal-dialog").removeClass("modal-lg").addClass("modal-sm").find(".modal-body").find("p").html("Não foi possivel obter a bandeira de pagamento, recarregue a página e tente novamente!");
			console.log("Error getBrand: " + response );
			console.log(response.responseText());
		}
	});
});

hackAnimation = (function(){
	$("html,body").animate({scrollTop: $("#new-cadastro-enderecos").offset().top - 90}, 550);
	$("#new-cadastro-enderecos").find(".new-caixa-checkout").css({ "border-color": "#ffe3e3","background-color": "#ffe3e3" }).delay(2500).queue(function(ee){
		$(this).removeAttr("style");
		ee();
	});
});

verificarToken = (function(e) {
	var DataPgto = $("input[data-pgto='pagamento']:checked"),
		DataToken = $('input[name="token"]');

	if(	DataPgto.val() === "Mp Cartão" && DataToken.length ) {
		Checkout.finalizaPgto();
		return false;
	}

	if(	DataPgto.val() === "Pagar Me" && DataToken.length ) {
		Checkout.finalizaPgto();
		return false;
	}

	if(	DataPgto.val() === "PagSeguro" && DataToken.length ) {
		Checkout.finalizaPgto();
		return false;
	}

	setTimeout(verificarToken, 100);
});

doPay = (function (elem) {
	// console.log("doPay", elem);
	// console.log($("input[data-pgto='pagamento']:checked").val());
	if( $("input[name=token]").length ) $("input[name=token]").remove();

	var ElementCheckedFreteCheck = $("input[name='frete']:checked"),
	ElemCardJadLog = $("input[name=pudoIdValue]").val(),
	ElemDataPgto = $("input[data-pgto='pagamento']:checked");

	if(ElementCheckedFreteCheck.attr("id") === undefined) {
		ModalSite.modal("show");
		ModalSite.find("[aria-label=Close]").show(0);
		ModalSite.find(".modal-dialog").addClass("modal-sm").find(".modal-body").find("p").html("Selecione uma forma de envio!");
		hackAnimation();
		return;
	}

	if(ElemDataPgto.val() === undefined) {
		ModalSite.modal("show");
		ModalSite.find("[aria-label=Close]").show(0);
		ModalSite.find(".modal-dialog").addClass("modal-sm").find(".modal-body").find("p").html("Selecione uma forma de pagamento!");
		return;
	}

	// Force
	if(	ElementCheckedFreteCheck.attr("id") === "JADLOG-ECONOMICO" && !ElemCardJadLog ) {
		ModalSite.modal("show").find(".modal-dialog").find(".modal-body").find("p").html('Selecione um ponto de coleta!');
		ModalSite.modal("show").find(".modal-header").find("button").attr({ "onclick": "$('input#JADLOG-ECONOMICO').trigger('click');$(this).delay(880).queue(function(e){ $(this).removeAttr('onclick'); e(); })"});
		return;
	}

	ModalSite.modal("show").find(".modal-dialog").removeClass("modal-lg").addClass("modal-sm").find(".modal-body").find("p").html("Processando dados...");

	var card = card || {};
	if( ElemDataPgto.val() === 'Pagar Me' ) {
		if( ! doSubmit ) {

			card.card_holder_name = $("input[data-checkout='cardholderName']").val();
			card.card_expiration_date = $("input[data-checkout=cardExpiration]").val();
			card.card_number = $("input[data-checkout='cardNumber']").val();
			card.card_cvv = $("input[data-checkout='securityCode']").val();

			var cardValidations = pagarme.validate({ card: card });

			$("input[data-checkout='cardNumber']").next().remove();
			$("input[data-checkout='cardNumber']").parent().removeClass("has-error");
			$("input[data-checkout='cardholderName']").next().remove();
			$("input[data-checkout='cardholderName']").parent().removeClass("has-error");
			$("input[data-checkout='cardExpiration']").next().remove();
			$("input[data-checkout='cardExpiration']").parent().removeClass("has-error");
			$("input[data-checkout='securityCode']").next().remove();
			$("input[data-checkout='securityCode']").parent().removeClass("has-error");
			$("select[data-checkout='installments']").next().remove();
			$("select[data-checkout='installments']").parent().removeClass("has-error");

			if ( ! cardValidations.card.card_number ) {
				ModalSite.find(".modal-dialog").addClass("modal-sm").find(".modal-body").find("p").html('Você deve preencher o número do cartão');
				// ButtonSubmit.attr({"disabled": false});

				$("input[data-checkout='cardNumber']").parent().addClass("has-error");
				$("input[data-checkout='cardNumber']").parent().after("<p>&#10008; O número do cartão não é válido!</p>");
			}
			else if ( ! cardValidations.card.card_expiration_date ) {
				ModalSite.find(".modal-dialog").addClass("modal-sm").find(".modal-body").find("p").html('Digite a data de válidade do cartão');
				// ButtonSubmit.attr({"disabled": false});

				$("input[data-checkout='cardExpiration']").parent().addClass("has-error");
				$("input[data-checkout='cardExpiration']").parent().after("<p>&#10008; Data inválida!</p>");
			}
			else if ( ! cardValidations.card.card_cvv ) {
				ModalSite.find(".modal-dialog").addClass("modal-sm").find(".modal-body").find("p").html('Digite código de segurança do cartão');
				// ButtonSubmit.attr({"disabled": false});

				$("input[data-checkout='securityCode']").parent().addClass("has-error");
				$("input[data-checkout='securityCode']").parent().after("<p>&#10008; Código inválido!</p>");
			}
			else if ( ! cardValidations.card.card_holder_name || $("input[data-checkout='cardholderName']").val() === "" ) {
				ModalSite.find(".modal-dialog").addClass("modal-sm").find(".modal-body").find("p").html('Digite o Títular do cartão');
				// ButtonSubmit.attr({"disabled": false});

				$("input[data-checkout='cardholderName']").parent().addClass("has-error");
				$("input[data-checkout='cardholderName']").parent().after("<p>&#10008; Digite o Títular do cartão!</p>");
			}
			else if ( $("select[data-checkout='installments']").children("option:selected").val() === "-1" ) {
				ModalSite.find(".modal-dialog").addClass("modal-sm").find(".modal-body").find("p").html('Selecione a forma de parcelamento');
				// ButtonSubmit.attr({"disabled": false});

				$("select[data-checkout='installments']").parent().addClass("has-error");
				$("select[data-checkout='installments']").parent().after("<p>&#10008; Selecione a forma de parcelamento!</p>");
			}
			else {
				pagarme.client.connect({ encryption_key: "<?php echo $CONFIG['pagamentos']['pagarme_api_token']?>"}).then(client => client.security.encrypt(card)).then(card_hash => {
					$("#form-minha-compra").append([ $( "<input/>", { name: "token", value: card_hash, type: "hidden" } ) ]);
					$("#form-minha-compra").append([ $( "<input/>", { name: "cardExpiration", value: card.card_expiration_date, type: "hidden" } ) ]);
					$("#form-minha-compra").append([ $( "<input/>", { name: "docNumber", value: $("input[data-checkout=docNumber]").val(), type: "hidden" } ) ]);
					verificarToken();
				});
			}
			return;
		}
	}

	if( ElemDataPgto.val() === 'PagSeguro' ) {

		var DataCard = ($("input[data-checkout='cardExpiration']").val()).replace(/\D/g, ""),
			DataCardMonth = DataCard.substring(0, 2),
			DataCardYear = "20" +  DataCard.substring(2, 4);

			console.log(DataCard, DataCardMonth, DataCardYear);

		PagSeguroDirectPayment.createCardToken({
			// Número do cartão de crédito
			cardNumber: ($("input[data-checkout=cardNumber]").val()).replace(/\D/g, ""),
			// Bandeira do cartão
			brand: $("input[data-checkout=cardBrand]").val(),
			// CVV do cartão
			cvv: $("input[data-checkout=securityCode]").val(),
			// Mês da expiração do cartão
			expirationMonth: DataCardMonth,
			// Ano da expiração do cartão, com 4 dígitos.
			expirationYear: DataCardYear,
			success: function(response) {
				// Retorna o cartão tokenizado.
				// console.log("A", response);
				$("#form-minha-compra").append([$("<input/>", { name: "token", value: response.card.token, type: "hidden" })]);
				if( doSubmit ) doSubmit = true;
				verificarToken();
			},
			error: function(response) {
				// Callback para chamadas que falharam.
				// console.log("B", response);
				doSubmit = false;
				if (response.error) {
					$.each(response.errors, function (i, value) {
						console.log(i, value);
						ModalSite.find(".modal-dialog").addClass("modal-sm").find(".modal-body").find("p").html(pagseguro_repostas(i));
					});
				}
			},
			complete: function(response) {
				// Callback para todas chamadas.
			}
		});
		return;
	}

	if( ElemDataPgto.val() === "Mp Cartão" ) {
		if( ! doSubmit ) {
			var $form = document.querySelector("#form-minha-compra");
			// The function "sdkResponseHandler" is defined below
			Mercadopago.createToken($form, sdkResponseHandler);
			verificarToken();
		}
		return;
	}

	if(	ElemDataPgto.val() !== "Mp Cartão" ||
		ElemDataPgto.val() !== "Pagar Me" ||
	    ElemDataPgto.val() !== "PagSeguro" ) {
	   	Checkout.finalizaPgto();
		return;
	}
});

// JADLOG PARA BUSCA DE PEDIDO EM UM FILIAL
$("#new-checkout-reload").on("click", "input#JADLOG-ECONOMICO", function(e){
	$.ajax({
		url: "/identificacao/PudosJadLog",
		type: 'post',
		dataType: 'html',
		data: { acao: 'PudosJadLog' },
		success: function( str ){
			var list = $("<div/>", { html: str});
			ModalSite.modal("show").find(".modal-dialog").addClass("modal-lg").find(".modal-body").find("p").html(list.find("#recarregar-html").html());
		}
	});
});

// Ziné
$("#new-checkout-reload").on("click", "input#Zeni", function(e){
	ModalSite.modal("show").find(".modal-dialog").addClass("modal-lg").find(".modal-body").find("p").html("Após o prazo de entrega você irá retirar seu produto no seguinte endereço: Av Carlos De Campos, 946/948 - Pari das 8:00 às 16:00");
});

$(document).on("click", "input[name=pudoId]", function(e){
	$.ajax({
		url: "/identificacao/AtualizarPudosJadLog",
		type: "post",
		dataType: "html",
		data: { acao: "AtualizarPudosJadLog", jadlog_pudoid: $(this).val() },
		success: function( str ) {
			var list = $("<div/>", { html: str });
			$("#AtualizarPudosJadLog").html([
				list.find("#AtualizarPudosJadLog").html(),
				$("<input/>",{ name: "pudoIdValue", type: "hidden", value: "1" })
			]);
			ModalSite.modal("hide").find(".modal-dialog").removeClass("modal-lg").find(".modal-body").find("p").html("");
		}
	});
});

$(document).on("click", "a.entregar_em_casa", function(e){
	$.ajax({
		url: "/identificacao/RemoverPudosJadLog",
		type: "post",
		dataType: "html",
		data: { acao: "RemoverPudosJadLog" },
		success: function( str ) {
			var list = $("<div/>", { html: str });
			$("#AtualizarPudosJadLog").html("");
			$('#frete_selecionado').val('false');
			$('input[name=frete]').prop('checked', false);
			Checkout.atualizar_carrinho("");
		}
	});
});

var CountTrack = 0;
Checkout = {
	finalcompra: function () {
		if( $("[data-estoque='zero']").length > 0 )
			$("button#finalizar-pedido").attr({"disabled":"disabled"}).addClass("disabled").fadeOut();
		else
			$("button#finalizar-pedido").removeAttr("disabled").removeClass("disabled").fadeIn();
	},
	/**
	 * Finaliza o pagamento
	 */
	finalizaPgto: function() {
		var ClearSaleVerify = true,
			FormData = $("#new-checkout-reload").find("input[name], select[name]").serializeArray();

		$.ajax({
			url: window.location.href,
			type: "POST",
			data: FormData,
			dataType: "json",
			beforeSend: function() {
				ModalSite.modal("show");
				ModalSite.find("[aria-label=Close]").hide(0);
				ModalSite.find(".modal-dialog").addClass("modal-sm").find(".modal-body").find("p").html("Finalizando pedido...");
			},
			success: function( str ) {

				var mensagem = str.erros === 1 ? str.mensagem : str.mensagem;
				if (str.erros) {
					ModalSite.find(".modal-dialog").addClass("modal-sm").find(".modal-body").find("p").html(mensagem);
					doSubmit = false;
				} else {
					ModalSite.find("[aria-label=Close]").show(0);
					ModalSite.find(".modal-dialog").addClass("modal-sm").find(".modal-body").find("p").html(mensagem);
				}
			},
			error: function( E1, E2, E3 ){
				console.log( E1.responseText+"\n"+E2+"\n"+E3 );
			}
		});
	},
	reload_frete: function(){
		$.ajax({
			// url: window.location.href,
			// data: { track: "reload_frete" },
			url: "/app/includes/ajax-correios-produto.php",
			data: { acao: "CalcularFreteCarrinho" },
			success: function( str ) {
				var list = $("<div/>", {html: str}),
					// reload = list.find("#reload_frete"),
					reload = list.find("#recarregar-frete"),
					reload_frete = reload.html();

				if( reload.length ) {
					if( reload_frete.length ) {
						$("#reload_frete").html( reload.html() );
						CountTrack = 0;
					}
					else {
						CountTrack++;
						if( CountTrack <= 5 )
							setTimeout(function(){ Checkout.reload_frete(); }, 5000);
					}
				}
			}
		});
	},
	convertToFloat: function (val) {
		if (val !== '') {
			return val.replace(/\D/g, "").replace(/(\d+)(\d{2})/, "$1.$2");
		}
	},
	reload_element: function( str, element ) {
		var list = $("<div/>", { html: str });
		$( "#new-checkout-reload" ).html( list.find( "#new-checkout-reload" ).html() );
		<?php echo (isset($PERSONALIZADO[0]['_Telefone_Whatsapp'][0]) ? sprintf('$("input[data-mask=telefone]").val("%s");', $PERSONALIZADO[0]['_Telefone_Whatsapp'][0]) : '');?>
	},
	atualizar_carrinho: function( THIS ) {
		var GRATIS = $(THIS).attr('data-gratis')||"",
			TIPOFRETE = $(THIS).attr('id')||"",
			VALORFRETE = $(THIS).attr('data-valor')||"",
			PRAZOSFRETE = $(THIS).parent().next().find('span').next().html()||"",
			PRAZOSFRETE = PRAZOSFRETE.trim();

		$("#AtualizarPudosJadLog").html("");

		$.ajax({
			url: window.location.href,
			type: "POST",
			dataType: "json",
			data: { acao: "AtualizarCarrinho", tipofrete: TIPOFRETE, valorfrete: VALORFRETE, prazosfrete: PRAZOSFRETE },
			complete: function() {
				/**
				 * Remove os dados dos inputs
				 * Não remove o cpf do cliente para o mercado pago
				 */
				$("#form-minha-compra").find("div").find("input[type=text]").each(function(a, b){
					if( $(b).attr("not_empty") !== "true" )
						$("#form-minha-compra").find("div").find(b).val("");
				});

				/**
				 * Desabilita os inputs radios
				 */
				$("#form-minha-compra").find("div").find("input[type=radio]").prop("checked", false);

				/**
				 * Desabilita os selects
				 */
				$("#form-minha-compra").find("div").find("select").prop("selected", false);

				$("#aminacao-site").fadeOut(0);
			},
			success: function( str ) {
				$.each(str, function( i, item) { $("[" +i+ "]").html( item ); });
				$("[data-boleto-transferencia]").attr({"data-boleto-transferencia": str.total_boleto});
				$("[quantidade_parcela]").attr({"quantidade_parcela": str.quantidade_parcela});
				$("[data-compra]").attr({"data-compra": str.total_carrinho_frete});
				$("[data-gratis]").html( GRATIS );
				$("[data-frete]").val( TIPOFRETE );
			},
			error: function(x,m,t){
				console.log(x.responseText+"\n"+m+"\n"+t);
				if(t === "timeout") {
					alert("Opss algo falhou tente novamente");
				}
			}
		});
	}
};

Checkout.finalcompra();
Checkout.reload_frete();
Checkout.atualizar_carrinho("");

/**
 * Seleciona outro endereco
 */
$("#new-checkout-reload").on("click", "[data-select=endereco]", function(e){
	e.preventDefault();
	var href = null,
		href = this.href||href,
		href = e.target.href||href,
		href = $(e.target).data("href")||href;

	$.ajax({
		url: href,
		success: function( str ) {
			Checkout.reload_element( str, "#new-checkout-reload" );
		},
		error: function( E1, E2, E3 ){
			console.log( E1.responseText+"\n"+E2+"\n"+E3 );
		},
		complete: function(){
			Checkout.atualizar_carrinho("");
			Checkout.reload_frete();
		}
	});
});

$("#new-checkout-reload").find("input[name=TipoPessoa]:checked").trigger("click");

$("input[name],select[name]")
	.focus(function(){
		$(this).parent().addClass("border-in");
	})
	.blur(function(){
		$(this).parent().removeClass("border-in");
});

$("#new-checkout-reload").find("input[data-mask=cep]").mask("00000-000", { onComplete: busca_cidade });

$("#new-checkout-reload").find("input[data-mask=telefone]").mask(SPMaskBehavior, spOptions);

$("#new-checkout-reload").find("input[data-mask=data_nascimento]").mask("00 / 00 / 0000");

$("#new-checkout-reload").find("input[data-mask=cpfcnpj]").mask('000.000.000-00');

/**
 * Seleciona as Formas de Pagamento para finalizar pedido
 */
$("#new-checkout-reload").on("click", "input[data-pgto='pagamento']", function() {
	var $this = $(this),
		ElementCheckedFreteCheck = $("input[name='frete']:checked");
	$this.parent().next().fadeIn();

	// HACK
	// Se, selecionado o pagamento antes do frete
	if( $this.is(":checked") && ElementCheckedFreteCheck.attr("id") === undefined) {
		ModalSite.modal("show");
		ModalSite.find("[aria-label=Close]").show(0);
		ModalSite.find(".modal-dialog").addClass("modal-sm").find(".modal-body").find("p").html("Selecione uma forma de envio!");
		hackAnimation();
		return;
	}

	// $("#new-checkout-reload").find("#form-minha-compra").find("[data-hidden=hidden]").fadeOut(0);
	$("#new-checkout-reload").find("#form-minha-compra").find("[data-hidden=hidden-solo]").css({"display":"block"});
	$("#new-checkout-reload").find("#total_carrinho_frete").html( $this.next().next().html() );
	$("#new-checkout-reload").find("#pagamentoAmount").val( Checkout.convertToFloat( $this.next().next().html() ) );

	// Verifica e seleciona, aonde não for esses pagamentos
	if( $this.val() !== "Pix" &&
		$this.val() !== "Boleto" &&
		$this.val() !== "Transferência" ) {

		// Mp Cartão
		if($this.val() === "Mp Cartão") {
			$("#card-doc-number").fadeIn();
		}
		else {
			$("#card-doc-number").fadeOut();
		}

		$("#card-wrapper, #card-form").fadeIn();
		$('[data-checkout="cardNumber"],[data-checkout="cardExpiration"],[data-checkout="securityCode"],[data-checkout="installments"],[data-checkout="cardholderName"]').val("").change();
		$("#new-checkout-reload").find("#form-minha-compra").find("[data-hidden=hidden]").fadeOut(0);
	}
	else {
		$("#card-wrapper, #card-form").fadeOut();
		$this.next().stop().fadeIn(0);
	}
});

/**
 * Cadastro de cliente
 */
$("#new-checkout-reload").on("click", "input[name=TipoPessoa]", function() {
	var This = $(this),
		DataTipoName = $("[data-tipo-name]"),
		DataTipoCpfCnpj = $("[data-tipo-cpfcnpj]");

	if( This.val() === "2" ) {
		DataTipoName.attr( { "data-tipo-name": "" } ).html( "Razão Social" );
		DataTipoCpfCnpj.attr( { "data-tipo-cpfcnpj": "" } ).html( "CNPJ" );
		$("input[data-mask=cpfcnpj]").mask('00.000.000/0000-00');
		// return false;
	}

	if( This.val() === "1" ) {
		DataTipoName.attr( { "data-tipo-name": "" } ).html( "Nome Completo" );
		DataTipoCpfCnpj.attr( { "data-tipo-cpfcnpj": "" } ).html( "CPF" );
		$("input[data-mask=cpfcnpj]").mask("000.000.000-00");
		// return false;
	}
});

/**
 * Editar o cadastro de clitente
 */
$("#new-checkout-reload").on("click", ".checkout-editar-dados", function(e) {
	e.preventDefault();
	// console.log( $( e.target ).parents().find("form") );
	$.ajax({
		url: e.target.href||this.href,
		success: function( str ) {
			Checkout.reload_element( str, "#new-checkout-reload" );
		},
		error: function( E1, E2, E3 ){
			console.log( E1.responseText+"\n"+E2+"\n"+E3 );
		}
	});
});

/**
 * Validar e cadastrar clitente
 */
$("#new-checkout-reload").on("click", "button[data-type=submit]", function(e) {
	/**
	 * Pega o formulario que está em AcaoEnderecos
	 */
	var FomrValidate = $(e.target).parent().parent();

	$( FomrValidate ).validate({
		debug: true,
		errorClass: "input-error-span-2 text-right",
		errorElement: "span",
		rules: {
			// "cadastro[email]": { required: true, maxlength: 85, email: true },
			// "cadastro[nome]": { required: true, minlength: 4, maxlength: 85 },
			// "cadastro[cpfcnpj]": { required: true, minlength: 14, maxlength: 21 },
			// "cadastro[telefone]": { required: true, minlength: 14, maxlength: 15 },
			// "cadastro[senha1]": { required: true, minlength: 6, maxlength: 12 },
			// "cadastro[senha2]": { required: true, minlength: 6, maxlength: 12, equalTo: '#senha' },

			// "endereco[cep]": { required: true },
			// "endereco[endereco]": { required: true },
			// "endereco[numero]": { required: true, number: true },
			// "endereco[bairro]": { required: true },
			// "endereco[cidade]": { required: true },
			// "endereco[uf]": { required: true },
			// "endereco[nome]": { required: true }
		},
		messages: {
			// "cadastro[email]": { required: "Campo obrigatório", maxlength: "Máximo de 85 caracterers permitido!", email: "Digite um e-mail válido" },
			// "cadastro[nome]": { required: "Campo obrigatório", minlength: "Nome muito curto", maxlength: "Nome muito longo" },
			// "cadastro[cpfcnpj]": { required: "Campo obrigatório", minlength: "Digite seu CPF ou CNPJ corretamente", maxlength: "Digite seu CPF ou CNPJ corretamente" },
			// "cadastro[telefone]": { required: "Campo obrigatório", minlength: "Digite seu telefone ou celular corretamente", maxlength: "Digite seu telefone ou celular corretamente" },
			// "cadastro[senha1]": { required: "Campo obrigatório", minlength: "Senha requer de 6 a 12 caracteres", maxlength: "Senha requer de 6 a 12 caracteres" },
			// "cadastro[senha2]": { required: "Campo obrigatório", minlength: "Senha requer de 6 a 12 caracteres", maxlength: "Senha requer de 6 a 12 caracteres", equalTo: "As senhas não conferem!" },

			// "endereco[cep]": { required: "Digite seu CEP!" },
			// "endereco[endereco]": { required: "Digite seu endereço!" },
			// "endereco[bairro]": { required: "Digite o bairro!" },
			// "endereco[numero]": { required: "Digite o número!", number: "Digite apenas números" },
			// "endereco[cidade]": { required: "Digite nome da sua cidade!" },
			// "endereco[uf]": { required: "Digite seu estado!" },
			// "endereco[nome]": { required: "Dê um nome para seu endereço!" }
		},
		highlight: function(element, errorClass, validClass) {
			$( element ).parent().parent().addClass("new-checkout-error").removeClass("new-checkout-ok");
		},
		unhighlight: function(element, errorClass, validClass) {
			$( element ).parent().parent().removeClass("new-checkout-error").addClass("new-checkout-ok");
		},
		submitHandler: function( form, validator ) {
			var FormData = $( validator.target ).find('input[name], select[name]').serialize(),
				FormAction = $( validator.target ).attr("action"),
				FormById = $( validator.target ).attr("id");
			$.ajax({
				url: FormAction,
				type: "post",
				data: FormData,
				cache: false,
				success: function( str ) {
					Checkout.reload_element( str, "#" + FormById );
				},
				complete: function(a, b) {
					var list = $("<div/>",{ html: a.responseText }),
						ErrorCadastro = list.find("#error");
//                            console.log("A1");
//                            console.log( ErrorCadastro.find("span").html() );
//                            console.log( ErrorCadastro.find("span").length );
					if( ErrorCadastro.find("span").length > 0 ) {
						$("#form-cadastro-cliente").html( list.find("#form-cadastro-cliente").html() );
						ModalSite.modal("show");
						ModalSite.find(".modal-dialog").addClass("modal-sm").find(".modal-body").find("p").html( ErrorCadastro.find("span").html() );
						return false;
					}
					Checkout.reload_frete();
				},
				error: function( E1, E2, E3 ){
					console.log( E1.responseText+"\n"+E2+"\n"+E3 );
				}
			});
		}
	});
});

/**
 * Validar dados do pedido a ser finalizado
 */
$("#new-checkout-reload").on("click", "button[data-type=pedido]", function(e){
	/**
	 * Pega o formulario que está em AcaoEnderecos
	 */
	var FomrValidate = $(e.target).parent().parent().parent().parent();

	$( FomrValidate ).validate({
		debug: true,
		errorClass: "input-error-span-2 text-right",
		errorElement: "span",
		highlight: function(element, errorClass, validClass) {
			$( element ).parent().parent().addClass("new-checkout-error").removeClass("new-checkout-ok");
		},
		unhighlight: function(element, errorClass, validClass) {
			$( element ).parent().parent().removeClass("new-checkout-error").addClass("new-checkout-ok");
		},
		submitHandler: doPay
	});
});
