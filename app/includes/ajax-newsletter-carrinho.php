<?php
include '../vendor/autoload.php';
include '../settings.php';
include '../settings-config.php';
include '../assets/' . ASSETS .  '/settings.php';
include 'ajax-emails.php';
include './bibli-funcoes.php';
$data_envio = date('Y-m-d Hi:s');
$queryCarrinho = ''
  . 'SELECT 1 FROM carrinho '
  . 'WHERE EXISTS( SELECT 1 FROM clientes WHERE carrinho.id_cliente = clientes.id )';
$resultCarrinho = Carrinho::find_by_sql($queryCarrinho);

if (!count($resultCarrinho)) {
  return false;
}
//echo
$queryCarrinhoAbandonados = ''
  . 'SELECT '
  . 'clientes.nome, '
  . 'clientes.email, '
  . 'produtos.id, '
  . 'produtos.nome_produto, '
  . 'produtos.preco_promo, '
  . 'produtos_imagens.imagem as foto1, '
  . 'carrinho.quantidade, '
  . 'carrinho.id_session, '
  . 'carrinho.status, '
  . 'carrinho.created_at '
  . 'FROM carrinho '
  . 'INNER JOIN clientes ON clientes.id = carrinho.id_cliente '
  . 'INNER JOIN produtos ON produtos.id = carrinho.id_produto '
  . 'INNER JOIN produtos_imagens ON produtos.codigo_id = produtos_imagens.codigo_id and produtos_imagens.cor_id = produtos.id_cor '
  . 'WHERE produtos_imagens.capa=1 AND clientes.id > 0 AND '
  . '(carrinho.lista_desejos = 0 OR carrinho.lista_desejos IS NULL AND ( carrinho.status IS NULL OR carrinho.status_2 IS NULL ) ) ' . PHP_EOL
  . 'GROUP BY carrinho.id_session, produtos.id '
  . '';
//echo
$resultCarrinhoAbandonados = Carrinho::find_by_sql($queryCarrinhoAbandonados);

if (count($resultCarrinhoAbandonados) > 0) {
  $dados = array();
  $data_envio = date('Y-m-d H:i:s');
  foreach ($resultCarrinhoAbandonados as $array) {
    $array = $array->to_array();
    $dados[$array['id_session']][] = $array;
  }
  foreach ($dados as $i => $RwMails) {
    //		$corpo = mail_topo();
    $corpo = ""
      . " <tr>"
      . "<td align='center'>"
      . "<div style='margin: -6px -5.5547px; 0'>"
      . "<img src='" . url_base . "/imgs/tudoenxovais-carrinho.jpg' style='width: 100%;'/>"
      . "<h2>Reservamos para você os produtos que você gostou!</h2>"
      . "</div>"
      . "</td>"
      . "</tr>"
      . " <tr>"
      . "<td colspan='2'>"
      . "<h3>Olá {$RwMails[0]['nome']}</h3>"
      . "Não esquecemos de você!<br/>"
      . "Vimos que você visitou alguns de nossos produtos e selecionamos novamente eles especialmente para você."
      . "</td>"
      . "</tr>"
      . "<tr>"
      . "<td colspan='2'>"
      . "<div style='overflow:hidden'>";
    $x = 1;
    $corpo .= '<div style="float:left;width:100%;border-top:solid 1px #ccc"></div>';
    foreach ($RwMails as $ii => $values) {
      $VALOR_BOLETO = ($values['preco_promo'] - ($CONFIG['desconto_boleto'] / 100) * $values['preco_promo']);

      $corpo .= ''
        . '<div style="float:left; width:' . ((count($RwMails) > 1) ? '50' : '100') . '%; margin:15px 0;text-align: center;">'
        . '<a '
        . 'target="_blank" '
        . 'style="margin: 25px 0; display: block; text-decoration:none; color: inherit;" '
        . 'href="//'
        . $_SERVER['SERVER_NAME']
        . "/cart/?acao=_" . sha1('RecuperarCarrinho') . "&car={$i}&time=" . time() . "&data=" . strtotime($RwMails[0]['created_at']) . "&u=//"
        . $_SERVER['SERVER_NAME']
        . '/produto/'
        . converter_texto($values['nome_produto'])
        . '/' . $values['id']
        . '">'
        . '<img src="' . URL_VIEWS_BASE_PUBLIC_IMAGENS . 'imgs/produtos/smalls/' . $values['foto1'] . '" width="100%" style="max-width: 100%;"/>'
        . '<h3>' . $values['nome_produto'] . '</h3>'
        . '<p >'
        . '<span style="text-decoration: line-through;">Por R$:' . number_format($values['preco_promo'], 2, ',', '.') . '</span><br/>'
        . 'Por <font size="5" color="#f58985">R$: ' . number_format($values['preco_promo'], 2, ',', '.') . '</font>'
        . '</p>'
        . '<span>ou em <font color="#f58985">'
        . parcelamento($values['preco_promo'], $CONFIG['qtde_parcelas'], $CONFIG['parcela_minima']) . 'x de R$: '
        . number_format($values['preco_promo'] / parcelamento($values['preco_promo'], $CONFIG['qtde_parcelas'], $CONFIG['parcela_minima']), 2, ',', '.')
        . '</font> sem juros</span> <br/>'
        . '<font color="#f58985" size="3">OU R$: ' . number_format($VALOR_BOLETO, 2, ',', '.') . ' NO BOLETO</font>'
        . '</a>'
        . '</div>';
      $corpo .= (($x % 2) == 0) ? '<div style="float:left;width:100%;border-top:solid 1px #ccc"></div>' : '';
      $x++;
    }
    $corpo .= "</div>"
      . "</td>"
      . "</tr>"
      . " <tr>"
      . "<td align='center'>"
      . "<a href='//"
      . $_SERVER['SERVER_NAME']
      . "/recuperar_carrinho.php?acao=_" . sha1('RecuperarCarrinho') . "&car={$i}&time=" . time() . "&data=" . strtotime($RwMails[0]['created_at']) . "&u=//"
      . $_SERVER['SERVER_NAME']
      . "/identificacao/carrinho' target='_blank' class='btn btn-primary' style='text-transform: uppercase;'>voltar para finalizar compra</a>"
      . "<br/>"
      . "<br/>"
      . "<br/>"
      . "</td>"
      . "</tr>";

    $corpo = email_body($CONFIG, $corpo);

    $mail->setFrom($CONFIG['email_contato'], $CONFIG['nome_fantasia']);
    $mail->addAddress(strtolower($RwMails[0]['email']), $RwMails[0]['nome']);                       // Add a recipient
    $mail->Subject = "{$RwMails[0]['nome']} - Seu carrinho está a sua espera!";
    $mail->msgHTML($corpo);
    $mail->AltBody = 'Para ver a mensagem, use um visualizador de e-mail compatível com HTML!';

    /**
     * Filtrar os emails dos clientes
     */
    if (filter_var(strtolower($RwMails[0]['email']), FILTER_VALIDATE_EMAIL)) {
      /**
       * Seleciona os envios a partir de 2 horas
       * Buscando a sequencia com dados da session do carrinho
       */
      $Result48Hs = Carrinho::find_by_sql(''
        . 'SELECT id, created_at FROM carrinho '
        . 'WHERE carrinho.enviado <= (now() - INTERVAL 48 HOUR) AND carrinho.status IS NOT NULL AND carrinho.id_session=?', array($i));
      if (count($Result48Hs) > 0) {
        $r48Hs = current($Result48Hs)->to_array();
        echo 'Envio de 48 horas<br/>';
        if ($mail->send()) {
          Carrinho::update_all(
            array(
              'set' => array(
                'status_2' => 'E',
                'enviado_2' => $data_envio
              ),
              'conditions' => array(
                'id_session' => $i
              )
            )
          );
          echo 'Enviado com sucesso<br/>';
        }
      }

      /**
       * Seleciona os emails a partir de 48 horas
       * Buscando a sequencia com dados da session do carrinho
       */
      $Result2Hs = Carrinho::find_by_sql(''
        . 'SELECT id, created_at FROM carrinho '
        . 'WHERE carrinho.created_at <= (now() - INTERVAL 2 HOUR) AND carrinho.status IS NULL AND carrinho.id_session=?', array($i));
      if (count($Result2Hs) > 0) {
        $r2hs = current($Result2Hs)->to_array();
        echo 'Envio de 2 horas<br/>';
        if ($mail->send()) {
          Carrinho::update_all(
            array(
              'set' => array(
                'status' => 'E',
                'enviado' => $data_envio
              ),
              'conditions' => array(
                'id_session' => $i
              )
            )
          );
          echo 'Enviado com sucesso<br/>';
        }
      }
      unset($corpo);
    }
    $mail->clearAddresses();
    $mail->clearAttachments();
  }
}
$mail->SmtpClose();
