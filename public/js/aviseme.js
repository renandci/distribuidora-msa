/*!
 * @author Renan Henrique <renan@dcisuporte.com.br>
 * @company Data Control Infomatica
 */

/**
 *
 * @param {type} $id
 * @returns {true|false}
 * Verificar se há estoque disponivel na pagina do Produto
 */
AviseMe = {
  acaoCadastro: function (form) {
    var $this = $(form),
      $text = $this.find("button[type=submit]").html(),
      $InputDados = $this.serialize();

    $this
      .find("button[type=submit]")
      .prop("disabled", true)
      .addClass("disabled text-center")
      .html([
        $("<i/>", {
          class: "fa fa-refresh fa-spin fa-fw margin-bottom",
        }),
      ]);

    $.ajax({
      url: window.location.href,
      type: "post",
      dataType: "json",
      data: $InputDados,
      success: function (str) {
        console.log(str.mensagem);

        if (str.error === false) {
          $("#modal-aviseme").find("div").html(str.mensagem);
        } else {
          $("#modal-aviseme")
            .find("button[type=submit]")
            .prop("disabled", false)
            .removeClass("disabled")
            .html($text);

          $("#modal-aviseme").find("button").after(str.mensagem);
        }
      },
      error: function (a, b, c) {
        console.log(a.responseText + "\n" + b + "\n" + c);
      },
    });
  },
  initScript: function (ProdutoId) {
    var SPMaskBehavior = function (val) {
      return val.replace(/\D/g, "").length === 11
        ? "(00) 00000-0000"
        : "(00) 0000-00009";
    };
    var spOptions = {
      onKeyPress: function (val, e, field, options) {
        field.mask(SPMaskBehavior.apply({}, arguments), options);
      },
    };

    $.ajax({
      url: window.location.href,
      dataType: "json",
      data: { acao: "InfoAviseMe", produto_id: ProdutoId },
      success: function (str) {
        $("#modal-aviseme").find("#dados-produto").html(str.html);
      },
      error: function (a, b, c) {
        console.log(a.responseText + "\n" + b + "\n" + c);
      },
    });

    $("#modal-aviseme")
      .find("input[name=telefone]")
      .mask(SPMaskBehavior, spOptions);

    $("#modal-aviseme").validate({
      debug: false,
      errorClass: "error-aviseme",
      validClass: "valid-aviseme",
      rules: {
        nome: { required: true, minlength: 5 },
        email: { required: true, email: true },
      },
      messages: {
        nome: {
          required: "Digite seu nome completo",
          minlength: "Digite seu nome",
        },
        email: {
          required: "Digite seu e-mail",
          email: "Digite um e-mail válido",
        },
      },
      submitHandler: AviseMe.acaoCadastro,
    });
  },
  /**
   * @params string LinkProduto Pega o link do produto na url
   * @params boolean Type Define os campos e o envio do formulario
   */
  tela: function (LinkProduto = null, Type = null) {
    var ModalSite = $("#modal-site"),
      Href = LinkProduto || window.location.href,
      Href =
        ["produto", $("input[name=produto_id]").val(), "p"]
          .join("/")
          .split("/") || Href.split("/"),
      ProdutoId = Href[Href.length - 2],
      TelaForm = $("<form/>", {
        id: "modal-aviseme",
        class: "modal-aviseme",
        method: "post",
        action: "",
        append: [
          $("<button/>", {
            type: "button",
            class: "close",
            attr: { "data-dismiss": "modal", "aria-label": "Close" },
            html: [
              $("<span/>", {
                html: ["&times;"],
                attr: { "aria-hidden": "true" },
              }),
            ],
          }),
          $("<div/>", {
            append: [
              $("<script>", {
                type: "text/javascript",
                append: ["AviseMe.initScript(" + ProdutoId + ");"],
              }),
              $("<div/>", {
                id: "dados-produto",
                class: "clearfix text-center",
                append: [
                  '<center><img src="/public/imgs/ajax-loader.gif" class="mt10 mb10 tag-block" width="43px"/></center>',
                ],
              }),
              $("<input/>", {
                type: "text",
                name: "nome",
                placeholder: "Seu nome:",
                autocomplete: "off",
              }),
              $("<input/>", {
                type: "email",
                name: "email",
                placeholder: "Seu e-mail:",
                autocomplete: "off",
              }),
              Type
                ? $("<input/>", {
                    type: "tel",
                    name: "telefone",
                    placeholder: "Seu telefone:",
                    autocomplete: "off",
                  })
                : null,
              Type
                ? $("<input/>", {
                    type: "text",
                    name: "cidade",
                    placeholder: "Qual sua cidade:",
                    autocomplete: "off",
                  })
                : null,
              Type
                ? $("<textarea/>", {
                    name: "mensagem",
                    placeholder: "Digite uma mensagem:",
                    autocomplete: "off",
                  })
                : null,
              $("<input/>", {
                type: "hidden",
                name: "produtos_id",
                value: ProdutoId,
              }),
              $("<input/>", {
                type: "hidden",
                name: "acao",
                value: Type ? "SolicitaOrcamento" : "AviseMeCadastro",
              }),
              $("<button/>", {
                type: "submit",
                text: "cadastrar",
                class: "btn btn-primary btn-lg btn-block",
              }),
            ],
          }),
        ],
      });

    console.log(ProdutoId);

    ModalSite.find(".modal-header").fadeOut(0);
    ModalSite.find(".modal-dialog").removeClass("modal-lg modal-sm");
    if ($("#remove-temp-body").length) {
      ModalSite.modal("show");
      ModalSite.find("#remove-temp-body").show(0).html(TelaForm.fadeIn());
      return false;
    } else {
      ModalSite.modal("show")
        .find(".modal-body")
        .show(0)
        .html(TelaForm.fadeIn());
      return false;
    }
  },
  button: function () {
    // var NewButton = $("<button/>", {
    // id: "btn-aviseme",
    // type: "button",
    // class: "btn btn-aviseme btn-lg",
    // onclick: "AviseMe.tela();",
    // append: [
    // $("<i>", {
    // class: "fa fa-paper-plane-o"
    // }),
    // $("<span>", {
    // text: " avise-me!"
    // })
    // ]
    // });
    // if(!$(document).find(".btn-aviseme").length)
    // return $(document).find(".btn-comprar").after(NewButton);
  },
  produto: function ($ID) {
    // $.ajax({
    // url: window.location.href,
    // data: { acao: "AviseMeInit", produto_id: $ID },
    // dataType: "json",
    // success: function(str) {
    // AviseMe.button();
    // $(document).find("#btn-aviseme").fadeOut(100);
    // $(document).find("#formulario-frete").fadeIn(100);
    // $(document).find("button.btn-comprar").removeClass("disabled").fadeIn(0).prop("disabled", false);
    // $(document).find("button#btn-add-lista").removeClass("disabled").fadeIn(0).prop("disabled", false);
    // if(str.estoque === false){
    // $(document).find("#btn-aviseme").fadeIn(100);
    // $(document).find("#formulario-frete").fadeOut(100);
    // $(document).find("button.btn-comprar").addClass("disabled").prop("disabled", true).fadeOut(100);
    // $(document).find("button#btn-add-lista").addClass("disabled").prop("disabled", true).fadeOut(100);
    // }
    // },
    // error: function(a,b,c){
    // for(var i in a) {
    // if(i!=="channel"){
    // console.log(a[i]+'\n'+b+'\n'+c);
    // }
    // }
    // }
    // });
  },
  init: function (ID) {
    // $.ajax({
    // url: window.location.href,
    // type: "post",
    // data: { acao: "verificaEstoque", id: ID },
    // dataType: "json",
    // success: function(str){
    // console.log(str);
    // if(str.estoque === "0" || str.estoque <= "0") {
    // $("#text-btn-comprar").find("b").html("Produto indisponível no momento!");
    // $(".btn-comprar").fadeOut(0);
    // $("#btn-add-lista").fadeOut(0);
    // $(".ocultar-estoque").fadeOut(0);
    // $("#formulario-frete").fadeOut(0);
    // $("#btn-avise-me").fadeIn(0).attr("data-id", ID);
    // } else {
    // $("#btn-avise-me").fadeOut(0).attr("data-id", ID);
    // $(".ocultar-estoque").fadeIn(0);
    // $("#text-btn-comprar").find("b").empty();
    // $(".btn-comprar").fadeIn(0);
    // $("#btn-add-lista").fadeIn(0);
    // $("#formulario-frete").fadeIn(0);
    // }
    // },
    // error: function (a,b,c) {
    // console.log( a.responseText+"\n"+b+"\n"+c );
    // }
    // });
  },
};
