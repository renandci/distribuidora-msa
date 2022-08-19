<div class="col-md-9 col-sm-8 col-xs-12">
  <ul class="row" style='border-bottom: 1px solid #eee;'>
    <li class="col-md-12 col-sm-12 col-xs-12">
      <h2>
        Meus Endereços
        <a href="/identificacao/identificacao-endereco-cadastrar_editar/?_u=<?php echo URL_BASE; ?>/identificacao/meus-enderecos" class="btn btn-secundary pull-right btn-xs novo-endereco">novo endereço</a>
      </h2>
      <hr />
    </li>
    <?php
    $x = 0;
    foreach ($CONFIG['cliente_session']['enderecos'] as $end) {
      $CEP = soNumero($end['cep']);
      $x++;
    ?>
      <li class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
        <div class="<?php echo $end['status'] ? 'div-hover-endereco-ativo' : 'div-hover-endereco' ?>">
          <span class="show ml15 mr15 mt15 mb25 clearfix">
            <h3>
              <?php echo $end['nome']; ?>
              <?php echo $end['status'] ? '<small class="pull-right badge" title="Endereço ativo para compras online!">!</small>' : '' ?>
            </h3>
            <span class="show mb5"><?php echo "Receber: {$end['receber']}"; ?></span>
            <span class="show mb5"><?php echo "Endereço: {$end['endereco']}, {$end['numero']}"; ?></span>
            <span class="show mb5"><?php echo "Bairro: {$end['bairro']}"; ?></span>
            <span class="show mb5"><?php echo $end['complemento'] == '' ? '' : "Complemento: {$end['complemento']}"; ?></span>
            <span class="show mb5"><?php echo $end['referencia'] == '' ? '' : "Referêcia: {$end['referencia']}"; ?></span>
            <span class="show mb5"><?php echo "Cidade/UF: {$end['cidade']}-{$end['uf']}"; ?></span>
            <span class="show mb5"><?php echo "CEP: {$end['cep']}"; ?></span>

            <a href="/identificacao/identificacao-endereco-cadastrar_editar/?id=<?php echo $end['id']; ?>&_u=<?php echo URL_BASE; ?>/identificacao/meus-enderecos" class="novo-endereco btn btn-xs btn-primary pull-right">
              Editar
            </a>
          </span>
        </div>
      </li>
      <?php if (($x % 2) == 0) { ?>
        <li class="col-lg-12 col-md-12 col-sm-12 hidden-xs">
          <hr />
        </li>
      <?php } ?>
    <?php } ?>
  </ul>
</div>
<?php ob_start(); ?>
<script>
  //		$(function(){
  $(".novo-endereco").click(function(e) {
    e.preventDefault();
    var ModalSite = $("#modal-site"),
      Href = e.target.href || this.href;
    $.ajax({
      url: Href,
      //                type: "post",
      //                data: { acao : 'alteracaoEnderecos' },
      dataType: "html",
      beforeSend: function() {
        $("#aminacao-site").fadeIn(0);
        ModalSite.modal("show");
      },
      complete: function() {
        $("#aminacao-site").fadeOut(0);
      },
      success: function(str) {
        var list = $("<div/>", {
          html: str
        });
        //                    $("body").append( str );
        ModalSite.find(".modal-header").fadeOut(0);
        ModalSite.find(".modal-body").html([
          $("<button/>", {
            type: "button",
            class: "close",
            attr: {
              "data-dismiss": "modal",
              "aria-label": "Close"
            },
            html: [
              $("<span/>", {
                html: [
                  "&times;"
                ],
                attr: {
                  "aria-hidden": "true"
                }
              })
            ]
          }),
          list.find("#modal-content").html()
        ]);
      },
      error: function(X, T, M) {
        if (T === 'timeout') {
          alert('Opss algo falhou tente novamente');
        } else {
          alert(T);
        }
      }
    });
  });
  //		});
</script>
<?php
$str['script_manual'] .= ob_get_clean();

?>
