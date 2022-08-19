<?php
$rs = !empty($_SESSION['cliente']['id_cliente']) ? $CONFIG['cliente_session'] : null;
?>
<div class="col-md-9 col-sm-8 col-xs-12 mb30">
  <ul class="clearfix">
    <?php if ('meus-dados' == $GET_ACAO) { ?>
      <li class="clearfix">
        <h2>
          Meus Dados
          <a href="/identificacao/editar-cadastro/?_u=<?php echo URL_BASE ?>identificacao/meus-dados" class="btn btn-secundary pull-right btn-xs">
            Editar
          </a>
        </h2>
        <hr class="mt5" />
        <div class="clearfix">
          <table width='100%' align='left' class="table-2">
            <tr>
              <td align='right' class="bold">Nome completo:</td>
              <td><?php echo $rs['nome']; ?></td>
            </tr>
            <tr>
              <td align='right' class="bold">E-mail:</td>
              <td><?php echo $rs['email']; ?></td>
            </tr>
            <tr<?php echo empty($rs['sexo']) ? ' class="hidden"' : '' ?>>
              <td align='right' class="bold">Sexo:</td>
              <td><?php echo $rs['sexo']; ?></td>
              </tr>
              <tr<?php echo empty($rs['cpfcnpj']) ? ' class="hidden"' : '' ?>>
                <td align='right' class="bold">CPF:</td>
                <td><?php echo $rs['cpfcnpj']; ?></td>
                </tr>
                <tr<?php echo empty($rs['rg']) ? ' class="hidden"' : '' ?>>
                  <td align='right' class="bold">RG:</td>
                  <td><?php echo $rs['rg']; ?></td>
                  </tr>
                  <tr<?php echo empty($rs['data_nascimento']) ? ' class="hidden"' : '' ?>>
                    <td align='right' class="bold">Nascimento:</td>
                    <td><?php echo $rs['data_nascimento']; ?></td>
                    </tr>
                    <tr<?php echo empty($rs['telefone']) ? ' class="hidden"' : '' ?>>
                      <td align='right' class="bold">Telefone:</td>
                      <td><?php echo $rs['telefone']; ?></td>
                      </tr>
                      <tr<?php echo empty($rs['celular']) ? ' class="hidden"' : '' ?>>
                        <td align='right' class="bold">Celular:</td>
                        <td><?php echo !empty($rs['celular']) ? $rs['celular'] : 'Não informado.';
                            echo !empty($rs['operadora']) ? " - {$rs['operadora']}" : ''; ?></td>
                        </tr>
                        <tr<?php echo empty($rs['cidade']) ? ' class="hidden"' : '' ?>>
                          <td align='right' class="bold">Celular:</td>
                          <td><?php echo !empty($rs['cidade']) ? $rs['cidade'] : ''; ?></td>
                          </tr>
                          <tr<?php echo empty($rs['uf']) ? ' class="hidden"' : '' ?>>
                            <td align='right' class="bold">Celular:</td>
                            <td><?php echo !empty($rs['uf']) ? $rs['uf'] : ''; ?></td>
                            </tr>
          </table>
        </div>
      </li>
    <?php } ?>

    <?php if ($GET_ACAO == 'foto') { ?>
      <li class='clearfix'>
        <?php
        if (!empty($GET['acao']) && $GET['acao'] == 'RemoverFoto') {
          $image = glob(URL_VIEWS_BASE_PUBLIC_UPLOAD . "imgs/users/user-{$_SESSION['cliente']['id_cliente']}{.*}", GLOB_BRACE);
          if (count($image) == 1) {
            unlink(current($image));
          }
        }
        if (!empty($POST['acao']) && $POST['acao'] == 'ImageCadastrarEditar') {
          $temp = current($_FILES);
          $dir = URL_VIEWS_BASE_PUBLIC_UPLOAD . 'imgs/users';
          /**
           * Tenta crira um diretorio para as imagens
           */
          if (!is_dir($dir)) {
            mkdir($dir);
          }

          if (is_uploaded_file($temp['tmp_name'])) {
            // Verify extension
            if (!in_array(strtolower(pathinfo($temp['name'], PATHINFO_EXTENSION)), array('png', 'jpeg', 'gif', 'jpg'))) {
              header("HTTP/1.0 500 Extensão inválida.");
              return;
            }
            switch ($temp['error']) {
              case UPLOAD_ERR_OK:
                break;
              case UPLOAD_ERR_NO_FILE:
                header("HTTP/1.0 500 Nenhum arquivo enviado.");
                break;
              case UPLOAD_ERR_INI_SIZE:
              case UPLOAD_ERR_FORM_SIZE:
                header("HTTP/1.0 500 Limite de tamanho de arquivo excedido.");
                break;
              default:
                header("HTTP/1.0 500 Erros desconhecidos.");
                break;
            }
            $filetowrite = $dir
              . '/user-'
              . $_SESSION['cliente']['id_cliente']
              . '.'
              . strtolower(pathinfo($temp['name'], PATHINFO_EXTENSION));

            $ext = pathinfo($temp['name']);
            $ext = $ext['extension'];

            /**
             * Carregar a imagem no upload
             */
            $WideImageSquare = WideImage\WideImage::load('./public/imgs/_quadro.' . $ext);
            $WideImageTmpName = WideImage\WideImage::load($temp['tmp_name']);

            $WideImage = $WideImageSquare->resize(128, 128);
            $WideImageFotoUsers = $WideImageTmpName->resize(128, 128);
            $WideImage->merge($WideImageFotoUsers, 'center', 'center')->saveToFile($filetowrite);

            $WideImage->destroy();
            $WideImageSquare->destroy();
            $WideImageTmpName->destroy();
            $WideImageFotoUsers->destroy();
          }
        }
        ?>
        <h2>Adicionar/Alterar Foto</h2>
        <hr />
        <span class="show ft12px mb15">Selecione uma imagem no botão abaixo</span>
        <form id="form-foto" method="post" enctype="multipart/form-data" action="" class="ml15">
          <span class="img-user">
            <span class="icon-image">
              <?php
              /**
               * Carregar a foto do usuario se enviada para o servidor
               */
              if (!empty($_SESSION['cliente']['id_cliente'])) {
                $image = glob(URL_VIEWS_BASE_PUBLIC_UPLOAD . "imgs/users/user-{$_SESSION['cliente']['id_cliente']}{.*}", GLOB_BRACE);
                if (count($image) == 1) {
                  echo '<img src="' . current($image) . '?t=' . time() . '"/>';
                } else {
                  echo '<img src="/public/imgs/icon-users.gif"/>';
                }
              }
              ?>
            </span>
          </span>
          <div class="clearfix mt5">
            <input type="file" name="foto" id="foto" class="input-file mb5" />
            <label for="foto" class="btn btn-secundary">
              <i class="fa fa-image"></i>
              selecionar foto
            </label>

            <a class="btn btn-danger mt10<?php echo empty($image) ? ' hidden' : '' ?>" href="/identificacao/foto?acao=RemoverFoto" id="remover-foto">
              <i class="fa fa-trash"></i>
              remover foto
            </a>
          </div>
          <input type="hidden" name="acao" value="ImageCadastrarEditar" />
        </form>
      </li>
    <?php } ?>

    <?php if ('minha-senha' == $GET_ACAO) { ?>
      <li class='clearfix'>
        <h2>Alterar Senha</h2>
        <hr />
        <span class="show ft13px text-right">Campos com (*) são obrigatórios</span>
        <form id="mudar-senha">
          <div class="form-horizontal">
            <div class="form-group has-feedback-inverter has-feedback form-group-lg">
              <label class="col-sm-4 control-label" for="senhaantiga">Senha antiga *</label>
              <div class="col-sm-4">
                <input type="password" autocomplete="off" name="senhaantiga" class="form-control" id="senhaantiga" />
              </div>
            </div>
            <div class="form-group has-feedback-inverter has-feedback form-group-lg">
              <label class="col-sm-4 control-label" for="senha1">Nova Senha *</label>
              <div class="col-sm-4">
                <input type="password" autocomplete="off" name="senha1" id="senha1" class="form-control" />
              </div>
            </div>
            <div class="form-group has-feedback-inverter has-feedback form-group-lg">
              <label class="col-sm-4 control-label" for="senha2">Confirmar Senha *</label>
              <div class="col-sm-4">
                <input type="password" autocomplete="off" name="senha2" id="senha2" class="form-control" />
              </div>
            </div>
          </div>
          <center>
            <button type="submit" class="mt20 btn btn btn-primary"> ALTERAR SENHA </button>
          </center>
          <input type="hidden" name="acao" value="TrocarSenha" />
        </form>
      </li>
    <?php } ?>
  </ul>
  <style>
    .input-file {
      display: none;
    }

    .input-file+label {}

    .table-2 td {
      padding: 7px;
    }
  </style>
</div>

<?php ob_start(); ?>
<script>
  console.clear();
  $("#mudar-senha").validate({
    // debug: true,
    errorClass: "ft12px text-danger",
    errorElement: "span",
    rules: {
      senhaantiga: {
        required: true
      },
      senha1: {
        required: true,
        minlength: 6,
        maxlength: 12
      },
      senha2: {
        required: true,
        minlength: 6,
        maxlength: 12,
        equalTo: '#senha1'
      }
    },
    messages: {
      senhaantiga: {
        required: 'Digite sua senha antiga'
      },
      senha1: {
        required: 'Digite uma senha',
        minlength: 'Senha muito curta',
        maxlength: 'Senha muito longa'
      },
      senha2: {
        required: 'Confirme sua senha',
        minlength: 'Senha muito curta',
        maxlength: 'Senha muito longa',
        equalTo: 'As senha não conferem'
      }
    },
    highlight: function(element, errorClass, validClass) {
      $(element).parent().parent().addClass("has-error");
    },
    unhighlight: function(element, errorClass, validClass) {
      $(element).parent().parent().addClass("has-error");
    },
    // errorPlacement: function(error, element) {
    // console.log(error);
    // error.insertAfter(element.next('div'));
    // },
    submitHandler: function(form) {
      var ModalSite = $("#modal-site"),
        formData = $("#mudar-senha").serialize();
      $.ajax({
        url: window.location.href,
        type: 'POST',
        data: formData,
        dataType: 'json',
        error: function(X, T, M) {
          if (T === 'timeout') {
            alert('Opss algo falhou tente novamente');
          } else {
            console.log(T + '\n' + M);
          }
        },
        beforeSend: function() {
          ModalSite.modal("show").find("p").html('Aguarde...');
        },
        success: function(str) {
          ModalSite.find("p").html(str.msg);
          $(str.campo).addClass('error');
        }
      });
    }
  });

  $("#form-foto").submit(function(e) {
    e.preventDefault(); //prevent default action
    var post_url = $(this).attr("action"),
      request_method = $(this).attr("method"),
      form_data = new FormData(this),
      Modal = $("#modal-site"); //Encode form elements for submission
    console.log(form_data);
    $.ajax({
      url: post_url,
      type: request_method,
      data: form_data,
      contentType: false,
      processData: false,
      beforeSend: function() {
        Modal.modal("show").find("p").html([
          $("<span/>", {
            html: "Aguarde um momento..."
          }),
          $("<span/>", {
            id: "status",
            class: "show"
          })
        ]);
      },
      xhr: function() {
        var xhr = $.ajaxSettings.xhr();
        if (xhr.upload) {
          xhr.upload.addEventListener('progress', function(event) {
            var percent = 0;
            var position = event.loaded || event.position;
            var total = event.total;
            if (event.lengthComputable) {
              percent = Math.ceil(position / total * 100);
            }
            Modal.find("#status").html("Enviando imagem " + percent + "%");
          }, true);
        }
        return xhr;
      },
      success: function(response) {
        var list = $("<span/>", {
          html: response
        });
        $(".img-user").html(list.find(".img-user").html());
      },
      error: function(a, b, c) {
        if (b === 'timeout') {
          alert('Opss algo falhou tente novamente!');
        } else {
          console.log(a.responseText + '\n' + c);
        }
      }
    });
  });
  /**
   * Apenas faz o submit do formulario
   */
  $("#form-foto").on("change", "input[type=file]", function() {
    $(this).trigger("submit");
  });
  /**
   * Apenas remover a foto do usuario
   */
  $("#form-foto").on("click", "#remover-foto", function(e) {
    var Modal = $("#modal-site");
    e.preventDefault();
    $.ajax({
      url: e.target.href || this.href,
      beforeSend: function() {
        Modal.modal("show").find("p").html([$("<span/>", {
          html: "Aguarde um momento..."
        })]);
      },
      success: function(response) {
        var list = $("<span/>", {
          html: response
        });
        $(".img-user").html(list.find(".img-user").html());
      },
      complete: function() {
        Modal.modal('hide');
      },
      error: function(a, b, c) {
        if (b === 'timeout') {
          alert('Opss algo falhou tente novamente!');
        } else {
          console.log(a.responseText + '\n' + c);
        }
      }
    });
  });
</script>
<?php $str['script_manual'] .= ob_get_clean(); ?>
