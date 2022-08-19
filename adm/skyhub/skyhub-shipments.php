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
    <div class="panel-heading panel-store text-uppercase">Etiquetas</div>
    <div class="panel-body">

        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist" id="nav-tabs">
            <li role="presentation" class="active"><a href="#tab_a" aria-controls="home" role="tab" data-toggle="tab">Pedidos para agrupamento</a></li>
            <li role="presentation"><a href="#tab_b" aria-controls="profile" role="tab" data-toggle="tab">Pré Lista de Postagem</a></li>
            <li role="presentation"><a href="#tab_c" aria-controls="messages" role="tab" data-toggle="tab">Solicitar Coleta</a></li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="tab_a"></div>
            <div role="tabpanel" class="tab-pane" id="tab_b">B...</div>
            <div role="tabpanel" class="tab-pane" id="tab_c">C...</div>
        </div>
    </div>
</div>

<?php ob_start(); ?>
<script>
    <?php
    require PATH_ROOT . 'public/bootstrap/js/bootstrap.js';
    ?>
    var ConfigHeaders = {
        "X-User-Email": "<?php echo $ConfigSkyhub->user;?>",
        "X-Api-Key": "<?php echo $ConfigSkyhub->api_key?>",
        "X-Accountmanager-Key": "<?php echo $ConfigSkyhub->account?>",
        "Content-Type": "application/json"
    },
    Alterar = '<?php echo _P( "skyhub-products", $_SESSION['admin']['id_usuario'], 'alterar' )?>',
    Excluir = '<?php echo _P( "skyhub-products", $_SESSION['admin']['id_usuario'], 'excluir' )?>',
    PermissaoAlterar = Alterar == 'acessar="0" ' ? false : true,
    PermissaoExcluir = Excluir == 'acessar="0" ' ? false : true;
    
    ajax_error = function(x, y, z) {
        alert("Algo de errado não deu certo!");
        var str = JSON.parse(x.responseText);        
        if(str['result'] === 'erro')
            $(".panel").before([
                $("<div/>", { class: "mt15 alert text-center alert-warning", html: str['message'] }).stop().delay(2500).queue(function(a){
                    $(this).fadeOut(110).delay(111).remove();
                    a();
                })
            ]);
        console.log(x, y, z); 
    };

    $("a[href='#tab_a']").delay(110).queue(function(e){
        $(this).trigger("click");
        e();
    });

    // Seta os pedidos para serem agrupados
    var inputs_a = new Array();
    $("#tab_a").on("click", "input[type=checkbox]", function(e){
        var elem = $(e.target),
            index = inputs_a.indexOf(elem.val());

        // Checa e adiciona no array
        if(elem.is(":checked"))
            inputs_a.push(elem.val());
        else
            inputs_a.splice(index, 1);
    });

    $("#tab_a").on("click", "#agrupar_pedidos", function(e){

        if(inputs_a.length === 0){
            alert("Selecione ao menos um pedido!");
            return;
        }

        var data_str = {
            "order_remote_codes": inputs_a
        };

        $.ajax({
            url: "https://api.skyhub.com.br/shipments/b2w/",
            method: "POST",
            headers: ConfigHeaders,
            data: JSON.stringify(data_str),
            global: false,
            success: function(result) {
                $("a[href='#tab_a']").trigger("click");
            },
            error: ajax_error
        });
    });

    // Pedidos para agrupar
    $("#nav-tabs").on("click", "a[href='#tab_a']", function (e) {
        e.preventDefault();
        
        $.ajax({
            url: "https://api.skyhub.com.br/shipments/b2w/to_group",
            method: "GET",
            headers: ConfigHeaders,
            global: false,
            beforeSend: function(){
                $("#tab_a").html([
                    $("<h4/>", { class: "text-center", html: "Carregando lista..." } )
                ]);
            },
            success: function(result) {
                $("#tab_a").html([
                    $("<table/>", {
                        id: "table_tab_a",
                        class: "table table-striped table-hover",
                        html: [
                            $("<thead/>", {
                                html: [
                                    $("<tr/>", {
                                        class: "plano-fundo-adm-003 ocultar bold",
                                        html: [
                                            $("<td/>", {
                                                nowrap: "nowrap",
                                                width: "1%",
                                                bgcolor: "#fff",
                                                html: [
                                                    $("<input/>", {
                                                        type: "checkbox",
                                                        id: "checkbox_a_00"
                                                    }),
                                                    $("<label/>", {
                                                        for: "checkbox_a_00",
                                                        class: "input-checkbox"
                                                    })
                                                ]
                                            }),
                                            $("<td/>", { html: "Pedido" }),
                                            $("<td/>", { html: "Cliente" }),
                                            $("<td/>", { html: "Valor" }),
                                            $("<td/>", { html: "Expedição" })
                                        ]
                                    })
                                ]
                            }),
                            $("<tbody/>", { html: ""})
                        ],
                    })
                ]);

                $.each(result.orders, function(i, rs) {
                    $("#table_tab_a").find("tbody").append([
                        $("<tr/>", {
                            html: [
                                $("<td/>", {
                                    nowrap: "nowrap",
                                    width: "1%",
                                    html: [
                                        $("<input/>", {
                                            type: "checkbox",
                                            id: "checkbox_a_" + rs.code,
                                            "data-id": i,
                                            value: rs.code
                                        }),
                                        $("<label/>", {
                                            for: "checkbox_a_" + rs.code,
                                            class: "input-checkbox"
                                        })
                                    ]
                                }),
                                $("<td/>", { html: rs.code }),
                                $("<td/>", { html: rs.customer }),
                                $("<td/>", { html: "R$: " + rs.value }),
                                $("<td/>", { html: rs.shipping }),
                            ]
                        })
                    ]);
                });

                $("#tab_a").append([
                    $("<button/>", {
                        type: "button",
                        id: "agrupar_pedidos",
                        class: "btn btn-primary",
                        html: "Agrupar pedidos"
                    })
                ]);
            },
            error: ajax_error
        });
    });
    
    // Lista de PLP
    $("#nav-tabs").on("click", "a[href='#tab_b']", function (e) {
        e.preventDefault();
        $.ajax({
            url: "https://api.skyhub.com.br/shipments/b2w",
            method: "GET",
            headers: ConfigHeaders,
            global: false,
            beforeSend: function(){
                $("#tab_b").html([
                    $("<h4/>", { class: "text-center", html: "Carregando lista..." } )
                ]);
            },
            success: function(result) {
                $("#tab_b").html([
                    $("<table/>", {
                        id: "table_tab_b",
                        class: "table table-striped table-hover",
                        html: [
                            $("<thead/>", {
                                html: [
                                    $("<tr/>", {
                                        class: "plano-fundo-adm-003 ocultar bold",
                                        html: [
                                            $("<td/>", { nowrap: "nowrap", width: "1%", html: "ID" }),
                                            $("<td/>", { html: "Expira em" }),
                                            $("<td/>", { html: "Pedidos Agrupados" }),
                                            $("<td/>", { html: "Ações" })
                                        ]
                                    })
                                ]
                            }),
                            $("<tbody/>", { html: ""})
                        ],
                    })
                ]);

                $.each(result.plp, function(i, rs) {

                    var order = new Array(),
                        order_html = '';
                    $.each(rs.orders, function(i, rs1) {
                        order_html += '<button class="mr5 ft10px btn btn-xs btn-primary btn-desagrupar-unic" title="Clique para desagrupar esse pedido" data-json="' + rs1.code + '">';
                        order_html += rs1.code;
                        order_html += '</button>';
                        order.push(rs1.code);
                    });

                    var expiration_date = (((rs.expiration_date).split("-")).reverse()).join("/");

                    $("#table_tab_b").find("tbody").append([
                        $("<tr/>", {
                            html: [
                                $("<td/>", { nowrap: "nowrap", width: "1%", html: rs.id }),
                                $("<td/>", { html: expiration_date }),
                                $("<td/>", { 
                                    id: "td_" + rs.id,
                                    html: order_html
                                }),
                                $("<td/>", { 
                                    width: "1%",
                                    nowrap: "nowrap",
                                    html: [
                                    $("<a/>", {
                                        class: "btn btn-xs btn-info mr5",
                                        html: "imprimir",
                                        target: "_blank",
                                        href: "/adm/skyhub/skyhub-print.php?plp_id=" + rs.id
                                    }),
                                    $("<button/>", {
                                        class: "btn btn-xs btn-danger btn-desagrupar-all",
                                        html: "desagrupar",
                                        target: "_blank",
                                        // "data-json": order.join(",")
                                        "data-json": rs.id
                                    })
                                ]}),
                            ]
                        })
                    ]);
                });
            },
            error: ajax_error
        });
    });

    $("#tab_b").on("click", "button.btn-desagrupar-unic", function (e) {
        
        if(!confirm("Deseja realmente desagrupar!")) return;

        var id = $(this).data("json");

        $.ajax({
            url: "https://api.skyhub.com.br/shipments/b2w/" + id,
            method: "DELETE",
            headers: ConfigHeaders,
            global: false,
            success: function(result) {
                $("a[href='#tab_b']").trigger("click");
            },
            error: ajax_error
        });
    });

    $("#tab_b").on("click", "button.btn-desagrupar-all", function (e) {
        
        if(!confirm("Deseja realmente desagrupar!")) return;

        var id = $(this).data("json");

        $.ajax({
            url: "https://api.skyhub.com.br/shipments/b2w?plp_id=" + id,
            method: "DELETE",
            headers: ConfigHeaders,
            global: false,
            success: function(result) {
                $("a[href='#tab_b']").trigger("click");
            },
            error: ajax_error
        });
    });
    
    // Seta os pedidos para serem coletados
    var inputs_c = new Array();
    $("#tab_c").on("click", "input[type=checkbox]", function(e){
        var elem = $(e.target),
            index = inputs_c.indexOf(elem.val());

        // Checa e adiciona no array
        if(elem.is(":checked"))
            inputs_c.push(elem.val());
        else
            inputs_c.splice(index, 1);
    });

    $("#tab_c").on("click", "button#confirm_collection", function(e){

        if(inputs_c.length === 0){
            alert("Selecione ao menos um pedido!");
            return;
        }

        var data_str = {
            "order_codes": inputs_c
        };

        $.ajax({
            url: "https://api.skyhub.com.br/shipments/b2w/confirm_collection",
            method: "POST",
            headers: ConfigHeaders,
            data: JSON.stringify(data_str),
            global: false,
            success: function(result) {
                $("a[href='#tab_c']").trigger("click");
            },
            error: ajax_error
        });
    });

    // Lista de Coleta
    $("#nav-tabs").on("click", "a[href='#tab_c']", function (e) {
        e.preventDefault();
        
        $.ajax({
            url: "https://api.skyhub.com.br/shipments/b2w/collectables?requested=false&offset=1",
            method: "GET",
            headers: ConfigHeaders,
            global: false,
            beforeSend: function(){
                $("#tab_c").html([$("<h4/>", { class: "text-center", html: "Carregando lista..." })]);
            },
            success: function(result) {
                $("#tab_c").html([
                    $("<table/>", {
                        id: "table_tab_c",
                        class: "table table-striped table-hover",
                        html: [
                            $("<thead/>", {
                                html: [
                                    $("<tr/>", {
                                        class: "plano-fundo-adm-003 ocultar bold",
                                        html: [
                                            $("<td/>", { nowrap: "nowrap", width: "1%", html: "ID" }),
                                            $("<td/>", { html: "Pedido" }),
                                            $("<td/>", { html: "Cliente" }),
                                            $("<td/>", { html: "Valor" })
                                        ]
                                    })
                                ]
                            }),
                            $("<tbody/>", { html: ""})
                        ],
                    })
                ]);

                $.each(result.orders, function(i, rs) {
                    $("#table_tab_c").find("tbody").append([
                        $("<tr/>", {
                            html: [
                                $("<td/>", {
                                    nowrap: "nowrap",
                                    width: "1%",
                                    html: [
                                        $("<input/>", {
                                            type: "checkbox",
                                            id: "checkbox_c_" + rs.code
                                        }),
                                        $("<label/>", {
                                            for: "checkbox_c_" + rs.code,
                                            class: "input-checkbox"
                                        })
                                    ]
                                }),
                                $("<td/>", { html: rs.code }),
                                $("<td/>", { html: rs.customer }),
                                $("<td/>", { html: "R$: " + rs.value })
                            ]
                        })
                    ]);
                });
                $("#tab_c").append([
                    $("<button/>", {
                        type: "button",
                        id: "confirm_collection",
                        class: "btn btn-primary",
                        html: "Confirmar Coleta"
                    })
                ]);
            },
            error: ajax_error
        });
    });
</script>
<?php
$SCRIPT['script_manual'] .= ob_get_clean();
include '../rodape.php';