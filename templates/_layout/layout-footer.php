        <div class="bg-overlay white aminacao-site" id="aminacao-site">
          <div class="loader"></div>
        </div>

        <div class="modal" tabindex="-1" role="dialog" id="modal-site">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="line-height: 25px;">x</span></button>
                <h4 class="modal-title">ATENÇÃO</h4>
              </div>
              <div class="modal-body">
                <p></p>
              </div>
            </div><!-- /.modal-content -->
          </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        <!--
		<div class="cobreSite" id="RegrasFrete">
			<div class="RegrasFrete">
				<div style="width: 35px; height: 35px; float: right; cursor: pointer;" onclick="$('.cobreSite').fadeOut(50);"></div>
			</div>
		</div>
		-->
        <div id="fb-root"></div>

        <?php echo (isset($BIBLIOTECAS) && $BIBLIOTECAS != '' ? $BIBLIOTECAS : '') ?>

        <?php if (!empty($STORE['config']['ebit']['id_ebit'])) : ?>
          <script src="https://imgs.ebit.com.br/ebitBR/selo-ebit/js/getSelo.js?<?php echo $STORE['config']['ebit']['id_ebit'] ?>" id="getSelo"></script>
        <?php endif; ?>

        <!--
		<script id="cart-template" type="text/template">
			{{#produtos}}
			<li class="clearfix mb5">
				<div class="row">
					<div class="col-xs-3"><img src="{{ imagem }}" alt="{{ nome_produto }}" class="img-responsive"></div>
					<div class="col-xs-9"><h5>{{ nome_produto }}</h5></div>
					<div class="col-xs-4">
						{{ preco_promo }}
					</div>
					<div class="col-xs-5">

					</div>
					<div class="col-xs-5">

					</div>
				</div>
			</li>
			{{/produtos}}
		</script>
		-->

        <script>
          <?php
          // Adiciona as regras de frete em imagem
          $str['script_manual'] .= HelperHtml::popup_frete();

          // inicia a captação do buffer
          ob_start();
          ?>



          // $("#carrinho").hover(function(a) {
          // 	$("body").append([
          // 		$("<ul/>", {
          // 			id: "ul-cart-prod",
          // 			css: {
          // 				"position": "fixed",
          // 				"top": "0",
          // 				"left": "0",
          // 				"margin": "0",
          // 				"z-index": "999",
          // 				"background-color": "#fff",
          // 				"padding": "7.5px 10px 7.5px 15px",
          // 				"width": "320px",
          // 				"height": "100%"
          // 			},
          // 			html: Mustache.render($("#cart-template").html(), <?php echo json_encode($c) ?>)
          // 		})
          // 	]);
          // }, function(b) {
          // 	// $("body").find("#ul-cart-prod").remove();
          // });

          <?php if (!empty($CONFIG['clearsale']['mapper'])) { ?>
              (function(a, b, c, d, e, f, g) {
                a['CsdmObject'] = e;
                a[e] = a[e] || function() {
                  (a[e].q = a[e].q || []).push(arguments)
                }, a[e].l = 1 * new Date();
                f = b.createElement(c),
                  g = b.getElementsByTagName(c)[0];
                f.async = 1;
                f.src = d;
                g.parentNode.insertBefore(f, g)
              })(window, document, 'script', '//device.clearsale.com.br/m/cs.js', 'csdm');
            csdm("app", "<?php echo $CONFIG['clearsale']['mapper'] ?>");
          <?php } ?>

          <?php /*if (!empty($CONFIG['insta_link'])) { ?>
$(window).on("load", function() {
  $.instagramFeed({
    "username": "<?php echo substr($CONFIG['insta_link'], 26) ?>",
    "container": "#insta_feed",
    "display_profile": false,
    "display_biography": false,
    "display_igtv": false,
    "items_per_row": 5,
    "items": 5,
    "margin": 0.5,
    "image_size": 320,
  });
});
<?php } */ ?>

          <?php if (isset($str['script_manual']) && $str['script_manual'] != '') { ?>
            $(document).ready(function() {

              <?php /* if ($modulo != 'identificacao') { ?>

      var ajaxDeImplentacao = new Promise((resolve, reject) => {
        $.ajax({
          url: window.location.href,
          type: "POST",
          data: {
            preLoadSubMenus: "<?php echo $CONFIG['preLoadSubMenus'] ?>"
          },
          complete: function() {},
          beforeSend: function() {},
          success: function(resp) {
            var html = $("<div/>", {
              html: resp
            });
            $(".menus-topo").html(html.find(".menus-topo").html());
            $(".menus-lateral").html(html.find(".menus-lateral").html());

            <?php if ($modulo == 'index') { ?>
            $(".banner-index").html(html.find(".banner-index").html()).owlCarousel({
              autoPlay: 7000,
              items: 1, // 7 items above 1000px browser width
              itemsDesktop: [1090, 1], // 5 items between 1000px and 901px
              itemsDesktopSmall: [880, 1], // 3 items betweem 880px and 601px
              itemsTablet: [400, 1], // 2 items between 600 and 0;
              itemsMobile: false, // itemsMobile disabled - inherit from itemsTablet option
              navigation: false,
              pagination: true,
              lazyLoad: true
            });
            <?php } ?>
            resolve("ajaxDeImplentacao realizado com sucesso.");
          },
          error: function() {
            reject("ajaxDeImplentacao rejected");
          }
        });
      });

      Promise.all([ajaxDeImplentacao]).then(values => {
        console.log("We waited until ajax ended: " + values);
        console.log("My few ajax ended, lets do some things!!")
      }, reason => {
        console.log("Promises failed: " + reason);
      });

      <?php } */ ?>
              <?php if ($modulo == 'index') { ?>
                $(".banner-index").owlCarousel({
                  autoPlay: 7000,
                  items: 1, // 7 items above 1000px browser width
                  itemsDesktop: [1090, 1], // 5 items between 1000px and 901px
                  itemsDesktopSmall: [880, 1], // 3 items betweem 880px and 601px
                  itemsTablet: [400, 1], // 2 items between 600 and 0;
                  itemsMobile: false, // itemsMobile disabled - inherit from itemsTablet option
                  navigation: false,
                  pagination: true,
                  lazyLoad: true
                });
              <?php } ?>
              $(".modal").on("hide.bs.modal", function() {
                $.removeData($("img"), "elevateZoom");
                $(".zoomContainer").remove();
              });

              <?php echo str_replace(['<script>', '</script>'], [null, null], $str['script_manual']) ?>
            });
          <?php }
          // $MinifyJS = new MatthiasMullie\Minify\JS();
          // $MinifyJS->add(ob_get_clean());
          // $MinifyJSContent = $MinifyJS->minify();
          // echo $MinifyJSContent;
          // Instancia e joga o javascript na tela
          $JSqueeze = new Patchwork\JSqueeze();
          $JSqueezeContent = $JSqueeze->squeeze(ob_get_clean(), true, false, false);
          echo $JSqueezeContent;
          ?>
        </script>
        </body>

        </html>
