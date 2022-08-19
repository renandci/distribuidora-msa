<?php

use NFePHP\NFe\Tools;
use NFePHP\Common\Certificate;
use NFePHP\Common\Soap\SoapCurl;
use NFePHP\NFe\Complements;
use NFePHP\NFe\Common\Standardize;

include_once dirname(__DIR__) . '/topo.php';

$id_lote    = filter_input(INPUT_GET, 'id_lote');
$id_usuario = $_SESSION['admin']['id_usuario'];
$dir = sprintf('%sassets/%s/xml/', PATH_ROOT, ASSETS);
$filename = '%s%s.xml';

try {
	$Protocolos = file_get_contents(sprintf($filename, $dir, "{$id_lote}-consultas"));

	$stdCl = new Standardize($Protocolos);
	$std = $stdCl->toStd();

	$std->protNFe = !is_array($std->protNFe) ? [$std->protNFe] : $std->protNFe;

	if ($std->cStat == '103' || $std->cStat == '105') {
		$NfeNotas = NfeNotas::first(['conditions' => ['id_lote=?', $id_lote]]);
		$NfeEmitentes = NfeEmitentes::first(['conditions' => ['id=?', $NfeNotas->id_emitentes]]);
		$NrRec = $NfeNotas->nrrec;

		$Certificate = file_get_contents(sprintf('%sassets/%s/pfx/pfx-%u.pfx', PATH_ROOT, ASSETS, $NfeEmitentes->id));
		$Soap = new SoapCurl();
		$Soap->httpVersion('1.1');

		$Tools = new Tools($NfeEmitentes->jsonnfe(), Certificate::readPfx($Certificate, $NfeEmitentes->senha));
		$Tools->loadSoapClass($Soap);

		$SefazConsultaRecibo = $Tools->sefazConsultaRecibo($NrRec);

		$filename_consultas = sprintf($filename, $dir, "{$id_lote}-consultas");
		if (!file_put_contents($filename_consultas, $SefazConsultaRecibo))
			throw new Exception("Não foi possível criar o xml da consulta", 1);
		chmod($filename_consultas, 0777);

		echo '<h4>Processando dados, aguarde...</h4>';
		header(sprintf('Refresh: 3; URL=/adm/nfe/nfe-finalizar.php?id_lote=', $id_lote));
		return;
	}

	foreach ($std->protNFe as $rws) {
		$Assinados = file_get_contents(sprintf($filename, $dir, "{$rws->infProt->chNFe}-assinada"));
		$Complements = Complements::toAuthorize($Assinados, $Protocolos);
		$autorizadas = sprintf($filename, $dir, "{$rws->infProt->chNFe}-autorizada");
		file_put_contents($autorizadas, $Complements);
		chmod($autorizadas, 0777);

		$NfeNotas = NfeNotas::find(['conditions' => ['chavenfe=?', $rws->infProt->chNFe]]);
		$NfeNotas->id = $NfeNotas->id;
		$NfeNotas->motivo = !empty($rws->infProt->cStat) && $rws->infProt->cStat != 100 ? $rws->infProt->xMotivo : null;
		$NfeNotas->nrprot = !empty($rws->infProt->nProt) ? $rws->infProt->nProt : null;
		$NfeNotas->save_log();
	}

	// if( is_array($std->protNFe) ) {
	// 	foreach($std->protNFe as $rws) {
	// 		$Assinados = file_get_contents(sprintf($filename, $dir, "{$rws->infProt->chNFe}-assinada"));
	// 		$Complements = Complements::toAuthorize($Assinados, $Protocolos);
	// 		$autorizadas = sprintf($filename, $dir, "{$rws->infProt->chNFe}-autorizada");
	// 		file_put_contents($autorizadas, $Complements);	
	// 		chmod($autorizadas, 0777);

	// 		$NfeNotas = NfeNotas::find(['conditions' => ['chavenfe=?', $rws->infProt->chNFe]]);
	// 		$NfeNotas->id = $NfeNotas->id;
	// 		$NfeNotas->motivo = !empty($rws->infProt->cStat) && $rws->infProt->cStat != 100 ? $rws->infProt->xMotivo : null;
	// 		$NfeNotas->nrprot = !empty($rws->infProt->nProt) ? $rws->infProt->nProt : null;
	// 		$NfeNotas->save_log();
	// 	}
	// } 
	// else {
	// 	$Assinados = file_get_contents(sprintf($filename, $dir, "{$std->protNFe->infProt->chNFe}-assinada"));
	// 	$Complements = Complements::toAuthorize($Assinados, $Protocolos);
	// 	$autorizadas = sprintf($filename, $dir, "{$std->protNFe->infProt->chNFe}-autorizada");
	// 	file_put_contents($autorizadas, $Complements);	
	// 	chmod($autorizadas, 0777);

	// 	$NfeNotas = NfeNotas::find(['conditions' => ['chavenfe=?', $std->protNFe->infProt->chNFe]]);
	// 	$NfeNotas->id = $NfeNotas->id;
	// 	$NfeNotas->motivo = $std->protNFe->infProt->cStat != 100 ? $std->protNFe->infProt->xMotivo : null;
	// 	$NfeNotas->nrprot = !empty($std->protNFe->infProt->nProt) ? $std->protNFe->infProt->nProt : null;
	// 	$NfeNotas->save_log();
	// }
	header('location: /adm/nfe/nfe-emitir.php?error=Finalizadas com sucesso.');
	return;
} catch (\Exception $e) {

	// NfeNotas::update_all([
	// 	'conditions' => ['id_lote=?', $id_lote],
	// 	'set' => sprintf('motivo="%s"', $e->getMessage()),
	// ]);

	// header(sprintf('location: /adm/nfe/nfe-emitir.php?id_lote=%s&error=%s&acao=lista_nfes', $id_lote, "Não foi possivel autorizar a nfes"));
	// return;
}

include_once dirname(__DIR__) . '/rodape.php';
