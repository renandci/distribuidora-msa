<?php
include '../topo.php';
$ConfigSkyhub = Skyhub::find(['conditions' => ['excluir = 0 and loja_id=?', $CONFIG['loja_id'] ]]);
$bdMarcas = Marcas::all(['conditions' => ['excluir=? and loja_id=?', 0, $CONFIG['loja_id']], 'order' => 'marcas asc']);

// $rs['id'] = '1612375183';
// // $rs['id'] = '1612375183-2568';
// // Verificar se produto for unico
// $strpos = strpos($rs['id'], '-');
// if($strpos === false)
// echo 'asdfasdfasd - ' . $rs['id'];
// else{
//     echo '123123 - ' . substr($rs['id'], $strpos + 1);
// }

if(count($ConfigSkyhub) == 0)
    return;
?>
<style>
</style>

<div class="panel panel-default">
    <div class="panel-heading panel-store text-uppercase">PEDIDOS</div>
    <div class="panel-body">

        <form name="formPesquisa" class="row mb20">
            <div class="col-md-2">
                <label class="ft12px text-muted">Status:</label>
                <select class="form-control" name="filtro_status">
                    <option value="">Todos</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="ft12px text-muted">Data Inicio:</label>
                <input type="text" name="filtro_data_ini" class="datepicker form-control hasDatepicker" value="<?php echo  date("d/m/Y", strtotime("-30 days"))?>">
            </div>
            <div class="col-md-2">
                <label class="ft12px text-muted">Data Final:</label>
                <input type="text" name="filtro_data_fin" class="datepicker form-control hasDatepicker" value="<?php echo  date("d/m/Y")?>">
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
        </div>

        <table width="100%" border="0" cellpadding="8" cellspacing="0" class="table">
            <thead>
                <tr class="plano-fundo-adm-003 ocultar bold">
                    <td nowrap="nowrap" width="1%" class="text-left">STATUS</td>
                    <td nowrap="nowrap" width="1%">NR PEDIDO</td>
                    <td nowrap="nowrap" width="1%">DATA/HORA</td>
                    <td>CLIENTE</td>
                    <td class="text-right">VALOR</td>
                    <td>FRETE</td>
                    <td class="text-center" nowrap="nowrap" width="1%">AÇÕES</td>
                </tr>
            </thead>
            <tbody id="bodyLista"></tbody>
        </table>
    </div>

</div>

<?php ob_start(); ?>
<script>

    function in_array(needle, haystack) {
        return haystack.indexOf(needle) !== -1;
    }

    $.fn.convert_to_value_us = function(){
        var Valor = $(this).val();
            Valor = Valor.replace('.', ''); 
            Valor = Valor.replace(',', '.')

        return parseFloat(Valor).toFixed(2);
    };

    var formatter = new Intl.NumberFormat( 'pt-BR', {
        style: 'currency',
        currency: 'BRL',
        minimumFractionDigits: 2
    } );

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
    listaStatus = [],
    TempCode = null;
    
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

    var ModalPedido = $("<form/>", {
        id: "FormPedido",
        class: "row",
        html: [ $("<div/>", { id: "divFormPedido", class: "col-md-12", html: "" } ) ]
    } ).dialog({
        dialogClass: "classe-ui",
        autoOpen: false,
        width: $(window).width() - 50,
        height: $(window).height() - 50,
        modal: true,
        title: "Pedido"
    });

    var CamposFicais = $("<form/>", {
        id: "campos_ficais"
    }).dialog({
        title: "Campos Fiscais",
        width: 800,
        height: 532,
        modal: true,
        autoOpen: false
    });

    var ModalEmissaoNfe = $("<form/>", {
        id: "FormEmissaoNfe",
        class: "row",
        html: [ $("<div/>", { id: "divFormEmissaoNfe", class: "col-md-12", html: "" } ) ]
    } ).dialog({
        dialogClass: "classe-ui",
        autoOpen: false,
        width: 800,
        height: 550,
        modal: true,
        title: "Emissão de Nota Fiscal"
    });

    var ModalEnviarParaEntrega = $("<form/>", {
        id: "FormEnviarParaEntrega",
        class: "row",
        html: [ $("<div/>", { id: "divFormEnviarParaEntrega", class: "col-md-12", html: "" } ) ]
    } ).dialog({
        dialogClass: "classe-ui",
        autoOpen: false,
        width: 500,
        height: 250,
        modal: true,
        title: "Emissão de Nota Fiscal"
    });

    var ModalEnviarXmlNfe = $("<form/>", {
        id: "FormModalEnviarXmlNfe",
        class: "row",
        method: "post",
        enctype: "multipart/form-data",
        html: [ $("<div/>", { id: "divFormModalEnviarXmlNfe", class: "col-md-12", html: "" } ) ]
    } ).dialog({
        dialogClass: "classe-ui",
        autoOpen: false,
        width: 800,
        height: 550,
        modal: true,
        title: "Emissão de Nota Fiscal"
    });

    var RandCod = function(length = 12, current) {
        current = current ? current : '';
        return length ? RandCod(--length, "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz".charAt(Math.floor(Math.random() * 60)) + current) : current;
    };

    var CarregaStatus = function()
    {
        $.ajax({
            url: "https://api.skyhub.com.br/statuses",
            method: "GET",
            headers: ConfigHeaders,
            global: false,
            beforeSend: function(){
                ModalLoading.find("#divLoading").html([
                    $("<h2/>", { class: "text-center", html: "Carregando lista de Status" } )
                ]);
                ModalLoading.dialog("open");
            },
            success: function(result){
                ModalLoading.dialog("close");
                $.each(result, function(i, rs){
                    $("select[name='filtro_status']").append([
                        $("<option/>", { value: rs.code, html: rs.label } )
                    ]);
                });

                CarregaPedidos();
            },
            error: function(x,y,z) {
                alert("Algo de errado não deu certo!");
                console.log(x.responseText);
            }
        });
    };

    var CarregaPedidos = function()
    {
        urlRequest = "https://api.skyhub.com.br/orders";
        var conditions = "";

        if($("select[name='filtro_status'] option:selected").val().length > 0)
            conditions += conditions.length > 0 ? "&filters[statuses][]="+ $("select[name='filtro_status'] option:selected").val() : "?filters[statuses][]="+ $("select[name='filtro_status'] option:selected").val();

        if($("input[name='filtro_data_ini']").val().length == 10)
            conditions += conditions.length > 0 ? "&filters[start_date]="+ $("input[name='filtro_data_ini']").val() : "?filters[start_date]="+ $("input[name='filtro_data_ini']").val();

        if($("input[name='filtro_data_fin']").val().length == 10)
            conditions += conditions.length > 0 ? "&filters[end_date]="+ $("input[name='filtro_data_fin']").val() : "?filters[end_date]="+ $("input[name='filtro_data_fin']").val();

        urlRequest = urlRequest + conditions;

        $.ajax({
            url: urlRequest,
            method: "GET",
            headers: ConfigHeaders,
            global: false,
            beforeSend: function(){
                $("#bodyLista").html([
                    $("<tr/>", { html: [
                        $("<td/>", { class: "text-center", colspan: 6, html: "Carregando lista..." } )
                    ] } )
                ]);
            },
            success: function(result){
                $("#bodyLista").html("");
                $.each(result.orders, function(i, rs){

                    var Data = new Date(rs.placed_at),
                        DataVenda = ("00" + Data.getDate()).slice(-2) + "/" + ("00" + (Data.getMonth() + 1)).slice(-2) + "/" + Data.getFullYear() + " " + ("00" + (Data.getHours() + 1)).slice(-2) + ":" + ("00" + (Data.getMinutes())).slice(-2),
                        ImgStatus = "https://imgx.datacontrolinformatica.com.br/";

                    if(rs.status.type == "NEW")
                        ImgStatus += "status-1.png";
                    else if(rs.status.type == "APPROVED")
                        ImgStatus += "status-3.png";
                    else if(rs.status.type == "INVOICED")
                        ImgStatus += "status-7.png";
                    else if(rs.status.type == "SHIPPED")
                        ImgStatus += "status-8.png";
                    else if(rs.status.type == "DELIVERED")
                        ImgStatus += "status-9.png";
                    else if(rs.status.type == "CANCELED")
                        ImgStatus += "status-10.png";
                    else if(rs.status.type == "PAYMENT_OVERDUE")
                        ImgStatus += "status-5.png";

                    $("#bodyLista").append([
                        $("<tr/>", { class: "in-hover lista-zebrada", html: [
                            $("<td/>", { nowrap: "nowrap", width: "1%", class: "text-left", html: [ $("<img/>", { src: ImgStatus, width: 55 } ) ] } ),
                            $("<td/>", { nowrap: "nowrap", width: "1%", class: "text-left", html: rs.import_info.remote_code } ),
                            $("<td/>", { nowrap: "nowrap", width: "1%", html: DataVenda } ),
                            $("<td/>", { html: rs.customer.name } ),
                            $("<td/>", { class: "text-right ft22px", style: "color: #a20000;", html: formatter.format(parseFloat(rs.total_ordered)) } ),
                            $("<td/>", { html: rs.shipping_method } ),
                            $("<td/>", { class: "text-right", nowrap: "nowrap", width: "1%", attr: { "data-json" : JSON.stringify(rs), "data-code": rs.code }, html: [
                                $("<button/>", { class: "btn btn-md btn-primary btn-abre-pedido mr10", html: [ $("<i/>", { class: "fa fa-eye" } ) ], attr: { "data-status" : rs.status.type } } ),
                                
                                // calculation_type verificação para pedido B2W Entrega Direct
                                // @link https://desenvolvedores.skyhub.com.br/integracao-pedido/faturamento-pedido-b2w-entrega-direct
                                
                                $("<button/>", { style: rs.status.type == "NEW" || rs.status.type == "CANCELED" || rs.status.type == "PAYMENT_OVERDUE" || rs.status.type == "DELIVERED" ? "display: none;": "", class: "btn btn-md btn-success btn-avancar mr10", html: [ $("<i/>", { class: "fa fa-chevron-right" } ) ], attr: { "data-status" : rs.status.type, "data-calculation-type": rs.calculation_type } } ),

                                $("<button/>", { style: rs.status.type == "CANCELED" || rs.status.type == "DELIVERED" ? "display: none;" : "", class: "btn btn-md btn-danger btn-cancelar mr10", html: [ $("<i/>", { class: "fa fa-trash" } ) ], attr: { "data-status" : rs.status.type } } )
                            ] } )
                        ] } )
                    ]);
                });
            },
            error: function(x,y,z)
            {
                console.log(x.responseText);
                alert("Algo de errado não deu certo!"); 
            }
        });
    };

    var AvancaFaturamento = function(code, rs)
    {
        $.ajax({
            url: "funcoes.php",
            method: "POST",
            data: { 
                acao: "get_emitentes",
                produtos: rs.items,
                cidade_cliente: rs.shipping_address.city,
                uf_cliente: rs.shipping_address.region
            },
            global: false,
            beforeSend: function(){
                ModalEmissaoNfe.find("#divFormEmissaoNfe").html([
                    $("<div/>", { class: "col-md-12", html: [ $("<h2/>", { class: "text-center", html: "Recuperando informações..." } ) ] } )
                ]);
                ModalEmissaoNfe.dialog("open");
            },
            success: function(result)
            {
                ModalEmissaoNfe.find("#divFormEmissaoNfe").html("");

                TempCode = code;
                if(result.produtos_com_erro.length > 0)
                {
                    ModalEmissaoNfe.find("#divFormEmissaoNfe").html([
                        $("<div/>", { class: "alert alert-danger text-center ft16px bold mb0", html: "Há produtos sem Dados Fiscais!" } ),
                        $("<table/>", { class: "mt5 table", cellpadding: 5, cellspacing: 1, align: "center", html: [
                            $("<thead/>", { html: [
                                $("<tr/>", { class: "plano-fundo-adm-003 ocultar bold", html: [
                                    $("<th/>", { html: "", nowrap: "nowrap", width: "1%" } ),
                                    $("<th/>", { html: "Produto" } )
                                ] } )
                            ] } ),
                            $("<tbody/>", { id: "tBody" } )
                        ] } )
                    ]);

                    $.each(result.produtos_com_erro, function(i, rs_e){
                        var IndexProdErro = rs.items.findIndex( x => x.id === rs_e.sku ),
                            ProdErro = rs.items[IndexProdErro];

                        ModalEmissaoNfe.find("#tBody").append([
                            $("<tr/>", { class: "in-hover lista-zebrada", html: [
                                $("<td/>", { nowrap: "nowrap", width: "1%", html: [
                                    $("<button/>", { type: "button", class: "btn btn-md btn-warning btn-editar_dados-fiscais", attr: { "data-id": rs_e.codigo_id }, html: [ $("<i/>", { class: "fa fa-pencil-square-o" } ) ] } )
                                ] } ),
                                $("<td/>", { style: "vertical-align: middle;", html: rs_e.nome } )
                            ] } )
                        ]);
                    });
                }
                else
                {
                    var Data = new Date(rs.placed_at),
                        DataVenda = Data.getFullYear() + "-" + ("00" + (Data.getMonth() + 1)).slice(-2) + "-" + ("00" + Data.getDate()).slice(-2) + " " + ("00" + (Data.getHours() + 1)).slice(-2) + ":" + ("00" + (Data.getMinutes())).slice(-2) + ":" + ("00" + (Data.getSeconds())).slice(-2);
                    
                    ModalEmissaoNfe.find("#divFormEmissaoNfe").html([
                        $("<input/>", { name: "data_venda", value: DataVenda, type: "hidden" } ),
                        $("<input/>", { name: "produtos", value: JSON.stringify(result.produtos), type: "hidden" } ),
                        $("<input/>", { name: "cliente_nome", value: rs.customer.name, type: "hidden" } ),
                        $("<input/>", { name: "cliente_email", value: rs.customer.email, type: "hidden" } ),
                        $("<input/>", { name: "cliente_cpf", value: rs.customer.vat_number, type: "hidden" } ),
                        $("<input/>", { name: "endereco", value: JSON.stringify(rs.shipping_address), type: "hidden" } ),
                        $("<input/>", { name: "valor_frete", value: rs.shipping_cost, type: "hidden" } ),
                        // outros faturamentos
                        $("<input/>", { name: "calculation_type", value: rs.calculation_type, type: "hidden" } ),
                        $("<div/>", { class: "row", html: [
                            $("<div/>", { class: "col-md-2", html: [
                                $("<label/>", { for: "volume_qty", class: "text-muted ft12px", html: "Qtde Etiquetas" } ),
                                $("<select/>", { class: "form-control", name: "volume_qty", html: "", id: "volume_qty" } )
                            ] } ),
                            $("<div/>", { class: "col-md-6", html: [
                                $("<label/>", { for: "emitente", class: "text-muted ft12px", html: "Emitente" } ),
                                $("<select/>", { class: "form-control", name: "emitente", html: "" } )
                            ] } ),
                            $("<div/>", { class: "col-md-4", html: [
                                $("<label/>", { for: "cidade_cliente", class: "text-muted ft12px", html: "Cidade do Cliente" } ),
                                $("<select/>", { class: "form-control", name: "cidade_cliente", html: "" } )
                            ] } ),
                            $("<div/>", { class: "col-md-12 text-center mt20", html: [
                                $("<button/>", { type: "submit", class: "btn btn-md btn-success btn-emitir-nfe", html: "Emitir NFe e Avançar Status" } )
                            ] } )
                        ] } )
                    ]);
                    
                    for(var x = 1; x <= 10; x++) {
                        $("#volume_qty").append($("<option/>", {value: x, html: x}));
                    }

                    if(result.emitentes.length != 1)
                        ModalEmissaoNfe.find("select[name=emitente]").html([ $("<option/>", { value: "", html: "-" } ) ]);

                    $.each(result.emitentes, function(e, rs_e){
                        ModalEmissaoNfe.find("select[name=emitente]").append([
                            $("<option/>", { value: rs_e.id, html: rs_e.nome } )
                        ]);
                    });
                    
                    if(result.cidades.length != 1)
                        ModalEmissaoNfe.find("select[name=cidade_cliente]").html([ $("<option/>", { value: "", html: "-" } ) ]);

                    $.each(result.cidades, function(e, rs_c){
                        ModalEmissaoNfe.find("select[name=cidade_cliente]").append([
                            $("<option/>", { value: rs_c.cod_ibge, html: rs_c.nome, attr: { "data-uf": rs_c.uf } } )
                        ]);
                    });
                }
            },
            error: function(x,y,z)
            {
                alert("Algo de errado não deu certo!"); 
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
                        ModalLoading.find("#divLoading").html([ $("<h2/>", { class: "text-center", html: "Atualizando estoque do " + nrItem + "º produto" } ) ]);
                    },
                    success: function(){ },
                    complete: function(){ ModalLoading.dialog("open") },
                    error: function(x,y,z) { console.log(x.responseText); }
                });
            },
            error: function(x,y,z)
            {
                console.log(x.responseText);
            }
        });
    };

    var AvancaEnviaParaEntrega = function(code, rs, Tipo)
    {
        TempCode = code;
        ModalEnviarParaEntrega.dialog("open");
        ModalEnviarParaEntrega.find("#divFormEnviarParaEntrega").html([
            $("<h2/>", { class: "col-md-12 text-center", html: "Calculando cubagem..." } )
        ]);
        
        var nrItems = rs.items.length,
            ItensProc = 0,
            cubagem = [],
            produtos = [];

        cubagem['altura'] = 0;
        cubagem['largura'] = 0;
        cubagem['comprimento'] = 0;
        cubagem['peso'] = 0;

        $.each( rs.items, function(i, rs_item){
            $.ajax({
                url: "https://api.skyhub.com.br/products/" + rs_item.product_id,
                method: "GET",
                headers: ConfigHeaders,
                global: false,
                beforeSend: function(){ },
                success: function(rs_prod){

                    produtos.push(rs_prod);
                    ItensProc++;

                    if( parseFloat(cubagem['altura']) < parseFloat(rs_prod.height) )
                        cubagem['altura'] += rs_prod.height;

                    if( parseFloat(cubagem['largura']) < parseFloat(rs_prod.width) )
                        cubagem['largura'] = rs_prod.width;

                    if( parseFloat(cubagem['comprimento']) < parseFloat(rs_prod.length) )
                        cubagem['comprimento'] = rs_prod.length;

                    cubagem['peso'] += rs_prod.weight;

                    if( ItensProc == nrItems )
                        GerarEtiqueta(code, rs, Tipo, cubagem, produtos);
                },
                error: function(x,y,z)
                {
                    console.log("Calculo de cubagem");
                    console.log(x.responseText);
                }
            });
        } );
    };

    var GerarEtiqueta = function(code, rs, Tipo, cubagem, produtos)
    {
        
        ModalEnviarParaEntrega.dialog({title: "Código de Rastreamento"});

        $.ajax({
            url: "/adm/skyhub/funcoes.php",
            method: "POST",
            data:{ 
                acao: "CodigoCorreios",
                Tipo: in_array(Tipo, ["Correios", "Sedex", "Pac"])
                // cubagem: {
                //     "altura": cubagem.altura,
                //     "largura": cubagem.largura,
                //     "comprimento": cubagem.comprimento,
                //     "peso": cubagem.peso,
                // },
                // cep_destino: rs.shipping_address.postcode
            },
            global: false,
            beforeSend: function(){
                ModalEnviarParaEntrega.find("#divFormEnviarParaEntrega").html([
                    $("<h2/>", { class: "col-md-12 text-center", html: "Carregando informação..." } )
                ]);
            },
            success: function(result){
                
                ModalEnviarParaEntrega.find("#divFormEnviarParaEntrega").html([
                    $("<input/>", { type: "hidden", name: "tipo", value: Tipo } ),
                    $("<input/>", { type: "hidden", name: "produtos", value: JSON.stringify(produtos) } ),
                    $("<input/>", { type: "hidden", name: "pedido", value: JSON.stringify(rs) } ),
                    $("<div/>", { class: "row", html: [
                        // $("<div/>", { class: "col-md-6", html: [
                        //     $("<label/>", { for: "volume", class: "text-muted ft12px", html: "Qtde Volume" } ),
                        //     $("<input/>", { name: "volume", type: "number", min: 1, placeholder: "Qtde Volume", class: "form-control", value: 1 } )
                        // ] } ),
                        // $("<div/>", { class: "col-md-6", html: [
                        //     $("<label/>", { for: "frete_seguro", class: "text-muted ft12px", html: "Seguro Adicional" } ),
                        //     $("<select/>", { name: "frete_seguro", class: "form-control", html: [
                        //         $("<option/>", { value: "0", html: "Não" } ),
                        //         $("<option/>", { value: "1", html: "Sim" } )
                        //     ] } )
                        // ] } ),
                        $("<div/>", { class: "col-md-12", html: [
                            $("<label/>", { for: "frete_servico", class: "text-muted ft12px", html: "Selecione o modo de Envio" } ),
                            $("<select/>", { name: "frete_servico", class: "form-control", html: [
                                $("<option/>", { value: null, html: "Selecione o serviço" } )
                            ] } )
                        ] } ),
                        $("<div/>", { class: "col-md-12 text-center mt25", html: [
                            $("<button/>", { type: "submit", class: "btn btn-success btm-md", html: "gerar etiqueta" } )
                        ] } )
                    ] } )
                ]);

                $.each(result.codigo, function(i, rs){
                    ModalEnviarParaEntrega.find("select[name='frete_servico']").append([
                        $("<option/>", { value: rs.codigo, html: rs.servico } ).attr({
                            "data-url": result.url,
                            "data-code": rs.codigo,
                            "data-carrier": result.carrier,
                            "data-method": rs.servico
                        })
                    ]);
                });
            },
            error: function(x, y, z){
                alert("Algo de errado não deu certo");
                console.log(x.responseText);
            }
        });
    };

    ModalEnviarParaEntrega.on("submit", function(e){
        e.preventDefault();
        var Form        = $(this),
            rsPedido    = JSON.parse(Form.find("input[name='pedido']").val()),
            Tipo        = Form.find("input[name='tipo']").val(),
            Selected    = Form.find("select[name='frete_servico'] option:selected"),
            code        = Selected.data("code"),
            carrier     = Selected.data("carrier"),
            method      = Selected.data("method"),
            url         = Selected.data("url");
        
        ModalEnviarParaEntrega.find("#divFormEnviarParaEntrega").html([
            $("<p/>", { class: "ft16px bold mb10", html: "Código de Rastreio: " + code } ),
            $("<p/>", { class: "mb5", html: "" + carrier } ),
            $("<p/>", { class: "mb5", html: "" + method } ),
            $("<p/>", { class: "mb5", html: "" + url } )
        ]);
        
        var DataAtual = new Date(),
            txt_data = DataAtual.toISOString(),
            shipment_items = [];

        $.each(rsPedido.items, function(i, rs){
            shipment_items.push({
                sku: rs.id,
                qty: rs.qty
            });
        });

        var request = {
            "status": "order_shipped",
            "shipment": {
                "code": TempCode,
                "delivered_carrier_date": txt_data.split(".")[0] + "-03:00",
                "items": shipment_items,
                "track": {
                    "code": code,
                    "carrier": carrier,
                    "method": method,
                    "url": url,
                }
            }
        };

        AtualizaStatus('shipments', JSON.stringify(request), TempCode);
        ModalEnviarParaEntrega.delay(880).dialog("close");
    });

    ModalEmissaoNfe.on("submit", function(e)
    {
        e.preventDefault();
        var Form = $(this),
            volume_qty = Form.find("select[name='volume_qty'] option:selected").val(),
            calculation_type = Form.find("input[name='calculation_type']").val();

        $.ajax({
            url: "nfe-xml.php",
            method: "POST",
			data: {
                emitente: Form.find("select[name='emitente'] option:selected").val(),
                cod_ibge: Form.find("select[name='cidade_cliente'] option:selected").val(),
                cliente_uf: Form.find("select[name='cidade_cliente'] option:selected").attr("data-uf"),
                data_venda: Form.find("input[name='data_venda']").val(),
                produtos: JSON.parse(Form.find("input[name='produtos']").val()),
                cliente_nome: Form.find("input[name='cliente_nome']").val(),
                cliente_email: Form.find("input[name='cliente_email']").val(),
                cliente_cpf: Form.find("input[name='cliente_cpf']").val(),
                endereco: JSON.parse(Form.find("input[name='endereco']").val()),
                valor_frete: Form.find("input[name='valor_frete']").val()
            },
            global: false,
            beforeSend: function(){
                ModalEmissaoNfe.find("#divFormEmissaoNfe").html([
                    $("<div/>", { class: "col-md-12", html: [ $("<h2/>", { class: "text-center", html: "Emitindo NFe..." } ) ] } )
                ]);
            },
            success: function(rs)
            {
                if( rs.erro != null )
                {
                    ModalEmissaoNfe.find("#divFormEmissaoNfe").html([
                        $("<div/>", { class: "alert alert-danger text-center ft16px bold mb0", html: rs.erro } )
                    ]);
                }
                else 
                {
                    ModalEmissaoNfe.find("#divFormEmissaoNfe").html([
                        $("<div/>", { class: "alert alert-success text-center ft16px bold mb0", html: "Chave: " + rs.chave } )
                    ]);

                    if(calculation_type === "b2wentregadirect") {

                        ModalEnviarXmlNfe.dialog("open").find("#divFormModalEnviarXmlNfe").html([
                            $("<div/>", { class: "alert alert-info text-center mb15", 
                                html: $("<strong/>", { class: "show", html: "Clique para efetuar o download do xml"})
                            }),
                            $("<div/>", {
                                class: "text-center",
                                html: [
                                    $("<a/>", { 
                                        href: "/adm/nfe/nfe-download.php?f=" + rs.chave + "-autorizada.xml", 
                                        target: "_blank", 
                                        class: "btn btn-success", 
                                        html: "clique aqui continuar", 
                                        attr: { "onclick": "ModalEnviarXmlNfe.dialog('close')" }
                                    }),
                                    $("<hr/>")
                                ]
                            }),
                            $("<input/>", { type: "hidden", name: "code", value: TempCode}),
                            $("<input/>", { type: "hidden", name: "status", value: "order_invoiced"}),
                            $("<input/>", { type: "hidden", name: "issue_date", value: rs.dhemi}),
                            $("<input/>", { type: "hidden", name: "volume_qty", value: volume_qty}),
                            $("<label/>", { for: "file", class: "mt5 show", html: "Selecione o aquivo xml baixado", accept: "text/xml" } ),
                            $("<input/>", { type: "file", name: "file", id: "file", class: "form-control mb15" }),
                            $("<button/>", { type: "submit", html: "Enviar arquivo", class: "btn btn-primary" }),
                        ]);
                    } 
                    else {
                        var json = '{ "status": "order_invoiced", "invoice": { "key": "' + rs.chave + '", "volume_qty": "'+volume_qty+'" } }';
                        AtualizaStatus("invoice", json, TempCode );
                        // ModalEmissaoNfe.dialog("close");
                    }
                }
            },
            error: function(x,y,z)
            {
                alert("Algo de errado não deu certo!"); 
                console.log(x.responseText);
            }
        });
    });

    $("form[name='formPesquisa']").on("submit", function(e)
    {
        e.preventDefault();
        CarregaPedidos();
    });

    $(document).on("click", ".btn-abre-pedido", function(e)
    {
        e.preventDefault();
        var Item = $(this),
            rs = JSON.parse(Item.parent().attr("data-json"));

        var Data = new Date(rs.placed_at);
        var DataVenda = ("00" + Data.getDate()).slice(-2) + "/" + ("00" + (Data.getMonth() + 1)).slice(-2) + "/" + Data.getFullYear() + " " + ("00" + (Data.getHours() + 1)).slice(-2) + ":" + ("00" + (Data.getMinutes() + 1)).slice(-2);
        var Nascimento = rs.customer.date_of_birth.split("-");
        
        ModalPedido.find("#divFormPedido").html([
            $("<div/>", { class: "row", html: [

                $("<div/>", { class: "col-md-6", html: [
                    $("<table/>", { border: 0, cellpadding: 0, cellspacing: 0, html: [
                        $("<thead/>", { html: [
                            $("<tr/>", { class: "plano-fundo-adm-003 ocultar bold", html: [
                                $("<td/>", { colspan: 2, html: "Pedido" } )
                            ] } )
                        ] } ),
                        $("<tbody/>", { html: [
                            $("<tr/>", { class: "lista-zebrada", html: [
                                $("<td/>", { nowrap: "nowrap", width: "1%", html: [$("<strong/>", { html: "Data Pedido:" } )] } ),
                                $("<td/>", { html: DataVenda } )
                            ] } ),
                            $("<tr/>", { class: "lista-zebrada", html: [
                                $("<td/>", { nowrap: "nowrap", width: "1%", html: [$("<strong/>", { html: "Status:" } )] } ),
                                $("<td/>", { html: rs.status.label } )
                            ] } ),
                            $("<tr/>", { class: "lista-zebrada", html: [
                                $("<td/>", { nowrap: "nowrap", width: "1%", html: [$("<strong/>", { html: "Método de Envio:" } )] } ),
                                $("<td/>", { html: rs.shipping_method } )
                            ] } ),
                            $("<tr/>", { class: "lista-zebrada", html: [
                                $("<td/>", { nowrap: "nowrap", width: "1%", html: [$("<strong/>", { html: "Valor Do Desconto:" } )] } ),
                                $("<td/>", { html: formatter.format(rs.discount) } )
                            ] } ),
                            $("<tr/>", { class: "lista-zebrada", html: [
                                $("<td/>", { nowrap: "nowrap", width: "1%", html: [$("<strong/>", { html: "Valor do Juros:" } )] } ),
                                $("<td/>", { html: formatter.format(rs.interest) } )
                            ] } ),
                            $("<tr/>", { class: "lista-zebrada", html: [
                                $("<td/>", { nowrap: "nowrap", width: "1%", html: [$("<strong/>", { html: "Valor do Custo de Envio (Vendedor):" } )] } ),
                                $("<td/>", { html: formatter.format(rs.seller_shipping_cost) } )
                            ] } ),
                            $("<tr/>", { class: "lista-zebrada", html: [
                                $("<td/>", { nowrap: "nowrap", width: "1%", html: [$("<strong/>", { html: "Valor do Frete:" } )] } ),
                                $("<td/>", { html: formatter.format(rs.shipping_cost) } )
                            ] } ),
                            $("<tr/>", { class: "lista-zebrada", html: [
                                $("<td/>", { nowrap: "nowrap", width: "1%", html: [$("<strong/>", { html: "Valor do Pedido:" } )] } ),
                                $("<td/>", { html: formatter.format(rs.total_ordered) } )
                            ] } )
                        ] } )
                    ] } )
                ] } ),
                
                $("<div/>", { class: "col-md-6", html: [
                    $("<table/>", { border: 0, cellpadding: 0, cellspacing: 0, html: [
                        $("<thead/>", { html: [
                            $("<tr/>", { class: "plano-fundo-adm-003 ocultar bold", html: [
                                $("<td/>", { colspan: 2, html: "Dados do Cliente" } )
                            ] } )
                        ] } ),
                        $("<tbody/>", { html: [
                            $("<tr/>", { class: "lista-zebrada", html: [
                                $("<td/>", { nowrap: "nowrap", width: "1%", html: [$("<strong/>", { html: "Nome:" } )] } ),
                                $("<td/>", { html: rs.customer.name } )
                            ] } ),
                            $("<tr/>", { class: "lista-zebrada", html: [
                                $("<td/>", { nowrap: "nowrap", width: "1%", html: [$("<strong/>", { html: "Email:" } )] } ),
                                $("<td/>", { html: rs.customer.email } )
                            ] } ),
                            $("<tr/>", { class: "lista-zebrada", html: [
                                $("<td/>", { nowrap: "nowrap", width: "1%", html: [$("<strong/>", { html: "CPF/CNPJ:" } )] } ),
                                $("<td/>", { html: rs.customer.vat_number } )
                            ] } ),
                            $("<tr/>", { class: "lista-zebrada", html: [
                                $("<td/>", { nowrap: "nowrap", width: "1%", html: [$("<strong/>", { html: "Data Nascimento:" } )] } ),
                                $("<td/>", { html: Nascimento[2] + "/" + Nascimento[1] + "/" + Nascimento[0] } )
                            ] } ),
                            $("<tr/>", { class: "lista-zebrada", html: [
                                $("<td/>", { nowrap: "nowrap", width: "1%", html: [$("<strong/>", { html: "Telefone:" } )] } ),
                                $("<td/>", { html: rs.customer.phones[0] } )
                            ] } )
                        ] } )
                    ] } )
                ] } ),

                $("<div/>", { class: "col-md-12 mt20", html: []}),

                $("<div/>", { class: "col-md-6", html: [
                    $("<table/>", { border: 0, cellpadding: 0, cellspacing: 0, html: [
                        $("<thead/>", { html: [
                            $("<tr/>", { class: "plano-fundo-adm-003 ocultar bold", html: [
                                $("<td/>", { colspan: 2, html: "Endereço de Entrega" } )
                            ] } )
                        ] } ),
                        $("<tbody/>", { html: [
                            $("<tr/>", { class: "lista-zebrada", html: [
                                $("<td/>", { nowrap: "nowrap", width: "1%", html: [$("<strong/>", { html: "Nome:" } )] } ),
                                $("<td/>", { html: rs.shipping_address.full_name } )
                            ] } ),
                            $("<tr/>", { class: "lista-zebrada", html: [
                                $("<td/>", { nowrap: "nowrap", width: "1%", html: [$("<strong/>", { html: "Endereço:" } )] } ),
                                $("<td/>", { html: rs.shipping_address.street + ", " + rs.shipping_address.number } )
                            ] } ),
                            $("<tr/>", { class: "lista-zebrada", html: [
                                $("<td/>", { nowrap: "nowrap", width: "1%", html: [$("<strong/>", { html: "Complemento:" } )] } ),
                                $("<td/>", { html: rs.shipping_address.complement } )
                            ] } ),
                            $("<tr/>", { class: "lista-zebrada", html: [
                                $("<td/>", { nowrap: "nowrap", width: "1%", html: [$("<strong/>", { html: "Bairro:" } )] } ),
                                $("<td/>", { html: rs.shipping_address.neighborhood } )
                            ] } ),
                            $("<tr/>", { class: "lista-zebrada", html: [
                                $("<td/>", { nowrap: "nowrap", width: "1%", html: [$("<strong/>", { html: "Referência:" } )] } ),
                                $("<td/>", { html: rs.shipping_address.detail } )
                            ] } ),
                            $("<tr/>", { class: "lista-zebrada", html: [
                                $("<td/>", { nowrap: "nowrap", width: "1%", html: [$("<strong/>", { html: "Cidade:" } )] } ),
                                $("<td/>", { html: rs.shipping_address.city } )
                            ] } ),
                            $("<tr/>", { class: "lista-zebrada", html: [
                                $("<td/>", { nowrap: "nowrap", width: "1%", html: [$("<strong/>", { html: "Estado:" } )] } ),
                                $("<td/>", { html: rs.shipping_address.region } )
                            ] } )
                        ] } )
                    ] } )
                ] } ),

                $("<div/>", { class: "col-md-6", html: [
                    $("<table/>", { id: "tbFaturamento", border: 0, cellpadding: 0, cellspacing: 0, html: [
                        $("<thead/>", { html: [
                            $("<tr/>", { class: "plano-fundo-adm-003 ocultar bold", html: [
                                $("<td/>", { colspan: 2, html: "Nota Fiscal" } )
                            ] } )
                        ] } ),
                        $("<tbody/>", { html: [
                            $("<tr/>", { class: "lista-zebrada", html: [
                                $("<td/>", { nowrap: "nowrap", width: "1%", html: [$("<strong/>", { html: "Nr. NFe:" } )] } ),
                                $("<td/>", { id: "nr_nfe", html:  "-" } )
                            ] } ),
                            $("<tr/>", { class: "lista-zebrada", html: [
                                $("<td/>", { nowrap: "nowrap", width: "1%", html: [$("<strong/>", { html: "Chave NFe:" } )] } ),
                                $("<td/>", { id: "chave_nfe", html:  "-" } )
                            ] } )
                        ] } )
                    ] } ),

                    $("<table/>", { class: "mt20", id: "tbEntrega", border: 0, cellpadding: 0, cellspacing: 0, html: [
                        $("<thead/>", { html: [
                            $("<tr/>", { class: "plano-fundo-adm-003 ocultar bold", html: [
                                $("<td/>", { colspan: 2, html: "Entrega" } )
                            ] } )
                        ] } ),
                        $("<tbody/>", { html: [
                            $("<tr/>", { class: "lista-zebrada", html: [
                                $("<td/>", { nowrap: "nowrap", width: "1%", html: [$("<strong/>", { html: "Código de Rastreio:" } )] } ),
                                $("<td/>", { id: "rastreio", html:  "" } )
                            ] } ),
                            $("<tr/>", { class: "lista-zebrada", html: [
                                $("<td/>", { colspan: 2, class: "text-center", html:  [
                                    $("<a/>", { class: "btn btn-primary mt15", type: "button", id: "btn_rastreiar", html: "rastreiar", target: "_blank", href: "" } )
                                ] } )
                            ] } )
                        ] } )
                    ] } )
                ] } ),

                $("<h2/>", { class: "col-md-12 text-center mt25", html: "Produtos" } ),

                $("<div/>", { class: "col-md-12", html: [
                    $("<table/>", { border: 0, cellpadding: 0, cellspacing: 0, html: [
                        $("<thead/>", { html: [
                            $("<tr/>", { class: "plano-fundo-adm-003 ocultar bold", html: [
                                $("<td/>", { class: "text-left", nowrap: "nowrap", width: "1%", html: "SKU" } ),
                                $("<td/>", { html: "NOME" } ),
                                $("<td/>", { class: "text-right", nowrap: "nowrap", width: "1%", html: "QUANTIDADE" } ),
                                $("<td/>", { class: "text-right", nowrap: "nowrap", width: "1%", html: "PREÇO ORIGINAL" } ),
                                $("<td/>", { class: "text-right", nowrap: "nowrap", width: "1%", html: "PREÇO DE VENDA" } )
                            ] } )
                        ] } ),
                        $("<tbody/>", { id: "bProdutos" } )
                    ] } )
                ]}),

            ] } )
        ]);

        if( rs.invoices.length > 0 )
        {
            ModalPedido.find("#nr_nfe").html(rs.invoices[0].number);
            ModalPedido.find("#chave_nfe").html(rs.invoices[0].key);
        }
        else
            ModalPedido.find("#tbFaturamento").fadeOut(0);

        if( rs.shipments.length > 0 )
        {
            console.log(rs.shipments[0].tracks[0].code);
            ModalPedido.find("#rastreio").html(rs.shipments[0].tracks[0].code);
            ModalPedido.find("#btn_rastreiar").attr({ "href" :  rs.shipments[0].tracks[0].url});
        }
        else
            ModalPedido.find("#tbEntrega").fadeOut(0);

        ModalPedido.dialog({title: "Pedido: " + rs.import_info.remote_code}).dialog("open");
        $.each(rs.items, function(i, rs_p){
            $.ajax({                
                url: "https://api.skyhub.com.br/products/" + rs_p.product_id,
                method: "GET",
                headers: ConfigHeaders,
                global: false,
                beforeSend: function(){
                    ModalPedido.find("#bProdutos").append([
                        $("<tr/>", { class: "in-hover lista-zebrada", html: [
                            $("<td/>", { class: "text-left", nowrap: "nowrap", width: "1%", html: rs_p.id } ),
                            $("<td/>", { id: "tdNomeProd-" + rs_p.id, html: "Recuperando nome do produto" } ),
                            $("<td/>", { class: "text-right", nowrap: "nowrap", width: "1%", html: rs_p.qty } ),
                            $("<td/>", { class: "text-right", nowrap: "nowrap", width: "1%", html: formatter.format(rs_p.original_price) } ),
                            $("<td/>", { class: "text-right", nowrap: "nowrap", width: "1%", html: formatter.format(rs_p.special_price) } )
                        ] } )
                    ]);
                },
                success: function(rs_produto_geral) {
                    ModalPedido.find("#tdNomeProd-" + rs_p.id).html(rs_produto_geral.name);
                },
                error: function(x,y,z) { console.log(x.responseText); }
            });
        });
    });

    $(document).on("click", ".btn-avancar", function(e)
    {
        e.preventDefault();
        var Item = $(this),
            Status = Item.attr("data-status"),
            rs = JSON.parse(Item.parent().attr("data-json")),
            calculation_type = Item.data("calculation-type"),
            code = rs.code;

            ModalEmissaoNfe.dialog({title: "Faturamento Pedido - " + rs.import_info.remote_code});
            TempCode = null;

        if( Status == "APPROVED" )
            AvancaFaturamento(code, rs);
        else if(Status == "INVOICED")
            AvancaEnviaParaEntrega(code, rs, in_array(rs.shipping_carrier, ['Correios', 'Sedex', 'Pac']) ? "Correios" : "Jadlog");
        else if(Status == "SHIPPED")
        {
            if(!confirm("Deseja realmente marcar esse pedido para entregue?"))
                return;

            var Data = new Date(),
                DataDelivered = ("00" + Data.getDate()).slice(-2) + "/" + ("00" + (Data.getMonth() + 1)).slice(-2) + "/" + Data.getFullYear() + " " + ("00" + (Data.getHours() + 1)).slice(-2) + ":" + ("00" + (Data.getMinutes())).slice(-2);
                request = {
                    "status": "complete",
                    "delivered_date": DataDelivered
                };
            AtualizaStatus( 'delivery', JSON.stringify(request), code);
        }
    })

    ModalEmissaoNfe.on("click", ".btn-editar_dados-fiscais", function(e)
    {
        e.preventDefault();
        var Item = $(this),
            CodigoId = Item.attr("data-id");

        $.ajax({
            url: "/adm/produtos/produtos-cadastrar.php?acao=ProdutosEditar&codigo_id=" + CodigoId,
            global: false,
            beforeSend: function(){
                CamposFicais.dialog("open");
                CamposFicais.html([
                    $("<div/>", { class: "row", html: [
                        $("<div/>", { class: "col-md-12", html: [
                            $("<h2/>", { class: "text-center", html: "Carregando edição..." } )
                        ] } )
                    ] } )
                ]);
            },
            success: function( str ) {
                var list = $("<div/>", { html: str });
                CamposFicais.html([
                    $("<h2/>", { class: "clearfix", html: list.find("#produto-nome").html() }),
                    list.find("#campos_ficais").html(),
                    $("<input/>", { name: "codigo_id", type: "hidden", value: CodigoId}),
                    $("<input/>", { name: "acao", type: "hidden", value: "alterar_num_ncm"}),
                    $("<style/>", {
                        html: ".ui-widget input, .ui-widget select, .ui-widget textarea, .ui-widget button { font-family: inherit; font-size: 16px; } #campos_ficais a.btn-danger{ display: none; }"
                    })
                ]);
            },
            error: function(x,y,z)
            {
                alert("Algo de errado não deu certo!"); 
                console.log(x.responseText);
            }
        });
    });

    // Envia o xml para
    $(document).on("submit", "#FormModalEnviarXmlNfe", function(e) {
        e.preventDefault();
        
        var input = ($(this)[0]),
            form = new FormData();
            form.append("file", input.file.files[0], "/<?php echo sprintf('%sassets/%s/xml/', str_replace('\\', '/', PATH_ROOT), ASSETS)?>" + (input.file.value).split(/(\\|\/)/g).pop());
            form.append("status", input.status.value);
            form.append("issue_date", input.issue_date.value);
            form.append("volume_qty", input.volume_qty.value);

        var uri = encodeURI("https://api.skyhub.com.br/orders/" + input.code.value + "/invoice");

        $.ajax({
            url: uri,
            method: "POST",
            headers: {
                "X-User-Email": "<?php echo $ConfigSkyhub->user;?>",
                "X-Api-Key": "<?php echo $ConfigSkyhub->api_key?>",
                "X-Accountmanager-Key": "<?php echo $ConfigSkyhub->account?>",
                "Accept": "application/json"
            },
            async: false,
            cache: false,
            contentType: false,
            enctype: "multipart/form-data",
            processData: false,
            data: form,
            beforeSend: function() {
                $("#bodyLista").html([
                    $("<tr/>", { html: [
                        $("<td/>", { class: "text-center", colspan: 6, html: "Atualizando status..." } )
                    ] } )
                ]);
            },
            success: function(str) {
                CarregaPedidos();
            },
            complete: function() {
                ModalEmissaoNfe.dialog("close");
                ModalEnviarXmlNfe.dialog("close");
            },
            error: function(x,y,z) {
                var msg = x.responseText;
                console.log(x,y,z);

                if( msg.length < 3 )
                    CarregaPedidos();
                else
                    alert("Algo de errado não deu certo!");
            }
        });
    });

    CamposFicais.on("click", "button[type=submit]", function(e)
    {
        e.preventDefault();
        var dataSerialize = CamposFicais.serialize();
        
        $.ajax({
            url: "funcoes.php",
            data: dataSerialize,
            type: "post",
            global: false,
            beforeSend: function() {
                CamposFicais.html([
                    $("<div/>", { class: "row", html: [
                        $("<div/>", { class: "col-md-12", html: [
                            $("<h2/>", { class: "text-center", html: "Salvando alterações..." } )
                        ] } )
                    ] } )
                ]);
            },
            complete: function() {
                var btn = $(document).find("td[data-code='"+TempCode+"']").find(".btn-avancar");

                CamposFicais.dialog("close");
                ModalEmissaoNfe.dialog("close");
                btn.trigger("click");
            },
            success: function( str ) { },
            error: function(x,y,z)
            {
                alert("Algo de errado não deu certo!"); 
                console.log(x.responseText);
            }
        });
    });

    $(document).on("click", ".btn-cancelar", function(e)
    {
        e.preventDefault();
        var Item = $(this),
            Status = Item.attr("data-status"),
            rs = JSON.parse(Item.parent().attr("data-json"));

        if(!confirm("Deseja realmente cancelar este pedido?"))
            return;
        
        $("#bodyLista").html([ $("<tr/>", { html: [ $("<td/>", { class: "text-center", colspan: 6, html: "Processando..." } ) ] } ) ]);

        ModalLoading.dialog("open");

        var nrItem = 1;
        $.each(rs.items, function(i, rs_item){
            $.ajax({
                url: "https://api.skyhub.com.br/variations/"+rs_item.id,
                method: "GET",
                headers: ConfigHeaders,
                global: false,
                beforeSend: function(){
                    ModalLoading.find("#divLoading").html([ $("<h2/>", { class: "text-center", html: "Recuperando informações do " + nrItem + "º produto" } ) ]);
                },
                success: function(rs_variacao){
                    
                    var Variation = {
                        variation: {
                            ean: rs_variacao.variation.ean,
                            qty: parseInt(rs_variacao.variation.qty) + parseInt(rs_item.qty),
                            images: rs_variacao.variation.images,
                            specifications: rs_variacao.variation.specifications
                        }
                    };

                    var dataRequest = JSON.stringify(Variation);
                    $.ajax({
                        url: "https://api.skyhub.com.br/variations/"+rs_item.id,
                        method: "PUT",
                        data: dataRequest,
                        headers: ConfigHeaders,
                        global: false,
                        beforeSend: function(){
                            ModalLoading.find("#divLoading").html([ $("<h2/>", { class: "text-center", html: "Atualizando estoque do " + nrItem + "º produto" } ) ]);
                        },
                        success: function(){
                            AtualizaEstoqueGeral(rs_variacao.product.sku);
                        },
                        complete: function(){ },
                        error: function(x,y,z)
                        {
                            console.log(x.responseText);
                        }
                    });

                },
                complete: function(){ },
                error: function(x,y,z)
                {
                    console.log(x.responseText);
                }
            });
        });

        AtualizaStatus("cancel", '{ "status": "order_canceled" }', rs.code);
    })

    var AtualizaStatus = function( NewStatus, DataRequest, code )
    {
        $.ajax({
            url: "https://api.skyhub.com.br/orders/"+ code + "/" + NewStatus,
            method: "POST",
            data: DataRequest,
            headers: ConfigHeaders,
            global: false,
            beforeSend: function(){
                $("#bodyLista").html([
                    $("<tr/>", { html: [
                        $("<td/>", { class: "text-center", colspan: 6, html: "Atualizando status..." } )
                    ] } )
                ]);
            },
            success: function(){
                CarregaPedidos();
            },
            complete: function(){ 
                TempCode = null;
                ModalEmissaoNfe.dialog("close");
            },
            error: function(x,y,z)
            {
                var msg = x.responseText;
                console.log(msg);

                if( msg.length < 3 )
                    CarregaPedidos();
                else
                    alert("Algo de errado não deu certo!");
            }
        });
    };

    CarregaStatus();

</script>
<?php
$SCRIPT['script_manual'] .= ob_get_clean();
include '../rodape.php';