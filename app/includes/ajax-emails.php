<?php
$mail = new PHPMailer();
$mail->isHTML(true);
$mail->CharSet = 'UTF-8';

if (strlen(strpos(URL_BASE, '.test')) > 0) {
  $mail->isSMTP();
  $mail->Host = 'smtp.mailtrap.io';
  $mail->SMTPAuth = true;
  $mail->Port = 2525;
  $mail->Username = '7b7a683ec0e0f1';
  $mail->Password = 'cefb458e7648ad';
} else {
  $mail->isSMTP();
  $mail->SMTPAuth = true; // Enable SMTP authentication
  $mail->SMTPSecure = 'ssl'; // Enable TLS encryption, `ssl` also accepted
  $mail->Port = 465;   // TCP port to connect to
  $mail->Host = 'mail.distribuidoramsa.com.br';
  $mail->Username = 'noreply@distribuidoramsa.com.br';
  $mail->Password = 'contato@1365';
}

//FUNCOES
// email_confirmacao_compra
// confirmacao_cadastro
// email_mewsletters
// recuperar_senha

// STATUS DOS EMAILS DA LOJA
// 0 - PEDIDO EXCLUIDO
// 1 - PEDIDO REALIZADO						 	|| estoque 0 pendente 1
// 2 - PEDIDO AGUARDANDO PAGAMENTO			 	|| estoque 0 pendente 1
// 3 - PEDIDO PAGAMENTO APROVADO				|| estoque 0 pendente 1
// 4 - PEDIDO PAGAMENTO NAO APROVADO			|| estoque 1 pendente 0
// 5 - PEDIDO PAGAMENTO NAO EFETUADO			|| estoque 1 pendente 0
// 6 - PEDIDO PRODUTO EM PRODUCAO			 	|| estoque 0 pendente 1
// 7 - PEDIDO PRODUTO EM SEPARACAO DE ESTOQUE 	|| estoque 0 pendente 1
// 8 - PEDIDO PRODUTO EM TRANSPORTE			 	|| estoque 0 pendente 0
// 9 - PEDIDO PRODUTO ENTREGUE				 	|| estoque 0 pendente 0
// 10 - PEDIDO PEDIDO CANCELADO				 	|| estoque 1 pendente 0
// 11 - PEDIDO EM ANÁLISE					 	|| estoque 0 pendente 1

function status_imgs($status = 0)
{
  $w = (100 / 12) * 1 . '%';
  $html = '';
  $html .= sprintf('<img src="%s" width="%s" style="width: %s" data-status="1" />', Imgs::src(($status == 1 ? 'status-1.png' : 'off-1.png'), 'status'), $w, $w);
  $html .= sprintf('<img src="%s" width="%s" style="width: %s" data-status="2" />', Imgs::src(($status == 2 ? 'status-2.png' : 'off-2.png'), 'status'), $w, $w);
  $html .= sprintf('<img src="%s" width="%s" style="width: %s" data-status="11" />', Imgs::src(($status == 11 ? 'status-11.png' : 'off-11.png'), 'status'), $w, $w);
  $html .= sprintf('<img src="%s" width="%s" style="width: %s" data-status="3" />', Imgs::src(($status == 3 ? 'status-3.png' : 'off-3.png'), 'status'), $w, $w);
  $html .= sprintf('<img src="%s" width="%s" style="width: %s" data-status="4" />', Imgs::src(($status == 4 ? 'status-4.png' : 'off-4.png'), 'status'), $w, $w);
  $html .= sprintf('<img src="%s" width="%s" style="width: %s" data-status="5" />', Imgs::src(($status == 5 ? 'status-5.png' : 'off-5.png'), 'status'), $w, $w);
  $html .= sprintf('<img src="%s" width="%s" style="width: %s" data-status="6" />', Imgs::src(($status == 6 ? 'status-6.png' : 'off-6.png'), 'status'), $w, $w);
  $html .= sprintf('<img src="%s" width="%s" style="width: %s" data-status="7" />', Imgs::src(($status == 7 ? 'status-7.png' : 'off-7.png'), 'status'), $w, $w);
  $html .= sprintf('<img src="%s" width="%s" style="width: %s" data-status="8" />', Imgs::src(($status == 8 ? 'status-8.png' : 'off-8.png'), 'status'), $w, $w);
  $html .= sprintf('<img src="%s" width="%s" style="width: %s" data-status="12" />', Imgs::src(($status == 12 ? 'status-12.png' : 'off-12.png'), 'status'), $w, $w);
  $html .= sprintf('<img src="%s" width="%s" style="width: %s" data-status="9" />', Imgs::src(($status == 9 ? 'status-9.png' : 'off-9.png'), 'status'), $w, $w);
  $html .= sprintf('<img src="%s" width="%s" style="width: %s" data-status="10" />', Imgs::src(($status == 10 ? 'status-10.png' : 'off-10.png'), 'status'), $w, $w);
  return $html;
}

function mail_status($str)
{
  $html = ""
    . "<tr>"
    . "<td align='center'>"
    . status_imgs($str['status'])
    . "</td>"
    . "</tr>";
  return $html;
}

// function icon_status($str){
//     return "<img src='".URL_STATIC."/plataformaimgs/22x22/square/imgs_icons-status_status-{$str}.png'/>";
// }

function mail_buttons($str, $class_btn = '', $btn_text = 'PAGAR NOVAMENTE')
{
  if ($str['status'] == '4' || $str['status'] == '5') {
    return ''
      . ''
      . '<a '
      . ($class_btn ? "class='{$class_btn}' " : 'style="text-align:center;padding: 6px 10px; display:block !important;background-color:' . background001 . ';color:' . color002 . ';font-weight:700;width:175px;margin: 10px auto;border:none;cursor:pointer;"')
      . 'href="' . URL_BASE . 'identificacao/paga-novamente/?pedido=' . $str['id'] . '" '
      . 'target="_blank">'
      . $btn_text
      . '</a>';
  }
}

function mail_buttons_created($btn_text = false, $href = false, $class_btn = false)
{

  return ''
    . '<a '
    . (!empty($class_btn) ? "class='{$class_btn}' " : 'style="text-align:center;padding: 7.5px 10px; display:block !important;background-color:' . background001 . ';color:' . color002 . ';font-weight:700;width:175px;margin: 10px auto;border:none;cursor:pointer;"')
    . 'href="' . $href . '" '
    . 'target="_blank">'
    . $btn_text
    . '</a>';
}

/**
 * @param string $str Codigo do rastreio do pedido
 */
function rastreio($str, $class = '', $style = 'text-align:center; padding:5px 8px; display:block !important; font-weight:700;width:175px;margin:10px 0;border:none;cursor:pointer;border-radius: 5px;-webkit-border-radius: 5px;-moz-border-radius: 5px;', $text = true)
{
  return ''
    . ($text ? '<br/>Seu código de rastreio: <b>' . $str . '</b><br/>' : null)
    . '<a href="http://lojascorreios.dcisuporte.com.br/'
    . '?__token=$2a$08$MTU2OTA3MjY3ODU3ZWU3N.Uu9OcgDALScZpMFazKl2ZnwBO/2zGNK'
    . '&__cod__rastreio=' . $str . '&__url__return=false" '
    . 'target="_blank" '
    . 'class="' . $class . '" '
    . 'style="' . $style . '">'
    . 'RASTREAR OBJETO'
    . '</a>';
}

function email_body($CONFIG = '', $CONTEUDO_MAIL = '')
{
  return ""
    . "<html>"
    . "<head>"
    . "<meta charset='utf-8'/>"
    . "<style type='text/css'>"
    . "body{ background-color: #f3f3f3; line-height: 1; } "
    . "table{ border: solid thin #cccccc; background-color: #ffffff; font: normal 13px tahoma; color: #666; }"
    . "table td{ padding: .5em; } "
    . "a{ color: inherit; text-decoration:none; } "
    . "p{line-height:15px}"
    . "hr{ border: 0; border-bottom-color:#ccc; border-bottom-style: solid; border-bottom-width: 1px; }"
    . "button, html [type=button], [type=reset], [type=submit] {-webkit-appearance: button;}"
    . ".text-uppercase{ text-transform: uppercase }"
    . ".show{ display: inline-block; display: block; }"
    . ".hidden{ display: none; }"
    . ".mensagem-pedido{ line-height: 19px; display: block;} "
    . ".info-mail-site *{ margin: 0 auto; line-height: 17px; color: #999999; } "
    . ".class-border-top{ border-top: dotted 1px #cccccc; } "
    . ".btn-paga-novamente{ padding: 5px 8px; display: inline-block !important; background-color: #6adba8; color: #999999; font-weight: 700; width: 175px; margin: 10px 0 0 0; border: none; cursor: pointer; } "
    . ".text-maiusculo{ text-transform: uppercase; }"
    . ".branco{color: " . color001 . " ! important;}"
    . ".bold{font-weight:bold;}"
    . ".mb5{margin-bottom:5px;}"
    . ".ft14px{font-size:14px}"
    . ".ft15px{font-size:15px}"
    . ".ft16px{font-size:16px}"
    . ".ft18px{font-size:18px}"
    . ".btn{font-family:'Titillium Web',sans-serif; display: inline-block;margin-bottom: 0;font-weight: 400;text-align: center;white-space: nowrap;vertical-align: middle;-ms-touch-action: manipulation;touch-action: manipulation;cursor: pointer;background-image: none;border: 1px solid transparent;padding: 6px 12px;font-size: 14px;line-height: 1.42857143;border-radius: 4px;-webkit-user-select: none;-moz-user-select: none;-ms-user-select: none;user-select: none;}"
    . ".btn-default{color: #333;background-color: #fff;border-color: #ccc;}"
    . ".btn-primary{background-color:" . background001 . ";}"
    . ".btn-primary:hover{background-color:" . background001 . ";}"
    . "</style>"
    . "</head>"
    . "<body>"
    . "<table width='700px' align=center cellpadding='8' cellspacing='0' bgcolor='#ffffff' style='width: 700px'>"
    . "<tr bgcolor='#ffffff'><td align='center'><br /><br /></td></tr>"
    . "<tr bgcolor='" . background001 . "'><td align='center'><img src='" . Imgs::src($CONFIG['logo_desktop'], 'imgs') . "'/></td></tr>"
    . "" . $CONTEUDO_MAIL . ""
    . "<tr bgcolor='" . background001 . "'>"
    . "<td align='center' class='info-mail-site branco' style='color:#ffffff'>"
    . "<h3><a href='" . URL_BASE . "' target='_blank' class='branco'>" . URL_BASE . "</a></h3>"
    // E-mail de configuracao
    . (!empty($CONFIG['email_contato']) ? $CONFIG['email_contato'] . '<br/>' : '')
    // telefones de configuracao
    . (!empty($CONFIG['telefone']) ? 'Telefone: ' . $CONFIG['telefone'] : '')
    . (!empty($CONFIG['telefone']) && !empty($CONFIG['celular']) ? ' - ' : '')
    . (!empty($CONFIG['celular']) ? 'Whatsapp: ' . $CONFIG['celular'] : '')
    . (!empty($CONFIG['telefone']) || !empty($CONFIG['celular']) ? '<br/>' : '')
    // dados de atendimento
    . (!empty($CONFIG['telefone']) ? 'Horário de atendimentos: ' . $CONFIG['horario_atendimentos'] . '<br/>' : '')
    // Nome de configuracao
    . (!empty($CONFIG['nome_fantasia']) ? $CONFIG['nome_fantasia'] : '')
    . (!empty($CONFIG['cnpj']) ? 'CNPJ/IE: ' . $CONFIG['cnpj'] . '<br/>' : '<br/>')
    // dados do endereco
    . '<small>'
    . (!empty($CONFIG['endereco']) ? $CONFIG['endereco'] : '')
    . (!empty($CONFIG['numero']) ? ',' . $CONFIG['numero'] : '')
    . (!empty($CONFIG['cep']) ? ' - ' . $CONFIG['cep'] : '')
    . (!empty($CONFIG['cidade']) ? ' - ' . $CONFIG['cidade'] : '')
    . (!empty($CONFIG['uf']) ? '/' . $CONFIG['uf'] : '')
    . '</small>'
    . "</td>"
    . "</tr>"
    . "<tr bgcolor='#ffffff'><td align='center'><br /><br /></td></tr>"
    . "</table>"
    . "</body>"
    . "</html>";
}

function mail_mensagens($str = '', $rastreio = '', $motivos = '')
{
  global $CONFIG;
  switch ($str['status']) {
    case 1:
      $str['mensagem'] = "O Pedido da compra {$str['codigo']} foi efetuado com sucesso.<br />Enviaremos um novo e-mail a cada evolução no andamento do seu pedido.<br />Qualquer dúvida entre em contato pelo telefone abaixo.<br />Obrigado.";
      $str['titulo_status'] = "Pedido realizado";
      break;
    case 2:
      $str['mensagem'] = 'Aguardando pagamento';
      $str['titulo_status'] = 'Aguardando pagamento';
      break;

    case 3:
      $str['mensagem'] = "O pagamento da compra {$str['codigo']} foi aprovado com sucesso.<br />Sua mercadoria será enviada e você receberá novos e-mails informando o número para rastreio.<br />Obrigado!";
      $str['titulo_status'] = 'Pagamento aprovado';
      break;

    case 4:
      $str['mensagem'] = "Seu Pedido {$str['codigo']} foi cancelado.<br />Motivo: {$motivos}";
      $str['titulo_status'] = 'Pagamento não aprovado';
      $str['btn'] = mail_buttons($str);
      break;

    case 5:
      $str['mensagem'] = "Seu Pedido {$str['codigo']} foi cancelado.<br />Motivo: {$motivos}";
      $str['titulo_status'] = 'Pagamento não efetuado';
      $str['btn'] = mail_buttons($str);
      break;

    case 6:
      $str['mensagem'] = "Seu pedido {$str['codigo']} está em produção.<br />Você receberá novos e-mails informando sobre o status do seu pedido";
      $str['titulo_status'] = "Pedido em produção";
      break;

    case 7:
      $str['mensagem'] = "Seu pedido {$str['codigo']} está em separaçao de estoque.<br />Você receberá novos e-mails informando sobre o status do seu pedido";
      $str['titulo_status'] = 'Pedido em separaçao de estoque';
      break;

    case 8:
      $str['mensagem'] = 'O pedido da compra <b>' . $str['codigo'] . '</b> já se encontra em transportadora.<br/>';
      $str['mensagem'] .= '<b>Sua nota fiscal de compra está em anexo nesse e-mail.</b><br/>';
      $str['mensagem'] .= 'Clique no botão abaixo para Rastrear sua encomenda.';
      $str['mensagem'] .= rastreio($str['rastreio']);
      $str['titulo_status'] = 'Pedido em transportadora';
      break;

    case 9:
      $str['mensagem'] = "O pedido {$str['codigo']} foi entregue com sucesso.";
      $str['titulo_status'] = "Seu produto chegou!";
      break;

    case 10:
      $str['mensagem'] = "O pedido da compra {$str['codigo']} foi cancelado<br />Motivo: {$motivos}";
      $str['titulo_status'] = "Pedido cancelado";
      break;

    case 11:
      $str['mensagem'] = "Seu pedido {$str['codigo']} está em análise<br />Motivo: {$motivos}";
      $str['titulo_status'] = "Pedido em análise";
      break;

    case 12:
      $str['mensagem'] = "Prezado cliente, " . PHP_EOL
        . "Conforme consulta no site dos Correios, sua mercadoria encontra-se com o " . PHP_EOL
        . "status: Aguardando Retirada." . PHP_EOL
        . "" . PHP_EOL
        . "Código de rastreio: <strong>{$rastreio}</strong>" . PHP_EOL
        . "" . PHP_EOL
        . "Por gentileza levar seu código de rastreio e um documento com foto para" . PHP_EOL
        . "retirar sua mercadoria nos Correios, evitando assim que sua mercadoria" . PHP_EOL
        . "retorne para nossa loja." . PHP_EOL
        . "Continuamos à disposição para qualquer dúvida." . PHP_EOL
        . "Atenciosamente, Equipe " . $CONFIG['nome_fantasia'] . ".";

      $str['mensagem'] = nl2br($str['mensagem']) . "<br/>Motivo: {$motivos}";
      $str['titulo_status'] = "Aguardando Retirada";
      break;
  }
  return $str;
}

/**
 * Nova API para os envios dos e-mail para os pedidos do sistema
 * @global PHPMailer $mail
 * @global type $CONFIG
 * @param type $id_pedido id do pedido "Tanto pode ser como hash ou int"
 * @param type $status Status em que o pedido irá receber
 * @param type $rastreio Código de ratreiamento dos correios
 * @param type $motivos Adicionar algum motivo de erro ou cancelamento etc para o pedido
 * @return string
 */
function EmailComfirmacaoCompra($id_pedido = 0, $status = null, $rastreio = null, $motivos = null)
{
  global $CONFIG, $mail;

  // $Pedidos = Pedidos::getVendasAll($id_pedido);
  $Pedido = Pedidos::find($id_pedido)->to_array([
    'include' => [
      'cliente',
      'pedido_endereco',
      'pedidos_vendas' => [
        'include' => [
          'produto' => [
            'include' => [
              'capa',
              'cor',
              'tamanho'
            ]
          ]
        ]
      ],
      'nfe_notas',
    ]
  ]);

  $Pedido = mail_mensagens($Pedido, $rastreio, $motivos) + $Pedido;

  $html = ''
    . mail_status($Pedido)
    . '<tr>'
    . '<td>'
    . sprintf('<b>Olá %s</b><br/>', $Pedido['cliente']['nome'])
    . sprintf('Data compra: %s', date('d/m/Y H:i', strtotime($Pedido['data_venda'])))
    . '</td>'
    . '</tr>'
    . '<tr>'
    . '<td>'
    . $Pedido['mensagem']
    . ($Pedido['btn'] ? sprintf('<hr/><center>%s</center>', $Pedido['btn']) : '')
    . '</td>'
    . '</tr>';

  // Verifica há existencia de um formulario
  $CountQuestionario = (int)count($CONFIG['questionario']);
  if ($CountQuestionario > 0 && $Pedido['status'] == '9') {
    $html .= ''
      . '<tr>'
      . '<td align="center" style="border-top: 1px solid #ccc;">'
      . '<h3>' . $Pedido['cliente']['nome'] . '</h3>'
      . 'Conte-nos como foi sua experiência de compra, preenchendo nosso questionário.<br/>'
      . mail_buttons_created('FORMULÁRIO DE SATISFAÇÃO', URL_BASE . 'identificacao/questionario/?cliente_id=token_' . strrev(sha1($Pedido['cliente']['id'])) . '&pedido_id=token_' . strrev(sha1($Pedido['id'])))
      . '<br/></td>'
      . '</tr>';
  }

  $html .= ''
    . '<tr>'
    . '<td>'
    . '<hr/>'
    . '<table cellspacing="0" width="100%" cellpadding="0" style="border:none;">'
    . '<tr>'
    . '<td><b>Produtos Adquiridos</b></td>'
    . '<td align="center"><b>Qtde</b></td>'
    . '<td align="center"><b>Valor</b></td>'
    . '</tr>';

  $i = 0;
  $QTDE = 0;
  foreach ($Pedido['pedidos_vendas'] as $rr) {
    $html .= ''
      . '<tr>'
      . sprintf('<td width="105px" nowrap="nowrap"%s>', ($i > 0 ? ' class="class-border-top"' : ''))
      . sprintf('<img src="%s" style="vertical-align: middle;" width="105px" />', Imgs::src($rr['produto']['capa']['imagem'], 'smalls'))
      . '</td>'
      . sprintf('<td%s>', ($i > 0 ? ' class="class-border-top"' : ''))
      . sprintf('<b>%s</b>', $rr['produto']['nome_produto'])
      . ($rr['produto']['cor']['nomecor'] != '' ? sprintf('<br />%s', $rr['produto']['cor']['nomecor']) : '')
      . ($rr['produto']['tamanho']['nometamanho'] != '' ? sprintf('<br />%s', $rr['produto']['tamanho']['nometamanho']) : '');

    if (!empty($rr['personalizado'])) {

      $personalizado = json_decode(html_entity_decode($rr['personalizado']), true);

      $html .= sprintf('<br/><span style="display: block; font-size: 16px; color: red; font-weight: 600">%s</span> ', 'Produto Personalizado');

      foreach ((array)$personalizado as $key => $value) {
        $val = '';
        $html .= '<br/><span style="display: block; color: red;">';
        foreach ((array)$value as $key2 => $value2) {
          $val .= "$value2: ";
        }
        $html .= sprintf('<span style="font-size: 14px;">%s</span> ', rtrim($val, ': '));
        $html .= '</span>';
      }
    }

    $html .= '</td>';

    $html .= ''
      . sprintf('<td align="center"%s>', ($i > 0 ? ' class="class-border-top"' : ''))
      . $rr['quantidade']
      . '</td>'
      . sprintf('<td width="1%%" nowrap="nowrap" align="center" class="%s ft18px">', ($i > 0 ? ' class-border-top' : null))
      . sprintf('R$: %s', number_format($rr['valor_pago'], 2, ',', '.'))
      . '</td>'
      . '</tr>';

    $i++;
    $QTDE += $rr['quantidade'];
  }

  $TOTAL = valor_pagamento($Pedido['valor_compra'], $Pedido['frete_valor'], $Pedido['desconto_cupom'], '$', $Pedido['desconto_boleto']);

  // Forma de Pagamento
  $html .= ''
    . '<tr>'
    . '<td align="left" style="border-top: 1px solid #ccc;" colspan="4">'
    . sprintf('<h5>Forma de Pagamento: <span class="ft15px">%s</span></h5>', $Pedido['forma_pagamento'])
    . ($Pedido['cartao'] ? '<br/>Cartão: ' . $Pedido['cartao'] : '')
    . ($Pedido['parcelas'] ? '<br/>Parcelas: ' . $Pedido['parcelas'] : '');

  if ($Pedido['forma_pagamento'] == 'Boleto') {
    $boleto = ''
      . '<a href="%sboleto/index.php?id=%u" target="_blank" class="btn btn-default btn-lg btn-block center-block mb15 mt5" style="max-width: 320px">'
      . '<img src="%s" width="135px" style="width: 135px;"/>'
      . '</a>';
    $html .= sprintf($boleto, URL_BASE, $id_pedido, Imgs::src('boleto.jpg', 'public'));
  }

  if ($Pedido['forma_pagamento'] == 'Transferência') {
    foreach ($CONFIG['transferencias'] as $trans) {
      $html .= "<table border='0' width='50%'><tr>";
      $html .= "<td width='1%' nowrap='nowrap'><img src='" . Imgs::src(sprintf('imagens-bancos-%s', $trans['banco_logo']), 'public') . "' width='75' style='width: 75px;'/></td>";
      $html .= "<td><b>Valor:</b> ";
      $html .= "<span class='ft18px'>R$: " . number_format($TOTAL['TOTAL_COMPRA_C_BOLETO'], 2, ',', '.') . "</span>";
      $html .= "<span class='show ft16px'>" . $trans['banco_titulo'] . "</span>";
      $html .= "<span class='show'><b>AGÊNCIA:</b> <b>" . $trans['banco_ag'] . "</b></span>";
      $html .= "<span class='show'><b class='mb5 text-uppercase'>" . $trans['banco_tipocc'] . ":</b> <b>" . $trans['banco_cc'] . "</b></span>";
      $html .= "<span class='" . ($trans['banco_operacao'] == '' ? 'hidden' : 'show') . "'>";
      $html .= "<b class='mb5 text-uppercase'>operaçao:</b> <b>" . $trans['banco_operacao'] . "</b>";
      $html .= "</span>";
      $html .= "<span class='show'><b class='mb5'>NOME:</b> " . $trans['banco_razaosocial'] . "</span>";
      $html .= "<span class='" . ($trans['banco_cpfcnpj'] == '' ? 'hidden' : 'show') . "'>";
      $html .= "<b class='mb5'>" . ((strlen($trans['banco_cpfcnpj']) > 14) ? 'CNPJ' : 'CPF') . ":</b>";
      $html .= $trans['banco_cpfcnpj'];
      $html .= "</span></td>";
      $html .= "</td></tr></table>";
    }
  }

  if ($Pedido['forma_pagamento'] == 'Pix') {
    $boleto = ''
      . '<a href="%spix/index.php?id=%u" target="_blank" class="btn btn-default btn-lg btn-block center-block mb15 mt5" style="max-width: 320px">'
      . '<img src="%s" width="135px" style="width: 135px;"/>'
      . '</a>';
    $html .= sprintf($boleto, URL_BASE, $id_pedido, Imgs::src('pix.jpg', 'public'));
  }

  // if( $Pedido['forma_pagamento'] == 'Pix' ) {
  // 	$PayLoad = (new \App\Pix\Payload)->setPixKey($CONFIG['pagamentos']['pix_key'])
  //                        ->setDescription(sprintf('Pgto. Refer: %s', $Pedido['codigo']))
  //                        ->setMerchantName($CONFIG['pagamentos']['pix_name'])
  //                        ->setMerchantCity($CONFIG['pagamentos']['pix_city'])
  //                        ->setAmount($TOTAL['TOTAL_COMPRA_C_BOLETO'])
  //                        ->setTxid($Pedido['codigo']);

  // 	$PayLoadQrCode = $PayLoad->getPayload();

  // 	$QRCode = (new \chillerlan\QRCode\QRCode)->render($PayLoadQrCode);

  // 	$html .= "<small>1. Abra seu app de pagamentos ou Internet Banking.</small><br/>";
  // 	$html .= "<small>2. Busque pela opção de pagamento via Pix.</small><br/>";
  // 	$html .= "<small>3. Escaneie ou copie e cole o código abaixo:</small><br/><br/>";

  // 	$html .= "<table border='0' width='100%'><tr>";
  // 	$html .= "<td width='1%' nowrap='nowrap'><img src='".($QRCode??null)."' alt='QrCode' width='105px' height='105px' style='width: 105px; height: 105px;'/></td>";
  // 	$html .= "<td>";
  // 	// $html .= "<p>Cópie o código de pagamento, e efetue seu pagamento no app do banco de sua preferência.</p>";
  // 	$html .= "<i>".$PayLoadQrCode."</i>";
  // 	$html .= "</td>";
  // 	$html .= "</td></tr></table>";
  // }

  $html .= ''
    . '</td>'
    . '</tr>';

  // Endereço de Entrega
  $html .= ''
    . '<tr>'
    . '<td align="left" style="border-top:1px solid #ccc;" colspan="4">'
    . '<h5>Endereço de Entrega:</h5>'
    . ($Pedido['pedido_endereco']['endereco'] ? "Endereço: {$Pedido['pedido_endereco']['endereco']}, {$Pedido['pedido_endereco']['numero']} " : '')
    . ($Pedido['pedido_endereco']['endereco'] ? "<br/>Bairro: {$Pedido['pedido_endereco']['bairro']} " : '')
    . ($Pedido['pedido_endereco']['endereco'] ? "<br/>Complemento: {$Pedido['pedido_endereco']['complemento']} " : '')
    . ($Pedido['pedido_endereco']['endereco'] ? "<br/>Referência: {$Pedido['pedido_endereco']['referencia']} " : '')
    . ($Pedido['pedido_endereco']['endereco'] ? "<br/>Cidade/UF: {$Pedido['pedido_endereco']['cidade']}/{$Pedido['pedido_endereco']['uf']} " : '')
    . ($Pedido['pedido_endereco']['endereco'] ? "<br/>CEP: {$Pedido['pedido_endereco']['cep']} " : '')
    . ($Pedido['cliente']['telefone'] ? "Telefone: {$Pedido['cliente']['telefone']}" : '')
    . ($Pedido['cliente']['celular'] ? ", Celular: {$Pedido['cliente']['celular']}" : '')
    . '</td>'
    . '</tr>';

  $html .= ''
    . '<tr>'
    . '<td align="left" style="border-top:1px solid #ccc;">'
    . (!empty($Pedido['frete_prazo']) ? $Pedido['frete_prazo'] : '');

  if (!empty($Pedido['frete_pudoid']) && $Pedido['frete_pudoid'] != null) {
    $PickupPoints = new JadLogNew($CONFIG['jadlog']['token']);
    $ReturnPickupPoints = $PickupPoints->post(sprintf('/pickup/pudos/%s', $Pedido['cep']));
    $array = $ReturnPickupPoints['body']->pudos;

    $html .= array_reduce($array, function ($html, $data) use ($Pedido) {
      if (!empty($Pedido['frete_pudoid']) && $Pedido['frete_pudoid'] == $data->pudoId) {
        $html = '';
        $html .= '<table width="100%" cellpadding="0" bgcolor="#f9f9f9" style="border-top:1px solid #ccc;">';
        $html .= sprintf('<tr><td><strong style="font-size: 16px; display: block;">%s</strong><br/>%s</td></tr>', $data->razao, $data->responsavel);
        $html .= '<tr><td class="ft16px mb5 show">';
        $html .= '<ul class="ft12px mb5" style="list-style-type:none;padding:0;margin:0">';
        $html .= sprintf('<li class="mb5">Endereço: %s, %s</li>', $data->pudoEnderecoList[0]->endereco, $data->pudoEnderecoList[0]->numero);
        $html .= sprintf('<li class="mb5">Bairro: %s</li>', $data->pudoEnderecoList[0]->bairro);
        $html .= sprintf('<li class="mb5">Cidade/UF: %s/%s</li>', $data->pudoEnderecoList[0]->cidade, $data->pudoEnderecoList[0]->uf);
        $html .= '</ul>';
        $html .= sprintf('<span class="show">CNPJ: %s</span>', $data->cnpjCpf);
        $html .= '<br/>';
        $html .= '<span class="show" style="color: #a20000">ATENÇAO: Quando sua encomenda estiver disponível para retirada no endereço acima, lhe informaremos via e-mail, obrigado.</span>';
        $html .= '</td></tr>';
        $html .= '</table>';
      }
      return $html;
    });
  }

  $html .= ""
    . "</td>"
    . "<td align='right' style='border-top: 1px solid #ccc; background-color: #f3f3f3' colspan='3'>"
    . "<span class='show'>Frete: {$Pedido['frete_tipo']}</span><br/>"
    . "<span class='show'>SubTotal: <font color='#a20000'>R$: " . number_format($TOTAL['VALOR_PRODUTOS'], 2, ',', '.') . "</font></span><br/>"
    . "<span class='show'>Total de Itens: <font color='#a20000'>{$QTDE}</font></span><br/>"
    . ($Pedido['desconto_cupom'] > 0 ? "<span class='show'>Cupom desconto R$: -" . number_format($Pedido['desconto_cupom'], 2, ',', '.') . "</span><br/>" : '')
    . ($Pedido['desconto_boleto'] > 0 ? "<span class='show'>Desconto no boleto -{$Pedido['desconto_boleto']}%</span><br/>" : '')
    . "<span class='show'>Valor Frete: <font color='#a20000'>R$: " . number_format($Pedido['frete_valor'], 2, ',', '.') . "</font></span><br/>"
    . "<span class='show'>"
    . "<b>Total da compra</b>: "
    . "<font color='#a20000' size='6'>R$: " . ($Pedido['desconto_boleto']
      ? number_format($TOTAL['TOTAL_COMPRA_C_BOLETO'], 2, ',', '.')
      : number_format($TOTAL['TOTAL_COMPRA'], 2, ',', '.'))
    . "</font></span>"
    . '</td>'
    . '</tr>'
    . '</table>'
    . '<hr />'
    . '</td>'
    . '</tr>';

  $CONTEUDO_MAIL = email_body($CONFIG, $html);

  // $mail->From = trim($CONFIG['email_contato']); // Sua conta de email que será remetente da mensagem
  // $mail->Sender = trim($CONFIG['email_contato']); // Conta de email existente e ativa em seu domínio
  // $mail->FromName = "Contato - {$CONFIG['nome_fantasia']}"; // Nome da conta de email
  $mail->setFrom($CONFIG['email_contato'], $CONFIG['nome_fantasia']);
  $mail->addAddress($Pedido['cliente']['email'], $Pedido['cliente']['nome']);
  $mail->addBCC($CONFIG['email_contato'], $CONFIG['nome_fantasia']);
  $mail->Body = $CONTEUDO_MAIL;
  $mail->Subject = $CONFIG['nome_fantasia'] . ' - ' . $Pedido['titulo_status'] . ' | CÓD:. ' . $Pedido['codigo'];

  // Envio da NF-e
  $filename = null;
  $CountNfe = (int)count($Pedido['nfe_notas']);

  if ($CountNfe > 0 && $status == 8) {
    // if ($CountNfe > 0 && $Pedido['status'] == 8) {

    // use somente imagens JPEG
    $pathLogo = sprintf('%s/assets/%s/imgs/%s', PATH_ROOT, ASSETS, str_replace('.png', '.jpg', $CONFIG['logo_desktop']));

    $dir = sprintf('%sassets/%s/xml/', PATH_ROOT, ASSETS);
    $filename = '%s%s.xml';

    $a = sprintf($filename, $dir, "{$Pedido['nfe_notas']['chavenfe']}-assinada");
    if (file_exists($a)) $xml = $a;

    $b = sprintf($filename, $dir, "{$Pedido['nfe_notas']['chavenfe']}-autorizada");
    if (file_exists($b)) $xml = $b;

    try {
      $docxml = NFePHP\DA\Legacy\FilesFolders::readFile($xml);
      $Danfe = new NFePHP\DA\NFe\Danfe($docxml, 'P', 'A4', $pathLogo, 'I', '');
      $Danfe->montaDANFE();
      $pdf = $Danfe->render();

      $filename = str_replace('.xml', '.pdf', $xml);
      file_put_contents($filename, $pdf);

      $mail->AddAttachment($xml, $Pedido['nfe_notas']['chavenfe'] . '.xml');
      $mail->AddAttachment($filename, $Pedido['nfe_notas']['chavenfe'] . '.pdf');
      unset($pdf);
    } catch (Exception $e) {
    }
  }

  $mail->Send();
  $mail->ClearAllRecipients();
  $mail->SmtpClose();
}

// print_r(EmailComfirmacaoCompra(292, 8));
// die;

function LembretePagamentoBoletoTransferencia($id_pedido = '')
{
  global $CONFIG, $mail;
  $BuscaGeral = ''
    . 'select '
    . 'ped.id, '
    . 'date_format(ped.data_venda, "%d/%m/%Y - %H:%i") as data_compra, '
    . 'ped.codigo, '
    . 'cli.nome, '
    . 'cli.email, '
    . '((ped.valor_compra-(ped.desconto_boleto/100)*ped.valor_compra) + ped.frete_valor) as valor_compra '
    . 'from pedidos ped '
    . 'inner join clientes cli on ped.id_cliente = cli.id '
    . sprintf('where ped.id = %u ', $id_pedido)
    . 'group by ped.id';

  $rs = Pedidos::connection()->query($BuscaGeral)->fetch();

  $html = ""
    . "<tr>"
    . "<td align='left'>"
    . "<h2>Olá {$rs['nome']}</h2>"
    . "<p>O pagamento referente ao pedido {$rs['codigo']} está para vencer.</p>"
    . "<p>A entrega de sua compra depende do pagamento, e quanto mais rápido você efetuar o pagamento, em menos tempo você recebe sua compra.</p>"
    . "<p>Caso ainda não tenha em mãos a segunda via.</p>"
    . '<a href="' . URL_BASE . '/boleto/index.php?id=' . $id_pedido . '" target="_blank" class="btn btn-default btn-lg btn-block center-block mb15 mt5" style="max-width: 320px">'
    . '<img src="' . Imgs::src('boleto.jpg', 'public') . '" width="135" style="width: 135px;"/>'
    . '</a>'
    // . "<p><a href='".URL_BASE."boleto/index.php?id=" . $rs['id'] . "' target='_blank'><button type='button' class='btn-paga-novamente'>"
    // . "CLIQUE PARA IMPRIMIR O BOLETO NOVAMENTE</button></a></p>"
    . "<ul>"
    . "<li>Pedido Nº: {$rs['codigo']}</li>"
    . "<li>Valor do Pedido: R$ " . number_format($rs['valor_compra'], 2, ',', '.') . "</li>"
    . "<li>Data de realização do pedido: {$rs['data_compra']}</li>"
    . "</ul>								"
    . "<p>Se você já efetuou o pagamento, desconsidere esta mensagem."
    . "<p>Agradecemos a preferência e desejamos tê-lo sempre em nosso site."
    . "<p><b>Obrigado,</b></p>"
    . "<p>Se você tiver qualquer dúvida sobre alteração cadastral, por favor, sinta-se à vontade para contatar-nos pelo endereço eletrônico {$CONFIG['email_contato']} ou pelo telefone {$CONFIG['telefone']} em horário comercial.<p>"
    . "<p>______________________________________________________________________________________________</p>"
    . "<p>{$CONFIG['horario_atendimentos']}.</p>"
    . "</td>"
    . "</tr>";

  $CONTEUDO_MAIL = email_body($CONFIG, $html);

  $mail->setFrom($CONFIG['email_contato'], $CONFIG['nome_fantasia']);
  $mail->addAddress($rs['email'], $rs['nome']);
  $mail->addBCC($CONFIG['email_contato'], $CONFIG['nome_fantasia']);

  $mail->Subject = $CONFIG['nome_fantasia'] . ' - Lembrete Pagamento: ' . $rs['codigo'];
  $mail->Body    = $CONTEUDO_MAIL;
  $MSG = '';
  if (!$mail->send()) {
    $MSG .= ''
      . 'Message could not be sent.'
      . 'Mailer Error: ' . $mail->ErrorInfo;
  } else {
    $MSG .= 'Message has been sent';
  }
  $mail->SmtpClose();
  return $MSG;
}

/**
 * Envia um e-mail para o cliente informando sobre o comentario feito no site
 * @global $CONFIG String com as configurações do sistema
 * @global PHPMailer $mail string com a classe para envio de email
 * @param type $comentario_id id da tabela
 */
function EmailComentariosProdutos($comentario_id = 0)
{
  global $CONFIG, $mail;

  $rsComentario = ProdutosComentarios::find($comentario_id);

  $BODY = '<tr>
            <td align="center">
                <h3>Olá ' . $rsComentario->cliente->nome . '</h3>';
  if ($rsComentario->motivo != '') {
    $BODY .= 'Seu comentário não foi ativo por motivos:<br />';
    $BODY .= $rsComentario->motivo;
    $titulo_comentario = 'Seu comentario nao foi aceito';
  } else {
    $BODY .= 'Seu comentário foi ativado com sucesso.<br/>Agradecemos sua participação.<br/>';
    $titulo_comentario = 'Seu comentario foi ativado!';
  }
  $BODY .= "</td>
        </tr>
        <tr>
            <td>
            <table>
                <td width='1%' nowrap='nowrap' " . (($i > 0) ? "class='class-border-top'" : '') . ">
                    <img src='" . URL_BASE . "/imgs/imagens-produtos/smalls/{$rsComentario->produto->capa->imagem}' style='vertical-align: middle; width: 70px;' width='70'/>
                </td>
                <td " . (($i > 0) ? "class='class-border-top'" : '') . ">
                    <h3>{$rsComentario->produto->nome_produto}</h3>";
  $BODY .= $rsComentario->produto->marca->marcas != '' ? "<br />{$rsComentario->produto->marca->marcas}" : '';
  $BODY .= $rsComentario->produto->cor->nomecor != '' ? '<br/>' . ($rsComentario->produto->cor->opcoes->tipo != '' ? "{$rsComentario->produto->cor->opcoes->tipo}: " : '') . $rsComentario->produto->cor->nomecor : '';
  $BODY .= $rsComentario->produto->tamanho->nometamanho != '' ? '<br/>' . ($rsComentario->produto->tamanho->opcoes->tipo != '' ? "{$rsComentario->produto->tamanho->opcoes->tipo}: " : '') . $rsComentario->produto->tamanho->nometamanho : '';

  $BODY .= "</td>
            </table>
            </td>
        </tr>
        <tr>
            <td>
                <b>Seu comentário</b><br />
                <div style='line-height: 22px;'>
                    <b>{$rsComentario->titulocomentario}</b><br />
                    <b>Data de cadastro:</b> " . $rsComentario->created_at->format('d.m.Y') . "<br />
                    <div style='border:solid thin #aaa; padding: 5px; width: 55%;'>
                        " . nl2br($rsComentario->comentario) . "
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <center>
                    <a href='" . URL_BASE . 'produto/' . converter_texto($rsComentario->produto->nome_produto) . '/' . $rsComentario->produto->id . "' target='_blank'>
                        <button type='button' style='font-weight: 700; background-color:" . background001  . "; color:" . color002 . "; border: 0; padding: 5px; cursor: pointer;'>
                            VER COMENTÁRIO
                        </button>
                    </a>
                </center>
            </td>
        </tr>";

  $CONTEUDO_MAIL = email_body($CONFIG, $BODY);

  $mail->setFrom($CONFIG['email_contato'], $CONFIG['nome_fantasia']);
  $mail->addAddress($rsComentario->cliente->email, $rsComentario->cliente->nome);
  $mail->addBCC($CONFIG['email_contato'], $CONFIG['nome_fantasia']);

  $mail->Subject = $CONFIG['nome_fantasia'] . ' - ' . $titulo_comentario;
  $mail->Body    = $CONTEUDO_MAIL;
  $MSG = '';
  if (!$mail->send()) {
    $MSG .= ''
      . 'Message could not be sent.'
      . 'Mailer Error: ' . $mail->ErrorInfo;
  } else {
    $MSG .= 'Message has been sent';
  }
  $mail->SmtpClose();
  return $MSG;
}

function recuperar_senha($email = '')
{
  global $mail, $CONFIG;

  $Clientes = Clientes::first(['conditions' => ['email=? and loja_id=?', $email, $CONFIG['loja_id']]]);
  $rs = $Clientes->to_array();
  $cliente_id = base64_encode('&cliente_id=' . sha1($rs['id']));

  $html = ""
    . "<tr>"
    . "<td colspan='2' align='center'>"
    . "<b>Olá {$rs['nome']}</b>"
    . "</td>"
    . "</tr>"
    . "<tr>"
    . "<td colspan='2' >"
    . "<p>Você solicitou redefinir sua senha.</p>"
    . "<b>Para redefinir sua senha click no link abaixo e siga as instruçoes.</b><br />"
    . "<br />"
    . "<center>"
    . "<a href='" . URL_BASE . "identificacao/redefinir-senha/?token={$cliente_id}&_loja=" . URL_BASE . "' "
    . "target='_blank' "
    . "style='"
    . "background-color:" . background001  . "; "
    . "color:" . color002 . "; "
    . "border: 0; "
    . "padding: 5px 15px; "
    . "cursor: pointer; "
    . "display: block; "
    . "display: inline-block; "
    . "width: 200px;'>"
    . "redefinir agora"
    . "</a>"
    . "</center>"
    . ""
    . "</td>"
    . "</tr>";

  $CONTEUDO_MAIL = email_body($CONFIG, $html);

  $mail->setFrom($CONFIG['email_contato'], $CONFIG['nome_fantasia']);
  $mail->addAddress($rs['email'], $rs['nome']);

  $mail->Subject = $CONFIG['nome_fantasia'] . ' - Recuperar senha de ' . $rs['nome'];
  $mail->Body    = $CONTEUDO_MAIL;

  $MSG = '';
  if (!$mail->send()) {
    $MSG .= ''
      . 'Message could not be sent.'
      . 'Mailer Error: ' . $mail->ErrorInfo;
  } else {
    $MSG .= 'Message has been sent';
  }
  $mail->SmtpClose();
  return $MSG;
}

/**
 * E-mail de contato
 * @global PHPMailer $mail
 * @param type $validate array
 * <ul>
 * <li>Determina quais campos deve ser obrigatorios</li>
 * <li>['nome' => true, 'email' => true] etc...</li>
 * </ul>
 * @param type $email Define se contato deve ser enviado para o email de cadastro do sistema ou um especifico quando $email for definido
 * @return boolean array
 */
function email_base_contato($validate = [], $email = null)
{

  global $mail, $CONFIG;

  parse_str(file_get_contents("php://input"), $_CONTATO);

  $ERROR['error'] = false;

  if (isset($_CONTATO['_METHOD']) && $_CONTATO['_METHOD'] == 'CONTATO') {
    if (isset($_CONTATO['nome']) && $_CONTATO['nome'] == '' && $validate['nome'] == true) {
      $ERROR['error'] = true;
      $ERROR['nome'] = [
        'error' => [
          'Digite seu nome!'
        ],
      ];
    }
    if (isset($_CONTATO['email']) && $_CONTATO['email'] == '' && $validate['email'] == true) {
      $ERROR['error'] = true;
      $ERROR['email'] = [
        'error' => [
          'Digite seu e-mail!'
        ],
      ];
    }
    if (isset($_CONTATO['telefone']) && $_CONTATO['telefone'] == '' && $validate['telefone'] == true) {
      $ERROR['error'] = true;
      $ERROR['telefone'] = [
        'error' => [
          'Digite seu nome!'
        ],
      ];
    }
    if (isset($_CONTATO['descricao']) && $_CONTATO['descricao'] == '' && $validate['descricao'] == true) {
      $ERROR['error'] = true;
      $ERROR['descricao'] = [
        'error' => [
          'Digite sua mensagem!'
        ],
      ];
    }
    $BODY = '';
    if (is_array($ERROR) && $ERROR['error'] == false) {
      $BODY .= "<tr><td align='center'><h2>Contato Via Web Stite</h2></td></tr>";
      $BODY .= $_CONTATO['nome'] ? "<tr><td>Nome: {$_CONTATO['nome']}</td></tr>" : '';
      $BODY .= $_CONTATO['email'] ? "<tr><td>E-mail: {$_CONTATO['email']}</td></tr>" : '';
      $BODY .= $_CONTATO['telefone'] ? "<tr><td>Telefone: {$_CONTATO['telefone']}</td></tr>" : '';
      $BODY .= $_CONTATO['descricao'] ? '<tr><td>Descrição: ' . nl2br($_CONTATO['descricao']) . '</td></tr>' : '';

      $CONTEUDO_MAIL = email_body($CONFIG, $BODY);

      $mail->setFrom((!empty($email) && $email != '' ? $email : $CONFIG['email_contato']), $CONFIG['nome_fantasia']);
      $mail->addAddress($_CONTATO['email'], $_CONTATO['nome']);

      $mail->Body = $CONTEUDO_MAIL;
      $mail->Subject = $CONFIG['nome_fantasia'] . ' - Contato Via Web';

      $message = '';
      if (!$mail->send()) {
        $message .= 'Mailer Error: ' . $mail->ErrorInfo;
      } else {
        $message .= 'Mensagem enviada com sucesso!';
      }
      $mail->SmtpClose();
      return (['mensagem' => $message]);
    }
  }

  return (['post' => $_CONTATO] + $ERROR);
}
