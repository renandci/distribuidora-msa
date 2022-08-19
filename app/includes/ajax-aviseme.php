<?php

/**
 * @author Renan Henrique <renan@dcisuporte.com.br>
 * @company Data Control Infomatica
 * @return Script para aviseme e envio automatico do email, caso o produto requisitado esteja em estoque
 */

/**
 * @return Funcao ira automatizar todas as resquisicao do produtos_aviseme
 */
function FunctionEmailAviseMe()
{
  global $mail;
  global $CONFIG;

  /*
     * Select do produto que o cliente requisitou
     */
  $sql = ''
    . 'select '
    . 'p.nome_produto, '
    . 'img.imagem, '
    . 'c.nomecor, '
    . 't.nometamanho, '
    . 'pmail.id, '
    . 'pmail.produtos_id, '
    . 'pmail.nome, '
    . 'pmail.email '
    . 'from produtos p '
    . 'join produtos_imagens img on img.codigo_id = p.codigo_id '
    . 'join cores c on c.id = p.id_cor '
    . 'join tamanhos t on t.id = p.id_tamanho '
    . 'join produtos_aviseme pmail on pmail.produtos_id = p.id '
    . 'where p.id in(select produtos_id from produtos_aviseme) and img.cor_id = p.id_cor and img.capa = 1 and p.estoque > 0 limit 5';

  $result = Produtos::find_by_sql($sql);

  if (!count($result))
    return false;

  foreach ($result as $rs) {
    $rs = $rs->to_array();
    $corpo = ''
      . '<tr>'
      . '<td align="center">'
      . '<h3>Olá <b>' . $rs['nome'] . '</b></h3><hr/>'
      . '<p>'
      . 'Seu produto chegou em nosso estoque,<br/>'
      . 'Corra e garanta já o seu!'
      . '</p>'
      . '</td>'
      . '</tr>'

      . '<tr>'
      . '<td align="center">'
      . '<table width="100%"><tr>'
      . '<td rowspan="rowspan" width="105px">'
      . sprintf('<img src="%s" width="105px" height="105px">', Imgs::src($rs['imagem'], 'smalls'))
      . '</td>'
      . '<td>'
      . '<h3>' . $rs['nome_produto'] . '</h3>';
    $corpo .= $rs['nomecor'] ? '<br/>COR: ' . $rs['nomecor'] : '';
    $corpo .= $rs['nometamanho'] ? '<br/>TAM: ' . $rs['nometamanho'] : '';
    $corpo .= ''
      . '</td>'
      . '<tr>'
      . '<td colspan="2" align="center">'
      . '<a href="' . URL_BASE . 'produto/' . converter_texto($rs['nome_produto']) . '/' . $rs['produtos_id'] . '" target="_blank" class="btn btn-paga-novamente">COMPRAR AGORA</a>'
      . '</td>'
      . '</tr>'
      . '</tr></table>';

    $BODY_HTML = email_body($CONFIG, $corpo);

    $mail->setFrom($CONFIG['email_contato'], $CONFIG['nome_fantasia']);
    $mail->addAddress(strtolower($rs['email']), $rs['nome']);                       // Add a recipient
    $mail->Subject = $CONFIG['nome_fantasia'] . ' - Seu produto voltou!';

    $mail->msgHTML($BODY_HTML);
    $mail->AltBody = 'Para ver a mensagem, use um visualizador de e-mail compatível com HTML!';

    if ($mail->send()) {
      ProdutosAviseMe::delete_all(array('conditions' => array('id=?', $rs['id'])));
    } else {
      ProdutosAviseMe::delete_all(array('conditions' => array('id=?', $rs['id'])));
    }

    $mail->clearAddresses();
    $mail->clearAttachments();
  }
  $mail->SmtpClose();
}

$ACTION = count($POST) > 0 ? $POST : $GET;

switch ($ACTION['acao']) {
  case 'InfoAviseMe':

    $Produtos = Produtos::find((int)$GET['produto_id']);

    $str['html'] = ''
      . '<div class="clearfix">'
      . '<div class="row">'
      . '<div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">'
      . '<img src="' . Imgs::src($Produtos->capa->imagem, 'smalls') . '" width="275px" class="img-responsive">'
      . '</div>'
      . '<div class="col-lg-7 col-md-7 col-sm-6 col-xs-12">'
      . '<h3 class="show mb5 text-center">' . $Produtos->nome_produto . '</h3>';
    $str['html'] .= $Produtos->nomecor
      ? '<span class="show">' . ($Produtos->nomecor ? "{$Produtos->cor->opcoes->tipo}: " : '') . $Produtos->nomecor . '</span>' : '';
    $str['html'] .= $Produtos->nometamanho
      ? '<span class="show">' . ($Produtos->nometamanho ? "{$Produtos->tamanho->opcoes->tipo}: " : '') . $Produtos->nometamanho . '</span>' : '';
    $str['html'] .= ''
      . '</div>'
      . '</div>'
      . '</div>';

    exit(json_encode($str));
    break;

  case 'AviseMeInit':

    // FunctionEmailAviseMe();
    $rs = null;
    if (ctype_digit($GET['produto_id']) && $GET['produto_id'] > 0) {
      $rs = Produtos::find((int)$GET['produto_id']);
    }

    $str['estoque'] = true;
    if (($rs->estoque === '0' || $rs->estoque <= '0')) {
      $str['estoque'] = false;
    }

    exit(json_encode($str));

    break;

  case 'AviseMeCadastro':
  case 'SolicitaOrcamento':

    $HTML_SUCESSO = ''
      . '<h2>Obrigado!</h2>'
      . '<p class="ft16px">'
      . '<b>' . $POST['nome'] . '</b><br/>'
      . 'Você irá receber um e-mail no seu ' . $POST['email'] . ', assim que nosso produto chegar ao estoque!'
      . '<br/>Continue comprando!'
      . '</p>';

    $HTML_SUCESSO_2 = ''
      . '<h2>Obrigado!</h2>'
      . '<p class="ft16px">'
      . '<b>' . $POST['nome'] . '</b><br/>'
      . 'Entraremos em contato o mais breve possível, seu ' . $POST['email'] . ' foi cadastrado em nosso sistema com sucesso!'
      . '<br/>Continue visitando nosso site, e fique por dentro das novidades!'
      . '</p>';

    $HTML_ERROR = '<p>Não foi possível cadastrar seu ' . $POST['email'] . '. Tente novamente!</p>';

    if (!$POST['nome'] || !$POST['email']) {
      $str['error'] = true;
      $str['mensagem'] = $HTML_ERROR;
    }

    if ($ACTION['acao'] == 'SolicitaOrcamento') {
      $Produtos = Produtos::find($POST['produtos_id']);
      $BODY_HTML = ""
        . "<tr>"
        . "<td>"
        . "<b>Olá {$CONFIG['nome_fantasia']}</b><br/>"
        . "Estou entrando em contato para solicitar o orçamento do produto {$Produtos->nome_produto}<br/>"
        . "<i>Nome:</i> <b>{$POST['nome']}</b><br/>"
        . "<i>E-mail:</i> <b>{$POST['email']}</b><br/>"
        . ($POST['telefone'] ? "<i>Nome:</i> <b>{$POST['telefone']}</b><br/>" : '')
        . ($POST['cidade'] ? "<i>Cidade:</i> <b>{$POST['cidade']}</b><br/>" : '')
        . ($POST['mensagem'] ? "<i>Mensagem:</i><br/>{$POST['mensagem']}" : '')
        . "</td>"
        . "</tr>";

      $BODY_HTML = email_body($CONFIG, $BODY_HTML);

      $mail->setFrom(strtolower($POST['email']), $POST['nome']);
      $mail->addAddress($CONFIG['email_contato'], $CONFIG['nome_fantasia']); // Add a recipient
      $mail->Subject = $CONFIG['nome_fantasia'] . ' - Orçamento de produtos!';

      $mail->msgHTML($BODY_HTML);
      $mail->AltBody = 'Para ver a mensagem, use um visualizador de e-mail compatível com HTML!';

      $mail->send();
      $mail->clearAddresses();
      $mail->clearAttachments();
      $mail->SmtpClose();

      $str['error'] = false;
      $str['mensagem'] = $HTML_SUCESSO_2;

      exit(json_encode($str));
    }

    /**
     * Verificar se há cadastro para a resuisicao
     */
    $ProdutosAviseMeCount = ProdutosAviseMe::count(['conditions' => ['produtos_id=? and nome=? and email =?', $POST['produtos_id'], $POST['nome'], $POST['email']]]);
    if ($ProdutosAviseMeCount == 0) {
      $ProdutosAviseMe = new ProdutosAviseMe();
      $ProdutosAviseMe->produtos_id = $POST['produtos_id'];
      $ProdutosAviseMe->ip = retornaIpReal();
      $ProdutosAviseMe->nome = $POST['nome'];
      $ProdutosAviseMe->email = $POST['email'];

      if ($ProdutosAviseMe->save()) {
        $str['error'] = false;
        $str['mensagem'] = $HTML_SUCESSO;
      } else {
        $str['error'] = true;
        $str['mensagem'] = $HTML_ERROR;
      }
    } else {
      $str['error'] = false;
      $str['mensagem'] = $HTML_SUCESSO;
    }
    exit(json_encode($str));
    break;
}
