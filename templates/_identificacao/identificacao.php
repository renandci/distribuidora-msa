<?php
$modulo2 = isset($GET_ACAO) && $GET_ACAO !== '' ? $GET_ACAO : '';
$ACAO_GET = isset($GET_ACAO) && $GET_ACAO !== '' ? $GET_ACAO : '';
$ACAO_POST = isset($POST['acao']) && $POST['acao'] != '' ? $POST['acao'] : '';
$TITULO_PAGINA = str_replace('-', ' ', $ACAO_GET);

// ClearSale
switch ($modulo2) {
  case 'cadastre-se':
  case 'editar-cadastro':
    $STORE['cs:page'] = !empty($_SESSION['cliente']['id_cliente']) ? 'edit-account' : 'create-account';
    break;

  case 'carrinho':
    $STORE['cs:page'] = 'cart';
    break;

  case 'redefinir-senha':
    $STORE['cs:page'] = 'password-reset';
    break;

  case 'checkout-new':
    $STORE['cs:page'] = 'checkout';
    break;

  case 'obrigado':
  case 'finalizado':

    $id = filter_input(INPUT_GET, 'pedidos_id', FILTER_SANITIZE_NUMBER_INT);
    $arr_pay_type = ['Pagar Me' => 'credit-card', 'Transferência' => 'transfer', 'Boleto' => 'other'];

    $Pedidos = Pedidos::find($id);

    $STORE['cs:page'] = 'purchase-confirmation';
    $STORE['cs:description'] = sprintf('code=%s, payment-type=%s', $Pedidos->codigo, $arr_pay_type[$Pedidos->forma_pagamento]);
    $TOTAL = valor_pagamento($Pedidos->valor_compra, $Pedidos->frete_valor, $Pedidos->desconto_cupom, '$', $Pedidos->desconto_boleto);

    ob_start(); ?>
    <script>
      gtag('event', 'conversion', {
        'send_to': 'AW-774145003/WXVTCKrLg7MBEOuHkvEC',
        'value': <?php echo number_format($TOTAL['TOTAL_COMPRA_C_BOLETO'], 2, '.', '') ?>,
        'currency': 'BRL',
        'transaction_id': "<?php echo $Pedidos->codigo ?>"
      });
    </script>
    <script>
      gtag('event', 'purchase', {
            "event_category": "Comprafinalizada",
            "transaction_id": "<?php echo $Pedidos->codigo ?>",
            "affiliation": "Detalhes Pequenos",
            "value": <?php echo number_format($TOTAL['TOTAL_COMPRA_C_BOLETO'], 2, '.', '') ?>,
            "currency": "BRL",
            "shipping": <?php echo number_format($Pedidos->frete_valor, 2, '.', '') ?>
          );
    </script>
<?php $str['script_header'] .= ob_get_clean();
    break;
}

// Verificar se é um dispositivo móvel que está sendo acessado
include dirname(__DIR__) . '/_layout/layout-header.php';
if (($MobileDetect->isMobile() || $MobileDetect->isTablet()) && $menu == false) {
  include dirname(__DIR__) . '/_layout/layout-header-mobile-topo.php';
} else {
  include sprintf('%stopo.php', URL_VIEWS_BASE);
}

switch ($modulo2) {
  case 'AtualizarCarrinho':

    $POST['tipofrete'] = $POST['tipofrete'] ? $POST['tipofrete'] : 'GRÁTIS';

    $set = [
      'frete_prazo' => trim($POST['prazosfrete']),
      'frete_tipo' => trim($POST['tipofrete']),
      'frete_valor' => dinheiro($POST['valorfrete']),
      'cep' => $POST['cep']
    ];

    if ($POST['tipofrete'] != 'JADLOG-ECONOMICO') {
      $set = ($set + ['jadlog_pudoid' => null]);
    }

    Carrinho::update_all(['set' => $set, 'conditions' => ['id_session=?', session_id()]]);

    $i                      = 1;
    $TOTAL_ITENS       = 0;
    $TIPO_FRETE       = 0;
    $TOTAL_FRETE       = 0;
    $TOTAL_DESCONTO      = 0;
    $TOTAL_CARRINHO      = 0;
    $TOTAL_CARRINHO_FRETE  = 0;
    $TOTAL_ESTOQUE          = 0;

    // $CarrinhoCompras = Carrinho::cart();
    $CarrinhoCompras = $CONFIG['carrinho_all'];
    foreach ($CarrinhoCompras as $r) {
      $TOTAL_CARRINHO      += ($r->preco_promo * $r->quantidade);
      $TOTAL_FRETE_SOMA    = $r->frete_valor;

      $ID_CUPOM        = $r->id_cupom;
      $CUPOM           = $r->cupom_codigo;
      $CUPOM_TIPO        = $r->cupom_desconto;
      $CUPOM_VALOR      = $r->cupom_value;
    }

    $TOTAL = 0;
    $TOTAL = valor_pagamento($TOTAL_CARRINHO, $TOTAL_FRETE_SOMA, $CUPOM_VALOR, $CUPOM_TIPO, $CONFIG['desconto_boleto']);

    $str['total_frete']            = 'R$: ' . number_format($TOTAL['TOTAL_FRETE'], 2, ',', '.');
    $str['total_desconto']          = $TOTAL['TOTAL_CUPOM'] ? $TOTAL['TOTAL_CUPOM'] : 'R$: 0,00';
    $str['total_boleto']          = 'R$: ' . number_format($TOTAL['TOTAL_COMPRA_C_BOLETO'], 2, ',', '.');
    $str['total_transferencia']        = 'R$: ' . number_format($TOTAL['TOTAL_COMPRA_C_BOLETO'], 2, ',', '.');
    $str['total_carrinho']          = 'R$: ' . number_format($TOTAL['TOTAL_COMPRA'], 2, ',', '.');
    $str['total_carrinho_frete']       = 'R$: ' . number_format($TOTAL['TOTAL_COMPRA'], 2, ',', '.');
    $str['quantidade_parcela']         = parcelamento($TOTAL['TOTAL_COMPRA'], $CONFIG['qtde_parcelas'], $CONFIG['parcela_minima']);

    $str['installments_html'] = ''
      . '<div class="row ft12px mb5">'
      . '<div class="col-xs-12">'
      . 'Parcelamento via cartão'
      . '</div>';
    for ($p = 1; $p <= ($str['quantidade_parcela']); ++$p) {
      $str['installments_html'] .= ''
        . '<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">'
        . sprintf('%ux de <span class="color-004 ft14px">R$: %s</span>', $p, number_format(($TOTAL['TOTAL_COMPRA'] / $p), 2, ',', '.'))
        . '</div>';
    }
    $str['installments_html'] .= '</div>';


    $taxa           = 2 / 100;
    $str['selecione']     = 'Selecione...';
    $str['installments']   = '<option value="-1">Selecione...</option>';

    for ($p = 1; $p <= ($str['quantidade_parcela']); ++$p) {
      $str['installments'] .= sprintf('<option value="%u">%ux de R$: %s</option>', $p, $p, number_format(($TOTAL['TOTAL_COMPRA'] / $p), 2, ',', '.'));
    }

    echo sprintf('<gdiv id="installments">%s</gdiv>', json_encode($str));

    break;

  case 'RemoverPudosJadLog':

    login_existe($_SESSION['cliente']['id_cliente'], URL_BASE . 'identificacao/login?_u=' . URL_BASE . 'identificacao/minha-compra');

    $Carrinho = Carrinho::update_all([
      'set' => [
        'cep' => '',
        'frete_tipo' => null,
        'frete_valor' => null,
        'frete_prazo' => null,
        'jadlog_pudoid' => null,
      ],
      'conditions' => ['id_session=?', session_id()]
    ]);

    break;

  case 'AtualizarPudosJadLog':

    login_existe($_SESSION['cliente']['id_cliente'], URL_BASE . 'identificacao/login?_u=' . URL_BASE . 'identificacao/minha-compra');

    $rws = ClientesEnderecos::first(['conditions' => ['md5(id_cliente)=? and status="ativo"', $_SESSION['cliente']['id_cliente']]]);

    $Carrinho = Carrinho::update_all([
      'set' => ['jadlog_pudoid' => $POST['jadlog_pudoid']],
      'conditions' => ['id_session=?', session_id()]
    ]);

    $PickupPoints = new JadLogNew($CONFIG['jadlog']['token']);
    $ReturnPickupPoints = $PickupPoints->post(sprintf('/pickup/pudos/%s', $rws->cep));
    $array = $ReturnPickupPoints['body']->pudos;

    $PudoId = array_reduce($array, function ($html, $data) use ($POST) {
      if (!empty($POST['jadlog_pudoid']) && $POST['jadlog_pudoid'] == $data->pudoId) {
        $html .= '<div class="col-sm-12 col-xs-12">';
        $html .= '<span class="ft15px show mb5" style="color:#a20000;">Local escolhido para a retirada da sua encomenda:</span>';
        $html .= sprintf('<span class="ft14px mb5 bold show">%s</span>', $data->razao);
        $html .= sprintf('<strong class="show ft12px">%s</strong>', $data->responsavel);
        $html .= '<ul class="ft11px mb5">';
        $html .= sprintf('<li class="mb5">Endereço: %s, %s</li>', $data->pudoEnderecoList[0]->endereco, $data->pudoEnderecoList[0]->numero);
        $html .= sprintf('<li class="mb5">Bairro: %s</li>', $data->pudoEnderecoList[0]->bairro);
        $html .= sprintf('<li class="mb5">Cidade/UF: %s/%s</li>', $data->pudoEnderecoList[0]->cidade, $data->pudoEnderecoList[0]->uf);
        $html .= '</ul>';
        $html .= sprintf('<span class="show">CNPJ: %s</span>', $data->cnpjCpf);
        $html .= '<a class="pull-right btn btn-link btn-small entregar_em_casa" href="javascript: void(0);">Selecionar outro local.</a>';
        $html .= '</div>';
      }
      return $html;
    });

    $html = '';
    $html .= '<div id="AtualizarPudosJadLog">';
    $html .= '<hr/>';
    $html .= $PudoId;
    $html .= '</div>';

    echo $html;

    break;

  case 'PudosJadLog':
    login_existe($_SESSION['cliente']['id_cliente'], URL_BASE . 'identificacao/login?_u=' . URL_BASE . 'identificacao/minha-compra');

    $rws = ClientesEnderecos::first(['conditions' => ['md5(id_cliente)=? and status="ativo"', $_SESSION['cliente']['id_cliente']]]);

    $PickupPoints = new JadLogNew($CONFIG['jadlog']['token']);
    $ReturnPickupPoints = $PickupPoints->post(sprintf('/pickup/pudos/%s', $rws->cep));
    $array = $ReturnPickupPoints['body']->pudos;

    $i = 1;
    $html = '<div class="clearfix ft13px">';
    $html .= '<p class="mb5 text-danger">SELECIONE AONDE DESEJA BUSCAR SEU PEDIDO!</p>';
    $html .= '<div class="row">';
    foreach ($array as $data) {

      $html .= '<div class="col-sm-6 col-xs-12 div-hover-endereco">';
      $html .= '<span class="pull-left mr5" style="width:20px;height:20px;">';
      $html .= sprintf('<input type="radio" name="pudoId" id="pudo%s" value="%s"/>', $data->pudoId, $data->pudoId);
      $html .= sprintf('<label for="pudo%s" class="input-checkbox fa ft22px"></label>', $data->pudoId);
      $html .= '</span>';
      $html .= sprintf('<span class="ft18px mb15 bold show">%s</span>', $data->razao);
      $html .= sprintf('<strong class="ft13px show mb5">%s</strong>', $data->responsavel);
      $html .= '<ul class="ft13px mb5">';
      $html .= sprintf('<li class="mb5">Endereço: %s, %s</li>', $data->pudoEnderecoList[0]->endereco, $data->pudoEnderecoList[0]->numero);
      $html .= sprintf('<li class="mb5">Bairro: %s</li>', $data->pudoEnderecoList[0]->bairro);
      $html .= sprintf('<li class="mb5">Cidade/UF: %s/%s</li>', $data->pudoEnderecoList[0]->cidade, $data->pudoEnderecoList[0]->uf);
      $html .= '</ul>';
      $html .= sprintf('<span class="show">CNPJ: %s</span>', $data->cnpjCpf);
      $html .= '</div>';

      if (($i % 2) == 0)
        $html .= '<div class="col-sm-12 col-xs-12 mb25"></div>';

      $i++;
    }

    $html .= '</div>';
    $html .= '</div>';
    echo $html;
    break;


  case 'facebook-login':
    include dirname(__DIR__) . '/_identificacao/checkout-facebook.php';
    break;

  case 'sair':

    $array = [
      'id_cliente' => !empty($CONFIG['cliente_session']['id']) ? $CONFIG['cliente_session']['id'] : 0,
      'id_session' => session_id(),
      'acao' => 'logout',
      'ip' => retornaIpReal()
    ];
    // Gera um log para o cliente
    $ClientesLogs = ClientesLogs::create($array);

    // Deleta a session do cliente
    if (empty($_SESSION))
      foreach ($_SESSION as $k => $v) {
        foreach ($v as $k1 => $v1) {
          unset($_SESSION[$k][$k1]);
        }
      }
    session_unset();
    session_destroy();

    /**
     * Retorna a pagina de login do site
     */
    if (empty($_SESSION)) {
      if (!empty($GET['_u'])) {
        header('Location: ' . $GET['_u']);
        return;
      }
      header('Location: ' . URL_BASE . 'identificacao/login?_u=' . URL_BASE . 'identificacao/meus-dados');
      return;
    } else {
      header('Location: ' . URL_BASE);
      return;
    }
    break;

  case 'login':
    // include dirname(__DIR__) . '/_identificacao/identificacao-login.php';
    include dirname(__DIR__) . '/_identificacao/checkout-new-login.php';
    break;

  case 'cadastre-se':
  case 'editar-cadastro':
    if ($_SESSION['cliente']['id_cliente']) {
      include dirname(__DIR__) . '/_identificacao/identificacao-menu-cliente.php';
    }
    include dirname(__DIR__) . '/_identificacao/identificacao-cadastro.php';
    break;

  case 'carrinho':
  case 'lista-desejos':
    include dirname(__DIR__) . '/_identificacao/identificacao-carrinho.php';
    break;

  case 'minha-compra':
    login_existe($_SESSION['cliente']['id_cliente'], URL_BASE . 'identificacao/login?_u=' . URL_BASE . 'identificacao/minha-compra');
    include dirname(__DIR__) . '/_identificacao/identificacao-minha-compra.php';
    break;

  case 'meus-pedidos':
    login_existe($_SESSION['cliente']['id_cliente'], URL_BASE . 'identificacao/login?_u=' . URL_BASE . 'identificacao/meus-pedidos');
    include dirname(__DIR__) . '/_identificacao/identificacao-menu-cliente.php';
    include dirname(__DIR__) . '/_identificacao/identificacao-meus-pedidos.php';
    break;

  case 'meus-enderecos':
    login_existe($_SESSION['cliente']['id_cliente'], URL_BASE . 'identificacao/login?_u=' . URL_BASE . 'identificacao/meus-enderecos');
    include dirname(__DIR__) . '/_identificacao/identificacao-menu-cliente.php';
    include dirname(__DIR__) . '/_identificacao/identificacao-meus-enderecos.php';
    break;

  case 'foto':
  case 'minha-senha':
  case 'meus-dados':
    login_existe($_SESSION['cliente']['id_cliente'], URL_BASE . 'identificacao/login?_u=' . URL_BASE . 'identificacao/meus-dados');
    include dirname(__DIR__) . '/_identificacao/identificacao-menu-cliente.php';
    include dirname(__DIR__) . '/_identificacao/identificacao-meus-dados.php';
    break;

  case 'identificacao-endereco-cadastrar_editar':
    include dirname(__DIR__) . '/_identificacao/identificacao-novo-endereco.php';
    break;

  case 'redefinir-senha':
    include dirname(__DIR__) . '/_identificacao/identificacao-senha.php';
    break;

  case 'identificacao-meus-pedidos-detalhes':
    include dirname(__DIR__) . '/_identificacao/identificacao-meus-pedidos-detalhes.php';
    break;

  case 'paga-novamente':
    include dirname(__DIR__) . '/_identificacao/identificacao-paga-novamente.php';
    break;

  case 'fatura-online':
    include dirname(__DIR__) . '/_identificacao/identificacao-fatura-online.php';
    break;

  case 'obrigado':
  case 'finalizado':
    include dirname(__DIR__) . '/_identificacao/identificacao-obrigado.php';
    break;

  case 'questionario':
    include dirname(__DIR__) . '/_identificacao/identificacao-questionario.php';
    break;

    // Tratar os post do PicPay Api
  case 'picpay':
    include dirname(__DIR__) . '/_identificacao/identificacao-picpay.php';
    break;

  case 'checkout-new':

    login_existe($_SESSION['cliente']['id_cliente'], URL_BASE . 'identificacao/checkout-new');

    if (Carrinho::count(['conditions' => ['id_session=?', session_id()]]) == 0) {
      header('Location: ' . URL_BASE);
      return;
    }
    if (!empty($STORE['config']['tpl_pgto'])) {
      include dirname(__DIR__) . '/_identificacao/checkout-new-2.php';
    } else {
      include dirname(__DIR__) . '/_identificacao/checkout-new.php';
    }
    break;



  case 'pagseguro':
    \PagSeguro\Library::initialize();
    \PagSeguro\Library::cmsVersion()->setName("Nome")->setRelease("1.0.0");
    \PagSeguro\Library::moduleVersion()->setName("Nome")->setRelease("1.0.0");

    \PagSeguro\Configuration\Configure::setEnvironment($CONFIG['pagamentos']['pagseguro_mode']); //production or sandbox
    \PagSeguro\Configuration\Configure::setAccountCredentials(
      $CONFIG['pagamentos']['pagseguro_email'],
      $CONFIG['pagamentos']['pagseguro_token']
    );

    $code = 'A545F114-FDC3-427E-8A1B-0907C19C2A20';
    $code = 'D94AD0B4-F15B-49C5-8A23-F5476093B368';

    $options = [
      'initial_date' => '2017-10-01T14:55',
    ];
    $reference = (string)'0000000006';
    try {
      $response = \PagSeguro\Services\Transactions\Search\Code::search(
        \PagSeguro\Configuration\Configure::getAccountCredentials(),
        $code
      );

      echo '<pre>';
      print_r($response->getPaymentMethod()->getType());
      print_r($response->getPaymentMethod()->getCode());
      print_r($response->getStatus());
      //            print_r($response);
      echo '</pre>';
    } catch (Exception $e) {
      echo '<pre>';
      print_r($e);
      echo '</pre>';
    }

    //        $name = "pagseguro.txt";
    //        $text = var_export($POST, true);
    //        $file = fopen($name, 'a');
    //        fwrite($file, $text);
    //        fclose($file);
    break;

    // Integração via boleto BoletoFacil
  case 'returnbolets':

    $PaymentToken = filter_input(INPUT_POST, 'paymentToken');

    if (!isset($PaymentToken)) return false;

    $BoletoFacil = new BoletoFacil\BoletoFacil($CONFIG['boleto_token'], ($CONFIG['boleto_mode'] == 0 ? true : false));

    $Json = $BoletoFacil->fetchPaymentDetails($PaymentToken);

    $return = current(json_decode($Json));

    // condicoes para a busca
    $conditions = ['conditions' => ['code=?', $return->payment->charge->code]];

    // verifica a existencia do pedidos boletos no sistema
    if (PedidosBoletos::count($conditions) > 0) {

      $PedBoletos = PedidosBoletos::first($conditions);

      $Ped = Pedidos::find($PedBoletos->pedidos_id);
      $Ped->status = ('CONFIRMED' == $return->payment->status ? '3' : '10');
      $Ped->save();

      $Logs = new PedidosLogs();
      $Logs->id_adm = 0;
      $Logs->id_pedido = $PedBoletos->pedidos_id;
      $Logs->descricao = ('CONFIRMED' == $return->payment->status ? 'Pagamento via Boleto confirmado' : 'Pagamento via Boleto cancelado');
      $Logs->data_envio = date('Y-m-d H:i:s');
      $Logs->status = ('CONFIRMED' == $return->payment->status ? '3' : '10');
      $Logs->save();
    }

    break;
}

include sprintf('%srodape.php', URL_VIEWS_BASE);
