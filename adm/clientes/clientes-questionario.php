<?php
include '../topo.php';

// Salvar o formulário com as perguntas
if (isset($POST['acao']) && $POST['acao'] == 'formulario_pergunta_salvar') {


  $LojasQuestionario = LojasQuestionario::action_cadastrar_editar(['LojasQuestionario' => [(int)$POST['id'] => [
    'parent_id' => 0,
    'descricao' => $POST['descricao'],
    'input_type' => $POST['input_type']
  ]]], (empty($POST['id']) ? 'cadastrar' : 'alterar'), 'descricao');


  if ($POST['id'] > 0)
    header('Location: /adm/clientes/clientes-questionario.php');
  else
    header('Location: /adm/clientes/clientes-questionario.php?acao=cadastrar_questao_opc');

  return;
}

// Salvar o formulário das opções das perguntas
if (isset($POST['acao']) && $POST['acao'] == 'formulario_salvar_opc') {

  LojasQuestionario::action_cadastrar_editar(['LojasQuestionario' => [(int)$POST['id'] => [
    'parent_id' => $POST['parent_id'],
    'descricao' => $POST['descricao'],
    'input_type' => $POST['input_type']
  ]]], (empty($POST['id']) ? 'cadastrar' : 'alterar'), 'descricao');

  header('Location: /adm/clientes/clientes-questionario.php');
  return;
}

// Salvar o formulário das opções das perguntas
if (isset($GET['acao']) && ($GET['acao'] == 'excluir_questao' || $GET['acao'] == 'excluir_questao_opc')) {

  LojasQuestionario::action_cadastrar_editar(['LojasQuestionario' => [(int)$GET['id'] => [
    'descricao' => $POST['descricao']
  ]]], 'delete', 'descricao');

  header('Location: /adm/clientes/clientes-questionario.php');
  return;
}
$rws = new stdClass();
$questaoopcao = array();
?>
<div id="div-edicao">
  <h2>
    Questionário de Clientes
    <small>Crie suas perguntas para seus clientes</small>
    <?php if (!isset($GET['acao'])) { ?>
      <a href="/adm/clientes/clientes-questionario.php?acao=cadastrar_questao_opc" class="btn btn-primary btn-sm pull-right ml15">
        cadastrar opções das pergunta
      </a>
      <a href="/adm/clientes/clientes-questionario.php?acao=cadastrar_questao" class="btn btn-success btn-sm pull-right">
        cadastrar pergunta
      </a>
    <?php } ?>
  </h2>

  <div class="row">
    <?php
    $all_questions = LojasQuestionario::all(['conditions' => ['id > ? and parent_id = ?', 0, 0], 'order' => 'id DESC']);

    if (isset($GET['acao']) && $GET['acao'] == 'editar_questao' || isset($GET['acao']) && $GET['acao'] == 'cadastrar_questao') :

      if (isset($GET['id']) && $GET['id'] > 0)
        $rws = LojasQuestionario::find((int)$GET['id']); ?>

      <form class="col-md-7 col-xs-12 fieldset col-md-offset-2" action="/adm/clientes/clientes-questionario.php" method="post">

        <div class="row">
          <div class="form-group col-md-10">
            <label class="bold" for="pergunta">Digite a pergunta</label>
            <div class="input-group">
              <div class="input-group-addon"><i class="fa fa-question-circle-o"></i></div>
              <input type="text" class="form-control" name="descricao" id="pergunta" placeholder="Digite uma pergunta para seu cliente!" value="<?php echo $rws->descricao ?>">
            </div>
          </div>
        </div>

        <button type="submit" class="btn btn-primary">Salvar pergunta</button>
        <input type="hidden" name="acao" value="formulario_pergunta_salvar" />
        <input type="hidden" name="id" value="<?php echo $rws->id ?>" />
      </form>

    <?php
    // Cadastrar/Editar as opções das perguntas
    elseif (isset($GET['acao']) && $GET['acao'] == 'editar_questao_opc' || isset($GET['acao']) && $GET['acao'] == 'cadastrar_questao_opc') :

      if (isset($GET['id']) && $GET['id'] > 0)
        $rws = LojasQuestionario::find((int)$GET['id']);

      if (isset($GET['parent_id']) && $GET['parent_id'] > 0)
        $rws_parent_id = LojasQuestionario::find((int)$GET['parent_id']);

      if (count($rws_parent_id) > 0)
        foreach ($rws_parent_id->questaoopcao as $values)
          array_push($questaoopcao, ['id' => $values->id, 'descricao' => $values->descricao, 'input_type' => $values->input_type]); ?>

      <form class="col-md-8 col-xs-12 fieldset col-md-offset-2" action="/adm/clientes/clientes-questionario.php" method="post">
        <div class="form-group">
          <label class="bold" for="pergunta">Selecione a pergunta</label>
          <div class="input-group">
            <div class="input-group-addon"><i class="fa fa-question-circle-o"></i></div>
            <select name="parent_id" class="form-control select_no_init">
              <?php foreach ($all_questions as $questions) : ?>
                <option value="<?php echo $questions->id ?>" <?php echo ($questions->id == $rws->parent_id ? 'selected' : '') ?>><?php echo $questions->descricao ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="row">
          <div class="form-group col-md-10">
            <label class="bold" for="pergunta">Digite as opções da pergunta</label>
            <div class="input-group">
              <div class="input-group-addon"><i class="fa fa-question-circle-o"></i></div>
              <input type="text" class="form-control" name="descricao" id="pergunta" placeholder="Digite uma pergunta para seu cliente!" value="<?php echo $rws->descricao ?>">
            </div>
          </div>
        </div>

        <p>Opções da pergunta</p>

        <strong>Selcione o tipo do campo para o formulário</strong>

        <div class="checkbox mb15">
          <label>
            <input type="radio" name="input_type" value="vachar" id="vachar<?php echo $values->id ?>" <?php echo count(LojasQuestionario::custom_array_search($questaoopcao, 'vachar', 'input_type', null)) ? 'checked' : '' ?>>
            <span for="vachar<?php echo $values->id ?>" class="input-text fa"></span>
            Campo digitação (<small>Permite até 255 caracteres.</small>)
          </label>
        </div>
        <div class="checkbox mb15">
          <label>
            <input type="radio" name="input_type" value="checkbox" id="checkbox<?php echo $values->id ?>" <?php echo count(LojasQuestionario::custom_array_search($questaoopcao, 'checkbox', 'input_type', null)) ? 'checked' : '' ?>>
            <span for="checkbox<?php echo $values->id ?>" class="input-checkbox fa"></span>
            Selecionar vários
          </label>
        </div>
        <div class="checkbox mb15">
          <label>
            <input type="radio" name="input_type" value="radio" id="radio<?php echo $values->id ?>" <?php echo count(LojasQuestionario::custom_array_search($questaoopcao, 'radio', 'input_type', null)) ? 'checked' : '' ?>>
            <span for="radio" class="input-radio fa"></span>
            Selecionar um único
          </label>
        </div>
        <div class="checkbox mb15">
          <label>
            <input type="radio" name="input_type" value="avaliable" id="radio<?php echo $values->id ?>" <?php echo count(LojasQuestionario::custom_array_search($questaoopcao, 'avaliable', 'input_type', null)) ? 'checked' : '' ?>>
            <span for="radio" class="input-stars fa"></span>
            Avaliar Produto Adquirido
          </label>
        </div>
        <button type="submit" class="btn btn-primary">Salvar</button>
        <a href="/adm/clientes/clientes-questionario.php" class="btn btn-success">
          voltar
        </a>
        <input type="hidden" name="acao" value="formulario_salvar_opc" />
        <input type="hidden" name="id" value="<?php echo $values->id ?>" />
      </form>
    <?php else : ?>
      <div class="col-md-12 col-xs-12 mt15">
        <table class="table table-borded">
          <tr>
            <th>
              Questões
            </th>
            <th nowrap="nowrap" width="1%">
              Ações
            </th>
          </tr>
          <?php
          $group = null;
          $LojasQuestionario = LojasQuestionario::all(['conditions' => ['id > ? and parent_id = ?', 0, 0], 'order' => 'id ASC']);

          foreach ($LojasQuestionario as $key => $values) :

            foreach ($values->questaoopcao as $key1 => $values2) : ?>
              <tr>
                <td colspan="2">
                  <?php if ($group != $values->descricao) : $group = $values->descricao; ?>
                    <h4 style="background-color: #f0f0f0;padding: 7.5px" class="mt0 mb0">
                      <?php echo $values->descricao ?>
                      <a href="/adm/clientes/clientes-questionario.php?acao=excluir_questao&id=<?php echo $values2->parent_id ?>" class="btn btn-info pull-right btn-xs ml15" onclick="return confirm('Deseja realmente excluir!')">
                        excluir
                      </a>
                      <a href="/adm/clientes/clientes-questionario.php?acao=editar_questao&id=<?php echo $values2->parent_id ?>" class="btn btn-success btn-xs pull-right">
                        editar
                      </a>
                    </h4>
                  <?php endif; ?>
                  <div class="checkbox mb0 text-right">

                    <?php if ($values2->input_type == 'avaliable') : ?>
                      <label class="pull-left">
                        <input type="radio" name="input_type<?php echo $values2->id ?>" id="radio<?php echo $values2->id ?>">
                        <span for="radio<?php echo $values2->id ?>" class="input-stars fa"></span>
                        <?php echo $values2->descricao ?>
                      </label>
                    <?php endif; ?>

                    <?php if ($values2->input_type == 'radio') : ?>
                      <label class="pull-left">
                        <input type="radio" name="input_type<?php echo $values2->id ?>" id="radio<?php echo $values2->id ?>">
                        <span for="radio<?php echo $values2->id ?>" class="input-radio fa"></span>
                        <?php echo $values2->descricao ?>
                      </label>
                    <?php endif; ?>

                    <?php if ($values2->input_type == 'checkbox') : ?>
                      <label class="pull-left">
                        <input type="checkbox" name="input_type<?php echo $values2->id ?>" id="checkbox<?php echo $values2->id ?>">
                        <span for="checkbox<?php echo $values2->id ?>" class="input-checkbox fa"></span>
                        <?php echo $values2->descricao ?>
                      </label>
                    <?php endif; ?>

                    <?php if ($values2->input_type == 'vachar') : ?>
                      <label class="pull-left">
                        <?php echo $values2->descricao ?>
                        <small>(Campo digitação)<small>
                      </label>
                    <?php endif; ?>

                    <a href="/adm/clientes/clientes-questionario.php?acao=editar_questao_opc&parent_id=<?php echo $values2->parent_id ?>&id=<?php echo $values2->id ?>" class="btn btn-warning btn-xs">
                      editar
                    </a>
                    <a href="/adm/clientes/clientes-questionario.php?acao=excluir_questao_opc&parent_id=<?php echo $values2->parent_id ?>&id=<?php echo $values2->id ?>" class="btn btn-danger btn-xs" onclick="return confirm('Deseja realmente excluir!')">
                      excluir
                    </a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endforeach;  ?>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php ob_start(); ?>
<script>
  // function busca_cidade( a, b ) {
  // var cep = a,
  // id=b.target.id;

  // $.ajax({
  // url: "../",
  // type: "post",
  // data: { acao : "BuscaCidade", cep : cep },
  // dataType: "json",
  // beforeSend: function() {
  // JanelaModal.find("#cidade"+id).val( "Carregando..." );
  // JanelaModal.find("#uf"+id).val( "" );
  // },
  // success: function( str ) {
  // JanelaModal.find("#cidade"+id).val( str.cidade );
  // JanelaModal.find("#uf"+id).val( str.uf );
  // },
  // error: function( x,m,t ){
  // alert( x.responseText );
  // }
  // });
  // }

  // $("#div-edicao").on("click", ".analisar-cliente", function(e){
  // e.preventDefault();
  // $.ajax({
  // url: this.href||e.target.href,
  // cache: false,
  // dataType: "html",
  // success: function( str ){
  // var list = $("<div/>", { html: str });

  // JanelaModal
  // .dialog({
  // autoOpen: true,
  // width: 850,
  // heigth: 650,
  // title: "Clientes Analisar/Alterar"
  // })
  // .html( list.find("#div-edicao").html() );
  // },
  // error: function(x,t,m){
  // console.log( x.responseText+"\n"+t+"\n"+m );
  // }
  // });
  // });

  // var SPMaskBehavior = function (val) {
  // return val.replace(/\D/g, '').length === 11 ? "(00) 00000-0000" : "(00) 0000-00009";
  // };
  // var spOptions = {
  // onKeyPress: function(val, e, field, options) {
  // field.mask(SPMaskBehavior.apply({}, arguments), options);
  // }
  // };

  // JanelaModal.find("input[name=cep]").mask("00000-000", { onComplete : busca_cidade });
  // JanelaModal.find('input[name=data_nascimento]').mask('99 / 99 / 9999');
  // JanelaModal.find('input[name=telefone]').mask(SPMaskBehavior, spOptions);
  // JanelaModal.find('input[name=celular]').mask(SPMaskBehavior, spOptions);

  // JanelaModal.on('click', 'input[type=radio]', function(e) {
  // if( $(this).val() === 'true' ) {
  // $( '#conteudos-recarregar' ).find('input[name=pesquisar]').mask('999.999.999-99');
  // } else {
  // $( '#conteudos-recarregar' ).find('input[name=pesquisar]').unmask('');
  // }

  // /**
  // * Nesse if vai passar somente datas
  // */
  // if($(this).val() === 'data'){
  // $('#ocultar-datas').fadeIn(10);
  // } else {
  // $('#ocultar-datas').fadeOut(0);
  // }
  // });
</script>
<?php
$SCRIPT['script_manual'] .= ob_get_clean();

include '../rodape.php';
