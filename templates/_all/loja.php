<?php
$configuracoes_paginas_id = (int)Url::getURL(2);

if (!$configuracoes_paginas_id) {
  return;
}

$ConfiguracoesPaginas = ConfiguracoesPaginas::find($configuracoes_paginas_id);
if (!count($ConfiguracoesPaginas)) {
  return;
}

$STORE['TITULO_PAGINA'] = $ConfiguracoesPaginas->pagina . ' | ' . $STORE['TITULO_PAGINA'];
$STORE['image'] = URL_IMAGENS . 'assets/' . ASSETS . '/imgs/logo.gif';

/**
 * Verificar se é um dispositivo móvel que está sendo acessado
 */
if ($MobileDetect->isMobile() || $MobileDetect->isTablet()) {
  include dirname(__DIR__) . '/_layout/layout-header.php';
  include dirname(__DIR__) . '/_layout/layout-header-mobile-topo.php';
} else {
  include dirname(__DIR__) . '/_layout/layout-header.php';
  include sprintf('%stopo.php', URL_VIEWS_BASE);
}

if (!empty($POST['acao']) && $POST['acao'] == 'enviar') {

  if (
    empty($POST['nome']) || empty($POST['email']) ||
    empty($POST['telefone']) || empty($POST['assunto']) || empty($POST['mensagem'])
  ) {
    echo '<div class="alert alert-danger text-center">Campos com (*) são obrigatórios.</div>';
  } else if (!filter_var($POST['email'], FILTER_VALIDATE_EMAIL)) {
    echo '<div class="alert alert-danger text-center">Digite um e-mail válido.</div>';
  } else {
    $corpo = "<tr>
                    <td>
                        <b>Nome:</b> {$POST['nome']}<br/>
                        <b>E-mail:</b> {$POST['email']}<br/>
                        " . (!empty($POST['celular']) ? "<b>Telefone:</b> {$POST['Telefone']}<br/>" : '') . "
                        " . (!empty($POST['celular']) ? "<b>Celular:</b> {$POST['celular']}<br/>" : '') . "
                        " . (!empty($POST['cidade']) ? "<b>Cidade:</b> {$POST['cidade']}<br/>" : '') . "
                        " . (!empty($POST['assunto']) ? "<b>Assunto:</b> {$POST['assunto']}<br/>" : '') . "
                        " . (!empty($POST['mensagem']) ? "<b>Mensagem:</b><br/>" . nl2br($POST['mensagem']) : '') . "
                    </td>
                </tr>";

    $BODY_HTML = email_body($CONFIG, $corpo);
    $mail->addAddress($CONFIG['email_contato'], $CONFIG['nome_fantasia']);
    $mail->setFrom(strtolower($POST['email']), $POST['nome']);                       // Add a recipient
    $mail->Subject = $CONFIG['nome_fantasia'] . ' - Contato Via WebSite!';

    $mail->msgHTML($BODY_HTML);
    $mail->AltBody = 'Para ver a mensagem, use um visualizador de e-mail compatível com HTML!';

    if ($mail->send()) {
      echo '<div class="alert alert-success text-center">Sua mensagem foi enviada com sucesso.</div>';
    } else {
      echo '<div class="alert alert-danger text-center">Desculpe! Tivemos um problema ao enviar sua mensagem<br>Tente novamente!</div>';
    }

    $mail->clearAddresses();
    $mail->clearAttachments();

    $mail->SmtpClose();
  }
}
?>
<style>
  .container-flex {
    display: flex;
    flex-direction: row;
    align-items: center;
  }

  .container-flex>div {
    padding: 16px;
  }

  @media (max-width: 767px) {
    .container-flex {
      flex-direction: column;
    }
  }

  .form-contato label {
    display: block;
    margin-bottom: 5px;
  }

  .form-contato input {
    display: block;
    width: 100%;
  }

  .form-contato textarea {
    display: block;
    width: 100%;
    height: 175px;
  }
</style>
<?php
echo sprintf("%s", htmlspecialchars_decode($ConfiguracoesPaginas->descricao, HTML_SPECIALCHARS | ENT_QUOTES));


include sprintf('%srodape.php', URL_VIEWS_BASE);
