<?php
include '../topo.php';
?>
<div id="aba4">
  <form action="/adm/produtos/produtos-personalize.php?codigo_id=<?php echo $GET['codigo_id'] ?>" method="post" class="clearfix" id="form-personalize">
    <div class="col-md-6">
      <fieldset style="height: 115px">
        <input name="codigo_id" value="<?php echo $GET['codigo_id'] ?>" type="hidden">
        <label class="show mb10 ft16px">Tipo de Campo</label>
        <span>Texto: </span>
        <input type="radio" name="input_type" id="text" value="input" checked />
        <label for="text" class="input-radio"></label>
        <span>File: </span>
        <input type="radio" name="input_type" id="file" value="file" />
        <label for="file" class="input-radio"></label>
      </fieldset>
    </div>
    <div class="col-md-6">
      <fieldset style="height: 115px">
        <label class="show mb10 ft16px">Adicionar valores nos campos</label>
        <span class="show mb5 ft12px">Nome do campo: </span>
        <input type="text" name="input_description" style="width: 70%" />
        <button type="submit" class="btn btn-primary" style="width: 25%">salvar</button>

      </fieldset>
    </div>
    <?php
    if (isset($GET['acao']) && $GET['acao'] === 'Remover') {
      ProdutosPersonalizados::action_cadastrar_editar(['ProdutosPersonalizados' => [$GET['personalize_id'] => ['id' => $GET['personalize_id']]]], 'delete', 'input_name');
    }

    if (isset($GET['input_description']) && $GET['input_description']) {
      $codigo_id = filter_input(INPUT_GET, 'codigo_id');
      $input_type = filter_input(INPUT_GET, 'input_type');
      $input_description = filter_input(INPUT_GET, 'input_description');
      $input_name = str_replace('-', '_', converter_texto($input_description));

      ProdutosPersonalizados::action_cadastrar_editar([
        'ProdutosPersonalizados' => ['0' => [
          'codigo_id' => $codigo_id,
          'input_type' => $input_type,
          'input_name' => $input_name,
          'input_value' => ($input_type == 'select' ? $input_description : 'text'),
          'input_description' => $input_description,
        ]]
      ], 'cadastrar', 'input_name');
    }
    ?>
    <table class="table mt5 ml15 mr15">
      <tr>
        <th>
          Tipo de Personalização
        </th>
        <th>
          Campo
        </th>
        <th>
          Ações
        </th>
      </tr>
      <?php
      // echo
      $result = ProdutosPersonalizados::find_by_sql('SELECT * FROM produtos_personalizado WHERE codigo_id=? ORDER BY id DESC', [$GET['codigo_id']]);
      foreach ($result as $rs) {
        $rs = $rs->to_array(); ?>
        <tr>
          <td>
            <?php echo $rs['input_description'] ?>
          </td>
          <td align="center" nowrap="nowrap" width="1%">
            <?php echo $rs['input_type'] == 'input' ? 'Texto' : '' ?>
          </td>
          <td align="center" nowrap="nowrap" width="1%">
            <a href="/adm/produtos/produtos-personalize.php?acao=Remover&personalize_id=<?php echo $rs['id'] ?>&codigo_id=<?php echo $GET['codigo_id'] ?>" class="btn btn-danger btn-xs remover-personalize">
              remover
            </a>
          </td>
        </tr>
      <?php } ?>
    </table>
  </form>
</div>
<?php
include '../rodape.php';
