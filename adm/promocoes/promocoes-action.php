<?php
include '../topo.php';

$POST['setup_ini'] = !empty($POST['setup_ini_date']) ? implode('-', array_reverse(explode('/', $POST['setup_ini_date']))) . ' ' . $POST['setup_ini_time'] : null;

$POST['setup_fin'] = !empty($POST['setup_fin_date']) ? implode('-', array_reverse(explode('/', $POST['setup_fin_date']))) . ' ' . $POST['setup_fin_time'] : null;

$Promocoes['loja_id'] = $CONFIG['loja_id'];
$Promocoes['id_marca'] = $POST['id_marca'];
$Promocoes['codigo_id'] = $POST['codigo_id'];
$Promocoes['setup_type'] = $POST['setup_type'];
$Promocoes['setup_value'] = dinheiro($POST['setup_value']);
$Promocoes['setup_text']  = addslashes($POST['setup_text']);
$Promocoes['setup_color'] = $POST['setup_color'];
$Promocoes['setup_hex'] = $POST['setup_hex'];
$Promocoes['setup_ini'] = $POST['setup_ini'];
$Promocoes['setup_fin'] = $POST['setup_fin'];

if( ! empty( $POST['id'] ) )
	Promocoes::action_cadastrar_editar([ 'Promocoes' => [ $POST['id'] => $Promocoes ] ], 'alterar', 'setup_text');
else
	Promocoes::action_cadastrar_editar([ 'Promocoes' => [ 0 => $Promocoes ] ], 'cadastrar', 'setup_text');

header('location: /adm/promocoes/promocoes.php');
return;
 
include '../rodape.php';