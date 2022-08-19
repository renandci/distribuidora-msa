<?php
include '../topo.php';	
AcessoML($_SESSION, $PgAt);

$item = $meli->get('items/' . $GET['item'], $params); 

printf('<pre>%s</pre>', print_r(json_encode($item, JSON_PRETTY_PRINT), 1));

include '../rodape.php';