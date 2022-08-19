<?php include '../topo.php'; ?>
<style>
	body{ background-color: #f1f1f1 }
</style>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="panel panel-default">
			<div class="panel-body">
				<form action="/adm/clientes/clientes.php" class="row mb15" method="get">
					<div class="col-md-9">
						<h3 class="clearfix mt0">Clientes</h3>
						
					</div>
					<div class="col-md-3">
						<div class="input-group">
							<input type="text" name="q" class="form-control" placeholder="Pesquisar dados...">
							<span class="input-group-btn">
								<button type="submit" class="btn btn-default"><i class="fa fa-<?php echo (!empty($GET['q'])?'close':'search')?>"></i></button>
							</span>
						</div>
					</div>
				</form>
				
				<div class="table-responsive" id="div-edicao">
					<table class="table table-borded table-borded table-hover" id="sortable">
						<tr>
							<th nowrap="nowrap" width="1%">
								Nome do cliente 
								<a href="/adm/clientes/clientes.php?pag=<?php echo $GET['pag']?>&q=<?php echo $GET['q']?>&data_ini=<?php echo $GET['data_ini']?>&data_fin=<?php echo $GET['data_fin']?>&selecione=<?php echo $GET['selecione']?>&ordem=<?php echo $GET['ordem'] === 'nome_asc' ? 'nome_desc' : 'nome_asc';?>&ordermdata=<?php echo $GET['ordermdata']?>">
									<span class="fa-stack ft12px" style="margin-top:-10px">
										<i class="fa fa-square-o fa-stack-2x"></i>
										<i class="fa fa-arrow-<?php echo $GET['ordem'] == 'nome_asc' ? 'down':'up';?> fa-stack-1x"></i>
									</span>
								</a>
							</th>
							<th>E-mail</th>
							<th>Telefone</th>
							<th>CPF/CNPJ</th>
							<th class="text-center">Ativo/Desc</th>
							<th nowrap="nowrap" width="1%">
								Cliente desde
								<a href="/adm/clientes/clientes.php?pag=<?php echo $GET['pag']?>&q=<?php echo $GET['q']?>&data_ini=<?php echo $GET['data_ini']?>&data_fin=<?php echo $GET['data_fin']?>&selecione=<?php echo $GET['selecione']?>&ordem=<?php echo $GET['ordem']?>&ordermdata=<?php echo $GET['ordermdata'] === 'cadastro_asc' ? 'cadastro_desc' : 'cadastro_asc';?>">
									<span class="fa-stack ft12px" style="margin-top:-10px">
										<i class="fa fa-square-o fa-stack-2x"></i>
										<i class="fa fa-arrow-<?php echo $GET['ordermdata'] == 'cadastro_asc' ? 'down':'up';?> fa-stack-1x"></i>
									</span>
								</a>
							</th>
							<th align="center"><b>Ações</b></th>
						</tr>
						<tbody>
						<?php
						$i		= 0;
        
						$maximo = 25;
						
						$pag 	= isset( $GET['pag'] ) && $GET['pag'] !== '' ? $GET['pag'] : 1;
						
						$inicio = (($pag * $maximo) - $maximo);
						
						$conditions = array();
						
						$conditions['conditions'] = sprintf('excluir = 0 and loja_id=%u and atacadista = 1 ', $CONFIG['loja_id']);
						
						$q = isset( $GET['q'] ) && $GET['q'] != '' ? addslashes($GET['q']) : null;
						
						$IS_CPF = new ValidaCPFCNPJ($q);
						/*
						 * Retorna uma verificacao na string contendo uma necessidade de um numero de cpf
						 */
						if( $IS_CPF->valida() ) {
							$conditions['conditions'] .= $q != '' ? queryInjection('AND cpfcnpj = "%s" ', $q) : ''; 	
						}
						else {
							/*
							 * Percorrre uma string de busca comum
							 */
							$conditions['conditions'] .= $q != '' ? queryInjection('AND (nome like "%s%%" or (email like "%s%%")) ', $q, $q) : ''; 
							
							/*
							 * Percorre uma string de busca entre intervalo de datas
							 */
							if(isset($GET['selecione']) && $GET['selecione'] === 'data') {
								$conditions['conditions'] .= $q !== '' || 
									$GET['data_ini'] !== '' || 
									$GET['data_fin'] !== '' || 
										converterDatas($GET['data_ini']) > converterDatas($GET['data_fin'])
											? queryInjection('AND (data_cadastro between "%s" and "%s")', converterDatas($GET['data_ini']), converterDatas($GET['data_fin'])) : '';
									
								$mensagem_error = converterDatas($GET['data_ini']) > converterDatas($GET['data_fin']) ? 'Desculpe, a data inicial não pode ser maior que a final.':'';
							}
						}
						
						$conditions['order'] = $GET['ordermdata'] ? ' data_' . str_replace('_', ' ', $GET['ordermdata']).',' : '';
						$conditions['order'] .= $GET['ordem'] ? ' ' . str_replace('_', ' ', $GET['ordem']) : ' 1 ';

						$total = ceil( Clientes::count($conditions) / $maximo );

						$conditions['limit'] = $maximo;

						$conditions['offset'] = ($maximo * ($pag - 1));
						
						$sqlClientes = Clientes::all( $conditions );
						foreach( $sqlClientes as $rws ) { ?>
							<tr class="in-hover lista-zebrada">
								<td><?php echo $rws->nome;?></td>
								<td><?php echo $rws->email;?></td>
								<td><?php echo $rws->telefone;?></td>
								<td><?php echo $rws->cpfcnpj;?></td>
								<td width="1%" align="center" nowrap="nowrap">
									<?php echo $rws->atacadista_desconto ? 'SIM':'NÃO';?><br/><?php echo $rws->atacadista_desconto ? $rws->atacadista_desconto . '%' : '';?>
								</td>
								<td align="center"><?php echo $rws->data_cadastro->format('d/m/Y');?></td>
								<td width="1%" align="center" nowrap="nowrap">
									<a href="/adm/clientes/clientes-analisar.php?acao=analisar&id=<?php echo $rws->id;?>" class="btn btn-xs btn-primary-default analisar-cliente" <?php echo _P('clientes', $_SESSION['admin']['id_usuario'], 'alterar')?>>analisar dados</a>
								</td>
							</tr>
						<?php } ?>
						</tbody>
					</table>
				</div>
				<div class="pagination pagination-sm">
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

                            if($i == $pag) {
								echo sprintf('<li class="active"><span>%s</span></li>', $i);
                            }
                            else {
								extract($GET);
								echo sprintf('<li><a href="/adm/clientes/clientes.php?pag=%u&q=%s&data_ini=%s&data_fin=%s&selecione=%s&ordem=%s&ordermdata=%s\">%u</a>', 
									$i, $q, $data_ini, $data_fin, $selecione, $ordem, $ordermdata, $i);
                            }
                        }
                    }
                    else {
                        echo '<h2>Nada encontrado!</h2>';
                    }
                    ?>
                </div>
			</div>
		</div>
	</div>
</div>

<?php ob_start(); ?>
<script>
    function busca_cidade( a, b ) {
        var cep = a, 
            id=b.target.id;

        $.ajax({
            url: "../",
            type: "post",
            data: { acao : "BuscaCidade", cep : cep },
            dataType: "json",
            beforeSend: function() {
                JanelaModal.find("#cidade"+id).val( "Carregando..." );
                JanelaModal.find("#uf"+id).val( "" );
            }, 
            success: function( str ) {
                JanelaModal.find("#cidade"+id).val( str.cidade );
                JanelaModal.find("#uf"+id).val( str.uf );
            }, 
            error: function( x,m,t ){ 
                alert( x.responseText ); 
            }
        });
    }

    $("#div-edicao").on("click", ".analisar-cliente", function(e){
        e.preventDefault();
        $.ajax({
            url: this.href||e.target.href,
            cache: false,
            dataType: "html",
            success: function( str ){ 
                var list = $("<div/>", { html: str });

                JanelaModal
                    .dialog({ 
                        autoOpen: true, 
                        width: 850,
                        heigth: 650,
                        title: "Clientes Analisar/Alterar"
                    })
                    .html( list.find("#div-edicao").html() );
            },
            error: function(x,t,m){ 
                console.log( x.responseText+"\n"+t+"\n"+m ); 
            }
        }); 
    });

    var SPMaskBehavior = function (val) {
        return val.replace(/\D/g, '').length === 11 ? "(00) 00000-0000" : "(00) 0000-00009";
    };
    var spOptions = {
        onKeyPress: function(val, e, field, options) {
            field.mask(SPMaskBehavior.apply({}, arguments), options);
        }
    };
	
    JanelaModal.find("input[name=cep]").mask("00000-000", { onComplete : busca_cidade });
    JanelaModal.find('input[name=data_nascimento]').mask('99 / 99 / 9999');
    JanelaModal.find('input[name=telefone]').mask(SPMaskBehavior, spOptions);
    JanelaModal.find('input[name=celular]').mask(SPMaskBehavior, spOptions);

    JanelaModal.on('click', 'input[type=radio]', function(e) {
        if( $(this).val() === 'true' ) {
            $( '#conteudos-recarregar' ).find('input[name=q]').mask('999.999.999-99');
        } else {
            $( '#conteudos-recarregar' ).find('input[name=q]').unmask('');
        }

        /**
         * Nesse if vai passar somente datas
         */
        if($(this).val() === 'data'){
            $('#ocultar-datas').fadeIn(10);
        } else {
            $('#ocultar-datas').fadeOut(0);
        }			
    });
</script>
<?php 
$SCRIPT['script_manual'] .= ob_get_clean();

include '../rodape.php';