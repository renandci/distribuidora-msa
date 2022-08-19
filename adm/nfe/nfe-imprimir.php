<?php
use NFePHP\DA\NFe\Danfe;
use NFePHP\DA\Legacy\FilesFolders;

define('PATH_ROOT', realpath($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR);
define('URL_BASE_HTTPS', 'https://' . $_SERVER['SERVER_NAME'] . '/' );

require_once PATH_ROOT . '/app/settings.php';
require_once PATH_ROOT . '/app/vendor/autoload.php';
require_once PATH_ROOT . '/app/settings-config.php';
// require_once PATH_ROOT . '/app/php-sigep/PhpSigepFPDF/fpdf.php';
// require_once PATH_ROOT . '/app/php-sigep/PhpSigepFPDF/fpdi.php';


$id_usuario = $_SESSION['admin']['id_usuario'];
$id_pedido = filter_input(INPUT_GET, 'id_pedido', FILTER_SANITIZE_NUMBER_INT);
$id_emitentes = filter_input(INPUT_GET, 'id_emitente', FILTER_SANITIZE_NUMBER_INT);
$id_nota = filter_input(INPUT_GET, 'id_nota', FILTER_SANITIZE_NUMBER_INT);

// use somente imagens JPEG
$pathLogo = sprintf('%s/assets/%s/imgs/%s', PATH_ROOT, ASSETS, str_replace('.png', '.jpg', $CONFIG['logo_desktop']));

$nfe = NfeNotas::find($id_nota);

// NOTA:
// Sempre as datas created_aat com as do $ano e $mes, que se encontra em emitir
$ano = $nfe->created_at->format('Y');
$mes = $nfe->created_at->format('m');

$dir = sprintf('%sassets/%s/xml/', PATH_ROOT, ASSETS);
$filename = '%s%s.xml';

$a = sprintf($filename, $dir, "{$nfe->chavenfe}-assinada");
if(file_exists($a)) $xml = $a;

$b = sprintf($filename, $dir, "{$nfe->chavenfe}-autorizada");
if(file_exists($b)) $xml = $b;

$c = sprintf($filename, $dir, "{$nfe->chavenfe}-cancelada");
if(file_exists($c)) $xml = $c;

try {   
    $docxml = FilesFolders::readFile($xml);
    $danfe = new Danfe($docxml, 'P', 'A4', $pathLogo, 'I', '');
    $danfe->montaDANFE();
    $pdf = $danfe->render();
    
} catch (InvalidArgumentException $e) {
    echo 'Ocorreu um erro durante o processamento: ' . $e->getMessage();
}

header('Content-Type: application/pdf');
echo $pdf;
return;