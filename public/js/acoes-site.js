/*!
 * @author renan henrique <renan@dcisuporte.com.br>
 * @company Data Control Informatica - 16 3262-1365
 * @return Funcoes para o site
 */
//jQuery.noConflict();

/**
 * Animação do carrinho de Compra
 * @type type
 */
Carrinho = {
  setaAnimada: function () {
    //        var seta = $('#animacao-seta');
    //        seta
    //        .fadeIn(10)
    //        .delay(20)
    //        .animate({'margin-top':'0'},{direction:'top',times:3},10000)
    //        .delay(800)
    //        .fadeOut(100)
    //        .delay(100)
    //        .css({'margin-top':'205px'});
  },
  ocultarProdutoSelecionado: function (id) {
    $("#produto" + id).addClass("hidden");
  },
  recarregarCarrinho: function () {
    $.get(window.location.href, function (str) {
      var list = $("<div/>", { html: str });
      $("#carrinho").html(list.find("#carrinho").html());
      $("#carrinho-mobile").html(list.find("#carrinho-mobile").html());
    });
  },
  addCarrinho: function (id) {
    $.ajax({
      url: window.location.href,
      type: "post",
      data: { acao: "addCarrinho", id: id },
      dataType: "json",
      error: function (x, t, m) {
        console.log(x.responseText + "\\n" + t + "\\n" + m);
      },
      beforeSend: this.setaAnimada,
      success: function (str) {
        $.each(str, function (i, values) {
          $("#" + i).html(values);
        });
      },
      complete: this.ocultarProdutoSelecionado(id),
    });
    return false;
  },
  limparCarrinho: function () {
    $.ajax({
      url: window.location.href,
      type: "post",
      data: { acao: "limparCarrinho" },
      dataType: "json",
      error: function (x, t, m) {
        console.log(x.responseText + "\\n" + t + "\\n" + m);
      },
      success: function (str) {
        $.each(str.carrinho, function (i, values) {
          $("#" + i).html(values);
        });

        $.each(str.produto, function (i, values) {
          $("#produto" + values.id_produto).removeClass("hidden");
        });
      },
    });
  },
};

/**
 * Funções do Frete
 * @type type
 */
//Frete = {
//    init: function(){
//        setInterval(function(){
//            $('[data-frete=frete]').each(function(i, e) {
////                if($(this).html() === 'FRETE')
////                    $(this).html( $(this).attr('data-texto-2') );
////                else
////                    $(this).html( $(this).attr('data-texto-1') );
//            });
//        }, 1000);
//    }
//};

/**
 * Funções para tela de espiar o produto do sistema
 * @type type
 */
Funcoes = {
  espiar: function () {
    $(document).on("click", "[btn-espiar]", function (e) {
      var $this = $(this),
        $link = $this.attr("btn-espiar");
      $.ajax({
        url: $link,
        beforeSend: function () {
          $("#aminacao-site").fadeIn(0);
        },
        complete: function () {
          $("#aminacao-site").fadeOut(0);

          if ($("#div-produto").find("#btn-comprar-mobile").length > 0)
            $("#div-produto")
              .find("#btn-comprar-mobile")
              .removeClass("btn-comprar-mobile");
          if ($("#div-produto").find("#price-mobile").length > 0)
            $("#div-produto").find("#price-mobile").addClass("hidden");
        },
        success: function (a, b) {
          var list = $("<div/>", { html: a }),
            ModalSite = $("#modal-site")
              .modal("show")
              .find(".modal-dialog")
              .addClass("modal-lg");
          if (b === "success") {
            ModalSite.find(".modal-header").remove();
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
                    html: ["&times;"],
                    attr: {
                      "aria-hidden": "true",
                    },
                  }),
                ],
              }),
              list.find("#div-produto"),
            ]);

            Produto.init();
          }
        },
      });
      e.preventDefault();
    });
  },
};

(function ($) {
  $.ajaxSetup({ cache: false });

  $(document).ajaxStop(function () {
    // Reload init lazyload
    CarregarImagens();

    if ($(document).find("#carregar_thumblist").find(".elevateZoom").length) {
      var ElevateZoom = $("#elevate_zoom");

      // remove zoom instance from image
      $.removeData(ElevateZoom, "elevateZoom");

      // remove zoom container from DOM
      $(".zoomContainer").remove();
    }
  });

  /**
   * KeyUp with delay event setup
   *
   * @link http://stackoverflow.com/questions/1909441/jquery-keyup-delay#answer-12581187
   * @param function callback
   * @param int ms
   */
  $.fn.delayKeyup = function (callback, ms = 990) {
    $(this).keyup(function (event) {
      var srcEl = event.currentTarget;
      if (srcEl.delayTimer) clearTimeout(srcEl.delayTimer);
      srcEl.delayTimer = setTimeout(function () {
        callback($(srcEl));
      }, ms);
    });

    return $(this);
  };

  $.fn.extend({
    /**
     * url: window.location.href, name: 'preco', value: select_value
     */
    replace_url_params: function (parans) {
      var pattern = new RegExp("(\\?|\\&)(" + parans.name + "=).*?(&|$)"),
        newUrl = parans.url;

      if (newUrl.search(pattern) >= 0)
        newUrl = newUrl.replace(pattern, "$1$2" + parans.value + "$3");
      else
        newUrl =
          newUrl +
          (newUrl.indexOf("?") > 0 ? "&" : "?") +
          parans.name +
          "=" +
          parans.value;

      return newUrl;
    },
    caixa_cores: function (options) {
      var defaults = {};

      settings = $.extend({}, defaults, options);

      $(this)
        .parents("a")
        .attr({
          href: $(this)
            .parents("a")
            .attr("href")
            .replace(/(\d+)/g, settings.id),
        });

      $(this)
        .parents("a")
        .find("button[btn-comprar]")
        .attr({
          "btn-comprar": $(this)
            .parents("a")
            .attr("href")
            .replace(/(\d+)/g, settings.id),
        });

      $(this)
        .parents("a")
        .find("img")
        .attr({ src: settings.src, "data-original": settings["data-original"] })
        .lazyload();

      // $(this).parents('a').find('[data-hidden]').fadeOut(0);

      // $(this).parents('a').find('[data-hidden='+settings.id+']').fadeIn(0);

      return false;
      // return this.click( function (  ) {  } );
    },
  });

  busca_cidade = function (cep) {
    $.ajax({
      url: window.location.href,
      type: "post",
      data: { acao: "BuscarCidades", cep: cep },
      dataType: "json",
      beforeSend: function () {
        $("input#endereco").val("");
        $("input#bairro").val("");
        $("input#cidade").val("Buscando sua cidade...");
        $("input#uf").val("");
      },
      success: function (str) {
        $("input#endereco").val(str.endereco);
        $("input#bairro").val(str.bairro);
        $("input#cidade").val(str.cidade);
        $("input#uf").val(str.uf);
        $("select#uf").val(str.uf);
      },
      error: function (x, m, t) {
        console.log(x.responseText);
      },
    });
  };

  Funcoes.espiar();

  // var owlBanner = $(".banner-index");
  // owlBanner.owlCarousel({
  // 	autoPlay: 7000,
  // 	items: 1, 			// 7 items above 1000px browser width
  // 	itemsDesktop: [1090, 1], 	// 5 items between 1000px and 901px
  // 	itemsDesktopSmall: [880, 1], 	// 3 items betweem 880px and 601px
  // 	itemsTablet: [400, 1], 	// 2 items between 600 and 0;
  // 	itemsMobile: false, 		// itemsMobile disabled - inherit from itemsTablet option
  // 	navigation: false,
  // 	pagination: true,
  // 	lazyLoad: true
  // });

  //    $(document).on("mouseenter", "[vitrine-id]", function(e) {
  //
  //    }).on("mouseleave", "[btn-hovers]", function(e) {
  //
  //    });
  //
  //    $(document).on("mouseenter", "[btn-hovers]", function(){
  //        $(this).find("button[btn]").animate({ "opacity": 1, "filter": 1 }, 170).addClass("col-md-5");
  //    }).on("mouseleave", "[btn-hovers]", function() {
  //        $(this).find("button").animate({"opacity": 0, "filter": 0 }, 270);
  //    }).on("touchstart", "[btn-hovers]", function(){
  //        $(this).find("button[btn-espiar]").fadeOut(0);
  //        $(this).find("button").animate({ "opacity": 1, "filter": 1 }, 170).addClass("col-xs-12 mb5");
  //    }).on("touchend", "[btn-hovers]", function() {
  //        $(this).find("button").animate({ "opacity": 0, "filter": 0 }, 270);
  //    });

  CarregarImagens = function () {
    if (!$("img.lazy").is(".loaded"))
      return $("img.lazy")
        .lazyload({
          placeholder: settings.lazyplaceholder,
          skip_invisible: true,
          effect: "fadeIn",
        })
        .addClass("loaded");
  };
  CarregarImagens();

  /**
   * Btn de Compra Rapida
   */
  $(document).on("click", "[btn-comprar]", function (e) {
    var btn = $(this).attr("btn-comprar"),
      btn = btn.split("/"),
      produtoId = btn[btn.length - 2];
    Produto.adicionarProdutoCarrinho(produtoId);
    e.preventDefault();
  });

  $("input.input-pesquisar")
    .focus(function () {
      $(".desenho-campo-busca").addClass("input-hover");
      $(".retornar-pesquisa").fadeIn(0);
    })
    .blur(function () {
      $(".desenho-campo-busca").removeClass("input-hover");
      $(".retornar-pesquisa")
        .delay(550)
        .queue(function (e) {
          $(this).fadeOut(0);
          e();
        });
    })
    .delayKeyup(function (e) {
      var q = $(e).val() || null;
      if (q === null) {
        $(".retornar-pesquisa").fadeOut(0);
        return;
      }

      $.ajax({
        url: "/pesquisa-rapida",
        data: { pesquisar: q },
        dataType: "json",
        beforeSend: function () {
          $(".retornar-pesquisa")
            .css({ "border-width": "0px 1px 3px 1px", "min-height": "0" })
            .html("");
        },
        success: function (str) {
          $.each(str.results, function (a, b) {
            $(".retornar-pesquisa").append([
              $("<li/>", {
                tabindex: a,
                css: { cursor: "pointer" },
                class: "clearfix mb5 mt5",
                html: [
                  $("<span/>", {
                    class: "col-sm-3 col-xs-3",
                    html: $("<img/>", {
                      src: b.image,
                      class: "img-responsive",
                    }),
                  }),
                  $("<span/>", {
                    class: "col-sm-9 col-xs-9",
                    html: [
                      $("<span/>", { class: "show ft15px mb5", html: b.text }),
                      $("<s/>", { class: "show ft12px", html: b.preco_venda }),
                      $("<span/>", {
                        class: "show ft17px",
                        html: b.preco_promo,
                      }),
                    ],
                  }),
                ],
                click: function () {
                  window.location.href = b.uri;
                },
              }),
            ]);
          });
          $(".retornar-pesquisa").fadeIn();
        },
        error: function (a, b, c) {
          console.log(a.responseText + "\n" + b + "\n" + c);
        },
      });
    });

  /**
   * Novo modelo do meutopo
   */
  $(document).on("click", ".open-close", function (e) {
    e.preventDefault();
    // if deve montar outro menu a partir do mesmo click que há dentro do menu de login
    if ($(this).find("#produtos-site").length === 1) {
      // $(".menus-site").click();
    } else if ($("#meutopo").attr("visible") === "false") {
      $("#meutopo").animate({ marginLeft: "0" }, 300).attr({ visible: "true" });
      $("body").css({ "overflow-y": "hidden" });
    } else {
      $("#meutopo")
        .animate({ marginLeft: "-100%" }, 200)
        .attr({ visible: "false" });
      $("body").css({ "overflow-y": "auto" });
    }
    // Isso tende a adicionar uma tag nos menus
    // Por momento, comentado [19-08-2022 16:00]
    template_implements();
  });

  /**
   * Novo menu lateral do site
   */
  template_implements = function (ajax_bolean) {
    // tenta recuperar o indice do menu
    var test = $(document).find("#menus-lateral").find(".menus-lateral-title");
    // tenta ocultar o menus se possivel
    test.next().fadeOut(110);

    h = test.height();

    // ver se existe esse elemento, caso cria um para cada
    if (test.find("i").length == 0) {
      test.append([
        $("<i/>", {
          class: "fa fa-chevron-right pull-right",
          css: {
            zIndex: "99",
            width: "15px",
            height: "15px",
            lineHeight: "20px",
            marginTop: (h / 2 - 17 / 2) * 1,
          },
          click: function (e) {
            // test.next().fadeOut(110);
            test
              .find("i")
              .removeClass("fa-chevron-down")
              .addClass("fa-chevron-right");

            // vemos se esta ativo, caso sim; fechar o mesmo
            if ($(e.currentTarget).parent().next().is(":visible"))
              $(e.currentTarget).parent().next().fadeOut(115);
            else {
              $(e.currentTarget).toggleClass(
                "fa-chevron-right fa-chevron-down"
              );
              $(e.currentTarget).parent().next().fadeIn(115);
            }
            return false;
          },
        }),
      ]);
    }
    if (ajax_bolean) clearInterval(timerId);
  };

  // $(document).on("click", "font.menus-site, #produtos-site", function(e) {
  //     e.preventDefault();
  //     // var page = window.location.pathname.split("/")[1].trim();
  //     // // vamos verficar se existe, caso nao, vamos fazer uma copia do menu com ajax
  //     // if( $(document).find(".menus-lateral-pushindex").length === 0 && page === "produtos") {
  // 	// 	console.log("Produtos");
  // 	// 	$.ajax({
  //     //         url: window.location.href.split("?")[0],
  //     //         success: function(str){
  //     //             var list = $("<div/>", { html: str });
  // 	// 			$("#menus-lateral").html( list.find("#menus-lateral").html() );
  //     //             $(document).find("#menus-lateral").addClass("menus-lateral-mobile menus-lateral-pushindex").animate({marginLeft: "0"}, 330).attr({"visible":"true"});

  //     //         }
  //     //     });
  // 	// }
  //     // else
  //     if( $(document).find(".menus-lateral").length === 0 ) {
  //         console.log("Create Menu");
  //         $.ajax({
  //             url: "/produtos",
  //             success: function(str){
  //                 var list = $("<div/>", { html: str });
  //                 $("body").append([
  //                     $("<div/>", {
  //                         id: "menus-lateral",
  //                         class: "col-lg-3 col-md-3 hidden-sm hidden-xs menus-lateral",
  //                         "visible": "false",
  //                         html: list.find("#menus-lateral").html()
  //                     })
  //                 ]);
  //                 $(document).find("#menus-lateral").addClass("menus-lateral-mobile menus-lateral-pushindex").animate({marginLeft: "0"}, 330).attr({"visible":"true"});
  //                 template_implements();
  //                 // timerId = setInterval(template_implements, 100, ajax_bolean);
  //             }
  //         });
  //     }
  //     else {
  //         console.log("All");
  //         if($(document).find(".menus-lateral").attr("visible")==="false"){
  //             $(document).find(".menus-lateral").addClass("menus-lateral-mobile").animate({ marginLeft: "0"}, 330).attr({"visible":"true"});
  //             $("body").css({"overflow-y":"hidden"});
  //         }
  //         else {
  //             $(document).find(".menus-lateral").addClass("menus-lateral-mobile").animate({marginLeft: "-100%"}, 200).attr({"visible":"false"}).css({ "display" :"none" });
  // 			$("body").css({"overflow-y":"auto"});
  //         }
  //         template_implements();
  //     }
  // });

  /**
   * Efeito para o menu do sistema
   */
  $(document)
    .on("mouseenter", ".lista-menu-topo", function () {
      if ($(window).width() < 768) return false;

      $(this)
        .stop()
        .addClass("add-link-menu-topo")
        .children(".lista-submenus-topo")
        .fadeIn(110);
    })
    .on("mouseleave", ".lista-menu-topo", function () {
      if ($(window).width() < 768) return false;

      $(this)
        .stop()
        .delay(330)
        .queue(function (ex) {
          $(this).removeClass("add-link-menu-topo");
          $(this).children(".lista-submenus-topo").fadeOut(50);
          ex();
        });
    });

  var $HeightTopo =
      $("#topo-movel").height() + $("#bg-topo-costurado").height(),
    $IdentificacoHref = window.location.href,
    $IdentificacoHref = $IdentificacoHref.indexOf("identificacao");
  $(window).scroll(function () {
    if ($(window).width() > 768 && $IdentificacoHref < 0) {
      if ($(this).scrollTop() >= $HeightTopo) {
        if ($("#topo-movel").hasClass("add-topo-movel")) return false;
        $("#topo-movel")
          .stop()
          .addClass("add-topo-movel")
          .fadeIn(900)
          .find("#img-new-cart")
          .fadeOut(0);
        $("body").css({ "padding-top": $HeightTopo });
      } else {
        $("#topo-movel")
          .removeAttr("style")
          .removeClass("add-topo-movel")
          .find("#img-new-cart")
          .fadeIn();
        $("body").css({ "padding-top": 0 });
      }
    } else {
      $("#topo-movel")
        .removeAttr("style")
        .removeClass("add-topo-movel")
        .find("#img-new-cart")
        .fadeIn();
      $("body").css({ "padding-top": 0 });
    }
  });
})(jQuery);
