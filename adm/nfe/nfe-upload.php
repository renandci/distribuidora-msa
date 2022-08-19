<?php
use NFePHP\NFe\Tools;
use NFePHP\Common\Certificate;
use NFePHP\Common\Soap\SoapCurl;
use NFePHP\NFe\Common\Standardize;
include_once dirname(__DIR__) . '/topo.php';


/* formatos de xml permitidos */
$permitidos = array(".xml", ".XML");
$dir = sprintf('%sassets/%s/xml/', PATH_ROOT, ASSETS);
$id = (int)filter_input(INPUT_POST, 'id');

try {
    
    if(!isset($_POST)) {
        throw new Exception('Selecione o xml XML', 3);
    }

    $NfeNotas = NfeNotas::find($id);
    $NfeEmitentes = $NfeNotas->emitente;

    $Soap = new SoapCurl();
    $Soap->httpVersion('1.1');
    
    $Certificate = file_get_contents(sprintf('%sassets/%s/pfx/pfx-%u.pfx', PATH_ROOT, ASSETS, $id_emitente));
    $Tools = new Tools($NfeEmitentes->jsonnfe(), Certificate::readPfx($Certificate, $NfeEmitentes->senha));
    $Tools->loadSoapClass($Soap);
    
    $nome_xml = $_FILES['xml']['name'];
    
    $tamanho_xml = $_FILES['xml']['size'];
    
    // nome que dará a xml
    $tmp = $_FILES['xml']['tmp_name'];
    
    $filename = sprintf('%s%s.xml', $dir, $NfeNotas->chavenfe);
    $filename_assinado = sprintf('%s%s-assinada.xml', $dir, $NfeNotas->chavenfe);

    /* pega a extensão do arquivo */
    $ext = strtolower(strrchr($nome_xml, '.'));

    /*  verifica se a extensão está entre as extensões permitidas */
    if(!in_array($ext, $permitidos)) {
        throw new Exception('Somente são aceitos arquivos do tipo XML', 3);
    }
    
    /* converte o tamanho para KB */
    $tamanho = round($tamanho_xml / 1024);

    // se xml for até 1MB envia
    if($tamanho > 1024) { 
        throw new Exception('A xml deve ser de no máximo 1MB', 3);
    }

    // Tenta enviar o xml
    if(!move_uploaded_file($tmp, $filename)) {
        throw new Exception('Não foi possivel enviar o xml, tente novamente!', 3);
    }
            
    // O conteúdo do XML assinado fica armazenado na variável $xml
    // Vamos tentar assinar novamente
    $NewXml = file_get_contents($filename);
    $SignNFe = $Tools->signNFe($NewXml);

    // $std irá conter uma representação em stdClass do XML retornado
    $stdCl = new Standardize($SignNFe);
    $std = $stdCl->toStd();

    $NewChave = $std->infNFe->Id;
    $dhemi = $std->infNFe->ide->dhEmi;
    
    // Gerar o XML da NFe
    if( ! file_put_contents($filename_assinado, $SignNFe) ) {
        throw new Exception('Não foi possível cria os xmls', 3);
    } 

    chmod($filename_assinado, 0775);

    $data['NfeNotas'] = [ $id => [ 'motivo' => '', 'dhemi' => $dhemi ] ];
    NfeNotas::action_cadastrar_editar($data, 'alterar', 'chavenfe');
    
    header('location: /adm/nfe/nfe-emitir.php?acao=lista_nfes&error=XML Enviado e Assinado com sucesso');
    return;

} 
catch (\Exception $e) {
    header(sprintf('location: /adm/nfe/nfe-emitir.php?error=%s&acao=lista_nfes', $e->getMessage()));
    return;
}
include_once dirname(__DIR__) . '/rodape.php';