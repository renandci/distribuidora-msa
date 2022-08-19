<?php
include '../topo.php';
$ConfigSkyhub = Skyhub::find(['conditions' => ['excluir = 0 and loja_id=?', $CONFIG['loja_id'] ]]);
$bdMarcas = Marcas::all(['conditions' => ['excluir=? and loja_id=?', 0, $CONFIG['loja_id']], 'order' => 'marcas asc']);

if(count($ConfigSkyhub) == 0)
    return;
?>
<style>
    body {
		background-color: #f1f1f1
	}
</style>

<div class="panel panel-default">
    <div class="panel-heading panel-store text-uppercase">PRODUTOS</div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12">
                <button type="button" href="" class="pull-right mr5 btn btn-primary mb15 btn-cadastrar btn-travado" <?php echo _P( "skyhub-products", $_SESSION['admin']['id_usuario'], 'incluir' )?> disabled>
                    <i class="fa fa-edit"></i> cadastrar
                </button>
            </div>
        </div>

        <form name="formPesquisa" class="row mb20">
            <div class="col-md-2">
                <label class="ft12px text-muted">Ativo/Desativado:</label>
                <select class="form-control" name="filtro_ativo">
                    <option value="">Todos</option>
                    <option value="enabled">Ativos</option>
                    <option value="disabled">Desativados</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="ft12px text-muted">SKU:</label>
                <input type="text" name="filtro_sku" placeholder="Pesquisa por SKU" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="ft12px text-muted">Nome Produto:</label>
                <input type="text" name="filtro_nome" placeholder="Pesquisa por nome do Produto" class="form-control">
            </div>
            <div class="col-md-2">
                <label class="ft12px text-muted">Estoque até:</label>
                <input type="number" min="0" name="filtro_estoque" placeholder="Pesquisa pelo estoque" class="form-control">
            </div>
            <div class="col-md-12 text-center">
                <button type="submit" class="btn btn-success mt25">
                    <i class="fa fa-search"></i> pesquisar
                </button>
            </div>
        </form>

        <div style="display: none;">
            <input type="hidden" name="permissao-alterar" value='<?php echo _P( "skyhub-products", $_SESSION['admin']['id_usuario'], 'alterar' )?>'>
            <input type="hidden" name="permissao-excluir" value='<?php echo _P( "skyhub-products", $_SESSION['admin']['id_usuario'], 'excluir' )?>'>

            <select style="display: none;" name="modelo-marcas">
                <option value=" - "> - </option>
                <?php foreach($bdMarcas as $rs){ ?>
                    <option value="<?php echo $rs->marcas?>"><?php echo $rs->marcas?></option>
                <?php } ?>
            </select>
        </div>

        <table width="100%" border="0" cellpadding="8" cellspacing="0" class="table">
            <thead>
                <tr class="plano-fundo-adm-003 ocultar bold">
                    <td class="text-center" nowrap="nowrap" width="1%"></td>
                    <td>NOME</td>
                    <td>MARCA</td>
                    <td class="text-right">QTD</td>
                    <td class="text-center">ATIVO</td>
                    <td class="text-center" nowrap="nowrap" width="1%">AÇÕES</td>
                </tr>
            </thead>
            <tbody id="bodyLista"></tbody>
        </table>
    </div>

</div>

<?php ob_start(); ?>
<script>
    $.fn.convert_to_value_us = function(){
        var Valor = $(this).val();
            Valor = Valor.replace('.', ''); 
            Valor = Valor.replace(',', '.')

        return parseFloat(Valor).toFixed(2);
    };

    $(":input.preco-mask").unmask().mask("#.##0,00", { reverse: true }).change();

    var ConfigHeaders = {
        "X-User-Email": "<?php echo $ConfigSkyhub->user;?>",
        "X-Api-Key": "<?php echo $ConfigSkyhub->api_key?>",
        "X-Accountmanager-Key": "<?php echo $ConfigSkyhub->account?>",
        "Content-Type": "application/json"
    },
    Alterar = '<?php echo _P( "skyhub-products", $_SESSION['admin']['id_usuario'], 'alterar' )?>',
    Excluir = '<?php echo _P( "skyhub-products", $_SESSION['admin']['id_usuario'], 'excluir' )?>',
    PermissaoAlterar = Alterar == 'acessar="0" ' ? false : true,
    PermissaoExcluir = Excluir == 'acessar="0" ' ? false : true,
    listaCategorias = [],
    listaEspecificacoes = [],
    f_Categorias = false,
    f_Especificacoes = false,
    PrimeiroCarregamento = true;
    
    var ModalLoading = $("<div/>", {
        class: "row",
        html: [ $("<div/>", { id: "divLoading", class: "col-md-12 text-center mt25", html: "" } ) ]
    } ).dialog({
        dialogClass: "classe-ui",
        autoOpen: false,
        width: 500,
        height: 200,
        modal: true,
        closeOnEscape: false
    }).css({ "overflow-x": "hidden" })

    var ModalCadastar = $("<form/>", {
        id: "FormCadastar",
        class: "row",
        html: [ $("<div/>", { id: "divFormCadastar", class: "col-md-12", html: "" } ) ]
    } ).dialog({
        dialogClass: "classe-ui",
        autoOpen: false,
        width: $(window).width() - 50,
        height: $(window).height() - 50,
        modal: true,
        title: "Lista de Produtos"
    });

    var ModalEditar = $("<form/>", {
        id: "FormEditar",
        class: "row",
        html: [ $("<div/>", { id: "divFormEditar", class: "col-md-12", html: "" } ) ]
    } ).dialog({
        dialogClass: "classe-ui",
        autoOpen: false,
        width: $(window).width() - 50,
        height: $(window).height() - 50,
        modal: true,
        title: "Produto",
        buttons: [
            {
                text: "Salvar",
                class: "btn btn-success btn-xs",
                click: function(){ $(this).submit(); }
            },
            {
                text: "Cancelar",
                class: "btn btn-danger btn-xs",
                click: function(){ $(this).dialog("close"); }
            }
        ]
    });

    var ModalEditarVariacao = $("<form/>", {
        id: "FormEditarVariacao",
        class: "row",
        html: [ $("<div/>", { id: "divFormEditarVariacao", class: "col-md-12", html: "" } ) ]
    } ).dialog({
        dialogClass: "classe-ui",
        autoOpen: false,
        width: $(window).width() - 50,
        height: $(window).height() - 50,
        modal: true,
        title: "Variação",
        buttons: [
            {
                text: "Salvar",
                class: "btn btn-success btn-xs",
                click: function(){ $(this).submit(); }
            },
            {
                text: "Cancelar",
                class: "btn btn-danger btn-xs",
                click: function(){ $(this).dialog("close"); }
            }
        ]
    });

    var ModalTransferencia = $("<form/>", {
        id: "FormTransferencia",
        class: "row",
        html: [ $("<div/>", { id: "divFormTransferencia", class: "col-md-12", html: "" } ) ]
    } ).dialog({
        dialogClass: "classe-ui",
        autoOpen: false,
        width: 400,
        height: 200,
        modal: true,
        title: "Transferência de Estoque",
        buttons: [
            {
                text: "Enviar",
                class: "btn btn-success btn-xs",
                click: function(){ $(this).submit(); }
            },
            {
                text: "Cancelar",
                class: "btn btn-danger btn-xs",
                click: function(){ $(this).dialog("close"); }
            }
        ]
    });

    var RemoveDisable = function()
    {
        if( !f_Categorias || !f_Especificacoes )
            return;

        $(document).find(".btn-travado").prop("disabled", false);
    };

    CarregaCategorias = function()
    {
        PrimeiroCarregamento = false;
        $.ajax({
            url: "https://api.skyhub.com.br/categories",
            method: "GET",
            headers: ConfigHeaders,
            global: false,
            success: function(result) { listaCategorias = result; f_Categorias = true; RemoveDisable(); },
            error: function(x,y,z) { alert("Algo de errado não deu certo!"); console.log(x.responseText); }
        });
    };

    CarregaEspecificacoes = function()
    {
        PrimeiroCarregamento = false;
        $.ajax({
            url: "https://api.skyhub.com.br/attributes",
            method: "GET",
            headers: ConfigHeaders,
            global: false,
            success: function(result) { listaEspecificacoes = result; f_Especificacoes = true; RemoveDisable(); },
            error: function(x,y,z) { alert("Algo de errado não deu certo!"); console.log(x.responseText); }
        });
    };

    var AtualizaLista = function( URL = '', AutoNext = false, AutoClick = "" )
    {
        var urlRequest = "";
        if(URL.length > 0)
            urlRequest = URL;
        else
        {
            urlRequest = "https://api.skyhub.com.br/products";
            var conditions = "";

            if($("select[name='filtro_ativo'] option:selected").val().length > 0)
                conditions += conditions.length > 0 ? "&filters[status]="+ $("select[name='filtro_ativo'] option:selected").val() : "?filters[status]="+ $("select[name='filtro_ativo'] option:selected").val();

            if($("input[name='filtro_nome']").val().length > 0)
                conditions += conditions.length > 0 ? "&filters[name]="+ $("input[name='filtro_nome']").val() : "?filters[name]="+ $("input[name='filtro_nome']").val();

            if($("input[name='filtro_sku']").val().length > 0)
                conditions += conditions.length > 0 ? "&filters[sku]="+ $("input[name='filtro_sku']").val() : "?filters[sku]="+ $("input[name='filtro_sku']").val();

            if($("input[name='filtro_estoque']").val().length > 0 && parseInt($("input[name='filtro_estoque']").val()) > 0)
                conditions += conditions.length > 0 ? "&filters[qty_from]=0&filters[qty_to]="+ $("input[name='filtro_estoque']").val(): "?filters[qty_from]=0&filters[qty_to]="+ $("input[name='filtro_estoque']").val();

            urlRequest = urlRequest + conditions;
        }

        $.ajax({
            url: urlRequest,
            method: "GET",
            headers: ConfigHeaders,
            global: false,
            beforeSend: function(){
                $("#bodyLista").html([
                    $("<tr/>", { html: [
                        $("<td/>", { class: "text-center", colspan: 5, html: "carregando lista..." } )
                    ] } )
                ]);
            },
            success: function(result){
                $("#bodyLista").html("");

                $.each(result.products, function(i, rs){
                    $("#bodyLista").append([
                        $("<tr/>", { class: "in-hover lista-zebrada", html: [
                            $("<td/>", { html: [
                                $("<img/>", { width: "70", src: rs.images[0] } )
                            ] } ),
                            $("<td/>", { html: rs.name } ),
                            $("<td/>", { html: rs.brand } ),
                            $("<td/>", { class: "text-right", html: rs.qty } ),
                            $("<td/>", { class: "text-center", html: rs.status == "enabled" ? "ATIVO" : "DESATIVADO" } ),
                            $("<td/>", { nowrap: "nowrap", width: "1%", html: [
                                $("<button/>", { type: "button", class: "btn btn-xs mr10 btn-warning btn-editar btn-travado", html: "EDITAR", attr: { "data-sku" : rs.sku, "data-json" : JSON.stringify(rs), "disabled" : true } } ),
                            ] } )
                        ] } )
                    ]);
                });

                $("#bodyLista").append([
                    $("<tr/>", { html: [
                        $("<td/>", { colspan: 6, class: "text-center", html: [
                            $("<button/>", { type: "button", class: "btn mr10 mt20 btn-primary btn-next", html: "PRÓXIMO", attr: { "data-next" : result.next } } )
                        ] } )
                    ] } )
                ]);

                if(!PermissaoAlterar)
                    $(document).find(".btn-editar").fadeOut(0);

                if(PrimeiroCarregamento)
                {
                    CarregaCategorias();
                    CarregaEspecificacoes();
                }

                if(AutoNext)
                    AtualizaLista(result.next);
                else
                    RemoveDisable();

                if(AutoClick.length > 0)
                    AbreSku(AutoClick);
                    // $(document).find("button[data-sku='"+AutoClick+"']").trigger("click");

            },
            error: function(x,y,z)
            {
                console.log(x.responseText);

                if(URL.length > 0)
                    AtualizaLista('', true);
                else
                    alert("Algo de errado não deu certo!"); 
            }
        });
    };

    var AbreModalEditar = function( Product = null )
    {
        if(Product == null)
            return;
        
        rs = JSON.parse(Product);

        ModalEditar.find("#divFormEditar").html([
            $("<input/>", { type: "hidden", name: "data-json", value: Product } ),
            $("<div/>", { class: "row", html: [
                $("<div/>", { class: "col-md-12 text-right", html: [
                    $("<button/>", { type: "button", class: "btn btn-xs btn-primary btn-reenviar", html: "reenviar produto", attr: { "data-sku": rs.sku } } )
                ] } ),
                $("<div/>", { class: "col-md-2", html: [
                    $("<label/>", { for: "status", class: "text-muted ft12px", html: "Status" } ),
                    $("<select/>", { name: "status", class: "form-control", html: [
                        $("<option/>", { value: "enabled", html: "Ativo", attr: { "selected" : rs.status == "enabled" ? true : false } } ),
                        $("<option/>", { value: "disabled", html: "Desativado", attr: { "selected" : rs.status == "disabled" ? true : false } } )
                    ] } )
                ] } ),
                $("<div/>", { class: "col-md-3", html: [
                    $("<label/>", { for: "sku", class: "text-muted ft12px", html: "Código" } ),
                    $("<input/>", { name: "sku", type: "text", placeholder: "SKU", class: "form-control", value: rs.sku, attr: { "readonly": "true" } } )
                ] } ),
                $("<div/>", { class: "col-md-7", html: [
                    $("<label/>", { for: "name", class: "text-muted ft12px", html: "Nome" } ),
                    $("<input/>", { name: "name", type: "text", placeholder: "Nome do Produto", class: "form-control", value: rs.name } )
                ] } ),
                $("<div/>", { class: "col-md-12 mt15", html: [
                    $("<label/>", { for: "description", class: "text-muted ft12px", html: "Descrição" } ),
                    $("<textarea/>", { name: "description", html: rs.description, class: "form-control", rows: "7" } )
                ] } ),
                $("<div/>", { class: "col-md-3 mt15", html: [
                    $("<label/>", { for: "brand", class: "text-muted ft12px", html: "Marca" } ),
                    $("<select/>", { name: "brand", class: "form-control", html: $("select[name='modelo-marcas']").html() } )
                ] } ),
                $("<div/>", { class: "col-md-3 mt15", html: [
                    $("<label/>", { for: "cost", class: "text-muted ft12px", html: "Custo" } ),
                    $("<input/>", { name: "cost", placeholder: "Custo", value: parseFloat(rs.cost).toFixed(2), class: "form-control preco-mask" } )
                ] } ),
                $("<div/>", { class: "col-md-3 mt15", html: [
                    $("<label/>", { for: "price", class: "text-muted ft12px", html: "Preço" } ),
                    $("<input/>", { name: "price", placeholder: "Preço", value: parseFloat(rs.price).toFixed(2), class: "form-control preco-mask" } )
                ] } ),
                $("<div/>", { class: "col-md-3 mt15", html: [
                    $("<label/>", { for: "promotional_price", class: "text-muted ft12px", html: "Preço Promocional" } ),
                    $("<input/>", { name: "promotional_price", placeholder: "Preço Promocional", value: parseFloat(rs.promotional_price).toFixed(2), class: "form-control preco-mask" } )
                ] } ),
                $("<div/>", { class: "col-md-12", html: "" } ),
                $("<div/>", { class: "col-md-3 mt15", html: [
                    $("<label/>", { for: "weight", class: "text-muted ft12px", html: "Peso (Kg)" } ),
                    $("<input/>", { name: "weight", placeholder: "Peso", value: parseFloat(rs.weight).toFixed(2), class: "form-control preco-mask" } )
                ] } ),
                $("<div/>", { class: "col-md-3 mt15", html: [
                    $("<label/>", { for: "height", class: "text-muted ft12px", html: "Altura (cm)" } ),
                    $("<input/>", { name: "height", placeholder: "Altura", value: rs.height, class: "form-control", type: "number", min: 0 } )
                ] } ),
                $("<div/>", { class: "col-md-3 mt15", html: [
                    $("<label/>", { for: "width", class: "text-muted ft12px", html: "Largura (cm)" } ),
                    $("<input/>", { name: "width", placeholder: "Largura", value: rs.width, class: "form-control", type: "number", min: 0 } )
                ] } ),
                $("<div/>", { class: "col-md-3 mt15", html: [
                    $("<label/>", { for: "length", class: "text-muted ft12px", html: "Comprimento (cm)" } ),
                    $("<input/>", { name: "length", placeholder: "Comprimento", value: rs.length, class: "form-control", type: "number", min: 0 } )
                ] } ),
                $("<div/>", { class: "col-md-3 mt15", html: [
                    $("<label/>", { for: "ean", class: "text-muted ft12px", html: "EAN" } ),
                    $("<input/>", { name: "ean", placeholder: "EAN", value: rs.ean, class: "form-control" } )
                ] } ),
                $("<div/>", { class: "col-md-3 mt15", html: [
                    $("<label/>", { for: "nbm", class: "text-muted ft12px", html: "NBM" } ),
                    $("<input/>", { name: "nbm", placeholder: "NBM", value: rs.nbm, class: "form-control" } )
                ] } ),


                $("<div/>", { class: "col-md-12 mt20", html: [
                    $("<h2/>", { class: "text-left", html: "CATEGORIAS" } )
                ] } ),
                $("<div/>", { class: "col-md-4", html:[
                    $("<label/>", { for: "add-categoria", class: "text-muted ft12px", html: "Categoria" } ),
                    $("<select/>", { class: "form-control", name: "add-categoria" } )
                ] } ),
                $("<div/>", { class: "col-md-4", html: [
                    $("<button/>", { type: "button", class: "btn btn-md btn-success btn-add-categoria mt25", html: "ADICIONAR" } )
                ] } ),
                $("<div/>", { class: "col-md-12 mt10", html: [
                    $("<table/>", { width: "100%", border: "0", cellpadding: "0", cellspacing: "0", class: "table", html: [
                        $("<thead/>", { html: [
                            $("<tr/>", { class: "plano-fundo-adm-003 ocultars bold", html: [
                                $("<td/>", { nowrap: "nowrap", width: "1%", html: "" } ),
                                $("<td/>", { html: "CATEGORIA" } )
                            ] } )
                        ] } ),
                        $("<tbody/>", { id: "bodyCategorias" } )
                    ] } )
                ] } ),
                $("<div/>", { class: "col-md-12 mt20", html: [
                    $("<h2/>", { class: "text-left", html: "VARIAÇÕES" } )
                ] } ),
                $("<div/>", { class: "col-md-12 mt10", html: [
                    $("<table/>", { width: "100%", border: "0", cellpadding: "0", cellspacing: "0", class: "table", html: [
                        $("<thead/>", { html: [
                            $("<tr/>", { class: "plano-fundo-adm-003 ocultars bold", html: [
                                $("<td/>", { nowrap: "nowrap", width: "1%", html: "" } ),
                                $("<td/>", { nowrap: "nowrap", width: "1%", html: "SKU" } ),
                                $("<td/>", { html: "VARIAÇÃO" } ),
                                $("<td/>", { nowrap: "nowrap", width: "1%", class: "text-right", html: "QTD" } ),
                                $("<td/>", { nowrap: "nowrap", width: "1%", class: "text-center", html: "AÇÕES" } )
                            ] } )
                        ] } ),
                        $("<tbody/>", { id: "bodyVariacoes" } )
                    ] } )
                ] } ),

                $("<div/>", { class: "col-md-12 mt20", html: [
                    $("<h2/>", { class: "text-left", html: "ATRIBUTO PARA VARIAÇÃO" } )
                ] } ),
                $("<div/>", { class: "col-md-4", html:[
                    $("<label/>", { for: "add-attributes", class: "text-muted ft12px", html: "Atributo" } ),
                    $("<select/>", { class: "form-control", name: "add-attributes" } )
                ] } ),
                $("<div/>", { class: "col-md-4", html: [
                    $("<button/>", { type: "button", class: "btn btn-md btn-success btn-add-attributes mt25", html: "ADICIONAR" } )
                ] } ),
                $("<div/>", { class: "col-md-12 mt10", html: [
                    $("<table/>", { width: "100%", border: "0", cellpadding: "0", cellspacing: "0", class: "table", html: [
                        $("<thead/>", { html: [
                            $("<tr/>", { class: "plano-fundo-adm-003 ocultars bold", html: [
                                $("<td/>", { nowrap: "nowrap", width: "1%", html: "" } ),
                                $("<td/>", { html: "ATRIBUTO" } )
                            ] } )
                        ] } ),
                        $("<tbody/>", { id: "bodyVariationAttributes" } )
                    ] } )
                ] } )
            ] } )
        ]);

        ModalEditar.find("select[name='add-attributes']").html([ $("<option/>", { value: "-", html: "-" } ) ]);
        $.each(listaEspecificacoes.attributes, function(i, rs_spec){
            ModalEditar.find("select[name='add-attributes']").append([
                $("<option/>", { value: rs_spec.name, html: rs_spec.label } )
            ]);
        });

        $.each(rs.variation_attributes, function(i, rs_attr){
            ModalEditar.find("#bodyVariationAttributes").append([
                $("<tr/>", { class: "in-hover lista-zebrada", html: [
                    $("<td/>", { nowrap: "nowrap", width: "1%", html: [
                        $("<button/>", { class: "btn btn-md mr10 btn-danger btn-deletar-variation_attributes", html: [ $("<i/>", { class: "fa fa-trash" } ) ], attr: { "data-attr" : rs_attr } } )
                    ] } ),
                    $("<td/>", { style: "vertical-align: middle;", html: rs_attr } )
                ] } )
            ]);
        });

        ModalEditar.find("select[name='add-categoria']").html([ $("<option/>", { value: "-", html: "-" } ) ]);
        $.each(listaCategorias, function(i, rsCat){
            ModalEditar.find("select[name='add-categoria']").append([
                $("<option/>", { value: rsCat.code, html: rsCat.name } )
            ]);
        });

        $.each(rs.categories, function(i, rsCat){
            ModalEditar.find('#bodyCategorias').append([
                $("<tr/>", { class: "in-hover lista-zebrada", html: [
                    $("<td/>", { nowrap: "nowrap", width: "1%", html: [
                        $("<button/>", { type: "button", class: "btn btn-md btn-danger btn-del-categoria", html: [ $("<i/>", { class: "fa fa-trash" } ) ], attr: { "data-json" : JSON.stringify(rsCat) } } )
                    ] } ),
                    $("<td/>", { style: "vertical-align: middle;", html: rsCat.name } )
                ] } )
            ]);
        });

        $.each(rs.variations, function(i, rsVar){
            var Especificacao = "";
            $.each( rsVar.specifications, function(i_spec, rs_spec){
                Especificacao += Especificacao.length > 0 ? " | " + rs_spec.key + ": "+ rs_spec.value : rs_spec.key + ": "+ rs_spec.value;
            } );

            ModalEditar.find('#bodyVariacoes').append([
                $("<tr/>", { class: "in-hover lista-zebrada", html: [
                    $("<td/>", { nowrap: "nowrap", width: "1%", html: [
                        $("<img/>", { width: "70", src: rsVar.images[0] } )
                    ] } ),
                    $("<td/>", { style: "vertical-align: middle;", nowrap: "nowrap", width: "1%", html: rsVar.sku } ),
                    $("<td/>", { style: "vertical-align: middle;", html: Especificacao } ),
                    $("<td/>", { style: "vertical-align: middle;", nowrap: "nowrap", width: "1%", class: "text-right", html: rsVar.qty } ),
                    $("<td/>", { style: "vertical-align: middle;", nowrap: "nowrap", width: "1%", class: "text-center", html: [
                        $("<button/>", { type: "button", class: "btn btn-md mr10 btn-warning btn-editar-variacao", html: [ $("<i/>", { class: "fa fa-pencil-square-o" } ) ], attr: { "data-sku-principal" : rs.sku, "data-json" : JSON.stringify(rsVar) } } ),
                        $("<button/>", { type: "button", class: "btn btn-md mr10 btn-danger btn-deletar-variacao", html: [ $("<i/>", { class: "fa fa-trash" } ) ], attr: { "data-sku-principal" : rs.sku, "data-json" : JSON.stringify(rsVar) } } ),
                        $("<button/>", { type: "button", class: "btn btn-md mr10 btn-primary btn-transf-estoque", html: [ $("<i/>", { class: "fa fa-cart-plus" } ) ], attr: { "data-sku-principal" : rs.sku, "data-json" : JSON.stringify(rsVar) } } ),
                    ] } )
                ] } )
            ]);
        });

        if(!PermissaoAlterar)
            $(document).find(".btn-editar-variacao").fadeOut(0);
        
        if(!PermissaoExcluir)
        {
            $(document).find(".btn-deletar-variacao").fadeOut(0);
            $(document).find(".btn-del-categoria").fadeOut(0);
            $(document).find(".btn-deletar-variation_attributes").fadeOut(0);
        }

        ModalEditar.dialog("open");
        ModalEditar.find("select[name='brand']").find("option[value='"+rs.brand+"']").attr("selected", true);
    };

    var SalvaLog = function(IdProduto, EstoqueLocal, EstoqueTrans, EstoqueSky)
    {
        $.ajax({
            url: "funcoes.php",
            method: "POST",
            data: { 
                acao: "transferencia_estoque",
                id_produto: IdProduto,
                estoque_local: EstoqueLocal,
                estoque_trans: EstoqueTrans,
                estoque_sky: EstoqueSky
            },
            global: false,
            beforeSend: function(){
                $("#bodyLista").html([
                    $("<tr/>", { html: [
                        $("<td/>", { class: "text-center", colspan: 5, html: "Registrando transferência em nosso banco de dados..." } )
                    ] } )
                ]);
            },
            success: function(result){
                if( parseInt(EstoqueTrans) > 0 )
                    AtualizaEstoqueGeral(result.sku_principal);
                else
                {
                    AbreSku(result.sku_principal);
                    AtualizaLista();
                }
            },
            error: function(x,y,z)
            {
                alert("Algo de errado não deu certo!"); 
                console.log(x.responseText);
            }
        });
    };

    var AbreSku = function(SKU)
    {
        $.ajax({
            url: "https://api.skyhub.com.br/products/"+SKU,
            method: "GET",
            headers: ConfigHeaders,
            global: false,
            beforeSend: function(){
                ModalLoading.find("#divLoading").html([
                   $("<div/>", { class: "row", html: [
                       $("<div/>", { class: "col-md-12", html: [
                           $("<h2/>", { class: "text-center", html: "Carregando Produto..." } )
                       ] } )
                   ] } ) 
                ]);
                ModalLoading.dialog("open");
            },
            success: function(result){
                ModalLoading.dialog("close");
                AbreModalEditar(JSON.stringify(result));
            },
            error: function(x,y,z)
            {
                console.log(x.responseText);
            }
        });
    };

    var AtualizaEstoqueGeral = function(SKU)
    {
        $.ajax({
            url: "https://api.skyhub.com.br/products/"+SKU,
            method: "GET",
            headers: ConfigHeaders,
            global: false,
            beforeSend: function(){
                $("#bodyLista").html([
                    $("<tr/>", { html: [
                        $("<td/>", { class: "text-center", colspan: 5, html: "Recuperando produto principal..." } )
                    ] } )
                ]);
            },
            success: function(rs){
                TotalQty = 0;
                $.each(rs.variations, function(i, rss){
                    TotalQty += rss.qty
                });

                var Item = {
                    product: {
                        images: rs.variations[0].images,
                        specifications: rs.variations[0].specifications,
                        variations: rs.variations,
                        categories: rs.categories,
                        variation_attributes: rs.variation_attributes,
                        cost: rs.cost,
                        price: rs.price,
                        promotional_price: rs.promotional_price,
                        ean: rs.ean,
                        brand: rs.branc,
                        status: rs.status,
                        height: rs.height,
                        length: rs.length,
                        width: rs.width,
                        weight: rs.weight,
                        description: rs.description,
                        name: rs.name,
                        nbm: rs.nbm,
                        qty: TotalQty
                    }
                };

                var dataRequest = JSON.stringify(Item);
                $.ajax({
                    url: "https://api.skyhub.com.br/products/"+rs.sku,
                    method: "PUT",
                    data: dataRequest,
                    headers: ConfigHeaders,
                    global: false,
                    beforeSend: function(){
                        $("#bodyLista").html([
                            $("<tr/>", { html: [
                                $("<td/>", { class: "text-center", colspan: 5, html: "Atualizando estoque geral..." } )
                            ] } )
                        ]);
                    },
                    success: function(){ },
                    complete: function(){
                        AtualizaLista("", false, rs.sku);
                    },
                    error: function(x,y,z)
                    {
                        alert("Algo de errado não deu certo!"); 
                        console.log(x.responseText);
                    }
                });
            },
            error: function(x,y,z)
            {
                console.log(x.responseText);
            }
        });
    };

    var EnviaProduto = function(jProduto, Method, SKU)
    {
        var urlRequest = Method == "POST" ? "https://api.skyhub.com.br/products" : "https://api.skyhub.com.br/products/" + SKU;

        var dataRequest = JSON.stringify(jProduto);
        $.ajax({
            url: urlRequest,
            method: Method,
            data: dataRequest,
            headers: ConfigHeaders,
            global: false,
            beforeSend: function(){
                ModalCadastar.dialog("close");
                ModalEditar.dialog("close");
                $("#bodyLista").html([
                    $("<tr/>", { html: [
                        $("<td/>", { class: "text-center", colspan: 5, html: "Enviando produto para a Skyhub..." } )
                    ] } )
                ]);
            },
            success: function(){
                if(Method == "POST")
                    SalvaLog(jProduto.product.id, 0, 0, 0);
                else
                    AtualizaLista("", false, jProduto.product.sku);
            },
            complete: function(){
                if(Method == "POST")
                    alert("Produto enviado para a SkyHub. Pode demorar algumas horas para o produto começar aparecer na lista!");
            },
            error: function(x,y,z)
            {
                var msg = x.responseText;
                console.log(msg);

                if(Method == "PUT")
                    alert("Algo de errado não deu certo!"); 

                if(msg.length < 3 && Method == "POST")
                    SalvaLog(jProduto.product.id, 0, 0, 0);
            }
        });
    };

    var ListarProdutosDb = function(Lista)
    {
        $.each(Lista, function(i, rs){
            ModalCadastar.find("#bListaProdutos").append([
                $("<tr/>", { class: "in-hover lista-zebrada", html: [
                    $("<td/>", { style: "vertical-align: middle;", nowrap: "nowrap", width: "1%", html: [
                        $("<button/>", { type: "button", class: "btn btn-md mr10 btn-success btn-add-enviar", attr: { "data-json": JSON.stringify(rs) }, html: [$("<i/>", { class: "fa fa-check" } )] } )
                    ] } ),
                    $("<td/>", { html: [ $("<img/>", { width: "70", src: rs.variacoes[0].imagens[0] } ) ] } ),
                    $("<td/>", { style: "vertical-align: middle;", html: rs.nome } ),
                    $("<td/>", { style: "vertical-align: middle;", html: rs.marca } )
                ] } )
            ]);
        });
    };

    ModalEditar.on("click", ".btn-reenviar", function(e)
    {
        e.preventDefault();
        var Item = $(this);

        if(!confirm("Deseja realmente reenviar este produto?"))
            return;

        $.ajax({
            url: "funcoes.php",
            method: "POST",
            data: { 
                acao: "get_produto",
                codigo_id: Item.attr("data-sku")
            },
            global: false,
            beforeSend: function(){
                ModalEditar.find("#divFormEditar").html([
                    $("<div/>", { class: "col-md-12", html: [ $("<h2/>", { class: "text-center", html: "Carregando produto..." } ) ] } )
                ]);
            },
            success: function(rs)
            {
                var a_variacoes = [],
                    a_categories = [];

                var a_specifications = [
                    {
                        "key": "Cor",
                        "value": rs.variacoes[0].cor
                    },
                    {
                        "key": "Tamanho",
                        "value": rs.variacoes[0].tamanho
                    }
                ];

                $.each(rs.categorias, function(i, rsc){
                    if(rsc != null)
                    {
                        a_categories.push(
                            {
                                "code": rsc,
                                "name": rsc
                            }
                        );
                    }
                });

                $.each(rs.variacoes, function(i, rsv){
                    a_variacoes.push(
                        {
                            "images": rsv.imagens,
                            "ean": "",
                            "qty": 0,
                            "sku": rsv.sku,
                            "specifications": [
                                {
                                    "key": "Cor",
                                    "value": rsv.cor
                                },
                                {
                                    "key": "Tamanho",
                                    "value": rsv.tamanho
                                }
                            ]
                        }
                    );
                });

                var Produto = {
                    product: {
                        images: rs.variacoes[0].imagens,
                        specifications: a_specifications,
                        variations: a_variacoes,
                        categories: a_categories,
                        variation_attributes: ["Cor", "Tamanho"],
                        cost: parseFloat(rs.preco_custo).toFixed(2),
                        price: parseFloat(rs.preco).toFixed(2),
                        promotional_price: parseFloat(rs.preco_promo).toFixed(2),
                        ean: "",
                        brand: rs.marca,
                        status: "enabled",
                        height: parseInt(rs.altura),
                        length: parseInt(rs.comprimento),
                        width: parseInt(rs.largura),
                        weight: parseFloat(rs.peso).toFixed(2),
                        description: rs.descricao,
                        name: rs.nome,
                        nbm: rs.ncm,
                        qty: 0,
                    }
                };

                // console.log(Produto);
                EnviaProduto(Produto, "PUT", rs.sku);
            },
            error: function(x,y,z)
            {
                alert("Algo de errado não deu certo!"); 
                console.log(x.responseText);
            }
        });
        
    })

    ModalEditar.on("click", ".btn-add-attributes", function(e)
    {
        e.preventDefault();
        var Atributo = ModalEditar.find("select[name='add-attributes'] option:selected").val();

        ModalEditar.find("#bodyVariationAttributes").prepend([
            $("<tr/>", { class: "in-hover lista-zebrada", html: [
                $("<td/>", { nowrap: "nowrap", width: "1%", html: [
                    $("<button/>", { class: "btn btn-md mr10 btn-danger btn-deletar-variation_attributes", html: [ $("<i/>", { class: "fa fa-trash" } ) ], attr: { "data-attr" : Atributo } } )
                ] } ),
                $("<td/>", { style: "vertical-align: middle;", html: Atributo } )
            ] } )
        ]);

        ModalEditar.find("select[name=add-attributes]").val("-").trigger("change");
    });

    ModalEditar.on("click", ".btn-deletar-variation_attributes", function(e)
    {
        e.preventDefault();
        var Item = $(this),
            rs = Item.attr("data-attr");

        if(!confirm('Deseja realmente excluir o atributo para variação "'+rs+'"?'))
            return

        Item.parent().parent().remove();
    });

    ModalEditar.on("click", ".btn-editar-variacao", function(e)
    {
        e.preventDefault();
        var Item = $(this),
            rs = JSON.parse(Item.attr("data-json")),
            SkuPrincipal = Item.attr("data-sku-principal");

        ModalEditarVariacao.find("#divFormEditarVariacao").html([
            $("<input/>", { type: "hidden", name: "sku_principal", value: SkuPrincipal } ),
            $("<div/>", { class: "row", html: [
                $("<div/>", { class: "col-md-3", html: [
                    $("<label/>", { for: "sku", class: "text-muted ft12px", html: "Código" } ),
                    $("<input/>", { name: "sku", type: "text", placeholder: "SKU", class: "form-control", value: rs.sku, attr: { "readonly": "true" } } )
                ] } ),
                $("<div/>", { class: "col-md-3", html: [
                    $("<label/>", { for: "qty", class: "text-muted ft12px", html: "Quantidade" } ),
                    $("<input/>", { name: "qty", type: "text", placeholder: "Quantidade", class: "form-control", value: rs.qty, attr: { "readonly": "true" } } )
                ] } ),
                $("<div/>", { class: "col-md-3", html: [
                    $("<label/>", { for: "ean", class: "text-muted ft12px", html: "EAN" } ),
                    $("<input/>", { name: "ean", type: "text", placeholder: "EAN", class: "form-control", value: rs.ean } )
                ] } ),


                $("<div/>", { class: "col-md-12 mt20", html: [
                    $("<h2/>", { class: "text-left", html: "ESPECIFICAÇÕES" } )
                ] } ),
                $("<div/>", { class: "col-md-4", html:[
                    $("<label/>", { for: "add-attributes", class: "text-muted ft12px", html: "Atributo" } ),
                    $("<select/>", { class: "form-control", name: "add-attributes" } )
                ] } ),
                $("<div/>", { class: "col-md-4", html:[
                    $("<label/>", { for: "add-valor", class: "text-muted ft12px", html: "Valor" } ),
                    $("<input/>", { type: "text", class: "form-control", name: "add-valor" } )
                ] } ),
                $("<div/>", { class: "col-md-4", html: [
                    $("<button/>", { type: "button", class: "btn btn-md btn-success btn-add-attributes mt25", html: "ADICIONAR" } )
                ] } ),
                $("<div/>", { class: "col-md-12 mt10", html: [
                    $("<table/>", { width: "100%", border: "0", cellpadding: "0", cellspacing: "0", class: "table", html: [
                        $("<thead/>", { html: [
                            $("<tr/>", { class: "plano-fundo-adm-003 ocultars bold", html: [
                                $("<td/>", { nowrap: "nowrap", width: "1%", html: "" } ),
                                $("<td/>", { html: "ATRIBUTO" } )
                            ] } )
                        ] } ),
                        $("<tbody/>", { id: "bodySpecifications" } )
                    ] } )
                ] } ),


                $("<div/>", { class: "col-md-12 mt20", html: [
                    $("<h2/>", { class: "text-left", html: "IMAGENS" } )
                ] } ),
                $("<div/>", { class: "col-md-12", id: "variacao-images", html: "" } )


            ] } )
        ]);

        $.each(rs.images, function(i, img)
        {
            ModalEditarVariacao.find("#variacao-images").append([
                $("<div/>", { class: "col-md-3 mb15", html: [
                    $("<img/>", { src: img, class: "w100" } ),
                    $("<div/>", { style: "background-color: #dedede; padding: 5px 0;", class: "clearfix ft10px", html: [
                        $("<div/>", { class: "row", html: [
                            $("<div/>", { class: "col-md-12 text-center", html: [
                                $("<a/>", { href: "javascript: void(0)", class: "remove-imagem", html: [
                                    "Excluir", $("<br/>"), $("<div/>", { class: "fa fa-times-circle cor-001 fa-2x" } )
                                ] } )
                            ] } )
                        ] } )
                    ] } )
                ] } )
            ]);
        });
        
        ModalEditarVariacao.find("select[name='add-attributes']").html([ $("<option/>", { value: "-", html: "-" } ) ]);
        $.each(listaEspecificacoes.attributes, function(i, rs_spec){
            ModalEditarVariacao.find("select[name='add-attributes']").append([
                $("<option/>", { value: rs_spec.name, html: rs_spec.label } )
            ]);
        });

        $.each(rs.specifications, function(i, rs_spec){
            var Atributo = rs_spec.key+": " + rs_spec.value;
            ModalEditarVariacao.find("#bodySpecifications").append([
                $("<tr/>", { class: "in-hover lista-zebrada", html: [
                    $("<td/>", { nowrap: "nowrap", width: "1%", html: [
                        $("<button/>", { class: "btn btn-md mr10 btn-danger btn-deletar-specifications", html: [ $("<i/>", { class: "fa fa-trash" } ) ], attr: { "data-json" : JSON.stringify(rs_spec) } } )
                    ] } ),
                    $("<td/>", { style: "vertical-align: middle;", html: Atributo } )
                ] } )
            ]);
        });

        ModalEditarVariacao.dialog("open");
    });

    ModalEditar.on("submit", function(e)
    {
        e.preventDefault();
        var JsonOld = JSON.parse(ModalEditar.find("input[name='data-json']").val()),
            TotalQty = 0,
            a_categories = [],
            a_variation_attributes = [];

        ModalEditar.find('#bodyCategorias').find("button").each(function(){
            var Item = $(this);
            a_categories.push( JSON.parse(Item.attr("data-json")) );
        });

        ModalEditar.find('#bodyVariationAttributes').find("button").each(function(){
            var Item = $(this);
            a_variation_attributes.push( Item.attr("data-attr") );
        });

        $.each(JsonOld.variations, function(i, rs){
            TotalQty += rs.qty
        });

        var Item = {
            product: {
                images: JsonOld.variations[0].images,
                specifications: JsonOld.variations[0].specifications,
                variations: JsonOld.variations,
                categories: a_categories,
                variation_attributes: a_variation_attributes,
                cost: parseFloat(ModalEditar.find("input[name='cost']").convert_to_value_us()).toFixed(2),
                price: parseFloat(ModalEditar.find("input[name='price']").convert_to_value_us()).toFixed(2),
                promotional_price: parseFloat(ModalEditar.find("input[name='promotional_price']").convert_to_value_us()).toFixed(2),
                ean: ModalEditar.find("input[name='ean']").val(),
                brand: ModalEditar.find("select[name='brand'] option:selected").val(),
                status: ModalEditar.find("select[name='status'] option:selected").val(),
                height: ModalEditar.find("input[name='height']").val(),
                length: ModalEditar.find("input[name='length']").val(),
                width: ModalEditar.find("input[name='width']").val(),
                weight: parseFloat(ModalEditar.find("input[name='weight']").convert_to_value_us()).toFixed(2),
                description: ModalEditar.find("input[name='description']").val(),
                name: ModalEditar.find("input[name='name']").val(),
                nbm: ModalEditar.find("input[name='nbm']").val(),
                qty: TotalQty
            }
        };

        var dataRequest = JSON.stringify(Item);
        $.ajax({
            url: "https://api.skyhub.com.br/products/"+JsonOld.sku,
            method: "PUT",
            data: dataRequest,
            headers: ConfigHeaders,
            global: false,
            beforeSend: function(){
                ModalEditarVariacao.dialog("close");
                ModalEditar.dialog("close");
                $("#bodyLista").html([
                    $("<tr/>", { html: [
                        $("<td/>", { class: "text-center", colspan: 5, html: "Salvando produto..." } )
                    ] } )
                ]);
            },
            success: function(){ },
            complete: function(){
                AtualizaLista("", false, JsonOld.sku);
            },
            error: function(x,y,z)
            {
                alert("Algo de errado não deu certo!"); 
                console.log(x.responseText);
            }
        });
    });

    ModalEditarVariacao.on("submit", function(e)
    {
        e.preventDefault();

        var a_images = [],
            a_specifications = [],
            sku = ModalEditarVariacao.find("input[name='sku']").val(),
            SkuPrincipal = ModalEditarVariacao.find("input[name='sku_principal']").val();

        ModalEditarVariacao.find("#variacao-images").find("img").each(function(){
            var Item = $(this);
            a_images.push(Item.attr("src"));
        });

        ModalEditarVariacao.find("#bodySpecifications").find("button").each(function(){
            var Item = $(this);
            a_specifications.push( JSON.parse(Item.attr("data-json")) );
        });

        var Item = {
            variation: {
                ean: ModalEditarVariacao.find("input[name='ean']").val(),
                qty: ModalEditarVariacao.find("input[name='qty']").val(),
                images: a_images,
                specifications: a_specifications
            }
        };

        var dataRequest = JSON.stringify(Item);

        $.ajax({
            url: "https://api.skyhub.com.br/variations/"+sku,
            method: "PUT",
            data: dataRequest,
            headers: ConfigHeaders,
            global: false,
            beforeSend: function(){
                ModalEditarVariacao.dialog("close");
                ModalEditar.dialog("close");
                $("#bodyLista").html([
                    $("<tr/>", { html: [
                        $("<td/>", { class: "text-center", colspan: 5, html: "Salvando variação..." } )
                    ] } )
                ]);
            },
            success: function(){ },
            complete: function(){
                AtualizaLista("", false, SkuPrincipal);
            },
            error: function(x,y,z)
            {
                alert("Algo de errado não deu certo!"); 
                console.log(x.responseText);
            }
        });


    });

    ModalEditarVariacao.on("click", ".remove-imagem", function(e)
    {
        e.preventDefault();
        var Item = $(this);

        if(!confirm("Deseja realmente excluir essa imagem?"))
            return;

        Item.parent().parent().parent().parent().remove();
    })

    ModalEditarVariacao.on("click", ".btn-add-attributes", function(e)
    {
        e.preventDefault();
        var Atributo = ModalEditarVariacao.find("select[name='add-attributes'] option:selected").val() + ": " + ModalEditarVariacao.find("input[name='add-valor']").val();
        var NovoItem = {
            "key" : ModalEditarVariacao.find("select[name='add-attributes'] option:selected").val(),
            "value" : ModalEditarVariacao.find("input[name='add-valor']").val()
        };

        ModalEditarVariacao.find("#bodySpecifications").prepend([
            $("<tr/>", { class: "in-hover lista-zebrada", html: [
                $("<td/>", { nowrap: "nowrap", width: "1%", html: [
                    $("<button/>", { class: "btn btn-md mr10 btn-danger btn-deletar-specifications", html: [ $("<i/>", { class: "fa fa-trash" } ) ], attr: { "data-json" : JSON.stringify(NovoItem) } } )
                ] } ),
                $("<td/>", { style: "vertical-align: middle;", html: Atributo } )
            ] } )
        ]);

        ModalEditarVariacao.find("select[name=add-attributes]").val("-").trigger("change");
    });

    ModalEditarVariacao.on("click", ".btn-deletar-specifications", function(e)
    {
        e.preventDefault();
        var Item = $(this),
            rs = JSON.parse(Item.attr("data-json"));

        if(!confirm('Deseja realmente excluir o atributo "'+rs.key+'"?'))
            return

        Item.parent().parent().remove();
    });

    ModalEditar.on("click", ".btn-deletar-variacao", function(e)
    {
        e.preventDefault();
        var Item = $(this),
            rs = JSON.parse(Item.attr("data-json")),
            SkuPrincipal = Item.attr("data-sku-principal");

        if(!confirm("Deseja realmente excluir essa variação? Essa ação é irreversível!"))
            return;

        $.ajax({
            url: "https://api.skyhub.com.br/variations/" + rs.sku,
            method: "DELETE",
            headers: ConfigHeaders,
            global: false,
            beforeSend: function(){
                ModalEditar.dialog("close");
                $("#bodyLista").html([
                    $("<tr/>", { html: [
                        $("<td/>", { class: "text-center", colspan: 5, html: "Excluindo variação..." } )
                    ] } )
                ]);
            },
            success: function(result) {       
                AtualizaLista("", false, SkuPrincipal);
            },
            error: function(x,y,z) { alert("Algo de errado não deu certo!"); console.log(x.responseText); }
        });
        
    });

    ModalEditar.on("click", ".btn-add-categoria", function(e)
    {
        e.preventDefault();
        var NovoItem = {
            "code" : ModalEditar.find("select[name='add-categoria'] option:selected").val(),
            "name" : ModalEditar.find("select[name='add-categoria'] option:selected").text()
        };

        ModalEditar.find('#bodyCategorias').prepend([
            $("<tr/>", { class: "in-hover lista-zebrada", html: [
                $("<td/>", { nowrap: "nowrap", width: "1%", html: [
                    $("<button/>", { type: "button", class: "btn btn-md btn-danger btn-del-categoria", html: [ $("<i/>", { class: "fa fa-trash" } ) ], attr: { "data-json" : JSON.stringify(NovoItem) } } )
                ] } ),
                $("<td/>", { style: "vertical-align: middle;", html: ModalEditar.find("select[name='add-categoria'] option:selected").text() } )
            ] } )
        ]);
        ModalEditar.find("select[name=add-categoria]").val("-").trigger("change");
    });

    ModalEditar.on("click", ".btn-del-categoria", function(e)
    {
        e.preventDefault();
        var Item = $(this),
            rs = JSON.parse(Item.attr("data-json"));

        if(!confirm('Deseja realmente excluir a categoria "'+rs.name+'"?'))
            return;

        Item.parent().parent().remove();
    });

    ModalEditar.on("click", ".btn-transf-estoque", function(e)
    {
        e.preventDefault();
        var Item = $(this),
            rs_v = JSON.parse(Item.attr("data-json"));

            $.ajax({
            url: "funcoes.php",
            method: "POST",
            data: { 
                acao: "get_estoque",
                sku: rs_v.sku
            },
            global: false,
            beforeSend: function(){
                ModalLoading.find("#divLoading").html([
                    $("<div/>", { class: "col-md-12", html: [ $("<h2/>", { class: "text-center", html: "Recuperando estoque..." } ) ] } )
                ]);
                ModalLoading.dialog("open");
            },
            success: function(rs)
            {
                // console.log(rs);
                ModalLoading.dialog("close");
                ModalTransferencia.find("#divFormTransferencia").html([
                    $("<input/>", { type: "hidden", value: Item.attr("data-json"), name: "txt_json" } ),
                    $("<input/>", { type: "hidden", value: rs.id, name: "id_produto" } ),
                    $("<input/>", { type: "hidden", value: rs.estoque, name: "estoque_local" } ),
                    $("<input/>", { type: "hidden", value: rs_v.qty, name: "estoque_sky" } ),
                    $("<div/>", { class: "row mt15", html: [
                        $("<div/>", { class: "col-md-4 text-center", html: [
                            $("<p/>", { class: "mt10", html: $("<b/>", { html: "EM ESTOQUE" } ) } ),
                            $("<p/>", { html: rs.estoque } )
                        ] } ),
                        $("<div/>", { class: "col-md-4 text-center", html: [
                            $("<i/>", { class: "fa fa-arrow-right fa-4x", style: "color: #bfe6ff" } )
                        ] } ),
                        $("<div/>", { class: "col-md-4", html: [
                            $("<label/>", { for: "txt_transferir", class: "text-muted ft12px", html: "Transferir" } ),
                            $("<input/>", { class: "form-control", type: "number", min: 1, value: 1, name: "txt_transferir" } )
                        ] } )
                    ] } )
                ]);
                ModalTransferencia.dialog("open");
            },
            error: function(x,y,z)
            {
                alert("Algo de errado não deu certo!"); 
                console.log(x.responseText);
            }
        });
    });

    $(document).on("click", ".btn-cadastrar", function(e)
    {
        e.preventDefault();
        ModalCadastar.dialog("open");

        $.ajax({
            url: "funcoes.php",
            method: "POST",
            data: { acao: "lista_produtos" },
            global: false,
            beforeSend: function(){
                ModalCadastar.find("#divFormCadastar").html([
                    $("<div/>", { class: "col-md-12", html: [ $("<h2/>", { class: "text-center", html: "Carregando lista de produtos..." } ) ] } )
                ]);
            },
            success: function(result)
            {
                ModalCadastar.find("#divFormCadastar").html([
                    $("<div/>", { class: "row", html: [
                        $("<div/>", { class: "col-md-9", html: [
                            $("<label/>", { for: "f_pesquisa", class: "text-muted ft12px", html: "Pesquisa" } ),
                            $("<input/>", { type: "text", placeholder: "Pesquisa por nome do produto", name: "f_pesquisa", class: "form-control" } )
                        ] } ),
                        $("<div/>", { class: "col-md-3", html: [
                            $("<button/>", { type: "submit", class: "btn btn-primary mt25 btn-pesquisar", html: [ $("<i/>", { class: "fa fa-search" } ) ] } )
                        ] } ),
                        $("<div/>", { class: "col-md-12 mt20", html:[
                            $("<table/>", { width: "100%", border: 0, cellpadding :8, cellspacing: 0, class: "table", html: [
                                $("<thead/>", { html: [
                                    $("<tr/>", { class: "plano-fundo-adm-003 ocultar bold", html: [
                                        $("<td/>", { nowrap: "", width: "1%", html: "" } ),
                                        $("<td/>", { nowrap: "", width: "1%", html: "" } ),
                                        $("<td/>", { html: "NOME PRODUTO" } ),
                                        $("<td/>", { html: "MARCA" } ),
                                    ] } )
                                ] } ),
                                $("<tbody/>", { id: "bListaProdutos" } )
                            ] } )
                        ] } )
                    ] } )
                ]);

                ListarProdutosDb(result);
            },
            error: function(x,y,z)
            {
                alert("Algo de errado não deu certo!"); 
                console.log(x.responseText);
            }
        });
    });

    ModalCadastar.on("submit", function(e)
    {
        e.preventDefault();
        var Pesquisa = ModalCadastar.find("input[name='f_pesquisa']").val();
        
        $.ajax({
            url: "funcoes.php",
            method: "POST",
            data: { 
                acao: "lista_produtos",
                pesquisa: Pesquisa
            },
            global: false,
            beforeSend: function(){
                ModalCadastar.find("#bListaProdutos").html([
                    $("<tr/>", { class: "in-hover lista-zebrada", html: [
                        $("<td/>", { colspan: 4, class: "text-center", html: "Carregando lista de produtos..." } )
                    ] } )
                ]);
            },
            success: function(result)
            {
                ModalCadastar.find("#bListaProdutos").html("");
                ListarProdutosDb(result);
            },
            error: function(x,y,z)
            {
                alert("Algo de errado não deu certo!"); 
                console.log(x.responseText);
            }
        });

    });

    $(document).on("click", ".btn-editar", function(e)
    {
        e.preventDefault();
        var Item = $(this);
        AbreModalEditar(Item.attr("data-json"));
    });

    ModalCadastar.on("click", ".btn-add-enviar", function(e)
    {
        e.preventDefault();
        var Item = $(this),
            rs = JSON.parse(Item.attr("data-json"));

        if(!confirm('Deseja realmente enviar o produto "'+rs.nome+'" para a Skyhub?'))
            return;

        var a_variacoes = [],
            a_categories = [];

        var a_specifications = [
            {
                "key": "Cor",
                "value": rs.variacoes[0].cor
            },
            {
                "key": "Tamanho",
                "value": rs.variacoes[0].tamanho
            }
        ];

        $.each(rs.categorias, function(i, rsc){
            if(rsc != null)
            {
                a_categories.push(
                    {
                        "code": rsc,
                        "name": rsc
                    }
                );
            }
        });

        $.each(rs.variacoes, function(i, rsv){
            a_variacoes.push(
                {
                    "images": rsv.imagens,
                    "ean": "",
                    "qty": 0,
                    "sku": rsv.sku,
                    "specifications": [
                        {
                            "key": "Cor",
                            "value": rsv.cor
                        },
                        {
                            "key": "Tamanho",
                            "value": rsv.tamanho
                        }
                    ]
                }
            );
        });

        var Produto = {
            product: {
                images: rs.variacoes[0].imagens,
                specifications: a_specifications,
                variations: a_variacoes,
                categories: a_categories,
                variation_attributes: ["Cor", "Tamanho"],
                cost: parseFloat(rs.preco_custo).toFixed(2),
                price: parseFloat(rs.preco).toFixed(2),
                promotional_price: parseFloat(rs.preco_promo).toFixed(2),
                ean: "",
                brand: rs.marca,
                status: "enabled",
                height: parseInt(rs.altura),
                length: parseInt(rs.comprimento),
                width: parseInt(rs.largura),
                weight: parseFloat(rs.peso).toFixed(2),
                description: rs.descricao,
                name: rs.nome,
                nbm: rs.ncm,
                qty: 0,
                sku: rs.sku,
                id: rs.id
            }
        };

        EnviaProduto(Produto, "POST");
    } );

    $("form[name='formPesquisa']").on("submit", function(e)
    {
        e.preventDefault();
        AtualizaLista();
    });

    ModalTransferencia.on("submit", function(e)
    {
        e.preventDefault();
        var Item = $(this),
            rs = JSON.parse(Item.find("input[name='txt_json']").val());

        var Variation = {
            variation: {
                ean: rs.ean,
                qty: parseInt(rs.qty) + parseInt(Item.find("input[name='txt_transferir']").val()),
                images: rs.images,
                specifications: rs.specifications
            }
        };

        var dataRequest = JSON.stringify(Variation);
        
        $.ajax({
            url: "https://api.skyhub.com.br/variations/"+rs.sku,
            method: "PUT",
            data: dataRequest,
            headers: ConfigHeaders,
            global: false,
            beforeSend: function(){
                ModalEditarVariacao.dialog("close");
                ModalEditar.dialog("close");
                ModalTransferencia.dialog("close");
                $("#bodyLista").html([
                    $("<tr/>", { html: [
                        $("<td/>", { class: "text-center", colspan: 5, html: "Salvando variação..." } )
                    ] } )
                ]);
            },
            success: function(){
                SalvaLog(Item.find("input[name='id_produto']").val(), Item.find("input[name='estoque_local']").val(), Item.find("input[name='txt_transferir']").val(), Item.find("input[name='estoque_sky']").val());
            },
            complete: function(){ },
            error: function(x,y,z)
            {
                alert("Algo de errado não deu certo!"); 
                console.log(x.responseText);
            }
        });
    })

    AtualizaLista();
</script>
<?php
$SCRIPT['script_manual'] .= ob_get_clean();
include '../rodape.php';