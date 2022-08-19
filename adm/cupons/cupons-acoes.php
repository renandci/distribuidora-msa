<?php
include '../topo.php';

if (!isset($POST['acao'])) {
  header('Location: /adm/cupons/cupons.php');
  return;
}

$cupom_id = isset($POST['cupom_id']) ? $POST['cupom_id'] : '';
$cupom_codigo = $POST['cupom_codigo'];
$cupom_desconto = dinheiro($POST['cupom_desconto']);
$cupom_value = dinheiro($POST['cupom_value']);
$cupom_valormin = dinheiro($POST['cupom_valormin']);
$cupom_dataini = converterDatas($POST['cupom_dataini']) . ' 00:00:00';
$cupom_datafin = isset($POST['cupom_datafin']) && $POST['cupom_datafin'] != '' ? converterDatas($POST['cupom_datafin']) . ' 23:59:59' : false;
$cupom_cliente_id = (int)isset($POST['cliente_id']) && $POST['cliente_id'] != '' ? $POST['cliente_id'] : 0;
$cupom_envios = $POST['cupom_envios'];
$cupom_uf = $POST['cupom_uf'];

/**
 * Gerar e guarda o cupom de desconto
 */
$Cupom = new Cupons();
$Cupom->cupom_codigo = $cupom_codigo;
$Cupom->cupom_value = $cupom_value;
$Cupom->cupom_desconto = $cupom_desconto;
$Cupom->cupom_valormin = $cupom_valormin;
$Cupom->cupom_dataini = $cupom_dataini;
$Cupom->cupom_datafin = !empty($cupom_datafin) ? $cupom_datafin : null;
$Cupom->cupom_cliente_id = $cupom_cliente_id;
$CupomId = $Cupom->save_log();
unset($Cupom);

$Cupom = Cupons::find($CupomId['id']);

if (empty($Cupom->id)) {
  header('Location: /adm/cupons/cupons.php');
  return;
}


// Envios para estados em geral
$cupomEnviosCount = (int)count($cupom_uf);
if ($cupomEnviosCount > 0 && $cupom_envios == 'uf') {

  $ClientesEnderecos = ClientesEnderecos::all([
    'select' => 'clientes.nome, clientes.email, clientes.id',
    'joins' => 'inner join clientes on clientes.id = clientes_enderecos.id_cliente',
    'conditions' => [
      'clientes_enderecos.uf in(?) and clientes_enderecos.status = "ativo"', $cupom_uf
    ],
    'group' => 'clientes.id'
  ]);

  foreach ($ClientesEnderecos ?? [] as $rws) {
    $body = ''
      . '<tr bgcolor="#ffffff">'
      . '<td colspan="2" style="padding: 50px 25px;">'
      . '<span style="display:block;font-size: 22px; margin-bottom: 15px;">Parabéns!</span><br/>'
      . 'Olá ' . $rws->nome . ', você ganhou um Cupom de Desconto<br/>'
      . 'no valor de <strong>' . ($cupom_desconto == '$' ? 'R$: ' . number_format($cupom_value, 2, ',', '.') : "{$cupom_value}%") . '</strong> para usar em nosso site.<br/><br/>'
      . '<center>Seu código de desconto é: <br/><strong style="font-size: 18px">' . $cupom_codigo . '</strong></center><br/><br/>'
      . ($cupom_datafin > 0 ? 'Seu cupom é válido até: ' . date('d/m/Y', strtotime($cupom_datafin)) . '<br/>' : '')
      . 'Para utilizar seu cupom de desconto, insira o código acima na página do carrinho ou no final de sua compra.'
      . '</td>'
      . '</tr>';

    $CuponsSend = new CuponsSend();
    $CuponsSend->id_cupons = $Cupom->id;
    $CuponsSend->id_clientes = $rws->id;
    $CuponsSend->body_mail = $body;
    $CuponsSend->save_log();
  }

  header('Location: /adm/cupons/cupons.php');
  return;
}


if (isset($cupom_envios, $cupom_cliente_id) && $cupom_envios == 'unico' && $cupom_cliente_id > 0) {
  $body = '';
  $body .= ''
    . '<tr bgcolor="#ffffff">'
    . '<td colspan="2" style="padding: 50px 25px;">'
    . '<span style="display:block;font-size: 22px; margin-bottom: 15px;">Parabéns!</span><br/>'
    . 'Olá ' . $Cupom->cliente->nome . ', você ganhou um Cupom de Desconto<br/>'
    . 'no valor de <strong>' . ($cupom_desconto == '$' ? 'R$: ' . number_format($cupom_value, 2, ',', '.') : "{$cupom_value}%") . '</strong> para usar em nosso site.<br/><br/>'
    . '<center>Seu código de desconto é: <br/><strong style="font-size: 18px">' . $cupom_codigo . '</strong></center><br/><br/>'
    . ($cupom_datafin > 0 ? 'Seu cupom é válido até: ' . date('d/m/Y', strtotime($cupom_datafin)) . '<br/>' : '')
    . 'Para utilizar seu cupom de desconto, insira o código acima na página do carrinho ou no final de sua compra.'
    . '</td>'
    . '</tr>';

  $body = email_body($CONFIG, $body);

  $mail->setFrom($CONFIG['email_contato'], 'Cupom de desconto ' . $CONFIG['nome_fantasia']);
  $mail->addAddress($Cupom->cliente->email, $Cupom->cliente->nome);

  $mail->Subject = 'Olá ' . $Cupom->cliente->nome . ', você ganhou um cupom de desconto. Aproveite!';
  $mail->msgHTML($body);
  $mail->AltBody = 'Para ver a mensagem, use um visualizador de e-mail compatível com HTML!';

  if (!$mail->send())
    throw new Exception('Não foi possivel enviar o E-mail');

  header('Location: /adm/cupons/cupons.php');
  return;
}

include '../rodape.php';
