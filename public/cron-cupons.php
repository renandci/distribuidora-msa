<?php
defined('PATH_ROOT') || define('PATH_ROOT', realpath($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR);
include PATH_ROOT . 'app/vendor/autoload.php';
include PATH_ROOT . 'app/settings.php';
include PATH_ROOT . 'app/includes/bibli-funcoes.php';
include PATH_ROOT . 'app/includes/ajax-emails.php';

$auto_db   = implode(substr('datacontrolinformatica', 0, 8), ['', substr('_e' . ASSETS, 0, 10)]);
$auto_user = 'datacont_db';
$auto_pass = 'dA@1155a$!';

$auto_connections = [
  'production' => "mysql://{$auto_user}:{$auto_pass}@localhost/{$auto_db}?charset=utf8",
  'development' => 'mysql://root:root@localhost/ecommerce_db?charset=utf8',
];

$cfg = ActiveRecord\Config::instance();
$cfg->set_model_directory(sprintf('%sapp/models', PATH_ROOT));
$cfg->set_connections(['development' => (strlen(strstr(SERVER_NAME, '.test')) > 0 ? $auto_connections['development'] : $auto_connections['production'])]);

$Lojas = Lojas::find(ASSETS);
$Configuracoes = Configuracoes::find(['conditions' => ['loja_id=?', $Lojas->id]]);
$CONFIG = $Configuracoes->to_array();

$CuponsSend = CuponsSend::all([
  'select' => 'clientes.nome, clientes.email, cupons_send.body_mail, cupons_send.id',
  'joins' => [
    'cliente',
    'cupom',
  ],
  'conditions' => [
    'cupons_send.send = ? and cupom.excluir = ?', 0, 0
  ],
  'order' => 'clientes.nome',
  'limit' => '15'
]);

$mail->setFrom($CONFIG['email_contato'], 'Cupom de desconto ' . $CONFIG['nome_fantasia']);
$mail->AltBody = 'Para ver a mensagem, use um visualizador de e-mail compatível com HTML!';

foreach ($CuponsSend ?? [] as $rws) {
  echo $body = email_body($CONFIG, $rws->body_mail);

  $mail->addAddress($rws->email, $rws->nome);
  $mail->Subject = 'Olá ' . $rws->nome . ', você ganhou um cupom de desconto. Aproveite!';
  $mail->msgHTML($body);

  if (!$mail->send())
    echo 'Mail fail';

  $CuponsSend = CuponsSend::find($rws->id);
  $CuponsSend->send = 1;
  $CuponsSend->save();
}
