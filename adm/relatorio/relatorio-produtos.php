<?php
include '../topo.php';
$Marcas = Marcas::all(['conditions' => ['excluir = 0 and loja_id = ? ', $CONFIG['loja_id']]]);
$Grupos = Grupos::all(['conditions' => ['excluir = 0 and loja_id = ? ', $CONFIG['loja_id']]]);
$Produtos = Produtos::all(['conditions' => ['excluir = 0 and status = 0 and loja_id = ? ', $CONFIG['loja_id']], 'group' => 'codigo_id']);
?>

<style>
  body {
    background-color: #f1f1f1
  }

  .border-top {
    border-top-color: #dedede;
    border-top-width: 1px;
    border-top-style: solid;
  }
</style>

<div class="row">
  <form action="/adm/relatorio/relatorio-produtos-print.php" method="post" target="_blank" class="col-lg-8 col-lg-offset-2 col-md-8 col-md-offset-2 col-sm-12 col-xs-12" id="form_rel">
    <div class="panel panel-default">
      <div class="panel-heading panel-store">RELATÓRIO DE PRODUTOS</div>
      <div class="panel-body">
        <div class="row">
          <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 mb15">
            <input type="checkbox" id="input_0" name="produto" value="1" />
            <label for="input_0" class="input-checkbox"></label> NOME PRODUTO
          </div>
          <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 mb15">
            <input type="checkbox" id="input_1" name="fotos" value="1" />
            <label for="input_1" class="input-checkbox"></label> SEM FOTO
          </div>
          <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 mb15">
            <input type="checkbox" id="input_2" name="estoque" value="1" />
            <label for="input_2" class="input-checkbox"></label> ESTOQUE
          </div>
          <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 mb15">
            <input type="checkbox" id="input_3" name="marca" value="1" />
            <label for="input_3" class="input-checkbox"></label> MARCA
          </div>
          <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 mb15">
            <input type="checkbox" id="input_20" name="margem" value="1" />
            <label for="input_20" class="input-checkbox"></label> MAGEM DE LUCRO
          </div>
          <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 mb15">
            <input type="checkbox" id="input_21" name="custo" value="1" />
            <label for="input_21" class="input-checkbox"></label> PREÇO DE CUSTO
          </div>
          <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 mb15">
            <input type="checkbox" id="input_22" name="promo" value="1" />
            <label for="input_22" class="input-checkbox"></label> PREÇO DE VENDA
          </div>
          <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 mb15">
            <input type="checkbox" id="input_5" name="grupos" value="1" />
            <label for="input_5" class="input-checkbox"></label> GRUPOS
          </div>
          <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 mb15">
            <input type="checkbox" id="input_9" name="frete" value="1" />
            <label for="input_9" class="input-checkbox"></label> SEM FRETE
          </div>
          <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 mb15">
            <input type="checkbox" id="input_6" name="fiscais" value="1" />
            <label for="input_6" class="input-checkbox"></label> CAMPOS FISCAIS
          </div>
        </div>

        <!-- POR MARCAS -->
        <div class="row border-top" style="display: none;" id="produtos_opc">
          <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 mt5 mb15">
            <label class="show bold-3">Nome do produto:</label>
            <select name="produto_id" class="w100" style="width: 100%;">
              <option value="-1">Todos</option>
              <option value="0">Sem Nome</option>
              <?php foreach ($Produtos as $rp) { ?>
                <option value="<?php echo $rp->codigo_id ?>"><?php echo $rp->nome_produto ?></option>
              <?php } ?>
            </select>
          </div>
          <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 mt5 mb15">
            <label class="show bold-3">Ordem:</label>
            <select name="produto_ordem" class="w100" style="width: 100%;">
              <option value="produto_asc">Nome de (A-Z)</option>
              <option value="produto_desc">Nome de (Z-A)</option>
            </select>
          </div>
        </div>

        <div class="row border-top">
          <!-- POR ESTOQUE -->
          <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mt5 mb15" style="display: none;" id="estoque_opc">
            <div class="row">
              <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <label for="estoque_1" class="show bold-3">Estoque De:</label>
                <input type="number" id="estoque_1" name="estoques[]" class="form-input text-right" value="0">
              </div>
              <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <label for="estoque_2" class="show bold-3">Estoque Até:</label>
                <input type="number" id="estoque_2" name="estoques[]" class="form-input text-right" value="0">
              </div>
            </div>
          </div>

          <!-- POR VALORES -->
          <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mt5 mb15" style="display: none;" id="margem_opc">
            <div class="row">
              <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <label for="preco_lucro_1" class="show bold-3">Margem De:</label>
                <input type="text" id="preco_lucro_1" name="preco_lucro[]" class="form-input text-right" value="0" autocomplete="off">
              </div>
              <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <label for="preco_lucro_2" class="show bold-3">Margem Até:</label>
                <input type="text" id="preco_lucro_2" name="preco_lucro[]" class="form-input text-right" value="0" autocomplete="off">
              </div>
            </div>
          </div>

          <!-- POR VALORES -->
          <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mt5 mb15" style="display: none;" id="custo_opc">
            <div class="row">
              <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <label for="preco_custo_1" class="show bold-3">Custo De:</label>
                <input type="text" id="preco_custo_1" name="preco_custo[]" class="form-input text-right" value="0,00" autocomplete="off">
              </div>
              <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <label for="preco_custo_2" class="show bold-3">Custo Até:</label>
                <input type="text" id="preco_custo_2" name="preco_custo[]" class="form-input text-right" value="0,00" autocomplete="off">
              </div>
            </div>
          </div>

          <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mt5 mb15" style="display: none;" id="preco_opc">
            <div class="row">
              <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <label for="preco_promo_1" class="show bold-3">Preço De:</label>
                <input type="text" id="preco_promo_1" name="preco_promo[]" class="form-input text-right" value="0,00" autocomplete="off">
              </div>
              <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <label for="preco_promo_2" class="show bold-3">Preço Até:</label>
                <input type="text" id="preco_promo_2" name="preco_promo[]" class="form-input text-right" value="0,00" autocomplete="off">
              </div>
            </div>
          </div>

        </div>

        <!-- POR MARCAS -->
        <div class="row border-top" style="display: none;" id="marca_opc">
          <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 mt5 mb15">
            <label class="show bold-3">Marca:</label>
            <select name="marca_id" class="w100" style="width: 100%;">
              <option value="0">Sem Marcas</option>
              <option value="-1">Com Marcas</option>
              <?php foreach ($Marcas as $rm) { ?>
                <option value="<?php echo $rm->id ?>"><?php echo $rm->marcas ?></option>
              <?php } ?>
            </select>
          </div>
          <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 mt5 mb15">
            <label class="show bold-3">Ordem:</label>
            <select name="marca_ordem" class="w100" style="width: 100%;">
              <option value="marca_asc">Marcas (A-Z)</option>
              <option value="marca_desc">Marcas (Z-A)</option>
            </select>
          </div>
        </div>
        <!-- POR GRUPOS -->
        <div class="row border-top" style="display: none;" id="grupos_opc">
          <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 mt5 mb15">
            <label class="show bold-3">Grupos:</label>
            <select name="grupos_id" class="w100" style="width: 100%;">
              <option value="0">Sem Grupo (Também se ressume para os Sub Grupos)</option>
              <option value="-1">Com Grupo (Também se ressume para os Sub Grupos)</option>
              <?php foreach ($Grupos as $rgp) { ?>
                <option value="<?php echo $rgp->id ?>"><?php echo $rgp->grupo ?></option>
              <?php } ?>
            </select>
          </div>
          <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 mt5 mb15">
            <label class="show bold-3">Ordem:</label>
            <select name="grupos_ordem" class="w100" style="width: 100%;">
              <option value="grupos_asc">Grupos (A-Z)</option>
              <option value="grupos_desc">Grupos (Z-A)</option>
            </select>
          </div>
        </div>

        <!-- CAMPOS FISCAIS -->
        <div class="row border-top" style="display: none; background-color: #d8e5e8;" id="fiscais_opc">
          <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 mt5 mb15">
            <label class="show bold-3">CSOSN:</label>
            <select name="csosn" class="w100" style="width: 100%;">
              <option value="1">Preenchidos</option>
              <option value="0">Não Preenchidos</option>
            </select>
          </div>
          <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 mt5 mb15">
            <label class="show bold-3">Tipo Unidade:</label>
            <select name="unid" class="w100" style="width: 100%;">
              <option value="1">Preenchidos</option>
              <option value="0">Não Preenchidos</option>
            </select>
          </div>
          <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 mt5 mb15">
            <label class="show bold-3">CFOP:</label>
            <select name="cfop" class="w100" style="width: 100%;">
              <option value="1">Preenchidos</option>
              <option value="0">Não Preenchidos</option>
            </select>
          </div>
          <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 mt5 mb15">
            <label class="show bold-3">NCM:</label>
            <select name="ncm" class="w100" style="width: 100%;">
              <option value="1">Preenchidos</option>
              <option value="0">Não Preenchidos</option>
            </select>
          </div>
          <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 mt5 mb15">
            <label class="show bold-3">CEST:</label>
            <select name="cest" class="w100" style="width: 100%;">
              <option value="1">Preenchidos</option>
              <option value="0">Não Preenchidos</option>
            </select>
          </div>
        </div>

        <div class="text-center">
          <button type="submit" formaction="/adm/relatorio/relatorio-produtos-print.php" name="rel_lucro" value="" class="btn btn-primary mt15">imprimir relatório (incompletos)</button>
          <button type="submit" formaction="/adm/relatorio/relatorio-produtos-print.php" name="rel_lucro" value="rel_lucro" class="btn btn-primary mt15">imprimir relatório (margem lucro)</button>
        </div>

      </div>
    </div>
  </form>
</div>

<?php ob_start(); ?>
<script>
  $("#form_rel").on("submit", function(e) {
    var estoque_1 = $("#estoque_1").val(),
      estoque_2 = $("#estoque_2").val(),

      estoque_1 = estoque_1.replace(/\D/, ''),
      estoque_2 = estoque_2.replace(/\D/, ''),

      preco_promo_1 = $("#preco_promo_1").val(),
      preco_promo_2 = $("#preco_promo_2").val(),

      preco_promo_1 = preco_promo_1.replace(/\D/, ''),
      preco_promo_2 = preco_promo_2.replace(/\D/, '');

    if (estoque_1 > estoque_2) {
      alert("O Estoque De, não pode ser maior que o Estoque Até");
      return false;
    }

    if (preco_promo_1 > preco_promo_2) {
      alert("O Preço De, não pode ser maior que o Preço Até");
      return false;
    }
  });

  $("input[name=produto]").on("change", function(e) {
    var nome_produto = $(e.target);

    if (!nome_produto.is(":checked")) {
      $("#produtos_opc").fadeOut(0);
    } else {
      $("#produtos_opc").fadeIn(550);
    }
  });

  $("input[name=estoque]").on("change", function(e) {
    var estoque = $(e.target);

    if (!estoque.is(":checked")) {
      $("#estoque_opc").fadeOut(0).find("input").val(0);
    } else {
      $("#estoque_opc").fadeIn(550);
    }
  });

  $("input[name=marca]").on("change", function(e) {
    var marca = $(e.target);

    if (!marca.is(":checked")) {
      $("#marca_opc").fadeOut(0);
      $("#marca_opc").find("select").select2("val", "0");
    } else {
      $("#marca_opc").fadeIn(550);
    }
  });

  $("input[name=grupos]").on("change", function(e) {
    var grupo = $(e.target);

    if (!grupo.is(":checked")) {
      $("#grupos_opc").fadeOut(0);
      $("#grupos_opc").find("select").select2("val", "0");
    } else {
      $("#grupos_opc").fadeIn(550);
    }
  });

  $("input[name=fiscais]").on("change", function(e) {
    var fiscais = $(e.target);

    if (!fiscais.is(":checked")) {
      $("#fiscais_opc").fadeOut(0);
      $("#fiscais_opc").find("select").select2("val", "1");
    } else {
      $("#fiscais_opc").fadeIn(550);
    }
  });

  $("input[name=margem]").on("change", function(e) {
    var margem_venda = $(e.target);

    if (!margem_venda.is(":checked")) {
      $("#margem_opc").fadeOut(0).find("input").val("0");

    } else {
      $("#margem_opc").fadeIn(550);
    }
  });

  $("input[name=custo]").on("change", function(e) {
    var custo_venda = $(e.target);

    if (!custo_venda.is(":checked")) {
      $("#custo_opc").fadeOut(0).find("input").val("0,00");

    } else {
      $("#custo_opc").fadeIn(550);
    }
  });

  $("input[name=promo]").on("change", function(e) {
    var promo_venda = $(e.target);

    if (!promo_venda.is(":checked")) {
      $("#preco_opc").fadeOut(0).find("input").val("0,00");

    } else {
      $("#preco_opc").fadeIn(550);
    }
  });


  $("input[name='preco_custo[]'], input[name='preco_promo[]']").mask("#.##0,00", {
    reverse: true
  });
</script>
<?php
$SCRIPT['script_manual'] .= ob_get_clean();
include '../rodape.php';
