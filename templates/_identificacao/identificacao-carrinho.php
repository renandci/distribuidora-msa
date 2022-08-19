<?php

switch ($ACAO_POST) {
  case 'AlterarCarrinhoCompras':

    $a = null;
    $condissao = !empty($POST['condicao']) ? $POST['condicao'] : null;

    if ($condissao == 'mais') {
      $a = '+';
    } elseif ($condissao == 'menos') {
      $a = '-';
    }

    $Carrinho = Carrinho::find((int)$POST['id'])->to_array([
      'include' => ['produto']
    ]);

    $str = ($a == '+' || $a == '-');

    if ($a == '+' && $Carrinho['produto']['estoque'] > $Carrinho['quantidade']) {
      header(sprintf('location: /identificacao/carrinho?error[%u]=%s', $POST['id'], true));
      return;
    } else if ($str) {
      $Quantidade = ($a == '+') ? $Carrinho['quantidade'] + 1 : $Carrinho['quantidade'] - 1;

      Carrinho::update_all([
        'set' => [
          'quantidade' => $Quantidade,
          'cliente_ip' => retornaIpReal(),
          'id_cupom' => 0,
          'frete_tipo' => '',
          'frete_valor' => '0.00',
          'cep' => ''
        ],
        'conditions' => [
          'id_session=? and id=?', session_id(), (int)$POST['id']
        ]
      ]);

      if ($Quantidade == 0) {
        Carrinho::table()->delete(array('id' => array($POST['id'])));
        header('location: /identificacao/carrinho');
        return;
      }
    } else {
      Carrinho::table()->delete(array('id' => array($POST['id'])));
    }

    header('location: /identificacao/carrinho');
    return;
    break;

  case 'InserirCupomCompras':
    /**
     * query verifica o cupom desconto
     * retorna valores e id do cupom da tabela cupom_envios
     * adicina o o id no carrinho para efetuar somas
     */
    $CUPOM = $POST['cupom'];
    $conditions['conditions'] = ''
      . 'cupons.cupom_codigo="%s" and '
      . 'cupons.cupom_valormin <= (SELECT * FROM ('
      . 'SELECT SUM(PRD2.preco_promo * CAR2.quantidade) as T '
      . 'FROM produtos PRD2 '
      . 'LEFT OUTER JOIN carrinho CAR2 ON CAR2.id_produto = PRD2.id '
      . 'WHERE CAR2.id_session="%s") AS VALOR) and '
      . '(0 = (SELECT count(*) as N FROM ('
      . 'SELECT pedidos.id_cupom '
      . 'FROM pedidos '
      . 'LEFT OUTER JOIN cupons ON cupons.id = pedidos.id_cupom '
      . 'WHERE pedidos.ip="%s" and cupons.cupom_codigo="%s" and (cupons.cupom_cliente_id > 0 or (md5(pedidos.id_cliente)="%s"))) AS BLOCK)) and '
      . 'cupons.cupom_excluir=0';
    $conditions['conditions'] = sprintf(
      $conditions['conditions'],
      $CUPOM,
      session_id(),
      retornaIpReal(),
      $CUPOM,
      $_SESSION['cliente']['id_cliente']
    );

    $return = false;
    $resultCupom = Cupons::first($conditions);

    $Erro_Cupom = '';
    $Erro_Cupom = Cupons::connection()->last_query;

    $dtnow = strtotime(date('Y-m-d H:i:s'));
    $dtdb_ini = strtotime($resultCupom->cupom_dataini);
    $dtdb_fin = strtotime(!empty($resultCupom->cupom_datafin) ? $resultCupom->cupom_datafin : date('Y-m-d 23:59:59'));

    $return = true;
    if (!empty($resultCupom->id) > 0 && ($dtdb_ini <= $dtnow && $dtdb_fin >= $dtnow)) {
      if (Cupons::update_all([
        'set' => [
          'cupom_usados' => ($resultCupom->cupom_usados + 1),
        ],
        'conditions' => ['id=?', $resultCupom->id]
      ])) {
        Carrinho::update_all(['set' => ['id_cupom' => $resultCupom->id], 'conditions' => ['id_session=?', session_id()]]);
      } else {
        $return = false;
      }

      if (!$return)
        $Erro_Cupom = 'Cupom inválido!!!';
    } else {
      $Erro_Cupom = 'Cupom inválido!!!';
    }

    header('location: /identificacao/carrinho?error_cupom=' . $Erro_Cupom);
    return;
    break;

  case 'RemoverCupomCompras':
    /**
     * query verifica o cupom desconto
     * retorna valores e id do cupom da tabela cupom_envios
     * adicina o o id no carrinho para efetuar somas
     */
    if (Cupons::connection()->query(sprintf('UPDATE cupons SET cupom_usados = (cupom_usados - 1) WHERE cupom_codigo="%s" and cupom_excluir=0', $POST['cupom']))) {
      if (Carrinho::update_all(array('set' => array('id_cupom' => 0), 'conditions' => array('id_session=?', session_id())))) {
        $return = true;
      } else {
        $return = false;
      }
    } else {
      $return = false;
    }
    header('location: /identificacao/carrinho');
    return;
    break;
}

$x = 1;
$CarrinhoCompras = $CONFIG['carrinho_all'];
$CarrinhoComprasCount = count($CarrinhoCompras);
?>
<div id="carrinho-recarregar">
  <?php if ($ACAO_GET != 'lista-desejos') { ?>
    <h2 class="mb25">Meu Carrinho</h2>
  <?php } else { ?>
    <h2 class="mb25">
      <i class="fa fa-heart" aria-hidden="true"></i>
      <b>Lista de Desejos</b>
    </h2>
    <span class="ft18px mb30 show">
      Gostou de um produto e quer vê-lo mais tarde?<br />
      Adicione à sua lista de desejos para acessar quando quiser.
    </span>
  <?php } ?>

  <?php if ($CarrinhoComprasCount == 0) { ?>
    <div class="row">
      <div href="/produtos" class="col-sm-3 col-sm-12 ft50px">
        <i class="fa fa-5x fa-shopping-cart"></i>
      </div>
      <div class="col-sm-9 col-sm-12 h3">
        Seu Carrinho de compras encontra-se vazio!<br />
        <a href="/produtos">Clique aqui para continuar comprando.</a>
        <form action="/produtos" class="mt15 cx-pesquisa-topo clearfix">
          <span class="desenho-campo-busca-cart">
            <input autocomplete=off type="text" name="pesquisar" id="pesquisar-cart" placeholder="Buscar Produtos" class="input-pesquisar" />
            <button type="submit"><i class="fa fa-search"></i></button>
            <ul class="pesquisa-rapida retornar-pesquisa"></ul>
          </span>
        </form>
      </div>
    </div>
  <?php } ?>

  <?php if ($CarrinhoComprasCount > 0) { ?>
    <div class="clearfix mb25">
      <div class="clearfix">
        <ul class="table-carrinho table-carrinho-titulos row">
          <li class="col-md-7 col-xs-3">Item(s)</li>
          <li class="col-md-1 hidden-xs">Preço</li>
          <li class="col-md-1 hidden-xs">Quantidade</li>
          <li class="col-md-2 hidden-xs">Subtotal</li>
        </ul>
        <?php
        $r = [];
        $TOTAL_ITENS       = 0;
        $TIPO_FRETE       = 0;
        $TOTAL_FRETE       = 0;
        $TOTAL_FRETESOMA    = 0;
        $TOTAL_CARRINHOSOMA    = 0;

        $TOTAL_DESCONTO      = 0;
        $TOTAL_CARRINHO      = 0;
        $TOTAL_CARRINHO_FRETE  = 0;

        foreach ($CarrinhoCompras as $r) { ?>
          <ul class="row table-carrinho media">
            <li class="col-md-1 col-xs-4 media-left media-middle">
              <span class="media-left">
                <img src="<?php echo Imgs::src($r->imagem, 'smalls'); ?>" alt="<?php echo substr($r->nome_produto, 0, 30) ?>" class="lazy img-responsive" />
              </span>
            </li>
            <li class="col-md-5 col-xs-7 media-middle">
              <a class="product-title" href="/<?php echo converter_texto($r->nome_produto) ?>/<?php echo $r->id_produto ?>/p">
                <?php echo $r->nome_produto; ?>
              </a>
              <?php
              echo sprintf('<span class="show tag-small">Cód: %s</span>', CodProduto($r->nome_produto, $r->id_produto, $r->codigo_produto));
              echo ($r->nomecor) ? sprintf('<span class="show tag-small">%s: %s</span>', $r->tipocores, $r->nomecor) : '';
              echo ($r->nometamanho) ? sprintf('<span class="show tag-small">%s: %s</span>', $r->tipotamanhos, $r->nometamanho) : '';
              echo $r->estoque < $r->quantidade ? '<span class="show ft10px red" data-estoque="zero">Quantidade indisponível! (<a href="/identificacao/carrinho" class="span click-me" id="' . $r->id . '" value="" style="cursor: pointer;">clique aqui para continuar comprando</a>)</span>' : '';
              ?>
              <span class="text-danger ft11px <?php echo isset($GET['error'][$r->id_produto]) ? 'show' : 'hidden' ?>">
                Quantidade insuficiente em estoque.
              </span>
            </li>
            <li class="col-md-2 col-xs-12 media-middle">
              <span class="hidden-xs clearfix">
                <span class="price-old <?php echo $r->preco_venda > 0 ? 'show' : 'hidden'; ?>">De R$: <?php echo number_format($r->preco_venda, 2, ',', '.'); ?></span>
                <span class="price-now show">Por R$: <?php echo number_format(desconto_boleto($r->preco_promo, $CONFIG['desconto_boleto']), 2, ',', '.'); ?></span>
              </span>
            </li>
            <li class="col-md-1 col-xs-4 media-middle">
              <span class="campo-quantidade-carrinho">
                <a href="/identificacao/carrinho" class="span fa fa-minus click-me" id="<?php echo $r->id; ?>" value="menos"></a>
                <span><input type="text" class="input-carrinho black-40" value="<?php echo $r->quantidade; ?>" id="<?php echo $r->id; ?>" disabled /></span>
                <a href="/identificacao/carrinho" class="span fa fa-plus click-me" id="<?php echo $r->id; ?>" value="mais"></a>
              </span>
            </li>
            <li class="col-md-2 col-xs-5 text-right media-middle">
              <span class="color-001">
                R$: <?php echo number_format((desconto_boleto($r->preco_promo * $r->quantidade, $CONFIG['desconto_boleto'])), 2, ',', '.'); ?>
              </span>
            </li>
            <li class="col-md-1 col-xs-3 text-right media-middle">
              <a href="/identificacao/carrinho" title="remover" class="span fa fa-trash click-me ft20px" id="<?php echo $r->id; ?>" value="" style="cursor: pointer;"></a>
            </li>
          </ul>
        <?php
          $TOTAL_ITENS += $r->quantidade;
          $TIPO_FRETE = $r->frete_tipo;
          $TOTAL_FRETE = !empty($r->frete_valor) ? $r->frete_valor : 0;
          $CUPOM = $r->cupom_codigo;
          $ID_CUPOM = $r->id_cupom;
          $CUPOM_VALOR = $r->cupom_value;
          $CUPOM_TIPO = $r->cupom_desconto;

          $TOTAL_DESCONTO = number_format($r->cupom_value, 2, ',', '.');
          $TOTAL_CARRINHO += ($r->preco_promo * $r->quantidade);
          $TOTAL_CARRINHO_FRETE += ($r->preco_promo * $r->quantidade) + $r->frete_valor;

          ++$x;
        }

        $TOTAL = valor_pagamento($TOTAL_CARRINHO, $TOTAL_FRETE, $CUPOM_VALOR, $CUPOM_TIPO, $CONFIG['desconto_boleto']);
        // print_r($TOTAL);
        ?>

        <ul class="carrinho-total row">
          <li class="col-md-12 col-xs-12">
            <ul class="row">
              <li class="bg-branco col-md-8 col-xs-12 form-horizontal" id="cupom-desconto">
                <?php
                // Adicionado como true, para não haver o cupom de desconto
                // NOTA: se for falso deve mostra para todas as loja, pois ha muitas config para mexe, então o esquema esta ao contrario
                if (empty($STORE['config']['cupom'])) { ?>
                  <div class="form-group mb15 mt15">
                    <label class="col-sm-12 text-left control-label"><b>Você possui algum cupom de desconto? Insira abaixo.</b></label>
                    <div class="col-sm-6">
                      <input type="text" class="form-control mb5" id="cogido_cupom" value="<?php echo $CUPOM ?>" <?php echo isset($ID_CUPOM) && $ID_CUPOM > 0 ? ' disabled' : '' ?> autocomplete="off" />
                    </div>
                    <div class="col-sm-2" style="padding: 0;">
                      <?php if (isset($ID_CUPOM) && $ID_CUPOM == 0) { ?>
                        <button type="button" class="btn btn-primary btn-block" id="inserir-cupom">validar</button>
                      <?php } else { ?>
                        <button type="button" class="btn btn-secundary btn-block" id="remover-cupom">usar depois</button>
                      <?php } ?>
                      <img src="/public/imgs/ajax-loader.gif" style="display: none;" id="calcular-cupom" />
                    </div>
                    <span class="col-sm-12 ft11px text-default" id="erro_cupom"><?php echo isset($GET['error_cupom']) && $GET['error_cupom'] != '' ? $GET['error_cupom'] : '' ?></span>
                  </div>
                <?php } ?>
              </li>
              <li class="col-md-4 col-xs-12">
                <ul class="row mt15">
                  <!-- <li class="is-border col-md-12 col-xs-12 color-001 text-right" style="border: none">
									Subtotal (<?php echo ($TOTAL_ITENS > 1) ? "{$TOTAL_ITENS} itens" : "{$TOTAL_ITENS} item"; ?>):
									<span class="ft16px">R$: <?php echo number_format($TOTAL['TOTAL'], 2, ',', '.'); ?></span>
								</li> -->
                  <li class="is-border col-md-12 col-xs-12" style="border: none">
                    <span class="pull-right text-right">
                      Cupom de Desconto: <?php echo $TOTAL['TOTAL_CUPOM'] ?>
                    </span>
                  </li>
                  <li class="is-border col-md-12 col-xs-12 text-right">
                    <span class="load-preco"></span>
                    <span class="bold">Valor total</span>
                    <span class="ft28px color-001 bold">R$: <?php echo number_format($TOTAL['TOTAL_COMPRA_C_BOLETO'], 2, ',', '.'); ?></span>
                  </li>
                  <!-- <li class="is-border col-md-12 col-xs-12 text-right ft14px<?php echo (empty($CONFIG['desconto_boleto']) ? ' hidden' : '') ?>">
									<span class="load-preco"></span>
									ou no Boleto
									<span class="color-001 bold" data-atacadista="<?php echo (($CONFIG['atacadista'] && ($CONFIG['atacadista_min'] >= $TOTAL['TOTAL_COMPRA_C_BOLETO'] || $CONFIG['atacadista_max'] <= $TOTAL['TOTAL_COMPRA_C_BOLETO'])) ? 1 : 0) ?>" data-min="<?php echo (($CONFIG['atacadista_min'] >= $TOTAL['TOTAL_COMPRA_C_BOLETO']) ? 1 : 0) ?>" data-max="<?php echo (($CONFIG['atacadista_max'] <= $TOTAL['TOTAL_COMPRA_C_BOLETO']) ? 1 : 0) ?>">
										R$: <?php echo number_format($TOTAL['TOTAL_COMPRA_C_BOLETO'], 2, ',', '.'); ?>
									</span>
								</li> -->
                  <?php if (isset($STORE['config']['cart']['frete']) && $STORE['config']['cart']['frete'] == true) { ?>
                    <!--
								<li class="col-md-12 col-xs-12 form-horizontal">
									<div class="form-group carrinho-cep">
										<label class="is-border col-sm-6 col-xs-6 toggle-elem control-label<?php echo (isset($TOTAL['TOTAL_FRETE']) && $TOTAL['TOTAL_FRETE'] > 0) ? ' hidden' : '' ?>" val="1">
											Consultar CEP:
										</label>
										<div class="is-border col-sm-6 col-xs-6 toggle-elem<?php echo (isset($TOTAL['TOTAL_FRETE']) && $TOTAL['TOTAL_FRETE'] > 0) ? ' hidden' : '' ?>" val="1">
											<input type="tel" class="form-control text-right" id="carregarcep">
											<span><img src="<?php echo Imgs::src('ajax-loader.gif', 'public') ?>" width="25" style="display:none;" id="calcular-frete"/></span>
											<i class="fa fa-close close<?php echo (isset($TOTAL['TOTAL_FRETE']) && $TOTAL['TOTAL_FRETE'] == 0) ? ' hidden' : '' ?>" style="cursor: pointer; position: absolute; top: 0; margin: 7px 0 -15px 0; right: 0;"></i>
										</div>

										<label class="is-border col-sm-6 col-xs-6 toggle-elem control-label<?php echo (isset($TOTAL['TOTAL_FRETE']) && $TOTAL['TOTAL_FRETE'] == 0) ? ' hidden' : '' ?>" val="0">
											Frete:
										</label>
										<div class="is-border col-sm-6 col-xs-6 toggle-elem <?php echo (isset($TOTAL['TOTAL_FRETE']) && $TOTAL['TOTAL_FRETE'] == 0) ? ' hidden' : '' ?>" val="0">
											<i class="fa fa-pencil-square-o close" style="cursor:pointer;"></i>
											<span class="color-001 ft16px pull-right mr5">R$: <?php echo number_format($TOTAL['TOTAL_FRETE'], 2, ',', '.'); ?></span>
										</div>
									</div>
									<div id="consulta-cep">
										<?php // if($ACAO_POST == 'BuscarCep') { echo AtualizarFrete(session_id(), $_SESSION, $POST['cep']); }
                    ?>
									</div>
								</li>
								-->
                    <li class="col-md-12 col-xs-12 form-horizontal">
                      <div class="form-group carrinho-cep">
                        <label class="is-border col-sm-6 col-xs-6 toggle-elem control-label<?php echo isset($TIPO_FRETE) ? ' hidden' : '' ?>" val="1">
                          Consultar CEP:
                        </label>
                        <div class="is-border col-sm-6 col-xs-6 toggle-elem<?php echo isset($TIPO_FRETE) ? ' hidden' : '' ?>" val="1">
                          <input type="tel" class="form-control text-right" id="carregarcep">
                          <span><img src="<?php echo Imgs::src('ajax-loader.gif', 'public') ?>" width="25" style="display:none;" id="calcular-frete" /></span>
                          <i class="fa fa-close close<?php echo isset($TIPO_FRETE) ? ' hidden' : '' ?>" style="cursor: pointer; position: absolute; top: 0; margin: 7px 0 -15px 0; right: 0;"></i>
                        </div>
                        <label class="is-border col-sm-6 col-xs-6 toggle-elem control-label<?php echo !isset($TIPO_FRETE) ? ' hidden' : '' ?>" val="0">
                          Frete:
                        </label>
                        <div class="is-border col-sm-6 col-xs-6 toggle-elem <?php echo !isset($TIPO_FRETE) ? ' hidden' : '' ?>" val="0">
                          <i class="fa fa-pencil-square-o close" style="cursor:pointer;"></i>
                          <span class="color-001 ft16px pull-right mr5">R$: <?php echo number_format($TOTAL['TOTAL_FRETE'], 2, ',', '.'); ?></span>
                        </div>
                      </div>
                      <div id="consulta-cep">
                        <?php // if($ACAO_POST == 'BuscarCep') { echo AtualizarFrete(session_id(), $_SESSION, $POST['cep']); }
                        ?>
                      </div>
                    </li>
                  <?php } ?>
                </ul>
              </li>
            </ul>
          </li>
        </ul>
      </div>
      <div class="clearfix mt25 mb35">
        <?php if ($ACAO_GET != 'lista-desejos') { ?>
          <a href="/produtos" class="btn btn-secundary btn-lg pull-left btn-back btn-go-back">
            <i class="fa fa-arrow-left"></i>
            <span class="hidden-lg hidden-md">Comprar +</span>
            <span class="hidden-sm hidden-xs">Continuar comprando</span>
          </a>
          <a href="/identificacao/login/?_u=<?php echo URL_BASE ?>identificacao/checkout-new#new-ckeckout" class="btn btn-primary btn-lg btn-go-checkout pull-right btn-spinner">
            Finalizar <span class="hidden-xs">compra</span>
            <i class="fa fa-arrow-right"></i>
          </a>
        <?php } else { ?>
          <a href="/" class="btn btn-primary">Adicionar mais produtos à lista de desejos</a>
          <a href="/" class="btn btn-rosa" id="gerar-pedido">transformar em pedido</a>
        <?php } ?>
      </div>
    </div>
  <?php } ?>
</div>

<?php ob_start(); ?>
<script>
  // Define as mesma configurações para as requisições
  $.ajaxSetup({
    complete: Checkout.finalcompra,
    beforeSend: function() {
      $(".load-preco").html([$("<img/>", {
        src: "<?php echo Imgs::src('ajax-loader.gif', 'public') ?>",
        width: "25px"
      })]);
    },
    success: function(str) {
      var list = $("<div/>", {
        html: str
      });
      $("#carrinho-recarregar").html(list.find("#carrinho-recarregar").html());
    },
    error: function(x, m, t) {
      console.log("%O", x);
      console.log(x.responseText + "\n" + m + "\n" + t);
    }
  });

  Checkout = {
    finalcompra: function(str) {
      var list = $("<div/>", {
          html: str.responseText
        }),
        atacadista = list.find("[data-atacadista]").attr("data-atacadista"),
        atacadista_min = list.find("[data-min]").attr("data-min"),
        atacadista_max = list.find("[data-max]").attr("data-max");

      if (atacadista > 0)
        $($("<div/>", {
          id: "alert-info",
          class: "alert alert-info",
          html: [
            (atacadista_min > 0 ? "Você não atingiu seu limite de compra." : null),
            (atacadista_max > 0 ? "Você já atingiu seu limite de compra." : null),
            $("<span/>", {
              "data-estoque": "zero"
            })
          ]
        })).prependTo("#carrinho-recarregar");
      else
        $("#alert-info").remove();

      if ($("[data-estoque=zero]").length > 0)
        $(".btn-go-checkout").attr({
          "href": "javascript:void()"
        }).addClass("disabled");
      else
        $(".btn-go-checkout").attr({
          "href": "<?php echo URL_BASE ?>identificacao/login/?_u=<?php echo URL_BASE ?>identificacao/checkout-new#new-ckeckout"
        }).removeClass("disabled");
    },
    atualizar_carrinho: function(eThis) {
      var GRATIS = $(eThis).attr('data-gratis') || '',
        TIPOFRETE = $(eThis).attr('id') || '',
        VALORFRETE = $(eThis).attr('data-valor') || '',
        PRAZOSFRETE = $(eThis).parent().next().find('span').next().html();

      // $.when(
      //   $.ajax({
      //     url: window.location.href,
      //     type: "POST",
      //     dataType: "json",
      //     data: {
      //       acao: "AtualizarCarrinho",
      //       tipofrete: TIPOFRETE,
      //       valorfrete: VALORFRETE,
      //       prazosfrete: PRAZOSFRETE
      //     }
      //   }),
      //   $.get(window.location.href)
      // );
    }
  };

  $("#carregarcep").mask("99999-999", {
    onComplete: function(cep) {
      $.ajax({
        // url: window.location.href,
        // type: "post",
        // data: { acao: "BuscarCep", cep: cep },
        url: "/app/includes/ajax-correios-produto.php",
        data: {
          acao: "CalcularFreteCarrinho",
          produto_cep: cep
        },
        // retries: 3,
        // timeout: 5000,
        // retryInterval: 5000,
        beforeSend: function() {
          $("#calcular-frete").fadeIn(0);
        },
        complete: function() {
          $("#calcular-frete").fadeOut(0);
        },
        success: function(str) {
          var list = $("<div/>", {
            html: str
          });
          $("#consulta-cep").html(list.find("#recarregar-frete").html());
        }
      });
    }
  });

  /**
   * AlterarCarrinhoCompras
   */
  $("#carrinho-recarregar").on("click", "a.click-me", function(e) {
    e.preventDefault();
    var id = this.id,
      input = $("input[id=" + this.id + "]").val(),
      condicao = $(this).attr("value");

    console.log("TESTE", input, condicao, id);

    $.ajax({
      url: this.href,
      type: "post",
      data: {
        id: id,
        acao: "AlterarCarrinhoCompras",
        condicao: condicao
      }
    });
  });

  /**
   * Cupons de desconto
   */
  $("#carrinho-recarregar").on("click", "#inserir-cupom", function() {
    $.ajax({
      url: window.location.href,
      type: "post",
      data: {
        acao: "InserirCupomCompras",
        cupom: $("#cogido_cupom").val()
      }
    });
  });

  $("#carrinho-recarregar").on("click", "#remover-cupom", function() {
    $.ajax({
      url: window.location.href,
      type: "post",
      data: {
        acao: "RemoverCupomCompras",
        cupom: $("#cogido_cupom").val()
      }
    });
  });

  $("#carrinho-recarregar").on("click", ".close", function() {
    $(".toggle-elem").each(function() {
      if ($(this).attr("val") === "1")
        $(this).attr({
          "val": "0"
        }).removeClass("hidden");
      else
        $(this).attr({
          "val": "1"
        }).addClass("hidden");

    });
  });

  window.onload = function(e) {
    $.ajax(window.location.href);
  };
</script>
<?php $str['script_manual'] .= ob_get_clean();
