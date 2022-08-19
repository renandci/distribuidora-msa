<?php
$Conditions = [];
$Conditions['select'] = ''
  . 'produtos.id, '
  . 'produtos.loja_id, '
  . 'produtos.id_cor, '
  . 'produtos.codigo_id,'
  . 'produtos.codigo_produto, '
  . 'produtos.nome_produto, '
  . 'produtos.estoque, '
  . 'produtos.preco_venda, '
  . 'produtos.preco_promo, '
  . 'produtos.placastatus, '
  . 'produtos.frete, '
  . 'produtos.subnome_produto as descricao, '
  . 'marcas.marcas, '
  . 'produtos_imagens.imagem ';

$Conditions['joins'] = ''
  . 'INNER JOIN produtos ON produtos_menus.codigo_id = produtos.codigo_id '
  . 'INNER JOIN marcas ON produtos.id_marca = marcas.id '
  . 'INNER JOIN cores ON produtos.id_cor = cores.id '
  . 'INNER JOIN produtos_imagens ON produtos_imagens.codigo_id = produtos.codigo_id ';


$Conditions['conditions'] = ''
  . 'produtos.status = 0 '
  . 'AND produtos.excluir = 0 '
  . 'AND marcas.excluir = 0 '
  . 'AND produtos.id_cor = produtos_imagens.cor_id '
  . 'AND produtos_imagens.capa=1 ';
$Conditions['conditions'] .= sprintf('AND produtos.loja_id=%u ', $CONFIG['loja_id']);

$Conditions['order'] = 'produtos.id DESC';
$Conditions['group'] = 'produtos.codigo_id, produtos.id_cor';
$Conditions['limit'] = '12';

$CONFIG['produtos_index'] = ProdutosMenus::all($Conditions);

$Conditions['select'] .= ', count(pedidos_vendas.id_produto) as teste ';
$Conditions['joins'] .= 'INNER JOIN pedidos_vendas ON pedidos_vendas.id_produto=produtos.id ';
$Conditions['order'] = '13 DESC';
$CONFIG['produtos_index_sale'] = ProdutosMenus::all($Conditions);

/**
 * Verificar se é um dispositivo móvel que está sendo acessado
 */
include dirname(__DIR__) . '/_layout/layout-header.php';
if ($MobileDetect->isMobile() || $MobileDetect->isTablet()) {
  include dirname(__DIR__) . '/_layout/layout-header-mobile-topo.php';
} else {
  include sprintf('%stopo.php', URL_VIEWS_BASE);
}
?>
<br>
<div class="row" id="contact">
  <div class="col-md-12">
    <h1>Entre em Contato! </h1>
  </div>
  <div class="col-md-6">
    <form method="POST" action="contato" enctype="multipart/form-data">
      <div class="form-group "><label>Seu Nome*</label><input class="form-control" name="nome" type="text"></div>
      <div class="form-group "><label>Seu Email*</label><input class="form-control" name="email" type="email"></div>
      <div class="form-group "><label>Seu Telefone*</label><input class="form-control" name="tel" type="tel"></div>
      <div class="form-group "><label>Motivo do Contato</label><textarea class="form-control" name="motivo"></textarea></div>
      <button class="btn btn-large btn-info " type="submit">Enviar</button>
    </form>
  </div>
  <div class="col-md-6 text-center"><img class="rounded-circle img-fluid" data-tilt alt="IMG" id="pallo" src="<?php echo Imgs::src("contact.png", 'imgs'); ?>"></div>
  <div class="col-md-12 mt20"></div>

</div>
<?php

if (!empty($_POST['nome'])) {
  // $mail->isSMTP();
  // $mail->SMTPAuth = true; // Enable SMTP authentication
  // $mail->SMTPSecure = 'ssl'; // Enable TLS encryption, `ssl` also accepted
  // $mail->Port = 465;   // TCP port to connect to
  // $mail->Host = 'mail.distribuidoramsa.com.br';
  // $mail->Username = 'noreply@distribuidoramsa.com.br';
  // $mail->Password = 'contato@1365';

  $nome = isset($_POST['nome']) ? $_POST['nome'] : '';
  $email = isset($_POST['email']) ? $_POST['email'] : '';
  $tel = isset($_POST['tel']) ? $_POST['tel'] : '';
  $assunto = isset($_POST['motivo']) ? $_POST['motivo'] : '';

  $BODY = '';
  $BODY .= "<h1>Contato Via Web Site</h1>";
  $BODY .= $nome ? "<h2>Nome: {$nome}</h2>" : '';
  $BODY .= $email ? "<h2>E-mail: {$email}</h2>" : '';
  $BODY .= $tel ? "<h2>Telefone: {$tel}</h2>" : '';
  $BODY .= $assunto ? '<h2>Motivo do contato: ' . $assunto . '</h2>' : '';

  // $CONTEUDO_MAIL = email_body($CONFIG, $BODY);

  $mail->setFrom("noreply@distribuidoramsa.com.br");
  $mail->addAddress("contato@distribuidoramsa.com.br", "Contato");
  // $mail->AddReplyTo("guilherme@dcisuporte.com.br", $nome);

  $mail->Body = $BODY;
  $mail->Subject = $nome . ' - Contato Via WebSite';

  $message = '';
  $mail->send();

  if (!$mail->send()) {
    $message .= 'Mailer Error: ' . $mail->ErrorInfo;
  } else {
    $mail->ClearAllRecipients();
    $mail->ClearAttachments();
    $mail->ClearAddresses();
    $message .= 'Mensagem enviada com sucesso!';
  }
  echo '<span style="background-color: #0065b2;padding: 10px;border-radius: 9px;color: white;top: 0px;">' . $message . '</span>';
  $mail->SmtpClose();
  return (['mensagem' => $message]);
}
?>

<div class="col-md-12 text-center">
  <h4>Entre em contato conosco através de nosso telefone: (16) 3341-7709</h4>
  <h4>ou pelo email: msa.sorvetes@hotmail.com</h4>
  <h6>MSA PRODUTOS PARA SORVETERIA E CONFEITARIA</h6>
  <h6>AVENIDA ENGENHEIRO IVANIL FRANCISCHINI, 8-690 - CEP: 14940-034 - IBITINGA/SP</h6>
  <h6>CNPJ: 13.420.078/0001-07</h6>
</div>

<?php
include sprintf('%srodape.php', URL_VIEWS_BASE);
