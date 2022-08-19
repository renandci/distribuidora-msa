<?php
include 'topo.php';

/**
 * Cadastra
 */
if( isset($GET['acao']) && $GET['acao'] === 'cadastrar' ) {
    
    // Cadastra os dados no banco
    $Tamanhos = Tamanhos::action_cadastrar_editar($POST, 'cadastrar', 'id');
    header("Location: /adm/tamanhos.php?cor_id={$GET['cor_id']}&codigo_id={$GET['codigo_id']}&status={$GET['status']}&opcoes_id={$GET['opcoes_id']}");
    return;
	
	// // Arquivo temporario
    // $file = current($_FILES);
	
	// if( $file['size'] == 0 ) {
    // }
	
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
    //     $name_file = "t{$Tamanhos['id']}.{$ext}";
        
    //     // Carregar a imagem no upload
    //     $WideTmpName = WideImage\WideImage::load($file['tmp_name']);
    //     $WideTmpName->saveToFile($dir_temp . $name_file);
    //     $WideTmpName->destroy();
        
    //     Tamanhos::action_cadastrar_editar([ 'Tamanhos' => [ $Tamanhos['id'] => [ 'icon' => $name_file ] ] ], 'alterar', 'id');
    //     header("Location: /adm/tamanhos.php?cor_id={$GET['cor_id']}&codigo_id={$GET['codigo_id']}&status={$GET['status']}&opcoes_id={$GET['opcoes_id']}");
    //     return;
    // }
}

/**
 * Editar
 */
if( isset($GET['acao']) && $GET['acao'] === 'editar' ) {
	
	// edita os dados no banco
    $Tamanhos = Tamanhos::action_cadastrar_editar($POST, 'alterar', 'id');
    
	// // Arquivo temporario
    // $file = current($_FILES);
    
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
    //         exit ( json_encode( $json ) );
    //     }
        
    //     // nome do arquivo
    //     $name_file = "t{$Tamanhos['id']}.{$ext}";
        
    //     // tenta recarrregar a imagem do srvidor
    //     $name_glob = glob( $dir_temp . "t{$Tamanhos['id']}.{jpg,jpeg,png,gif}", GLOB_BRACE );
    //     $name_file_temp = end( $name_glob );

    //     // verifica se há imagem já upada no servidor
    //     if( count( $name_glob ) > 0 ) {
    //         // verfica se realmente existe
    //         if( file_exists( $name_file_temp ) ) {
    //             // pega a extensao do arquivo
    //             $ext_temp = strtolower( pathinfo( $name_file_temp, PATHINFO_EXTENSION ) );

    //             // se as extensões forem diferentes
    //             // tenta remover a antiga do servidor
    //             if( $ext_temp !== $ext ) {
    //                 if( ! unlink( $name_file_temp ) ) {
    //                 } 
    //             }
    //         } 
    //     }
        
    //     // Carregar a imagem no upload
    //     $WideTmpName = WideImage\WideImage::load( $file['tmp_name'] );
    //     $WideTmpName->saveToFile($dir_temp . $name_file);
    //     $WideTmpName->destroy();
		
    //     Tamanhos::action_cadastrar_editar([ 'Tamanhos' => [ $Tamanhos['id'] => [ 'icon' => $name_file ] ] ], 'alterar', 'id');
    //     header("Location: /adm/tamanhos.php?cor_id={$GET['cor_id']}&codigo_id={$GET['codigo_id']}&status={$GET['status']}&opcoes_id={$GET['opcoes_id']}");
    //     return;
    // }
    
    header("Location: /adm/tamanhos.php?cor_id={$GET['cor_id']}&codigo_id={$GET['codigo_id']}&status={$GET['status']}&opcoes_id={$GET['opcoes_id']}");
    return;
}

// // Remover icones
// if( isset($GET['acao']) && $GET['acao'] === 'excluir_imagem' ) : 
//     // tenta remover a imagem do srvidor
//     $dir_temp = '../' . URL_VIEWS_BASE_PUBLIC_UPLOAD . 'imgs/icon/t';
//     $name_file = current( glob( $dir_temp . $GET['id'] . '.{jpg,jpeg,png,gif}', GLOB_BRACE ) );
    
//     if( file_exists( $name_file ) ) :
//         // remove o arquivo
//         if( ! unlink( $name_file ) ) :
//             echo '<p>Não consegui remover sua imagem!</p>';
//         else :
//             echo '<p>Imagem removida com sucesso!</p>';
//         endif;
//     endif;
    
//     Tamanhos::action_cadastrar_editar([ 'Tamanhos' => [ $GET['id'] => ['icon' => NULL] ] ], 'alterar', 'id');
//     header("Location: /adm/tamanhos.php?cor_id={$GET['cor_id']}&codigo_id={$GET['codigo_id']}&status={$GET['status']}&opcoes_id={$GET['opcoes_id']}");
//     return;
// endif;

/**
 * Excluir
 */
if( isset($GET['acao']) && $GET['acao'] === 'excluir' ) {
    Tamanhos::action_cadastrar_editar([ 'Tamanhos' => [ $GET['id'] => ['excluir' => 1] ] ], 'excluir', 'nometamanho');
    header("Location: /adm/tamanhos.php?cor_id={$GET['cor_id']}&codigo_id={$GET['codigo_id']}&status={$GET['status']}&opcoes_id={$GET['opcoes_id']}");
    return;
}

/**
 * Remover em massa
 */
if( count( $POST['Tamanhos'] ) > 0 ) {
    Tamanhos::action_cadastrar_editar($POST, 'excluir', 'nometamanho');
    header("Location: /adm/tamanhos.php?cor_id={$GET['cor_id']}&codigo_id={$GET['codigo_id']}&status={$GET['status']}&opcoes_id={$GET['opcoes_id']}");
    return;
}

$TOTAL_CADASTROS_ATIVOS = Tamanhos::count(['conditions'=> ['excluir=? and loja_id=?', 0, $CONFIG['loja_id']]]);
// $TOTAL_CADASTROS_GERAL = Tamanhos::count(['conditions'=> ['excluir=? and loja_id=?', $CONFIG['loja_id']]);
// $TOTAL_CADASTROS_DESATIVOS = Tamanhos::count(['conditions'=> ['excluir=?', 1]]);

$GET_STATUS = isset( $POST['status'] ) && $POST['status'] != '' ? $POST['status'] : ( isset( $GET['status'] ) && $GET['status'] != '' ? $GET['status'] : '' );
$GET_PESQUISAR = isset( $GET['q'] ) && $GET['q'] != '' ? $GET['q'] : ( isset( $POST['q'] ) && $POST['q'] != '' ? $POST['q'] : '' );
$GET_OPCOES_ID = isset( $GET['opcoes_id'] ) && $GET['opcoes_id'] != '' ? $GET['opcoes_id'] : ( isset( $POST['opcoes_id'] ) && $POST['opcoes_id'] != '' ? $POST['opcoes_id'] : '' );
?>

<div class="panel panel-default">
    <div class="panel-heading panel-store text-uppercase ocultar">
        Variações: 
        <small class="ft12px">
            <?php
            $OpcoesTipo = OpcoesTipo::all(['conditions'=>['excluir=? and loja_id=?', 0, $CONFIG['loja_id']]]);
            $resultVariacao = OpcoesTipo::find_by_sql('select * from opcoes_tipo where loja_id= ? and excluir=? and exists(select 1 from tamanhos where tamanhos.opcoes_id = opcoes_tipo.id)', [ $CONFIG['loja_id'], 0 ]);
            if( count($resultVariacao) > 0 ) 
			{ 
                foreach( $resultVariacao as $var ) 
				{
                    $tipos[] = ""
                            . "<a href='/adm/tamanhos.php?codigo_id={$GET['codigo_id']}&cor_id={$GET['cor_id']}&q={$GET['q']}&status={$GET['status']}&opcoes_id={$var->id}'>"
                            . "{$var->tipo}"
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
					<td colspan="4">
						<form action="/adm/tamanhos.php?codigo_id=<?php echo $GET['codigo_id']?>&cor_id=<?php echo $GET['cor_id']?>&opcoes_id=<?php echo $GET['opcoes_id']?>" method="post" class="formulario-tamanhos">
							<div class="clearfix mb15" style="line-height: 17px;">
								<span class="cor-001">Total de <span class="ft18px"><?php echo $TOTAL_CADASTROS_ATIVOS?></span> tamanhos cadastrados</span> 
							</div>
							<input name="q" type="text" class="w50"/>
							<button type="submit" class="btn btn-primary">
								<i class="fa fa-search"></i>
							</button>
							<button class="btn btn-primary" type="button" onclick="$('.ocultar, .formulario00').slideToggle(0);" <?php echo _P( 'tamanhos', $_SESSION['admin']['id_usuario'], 'incluir|alterar' )?>>cadastrar</button>
                            <button class="btn btn-danger" type="button" data-action="btn-excluir-varios" data-href="/adm/tamanhos.php?codigo_id=<?php echo $GET['codigo_id']?>&cor_id=<?php echo $GET['cor_id']?>&opcoes_id=<?php echo $GET['opcoes_id']?>" <?php echo _P( 'tamanhos', $_SESSION['admin']['id_usuario'], 'excluir' )?>>
                                excluir seleção
                            </button>
					
							<button class="btn-adicionar-tam btn btn-success<?php echo (empty($_GET['cor_id']) ? ' hidden':'')?>" type="button" <?php echo _P('tamanhos', $_SESSION['admin']['id_usuario'], 'incluir')?> data-href="/adm/produtos/produtos-cores-tamanhos.php?codigo_id=<?php echo $GET['codigo_id']?>&cor_id=<?php echo $GET['cor_id']?>&opcoes_id=<?php echo $GET['opcoes_id']?>">
                                adicionar
                            </button>
						</form>
					</td>
				</tr>
				<tr class="ocultos formulario00">
					<td colspan="4" class="clearfix">
                        <form class="formulario-tamanhos container" action="/adm/tamanhos.php?codigo_id=<?php echo $GET['codigo_id']?>&cor_id=<?php echo $GET['cor_id']?>&acao=cadastrar&opcoes_id=<?php echo $GET['opcoes_id']?>" method="post" enctype="multipart/form-data">
                            <fieldset class="row">
								<div class="mb15">
                                    <span class="show mb5">Opçoes:</span>
                                    <select name="Tamanhos[0][opcoes_id]" style="width: 220px">
                                        <?php foreach ( $OpcoesTipo as $opc ) : $opc = $opc->to_array(); ?>
                                        <option value="<?php echo $opc['id']?>"<?php echo $opc['id']== $rs['opcoes_id']? ' selected' : ''?>>
                                            <?php echo $opc['tipo']?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
								<div class="mb15">
									<span class="show mb5">Nome da Variação:</span>
									<span class="show mb15">
                                        <input type="text" name="Tamanhos[0][nometamanho]" style="max-width: 320px"/>
									</span>
									<span class="show mb5">Ordem:</span>
									<span class="show">
										<input type="text" name="Tamanhos[0][ordem]" style="width: 120px"/>
									</span>
								</div>
								
								<fieldset class="mb15">
									<span class="show ft18px">Selecione as cores:</span>
									<span class="show mb5 clearfix">
										<span class="pull-left mr15">
											1ºcor:<br/> 
											<input type="text" class="input-box-color" name="Tamanhos[0][hex1]" id="corA" onchange="$(this).css({'background-color' : '#' + this.value });"/>
										</span>
										<span class="pull-left">
											2º cor:<br/> 
											<input type="text" class="input-box-color" name="Tamanhos[0][hex2]" id="corB" onchange="$(this).css({'background-color' : '#' + this.value });"/>
										</span>
									</span>
								</fieldset>

                                <div class="clearfix ft18px fieldset">
                                    <div class="row text-center">
                                        <span class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb5 ft14px">
                                            Selecione uma imagem
                                        </span>
                                        <span class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb5">
                                            <img src="/plataformaimgs/95x95/square/imgs_sem-foto-produto.png" id="img0" class="show center-block" width="95"/>
                                            <label for="icon0" class="btn fa fa-folder-open fa-1x"></label>
                                            <a href="#" class="btn fa fa-trash fa-1x" data-remove-imgs="img0"></a>
                                        </span>
                                    </div>
                                </div>
                                <input type="file" name="icon" id="icon0" class="hidden" onchange="$(this).preview_img({ img: '#img0' });">

                                <div class="mb15 mt15">
                                    <button type="submit" class="btn btn-primary btn-cadastros-cores" <?php echo _P( 'tamanhos', $_SESSION['admin']['id_usuario'], 'alterar' )?>>
                                        salvar
                                    </button>
                                    <button type="button" class="btn btn-danger" onclick="$('.ocultar, .formulario00').slideToggle(0);">cancela</button>
                                </div>
							</fieldset>
						</form>
					</td>
				</tr>
				<tr class="plano-fundo-adm-003 ocultar">
					<td colspan="1" bgcolor="#ffffff" align="center" nowrap="nowrap" width="1%">
                        <input type="checkbox" data-action="selecionados-exclusao-all" class="selecionados-exclusao-all" id="label" value=""/>
						<label for="label" class="input-checkbox"></label>
					</td>
					<td>Descrição</td>
					<td>Ordem</td>
					<td align="center">Ações</td>
				</tr>
				<?php
                $arr_id = [];
                if(isset($GET['codigo_id']) && $GET['codigo_id'] > 0)
                    foreach(Produtos::all(['conditions' => ['codigo_id=? and status=0 and excluir=0 and id_cor=?', $GET['codigo_id'], (int)$GET['cor_id']]]) as $p)
                        $arr_id[] = $p->id_tamanho;

				$i = 0;
				
				$maximo = 50;
				
				$pag = isset( $GET['pag'] ) && $GET['pag'] != '' ? $GET['pag'] : 1;
				
				$inicio = (($pag * $maximo) - $maximo);
				
				$conditions = array();
				
				$conditions['select'] = 'tamanhos.id, tamanhos.hex2, tamanhos.hex1, tamanhos.opcoes_id, opcoes_tipo.tipo, tamanhos.nometamanho, tamanhos.ordem';
				
				$conditions['conditions'] = sprintf('tamanhos.excluir=%u and tamanhos.id > %u and tamanhos.loja_id=%u', 0, 0, $CONFIG['loja_id']);
				
				$where .= isset( $GET_OPCOES_ID ) && $GET_OPCOES_ID != '' 
					? queryInjection( ' and tamanhos.opcoes_id = %u ', $GET_OPCOES_ID)  : '';
				
				$conditions['conditions'] .= isset( $GET_PESQUISAR ) && $GET_PESQUISAR != '' 
					? queryInjection(' and tamanhos.nometamanho like "%%%s%%" ', $GET_PESQUISAR)  : '';
				
				$conditions['joins'] = 'INNER JOIN opcoes_tipo ON (opcoes_tipo.id=tamanhos.opcoes_id)';
				
				$total = ceil(Tamanhos::count($conditions) / $maximo);
				
				$conditions['order'] = 'tamanhos.nometamanho asc, tamanhos.ordem desc';
				
				$conditions['limit'] = $maximo;
				
				$conditions['offset'] = ($maximo * ($pag - 1));
				
				$result = Tamanhos::all( $conditions );
				
				foreach ( $result as $rs ) { $rs = $rs->to_array() ?>
				<?php if( $rs['opcoes_id'] != $opcoes_id ) { $opcoes_id = $rs['opcoes_id']; ?>
                <tr class="formulario<?php echo $rs["id"];?> ocultar">
                    <td colspan="5" class="ft20px">
                        <?php 
                        echo $rs['tipo'];
                        $tipo_text = $rs['tipo'];
                        $i=1;
                        ?>
                    </td>
                </tr>
                <?php } ?>				
				<tr class="in-hover formulario<?php echo $rs['id'];?> ocultar" <?php echo ($i % 2) ? 'style="background-color:#f3f3f3"': ''?>>
					<td nowrap="nowrap" width="1%">
                        <input type="checkbox" name="Tamanhos[<?php echo $rs['id'];?>][excluir]" id="label<?php echo $rs['id']?>" value="1" data-action="selecionados-exclusao"/>
						<label for="label<?php echo $rs['id']?>" class="input-checkbox"></label>
					</td>
					<td>
						<?php echo $rs['nometamanho']?>
                        <?php echo !empty($arr_id) && in_array($rs['id'], $arr_id) ? '<span class="pull-right btn btn-info btn-xs ft10px">adicionado</span>': null?>
					</td>
					<td nowrap="nowrap" width="1%" align="center">
						<?php echo $rs["ordem"];?>
					</td>
					<td nowrap="nowrap" width="1%" align="center">
						<a href="javascript: void(0);" class="btn btn-primary btn-sm" onclick="$('.ocultar, .formulario<?php echo $rs['id']?>').slideToggle(0);" <?php echo _P( 'tamanhos', $_SESSION['admin']['id_usuario'], 'alterar' )?>>editar</a>
						
						<a href="/adm/tamanhos.php?codigo_id=<?php echo $GET['codigo_id']?>&opcoes_id=<?php echo $GET['opcoes_id']?>&cor_id=<?php echo $GET['cor_id']?>&id=<?php echo $rs['id']?>&acao=excluir" class="btn btn-danger btn-sm btn-excluir-modal" <?php echo _P( 'tamanhos', $_SESSION['admin']['id_usuario'], 'excluir' )?>>excluir</a>
					</td>
				</tr>
				
				<tr class="formulario<?php echo $rs['id'];?> ocultos" id="formulario<?php echo $rs['id'];?>">
					<td colspan="4">
                        <form class="formulario-tamanhos container" action="/adm/tamanhos.php?codigo_id=<?php echo $GET["codigo_id"]?>&cor_id=<?php echo $GET['cor_id']?>&acao=editar&opcoes_id=<?php echo $GET['opcoes_id']?>" method="post" enctype="multipart/form-data">
                            <fieldset class="row">
								<div class="mb15">
                                    <span class="show mb5">Opçoes:</span>
                                    <select name="Tamanhos[<?php echo $rs['id']?>][opcoes_id]" style="width: 220px">
                                        <?php foreach ( $OpcoesTipo as $opc ) { $opc = $opc->to_array(); ?>
                                        <option value="<?php echo $opc['id']?>"<?php echo $opc['id']== $rs['opcoes_id']? ' selected' : ''?>>
                                            <?php echo $opc['tipo']?>
                                        </option>
                                        <?php } ?>
                                    </select>
                                </div>
								<div class="mb15">
									<span class="show mb5">Nome <?php echo $tipo_text?>:</span>
									<span class="show mb15">
                                        <input type="text" value="<?php echo $rs['nometamanho']?>" name="Tamanhos[<?php echo $rs['id']?>][nometamanho]" style="width: 320px"/>
									</span>
									
									<span class="show mb5">Ordem:</span>
									<span class="show">
										<input type="text" value="<?php echo $rs['ordem']?>" name="Tamanhos[<?php echo $rs['id']?>][ordem]" style="width: 120px"/>
									</span>
								</div>
								
                                <fieldset class="mb15">
									<span class="show ft18px">Selecione as cores:</span>
									<span class="show mb5 clearfix">
										<span class="pull-left mr15">
											1ºcor:<br/> 
											<input type="text" value="<?php echo $rs['hex1']?>" class="input-box-color" name="Tamanhos[<?php echo $rs['id']?>][hex1]" id="corA<?php echo $rs['id']?>" style="background-color:#<?php echo $rs['hex1']?>" onchange="$(this).css({'background-color' : '#' + this.value });"/>
										</span>
										<span class="pull-left">
											2º cor:<br/> 
											<input type="text" value="<?php echo $rs['hex2']?>" class="input-box-color" name="Tamanhos[<?php echo $rs['id']?>][hex2]" id="corB<?php echo $rs['id']?>" style="background-color:#<?php echo $rs['hex2']?>" onchange="$(this).css({'background-color' : '#' + this.value });"/>
										</span>
									</span>
								</fieldset>

                                <?php
                                // recarrega a imagem
                                $filename = glob( '../' . URL_VIEWS_BASE_PUBLIC_UPLOAD . 'imgs/icon/t' . $rs['id'] . '.{jpg,jpeg,png,gif}', GLOB_BRACE ); 
                                // verficar se existe um vetor
                                if( count( $filename ) > 0 ){
                                    $ext = strtolower( pathinfo(current($filename), PATHINFO_EXTENSION ) );
                                }
                                ?>                                
                                <div class="clearfix ft18px fieldset">
                                    <div class="row text-center">
                                        <span class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb5 ft14px">
                                            Selecione uma imagem
                                        </span>
                                        <span class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb5">
                                            <img src="<?php echo file_exists( current($filename) ) ? Imgs::src("icon/t{$rs['id']}.{$ext}", 'imgs'): Imgs::src('sem-foto-produto.png', 'public');?>" id="img<?php echo $rs['id'];?>" class="show center-block" width="95"/>
                                            <label for="icon<?php echo $rs['id'];?>" class="btn fa fa-folder-open fa-1x"></label>
                                            <a href="/adm/tamanhos.php?codigo_id=<?php echo $GET['codigo_id']?>&opcoes_id=<?php echo $GET['opcoes_id']?>&cor_id=<?php echo $GET['cor_id']?>&id=<?php echo $rs['id']?>&acao=excluir_imagem" class="btn fa fa-trash fa-1x" ></a>
                                        </span>
                                    </div>
                                </div>
                                <input type="file" name="icon" id="icon<?php echo $rs['id'];?>" class="hidden" onchange="$(this).preview_img({ img: '#img<?php echo $rs['id'];?>' });">

                                <div class="mb15 mt15">
                                    <button type="submit" class="btn btn-primary btn-cadastros-cores" <?php echo _P( 'tamanhos', $_SESSION['admin']['id_usuario'], 'alterar' )?>>
                                        salvar
                                    </button>
                                    <button type="button" class="btn btn-danger" onclick="$('.ocultar, .formulario<?php echo $rs['id'];?>').slideToggle(0);">cancela</button>
                                </div>
							</fieldset>
						</form>
                        <script>
                            <?php ob_start(); ?>
                            $('#corA<?php echo $rs['id']?>').ColorPicker({
                                color: '#<?php echo $rs['hex1']?>',
                                onChange: function (hsb, hex, rgb) {
                                    $('#corA<?php echo $rs['id']?>').css({'background-color' : '#' + hex }).val( hex );
                                }
                            });
                            $('#corB<?php echo $rs['id']?>').ColorPicker({
                                color: '#<?php echo $rs['hex1']?>',
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
					<td colspan="4">
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
										echo sprintf('<a href="/adm/tamanhos.php?%s" class="btn-paginacao">%s</a>', $data, $i);
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
					"height": "45px"
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
		if( href.search('excluir') > '0') {
			if( ! confirm("Deseja realmente excluir!") ) {  
                return false; 
            }
        }
    });
</script>
<?php
include 'rodape.php';