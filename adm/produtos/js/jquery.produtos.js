const round = (n, dp) => {
  const h = +"1".padEnd(dp + 1, "0");
  return Math.round(n * h) / h;
};

$("input.preco-mask").mask("#.##0,00", { reverse: true });
$("input[name=preco_promo],input.preco-promo").mask("#.##0,00", {
  reverse: true,
});
$("input[name=preco_venda],input.preco-venda").mask("#.##0,00", {
  reverse: true,
});
$("input[name=preco_custo],input.preco-custo").mask("#.##0,00", {
  reverse: true,
});

$(document)
  .on("keyup", "input[name='preco_custo[]']", function (e) {
    var elem = $(this);
    var preco_lucro = 0;
    var preco_custo = e.currentTarget.value.replace(",", ".");
    var preco_promo = $(elem)
      .parent()
      .next()
      .next()
      .find("input[name='preco_promo[]']")
      .val()
      .replace(",", ".");

    var preco_lucro_val = $(elem)
      .parent()
      .next()
      .next()
      .next()
      .find("input[name='preco_lucro[]']");

    preco_lucro = (preco_promo * 100) / preco_custo - 100;
    preco_lucro = round(preco_lucro, 2);
    preco_lucro_val.val(preco_lucro !== Infinity ? preco_lucro : 0);
  })
  .trigger("change");

$(document)
  .on("keyup", "input[name='preco_promo[]']", function (e) {
    var elem = $(this);
    var preco_lucro = 0;
    var preco_custo = $(elem)
      .parent()
      .prev()
      .prev()
      .find("input[name='preco_custo[]']")
      .val()
      .replace(",", ".");
    var preco_promo = e.currentTarget.value.replace(",", ".");

    var preco_lucro_val = $(elem)
      .parent()
      .next()
      .find("input[name='preco_lucro[]']");

    preco_lucro = (preco_promo * 100) / preco_custo - 100;
    preco_lucro = round(preco_lucro, 2);
    preco_lucro_val.val(preco_lucro !== Infinity ? preco_lucro : 0);
  })
  .trigger("change");

// /**-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*
//  função de submit sem reload
// 	**-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**/
// submit_produto_semreload = function() {

// 	var $formData = $("#formulario-produtos").serialize(),
// 		$formAction = $("#formulario-produtos").attr("action");
// 		console.log($formData);

// 	$.ajax({
// 		url: $formAction,
// 		type: "post",
// 		data: $formData,
// 		success: function( str ) {
// 			var list = $("<div/>", { html: str });
// 			$("#aba1").html( list.find("#aba1").html() );
// 		},
// 		error: function(x,t,m){
// 			console.log(x.responseText+'\n'+t+'\n'+m);
// 		}
// 	});
// };

/**-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*
 SELECIONA/EXCLUIR TODOS DADOS SELLECIONADOS
**-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**/
$(document).on("click", "input[name=selecionados-exclusao-all]", function (e) {
  var $this = $(this).parents(),
    $all = $this.find("input[name=selecionados-exclusao]").serialize();

  if (!$all.length)
    $this.find("input[name=selecionados-exclusao]").prop({ checked: true });
  else $this.find("input[name=selecionados-exclusao]").prop({ checked: false });
});

$(document).on("click", "[data-id=btn-excluir-varios]", function (e) {
  var $this = $(this).parents(),
    $link = $this.find("[data-href]").attr("data-href"),
    $all = $this.find("input[name=selecionados-exclusao]").serializeArray(),
    $IsLoaded = JanelaModal.is(":visible") ? true : false;

  if (!$all.length) return confirm("Selecione ao menos um para excluir!");

  if (!confirm("Deseja realmente excluir!")) return false;

  $.ajax({
    url: $link,
    type: "post",
    data: { campos: $all },
    success: function (str) {
      var list = $("<div/>", { html: str });
      if (!$IsLoaded) {
        $("#div-edicao").html(list.find("#div-edicao").html());
      } else {
        JanelaModal.html(list.find("#div-edicao").html());
      }
    },
    error: function (a, b, c) {
      console.log(a.responseText + "\n" + b + "\n" + c);
    },
  });
});

/**
//  * Abas animadas
//  */
// $("#abas").on("click", "a", function(e){
// 	e.preventDefault();
// 	$("#abas").find("a").removeClass('abas-ativas');
// 	$(".cx-abas").find(".cx-internas-abas").removeClass('cx-internas-ativa');

// 	$(this).addClass("abas-ativas");
// 	$($(this).attr('href')).addClass('cx-internas-ativa');

// 	if( $(this).attr('href') === '#aba1' )
// 		$('#formulario-produtos').find('button[type=submit]').fadeIn(100).prop('disabled', false);
// 	else
// 		$('#formulario-produtos').find('button[type=submit]').fadeOut(330).prop('disabled', true);
// });

// /**
//  * Placa de Status 'Promoção'|'Lançamento'|'Exclusivo'
//  */
// $("#aba1").on("click", "[data-placas=placastatus]", function(i,e){
// 	var $dataPlacas = $("#placas_status_fretes"), arrayPlacas = [];
// 	$dataPlacas.find("[data-placas=placastatus]").each(function(i) {
// 		if( $(this).is(":checked") ) {
// 			arrayPlacas.push($(this).val());
// 		}
// 	});

// 	console.log(arrayPlacas);

// 	$.get(window.location.href, { acao: "placaStatus", placas: arrayPlacas }, function(str) {
// 		var list = $('<div/>', {html: str});
// 		$("#placas_status_fretes").html(list.find("#placas_status_fretes").html());
// 	}, "html")
// 	.fail(function(x,t,m){
// 		console.log(x.responseText+"\n"+t+"\n"+m);
// 	});
// });

// /**
//  * Fretes dos produtos
//  */
// $("#aba1").on("click", "[data-placas=placasfretes]", function(i,e){
// 	var $dataPlacas = $("#placas_status_fretes"), arrayPlacas = [];
// 	$dataPlacas.find("[data-placas=placasfretes]").each(function(i){
// 		if( $(this).is(":checked") ) {
// 			arrayPlacas.push($(this).val());
// 		}
// 	});
// 	$.get(window.location.href, { acao: "placaFretes", frete: arrayPlacas }, function(str) {
// 		var list = $('<div/>', {html: str});
// 		$("#placas_status_fretes").html(list.find("#placas_status_fretes").html());
// 	}, "html")
// 	.fail(function(x,t,m){
// 		console.log(x.responseText+"\n"+t+"\n"+m);
// 	});
// });

/**-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**
 Edição da tela de imagens
	**-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**/
$("#form-produtos").on("click", "a.btn-fotos", function (e) {
  e.preventDefault();
  $.ajax({
    url: this.href,
    success: function (str) {
      var list = $("<div/>", { html: str });
      JanelaModal.html(list.find("#div-edicao").html()).dialog({
        title: "Cadastrar/Editar - Fotos",
        autoOpen: true,
      });
    },
    error: function (x, t, m) {
      console.log(x.responseText + "\n" + t + "\n" + m);
    },
  });
});

// /**-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**
//  Apenas recarrega a aba
// **-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**/
// $(document).on("click", "a[href=#aba1]", function(e){
// 	$.ajax({
// 		url: window.location.href,
// 		cache: false,
// 		success: function( str ){
// 			var list = $("<div/>", { html: str });
// 			$("#aba1").html( list.find("#aba1").html() );
// 		},
// 		error: function(x,t,m){
// 			console.log(x.responseText+'\n'+t+'\n'+m);
// 		}
// 	});
// });

/**-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**
 Edição da tela do descricao
	**-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**/
$("#form-produtos").on("click", "#btn-descricao", function (e) {
  e.preventDefault();
  $.ajax({
    url: this.href,
    success: function (str) {
      var list = $("<div/>", { html: str });
      JanelaModal.html(list.find("#div-edicao").html()).dialog({
        title: "Cadastrar/Editar - Descrição",
        autoOpen: true,
      });
    },
    error: function (x, t, m) {
      console.log(x.responseText + "\n" + t + "\n" + m);
    },
  });
});

/**-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**
 Edição da tela do *
	**-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**/
$("#form-produtos").on("click", "#btn-marcas", function (e) {
  e.preventDefault();
  $.ajax({
    url: this.href,
    success: function (str) {
      var list = $("<div/>", { html: str });
      JanelaModal.html(list.find("#div-edicao").html()).dialog({
        title: "Cadastrar/Editar - Marcas",
        autoOpen: true,
      });
    },
    error: function (x, t, m) {
      console.log(x.responseText + "\n" + t + "\n" + m);
    },
  });
});

$("#form-produtos").on("click", ".btn-dados-frete", function (e) {
  e.preventDefault();
  $.ajax({
    url: this.href,
    success: function (str) {
      var list = $("<div/>", { html: str });
      JanelaModal.html(list.find("#div-edicao").html()).dialog({
        title: "Cadastrar/Editar - Dados de Frete",
        autoOpen: true,
      });
    },
    error: function (x, t, m) {
      console.log(x.responseText + "\n" + t + "\n" + m);
    },
  });
});

$("#form-produtos").on("click", ".btn-open", function (e) {
  e.preventDefault();
  var elem = $(e.currentTarget),
    title = elem.data("title");

  $.ajax({
    url: this.href,
    success: function (str) {
      var list = $("<div/>", { html: str });
      JanelaModal.html(list.find("#div-edicao").html()).dialog({
        title: title,
        autoOpen: true,
      });
    },
    error: function (x, t, m) {
      console.log(x.responseText + "\n" + t + "\n" + m);
    },
  });
});

$("#form-produtos").ajaxForm({
  success: function (str) {
    var list = $("<div/>", { html: str });
    $("#grid_variacao").html(list.find("#grid_variacao").html());
  },
});

// $("#form-produtos").on("submit", function(e) {
// 	e.preventDefault();

// 	var formData = new FormData(this);
// 	console.log( formData );

// });

// JanelaModal.on("click", "a.btn-adicionar-cor", function(e){
// 	var href = e.target.href||this.href,
// 		values = href.split("corid=")[1];

// 	if( $("#aba7").is(":visible") ) {
// 		$("#aba7").find("#id_cor").val(values).attr("selected", true).trigger("change");
// 		$("#aba7").find("button#btn-adicionar-cores").delay(200).trigger("click");
// 	}

// 	if( $("#aba5").is(":visible") ) {
// 		$("#aba5").find("#id_cor").val(values).attr("selected", true).trigger("change");
// 		$("#aba5").find("button#btn-adicionar-cores").delay(200).trigger("click");
// 	}

// 	$(this).parent().parent().next().remove();
// 	$(this).parent().parent().remove();

// 	e.preventDefault();
// });

// Cadastrar dados dentro do Modal
// JanelaModal.on("submit", "form", function(e){
// JanelaModal.on("submit", ".formulario-tamanhos, .formulario-cores, .formulario-marcas, .formulario-produtos-descricoes", function(e){

JanelaModal.on("submit", "[class^='formulario-']", function (e) {
  //Apenas trava o comando submit por modal
  if ($(this).hasClass("no-action") === false) {
    e.preventDefault();
    var DataSerialize = $(this).serialize(),
      DataAction = e.target.action || $(this).attr("action");

    $.when(
      $.ajax({
        url: DataAction,
        type: "post",
        data: DataSerialize,
      }),
      $.ajax({
        url: window.location.href,
      })
    ).done(function (strModal, strProduto) {
      var listModal = $("<div/>", { html: strModal }),
        listProduto = $("<div/>", { html: strProduto });

      JanelaModal.html(listModal.find("#div-edicao").html());

      $("#grid_variacao").html(listProduto.find("#grid_variacao").html());

      $("#id_marca").html(listProduto.find("#id_marca").html());
      $("#id_descricao").html(listProduto.find("#id_descricao").html());
    });
  }
});

JanelaModal.on("click", "a.btn-add-frete", function (e) {
  e.preventDefault();
  var href = e.target.href || this.href,
    query = this.href.slice(1),
    partes = query.split("&"),
    data = {};

  partes.forEach(function (parte) {
    var chaveValor = parte.split("=");
    var chave = chaveValor[0];
    var valor = chaveValor[1];
    data[chave] = valor;
  });

  $.when(
    $.ajax({
      url: href,
      type: "post",
      data: { acao: "AddFrete" },
    }),
    $.ajax({
      url: window.location.href,
    })
  ).done(function (strModal, strProduto) {
    var listModal = $("<div/>", { html: strModal }),
      listProduto = $("<div/>", { html: strProduto });

    JanelaModal.html(listModal.find("#div-edicao").html());

    $("#frete_prod_" + data["produto_id"]).html(
      listProduto.find("#frete_prod_" + data["produto_id"]).html()
    );
  });
});

/**-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**
 Paginar|menus|submenus dentro da tela modal
	**-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**/
JanelaModal.on("click", "a.btn-paginacao", function (e) {
  e.preventDefault();
  $.ajax({
    url: this.href,
    success: function (str) {
      var list = $("<div/>", { html: str });
      JanelaModal.html(list.find("#div-edicao").html());
    },
    error: function (x, t, m) {
      console.log(x.responseText + "\n" + t + "\n" + m);
    },
  });
});

/**-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**
 Exlcuir os dados dentro das telas Modal menus|submenus|cores|tamanhos|descricao|marcas
	**-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**/
JanelaModal.on("click", "a.btn-excluir-modal", function (e) {
  if (!confirm("Deseja realmente excluir!")) return false;
  e.preventDefault();
  $.ajax({
    url: this.href,
    cache: false,
    success: function (str) {
      var list = $("<div/>", { html: str });
      JanelaModal.html(list.find("#div-edicao").html());
    },
    error: function (x, t, m) {
      console.log(x.responseText + "\n" + t + "\n" + m);
    },
  });
});

// /**-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**
//  * Ação das cores e tamanhos
//  * Exlcuir os dados dentro das telas Modal menus|submenus|cores|tamanhos|descricao|marcas
//  **-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**/
// JanelaModal.on("click", ".btn-adicionar-tam", function(e){
// 	e.preventDefault();

// 	DataInputs = JanelaModal.find("input[name]:checked").serializeArray(),
// 	DataInputsVal = [];

// 	if( ! DataInputs.length ) return alert("Selecione ao menos um checkbox!");

// 	$.each(DataInputs, function(ii, ee) {
// 		var name = ee.name;
// 		DataInputsVal.push(name.match(/\d+/g)[0]);
// 	});

// 	// console.log( DataInputsVal );

// 	$.ajax({
// 		url: $(this).data("href"),
// 		type: "post",
// 		data: {
// 			acao: "AdicionarTamanhos",
// 			codigo_id: GLOBALS.codigo_id,
// 			tamanho_id: DataInputsVal,
// 		},
// 		success: function( str ){
// 			var list = $("<div/>", { html: str });
// 			if( $("#aba7").is(":visible") )
// 				return $("#aba7").html(list.find("#aba7").html());
// 		},
// 		// complete: function(){
// 			// if( $("#aba7").is(":visible") )
// 				// $("a[href=#aba7]").trigger("click");

// 			// if( $("#aba5").is(":visible") )
// 				// $("a[href=#aba5]").trigger("click");
// 		// },
// 		error: function(x,t,m){
// 			console.log(x.responseText+"\n"+t+"\n"+m);
// 		}
// 	});
// });

/**-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**
 NOME FIXO NO TOPO
	**-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**/
var $HeightTopo = $(".topo .navs-menus:last").height();
$(document).scroll(function () {
  if ($(this).scrollTop() >= $HeightTopo) {
    if ($("#produto-nome").hasClass("add-nome-movel")) return false;
    $("#produto-nome").addClass("add-nome-movel").fadeIn();
  } else {
    $("#produto-nome").removeClass("add-nome-movel").fadeOut();
  }
});
