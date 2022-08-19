<?php
include 'topo.php';

/**
 * Cadastra
 */
if( isset($GET['acao']) && $GET['acao'] === 'cadastrar' ) {
    // Arquivo temporario
    // $file = current($_FILES);
	
    // Cadastra os dados no banco
    $Cores = Cores::action_cadastrar_editar($POST, 'cadastrar', 'id');
	
	if( $file['size'] == 0 ) {
		header("Location: /adm/cores.php?cor_id={$GET['cor_id']}&codigo_id={$GET['codigo_id']}&status={$GET['status']}&opcoes_id={$GET['opcoes_id']}");
        return;
    }
	
    
    // // caminho dos arquivos
    // $dir_temp = '../' . URL_VIEWS_BASE_PUBLIC_UPLOAD . 'imgs/icon/';

    // // Verificar se é uma requisição via HTTP FILES
    // if( is_uploaded_file( $file['tmp_name'] ) )
    // {
    //     // Verifica se o caminho existe
    //     if( ! is_dir( $dir_temp ) ) {
    //         // Tenta criar um diretorio de caminho
    //         if( ! mkdir($dir_temp) ) {
    //         }
    //     }

    //     // extensão pré carregada
    //     $ext = strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );
    //     $ext_pre = ['png', 'jpg', 'jpeg', 'gif'];

    //     switch ($file['error'])
    //     {
    //         case UPLOAD_ERR_OK:
    //             break;
    //         case UPLOAD_ERR_NO_FILE :
    //             exit( json_encode( [ 'mensagem' => 'Nenhum arquivo enviado.', 'error' => '1' ] ) ) ;
    //             break;
    //         case UPLOAD_ERR_INI_SIZE:
    //         case UPLOAD_ERR_FORM_SIZE:
    //             exit( json_encode( [ 'mensagem' => 'Limite de tamanho de arquivo excedido.', 'error' => '1' ] ) ) ;
    //             break;
    //         default: 
    //             exit( json_encode( [ 'mensagem' => 'Erros desconhecidos.', 'error' => '1' ] ) ) ;
    //             break;
    //     }
        
    //     // Verifica a extensão do arquivo
    //     if ( ! in_array( $ext, $ext_pre ) ) 
    //     {
    //         $json['error'] = 1;
    //         $json['mensagem'] = ''
    //                 . "Não é permitido arquivos <b>{$ext}</b><br/>"
    //                 . "Tente enviar os seguintes tipo de arquivos: "
    //                 . "<span class='show ft16px'><b>" . join($ext_pre, '</b> <b>') . "<b></span>";
    //         exit ( json_encode( $json ) ) ;
    //     }
        
    //     // nome do arquivo
    //     $name_file = "c{$Cores['id']}.{$ext}";
        
    //     // Carregar a imagem no upload
    //     $WideTmpName = WideImage\WideImage::load($file['tmp_name']);
    //     $WideTmpName->saveToFile($dir_temp . $name_file);
    //     $WideTmpName->destroy();
        
    //     Cores::action_cadastrar_editar([ 'Cores' => [ $Cores['id'] => [ 'icon' => $name_file ] ] ], 'alterar', 'id');
    //     header("Location: /adm/cores.php?cor_id={$GET['cor_id']}&codigo_id={$GET['codigo_id']}&status={$GET['status']}&opcoes_id={$GET['opcoes_id']}");
    //     return;
    // }
}

/**
 * Editar
 */
if( isset($GET['acao']) && $GET['acao'] === 'editar' ) {
    
    // edita os dados no banco
    $Cores = Cores::action_cadastrar_editar($POST, 'alterar', 'id');
        
    header("Location: /adm/cores.php?cor_id={$GET['cor_id']}&codigo_id={$GET['codigo_id']}&status={$GET['status']}&opcoes_id={$GET['opcoes_id']}");
    return;
}

/**
 * Excluir
 */
if( isset($GET['acao']) && $GET['acao'] === 'excluir' ) {
    Cores::action_cadastrar_editar([ 'Cores' => [ $GET[id] => ['excluir' => 1] ] ], 'excluir', 'nomecor');
    header('Location: /adm/cores.php?codigo_id=' . $GET['codigo_id']);
    return;
}

/**
 * Remover em massa
 */
if( count( $POST['Cores'] ) > 0 ) {
    Cores::action_cadastrar_editar($POST, 'excluir', 'nomecor');
    header('Location: /adm/cores.php?codigo_id=' . $GET['codigo_id']);
    return;
}

$TOTAL_CADASTROS_GERAL = Cores::count(['conditions' => ['loja_id=?', $CONFIG['loja_id']]]);
$TOTAL_CADASTROS_ATIVOS = Cores::count(['conditions'=> ['excluir=? and loja_id=?', 0, $CONFIG['loja_id']]]);
$TOTAL_CADASTROS_DESATIVOS = Cores::count(['conditions'=> ['excluir=? and loja_id=?', 1, $CONFIG['loja_id']]]);

$GET_STATUS = isset($POST['status']) && $POST['status'] != '' ? $POST['status'] : (isset( $GET['status']) && $GET['status'] != '' ? $GET['status']:'');
$GET_PESQUISAR = isset($GET['q']) && $GET['q'] != '' ? $GET['q'] : (isset($POST['q']) && $POST['q'] != '' ? $POST['q']:'');
$GET_OPCOES_ID = isset($GET['opcoes_id']) && $GET['opcoes_id'] != '' ? $GET['opcoes_id'] : (isset($POST['opcoes_id']) && $POST['opcoes_id'] != '' ? $POST['opcoes_id']:'');
?>

<div class="panel panel-default">
    <div class="panel-heading panel-store text-uppercase ocultar">
        Variações: 
        <small class="ft12px">
            <?php
            $OpcoesTipo = OpcoesTipo::all(['conditions' => ['excluir=? and loja_id=?', 0, $CONFIG['loja_id']]]);
			
            $resultVariacao = OpcoesTipo::find_by_sql('select * from opcoes_tipo where excluir=0 and loja_id=? and exists(select 1 from cores where cores.opcoes_id = opcoes_tipo.id)', [$CONFIG['loja_id']]);
            if( count($resultVariacao) > 0 ) { 
                foreach( $resultVariacao as $var ) {
                    $var = $var->to_array();
                    $tipos[] = ""
                            . "<a href='/adm/cores.php?q={$GET['q']}&codigo_id={$GET['codigo_id']}&status={$GET['status']}&opcoes_id={$var['id']}'>"
                            . "{$var['tipo']}"
                            . "</a>";
                } 
                echo implode(' / ', $tipos);
            } 
            ?>
        </small>
    </div>
	<div id="div-edicao" class="panel-body">
		<style>	
            body{ background-color: #f1f1f1 }
			.ocultos{ display: none; }
		</style>
		
		<table width="100%" border="0" cellpadding="8" cellspacing="0">
			<tbody>
				<tr class="ocultar">
					<td colspan="5">
						<form action="/adm/cores.php?codigo_id=<?php echo $GET['codigo_id']?>&opcoes_id=<?php echo $GET['opcoes_id']?>&status=<?php echo $GET['status']?>" method="post" class="formulario-cores">
							<div class="clearfix mb15" style="line-height: 17px;">
								<span class="cor-001">Total de <span class="ft18px"><?php echo $TOTAL_CADASTROS_ATIVOS?></span> cores cadastrados</span> 
							</div>
							<input name="q" type="text" class="w50"/>
							<button type="submit" class="btn btn-primary">
								<i class="fa fa-search"></i>
							</button>
							<button class="btn btn-primary" type="button" onclick="$('.ocultar, .formulario00').slideToggle(0);" <?php echo _P( 'cores', $_SESSION['admin']['id_usuario'], 'incluir' )?>>cadastrar</button>
                            <button class="btn btn-danger" type="button" data-action="btn-excluir-varios" data-href="/adm/cores.php?codigo_id=<?php echo $GET['codigo_id']?>&opcoes_id=<?php echo $GET['opcoes_id']?>&status=<?php echo $GET['status']?>" <?php echo _P( 'cores', $_SESSION['admin']['id_usuario'], 'excluir' )?>>
                                excluir seleção
                            </button>
						</form>
					</td>
				</tr>
				<tr class="ocultos formulario00">
					<td colspan="5" class="clearfix">
						<form class="formulario-cores col-lg-8 col-lg-offset-2" action="/adm/cores.php?codigo_id=<?php echo $GET['codigo_id']?>&opcoes_id=<?php echo $GET['opcoes_id']?>&status=<?php echo $GET['status']?>&acao=cadastrar" method="post" enctype="multipart/form-data">
							<fieldset class="clearfix">
								<div class="mb15">
                                    <span class="show mb5 bold">Opçoes:</span>
                                    <select name="Cores[0][opcoes_id]" style="width: 220px">
                                        <?php foreach ( $OpcoesTipo as $opc ) { $opc = $opc->to_array(); ?>
                                        <option value="<?php echo $opc['id']?>"<?php echo $opc['id']== $rs['opcoes_id']? ' selected' : ''?>>
                                            <?php echo $opc['tipo']?>
                                        </option>
                                        <?php } ?>
                                    </select>
                                </div>
								<div class="mb15">
									<span class="show mb5 bold">Nome da variação:</span>
									<span class="show mb15">
                                        <input type="text" name="Cores[0][nomecor]" style="max-width: 320px"/>
									</span>
									
									<span class="show mb5 bold">Ordem:</span>
									<span class="show">
										<input type="text" name="Cores[0][ordem]" style="width: 120px"/>
									</span>
								</div>
                                
                                <fieldset class="mb15 show">
                                    <div class="row">
                                        <div class="col-md-7">
                                            <span class="show ft18px bold">Selecione as cores:</span>
                                            <span class="row">
                                                <span class="col-md-6">
                                                    <span class="show bold">1º Cor:</span>
                                                    <input type="text" value="" class="input-box-color" name="Cores[0][cor1]" id="corA" style="background-color:tranparent" onchange="$(this).css({'background-color' : '#' + this.value });"/>
                                                </span>
                                                <span class="col-md-6">
                                                    <span class="show bold">2º Cor:</span>
                                                    <input type="text" value="" class="input-box-color" name="Cores[0][cor2]" id="corB" style="background-color:transparent" onchange="$(this).css({'background-color' : '#' + this.value });"/>
                                                </span>
                                            </span>    
                                        </div>
                                        <div class="col-md-5">
                                            <span class="show ft18px bold">Icones:</span>
                                            <small class="show">Selecione essa função para mostrar o icone da foto do produto</small>
                                            <select name="Cores[0][icon]" style="width: 100%;">
                                                <option value="0" selected>Não</option>
                                                <option value="1">Sim</option>
                                            </select>
                                        </div>
                                    </div>
								</fieldset>
                                
                                <div class="mb15 mt15">
                                    <button type="submit" class="btn btn-primary btn-cadastros-cores" <?php echo _P( 'cores', $_SESSION['admin']['id_usuario'], 'incluir' )?>>
                                        salvar
                                    </button>
                                    <button type="button" class="btn btn-danger" onclick="$('.ocultar, .formulario00').slideToggle(0);">
                                        cancela
                                    </button>
                                </div>
							</fieldset>
						</form>
					</td>
				</tr>
				<tr class="plano-fundo-adm-003 ocultar">
					<td bgcolor="#ffffff" align="center" nowrap="nowrap" width="1%">
                        <input type="checkbox" data-action="selecionados-exclusao-all" class="selecionados-exclusao-all" id="label" value=""/>
						<label for="label" class="input-checkbox"></label>
					</td>
					<td colspan="2">Cor</td>
					<td>Ordem</td>
					<td align="center">Ações</td>
				</tr>
				<?php
                $arr_id = [];

                if(isset($GET['codigo_id']) && $GET['codigo_id'] > 0)
                    foreach(Produtos::all(['conditions' => ['codigo_id=? and status=0 and excluir=0', $GET['codigo_id']]]) as $p)
                        $arr_id[] = $p->id_cor;

                $i = 0;
				
				$maximo = 25;
				
				$pag = isset( $GET['pag'] ) && $GET['pag'] != '' ? $GET['pag'] : 1;
				
				$inicio = (($pag * $maximo) - $maximo);
				
				$conditions = array();
				
				$conditions['select'] = 'cores.id, cores.opcoes_id, opcoes_tipo.tipo, cores.nomecor, cores.cor1, cores.cor2, cores.icon, cores.ordem';
				
				$conditions['conditions'] = sprintf('cores.excluir=0 and cores.loja_id=%u ', $CONFIG['loja_id']);
				
				$where .= isset( $GET_OPCOES_ID ) && $GET_OPCOES_ID != '' 
					? queryInjection('and cores.opcoes_id=%u ', $GET_OPCOES_ID)  : '';
				
				$conditions['conditions'] .= isset( $GET_PESQUISAR ) && $GET_PESQUISAR != '' 
					? queryInjection('and cores.nomecor like "%%%s%%" ', $GET_PESQUISAR)  : '';
					
				// $conditions['conditions'] .= isset($GET['codigo_id']) && $GET['codigo_id'] > 0 
				// 	? queryInjection(' AND cores.id NOT IN(' 
				// 		. 'SELECT produtos.id_cor ' 
				// 			. 'FROM produtos ' 
				// 				. 'WHERE produtos.codigo_id = %u AND produtos.excluir = 0) ', $GET['codigo_id']) : '';
				
				$conditions['joins'] = 'INNER JOIN opcoes_tipo ON (opcoes_tipo.id=cores.opcoes_id)';
				
				$total = ceil( Cores::count($conditions) / $maximo );
				
				$conditions['order'] = 'cores.nomecor asc, cores.ordem desc';
				
				$conditions['limit'] = $maximo;
				
				$conditions['offset'] = ($maximo * ($pag - 1));
				
				$result = Cores::all( $conditions );
				
				foreach ( $result as $rs ) { $rs = $rs->to_array() ?>
				<?php if( $rs['opcoes_id'] != $opcoes_id ) { $opcoes_id = $rs['opcoes_id']; ?>
                <tr class="in-hover formulario<?php echo $rs['id'];?> ocultar">
                    <td colspan="5" class="ft20px">
                        <?php 
                        echo $rs['tipo'];
                        $tipo_text = $rs['tipo'];
                        $i=1;
                        ?>
                    </td>
                </tr>
                <?php } ?>
				<tr class="in-hover formulario<?php echo $rs['id'];?> ocultar teste_<?php echo $i?>" <?php echo ($i % 2) ? 'style="background-color:#f3f3f3"': ''?>>
					<td nowrap="nowrap" width="1%">
                        <input type="checkbox" name="Cores[<?php echo $rs['id'];?>][excluir]" id="label<?php echo $rs['id']?>" value="1" data-action="selecionados-exclusao"/>
						<label for="label<?php echo $rs['id']?>" class="input-checkbox"></label>
					</td>
					<td nowrap="nowrap" width="1%">
						<span class="cx-cor-relativa show">
							<span class="cor-style-1" style="background-color: #<?php echo $rs['cor1']?>">
								<span class="cor-style-2" style="border-bottom-color: #<?php echo $rs['cor2']?>"></span>
							</span>
						</span>
					</td>
					<td>
						<?php echo $rs['nomecor'];?>
                        <?php echo !empty($arr_id) && in_array($rs['id'], $arr_id) ? '<span class="pull-right btn btn-info btn-xs ft10px">adicionado</span>': null?>
					</td>
					<td nowrap="nowrap" width="1%">
						<?php echo $rs['ordem'];?>
					</td>
					<td nowrap="nowrap" width="1%" align="center">
						<!--
						<a href="/adm/cores.php?codigo_id=<?php echo $GET['codigo_id']?>&opcoes_id=<?php echo $GET['opcoes_id']?>&status=<?php echo $GET['status']?>&corid=<?php echo $rs['id']?>" class="btn btn-warning btn-sm btn-adicionar-cor<?php echo '' == $GET['codigo_id'] 
						? ' hidden' : ''?>" <?php echo _P( 'cores', $_SESSION['admin']['id_usuario'], 'incluir' )?>>adicionar</a>
						-->
						
						<a href="javascript: void(0);" class="btn btn-primary btn-sm" onclick="$('.ocultar, .formulario<?php echo $rs['id']?>').slideToggle(0);" <?php echo _P( 'cores', $_SESSION['admin']['id_usuario'], 'alterar' )?>>editar</a>
                        
						<a href="/adm/cores.php?codigo_id=<?php echo $GET['codigo_id']?>&opcoes_id=<?php echo $GET['opcoes_id']?>&status=<?php echo $GET['status']?>&id=<?php echo $rs['id']?>&acao=excluir" class="btn btn-danger btn-sm btn-excluir-modal" <?php echo _P( 'cores', $_SESSION['admin']['id_usuario'], 'excluir' )?>>excluir</a>
					</td>
				</tr>
				
				<tr class="formulario<?php echo $rs['id'];?> ocultos" id="formulario<?php echo $rs['id'];?>">
					<td colspan="5">
						<form class="formulario-cores col-lg-8 col-lg-offset-2" action="/adm/cores.php?codigo_id=<?php echo $GET['codigo_id']?>&acao=editar" method="post" enctype="multipart/form-data">
							<fieldset class="clearfix">
								<div class="mb15" style="width: 100%;">
                                    <span class="show mb5 bold">Opçoes:</span>
                                    <select name="Cores[<?php echo $rs['id'];?>][opcoes_id]" style="width: 220px">
                                        <?php foreach ( $OpcoesTipo as $opc ) { $opc = $opc->to_array(); ?>
                                        <option value="<?php echo $opc['id']?>"<?php echo $opc['id']== $rs['opcoes_id']? ' selected' : ''?>>
                                            <?php echo $opc['tipo']?>
                                        </option>
                                        <?php } ?>
                                    </select>
                                </div>
								<div class="row mb15">
                                    <div class="col-md-8">
                                        <span class="show mb5 bold">Nome <?php echo $tipo_text?>:</span>
                                        <span class="show mb15">
                                            <input type="text" value="<?php echo $rs['nomecor']?>" name="Cores[<?php echo $rs['id'];?>][nomecor]" style="width: 320px"/>
                                        </span>
                                    </div>
                                    <div class="col-md-5">
                                        <span class="show mb5 bold">Ordem:</span>
                                        <span class="show">
                                            <input type="text" value="<?php echo $rs['ordem']?>" name="Cores[<?php echo $rs['id'];?>][ordem]" style="width: 120px"/>
                                        </span>
                                    </div>
								</div>
                                <fieldset class="mb15 show">
                                    <div class="row">
                                        <div class="col-md-7">
                                            <span class="show ft18px bold">Selecione as cores:</span>
                                            <span class="row">
                                                <span class="col-md-6">
                                                    <span class="show bold">1º Cor:</span>
                                                    <input type="text" value="<?php echo $rs['cor1']?>" class="input-box-color" name="Cores[<?php echo $rs['id'];?>][cor1]" id="corA<?php echo $rs['id']?>" style="background-color:#<?php echo $rs['cor1']?>" onchange="$(this).css({'background-color' : '#' + this.value });"/>
                                                </span>
                                                <span class="col-md-6">
                                                    <span class="show bold">2º Cor:</span>
                                                    <input type="text" value="<?php echo $rs['cor2']?>" class="input-box-color" name="Cores[<?php echo $rs['id'];?>][cor2]" id="corB<?php echo $rs['id']?>" style="background-color:#<?php echo $rs['cor2']?>" onchange="$(this).css({'background-color' : '#' + this.value });"/>
                                                </span>
                                            </span>    
                                        </div>
                                        <div class="col-md-5">
                                            <span class="show ft18px bold">Icones:</span>
                                            <small class="show">Selecione essa função para mostrar o icone da foto do produto</small>
                                            <select name="Cores[<?php echo $rs['id'];?>][icon]" style="width: 100%;">
                                                <option value="0"<?php echo $rs['icon'] ? ' selected':null?>>Não</option>
                                                <option value="1"<?php echo $rs['icon'] ? ' selected':null?>>Sim</option>
                                            </select>
                                        </div>
                                    </div>
								</fieldset>

                                <div class="mb15 mt15">
                                    <button type="submit" class="btn btn-primary btn-cadastros-cores" <?php echo _P( 'cores', $_SESSION['admin']['id_usuario'], 'alterar' )?>>
                                        salvar
                                    </button>
                                    <button type="button" class="btn btn-danger" onclick="$('.ocultar, .formulario<?php echo $rs['id'];?>').slideToggle(0);">
                                        cancela
                                    </button>
                                </div>
							</fieldset>
						</form>
						<script>
                            <?php ob_start(); ?>
                            $('#corA<?php echo $rs['id']?>').ColorPicker({
                                color: '#<?php echo $rs['cor1']?>',
                                onChange: function (hsb, hex, rgb) {
                                    $('#corA<?php echo $rs['id']?>').css({'background-color' : '#' + hex }).val( hex );
                                }
                            });
                            $('#corB<?php echo $rs['id']?>').ColorPicker({
                                color: '#<?php echo $rs['cor2']?>',
                                onChange: function (hsb, hex, rgb) {
                                    $('#corB<?php echo $rs['id']?>').css({ 'background-color' : '#' + hex }).val( hex );
                                }
                            });
                            <?php
                            $script_color .= ob_get_clean();
                            ?>
						</script>
					</td>
				</tr>
				<?php ++$i; } ?>
				<tr class="ocultar">
					<td colspan="5">
						<div class="paginacao paginacao-add">
							<?php
							if( $total > 0 )
							{
								for( $i = $pag - 5, $limiteDeLinks = $i + 10; $i <= $limiteDeLinks; ++$i )
								{
									if($i < 1)
									{
										$i = 1;
										$limiteDeLinks = 9;
									}
								
									if($limiteDeLinks > $total)
									{
										$limiteDeLinks = $total; 
										$i = $limiteDeLinks - 10;
									}

									if($i < 1)
									{
										$i = 1;
										$limiteDeLinks = $total;
									}
									
									if($i == $pag) {
										echo "<span class=\"at plano-fundo-adm-001\">{$i}</span>";
									}
									else {
									    $data = http_build_query(array_replace($GET, ['pag' => $i]));
										echo sprintf('<a href="/adm/cores.php?%s" class="btn-paginacao">%s</a>', $data, $i);

                                        // echo "<a href=\"/adm/cores.php?codigo_id={$GET['codigo_id']}&opcoes_id={$GET['opcoes_id']}&status={$GET['status']}&pag={$i}\" class='btn-paginacao'>{$i}</a>";
									}
								}
							}
							?>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
        <script> 
            <?php ob_start(); ?>
			$(function(){
				$("input.input-box-color").css({
					"border": "none",
					"border-color": "ciano",
					"border-style": "solid",
					"border-width": "thin",
					"cursor": "pointer",
					"width": "145px",
					"height": "45px",
//					"text-indent": "-99999px"
				});	
                
				$('#corA').ColorPicker({
					color: '',
					onChange: function (hsb, hex, rgb) {
						$('#corA').css({ "background-color" : '#' + hex }).val( hex );
					}
				});
                
				$('#corB').ColorPicker({
					color: '',
					onChange: function (hsb, hex, rgb) {
						$('#corB').css({ "background-color" : '#' + hex }).val( hex );
					}
				});
                <?php echo $script_color;?>
			});
            <?php
            $script = ob_get_clean();
            
            $JSqueeze = new Patchwork\JSqueeze();
            $content = $JSqueeze->squeeze($script, true, false, false);
            echo $content;
            ?>
		</script>
	</div>
</div>
<script>
    $(document).on("click", "a", function(){
        var href = this.href || e.target.href;		
        if( href.search('excluir') > '0')
            if( ! confirm("Deseja realmente excluir!") ) return false;

    });
</script>
<?php
include 'rodape.php';
