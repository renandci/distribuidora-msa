<?php
ob_start();
include '../app/settings.php';
include PATH_ROOT . '/app/vendor/autoload.php';
include PATH_ROOT . '/app/settings-config.php';
include PATH_ROOT . '/assets/' . ASSETS .  '/settings.php';
include PATH_ROOT . '/app/includes/bibli-funcoes.php';
include PATH_ROOT . '/app/includes/ajax-emails.php';


$data = null;

if (isset($POST['acao']) && $POST['acao'] == 'FazerLogin') {

  $login_user = addslashes($POST['login_user']);
  $login_pass = addslashes($POST['login_pass']);

  if (empty($login_user) || empty($login_pass)) {
    $_SESSION['post']['login_user'] = $login_user;

    if (empty($login_user))
      $_SESSION['error']['login_user'] = 'Digite seu usuário';
    if (empty($login_pass))
      $_SESSION['error']['login_pass'] = 'Digite sua senha';
  } else {
    $result = Adm::connection()->query(sprintf('SELECT * FROM adm WHERE usuario = "%s" and senha = "%s"', $login_user, $login_pass));

    if ($result->rowCount() == 0) {
      $_SESSION['post']['login_user'] = $login_user;
      $_SESSION['error']['success'] = 'Usuário ou Senha inválido';
    } else {

      $rws = $result->fetch();

      $_SESSION['admin']['apelido'] = $rws['apelido'];
      $_SESSION['admin']['id_usuario'] = $rws['id'];
      Logs::my_logs(['adm' => $rws['apelido']], ['adm' => 'Efetuou Login'], (int)$_SESSION['admin']['id_usuario'], 'adm');

      if ($_SESSION['admin']['id_usuario'] > 0 && substr($_SERVER['REQUEST_URI'], -9) == 'login.php') {

        // $explode = pathinfo($_SERVER['HTTP_REFERER']);

        // $uri_before = $explode['filename'];
        // $uri_after = substr($_SERVER['REQUEST_URI'], -9);

        // if("{$uri_before}.php" != $uri_after)
        // $uri = $_SERVER['HTTP_REFERER'];
        // else

        header('location: /adm/index.php');
        return;

        // echo '<h4>Fazendo login, aguarde...</h4>';
        // $uri = URL_BASE . 'adm/index.php';
        // header('Refresh: 1; URL=' . $uri);
        // return;
      }
    }
  }

  header('Location: /adm/login.php');
  return;
}

if (isset($POST['acao']) && $POST['acao'] == 'EnviarSenha') {
  if (isset($POST['login_user']) && $POST['login_user'] == '') {
    $_SESSION['error']['login_user'] = 'Digite seu e-mail!';
  } else {
    if (filter_var($POST['login_user'], FILTER_VALIDATE_EMAIL)) {
      $result = Logs::connection()->query(sprintf('SELECT * FROM adm WHERE usuario = "%s"', addslashes($POST['login_user'])));

      if ($result->rowCount() > 0) {
        // $_SESSION['error']['login_user'] = 'Enviamos um e-mail para sua caixa de mensagens!';

        $rws = $result->fetch();

        $HTML = ""
          . "<tr>"
          . "<td>"
          . "<b>Olá {$rws["apelido"]}</b><br/>"
          . "Seu Login de acesso: {$rws["login_user"]}<br/>"
          . "Sua Senha de acesso: {$rws["login_pass"]}"
          . "</td>"
          . "</tr>";

        $CONTEUDO_MAIL = email_body($CONFIG, $HTML);

        $mail->setFrom($CONFIG['email_contato'], $CONFIG['nome_fantasia']);
        $mail->addAddress($rws['login_user'], $rws['apelido']);
        $mail->addBCC($CONFIG['email_contato'], $CONFIG['nome_fantasia']);

        $mail->Subject = $CONFIG['nome_fantasia'] . ' | Recuperação de login_pass ' . $rws['apelido'];
        $mail->Body    = $CONTEUDO_MAIL;
        $MSG = '';
        if (!$mail->send()) {
          $_SESSION['error']['success'] = 'Ops! Não consegui enviar seus dados de acesso no momento! Tente novamente.';
        } else {
          $_SESSION['error']['success'] = 'Legal, enviamos um e-mail com seus dados de acesso para seu e-mail!';
          unset($POST);
        }
        $mail->SmtpClose();
      } else {
        $_SESSION['error']['success'] = 'Você não possui dados em nossa plataforma';
      }
    } else {
      $_SESSION['error']['mensagem'] = 'Você digitou um e-mail inválido!';
    }
  }
  header('Location: /adm/login.php?acao=minhaSenha');
  return;
}

$get_errors = isset($_SESSION['error']) ? $_SESSION['error'] : null;
$get_post = isset($_SESSION['post']) ? $_SESSION['post'] : null;

?>
<!doctype html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <title>Login</title>

  <style>
    <?php
    $fontes = array();
    $fontes['awesome'] = './../public/css/awesome.css';
    $fontes['titillium'] = './../public/css/fonte-titillium-web.css';
    $fontes['neosans'] = './../public/css/fonte-neo-sans.css';

    if (count($fontes) > 0) {
      $puts_font = '';
      foreach ($fontes as $font) {
        $puts_font .= file_get_contents($font);
      }
    }
    ?><?php ob_start(); ?><?php echo $puts_font ?><?php echo (file_get_contents('../public/bootstrap/css/bootstrap.css')) ?><?php echo (file_get_contents('../public/css/adm/login.css')) ?>html,
    body {
      height: 100%;
    }

    body {
      background-image: url('<?php echo Imgs::src('Fundo-Login.jpg', 'public') ?>');
      background-position: center center;
      background-repeat: no-repeat;
      background-size: 1920px auto;
      margin: 0;
      padding: 0;
      width: 100%;
      color: #10416c !important;
      overflow: hidden;
      display: flex;
      flex-direction: row;
      align-content: center;
      justify-content: center;
      align-items: center;
    }

    <?php
    $ob_get_contents_css = ob_get_clean();

    $minifier = new MatthiasMullie\Minify\CSS($ob_get_contents_css);
    printf("%s", $minifier->minify());
    ?>
  </style>
  <link rel="shortcut icon" href="<?php echo Imgs::src('favicon.png', 'imgs') ?>" />
  <link rel="icon" type="image/png" href="<?php echo Imgs::src('favicon.png', 'imgs') ?>" />
</head>

<body>

  <div class="loginmodal-container">
    <img src="<?php echo Imgs::src(($CONFIG['logo_desktop'] ? $CONFIG['logo_desktop'] : 'logo.png'), 'imgs') ?>" class="center-block" width="120px">
    <h1>Painel Administrativo</h1><br />
    <?php if (isset($_GET['acao']) && $_GET['acao'] == 'minhaSenha') : ?>
      <form method="post" action="/adm/login.php?acao=minhaSenha">
        <input type="text" name="login_user" placeholder="Digite seu e-mail" class="<?php echo $get_errors['login_user'] ? 'has-error' : '' ?>" value="<?php echo $get_post['login_user'] ?>" autocomplete="off" />
        <?php if ($get_errors['login_user']) : ?>
          <span class="show text-danger"><?php echo $get_errors['login_user'] ?></span>
        <?php endif ?>
        <button type="submit" class="login loginmodal-submit">ENVIAR SENHA</button>
        <input type="hidden" name="acao" value="EnviarSenha" />
      <?php else : ?>
        <form action="/adm/login.php" method="post">
          <input type="text" name="login_user" placeholder="Digite seu Usuário" class="<?php echo $get_errors['login_user'] ? 'has-error' : '' ?>" value="<?php echo $get_post['login_user'] ?>" autocomplete="off" />
          <?php if ($get_errors['login_user']) : ?>
            <span class="show text-danger"><?php echo $get_errors['login_user'] ?></span>
          <?php endif ?>
          <input type="password" name="login_pass" placeholder="Digite sua senha" class="<?php echo $get_errors['login_pass'] ? 'has-error' : '' ?>" autocomplete="off" />
          <?php if ($get_errors['login_pass']) : ?>
            <span class="show text-danger"><?php echo $get_errors['login_pass'] ?></span>
          <?php endif ?>
          <button type="submit" class="login loginmodal-submit">LOGIN</button>
          <input type="hidden" name="acao" value="FazerLogin" />
        <?php endif; ?>
        </form>
        <?php if (count($get_errors['success'])) : ?>
          <div class="alert alert-danger">
            <?php echo $get_errors['success'] ?>
          </div>
        <?php endif; ?>
        <div class="login-help">
          <!--
				<a href="#">Cirar um usuário</a>
				-->
          <?php if (isset($_GET['acao']) && $_GET['acao'] == 'minhaSenha') : ?>
            <a href="/adm/login.php">Voltar</a>
          <?php else : ?>
            <a href="/adm/login.php?acao=minhaSenha">Esqueci minha senha</a>
          <?php endif; ?>
        </div>
  </div>
  <!--
		<video autoplay muted loop>
			<source src="Video.webm" type="video/webm">
			Your browser does not support HTML5 video.
		</video>
		-->
</body>

</html>
<?php
echo CompactarHtml(ob_get_clean());
unset($_SESSION['error'], $_SESSION['post'], $_SESSION['success']);
$pdo = null;
