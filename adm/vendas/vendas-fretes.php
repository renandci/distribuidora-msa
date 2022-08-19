<?php
include '../topo.php';

$acao 	 	 = filter_input(INPUT_GET, 'acao');
$altura  	 = filter_input(INPUT_GET, 'altura');
$largura 	 = filter_input(INPUT_GET, 'largura');
$comprimento = filter_input(INPUT_GET, 'comprimento');
$peso 		 = filter_input(INPUT_GET, 'peso');
$cep 		 = filter_input(INPUT_GET, 'cep');

$FRETE = [];

$Correios = calcular_preco_frete($STORE['config']['correios'], $CONFIG['cep'], $cep, $peso, $altura, $largura, $comprimento);
$JadLog = calcular_fretejadlog($peso, $CONFIG['cep'], $cep, $STORE['config']['jadlog']);

$FRETE = ($Correios + $JadLog);

// printf('<pre>%s</pre>', print_r($CONFIG, true));
printf('<pre id="frete-pre">%s</pre>', json_encode($FRETE));

include '../rodape.php';