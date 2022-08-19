
// dialog de cancelamento

strstr = function (haystack, needle, before_needle) {
    if(haystack.indexOf(needle) >= 0) 
        return before_needle ? haystack.substr(0, haystack.indexOf(needle)) 
               : haystack.substr(haystack.indexOf(needle));
    return false;
}


emitir_nfe_xml = $("#emitir_nfe_xml"),
save_nfe_xml = $("<h3/>", {
    class: "text-center",
    html: [
        "Salvando as alterações, aguarde",
        $("<i/>", { class: "fa fa-spinner fa-pulse fa-fw"})        
    ]
}),
load_nfe_xml = $("<h3/>", {
    class: "text-center",
    html: [
        "Carregando informações, aguarde",
        $("<i/>", { class: "fa fa-spinner fa-pulse fa-fw"})        
    ]
}),
// Modal para o campo fiscal do produto
campos_ficais = $("<form/>", {
    id: "campos_ficais"
}).dialog({
    title: "Campos Fiscais",
    width: 800,
    height: 532,
    modal: true,
    autoOpen: false
}),
cancelar_nfe_xml = $("<form/>", {
    id: "cancelar_nfe_xml",
    action: "/adm/nfe/nfe-cancelar.php",
    method: "post",
    html: [
        $("<div/>", {
            class: "row",
            html: [
                $("<div/>", { 
                    class: "col-md-12", 
                    html: [
                        $("<div/>", {
                            class: "alert alert-danger ft18px bold text-center", 
                            html: [
                                $("<i/>", { class: "fa fa-exclamation-triangle mr5"}),
                                "As notas podem ser canceladas no prazo máximo de até 24h após a data de emissão."
                            ] 
                        }),
                    ] 
                }),
                $("<div/>", { 
                    class: "col-md-12", 
                    html: [
                        $("<div/>", { 
                            id: "data-validade",
                            class: "alert alert-info hidden ft12px text-center", 
                            html: "--"
                        }),
                    ] 
                }),
                $("<div/>" , {
                    class: "col-md-6",
                    html: [
                        $("<fieldset/>" , {
                            html: [
                                $("<legend/>", {
                                    class: "bold",
                                    html: "Dados do Emitente" 
                                }),
                                $("<div/>",{
                                    class: "clearfix",
                                    html: [
                                        $("<label/>", {
                                            html: "Emitente *" 
                                        }),
                                        $("<select/>", {
                                            css: { "width": "100%" },
                                            name: "id_emitente",
                                            html: [
                                                emitir_nfe_xml.find("select[name=id_emitente]").html()
                                            ]
                                        })
                                    ]
                                })
                            ]
                        })
                    ]
                }),
                $("<div/>" , {
                    class: "col-md-6",
                    html: [
                        $("<fieldset/>" , {
                            html: [
                                $("<legend/>", {
                                    class: "bold",
                                    html: "Dados do Emitente" 
                                }),
                                $("<div/>",{
                                    class: "clearfix",
                                    html: [
                                        $("<label/>", {
                                            html: "NF-e *" 
                                        }),
                                        $("<select/>", {
                                            css: { "width": "100%" },
                                            name: "id_nota",
                                            html: [
                                                emitir_nfe_xml.find("select[name=id_nota]").html()
                                            ],
                                            change: function(e){ return init_date_validade(e); },
                                        })
                                    ]
                                })
                            ]
                        })
                    ]
                }),
                $("<div/>" , {
                    class: "col-md-12",
                    html: [
                        $("<fieldset/>" , {
                            html: [
                                $("<legend/>", {
                                    class: "bold",
                                    html: "Digite o motivo do cancelamento" 
                                }),
                                $("<div/>",{
                                    class: "clearfix",
                                    html: [
                                        $("<label/>", {
                                            html: "Descrição *" 
                                        }),
                                        $("<input/>",{
                                            type: "text",
                                            name: "xMotivo",
                                            class: "w100 mb15",
                                            maxlenght: 255
                                        }),
                                        $("<button/>", {
                                            class: "btn btn-danger",
                                            type: "submit",
                                            html: "Cancelar NF-e"
                                        }),
                                    ]
                                })
                            ]
                        })
                    ]
                })
            ]
        })
    ] 
}).dialog({
    title: "Cancelar NFe",
    autoOpen: false,
    modal: true,
    width: 757,
    height: 576,
}),
reload_dados_nfe = function(){
    $.ajax({
        url: "/adm/vendas/vendas-detalhes.php?id=<?php echo $id_pedido?>",
        success: function(str){
            var list = $("<div/>", { html: str });
            $("#div-edicao").html(list.find("#div-edicao").html());
        }
    });
}, 
init_date_validade = (function(e) {
    var text = $(e.target).find("option:selected").attr("data-validade"),
        text_is_cancel = $(e.target).find("option:selected").attr("data-validade-cancel");
    
    cancelar_nfe_xml.find("#data-validade").removeClass("hidden").html(text);
    
    if( text_is_cancel > "0" )
        cancelar_nfe_xml.find("button").fadeOut(0);
    else
        cancelar_nfe_xml.find("button").fadeIn(0);
});

// Percorra e busque produtos sem NCM
emitir_nfe_xml.find("[data-invalid=true]").each(function() {
    // Oculta os buttons do sistema
    $("#reload_dados_nfe_buttons").fadeOut(100);
    
    // Adiciona um event no hml
    $($("<p/>", { 
        html: "Há produtos sem Dados Fiscais!", 
        class: "alert alert-danger text-center ft16px bold mb0" 
    })).insertBefore("#reload_dados_nfe");
});

emitir_nfe_xml.on("change", "select[name]", function(e){ 
    
    if( $("#remove_nfenr").length )
        $("#remove_nfenr").remove();
    
    if(e.target.name !== "id_emitente" || $(e.target).val() === '0')
        return false;
        
    $($("<div/>", { 
        id: "remove_nfenr", 
        class: "row ft12px", 
        html: [
            $("<div/>", {
                class: "col-md-12",
                html: [
                    ($(e.target).children("option:selected").attr("data-info") == 2 ? $("<div/>", {
                        class: "alert alert-warning ft18px text-center bold",
                        html: "Você está em Ambiente de Homologação"
                    }):null)
                ]
            })
        ]
    })).delay(110).queue(function(x){
        $(this).insertBefore("#emitir_nfe_xml");
        x();
    });

    emitir_nfe_xml.find("#nrnfe_text").html($(e.target).children("option:selected").attr("data-value")).addClass("text-danger pull-right bold ft12px");
    emitir_nfe_xml.find("input[name=nrnfe]").val($(e.target).children("option:selected").attr("data-nfe"));
    
});

emitir_nfe_xml.on("change", "#porcentagem", function( e ) {
    $.ajax({
        url: "/adm/nfe/nfe.php",
        data: { 
            acao: "gerar_percent_nfe", 
            id_pedido: "<?php echo $id_pedido?>",
            porcnota: $(e.target).val() 
        },
        beforeSend: function(){
            emitir_nfe_xml.html(save_nfe_xml);
        },
        complete: function(){
            emitir_nfe_xml.find("select[name=id_emitente]").trigger("change");
        },
        success: function( str ) {
            var list = $("<div/>", { html: str });
            emitir_nfe_xml.html(list.find("#emitir_nfe_xml").html());
        }
    });
});

// // Alterar o numero da nota
// emitir_nfe_xml.on("change", "#nrnfe", function ( e ) { 
//     var select_emitent = emitir_nfe_xml.find("select[name=id_emitente] :selected");
//     var empty = select_emitent.filter(function() {
//         return this.value === "";
//     });
    
//     if( empty.length ) {
//         alert("Selecione um emitente!");
//         return false;
//     }
    
//     $.ajax({
//         url: "/adm/nfe/nfe.php",
//         data: { 
//             acao: "alterar_num_nfe", 
//             nrnfe: $(e.target).val(),
//             id_emitente: select_emitent.val(),
//             id_pedido: "<?php echo $id_pedido?>"
//         },
//         beforeSend: function() { 
//             emitir_nfe_xml.html(save_nfe_xml);
//         },
//         complete: function(){
//             emitir_nfe_xml.find("select[name=id_emitente]").trigger("change");
//         },
//         success: function( str ) {
//             var list = $("<div/>", { html: str });
//             $("#emitir_nfe_xml" ).html(list.find("#emitir_nfe_xml").html());
//         }
//     });
// });

emitir_nfe_xml.ajaxForm({
    type: "post",
    url: emitir_nfe_xml.find("button[type]").attr("formaction") || emitir_nfe_xml.find("button[type]").attr("action"),
    beforeSubmit: function( formData, jqForm, options ) {
        
        var empty = emitir_nfe_xml.find("select[name='id_emitente'] :selected").filter(function(){ return this.value === ""; }),
            empty_ids = emitir_nfe_xml.find("input[name='id_produto[]']:checked"),
            empty_natop = emitir_nfe_xml.find("select[name='natOp'] option:selected").val();
        
        if( empty.length ) {
            alert("Selecione um emitente!");
            return false;
        } 
        // Devolução
        else if( empty_ids.length === 0 && strstr(empty_natop, ' ', true) === "Devolucao" ) {
            alert("Selecione os produtos!");
            return false;
        }
        else {
            return true;
        }
        
    },
    beforeSend: function(){
        emitir_nfe_xml.html([ 
            "Gerando XML, aguarde",
            $("<i/>", { class: "fa fa-spinner fa-pulse fa-fw"}) 
        ]);
    },
    success: function( str ) {
        var list = $("<div/>", { html: str });
        emitir_nfe_xml.html( list.find("#emitir_nfe_xml").html() );
        reload_dados_nfe();
    },
    complete: function() {
        emitir_nfe_xml.find("select[name=id_emitente]").trigger("change");
    },
    resetForm: true
});

// Abre a tela para cancelamento
emitir_nfe_xml.on("click", "#button_cancelar_nfe_xml", function(){
    cancelar_nfe_xml.dialog("open").find("select[name=id_nota]").trigger("change");
});

// Form para o cancelamento ajax
cancelar_nfe_xml.ajaxForm({
    beforeSubmit: function( formData, jqForm, options ) {
        var empty = cancelar_nfe_xml.find("select[name] :selected, input[name]").filter(function(){
            return this.value === "";
        });

        if( empty.length ) {
            alert("Selecione ou preencha todos os campos (*)!");
            return false;
        }
        else {
            return true;
        };
    },
    type: "post",
    success: function( str ) {
        var list = $("<div/>", { html: str });
        emitir_nfe_xml.html(list.find("#emitir_nfe_xml").html());
        reload_dados_nfe();
    },
    beforeSend: function(){
        // emitir_nfe_xml.find("button[type=submit]").remove("i");
        emitir_nfe_xml.html([ 
            "Cancelando NFE-e, aguarde",
            $("<i/>", { class: "fa fa-spinner fa-pulse fa-fw"}) 
        ]);
    },
    complete: function(){
        emitir_nfe_xml.find("select[name=id_emitente]").trigger("change");
        cancelar_nfe_xml.dialog("close");
    },
    resetForm: true
});

// Alterar os dados fiscais do produto
emitir_nfe_xml.on("click", ".btn-edit-ncm", function ( a ) { 
    var e = $(this),
        value_codigo_id = e.data("id");
    
    campos_ficais.dialog("open");

    $.ajax({
        url: "/adm/produtos/produtos-cadastrar.php?acao=ProdutosEditar&codigo_id=" + value_codigo_id,
        beforeSend: function(){
            campos_ficais.html(load_nfe_xml);
        },
        success: function( str ) {
            var list = $("<div/>", { html: str });
            campos_ficais.html([
                $("<h2/>", { class: "clearfix", html: list.find("#produto-nome").html() }),
                list.find("#campos_ficais").html(),
                $("<input/>", { name: "codigo_id", type: "hidden", value: value_codigo_id}),
                $("<input/>", { name: "acao", type: "hidden", value: "alterar_num_ncm"}),
                $("<style/>", {
                    html: ".ui-widget input, .ui-widget select, .ui-widget textarea, .ui-widget button { font-family: inherit; font-size: 16px; } #campos_ficais a.btn-danger{ display: none; }"
                })
            ]);
        }
    });
});

campos_ficais.on("click", "button[type=submit]", function(e) {
    e.preventDefault();
    
    var dataSerialize = campos_ficais.serialize();
    
    $.ajax({
        url: "<?php echo sprintf('/adm/nfe/nfe.php?acao=alterar_num_ncm&id_emitente=%u&id_pedido=%u', $id_emitente, $id_pedido)?>",
        data: dataSerialize,
        type: "post",
        beforeSend: function() {
            campos_ficais.html(save_nfe_xml);
        },
        complete: function() {
            emitir_nfe_xml.find("select[name=id_emitente]").trigger("change");
            campos_ficais.dialog("close");
        },
        success: function( str ) {
            var list = $("<div/>", { html: str });
            emitir_nfe_xml.html(list.find("#emitir_nfe_xml").html());
        }
    });
});

/**
 * Nota de Devolução
 */
emitir_nfe_xml.on("change", "select[name=natOp]", function( e ) {

    var nfe_buttons = $("#reload_dados_nfe_buttons");

    if(nfe_buttons.find("#devolucao").length > 0) nfe_buttons.find("#devolucao").remove();
    
    if(strstr(this.value, ' ', true) === "Devolucao") {
        nfe_buttons.append([$("<button/>", {type: "submit", class: "btn btn-danger", html: "gerar nota de devolução", id: "devolucao"})]);
        emitir_nfe_xml.find(".checkboxs").removeClass("hidden");
    } 
    else {
        emitir_nfe_xml.find(".checkboxs").addClass("hidden");
    }
});