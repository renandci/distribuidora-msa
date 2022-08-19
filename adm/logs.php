<?php
include 'topo.php';

$where_array[0] = ' id > ? ';
$where_array[] = 0;

/**
 * Pesquisar log
 */
if( ! empty( $GET['q'] ) && $GET['q'] !== '' ) {
    $where_array[0] .= 'and log LIKE ? ';
    $where_array[] = "%{$GET['q']}%";
}

/**
 * Pesquisar data retornar data do mes selecionado com mes final atuals
 */
if( ! empty( $GET['data_ini'] ) && $GET['data_ini'] !== '' && $GET['data_fin'] === '' ) {
    $where_array[0] .= 'and created_at >= ? and created_at < ? ';
    $where_array[] = converterDatas($GET['data_ini']). ' 00:00:00';
    $where_array[] = date('Y-m-t 23:59:59');
}

/**
 * Pesquisar data retorna dia inicial do mes com data final selecionada
 */
if( ! empty( $GET['data_fin'] ) && $GET['data_fin'] !== '' && $GET['data_ini'] === '' ) {
    $where_array[0] .= 'and created_at >= ? and created_at < ? ';
    $where_array[] = date('Y-m-01 00:00:00');
    $where_array[] = converterDatas($GET['data_fin']). ' 23:59:59';
}

/**
 * Pesquisar data
 */
if( isset( $GET['data_fin'], $GET['data_ini']) && $GET['data_fin'] !== '' && $GET['data_ini'] !== '' ) {
    $where_array[0] .= 'and created_at >= ? and created_at <= ? ';
    $where_array[] = converterDatas($GET['data_ini']) . ' 00:00:00';
    $where_array[] = converterDatas($GET['data_fin']) . ' 23:59:59';
}

/**
 * Pesquisa por admin
 */
if( ! empty( $GET['adm_id'] ) && $GET['adm_id'] !== '' ) {
    $where_array[0] .= 'and adm_id = ? ';
    $where_array[] = (int)$GET['adm_id'];   
}

if( ! empty( $GET['log'] ) && $GET['log'] !== '' ) {
	$a = $GET['log'];
	$where_array[0] .= 'and acao like ? ';
	$where_array[] = in_array($a, ['insert', 'update', 'select', 'delete']) ? $a:null;
}

if( ! empty( $GET['pag'] ) && $GET['pag'] !== '' ) {
    $where_array[0] .= 'and id < ? ';
    $where_array[] = (int)$GET['pag'];   
}

$conditions['conditions'] = $where_array;
$conditions['order'] = 'created_at desc, id desc';
$conditions['limit'] = 25;

$User = Adm::all(['conditions' => ['id > ?', 0]]);

$Logs = Logs::all($conditions);
?>
<style>
	body{ background-color: #f1f1f1 }
</style>
<h2>Logs - <small>logs gerados no sistema, abrangindo cadastros, alterações e exclusões</small></h2>
<div id="div-edicao" class="panel panel-default">
	<div class="panel-body">
		<form action="/adm/logs.php">
			<p>Pesquisa avançada:</p>
			<div class="clearfix">
				<div class="col-md-8 form-group">
					<label>Descrição:</label>
					<input type="text" class="form-control" name="q">
				</div>
				<div class="col-md-2 form-group">
					<label>Data Inicial:</label>
					<input type="text" name="data_ini" class="form-control datepicker">
				</div>
				<div class="col-md-2 form-group">
					<label>Data Final:</label>
					<input type="text" name="data_fin" class="form-control datepicker">
				</div>
				<div class="col-md-3 form-group">
					<label>Ação:</label>
					<select name="log" class="form-control">
						<option value="">Selecione uma ação</option>
						<option value="insert">Cadastro</option>
						<option value="update">Alterações</option>
						<option value="delete">Exclusão</option>
						<option value="select">Selecões</option>
					</select>
				</div>
				<div class="col-md-2 form-group">
					<label>Usuários:</label>
					<select name="adm_id" class="form-control">
						<option value='0'>Selecione um usuário</option>
						<?php foreach ($User as $r) {
							echo sprintf('<option value="%u">%s</option>', $r->id, $r->apelido);
						} ?>
					</select>
				</div>
				
				<button type="submit" class="btn btn-primary mt25">
					<i class="fa fa-search"></i>
					buscar
				</button>
			</div>
		</form>
		<table width="100%" class="table">
			<tr class="text-uppercase plano-fundo-adm-001<?php echo !empty( $GET['pag'] ) && $GET['pag'] !== '' ? ' hidden' : ''?>">
				<th>Log</th>
				<th>Ação</th>
				<th class="text-center">Tabela</th>
				<th class="text-center">Usuário</th>
				<th class="text-center">IP</th>
				<th class="text-center">Data</th>
			</tr>
			<?php
			$date = null;
			foreach ( $Logs as $val ) {
				
				$explode = explode("\n", $val->log);

				$get_table = strstr($explode[0], ':');
				$get_table = trim($get_table);
				$get_table = strtolower(ltrim($get_table, ':'));
				$get_table = strstr($explode[1], 'Cadastrou de Produto') ? 'produtos' : $get_table;
				$get_table = strstr($explode[1], 'Adicionou Menu') ? 'produtos_menus' : $get_table;

				$get_descricao = strstr($explode[1], 'alterado') ? 'update' : null;
				$get_descricao = strstr($explode[1], 'removido') ? 'delete' : $get_descricao;
				$get_descricao = strstr($explode[1], 'inserido') || strstr($explode[1], 'Cadastrou de Produto') || strstr($explode[1], 'Adicionou Menu') ? 'insert' : $get_descricao;
			
				if( empty($val->tabela) ) 
				{
					$val->tabela = $get_table;
					$val->log = count($explode) > 1 ? preg_replace('/^.+\n/', '', $val->log) : $val->log;
					$val->save();
				}

				if( $date != $val->created_at->format('Y-m-d') ) {
					$date = $val->created_at->format('Y-m-d');
					echo sprintf('<tr class="plano-fundo-adm-003"><td colspan="6" class="ft16px">Eventos - %s</td></tr>', $date);
				}
				

				$val->acao = str_replace(['select', 'update', 'delete', 'insert'], ['Leitura', 'Alteração', 'Excluiu', 'Cadastrou'], $val->acao);

				echo ''
					. sprintf('<tr class="in-hover" data-id="%u">', $val->id)
					. sprintf('<td>%s</td>', nl2br($val->log))
					. sprintf('<td nowrap="nowrap" width="1%%">%s</td>', $val->acao)
					. sprintf('<td nowrap="nowrap" width="1%%" align="center">%s</td>', $val->tabela)
					. sprintf('<td nowrap="nowrap" width="1%%">%s</td>', $val->user->apelido)
					. sprintf('<td nowrap="nowrap" width="1%%">%s</td>', $val->ip)
					. sprintf('<td nowrap="nowrap" width="1%%">%s</td>', $val->created_at->format('d/m/Y H:i'))
					. '</tr>';
			}
			?>
		</table>
	</div>
	<span class="show text-center">
		<i class="fa fa-spinner fa-spin fa-fw"></i>
	</span>
</div>
<?php ob_start(); ?>
<script>
	$(window).scroll(function() {
	    if($(window).scrollTop() + $(window).height() >= $(document).height()) {
	        var last_id = $(".in-hover:last").data("id");
	        load_last_data(last_id);
	    }
	});
    load_last_data = (function (last_id) {
        $.ajax({
            url: "/adm/logs.php",
            data: {
                q: "<?php echo $GET['q'] ? $GET['q'] : ''?>",
                log: "<?php echo $GET['log'] ? $GET['log'] : ''?>",
                data_ini: "<?php echo $GET['data_ini'] ? $GET['data_ini'] : ''?>",
                data_fin: "<?php echo $GET['data_fin'] ? $GET['data_fin'] : ''?>",
                pag: last_id
            },
            success: function(data) {
                var list = $("<div/>",{ html: data });
                $("table").append( list.find("table").html() );
            }
        });
    });
</script>
<?php 
$SCRIPT['script_manual'] .= ob_get_clean(); 
include 'rodape.php';