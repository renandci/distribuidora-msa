<?php
/* 
 *  @Author: Renan - Data Control Informatica.
 *  @Mail: renan@dcisuporte.com.br
 *  @Date: 05/01/2016 
 *  @Time: 15:08:08
 */
include '../topo.php';
$id_marca = is_string($GET['id_marca']) && $GET['id_marca'] != '' ? addslashes((int)$GET['id_marca']) : null;
$pesquisar = is_string($GET['pesquisar']) && $GET['pesquisar'] != '' ? addslashes((string)$GET['pesquisar']) : null;
?>

    <ul class="lista-comuns mb35" id="atualizar-precos">
        <?php
        switch ($GET['acao'])
        {
            case 'alterar-precos': ?>
            <li>
                <form id="janela-precos" action="/adm/produtos/produtos-precos.php" method="post">
                    <div class="clearfix mb15 plano-fundo-adm-003">
                        <span class='mb10 mt10 ml5 mr5 pull-left'>
                            <input type='radio' id='real' name='seleciona' value="real" checked />
                            <label for='real' class='input-radio'></label> <b>R$</b>
                        </span>
                        <span class='mb10 mt10 ml5 mr5 pull-left'>
                            <input type='radio' id='percentual' name='seleciona' value="percentual"/>
                            <label for='percentual' class='input-radio'></label> <b>%</b>
                        </span>
                        <span class='mb10 mt10 ml5 mr5 pull-left'>
                            <input type='radio' id='operador-positivo' name='operador' value="+" checked/>
                            <label for='operador-positivo' class='input-radio'></label> <i class="fa fa-plus fa-1x"></i>
                        </span>
                        <span class='mb10 mt10 ml5 mr5 pull-left'>
                            <input type='radio' id='operador-negativo' name='operador' value="-"/>
                            <label for='operador-negativo' class='input-radio'></label> <i class="fa fa-minus fa-1x"></i>
                        </span>
                    </div>
                    <div id="valor-real" class="selecao">
                        <p class="bold mb0">Preço:</p>
                        <input name="preco_real" onKeyPress="return(MascaraMoeda(this,'.',',',event))" class="w50" autocomplete="off"/>
                    </div>
                    <div id="valor-percentual" class="selecao hidden">
                        <p class="bold mb0 mt10">Preço Percentual:</p>
                        <input name="preco_percentual" onkeyup="return(this.value=this.value.replace(/\D/g,''))" class="w30" autocomplete="off"/>%
                    </div>
                    <center class="clearfix">
                        <button type="submit" class="btn btn-sm btn-primary mt25">salvar</button>
                        <input type="hidden" name="id_marca" value="<?php echo $id_marca?>"/>
                        <input type="hidden" name="acao" value="salvar-dados"/>
                    </center>
                    <script>
                        $(function(){
                            $('#janela-precos').find('input[name="seleciona"]').click(function(e){
                                $('.selecao')
                                .addClass('hidden')
                                .find('input')
                                .val('');

                                if( $('#'+e.target.id).is(':checked') ){
                                    $('#valor-'+e.target.id).removeClass('hidden');
                                }
                            });
                        });
                    </script>
                </form>
            </li>
        <?php
            break;
            default :
        ?>
            <li class="clearfix mb5">
                <form action="produtos-precos.php" class="mb15 pull-left">
                    <b class="cor-001">Pesquisar:</b> <input type="text" name="pesquisar" size="50" autocomplete="off"/> 
                    <button type="submit" class="btn btn-primary">pesquisar</button>
                </form>
            </li>
            <li>
                <table width="100%" cellpadding="8" cellspacing="0" border="0">
                    <thead class='plano-fundo-adm-003'>
                        <tr>
                            <th><b class="cor-001" width="15px">#</b></th>
                            <th width='70%'><b class="cor-001">NOME MARCA</b></th>
                            <th width="30%" align="center"><b class="cor-001">AÇÕES</b></th>
                        </tr>
                    </thead>
                    <tbody>				
                        <?php
                        switch ($POST['acao']){
                            case 'salvar-dados':
                                $id_marca       = is_string($POST['id_marca']) && $POST['id_marca'] != '' ? addslashes((int)$POST['id_marca']) : null;
                                $preco_real     = is_string($POST['preco_real']) && $POST['preco_real'] != '' ? addslashes(dinheiro($POST['preco_real'])) : null;
                                $percentual     = is_string($POST['preco_percentual']) && $POST['preco_percentual'] != '' ? addslashes((float)$POST['preco_percentual']/100) : null;
                                $selecionado    = is_string($POST['seleciona']) && $POST['seleciona'] != '' ? addslashes((string)$POST['seleciona']) : null;
                                $operador       = is_string($POST['operador']) && $POST['operador'] != '' ? addslashes((string)$POST['operador']) : null;
                                    
                                $query = '';
                                /*
                                 * PRECO EM VALOR PERCENTUAL (%)
                                 */
                                if('percentual' == $selecionado){
                                    $query = "update produtos set "
                                    . "preco_venda = (preco_venda $operador ($percentual) * preco_venda), "
                                    . "preco_promo = (preco_promo $operador ($percentual) * preco_promo) "
                                    . "where id_marca = %u ";
                                }
                                /*
                                 * PRECO EM VALOR REAL (R$)
                                 */
                                if('real' == $selecionado){
                                    $query = "update produtos set "
                                    . "preco_venda = ( CASE WHEN preco_venda > '0.00' THEN (preco_venda $operador $preco_real) END ),  "
                                    . "preco_promo = ( CASE WHEN preco_promo > '0.00' THEN (preco_promo $operador $preco_real) END ) "
                                    . "where id_marca = %u ";
                                }
                                $logs = "Aleração de preços";
                                logs($log, $_SESSION['admin']['id_usuario']);
                                
                                $query = queryInjection($query, $id_marca);
                                
                                Lojas::connection()->query($conexao);
                            break;
                        }
                       
                        $where .= 'marcas.excluir = 0 ';
                        $where = $pesquisar ? sprintf('and m.marcas like "%%s%%%" ', $pesquisar) : '';
                        
                        $buscaSql   = '' 
                            . 'select m.id, m.marcas from 
                                produtos p
                                join marcas m on m.id = p.id_marca
                            where 
                                1 = 1 
                                {$where} 
                            group by 
                                m.id
                            order by 
                                m.marcas asc
                        ';

                        $i 			= 0;
                        $maximo 	= 100;	
                        $pag 		= filter_input(INPUT_GET,'pag') ? (int)$_GET['pag'] : 1; 
                        $inicio 	= ( $pag * $maximo ) - $maximo;
                        $total 		= ceil ( mysqli_num_rows( mysqli_query($conexao, $buscaSql ) ) / $maximo );

                        $buscaSql 	.= " limit {$inicio}, {$maximo}";

                        $sql 		= mysqli_query($conexao, $buscaSql );
                        while( $rs  = mysqli_fetch_assoc( $sql ) )
                        {
                        ?>
                        <tr class="formulario<?php echo $rs['id'];?> lista-zebrada in-hover">
                            <td align='center' width="15px"><?php echo $rs['id']?></td>
                            <td width="70%"><?php echo $rs['marcas'];?></td>
                            <td width="30%" align="center">
                                <a href="produtos-precos.php?acao=alterar-precos&id_marca=<?php echo $rs['id']?>" data-btn="acao" class="btn btn-secundary btn-sm">alterar preços</a>
                            </td>
                        </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </li>
            <li>
                <div class="paginacao clearfix">
                    <?php
                    if( $total > 0 )
                    {
                        if( $pag != 1 )
                        { 
                            echo "<a href='produtos-precos.php?pesquisar={$pesquisar}&pag=1'>Primeira página</a>";
                        }

                        for( $i = $pag - 10, $limiteDeLinks = $i + 20; $i <= $limiteDeLinks; ++$i )
                        {
                            if($i < 1)
                            {
                                $i = 1;
                                $limiteDeLinks = 19;
                            }

                            if($limiteDeLinks > $total)
                            {
                                $limiteDeLinks = $total; 
                                $i = $limiteDeLinks - 20;
                            }

                            if( $i < 1 )
                            {
                                $i = 1;
                                $limiteDeLinks = $total;
                            }

                            if($i == $pag)
                            {
                                echo "<span class='at plano-fundo-adm-001'>{$i}</span>";
                            }
                            else
                            {							
                                echo "<a href='produtos-precos.php?pesquisar={$pesquisar}&pag={$i}'>{$i}</a>";
                            }
                        } 

                        if( $pag != $total )
                        {  
                            if( $pag == $i && $total > 0 )
                            { 
                                echo "<span class='lipg'>Última página</span>";
                            }
                            else
                            { 
                                echo "<a href='produtos-precos.php?pesquisar={$pesquisar}&pag={$total}'>Última página</a>"; 
                            }
                        }
                    }
                    ?>
                </div>
            </li>
        <?php
        }
        ?>
    </ul>    
<?php ob_start() ?>
<script>
    JanelaModal.dialog({
        title  : 'Alterção de preços',
        width  : 395,
        height : 275
    });

    $('#janela-cadastros').on('submit', '#janela-precos', function(e){
        var action = e.target.action, DataStr = $(this).serialize();
        $.ajax({
            url      : action,
            type     : 'post',
            data     : DataStr,
            cache	 : false,
            beforeSend : function(){ infoSite( 'Aguarde...', 'info-concluido'); },
            success  : function( str ){
                var \$list = $(str).find('#atualizar-precos');
                $('#conteudos-recarregar').html( \$list );
            },
            complete : function(){
                JanelaModal.dialog('close').html('');
                infoSite( 'Salvo com sucesso...', 'info-concluido');
            },
            error 	 : function(x,t,m){ 
                console.log(x.responseText+'\\n'+t+'\\n'+m);
            }
        });
        e.preventDefault();
    });

    $('#conteudos-recarregar').on('click', '[data-btn=acao]', function(e){
        $.ajax({
            url      : e.target.href,
            cache	 : false,
            beforeSend : function(){ infoSite( 'Aguarde...', 'info-concluido'); },
            success  : function( str ){
                var \$list = $(str).find('#janela-precos');
                JanelaModal.dialog('open').html( \$list );
            },
            error 	 : function(x,t,m){ 
                console.log(x.responseText+'\\n'+t+'\\n'+m);
            }
        });
        e.preventDefault();
    });
</script>
<?php
$SCRIPT['script_manual'] .= ob_get_clean(); 
include '../rodape.php';