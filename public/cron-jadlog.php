<?php
defined('PATH_ROOT') || define('PATH_ROOT', realpath($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR);
include PATH_ROOT . 'app/vendor/autoload.php';
include PATH_ROOT . 'app/settings.php';
include PATH_ROOT . 'app/settings-config.php';
include PATH_ROOT . 'app/includes/bibli-funcoes.php';
include PATH_ROOT . 'app/includes/ajax-emails.php';
include PATH_ROOT . 'adm/correios/correios-bootstrap.php';

$JadLogNew = new JadLogNew($CONFIG['jadlog']['token']);

$cod = [];
$consultas = [];

$Pedidos = Pedidos::all([
  'conditions' => ['loja_id=? and status in(?)', $CONFIG['loja_id'], [7, 8]],
  'include' => ['pedido_log', 'jadlog_etiqueta',]
]);

$PedidosCount = count($Pedidos);
if ($PedidosCount == 0) return;

foreach ($Pedidos as $rws) {
  if (!empty($rws->jadlog_etiqueta) && $rws->jadlog_etiqueta->id > 0) {
    $cod[$rws->jadlog_etiqueta->codigo] = [
      'id' => $rws->id,
      'status' => $rws->pedido_log->status
    ];
    $consultas['consulta'][] = ['codigo' => $rws->jadlog_etiqueta->codigo];
  }
}

$return = $JadLogNew->post('/tracking/consultar', $consultas);

$boolean = $return['body']->consulta;
// printf('<pre>%s</pre>', print_r($boolean, 1));
if (!empty($boolean)) {
  $a = 0;
  foreach ($boolean as $std) {
    $b = 0;
    $codigo = $std->codigo;
    foreach ($std->tracking->eventos as $rws) {
      $label = trim($rws->status);
      $status = $cod[$codigo]['status'];
      $id_pedido = (int)$cod[$codigo]['id'];

      echo sprintf('%u Registro e %u de consultas "%s"<br>', $a, $b, $label);

      // Adicona o status de Separação de Estoque para Transporte
      if ($label == 'EMISSAO' && $status == 7) {
        echo 'Emitido um novo registro para o pedido<br/>';
        // // Adicona o status de Separação de Estoque para Transporte
        // Pedidos::action_cadastrar_editar([ 'Pedidos' => [ $id_pedido => [ 'status' => 8, 'rastreio' => $etiqueta ] ] ], 'alterar', 'codigo');

        $Pedidos = Pedidos::find((int)$id_pedido);
        $Pedidos->rastreio = $etiqueta;
        $Pedidos->status = 8;
        $Pedidos->save();

        // Adiciona um novo logs de pedidos
        PedidosLogs::logs($id_pedido, 0, 'Pedido em Transporte', 8);
      }
      // Adiciona o status de Transporte para Entregue
      else if ($label == 'ENTREGUE' && $status == 8) {
        echo 'Emitido um novo registro para o pedido<br/>';
        // // Adicona o status de Separação de Estoque para Transporte
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