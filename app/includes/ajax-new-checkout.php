<?php
$ErrorCheckoutCadastrarEditarAll = null;
// echo Bcrypt::hash( 'data@123' );

$STORE['config']['cadastro']['nome']['text_required_nome_invalid'] = 'Nome inválido, verifique seu nome!';
$STORE['config']['cadastro']['email']['text_required_unic'] = 'Esse e-mail já está cadastrado!';
$STORE['config']['cadastro']['cpfcnpj']['text_required_cpfcnpj'] = 'Seu CPF ou CNPJ é inválido!';

$STORE['config']['cadastro']['telefone']['text_required_tel_invalid'] = 'O número do telefone é inválido!';
$STORE['config']['cadastro']['celular']['text_required_cel_invalid'] = 'O número do celular é inválido!';

$STORE['config']['cadastro']['senha_real']['text_required'] = 'Campo senha é obrigatório!';
$STORE['config']['cadastro']['senha_confirm']['text_required'] = 'Campo senha é obrigatório!';

$STORE['config']['cadastro']['senha_real']['text_required_strlen'] = 'Senha requer de 4 a 12 caracteres!';
$STORE['config']['cadastro']['senha_confirm']['text_required_strlen'] = 'Senha requer de 4 a 12 caracteres!!';

$STORE['config']['cadastro']['senha_real']['text_required_identic_a'] = 'A senha de acesso não conferem com a senha abaixo!';
$STORE['config']['cadastro']['senha_confirm']['text_required_identic_b'] = 'A senhas não conferem com a senha de acesso!';

$STORE['config']['endereco']['numero']['text_required_num'] = 'Somente números!';
$STORE['config']['endereco']['numero']['text_required_cep'] = 'Cep inválido!';

$NEW_ACAO = filter_input(INPUT_POST, 'acao', FILTER_SANITIZE_STRING);

switch ($NEW_ACAO):
  case 'BuscaCidade':
  case 'BuscarCidades':
    $CEP = soNumero($POST['cep']);
    $CorreiosCepReal = new PhpSigep\Services\SoapClient\Real();
    $CorreiosCep   = $CorreiosCepReal->consultaCep($CEP);

    $str['endereco'] = $CorreiosCep->getResult()->getEndereco();
    $str['bairro']    = $CorreiosCep->getResult()->getBairro();
    $str['cidade']    = $CorreiosCep->getResult()->getCidade();
    $str['uf']      = $CorreiosCep->getResult()->getUf();
    $str['cep']    = $CorreiosCep->getResult()->getCep();

    exit(json_encode($str));
    break;

  case 'VerificarCadastroDeEmail':

    $isEmail = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);

    $post = ['email' => $isEmail];

    $Clientes = Clientes::count(['conditions' => ['loja_id=? and email=?', $CONFIG['loja_id'], $isEmail]]);

    if ($Clientes > 0) {
      $ErrorCheckoutCadastrarEditarAll['cadastro']['email'] = ''
        . '<span>'
        . '<span id="cadastro[email]-error" class="text-danger ft12px">'
        . $STORE['config']['cadastro']['email']['text_required_unic']
        . '</span>'
        . '<script>$("input#email").parent().parent().addClass("has-error");</script>'
        . '</span>';
    } else {
      $ErrorCheckoutCadastrarEditarAll['cadastro']['email'] = ''
        . '<span>'
        . '<span id="cadastro[email]-error" class="text-danger ft12px">'
        . '</span>'
        . '<script>$("input#email").parent().parent().removeClass("has-error");</script>'
        . '</span>';
    }

    break;

  case 'TrocarSenha':
  case 'trocar-senha':

    $senha   = Bcrypt::hash($POST['senha1']);

    $Clientes = Clientes::first(['conditions' =>  ['md5(id)=?', $_SESSION['cliente']['id_cliente']]]);

    if (Bcrypt::check($POST['senhaantiga'], $Clientes->senha)) {
      $Clientes->senha = $senha;
      // verificar se a senha real existe
      $Clientes->senha_real = $POST['senha1'];
      // e em seguida checa se ela esta vazia
      $Clientes->senha_confirm = $POST['senha2'];

      try {
        $is = $Clientes->save();

        $str['msg'] = ''
          . 'Senha alterada com sucesso...'
          . '<script>window.location.href="' . URL_BASE . 'identificacao/meus-dados";</script>';
      } catch (Exception $e) {
        $str['msg'] = ''
          // . '<pre>'
          // . @var_export($e, 1)
          // . '</pre>'
          . 'Não foi possivel alterar sua senha!';
      }
    } else {
      $str['msg'] = 'Sua senha antiga não está correta...';
    }
    die(json_encode($str));
    break;

  case 'redefinirSenhaUsusario':

    $token = filter_input(INPUT_GET, 'token', FILTER_SANITIZE_STRING);

    $ClientIdExplode = explode('&cliente_id=', base64_decode($token));

    $ClientId = end($ClientIdExplode);

    $senha = $POST['senha1'];

    $str['msg'] = '';

    try {
      $Clientes = Clientes::first(['conditions' =>  [' sha1(id) = ? and loja_id = ?', $ClientId, $CONFIG['loja_id']]]);

      $params['id'] = $Clientes->id;

      // verificar se a senha real existe
      $params['senha_real'] = $senha;

      // e em seguida checa se ela esta vazia
      $params['senha_confirm'] = $senha;

      $params['senha'] = Bcrypt::hash($senha);

      $return = $Clientes->new_save($params);

      if (empty($return['id'])) {
        foreach ($return as $column_name => $error) {
          $str['msg'] .= ''
            . '<span>'
            . sprintf('<span id="cadastro[%s]-error" class="input-error-span-2 text-right">', $column_name)
            . $STORE['config']['cadastro'][$column_name][$error]
            . '</span>'
            . sprintf('<script>$("input#%s").parent().parent().addClass("new-checkout-error").removeClass("new-checkout-ok");</script>', $column_name)
            . '<span>';
        }
      } else {
        $str['msg'] = ''
          . 'Senha alterada com sucesso...'
          . '<script>'
          . sprintf('window.location.href="%sidentificacao/login/?_u=%sidentificacao/meus-dados";', URL_BASE, URL_BASE)
          . '</script>';
      }
    } catch (Exception $ex) {
      $str['msg'] = 'Algo deu errado, tente novamente!...';
    }

    exit(json_encode($str));
    break;

  case 'RedefinirSenha':

    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    if (Clientes::count(['conditions' => ['email=? and loja_id=?', $email, $CONFIG['loja_id']]]) > 0) {
      $sss = recuperar_senha($email);
      if ($sss) {
        $str['msg'] = ''
          . 'Uma solicitação de redefinição de senha foi enviada para seu e-mail.';
      } else {
        $str['msg'] = ''
          . 'Algo deu errado, tente novamente!';
      }
    } else {
      $str['msg'] = ''
        . 'Você não está cadastrado com esse e-mail, tente outro ou cadastre-se!'
        . '<center>'
        . '<span class="btn btn-primary btn-small mt5" onclick=\"$(\'#from-recuperar-senha\').fadeIn(0),$(\'.cx-error\').fadeOut(0);\">'
        . 'voltar'
        . '</span>'
        . '</center>';
    }
    die(json_encode($str));
    break;

  case 'FazerLogOut':

    $array = [
      'id_cliente' => current(Clientes::all(['conditions' => ['md5(id)=?', $_SESSION['cliente']['id_cliente']]]))->id,
      'id_session' => SESSION_ID,
      'acao' => 'logout',
      'ip' => retornaIpReal()
    ];
    // Gera um log para o cliente
    $ClientesLogs = ClientesLogs::create($array);
    if ($ClientesLogs) {
      // Deleta a session do cliente
      foreach ($_SESSION as $k => $v) {
        foreach ($v as $k1 => $v1) {
          unset($_SESSION[$k][$k1]);
        }
      }
      session_unset();
      session_destroy();
    }

    /**
     * Retorna a pagina de login do site
     */
    if (empty($_SESSION)) {
      header('Location: ' . URL_BASE . 'identificacao/login?_u=' . URL_BASE . 'identificacao/meus-dados');
      return;
    }

    break;

    // Edita ou cadastra os cliente no sistema a partir do cadastro comum do sistema.
  case sha1('CadastroCadastrarEditar'):
    $params = array();

    $array_clientes = array();

    $array_enderecos = array();

    $ErrorCheckoutCadastrarEditarAll = array();

    $URL_DEFINE = filter_input(INPUT_GET, '_u', FILTER_SANITIZE_STRING);

    $URL_ATACADISTA = filter_input(INPUT_GET, '_atacadista', FILTER_SANITIZE_STRING);

    $cadastro = $POST['cadastro'];

    $endereco = $POST['endereco'];

    $post = ($cadastro + ['endereco' => (object)$endereco]);

    if (isset($_SESSION['cliente']['id_cliente']) && $_SESSION['cliente']['id_cliente'] != '') :
      $Clientes = Clientes::first(['conditions' => ['md5(id)=?', $_SESSION['cliente']['id_cliente']]]);
      $params['id'] = $Clientes->id;
    else :
      $Clientes = new Clientes();
      $Enderecos = new ClientesEnderecos();
    endif;

    $params['ip'] = retornaIpReal();
    $params['loja_id'] = $CONFIG['loja_id'];
    $params['atacadista'] = $URL_ATACADISTA ? 1 : 0;

    // verificar se a senha real existe
    // e em seguida checa se ela esta vazia
    $params['senha_real'] = isset($cadastro['senha_real']) && !empty($cadastro['senha_real']) ? $cadastro['senha_real'] : '#1234#@';

    // verificar se a senha real existe
    // e em seguida checa se ela esta vazia
    $params['senha_confirm'] = isset($cadastro['senha_confirm']) && !empty($cadastro['senha_confirm']) ? $cadastro['senha_confirm'] : '#1234#@';

    // unset($cadastro['senha_real'], $cadastro['senha_confirm']);

    foreach ($cadastro as $name => $values) {
      $params[$name] = addslashes($values);
    }

    if (isset($params['senha_real']) && $params['senha_real'] !== '#1234#@') {
      $params['senha'] = Bcrypt::hash($params['senha_real']);
    }

    $array_clientes = $Clientes->new_save($params);

    // Somente para o cadastro
    if (count($endereco) > 0) {
      $endereco['status'] = 'ativo';
      $endereco['id_cliente'] = (int)@$array_clientes['id'];
      foreach ($endereco as $name => $values) {
        $params[$name] = addslashes($values);
      }

      $array_enderecos = $Enderecos->new_save($params);
    }

    $return = ($array_clientes + $array_enderecos);

    if (isset($array_clientes['id']) && $array_clientes['id'] > 0) {

      if ($post['atacadista']) {
        $ErrorCheckoutCadastrarEditarAll['cadastro']['error'] = ''
          . '<span id="location_href">'
          . 'Cadastro realizado com sucesso!<br/>'
          . 'Entraremos em contato para efetivar seu e-mail de cadastro.'
          . '<script>$("input[name]").val("");</script>'
          . '<div class="text-center mt5"><a href="/" class="btn btn-primary">voltar ao site</a></div>'
          . '</span>';
      } else {
        // Tenta salvar a Indicação do novo cliente
        if (isset($cadastro['indicacao']) && $cadastro['indicacao'] != '') {
          ClientesIndicacoes::create([
            'id_pedido' => 0,
            'id_cliente' => $array_clientes['id'],
            'id_session' => SESSION_ID,
            'indicacao' => $cadastro['indicacao'],
            'outros' => $cadastro['outros']
          ]);
        }

        $name_last = explode(' ', $params['nome']);
        $_SESSION['cliente']['nome'] = reset($name_last);
        $_SESSION['cliente']['email'] = $params['email'];
        $_SESSION['cliente']['id_cliente'] = md5($return['id']);

        if (!empty($URL_DEFINE)) {
          $ErrorCheckoutCadastrarEditarAll['cadastro']['error'] = ''
            . '<span id="location_href">'
            . 'Salvo com sucesso!'
            . sprintf('<script>window.location.href="%s";</script>', $URL_DEFINE)
            . '</span>';
          // header('Location: ' . $URL_DEFINE);
          // return;
        } else {
          $ErrorCheckoutCadastrarEditarAll['cadastro']['error'] = ''
            . '<span id="location_href">'
            . 'Salvo com sucesso!'
            . '<script>window.location.href="/identificacao/checkout-new";</script>'
            . '</span>';
          // header('Location: /identificacao/checkout-new');
          // return;
        }
      }
    } else {
      if ((is_array($array_clientes) ? count($array_clientes) : 0) > 0) {
        foreach ($array_clientes as $column_name => $error) {
          $ErrorCheckoutCadastrarEditarAll['cadastro'][$column_name] = ''
            . '<span>'
            . sprintf('<span id="cadastro[%s]-error" class="text-danger ft12px">', $column_name)
            . $STORE['config']['cadastro'][$column_name][$error]
            . '</span>'
            . sprintf('<script>$("input#%s").parent().parent().addClass("has-error");</script>', $column_name)
            . '</span>';
        }
      }

      if ((is_array($array_enderecos) ? count($array_enderecos) : 0) > 0) {
        foreach ($array_enderecos as $column_name => $error) {
          $column_name = ($column_name == 'nomeendereco' ? 'nome' : $column_name);
          $ErrorCheckoutCadastrarEditarAll['endereco'][$column_name] = ''
            . '<span>'
            . sprintf('<span id="endereco[%s]-error" class="text-danger ft12px">', $column_name)
            . $STORE['config']['endereco'][$column_name][$error]
            . '</span>'
            . sprintf('<script>$("input#%s").parent().parent().addClass("has-error");</script>', $column_name)
            . '</span>';
        }
      }
    }

    break;

    // New Checkout
    // Pagina de cadastro de cliente endereco e finalização do pedido
  case sha1('NewCheckoutCadastreSe'):
  case sha1('NewCheckoutEditarCadastro'):

    $ErrorCheckoutCadastreSe = array();

    $cadastro = $POST['cadastro'];

    $endereco = $POST['endereco'];

    $post = isset($POST['cadastro']) && $POST['cadastro'] != '' ? $POST['cadastro'] : false;

    $post = ($cadastro + ['endereco' => (object)$endereco]);

    $URL_DEFINE = filter_input(INPUT_GET, '_u', FILTER_SANITIZE_STRING);

    if (isset($_SESSION['cliente']['id_cliente']) && $_SESSION['cliente']['id_cliente'] != '') :
      $Clientes = Clientes::first(['conditions' => ['md5(id)=?', $_SESSION['cliente']['id_cliente']]]);
      $params['id'] = $Clientes->id;
    else :
      $Clientes = new Clientes();
    endif;

    $params['loja_id'] = $CONFIG['loja_id'];

    $params['ip'] = retornaIpReal();

    $params['atacadista'] = $URL_ATACADISTA ? 1 : 0;


    foreach ($cadastro as $name => $values) {
      // if( $name == 'cpfcnpj' ) {
      // 	if( (new ValidaCPFCNPJ($values))->valida() )
      // 		$params[ $name ] = $values;
      // 	else
      // 		$params[ $name ] = '';
      // }
      // else {
      //     $params[ $name ] = addslashes($values);
      // }

      $params[$name] = addslashes($values);
    }

    // verificar se a senha real existe
    // e em seguida checa se ela esta vazia
    $params['senha_real'] = isset($cadastro['senha_real']) && !empty($cadastro['senha_real']) ? $cadastro['senha_real'] : '#1234#@';

    // verificar se a senha real existe
    // e em seguida checa se ela esta vazia
    $params['senha_confirm'] = isset($cadastro['senha_confirm']) && !empty($cadastro['senha_confirm']) ? $cadastro['senha_confirm'] : '#1234#@';

    // // verificar se a senha real existe
    // // e em seguida checa se ela esta vazia
    // $params['senha_real'] = isset( $post['senha_real'] ) ? ( ! empty( $post['senha_real'] ) ? $post['senha_real'] : false ) : null;

    // // verificar se a senha real existe
    // // e em seguida checa se ela esta vazia
    // $params['senha_confirm'] = isset( $post['senha_confirm'] ) ? ( ! empty( $post['senha_confirm'] ) ? $post['senha_confirm'] : false ) : null;

    // $params['senha'] = Bcrypt::hash( $post['senha_real'] );

    if (isset($params['senha_real']) && $params['senha_real'] !== '#1234#@') {
      $params['senha'] = Bcrypt::hash($params['senha_real']);
    }

    $return = $Clientes->new_save($params);

    if (isset($return['id']) && $return['id'] > 0) {

      if ($post['atacadista']) {
        $ErrorCheckoutCadastreSe['cadastro']['error'] = ''
          . '<span>'
          . 'Salvo com sucesso!'
          . '<script>window.location.href="/identificacao/atacadista";</script>'
          . '</span>';
        header('Location: /identificacao/atacadista');
        return;
      }

      $_SESSION['cliente']['nome'] = reset(explode(' ', $params['nome']));
      $_SESSION['cliente']['email'] = $params['email'];
      $_SESSION['cliente']['id_cliente'] = md5($return['id']);

      if (!empty($URL_DEFINE)) {
        $ErrorCheckoutCadastreSe['cadastro']['error'] = ''
          . '<span>'
          . 'Salvo com sucesso!'
          . '<script>window.location.href="' . $URL_DEFINE . '";</script>'
          . '</span>';
        header('Location: ' . $URL_DEFINE);
        return;
      } else {
        $ErrorCheckoutCadastreSe['cadastro']['error'] = ''
          . '<span>'
          . 'Salvo com sucesso!'
          . '<script>window.location.href="/identificacao/checkout-new";</script>'
          . '</span>';
        header('Location: /identificacao/checkout-new');
        return;
      }
    } else {
      foreach ($return as $column_name => $error) {
        $ErrorCheckoutCadastreSe['cadastro'][$column_name] = ''
          . '<span>'
          . sprintf('<span id="cadastro[%s]-error" class="input-error-span-2 text-right">', $column_name)
          . $STORE['config']['cadastro'][$column_name][$error]
          . '</span>'
          . sprintf('<script>$("input#%s").parent().parent().addClass("new-checkout-error").removeClass("new-checkout-ok");</script>', $column_name)
          . '<span>';
      }
    }

    break;

  case sha1('NewCheckoutEnderecos'):

    $ErrorCheckoutEditarEnderecos = array();

    $URL_DEFINE = filter_input(INPUT_GET, '_u', FILTER_SANITIZE_STRING);

    $post = isset($POST['endereco']) && $POST['endereco'] != '' ? $POST['endereco'] : null;

    $endereco = isset($POST['endereco']) && $POST['endereco'] != '' ? $POST['endereco'] : null;

    $Clientes = Clientes::first(['conditions' => ['md5(id)=?', $_SESSION['cliente']['id_cliente']]]);

    // Buscar endereco do cliente
    if (!empty($post['hashendereco']) && $post['hashendereco'] != '') {
      $ClientesEnderecos = ClientesEnderecos::first(['conditions' => ['sha1(id)=?', $post['hashendereco']]]);
      $params['id'] = $ClientesEnderecos->id;
    } else {
      $ClientesEnderecos = new ClientesEnderecos();
    }

    // Zera todos os enrederco com status ativo
    if ($Clientes->id > 0) {
      ClientesEnderecos::update_all(['set' => ['status' => ''], 'conditions' => ['id_cliente=?', $Clientes->id]]);
    }

    $params['id_cliente'] = $Clientes->id;
    $params['status'] = 'ativo';

    foreach ($endereco as $name => $values) {
      $params[$name] = addslashes($values);
    }

    $return = $ClientesEnderecos->new_save($params);

    if (isset($return['id']) && $return['id'] > 0) {

      if (!empty($URL_DEFINE)) {
        $ErrorCheckoutEditarEnderecos['endereco']['error'] = ''
          . '<span>'
          . 'Salvo com sucesso!'
          . sprintf('<script>window.location.href="%s";</script>', $URL_DEFINE)
          . '</span>';
        header('Location: ' . $URL_DEFINE);
        return;
      } else {
        $ErrorCheckoutEditarEnderecos['endereco']['error'] = ''
          . '<span>'
          . 'Salvo com sucesso!'
          . '<script>window.location.href="/identificacao/checkout-new";</script>'
          . '</span>';
        header('Location: /identificacao/checkout-new');
        return;
      }
    } else {
      foreach ($return as $column_name => $error) {
        $ErrorCheckoutEditarEnderecos['endereco'][$column_name] = ''
          . '<span>'
          . sprintf('<span id="endereco[%s]-error" class="input-error-span-2 text-right">', $column_name)
          . $STORE['config']['endereco'][$column_name][$error]
          . '</span>'
          . sprintf('<script>$("input#%s").parent().parent().addClass("new-checkout-error").removeClass("new-checkout-ok");</script>', $column_name)
          . '<span>';
      }
    }

    break;

  case 'CheckoutLogOut':

    if (empty($_SESSION)) :
      return false;
    endif;

    foreach ($_SESSION['cliente'] as $k => $v) :
      unset($_SESSION['cliente'][$k]);
    endforeach;

    session_unset();
    session_destroy();

    $URL_DEFINE = filter_input(INPUT_GET, '_u', FILTER_SANITIZE_STRING) || URL_BASE;
    $LOGOUT = sprintf('<script>window.location.href="%s";</script>', $URL_DEFINE);

    break;

  case 'NewCheckoutLogin':

    $MensagemNovoCheckoutLogin = '';
    $email = $POST['email'];
    $senha = $POST['senha'];

    $URL_DEFINE = filter_input(INPUT_GET, '_u', FILTER_SANITIZE_STRING);
    $URL_ATACADISTA = filter_input(INPUT_GET, '_atacadista', FILTER_SANITIZE_STRING);

    // Adiciona um e-mail como temp para a compra nesse momento
    // Pre cadastro de cliente para a primeira compra ou abandono de carrinho
    $UpCartUsr = Carrinho::update_all(['set' => ['cliente_tmp' => $email], 'conditions' => ['id_session=?', SESSION_ID]]);

    // Buscar o cliente do site
    $Usuarios = Clientes::first(['conditions' => ['email = ? AND loja_id = ?', $email, $CONFIG['loja_id']]]);
    $UsuariosCount = (int)$Usuarios;

    // Verificar se exista registro no db
    if ($UsuariosCount > 0) {
      // Verifica se o cliente é atacadista
      if ($Usuarios->atacadista == 1 && empty($Usuarios->atacadista_desconto)) {
        $MensagemNovoCheckoutLoginEmail = ''
          . '<span id="cadastro[email]-error" class="input-error-span-2 text-left">Você não foi efetivado como atacadista no momento!</span>'
          . '<script>$("input[name]").focus().parent().parent().addClass("new-checkout-error").removeClass("new-checkout-ok");</script>';
      }
      // Verificar se o cliente ainda esta ativo para compras
      else if ($Usuarios->excluir == 1) {
        $MensagemNovoCheckoutLogin = ''
          . '<span id="" class="row">'
          . '<div class="alert alert-danger col-md-5 col-xs-12 mt15 ft12px">'
          . 'Por favor entrar em contato com <br/><strong>'
          . $CONFIG['nome_fantasia']
          . '</strong> pelo telefone <strong>'
          . ($CONFIG['telefone'] ? $CONFIG['telefone'] : ($CONFIG['celular'] ? $CONFIG['celular'] : null)) . '</strong>'
          . '</div>'
          . '</span>';
      } else {
        // Gera um nova hash para o cliente
        $_SESSION['cliente']['email'] = $Usuarios->email;

        $is_senha = Bcrypt::check($senha, $Usuarios->senha);

        if ($is_senha && $senha) {

          $_SESSION['cliente']['nome'] = reset(explode(' ', $Usuarios->nome));
          $_SESSION['cliente']['id_cliente'] = md5($Usuarios->id);

          // Gera um log para o cliente
          $ClientesLogs = ClientesLogs::create([
            'id_cliente' => $Usuarios->id,
            'id_session' => SESSION_ID,
            'acao' => 'login',
            'ip' => retornaIpReal()
          ]);

          // Adiciona o cliente no comentario de produto
          $ProdutosComentariosCount = (int)ProdutosComentarios::count(['conditions' => ['id_session=?', SESSION_ID]]);
          if ($ProdutosComentariosCount > 0) {
            $ProdComentarios = ProdutosComentarios::find(['conditions' => ['id_session=?', SESSION_ID]]);
            $ProdComentarios->id_cliente = $Usuarios->id;
            $ProdComentarios->id_session = '';
            $ProdComentarios->save();
          }

          $MensagemNovoCheckoutLogin = ''
            . '<span id="">'
            . sprintf('<script>window.location.href="%s";$("#aminacao-site").fadeIn(0);</script>', $URL_DEFINE)
            . '</span>';
        } else if (!$is_senha && $senha) {
          $MensagemNovoCheckoutLoginSenha = ''
            . '<span id="cadastro[senha]-error" class="text-danger ft13px text-right show">'
            . 'Senha inválida!'
            . '</span>'
            . '<script>'
            . '$("input[type=password").focus().parent().addClass("has-error");'
            . '</script>';
        }
      }
      // } else if( $URL_ATACADISTA == 1 ) {
      // /**
      // * Caso não exista cliente cadastrado como atacadista
      // * Deve se passar para o acesso ao sistema em forma de cadastro
      // */

      // $MensagemNovoCheckoutLoginEmail = ''
      // . '<script>'
      // . '$("button[type=submit]").fadeOut(0);'
      // . sprintf('window.location.href="/identificacao/cadastre-se/?_u=%sidentificacao/meus-dados&email=%s&_atacadista=1"', URL_BASE, $email)
      // . '</script>';
    } else {
      $MensagemNovoCheckoutLoginEmail = ''
        . '<span id="cadastro[email]-error" class="text-danger ft13px text-right show">'
        . 'Você não é usuário ' . $CONFIG['nome_fantasia']
        . '</span>'
        . '<script>$("input[name]").focus().parent().parent().addClass("new-checkout-error").removeClass("new-checkout-ok");</script>';
      /**
       * Caso não exista cliente cadastrado
       * Deve se passar para o acesso ao sistema em forma de cadastro
       */
      $explode = @end(explode('/', $URL_DEFINE));
      if ('minha-compra' == $explode || 'checkout-new' == $explode) {
        $MensagemNovoCheckoutLoginEmail = ''
          . '<script>'
          . sprintf('window.location.href="/identificacao/cadastre-se/?email=%s"', $email)
          . '</script>';
      }
    }

    $ErrorCheckoutEditarEnderecos['cadastro']['error'] = ''
      . '<span>'
      . ($MensagemNovoCheckoutLoginEmail ? $MensagemNovoCheckoutLoginEmail : $MensagemNovoCheckoutLoginSenha)
      . '</span>';

    $ErrorCheckoutCadastrarEditarAll['cadastro']['error'] = $MensagemNovoCheckoutLoginSenha;

    break;

  default:
    $NEW_ACAO = isset($GET['AcaoEnderecos']) && $GET['AcaoEnderecos'] != '' ? $GET['AcaoEnderecos'] : '';
    switch ($NEW_ACAO) {
        /**
       * Seleciona um outro endereco
       */
      case 'SelecionaEndereco':
        if (empty($_SESSION['cliente']['id_cliente']) && $_SESSION['cliente']['id_cliente'] == '')
          exit('Sem pemissão para edição');
        /**
         * Zera todos os enrederco com status ativo
         */
        ClientesEnderecos::update_all(['set' => ['status' => ''], 'conditions' => ['md5(id_cliente)=?', $_SESSION['cliente']['id_cliente']]]);

        $ClientesEnderecos = ClientesEnderecos::find((int)$GET['endereco_id']);

        $return = $ClientesEnderecos->new_save(['status' => 'ativo', 'id' => $ClientesEnderecos->id]);

        if (isset($return['id']) && $return['id'] > 0) {
          header(sprintf('Location: %sidentificacao/checkout-new', URL_BASE));
          return;
        }
        break;

        /**
         * Excluir enderecos
         */
      case 'ExcluirEndereco':
        if (empty($_SESSION['cliente']['id_cliente']) && $_SESSION['cliente']['id_cliente'] == '')
          exit('Sem pemissão para edição');

        $ClientesEnderecos = ClientesEnderecos::first(['conditions' => ['sha1(id)=?', $GET['endereco_id']]]);
        try {
          $ClientesEnderecos->delete();

          $Enderecos = ClientesEnderecos::first(['conditions' => ['md5(id_cliente)=?', $_SESSION['cliente']['id_cliente']], 'order' => 'id desc', 'limit' => '1']);
          $return = $ClientesEnderecos->new_save(['status' => 'ativo', 'id' => $Enderecos->id]);
          if (isset($return['id']) && $return['id'] > 0) {
            header(sprintf('Location: %sidentificacao/checkout-new', URL_BASE));
            return;
          }
        } catch (Exception $ex) {
          echo 'Não foi possivel remover enderço!';
        }

        break;
    }
    break;
endswitch;

function tigger_error_html($instance = null, $field = '')
{
  if (!isset($field, $instance) && $field !== '' && $instance !== '') return false;

  return ''
    . '<span id="cadastro[' . $field . ']-error" class="input-error-span-2 text-right">' . $instance->errors->{$field} . '</span>'
    . '<script>$("input#' . $field . '").parent().parent().addClass("new-checkout-error").removeClass("new-checkout-ok");</script>';
}

function tigger_error($str)
{
  if (!isset($str)) return false;
  return $str;
}

function is_post_value($str = false)
{
  if (!empty($str))
    return $str;
}
