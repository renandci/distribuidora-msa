<?php
use NFePHP\NFe\Tools;
use NFePHP\Common\Certificate;


define('PATH_ROOT', realpath($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR);

$ChaveNfe = filter_input(INPUT_GET, 'chavenfe', FILTER_SANITIZE_STRING); 

include PATH_ROOT . '/adm/topo.php';

try {

    if(strlen($ChaveNfe) == 0 ) throw new Exception('Digite a chave ?chavenfe=?');

    $NfeNotas = NfeNotas::find(['conditions' => ['chavenfe=?', $ChaveNfe]]);

    print_r($NfeNotas);
    return;
    $NfeEmitentes = NfeEmitentes::first(['conditions' => ['id=?', $NfeNotas->id_emitente]]);

    // $Certificate = file_get_contents(sprintf('%sassets/%s/pfx/pfx-%u.pfx', PATH_ROOT, ASSETS, $NfeNotas->id_emitente));

	// // O conteúdo do XML assinado fica armazenado na variável $xml
	// $Tools = new Tools($NfeEmitentes->jsonnfe(), Certificate::readPfx($Certificate, $NfeEmitentes->senha));

    // //só funciona para o modelo 55
    // $Tools->model('55');
    // //este serviço somente opera em ambiente de produção
    // $Tools->tools->setEnvironment(1);
    
    // $response = $Tools->sefazDownload($ChaveNfe);
 
    // $Standardize = new Standardize($response);
    // $std = $Standardize->toStd();
    // if ($std->cStat != 138) {
    //     echo "Documento não retornado. [$std->cStat] $std->xMotivo";  
    //     die;
    // }    
    // $zip = $std->loteDistDFeInt->docZip;
    // $xml = gzdecode(base64_decode($zip));

    // // header('Content-type: text/xml; charset=UTF-8');
    // echo $xml;

    // echo $response;
    
} catch (\Exception $e) {
    print_r($e);
    echo str_replace("\n", "<br/>", $e->getMessage());
}


include PATH_ROOT . '/adm/rodape.php';


