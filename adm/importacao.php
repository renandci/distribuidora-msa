<?php include 'topo.php' ?>
        
    <style>

        label[for=file] {
            padding: 15px;
            background-color: #f1f1f1;
            display: block;
            text-align: center;
        }
        label[for=file] + input[type=file] {
            display: none;
        }
        label[for=file] > i {
            display: block;
            cursor: pointer;
        }

    </style>
    <form action="/adm/importacao-acao.php" method="post" enctype="multipart/form-data" class="col-lg-4 col-lg-offset-4">
        <div class="panel panel-default">
            <div class="panel-heading panel-store">
                Importação de Produtos
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label for="tipo">Tipo</label>
                    <select name="tipo" id="tipo" class="form-control">
                        <option value="Bling">Bling</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="file">
                        <i class="fa fa-4x fa-upload"></i>
                        Selecione a Planilha
                    </label>
                    <input name="file" id="file" type="file" onchange="$(this).next().html(this.value)" accept=".xlsx, .xls, .csv"/>
                    <span class="show"></span>
                </div>
            </div>
        </div>

        <button class="btn btn-primary" type="submit">enviar</button>
    </form>
    
<?php include 'rodape.php';