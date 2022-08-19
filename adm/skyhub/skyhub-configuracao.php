<?php
include '../topo.php';


if(isset($POST['user']) && $POST['user'] != '') {

$id = (int)$POST['id'];
$user = $POST['user'];
$api_key = $POST['api_key'];
$account = $POST['api_key'];

    Skyhub::action_cadastrar_editar([
        'Skyhub' => [ 
            $id => [ 
            'user' => $user,
            'api_key' => $api_key,
            'account' => $api_key
            ] 
        ] 
    ], 'alterar', 'user');

        header('Location: /adm/skyhub/skyhub-configuracao.php');
        return;
}

$Skyhub = Skyhub::find(['conditions' => ['excluir = 0 and loja_id=?', $CONFIG['loja_id'] ]]);
$bdMarcas = Marcas::all(['conditions' => ['excluir=? and loja_id=?', 0, $CONFIG['loja_id']], 'order' => 'marcas asc']);

// $Count = (int)count($ConfigSkyhub);
// if( $Count == 0){
//     header('location: /adm/sair.php?acao=sair');
//     return;
// }
?>

<style>
	body{ background-color: #f1f1f1 }
</style>
<div class="row">
	<div class="col-md-6 col-md-offset-3 col-sm-12 col-xs-12 mt50">
		<div class="panel panel-default mb25">
			<div class="panel-heading panel-store text-uppercase">CONFIGURAÇÕES - <small>SkyHub</small></div>
			<div class="panel-body">
                <form action="/adm/skyhub/skyhub-configuracao.php" class="clearfix" method="post">
                    <fieldset>
                        <legend>Configurações Gerais</legend>
                        <div class="form-group">
                            <label>Usuário:</label>
                            <input type="text" name="user" value="<?php echo $Skyhub->user;?>" class="form-control"/>
                        </div>
                        <div class="form-group">
                            <label>TOKEN:</label>
                            <input type="text" name="api_key" value="<?php echo $Skyhub->api_key;?>" class="form-control"/>
                            <input type="hidden" name="id" value="<?php echo $Skyhub->id;?>" class="hidden"/>
                        </div>
                        <button type="submit" class="btn btn-primary">salvar</button>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
// $SCRIPT['script_manual'] .= ob_get_clean();
include '../rodape.php';