<?php
function removeacentos($str){
    return preg_replace( '/[`^~\'"]/', null, iconv( 'UTF-8', 'ASCII//TRANSLIT', $str ) );
}

include_once '../topo.php';

if(isset($GET['acao']) && $GET['acao'] == 'download')
{
	$arquivo = filter_input(INPUT_GET, 'arquivo');
	if($arquivo != '' and is_file($arquivo)) {	
		if ($excluir){
			unlink($arquivo);
		}
	}
}


$id_usuario = $_SESSION['admin']['id_usuario'];

$id_pedido = filter_input(INPUT_GET, 'id_pedido', FILTER_SANITIZE_NUMBER_INT);

$id_emitente = filter_input(INPUT_GET, 'id_emitente', FILTER_SANITIZE_NUMBER_INT);

$porc_nota = filter_input(INPUT_GET, 'porc_nota', FILTER_SANITIZE_NUMBER_INT);

$nfe_nrnota = filter_input(INPUT_GET, 'nfe_nrnota', FILTER_SANITIZE_NUMBER_INT);
$nfe_nrnota = $nfe_nrnota + 1;

$sql = 'UPDATE pedidos_enderecos AS PEDEND ' 
	 . 'INNER JOIN nfe_cidades AS NFECID ON (NFECID.nome = PEDEND.cidade) ' 
	 . 'SET PEDEND.id_cidade = NFECID.id ' 
	 . 'WHERE PEDEND.id_pedido=%u AND NFECID.uf = PEDEND.uf ';

$result = PedidosEnderecos::connection()->query(sprintf($sql, $id_pedido));

// Dados do Emitente
$NfeEmitentes = NfeEmitentes::find($id_emitente);

// Dados para o pedido do site
$PedidosVendas = PedidosVendas::all(['conditions'=> ['id_pedido=?', $id_pedido]]);

// Dados do pedido
$pedido = current($PedidosVendas)->pedido;

// Dados do cliente
$cliente = current($PedidosVendas)->pedido->cliente;

// Dados do endereco do cliente
$endereco = current($PedidosVendas)->pedido->pedido_endereco;

$txt = "NOTAFISCAL|1\r\n";
$txt .= "A|{$NfeEmitentes->versao}|||\r\n"; // grupo A - versão do leiaute e chave de acesso.

//Grupo B - Identificação da Nota    
$dhSaiEnt 	= '';
$cUF  		= $NfeEmitentes->cuf;
$cNF  		= str_pad((int)$pedido->codigo, 8, "0", STR_PAD_LEFT);
$tpNF 		= 1; 
$idDest 	= $endereco->uf == 'SP' ? 1 : 2;
$CFOP   	= $endereco->uf == 'SP' ? 5102 : 6102;
$cMunFG 	= $NfeEmitentes->cmunfg;
$tpImp 		= 1;
$cDV 		= '';
$tpAmb 		= $NfeEmitentes->tpamb;
$finNFe 	= 1;
$indFinal 	= 1; //normal, caso indicar operação com o consumidor final, colocar 1
$indPess 	= 2; //operação não presencial, pela internet
$procEmi 	= 0; //emissão de nf com o emissor do governo
$tpEmiss 	= 1; //emissão normal (não em Contigência);  
$indPag 	= 0;
$natOp 		= 'VENDA';
$mod 		= $NfeEmitentes->modelo; 
$serie 		= $NfeEmitentes->serie;
$nNF 		= $nfe_nrnota;
$dhEmi 		= $pedido->nrnfe == 0 ? date("c") : $pedido->dheminfe; 

// $txt .= "B|35|$cNF|$natOp|$indPag|$mod|$serie|$nNF|$dhEmi|$dhSaiEnt|$tpNF|$idDest|$cMunFG|$tpImp|$tpEmiss|$cDV|$tpAmb|$finNFe|$indFinal|$indPess|$procEmi||||\r\n";
$txt .= "B|$cUF|$cNF|$natOp|$mod|$serie|$nNF|$dhEmi|$dhSaiEnt|$tpNF|$idDest|$cMunFG|$tpImp|$tpEmiss|$cDV|$tpAmb|$finNFe|$indFinal|$indPess|$procEmi||||\r\n";

//Grupo C - Identificação do Emitente
$xNome 		= $NfeEmitentes->razaosocial; 
$xFant 		= $NfeEmitentes->nomefantasia;
$IE 		= $NfeEmitentes->inscest;
$IEST 		= '';
$IM 		= '';
$CNAE 		= '';
$CRT 		= (int)$NfeEmitentes->crt;
$cpfcnpj 	= soNumero($NfeEmitentes->cnpj);
$xLgr 		= $NfeEmitentes->endereco;
$nro 		= $NfeEmitentes->nro;
$xCPL 		= '';
$xBairro 	= $NfeEmitentes->bairro;
$xMun 		= removeacentos($NfeEmitentes->municipio);
$uf 		= $NfeEmitentes->uf;
$cep 		= soNumero($NfeEmitentes->cep);
$fone 		= soNumero($NfeEmitentes->telefone);

$txt .= "C|$xNome|$xFant|$IE|$IEST|$IM|$CNAE|$CRT|\r\n";

// informado porque é pessoa juridica, se fosse pessoa física, deveria informar como grupo C02a
$txt .= "C02|$cpfcnpj|\r\n"; 
$txt .= "C05|$xLgr|$nro|$xCPL|$xBairro|$cMunFG|$xMun|$uf|$cep|1058|Brasil|$fone|\r\n";


//Grupo E. Identificação do Destinatário da Nota Fiscal eletrônica
$xNomeCli 	= $cliente->nome;
$indIEDest	= 9; 
$IECli 		= ''; //inscrição estadual
$ISUFCli 	= ''; //inscrição SUFRAMA 
$IMCli 		= ''; //inscrição municipal do tomador do seviço
$email 		= $cliente->email; //e-mail de envio da nfe
$cpfcnpjCli = soNumero($cliente->cpfcnpj);

$xLgrCli 	= $endereco->endereco;
$nroCli 	= $endereco->numero;
$xCplCli 	= $endereco->complemento;
$xBairroCli = $endereco->bairro;
$cMunCli 	= $endereco->cod_ibge->cod_ibge;
$xMunCli 	= removeacentos($endereco->cidade);
$UFCli 		= $endereco->uf;
$CEPCli 	= soNumero($endereco->cep);
$foneCli 	= soNumero($cliente->telefone);

$txt .= "E|$xNomeCli|$indIEDest|$IECli|$ISUFCli|$IMCli|$email|\r\n";

if ( strlen($cpfcnpjCli) > 11){ 
	$txt .= "E02|$cpfcnpjCli|\r\n";
}
else{
	$txt .= "E03|$cpfcnpjCli|\r\n";
}

$txt .= "E05|$xLgrCli|$nroCli|$xCplCli|$xBairroCli|$cMunCli|$xMunCli|$UFCli|$CEPCli|1058|Brasil|$foneCli|\r\n";


//Geupo H. Detalhamento de Produtos e Serviços da NF-e
//Grupo I. Produtos e Serviços da NF-e
$i 			= 8;
$nItem 		= 1;
$vTotProd 	= 0.00;
$vTotDesc 	= 0.00;
$vNF 		= 0;
$TotProd	= count($PedidosVendas);


foreach ($PedidosVendas as $prod) 
{
	$ncm = NfeNcm::first(['select' => 'nfe_ncm.ncm as ncm_padrao, nfe_ncm.aliqnac as ncm_aliqnac', 'limit' => 1, 'conditions' => ['ncm=?', $rws->produto->ncm]]);
	
	$vUnCom = number_format( $prod->valor_pago * ( $porc_nota / 100 ), 2, '.', ''); //valor unitário
	$vProd 	= number_format($vUnCom * $prod->quantidade, 2, '.', '');//valor total bruto
	
	// valor unitário do desconto
	$vDesc 	= $pedido->desconto_boleto > 0 ? number_format((($vUnCom * $prod->quantidade) * ($pedido->desconto_boleto / 100.00)), 2, '.', '') : 0.00;
	
	$vTotProd = $vTotProd + $vProd;        
	$vTotDesc = $vTotDesc + $vDesc;
	
	$txt .= "H|$nItem||\r\n";
	
	$vFrete = $nItem == $TotProd && $pedido->frete_valor > 0 ? $pedido->frete_valor : '0.00';
	$cProd 	= CodProduto($prod->produto->nome_produto, $prod->produto->id);
	$xProd	= $prod->produto->nome_produto;
	$qCom  	= $prod->quantidade;
	
	$NCM  	= $prod->produto->ncm != '' ? $prod->produto->ncm : $ncm->ncm_padrao; 
	$indTot = 1;
	
//        $vProd = number_format($prod->['valor_pago'] * $prod->['quantidade'], 2, '.', '');
//        $vDesc = $pedido->desconto_boleto'] > 0 ? number_format((($prod->['valor_pago'] * $prod->['quantidade']) * ($pedido->desconto_boleto'] / 100.00)), 2, '.', '') : '';        
//        $vTotProd = $vTotProd + $vProd;        
//        $vTotDesc = $vTotDesc + $vDesc;        
			   // I|24214|SEM GTIN|Saída de Maternidade Puro Amor Penélope|61112000||||||6102|UN|1|139.90|139.90|SEM GTIN|UN|1|139.90|23.96||||1||||
	$txt .= "I|$cProd|SEM GTIN|$xProd|$NCM||||||$CFOP|UN|$qCom|$vUnCom|$vProd|SEM GTIN|UN|$qCom|$vUnCom|$vFrete||$vDesc||$indTot||||\r\n";
	$txt .= "M||\r\n";
	$txt .= "N|\r\n";
	$txt .= "N10d|0|102|\r\n";
	$txt .= "Q|\r\nQ05|99|0.00|\r\n";
	$txt .= "Q07|0.00|0.00|0.00|\r\n";
	$txt .= "S|\r\n";
	$txt .= "S05|99|0.00|\r\n";
	$txt .= "S07|0.00|0.00|\r\n";
	$nItem++;
	$i++;
}

$vTotProd 	= number_format($vTotProd, 2, '.', '');
$vTotDesc 	= number_format($vTotDesc, 2, '.', '');
$vFrete 	= number_format($vFrete, 2, '.', '');
$vNF 		= number_format(($vTotProd - $vTotDesc) + $vFrete, 2, '.', '');
$txt .= "W|\r\n"; 
// $txt .= "W02|0.00|0.00|0.00|0.00|0.00|$vTotProd|$vFrete|0.00|$vTotDesc|0.00|0.00|0.00|0.00|0.00|$vNF|0.00|\r\n";
// $txt .= "W02|0.00|0.00|0.00|0.00|0.00|0.00|0.00|0.00|$vTotProd|$vFrete|0.00|$vTotDesc|0.00|0.00|0.00|0.00|0.00|0.00|$vNF|0.00|\r\n";
$txt .= "W02|0.00|0.00|0.00|0.00|0.00|0.00|0.00|0.00|0.00|0.00|0.00|$vTotProd|$vFrete|0.00|$vTotDesc|0.00|0.00|0.00|0.00|0.00|0.00|$vNF|\r\n";
$txt .= "W04c|0.00|\r\n"; 
$txt .= "W04e|0.00|\r\n"; 
$txt .= "W04g|0.00|\r\n";

//Grupo X. Informações do Transporte da NF-e    
$modFrete = $pedido->frete_tipo == 'GRÁTIS' ? '0' : '1';
$txt .= "X|$modFrete|\r\n";
$i++;

// Grupo Y. Dados da Cobrança
// $nFat = $pedido->codigo_venda'];
// $vOrig = number_format($pedido->valor_compra'], 2, '.', '');
// $vDesc = $vTotDesc > 0 ? number_format($vTotDesc, 2, '.', '') : '';
// $vLiq = number_format(($vOrig - $vTotDesc), 2, '.', '');
// $txt .= "Y|\r\n"; 
// $txt .= "Y02|$nFat|$vOrig|$vDesc|$vLiq|\r\n";

$tPag 	= ( ! empty( $pedido->tipo_cartao ) && ( $pedido->forma_pagamento != 'Boleto' && $pedido->forma_pagamento != 'Transferência' ) ? 03 : ( $pedido->forma_pagamento == 'Boleto' ? 15 : 99 ) ); 
$vPag 	= $vNF;
$card 	= ! empty( $pedido->tipo_cartao ) && ( $pedido->forma_pagamento != 'Boleto' && $pedido->forma_pagamento != 'Transferência' ) ? strtolower( $pedido->tipo_cartao ) : '';
$CNPJ 	= null;
$tBand 	= ! empty( $pedido->tipo_cartao ) && ( $pedido->forma_pagamento != 'Boleto' && $pedido->forma_pagamento != 'Transferência' ) ? strtolower( $pedido->tipo_cartao ) : '';
$cAut 	= null;

// $txt .= "YA\r\n";
// $txt .= "YA01|1|$tPag|$vPag||1|$CNPJ|$tBand|$cAut\r\n";

$txt .= "YA|1|$tPag|$vPag||1|$CNPJ|$tBand|$cAut\r\n";

try{
	$arquivo = '../nfe/txt/' . $pedido->codigo . '.txt';
	$fp = fopen($arquivo, 'w');
	if ($fp == false) {
		header( 'location: /adm/vendas/vendas.php?id=' . $id_pedido . '&error_nfe=Não foi possível criar o arquivo!' );
		return;
	}
	fwrite($fp, iconv("UTF-8", "WINDOWS-1252", $txt));
	fclose($fp);
	
	if ( !empty($arquivo) && is_file($arquivo) ) {
		echo $nfe_nrnota;
		$Pedidos = Pedidos::find($id_pedido);
		$Pedidos->nrnfe = $nfe_nrnota;
		$Pedidos->dheminfe = $dhEmi;
		$Pedidos->porc_nota = $porc_nota;
		$Pedidos->save_log();
		
		$NfeEmitentes = NfeEmitentes::find($id_emitente);
		$NfeEmitentes->nrnfe = $nfe_nrnota;
		$NfeEmitentes->save_log();
		
		header('location: /adm/download.php?arquivo=./nfe/txt/' . $pedido->codigo . '.txt');
		return;
    }
	
} catch(\Exception $e) {
	echo '<pre>';
	print_r($e);
	echo '</pre>';
}

include_once '../rodape.php';