<?php
include '../topo.php';	
AcessoML($_SESSION, $PgAt);
$params = array('access_token' => $_SESSION['access_token']);
$response = $meli->get('items/' . $GET['ml_id'], $params );
if ($response['body']->error == '') 
{
	echo $response['body']->permalink;
    header('Location: ' . $response['body']->permalink);
    return;
}
include '../rodape.php';