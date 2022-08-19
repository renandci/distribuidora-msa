<?php
include 'topo.php';
$data_action = http_build_query($GET);
/**
 * Cadastra
 */
if (isset($GET['acao']) && $GET['acao'] === 'cadastrar') {
  DadosFrete::action_cadastrar_editar($POST, 'cadastrar', 'nome_frete');
  header("Location: /adm/frete.php?codigo_id={$GET['codigo_id']}&produto_id={$GET['produto_id']}&id_frete={$GET['id_frete']}");
  return;
}

/**
 * Editar
 */
if (isset($GET['acao']) && $GET['acao'] === 'editar') {
  DadosFrete::action_cadastrar_editar($POST, 'alterar', 'nome_frete');
  header("Location: /adm/frete.php?codigo_id={$GET['codigo_id']}&produto_id={$GET['produto_id']}&id_frete={$GET['id_frete']}");
  return;
}

/**
 * Excluir
 */
if (isset($GET['acao']) && $GET['acao'] === 'excluir') {
  echo 'asdfsd';
  DadosFrete::action_cadastrar_editar(['DadosFrete' => [$GET['id'] => ['excluir' => 1]]], 'excluir', 'nome_frete');
  header("Location: /adm/frete.php?codigo_id={$GET['codigo_id']}&produto_id={$GET['produto_id']}&id_frete={$GET['id_frete']}");
  return;
}

/**
 * Remover em massa
 */
if (count($POST['DadosFrete']) > 0) {
  DadosFrete::action_cadastrar_editar($POST, 'excluir', 'nome_frete');
  header("Location: /adm/frete.php?codigo_id={$GET['codigo_id']}&produto_id={$GET['produto_id']}&id_frete={$GET['id_frete']}");
  return;
}

$TOTAL_CADASTROS_ATIVOS = DadosFrete::find_num_rows('select id from dados_frete where excluir = 0 and loja_id=?', [$CONFIG['loja_id']]);
$TOTAL_CADASTROS_DESATIVOS = DadosFrete::find_num_rows('select id from dados_frete where excluir = 1 and loja_id=?', [$CONFIG['loja_id']]);

$GET_STATUS = isset($POST['status']) && $POST['status'] != '' ? $POST['status'] : (isset($GET['status']) && $GET['status'] != '' ? $GET['status'] : '');
$GET_PESQUISAR = isset($GET['pesquisar']) && $GET['pesquisar'] != '' ? $GET['pesquisar'] : (isset($POST['pesquisar']) && $POST['pesquisar'] != '' ? $POST['pesquisar'] : '');
?>
<div class="panel panel-default">
    <div class="panel-heading panel-store text-uppercase ocultar">DADOS DE FRETE</div>
    <div id="div-edicao" class="panel-body">
        <style>
        body {
            background-color: #f1f1f1
        }

        .ocultos {
            display: none;
        }
        </style>
        <table width="100%" border="0" cellpadding="8" cellspacing="0">
            <tbody>
                <tr class="ocultar">
                    <td colspan="7">
                        <form action="/adm/frete.php?<?php echo $data_action ?>" method="post" class="formulario-frete">
                            <div class="clearfix mb15" style="line-height: 17px;">
                                <span class="cor-001">Total de <span
                                        class="ft18px"><?php echo $TOTAL_CADASTROS_ATIVOS ?></span> fretes
                                    cadastrados</span>
                            </div>
                            <input name="pesquisar" type="text" class="w50" />
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-search"></i>
                            </button>
                            <button class="btn btn-primary" type="button" onclick="$('.ocultar').slideToggle(0);"
                                <?php echo _P('frete', $_SESSION['admin']['id_usuario'], 'incluir|alterar') ?>>cadastrar</button>
                            <button class="btn btn-danger" type="button" data-action="btn-excluir-varios"
                                data-href="/adm/frete.php?<?php echo $data_action ?>"
                                <?php echo _P('descricao', $_SESSION['admin']['id_usuario'], 'excluir') ?>>
                                excluir seleção
                            </button>
                        </form>
                    </td>
                </tr>

                <tr id="formulario" class="ocultos ocultar">
                    <td colspan="7">
                        <form class="formulario-frete container-fluid"
                            action="/adm/frete.php?<?php echo $data_action ?>" method="post">
                            <div class="row mb15">
                                <div class="pull-left w100 mb15">
                                    <p>Descricão:</p>
                                    <input type="text" value="" name="DadosFrete[0][nome_frete]" class="w100" />
                                </div>
                                <div class="pull-left w30">
                                    <p>Altura: <br /><small>(cm) - Mínimo 2 e Máximo 105</small></p>
                                    <input type="text" value="" name="DadosFrete[0][altura]" class="w95" />
                                </div>
                                <div class="pull-left w30">
                                    <p>Largura: <br /><small>(cm) - Mínimo 11 e Máximo 105</small></p>
                                    <input type="text" value="" name="DadosFrete[0][largura]" class="w95" />
                                </div>
                                <div class="pull-left w30">
                                    <p>Comprimento: <br /><small>(cm) - Mínimo 16 e Máximo 105</small></p>
                                    <input type="text" value="" name="DadosFrete[0][comprimento]" class="w95" />
                                </div>
                                <div class="pull-left w30">
                                    <p>Peso: <small>(kg)</small></p>
                                    <input type="text" value="" name="DadosFrete[0][peso]" class="peso w95" />
                                </div>
                                <div class="pull-left w100 mt15">
                                    <small>A soma resultante do comprimento + largura + altura não deve superar 200
                                        cm.</small><br />
                                    <small>A soma resultante do comprimento + o dobro do diâmetro não pode ser menor que
                                        28 cm.</small><br />
                                    <small>* Medidas mínimas e máximas para cálculo dos correios.</small>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-cadastros-frete"
                                <?php echo _P('frete', $_SESSION['admin']['id_usuario'], 'incluir') ?>>salvar</button>
                            <button type="button" class="btn btn-danger"
                                onclick="$('.ocultar').slideToggle(0);">cancelar</button>
                        </form>
                    </td>
                </tr>

                <tr class="plano-fundo-adm-003 ocultar">
                    <td bgcolor="#ffffff" align="center" nowrap="nowrap" width="1%">
                        <input type="checkbox" data-action="selecionados-exclusao-all" class="selecionados-exclusao-all"
                            id="label" value="" />
                        <label for="label" class="input-checkbox"></label>
                    </td>
                    <td>Descricão</td>
                    <td align='center'>A.</td>
                    <td align='center'>L.</td>
                    <td align='center'>C.</td>
                    <td align='center'>Kg.</td>
                    <td align='center'>Ações</td>
                </tr>

                <?php
        $i = 0;

        $maximo = 25;

        $pag = isset($GET['pag']) && $GET['pag'] != '' ? $GET['pag'] : 1;

        $inicio = (($pag * $maximo) - $maximo);

        $conditions = array();

        $conditions['conditions'] = sprintf('excluir = 0 and loja_id=%u', $CONFIG['loja_id']);

        $conditions['conditions'] .= isset($GET_PESQUISAR) && $GET_PESQUISAR != ''
          ? queryInjection(' and nome_frete like "%%%s%%" ', $GET_PESQUISAR)  : '';

        // $conditions['conditions'] .= isset( $GET['codigo_id'] ) && $GET['codigo_id'] > 0
        // ? queryInjection(' AND id NOT IN('
        // . 'SELECT produtos.id_frete '
        // . 'FROM produtos '
        // . 'WHERE produtos.codigo_id = %u AND produtos.excluir = 0) ', $GET['codigo_id']) : '';

        // $conditions['conditions'] .= isset( $GET['id_frete'] ) && $GET['codigo_id'] > 0 ? queryInjection(' AND id != %u', $GET['id_frete']) : '';

        $total = ceil(DadosFrete::count($conditions) / $maximo);

        $conditions['order'] = 'nome_frete asc';

        $conditions['limit'] = $maximo;

        $conditions['offset'] = ($maximo * ($pag - 1));

        $result = DadosFrete::all($conditions);

        foreach ($result as $rs) {
          $rs = $rs->to_array(); ?>
                <tr class="lista-zebrada in-hover formulario<?php echo $rs['id']; ?> ocultar"
                    <?php echo ($i % 2) ? 'style="background-color:#f3f3f3"' : '' ?>>
                    <td nowrap="nowrap" width="1%">
                        <input type="checkbox" name="DadosFrete[<?php echo $rs['id']; ?>][excluir]"
                            id="label<?php echo $rs['id'] ?>" value="1" data-action="selecionados-exclusao" />
                        <label for="label<?php echo $rs['id'] ?>" class="input-checkbox"></label>
                    </td>
                    <td>
                        <?php echo $rs['nome_frete'] ?>
                        <?php echo !empty($GET['id_frete']) && $GET['id_frete'] == $rs['id'] ? '<span class="pull-right btn btn-info btn-xs ft10px">adicionado</span>' : null ?>
                    </td>
                    <td align="center" nowrap="nowrap" width="1%"><?php echo $rs['altura']; ?></td>
                    <td align="center" nowrap="nowrap" width="1%"><?php echo $rs['largura']; ?></td>
                    <td align="center" nowrap="nowrap" width="1%"><?php echo $rs['comprimento']; ?></td>
                    <td align="center" nowrap="nowrap" width="1%"><?php echo number_format($rs['peso'], 3, '.', ''); ?>
                    </td>
                    <td align="center" nowrap="nowrap" width="1%">
                        <a href="/adm/produtos/produtos-cadastrar.php?codigo_id=<?php echo $GET['codigo_id'] ?>&frete_id=<?php echo $rs['id'] ?>&produto_id=<?php echo $GET["produto_id"] ?>&acao=adicionar-frete&id_frete=<?php echo $GET['id_frete'] ?>"
                            class="btn btn-primary btn-sm btn-add-frete<?php echo $GET['codigo_id'] == '' ? ' hidden' : '' ?>"
                            <?php echo _P('produtos-cadastrar', $_SESSION['admin']['id_usuario'], 'incluir') ?>>
                            adicionar frete
                        </a>

                        <a href='javascript: void(0);' class="btn btn-warning btn-sm"
                            onclick="$('.formulario<?php echo $rs['id'] ?>').slideToggle(0);"
                            <?php echo _P('frete', $_SESSION['admin']['id_usuario'], 'alterar') ?>>editar</a>

                        <a href='/adm/frete.php?id=<?php echo $rs['id'] ?>&codigo_id=<?php echo $GET['codigo_id'] ?>&produto_id=<?php echo $GET["produto_id"] ?>&acao=excluir&id_frete=<?php echo $GET['id_frete'] ?>'
                            class='btn btn-danger btn-sm btn-excluir-modal'
                            <?php echo _P('frete', $_SESSION['admin']['id_usuario'], 'excluir') ?>>excluir</a>
                    </td>
                </tr>
                <tr class="formulario<?php echo $rs['id']; ?> ocultos lista-zebrada"
                    id='formulario<?php echo $rs['id']; ?>'>
                    <td colspan="7">
                        <form class="formulario-frete container-fluid"
                            action="/adm/frete.php?codigo_id=<?php echo $GET['codigo_id'] ?>&produto_id=<?php echo $GET["produto_id"] ?>&acao=editar&id_frete=<?php echo $GET['id_frete'] ?>"
                            method="post">
                            <div class="row mb15 mt15 fieldset">
                                <div class="pull-left w100 mb15">
                                    <p>Descricão:</p>
                                    <input type="text" value="<?php echo $rs['nome_frete']; ?>"
                                        name="DadosFrete[<?php echo $rs['id']; ?>][nome_frete]" class="w100" />
                                </div>
                                <div class="pull-left w30">
                                    <p>Altura: <br /><small>(cm) - Mínimo 2 e Máximo 105</small></p>
                                    <input type="text" value="<?php echo $rs['altura']; ?>"
                                        name="DadosFrete[<?php echo $rs['id']; ?>][altura]" class="w95" />
                                </div>
                                <div class="pull-left w30">
                                    <p>Largura: <br /><small>(cm) - Mínimo 11 e Máximo 105</small></p>
                                    <input type="text" value="<?php echo $rs['largura']; ?>"
                                        name="DadosFrete[<?php echo $rs['id']; ?>][largura]" class="w95" />
                                </div>
                                <div class="pull-left w30">
                                    <p>Comprimento: <br /><small>(cm) - Mínimo 16 e Máximo 105</small></p>
                                    <input type="text" value="<?php echo $rs['comprimento']; ?>"
                                        name="DadosFrete[<?php echo $rs['id']; ?>][comprimento]" class="w95" />
                                </div>
                                <div class="pull-left w30">
                                    <p>Peso: <small>(kg)</small></p>
                                    <input type="text" value="<?php echo $rs['peso']; ?>"
                                        name="DadosFrete[<?php echo $rs['id']; ?>][peso]" class="peso w95" />
                                </div>
                                <div class="pull-left w100 mt15">
                                    <small>A soma resultante do comprimento + largura + altura não deve superar 200
                                        cm.</small><br />
                                    <small>A soma resultante do comprimento + o dobro do diâmetro não pode ser menor que
                                        28 cm.</small><br />
                                    <small>* Medidas mínimas e máximas para cálculo dos correios.</small>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-cadastros-frete"
                                <?php echo _P('frete', $_SESSION['admin']['id_usuario'], 'alterar') ?>>salvar</button>
                            <button type="button" class="btn btn-danger"
                                onclick="$('.formulario<?php echo $rs['id']; ?>').slideToggle(0);">cancelar</button>
                        </form>
                    </td>
                </tr>
                <?php
          ++$i;
        }
        ?>
                <tr class="ocultar">
                    <td colspan="7">
                        <div class="paginacao paginacao-add">
                            <?php
              if ($total > 0) {
                for ($i = $pag - 5, $limiteDeLinks = $i + 10; $i <= $limiteDeLinks; ++$i) {
                  if ($i < 1) {
                    $i = 1;
                    $limiteDeLinks = 9;
                  }

                  if ($limiteDeLinks > $total) {
                    $limiteDeLinks = $total;
                    $i = $limiteDeLinks - 10;
                  }

                  if ($i < 1) {
                    $i = 1;
                    $limiteDeLinks = $total;
                  }

                  if ($i == $pag) {
                    echo "<span class=\"at plano-fundo-adm-001\">{$i}</span>";
                  } else {
                    $data = http_build_query(array_replace($GET, ['pag' => $i]));
                    echo sprintf('<a href="/adm/frete.php?%s" class="btn-paginacao">%s</a>', $data, $i);
                  }
                }
              }
              ?>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<script>
$(document).on("click", "a", function() {
    var href = this.href || e.target.href;
    if (href.search('excluir') > '0')
        if (!confirm("Deseja realmente excluir!")) return false;

});
</script>
<?php
include 'rodape.php';