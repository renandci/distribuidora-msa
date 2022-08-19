<?php 

set_time_limit(-1);

use Ifsnop\Mysqldump as IMysqldump;

include '../topo.php';

$DIR_ABSOLUTE = PATH_ROOT . '/adm/backup/';
$DIR_ABSOLUTE = './adm/backup/';

	$dump_settings = [
		'include-tables' => [],
		'exclude-tables' => [],
		// 'exclude-tables' => array('cidades', 'nfe_cidades', 'nfe_emitentes', 'nfe_ncm', 'nfe_notas', 'adm', 'adm_perfils', 'adm_grupos', 'adm_permissoes'),
		'compress' => IMysqldump\Mysqldump::GZIP,
		'init_commands' => array(),
		'no-data' => array(),
		'reset-auto-increment' => true,
		'add-drop-database' => false,
		'add-drop-table' => true,
		'add-drop-trigger' => true,
		'add-locks' => false,
		'complete-insert' => false,
		'databases' => false,
		'default-character-set' => IMysqldump\Mysqldump::UTF8,
		'disable-keys' => true,
		'extended-insert' => true,
		'events' => false,
		'hex-blob' => true, /* faster than escaped content */
		'insert-ignore' => true,
		'net_buffer_length' => IMysqldump\Mysqldump::MAXLINESIZE,
		'no-autocommit' => true,
		'no-create-info' => false,
		'lock-tables' => false,
		'routines' => false,
		'single-transaction' => true,
		'skip-triggers' => false,
		'skip-tz-utc' => false,
		'skip-comments' => false,
		'skip-dump-date' => false,
		'skip-definer' => false,
		'where' => sprintf('loja_id=%u or loja_id=0', $CONFIG['loja_id']),
		// 'where' => null,
		/* deprecated */
		'disable-foreign-keys-check' => true
	];

	if( isset($GET['acao']) && $GET['acao'] == 'FazerBackUpDB' ) {
		try {
			
			$LojasBackUp = new LojasBackUp();
			
			// $LojasBackUp::$validates_presence_of[0] = ['descricao', 'message' => 'Selecione uma marca!'];
			
			$return = $LojasBackUp->new_save(['descricao' => 'Backup do Banco de Dados', 'loja_id' => $CONFIG['loja_id']]);

			$arquivo = sprintf('backup-banco-de-dados-%s-%u.sql.zip', date('d-m-Y-H-i'), $return['id']);
			
			$dump = new IMysqldump\Mysqldump('mysql:host=' . HOST . ';dbname=' . DB, USER, PASS, $dump_settings);
			
			$buffer = $dump->start($arquivo);

			header(sprintf('location: /adm/backup/backup-download.php?arquivo=%s', $arquivo));
			return;
			
		} catch (\Exception $e) {
			echo 'mysqldump-php error: ' . $e->getMessage();
		}
	}

	if( isset($GET['acao']) && $GET['acao'] == 'FazerBackUpImg' ) {

		$dir = './../../assets/' . ASSETS;
		
		try {
			$LojasBackUp = new LojasBackUp();
			
			// $LojasBackUp::$validates_presence_of[0] = ['descricao', 'message' => 'Selecione uma marca!'];
			
			$return = $LojasBackUp->new_save(['descricao' => 'Backup de Imagens', 'loja_id' => $CONFIG['loja_id']]);

			$arquivo = sprintf('backup-de-imagens-%s-%u.zip', date('d-m-Y-H-i'), $return['id']);
			
			$array = show_files( $dir );
			
			$data = explode(',', $array);
			
			$buffer = create_zip($data, $arquivo);

			header(sprintf('location: /adm/backup/backup-download.php?arquivo=%s', $arquivo));
			return;
		} catch (\Exception $e) {
			echo $e->getMessage();
		}
	}
	
	if( isset($GET['acao']) && $GET['acao'] == 'FazerBackUpNFe' ) {

		$dir = './../../assets/' . ASSETS . '/xml/';
		
		$dump_settings['include-tables'] = ['nfe_cidades', 'nfe_emitentes', 'nfe_notas', 'nfe_ncm'];
		
		try {
			
			$xmls_list = [];
			
			$xmls = NfeNotas::all(['conditions' => ['loja_id=? or loja_id=0 and id > 0', $CONFIG['loja_id']]]);
			
			foreach( $xmls as $rws ) {
				$xmls_list[] = $dir . $rws->chavenfe . '.xml';
			}
			
			$LojasBackUp = new LojasBackUp();
			
			// $LojasBackUp::$validates_presence_of[0] = ['descricao', 'message' => 'Selecione uma marca!'];
			
			$return = $LojasBackUp->new_save(['descricao' => 'Backup xml NFe', 'loja_id' => $CONFIG['loja_id']]);

			$arquivo = sprintf('backup-nfe-%s-%u.zip', date('d-m-Y-H-i'), $return['id']);
			
			$buffer = create_zip($xmls_list, $arquivo);

			header(sprintf('location: /adm/backup/backup-download.php?arquivo=%s', $arquivo));
			return;
			
		} catch (\Exception $e) {
			echo $e->getMessage();
		}
	}

	if(isset($GET['acao']) && $GET['acao'] == 'remove_backup' ) {
		
		$dir = $DIR_ABSOLUTE;
		
		$LojasBackUp = LojasBackUp::find((INT)$GET['id']);
		
		$add_ext = strstr($LojasBackUp->descricao, 'Bando de Dados') ? '.sql.zip' : '.zip';
		
		$file = $LojasBackUp->id . '-archive' . $add_ext;
		
		try {
			$is = @filesize("{$dir}{$file}");
			if( empty( $is ) )
				throw new Exception($file . ' inválido!');

			if( unlink("{$dir}{$file}") ) {			
				LojasBackUp::delete_log(['id' => $GET['id']]);
				header('location: /adm/backup/backup.php?message=Removido com sucesso!');
				return;
			}
			
		} catch (\Exception $e) {
			echo $e->getMessage();
			LojasBackUp::delete_log(['id' => $GET['id']]);
			header('location: /adm/backup/backup.php?message=Removido com sucesso!');
			return;
		}
	}

	?>
	<style>
		body{ background-color: #f1f1f1 }
	</style>
	<!-- <h2>BACKUP <small>Configuração de backup</small></h2> -->
	<?php if(isset($GET['message']) && $GET['message'] != '') { ?>
		<div class="alert alert-success alert-dismissible" role="alert">
		  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		  <?php echo $GET['message']?>.
		</div>
	<?php } ?>
	<div class="row">
		<div class="col-lg-4 col-lg-4 col-sm-6 col-xs-12">
			<div class="panel panel-default">
				<div class="panel-heading panel-store text-uppercase"><i class="fa fa-database"></i> Backup Banco de dados</div>
				<div class="panel-body">
					<?php if(isset($GET['message_db']) && $GET['message_db'] == 'ok') { ?>
					<div class="alert alert-info alert-dismissible" role="alert">
					  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					  <strong>BACKUP</strong> gerado com sucesso!.
					</div>
					<?php } ?>
					<small class="show">(O backup será somente voltado ao banco de dados do seu sistema.)</small>
					<a class="btn btn-danger btn-block mt5 mb15" href="/adm/backup/backup.php?acao=FazerBackUpDB">
						fazer backup agora
					</a>
					<!--
					<form action="/adm/backup.php" method="post" enctype="multipart/form-data">
						<button type="submit" class="btn btn-primary btn-block">restaurar backup agora</button>
						<input type="hidden" name="acao" value="RestaurarBackUp">
					</form>
					-->
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading panel-store text-uppercase"><i class="fa fa-picture-o"></i> Backup Banco de Imagens</div>
				<div class="panel-body">
					<?php if(isset($GET['message_img']) && $GET['message_img'] == 'ok') { ?>
					<div class="alert alert-info alert-dismissible" role="alert">
					  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					  <strong>BACKUP</strong> gerado com sucesso!.
					</div>
					<?php } ?>
					<small class="show">(O backup será somente voltado ao banco de imagens do seu sistema.)</small>
					<a class="btn btn-danger btn-block mt5 mb15" href="/adm/backup/backup.php?acao=FazerBackUpImg">
						fazer backup agora
					</a>
					<!--
					<form action="/adm/backup.php" method="post" enctype="multipart/form-data">
						<button type="submit" class="btn btn-block btn-primary">restaurar backup agora</button>
						<input type="hidden" name="acao" value="RestaurarBackUp">
					</form>
					-->
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading panel-store text-uppercase"><i class="fa fa-archive"></i> Backup de Xml NF-e</div>
				<div class="panel-body">
					<?php if(isset($GET['message']) && $GET['message'] == 'ok') { ?>
					<div class="alert alert-info alert-dismissible" role="alert">
					  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					  <strong>BACKUP</strong> gerado com sucesso!.
					</div>
					<?php } ?>
					<small class="show">(O backup será somente voltado ao banco de imagens do seu sistema.)</small>
					<a class="btn btn-danger btn-block mt5 mb15" href="/adm/backup/backup.php?acao=FazerBackUpNFe">
						fazer backup agora
					</a>
					<!--
					<form action="/adm/backup.php" method="post" enctype="multipart/form-data">
						<button type="submit" class="btn btn-primary btn-block">restaurar backup agora</button>
						<input type="hidden" name="acao" value="RestaurarBackUp">
					</form>
					-->
				</div>
			</div>
		</div>
		<div class="col-lg-8 col-lg-8 col-sm-6 col-xs-12">
			<div class="panel panel-default">
				<div class="panel-heading panel-store text-uppercase"><i class="fa fa-cloud-upload"></i> Lista de Backup</div>
				<div class="panel-body" id="panel-body">
					<table class="table table-striped">
					<?php
					$i = 0;
					$max = 25;
					$pag = isset($GET['pag']) && $GET['pag'] > 0 ? $GET['pag'] : 1;
					$ini = (($pag * $max) - $max);
					
					$conditions = null;
					$conditions['conditions'] = sprintf('loja_id=%u', $CONFIG['loja_id']);

					$tot = ceil(LojasBackUp::count($conditions) / $max);
					
					$conditions['order'] = 'id desc';
					$conditions['limit'] = $max;
					$conditions['offset'] = ($max * ($pag - 1));
					$LojasBackUp = LojasBackUp::all($conditions);
					
					foreach( $LojasBackUp as $rws ) { ?>
						<tr>
							<td><?php echo $rws->descricao?></td>
							<td nowrap="nowrap" width="1%">
								<a href="/adm/backup/backup.php?id=<?php echo $rws->id;?>&acao=remove_backup" class="btn btn-xs btn-danger" onclick="return confirm('Deseja realmente excluir');">
									<i class="fa fa-trash"></i> 
									excluir
								</a>
							</td>
						</tr>
					<?php } ?>
						<td colspan="2">
							<div class="paginacao">
								<?php
								
								if( $tot > 0 )
								{
									for( $i = $pag - 5, $limiteDeLinks = $i + 10; $i <= $limiteDeLinks; ++$i )
									{
										if($i < 1)
										{
											$i = 1;
											$limiteDeLinks = 9;
										}
									
										if($limiteDeLinks > $tot)
										{
											$limiteDeLinks = $tot; 
											$i = $limiteDeLinks - 10;
										}

										if($i < 1)
										{
											$i = 1;
											$limiteDeLinks = $tot;
										}
										
										if($i == $pag) { ?>
											<span class="at plano-fundo-adm-001"><?php echo $i?></span>
										<?php } else { ?>
											<a href="/adm/backup/backup.php?pag=<?php echo $i?>" class="btn-paginacao"><?php echo $i?></a>
										<?php }
									}
								}
								?>
							</div>
						</td>
					</table>
				</div>
			</div>
		</div>
	</div>
	<?php ob_start() ?>
	<script>
		$("#panel-body").on("click", "a.btn-paginacao", function(a){
			a.preventDefault();
			var e = $(this)||$(e.target),
				href = e.attr('href');
			$.ajax({
				url: href,
				beforeSend: function(){},
				complete: function(){},
				success: function(str){
					var list = $("<div/>", { html: str });
					$("#panel-body").html(list.find("#panel-body").html());
				}
			})
		});
	</script>
	<?php $SCRIPT['script_manual'] .= ob_get_clean();?>

<?php 
include '../rodape.php'; 