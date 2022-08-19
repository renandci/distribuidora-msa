<?php
include 'topo.php';

$token = array();
$token['hashid'] = $CONFIG['loja_id'];
$token['assets'] = $CONFIG['nome_fantasia'];
$token['user'] = implode('@', [ASSETS, 'desktop.com.br']);
$token['pass'] = '123123';

$secret_server_key = sprintf('A4!%s*44%d', ASSETS, $CONFIG['loja_id']);
$jwt = ReallySimpleJWT\Token::customPayload($token, $secret_server_key);
?>

<style>
	body{ background-color: #f1f1f1 }
</style>
<div class="container">
	<div class="row">
		<div class="panel panel-default">
			<div class="panel-heading panel-store text-uppercase">API <small>Configuração da chave da api via desktop</small></div>
			<div class="panel-body">
				<small class="show">Sua chave de authentication. cópie e guarde em um lugar em que voçe possa utilizar mais tarde.</small>
				<div class="show alert alert-info mt5 mb15" style="word-break: break-word;">
					<?php echo $jwt?>
					<input type="hidden" value="<?php echo $jwt?>" id="input_copy">
				</div>
				<!-- <button onclick="clipboard()" class="btn btn-info">Cópiar</button> -->
			</div>
		</div>
		<script>
		// clipboard = function() {
		//   var copyText = document.getElementById("input_copy").value;
		//   copyText.select();
		//   document.execCommand("copy");
		//   alert("Copiado: " + copyText.value);
		// }
		</script>
	</div>
</div>

<?php
include 'rodape.php'; 