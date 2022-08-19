<?php

// AULA 160 15 MIN

include '../topo.php';

if (!empty($GET['codigo_id']) && (int)$GET['codigo_id'] == 0) {
  header('Location: /adm/sair.php');
  return;
}

switch ($POST['acao']) {
  case 'ProdutosEditar':

    // echo '<div id="grid_variacao">';
    // $CountIds = (int)count($POST['id']);
    // for ($i = 0; $i < $CountIds; $i++)
    //   printf(
    //     "<pre>%s %s %s %s</pre>",
    //     print_r($POST['codigo_produto'][$i], 1),
    //     print_r((int)$POST['id'][$i], 1),
    //     print_r($POST['id_tamanho'][$i], 1),
    //     implode(',', $POST['placastatus'])
    //   );
    // return;

    $CountIds = (int)count($POST['id']);
    for ($i = 0; $i < $CountIds; $i++) {
      $Produtos = Produtos::find((int)$POST['id'][$i]);
      $Produtos->id = (int)$POST['id'][$i];

      $Produtos->id_cor = $POST['id_cor'][$i];
      $Produtos->id_tamanho = $POST['id_tamanho'][$i];
      $Produtos->id_frete = $POST['id_frete'][$i];

      $Produtos->id_marca = $POST['id_marca'];
      $Produtos->id_descricao = $POST['id_descricao'];

      $Produtos->codigo_id = $POST['codigo_id'];
      // $Produtos->codigo_referencia = $POST['codigo_referencia'];
      $Produtos->codigo_produto = $POST['codigo_produto'][$i];

      $Produtos->nome_produto = $POST['nome_produto'];
      $Produtos->subnome_produto = $POST['subnome_produto'];
      $Produtos->descricao_produto = $POST['descricao_produto'];
      $Produtos->descricao_produto2 = $POST['descricao_produto2'];
      $Produtos->postagem = $POST['postagem'];

      $Produtos->estoque = (int)$POST['estoque'][$i];
      $Produtos->estoque_min = (int)$POST['estoque_min'][$i];
      $Produtos->preco_custo = dinheiro($POST['preco_custo'][$i]);
      $Produtos->preco_venda = dinheiro($POST['preco_venda'][$i]);
      $Produtos->preco_promo = dinheiro($POST['preco_promo'][$i]);
      $Produtos->placastatus = !empty($POST['placastatus']) ? implode(',', $POST['placastatus']) : null;

      $Produtos->utilidades = $POST['utilidades'];
      $Produtos->categoria = $POST['categoria'][$i];
      $Produtos->unid = $POST['unid'];
      $Produtos->csosn = $POST['csosn'];
      $Produtos->cfop = $POST['cfop'];
      $Produtos->cst = $POST['cst'];
      $Produtos->ncm = $POST['ncm'];
      $Produtos->cest = $POST['cest'];
      $Produtos->orcamento = $POST['orcamento'];

      // $Produtos->save();
      $Produtos->save_log();
    }

    // Dados para os menus
    $Menus = [];
    $MenusNews = [];

    $not_id = [];

    $grupos = $POST['id_grupo'];
    $subgrupos = !empty($POST['id_subgrupo']) ? $POST['id_subgrupo'] : [];
    $codigo_id = $POST['codigo_id'];

    $CountGrupos = (int)count($grupos);

    // $ProdutosMenus = ProdutosMenus::all(['conditions' => ['codigo_id=?', $codigo_id]]);
    // $Menus = (array_map(function($rs){ return ['id' => $rs->id, 'codigo_id' => $rs->codigo_id, 'id_grupo' => $rs->id_grupo, 'id_subgrupo' => $rs->id_subgrupo]; }, $ProdutosMenus));

    if ($CountGrupos > 0) {
      foreach ($grupos as $g => $grupo) {
        if (!empty($grupo) && !empty($subgrupos[$g])) {
          foreach ($subgrupos[$g] as $sg => $subgrupo) {
            $MenusNews[] = ['codigo_id' => $codigo_id, 'id_grupo' => $grupo, 'id_subgrupo' => $subgrupo];
          }
        } else {
          $MenusNews[] = ['codigo_id' => $codigo_id, 'id_grupo' => $grupo, 'id_subgrupo' => 0];
        }
      }

      foreach ($MenusNews as $k => $rws) {
        $ProdutosMenusFind = ProdutosMenus::find([
          'conditions' => ['codigo_id=? and id_grupo=? and id_subgrupo=?', $codigo_id, $rws['id_grupo'], $rws['id_subgrupo']]
        ]);
        $ProdutosMenus = new ProdutosMenus();
        $ProdutosMenus->id = (int)$ProdutosMenusFind->id;
        $ProdutosMenus->codigo_id = $codigo_id;
        $ProdutosMenus->id_grupo = $rws['id_grupo'];
        $ProdutosMenus->id_subgrupo = $rws['id_subgrupo'];

        $ProdutosMenusSave = $ProdutosMenus->save_log();

        $not_id[] = (int)$ProdutosMenusSave['id'];
      }

      ProdutosMenus::delete_all(array('conditions' => array('codigo_id=? and id NOT IN(?)', $codigo_id, $not_id)));

      // printf("<pre>%s</pre>", print_r($Menus, 1));
      printf("<pre>%s</pre>", print_r($MenusNews, 1));
    } else {
      ProdutosMenus::delete_all(array('conditions' => array('codigo_id=?', $codigo_id)));
    }

    header(sprintf('location: /adm/produtos/produtos-cadastrar.php?acao=%s&codigo_id=%u', $POST['acao'], $POST['codigo_id']));
    return;
    break;

    // Copia e gera uma nova vairação
  case 'CopyProduto':

    $ProdutosCopy = Produtos::first(['conditions' => ['codigo_id=? and excluir=0 and status=0', (int)$POST['codigo_id']]]);

    $Produtos = new Produtos();
    $Produtos->codigo_id = $ProdutosCopy->codigo_id;
    $Produtos->id_marca = $ProdutosCopy->id_marca;
    $Produtos->id_descricao = $ProdutosCopy->id_descricao;
    $Produtos->id_frete = $ProdutosCopy->id_frete;
    $Produtos->codigo_id = $ProdutosCopy->codigo_id;
    $Produtos->codigo_referencia = $ProdutosCopy->codigo_referencia;
    $Produtos->codigo_produto = $ProdutosCopy->codigo_produto;
    $Produtos->nome_produto = $ProdutosCopy->nome_produto;
    $Produtos->subnome_produto = $ProdutosCopy->subnome_produto;
    $Produtos->descricao_produto = $ProdutosCopy->descricao_produto;
    $Produtos->descricao_produto2 = $ProdutosCopy->descricao_produto2;
    $Produtos->postagem = $ProdutosCopy->postagem;
    $Produtos->preco_custo = $ProdutosCopy->preco_custo;
    $Produtos->preco_venda = $ProdutosCopy->preco_venda;
    $Produtos->preco_promo = $ProdutosCopy->preco_promo;
    $Produtos->placastatus = $ProdutosCopy->placastatus;
    $Produtos->utilidades = $ProdutosCopy->utilidades;
    $Produtos->categoria = $ProdutosCopy->categoria;
    $Produtos->unid = $ProdutosCopy->unid;
    $Produtos->csosn = $ProdutosCopy->csosn;
    $Produtos->cfop = $ProdutosCopy->cfop;
    $Produtos->cst = $ProdutosCopy->cst;
    $Produtos->ncm = $ProdutosCopy->ncm;
    $Produtos->cest = $ProdutosCopy->cest;
    $Produtos->orcamento = $ProdutosCopy->orcamento;
    $Produtos->save_log();
    // $Produtos->save();

    header('location: /adm/produtos/produtos-cadastrar.php?acao=ProdutosEditar&codigo_id=' . $ProdutosCopy->codigo_id);
    return;
    break;

  case 'AddFrete':

    $Produtos = Produtos::find((int)$GET['produto_id']);
    $Produtos->id = (int)$GET['produto_id'];
    $Produtos->id_frete = (int)$GET['frete_id'];
    $Produtos->save_log();

    header('location: /adm/produtos/produtos-cadastrar.php?acao=ProdutosEditar&codigo_id=' . $ProdutosCopy->codigo_id);
    return;
    break;
}

switch ($GET['acao']) {

  case 'Excluir':
    if (isset($GET['codigo_id'], $GET['id'])) {
      Produtos::action_cadastrar_editar(['Produtos' => [(int)$GET['id'] => ['excluir' => 1]]], 'alterar', 'nome_produto');
    }
    header('location: /adm/produtos/produtos-cadastrar.php?acao=ProdutosEditar&codigo_id=' . $GET['codigo_id']);
    return;
    break;

  case 'CriarNovoProduto':
    $CODIGO_ID = time();

    Produtos::action_cadastrar_editar(['Produtos' => [0 => ['codigo_id' => $CODIGO_ID]]], 'cadastrar', 'nome_produto');

    header('Location: /adm/produtos/produtos-cadastrar.php?acao=ProdutosEditar&codigo_id=' . $CODIGO_ID);
    return;
    break;

  case 'CancelarNovoProduto':
    $Produtos = Produtos::all(['conditions' => ['codigo_id=?', $GET['codigo_id']]]);
    foreach ($Produtos as $rws) {
      Produtos::action_cadastrar_editar(['Produtos' => [$rws->id => ['excluir' => 1]]], 'cadastrar', 'nome_produto');
    }

    header('Location: /adm/produtos/produtos.php');
    return;
    break;
}


$conditions_all['conditions'] = sprintf('loja_id=%u and excluir=0', $CONFIG['loja_id']);

$Produtos = new stdClass();
$Marcas = Marcas::all(['conditions' => ['excluir=? and loja_id=?', 0, $CONFIG['loja_id']], 'order' => 'marcas asc']);
$DadosFrete = DadosFrete::all($conditions_all);
$PlaquinhaStatus = PlaquinhaStatus::all(['conditions' => ['excluir=? and ativo = 1 and loja_id=?', 0, $CONFIG['loja_id']], 'order' => 'placa_text asc']);
$ProdutosDescricoes = ProdutosDescricoes::all($conditions_all);
$OpcoesTipo = OpcoesTipo::all($conditions_all);

$Produtos = Produtos::first([
  'order' => 'id DESC',
  'conditions' => [
    'codigo_id=? and excluir=0 and status=0', (int)$GET['codigo_id']
  ],
  'select' => ''
    . 'produtos.*, '
    . '(SELECT COUNT(A.id) FROM produtos_imagens A WHERE A.codigo_id = produtos.codigo_id ) as total_imagens, '
    . '(SELECT COUNT(A.id) FROM produtos A WHERE A.codigo_id = produtos.codigo_id AND produtos.codigo_produto != A.codigo_produto AND excluir = 0) AS if_codigo_produto, '
    . '(SELECT COUNT(A.id) FROM produtos A WHERE A.codigo_id = produtos.codigo_id AND produtos.id_frete != A.id_frete AND excluir = 0) AS if_frete, '
    . '(SELECT COUNT(A.id) FROM produtos A WHERE A.codigo_id = produtos.codigo_id AND produtos.estoque != A.estoque AND excluir = 0 ) AS if_estoque, '
    . '(SELECT COUNT(A.id) FROM produtos A WHERE A.codigo_id = produtos.codigo_id AND produtos.categoria != A.categoria AND excluir = 0 ) AS if_categoria, '
    . '(SELECT COUNT(A.id) FROM produtos A WHERE A.codigo_id = produtos.codigo_id AND produtos.preco_custo != A.preco_custo AND excluir = 0) AS if_preco_custo, '
    . '(SELECT COUNT(A.id) FROM produtos A WHERE A.codigo_id = produtos.codigo_id AND produtos.preco_venda != A.preco_venda AND excluir = 0) AS if_preco_venda, '
    . '(SELECT COUNT(A.id) FROM produtos A WHERE A.codigo_id = produtos.codigo_id AND produtos.preco_promo != A.preco_promo AND excluir = 0) AS if_preco_promo, '
    . '(SELECT COUNT(A.id) FROM produtos A WHERE A.codigo_id = produtos.codigo_id AND produtos.id_cor != A.id_cor AND produtos.id_tamanho = 0) AS if_cores, '
    . '(SELECT COUNT(A.id) FROM produtos A WHERE A.codigo_id = produtos.codigo_id AND produtos.id_tamanho != A.id_tamanho AND produtos.id_cor = 0) AS if_tamanhos, '
    . '(SELECT COUNT(A.id) FROM produtos A WHERE A.codigo_id = produtos.codigo_id AND produtos.id_cor > 0 AND produtos.id_tamanho > 0) AS if_cores_tamanhos, '
    . '(SELECT SUM(pedidos_vendas.quantidade) as total FROM pedidos_vendas INNER JOIN pedidos ON pedidos.id = pedidos_vendas.id_pedido WHERE pedidos.status in(1,2,3,6,7,11) AND pedidos_vendas.id_produto = produtos.id) AS pendentes '
]);

$ProdutosOrdensAZ = $Produtos->produtos_all ?? [];

$ProdutosOrdensAZCount = (int)count($ProdutosOrdensAZ);
if ($ProdutosOrdensAZCount === 0) {
?>
  <div class="text-center">
    <h2>Esse produto encontra-se desativado.</h2>
    <a href="/adm/produtos/produtos.php" class="btn btn-warning">voltar</a>
  </div>
<?php
  include '../rodape.php';
  die;
}

// Tenta organizar a matriz do produto pelo nome
array_multisort(array_map(function ($obj) {
  return $obj->cor->nomecor . $obj->tamanho->ordem . $obj->id;
}, $ProdutosOrdensAZ), SORT_ASC, $ProdutosOrdensAZ);


$ProdutosMenus = $Produtos->produtos_menus;
// // Tenta organizar a matriz do produto pelo nome
// array_multisort(array_map(function($obj) {
// 	return $obj->grupo->ordem . $obj->subgrupo->ordem;
// }, $ProdutosMenusOrdensAz), SORT_ASC, $ProdutosMenusOrdensAz);
// // printf('<pre>%s</pre>', print_r($ProdutosMenusOrdensAz, 1));

?>
<style>
  input[name='estoque[]'],
  input[name='preco_custo[]'],
  input[name='preco_venda[]'],
  input[name='preco_promo[]'],
  input[name='preco_boleto[]'] {
    text-align: center;
  }

  div.affix-bottom {
    position: fixed;
    bottom: 0;
    right: 0;
    background-color: #dedede;
    width: 100%;
    text-align: center;
    padding: 5px 0;
  }

  h2.affix-top {
    position: fixed;
    top: 0;
    right: 0;
    margin-top: 55px;
    background-color: #dedede;
    width: 100%;
    text-align: center;
    padding: 5px 0;
    display: none;
    z-index: 98;
  }

  body {
    background-color: #f1f1f1
  }
</style>


<form action="/adm/produtos/produtos-cadastrar.php" class="row mt35" id="form-produtos" method="post" enctype="multipart/form-data">
  <h2 id="produto-nome" class="affix-top"><?php echo $Produtos->nome_produto ?></h2>
  <div class="col-lg-10 col-lg-offset-1">
    <div class="panel panel-default">
      <div class="panel-heading panel-store">
        <i class="fa fa-minus" onclick="my_toggle(this)" style="cursor: pointer;"></i> Dados Cadastrais
      </div>
      <div class="panel-body">
        <div class="form-group">
          <div class="row">
            <div class="col-md-10 col-sm-6 col-xs-12 mb15">
              <label>Placas de Decorativas:</label>
              <div class="row" id="placas_status_fretes">
                <?php
                $placas = explode(',', $Produtos->placastatus) ?? [];
                foreach ($placas as $x => $string) {
                  $placastatus[] = soNumero(substr($string, 1, strpos($string, ' style')));
                }
                foreach ($PlaquinhaStatus as $i => $pls) { ?>
                  <div class="col-md-3 col-sm-3 col-xs-6">
                    <input type="checkbox" id="p<?php echo $i ?>" name="placastatus[]" value="<?php echo $pls->id; ?>" <?php echo in_array($pls->id, $placastatus) ? ' checked' : '' ?> />
                    <label class="input-checkbox" for="p<?php echo $i ?>"></label>
                    <span class="ft12px">
                      <?php echo $pls->placa_text ?>
                    </span>
                  </div>
                <?php } ?>
              </div>
            </div>
            <div class="col-lg-3">
              <label for="codigo_produto">
                Referência/Código:
                <span class="info-title" data-toggle="tooltip" title="Campo opcional. Caso deixado em branco, será gerado um código automático e individual.">?</span>
              </label>
              <input type="text" class="form-control" id="codigo_produto" name="codigo_produto" value="<?php echo $Produtos->if_codigo_produto == 0 ? $Produtos->codigo_produto : ''; ?>" <?php echo $Produtos->if_codigo_produto > 0 ? ' disabled' : '' ?> placeholder="<?php echo $Produtos->if_codigo_produto > 0 ? 'Consulte o código na guia opções!' : ($Produtos->codigo_produto == '' ? CodProduto($Produtos->nome_produto, $Produtos->id) : $Produtos->codigo_produto) ?>" />
            </div>
            <div class="col-sm-3 col-xs-12">
              <label for="postagem">
                Disponibilidade para postagem:
                <span class="info-title" data-toggle="tooltip" title="Somente utilizar este campo caso o tempo de postagem deste produto sejá diferente do prazo cadastrado na marca.">?</span>
              </label>
              <input type="text" name="postagem" class="form-control" id="postagem" value="<?php echo $Produtos->postagem; ?>" />
            </div>
            <div class="form-group col-sm-3 col-xs-12">
              <label for="orcamento">Orçamento: <span class="info-title" data-toggle="tooltip" title="Ative o orçamento somente se o produto for para fins de informativo.">?</span></label>
              <select name="orcamento" id="orcamento" class="form-control" style="width: 100%;">
                <option value="0" <?php echo $Produtos->orcamento ? ' selected' : '' ?>>Não</option>
                <option value="1" <?php echo $Produtos->orcamento ? ' selected' : '' ?>>Sim</option>
              </select>
            </div>

          </div>
        </div>

        <div class="form-group">
          <label for="nome_produto">Nome produto: <span class="info-title" data-toggle="tooltip" title="Nome que ficará visível para clientes no site.">?</span></label>
          <input type="text" name="nome_produto" value="<?php echo $Produtos->nome_produto ?>" class="form-control count-input" id="nome_produto" maxlength="100" />
        </div>

        <div class="form-group">
          <label for="id_marca">Marcas do produto: <small style="font-size:11px">(opcional)</small></label>
          <div class="input-group">
            <select name="id_marca" id="id_marca" class="form-control" style="width: 99%">
              <option value="0">Selecione uma marca</option>
              <?php foreach ($Marcas as $rsMarcas) { ?>
                <option value="<?php echo $rsMarcas->id ?>" <?php echo $Produtos->id_marca == $rsMarcas->id ? 'selected' : ''; ?>><?php echo $rsMarcas->marcas ?>
                </option>
              <?php } ?>
            </select>
            <a href="/adm/marcas.php?id_marca=<?php echo $Produtos->id_marca ?>" class="btn-open input-group-addon btn fa fa-folder-open" <?php echo _P('marcas', $_SESSION['admin']['id_usuario'], 'acessar') ?> data-tile="Cadastrar/Editar - Marcas" style="font-size: 25px;"></a>
          </div>
        </div>
        <div class="form-group">
          <label for="id_descricao">Descrição produto:</label>
          <div class="input-group">
            <select name="id_descricao" id="id_descricao" class="form-control" style="width: 99%">
              <option value="0">Selecione uma descrição</option>
              <?php foreach ($ProdutosDescricoes as $rsDescricao) { ?>
                <option value="<?php echo $rsDescricao->id ?>" <?php echo ($Produtos->id_descricao == $rsDescricao->id) ? 'selected' : '' ?>>
                  <?php echo $rsDescricao->nome ?>
                </option>
              <?php } ?>
            </select>
            <a href="/adm/descricao.php?id_descricao=<?php echo $Produtos->id_descricao ?>" class="btn-open input-group-addon btn fa fa-folder-open" <?php echo _P('descricao', $_SESSION['admin']['id_usuario'], 'acessar') ?> data-tile="Cadastrar/Editar - Descrições" style="font-size: 25px;"></a>
          </div>
        </div>
        <div class="form-group">
          <label for="subnome_produto">Descrição Resumida:</label>
          <textarea name="subnome_produto" id="subnome_produto" rows="3" class="form-control count-input" maxlength="505"><?php echo $Produtos->subnome_produto ?></textarea>
        </div>
        <div id="campos_ficais" class="form-group">
          <style>
            .campos-fiscais {
              background-color: #d8e5e8;
            }

            .campos-fiscais fieldset {
              border-color: #a2b5b9;
            }

            .campos-fiscais legend,
            .campos-fiscais fieldset>legend {
              color: #718c92;
            }
          </style>
          <fieldset class="campos-fiscais clearfix mb15">
            <legend class="bold text-uppercase">Campos fiscais</legend>
            <span class="col-sm-6 col-xs-12 mb15">
              <fieldset>
                <legend class="bold">CSOSN: <span class="info-title" data-toggle="tooltip" title="(Código de Situação da Operação no Simples Nacional)">?</span></legend>
                <select name="csosn" id="csosn" class="form-control" style="width: 100%">
                  <option value="">Selecione um CSOSN</option>
                  <?php foreach (([
                    ['id' => '101', 'csosn' => 'Tributada pelo Simples Nacional com permissão de crédito'],
                    ['id' => '102', 'csosn' => 'Tributada pelo Simples Nacional sem permissão de crédito'],
                    ['id' => '103', 'csosn' => 'Isenção do ICMS no Simples Nacional para faixa de receita bruta'],
                    ['id' => '201', 'csosn' => 'Tributada pelo Simples Nacional com permissão de crédito e com cobrança do ICMS por substituição tributária'],
                    ['id' => '202', 'csosn' => 'Tributada pelo Simples Nacional sem permissão de crédito e com cobrança do ICMS por substituição tributária'],
                    ['id' => '203', 'csosn' => 'Isenção do ICMS no Simples Nacional para faixa de receita bruta e com cobrança do ICMS por substituição tributária'],
                    ['id' => '300', 'csosn' => 'Imune'],
                    ['id' => '400', 'csosn' => 'Não tributada pelo Simples Nacional'],
                    ['id' => '500', 'csosn' => 'ICMS cobrado anteriormente por substituição tributária (substituído) ou por antecipação'],
                    ['id' => '900', 'csosn' => 'Outros'],
                  ]) as $csosn) { ?>
                    <option value="<?php echo $csosn['id'] ?>" <?php echo ($Produtos->csosn == $csosn['id'] ? ' selected' : null) ?>>
                      <?php echo $csosn['id'] ?> - <?php echo $csosn['csosn'] ?>
                    </option>
                  <?php } ?>
                </select>
              </fieldset>
            </span>
            <span class="col-sm-3 col-xs-12">
              <fieldset>
                <legend class="bold">Tipo Unidade: <span class="info-title" data-toggle="tooltip" title="(PÇ, PCT, KG etc...)">?</span></legend>
                <select name="unid" id="unid" class="form-control" style="width: 100%">
                  <option value="">Selecione </option>
                  <?php foreach (([
                    ['id' => 'UN', 'unid' => 'Unidade'],
                    ['id' => 'PÇ', 'unid' => 'Peça'],
                    ['id' => 'CX', 'unid' => 'Caixa'],
                    ['id' => 'KG', 'unid' => 'Kilo'],
                    ['id' => 'PCT', 'unid' => 'Pacote'],
                  ]) as $unid) { ?>
                    <option value="<?php echo $unid['id'] ?>" <?php echo ($Produtos->unid == $unid['id'] ? ' selected' : null) ?>>
                      <?php echo $unid['unid'] ?>
                    </option>
                  <?php } ?>
                </select>
              </fieldset>
            </span>
            <span class="col-sm-3 col-xs-12">
              <fieldset>
                <legend class="bold">CFOP: <span class="info-title" data-toggle="tooltip" title="(Nomenclatura Comum do MERCOSUL)">?</span></legend>
                <input type="text" name="cfop" value="<?php echo $Produtos->cfop ?>" class="form-control count-input" maxlength="8" />
              </fieldset>
            </span>
            <span class="col-sm-12 col-xs-12"></span>
            <span class="col-sm-3 col-xs-12 mb15">
              <fieldset>
                <legend class="bold">NCM: <span class="info-title" data-toggle="tooltip" title="(Nomenclatura Comum do MERCOSUL)">?</span></legend>
                <input type="text" name="ncm" value="<?php echo $Produtos->ncm ?>" class="form-control count-input" maxlength="8" />
              </fieldset>
            </span>
            <span class="col-sm-3 col-xs-12 mb15">
              <fieldset>
                <legend class="bold">CEST: <span class="info-title" data-toggle="tooltip" title="(Cód. Especificador/Substituição Tributária)">?</span></legend>
                <input type="text" name="cest" value="<?php echo $Produtos->cest ?>" class="form-control count-input" maxlength="20" />
              </fieldset>
            </span>
          </fieldset>
        </div>
      </div>
    </div>

    <div class="panel panel-default">
      <div class="panel-heading panel-store">
        <i class="fa fa-plus" onclick="my_toggle(this)" style="cursor: pointer;"></i> Grid de Variação
        <button type="button" class="btn-success btn btn-xs pull-right ml5 btn-add-grid">
          adicionar variação <i class="fa fa-plus"></i>
        </button>
        <a class="btn-open btn-success btn btn-xs pull-right ml5" href="/adm/tamanhos.php?codigo_id=<?php echo $GET['codigo_id'] ?>" data-title="Cadastrar/Editar de Tamanhos">
          Tamanhos <i class="fa fa-folder-open"></i>
        </a>
        <a class="btn-open btn-success btn btn-xs pull-right ml5" href="/adm/cores.php?codigo_id=<?php echo $GET['codigo_id'] ?>" data-title="Cadastrar/Editar de Cores">
          Cores <i class="fa fa-folder-open"></i>
        </a>
      </div>
      <div class="panel-body" id="grid_variacao" style="display: none;">
        <?php
        $countInc = 1;
        foreach ($ProdutosOrdensAZ as $loop) { ?>
          <div class="row" id="copy_<?php echo $loop->id ?>">
            <input name="id[]" type="hidden" value="<?php echo $loop->id ?>" />
            <div class="col-md-12 cols-xs-12 clearfix">
              <?php
              $id_cor = 0;
              foreach ($OpcoesTipo as $tipo) { ?>
                <span id="grid_<?php echo $tipo->id ?>_<?php echo $countInc ?>">
                  <?php if ($tipo->id == $loop->cor->opcoes_id) { ?>
                    <span class="badge">
                      <?php echo $loop->cor->nomecor ?>
                    </span>
                    <?php if (!empty($loop->cor->icon) || !empty($loop->cor->cor1)) { ?>
                      <span class="badge cx-cor-relativa <?php echo !empty($loop->cor->icon) ? 'is_icon' : null ?>" style="overflow: hidden; width: 20px; height: 20px;">
                        <span class="cor-style-1" style="background-color: <?php echo !empty($loop->cor->icon) ? sprintf('url(%s)', Imgs::src($loop->capa->imagem, 'xs')) : "#{$loop->cor->cor1}" ?>">
                          <span class="cor-style-2" style="border-bottom-color: #<?php echo $loop->cor->cor2 ?>"></span>
                        </span>
                      </span>
                    <?php } ?>
                  <?php } ?>

                  <?php if ($tipo->id == $loop->tamanho->opcoes_id) { ?>
                    <span class="badge">
                      <?php echo $loop->tamanho->nometamanho ?>
                    </span>
                  <?php } ?>
                </span>
              <?php } ?>

              <input type="hidden" name="id_cor[]" value="<?php echo $loop->id_cor ?>">
              <input type="hidden" name="id_tamanho[]" value="<?php echo $loop->id_tamanho ?>">

              <a href="/adm/produtos/produtos-cadastrar.php?acao=Excluir&codigo_id=<?php echo $loop->codigo_id ?>&id=<?php echo $loop->id ?>" class="btn-excluir-skus btn btn-xs btn btn-danger pull-right ml5" <?php echo _P('produtos-cores-tamanhos', $_SESSION['admin']['id_usuario'], 'excluir') ?>>
                <i class="fa fa-trash"></i>
                excluir
              </a>

              <a href="/adm/fotos.php?codigo_id=<?php echo $loop->codigo_id ?>&cor_id=<?php echo $loop->id_cor ?>" class="btn btn-xs btn-warning btn-fotos pull-right ml5" <?php echo _P('fotos', $_SESSION['admin']['id_usuario'], 'acessar') ?>>
                <i class="fa fa-camera"></i>
                <?php echo count($loop->fotos) ?> fotos
              </a>

              <?php
              $arrayopc = null;
              foreach ($OpcoesTipo as $tipo1) { ?>
                <?php
                foreach ($tipo1->cor_all as $tipo2)
                  $arrayopc[$tipo1->id][] = [
                    'id' => $tipo2->id,
                    'text' => $tipo2->nomecor,
                    'hex1' => $tipo2->cor1,
                    'hex2' => $tipo2->cor2,
                    'icon' => $tipo2->icon,
                    'grid' => "#grid_{$tipo1->id}_{$countInc}",
                    'checked' => ($tipo2->id == $loop->id_cor),
                    'id_elem' => 'id_cor'
                  ];

                foreach ($tipo1->tam_all as $tipo2)
                  $arrayopc[$tipo1->id][] = [
                    'id' => $tipo2->id,
                    'text' => $tipo2->nometamanho,
                    'grid' => "#grid_{$tipo1->id}_{$countInc}",
                    'checked' => ($tipo2->id == $loop->id_tamanho),
                    'id_elem' => 'id_tamanho'
                  ];
                ?>

                <button type="button" class="btn btn-xs btn btn-info pull-right ml5 open-grid" data-json='<?php echo json_encode($arrayopc[$tipo1->id]) ?>' <?php echo _P('tamanhos', $_SESSION['admin']['id_usuario'], 'excluir') ?>>
                  <i class="fa fa-plus"></i>
                  <?php echo $tipo1->tipo ?>
                </button>

              <?php } ?>
            </div>
            <div class="col-md-3 cols-xs-12 form-group">
              <label for="codigo_produto<?php echo $countInc ?>">
                Referência/Código:
                <span class="info-title" data-toggle="tooltip" title="Campo opcional. Caso deixado em branco, será gerado um código automático e individual.">?</span>
              </label>
              <input type="text" class="form-control" id="codigo_produto<?php echo $countInc ?>" name="codigo_produto[]" value="<?php echo $loop->codigo_produto == '' ? CodProduto($rws->nome_produto, $loop->id) : $loop->codigo_produto ?>" />
            </div>
            <div class="col-sm-3 col-xs-12 form-group">
              <label for="categoria<?php echo $countInc ?>">Categoria <small>(opcional)</small></label>
              <select name="categoria[]" id="categoria<?php echo $countInc ?>" class="form-control" style="width: 100%;">
                <option value="null">Selecione</option>
                <option value="F" <?php echo $loop->categoria == "F" ? " selected" : "" ?>>Feminino</option>
                <option value="M" <?php echo $loop->categoria == "M" ? " selected" : "" ?>>Masculino</option>
                <option value="N" <?php echo $loop->categoria == "N" ? " selected" : "" ?>>Neutro</option>
              </select>
            </div>
            <div class="col-sm-5 col-xs-12 form-group pull-right" style="background-color:#f3f3f3; padding: 10px 15px 10px 15px" id="frete_prod_<?php echo $loop->id ?>">
              <label class="show">Dados Frete: <?php echo $loop->freteproduto->nome_frete ?? '--' ?></label>
              Medidas: A: <?php echo $loop->freteproduto->altura ?>cm | L: <?php echo $loop->freteproduto->largura ?>cm |
              C: <?php echo $loop->freteproduto->comprimento ?>cm | P: <?php echo $loop->freteproduto->peso ?>Kg
              <a href="/adm/frete.php?codigo_id=<?php echo $loop->codigo_id ?>&produto_id=<?php echo $loop->id ?>&id_frete=<?php echo $loop->id_frete ?>" class="btn btn-default btn-xs btn-dados-frete pull-right" style="margin-top: -15px">
                adicionar frete
              </a>
              <input type="hidden" name="id_frete[]" value="<?php echo $loop->id_frete ?>" />
            </div>
            <div class="col-sm-12 col-xs-12"></div>
            <div class="col-sm-2 col-xs-12 form-group">
              <label for="preco_custo<?php echo $countInc ?>">Preço de custo: <small>(opcional)</small></label>
              <input type="text" name="preco_custo[]" value="<?php echo number_format($loop->preco_custo, 2, ',', '.') ?>" class="form-control text-right preco-mask" id="preco_custo<?php echo $countInc ?>" />
            </div>
            <div class="col-sm-2 col-xs-12 form-group">
              <label for="preco_venda<?php echo $countInc ?>">
                Preço de: <small>(opcional)</small>
                <span class="info-title" data-toggle="tooltip" title="Preencha este campo caso queira que apareça no site, ex: preço de R$: 15,00 por R$: 12,00">?</span>
              </label>
              <input type="text" name="preco_venda[]" value="<?php echo number_format($loop->preco_venda, 2, ',', '.') ?>" class="form-control text-right preco-mask" id="preco_venda<?php echo $countInc ?>" />
            </div>
            <div class="col-sm-2 col-xs-12 form-group">
              <label for="preco_promo<?php echo $countInc ?>">Preço no Site: <span class="info-title" data-toggle="tooltip" title="Preço que irá prevalecer nas vendas">?</span></label>
              <input type="text" name="preco_promo[]" value="<?php echo number_format($loop->preco_promo, 2, ',', '.') ?>" class="form-control text-right preco-mask" id="preco_promo<?php echo $countInc ?>" />
            </div>
            <div class="col-sm-2 col-xs-12 form-group">
              <label for="preco_lucro<?php echo $countInc ?>">Lucro % <span class="info-title" data-toggle="tooltip" title="Sua margem de lucro que você ganhara sobre esse produto">?</span></label>
              <input type="text" name="preco_lucro[]" value="<?php echo round($loop->preco_lucro, 2) ?>" class="form-control text-right" id="preco_lucro<?php echo $countInc ?>" readonly />
            </div>
            <!-- <div class="col-sm-12 col-xs-12"></div> -->
            <div class="col-sm-2 col-xs-12 form-group">
              <label for="estoque<?php echo $countInc ?>">
                Estoque:
              </label>
              <input type="text" name="estoque[]" value="<?php echo $loop->estoque ?>" class="form-control text-center" id="estoque<?php echo $countInc ?>" />
              <small>(Estoque comum do site)</small>
            </div>
            <div class="col-sm-2 col-xs-12 form-group">
              <label for="estoque_min<?php echo $countInc ?>">Qtde mínima p/ compra:</label>
              <input type="text" name="estoque_min[]" value="<?php echo $loop->estoque_min ?>" class="form-control text-center" id="estoque_min<?php echo $countInc ?>" />
              <small>(Qtde mínima de compra)</small>
            </div>
            <?php if ($Produtos->if_estoque > 0) { ?>
              <span class="bold ft14px col-sm-12 col-xs-12" style="margin-top: -15px; color: #dc4e4e">PENDENTES:
                <?php echo (int)$Produtos->pendentes ?></span>
            <?php } ?>
            <div class="col-lg-12" style="border-bottom: 1px #e3e3e3 solid; padding: 5px 15px; margin-bottom: 15px"></div>
          </div>
        <?php $countInc++;
        } ?>
        <?php ob_start(); ?>
        <script>
          my_toggle = (e) => {
            if ($(e).parent().next().is(":visible"))
              return $(e).toggleClass("fa-minus fa-plus").parent().next().fadeOut();

            return $(e).toggleClass("fa-plus fa-minus").parent().next().fadeIn();
          };

          // adiciona uma nova vairação
          var countInc = "<?php echo $countInc ?>";
          $("#form-produtos").on("click", "button.btn-add-grid", function(e) {
            var uri = window.location.href;
            // Tenta aumentar o counter
            countInc = countInc + 1;
            $.ajax({
              url: uri,
              type: "post",
              data: {
                codigo_id: "<?php echo $Produtos->codigo_id ?>",
                acao: "CopyProduto"
              },
              success: function(str) {

                var list = $("<div/>", {
                  html: str
                });

                // https://stackoverflow.com/questions/14542203/sorting-li-by-id-value
                // Link acima, mostra como tentar fazer isso
                // Tenta ordenar os elementos e pega o maior valor por padrao

                var items = list.find("#grid_variacao > .row");
                var sortedArray = items.map(function() {
                  return {
                    id: $(this).attr("id"),
                    element: $(this)[0].outerHTML
                  };
                });

                var appendTo = items.parent();

                items.remove();

                sortedArray.sort(function(a, b) {
                  return a.id > b.id ? -1 : 1;
                });

                $("#grid_variacao").prepend([
                  sortedArray[0].element
                ]).find(".row").first().css({
                  "background-color": "#fff6f6"
                });
              }
            });
          });

          // abre um select com um json dinamico para cada dados de grid
          $("#form-produtos").on("click", "button.open-grid", function(e) {
            var elem = $(e.currentTarget),
              json = elem.data("json"),
              select = $("<select/>", {
                name: "tmp",
                id: "tmp"
              });

            select.append($("<option/>", {
              value: "",
              text: "Selecione",
            }));

            $.map(json, function(v, i) {
              select.append([
                $("<option/>", {
                  value: v.id,
                  text: v.text,
                  selected: v.checked,
                  hex1: v.hex1,
                  hex2: v.hex2,
                  "data-grid": v.grid,
                  id_elem: v.id_elem
                })
              ])
            });

            $(elem).before([
              select,
              $("<span/>", {
                class: "badge ml5",
                html: [
                  $("<i/>", {
                    class: "fa fa-close",
                    click: function(e) {
                      $(e.currentTarget).parent().remove();
                      $(select).next().remove();
                      $(select).remove();
                    }
                  })
                ]
              })
            ]);

            $(elem).queue(function(ee) {
              $("#form-produtos").find("select").select2({
                templateResult: format_state,
                tags: true
              })
              ee();
            });
          });

          // Adiciona uma grid
          $("#form-produtos").on("change", "select[name='tmp']", function(e) {
            var elem = $(e.currentTarget).children(":selected"),
              data_grid = elem.data("grid")

            $("span" + data_grid).html([
              $("<span/>", {
                class: "badge",
                html: elem.text()
              }),
              // $("<input/>", { value: elem.val(), name: elem.attr("id_elem") + "[]", type: "hidden"})
            ]);

            var test = elem.attr("id_elem") + "[]";
            $(data_grid).parent().find($("input[name='" + test + "']")).val(elem.val());
          });
        </script>
        <?php $SCRIPT['script_manual'] .= ob_get_clean(); ?>
      </div>
    </div>

    <div class="panel panel-default">
      <div class="panel-heading panel-store clearfix">
        <div class="pull-left mr15" onclick="my_toggle(this)" style="cursor: pointer;">
          <i class="fa fa-plus"></i> Menus
        </div>
        <!-- <input type="text" id="myInput" class="form-control input-sm pull-left" onkeyup="my_search_menus()" placeholder="Procurar menus..." style="width: 320px;"/> -->
        <a class="btn-open btn-success btn btn-xs pull-right ml5" href="/adm/sub-grupos.php?codigo_id=<?php echo $GET['codigo_id'] ?>" data-title="Cadastrar/Editar de SubGrupos">
          Sub Menus <i class="fa fa-folder-open"></i>
        </a>
        <a class="btn-open btn-success btn btn-xs pull-right ml5" href="/adm/grupos.php?codigo_id=<?php echo $GET['codigo_id'] ?>" data-title="Cadastrar/Editar de Grupos">
          Menus <i class="fa fa-folder-open"></i>
        </a>
      </div>
      <div class="panel-body" id="grid_menus" style="display: none;">
        <?php
        $x = 0;
        $conditions['conditions'] = sprintf('loja_id=%u AND excluir = 0', $CONFIG['loja_id']);
        $Grupos = Grupos::all($conditions);
        $SubGrupos = SubGrupos::all($conditions);
        foreach ($Grupos as $k => $Grupo) {

          $grupo_check = false;
          foreach ($ProdutosMenus as $key => $value) {
            if ($Grupo->id == $value->id_grupo)
              $grupo_check = true;
          }
        ?>

          <div class="row">
            <div class="col-sm-12 col-xs-12 form-group">
              <input type="checkbox" name="id_grupo[<?php echo $x ?>]" value="<?php echo $Grupo->id ?>" id="g_<?php echo $k ?>" <?php echo $grupo_check ? 'checked' : null ?>>
              <label for="g_<?php echo $k ?>" class="input-checkbox"></label>

              <strong class="ft16px"><?php echo $Grupo->grupo ?></strong>

              <button type="button" class="fa fa-plus pull-right" onclick="my_menus_toggle('.sub-menu-<?php echo $Grupo->id ?>', this)"></button>

            </div>

            <div class="col-lg-12" style="border-bottom: 1px #e3e3e3 solid; margin: 0 0 15px 0"></div>

            <?php
            foreach ($SubGrupos as $k2 => $SubGrupo) {
              $subgrupo_check = false;
              foreach ($ProdutosMenus as $value2) {
                if ($Grupo->id == $value2->id_grupo && $SubGrupo->id == $value2->id_subgrupo)
                  $subgrupo_check = true;
              }
            ?>
              <div class="col-sm-11 col-sm-push-1 col-xs-12 form-group sub-menu-<?php echo $Grupo->id ?>" style="display: none; border-bottom: 1px #e3e3e3 solid; margin: 0 0 15px 0; padding-bottom: 10px;">
                <input type="checkbox" name="id_subgrupo[<?php echo $x ?>][]" value="<?php echo $SubGrupo->id ?>" id="sg_<?php echo $k . $k2 ?>" <?php echo $subgrupo_check ? 'checked' : null ?>>
                <label for="sg_<?php echo $k . $k2 ?>" ref="g_<?php echo $k ?>" class="input-checkbox"></label>
                <strong class="ft13px"><?php echo $SubGrupo->subgrupo ?></strong>
              </div>
            <?php } ?>

          </div>
          <?php
          // counter
          $x++;
          ?>
        <?php } ?>
        <?php ob_start(); ?>
        <script>
          my_menus_toggle = (e, ee) => {
            if ($(e).is(":visible")) {
              $(ee).toggleClass("fa-minus fa-plus");
              $(e).fadeOut();
              return;
            }
            $(ee).toggleClass("fa-plus fa-minus");
            $(e).fadeIn();
          };

          my_search_menus = () => {
            // // Declare variables
            // var input, filter, table, tr, td, i, txtValue;
            // input = document.getElementById("myInput");
            // filter = input.value.toUpperCase();
            // table = document.getElementById("grid_menus");
            // tr = table.getElementsByTagName("div");
            // // Loop through all table rows, and hide those who don't match the search query
            // for (i = 0; i < tr.length; i++) {
            // 	td = tr[i].getElementsByTagName("div")[0];
            // 	console.log(td);
            // 	if (td) {
            // 		txtValue = td.textContent || td.innerText;
            // 		if (txtValue.toUpperCase().indexOf(filter) > -1) {
            // 			tr[i].style.display = "";
            // 		} else {
            // 			tr[i].style.display = "none";
            // 		}
            // 	}
            // }
          }

          // $('input[id="myInput"]').keyup(function() {
          // 	var that = this,
          // 		$allListElements = $('#grid_menus > div.row');

          // 	var $matchingListElements = $allListElements.filter(function(i, li) {
          // 		var listItemText = $(li).text().toUpperCase(),
          // 			searchText = that.value.toUpperCase();

          // 		return ~listItemText.indexOf(searchText);
          // 	});

          // 	$allListElements.hide();
          // 	$matchingListElements.show();

          // 	//add this
          // 	$allListElements.parents('.form-group').hide();
          // 	$matchingListElements.parents('.form-group').show();
          // 	console.log($allListElements);
          // });
        </script>
        <?php $SCRIPT['script_manual'] .= ob_get_clean(); ?>
      </div>
    </div>
    <!--
		<div class="panel panel-default">
			<div class="panel-heading panel-store">
				Descrição
			</div>
			<div class="panel-body" id="abaDescricoes">
				<textarea id=""></textarea>
			</div>
		</div> -->

    <div class="panel panel-default" <?php echo _P('produtos-personalize', $_SESSION['admin']['id_usuario'], 'acessar') ?>>
      <div class="panel-heading panel-store">
        <i class="fa fa-plus" onclick="my_toggle(this)" style="cursor: pointer;"></i> Produtos Personalizados
      </div>
      <div class="panel-body" style="display: none;" id="form-personalize">
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
            <button type="button" class="btn btn-primary" id="save_personalize" style="width: 25%">salvar</button>
          </fieldset>
        </div>
        <?php
        if (isset($GET['action_type']) && $GET['action_type'] === 'RemoverPesonalized') {
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
        <table class="table mt5">
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
          $ProdutosPersonalizados = ProdutosPersonalizados::all(['conditions' => ['codigo_id=?', $GET['codigo_id']]]);
          foreach ($ProdutosPersonalizados as $personalize) { ?>
            <tr>
              <td>
                <?php echo $personalize->input_description ?>
              </td>
              <td align="center" nowrap="nowrap" width="1%">
                <?php echo $personalize->input_type == 'input' ? 'Texto' : '' ?>
              </td>
              <td align="center" nowrap="nowrap" width="1%">
                <a href="/adm/produtos/produtos-cadastrar.php?action_type=RemoverPesonalized&personalize_id=<?php echo $personalize->id ?>&codigo_id=<?php echo $GET['codigo_id'] ?>&acao=<?php echo $GET['acao'] ?>" class="btn btn-danger btn-xs remover-personalize">
                  remover
                </a>
              </td>
            </tr>
          <?php } ?>
        </table>
      </div>
      <?php ob_start(); ?>
      <script>
        // /**
        //  * Jquery para Personalizado
        //  */
        // $("a[href=#aba4]").on("click", function() {
        //   $.ajax({
        //     url: "/adm/produtos/produtos-personalize.php",
        //     data: {
        //       codigo_id: "<?php echo $GET['codigo_id'] ?>"
        //     },
        //     success: function(str) {
        //       var list = $("<div/>", {
        //         html: str
        //       });
        //       $("#aba4").html(list.find("#aba4").html());
        //     }
        //   });
        // });

        $("#form-personalize").on("click", "#save_personalize", function(e) {
          e.preventDefault();
          let data = $("#form-personalize").find("input[name]").serialize();
          $.ajax({
            url: window.location.href,
            data: data,
            success: function(str) {
              let list = $("<div/>", {
                html: str
              });
              $("#form-personalize").html(list.find("#form-personalize").html());
            }
          });
        });

        $("#form-personalize").on("click", "a.remover-personalize", function(e) {
          e.preventDefault();
          var Href = this.href || e.target.href;
          if (Href.indexOf('RemoverPesonalized').lenght !== -1)
            if (!confirm("Deseja realmente excluir!"))
              return false;

          $.ajax({
            url: this.href || e.target.href,
            success: function(str) {
              let list = $("<div/>", {
                html: str
              });
              $("#form-personalize").html(list.find("#form-personalize").html());
            }
          });
        });
      </script>
      <?php $SCRIPT['script_manual'] .= ob_get_clean(); ?>
    </div>
  </div>

  <div class="affix-bottom">
    <button type="submit" class="btn btn-primary">salvar</button>
    <a href="/adm/produtos/produtos-cadastrar.php?acao=CancelarNovoProduto&codigo_id=<?php echo $Produtos->codigo_id ?>" class="btn btn-danger" <?php echo _P('produtos-cadastrar', $_SESSION['admin']['id_usuario'], 'incluir') ?>>excluir</a>
  </div>
  <input type="hidden" name="acao" value="<?php echo $GET['acao'] ?>" />
  <input type="hidden" name="codigo_id" value="<?php echo $Produtos->codigo_id ?>" />
</form>


<?php ob_start(); ?>
<script>
  $('[data-toggle="tooltip"]').tooltip();
  GLOBALS = {
    cores: "<?php echo $GET['cores'] ?>",
    tamanhos: "<?php echo $GET['tamanhos'] ?>",
    codigo_id: "<?php echo $GET['codigo_id'] ?>"
  };

  $(document).on("click", "a", function() {
    var href = this.href || e.target.href;
    if (href.search('CancelarNovoProduto') > '0')
      if (!confirm("Deseja realmente excluir!")) return false;
  });

  /**
   * Produtos descrições com abas
   * Fase de teste 04-12-2017
   */
  $("a[href=#abaDescricoes]").on("click", function() {
    $.ajax({
      url: "/adm/produtos/produtos-descricoes-abas.php",
      data: {
        codigo_id: "<?php echo $GET['codigo_id'] ?>"
      },
      success: function(str) {
        var list = $("<div/>", {
          html: str
        });
        $("#abaDescricoes").html(list.find("#abaDescricoes").html());
      }
    });
  });

  $("#abaDescricoes").on("submit", function(e) {
    e.preventDefault();
    var DataStr = $(e.target).serialize(),
      DataAction = $(e.target).attr("action");
    $.ajax({
      url: DataAction,
      type: "post",
      data: DataStr,
      success: function(str) {
        var list = $("<div/>", {
          html: str
        });
        $("#abaDescricoes").html(list.find("#abaDescricoes").html());
      },
      error: function(a, b, c) {
        console.log(a.responseText + "\n" + b + "\n" + c);
      },
      complete: function() {
        $("a[href=#abaDescricoes]").trigger("click");
      }
    });
  });

  /**
   * Delete descrições/complementares
   */
  $("#abaDescricoes").on("click", "a[data-excluir]", function(e) {
    e.preventDefault();
    var href = this.href || e.target.href;
    if (href.search('excluir') > '0')
      if (!confirm("Deseja realmente excluir!")) return false;

    $.ajax({
      url: href,
      success: function(str) {
        var list = $("<div/>", {
          html: str
        });
        $("#abaDescricoes").html(list.find("#abaDescricoes").html());
      },
      error: function(a, b, c) {
        console.log(a.responseText + "\n" + b + "\n" + c);
      },
      complete: function() {
        $("a[href=#abaDescricoes]").trigger("click");
      }
    });
  });

  /**
   * Inicia o editor de texto
   */
  $("#abaDescricoes").on("click", "[data-init]", function(e) {
    var textarea = $(this).attr("data-init");
    if (tinyMCE.activeEditor !== null)
      tinyMCE.EditorManager.execCommand('mceRemoveEditor', true, textarea);

    $("#" + textarea).tinymce({
      entity_encoding: "raw",
      language: "pt_BR",
      selector: "#" + textarea,
      toolbar_items_size: "small",
      menubar: false,
      toolbar1: "newdocument cut copy paste | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | styleselect formatselect fontselect fontsizeselect",
      toolbar2: "undo redo | bullist numlist | outdent indent blockquote | link unlink anchor image media code | forecolor backcolor | insertdatetime preview",
      plugins: [
        "advlist autolink autosave link image lists charmap print preview hr anchor pagebreak spellchecker",
        "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
        "table contextmenu directionality emoticons template textcolor paste textcolor colorpicker textpattern"
      ],
      paste_data_images: true,
      image_advtab: true,
      image_title: true,
      relative_urls: false,
      remove_script_host: false,
      convert_urls: true,
      // enable automatic uploads of images represented by blob or data URIs
      automatic_uploads: true,
      // URL of our upload handler (for more details check: https://www.tinymce.com/docs/configure/file-image-upload/#images_upload_url)
      images_upload_url: "<?php echo URL_BASE ?>public/imgs/tiny-mce/uploads.php",
      images_upload_base_path: "/public/imgs/tiny-mce/",
      // here we add custom filepicker only to Image dialog
      file_picker_types: "image",
      image_list: [
        <?php
        $js = '';
        foreach (glob('../public/imgs/tiny-mce/{*.jpg}', GLOB_BRACE) as $name => $url) {
          $title = explode('/', $url);
          $js .= '{ title: "' . end($title) . '", value: "' . $url . '"},';
        }
        echo rtrim($js, ',');
        ?>
      ]
    });
  });

  /**
  	//  * Jquery para Personalizado
      //  */
  // $("a[href=#aba4]").on("click", function(){
  // 	$.ajax({
  // 		url: "/adm/produtos/produtos-personalize.php",
  // 		data: { codigo_id: "<?php echo $GET['codigo_id'] ?>" },
  // 		success: function(str){
  // 			var list = $("<div/>",{ html: str });
  // 			$("#aba4").html( list.find("#aba4").html() );
  // 		}
  // 	});
  // });

  // $("#aba4").on("submit", "#form-personalize", function(e){
  // 	e.preventDefault();
  // 	var Data = $(this).find("input[name]").serialize();
  // 	$.ajax({
  // 		url: "/adm/produtos/produtos-personalize.php?codigo_id=<?php echo $GET['codigo_id'] ?>",
  // 		data: Data,
  // 		success: function(str){
  // 			var list = $("<div/>",{ html: str });
  // 			$("#aba4").html( list.find("#aba4").html() );
  // 		}
  // 	});
  // });

  // $("#aba4").on("click", "a.remover-personalize", function(e){
  // 	e.preventDefault();
  // 	var Href = this.href||e.target.href;
  // 	if( Href.indexOf('Remover').lenght !== -1 )
  // 		if( ! confirm("Deseja realmente excluir!") )
  // 			return false;

  // 	$.ajax({
  // 		url: this.href||e.target.href,
  // 		success: function( str ){
  // 			var list = $("<div/>",{ html: str });
  // 			$("#aba4").html( list.find("#aba4").html() );
  // 		}
  // 	});
  // });

  // /**
  //  * Grid de Produtos
  //  */
  // $("a[href=#grid_produtos]").on("click", function(e){
  // 	e.preventDefault();
  // 	$.ajax({
  // 		url: "/adm/produtos/produtos-grid.php",
  // 		data: { codigo_id: "<?php echo $GET['codigo_id'] ?>" },
  // 		success: function(str){
  // 			var list = $("<div/>",{ html: str });
  // 			$("#grid_produtos").html( list.find("#grid_produtos").html() );
  // 		}
  // 	});
  // });

  // /**
  //  * Grid de Produtos
  //  */
  // $("a[href=#grid_kits]").on("click", function(e){
  // 	e.preventDefault();
  // 	$.ajax({
  // 		url: "/adm/produtos/produtos-kits.php",
  // 		data: { codigo_id: "<?php echo $GET['codigo_id'] ?>" },
  // 		success: function(str){
  // 			var list = $("<div/>",{ html: str });
  // 			$("#grid_kits").html( list.find("#grid_kits").html() );
  // 		}
  // 	});
  // });

  $.widget('custom.catcomplete', $.ui.autocomplete, {
    _create: function() {
      this._super();
      this.widget().menu('option', 'items', '> :not(.ui-autocomplete-category)');
    },
    _renderMenu: function(ul, items) {
      var that = this,
        currentCategory = '';
      $.each(items, function(index, item) {
        var li;
        if (item.category != currentCategory) {
          ul.append('<li class=\"ui-autocomplete-category\">' + item.category + '</li>');
          currentCategory = item.category;
        }
        li = that._renderItemData(ul, item);
        if (item.category) {
          li.attr('aria-label', item.codigo + ' : ' + item.label);
        }
      });
    }
  });

  <?php
  include __DIR__ . '/js/jquery.produtos.js';
  // include __DIR__ . '/js/jquery.produtos.cores.js';
  // include __DIR__ . '/js/jquery.produtos.tamanhos.js';
  // include __DIR__ . '/js/jquery.produtos.cores-tamanhos.js';
  // include __DIR__ . '/js/jquery.produtos.menus-submenus.js';
  include __DIR__ . '/js/jquery.produtos.fotos.js';
  ?>
</script>
<?php
$SCRIPT['script_manual'] .= ob_get_clean();

include '../rodape.php';
