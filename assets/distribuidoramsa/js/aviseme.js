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
    acaoCadastro : function(form){
        var $this = $(form), 
            $text = $this.find("button").html(),
            $InputDados = $this.serialize();
        
        $this.find("button")
             .prop("disabled", true)
             .addClass("disabled")
             .html('<center><img src="/imgs/ajax-loader.gif" class="mt10 mb10 tag-block" width="43px"/></center>');
        
        $.ajax({
            url : window.location.href,
            type: 'post',
            dataType : 'json',
            data : $InputDados,
            
            success : function(str){
                if(str.error===false) {
                    $("#modal-aviseme").find("div").html(str.mensagem);
                }
                else {
                    $this.find("button")
                    .prop("disabled", false)
                    .removeClass("disabled")
                    .html($text);
            
                    $("#modal-aviseme").find("button").after(str.mensagem);
                }
            },
            error : function(a,b,c){
                console.log(a.responseText+"\n"+b+"\n"+c);
            }
        });
    },
    initScript : function($PRODUTO_ID){
        $(function(){
            $.ajax({
                url : window.location.href,
                dataType : 'json',
                data : { acao : 'InfoAviseMe', produto_id : $PRODUTO_ID },
                success : function(str){
                    $('#modal-aviseme').find('#dados-produto').html(str.html);
                },
                error : function(a,b,c){
                    console.log(a.responseText+"\n"+b+"\n"+c);
                }
            });
            $("#modal-aviseme").validate({
                debug : false,
                errorClass : "error-aviseme",
                validClass : "valid-aviseme",
                rules : {
                    nome : { required : true, minlength : 5 },
                    email : { required : true, email : true } 
                },
                messages : {
                    nome : { required : "Digite seu nome completo", minlength : "Digite seu nome" }, 
                    email : { required : "Digite seu e-mail", email : "Digite um e-mail válido" }	
                },
                submitHandler : AviseMe.acaoCadastro
            }); 
        });
    },
    tela : function(){
        var $href = window.location.href, $PRODUTO_ID = $("#recarregar-html").find("#div-produto").attr("datavalue") || $href.split('/').pop(-1),
        telaForm = $("<form>", {
            id : "modal-aviseme",
            class : "modal-aviseme",
            method : "post",
            action : "",
            append : [
                $("<a>", {
                    id : "close", 
                    class : "fa fa-close",
                    onclick : "$(this).parent().prev().fadeOut(0);$(this).parent().fadeOut(0);$(this).parent()[0].reset();"
                }),
                $("<div>",{
                    append : [
                        $("<script>", {
                            type : "text/javascript",
                            append : ["AviseMe.initScript("+$PRODUTO_ID+");"]
                        }),
                        $("<div>", {
                            id : "dados-produto",
                            class : "clearfix text-center",
                            append:[
                                '<center><img src="/imgs/ajax-loader.gif" class="mt10 mb10 tag-block" width="43px"/></center>'
                            ]
                        }),
                        $("<input>",{ type : "text", name : "nome", placeholder : "Seu nome:", autocomplete:"off" }),
                        $("<input>",{ type : "text", name : "email", placeholder : "Seu e-mail:", autocomplete:"off" }),
                        $("<input>",{ type : "hidden", name : "produtos_id", value : $PRODUTO_ID }),
                        $("<input>",{ type : "hidden", name : "acao", value : "AviseMeCadastro" }),
                        $("<button>",{ type : "submit", text : "cadastrar", class: "btn btn-primary btn-large btn-block" })
                    ]
                })
            ]
        });
        if($("#modal-aviseme")) {
            $(".div-absoluta").fadeIn(0).after(telaForm.fadeIn());
            return false;
        }
        else{
            $(".div-absoluta").fadeIn(0).next().fadeIn();
            return false;
        }
            
    },
    button : function(){
        var NewButton = $("<button>", {
            id : "btn-aviseme",
            type : "button",
            class : "btn btn-aviseme mt5 btn-large",
            onclick : "AviseMe.tela();",
            append : [
                $("<i>", {
                    class : "fa fa-paper-plane-o"
                }),
                $("<span>", {
                    text:"avise-me!"
                })
            ]
        });
        
        if(!$(document).find("#btn-aviseme").length) 
            return $(document).find("#btn-comprar").after(NewButton);
        
    },
    produto : function($ID){
        
        $.ajax({
            url : window.location.href,
            data : { acao : 'AviseMeInit', produto_id : $ID },
            dataType : 'json',
            success : function(str){
                
                AviseMe.button();
                
                $(document).find("button#btn-aviseme").fadeOut(0);
                $(document).find("button#btn-comprar").removeClass("disabled").fadeIn(0).prop("disabled", false);
                $(document).find("button#btn-add-lista").removeClass("disabled").fadeIn(0).prop("disabled", false);
                
                if(str.estoque===false){
                    $(document).find("button#btn-aviseme").fadeIn(0);
                    $(document).find("button#btn-comprar").addClass("disabled").prop("disabled", true).fadeOut(0);
                    $(document).find("button#btn-add-lista").addClass("disabled").prop("disabled", true).fadeOut(0);
                }
                
            },
            error : function(a,b,c){
                for(var i in a) {  
                    if(i!=="channel"){
                        console.log(a[i]+'\n'+b+'\n'+c);
                    }
                }
            }
        });
    },
    init : function(ID){
        $.ajax({
            url : window.location.href,
            type : 'post',
            data : { acao : 'verificaEstoque', id : ID },
            dataType : 'json',
            success : function(str){
                if(str.estoque === '0' || str.estoque <= '0'){ 
                    $('#text-btn-comprar').find('b').html('Produto indisponível no momento!'); 
                    $('#btn-comprar').fadeOut(0); 
                    $('#btn-add-lista').fadeOut(0); 
                    $('.ocultar-estoque').fadeOut(0); 
                    $('#btn-avise-me').fadeIn(0).attr('data-id',ID); 
                }else{ 
                    $('#btn-avise-me').fadeOut(0).attr('data-id',ID); 
                    $('.ocultar-estoque').fadeIn(0); 
                    $('#text-btn-comprar').find('b').empty(); 
                    $('#btn-comprar').fadeIn(0); 
                    $('#btn-add-lista').fadeIn(0); 
                };
            }
        });
    }
};