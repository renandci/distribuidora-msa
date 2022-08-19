<?php
/**
 * @author Renan Henrique <renan@dcisuporte.com.br>
 * @company Data Control Infomatica
 */

$GET = filter_input_array(INPUT_GET);
$POST = filter_input_array(INPUT_POST);

function status_carrinho($str){
    switch ($str) {
        case 'E': return 'Enviado';
        case 'R': return 'Recuperado';
        case 'N': 
        default:
            return 'Não Enviado';
    }
}

include 'topo.php';

if(isset($GET['acao']) && 'recuperarlista' === $GET['acao']) { 
    $sql = queryInjection(''
            . 'update carrinho set '
            . 'status = "E" where id_session = "%s"', $GET['session_id']);
    logs("Recuperou uma lista de carrinho abandonado - Session: {$GET['session_id']}");
    if(Lojas::connection()->query($sql)){
        echo '<p>Lista recuperada com sucesso!</p>'
        . '<script type="text/javascript">window.history.back();</script>';
    }

} else { ?>

    <h2>Carrinho Abandonados</h2>
    <hr/>
    <?php
    
//    echo 
    $listaCarrinhoAbandonados = ''
            . 'SELECT '
            . 'produtos.id as produtos_id, '
            . 'clientes.id as carrinho_id, '
            . 'carrinho.id_session as session_id, '
            . 'clientes.nome, '
            . 'clientes.email, '
            . 'produtos.nome_produto, '
            . 'produtos_imagens.imagem as foto1, '
            . 'carrinho.quantidade, '
            . 'carrinho.created_at as data, '
            . 'carrinho.status, '
            . 'carrinho.recuperado, '
            . 'carrinho.status_2, '
            . 'carrinho.recuperado_2 '
            . 'FROM carrinho '
            . 'INNER JOIN produtos ON produtos.id = carrinho.id_produto '
            . 'INNER JOIN produtos_imagens ON produtos_imagens.codigo_id = produtos.codigo_id and produtos_imagens.cor_id = produtos.id_cor '
            . 'INNER JOIN clientes ON clientes.id = carrinho.id_cliente '
            . 'GROUP BY produtos_id '
            
            . '';
    
    $dados = array();
    $mails = array();
    $resultCarrinhoAbandonados = Lojas::connection()->query($listaCarrinhoAbandonados);
    while($array = $resultCarrinhoAbandonados->fecth() ) {
        $dados[ $array['session_id'] ][] = $array;
        $mails[] = $array['session_id'];
    }
    $clientes = array_unique(array_values($mails));
    ?>
    <table cellspacing="0" cellpadding="8" width="100%">
        <tbody>
            <tr class="plano-fundo-adm-001 bold cor-branco">
                <td>
                    Cliente:
                </td>
                <td>
                    E-mail:
                </td>
                <td align="center">
                    Criado:
                </td>
                <td align="center">
                    Status:
                </td>
                <td align="center">
                    Recuperado 2hs:
                </td>
                <td align="center">
                    Status:
                </td>
                <td align="center">
                    Recuperado 48hs:
                </td>
                <td align="center">
                    Ações
                </td>
            </tr>
            <?php foreach ($dados as $x => $DadosRw){ ?>

                <tr class="in-hover lista-zebrada">
                    <td nowrap="nowrap" width="1%">
                        <?php echo $DadosRw[0]['nome']?>
                    </td>
                    <td>
                        <?php echo $DadosRw[0]['email']?>
                    </td>
                    <td nowrap="nowrap" width="1%" align="center">
                        <?php echo date('d/m/Y - H:i', strtotime($DadosRw[0]['data']))?>
                    </td>
                    <td nowrap="nowrap" width="1%">
                        <?php echo status_carrinho($DadosRw[0]['status']);?>

                        <?php echo $DadosRw[0]['enviado'] 
                            ? '<span class="tag-block ft11px">Enviado as '. date('d/m/Y - H:i', strtotime($DadosRw[0]['enviado'])) .'</span>' : ''; ?>

                        <?php echo $DadosRw[0]['visualizado'] 
                            ? '<span class="tag-block ft11px">Recuperado as ' . date('d/m/Y - H:i', strtotime($DadosRw[0]['visualizado'])).'</span>' : ''; ?>
                    </td>
                    <td nowrap="nowrap" width="1%" align="center">
                        <?php echo $DadosRw[0]['recuperado'] ? date('d/m/Y - H:i', strtotime($DadosRw[0]['recuperado'])) : '?'?>
                    </td>                    
                    <td nowrap="nowrap" width="1%">
                        <?php echo status_carrinho($DadosRw[0]['status_2']);?>

                        <?php echo $DadosRw[0]['enviado_2'] 
                            ? '<span class="tag-block ft11px">Enviado as '. date('d/m/Y - H:i', strtotime($DadosRw[0]['enviado_2'])) .'</span>' : ''; ?>

                        <?php echo $DadosRw[0]['visualizado_2'] 
                            ? '<span class="tag-block ft11px">Recuperado as ' . date('d/m/Y - H:i', strtotime($DadosRw[0]['visualizado_2'])).'</span>' : ''; ?>
                    </td>
                    <td nowrap="nowrap" width="1%" align="center">
                        <?php echo $DadosRw[0]['recuperado_2'] ? date('d/m/Y - H:i', strtotime($DadosRw[0]['recuperado_2'])) : '?'?>
                    </td>
                    <td nowrap="nowrap" width="1%" align="center">
<!--                        <a 
                            href="marketing-carrinho.php?id_session=<?php echo $DadosRw[0]['id_session']?>&acao=recuperarlista" 
                            class="btn btn-danger-default btn-sm<?php echo $DadosRw[0]['status'] == 'R' ? ' hidden':''?>">recuperar carrinho</a>-->

                        <a 
                            href="marketing-carrinho.php?id_session=<?php echo $DadosRw[0]['id_session']?>&acao=verlista" 
                            class="btn btn-primary-default btn-sm" data-id="session-id-<?php echo $DadosRw[0]['id_session']?>">ver lista de produtos</a>
                    </td>
                </tr>
                <tr>
                    <td colspan="7" id="session-id-<?php echo $DadosRw[0]['id_session']?>" class="tag-hidden">
                        <table  cellspacing="0" cellpadding="0" width="100%">
                            <tr>
                                <td nowrap="nowrap" width="75px" class="bold" colspan="2">
                                    Produto
                                </td>
                                <td nowrap="nowrap" width="1%" class="bold" align="center">
                                    QTDE
                                </td>
                            </tr>
                        <?php foreach ($DadosRw as $i => $values){ ?>
                            <tr>
                                <td nowrap="nowrap" width="75px">
                                    <img src="<?php echo URL_VIEWS_BASE_PUBLIC_IMAGENS?>imgs/produtos/smalls/<?php echo $values['foto1']?>" width="175px"/>
                                </td>
                                <td>
                                    <p><?php echo $values['nome_produto']?></p>
                                </td>
                                <td nowrap="nowrap" width="1%" align="center">
                                    <?php echo $values['quantidade']?>
                                </td>
                            </tr>
                        <?php } ?>
                        </table>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    
<?php }
$SCRIPT['script_manual'] .= '
    
    JanelaModal.dialog({
        title : "Lista de Produtos - Carrinho",
        width: 800,
        height: 532
    });
    $("[href]").on("click", function(){
        var $this = $(this);

        if( $this.attr("href").indexOf("verlista") !== -1 ) {
            var $list = $( "#" + $this.attr("data-id") ).html();
            JanelaModal.dialog("open").html( $list );
            return false;
        }
        
        if( $this.attr("href").indexOf("recuperarlista") !== -1 ) {
            if(confirm("Deseja recuperar o carrinho!")){
                return true;
            }
            return false;
        }
    });
    
';
include 'rodape.php';