<?php
include '../topo.php';

/**
 * Cadastra
 */
if( isset($GET['acao']) && $GET['acao'] === 'cadastrar' ) {
    // Arquivo temporario
    // $file = current($_FILES);
	
    // Cadastra os dados no banco
    $PlaquinhaStatus = PlaquinhaStatus::action_cadastrar_editar($POST, 'cadastrar', 'placa_text');
	
    header("Location: /adm/produtos/produtos-plaquinhas.php?codigo_id={$GET['codigo_id']}");
    return;
}

/**
 * Editar
 */
if( isset($GET['acao']) && $GET['acao'] === 'editar' ) {
    
    // edita os dados no banco
    $PlaquinhaStatus = PlaquinhaStatus::action_cadastrar_editar($POST, 'alterar', 'placa_text');
        
    header("Location: /adm/produtos/produtos-plaquinhas.php?codigo_id={$GET['codigo_id']}");
    return;
}

/**
 * Excluir
 */
if( isset($GET['acao']) && $GET['acao'] === 'excluir' ) {
    PlaquinhaStatus::action_cadastrar_editar(['PlaquinhaStatus' => [ $GET['id'] => ['excluir' => 1] ] ], 'excluir', 'placa_text');
    header('Location: /adm/produtos/produtos-plaquinhas.php?codigo_id=' . $GET['codigo_id']);
    return;
}

/**
 * Remover em massa
 */
if( count( $POST['PlaquinhaStatus'] ) > 0 ) {

	$set = [];
	$PlaquinhaStatus = [];
	
	PlaquinhaStatus::update_all(['set' => 'ativo=0']);
    foreach($POST['PlaquinhaStatus'] as $ids => $vls) {
        $set[] = $ids;
		PlaquinhaStatus::action_cadastrar_editar(['PlaquinhaStatus' => [ $ids => ['ativo' => $vls['excluir'] ] ] ], 'alterar', 'placa_text');
    }

    Lojas::connection()->query(sprintf('ALTER TABLE `produtos` CHANGE `placastatus` `placastatus` SET("%s")', implode('","', $set)));
    header('Location: /adm/produtos/produtos-plaquinhas.php?codigo_id=' . $GET['codigo_id']);
    return;
}

$TOTAL_CADASTROS_GERAL = PlaquinhaStatus::count(['conditions' => ['loja_id=?', $CONFIG['loja_id']]]);
$TOTAL_CADASTROS_ATIVOS = PlaquinhaStatus::count(['conditions'=> ['excluir=? and loja_id=?', 0, $CONFIG['loja_id']]]);
$TOTAL_CADASTROS_DESATIVOS = PlaquinhaStatus::count(['conditions'=> ['excluir=? and loja_id=?', 1, $CONFIG['loja_id']]]);

$q = isset($GET['q']) && $GET['q'] != '' ? $GET['q'] : (isset($POST['q']) && $POST['q'] != '' ? $POST['q']:'');
?>

<div class="panel panel-default">
    <div class="panel-heading panel-store text-uppercase ocultar">
        Plaquinhas de Status:
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
						<form action="/adm/produtos/produtos-plaquinhas.php?codigo_id=<?php echo $GET['codigo_id']?>&opcoes_id=<?php echo $GET['opcoes_id']?>&status=<?php echo $GET['status']?>" method="post" class="formulario-produtos/produtos-plaquinhas">
							<div class="clearfix mb15" style="line-height: 17px;">
								<span class="cor-001">Total de <span class="ft18px"><?php echo $TOTAL_CADASTROS_ATIVOS?></span> cadastrados</span> 
							</div>
							<input name="q" type="text" class="w50"/>
							<button type="submit" class="btn btn-primary">
								<i class="fa fa-search"></i>
							</button>
							<button class="btn btn-primary" type="button" onclick="$('.ocultar, .formulario00').slideToggle(0);" <?php echo _P( 'produtos-plaquinhas', $_SESSION['admin']['id_usuario'], 'incluir' )?>>cadastrar</button>
                            <button class="btn btn-danger" type="button" data-action="btn-add-varios" data-href="/adm/produtos/produtos-plaquinhas.php?codigo_id=<?php echo $GET['codigo_id']?>" <?php echo _P('produtos-plaquinhas', $_SESSION['admin']['id_usuario'], 'excluir')?>>
                                adicionar seleção
                            </button>
						</form>
					</td>
				</tr>
				<tr class="ocultos formulario00">
					<td colspan="5" class="clearfix">
						<form class="formulario-produtos/produtos-plaquinhas col-lg-8 col-lg-offset-2" action="/adm/produtos/produtos-plaquinhas.php?codigo_id=<?php echo $GET['codigo_id']?>&acao=cadastrar" method="post" enctype="multipart/form-data">
							<fieldset class="clearfix">
								<div class="mb15">
									<span class="show mb5 bold">Nome da Plaquinha:</span>
									<span class="show mb15">
                                        <input type="text" name="PlaquinhaStatus[0][placa_text]" style="max-width: 320px" autocomplete="off"/>
									</span>
									<span class="show mb5 bold">Ordem:</span>
									<span class="show">
										<input type="text" name="PlaquinhaStatus[0][ordem]" style="width: 120px" autocomplete="off"/>
									</span>
								</div>
                                <fieldset class="mb15 show">
                                    <div class="row">
                                        <div class="col-md-7">
                                            <span class="show ft18px bold">Selecione as Cores:</span>
                                            <span class="row">
                                                <span class="col-md-6">
                                                    <span class="show bold">Cor do Texto:</span>
                                                    <input type="text" class="input-box-color" name="PlaquinhaStatus[0][placa_color]" id="corA" style="background-color:tranparent" onchange="$(this).css({'background-color' : '#' + this.value });" autocomplete="off"/>
                                                </span>
                                                <span class="col-md-6">
                                                    <span class="show bold">Cor de Fundo:</span>
                                                    <input type="text" value="" class="input-box-color" name="PlaquinhaStatus[0][placa_background]" id="corB" style="background-color:transparent" onchange="$(this).css({'background-color' : '#' + this.value });" autocomplete="off"/>
                                                </span>
                                            </span>    
                                        </div>
                                    </div>
								</fieldset>
                                <div class="mb15 mt15">
                                    <button type="submit" class="btn btn-primary btn-cadastros-cores"<?php echo _P('produtos-plaquinhas',$_SESSION['admin']['id_usuario'],'incluir')?>>
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
					<td>Placa</td>
					<td class="text-center">Ativo</td>
					<td class="text-center">Ordem</td>
					<td class="text-center">Ações</td>
				</tr>
				<?php
                $arr_id = [];
                if(isset($GET['codigo_id']) && $GET['codigo_id'] > 0)
                    foreach(Produtos::all(['conditions' => ['codigo_id=? and status=0 and excluir=0', $GET['codigo_id']]]) as $p)
                        $arr_id[] = $p->id_cor;

                $i = 0;
				
				$maximo = 25;
				
				$pag = isset($GET['pag']) && $GET['pag'] != '' ? $GET['pag']:1;
				
				$inicio = (($pag * $maximo) - $maximo);
				
				$conditions = array();
                $conditions['conditions'] = sprintf('loja_id=%u ', $CONFIG['loja_id']);
             
                if(!empty($q)) {
                    $conditions['conditions'] .= sprintf('and placa_text like "%%%s%%" ', $q);
                }
				
				$total = ceil(PlaquinhaStatus::count($conditions) / $maximo);
				
				$conditions['order'] = 'placa_text asc, id desc';
				
				$conditions['limit'] = $maximo;
				
				$conditions['offset'] = ($maximo * ($pag - 1));
				
				$PlaquinhaStatus = PlaquinhaStatus::all( $conditions );
				
				foreach ( $PlaquinhaStatus as $rws ) { ?>
				<tr class="in-hover formulario<?php echo $rws->id;?> ocultar teste_<?php echo $i?><?php echo $rws->excluir == 1?' hidden':null?>" <?php echo ($i % 2 && $rws->excluir != 1) ? 'style="background-color:#f3f3f3"': ''?>>
					<td nowrap="nowrap" width="1%">
                        <input type="checkbox" name="PlaquinhaStatus[<?php echo $rws->id;?>][excluir]" id="label<?php echo $rws->id?>" value="1" data-action="selecionados-exclusao"/>
						<label for="label<?php echo $rws->id?>" class="input-checkbox"></label>
					</td>
					<td>
						<span class="btn btn-sm" style="color: #<?php echo $rws->placa_color?>;background-color: #<?php echo $rws->placa_background?>""><?php echo $rws->placa_text;?></span>
                        <?php echo !empty($arr_id) && in_array($rws->id, $arr_id) ? '<span class="pull-right btn btn-info btn-xs ft10px">adicionado</span>': null?>
					</td>
					<td nowrap="nowrap" width="1%" class="text-center">
						<?php echo $rws->ativo?'Sim':'Não';?>
					</td>
					<td nowrap="nowrap" width="1%" class="text-center">
						<?php echo $rws->ordem;?>
					</td>
					<td nowrap="nowrap" width="1%" class="text-center">
						<a href="javascript: void(0);" class="btn btn-primary btn-sm" onclick="$('.ocultar, .formulario<?php echo $rws->id?>').slideToggle(0);" <?php echo _P( 'produtos/produtos-plaquinhas', $_SESSION['admin']['id_usuario'], 'alterar' )?>>editar</a>
                        
						<a href="/adm/produtos/produtos-plaquinhas.php?codigo_id=<?php echo $GET['codigo_id']?>&id=<?php echo $rws->id?>&acao=excluir" class="btn btn-danger btn-sm btn-excluir-modal" <?php echo _P('produtos/produtos-plaquinhas', $_SESSION['admin']['id_usuario'], 'excluir')?>>excluir</a>
					</td>
				</tr>
				<tr class="formulario<?php echo $rws->id;?> ocultos" id="formulario<?php echo $rws->id;?>">
					<td colspan="5">
						<form class="formulario-produtos/produtos-plaquinhas col-lg-8 col-lg-offset-2" action="/adm/produtos/produtos-plaquinhas.php?codigo_id=<?php echo $GET['codigo_id']?>&acao=editar" method="post" enctype="multipart/form-data">
							<fieldset class="clearfix">
								<div class="row mb15">
                                    <div class="col-md-8">
                                        <span class="show mb5 bold">Nome da Plaquinha:</span>
                                        <span class="show mb15">
                                            <input type="text" value="<?php echo $rws->placa_text?>" name="PlaquinhaStatus[<?php echo $rws->id;?>][placa_text]" style="width:320px" autocomplete="off"/>
                                        </span>
                                    </div>
                                    <div class="col-md-5">
                                        <span class="show mb5 bold">Ordem:</span>
                                        <span class="show">
                                            <input type="text" value="<?php echo $rws->ordem?>" name="PlaquinhaStatus[<?php echo $rws->id;?>][ordem]" style="width: 120px" autocomplete="off"/>
                                        </span>
                                    </div>
								</div>
                                <fieldset class="mb15 show">
                                    <div class="row">
                                        <div class="col-md-7">
                                            <span class="show ft18px bold">Selecione as Cores:</span>
                                            <span class="row">
                                                <span class="col-md-6">
                                                    <span class="show bold">Cor do Texto:</span>
                                                    <input type="text" value="<?php echo $rws->placa_color?>" class="input-box-color" name="PlaquinhaStatus[<?php echo $rws->id;?>][placa_color]" id="corA<?php echo $rws->id?>" style="background-color:#<?php echo $rws->placa_color?>" onchange="$(this).css({'background-color' : '#' + this.value });" autocomplete="off"/>
                                                </span>
                                                <span class="col-md-6">
                                                    <span class="show bold">Cor de Fundo:</span>
                                                    <input type="text" value="<?php echo $rws->placa_background?>" class="input-box-color" name="PlaquinhaStatus[<?php echo $rws->id;?>][placa_background]" id="corB<?php echo $rws->id?>" style="background-color:#<?php echo $rws->placa_background?>" onchange="$(this).css({'background-color' : '#' + this.value });" autocomplete="off"/>
                                                </span>
                                            </span>    
                                        </div>
                                    </div>
								</fieldset>

                                <div class="mb15 mt15">
                                    <button type="submit" class="btn btn-primary btn-cadastros-cores" <?php echo _P('produtos-plaquinhas', $_SESSION['admin']['id_usuario'], 'alterar')?>>
                                        salvar
                                    </button>
                                    <button type="button" class="btn btn-danger" onclick="$('.ocultar, .formulario<?php echo $rws->id;?>').slideToggle(0);">
                                        cancela
                                    </button>
                                </div>
							</fieldset>
						</form>
						<script>
                            <?php ob_start(); ?>
                            $('#corA<?php echo $rws->id?>').ColorPicker({
                                color: '#<?php echo $rws->placa_color?>',
                                onChange: function (hsb, hex, rgb) {
                                    $('#corA<?php echo $rws->id?>').css({'background-color' : '#' + hex }).val( hex );
                                }
                            });
                            $('#corB<?php echo $rws->id?>').ColorPicker({
                                color: '#<?php echo $rws->placa_background?>',
                                onChange: function (hsb, hex, rgb) {
                                    $('#corB<?php echo $rws->id?>').css({ 'background-color' : '#' + hex }).val( hex );
                                }
                            });
                            <?php $script_color .= ob_get_clean(); ?>
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
										echo sprintf('<a href="/adm/produtos/produtos-plaquinhas.php?%s" class="btn-paginacao">%s</a>', $data, $i);

                                        // echo "<a href=\"/adm/produtos/produtos-plaquinhas.php?codigo_id={$GET['codigo_id']}&opcoes_id={$GET['opcoes_id']}&status={$GET['status']}&pag={$i}\" class='btn-paginacao'>{$i}</a>";
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
include '../rodape.php';
