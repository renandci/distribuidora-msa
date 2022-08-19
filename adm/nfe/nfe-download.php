<?php
define('PATH_ROOT', realpath($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR);
require_once PATH_ROOT . '/app/settings.php';

$file = filter_input(INPUT_GET, 'f', FILTER_SANITIZE_STRING); 

$file_test = sprintf('%sassets/%s/xml/%s', PATH_ROOT, ASSETS, $file);

if( file_exists( $file_test ) ) {
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    $basename = pathinfo($file, PATHINFO_BASENAME);
    $filesize = filesize($file_test);
    
    if( $ext != 'xml' ) exit("Não podemos baixar esse arquivo.");
    
    header("Content-type: application/{$ext}");
    header("Content-length: {$filesize}");
    header("Content-Disposition: attachment; filename=\"$basename\"");
    readfile($file_test);
    exit(0);
}

include_once PATH_ROOT . '/adm/topo.php';
?>
    <div class="row">
        <div class="col-sm-6 col-sm-offset-3 text-center">
            <div class="alert alert-danger ft18px text-center">
                <strong>Atenção:</strong> Você deve enviar o xml para o sefaz
            </div>
            <a class="btn btn-success block-center" href="/adm/nfe/nfe-emitir.php">clique aqui para continuar</a>
        </div>
    </div>
<?php
include_once PATH_ROOT . '/adm/rodape.php';


