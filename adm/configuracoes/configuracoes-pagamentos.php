<?php
include '../topo.php';	
$Configuracoes = ConfiguracoesPagamento::find(['conditions' => ['loja_id=?', $CONFIG['loja_id']]]);

$mensagem = array();
if(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'GET') {
	/**
	 * Exclui os dados do banco
	 */
	if(isset($GET['acao']) && $GET['acao'] == 'excluir') {
		$id = (INT)$GET['id'];
		if($id > 0) {
			// buscar o logo do banco se existir
			$logo = ConfiguracoesTransferencia::find($id);
			$logo = $logo->to_array();
			if(isset($logo['banco_logo']) && $logo['banco_logo'] != '')
			{
				if (file_exists("../../public/imgs/imagens-bancos/{$logo['banco_logo']}")) 
				{
					array_map('unlink', glob("../../public/imgs/imagens-bancos/{$logo['banco_logo']}"));
					array_map('unlink', glob("../../public/imgs/imagens-bancos/xs-{$logo['banco_logo']}"));
					$mensagem[ $id ] = 'Imagens excluida com successo!';
				}
			}
			
			if( ConfiguracoesTransferencia::action_cadastrar_editar(['ConfiguracoesTransferencia' => [ $id => [ 'id' => $id ] ] ], 'delete', 'banco_razaosocial') ) {
				$mensagem[ $id ] = '<br/>Banco com successo!';
			}
		}
	}
}

/**
 * Cadastrar ou Editar os dados
 */
if(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST') 
{
	/**
	 * Editar os dados do banco para transferencia
	 */
	if( ! empty( $POST['id'] ) && $POST['id'] > 0) {
		if( ConfiguracoesTransferencia::action_cadastrar_editar([ 'ConfiguracoesTransferencia' => [ $POST['id'] => [ 
			'banco_titulo' => $POST['banco_titulo'],
			'banco_razaosocial' => $POST['banco_razaosocial'],
			'banco_cpfcnpj' => $POST['banco_cpfcnpj'],
			'banco_tipocc' => $POST['banco_tipocc'],
			'banco_cc' => $POST['banco_cc'],
			'banco_ag' => $POST['banco_ag'],
			'banco_operacao' => $POST['banco_operacao'],
			'banco_cc' => $POST['banco_cc'],
			'banco_pix' => $POST['banco_pix'],
			'opcoes_pagamento_id' => $POST['opcoes_pagamento_id'],
		] ] ], 'alterar', 'id') ) {
			$mensagem[ $POST['id'] ] = 'Dados salvo com sucesso!';
		}
	} 
	/**
	 * Cadastra um banco novo para transferencia
	 */
	else {
		if( ConfiguracoesTransferencia::action_cadastrar_editar([ 'ConfiguracoesTransferencia' => [ 0 => [ 
			'banco_titulo' => $POST['banco_titulo'],
			'banco_razaosocial' => $POST['banco_razaosocial'],
			'banco_cpfcnpj' => $POST['banco_cpfcnpj'],
			'banco_tipocc' => $POST['banco_tipocc'],
			'banco_cc' => $POST['banco_cc'],
			'banco_ag' => $POST['banco_ag'],
			'banco_operacao' => $POST['banco_operacao'],
			'banco_cc' => $POST['banco_cc'],
			'banco_pix' => $POST['banco_pix'],
			'opcoes_pagamento_id' => $POST['opcoes_pagamento_id'],
		] ] ], 'cadastrar', 'id') ) {
			$mensagem[ $POST['id'] ] = 'Dados salvo com sucesso!';
		}
	}

	$temp = current($_FILES);
	if( is_array( $temp ) )
	{    
		if($temp['error']) {
			$mensagem[ $POST['id'] ] .= '<br/>Não é possível enviar a imagem!';
		}

		// Array com as extensões permitidas
		$extensoes_permitidas = array('.jpg', '.gif', '.png');

		// Faz a verificação da extensão do arquivo enviado
		$extensao = strrchr($temp['name'], '.');

		// Faz a validação do arquivo enviado
		if(in_array($extensao, $extensoes_permitidas) === true)
		{
			if($temp['size'] > 0 ) 
			{
				$ConfTrans = ConfiguracoesTransferencia::find($POST['id']);
				$anterior = $ConfTrans->to_array();
				
				$ext = pathinfo($temp['name']);
				$ext = $ext['extension'];
				$name = uniqid( time() ) . '.' . $ext; 

				if(isset($anterior['banco_logo']) && $anterior['banco_logo'] != '')
				{
					if (file_exists("../../public/imgs/imagens-bancos/{$anterior['banco_logo']}")) 
					{
						array_map('unlink', glob("../../public/imgs/imagens-bancos/{$anterior['banco_logo']}"));
						array_map('unlink', glob("../../public/imgs/imagens-bancos/xs-{$anterior['banco_logo']}"));
					}
				}

				$img = WideImage\WideImage::load($temp['tmp_name']);
				$img->resize(200, 200)->saveToFile("../../public/imgs/imagens-bancos/{$name}");
				$img->resize(45, 45)->saveToFile("../../public/imgs/imagens-bancos/xs-{$name}");
				$img->destroy();

				if ( ConfiguracoesTransferencia::action_cadastrar_editar(['ConfiguracoesTransferencia' => [ $POST['id'] => ['banco_logo'=>$name] ] ], 'alterar', 'id') ) {
					$mensagem[ $POST['id'] ] .= '<br/>Imagem salva com sucesso!';
				}
			}
		}
		else {
			$mensagem[ $POST['id'] ] .= '<br/>Atenção, envie somente imagens jpg, gif, png!';
		}
	}                
}

?>
<style>
    .formulario div{
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

<div class="container">
	<div class="row">
		<div class="col-xs-12 text-center">
			<button type="button" class="btn btn-primary btn-adcionar" id="clonar">adicionar</button>
			<h2>Configurações de pagamento via Transferência</h2>
		</div>
		
		<?php
		$ConfiguracoesTransferencia = ConfiguracoesTransferencia::all([ 'conditions' => [ 'opcoes_pagamento_id=? and loja_id=?', $Configuracoes->id, $Configuracoes->loja_id ], 'order' => 'banco_titulo asc' ]);
		foreach( $ConfiguracoesTransferencia as $rs ) { 
		?>
			<div class="col-sm-6 col-xs-12">
				<div class="panel panel-default" id="recarregar-form">
					<div class="panel-heading panel-store text-uppercase">
						TRANSFERÊNCIA <?php echo $rs->banco_titulo;?>
					</div>
					<form action="/adm/configuracoes/configuracoes-pagamentos.php" method="post" class="form-action formulario panel-body" enctype="multipart/form-data">
						<span class="pull-right text-uppercase">
							<input type="checkbox" name="banco_pix" id="banco_pix_<?php echo $rs->id?>" value="1" <?php echo $rs->banco_pix ? 'checked':''?>/>
							<label for="banco_pix_<?php echo $rs->id?>" class="input-checkbox"></label>
							Vincular com Pix
						</span>
						<div class="mb15">
							<div class="img">
								<?php if($rs->banco_logo != ''){ ?>
								<img src="<?php echo Imgs::src(sprintf('imagens-bancos-xs-%s', $rs->banco_logo), 'public')?>?v=<?php echo substr(time(), -2)?>" width="45"/>
								<?php } else { ?>
								Logo do banco
								<?php } ?>
							</div>
							<?php
							if(is_array($mensagem)) { 
							echo sprintf('%s', $mensagem[ $rs->id ]);
							}
							?>
							<input type="hidden" name="id" value="<?php echo $rs->id?>"/>
							<input type="hidden" name="opcoes_pagamento_id" value="<?php echo $Configuracoes->id?>"/>
							<span class="pull-left w100 mb15">
								<label class="mb5 show">Banco:</label>
								<input type="text" name="banco_titulo" value="<?php echo $rs->banco_titulo;?>" class="w80 form-control"/>
							</span>
							<span class="pull-left w100 mb15">
								<label class="mb5 show">Titular:</label>
								<input type="text" name="banco_razaosocial" value="<?php echo $rs->banco_razaosocial;?>"  class="w70 form-control"/>
							</span>
							<span class="pull-left w100 mb15">
								<label class="mb5 show">CNPJ/CPF:</label>
								<input type="text" name="banco_cpfcnpj" value="<?php echo $rs->banco_cpfcnpj;?>"  class="form-control" autocomplete="off" onkeypress="return mascara(this)"/>
							</span>
							<div class="mb35">
								<div>Tipo de Conta</div>
								<div class="clearfix">
									<span class="pull-left mr15">
										<input type="radio" id="corrente<?php echo $rs->id?>" name="banco_tipocc" value="Conta Corrente"<?php echo 'Conta Corrente' == $rs->banco_tipocc ? ' checked' : ''?>>
										<label for="corrente<?php echo $rs->id?>" class="input-radio"></label>
										Conta Corrente
									</span>
									<span class="pull-left">
										<input type="radio" id="poupanca<?php echo $rs->id?>" name="banco_tipocc" value="Conta Poupança" <?php echo 'Conta Poupança' == $rs->banco_tipocc ? ' checked' : ''?>>
										<label for="poupanca<?php echo $rs->id?>" class="input-radio"></label>
										Conta Poupança
									</span>
								</div>

								<span class="pull-left w100 mb15">
									<span class="mb5 show tipo-conta-text">Conta:</span>
									<input type="text" name="banco_cc" value="<?php echo $rs->banco_cc;?>" class="w40 form-control"/>
								</span>

								<span class="pull-left w100 mb15">
									<label class="mb5 show">Agência:</label>
									<input type="text" name="banco_ag" value="<?php echo $rs->banco_ag;?>" class="w40 form-control"/>
								</span>

								<span class="pull-left w100 mb15 <?php echo !$rs->banco_operacao ? 'hidden' : '';?> banco-operacao">
									<label class="mb5 show">Operção:</label>
									<input type="text" name="banco_operacao" value="<?php echo $rs->banco_operacao;?>" class="w30 form-control"/>
								</span>                        
							</div>
							<span class="pull-left w100">
								<label class="mb5 show">Logotipo:</label>
								<input type="file" name="banco_logo" class="form-control"/>
							</span>
							<span class="pull-left w100 mt15">
								<button type="submit" class="btn btn-primary">salvar</button>
								<a href="/adm/configuracoes/configuracoes-pagamentos.php?acao=excluir&id=<?php echo $rs->id?>" class="btn btn-danger btn-excluir">excluir</a>
							</span>
						</div>
					</form>
				</div>
			</div>
		<?php } ?>
	</div>
</div>    

<?php ob_start(); ?>
<script>		
	// var MaskCnpjCpfOptions = {
        // onKeyPress: function (cpf, ev, el, op) {
            // var masks = ['000.000.000-000', '00.000.000/0000-00'],
                // mask = (cpf.replace(/\D/g, '').length > 11) ? masks[1] : masks[0];
            // el.mask(mask, op);
        // }
    // };
    // $("input[name=banco_cpfcnpj]").mask('000.000.000-009999', MaskCnpjCpfOptions);
	mascara = function (str) {	
        if (str.value.length > 14)                       
            str.value = cnpj(str.value);
        else                           
            str.value = cpf(str.value);
    };

    function cpf(valor) {
        valor = valor.replace(/\D/g, "");                   
        valor = valor.replace(/(\d{3})(\d)/, "$1.$2");
        valor = valor.replace(/(\d{3})(\d)/, "$1.$2");
        valor = valor.replace(/(\d{3})(\d)$/, "$1-$2");     
        return valor;
    }

    function cnpj(valor) {
        valor = valor.replace(/\D/g, "");
        valor = valor.replace(/^(\d{2})(\d)/, "$1.$2");
        valor = valor.replace(/^(\d{2})\.(\d{3})(\d)/, "$1.$2.$3");
        valor = valor.replace(/\.(\d{3})(\d)/, ".$1/$2");
        valor = valor.replace(/(\d{4})(\d)/, "$1-$2");              
        return valor;
    }
    

    $("#clonar").click(function(){
        var $fom = $("<form/>", { 
            action: "/adm/configuracoes/configuracoes-pagamentos.php",
            id: "action",
            method: "post",
            class: "form-action formulario mb15",
            enctype: "multipart/form-data",
            html: [
                $("<input/>",{
                    type: "hidden",
                    name: "opcoes_pagamento_id", 
                    value: "<?php echo $Configuracoes->id?>"
                })
            ]
        });
        $("#conteudos-recarregar").html( $fom );
        $("#conteudos-recarregar").find("#action").delay(100).trigger("submit");
    });

    $("#recarregar-form").on("submit", "form", function(e){
        e.preventDefault();
       var $form = $(this);

        $( this ).ajaxSubmit({                    
            uploadProgress: function(event, position, total, percentComplete){ 
                $("#status-alteracao").fadeIn(0).html('Enviando imagem '+percentComplete+'%');
            },
            success : function( str ){ 
                var list = $("<div/>", { html: str });
                $("#recarregar-form").html( list.find("#recarregar-form").html() );
            },
            error: function(x,t,m){ console.log(x.responseText + "\n" + t + "\n" + m ); },
            beforeSend : function(){  },
            url: window.location.href,
            type: "post",
            data: $form.serialize(),
            dataType: "html",
            cache: false
        });
    });

    $("#recarregar-form").on("change", "input[type=file]", function () {
        var $this = $(this),
            $form = $this.parent().parent().find("div.img");
        var img = document.createElement("img"),
            file = this.files[0];                                    
        var reader = new FileReader();            
        reader.onloadend = function() {
            img.src = reader.result;  
            img.setAttribute("style", "max-width:45px;max-height:45px;width:45px;");
        };
        reader.readAsDataURL(file);
        $form.html(img);
    });

    $("#recarregar-form").on("click", ".btn-excluir", function(e){
        e.preventDefault();
        if(!confirm("Deseja realmente excluir!")) return false;
        $.ajax({
            url: e.target.href||this.href,
            beforeSend : function(){ 
                $("#status-alteracao").fadeIn(0).html('Excluindo dados...');
            },
            complete : function(){ 
                $("#status-alteracao").fadeIn(0).html('Excluido com sucesso...');
            },
            success: function(str){
                var list = $("<div/>", { html: str });
                 $("#recarregar-form").html( list.find("#recarregar-form").html() );
            },
            error: function(x,t,m){ 
                console.log(x.responseText + "\n" + t + "\n" + m ); 
            }
        });
    });

    $(document).on("click", "input[type=radio]", function() {
        $(this).parent().parent().parent().find("span").find(".tipo-conta-text").html( $(this).val() ); 
        if($(this).val() === 'Conta Poupança'){
            $(this).parent().parent().parent().find(".banco-operacao").removeClass("hidden"); 
        } else {
            $(this).parent().parent().parent().find(".banco-operacao").addClass("hidden").find("input").val(""); 
        }     
    });

    $(document).ajaxStop(function(){});
</script>
<?php $SCRIPT['script_manual'] .= ob_get_clean(); 
include '../rodape.php';