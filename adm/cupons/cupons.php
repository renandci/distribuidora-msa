<?php
$estadosBrasileiros = array(
  'AC' => 'Acre',
  'AL' => 'Alagoas',
  'AP' => 'Amapá',
  'AM' => 'Amazonas',
  'BA' => 'Bahia',
  'CE' => 'Ceará',
  'DF' => 'Distrito Federal',
  'ES' => 'Espírito Santo',
  'GO' => 'Goiás',
  'MA' => 'Maranhão',
  'MT' => 'Mato Grosso',
  'MS' => 'Mato Grosso do Sul',
  'MG' => 'Minas Gerais',
  'PA' => 'Pará',
  'PB' => 'Paraíba',
  'PR' => 'Paraná',
  'PE' => 'Pernambuco',
  'PI' => 'Piauí',
  'RJ' => 'Rio de Janeiro',
  'RN' => 'Rio Grande do Norte',
  'RS' => 'Rio Grande do Sul',
  'RO' => 'Rondônia',
  'RR' => 'Roraima',
  'SC' => 'Santa Catarina',
  'SP' => 'São Paulo',
  'SE' => 'Sergipe',
  'TO' => 'Tocantins'
);
include '../topo.php';

$produto_vlminimo = Cupons::get_preco_min();

// Remove temporario o cupom de desconto
if (isset($GET['acao']) && $GET['acao'] == 'Excluir') {
  $Cupons = Cupons::find((int)$GET['cupom_id']);
  $Cupons->cupom_excluir = 1;
  $Cupons->save();

  header('Location: /adm/cupons/cupons.php');
  return;
}

// TENTA CANCELAR TODOS OS ENVIOS DE E-MAILS
if (isset($GET['acao']) && $GET['acao'] == 'Cancel') {
  $CuponsSend = CuponsSend::all(['conditions' => [
    'id_cupons' => (int)$GET['cupom_id']
  ]]);

  foreach ($CuponsSend ?? [] as $rws) {
    $rws->cancel = 1;
    $rws->save_log();
  }

  header('Location: /adm/cupons/cupons.php');
  return;
}

// TENTA CANCELAR TODOS OS ENVIOS DE E-MAILS
if (isset($GET['acao']) && $GET['acao'] == 'Return') {
  $CuponsSend = CuponsSend::all(['conditions' => [
    'id_cupons' => (int)$GET['cupom_id']
  ]]);

  foreach ($CuponsSend ?? [] as $rws) {
    $rws->cancel = 0;
    $rws->save_log();
  }

  header('Location: /adm/cupons/cupons.php');
  return;
}

?>
<style>
  body {
    background-color: #f1f1f1
  }

  fieldset {
    -webkit-border-radius: 0;
    -moz-border-radius: 0;
    border-radius: 0;
    border-top: none;
    border-left: none;
    border-right: none;
  }
</style>
<div class="mt50 container" id="cupons" <?php echo isset($_GET['acao']) && $_GET['acao'] == 'CriarCupom' ? ' style="max-width: 900px"' : '' ?>>
  <div class="row">
    <div class="panel panel-default" id="cupons">
      <?php if (isset($_GET['acao']) && $_GET['acao'] == 'PesquisarCliente') { ?>
        <div class="panel-body" id="clientes">
          <form>
            <label class="show mb5">Digite o nome ou e-mail do cliente:</label>
            <input type="text" name="cupom_pesquisa" placeholder="Pesquise os dados do cliente..." style="width: 450px" />
            <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button>
          </form>
          <table class="table table-list table-hover mt15">
            <tr>
              <th>Cliente</th>
              <th>E-mail</th>
              <th align="center">Ações</th>
            </tr>
            <?php
            $cupom_pesquisa = $GET['cupom_pesquisa'];
            if ($cupom_pesquisa != '') {
              $sql = sprintf('SELECT id, nome, email FROM clientes WHERE loja_id=%u AND nome like "%s%%" AND email != "" OR (email="%%%s%%" AND email != "") ORDER BY nome ASC', $CONFIG['loja_id'], $cupom_pesquisa, $cupom_pesquisa);
              $result = Clientes::find_by_sql($sql);
              foreach ($result as $rs) { ?>
                <tr>
                  <td nowrap="nowrap" width="1%"><?php echo $rs->nome ?></td>
                  <td><?php echo $rs->email ?></td>
                  <td nowrap="nowrap" width="1%" align="center">
                    <a href="/adm/cupons/cupons.php?acao=CriarCupom&cliente_id=<?php echo $rs->id ?>" class="btn btn-danger btn-xs cliente-selecionado">
                      Selecionar
                    </a>
                  </td>
                </tr>
            <?php }
            }  ?>
          </table>
        </div>
      <?php } ?>

      <div class="panel-heading panel-store text-uppercase">Cupons Descontos</div>
      <?php
      if (isset($_GET['acao']) && $_GET['acao'] == 'CriarCupom') {
        $rs = isset($GET['cupom_id']) && $GET['cupom_id'] > 0 ? Cupons::find($GET['cupom_id']) : null;
        $rs = isset($rs) ? $rs->to_array() : false; ?>

        <form class="clearfix" action="/adm/cupons/cupons-acoes.php" method="post" id="cupom">
          <fieldset>
            <span class="show mb5 ft18px text-center">Gere um código para seu cupom</span>
            <label class="show mb5">Código do cupom: *</label>
            <input name="cupom_codigo" type="text" style="width: 300px;" value="<?php echo $rs['cupom_codigo'] ?>" />
            <button type="button" class="btn btn-secundary" onclick="return Password();">criar um código</button>
          </fieldset>

          <fieldset>
            <div class="clearfix">
              <span class="show mb5 ft18px text-center">Especifique os dados do cumpo de desconto</span>
              <div class="row">
                <div class="col-md-3 mb15">
                  <label class="show mb5">Vl. Desconto: *</label>
                  <input type="text" name="cupom_value" value="<?php echo $rs['cupom_value'] ?>" class="preco-mask text-right" />
                </div>
                <div class="col-md-3 mb15">
                  <label class="show mb5">Tipo de desconto: *</label>
                  <select name="cupom_desconto">
                    <option value="">Selecione</option>
                    <option value="$" <?php echo $rs['cupom_desconto'] == '$' ? ' selected' : ''; ?> selected>Em real (R$)</option>
                    <option value="%" <?php echo $rs['cupom_desconto'] == '%' ? ' selected' : ''; ?>>Em porcentagem (%)</option>
                  </select>
                </div>
                <div class="row"></div>
                <div class="col-md-4">
                  <label class="show mb5">Vl. minimo da compra:</label>
                  <input type="text" name="cupom_valormin" class="preco-mask text-right" placeholder="O minimo será de R$: <?php echo number_format($produto_vlminimo['preco_promo'], 2, ',', '.') ?>" value="<?php echo number_format($rs['cupom_valormin'], 2, ',', '.') ?>" />
                </div>
                <div class="col-md-4">
                  <label class="show mb5">Data inicial:</label>
                  <input type="text" name="cupom_dataini" class="datepicker" value="<?php echo $rs['cupom_dataini'] > 0 ? date('d/m/Y', strtotime($rs['cupom_dataini'])) : date('d/m/Y') ?>">
                </div>
                <div class="col-md-4">
                  <label class="show mb5">Data de validade:</label>
                  <input type="text" name="cupom_datafin" class="datepicker" value="<?php echo $rs['cupom_datafin'] > 0 ? date('d/m/Y', strtotime($rs['cupom_datafin'])) : '' ?>">
                </div>
              </div>
            </div>
          </fieldset>

          <fieldset>
            <div class="clearfix">
              <span class="show mb5 ft18px text-center">Diga o que você deseja fazer com o cupom</span>
              <div class="row">
                <div class="col-md-3 mb15">
                  <label class="show mb5">Selecione os envios: *</label>
                  <select name="cupom_envios" style="width: 100%;">
                    <option value="">Selecione</option>
                    <option value="unico">Envio único</option>
                    <option value="all">Indefinido</option>
                    <option value="uf">Por Estado</option>
                  </select>
                </div>

                <!--[BUSCA POR CLIENTE UNICO]-->
                <div class="col-md-7" id="seleciona_cliente" style="display: none">
                  <button type="button" id="pesquisar-clientes" class="btn btn-secundary mt25">pesquisar clientes</button>
                  <div id="cliente-selecionado" class="mt5">
                    <?php
                    if (isset($GET['cliente_id']) && $GET['cliente_id'] > 0) {
                      $cliente = Clientes::find((int)$GET['cliente_id']);
                      $cliente = $cliente->to_array(); ?>
                      <label>Cliente: </label><span class="ft16px"><?php echo $cliente['nome'] ?></span><br />
                      <label>Tel/Cel: </label><span class="ft16px"><?php echo $cliente['telefone'] ?></span><br />
                      <label>E-mail: </label><span class="ft16px"><?php echo $cliente['email'] ?></span>
                    <?php } ?>
                  </div>
                  <input type="text" name="cliente_id" value="<?php echo $cliente['id'] ?>" disabled style="width:1px;height:1px;padding:0;margin:0;border:none;" />
                </div>
                <!--[END BUSCA POR CLIENTE UNICO]-->

                <!--[BUSCA POR UF]-->
                <div class="col-md-12" id="seleciona_ufs" style="display: none;">
                  <label class="show mb5">Selecione os estados: *</label>
                  <select name="cupom_uf[]" style="width: 100%;" multiple>
                    <option value="">Selecione</option>
                    <?php foreach ($estadosBrasileiros as $k => $ufs) { ?>
                      <option value="<?php echo $k ?>"><?php echo $ufs ?> (<?php echo $k ?>)</option>
                    <?php } ?>
                  </select>
                </div>
                <!--[END BUSCA POR UF]-->

              </div>
            </div>
          </fieldset>
          <div class="col-md-12 mb15 mt15 text-center">
            <button type="submit" class="btn btn-primary">criar/enviar cupom</button>
          </div>
          <input type="hidden" name="acao" value="GerarCupom">
        </form>
      <?php } ?>

      <?php if (!isset($_GET['acao']) && $_GET['acao'] == '') { ?>
        <div class="mt5 mb5 text-center">
          <a href="/adm/cupons/cupons.php?acao=CriarCupom" class="btn btn-primary">
            criar cupom
          </a>
        </div>
        <table class="table table-list table-hover mt15">
          <tr>
            <th>
              Cód.
            </th>
            <th nowrap="nowrap" width="1%">
              Vl. Desconto
            </th>
            <th nowrap="nowrap" width="1%">
              Vl. min. Cupom
            </th>
            <th>
              Data
            </th>
            <th class="text-left">
              Cliente
            </th>
            <th class="text-center">
              Usados
            </th>
            <th>
              Ações
            </th>
          </tr>
          <?php
          $i = 0;
          $maximo = 25;
          $pag = isset($GET['pag']) &&  $GET['pag'] != '' ? $GET['pag'] : 1;
          $inicio = (($pag * $maximo) - $maximo);
          $total = (ceil(Cupons::count(['conditions' => ['cupons.id > 0 AND cupons.cupom_excluir = 0 and cupons.loja_id=?', $CONFIG['loja_id']]])) / $maximo);

          $result = Cupons::all([
            'conditions' => ['cupons.id > 0 AND cupons.cupom_excluir = 0 and cupons.loja_id=?', $CONFIG['loja_id']],
            'order' => 'cupons.id desc, cupons.cupom_usados desc',
            'limit' => $maximo,
            'offset' => ($maximo * ($pag - 1))
          ]);
          foreach ($result as $rs) { ?>
            <tr>
              <td nowrap="nowrap" width="1%">
                <?php echo $rs->cupom_codigo; ?>
              </td>
              <td nowrap="nowrap" width="1%">
                <?php echo $rs->cupom_desconto == '$' ? 'R$: ' . number_format($rs->cupom_value, 2, ',', '.') : ''; ?>
                <?php echo $rs->cupom_desconto == '%' ? round($rs->cupom_value) . '%' : ''; ?>
              </td>
              <td nowrap="nowrap" width="1%">
                <?php echo 'R$: ' . number_format($rs->cupom_valormin, 2, ',', '.'); ?>
              </td>
              <td nowrap="nowrap" width="1%">
                <?php echo $rs->cupom_dataini > 0 ? date('d/m/Y', strtotime($rs->cupom_dataini)) : ''; ?>
                <?php echo $rs->cupom_datafin > 0 ? ' válido até: ' . date('d/m/Y', strtotime($rs->cupom_datafin)) : ''; ?>
              </td>
              <td>
                <?php
                $cuponsSendCount = (int)count($rs->cuponssend);
                if ($cuponsSendCount > 0) {
                  $uf = [];
                  $send = 0;
                  $fila = 0;
                  $cancel = 0;
                  foreach ($rs->cuponssend as $rws_cupom) {
                    $uf[$rws_cupom->cliendereco->uf] = $rws_cupom->cliendereco->uf;
                    if ($rws_cupom->send)
                      $send++;

                    if (!$rws_cupom->send)
                      $fila++;

                    if ($rws_cupom->cancel)
                      $cancel++;
                  }
                ?>
                  <small class="show ft12px bold">Envio por Estados: <?php echo $cuponsSendCount ?> clientes</small>
                  <small class="show ft11px bold">Enviados: <?php echo $send ?> Na fila: <?php echo $fila ?> Cancelados: <?php echo $cancel ?></small>
                  <small class="show ft11px bold">(<?php echo implode(', ', $uf) ?>)</small>
                <?php } ?>
                <?php if ($rs->cliente->id > 0) { ?>
                  <?php echo $rs->cliente->nome . ' | ' ?>
                  <?php echo $rs->cliente->email . ' | ' ?>
                  <?php echo $rs->cliente->telefone ?>
                <?php } ?>
              </td>
              <td nowrap="nowrap" width="1%">
                <?php echo (int)count($rs->pedidos); ?>
              </td>
              <td nowrap="nowrap" width="1%">
                <a href="/adm/cupons/cupons.php?acao=Excluir&cupom_id=<?php echo $rs->id ?>" class="btn btn-danger btn-xs" onclick="if(!confirm('Deseja realmente excluir?')) return false;">excluir</a>
                <?php if ($cuponsSendCount > 0 && $cancel == 0) { ?>
                  <a href="/adm/cupons/cupons.php?acao=Cancel&cupom_id=<?php echo $rs->id ?>" class="btn btn-warning btn-xs" onclick="if(!confirm('Deseja realmente cancelar todos os envios?')) return false;">cancelar envios</a>
                <?php } ?>
                <?php if ($cuponsSendCount > 0 && $cancel > 0) { ?>
                  <a href="/adm/cupons/cupons.php?acao=Return&cupom_id=<?php echo $rs->id ?>" class="btn btn-info btn-xs">ativar reenvios</a>
                <?php } ?>
              </td>
            </tr>
          <?php } ?>
          <tr>
            <td colspan="7">
              <div class="paginacao clearfix">
                <?php
                if ($total > 0) {
                  if ($pag != 1) {
                    echo "<a href=\"/adm/cupons/cupons.php?pag=1\">Primeira página</a>";
                  }

                  for ($i = $pag - 10, $limiteDeLinks = $i + 20; $i <= $limiteDeLinks; ++$i) {
                    if ($i < 1) {
                      $i = 1;
                      $limiteDeLinks = 19;
                    }

                    if ($limiteDeLinks > $total) {
                      $limiteDeLinks = $total;
                      $i = $limiteDeLinks - 20;
                    }

                    if ($i < 1) {
                      $i = 1;
                      $limiteDeLinks = $total;
                    }

                    if ($i == $pag) {
                      echo "<span class=\"at plano-fundo-adm-001\">{$i}</span>";
                    } else {
                      echo "<a href=\"/adm/cupons/cupons.php?pag={$i}\">{$i}</a>";
                    }
                  }

                  if ($pag != $total) {
                    if ($pag == $i && $total > 0) {
                      echo "<span class=\"lipg\">Última página</span>";
                    } else {
                      echo "<a href=\"/adm/cupons/cupons.php?pag={$total}\">Última página</a>";
                    }
                  }
                }
                ?>
              </div>
            </td>
          </tr>
        </table>
      <?php } ?>
    </div>
  </div>
</div>
<?php ob_start(); ?>
<script>
  // $(document).ready(function(){

  $("input.preco-mask").mask("#.##0,00", {
    reverse: true
  });

  /**
   * Gerar nova instancia do Dialog Modal Jquery
   */
  JanelaModal.dialog({
    title: "Pesquisar clientes",
    width: 800,
    height: 600,
    autoOpen: false
  });

  /**
   * Modal para pesquisar clientes para unico envio de cupom
   */
  $("#pesquisar-clientes").on("click", function() {
    $.ajax({
      url: window.location.href,
      data: {
        acao: "PesquisarCliente"
      },
      beforeSend: function() {
        JanelaModal.dialog("open").html([
          $("<h3/>", {
            class: "text-center",
            html: "Carregando lista de clientes..."
          })
        ]);
      },
      success: function(str) {
        console.log(str);
        var list = $("<div/>", {
          html: str
        });
        JanelaModal.dialog("open").html(list.find("#clientes").html());
      }
    });
  });
  /**
   * Modal para pesquisar clientes para unico envio de cupom
   */
  JanelaModal.on("submit", function(e) {
    e.preventDefault();
    var Vl = JanelaModal.find("input[name=cupom_pesquisa]").val();
    $.ajax({
      url: window.location.href,
      data: {
        acao: "PesquisarCliente",
        cupom_pesquisa: Vl
      },
      success: function(str) {
        var list = $("<div/>", {
          html: str
        });
        JanelaModal.dialog("open").html(list.find("#clientes").html());
      }
    });
  });

  /**
   * Gerar dados para o cliente que está sendo selecionado
   */
  JanelaModal.on("click", "a.cliente-selecionado", function(e) {
    e.preventDefault();
    $.ajax({
      url: this.href || e.target.href,
      success: function(str) {
        var list = $("<div/>", {
          html: str
        });
        $("#cliente-selecionado").html(list.find("#cliente-selecionado").html());
        $("input[name=cliente_id]").val(list.find("input[name=cliente_id]").val());
      },
      complete: function() {
        JanelaModal.dialog("close").html('');
      }
    })
  });


  /**
   * Define tipo de envios do cupons com algumas acoes
   */
  $("#cupons").on("change", "select[name=cupom_envios]", function(e) {
    var Vl = $(this).val();
    console.log(Vl);

    $("#cupons").find("#seleciona_cliente").fadeOut(0);
    $("#cupons").find("input[name=cliente_id]").prop("disabled", true);

    $("#cupons").find("#seleciona_ufs").fadeOut(10);
    $("#cupons").find("[name=cupom_uf]").prop("disabled", true);

    if (Vl === "unico") {
      $("#cupons").find("#seleciona_cliente").fadeIn(10);
      $("#cupons").find("input[name=cliente_id]").prop("disabled", false);
    }

    if (Vl === "uf") {
      $("#cupons").find("#seleciona_ufs").fadeIn(10);
      $("#cupons").find("[name=cupom_uf]").prop("disabled", false);
    }

  });

  /*
   * Criar validação do cupom para não haver erros
   */
  $("#cupom").validate({
    errorClass: "show text-danger",
    errorElement: "font",
    rules: {
      cupom_codigo: {
        required: true
      },
      cupom_value: {
        required: true
      },
      cupom_desconto: {
        required: true
      },
      cupom_envios: {
        required: true
      },
      cliente_id: {
        required: true
      },
    },
    messages: {
      cupom_codigo: {
        required: "Digite ou gere um código para o cupom de desconto!"
      },
      cupom_value: {
        required: "Qual o valor do desconto!"
      },
      cupom_desconto: {
        required: "Qual o tipo do desconto!"
      },
      cupom_envios: {
        required: "Qual o tipo do desconto!"
      },
      cliente_id: {
        required: "Clique em pesquisar para selecionar um cliente!"
      },

    },
    invalidHandler: function(event, validator) {
      console.log(event);
      console.log(validator);
      if (validator.numberOfInvalids()) {
        // JanelaModal.dialog("open").html("");
      }
      // else {
      // $("div.cx-error,div.div-absoluta").hide();
      // }
    },
  });

  /**
   * Gerar uma hash para o cupom
   */
  Password = function() {
    var pass = "";
    var chars = 15; //Número de caracteres da senha
    generate = function(chars) {
      for (var i = 0; i < chars; i++)
        pass += this.getRandomChar();

      //document.getElementById("senha").innerHTML( pass );
      $("input[name=cupom_codigo]").val(pass);
    };
    this.getRandomChar = function() {
      /*
       * matriz contendo em cada linha indices (inicial e final) da tabela ASCII para retornar alguns caracteres.
       * [48, 57] = numeros;
       * [64, 90] = "@" mais letras maiusculas;
       * [97, 122] = letras minusculas;
       */
      var ascii = [
        [48, 57],
        [64, 90],
        [97, 122]
      ];
      var i = Math.floor(Math.random() * ascii.length);
      return String.fromCharCode(Math.floor(Math.random() * (ascii[i][1] - ascii[i][0])) + ascii[i][0]);
    };
    generate(chars);
  }
  // });
</script>
<?php
$SCRIPT['script_manual'] .= ob_get_clean();


include '../rodape.php';
