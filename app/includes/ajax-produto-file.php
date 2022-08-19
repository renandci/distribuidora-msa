<?php

/**
 * switch verificao da ação dos dados
 */
switch ($POST['acao']) {
    /**
   * Envia a foto do usuario para o seridor
   * Os arquivos serão salvos no na pasta imgs/temp
   */
  case 'foto_enviar':

    $json['error'] = null;
    $json['mensagem'] = null;

    $dir_temp = URL_VIEWS_BASE_PUBLIC_UPLOAD . '/temp/';

    // Verifica se o caminho existe
    if (!is_dir($dir_temp)) {
      // Tenta criar um diretorio de caminho
      if (!mkdir($dir_temp)) {
        $json['error'] = 1;
        $json['mensagem'] = 'Não foi possivel criar um diretorio: ' . $dir_temp;
      }
    }

    // se existir erros
    // break codigo
    if (!empty($json['error']) && $json['error'] === 1) {
      exit(json_encode($json));
    }

    // Arquivo file
    $file = current($_FILES);

    // Verificar se é uma requisição via HTTP FILES
    if (is_uploaded_file($file['tmp_name'])) {
      // extensão pré carregada
      $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
      $ext_pre = ['png', 'jpeg', 'gif', 'jpg'];

      // tenta recarrregar a imagem do srvidor
      $name_glob = glob($dir_temp . session_id() . '.{jpg,jpeg,png,gif}', GLOB_BRACE);
      $name_file_temp = end($name_glob);

      // verifica se há imagem já upada no servidor
      if (count($name_glob) > 0) {
        // verfica se realmente existe
        if (file_exists($name_file_temp)) {
          // pega a extensao do arquivo
          $ext_temp = strtolower(pathinfo($name_file_temp, PATHINFO_EXTENSION));

          // se as extensões forem diferentes
          // tenta remover a antiga do servidor
          if ($ext_temp !== $ext) {
            if (!unlink($name_file_temp)) {
              $json['mensagem'] .= '<p>Não consegui remover sua imagem!</p>';
            }
          }
        }
      }

      // Verifica a extensão do arquivo
      if (!in_array($ext, $ext_pre)) {
        $json['error'] = 1;
        $json['mensagem'] = ''
          . "Não é permitido arquivos <b>{$ext}</b><br/>"
          . 'Tente enviar os seguintes tipo de arquivos: '
          . "<span class='show ft16px'><b>" . join($ext_pre, '</b> <b>') . "<b></span>";
        exit(json_encode($json));
      }

      switch ($file['error']) {
        case UPLOAD_ERR_OK:
          break;
        case UPLOAD_ERR_NO_FILE:
          exit(json_encode(['mensagem' => 'Nenhum arquivo enviado.', 'error' => '1']));
          break;
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
          exit(json_encode(['mensagem' => 'Limite de tamanho de arquivo excedido.', 'error' => '1']));
          break;
        default:
          exit(json_encode(['mensagem' => 'Erros desconhecidos.', 'error' => '1']));
          break;
      }

      // cria um nome temporario com o session_id do sistema
      // o ultimo numero corrente será a hora que ele terá de vida
      $temp_name = session_id();
      $name_file = "{$temp_name}.{$ext}";

      // tenta enviar o arquivo para o servidor
      if (!move_uploaded_file($file['tmp_name'], $dir_temp . $name_file)) {
        $json['error'] = 1;
        $json['mensagem'] = 'Não consegui enviar o arquivo agora. Tente novamente mais tarde.';
      }
    }

    // recarrega a imagem novamente
    $name_file = end(explode('/', current(glob($dir_temp . session_id() . '.{jpg,jpeg,png,gif}', GLOB_BRACE))));
    // version img
    $version = implode('.', str_split(strrev(substr(time(), -2))));
    $json['mensagem'] = ''
      . '<div class="row text-center">'
      . '<div class="col-md-12 col-sm-12 col-xs-12">'
      . '<img src="' . Imgs::src($name_file, 'imgstemp') . '?v=' . $version . '" class="img-responsive center-block mb15">'
      . '</div>'
      . '<div class="col-md-12 col-sm-12 col-xs-12">'
      . '<a href="/personalize" class="btn btn-success btn-animated" onclick="javascript: window.location.href=\'/personalize\';">'
      . '<span>prosseguir</span>'
      . '<span>prosseguir</span>'
      . '</a> '
      . '<button type="button" class="btn btn-edit btn-animated" '
      . 'onclick="$(\'#enviar-foto\').trigger(\'click\');"><span>trocar imagem</span><span>trocar imagem</span></button> '
      . '<button type="button" class="btn btn-remove btn-animated" id="foto_remover"><span>remover</span><span>remover</span></button>'
      . '</div>'
      . '</div>';
    exit(json_encode($json, JSON_UNESCAPED_UNICODE));
    break;

    // Deve se remover a imagem do servidor
  case 'foto_remover':

    $json['mensagem'] = '';

    // tenta remover a imagem do srvidor
    $dir_temp = URL_VIEWS_BASE_PUBLIC_UPLOAD . 'temp/';
    $name_file = current(glob($dir_temp . session_id() . '.{jpg,jpeg,png,gif}', GLOB_BRACE));
    if (file_exists($name_file)) {
      // remove o arquivo
      if (!unlink($name_file)) {
        $json['mensagem'] .= '<p>Não consegui remover sua imagem!</p>';
      } else {
        $json['mensagem'] .= '<p>Imagem removida com sucesso!</p>';
      }
    } else {
      $json['mensagem'] .= '<p>Sua imagem já foi removida!</p>';
    }

    // tenta remove o carrinho de compra
    if (Carrinho::count(['id_session' => session_id()]) > 0) {
      $json['mensagem'] .= '<p>Também removi seu carrinho!</p>';
    }

    exit(json_encode($json, JSON_UNESCAPED_UNICODE));
    break;

    // carrega as informacoes para o carrinho
  case 'foto_personalizar':

    // Dados de personalização
    $data = $POST;

    // remove o campo acao com base no nome do campo
    $key = array_search('foto_personalizar', $data);
    if ($key !== false) {
      unset($data[$key]);
    }

    if (count($data) > 0) :
      // tenta remover a imagem do srvidor
      $dir_temp = URL_VIEWS_BASE_PUBLIC_UPLOAD . 'temp/';
      // nome da imagem
      $name_file = current(glob($dir_temp . session_id() . '.{jpg,jpeg,png,gif}', GLOB_BRACE));

      $Json = [['Imagem' => $name_file] + $data];

      $PERSONALIZADO = json_encode($Json, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    else :
      $PERSONALIZADO = null;
    endif;

    // remove os caracter citados abaixo
    // $PERSONALIZADO = str_replace(['2°', '3°', '4°', '5°', '_-_', '/'], ['', '', '', '', '', '_'], $PERSONALIZADO);
    $PERSONALIZADO = str_replace(['2°', '3°', '4°', '5°', '_-_'], ['', '', '', '', ''], $PERSONALIZADO);


    // // Gerar teste para ver se vetor esta correto
    // exit( $PERSONALIZADO );

    /**
     * Vamos pegar o produto escolhido para retornar a sua categoria.
     * Sendo assim o botao continuar comprando retorna a ultima pagina visualizada.
     */
    $sql_retorno = ''
      . 'select '
      . 'pr.id, '
      . 'g.id as grupo_id, '
      . 'g.grupo, '
      . 'sg.id as subgrupo_id, '
      . 'sg.subgrupo, '
      . 'pr.nome_produto '
      . 'from produtos pr '
      . 'left join produtos_menus prm on pr.codigo_id = prm.codigo_id '
      . 'left join grupos g on g.id = prm.id_grupo '
      . 'left join subgrupos sg on sg.id = prm.id_subgrupo '
      . 'where pr.nome_produto = ? '
      . 'group by pr.codigo_id';

    $Produto = current(Produtos::find_by_sql($sql_retorno, array($POST['Plano'])))->to_array();
    $retornar = isset($Produto['subgrupo_id']) && $Produto['subgrupo_id'] > 0
      ? URL_BASE . 'produtos/' . converter_texto($Produto['grupo']) . '/' . $Produto['grupo_id'] . '/' . converter_texto($Produto['subgrupo']) . '/' . $Produto['subgrupo_id']
      : URL_BASE . 'produtos/' . converter_texto($Produto['grupo']) . '/' . $Produto['grupo_id'];

    $json['mensagem'] = ''
      . '<div class="text-center clearfix">'
      . '<div class="ft20px mb5">'
      . 'Dados personalizado com sucesso'
      //                    . '<span id="data-nome-produto">' . ( isset($Produto['nome_produto']) && $Produto['nome_produto']!= '' ? $Produto['nome_produto'] : '') . '</span> adicionado!'
      . '</div>'
      . '<p>O que deseja fazer agora!</p>'
      . '<div class="clearfix">'

      //                    . '<a href="javascript:void(0)" class="btn btn-primary-default btn-xs btn-block mb15" onclick="window.location.href=\''.$retornar.'\'">'
      //                        . '<i class="fa fa-2x fa-shopping-cart"></i> '
      //                        . '<span class="ft28px">continuar comprando</span>'
      //                    . '</a>'

      . '<a href="' . URL_BASE . 'identificacao/login?_u=' . URL_BASE . 'identificacao/checkout-new" class="btn btn-primary btn-xs btn-block">'
      . '<i class="fa fa-2x fa-credit-card"></i> '
      . '<span class="ft28px">finalizar compra</span>'
      . '</a>'
      . '</div>'
      . '</div>';

    if (!isset($_SESSION['carrinho'])) {
      $_SESSION['carrinho'] = [
        'id_session' => session_id(),
        'quantidade' => 1,
        'personalizado' => $PERSONALIZADO,
        'cliente_ip' => retornaIpReal(),
        'id_cupom' => 0,
        'frete_tipo' => '',
        'frete_valor' => '0.00',
        'cep' => '',
      ];
    } else {
      array_merge($_SESSION['carrinho'], [
        'id_session' => session_id(),
        'quantidade' => 1,
        'personalizado' => $PERSONALIZADO,
        'cliente_ip' => retornaIpReal(),
        'id_cupom' => 0,
        'frete_tipo' => '',
        'frete_valor' => '0.00',
        'cep' => '',
      ]);
    }

    $Carrinho = Carrinho::update_all(
      array(
        'set' => array(
          'quantidade' => 1,
          'personalizado' => $PERSONALIZADO,
          'cliente_ip' => retornaIpReal(),
          'id_cupom' => 0,
          'frete_tipo' => '',
          'frete_valor' => '0.00',
          'cep' => ''
        ),
        'conditions' => array('id_session=?', session_id())
      )
    );

    $json['mensagem'] = ''
      . 'Redirecionando, só um instante...'
      . '<script>'
      //            . 'window.location.href="'.URL_BASE.'identificacao/login?_u='.URL_BASE.'identificacao/checkout-new";'
      . 'window.location.href="/personalize-session/?session_id=' . session_id() . '&_u=' . URL_BASE . 'identificacao/checkout-new";'
      . '</script>';
    if (!empty($Carrinho)) {
    } else {

      $Cart = new Carrinho();
      $Cart->id_session = session_id();
      $Cart->id_produto = $Produto['id'];
      $Cart->quantidade = 1;
      $Cart->cliente_ip = retornaIpReal();
      $Cart->personalizado = $PERSONALIZADO;
      try {
        $Cart->save();
      } catch (Exception $ex) {
        $json['mensagem'] = 'Não foi possivél inserir o produto no carrinho.';
      }
    }

    exit(json_encode($json, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    break;
}
