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

<form method="post" id="ajax" class="clearfix">
    <?php
    if( isset($POST['produtos']))
        $produto = current($POST);

    $result_query = Produtos::find_by_sql(''
            . 'select '
            . 'produtos.*, '
            . 'cores.nomecor, '
            . 'tamanhos.nometamanho, '
            . 'marcas.marcas, '
            . 'COR.tipo AS cortipo, '
            . 'TAM.tipo AS tamanhotipo '
            . 'from produtos '
            . 'inner join marcas on marcas.id = produtos.id_marca '
            . 'inner join cores on cores.id = produtos.id_cor '
            . 'inner join tamanhos on tamanhos.id = produtos.id_tamanho '
            . 'inner join opcoes_tipo COR ON (COR.id = cores.opcoes_id) '
            . 'inner join opcoes_tipo TAM ON (TAM.id = tamanhos.opcoes_id) '
            . sprintf('where produtos.codigo_id=%u and produtos.excluir=0 and produtos.status=0 ', $GET['codigo_id'])
            . 'group by produtos.id_cor, produtos.id_tamanho');
            
    $rs = current($result_query)->to_array();
    $rs_count = count($rs);
    
    $result_query_tamanhos = Tamanhos::find_by_sql(''
            . 'select tamanhos.nometamanho as tamanho, produtos.estoque, produtos.preco_promo, produtos.id_cor, produtos.id_marca '
                . 'from tamanhos '
                    . 'inner join produtos on produtos.id_tamanho=tamanhos.id '
                        . 'where produtos.codigo_id = ? and produtos.excluir=0 and produtos.status=0', [$GET['codigo_id']]);
                        
    // $tamanhos = [];
    // foreach( $result_query_tamanhos as $rws_tam ) { 
    //     $tamanhos[$rws_tam->id_cor][] = [
    //         'nometamanho' => $rws_tam->tamanho, 
    //         'estoque' => $rws_tam->estoque, 
    //         'preco_promo' => $rws_tam->preco_promo, 
    //         'id_cor' => $rws_tam->id_cor
    //     ];
    // }
    
    if( $rs_count == 0 ) { ?>
        <div class="row text-center">
            <div class="col-md-8 col-md-offset-2 mb25">
                <h2 class="neo-sans-medium">Atenção</h2>
                <p>Esse produto encontra-se desativado ou excluido do site.</p>
                <p><a href="/adm/produtos.php" class="btn btn-info btn-xs">resolver</a> <a class="btn btn-danger btn-xs"href="/adm/mercadolivre/ml-produtos.php">voltar</a></p>
            </div>
        </div>
    <?php } else { ?>
        <div class="col-md-8 col-md-offset-2 mb25 fieldset">
            <h4 class="neo-sans-medium"><?php echo $rs['nome_produto'];?></h4>
            <p>Titulo Mercado Livre</p>
            <p>Selecione o tipo de anuncio e a categoria do seu produto e suas variações.</p>
            <?php echo ( (strlen($rs['nome_produto']) > 60) ? '<small class="show">A categoria selecionada não suporta títulos com mais de 60 caracteres.</small>':'')?> 
            <input type="hidden" name="produtos[title]" size="60" maxlength="60" class="count-input mb25" value="<?php echo substr($rs['nome_produto'], 0, 60)?>"/>
        </div>
        <?php
        if( isset($GET['codigo_id'], $GET['categoria_id'])) {

            // // $categories = $meli->get('categories/' . $GET['categoria_id'], $params); 
            // // $categories = $meli->get(sprintf('categories/%s/technical_specs/output', $GET['categoria_id']), $params); 
            // $categories = $meli->post(sprintf('categories/%s/attributes/conditional', $GET['categoria_id'])); 
            // $categories = $meli->get(sprintf('categories/%s/technical_specs/input', $GET['categoria_id']), $params); 
            // $categories = $meli->get(sprintf('categories/%s/attributes', $GET['categoria_id']), $params); 
            // echo '<pre class="ft10px col-lg-12">';
            // print_r($categories);
            // echo '</pre>';
            // return;

            $attributes = $meli->get('categories/' . $GET['categoria_id'] . '/attributes', $params); 

            // online
            usort($attributes['body'], function( $a, $b ) {  
                return $a->name < $b->name ? 0 : 1;
            });
            
            $count = 0;
            $preco_promo = 0;
            $test_html = null;
            foreach ( $result_query as $rw ) { 
                if( $preco_promo <= $rs['preco_promo'] )
                    $preco_promo = $rs['preco_promo'];
                ?>
                <!--[ DIVISAO ]-->
                <div class="col-md-8 col-md-offset-2 mb25 fieldset">
                    <h4 class="neo-sans-medium mb15 mt0 pull-left" style="margin-top: 0; width: 100%">
                        Edite as variaçoes do Produto
                        <small class="small"><?php echo $rw->nomecor?> <?php echo $rw->nometamanho?></small>
                    </h4>

                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12 mb15">
                            <label class="ft13px" style="width: 100%">
                                Imagens
                            </label>
                            <?php foreach($rw->fotos as $ft) { ?>
                                <input type="hidden" name="produtos[variations][<?php echo $count?>][picture_ids][]" value="<?php echo $ft->id?>"/>
                                <img style="width: 75px" src="<?php echo (file_exists(sprintf('../../%simgs/produtos/%s', URL_VIEWS_BASE_PUBLIC_UPLOAD, $ft->imagem)) ? Imgs::src($ft->imagem, 'smalls'):Imgs::src('sem-foto-produto.png', 'public'))?>"/>
                            <?php } ?>
                        </div>
                        <?php ob_start(); ?>
                        <div class="col-md-3 col-sm-3 col-xs-6 mb15 text-right">
                            <label for="<?php echo $attribute->id?>" class="ft13px" style="width: 100%">Preço * <small>(Campo obrigatório)</small></label>
                            <input type="text" name="produtos[variations][<?php echo $count?>][price]" value="<?php echo number_format($preco_promo, 2, ',', '.')?>" style="width: 100%;" required="require" class="text-right"/>
                        </div>
                        <div class="col-md-3 col-sm-3 col-xs-6 mb15 text-right">
                            <label for="<?php echo $attribute->id?>" class="ft13px" style="width: 100%;">Estoque * <small>(Campo obrigatório)</small></label>
                            <input type="text" name="produtos[variations][<?php echo $count?>][available_quantity]" value="<?php echo $rw->estoque?>" style="width: 100%;" required="require" class="text-right"/>
                        </div>
                        <?php echo $test_html['PRECO_AND_ESTOQUE'] = ob_get_clean();?>

                        <div class="col-md-12 col-sm-12 col-xs-12 text-right"></div>

                        <?php
                        $colors = null;
                        // Redefinir as cores para os exadecimal
                        foreach ($attributes['body'] as $attribute) 
                            if( $attribute->tags->hidden != '1' ) 
                                if(in_array($attribute->id, ['COLOR', 'MAIN_COLOR']))
                                        foreach ( $attribute->values as $color )
                                            if( ! empty( $color->metadata->rgb ) )
                                                $colors[$color->name] = $color->metadata->rgb;
                        
                        $attrcount = 0;
                        foreach ($attributes['body'] as $keys => $attribute) 
                        {
                            ob_start();
                            // if( $attribute->tags->hidden != '1' ) 
                            // printf('<pre class="col-md-12 col-sm-12 col-xs-12 ft10px">%s</pre>', print_r($attribute, true));
                            if( 
                                // isset($attribute->tags->conditional_required) || 
                                isset($attribute->tags->catalog_required) || 
                                isset($attribute->tags->allow_variations) || 
                                isset($attribute->tags->variation_attribute) || 
                                isset($attribute->tags->new_required) || 
                                isset($attribute->tags->required) ) {
                                    printf('<pre class="col-md-12 col-sm-12 col-xs-12">%s</pre>', print_r($attribute, true));
                                    
                                // Ordenação das cores por nome
                                if (in_array($attribute->id, ['COLOR', 'MAIN_COLOR'])) {
                                    usort($attribute->values, function( $a, $b ) { 
                                        if($a->metadata->rgb == $b->metadata->rgb && $a->name < $b->name) return 0; 
                                        return (($a->metadata->rgb > $b->metadata->rgb && $a->name < $b->name) ? 0 : 1 );
                                    });
                                }
                                
                                $name_input = 'attributes';
                                $name_input = isset($attribute->tags->allow_variations) ? 'attribute_combinations' : $name_input;
                                // $name_input = isset($attribute->tags->defines_picture) ? 'attributes' : $name_input;

                                $required = 
                                    !empty($attribute->tags->required) || 
                                    !empty($attribute->tags->catalog_required) || 
                                    !empty($attribute->tags->defines_picture); // || 
                                    // in_array($attribute->id, ['SIZE', 'BRAND', 'MODEL']);
                                
                                if( $attribute->value_type == 'list' || count($attribute->values) > 0 ) { ?>
                                    <?php echo sprintf('<!--[ %s ]-->', $attribute->id)?>
                                    <div class="col-md-6 col-sm-6 col-xs-6 mb15">
                                        <label class="ft13px" style="width: 100%">
                                            <?php echo $attribute->name?><?php if($required) { ?>* <small>(Campo obrigatório)</small><?php } ?>
                                        </label>
                                        <input name="produtos[variations][<?php echo $count?>][<?php echo $name_input?>][<?php echo $attrcount?>][id]" type="hidden" value="<?php echo $attribute->id?>" style="width: 100%;"/>
                                        <input name="produtos[variations][<?php echo $count?>][<?php echo $name_input?>][<?php echo $attrcount?>][name]" type="hidden" value="<?php echo $attribute->name?>" style="width: 100%;"/>
                                        <input name="produtos[variations][<?php echo $count?>][<?php echo $name_input?>][<?php echo $attrcount?>][value_id]" type="hidden" value="-1" style="width: 100%;"/>
                                        <select name="produtos[variations][<?php echo $count?>][<?php echo $name_input?>][<?php echo $attrcount?>][value_name]" class="selected" style="width: 100%;"<?php echo !empty($required) ? ' required="require"':''?> onchange="$(this).prev().val($(this).find(':selected').data('value')||-1)">
                                            <option value="">Selecione...</option>
                                            <?php foreach ( $attribute->values as $id => $vlattr ) { ?>
                                                <?php
                                                $selected = null; // $rw->nomecor == $vlattr->name ? 'selected':null;
                                                ?>
                                                <option hex1="#<?php echo $colors[$vlattr->name]?>" hex2="#<?php echo $colors[$vlattr->name]?>" data-value="<?php echo $vlattr->id?>" value="<?php echo $vlattr->name?>" <?php echo $selected?>>
                                                    <?php echo $vlattr->name?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <?php echo sprintf('<!--[ END %s ]-->', $attribute->id)?>
                                <?php } else if( $attribute->value_type == 'string' ) { ?>
                                    <?php echo sprintf('<!--[ %s ]-->', $attribute->id)?>
                                    <div class="col-md-6 col-sm-6 col-xs-6 mb15">
                                        <label class="ft13px" style="width: 100%">
                                            <?php echo $attribute->name?><?php if($required) { ?>* <small>(Campo obrigatório)</small><?php } ?>
                                        </label>
                                        <input type="hidden" name="produtos[variations][<?php echo $count?>][<?php echo $name_input?>][<?php echo $attrcount?>][id]" value="<?php echo $attribute->id?>" style="width: 100%;"/>
                                        <input type="text" name="produtos[variations][<?php echo $count?>][<?php echo $name_input?>][<?php echo $attrcount?>][value_name]" value="" style="width: 100%;"<?php echo !empty($required) ? ' required="require"':''?>/>
                                    </div>
                                    <?php echo sprintf('<!--[ END %s ]-->', $attribute->id)?>
                                <?php } ?>
                                <?php
                                $attrcount++;
                            }
                            $test_html[$attribute->id] = ob_get_clean();
                            unset($test_html['MPN']);
                            echo $test_html[$attribute->id];
                        }
                        ?>
                    </div>
                </div>
                <!--[ ENDDIVISAO ]-->
                <?php
                $count++;
            } 
            ?>
            
            <div class="col-md-8 col-md-offset-2 fieldset mb25">
                <h4 class="neo-sans-medium mb15 mt0">Preço *<span class="info-title tooltip" title="Preço de venda no mercado livre">?</span></h4>
                <input type="text" name="produtos[preco]" class="text-right" onKeyPress="return(MascaraMoeda(this,'.',',',event))" value="<?php echo number_format($preco_promo, 2, ',', '.')?>" data-price/>
            </div>
            
            <div class="col-md-8 col-md-offset-2 fieldset mb35" id="recarregar-categorias">
                <h4 class="neo-sans-medium mb15 mt0" style="margin-top:0">Tipo de Anuncio</h4>
                <select name="produtos[listing_type_id]" data-name="listing-type-id" data-change class="select_no_init" style="width: 145px" required="required">
                <?php $listing_types = current( $meli->get('sites/MLB/listing_prices', array('price' => number_format(floatval($preco_promo), 2, '.', ''), 'category_id'=>$GET['categoria_id'])) ); ?>
                    <!-- <option value="">Tipo Anuncio</option> -->
                    <?php
                    $array_tradutor = [
                        'highest'   => 'Exposição máxima',
                        'high'      => 'Exposição alta',
                        'mid'       => 'Exposição media',
                        'low'       => 'Exposição baixa',
                        'lowest'    => 'Exposição menor',
                    ];
                    foreach ($listing_types as $vlType) { ?>
                        <option value="<?php echo $vlType->listing_type_id?>" data-price="R$: <?php echo number_format($vlType->sale_fee_amount, 2, ',', '.')?>" data-exposicao="<?php echo $array_tradutor[ $vlType->listing_exposure ]?>">
                            <?php print( $vlType->listing_type_name )?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            
            <div style="position: fixed; bottom: 0; right: 0; margin: 15px; z-index: 999;">
                <button type="submit" class="btn btn-primary" formaction="/adm/mercadolivre/ml-enviar.php" onclick="return Validar()">enviar</button>
            </div>
        
            <!--<input type="hidden" name="produtos[listing_type_id]" value="<?php echo $GET['listing_type_id']?>">-->
            <input type="hidden" name="produtos[categoria_id]" value="<?php echo $GET['categoria_id']?>">
            <input type="hidden" name="produtos[codigo_id]" value="<?php echo $rs['codigo_id']?>"> 
        <?php
        }
    } 
    // FIM ELSE DO PRODUTO EXCLUIDO
    ?>
    <input name="produtos[id]" value="<?php echo $GET['id'];?>" type="hidden"/>
    <input name="produtos[acao]" value="VerAttr" type="hidden"/>
</form>

<?php ob_start(); ?>
<script>
    // $(function(){
        /**
         * Remove um tamanho selecionado
         */
        $("#conteudos-recarregar").on("click", "a[btn=remove-tamanho]", function(e){
            e.preventDefault();
            $(e.target).parent().remove();
        });

        ClickEscolher = (function( ethis ) {
            var select = $(ethis).children(":selected");
            select.parent().prev().val(select.data("value"));
            select.parent().prev().prev().val(select.data("name"));
            select.parent().prev().prev().prev().val(select.data("id"));
		});

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
                            "class": "row mb25 fieldset",
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
		
        $("#recarregar-categorias").on("change", "select[name]", function(e){
            var $element = e,
                $this = $(this),
                $DataName = $this.attr("data-name"),
                $DataText = $this.find("option:selected").text(),
                $DataHref = $this.attr("data-href");
                
            $("tr[me-use=true]").find("input[data-name="+ $DataName +"]").next().remove();
            if( ! $this.val() ){
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
                                NewCount = list.find("#recarregar-categorias").find("select:last").find("option").length;
                            if( NewCount > 0 ) {
                                $("#recarregar-categorias").find("select:last").after([
                                    $("<select />", {
                                        "class": "select_no_init",
                                        "data-name": "categoria-id",
                                        "name": "produtos[categoria_id]",
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
                            } else {
                                $("#recarregar-categorias").find("select:last").after([
                                    $("<button/>",{
                                        "class": "btn btn-primary ml15",
                                        "type": "submit",
                                        "html": "continuar"
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