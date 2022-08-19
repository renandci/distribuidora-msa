<div class="menu-cliente col-md-3 col-sm-4 col-xs-12 mb15">
  <div class="row menus-clientes">
    <div class="clearfix icon-foto">
      <span class="img-user">
        <span class="icon-image">
          <?php
          // Carregar a foto do usuario se enviada para o servidor
          if (!empty($_SESSION['cliente']['id_cliente'])) {
            $image = glob(URL_VIEWS_BASE_PUBLIC_UPLOAD . "imgs/users/user-{$_SESSION['cliente']['id_cliente']}{.*}", GLOB_BRACE);
            if (count($image) > 0) {
              echo sprintf('<img src="%s"/>', current($image));
            } else {
              echo '<img src="/public/imgs/icon-users.gif"/>';
            }
          }
          ?>
        </span>
      </span>
      <h2>Olá <?php echo $_SESSION['cliente']['nome']; ?></h2>
    </div>
    <hr class="mt0" />
    <a href="/identificacao/meus-dados" ajax class="show model-radius <?php echo (in_array($GET_ACAO, ['meus-dados', 'editar-cadastro'])) ? 'add-active-menu' : ''; ?>">MEUS DADOS</a>
    <a href="/identificacao/meus-pedidos" ajax class="show model-radius <?php echo $GET_ACAO == 'meus-pedidos' ? 'add-active-menu' : ''; ?>">MEUS PEDIDOS</a>
    <a href="/identificacao/meus-enderecos" ajax class="show model-radius <?php echo 'meus-enderecos' == $GET_ACAO ? 'add-active-menu' : ''; ?>">MEUS ENDEREÇOS</a>
    <a href="/identificacao/foto" ajax class="show model-radius <?php echo $GET_ACAO == 'foto' ? 'add-active-menu' : ''; ?>">ALTERAR/EDITAR FOTO</a>
    <a href="/identificacao/minha-senha" ajax class="show model-radius <?php echo $GET_ACAO == 'minha-senha' ? 'add-active-menu' : ''; ?>">ALTERAR/EDITAR SENHA</a>
    <a href="/identificacao/sair" ajax class="show model-radius">SAIR</a>
  </div>
  <style>
    .icon-foto>h2,
    .icon-foto>span {
      float: left;
    }

    .img-user {
      position: relative;
      z-index: 1;
      width: 63px;
      height: 63px;
      display: block;
    }

    .img-user>span.icon-image {
      position: relative;
      z-index: 1;
      width: 63px;
      height: 63px;
      -webkit-border-radius: 100%;
      -moz-border-radius: 100%;
      border-radius: 100%;
      overflow: hidden;
      display: block;
    }

    .img-user>span.icon-image>img {
      position: absolute;
      top: 50%;
      left: 50%;
      margin: -64px 0 0 -64px;
      z-index: 1;
    }
  </style>
</div>
