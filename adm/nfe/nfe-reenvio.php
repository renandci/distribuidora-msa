<?php

use NFePHP\DA\NFe\Danfe;
use NFePHP\NFe\Common\Standardize;
use NFePHP\NFe\Complements;

include_once '../topo.php';

$dir = sprintf('%sassets/%s/xml/', PATH_ROOT, ASSETS);
$filename = '%s%s.xml';

// use somente imagens JPEG
$pathLogo = sprintf('%s/assets/%s/imgs/%s', PATH_ROOT, ASSETS, str_replace('.png', '.jpg', $CONFIG['logo_desktop']));

if (isset($GET['acao']) && $GET['acao'] == 'send_nfe') {
	try {
		if (strlen($POST['email']) == 0)
			throw new Exception('Error');

		$list = [];

		$arquivo = sprintf('xml-mensal-%s.zip', date('d-m-Y-H-i'));

		list($mes, $ano) = explode('-', $GET['mes']);

		$conditions['conditions'] = sprintf('loja_id=%u AND YEAR(created_at)="%s" AND MONTH(created_at)="%s" AND status=%u', $CONFIG['loja_id'], $ano, $mes, $GET['status']);
		$conditions['order'] = 'YEAR(created_at) DESC, MONTH(created_at) DESC';

		$NfeNotas = NfeNotas::all($conditions);

		$count_xmls = 0;

		foreach ($NfeNotas as $rws) {
			$xml = $dir . $rws->chavenfe . '-autorizada.xml';
			$pdf = $dir . $rws->chavenfe . '.pdf';

			$list[] = $xml;
			$list[] = $pdf;

			$pdfCount = (int)file_exists($pdf);
			if ($pdfCount == 0) {
				$docxml = file_get_contents($xml);
				$danfe = new Danfe($docxml, 'P', 'A4', $pathLogo);
				$danfe->montaDANFE();
				$pdfDanfe = $danfe->render();

				file_put_contents($pdf, $pdfDanfe);
				chmod($pdf, 0644);
			}

			$created_at = $rws->created_at;
			$count_xmls++;
		}

		create_zip($list, $arquivo, true);

		$html = ""
			. "<tr>"
			. "<td>"
			. "<br/>"
			. "Sengue em anexo os XMLs das Nfes " . strftime('do mês de %B de %Y', strtotime($created_at)) . "<br/>"
			. "No anexo temos um total de " . $count_xmls . " de Nota(s) gerada(s).<br/><br/>"
			. "</td>"
			. "</tr>";

		$CONTEUDO_MAIL = email_body($CONFIG, $html);

		$mail->AddAttachment($arquivo, $arquivo);
		$mail->setFrom($CONFIG['email_contato'], $CONFIG['nome_fantasia']);
		$mail->addAddress($POST['email']);
		$mail->addBCC($CONFIG['email_contato'], $CONFIG['nome_fantasia']);
		$mail->Body = $CONTEUDO_MAIL;
		$mail->Subject = 'Anexo de Xml mensal de Nfe';

		$send = $mail->send();
		$mail->SmtpClose();

		$arr[$GET['mes']][$GET['status']] = [
			'user' => $_SESSION['admin']['apelido'],
			'text' => $count_xmls . ' NFe foram enviadas com sucesso.',
			'email' => $POST['email'],
			'created_at' => date('d/m/Y H:i')
		];

		$id = (int)$_SESSION['admin']['id_usuario'];

		$json = json_encode($arr);

		Logs::create_logs($json, $id, 'log', 'nfe_notas');

		if (!$send)
			throw new Exception(1);

		unlink($arquivo);
		header('location: /adm/nfe/nfe-reenvio.php?message=Notas enviadas com sucesso!');
		return;
	} catch (\Exception $e) {
		header('location: /adm/nfe/nfe-reenvio.php?message=Não foi possível enviar os e-mail, tente novamente.' . $e->getMessage());
		return;
	}
}

try {
	$xmlIds = null;
	$countXmlInvalid = 0;

	$dateIni = date('Y-m-01 00:00:00', strtotime('-1 months'));
	$dateFini = date('Y-m-t 23:59:59', strtotime('-1 months'));

	$NfeNotas = NfeNotas::all(['conditions' => ['loja_id=? and status in(1,2,3) and created_at between ? and ?', $CONFIG['loja_id'], $dateIni, $dateFini]]);

	foreach ($NfeNotas as $rws) {
		$consultaXml = sprintf($filename, $dir, "{$rws->id_lote}-consultas");

		$testFile = (int)file_exists($consultaXml);
		if ($testFile > 0) {
			$protXml = file_get_contents($consultaXml);

			$stdCl = new Standardize($protXml);

			$std = $stdCl->toStd();

			$std->protNFe = !is_array($std->protNFe) ? [$std->protNFe] : $std->protNFe;

			foreach ($std->protNFe as $xml) {
				$autorizadaXml = sprintf($filename, $dir, "{$xml->infProt->chNFe}-autorizada");

				$testFileAutoriza = (int)file_exists($autorizadaXml);

				if ($testFileAutoriza == 0) {
					if ($xml->infProt->cStat == 100) {
						$assinadaXml = sprintf($filename, $dir, "{$xml->infProt->chNFe}-assinada");

						$testFileAssinada = (int)file_exists($assinadaXml);

						// Tenta refazer a xml - NFe
						if ($testFileAssinada > 0) {

							$assinadaXml = file_get_contents($assinadaXml);
							$Complements = Complements::toAuthorize($assinadaXml, $protXml);

							file_put_contents($autorizadaXml, $Complements);
							chmod($autorizadaXml, 0777);

							// $NfeNotasXml = NfeNotas::first(['conditions' => ['chavenfe=?', $xml->infProt->chNFe]]);
							// $NfeNotasXml->motivo = !empty($xml->infProt->cStat) && $xml->infProt->cStat != 100 ? $xml->infProt->xMotivo : null;
							// $NfeNotasXml->nrprot = !empty($xml->infProt->nProt) ? $xml->infProt->nProt : null;

							if (!empty($xml->infProt->cStat) && $xml->infProt->cStat != 100)
								printf('<div class="alert alert-warning">[%s] %s<br/>Chave %s</div>', $xml->infProt->cStat, $xml->infProt->xMotivo, $xml->infProt->chNFe);
							else
								printf('<div class="alert alert-success">[%s] Chave %s do Ped. %s corrigida</div>', $xml->infProt->cStat, $xml->infProt->chNFe, $rws->pedido->codigo);

							$NfeNotasXml = NfeNotas::find(['conditions' => ['chavenfe=?', $xml->infProt->chNFe]]);
							$NfeNotasXml->id = (int)$NfeNotasXml->id;
							$NfeNotasXml->motivo = !empty($xml->infProt->cStat) && $xml->infProt->cStat != 100 ? $xml->infProt->xMotivo : null;
							$NfeNotasXml->nrprot = !empty($xml->infProt->nProt) ? $xml->infProt->nProt : null;
							$NfeNotasXml->status = !empty($xml->infProt->cStat) && $xml->infProt->cStat == 100 ? 1 : 0;

							if (!empty($NfeNotasXml->id))
								$NfeNotasXml->save_log();
						}
					}
				}

				// printf('<pre>%s</pre>', print_r($xml, 1));

				// $NfeNotasXml = NfeNotas::find(['conditions' => ['chavenfe=?', $xml->infProt->chNFe]]);
				// $NfeNotasXml->id = (int)$NfeNotasXml->id;
				// $NfeNotasXml->motivo = !empty($xml->infProt->cStat) && $xml->infProt->cStat != 100 ? $xml->infProt->xMotivo : null;
				// $NfeNotasXml->nrprot = !empty($xml->infProt->nProt) ? $xml->infProt->nProt : null;
				// $NfeNotasXml->status = !empty($xml->infProt->cStat) && $xml->infProt->cStat == 100 ? 1 : 0;
				// if( !empty($NfeNotasXml->id) )
				// $NfeNotasXml->save_log();

				// if(!empty($xml->infProt->cStat) && $xml->infProt->cStat != 100)
				//     printf('<div class="alert alert-warning">[%s] %s<br/>Chave %s</div>' , $xml->infProt->cStat, $xml->infProt->xMotivo, $xml->infProt->chNFe);
			}
		}
	}
} catch (\Exception $e) {
	printf('<pre class="alert alert-danger">%s</pre>', print_r($e, 1));
}

// Isso pode deixar um pouco lento
$Logs = Logs::all(['select' => 'log', 'conditions' => ['tabela = "nfe_notas" and acao = "log"']]);
?>
<style>
	body {
		background-color: #f1f1f1
	}
</style>

<div class="container">
	<div class="row">
		<?php if (isset($GET['message']) && $GET['message'] != '') { ?>
			<div class="alert alert-success alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<?php echo $GET['message'] ?>
			</div>
		<?php } ?>
		<div class="panel panel-default">
			<div class="panel-heading panel-store"><i class="fa fa-archive"></i> Lista de Xml/Mês</div>
			<div class="panel-body">
				<table class="table table-striped table-hover">
					<tr class="active text-uppercase">
						<th>Total de Nf-e</th>
						<th class="text-center">Ações</th>
					</tr>
					<?php
					$i = 0;
					$max = 25;
					$pag = isset($GET['pag']) && $GET['pag'] > 0 ? $GET['pag'] : 1;
					$ini = (($pag * $max) - $max);

					$conditions = null;
					$conditions['select'] = 'status, created_at, count(id) as total';
					$conditions['conditions'] = sprintf('(id_pedido > 0 OR id_skyhub_orders > 0) AND loja_id=%u ', $CONFIG['loja_id']);
					$conditions['group'] = 'DATE_FORMAT(created_at, "%m-%Y"), status';
					// $conditions['group'] = '(SUBSTRING(chavenfe, -18, 8) * 1) and id desc, status';

					$Total = NfeNotas::all($conditions);
					// echo NfeNotas::connection()->last_query;
					$TotalCount = (int)count($Total);

					$TotalPages = ceil($TotalCount / $max);

					$conditions['order'] = 'id desc';
					$conditions['limit'] = $max;
					$conditions['offset'] = ($max * ($pag - 1));

					$NfeNotas = NfeNotas::all($conditions);
					foreach ($NfeNotas as $rws) {
						$html = null;

						$key = 'log';

						$search_a = $rws->created_at->format('m-Y');

						$search_b = $rws->status;

						$iCount = 0;

						foreach ($Logs as $loop) {
							$array = json_decode($loop->$key);
							$array = $array->$search_a;
							$array = $array->$search_b;

							if (!empty($array)) {
								$html['log'][] = $array;
								++$iCount;
							}
						}

					?>

						<tr<?php echo ($rws->status == 2 ? ' class="text-danger danger"' : '');
								echo ($rws->status == 3 ? ' class="text-warning warning"' : '') ?>>
							<td>
								<span class="bold ft16px"><?php echo (str_pad($rws->total, 2, '0', STR_PAD_LEFT)) ?></span> nota(s) no mês de
								<?php echo (!empty($rws->created_at) ? strftime('%b de %Y', strtotime($rws->created_at)) : '') ?>
								<?php echo ($rws->status == 2 ? ' - Notas canceladas' : '') ?>
								<?php echo ($rws->status == 3 ? ' - Nota de devolução' : '') ?>

								<?php if ($iCount > 0) { ?>
									<span style="cursor: pointer" class="badge pull-right btn-info-modal" data-json='<?php echo json_encode($html) ?>'>?</span>
								<?php } ?>

							</td>
							<td nowrap="nowrap" width="1%">
								<a href="/adm/nfe/nfe-reenvio.php?mes=<?php echo $rws->created_at->format('m-Y'); ?>&status=<?php echo $rws->status ?>&acao=send_nfe" class="btn btn-xs btn-primary btn-envio-nfe">
									<i class="fa fa-send"></i>
									enviar notas
								</a>
							</td>
							</tr>
						<?php } ?>
						<td colspan="2">
							<div class="paginacao">
								<?php
								if ($TotalPages > 0) {
									for ($i = $pag - 1, $limiteDeLinks = $i + 2; $i <= $limiteDeLinks; ++$i) {
										if ($i < 1) {
											$i = 1;
											$limiteDeLinks = 2;
										}

										if ($limiteDeLinks > $TotalPages) {
											$limiteDeLinks = $TotalPages;
											$i = $limiteDeLinks - 2;
										}

										if ($i < 1) {
											$i = 1;
											$limiteDeLinks = $TotalPages;
										}

										if ($i == $pag) { ?>
											<span class="at plano-fundo-adm-001"><?php echo $i ?></span>
										<?php } else { ?>
											<a href="/adm/nfe/nfe-reenvio.php?pag=<?php echo $i ?>" class="btn-paginacao"><?php echo $i ?></a>
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
	var options_validate = {
		errorClass: "help-block ft12px",
		errorElement: "span",
		highlight: function(element, errorClass) {
			$(element).closest('.form-group').addClass('has-error');
		},
		unhighlight: function(element, errorClass) {
			$(element).closest('.form-group').removeClass('has-error');
		},
		rules: {
			email: {
				required: true,
				email: true
			},
			// arquivo: { required: true },
		},
		messages: {
			email: {
				required: "Informe o e-mail do contador",
				email: "Ops, informe um email válido"
			},
			// arquivo: { required: "Selecione o arquivo para o envio" },
		},
		submitHandler: function(form) {

			var data_action = $(form).attr("action"),
				data_serialize = $(form).serialize();

			$.ajax({
				type: "POST",
				url: data_action,
				data: data_serialize,
				success: function(str) {
					var list = $("<div/>", {
						html: str
					});
					$("#conteudos-recarregar").html(list.find("#conteudos-recarregar").html());
					$(form)[0].reset();
					JanelaModal.dialog("close");
				}
			});
		}
	};

	JanelaModalInfo = $("<div/>", {
		id: "JanelaModalInfo"
	}).dialog({
		title: "Log de informações",
		width: 450,
		height: 395,
		modal: true,
		autoOpen: false
	});

	JanelaModal.dialog({
		title: "Confimação de e-mail",
		width: 450,
		height: 195,
	}).html([
		$("<form/>", {
			id: "form_envio",
			method: "POST",
			html: [
				$("<div/>", {
					class: "form-group",
					html: [
						$("<label/>", {
							for: "email",
							html: "Digite o e-mail do seu contador"
						}),
						$("<input/>", {
							id: "email",
							name: "email",
							type: "email",
							class: "form-control"
						}),
					]
				}),
				// $("<div/>", {
				// class: "form-group",
				// html: [
				// $("<label/>", {
				// for: "arquivo", 
				// html: "Selecione o arquivo com as nfe"
				// }),
				// $("<input/>", {
				// id: "arquivo", 
				// name: "arquivo", 
				// type: "file",
				// class: "form-control",
				// accept: ".xml,.zip"
				// })
				// ]
				// }),
				$("<button/>", {
					type: "submit",
					class: "btn btn-primary pull-right",
					html: "enviar"
				})
			]
		})
	]);

	// formulario para envio
	$("#form_envio").validate(options_validate);

	// Action da tela
	$("#conteudos-recarregar").on("click", ".btn-envio-nfe", function(e) {
		e.preventDefault();
		var href = this.href || $(e.target).attr("href");
		JanelaModal.dialog("open").find("#form_envio").attr({
			action: href
		});
	});

	// Action da tela info log
	$("#conteudos-recarregar").on("click", ".btn-info-modal", function(e) {
		var el = $(e.currentTarget),
			json = (el.data("json")).log,
			group = null,
			html = $("<div/>");

		$.map(json, function(r, i) {
			if (group != r.user) {
				group = r.user;
				html.append([
					$("<div/>", {
						html: group,
						class: "mt0 mb0 ft16px bold"
					}),
				])
			}
			html.append([
				$("<div/>", {
					html: [
						$("<hr/>", {
							class: "mt5 mb10"
						}),
						r.text,
						$("<br/>"),
						["Para o e-mail", r.email].join(" "),
						$("<br/>"),
						$("<span/>", {
							html: ["Hora de Envio", r.created_at].join(": "),
							class: "ft11px"
						})
					],
					class: "ft13px"
				})
			])
		})

		JanelaModalInfo.html(html.html()).dialog("open");

	});
</script>
<?php
$SCRIPT['script_manual'] .= ob_get_clean();
?>
<?php
include_once '../rodape.php';
