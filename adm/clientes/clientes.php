<?php include '../topo.php'; ?>
<style>
	body{ background-color: #f1f1f1 }
</style>
<!-- <div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"> -->
		<div class="panel panel-default">
			<div class="panel-heading panel-store text-uppercase">Clientes</div>
			<div class="panel-body">
				<form action="/adm/clientes/clientes.php" class="clearfix mb15" method="get">
					<div class="form-inline">
						<div class="input-group col-md-5 mb15">
							<input type="text" name="q" class="form-control" placeholder="Pesquisar dados...">
							<span class="input-group-btn">
								<button type="submit" class="btn btn-default"><i class="fa fa-<?php echo (!empty($GET['q'])?'close':'search')?>"></i></button>
							</span>
						</div>
						<div class="radio mr15 pull-right" style="margin-top: 7px;">
							<label>
								<input type="radio" id="n_ped" name="n_ped" value="1"<?php echo (!empty($GET['n_ped']) ? ' checked':'')?>/>
								<label for="n_ped" class="input-radio"></label> 
								Não compraram no site
							</label>
						</div>
						<div class="radio mr15 pull-right" style="margin-top: 7px;">
							<label>
								<input type="radio" id="inativos" name="at" value="1"<?php echo (!empty($GET['at']) && $GET['at'] == 1 ? ' checked':'')?>/>
								<label for="inativos" class="input-radio"></label> 
								Inativos
							</label>
						</div>
						<div class="radio mr15 pull-right" style="margin-top: 7px;">
							<label>
								<input type="radio" id="ativos" name="at" value="0"<?php echo (empty($GET['at']) ? ' checked':'')?>/>
								<label for="ativos" class="input-radio"></label> 
								Ativos
							</label>
						</div>
					</div>
					<div class="form-inline">
						<div class="input-group col-md-3 pull-left">
							<input type="text" name="dt_ini" class="form-control" placeholder="Data de cadastro...">
							<span class="input-group-btn">
								<button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
							</span>
						</div>
						<div class="input-group col-md-3 pull-left">
							<input type="text" name="dt_fin" class="form-control" placeholder="Data de cadastro...">
							<span class="input-group-btn">
								<button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
							</span>
						</div>
					</div>
				</form>
				
				<div class="table-responsive" id="div-edicao">
					<table class="table table-borded table-borded table-hover" id="sortable">
						<tr>
							<th nowrap="nowrap" width="1%">
								Nome do cliente 
								<a href="/adm/clientes/clientes.php?pag=<?php echo $GET['pag']?>&q=<?php echo $GET['q']?>&dt_ini=<?php echo $GET['dt_ini']?>&dt_fin=<?php echo $GET['dt_fin']?>&selecione=<?php echo $GET['selecione']?>&ordem=<?php echo $GET['ordem'] === 'nome_asc' ? 'nome_desc' : 'nome_asc';?>&ordermdata=<?php echo $GET['ordermdata']?>">
									<span class="fa-stack ft12px" style="margin-top:-10px">
										<i class="fa fa-square-o fa-stack-2x"></i>
										<i class="fa fa-arrow-<?php echo $GET['ordem'] == 'nome_asc' ? 'down':'up';?> fa-stack-1x"></i>
									</span>
								</a>
							</th>
							<th>E-mail</th>
							<th>Telefone</th>
							<th>CPF/CNPJ</th>
							<th nowrap="nowrap" width="1%">
								Cliente desde
								<a href="/adm/clientes/clientes.php?pag=<?php echo $GET['pag']?>&q=<?php echo $GET['q']?>&dt_ini=<?php echo $GET['dt_ini']?>&dt_fin=<?php echo $GET['dt_fin']?>&selecione=<?php echo $GET['selecione']?>&ordem=<?php echo $GET['ordem']?>&ordermdata=<?php echo $GET['ordermdata'] === 'cadastro_asc' ? 'cadastro_desc' : 'cadastro_asc';?>">
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
						$conditions['conditions'] = sprintf('loja_id=%u and atacadista=0 ', $CONFIG['loja_id']);
						$q = isset( $GET['q'] ) && $GET['q'] != '' ? addslashes($GET['q']) : null;
						$at = isset( $GET['at'] ) && $GET['at'] != '' ? $GET['at'] : 0;
						$conditions['conditions'] .= sprintf('AND excluir = %u ', $at);
						
						$IS_CPF = new ValidaCPFCNPJ($q);
						/*
						 * Retorna uma verificacao na string contendo uma necessidade de um numero de cpf
						 */
						if( $IS_CPF->valida() ) {
							$conditions['conditions'] .= $q != '' ? sprintf('AND cpfcnpj = "%s" ', $q) : '';
						}
						else {
							// Percorrre uma string de busca comum
							$conditions['conditions'] .= $q != '' ? sprintf('AND nome like "%%%s%%" OR email like "%s%%" ', $q, $q) : ''; 
							
							// Percorre uma string de busca entre intervalo de datas
							if(isset($GET['selecione']) && $GET['selecione'] === 'data') {
								$GET['dt_ini'] = converterDatas($GET['dt_ini']);
								$GET['dt_fin'] = converterDatas($GET['dt_fin']);
								$conditions['conditions'] .= $q !== '' || 
									$GET['dt_ini'] !== '' || 
									$GET['dt_fin'] !== '' || 
									$GET['dt_ini'] > $GET['dt_fin'] ? sprintf('AND created_at between "%s" and "%s"', $GET['dt_ini'], $GET['dt_fin']) : '';
									
								$mensagem_error = $GET['dt_ini'] > $GET['dt_fin'] ? 'Desculpe, a data inicial não pode ser maior que a final.':'';
							}

							if(isset($GET['n_ped']) && $GET['n_ped'] == '1') {
								$conditions['conditions'] .= 'AND id NOT IN(SELECT p.id_cliente FROM pedidos p WHERE p.excluir = 0)';
							}
						}
						
						$conditions['order'] = $GET['ordermdata'] ? ' data_' . str_replace('_', ' ', $GET['ordermdata']).',' : '';
						$conditions['order'] .= $GET['ordem'] ? ' ' . str_replace('_', ' ', $GET['ordem']) : ' created_at desc ';

						$total = ceil( Clientes::count($conditions) / $maximo );

						$conditions['limit'] = $maximo;

						$conditions['offset'] = ($maximo * ($pag - 1));
						
						$sqlClientes = Clientes::all( $conditions );
						foreach( $sqlClientes as $rws ) { ?>
							<tr class="in-hover lista-zebrada"<?php echo ($rws->excluir == 1 ? ' style="background-color: #fbdcdc !important"':'')?>>
								<td width="1%" nowrap="nowrap"><?php echo $rws->nome;?><?php echo ($rws->excluir == 1 ? ' - <span style="color: #bd3838">inativo</span>':'')?></td>
								<td><?php echo $rws->email;?></td>
								<td width="1%" align="center" nowrap="nowrap"><?php echo $rws->telefone;?></td>
								<td width="1%" align="center" nowrap="nowrap"><?php echo $rws->cpfcnpj;?></td>
								<td width="1%" align="center" nowrap="nowrap"><?php echo !empty($rws->created_at) ? $rws->created_at->format('d/m/Y H:i') : '';?></td>
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
								$data = http_build_query(array_replace($GET, ['pag' => $i]));
								echo sprintf('<li><a href="/adm/clientes/clientes.php?%s">%s</a></li>', $data, $i);
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
	<!-- </div>
</div> -->

<?php ob_start(); ?>
<script>
    function busca_cidade( a, b ) {
        var cep = a, 
            id=b.target.id;

        $.ajax({
            url: "../../",
            type: "post",
            data: { acao: "BuscaCidade", cep: cep },
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

                JanelaModal.html( list.find("#div-edicao").html() ).dialog({ 
					autoOpen: true, 
					width: 850,
					heigth: 650,
					title: "Clientes Analisar/Alterar"
				}).css({ "background-color": "#f1f1f1" });
			},
			complete: function() {
				$("a.ui-dialog-titlebar-maximize").trigger("click");
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