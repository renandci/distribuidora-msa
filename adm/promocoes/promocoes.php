<?php
include '../topo.php';

$in_black = array();
$in_black['marcas'][] = null;
$in_black['produtos'][] = null;
$setup_ini_date = null;
$setup_fin_date = null;
$setup_ini_time = null;
$setup_fin_time = null;
$setup_value = null;
$setup_text = 'BLACK FRIDAY';
$setup_color = 'ffffff';
$setup_hex = '000000';
$id = null;
$in_black['marcas'] = [];

$Marcas = Marcas::all(['conditions' => [ 'loja_id=? and excluir = 0', $CONFIG['loja_id'] ]]);
$Promocoes = Promocoes::all([
	'conditions' => [ 
		sprintf('loja_id=%u', $CONFIG['loja_id'])
	], 
	'order' => 'id desc',
]);
?>
<style>
	body{ background-color: #f1f1f1 }
	.btn-black{
		background-color: #000;
		border-color: #000;
		color: #fff;
	}
	.select2-selection--multiple{
		overflow: hidden !important;
		height: auto !important;
	}
</style>

<div class="container">
	<form class="row mt50" action="/adm/promocoes/promocoes-action.php" method="post">
		<h3 class="pull-left">Descontos e promoções</h3>
		<button type="submit" class="btn btn-primary pull-right">adicionar promoções</button>
	</form>
	<?php 
	$ajax = [];
	foreach( $Promocoes as $rws ) 
	{ 
		$setup_ini_date = ! empty($rws->setup_ini) ? date('d/m/Y', strtotime($rws->setup_ini)) : null;
		$setup_fin_date = ! empty($rws->setup_fin) ? date('d/m/Y', strtotime($rws->setup_fin)) : null;
		$setup_ini_time = ! empty($rws->setup_ini) ? date('H:i:s', strtotime($rws->setup_ini)) : null;
		$setup_fin_time = ! empty($rws->setup_fin) ? date('H:i:s', strtotime($rws->setup_fin)) : null;
		$id_marca = $rws->id_marca;
		
		$json = $rws->to_array([
			'include' => [
				'produto' => [
					'include' => [
						'capa'
					]		
				]
			]
		]);

		$ajax[] = [
			'id' => $json['produto']['codigo_id'],
			'text' => $json['produto']['nome_produto'],
			'image' => Imgs::src($json['produto']['capa']['imagem'], 'smalls'),
		];

		?>
		<form class="row" action="/adm/promocoes/promocoes-action.php" method="post">
			<input type="hidden" name="id" class="form-control count-input" maxlength="15" value="<?php echo $rws->id?>"/>
			<div class="panel panel-default">
				<div class="panel-heading panel-store">
					<?php echo ( ! empty( $rws->setup_text ) ? $rws->setup_text : 'Adicionar Descontos e Promoções' )?>
					<a href="/adm/promocoes/promocoes-action-reverse.php?id_black_friday_produtos=<?php echo $rws->id?>" class="btn btn-danger btn-xs pull-right" onclick="return confirm('Deseja cancelar a promoção ativa?');">
						<i class="fa fa-ban"></i> 
						Cancelar promoção
					</a>
				</div>
				<div class="panel-body">
					<div class="h3 mt0">
						<?php echo ( ! empty( $rws->setup_text ) ? $rws->setup_text : 'Descontos e promoções' )?>
					</div>
					<div class="border-bottom mt5 mb10"></div>					
					<div class="row">
						<div class="form-group col-lg-4 col-md-4 col-sm-12 col-xs-12">
							<label>
								Por Marcas:
								<small>Ative o desconto pelas marcas.</small>
							</label>
							<select name="id_marca" class="form-control">
								<option value="0">Nenhum</option>
								<?php foreach( $Marcas as $rwsm ) { ?>
								<option value="<?php echo $rwsm->id?>"<?php echo ($rwsm->id == $rws->id_marca ? ' selected':'')?>>
									<?php echo $rwsm->marcas?>
								</option>
								<?php } ?>
							</select>
						</div>
						<div class="form-group col-lg-8 col-md-8 col-sm-12 col-xs-12">
							<label>
								Produto Específico:
								<small>Ative o desconto por produto.</small>
							</label>
							<select name="codigo_id" class="form-control select_no_init" data-initvalue='<?php echo json_encode($ajax)?>'>
								<!-- <option value="0">Nenhum</option>
								<?php foreach( $Marcas as $rwsm ) { ?>
								<option value="<?php echo $rwsm->id?>"<?php echo ($rwsm->id == $rws->id_marca ? ' selected':'')?>>
									<?php echo $rwsm->marcas?>
								</option>
								<?php } ?> -->
							</select>
						</div>
						<div class="form-group col-lg-6 col-md-6 col-sm-12 col-xs-12">
							<label>Texto</label>
							<input type="text" name="setup_text" class="form-control count-input" maxlength="15" value="<?php echo $rws->setup_text?>"/>
						</div>
						<div class="form-group col-lg-3 col-md-3 col-sm-12 col-xs-12">
							<label>Cor do texto</label>
							<input type="text" name="setup_color" class="form-control count-input" maxlength="15" value="<?php echo $rws->setup_color?>" onchange="$(this).css({'background-color' : '#' + this.value });" id="setup_color_<?php echo $rws->id?>"/>
						</div>
						<div class="form-group col-lg-3 col-md-3 col-sm-12 col-xs-12">
							<label>Cor de fundo</label>
							<input type="text" name="setup_hex" class="form-control" value="<?php echo $rws->setup_hex?>" onchange="$(this).css({'background-color' : '#' + this.value });" id="setup_hex_<?php echo $rws->id?>"/>
						</div>
						<div class="form-group col-lg-6 col-md-6 col-sm-12 col-xs-12">
							<label>Data inicio</label>
							<input type="text" name="setup_ini_date" class="form-control datepicker" placeholder="Data inicio" value="<?php echo $setup_ini_date?>"/>
						</div>
						<div class="form-group col-lg-6 col-md-6 col-sm-12 col-xs-12">
							<label>Hora inicio</label>
							<input type="text" name="setup_ini_time" class="form-control datepicker_time" placeholder="Hora inicio" value="<?php echo $setup_ini_time?>"/>
						</div>
						<div class="form-group col-lg-6 col-md-6 col-sm-12 col-xs-12">
							<label>Data final</label>
							<input type="text" name="setup_fin_date" class="form-control datepicker" placeholder="Data final" value="<?php echo $setup_fin_date?>"/>
						</div>
						<div class="form-group col-lg-6 col-md-6 col-sm-12 col-xs-12">
							<label>Hora final</label>
							<input type="text" name="setup_fin_time" class="form-control datepicker_time" placeholder="Hora final" value="<?php echo $setup_fin_time?>"/>
						</div>
						<div class="form-group col-lg-4 col-md-4 col-sm-12 col-xs-12">
							<label>Tipo do Desconto</label>
							<select name="setup_type" class="form-control">
								<option value="$"<?php echo ($rws->setup_type == '$' ? ' selected': '')?>>Em Dinheiro</option>
								<option value="%" <?php echo (($rws->setup_type == '%' || $rws->setup_type == '') ? ' selected': '')?>>Em Porcentagem</option>
							</select>
						</div>
						<div class="form-group col-lg-8 col-md-8 col-sm-12 col-xs-12">
							<label>
								Digite o valor
								<small>Ex: (50,00 ou 5)</small>
							</label>
							<input type="tel" name="setup_value" class="form-control preco-mask text-right" value="<?php echo $rws->setup_value?>"/>
						</div>
						
						<div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
							<button type="submit" class="btn btn-primary btn-lg">salvar/editar</button>
						</div>
						<!--
						<div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center<?php echo count($ProdutosBlackFriday)==0?' hidden':''?>">
						</div>
						-->
					</div>
					<!--<small>* Nota: Nosso sistema se encarregar se.</small>-->
				</div>
			</div>
			<?php ob_start(); ?>
			<script>
				(function($$){
					
					$$('input[id=setup_color_<?php echo $rws->id?>]').ColorPicker({
						color: '#ffffff',
						onChange: function (hsb, hex, rgb) {
							$('input[id=setup_color_<?php echo $rws->id?>]').css({'background-color' : '#' + hex }).val( hex );
						}
					}).change();

					$$('input[id=setup_hex_<?php echo $rws->id?>]').ColorPicker({
						color: '#000000',
						onChange: function (hsb, hex, rgb) {
							$('input[id=setup_hex_<?php echo $rws->id?>]').css({'background-color' : '#' + hex }).val( hex );
						}
					}).change();
				})(jQuery);
				
			</script>
			<?php $SCRIPT['script_manual'] .= ob_get_clean(); ?>
		</form>
	<?php } ?>
	<?php ob_start(); ?>
	<script>
		$("select[name=codigo_id]").select2({ 
			tags: true, 
			allowClear: true, 
			minimumInputLength: 3,
			placeholder: "Pesquisar...",
			
			ajax: {
				url: "<?php echo sprintf('%spesquisa-rapida', URL_BASE)?>",
				delay: 550,
				dataType: 'json',
				data: function (params) {
					return {
						pesquisar: params.term,
						pesquisar_select: 1
					}
				}
			},
			initSelection: function (element, callback) {
				var json = $(element).data("initvalue")||null;
				callback(json);
				$(element).html($("<option/>", { value: json[0].id, text: json[0].text })).change();
			},
			templateResult: format_state, 
		});

		$(".datepicker_time").datetimepicker({
			format: 'H:i:s',
			datepicker: false,
			timeFormat: 'hh:mm:00'
		});
	</script>
	<?php $SCRIPT['script_manual'] .= ob_get_clean(); ?>
</div>

<?php
include '../rodape.php';