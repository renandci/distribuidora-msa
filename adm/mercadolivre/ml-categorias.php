<?php
include '../topo.php';	
AcessoML($_SESSION, $PgAt);
?>
<style>
    body{ 
        background-color: #f1f1f1;
    }
    .select2-container .select2-selection--single, 
    .select2-container--default .select2-selection--multiple{
        height: 34px;
    }
    .select_no_init, 
    .select2-container {
        margin-right: 15px !important;
    }

    .fieldset {
        border-radius: 3px;
        -moz-border-radius: 3px;
        -webkit-border-radius: 3px;
        border-color: #dedede;
        border-width: 1px;
        border-style: solid;
        padding: 15px;
        background-color: #fff;
    }
</style>

<form action="/adm/mercadolivre/ml-editar.php?codigo_id=<?php echo $GET['codigo_id']?>" method="post" id="ajax">
    <input type="hidden" name="codigo_id" value="<?php echo $GET['codigo_id']?>">
    <div class="col-md-8 col-md-offset-2 mb15 fieldset">
        <h2 class="neo-sans-medium pull-left" style="margin-top: 0; width: 100%;">Categoria do Produto</h2>
        <div class="pull-left mb15" style="width: 100%;" id="recarregar-categorias">
            <?php
            $Produto = Produtos::first(['conditions' => ['codigo_id=?', (int)$GET['codigo_id']]]);

            $LETRA = null;
            $PreCategoria = $meli->get('sites/MLB/category_predictor/predict', array('title' => urlencode($Produto->nome_produto)));
            if( count($PreCategoria['body']) > 0 && (empty($GET['categoria_id']) || $GET['categoria_id'] == '') ) 
            {    
                ?>
                <h4 class="neo-sans-medium pull-left" style="margin-top: 0; width: 100%;"><?php echo $Produto->nome_produto?></h4>
                <?php
                $categoria_id = null;
                foreach ($PreCategoria as $k => $v ) 
                {
                    if( ! empty( $v->path_from_root ) ) { ?>
                        <!--[ select count_<?php echo $k?> ]-->
                        <select data-name="categoria-id" size="15" class="select_no_init mb15">
                        <?php
                        $categorias = current( $meli->get( 'sites/MLB/categories' ) );
                        echo count($categorias) > 1 ? '<option value="">Tipos de Categorias</option>':'';
                        foreach ($categorias as $value)
                        {
                            if( $LETRA != StringLetra($value->name) ) { ?>
                            <optgroup label="<?php echo StringLetra($value->name)?>">
                            <?php $LETRA = StringLetra($value->name); } ?>
                                <option value="<?php echo $value->id?>" <?php echo checked_html($value->id, $v->path_from_root[0]->id, 'selected')?>>
                                    <?php print($value->name)?>
                                </option>
                            <?php if( $LETRA != StringLetra($value->name) ) { ?>
                            </optgroup>
                            <?php }
                        } 
                        ?>
                        </select>
                        <!--[ end select count_<?php echo $k?> ]-->
                        <?php foreach ( $v->path_from_root as $k1 => $v1 ) { ?>
                            <?php
                            $categorias = current( $meli->get( 'categories/' . $v1->id ) );
                            if( count($categorias->children_categories) > 0 ) { ?>
                                <!--[ select count_<?php echo $k1?> <?php echo $v1->id ?> <?php echo $v->path_from_root[($k1+1)]->id?>]-->
                                <select data-name="categoria-id" size="15" class="select_no_init mb15 count_<?php echo $k1?>">
                                <?php
                                echo count($categorias->children_categories) > 0 ? '<option value="">Tipos de Categorias</option>':'';
                                foreach ($categorias->children_categories as $value) {
                                    if( $LETRA != StringLetra($value->name) ) { ?> 
                                    <optgroup label="<?php echo StringLetra($value->name)?>"> 
                                    <?php $LETRA = StringLetra($value->name); } ?>
                                        <?php 
                                        if(!empty(checked_html($value->id, $v->path_from_root[($k1+1)]->id, 'selected'))) $categoria_id = $value->id;
                                        ?>
                                        <option value="<?php echo $value->id?>" <?php echo checked_html($value->id, $v->path_from_root[($k1+1)]->id, 'selected')?>>
                                            <?php print($value->name)?>
                                        </option>
                                    <?php if( $LETRA != StringLetra($value->name) ) { ?>
                                    </optgroup>
                                    <?php }
                                } ?>
                                </select>
                                <!--[ end select count_<?php echo $k1?> ]-->
                            <?php } else { ?>
                            <button type="submit" class="btn btn-primary" formaction="/adm/mercadolivre/ml-editar.php?codigo_id=<?php echo $GET['codigo_id']?>&categoria_id=<?php echo $categoria_id?>">continuar</button>
                            <?php
                            }
                        }
                    }
                }
            }
            else {
                $LETRA = '';
                if(isset($GET['categoria_id']) && $GET['categoria_id'] != '') { ?>
                    <select name="categoria_id" data-name="categoria-id" size="15" class="select_no_init mb15">
                    <?php
                    $categorias = current( $meli->get( 'categories' . (isset($GET['categoria_id']) && $GET['categoria_id'] != '' ? "/{$GET['categoria_id']}" : '') ) );
                    echo count($categorias) > 1 ? '<option value="">Tipos de Categorias</option>':'';
                    foreach ($categorias->children_categories as $value) {
                        if( $LETRA != StringLetra($value->name) ) { ?> 
                        <optgroup label="<?php echo StringLetra($value->name)?>"> 
                        <?php $LETRA = StringLetra($value->name); } ?>
                            <option value="<?php echo $value->id?>">
                                <?php print($value->name)?>
                            </option>
                        <?php if( $LETRA != StringLetra($value->name) ) { ?>
                        </optgroup>
                        <?php }
                    } ?>
                    </select>
                    <?php
                }
                else { ?>
                    <select name="categoria_id" data-name="categoria-id" size="15" class="select_no_init mb15">
                    <?php
                    $categorias = current( $meli->get('sites/MLB/categories' ) );
                    echo count($categorias) > 1 ? '<option value="">Tipos de Categorias</option>':'';
                    foreach ($categorias as $value) {
                        if( $LETRA != StringLetra($value->name) ) { ?> 
                        <optgroup label="<?php echo StringLetra($value->name)?>"> 
                        <?php $LETRA = StringLetra($value->name); } ?>
                            <option value="<?php echo $value->id?>">
                                <?php print($value->name)?>
                            </option>
                        <?php if( $LETRA != StringLetra($value->name) ) { ?>
                        </optgroup>
                        <?php }
                    } ?>
                    </select>
                    <?php
                }
            }
            ?>
        </div>
    </div>
</form>

<?php ob_start(); ?>
<script>
    // $(function(){
        // /**
        //  * Remove um tamanho selecionado
        //  */
        // $("#conteudos-recarregar").on("click", "a[btn=remove-tamanho]", function(e){
        //     e.preventDefault();
        //     $(e.target).parent().remove();
        // });

        // ClickEscolher = (function( ethis ) {
        //     var select = $(ethis).children(":selected");
        //     select.parent().prev().val(select.data("value"));
        //     select.parent().prev().prev().val(select.data("name"));
        //     select.parent().prev().prev().prev().val(select.data("id"));
		// });

        // /**
        //  * Adiciona um novo select com tamanhos
        //  */
		// var ClickEscolherCor = function( id_cor, name, value ){
		// 	$("#hNames_"+id_cor).val(name);
		// 	$("#hCores_"+id_cor).val(value);
		// }
		
		var Validar = function(){
			var erros = 0;
			$("form#ajax input").each(function(){			
				if( $(this).val() == "" ){
					var this_name = $(this).attr('name'),
                        this_value = $(this).val();
					
					// if( this_name != 'produtos[id]' && this_name.substr(-9) != '][SIZE][]' ){
					// 	erros++;
					// 	console.log( $(this).attr('name') );
					// }

					if( this_name != 'produtos[id]' && this_value === '') {
						console.log( $(this).attr('name') );
						erros++;
					}
				}
			});
			
			if(erros > 0 ){
				alert("Favor preencher todos os campos!");
				return false;
			}else{
				return true;
			}
		}
		 
        $("#conteudos-recarregar").on("click", "a[btn=add-tamanho]", function(e){
            e.preventDefault();
            var $this = $(e.target),
                $parent = $this.parent().parent(),
                $next = $parent.find(":first").next().next(),
                $data = $("form#ajax").serialize(),
                $IdPrincipal = $next.attr("id");
            
            $.ajax({
                url: window.location.href,
                type: "post",
                data: $data,
				beforeSend: function(  ) {
					$("#status-alteracao").html("Carregando...");
					$("#status-alteracao").fadeIn(220);
				},
                success:function(str){
					$("#status-alteracao").html("Processo concluído com sucesso!");
                    var list = $("<div/>", { html: str });
                    $next.after([
                        $("<div>",{
                            "id": "__0",
                            "class": "row mb15 fieldset",
                            "html": [
                                list.find("#"+$IdPrincipal+"").html()
                            ]
                        })
                    ]).delay(100).queue(function(a){
                        console.log($("#"+$IdPrincipal+"").find(".fa-trash"));
                        $("#"+$IdPrincipal+"").find(".fa-trash").removeClass("hidden");
                        $("#"+$IdPrincipal+"").find("select[name]").children("option").attr("selected", false);
                        $("#"+$IdPrincipal+"").find("input[name]").val("");
                        a();
                    });
                }
            });
        });
		
        $("#recarregar-categorias").on("change", "select[data-name]", function(e){
            var $element = e,
                $this = $(this),
                $DataName = $this.attr("data-name"),
                $DataText = $this.find("option:selected").text(),
                $DataHref = $this.attr("data-href");
                
            $("tr[me-use=true]").find("input[data-name="+ $DataName +"]").next().remove();
            if( ! $this.val() )
            {
                $("tr[me-use=true]").find("input[data-name="+ $DataName +"]").val( "" );
    //                return false;
            }

            $("tr[me-use=true]").find("input[data-name="+ $DataName +"]").val( $this.val() ).after([
                $("<span/>",{ html: $DataText.trim() })
            ]);
            
            switch( $DataName )
            {
                /**
                 * Seleciona o tipo da categorização
                 */
                case "categoria-id" :
                    $.ajax({
                        url: $DataHref||window.location.href,
                        type: "get",
                        data: { categoria_id: $this.val() },
                        beforeSend: function () {
                            $this.prop("disabled", true);
                            $($element.target).nextAll().remove();
                        },
                        complete: function(){
                            $this.prop("disabled", false);
                        },
                        success: function(str){
                            var list = $("<span/>", { html: str }), 
                                NewSelect = list.find("#recarregar-categorias").find("select").html(),
                                NewCount = list.find("#recarregar-categorias").find("select:last").find("option").length,
                                NewVal = $("#recarregar-categorias").find("select:last").find("option:selected").val()||0;
                                setTimeout(() => {
                                    NewVal = $("#recarregar-categorias").find("select:last").find("option:selected").val()||"";
                                }, 110); 

                            if( NewCount > 0 ) {
                                $("#recarregar-categorias").find("select:last").after([
                                    $("<select />", {
                                        "class": "select_no_init",
                                        "data-name": "categoria-id",
                                        "name": "categoria_id",
                                        "css":{
                                            width: "335px"
                                        },
                                        "attr": {
                                            "size": "15"
                                        },
                                        "html": [
                                           NewSelect
                                        ] 
                                    })
                                ]);
                            } 
                            else {
                                $("#recarregar-categorias").find("select:last").delay(120).after([
                                    $("<button/>",{
                                        "class": "btn btn-primary ml15",
                                        "type": "submit",
                                        "html": "continuar",
                                        "attr": {
                                            "formaction": "/adm/mercadolivre/ml-editar.php?codigo_id=<?php echo $GET['codigo_id']?>&categoria_id=" + NewVal
                                        }
                                    })
                                ]);
                            }
                        }
                    });
                break;
                case "listing-type-id" :
                    $($element.target).nextAll().remove();
                    $($element.target).after([
                        $("<span/>", {
                            class: "mt10 mb5 show ft16px",
                            html: $this.children("option:selected").attr("data-exposicao")
                        }),
                        $("<span/>", {
                            class: "show ft16px",
                            html: [
                                "Tarifa por venda: ",
                                $("<font/>", { color: "#a20000", class: "ft20px", html: $this.children("option:selected").attr("data-price") })
                            ]
                        })
                    ]);
                break;
            }
        });
        $("#conteudos-recarregar").on("blur", "input[data-price]", function(){
            var $DataSerialize = $("#ajax").serialize();
            $.ajax({
                url: window.location.href,
                type: "post",
                data: $DataSerialize,
                beforeSend: function () {
                },
                complete: function(){
                    $("select[data-name=listing-type-id]").nextAll().remove();
                },
                success: function(str){
                    var list = $("<span/>", { html: str });
                    $("select[data-name=listing-type-id]").html(list.find("select[data-name=listing-type-id]").html());
                }
            });
        });
    // });
</script>
<?php
$SCRIPT['script_manual'] .= ob_get_clean();
// 

include '../rodape.php';