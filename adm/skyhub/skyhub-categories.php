<?php
include '../topo.php';
$ConfigSkyhub = Skyhub::find(['conditions' => ['excluir = 0 and loja_id=?', $CONFIG['loja_id'] ]]);

if(count($ConfigSkyhub) == 0)
    return;
?>
<style>
    body {
		background-color: #f1f1f1
	}
</style>

<div class="panel panel-default">
    <div class="panel-heading panel-store text-uppercase">CATEGORIAS</div>
    <div class="panel-body">
        <button type="button" href="" class="pull-right mr5 btn btn-primary mb15 btn-cadastrar" <?php echo _P( "skyhub-categories", $_SESSION['admin']['id_usuario'], 'incluir' )?>>
            <i class="fa fa-edit"></i> cadastrar
        </button>

        <input type="hidden" name="permissao-alterar" value='<?php echo _P( "skyhub-categories", $_SESSION['admin']['id_usuario'], 'alterar' )?>'>
        <input type="hidden" name="permissao-excluir" value='<?php echo _P( "skyhub-categories", $_SESSION['admin']['id_usuario'], 'excluir' )?>'>

        <table width="100%" border="0" cellpadding="8" cellspacing="0" class="table">
            <thead>
                <tr class="plano-fundo-adm-003 ocultar bold">
                    <td>NOME</td>
                    <td class="text-center" nowrap="nowrap" width="1%">AÇÕES</td>
                </tr>
            </thead>
            <tbody id="bodyLista"></tbody>
        </table>
    </div>

</div>

<?php ob_start(); ?>
<script>
    $(document).ready(function(){

        var ConfigHeaders = {
            "X-User-Email": "<?php echo $ConfigSkyhub->user;?>",
            "X-Api-Key": "<?php echo $ConfigSkyhub->api_key?>",
            "X-Accountmanager-Key": "<?php echo $ConfigSkyhub->account?>",
            "Content-Type": "application/json"
        },
        Alterar = '<?php echo _P( "skyhub-categories", $_SESSION['admin']['id_usuario'], 'alterar' )?>',
        Excluir = '<?php echo _P( "skyhub-categories", $_SESSION['admin']['id_usuario'], 'excluir' )?>',
        PermissaoAlterar = Alterar == 'acessar="0" ' ? false : true,
        PermissaoExcluir = Excluir == 'acessar="0" ' ? false : true;

        var ModalCadastro = $("<form/>", {
            id: "FormCadastro",
            class: "row",
            html: [ $("<div/>", { id: "divFormCadastro", class: "col-md-12", html: "" } ) ]
        } ).dialog({
            dialogClass: "classe-ui",
            autoOpen: false,
            width: 400,
            height: 200,
            modal: true,
            title: "Categorias",
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

        var Cadastro = function( Edit = '' ){
            rs = Edit != '' ? JSON.parse(Edit) : null;

            ModalCadastro.find("#divFormCadastro").html([
                $("<input/>", { type: "hidden", name: "code", value: rs != null ? rs.code : '' } ),
                $("<div/>", { class: "row", html: [
                    $("<div/>", { class: "col-md-12", html: [
                        $("<label/>", { for: "name", class: "text-muted", style: "font-size: 12px;", html: "Nome" } ),
                        $("<input/>", { name: "name", type: "text", placeholder: "Nome", class: "form-control", value: rs != null ? rs.name : '' } )
                    ] } )
                ] } )
            ]);

            ModalCadastro.dialog("open");
        };

        var AtualizaLista = function(){
            $.ajax({
                url: "https://api.skyhub.com.br/categories",
                method: "GET",
                headers: ConfigHeaders,
                global: false,
                beforeSend: function(){
                    $("#bodyLista").html([
                        $("<tr/>", { html: [
                            $("<td/>", { class: "text-center", colspan: 2, html: "carregando lista..." } )
                        ] } )
                    ]);
                },
                success: function(result){
                    $("#bodyLista").html("");

                    $.each(result, function(i, rs){
                        $("#bodyLista").append([
                            $("<tr/>", { class: "in-hover lista-zebrada", html: [
                                $("<td/>", { html: rs.name } ),
                                $("<td/>", { nowrap: "nowrap", width: "1%", html: [
                                    $("<button/>", { type: "button", class: "btn btn-xs mr10 btn-warning btn-editar", html: "EDITAR", attr: { "data-name" : rs.name, "data-json" : JSON.stringify(rs) } } ),
                                    $("<button/>", { type: "button", class: "btn btn-xs mr10 btn-danger btn-excluir", html: "EXCLUIR", attr: { "data-name" : rs.name, "data-json" : JSON.stringify(rs) } } )
                                ] } )
                            ] } )
                        ]);
                    });

                    if(!PermissaoAlterar)
                        $(document).find(".btn-editar").fadeOut(0);

                    if(!PermissaoExcluir)
                        $(document).find(".btn-excluir").fadeOut(0);

                },
                error: function(x,y,z)
                {
                    alert("Algo de errado não deu certo!"); 
                    console.log(x.responseText);
                }
            });
        };

        $(document).on("click", ".btn-cadastrar", function(e){
            e.preventDefault();
            Cadastro();
        });

        $(document).on("click", ".btn-editar", function(e){
            e.preventDefault();
            var Item = $(this);
            Cadastro(Item.attr("data-json"));
        });

        $(document).on("click", ".btn-excluir", function(e){
            e.preventDefault();
            var Item = $(this);
            if(!confirm('Deseja realmente excluir a categoria "'+Item.attr("data-name")+'"?'))
                return;

            rs = JSON.parse(Item.attr("data-json"));

            $.ajax({
                url: "https://api.skyhub.com.br/categories/"+rs.code,
                method: "DELETE",
                headers: ConfigHeaders,
                global: false,
                beforeSend: function(){
                    $("#bodyLista").html([
                        $("<tr/>", { html: [
                            $("<td/>", { class: "text-center", colspan: 2, html: "Excluindo..." } )
                        ] } )
                    ]);
                },
                success: function(){
                    //
                },
                complete: function(){
                    AtualizaLista();
                },
                error: function(x,y,z)
                {
                    alert("Algo de errado não deu certo!"); 
                    console.log(x.responseText);
                }
            });
            
        });

        ModalCadastro.on("submit", function(e){
            e.preventDefault();
            
            var Name = $(this).find("input[name='name']").val();
            var urlRequest = "https://api.skyhub.com.br/categories";
            var methodRequest = "POST";

            var dataRequest = JSON.stringify( {
                "category" : {
                    "code" : Name,
                    "name": Name
                }
            });

            if($(this).find("input[name='code']").val().length > 0)
            {
                urlRequest = urlRequest + "/" + $(this).find("input[name='code']").val();
                methodRequest = "PUT";
                var dataRequest = JSON.stringify({ "name": Name });
            }

            $.ajax({
                url: urlRequest,
                method: methodRequest,
                data: dataRequest,
                headers: ConfigHeaders,
                global: false,
                beforeSend: function(){
                    ModalCadastro.find("#divFormCadastro").html([
                        $("<div/>", { class: "row", html: [
                            $("<div/>", { class: "col-md-12", html: [
                                $("<p/>", { class: "text-center", html: "Salvando..." } )
                            ] } )
                        ] } )
                    ]);
                },
                success: function(){
                    //
                },
                complete: function(){
                    ModalCadastro.dialog("close");
                    AtualizaLista();
                },
                error: function(x,y,z)
                {
                    alert("Algo de errado não deu certo!"); 
                    console.log(x.responseText);
                }
            });
            
        });

        AtualizaLista();

    });
</script>
<?php
$SCRIPT['script_manual'] .= ob_get_clean();
include '../rodape.php';