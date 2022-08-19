		</div>
		<span class="info" id="info"></span>
		<div class="cobre-total" id="cobre-total"></div>
		<div class="janela-opcoes" id="janela-opcoes"></div>
		<div id="janela-cadastros"></div>
		
        <?php echo isset($SCRIPT['bibliotecas']) && $SCRIPT['bibliotecas'] != '' ? $SCRIPT['bibliotecas'] : '';?>
        <script>
            <?php ob_start(); ?>
            var JanelaModal = $("#janela-cadastros");
            var options = {
                dateFormat: "dd/mm/yy",
                dayNames: ["Domingo","Segunda","Terça","Quarta","Quinta","Sexta","Sábado","Domingo"],
                dayNamesMin: ["D","S","T","Q","Q","S","S","D"],
                dayNamesShort: ["Dom","Seg","Ter","Qua","Qui","Sex","Sáb","Dom"],
                monthNames: ["Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro"],
                monthNamesShort: ["Jan","Fev","Mar","Abr","Mai","Jun","Jul","Ago","Set","Out","Nov","Dez"]
            };
			
			$.widget("ui.dialog", $.ui.dialog, {
				_allowInteraction: function (event) {
					if ( $(event.target).closest(".colorpicker, .select2-dropdown, .mce-widget").length)
						return true ;                    
					return this._super(event);
				}
			});
			
            format_state = function ( state ) {

                var $hex1 = $(state.element).attr("hex1"),
                    $hex2 = $(state.element).attr("hex2"),
                    $image = state['image'];

                if( $image )
                    return $("<div/>", {
                        class: "clearfix",
                        html:[
                            $("<div/>", {
                                css: {
                                    "-webkit-border-radius": "100%",
                                    "-moz-border-radius": "100%",
                                    "border-radius": "100%",
                                    "width": "45px",
                                    "overflow": "hidden"
                                },
                                class: "pull-left",
                                html: $("<img/>", { class: "img-responsive", src: $image})
                                
                            }),
                            $("<div/>", {
                                class: "show pull-left ml5",
                                html: state.text
                            })
                        ]
                    });

                return ($hex2 || $hex1) ? $("<div/>", {
                    class: "clearfix",
                    html:[
                        $("<div/>", {
                            css: {
                                "-webkit-border-radius": "100%",
                                "-moz-border-radius": "100%",
                                "border-radius": "100%",
                            },
                            class: "cx-cor pull-left",
                            html:[
                                $("<div/>", { 
                                    class: "cx-cor-001", 
                                    css: { "background-color": "#" + $hex1 },
                                    html: [ $("<div/>", { class: "cx-cor-002", css: {"border-bottom-color": "#" + $hex2 } }) ]
                                })
                            ]
                        }),
                        $("<div/>", {
                            class: "show pull-left ml5",
                            html: state.text
                        })
                    ]
                }) : state['text'];
            };
			
            $(document).on("click", ".datepicker", function(){
                var $this = $(this);
                if (!$this.hasClass("hasDatepicker")) $this.datepicker(options);
                $this.datepicker("show");
            });
			
			$(document).ready(function(){
				
				$("[acessar=0]").css({"display":"none"});
				$("[incluir=0]").css({"display":"none"});
				$("[alterar=0]").css({"display":"none"});
				$("[excluir=0]").css({"display":"none"});
				
                /**
                 * Percorrer e verificar a class para não atribuir o plugin
                 */
                $.each( $("select"), function(i, e){
                   if( ! $(e).hasClass("select_no_init") )
                       $(e).select2({ templateResult: format_state, tags: true });
                });

				$(".tooltip").tooltip({
					position: {
						my: "left top",
						at: "right+5 top-25",
						collision: "none"
					}
				});
                
                $.ajax = (($oldAjax) => {
                    // on fail, retry by creating a new Ajax deferred
                    function check( a, b, c ) {
                    var shouldRetry = b != 'success' && b != 'parsererror';
                    if( shouldRetry && --this.retries > 0 )
                        setTimeout(() => { $.ajax(this) }, this.retryInterval || 15000);
                    }
                    return settings => $oldAjax(settings).always(check)
                })($.ajax);
                
                $(document)
                .ajaxSend(function(evt, request, settings) {
                    console.log("Starting request at " + settings.url + ".");
                })
                .ajaxStart(function(e) {
					$(e.target).find("button[type],a[href]").attr({"disabled":true}).css({opacity:0.8}).bind('click', false);
				}).ajaxStop(function(e) {
                    $(e.target).find("button[type],a[href]").attr({"disabled":false}).css({opacity:1}).unbind('click', false);

					/**
                     * Percorrer e verificar a class para não atribuir o plugin
                     */
                    $.each( $("select"), function(i, e){
                        if( ! $(e).hasClass("select_no_init") )
                            $(e).select2({ templateResult: format_state, tags: true });
                    });
                    
					$("[acessar='0']").css({"display":"none"});
					$("[incluir='0']").css({"display":"none"});
					$("[alterar='0']").css({"display":"none"});
					$("[excluir='0']").css({"display":"none"});
					
					$(".tooltip").tooltip({
						position: {
							my: "left top",
							at: "right+5 top-25",
							collision: "none"
						}
                    });
                    
					$("#status-alteracao").html("Pronto! Salvamos suas alterações.").fadeIn(10).delay(3000).queue(function(ex){
										$(this).fadeOut(0);
						ex();
					});
				});
                
                // Menus geral
                $(".menus > ul > li").on("click", "a.menu-principal", function(e){
                    if(  $(".menus > ul > li").find(".menu-nivel-1").is(":visible") )
                        $(".menus > ul > li").removeClass("navs-menus-aticve");

                    $(this).parent().addClass("navs-menus-aticve");
                });
                
                // Para o menu topo sair|confirurações|lojas|planos
                $(".menus > ul > li.pull-right > .menu-principal").hover(function(){
					$(this).stop().next(".menu-nivel-1").fadeIn(100);
				}, function () {
                    $(this).stop().next(".menu-nivel-1").hover(function(){}, function() {
                        $(this).fadeOut(50);
                    });
                });
				
				JanelaModal.dialog({
					autoOpen: false,
					width: 960,
					height: 600,
					modal: true,
					dialogClass: "classe-ui"                    
				}).dialogExtend({
					"maximizable": true,
					"dblclick": "maximize",
					"icons": { "maximize": "ui-icon-arrow-4-diag" }
				}).css({
					"overflow-x": "hidden"
				});
				<?php echo isset($SCRIPT['script_manual']) ? str_replace(['<script>','</script>'], [null,null], $SCRIPT['script_manual']) : null;?>
			});
			<?php
            $JSqueeze = new Patchwork\JSqueeze();
			echo ($JSqueeze->squeeze(ob_get_clean(), true, false, false));
            ?>
		</script>
	</body>
	<div id="load"></div>
</html>
<?php
unset($_SESSION['error'], $PDO);
// Retorna o html mais puro possivel
echo(CompactarHtmlAdm(ob_get_clean()));
ob_end_clean();

session_write_close();
