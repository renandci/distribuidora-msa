<?php

use PagSeguro\Configuration\Configure;
// pego na url do .htaccess
$MODULO_GET = current(explode('/', filter_input(INPUT_GET, 'modulo')));

$auto_connections = [
  'production' => "mysql://root:root@192.168.57.16/edistribu?charset=utf8",
  'development' => 'mysql://root:root@localhost/edistribu?charset=utf8',
  'distribuidoramsa' => 'mysql://root:root@localhost/edistribu?charset=utf8',
];

// Modo para ativar o cliente direto como include
define('CLIENTE_ID', (!empty($_SESSION['cliente']['id_cliente']) ? $_SESSION['cliente']['id_cliente'] : md5(0)));
define('SESSION_ID', session_id());

// Para evitar mostrar dados a partir desse caminho novo
try {

  $cfg = ActiveRecord\Config::instance();
  $cfg->set_model_directory(sprintf('%sapp/models', PATH_ROOT));
  $cfg->set_connections(['development' => (strlen(strstr(SERVER_NAME, '.test')) > 0 ? $auto_connections['distribuidoramsa'] : $auto_connections['production'])]);

  $Lojas = Lojas::find(ASSETS);

  $Configuracoes = Configuracoes::find(['conditions' => ['loja_id=?', $Lojas->id]]);

  $CONFIG = $Configuracoes->to_array([
    'include' => [
      'lojas' => [
        'include' => [
          'plano'
        ]
      ],
      'pagamentos',
      'fretes_envios',
      'transferencias',
      'correios' => ['include' => []],
      'jadlog',
      // 'produtos',
      'banners',
      'paginas' => ['order' => 'ordem asc'],
      'questionario',
      'skyhub',
      'cliente_session' => [
        'include' => [
          'endereco',
          'enderecos',
          'pedidos' => [
            'include' => [
              'pedido_endereco',
              'correio_etiqueta',
              'jadlog_etiqueta',
              'pedidos_logs',
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
              'pedido_transacoes'
            ]
          ]
        ]
      ],
    ],
  ]);

  $ConditionsCart = [];
  $ConditionsCart['select'] = ''
    . 'SQL_CACHE carrinho.id, '
    . 'carrinho.loja_id, '
    . 'carrinho.id_session, '
    . 'carrinho.id_produto, '
    . 'carrinho.id_cupom, '
    . 'produtos.id_marca, '
    . 'produtos.codigo_id, '
    . 'produtos.preco_venda, '
    . 'produtos.preco_promo, '
    . 'produtos.nome_produto, '
    . 'produtos.subnome_produto, '
    . 'produtos.codigo_produto, '
    . 'produtos.estoque, '
    . 'produtos.status, '
    . 'carrinho.quantidade, '
    . 'carrinho.frete_tipo, '
    . 'carrinho.frete_valor, '
    . 'carrinho.frete_prazo, '
    . 'carrinho.pedidos_id, '
    . 'carrinho.cliente_tmp, '
    . 'carrinho.cliente_obs, '
    . 'carrinho.personalizado, '
    . 'carrinho.jadlog_pudoid, '
    . 'cupons.cupom_desconto, '
    . 'cupons.cupom_value, '
    . 'cupons.cupom_codigo, '
    . 'cores.nomecor, '
    . 'A.tipo as tipocores, '
    . 'tamanhos.nometamanho, '
    . 'B.tipo as tipotamanhos, '
    . 'produtos_imagens.imagem, '
    . '(SELECT SUM(prod.preco_promo * car.quantidade) as T FROM carrinho car JOIN produtos prod ON car.id_produto = prod.id WHERE car.id_session = carrinho.id_session) as valorcompra '
    . '';

  $ConditionsCart['joins'] = ''
    . 'JOIN produtos ON produtos.id = carrinho.id_produto '
    . 'JOIN cores ON cores.id = produtos.id_cor '
    . 'JOIN tamanhos ON tamanhos.id = produtos.id_tamanho '
    . 'JOIN opcoes_tipo A ON A.id = cores.opcoes_id '
    . 'JOIN opcoes_tipo B ON B.id = tamanhos.opcoes_id '
    . 'JOIN cupons ON cupons.id = carrinho.id_cupom '
    . 'JOIN produtos_imagens ON produtos_imagens.codigo_id = produtos.codigo_id AND produtos_imagens.cor_id = produtos.id_cor '
    . '';

  $ConditionsCart['group'] = 'carrinho.id';
  $ConditionsCart['conditions'] = ['carrinho.id_session=?', SESSION_ID];

  $CarrinhoViewsTemp = Carrinho::all($ConditionsCart);

  $CarrinhoViewsTempCount = (int)count($CarrinhoViewsTemp);
  $CONFIG['carrinho_all'] = $CarrinhoViewsTempCount > 0 ? $CarrinhoViewsTemp : [];

  $CONFIG['date'] = date('Y-m-d');
  $CONFIG['date_time'] = date('Y-m-d H:i:s');
  $CONFIG['timestamp'] = strtotime('now');

  $CONFIG['qtde_parcelas']  = $CONFIG['pagamentos']['qtde_parcelas'] != '' ? $CONFIG['pagamentos']['qtde_parcelas'] : 1;
  $CONFIG['parcela_minima'] = $CONFIG['pagamentos']['parcela_minima'] != '' ? $CONFIG['pagamentos']['parcela_minima'] : 1.00;
  $CONFIG['desconto_boleto'] = $CONFIG['pagamentos']['desconto_boleto'] != '' ? $CONFIG['pagamentos']['desconto_boleto'] : 1;

  $CONFIG['session_id'] = SESSION_ID;
  $CONFIG['cliente_id'] = !empty($_SESSION['cliente']['id_cliente']) ? $_SESSION['cliente']['id_cliente'] : null;
  $CONFIG['cliente_name'] = !empty($_SESSION['cliente']['id_cliente']) ? $_SESSION['cliente']['nome'] : null;

  // Habilita atacadista na loja toda
  $CONFIG['atacadista']     = (!empty($CONFIG['cliente_session']['id']) ? $CONFIG['cliente_session']['atacadista_desconto'] : 0);
  $CONFIG['atacadista_min'] = (!empty($CONFIG['cliente_session']['id']) ? $CONFIG['cliente_session']['atacadista_min'] : '0.00');
  $CONFIG['atacadista_max'] = (!empty($CONFIG['cliente_session']['id']) ? $CONFIG['cliente_session']['atacadista_max'] : '0.00');

  // Teste para desempenho
  $CONFIG['preLoadSubMenus'] = md5('preLoadSubMenus');
  $CONFIG['preLoadSubMenusPost'] = filter_input(INPUT_POST, 'preLoadSubMenus');
} catch (PDOException $e) {
  header('HTTP/1.1 400 BAD REQUEST');
  if (strlen(strstr(SERVER_NAME, '.test')) == 0) printf('<!--[<pre>%s</pre>]-->', print_r($e, 1));

  $msg = $e->getMessage();
  exit('Você1 não tem permissão de acesso<hr/>' . $msg);
} catch (Exception $e) {
  header('HTTP/1.1 400 BAD REQUEST');
  if (strlen(strstr(SERVER_NAME, '.test')) == 0) printf('<!--[<pre>%s</pre>]-->', print_r($e, 1));

  $msg = $e->getMessage();
  exit('Você2 não tem permissão de acesso<hr/>' . $msg);
} finally {
}

$UA_INFO = \donatj\UserAgent\parse_user_agent();
$MobileDetect = new Mobile_Detect();

// Personaliza a tela de dizeres dos precos dos produtos
$STORE['personalize_class'] = new HelperHtml();
$STORE['personalize_price'] = 'template_set_price';
$STORE['personalize_price_view_product'] = 'template_set_view_product_price';

$STORE['personalize_cols_index'] = 'col-lg-3 col-md-3 col-sm-6 col-xs-6';
$STORE['personalize_cols_product'] = 'col-lg-4 col-md-4 col-sm-6 col-xs-6';

$STORE['lg_line'] = 3;
$STORE['xs_line'] = 2;

// Modulo de dataLayer
$STORE['dataLayer']['id'] = $CONFIG['google_tag_manager'];
$STORE['dataLayer']['title'] = '';
$STORE['dataLayer']['modulo'] = '';
$STORE['dataLayer']['produto'] = '';
$STORE['dataLayer']['venda_cod'] = '';
$STORE['dataLayer']['venda_total'] = '';
$STORE['dataLayer']['frete_valor'] = '';
$STORE['dataLayer']['boleto_desc'] = '';

$STORE['personalize_box_color_top'] = false;
$STORE['personalize_box_color_bottom'] = false;
$STORE['frete_prod'] = true;

// $CONFIG['loja_id'] = $CONFIG['loja_id'] != '0' ? $CONFIG['loja_id'] : 0;

// Ajusta os estoques das cores
$CONFIG['stock_all'] = false;
$STORE['config']['url'] = $HTTP_HTTPS . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

// Include as configurações para cada loja
$STORE['config'] = ($STORE['config'] + current(include dirname(__FILE__) . '/settings.inc'));

$STORE['button_whatsapp'] = function ($tel = '', $text = '') {
  $tel = soNumero($tel, 1);
  if ($tel >= 11)
    return sprintf('<a href="https://wa.me/55%s?text=%s" target="_blank" class="ml5 mr5 fa fa-whatsapp"></a>', $tel, $text);
};

// Somente para os cadastro de Clientes
foreach ($STORE['config']['cadastro'] as $name => $array) {
  if ($array['required'] == 1) {

    Clientes::$validates_presence_of[] = [$name, 'message' => 'text_required'];

    if ($name == 'nome') {
      Clientes::$validates_format_of[] = [$name, 'with' => '/[A-zÀ-ú\']{1,}\s[A-zÀ-ú\']{2,}\'?-?[A-zÀ-ú\']{1,}\s?([A-zÀ-ú\']{2,})?/', 'message' => 'text_required_nome_invalid'];
    }

    if ($name == 'telefone') {
      Clientes::$validates_format_of[] = [$name, 'with' => '/^(\(?\d{2}\)?) ?9?\d{4}-?\d{4}$/', 'message' => 'text_required_tel_invalid'];
    }

    if ($name == 'celular') {
      Clientes::$validates_format_of[] = [$name, 'with' => '/^(\(?\d{2}\)?) ?9?\d{4}-?\d{4}$/', 'message' => 'text_required_cel_invalid'];
    }

    // if( $name == 'cpfcnpj' ) {
    //     Clientes::$validates_format_of[] = [
    // 		// $name, 'with' => '/^(([0-9]{3}.[0-9]{3}.[0-9]{3}-[0-9]{2})|([0-9]{11}))$/', 'message' => 'text_required_cpfcnpj'
    // 		// $name, 'with' => '/(^\d{3}\.\d{3}\.\d{3}\-\d{2}$)|(^\d{2}\.\d{3}\.\d{3}\/\d{4}\-\d{2}$)/', 'message' => 'text_required_cpfcnpj'
    // 		// $name, 'with' => '/^([0-9]{3}\.?[0-9]{3}\.?[0-9]{3}\-?[0-9]{2}|[0-9]{2}\.?[0-9]{3}\.?[0-9]{3}\/?[0-9]{4}\-?[0-9]{2})$/', 'message' => 'text_required_cpfcnpj'
    // 		// $name, 'with' => '/(^(\d{3}.\d{3}.\d{3}-\d{2})|(\d{11})$)|(^(\d{2}.\d{3}.\d{3}/\d{4}-\d{2})|(\d{14})$)/', 'message' => 'text_required_cpfcnpj'
    // 		// $name, 'with' => '\d{2}.?\d{3}.?\d{3}/?\d{4}-?\d{2}', 'message' => 'text_required_cpfcnpj'
    // 		$name, 'with' => '/^([0-9]{3}\.?[0-9]{3}\.?[0-9]{3}\-?[0-9]{2}|[0-9]{2}\.?[0-9]{3}\.?[0-9]{3}\/?[0-9]{4}\-?[0-9]{2})$/', 'message' => 'text_required_cpfcnpj'
    // 	];
    // }
  }
}

// Somente para o cadastro de endereco
foreach ($STORE['config']['endereco'] as $name => $array) {
  if (!empty($array['required']) && $array['required'] == 1) {
    ClientesEnderecos::$validates_presence_of[] = [$name, 'message' => 'text_required'];

    if ($name == 'cep')
      ClientesEnderecos::$validates_format_of[] = [$name, 'with' => '/^[0-9]{5,5}([-]?[0-9]{3})$/', 'message' => 'text_required_cep'];

    if ($name == 'numero')
      ClientesEnderecos::$validates_format_of[] = [$name, 'with' => '/^([0-9]+)$/', 'message' => 'text_required_num'];
  }
}

// printf('<pre>%s</pre>', print_r($CONFIG['pagamentos']['pagarme_boleto'], 1));
// die;
