<?php 
include '../topo.php';

$PEDIDO_ID = isset($GET['id']) && $GET['id'] != '' ? (int)$GET['id'] : 0;
$PEDIDO_ID = isset($POST['id']) && $POST['id'] != '' ? (int)$POST['id'] : $PEDIDO_ID;

if( isset($POST['id_endereco']) && $POST['id_endereco'] > 0 ) 
{
	$id				= $POST['id_endereco'];
	$nomeendereco 	= $POST['nomeendereco'];
	$receber 		= $POST['receber'];
	$endereco 		= $POST['endereco'];
	$numero 		= $POST['numero'];
	$bairro 		= $POST['bairro'];
	$complemento	= $POST['complemento'];
	$referencia		= $POST['referencia'];
	$cidade 		= $POST['cidade'];
	$estado 		= trim( strtoupper($POST['uf']) );
	$cep 			= trim( $POST['cep'] );
	
	if( PedidosEnderecos::action_cadastrar_editar(['PedidosEnderecos' => [ $id => [ 
            'nome' => $nomeendereco,
            'endereco' => $endereco,
            'numero' => $numero,
            'complemento' => $complemento,
            'referencia' => $referencia,
            'bairro' => $bairro,
            'cidade' => $cidade,
            'uf' => $estado,
            'cep' => $cep,
        ] ] ], 'alterar', 'endereco') ) {
        $mensagemEndereco[$id] = 'Endereço alterado com sucesso...';
	} 
    else {
		$mensagemEndereco[$id] = 'Erro ao alterar o endereço...';
	}
}

$x = 1;
$Pedido = Pedidos::find($PEDIDO_ID);
$NfeCidades = NfeCidades::all();
$end = $Pedido->pedido_endereco;
?>  
<div id="endereco_alterar" method="post" class="col-md-6">
	<form action="/adm/vendas/vendas-enderecos.php?id=<?php echo $GET['id'];?>" method="post" id="endereco_<?php echo $end->id?>">
		<input type="hidden" name="id_endereco" value="<?php echo $end->id?>" disabled="disabled"/>
		<div class="panel panel-default">
			<div class="panel-heading panel-store text-uppercase">
				Endereço: <?php echo strtoupper($end->nome)?>
				<a href='#' class="btn btn-xs btn-warning pull-right" toggletext='toggletext' data-id="endereco_<?php echo $end->id?>" <?php echo _P('clientes-analisar', $_SESSION['admin']['id_usuario'], 'alterar')?>>alterar</a>
			</div>
			<div class="panel-body">
				<?php echo isset($mensagemEndereco[$end->id]) ? "<small class='show mt5 mb15'>{$mensagemEndereco[$end->id]}</small>" : '';?>
				<table width="100%" cellpadding="5" align="left" class="mt5">
					<tr>
						<td colspan="2">
							<label for="" class="show">Nome do Endereço ou Recebedor:</label>
							<input type="text" name="nomeendereco" class="input-text form-control" disabled="disabled" value="<?php echo $end->nome;?>"/>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<label for="" class="show">Endereço:</label>
							<input type="text" name="endereco" class="input-text w75 form-control pull-left" disabled="disabled" value="<?php echo $end->endereco;?>"/>
							<input type="text" name="numero" value="<?php echo $end->numero;?>" class="input-text pull-left w20 ml5 form-control" disabled="disabled">
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<label for="" class="show">Bairro:</label>
							<input type="text" name="bairro" value="<?php echo $end->bairro;?>" class="input-text w100 form-control" disabled="disabled"/>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<label for="" class="show">Complemento:</label>
							<input type="text" name="complemento" value="<?php echo $end->complemento != '' ? $end->complemento:null;?>" class="input-text w100 form-control" disabled="disabled"/>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<label for="" class="show">Refêrencias:</label>
							<input type="text" name="referencia" value="<?php echo $end->referencia != '' ? $end->referencia:null;?>" class="input-text w100 form-control" disabled="disabled"/>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<label for="" class="show">Cidade/UF:</label>
							<input type="text" name="cidade" id="cidadecep<?php echo $end->id?>" value="<?php echo $end->cidade != '' ? $end->cidade: 'Não informado';?>" class="input-text w75 form-control pull-left" disabled="disabled"/>
							<input type="text" name="uf" id="ufcep<?php echo $end->id?>" value="<?php echo $end->uf != '' ? $end->uf: 'Não informado';?>" class="input-text form-control w20 pull-left ml5" disabled="disabled"/>
						</td>
					</tr>
					<tr>
						<td width="70%">
							<label for="" class="show">CEP:</label>
							<input type="text" name="cep" value="<?php echo $end->cep != '' ? $end->cep: 'Não informado';?>" id="cep<?php echo $end->id?>" class="input-text form-control" disabled="disabled"/>
							<small style="color: transparent">(Dados de emissão para nfe)</small>
						</td>
						<td width="30%">
							<label for="" class="show">Código Municipío:</label>
							<select name="id_cidade" class="input-text form-control" disabled="disabled">
								<?php foreach( $NfeCidades as $city ) { ?>
								<?php
								$txt_a = converter_texto($end->cidade);
								$txt_b = converter_texto($city->nome);
								?>
								<option value="<?php echo $city->id?>"<?php echo ($txt_b == $txt_a || $city->id == $end->id_cidade) ? ' selected':null?>>
									<?php echo $city->nome?>/<?php echo $city->uf?> - <?php echo $city->cod_ibge?>
								</option>
								<?php } ?>
							</select>
							<small>(Dados de emissão para nfe)</small>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</form>
	<script>
		$(function(){
			$.fn.toggleText = function(t1, t2){
				if (this.text() === t1) 
					this.text(t2);
				else
					this.text(t1);
				return this;
			};
			
			$.fn.toggleAttr = function(a, b) {
				var c = (b === undefined);
				return this.each(function() {
					if((c && !$(this).is("["+a+"]")) || (!c && b)) $(this).attr(a,a);
					else $(this).removeAttr(a);
				});
			};

			$("[toggletext=toggletext]").click(function(e) {
				e.preventDefault();
				var even = $(this), 
					data_id = even.data('id');
				
				if(even.text() === 'Salvar') {
					var data_str = $('#' + data_id ).serialize();
					
					$.when(
						$.ajax({
							url: '/adm/vendas/vendas-enderecos.php?id=<?php echo $GET['id'];?>',
							type: 'post',
							data: data_str
						}),
						$.ajax({
							url: '/adm/vendas/vendas-detalhes.php?id=<?php echo $GET['id'];?>',
						})
					).then(function(end, ped) {
						var list_end = $("<div/>", { html: end });
						JanelaModal.html(list_end.find("#endereco_alterar").html());

						var list_ped = $("<div/>", { html: ped });
						$("#endereco_entrega").html(list_ped.find("#endereco_entrega").html());
					});
				}
				
				even.toggleText("Salvar", "Alterar");
				
				$.each($('#' + data_id + ' input, #' + data_id + ' select, #' + data_id + ' textarea'), function (index, el) {
					$(el).toggleClass('input-text input-text-edit');
					$(el).toggleAttr('disabled');
				});
			});
		});
	</script>
</div>
<?php
include '../rodape.php';