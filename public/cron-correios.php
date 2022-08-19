<?php
defined('PATH_ROOT') || define('PATH_ROOT', realpath($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR);
include PATH_ROOT . 'app/vendor/autoload.php';
include PATH_ROOT . 'app/settings.php';
include PATH_ROOT . 'app/settings-config.php';
include PATH_ROOT . 'app/includes/bibli-funcoes.php';
include PATH_ROOT . 'app/includes/ajax-emails.php';
include PATH_ROOT . 'adm/correios/correios-bootstrap.php';

// $AccessData->setUsuario('gomesbabylar');
// $AccessData->setSenha('gomes@1365');

$cod = [];
$etiquetas = [];

$cod = [];
$etiquetas = [];

$Pedidos = Pedidos::all([
  'conditions' => ['loja_id=? and status in(?)', $CONFIG['loja_id'], [7, 8]],
  'include' => ['pedido_log', 'correio_etiqueta']
]);

$PedidosCount = count($Pedidos);
if ($PedidosCount == 0) return;

foreach ($Pedidos as $rws) {
  if (!empty($rws->correio_etiqueta) && $rws->correio_etiqueta->id > 0) {
    $string = mask($rws->correio_etiqueta->etiqueta, "##########{$rws->correio_etiqueta->dv}##");

    $Etiqueta = new \PhpSigep\Model\Etiqueta();
    $Etiqueta->setEtiquetaComDv($string);
    $etiquetas[] = $Etiqueta;

    $cod[$string] = [
      'id' => $rws->id,
      'status' => $rws->pedido_log->status
    ];
  }
}

$RastrearObjeto = new \PhpSigep\Model\RastrearObjeto();
$PhpSigep = new PhpSigep\Services\SoapClient\Real();
$AccessData = new \PhpSigep\Model\AccessData();
$AccessData->setUsuario('2317761600');
$AccessData->setSenha('E40W;3@8?L');

$RastrearObjeto->setAccessData($AccessData);
$RastrearObjeto->setEtiquetas($etiquetas);

$ResultPhpSigep = $PhpSigep->rastrearObjeto($RastrearObjeto);
$boolean = $ResultPhpSigep->getErrorCode();

if (empty($boolean)) {
  $a = 1;
  foreach ($ResultPhpSigep->getResult() as $std) {
    $b = 1;
    $etiqueta = $std->getEtiqueta()->getEtiquetaComDv();

    $status = $cod[$etiqueta]['status'];
    $id_pedido = (int)$cod[$etiqueta]['id'];

    echo sprintf('Pedido %u está no status e %u<br>', $id_pedido, $status);
    foreach ($std->getEventos() as $std_even) {
      $label = trim($std_even->getDescricao());
      $label_simple = substr($label, 0, 14);

      echo sprintf('%u Registro e %u de consultas "%s"<br>', $a, $b, $label);

      // Adiciona o status de Separacao de Estoque
      if ($label_simple == 'Objeto postado' && $status == 7) {
        echo 'Emitido um novo registro para o pedido<br/>';
        // // Adicona o status de Separação de Estoque para Transporte
        // Pedidos::action_cadastrar_editar(['Pedidos' => [$id_pedido => ['status' => 8, 'rastreio' => $etiqueta]]], 'alterar', 'codigo');

        $Pedidos = Pedidos::find((int)$id_pedido);
        $Pedidos->rastreio = $etiqueta;
        $Pedidos->status = 8;
        $Pedidos->save();

        // Adiciona um novo logs de pedidos
        PedidosLogs::logs($id_pedido, 0, 'Pedido em Transporte', 8);
      }
      // Adiciona o status de Transporte para Entregue
      else if ($label_simple == 'Objeto entregu' && $status == 8) {
        echo 'Emitido um novo registro para o pedido<br/>';
        // // Adicona o status de Separação de Entregue
        // Pedidos::action_cadastrar_editar([ 'Pedidos' => [ $id_pedido => [ 'status' => 9 ] ] ], 'alterar', 'codigo');

        $Pedidos = Pedidos::find((int)$id_pedido);
        $Pedidos->status = 9;
        $Pedidos->save();

        // Adiciona um novo logs de pedidos
        PedidosLogs::logs($id_pedido, 0, 'Pedido entregue ao destinatário', 9);
      }
      $b++;
    }
    echo '<hr>';
    $a++;
  }
}
return;
// return;
// $Correios->setCode('QD363523259BR');
// $Eventos = $Correios->getEventsList();
// printf('<pre>%s</pre>', print_r($Eventos, 1));
// return;

// print_r($etiquetas);
// // Lista todos eventos
// foreach( $cod as $key => $rws ) {
// 	$Correios->setCode($etiquetas);
// 	$Eventos = $Correios->getEventsList();
// 	foreach( $Eventos as $even )
// 	{
// 		$label = trim($even->getLabel());

// 		// Adicona o status de Separação de Estoque para Transporte
// 		if($label == 'Objeto postado' && $rws['status'] == 7) {
// 			echo 'PED.: ' . $key . ' - ' . $label . '<br/>';
// 			// email_confirmacao_compra($key, 8, $rws['etiqueta'], '');
// 		}
// 		// Adiciona o status de Transporte para Entregue
// 		else if($label == 'Objeto entregue ao destinatário' && $rws['status'] == 8) {
// 			echo 'PED.: ' . $key . ' - ' . $label . '<br/>';
// 			// email_confirmacao_compra($key, 9, $rws['etiqueta'], '');
// 		}

// 		// if( in_array(trim($even->getLabel()), ['Objeto entregue ao destinatário', 'Objeto postado']) ) {
// 			// printf('<pre>%s</pre>', print_r($even->getLabel(), true));
// 			// printf('<pre>%s</pre>', print_r($even->getLocation(), true));
// 			// printf('<pre>%s %s</pre>', $even->getDate(), $even->getHour());
// 		// }
// 	}
// 	unset($Eventos);
// 	sleep(0.2);
// }

// // return;