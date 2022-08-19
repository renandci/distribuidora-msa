<?php
include '../app/settings.php';
include '../app/vendor/autoload.php';
include '../app/settings-config.php';
include '../app/includes/bibli-funcoes.php';

//usuário => senha
$data = array(
    'admin' => 'EnviarBoleto', 
    'pass' => sha1('boletos@+123')
);
$data_atual = date('Y-m-d H:i:s');
$vencimento = ! empty($POST['vencimento']) ? converterDatas( $POST['vencimento'] ) : date('Y-m-01');
$created_at = date('Y-m-d 00:00:00', strtotime($vencimento));
$updated_at = date('Y-m-t 23:59:59', strtotime($vencimento));
$strtotime = strtotime($updated_at) - strtotime($data_atual);
$dias = floor( $strtotime / ( 60 * 60 * 24 ) );

if( empty( $POST['acao'] ) && $POST['acao'] != 'EnviarBoleto' ) {
    echo 'Você não tem permissão no momento!';
    return;
}

if( $POST['acao'] != $data['admin'] && $POST['pass'] != $data['pass'] ) {
    echo 'Você não tem permissão no momento!';
    return;
}

/**
 * Verificar se for edição do Boleto
 */
if( isset( $GET['id'] ) && $GET['id'] > 0 ) {
    $LojasPgto = LojasPgto::find( $GET['id'] );
} 
else {
    $LojasPgto = new LojasPgto();
}

$LojasPgto->lojas_id = (INT)$GET['lojas_id'];
$LojasPgto->formapgto = 'Boleto';
$LojasPgto->vencimento = $vencimento;
$LojasPgto->mes_inicial = $created_at;
$LojasPgto->mes_final = $updated_at;
if( ! empty($POST['vencimento']) ) {
    $LojasPgto->vencimento = converterDatas( $POST['vencimento'] );
}
try {
    $LojasPgto->save();
    $mensagem = 'Dados salvo com sucesso!<br/>';
} catch (Exception $ex) {
    $mensagem = 'Não foi possivel salvar os dados no momento!<br/>';
}


/*******************************************************
 * Only these origins will be allowed to upload images *
 ******************************************************/
$accepted_origins = array( URL_BASE . 'adm/lojas-boleto.php');

/*********************************************
 * Change this line to set the upload folder *
 *********************************************/
$dir_boleto = 'boletos/';

$temp = current($_FILES);
  
if (is_uploaded_file($temp['tmp_name'])) 
{
    // Sanitize input
    if (preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $temp['name'])) {
        header("HTTP/1.0 500 Nome de arquivo inválido.");
        return;
    }

    // Verify extension
    if ( ! in_array( strtolower( pathinfo($temp['name'], PATHINFO_EXTENSION) ), array("pdf") ) ) {
        header("HTTP/1.0 500 Extensão inválida.");
        return;
    }
    
    switch ($temp['error'])
    {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            header("HTTP/1.0 500 Nenhum arquivo enviado.");
            break;
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            header("HTTP/1.0 500 Limite de tamanho de arquivo excedido.");
            break;
        default:
            header("HTTP/1.0 500 Erros desconhecidos.");
            break;
    }
    
    // Accept upload if there was no origin, or if it is an accepted origin
    $filetowrite = $dir_boleto 
                . converter_texto($LojasPgto->formapgto) 
                . '_' 
                . md5($LojasPgto->lojas_id . '_' . $LojasPgto->id )
                . '.' 
                . strtolower( pathinfo($temp['name'], PATHINFO_EXTENSION) );
    
    if( ! move_uploaded_file($temp['tmp_name'], $filetowrite) ){
        header("HTTP/1.0 500 Não foi possivel enviar o arquivo.");
        return;
    }
    $mensagem .= 'Boleto enviado com sucesso!';

}
echo "<p class='text-center'>{$mensagem}</p>";
if (file_exists($filetowrite)) {
    echo ''
        . '<p class="text-center mt5">'
            . '<a href="' . URL_BASE . 'adm/'.$filetowrite.'" target="_blank" class="btn btn-secundary">'
                . 'ver boleto'
            . '</a> '
            . '<a href="/adm/lojas.php?acao=' . $GET['acao'] . '&lojas_id=' . $GET['lojas_id'] . '&id=' . $LojasPgto->id . '&Acao=BoletoExcluir&Boleto='.$filetowrite.'" class="btn btn-danger">'
                . 'remover boleto'
            . '</a> '
            . '<a href="/adm/lojas.php?acao=' . $GET['acao'] . '&Acao=BoletoEnviarEmail&lojas_id=' . $GET['lojas_id'] . '&id=' . $LojasPgto->id . '" class="btn btn-info">'
                . 'enviar um e-mail'
            . '</a>'
        . '</p>';
}
return;