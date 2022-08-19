<?php
include '../settings.php';
include '../vendor/autoload.php';
include '../settings-config.php';
include '../includes/bibli-funcoes.php';
include '../includes/ajax-emails.php';

\PagSeguro\Library::initialize();
\PagSeguro\Library::cmsVersion()->setName($CONFIG['nome_fantasia'])->setRelease('1.0.0');
\PagSeguro\Library::moduleVersion()->setName($CONFIG['nome_fantasia'])->setRelease('1.0.0');
\PagSeguro\Configuration\Configure::setEnvironment(empty($CONFIG['pagamentos']['pagseguro_mode']) ? 'sandbox':'production');
\PagSeguro\Configuration\Configure::setAccountCredentials($CONFIG['pagamentos']['pagseguro_email'], $CONFIG['pagamentos']['pagseguro_token']);
\PagSeguro\Configuration\Configure::setCharset('UTF-8');
\PagSeguro\Configuration\Configure::setLog(true, PATH_ROOT .  '/cache/log-notification.log');

try {
    if (\PagSeguro\Helpers\Xhr::hasPost()) {
        $response = \PagSeguro\Services\Transactions\Notification::check(\PagSeguro\Configuration\Configure::getAccountCredentials());

        $code = $response->getCode();
        $status = $response->getStatus();

        switch($status) {
            case 4 :
                $status = 3;
            break;
            case 5 :
                $status = 2;
            break;
            case 7 :
                $status = 10;
            break;
            case 6 :
            case 8 :
            case 9 :
                $status = 4;
            break;
        }
    } 
    else {
        throw new \InvalidArgumentException($_POST);
    }
} catch (\Throwable $th) {
    //throw $th;
    die('Não autorizado');
}

$PedidosTransacoes = PedidosTransacoes::first(['conditions' => ['pagseguro_checkout = ?', $code]]);

$PedidosLogs = new PedidosLogs();
$PedidosLogs->id_pedido = $PedidosTransacoes->pedidos_id;
$PedidosLogs->status = $status;
$PedidosLogs->descricao = text_status_vendas($status);
$PedidosLogs->data_envio = $CONFIG['date_time'];
$PedidosLogs->save();

$Pedidos = Pedidos::find($PedidosTransacoes->pedidos_id);
$Pedidos->status = $status;
$Pedidos->save();