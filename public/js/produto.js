/**
 * FUNCOES DO PRODUTO
 */
var ModalSite = ModalSite || $("#modal-site");

Button = {
  comprar_false: function () {
    var prodduto_name = $("#div-produto").find("[data-nome-produto]").html();

    ModalSite.find(".modal-dialog").removeClass("modal-lg");
    ModalSite.find(".modal-header").fadeIn(0);
    ModalSite.find(".modal-body").fadeOut(0);

    if (ModalSite.find("#remove-temp-body").length)
      ModalSite.find("#remove-temp-body").remove();

    ModalSite.find(".modal-body").after([
      $("<div/>", {
        class: "modal-body",
        id: "remove-temp-body",
        html: [
          $("<div/>", {
            class: "text-center clearfix",
            html: [
              $("<p/>", { html: "O que deseja fazer agora!" }),
              $("<div/>", {
                class: "clearfix",
                html: [
                  $("<button/>", {
                    attr: { "data-cart": "0" },
                    class:
                      "btn-comprar-define btn btn-primary-default btn-lg btn-block mb15",
                    type: "button",
                    html: [
                      $("<i/>", { class: "fa fa-shopping-cart ft30px" }),
                      " ",
                      $("<span/>", {
                        class: "ft28px",
                        html: "adicionar no carrinho",
                      }),
                    ],
                  }),
                  $("<button/>", {
                    attr: { "data-cart": "1" },
                    class:
                      "btn-comprar-define btn btn-primary btn-lg btn-block",
                    type: "button",
                    html: [
                      $("<i/>", { class: "fa fa-credit-card ft30px" }),
                      " ",
                      $("<span/>", {
                        class: "ft28px",
                        html: "adicionar e finalizar compra",
                      }),
                    ],
                  }),
                ],
              }),
            ],
          }),
        ],
      }),
    ]);

    if (ModalSite.find(".modal-header").length === 0)
      ModalSite.find("#remove-temp-body").before([
        $("<div/>", {
          class: "modal-header",
          html: [
            $("<button/>", {
              attr: {
                type: "button",
                "data-dismiss": "modal",
                "aria-label": "Close",
                class: "close",
              },
              html: [
                $("<span/>", {
                  html: "x",
                  attr: { "aria-hidden": "true", style: "line-height: 25px;" },
                }),
              ],
            }),
            $("<h4/>", { class: "modal-title" }),
          ],
        }),
      ]);

    ModalSite.find("button.close").attr({
      onclick: '$("#remove-temp-body").remove();',
    });
    ModalSite.find(".modal-title").html(
      "Você deseja adicionar " + prodduto_name + " ao carrinho!"
    );
    ModalSite.modal("show");
  },
};

Produto = {
  init: function () {
    var DivProd = $("#div-produto"),
      ElevateZoom = DivProd.find("#elevate_zoom"),
      OwlRelacionados = $("#produtos-relacionado"),
      OwlCarousel = DivProd.find("#owl_carousel"),
      MediaQueries = window.matchMedia("(min-width: 768px)");

    $.removeData(ElevateZoom, "elevateZoom");
    $(".zoomContainer").remove();

    if (!MediaQueries.matches) {
      // do something for screens > 768px, for example turn on zoom
      $.removeData(ElevateZoom, "elevateZoom");
      $(".zoomContainer").remove();
      OwlCarousel.owlCarousel({
        autoPlay: false,
        items: 1,
        center: true,
        itemsTablet: [767, 1],
        itemsMobile: [767, 1],
        navigation: false,
        pagination: true,
        lazyLoad: true,
      });
    } else {
      ElevateZoom.elevateZoom({
        zoomWindowFadeIn: 990,
        zoomWindowFadeOut: 750,
        lensFadeIn: 990,
        lensFadeOut: 750,
        gallery: "carregar_thumblist",
        galleryActiveClass: "active",
        cursor: "crosshair",
        imageCrossfade: true,
        zoomType: "inner",
        responsive: true,
      });

      ElevateZoom.bind("click", function () {
        var ez = $(".elevate_zoom").data("elevateZoom");
        $.fancybox(ez.getGalleryList());
        return false;
      });
    }

    OwlRelacionados.owlCarousel({
      autoPlay: false,
      items: 4, // 7 items above 1000px browser width
      itemsDesktop: [1140, 4], // 5 items between 1000px and 901px
      itemsDesktopSmall: [880, 3], // 3 items betweem 880px and 601px
      itemsTablet: [400, 2], // 2 items between 600 and 0;
      itemsMobile: true, // itemsMobile disabled - inherit from itemsTablet option
      navigation: false,
      pagination: false,
      lazyLoad: true,
      dotsContainer: "#carousel-custom-dots",
    });

    $("#carousel-custom-dots").on("click", ".owl-prev", function () {
      OwlRelacionados.trigger("owl.prev");
    });

    $("#carousel-custom-dots").on("click", ".owl-next", function () {
      OwlRelacionados.trigger("owl.next");
    });

    //        $(document).find("#carregar_thumblist > a.active").delay(100).trigger("click");

    OwlRelacionados.next("#carousel-custom-dots")
      .find("li")
      .css({
        "margin-top": -(OwlRelacionados.height() / 2),
      });
  },
  //	mensagem_erros: function(mensagem){
  //		var cxErro = $(".cx-erro-carrinho"),
  //            Absoluta = $(".div-absoluta");
  //		Absoluta.fadeIn(0);
  //		cxErro
  //		.fadeIn(10)
  //		.find("span")
  //		.empty()
  //		.html(mensagem);
  //	},
  animacao_ajax: function (pAcao) {
    if (pAcao === true) {
      $(".mostra-carregamento").fadeIn(0);
      $(".produtos-variacoes, .tag-tamanhos, .tag-cores").fadeOut(0);
    } else {
      $(".mostra-carregamento").fadeOut(0);
      $(".produtos-variacoes, .tag-tamanhos,.tag-cores").fadeIn(0);
    }
  },
  animacao_site: function (pAcao) {
    var Tela = $(".aminacao-site");
    if (pAcao === true) Tela.fadeIn(0);
    else if ($("#espiar-tela").length === 0) Tela.fadeOut(10);
  },
  trocar_variacoes: function (var_href) {
    // var var_id_produto = var_href.split("/").pop(-1);
    // AviseMe.produto(var_id_produto);
    // console.log(var_href);

    // if( $("input[type=radio][name=tamanho]").is("checked") ) console.log("O... Meu amo, vai mandar nem um bejo!!!", $("input[name=tamanho]").val());

    $.ajax({
      url: var_href,
      error: function (x, m, t) {
        console.log(x.responseText + "\\n" + m + "\\n" + t);
      },
      success: function (str) {
        var list = $("<div/>", { html: str });
        $("#carregar-gallery").html(list.find("#carregar-gallery").html());
        $("#carregar_thumblist").html(list.find("#carregar_thumblist").html());
        $("#carregar-descricao-texto").html(
          list.find("#carregar-descricao-texto").html()
        );

        // $("#carregar-variacao-produto").html( list.find("#carregar-variacao-produto").html() );
        $("#cx-cores").html(list.find("#cx-cores").html());
        $("#cx-cores-text").html(list.find("#cx-cores-text").html());

        $("#trocar-tamanhos-text").html(
          list.find("#trocar-tamanhos-text").html()
        );
        $("#trocar-tamanhos").html(list.find("#trocar-tamanhos").html());

        $("#recarregar-produtos-relacionado").html(
          list.find("#recarregar-produtos-relacionado").html()
        );

        // $("#formulario-frete").html( list.find("#formulario-frete").html() );

        $("#div-produto").attr({
          datavalue: list.find("#div-produto").attr("datavalue"),
        });
        $("input[name=produto_id]").val(
          list.find("input[name=produto_id]").val()
        );

        $("#btn-comprar-mobile").html(list.find("#btn-comprar-mobile").html());
        // $("button[data-estoque]").attr({ "data-estoque": list.find("button[data-estoque]").attr("data-estoque") });

        Produto.init();
      },
    });

    // var produto = window.location.pathname,
    //     base = produto.split("/");

    // // console.log("base: ", base, "pathname: ", produto);

    // if( base[1] === "produto" || base[3] === "p" ) {
    //     window.history.pushState({}, "", var_href);
    // }
    return;
  },
  acao_atacado: function (id = null, value = null, url = null) {
    $.ajax({
      url: url,
      type: "POST",
      data: { acao: "AddToAtacado", id_produto: id, id_value: value },
      beforeSend: function () {},
      complete: function () {},
      success: function (str) {
        var list = $("<div/>", { html: str });
        $("#" + id).val(list.find("#" + id).val());
      },
    });
  },
  acao_comprar: function (id_produto = null, is_cart = null) {
    var PersonalizadoRequired = $("[class=personalizado-required]"),
      PersonalizadoRequiredInput = $("input[class=personalizado-required]"),
      PersonalizadoRequiredSelect = $("select[class=personalizado-required]");

    if (PersonalizadoRequired.length) {
      console.log("Existe campos");
      if (PersonalizadoRequiredInput.val() === "") {
        alert("Campo(s) obrigátorio!");
        PersonalizadoRequiredInput.css({ "border-color": "red" });
        return false;
      }
      if (PersonalizadoRequiredSelect.val() === "") {
        alert("Campo(s) obrigátorio!");
        PersonalizadoRequiredSelect.css({ "border-color": "red" });
        return false;
      }
    }

    if (is_cart) {
      if ($("#div-produto").find("input[name=cart_direct]").length)
        $("#div-produto").find("input[name=cart_direct]").remove();

      $("#div-produto").append([
        $("<input/>", { name: "cart_direct", value: is_cart, type: "hidden" }),
      ]);
    }

    var DataSerialize = $("#div-produto")
      .find("input[name], select[name]")
      .serialize();

    ModalSite.modal("hide")
      .find(".modal-dialog")
      .removeClass("modal-lg modal-sm");

    if ($("#div-produto").find("input[name=tamanho]").length > 1) {
      if ($("input[name=tamanho]:checked").val() === undefined) {
        ModalSite.modal("show");
        ModalSite.find(".modal-header h4").html("Atenção!");
        ModalSite.find(".modal-body").html([
          $("<small/>", {
            html: "Selecione um tamanho!",
            class: "text-center mb10 text-danger show",
          }),
          $("<div/>", {
            html: $("<ul/>", {
              id: "trocar-tamanhos",
              class: "clearfix produtos-variacoes",
              html: $("#trocar-tamanhos").clone().html(),
            }),
            class: "text-center tag-tamanhos tag-tamanhos-lg",
          }),
        ]);

        ModalSite.on("click", "input[type=radio] + label", function (e) {
          var id = $(e.target).prev("input").val(),
            estoque = $(e.target).prev("input").data("estoque");

          if (estoque === false) {
            $("input[name='produto_id']").val(id);
            ModalSite.modal("hide");
            $("#aminacao-site").fadeOut(0);
            $("button.btn-comprar").fadeOut(0);
            $("button#btn-aviseme").fadeIn(0).trigger("click");
            return;
          } else {
            return Produto.adicionarProdutoCarrinho(id);
          }
        });
        return false;
      }
    }

    ModalSite.modal("hide");
    $("#aminacao-site").fadeIn(0);

    $.when(
      // Adiciona o produto no carrinho
      $.ajax({
        url: window.location.href,
        type: "post",
        data: DataSerialize,
        dataType: "json",
      }),
      // Recarrega o carrinho de compras
      $.ajax({
        url: window.location.href,
        type: "post",
        dataType: "html",
      })
    )
      .then(function (a, b) {
        // console.log(a, b);
        ModalSite.modal("show");
        ModalSite.find(".modal-header").fadeOut(0);
        ModalSite.find(".modal-body").html([
          $("<button/>", {
            type: "button",
            class: "close",
            attr: {
              "data-dismiss": "modal",
              "aria-label": "Close",
            },
            html: [
              $("<span/>", {
                html: "x",
                attr: {
                  "aria-hidden": "true",
                },
              }),
            ],
          }),
          a[0].mensage,
        ]);

        var list = $("<div/>", { html: b });
        $("#carrinho").html(list.find("#carrinho").html());
        $("#carrinho-mobile").html(list.find("#carrinho-mobile").html());
        $("#itens-carrinho").html(list.find("#itens-carrinho").html());
      })
      .fail(function (a, b) {
        // console.log("a", a, "b", b);
      });
  },
  adicionarProdutoCarrinho: function (var_id_produto) {
    $.when(
      // Adiciona o produto no carrinho
      $.ajax({
        url: window.location.href,
        type: "post",
        dataType: "json",
        data: {
          acao: "InserirCarrinho",
          produto_id: var_id_produto,
          id: var_id_produto,
        },
      }),
      // Recarrega o carrinho de compras
      $.ajax({
        url: window.location.href,
        type: "post",
        dataType: "html",
      })
    )
      .done(function (a, b) {
        // console.log("Ai SUKU", a[0].estoque);

        var ModalSite = $("#modal-site");
        ModalSite.modal("show")
          .find(".modal-dialog")
          .removeClass("modal-lg modal-sm");
        ModalSite.find(".modal-header").hide(0);
        if (a[0].estoque === 0) {
          ModalSite.find(".modal-dialog")
            .addClass("modal-sm")
            .html([
              $("<button/>", {
                type: "button",
                class: "close",
                attr: { "data-dismiss": "modal", "aria-label": "Close" },
                html: [
                  $("<span/>", {
                    html: "&times;",
                    attr: { "aria-hidden": "true" },
                  }),
                ],
              }),
              $("<span/>", {
                class: "text-center tag-block",
                html: "Produto indisponível",
                id: "remover",
              }),
            ]);
          return false;
        }

        ModalSite.modal("show");
        ModalSite.find(".modal-header").fadeOut(0);
        ModalSite.find(".modal-body").html([
          $("<button/>", {
            type: "button",
            class: "close",
            attr: { "data-dismiss": "modal", "aria-label": "Close" },
            html: [
              $("<span/>", { html: "x", attr: { "aria-hidden": "true" } }),
            ],
          }),
          a[0].mensage,
        ]);

        var list = $("<div/>", { html: b });
        $("#carrinho").html(list.find("#carrinho").html());
        $("#carrinho-mobile").html(list.find("#carrinho-mobile").html());
        $("#itens-carrinho").html(list.find("#itens-carrinho").html());
      })
      .fail(function (a, b) {
        // console.log("a", a, "b", b);
      });
  },
};

(function () {
  Produto.init();

  // TROCA AS TAMANHOS SELECIONADAS NO PRODUTO
  // $("#recarregar-html").on("click", "#trocar-tamanhos label", function() {
  $(document).on("click", ".produtos-variacoes [href]", function (e) {
    var a = $(this) || $(e.target),
      href = a.attr("href") || this.href;

    Produto.trocar_variacoes(href);
    e.preventDefault();
  });

  // Button tamanhos
  $(document).on("click", "input[name='tamanho']", function (e) {
    var a = $(this),
      href = a.parent().data("href"),
      value = this.value;

    $("input[name='produto_id']").val(value);
    a.parents()
      .find("#trocar-tamanhos-text")
      .html(a.next("label").data("text"));

    $.get(href, function (str) {
      var list = $("<div/>", { html: str }),
        list_estoque = list.find("button[data-estoque]").attr("data-estoque");

      $("#carregar-descricao-texto").html(
        list.find("#carregar-descricao-texto").html()
      );
      $("button[data-estoque]").attr({
        "data-estoque": list.find("button[data-estoque]").attr("data-estoque"),
      });

      if (list_estoque === "false") {
        ModalSite.modal("hide");
        $("button#btn-aviseme").fadeIn(0);
        $("button.btn-comprar").fadeOut(0);
        return;
      }

      $("button.btn-comprar").fadeIn(0);
      $("button#btn-aviseme").fadeOut(0);
    });
  });

  // BOTÃO COMPRAR
  // $("#recarregar-html").on("click", ".btn-comprar-define", function() {
  $(document).on("click", ".btn-comprar-define", function (e) {
    var elem = $(this) || $(e.target),
      id_produto = $("#div-produto").attr("datavalue"),
      is_cart = elem.data("cart");

    return Produto.acao_comprar(id_produto, is_cart);
  });

  // Mask do campo frete
  $("[name='frete']").mask("99999-999");

  $("[name='frete']").focusin(function () {
    $(this).attr("placeholder", "");
  });

  $("[name='frete']").focusout(function () {
    $(this).attr("placeholder", "DIGITE SEU CEP");
  });

  // CALCULAR FRETE DO PRODUTO
  $("#conteudo-html,#modal-site").on(
    "submit",
    "#formulario-frete",
    function (e) {
      e.preventDefault();
      var produto_cep = $("input[name=frete]").val(),
        produto_id = $("input[name=produto_id]").val();
      $.ajax({
        // url: window.location.href,
        url: "/app/includes/ajax-correios-produto.php",
        data: {
          acao: "CalcularFrete",
          produto_cep: produto_cep,
          produto_id: produto_id,
        },
        // dataType: "json",
        error: function (x, m, t) {
          // console.log( x.responseText+"\n"+m+"\n"+t );
        },
        beforeSend: function () {
          $(e.target).find("#calcular-frete").fadeIn(0);
          $(e.target).find("#button-frete").fadeOut(0);
          $(e.target).find("#info-frete").empty();
        },
        success: function (str) {
          var list = $("<div/>", { html: str });
          // console.log(list.find("#recarregar-frete").html());
          $(e.target)
            .find("#info-frete")
            .html(list.find("#recarregar-frete").html());

          // if( str.msgerro ) {
          //     $(e.target).find("#info-frete").html(str['msgerro']);
          // } else {
          //     $(e.target).find("#info-frete").html(str['corpo-frete']);
          // }
        },
        complete: function () {
          $(e.target).find("#calcular-frete").fadeOut(0);
          $(e.target).find("#button-frete").fadeIn(0);
        },
      });
    }
  );

  $("#produtos-relacionado").on(
    "click",
    ".btn-add-carrinho-ralacinados",
    function () {
      var id = $(this).data("id");
      return Produto.acao_comprar(id);
    }
  );
})(jQuery);
