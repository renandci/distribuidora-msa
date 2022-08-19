<?php

include '../topo.php';

/**
 * Editar o comentario
 */
if (!empty($GET['acao']) && $GET['acao'] === 'editar_comentario') {
  ProdutosComentarios::action_cadastrar_editar([
    'ProdutosComentarios' => [
      $POST['id'] => [
        'titulocomentario' => $POST['titulocomentario'],
        'comentario' => nl2br($POST['comentario'])
      ]
    ]
  ], 'alterar', 'titulocomentario');
  header('Location: /adm/produtos/produtos-qualificados.php?pag=' . $GET['pag']);
  return;
}

/**
 * Ativar/Desativar Comentario
 */
if (!empty($GET['acao']) && $GET['acao'] === 'desativar_comentario' || $GET['acao'] === 'ativar_comentario') {
  ProdutosComentarios::action_cadastrar_editar(['ProdutosComentarios' => [$GET['id'] => ['ativo' => $GET['ativo']]]], 'alterar', 'titulocomentario');
  // EmailComentariosProdutos( $GET['id'] );
  header('Location: /adm/produtos/produtos-qualificados.php?pag=' . $GET['pag']);
  return;
}

/**
 * Excluir Comentario
 */
if (!empty($GET['acao']) && $GET['acao'] === 'excluir_comentario') {
  ProdutosComentarios::action_cadastrar_editar(['ProdutosComentarios' => [$POST['id'] => ['ativo' => 2, 'motivo' => $POST['motivo']]]], 'alterar', 'titulocomentario');
  if (!empty($POST['is_mail']) && $POST['is_mail'] != '')
    EmailComentariosProdutos($GET['id']);

  header('Location: /adm/produtos/produtos-qualificados.php?pag=' . $GET['pag']);
  return;
}

?>
<style>
  body {
    background-color: #f1f1f1
  }

  .btn-grey {
    background-color: #D8D8D8;
    color: #FFF;
  }

  .rating-block {
    background-color: #FAFAFA;
    border: 1px solid #EFEFEF;
    padding: 15px 15px 20px 15px;
    border-radius: 3px;
  }

  .bold {
    font-weight: 700;
  }

  .padding-bottom-7 {
    padding-bottom: 7px;
  }

  .review-block {
    background-color: #FAFAFA;
    border: 1px solid #EFEFEF;
    padding: 15px;
    border-radius: 3px;
    margin-bottom: 15px;
  }

  .review-block-name {
    font-size: 14px;
    margin: 10px 0;
  }

  .review-block-email,
  .review-block-date {
    font-size: 12px;
  }

  .review-block-rate {
    font-size: 13px;
    margin-bottom: 15px;
  }

  .review-block-title {
    font-size: 15px;
    font-weight: 700;
    margin-bottom: 10px;
  }

  .review-block-description {
    font-size: 13px;
  }
</style>
<h2>Produtos Classificados - <small>Ative, edite os comentários dos produtos</small></h2>

<?php
$i = 0;

$maximo = 25;

$pag = isset($GET['pag']) && $GET['pag'] != '' ? $GET['pag'] : 1;

$inicio = (($pag * $maximo) - $maximo);

$total = (ceil(ProdutosComentarios::count(['conditions' => ['loja_id=?', $CONFIG['loja_id']], 'order' => 'created_at desc']) / $maximo));

$GROUP_PROD = '';

$result = ProdutosComentarios::all(['conditions' => ['loja_id=?', $CONFIG['loja_id']], 'order' => 'created_at desc', 'limit' => $maximo, 'offset' => $inicio]);

$GROUP_PROD = null;

ob_start();
?>
<ul class="pagination">
  <?php
  if ($total > 0) {
    for ($i = $pag - 1, $limiteDeLinks = $i + 5; $i <= $limiteDeLinks; ++$i) {
      if ($i < 1) {
        $i = 1;
        $limiteDeLinks = 5;
      }

      if ($limiteDeLinks > $total) {
        $limiteDeLinks = $total;
        $i = $limiteDeLinks - 4;
      }

      if ($i < 1) {
        $i = 1;
        $limiteDeLinks = $total;
      }

      if ($i == $pag) {
        echo sprintf('<li class="active"><span>%u</span></li>', $i);
      } else {
        echo sprintf('<li><a href="/adm/produtos/produtos-qualificados.php?pag=%u">%u</a></li>', $i, $i);
      }
    }
  }
  ?>
</ul>
<?php
$GET_PAGINATION = ob_get_clean();
echo $GET_PAGINATION;
?>
<div class="row">
  <?php foreach ($result as $rs) { ?>
    <?php if ($GROUP_PROD != $rs->id_produto) {
      $GROUP_PROD = $rs->id_produto; ?>
      <div class="col-md-12">
        <div class="panel panel-default">
          <div class="panel-body">
            <a href="<?php echo implode('/', [substr(URL_BASE, 0, -1), converter_texto($rs->produto->nome_produto), $rs->produto->id, 'p']) ?>" target="_blank" class="show">
              <div class="media">
                <div class="media-left media-middle">
                  <span>
                    <img class="media-object" src="<?php echo Imgs::src($rs->produto->capa->imagem, 'smalls'); ?>" width="205px" />
                  </span>
                </div>
                <div class="media-body">
                  <h4 class="mt35"><?php echo $rs->produto->nome_produto; ?></h4>
                </div>
              </div>
            </a>
          </div>
        </div>
        <hr />
      </div>
    <?php } ?>
    <div class="panel panel-default col-md-8 col-md-offset-2">
      <div class="panel-body">
        <div class="row">
          <div class="col-sm-12">
            <div class="review-block">
              <div class="row">
                <div class="col-sm-3">
                  <?php
                  $image = glob('../../' . URL_VIEWS_BASE_PUBLIC_UPLOAD . 'imgs/users/user-' . md5($rs->cliente->id) . '{.*}', GLOB_BRACE);
                  if (count($image) == 1) {
                    echo '<img src="' . current($image) . '?t=' . time() . '" class="img-rounded"/>';
                  } else {
                    echo '<img src="' . Imgs::src('icon-users.gif', 'public') . '" class="img-rounded"/>';
                  }
                  ?>
                  <div class="review-block-name"><a href="#"><?php echo $rs->cliente->nome ?></a></div>
                  <div class="review-block-email"><?php echo $rs->cliente->email ?></div>
                  <div class="review-block-date"><?php echo strftime('%A, %d de %B de %Y', strtotime((!empty($rs->data) ? $rs->data : $rs->created_at))) ?></div>
                </div>
                <div class="col-sm-5">
                  <div class="review-block-rate">
                    <?php for ($z = 1; $z <= 5; $z++) { ?>
                      <span class="fa fa-star" <?php echo ($rs->nota >= $z ? ' style="color:#f0ad4e"' : '') ?> aria-hidden="true"></span>
                    <?php } ?>
                  </div>
                  <div class="review-block-title"><?php echo $rs->titulocomentario ?></div>
                  <div class="review-block-description"><?php echo $rs->comentario ?></div>

                </div>
                <div class="col-sm-4 text-right">
                  <a href="/adm/produtos/produtos-qualificados.php?acao=<?php echo $rs->ativo == '0' ? 'ativar_comentario' : 'desativar_comentario'; ?>&id=<?php echo $rs->id; ?>&ativo=<?php echo $rs->ativo == '0' ? 1 : 0; ?>" class="btn btn-sm btn-<?php echo $rs->ativo == '0' ? 'success' : 'info'; ?>">
                    <?php echo $rs->ativo == '0' ? 'ativar' : 'desativar'; ?>
                  </a>
                  <a class="btn btn-warning btn-sm" onclick="$('#formulario<?php echo $rs->id; ?>').slideToggle(0); if($(this).html() === 'editar') { $(this).html('cancelar'); } else { $(this).html('editar'); }">editar</a>
                  <a href="/adm/produtos/produtos-qualificados.php?acao=excluir_comentario" class="btn btn-sm btn-danger btn-excluir-comentario" data-id="formulario<?php echo $rs->id; ?>" id="<?php echo $rs->id; ?>">excluir</a>
                </div>
                <form class="col-sm-9 mt15" id="formulario<?php echo $rs->id; ?>" action="/adm/produtos/produtos-qualificados.php?acao=editar_comentario&pag=<?php echo $GET['pag'] ?>" method="post" style="display: none;">
                  <hr />
                  <div class="form-group">
                    <label for="titulocomentario<?php echo $rs->id; ?>">Titulo</label>
                    <input type="text" class="form-control" id="titulocomentario<?php echo $rs->id; ?>" name="titulocomentario" value="<?php echo $rs->titulocomentario; ?>">
                  </div>
                  <div class="form-group">
                    <label for="comentario<?php echo $rs->id; ?>">Comentário</label>
                    <textarea name="comentario" class="form-control" id="comentario<?php echo $rs->id; ?>" cols="70" rows="5"><?php echo $rs->comentario; ?></textarea>
                  </div>
                  <button type="submit" class="btn btn-primary">salvar</button>
                  <input type="hidden" name="id" value="<?php echo $rs->id; ?>" />
                </form>
              </div>
              <hr />
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php } ?>
</div>
<?php echo $GET_PAGINATION; ?>
<?php ob_start(); ?>
<script>
  // $("#div-edicao").on("click", ".btn-editar-comentario", function(e){
  // var id = $( this ).attr("data-id"), idFom = $("#"+ id +" input, #"+ id +" textarea"), formData = $.param( idFom );
  // $.ajax({
  // url: this.href,
  // type: "post",
  // data: formData,
  // dataType: "html",
  // error: function(x,t,m){
  // console.log(x.responseText+"\n"+t+"\n"+m);
  // },
  // success: function(str) {
  // var list = $("<div/>", { html: str });
  // $("#div-edicao").html( list.find("#div-edicao").html() );
  // }
  // });
  // e.preventDefault();
  // });
  // $(document).on("click", ".btn--comentario", function(e) {
  // $.ajax({
  // url: this.href,
  // error: function(x,t,m){
  // console.log(x.responseText+"\n"+t+"\n"+m);
  // },
  // success: function(str) {
  // var list = $("<div/>", { html: str });
  // $("#div-edicao").html( list.find("#div-edicao").html() );
  // }
  // });
  // e.preventDefault();
  // });

  $(document).on("click", ".btn-excluir-comentario", function(e) {
    $("#excluir-comentario").dialog("open").attr({
      "data-id": this.id,
      "data-href": this.href
    });
    e.preventDefault();
  });

  $("<div/>", {
    id: "excluir-comentario",
    html: [
      $("<p>", {
        html: "Digite um motivo para o qual está removendo este comentário (opcional).",
        class: "ft12px"
      }),
      $("<textarea/>", {
        name: "motivo",
        class: "w100",
        rows: "4",
        style: {
          width: "100%",
          height: "75px"
        }
      }),
      $("<div/>", {
        class: "checkbox",
        html: [
          $("<label/>", {
            class: "checkbox",
            html: [
              $("<input/>", {
                css: {
                  "position": "relative",
                  "margin-left": "0px",
                  "opacity": "1",
                  "filter": "alpha(opacity=1)",
                  "width": "auto",
                  "height": "auto",
                  "padding": "0",
                  "margin": "2px"
                },
                name: "is_mail",
                type: "checkbox"
              }),
              "Enviar e-mail!"
            ]
          })
        ]
      })
    ]
  }).dialog({
    title: "Comentário - Exclusão",
    autoOpen: false,
    height: 275,
    width: 350,
    modal: true,
    buttons: {
      "Salvar": function() {
        var id_comentario = $(this).attr("data-id"),
          href = $(this).attr("data-href"),
          motivo = $("textarea[name=motivo]").val();
        $.ajax({
          url: href,
          type: "post",
          data: {
            acao: "acoes",
            motivo: motivo,
            id: id_comentario
          },
          dataType: "html",
          error: function(x, t, m) {
            console.log(x.responseText + "\n" + t + "\n" + m);
          },
          success: function(str) {
            var list = $("<div/>", {
              html: str
            });
            $("#div-edicao").html(list.find("#div-edicao").html());
          },
          complete: function() {
            $(this).dialog("close");
          }
        });
      },
      "Cancelar": function() {
        $(this).dialog("close");
        $("textarea[name=motivo]").empty();
      }
    },
    close: function() {
      $(this).dialog("close");
    }
  });

  //        $( ".paginacao a" ).click(function(e){
  //            $.ajax({
  //                url 	   : this.href,
  //                type 	   : "post",
  //                data	   : "acao=recarregar",
  //                dataType   : "html",
  //                cache	   : false,
  //                beforeSend : function(){ infoSite( "Carregando aguarde...", "info-concluido" ); },
  //                success    : function(str){ $(".conteudos").html( str );  },
  //                error      : function(x,t,m){ infoSite( t+"<br />"+m, "info-errado" ); },
  //            });
  //            e.preventDefault();
  //        });
</script>
<?php $SCRIPT['script_manual'] .= ob_get_clean();
include '../rodape.php';
