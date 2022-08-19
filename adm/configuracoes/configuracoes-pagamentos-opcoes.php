<?php
include '../topo.php';	
?>
<style>
    .formulario fieldset{
        border-radius: 3px;
        -moz-border-radius: 3px;
        -webkit-border-radius: 3px;
    }
    .formulario p {
        margin: 5px 0 7px 0;
        font-weight: 500;
    }
	body{ background-color: #f1f1f1 }
</style>

<div class="row" id="recarregar-form">
    <?php
    $mensagem = array();

    if(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST')
    {
        if( isset( $POST['opcoes'] ) && $POST['opcoes'] != '' )
        {					
            $value = (array)$POST['opcoes'];
            
            /**
             * Editar os dados do banco para transferencia
             */
            if( ! empty( $value['id'] ) && $value['id'] > 0) {
                if( ConfiguracoesPagamento::action_cadastrar_editar([ 'ConfiguracoesPagamento' => [ $value['id'] => [ 
                    'qtde_parcelas' => (isset( $value['qtde_parcelas'] ) ? $value['qtde_parcelas'] : 0),
                    'desconto_boleto' => (isset( $value['desconto_boleto'] ) ? $value['desconto_boleto'] : 0),
                    'parcela_minima' => (isset( $value['parcela_minima'] ) ? $value['parcela_minima'] : 0)
                ] ] ], 'alterar', 'id') ) {
                    header('Location: /adm/configuracoes/configuracoes-pagamentos-opcoes.php');
                    return;
                }
            } 
            /**
             * Cadastra um banco novo para transferencia
             */
            else {
                if( ConfiguracoesPagamento::action_cadastrar_editar([ 'ConfiguracoesPagamento' => [ 0 => [ 
                    'qtde_parcelas' => (isset( $value['qtde_parcelas'] ) ? $value['qtde_parcelas'] : 0),
                    'desconto_boleto' => (isset( $value['desconto_boleto'] ) ? $value['desconto_boleto'] : 0),
                    'parcela_minima' => (isset( $value['parcela_minima'] ) ? $value['parcela_minima'] : 0)
                ] ] ], 'cadastrar', 'id') ) {
                    header('Location: /adm/configuracoes/configuracoes-pagamentos-opcoes.php');
                    return;
                }
            }
        }
    }

    $Configuracoes = ConfiguracoesPagamento::find(['conditions' => ['loja_id=?', $CONFIG['loja_id']]]);
    if( count($Configuracoes) == 0 ) {
        ConfiguracoesPagamento::action_cadastrar_editar([ 'ConfiguracoesPagamento' => [ 0 => [ 
            'qtde_parcelas' => 12,
            'desconto_boleto' => 10,
            'parcela_minima' => 35.00
        ] ] ], 'cadastrar', 'id');
        header('Location: /adm/configuracoes/configuracoes-pagamentos-opcoes.php');
        return;
    }
	extract($Configuracoes->to_array());
	?>
    <!--[PAGAMENTO VIA PAGSEGURO]-->
    <form action="/adm/configuracoes/configuracoes-pagamentos-opcoes.php" method="post" class="form-action formulario mt50 col-md-8 col-md-offset-2 col-sm-12 col-xs-12">
		<!-- <h2>Opções de Pagamentos</h2> -->
        <input type="hidden" name="opcoes[id]" value="<?php echo $id;?>"/>
        <!--[PAGAMENTO VIA CIELO]-->
        <div class="panel panel-default">
			<div class="panel-heading panel-store text-uppercase">Configure as Opções de Pagamento de sua Loja</div>
            <div class="abri-configuracoes clearfix panel-body">
                <span class="pull-left w100 mb15">
                    <span class="mb5 show">Quantidade Máxima de Parcelas:</span>
                    <input type="text" name="opcoes[qtde_parcelas]" value="<?php echo $qtde_parcelas;?>" class="text-right"/>
                </span>
                <span class="pull-left w100 mb15">
                    <span class="mb5 show">Valor Mínimo da Parcela:</span>
                    <input type="text" name="opcoes[parcela_minima]" value="<?php echo $parcela_minima;?>" class="preco-mask text-right"/>
                </span>
                <span class="pull-left w100 mb15">
                    <span class="mb5 show">Desconto para Boleto/Transferência: <font class="ft11px">(O desconto deve ser em (%) porcentagem Ex: 10 = 10%</font></span>
                    <input type="text" name="opcoes[desconto_boleto]" value="<?php echo $desconto_boleto;?>" class="text-right"/>
                </span>
                <span class="show w100 mb15">
                    <button type="submit" class="btn btn-primary">salvar</button>
                </span>
                <span class="mb5 show">
                    * Configurações de pagamento com operadora Cielo, para configurações com outras platafomas acessar o site respectivo.
                    <font class="ft11px">(PagSeguro, MercadoPago, PayU, etc...)</font>
                </span>
            </div>
        </div>
    </form>
</div>    

<script>		
    <?php ob_start(); ?>
    $("#recarregar-form").on("click", "input[type=checkbox]", function(e){
        var $this = $(this),
            $AbriConfiguracoes = $this.parent().parent().find(".abri-configuracoes");
        if( $AbriConfiguracoes.is(":visible") ) {
            $AbriConfiguracoes.fadeOut();
            $this.parent().parent().find("button[type=submit]").trigger("click");
        }
        else {
            $AbriConfiguracoes.fadeIn();
        }
    });

    $("#recarregar-form").on("submit", "form", function(e){
        e.preventDefault();
       var $form = $(this);

        $( this ).ajaxSubmit({                    
            uploadProgress: function(event, position, total, percentComplete){ 
                infoSite("Enviando: " + percentComplete + "%", "info-concluido");
            },
            success : function( str ){ 
                var list = $("<div/>", { html: str });
                $("#recarregar-form").html( list.find("#recarregar-form").html() );
            },
            error: function(x,t,m){ 
                console.log(x.responseText + "\n" + t + "\n" + m ); 
            },
            url: window.location.href,
            type: "post",
            data: $form.serializeArray(),
            dataType: "html",
            cache: false
        });
    });
    <?php
    $SCRIPT['script_manual'] .= ob_get_clean();
    
    ?>
</script>

<?php
include '../rodape.php';