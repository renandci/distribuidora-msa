<?php
$rws = new stdClass();
$questaoopcao = [];
?>
<div class="clearfix">
  <h1 class="text-center">Questionário de Satisfação</h1>
  <p class="text-center"><?php echo !empty($STORE['questionario']['text']) ? nl2br($STORE['questionario']['text']) : null; ?></p>
  <div class="row">
    <form class="col-md-12 col-xs-12" action="/identificacao/questionario" method="post" id="cliente_questionario">
      <?php
      $ACAO = filter_input(INPUT_POST, 'acao');

      if (!empty($ACAO) && $ACAO == strrev(sha1('enviar_formulario'))) :

        $json = [];

        $input_type = filter_input_array(INPUT_POST);

        $input_type['pedido_id'] = explode('_', $input_type['pedido_id']);

        $input_type['pedido_id'] = strrev(end($input_type['pedido_id']));

        $Pedidos = Pedidos::first(['conditions' => ['sha1(id)=?', $input_type['pedido_id']]]);

        $attributes = ['id_pedido' => $Pedidos->id, 'loja_id' => $CONFIG['loja_id']];

        if (PedidosQuestionario::count(['conditions' => ['sha1(id_pedido) = ?', $input_type['pedido_id']]]) == 0) {
          $post = new PedidosQuestionario($attributes);
          $post->save();
        }

        $json['cliente'] = $Pedidos->cliente->nome;

        $json['codigo'] = $Pedidos->codigo;

        $input_type_rating = $input_type['rating'];

        // Formulario para Comentários de Avaliações dos produtos
        if (is_array($input_type_rating)) :

          foreach ($input_type_rating as $k => $v) :

            $created = [];

            foreach ($v as $x => $v) :

              $Produtos = Produtos::find((int)$input_type_rating['id'][$x]);

              $created[] = [
                'id_produto' => $input_type_rating['id'][$x],
                'id_cliente' => $Pedidos->cliente->id,
                'titulocomentario' => $input_type_rating['titulo'][$x],
                'comentario' => $input_type_rating['comentario'][$x],
                'nota' => $input_type_rating['nota'][$x],
                'ativo' => 0,
                'produto' => $Produtos->nome_produto,
                'img' => $Produtos->capa->imagem
              ];

            endforeach;

          endforeach;

          foreach ($created as $created_val) :

            array_push($json, [
              'titulo' => $created_val['titulocomentario'],
              'comentario' => $created_val['comentario'],
              'produto' => $created_val['produto'],
              'imagem' => $created_val['img'],
              'nota' => $created_val['nota'],
            ]);

            unset($created_val['produto'], $created_val['img']);

            $ProdComent = new ProdutosComentarios($created_val);
            $ProdComent->save();

          endforeach;

        endif;

        if (is_array($input_type['input_type']))
          foreach ($input_type['input_type'] as $keys => $values) :
            $Questao = LojasQuestionario::find((int)$keys);
            array_push($json, ['pergunta' => $Questao->descricao, 'resposta' => $values]);
          endforeach;

        $Pedidos->questionario->id_pedido = $Pedidos->id;
        $Pedidos->questionario->json = json_encode($json, JSON_UNESCAPED_UNICODE);
        $Pedidos->questionario->save();
      ?>
        <div class="alert alert-success text-center">
          Obrigado <?php echo $Pedidos->cliente->nome ?> por responder nosso questionário!<br />
        </div>
        <div class="text-center">
          <a href="/identificacao/meus-pedidos" class="btn btn-primary">meu pedidos</a>
          <a href="/produtos" class="btn btn-secundary">continuar comprando!</a>
        </div>
        <?php
      else :
        $group = null;
        try {
          $LojasQuestionario = LojasQuestionario::all(['conditions' => ['id > ? and parent_id = ? and loja_id=?', 0, 0, $CONFIG['loja_id']], 'order' => 'id ASC']);

          $input_type['pedido_id'] = filter_input(INPUT_GET, 'pedido_id', FILTER_SANITIZE_STRING);

          $input_type['pedido_id'] = explode('_', $input_type['pedido_id']);

          $input_type['pedido_id'] = strrev(end($input_type['pedido_id']));

          $Pedidos = Pedidos::first(['conditions' => ['sha1(id)=?', $input_type['pedido_id']]]);

          foreach ($Pedidos->pedidos_vendas as $kpr => $prd) :

            foreach ($LojasQuestionario as $key => $values) :

              foreach ($values->questaoopcao as $key1 => $values2) : ?>

                <?php if ($group != $values->descricao) : $group = $values->descricao; ?>
                  <h4<?php echo ($values2->input_type == 'avaliable' ? ' class="text-center"' : null) ?>><?php echo $values->descricao ?></h4>
                  <?php endif; ?>
                  <div class="checkbox mb25 ml15">
                    <!--[AVALIABLE PRODUCT]-->
                    <?php if ($values2->input_type == 'avaliable') : ?>
                      <!--
									<label>
										<input type="radio" name="input_type[<?php echo $values2->parent_id ?>]" id="radio<?php echo $values2->parent_id ?>" value="<?php echo $values2->descricao ?>">
										<span for="radio<?php echo $values2->parent_id ?>" class="input-radio fa ft18px"></span>
										<font><?php echo $values2->descricao ?> dsasasafdsfasdfsa</font>
									</label>
									-->
                      <div class="clearfix">
                        <div class="row">
                          <div class="col-md-3 col-xs-12"></div>
                          <div class="col-md-6 col-xs-12">
                            <div class="text-center">
                              <h3><?php echo $prd->produto->nome_produto ?></h3>
                              <img src="<?php echo Imgs::src($prd->produto->capa->imagem, 'smalls') ?>" class="img-responsive center-block" />
                            </div>
                            <div class="form-group" id="rating-ability-wrapper-<?php echo $prd->id ?>">
                              <label class="control-label" for="rating">
                                <input type="hidden" id="selected_rating_<?php echo $prd->id ?>" name="rating[nota][]" value="" required="required" />
                              </label>
                              <h2 class="bold rating-header" style="margin-top: -10px; margin-bottom: 10px;"><span class="selected-rating-<?php echo $prd->id ?>">0</span><small> / 5</small></h2>
                              <button type="button" class="btn_rating_<?php echo $prd->id ?> btn btn-default " data-attr="1" id="rating-star-1-<?php echo $prd->id ?>">
                                <i class="fa fa-star" aria-hidden="true"></i>
                              </button>
                              <button type="button" class="btn_rating_<?php echo $prd->id ?> btn btn-default " data-attr="2" id="rating-star-2-<?php echo $prd->id ?>">
                                <i class="fa fa-star" aria-hidden="true"></i>
                              </button>
                              <button type="button" class="btn_rating_<?php echo $prd->id ?> btn btn-default " data-attr="3" id="rating-star-3-<?php echo $prd->id ?>">
                                <i class="fa fa-star" aria-hidden="true"></i>
                              </button>
                              <button type="button" class="btn_rating_<?php echo $prd->id ?> btn btn-default " data-attr="4" id="rating-star-4-<?php echo $prd->id ?>">
                                <i class="fa fa-star" aria-hidden="true"></i>
                              </button>
                              <button type="button" class="btn_rating_<?php echo $prd->id ?> btn btn-default " data-attr="5" id="rating-star-5-<?php echo $prd->id ?>">
                                <i class="fa fa-star" aria-hidden="true"></i>
                              </button>
                            </div>
                            <div class="form-horizontal">
                              <div class="form-group">
                                <label for="rating_titulo_<?php echo $prd->id ?>" class="col-sm-2 control-label">Título</label>
                                <div class="col-sm-10">
                                  <input type="text" name="rating[titulo][]" class="form-control" id="rating_titulo_<?php echo $prd->id ?>" placeholder="Digite aqui o titulo do seu comentário" autocomplete="off" />
                                </div>
                              </div>
                              <div class="form-group">
                                <label for="rating_comentario_<?php echo $prd->id ?>" class="col-sm-2 control-label">Comentário</label>
                                <div class="col-sm-10">
                                  <textarea id="rating_comentario_<?php echo $prd->id ?>" name="rating[comentario][]" class="form-control" placeholder="Digite aqui o seu comentário" style="height: 150px;"></textarea>
                                </div>
                              </div>
                              <input type="text" name="rating[id][]" class="hidden" value="<?php echo $prd->id_produto ?>" />
                            </div>
                          </div>
                          <div class="col-md-3 col-xs-12"></div>
                        </div>
                      </div>
                      <?php ob_start() ?>
                      <script>
                        $(".btn_rating_<?php echo $prd->id ?>").mouseenter(function() {
                          var previous_value = $("#selected_rating_<?php echo $prd->id ?>").val(),
                            selected_value = $(this).attr("data-attr");

                          $("#selected_rating_<?php echo $prd->id ?>").val(selected_value);

                          $(".selected-rating-<?php echo $prd->id ?>").empty();
                          $(".selected-rating-<?php echo $prd->id ?>").html(selected_value);

                          for (i = 1; i <= selected_value; ++i) {
                            $("#rating-star-" + i + "-<?php echo $prd->id ?>").toggleClass('btn-warning');
                            $("#rating-star-" + i + "-<?php echo $prd->id ?>").toggleClass('btn-default');
                          }

                          for (ix = 1; ix <= previous_value; ++ix) {
                            $("#rating-star-" + ix + "-<?php echo $prd->id ?>").toggleClass('btn-warning');
                            $("#rating-star-" + ix + "-<?php echo $prd->id ?>").toggleClass('btn-default');
                          }
                        });

                        $(".btn_rating_<?php echo $prd->id ?>").on('click', (function(e) {

                          var previous_value = $("#selected_rating_<?php echo $prd->id ?>").val(),
                            selected_value = $(this).attr("data-attr");

                          $("#selected_rating_<?php echo $prd->id ?>").val(selected_value);

                          $(".selected-rating-<?php echo $prd->id ?>").empty();
                          $(".selected-rating-<?php echo $prd->id ?>").html(selected_value);

                          for (i = 1; i <= selected_value; ++i) {
                            $("#rating-star-" + i + "-<?php echo $prd->id ?>").toggleClass('btn-warning');
                            $("#rating-star-" + i + "-<?php echo $prd->id ?>").toggleClass('btn-default');
                          }

                          for (ix = 1; ix <= previous_value; ++ix) {
                            $("#rating-star-" + ix + "-<?php echo $prd->id ?>").toggleClass('btn-warning');
                            $("#rating-star-" + ix + "-<?php echo $prd->id ?>").toggleClass('btn-default');
                          }
                        }));

                        // Força o usuario dar uma nova avaliable
                        $(".btn_rating_<?php echo $prd->id ?>").eq(4).click();
                      </script>
                      <?php $str['script_manual'] .= ob_get_clean(); ?>
                    <?php endif; ?>
                    <!--[END AVALIABLE PRODUCT]-->

                    <?php if ($values2->input_type == 'radio') : ?>
                      <label>
                        <input type="radio" name="input_type[<?php echo $values2->parent_id ?>]" id="radio<?php echo $values2->parent_id ?>" value="<?php echo $values2->descricao ?>">
                        <span for="radio<?php echo $values2->parent_id ?>" class="input-radio fa ft18px"></span>
                        <font><?php echo $values2->descricao ?></font>
                      </label>
                    <?php endif; ?>

                    <?php if ($values2->input_type == 'checkbox') : ?>
                      <label>
                        <input type="checkbox" name="input_type[<?php echo $values2->parent_id ?>]" id="checkbox<?php echo $values2->id ?>" value="<?php echo $values2->descricao ?>">
                        <span for="checkbox<?php echo $values2->id ?>" class="input-checkbox fa ft18px"></span>
                        <font><?php echo $values2->descricao ?></font>
                      </label>
                    <?php endif; ?>

                    <?php if ($values2->input_type == 'vachar') : ?>
                      <label style="width: 70%; min-width: 300px; max-width: 600px">
                        <font class="show"><?php echo $values2->descricao ?></font>
                        <textarea name="input_type[<?php echo $values2->parent_id ?>]" id="vachar<?php echo $values2->id ?>" style="width: 100%; height: 105px"></textarea>
                        <!--
										<input type="text" name="input_type[<?php echo $values2->parent_id ?>]" id="vachar<?php echo $values2->id ?>" value="">
										<span for="vachar<?php echo $values2->id ?>" class="input-vachar fa ft18px"></span>
										-->
                      </label>
                    <?php endif; ?>
                  </div>
                <?php endforeach; ?>
              <?php endforeach; ?>
            <?php endforeach; ?>
            <button type="submit" class="btn btn-primary center-block">Enviar o formulário de satisfação</button>
            <input type="hidden" name="acao" value="<?php echo strrev(sha1('enviar_formulario')) ?>">
            <input type="hidden" name="pedido_id" value="<?php echo $GET['pedido_id'] ?>">
            <input type="hidden" name="cliente_id" value="<?php echo $GET['cliente_id'] ?>">
    </form>
  <?php } catch (exception $e) {
          echo 'Você não tem acesso a esse conteudo!';
        } ?>
<?php endif; ?>
  </div>
</div>

<?php ob_start(); ?>
<script>
  <?php
  if (file_exists('./public/js/jquery.form.js'))
    echo file_get_contents('./public/js/jquery.form.js');
  ?>
  $(function() {

    function validate(formData, jqForm, options) {
      // jqForm is a jQuery object which wraps the form DOM element
      //
      // To validate, we can access the DOM elements directly and return true
      // only if the values of both the username and password fields evaluate
      // to true

      var names = [],
        text = null;

      $('input[type="radio"],input[type="checkbox"]').each(function() {
        // Creates an array with the names of all the different checkbox group.
        names[$(this).attr('name')] = true;
      });

      // console.log(text);
      // Goes through all the names and make sure there's at least one checked.
      for (name in names) {
        var radio_buttons = $("input[name='" + name + "']");
        if (radio_buttons.filter(':checked').length == 0) {
          $("#modal-site").modal("show").find(".modal-body").html("Selecione ao menos uma opção de resposta!");
          $("input[name='" + name + "']").parents(".checkbox").addClass("has-error");
          return false;
        } else {
          // If you need to use the result you can do so without
          // another (costly) jQuery selector call:
          var val = radio_buttons.val();
          $("input[name='" + name + "']").parents(".checkbox").removeClass("has-error");
        }
      }
    }

    $("#cliente_questionario").ajaxForm({
      beforeSubmit: validate,
      success: function(str) {
        var list = $("<div/>", {
          html: str
        });
        $("#cliente_questionario").html(list.find("#cliente_questionario").html());
      }
    });
  });
</script>
<?php
$str['script_manual'] .= ob_get_clean();
