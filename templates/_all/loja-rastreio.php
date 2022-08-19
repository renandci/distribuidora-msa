<?php
$STORE['TITULO_PAGINA'] = 'Rastreio | ' . $STORE['TITULO_PAGINA'];
$STORE['image'] = URL_IMAGENS . 'assets/' . ASSETS . '/imgs/logo.gif';
/**
 * Verificar se é um dispositivo móvel que está sendo acessado
 */
$modulo = 'identificacao';
// include PATH_ROOT . '/adm/correios/correios-bootstrap.php';
if ($MobileDetect->isMobile() || $MobileDetect->isTablet()) {
  include dirname(__DIR__) . '/_layout/layout-header.php';
  include dirname(__DIR__) . '/_layout/layout-header-mobile-topo.php';
} else {
  include dirname(__DIR__) . '/_layout/layout-header.php';
  include sprintf('%stopo.php', URL_VIEWS_BASE);
}
?>
<style>
  .form-rastro {
    max-width: 875px;
    background-color: #f1f1f1;
    padding: 25px;
    -webkit-border-radius: 7px;
    -moz-border-radius: 7px;
    border-radius: 7px;
    margin-bottom: 50px;
  }
</style>

<h2 class="mt25 mb50 text-center">Rastreie seu Pedido</h2>
<form action="/loja/rastrear-pedido" method="post" class="center-block form-rastro" id="form_rastro">
  <div class="row">
    <div class="col-md-4 col-xs-12" style="background-color: #dedede; color: #777">
      <strong class="ft18px show mb15 mt15 text-center">Localize seu pedido <i class="fa fa-gift"></i></strong>
      <small class="show mb15">Digite o número do seu pedido para rastrear sua encomenda.</small>
    </div>
    <div class="col-md-8 col-xs-12">
      <div class="clearfix mt5" id="codigo_pedido">
        <label class="ft12px">Digite o Número do Pedido ou seu email *</label>
        <input type="tel" class="form-control input-lg" name="codigo_pedido" value="" placeholder="" required="required" />
      </div>

      <div class="clearfix text-center mt5">
        <button type="submit" class="btn btn-secundary btn-lg">
          <i class="fa fa-location-arrow"></i>
          Rastrear pedido
        </button>
      </div>
    </div>
    <div class="col-xs-12" id="result_rastro">
      <?php
      $telefone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
      $codigo_pedido = filter_input(INPUT_POST, 'codigo_pedido', FILTER_SANITIZE_STRING);
      // $codigo_pedido = "0000000062550";
      // $codigo_pedido = "renan@dcisuporte.com.br";
      // $telefone = '(16) 3262-1365';
      if (!empty($codigo_pedido)) {
        try {
          // if( filter_var($codigo_pedido, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '/[^0-9A-Z_@]/']]) ) {
          //     throw new Exception('Número de Pedido inválido!');
          // }

          $sql = ''
            . 'select '
            . 'c.nome, '
            . 'p.status, '
            . 'p.codigo, '
            . 'p.rastreio, '
            . 'p.data_venda, '
            . 'jdet.id as jadlog_id, '
            . 'jdet.shipment_id as jadlog_cod, '
            . 'coret.id as correio_id, '
            . 'coret.etiqueta as correio_cod, '
            . 'coret.dv as correio_dv '
            . 'from pedidos p '
            . 'join clientes c on c.id = p.id_cliente '
            . 'left join jadlog_etiqueta jdet on jdet.id_pedido = p.id '
            . 'left join correios_etiquetas coret on coret.id_pedidos = p.id '
            . 'where p.loja_id = %u and (p.codigo like "%%%s" or (c.email like "%%%s%%") ';

          if (isset($telefone) && $telefone != '') {
            $sql .= 'and (c.telefone like "%%%s%%" or (c.celular like "%%%s%%"))) ';
            $sql .= 'order by p.id desc ';
            $sql .= 'limit 10';
            $stmtp = Lojas::connection()->query(sprintf($sql, $CONFIG['loja_id'], $codigo_pedido, $codigo_pedido, $telefone, $telefone));
          } else {
            $sql .= ') order by p.id desc ';
            $sql .= 'limit 10';
            $stmtp = Lojas::connection()->query(sprintf($sql, $CONFIG['loja_id'], $codigo_pedido, $codigo_pedido));
          }

          $fetchAll = $stmtp->fetchAll();
          $rwsCount = $stmtp->rowCount();

          if ($rwsCount == 0) {
            throw new Exception('Desculpe, mas não encotramos seu pedido!');
          }
          // Isso deve ajudar em pedidos com mesmo numero de codigo
          if (empty($telefone) && $rwsCount > 1) {
            throw new Exception('<span id="test">requer_fonecel</span>');
          }
          echo sprintf('<h3 class="neo-sans-medium text-center mt15 mb25">%s, %s</h3><hr/>', boas_vindas(), $fetchAll[0]['nome']);
      ?>
          <ul class="row">
            <li class="col-sm-4 col-xs-6 font-bold bold text-uppercase">Cód Pedido</li>
            <li class="col-sm-2 col-xs-6 font-bold bold text-uppercase text-center">Dt. Venda</li>
            <li class="col-sm-6 hidden-xs font-bold bold text-uppercase"></li>

            <?php foreach ($fetchAll as $rws) {

              $correio_cod = mask($rws['correio_cod'], "##########{$rws['correio_dv']}##");

              $cod_rastro = !empty($rws['rastreio']) ? $rws['rastreio'] : null;
              $cod_rastro = !empty($rws['jadlog_cod']) ? $rws['jadlog_cod'] : $cod_rastro;
              $cod_rastro = !empty($rws['correio_cod']) ? $correio_cod : $cod_rastro;
            ?>
              <li class="col-sm-12 col-xs-12">
                <hr class="mt10 mb5" />
              </li>
              <li class="col-sm-4 col-xs-6">
                <strong class="show"><?php echo $rws['codigo'] ?></strong>
                <?php echo !empty($cod_rastro) ? rastreio($cod_rastro, 'btn btn-success btn-xs', null, false) : null ?>
              </li>
              <li class="col-sm-2 col-xs-6 text-center">
                <?php echo date('d/m/Y H:i', strtotime($rws['data_venda'])) ?>
              </li>
              <li class="col-sm-6 hidden-xs">
                <?php echo text_status_vendas($rws['status']) ?>
                <img src="<?php echo Imgs::src("status-{$rws['status']}", 'status') ?>.png" alt="" class="pull-right" width="55px" height="55px">
              </li>
            <?php } ?>
          </ul>
          <?php
          // echo sprintf('<h3 class="neo-sans-medium text-center mt15 mb25">%s, %s</h3><hr/>', boas_vindas(), $rws['nome']);

          // $correio_cod = mask($rws['correio_cod'], "##########{$rws['correio_dv']}##");
          // $cod_rastro = !empty($rws['jadlog_cod']) ? $rws['jadlog_cod'] : null;
          // $cod_rastro = !empty($rws['correio_cod']) ? $correio_cod : $cod_rastro;
          // printf('<div class="text-center">%s</div>', rastreio($cod_rastro));
          ?>
          <script>
            // $("a[href^='http://lojascorreios.']").queue(function(ex){
            //     $(this).append($("<i/>", {class: "fa fa-truck"})).removeAttr("style").css({"margin": "0 auto"}).addClass("btn btn-lg btn-warning").get(0).click();
            //     ex();
            // });
          </script>
          <?php

          $rws['jadlog_id'] = null;
          $rws['correio_id'] = null;

          if (!empty($rws['jadlog_id'])) {
            $rastro = (new JadLogNew($CONFIG['jadlog']['token']))->post('/tracking/consultar', ['consulta' => [['shipmentId' => $rws['jadlog_cod']]]]);
            // printf('<pre>JADLOG: %s</pre>', print_r($rastro, true));

            if (!empty($rastro['httpCode']) && $rastro['httpCode'] != 200) {
              throw new Exception(sprintf('Desculpe, serviço temporariamente indisponível, tente daqui alguns minutos.'));
            }

            printf('<p>Seu código de rastreio é: <strong>%s</strong>, seu pedido está <strong>%s</strong></p>', $rws['jadlog_cod'], $rastro['body']->consulta[0]->tracking->status);

            // printf('<pre>%s</pre>', print_r($rastro['body']->consulta[0]->tracking->status, 1));
            if (count($rastro['body']->consulta) > 0) foreach ($rastro['body']->consulta as $std) {

              if (count($rastro['body']->consulta) == 0) {
                throw new Exception(sprintf('Desculpe, serviço temporariamente indisponível, tente daqui alguns minutos.'));
              }

              foreach ($std->tracking->eventos as $even) { ?>
                <div class="row">
                  <div class="col-xs-12 ft20px neo-sans-medium">
                    <hr />
                    <?php echo $even->status ?>
                  </div>
                  <div class="col-md-9 col-xs-12 neo-sans-medium">
                    Local: <?php echo $even->unidade ?>
                  </div>
                </div>
              <?php }
            }
          }

          if (!empty($rws['correio_id'])) {
            $correio_cod = mask($rws['correio_cod'], "##########{$rws['correio_dv']}##");

            $Etiqueta = new \PhpSigep\Model\Etiqueta();
            $Etiqueta->setEtiquetaComDv($correio_cod);
            $etiquetas[] = $Etiqueta;

            $RastrearObjeto = new \PhpSigep\Model\RastrearObjeto();
            $PhpSigep = new PhpSigep\Services\SoapClient\Real();
            $AccessData = new \PhpSigep\Model\AccessData();
            $AccessData->setUsuario('2317761600');
            $AccessData->setSenha('E40W;3@8?L');

            $RastrearObjeto->setAccessData($AccessData);
            $RastrearObjeto->setEtiquetas($etiquetas);

            $ResultPhpSigep = $PhpSigep->rastrearObjeto($RastrearObjeto);
            $boolean = $ResultPhpSigep->getErrorCode();

            if (!empty($boolean)) {
              throw new Exception(sprintf('Desculpe, o serviço dos correios está temporariamente indisponível, tente daqui alguns minutos.'));
            }
            $ResultEventos = $ResultPhpSigep->getResult()[0];

            foreach ($ResultEventos->getEventos() ?? [] as $even) { ?>
              <div class="row">
                <div class="col-xs-3 neo-sans-medium">
                  <?php echo date('d/m/Y', strtotime($even->getDataHora())) ?><br />
                  <?php echo date('h:i', strtotime($even->getDataHora())) ?>
                  <hr class="mt0 mb0" />
                  Local: <?php echo $even->getCidade() ?>/<?php echo $even->getUf() ?>
                </div>
                <div class="col-md-6 ft20px col-xs-12 neo-sans-medium">
                  <?php echo $even->getDescricao() ?>
                </div>
                <div class="col-md-12 col-xs-12 neo-sans-medium">
                  <hr>
                </div>
              </div>
      <?php }
          }
        } catch (Exception $e) {
          printf('<hr/><p class="bold text-uppercase text-center text-danger">%s</p>', $e->getMessage());
        }
      }
      ?>
      <small class="mt15 show text-right">Caso não se lembre do número do seu pedido, <a href="<?php echo (isset($_SESSION['cliente']['id_cliente']) && $_SESSION['cliente']['id_cliente'] != '') ? '/identificacao/meus-pedidos' : '/identificacao/login/meus-dados' ?>">CLIQUE AQUI</a>.</small>
    </div>
  </div>
</form>
<?php ob_start() ?>
<script>
  var SPMaskBehavior = function(val) {
      return val.replace(/\D/g, "").length === 11 ? "(00) 00000-0000" : "(00) 0000-00009";
    },
    spOptions = {
      onKeyPress: function(val, e, field, options) {
        field.mask(SPMaskBehavior.apply({}, arguments), options);
      }
    };

  $("input[name=telefone]").mask(SPMaskBehavior, spOptions);

  form_rastro = $("<div/>", {
    class: "clearfix mt5",
    html: [
      $("<label/>", {
        class: "ft12px",
        html: "Confirme o número do seu telefone ou celular *"
      }),
      $("<input/>", {
        type: "tel",
        class: "form-control",
        name: "telefone",
        css: {
          "max-width": "275px"
        }
      }),
    ]
  })
  my_click = 0;
  $("#form_rastro").on("click", "button[type='submit']", function(e) {
    e.preventDefault();

    var codigo = $("input[name='codigo_pedido']").val(),
      telefone = $("input[name='telefone']").val();

    if (codigo === "") {
      alert('Digite o código do seu pedido!');
      return;
    }
    if (telefone !== undefined && telefone === "") {
      alert('Digite o número do seu telefone!');
      return;
    }

    my_click++;

    if (my_click > 1)
      return false;

    $.ajax({
      url: "/rastreio",
      type: "post",
      data: {
        codigo_pedido: codigo,
        phone: telefone
      },
      beforeSend: function() {
        $("#aminacao-site").fadeIn();
      },
      complete: function() {
        $("#aminacao-site").fadeOut();
        my_click = 0;
      },
      success: function(str) {
        var list = $("<div/>", {
            html: str
          }),
          test = list.find("#test").html() || 0;

        if (test.length > 0) {
          $("#codigo_pedido").after(form_rastro);
          return;
        }

        $("#result_rastro").html(list.find("#result_rastro").html());
      }
    })
  });
</script>
<?php
$str['script_manual'] .= ob_get_clean();
include sprintf('%srodape.php', URL_VIEWS_BASE);
