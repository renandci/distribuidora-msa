<?php

use NFePHP\NFe\Tools;
use NFePHP\Common\Certificate;
use NFePHP\Common\Soap\SoapCurl;
use NFePHP\NFe\Common\Standardize;

include_once dirname(__DIR__) . '/topo.php';

$id_lote = substr(str_pad(date('YmdHi0s'), 15, '0', STR_PAD_BOTH), 0, 15);
$id_nfes = filter_input(INPUT_POST, 'idnfes', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
$id_usuario = $_SESSION['admin']['id_usuario'];

$dir = sprintf('%sassets/%s/xml/', PATH_ROOT, ASSETS);
$filename_assinada = '%s%s-assinada.xml';
$filename_protocolo = '%s%s-protocolo.xml';

try {
    $NfesXmls = [];

    if (empty($id_nfes))
        throw new Exception('Selecione as Nfe-s');

    $NfeNotas = NfeNotas::all(['conditions' => ['id in(?)', $id_nfes], 'order' => 'id asc']);

    $NfeEmitentes = $NfeNotas[0]->emitente;

    $Certificate = file_get_contents(sprintf('%sassets/%s/pfx/pfx-%u.pfx', PATH_ROOT, ASSETS, $NfeEmitentes->id));

    $Soap = new SoapCurl();
    $Soap->httpVersion('1.1');

    $Tools = new Tools($NfeEmitentes->jsonnfe(), Certificate::readPfx($Certificate, $NfeEmitentes->senha));
    $Tools->loadSoapClass($Soap);

    if (count($NfeNotas) > 0) {
        foreach ($NfeNotas as $rws) {
            $test = sprintf($filename_assinada, $dir, $rws->chavenfe);
            // Verifica a existencia dos xmls
            if (file_exists($test)) {
                $NfesXmls[] = file_get_contents($test);
            }
            $id_lote = !empty($rws->id_lote) ? $rws->id_lote : $id_lote;
        }
    }

    if (count($NfesXmls) == 0)
        throw new Exception('Não há xmls para enviar');

    $SefazEnviaLote = $Tools->sefazEnviaLote($NfesXmls, $id_lote);

    $st = new Standardize($SefazEnviaLote);
    $std = $st->toStd();

    $recibo = $std->infRec->nRec;
    $xMotivo = $std->infRec->xMotivo;
    $cStat = $std->cStat;

    foreach ($NfeNotas as $rws) {
        $protocolos = sprintf($filename_protocolo, $dir, $rws->chavenfe);
        file_put_contents($protocolos, $SefazEnviaLote);
        chmod($protocolos, 0777);

        NfeNotas::new_save([
            'id' => $rws->id,
            'id_lote' => $id_lote,
            'nrrec' => $recibo,
            'motivo' => !empty($cStat) && $cStat != 103 ? $xMotivo : null,
        ]);
    }
    header(sprintf('location: /adm/nfe/nfe-consultar.php?id_lote=%s&id_notas=%s&acao=lista_nfes', $id_lote, implode(',', $id_nfes)));
    return;
} catch (Exception $e) {

    NfeNotas::update_all([
        'conditions' => ['id_lote=?', $id_lote],
        'set' => sprintf('motivo="%s"', $e->getMessage()),
    ]);

    header(sprintf('location: /adm/nfe/nfe-emitir.php?id_lote=%s&error=Não foi possivel assinar os xmls&acao=lista_nfes', $id_lote));
    return;
}

include_once dirname(__DIR__) . '/rodape.php';
