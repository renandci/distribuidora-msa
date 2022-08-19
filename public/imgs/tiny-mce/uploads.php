<?php
/*******************************************************
 * Only these origins will be allowed to upload images *
 ******************************************************/
$accepted_origins = array("http://www.leticiaenxovais.dev/imgs/diversas/uploads.php", "https://www.leticiaenxovais.com.br/imgs/diversas/uploads.php");

/*********************************************
 * Change this line to set the upload folder *
 *********************************************/
$imageFolder = '/';

$temp = current($_FILES);
  
if (is_uploaded_file($temp['tmp_name'])) 
{
//	if (isset($_SERVER['HTTP_ORIGIN'])) 
//    {
//        // same-origin requests won't set an origin. If the origin is set, it must be valid.
//        if (in_array($_SERVER['HTTP_ORIGIN'], $accepted_origins)) {
//          header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
//        } else {
//          header("HTTP/1.0 403 Origin Denied");
//          return;
//        }
//    }
    
    /*
      If your script needs to receive cookies, set images_upload_credentials : true in
      the configuration and enable the following two headers.
    */
    // header('Access-Control-Allow-Credentials: true');
    // header('P3P: CP="There is no P3P policy."');

    // Sanitize input
    if (preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $temp['name'])) {
        header("HTTP/1.0 500 Nome de arquivo inválido.");
        return;
    }

    // Verify extension
    if (!in_array(strtolower(pathinfo($temp['name'], PATHINFO_EXTENSION)), array("gif", "jpg", "png"))) {
        header('HTTP/1.0 500 Extensão inválida.');
        return;
    }
    
    switch ($temp['error'])
    {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            header('HTTP/1.0 500 Nenhum arquivo enviado.');
            break;
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            header('HTTP/1.0 500 Limite de tamanho de arquivo excedido.');
            break;
        default:
            header('HTTP/1.0 500 Erros desconhecidos.');
            break;
    }
    
    // Accept upload if there was no origin, or if it is an accepted origin
    $filetowrite = $imageFolder . $temp['name'];
    if( ! move_uploaded_file($temp['tmp_name'], $temp['name']) ){
        header('HTTP/1.0 500 Não foi possivel enviar a imagem.');
        return;
    }
    
    // Respond to the successful upload with JSON.
    // Use a location key to specify the path to the saved image resource.
    // { location : '/your/uploaded/image/file'}
    echo json_encode(array('location' => $filetowrite));
} else {
    // Notify editor that the upload failed
    header("HTTP/1.0 500 Server Error");
}
