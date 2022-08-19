<?php
include 'topo.php';

/**
 * Verfica a acao
 */
if( isset( $POST['acao'] ) && $POST['acao'] == 'lojas' ) 
{	
    $id = (INT)$POST['id'];
    $lojas_planos_id = (INT)$POST['lojas_planos_id'];
    $dominio = trim($POST['dominio'] );
    $max_cadastros = (INT)$POST['max_cadastros'];
    $max_visualizacoes = (INT)$POST['max_visualizacoes'];

    /**
     * Limita somente para cadastrar
     */
    if( isset($GET['Acao']) && $GET['Acao'] == 'Cadastrar' ) {
        $Lojas = new Lojas();
        $Lojas->lojas_planos_id = $lojas_planos_id;
        $Lojas->dominio = $dominio;
        $Lojas->max_cadastros = $max_cadastros;
        $Lojas->max_visualizacoes = $max_visualizacoes;
        if( $Lojas->save() ){
            header("Location: /adm/lojas.php?acao={$GET['acao']}&loja_id={$GET['loja_id']}");
            return;            
        }
    }
    
    /**
     * Limita somente para editar
     */
    if( isset($GET['Acao']) && $GET['Acao'] == 'Editar' ) {
        $Lojas = Lojas::find(['conditions' => ['id=?', $id]]);
        $Lojas->lojas_planos_id = $lojas_planos_id;
        $Lojas->dominio = $dominio;
        $Lojas->max_cadastros = $max_cadastros;
        $Lojas->max_visualizacoes = $max_visualizacoes;
		
        if( $Lojas->save() ){
            header("Location: /adm/lojas.php?acao={$GET['acao']}&loja_id={$GET['loja_id']}");
            return;            
        }
    }
}

$GET_STATUS = isset( $POST['status'] ) && $POST['status'] != '' ? $POST['status'] : ( isset( $GET['status'] ) && $GET['status'] != '' ? $GET['status'] : '' );
$GET_PESQUISAR = isset( $GET['pesquisar'] ) && $GET['pesquisar'] != '' ? $GET['pesquisar'] : ( isset( $POST['pesquisar'] ) && $POST['pesquisar'] != '' ? $POST['pesquisar'] : '' );

$result = LojasPlanos::find_by_sql('select * from lojas_planos order by valores asc');
foreach( $result as $rs ) {
    $planos[] = $rs->to_array();
}

?>
<div class="tag-opcoes clearfix">
	<h2>
        LOJAS - <small>PLANOS / BOLETOS / FATURAS</small>
        <a href="#" onclick="javascript:history.go(-1); return false;" class="btn btn-secundary btn-sm pull-right<?php echo empty($GET['acao']) ? ' hidden':''?>">voltar</a>
    </h2>
    <?php if( ! empty( $GET['Acao'] ) && $GET['Acao'] == 'BoletoEnviado' ) { ?>
        <div class="alert alert-info text-center">
            E-mail enviado com sucesso!
        </div>
    <?php } ?>
	<div id="div-edicao">
		<style>
            .fieldset{
                border-color: #aeaeae;
                border-width: 1px;
                border-style: solid;
                padding: 15px;
            }
			.ocultos{
				display: none;
			}
		</style>
        <table width="100%" border="0" cellpadding="10" cellspacing="0" class="table">
			<tbody>
                <?php if( ! empty( $GET['Acao'] ) && $GET['Acao'] == 'BoletoEnviarEmail' ) {
                    $LojasPgto = LojasPgto::find($GET['id']);
                    $filetowrite = './boletos/' . converter_texto($LOJA->pgtos[0]->formapgto) . '_' . md5($LOJA->pgtos[0]->loja_id . '_' . $LojasPgto->id ) . '.pdf';
                    $CONTEUDO_MAIL = ''
                            . '<tr>'
                            . '<td>'
                            . '<br/>'
                            . 'Olá, ' . $CONFIG['nome_fantasia'] 
                            . '<br/>'
                            . 'Seu Boleto referente ao Plano ' . $LOJA->plano->planos . ', já está disponível para Download no link abaixo!'
                            . '<br/>'
                            . '<a href="' . URL_BASE . 'adm/'.$filetowrite.'" target="_blank" class="btn">'
                            . 'Baixar Boleto'
                            . '</a>'
                            . '<br/>'
                            . '<br/>'
                            . '</td>'
                            . '<tr>';
                    
                    $CONTEUDO_MAIL = email_body($CONFIG, $CONTEUDO_MAIL);
                    
                    $mail->setFrom($CONFIG['email_contato'], $CONFIG['nome_fantasia']);
                    $mail->addAddress($CONFIG['email_contato'], $CONFIG['nome_fantasia']); 
                    
                    $mail->Subject = $CONFIG['nome_fantasia'] . ' - Boleto Loja Plano ' . $LOJA->plano->planos;
                    $mail->Body    = $CONTEUDO_MAIL;
                    if( ! $mail->send() ) {
                        echo '' 
                            . 'Message could not be sent.' 
                            . 'Mailer Error: ' . $mail->ErrorInfo;
                    } else {
                        $LojasPgto->mails = ($LojasPgto->mails + 1);
                        $LojasPgto->save();
                        header("Location: /adm/lojas.php?acao=Boletos&loja_id={$GET['loja_id']}");
                        return;
                    }
                    $mail->SmtpClose();
                    
                } else if( ! empty( $GET['Acao'] ) && $GET['Acao'] == 'BoletoExcluir' ) {
                    
                    $Boleto = ! empty( $GET['Boleto'] ) && $GET['Boleto'] != '' ? $GET['Boleto'] : false;
                    if( ! $Boleto ) {
                        header("Location: javascript://history.go(-1)");
                        return;
                    }
                    
                    if ( file_exists( $Boleto ) ) {
                        unlink( $Boleto );
                        header("Location: /adm/lojas.php?acao={$GET['acao']}&loja_id={$GET['loja_id']}&id={$GET['id']}");
                        return;
                    }
                    
                } elseif( ! empty( $GET['acao'] ) && $GET['acao'] == 'BoletoCadastrar' || $GET['acao'] == 'BoletoEditar' ) { ?>
                    <?php
                    $rs = null;
                    if( ! empty( $GET['id'] ) && $GET['id'] > 0 ) {
                        $rs = LojasPgto::find( $GET['id'] );
                    }
                    $data_vencimento = ! empty( $rs->vencimento ) ? date('d/m/Y', strtotime($rs->vencimento->format('Y-m-d'))) : date('10/m/Y');   
                    ?>
                    <tr>
                        <td colspan="6">
                            <form action="/adm/lojas-boleto.php?acao=<?php echo $GET['acao']?>&loja_id=<?php echo $GET['loja_id']?>&id=<?php echo $GET['id']?>" method="post" enctype="multipart/form-data" class="col-md-5 col-md-offset-3 fieldset" id="FormBoletos">
                                <p>Data Vencimento:</p>
                                <input name="vencimento" type="text" class="datepicker mb15" value="<?php echo $data_vencimento?>">
                                <p>Selecione o boleto:</p>
                                <input name="boleto" type="file" accept="pdf"/>
                                <div class="mt15 clearfix text-center">
                                    <button type="submit" class="btn btn-primary btn-lg">cadastrar</button>
                                </div>
                                <input name="acao" type="hidden" value="EnviarBoleto"/>
                                <input name="pass" type="hidden" value="<?php echo sha1('boletos@+123');?>"/>
                                <div id="target">
                                    <?php
                                    $filetowrite = './boletos/' . converter_texto($rs->formapgto) . '_' . md5($rs->loja_id . '_' . $rs->id ) . '.pdf';
                                    if ( file_exists( $filetowrite ) ) {
                                        echo ''
                                            . '<p class="text-center mt5">'
                                                . '<a href="' . URL_BASE . 'adm/'.$filetowrite.'" target="_blank" class="btn btn-secundary">'
                                                    . 'ver boleto'
                                                . '</a> '
                                                . '<a href="/adm/lojas.php?acao=' . $GET['acao'] . '&loja_id=' . $GET['loja_id'] . '&id=' . $GET['id'] . '&Acao=BoletoExcluir&Boleto='.$filetowrite.'" class="btn btn-danger">'
                                                    . 'remover boleto'
                                                . '</a> '
                                                . '<a href="/adm/lojas.php?Acao=BoletoEnviarEmail&loja_id=' . $GET['loja_id'] . '&id=' . $GET['id'] . '" class="btn btn-info">'
                                                    . 'enviar um e-mail'
                                                . '</a>'
                                            . '</p>';
                                    }
                                    ?>
                                </div>
                            </form>
                        </td>
                    </tr>
                <?php } else if( ! empty( $GET['acao'] ) && $GET['acao'] == 'BoletosPagar' ) { 
                    
                    try {
                        $LojasPgto = LojasPgto::find( $GET['id'] );
                        $LojasPgto->status = 1;
                        $LojasPgto->save();
                        header("location: javascript://location.reload(); ");
                        return;
                    }
                    catch (Exception $ex) {
                        header('Location: /adm/lojas.php');
                        return;
                    }
                    
                } else if( ! empty( $GET['acao'] ) && $GET['acao'] == 'Boletos' ) { ?>
                    <tr>
                        <td colspan="7" align="center">
                            <a href="/adm/lojas.php?acao=BoletoCadastrar&loja_id=<?php echo $GET['loja_id'];?>" class="btn btn-primary">
                                gerar boleto
                            </a>
                        </td>
                    </tr>
                    <tr class="plano-fundo-adm-001">
                        <td>Boletos em Abertos.</td>
                        <td align="center" class="text-center">Dias</td>
                        <td align="center" class="text-center">Lançamento</td>
                        <td align="center" class="text-center">Vencimento</td>
                        <td align="center" class="text-center" nowrap="nowrap" width="1%">E-mails</td>
                        <td align="center" class="text-center">Status</td>
                        <td align="center" class="text-center">Ações</td>
                    </tr>
                    <?php 
                    $group_status = '';
                    foreach ($LOJA->pgtos as $rs) { 
                        
                    $data = $rs->vencimento->format('Y-m-d');
                    $data_atual = date('Y-m-d H:i:s');
                    $updated_at = date('Y-m-t 23:59:59', strtotime($data));
                    $strtotime = strtotime($updated_at) - strtotime($data_atual);
                    
                    $dias = floor( $strtotime / ( 60 * 60 * 24 ) );
                    $DIAS = $dias <= 0 ? 'Vencido à ' . str_replace('-', '', $dias) . ' dias' : "{$dias} dias restantes";
                    
                    if( $group_status != $rs->status ) { $group_status = $rs->status; ?>
                    <tr class="plano-fundo-adm-001">
                        <td colspan="7"><?php echo empty( $rs->status ) ? 'Boletos em Abertos' : 'Boletos Pagos'?></td>
                    </tr>
                    <?php } ?>
                    <tr class="in-hover lista-zebrada"
                        <?php echo $dias <= 0 ? ( empty( $rs->status ) ? ' style="background-color: #fbdcdc !important"' : ' style="background-color: #e2fbe3 !important"' ) : ''?>>
                        <td>
                            <?php echo $rs->formapgto?>
                        </td>
                        <td align="center" nowrap="nowrap" width="1%">
                            <?php echo empty($rs->status) ? $DIAS : '-'?>
                        </td>
                        <td align="center" nowrap="nowrap" width="1%">
                            <?php echo $rs->mes_inicial->format('d/m/Y')?>
                        </td>
                        <td align="center" nowrap="nowrap" width="1%">
                            <?php echo date('d/m/Y', strtotime($rs->vencimento)); ?>
                        </td>
                        <td align="center" >
                            <?php echo $rs->mails?>
                        </td>
                        <td align="center" nowrap="nowrap" width="1%">
                            <?php echo empty( $rs->status ) ? '-' : 'Pago'?>
                        </td>
                        <td align="center" nowrap="nowrap" width="1%">
                            <a href="/adm/lojas.php?acao=BoletoEditar&loja_id=<?php echo $rs->loja_id?>&id=<?php echo $rs->id?>" class="btn btn-warning btn-sm">editar boleto</a>
                            <a href="/adm/lojas.php?acao=BoletosPagar&loja_id=<?php echo $rs->loja_id?>&id=<?php echo $rs->id?>" class="btn btn-info btn-sm">pagar</a>
                        </td>
                    </tr>
                    <?php } ?>
                    
                <?php } else { ?>
<!--				<tr class="ocultar">
					<td colspan="4">
						<form action="/adm/lojas.php" method="post" class="formulario-lojas">
							<div class="clearfix mb15" style="line-height: 17px;">
								<span class="cor-001">Total de <span class="ft18px"><?php echo $TOTAL_CADASTROS_ATIVOS?></span> lojas cadastradas</span> 
							</div>
							<input name="pesquisar" type="text" class="w65"/>
                            <input name="acao" type="hidden" value="lojas"/>
							<button type="submit" class="btn btn-primary">
								<i class="fa fa-search"></i>
							</button>
							<button class="btn btn-primary" type="button" <?php echo _P(  $PgAt, $_SESSION['admin']['id_usuario'], 'incluir' )?>>cadastrar</button>
							
							<button class="btn btn-danger" type="button" data-id="btn-excluir-varios" data-href="lojas.php?codigo_id=<?php echo $GET['codigo_id']?>" <?php echo _P(  $PgAt, $_SESSION['admin']['id_usuario'], 'excluir' )?>>excluir seleção</button>
							
						</form>
					</td>
				</tr>-->
				
<!--				<tr id="formulario" class="ocultos ocultar">
					<td colspan="4">
						<form class="col-md-5 col-md-offset-3 formulario-lojas fieldset" action="/adm/lojas.php?Acao=Cadastrar" method="post">
                            <div class='mb15'>
                                <p>Planos:</p>
                                <select name="lojas_planos_id" style="width: 320px">
                                    <option>Seleicione o plano da loja</option>
                                    <?php foreach ( $planos as $plano ){ ?>
                                    <option 
                                        value="<?php echo $plano['id']?>" 
                                        data-cadastros="<?php echo $plano['produtos']?>"
                                        data-visualizacoes="<?php echo $plano['visualizacoes']?>">
                                            <?php echo $plano['planos']?>
                                    </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class='mb15'>
                                <p>Loja/Domínio:</p>
                                <input type='text' name='dominio' class="w100"/>
                            </div>
                            <div class='mb15'>
                                <p>Máximo de Cadastros:</p>
                                <input type='text' name='max_cadastros' class="w50"/>
                            </div>
                            <div class='mb15'>
                                <p>Máximo de Views:</p>
                                <input type='text' name='max_visualizacoes' class="w50"/>
                            </div>
							<button type="submit" class="btn btn-primary btn-cadastros-lojas" <?php echo _P(  $PgAt, $_SESSION['admin']['id_usuario'], 'incluir' )?>>salvar</button>
							<button type="button" class="btn btn-danger" onclick="$('.ocultar').slideToggle(0);" <?php echo _P(  $PgAt, $_SESSION['admin']['id_usuario'], 'excluir' )?>>cancela</button>
							
							<a class='btn btn-primary btn-cadastros-lojas' data-id='formulario' href='lojas.php?codigo_id=<?php echo $GET['codigo_id']?>&acao=cadastrar'>salvar</a>
							<a href="javascript://;" class="btn btn-danger mb5 mt5" onclick="$('.ocultar').slideToggle(0);">cancela</a>
							
							<input type='hidden' name='codigo_id' value='<?php echo $GET['codigo_id']?>'/>
							<input type='hidden' name='acao' value='lojas'/>
						</form>
					</td>
				</tr>-->
			
				<tr class="plano-fundo-adm-003 ocultar">
					<th>Loja/Domínio</th>
					<th>Plano</th>
					<th class='text-center'>Ações</th>
				</tr>
				
				<?php
				$i = 0;
				$maximo = 25;
				$pag = isset( $GET['pag'] ) && $GET['pag'] != '' ? $GET['pag'] : 1;
				$inicio = ( $pag * $maximo ) - $maximo;
				$where .= isset( $GET_PESQUISAR ) && $GET_PESQUISAR != '' ? queryInjection( ' AND lojas.dominio like "%s" ',  "%{$GET_PESQUISAR}%")  : '';
				
				$sql = ''
                        . 'SELECT '
                        . 'lojas.*, '
                        . 'lojas_planos.planos, '
                        . 'lojas_planos.visualizacoes, '
                        . 'lojas_planos.produtos, '
                        . 'lojas_planos.valores '
                        . 'FROM lojas '
                        . 'INNER JOIN lojas_planos ON lojas_planos.id=lojas.lojas_planos_id '
                        . 'WHERE lojas.id=? '
                        . 'ORDER BY lojas.dominio ASC';
                        
				$total = ceil( Lojas::find_num_rows($sql, [ $GET['loja_id'] ])  / $maximo );
				$busca .= " limit {$inicio}, {$maximo}";					
				$result = Lojas::find_by_sql($sql, [ $GET['loja_id'] ]);			
				foreach( $result as $rs ) { $rs = $rs->to_array(); ?>
				<tr class="in-hover formulario<?php echo $rs['id'];?> ocultar" <?php echo ($i % 2) ? 'style="background-color:#f3f3f3"': ''?>>
					<td align="center" nowrap="nowrap" width="1%">
						<?php echo $rs['dominio'] ?>
					</td>
					<td>
                        <strong><?php echo $rs['planos']?> - R$: <?php echo number_format($rs['valores'], 2, ',', '.');?></strong>
                        <br/>Max. cadastros: <?php echo $rs['max_cadastros']?>/Page views: <?php echo $rs['max_visualizacoes']?>
					</td>
<!--					<td align="center" >
						<?php echo $rs['max_cadastros'] ?>
					</td>-->
					<td align="center" nowrap="nowrap" width="1%">
<!--                        <a href="/adm/lojas.php?acao=Boletos&loja_id=<?php echo $rs['id']?>" class="btn btn-info btn-sm" <?php echo _P(  $PgAt, $_SESSION['admin']['id_usuario'], 'acessar' )?>>boletos</a>-->
                        
						<a href='javascript: void(0);' class="btn btn-primary btn-sm" onclick="$('.formulario<?php echo $rs['id']?>').slideToggle(0);" <?php echo _P(  $PgAt, $_SESSION['admin']['id_usuario'], 'alterar' )?>>editar</a> 
						<!--
						<a href="lojas.php?codigo_id=<?php echo $GET['codigo_id'];?>&grupoid=<?php echo $rs['id']?>" class="btn btn-warning btn-sm btn-adicionar-novo-dominio<?php echo '' == $GET['codigo_id'] ? ' hidden' : ''?>" <?php echo _P(  $PgAt, $_SESSION['admin']['id_usuario'], 'incluir' )?>>adicionar</a>
						-->
						<!--
						<a href='lojas.php?codigo_id=<?php echo $GET['codigo_id']?>&id=<?php echo $rs['id']?>&acao=excluir' class='btn btn-danger btn-sm btn-cadastros-lojas' <?php echo _P(  $PgAt, $_SESSION['admin']['id_usuario'], 'excluir' )?>>excluir</a>
						-->
					</td>
				</tr>
				<tr class="formulario<?php echo $rs['id'];?> ocultos" id='formulario<?php echo $rs['id'];?>'>
					<td colspan="3">
                        <form class="col-md-5 col-md-offset-3 fieldset mb15 mt15" action="/adm/lojas.php?Acao=Editar&acao=<?php echo $GET['acao']?>&loja_id=<?php echo $GET['loja_id']?>" method="post">
                            <div class='mb15'>
                                <p>Planos:</p>
                                <select name="lojas_planos_id" style="width: 320px">
                                    <option>Seleicione o plano da loja</option>
                                    <?php foreach ( $planos as $plano ){ ?>
                                    <option 
                                        value="<?php echo $plano['id']?>" 
                                        data-cadastros="<?php echo $plano['produtos']?>"
                                        data-visualizacoes="<?php echo $plano['visualizacoes']?>"
                                        <?php echo $plano['id'] == $rs['lojas_planos_id'] ? ' selected':''?>>
                                            <?php echo $plano['planos']?>
                                    </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class='mb15'>
                                <p>Loja/Domínio:</p>
                                <input type='text' value='<?php echo $rs['dominio'];?>' name='dominio' class="w100"/>
                            </div>
                            <div class='mb15'>
                                <p>Máximo de Cadastros:</p>
                                <input type='text' value='<?php echo $rs['max_cadastros'];?>' name='max_cadastros' class="w50"/>
                            </div>
                            <div class='mb15'>
                                <p>Máximo de Views:</p>
                                <input type='text' value='<?php echo $rs['max_visualizacoes'];?>' name='max_visualizacoes' class="w50"/>
                            </div>
							<button type="submit" class="btn btn-primary btn-cadastros-lojas">salvar</button>
							<button type="button" class="btn btn-danger" onclick="$('.formulario<?php echo $rs['id'];?>').slideToggle(0);">cancela</button>
							
							<input type='hidden' name='id' value='<?php echo $rs['id'];?>'/>
							<input type='hidden' name='acao' value='lojas'/>
						</form>
					</td>
				</tr>
				<?php ++$i; } ?>
<!--				<tr>
					<td colspan="4">
						<div class="paginacao paginacao-add">
							<?php
							if( $total > 0 )
							{
								for( $i = $pag - 1, $limiteDeLinks = $i + 5; $i <= $limiteDeLinks; ++$i )
								{
									if($i < 1)
									{
										$i = 1;
										$limiteDeLinks = 5;
									}
								
									if($limiteDeLinks > $total)
									{
										$limiteDeLinks = $total; 
										$i = $limiteDeLinks - 4;
									}

									if($i < 1)
									{
										$i = 1;
										$limiteDeLinks = $total;
									}
									
									if($i == $pag)
									{
										echo "<span class=\"at plano-fundo-adm-001\">{$i}</span>";
									}
									else
									{							
										echo "<a href=\"/adm/lojas.php?pesquisar={$GET_PESQUISAR}&status={$GET_STATUS}&codigo_id={$GET['codigo_id']}&pag={$i}\" class='btn-paginacao'>{$i}</a>";
									}
								}
							}
							?>
						</div>
					</td>
				</tr>-->
                <?php } ?>
			</tbody>
		</table>
	</div>
</div>
<script>
    <?php ob_start(); ?>
    $("#FormBoletos").submit(function(event){
        event.preventDefault(); //prevent default action 
        var post_url = $(this).attr("action"); //get form action url
        var request_method = $(this).attr("method"); //get form GET/POST method
        var form_data = new FormData(this); //Encode form elements for submission

        $.ajax({
            url : post_url,
            type: request_method,
            data : form_data,
            contentType: false,
            processData: false,
            xhr: function() {
                var xhr = $.ajaxSettings.xhr();
                if (xhr.upload) {
                    xhr.upload.addEventListener('progress', function(event) {
                        var percent = 0;
                        var position = event.loaded || event.position;
                        var total = event.total;
                        if (event.lengthComputable) {
                            percent = Math.ceil(position / total * 100);
                        }
                        //update progressbar
                        $("#status-alteracao").fadeIn(0).html("Enviando imagem " + percent + "%");
                    }, true);
                }
                return xhr;
            }
        }).done(function(response){ //
            $("#target").html(response);
        });
    });
    
	$(document).on("click", "a", function(){
		var href = this.href || e.target.href;		
		if( href.search('excluir') > '0')
			if( ! confirm("Deseja realmente excluir!") ) return false;    
	});
    $("select[name=lojas_planos_id]").on("change", function(e) {
        $(e.target).parents().find("input[name='max_cadastros']").val( $(e.target).find("option:selected").attr("data-cadastros") );
        $(e.target).parents().find("input[name='max_visualizacoes']").val( $(e.target).find("option:selected").attr("data-visualizacoes") );
    });
    <?php 
    $SCRIPT['script_manual'] .= ob_get_clean();
    
    ?>
</script>
<?php
include 'rodape.php';
