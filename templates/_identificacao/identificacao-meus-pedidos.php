<div class="col-md-9 col-sm-8 col-xs-12" id="lista-pedidos">
  <h2 class="text-center">Meus Pedidos</h2>
  <div class="add-active-menu">
    <hr class="mb0">
    <table width="100%" class="table">
      <tr>
        <th><b>Número do pedido</b></th>
        <th align="center" class='hidden-xs'><b>Data Compra</b></th>
        <th align="center" class='hidden-xs'><b>Vl. Total</b></th>
        <th></th>
      </tr>

      <?php
      $pag = Url::getURL(3);
      $quantidade = 10;
      $atual = isset($pag) ? (int)$pag : 1;
      $pega_arquivo = array_chunk($CONFIG['cliente_session']['pedidos'], $quantidade);
      $contar = count($CONFIG['cliente_session']['pedidos']);
      $Pedidos = $pega_arquivo[$atual - 1];

      $ultima_pagina = ceil($contar / $quantidade);

      foreach ($Pedidos as $rp) {
        $TOTAL = valor_pagamento($rp['valor_compra'], $rp['frete_valor'], $rp['desconto_cupom'], '$', $rp['desconto_boleto']);
        $rp['pedido_id'] = $rp['id'];
      ?>
        <tr class="lista-zebrada">
          <td>
            <?php echo $rp['codigo']; ?>
            <?php echo ($rp['rastreio'] && $rp['status'] == 8) ? rastreio($rp['rastreio']) : ''; ?>
            <?php echo mail_buttons($rp, "ml5 btn btn-comprar' style='width: auto;") ?>
          </td>
          <td width="1%" nowrap="nowrap" class='hidden-xs'><?php echo date('d/m/Y H:i', strtotime($rp['data_venda'])); ?></td>
          <td width="1%" nowrap="nowrap" class='hidden-xs color-001 ft18px'>R$: <?php echo number_format($TOTAL['TOTAL_COMPRA_C_BOLETO'], 2, ',', '.'); ?></td>
          <td width="1%" nowrap="nowrap">
            <a href="/identificacao/identificacao-meus-pedidos-detalhes/?acao=verdetalhes&pedido=<?php echo $rp['id']; ?>" class="btn btn-primary btn-xs ver-detalhe">
              DETALHES
            </a>
          </td>
        </tr>
      <?php } ?>
    </table>
  </div>
  <div class="paginacao-site">
    <?php
    foreach (HelperHtml::range_limit($atual, $ultima_pagina, 5) as $i) { ?>
      <?php if ($i == $atual) { ?>
        <span class="semcor model-radius"><?php echo $i ?></span>
      <?php } else if ($i > 0) { ?>
        <a href="/identificacao/meus-pedidos/pag/<?php echo $i ?>" data-paginacao="paginacao"><?php echo $i ?></a>
      <?php } ?>
    <?php } ?>
  </div>
  <style>
    .lista-zebrada:nth-child(2n+2) {
      background-color: #fff;
    }
  </style>
  <script>
    <?php ob_start(); ?>
    $("#lista-pedidos").on("click", "[data-paginacao=paginacao]", function(e) {
      e.preventDefault();
      var AnimaSite = $("#aminacao-site");
      $.ajax({
        url: this.href || e.target.href,
        cache: false,
        beforeSend: function() {
          AnimaSite.fadeIn(0);
        },
        complete: function() {
          AnimaSite.fadeOut(0);
        },
        success: function(str) {
          var list = $("<div/>", {
            html: str
          });
          $("#lista-pedidos").html(list.find("#lista-pedidos").html());
        }
      });
    });

    $("#lista-pedidos").on("click", "a.ver-detalhe", function(e) {
      e.preventDefault();
      var AnimaSite = $("#aminacao-site"),
        ModalSite = $("#modal-site");

      $.ajax({
        url: this.href || e.target.href,
        cache: false,
        beforeSend: function() {
          AnimaSite.fadeIn(0);
        },
        complete: function() {
          AnimaSite.fadeOut(0);
          ModalSite.modal("show").find(".modal-dialog").addClass("modal-lg");
        },
        success: function(str) {
          var list = $("<div/>", {
            html: str
          });
          ModalSite.find(".modal-header").find("h4").html(['Detalhes do Ped.: ', list.find("#numPedido").html()]).addClass("color-001");
          ModalSite.find(".modal-body").html([
            list.find("#verpedido").html()
          ]);
        }
      });
    });
    <?php
    $str['script_manual'] .= ob_get_clean();

    ?>
  </script>
</div>
