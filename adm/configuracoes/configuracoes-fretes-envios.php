<?php
include '../topo.php';	
?>
<style>
	body{ background-color: #f1f1f1 }
</style>

<div class="row">
	<div class="container mt50">
		<!-- <h2>Configurações de envios</h2>		 -->
		<?php
		$mensagem = array();

		if(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST')
		{
			if( isset( $POST['opcoes'] ) && $POST['opcoes'] != '' )
			{					
				$value = (array)$POST['opcoes'];
				$value = implode('|', $value);
				
				if( isset( $POST['id'] ) && $POST['id'] > '0' ) {
					if( ConfiguracoesFretesEnvios::action_cadastrar_editar(['ConfiguracoesFretesEnvios' => [ 
							$POST['id'] => [ 
									'fretes_envios' => $value,
									'fretes_tipo' => $POST['fretes_tipo'],
									'fretes_valor' => dinheiro($POST['fretes_valor']),
									'fretes_sob_vl' => (!empty($POST['fretes_sob_vl']) ? 1 : 0)
								] 
							] 
						], 'alterar', 'id') ) {
						header('Location: /adm/configuracoes/configuracoes-fretes-envios.php');
						return;
					}
				}
				
				if( ConfiguracoesFretesEnvios::action_cadastrar_editar(['ConfiguracoesFretesEnvios' => [ 0 => [
									$POST['id'] => [ 
									'fretes_envios' => $value,
									'fretes_tipo' => $POST['fretes_tipo'],
									'fretes_valor' => dinheiro($POST['fretes_valor']),
									'fretes_sob_vl' => (!empty($POST['fretes_sob_vl']) ? 1 : 0)
								] 
							] 
						] 
					], 'cadastrar', 'id') ) {
					header('Location: /adm/configuracoes/configuracoes-fretes-envios.php');
					return;
				}
			}
		}

		$Configuracoes = ConfiguracoesFretesEnvios::find(['conditions' => ['loja_id=?', $CONFIG['loja_id']]]);

		if( count($Configuracoes) == 0 ) {
			ConfiguracoesFretesEnvios::action_cadastrar_editar(['ConfiguracoesFretesEnvios' => [ 0 => [
							0 => [ 
							'fretes_envios' => '',
							'fretes_tipo' => '$',
							'fretes_valor' => 0.00,
							'fretes_sob_vl' => 0
						] 
					] 
				] 
			], 'cadastrar', 'id');
			header('Location: /adm/configuracoes/configuracoes-frete-envios.php');
			return;
		}

		extract(( count( $Configuracoes ) ? $Configuracoes->to_array() : [null] ));
		$ENVIOS = explode('|', $fretes_envios); 
		$VALORES = explode('|', $fretes_valor);

		$x = 0;
		$Servicos = [];
		$CorreiosServicos = CorreiosServicos::all(['conditions' => ['loja_id=?', $CONFIG['loja_id']]]);
		$JadLogServicos = JadLogServicos::all(['conditions' => ['loja_id=?', $CONFIG['loja_id']]]);
		if( count($CorreiosServicos) > 0 )
			foreach( $CorreiosServicos as $arr )
				$Servicos[] = ['servico' => $arr->servico_text, 'servico_cod' => $arr->servico_int];

		if( count($JadLogServicos) > 0 )
			foreach( $JadLogServicos as $arr )
				$Servicos[] = ['servico' => $arr->servico_text, 'servico_cod' => $arr->servico_int];
		?>
			
		<form action="/adm/configuracoes/configuracoes-fretes-envios.php" method="post" class="row mb15" id="recarre gar-form">
			<input type="hidden" name="id" value="<?php echo $id?>"/>
			<div class="col-md-6 col-xs-12">
				<div class="panel panel-default">
					<div class="panel panel-heading panel-store text-uppercase">Selecione as formas de envios do Frete</div>
					<div class="panel-body" style="min-height: 205px;">
						<?php foreach( $Servicos as $rws ) { ?>
							<div class="clearfix mb15" >
								<?php $test_text = sprintf('%s*%s', $rws['servico'], $rws['servico_cod'])?>

								<input type="checkbox" name="opcoes[]" id="<?php echo $rws['servico']?>" value="<?php echo $rws['servico']?>*<?php echo $rws['servico_cod']?>" <?php echo in_array($test_text, $ENVIOS) ? 'checked':''?>/>
								<label for="<?php echo $rws['servico']?>" class="input-checkbox"></label>
								<?php echo $rws['servico']?>
							</div>
						<?php } ?>
						<!-- 						
						<div class="clearfix mb15">
							<input type="checkbox" name="opcoes[PAC]" id="PAC" value="PAC" <?php echo in_array('PAC', $ENVIOS) ? 'checked':''?>/>
							<label for="PAC" class="input-checkbox"></label>
							PAC
						</div>
						<div class="clearfix mb15">
							<input type="checkbox" name="opcoes[SEDEX]" id="SEDEX" value="SEDEX" <?php echo in_array('SEDEX', $ENVIOS) ? 'checked':''?>/>
							<label for="SEDEX" class="input-checkbox"></label>
							SEDEX
						</div>
						<div class="clearfix mb15">
							<input type="checkbox" name="opcoes[JADLOG]" id="JADLOG" value="JADLOG" <?php echo in_array('JADLOG', $ENVIOS) ? 'checked':''?>/>
							<label for="JADLOG" class="input-checkbox"></label>
							JADLOG
						</div>
						<div class="clearfix">
							<input type="checkbox" name="opcoes[JADLOG-ECONOMICO]" id="JADLOG-ECONOMICO" value="JADLOG-ECONOMICO" <?php echo in_array('JADLOG-ECONOMICO', $ENVIOS) ? 'checked':''?>/>
							<label for="JADLOG-ECONOMICO" class="input-checkbox"></label>
							JADLOG-ECONÔMICO
						</div>  
						-->
						<div class="mt15">
							<button type="submit" class="btn btn-primary" <?php echo _P( $PgAt, $_SESSION['admin']['id_usuario'], 'incluir|alterar' )?>>salvar</button>
						</div>
					</div>    
				</div>
			</div>
			<div class="col-md-6 col-xs-12">
				<div class="panel panel-default">
					<div class="panel panel-heading panel-store text-uppercase">Subsidiar o valor do Frete</div>
					<div class="panel-body" style="height: 205px;">
						<small class="show">* Você pode oferecer um desconto nos valore do frete.</small>
						<div class="row">
							<div class="form-group col-sm-6">
								<label class="control-label">Selecione </label>
								<select name="fretes_tipo" class="form-control" onchange="if( this.value === '$') { $('input[name=fretes_valor]').mask('#.##0,00', { reverse: true }).val(''); } else { $('input[name=fretes_valor]').unmask().val(''); }">
									<option value="$"<?php echo $fretes_tipo == '$'?' selected':''?>>Valor em Real</option>
									<option value="%"<?php echo $fretes_tipo == '%'?' selected':''?>>Valor em Porcentagem</option>
								</select>
							</div>
							
							<div class="form-group col-sm-6">
								<label class="control-label">Valor</label>
								<input type="text" name="fretes_valor" class="form-control text-right" value="<?php echo ($fretes_tipo == '$' ? number_format($fretes_valor, 2, ',', '.') : $fretes_valor)?>"/>
							</div>

							<div class="form-group col-sm-7">
								<label class="control-label" for="">Sobre o valor</label>
								<input type="checkbox" name="fretes_sob_vl" id="fretes_sob_vl" class="form-control" value="1" <?php echo ($fretes_sob_vl == 1 ? ' checked':'')?>/>
								<label for="fretes_sob_vl" class="input-checkbox"></label>
								<small class="show">Check para subsidiar sobre o valor da compra.</small>
							</div>
							<div class="col-sm-5 mt25">
								<button type="submit" class="btn btn-primary" <?php echo _P($PgAt, $_SESSION['admin']['id_usuario'], 'incluir|alterar')?>>salvar</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>    
</div>    

<?php ob_start(); ?>
<script>		
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
</script>
<?php
$SCRIPT['script_manual'] .= ob_get_clean();
include '../rodape.php';