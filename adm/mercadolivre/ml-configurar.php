<?php
include '../topo.php';

if( isset($POST['acao']) && $POST['acao'] == 'ConfigApp' )
{
	if( empty($MercadoLivre) && count( $MercadoLivre ) == 0 ){
		$MercadoLivre = new MercadoLivre();
	}
    $MercadoLivre->loja_id = $CONFIG['loja_id'];
    $MercadoLivre->app = $POST['app'];
	$MercadoLivre->app_id = $POST['app_id'];
	$MercadoLivre->app_key = $POST['app_key'];
	
	$return = $MercadoLivre->save();
    if( $return > 0 ){
        header('Location: /adm/mercadolivre/ml-configurar.php?e=0');
        return;
    } else {
        header('Location: /adm/mercadolivre/ml-configurar.php?e=1');
        return;
    }
}
	if( isset($GET['e']) && $GET['e'] == 0 ){
		echo '<div class="alert alert-success text-center">Salvo com sucesso!</div>';
	}
?>
    <form action="/adm/mercadolivre/ml-configurar.php" method="post" class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 col-sm-7 col-sm-offset-1 col-xs-12">
        <fieldset>
            <legend>Configurações do Mercado Livre</legend>
            
            <input type="hidden" name="acao" value="ConfigApp"/>
            <input type="hidden" name="id" value="<?php echo $CONFIG_MELI['id']?>"/>
            <p>Nr. Aplicação</p>
            <input name="app" type="text" class="mb15" size="20" value="<?php echo $CONFIG_MELI['app']?>"/>
            
            <p>Identificador da Aplicação</p>
            <input name="app_id" type="text" class="mb15" size="38" value="<?php echo $CONFIG_MELI['app_id']?>"/>
            <p>Chave da Aplicação</p>
            <input name="app_key" type="text" class="mb15" size="55" value="<?php echo $CONFIG_MELI['app_key']?>"/>
            
            <center>
                <button type="submit" class="btn btn-primary">configurar</button>
            </center>
            
        </fieldset>
    </form>
<?php
include '../rodape.php';