<?php
defined('PATH_ROOT') || define('PATH_ROOT', realpath($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR);
require_once PATH_ROOT . 'app/settings.php';
require_once PATH_ROOT . 'app/vendor/autoload.php';
require_once PATH_ROOT . 'app/settings-config.php';
require_once PATH_ROOT . 'assets/' . ASSETS .  '/settings.php';
require_once PATH_ROOT . 'adm/correios/correios-bootstrap.php';
require_once PATH_ROOT . 'app/includes/bibli-funcoes.php';

$modalidade = [
	3 => 'PACKPAGE',
	4 => 'RODOVIÁRIO',
	9 => '.COM',
	40 => 'PICKUP',
];

$conditions['group'] = 'jadlog_etiqueta.id';
$conditions['conditions'] = '0 = 0 ';
$conditions['conditions'] .= (!empty($GET['etiquetas_id']) && $GET['etiquetas_id'] > 0 ? sprintf('and jadlog_etiqueta.id_pedido=%u ', $GET['etiquetas_id']):null);
$conditions['conditions'] .= (!empty($GET['date_group']) && $GET['date_group'] > 0 ? sprintf('and date_format(jadlog_etiqueta.created_at, "%%Y%%m%%d")="%u" ', $GET['date_group']):null);

$JadLogEtiqueta = JadLogEtiqueta::all($conditions);


// mm: altura x largura = 140 x 105
$pdf = new MyFpdf('P', 'mm', [115, 105]);
$pdf->AddFont('Calibri', '', 'Calibri.php');
$pdf->AddFont('Calibri Bold', '', 'calibri-bold-2.php');

$pdf->SetFont('Calibri', '', 10);
$pdf->SetMargins(2, 2, 0);

foreach( $JadLogEtiqueta as $rs ) 
{
	$rs = $rs->to_array([
		'include' => [
			'skyhub_order' => [
				'include' => [
					'skyhub_produto'
				]
			],
			'pedido' => [
				'include' => [
					'nfe_notas',
					'pedido_cliente',
					'pedido_endereco',
					'pedidos_vendas' => [
						'include' => [
							'produto' => [
								'include' => [
									'freteproduto',
									'grid_kits' => [
										'produto'
									]
								]
							]
						]
					]
				]
			]
		]
	]);

	
	$id 			= !empty($rs['skyhub_order']['id']) ? $rs['id'] : $rs['id'];
	$nome 			= !empty($rs['skyhub_order']['id']) ? $rs['skyhub_order']['nome_cliente'] : $rs['pedido']['pedido_cliente']['nome'];
	$email 			= !empty($rs['skyhub_order']['id']) ? $rs['skyhub_order']['email'] : $rs['pedido']['pedido_cliente']['email'];
	$telefone 		= !empty($rs['skyhub_order']['id']) ? $rs['skyhub_order']['telefone'] : $rs['pedido']['pedido_cliente']['telefone'];	
	$endereco 		= !empty($rs['skyhub_order']['id']) ? $rs['skyhub_order']['endereco'] : $rs['pedido']['pedido_endereco']['endereco'];
	$numero 		= !empty($rs['skyhub_order']['id']) ? $rs['skyhub_order']['numero'] : $rs['pedido']['pedido_endereco']['numero'];
	$bairro 		= !empty($rs['skyhub_order']['id']) ? $rs['skyhub_order']['bairro'] : $rs['pedido']['pedido_endereco']['bairro'];
	$complemento 	= !empty($rs['skyhub_order']['id']) ? $rs['skyhub_order']['complemento'] : $rs['pedido']['pedido_endereco']['complemento'];
	$referencia 	= !empty($rs['skyhub_order']['id']) ? $rs['skyhub_order']['referencia'] : $rs['pedido']['pedido_endereco']['referencia'];
	$cidade 		= !empty($rs['skyhub_order']['id']) ? $rs['skyhub_order']['cidade'] : $rs['pedido']['pedido_endereco']['cidade'];
	$uf				= !empty($rs['skyhub_order']['id']) ? $rs['skyhub_order']['uf'] : $rs['pedido']['pedido_endereco']['uf'];
	$cep 			= !empty($rs['skyhub_order']['id']) ? $rs['skyhub_order']['cep'] : $rs['pedido']['pedido_endereco']['cep'];
	$volumes 		= $rs['volumes'];
	$modalidadeInt 	= $rs['modalidade'];
	$shipment_id 	= $rs['shipment_id'];
	$codigo_venda	= !empty($rs['skyhub_order']['id']) ? $rs['skyhub_order']['cod_venda'] : $rs['pedido']['codigo'];
	$nrnfe 			= substr((!empty($rs['skyhub_order']['id']) ? $rs['skyhub_order']['chave_nfe'] : $rs['pedido']['nfe_notas']['chavenfe']), 8, -18);
	
	// $quantidade = '';
	// $peso = '';
	// $nrnfe = '';

	// if( ! empty($rs['prod_kit']) && $rs['prod_kit'] != '' ) :
	// 	$codigo_id = $rs['prod_kit'];
	// else :
	// 	// Pega somente os produtos do kit para fazer a soma dos mesmos
	// 	$Kits = ProdutosKits::all(['conditions' => ['codigo_id=? and codigo_id_produtos=?', $codigo_id, $rs['codigo_id']]]);
	// 	if( ! empty( $Kits ) )
	// 		unset($rs, $resultAll[$k]);
	// endif;

	$peso = 0;
	$pedidos_vendas = !empty(count($rs['skyhub_order']['skyhub_produto'])) ? $rs['skyhub_order']['skyhub_produto'] : $rs['pedido']['pedidos_vendas'];
	
	foreach($pedidos_vendas as $p) {
		$p_peso = !empty($p['produto']['freteproduto']['peso']) ? $p['produto']['freteproduto']['peso']:$p['peso'];
		$peso += ($p_peso * $p['quantidade']);
	}
	
	if( ! empty($id) ) :
		for($i = 0; $i < $rs['volumes']; $i++) :
			$pdf->AddPage();
	
$string = 'Pedido: %s
Nota Fiscal: %s
ShipmentID: %s
Volumes: %s/%s
Peso (Kg): %s - %s';
			$MultiCell = sprintf($string, $codigo_venda, $nrnfe, $shipment_id, ($i + 1), $volumes, ((float)($peso > 0 ? $peso:0)), $modalidade[$modalidadeInt]);

			$x = $pdf->GetX();
			$y = $pdf->GetY();
			$pdf->setTextColor(0, 0, 0);
			$pdf->Cell(45.5, 20, $pdf->Image(PATH_ROOT . 'adm/jadlog/logo.jpg', $x, 2, 46), 0, 1, 'L');	
			
			$pdf->SetXY($x + 45.5, $y);
			$pdf->MultiCell(55.5, 4, $MultiCell, 0, 'L', false);
			$pdf->Ln(1);

			$pdf->Cell(101, 0, '', 'B', 2, 'C');
			$pdf->SetFont('Calibri Bold', '', 10);
			$pdf->setTextColor(255, 255, 255);

			$pdf->Cell(33.66, 5, 'DESTINATÁRIO', 0, 0, 'L', 1);
			$pdf->Cell(66.132, 5, '', 0, 2, 'C');
			$pdf->Ln(1);
			
			// DESTINATÁRIO
			$pdf->setTextColor(0, 0, 0);
			$pdf->SetFont('Calibri', '', 10);

$string = '%s - Fone: %s
%s, %s - %s
Comp: %s
Ref: %s
%s/%s
%s';
	
			$MultiCellDestinatario = sprintf($string, $nome, $telefone, $endereco, $numero, $bairro, $complemento, $referencia, $cidade, $uf, $cep);
			$pdf->MultiCell(101.5, 4, $MultiCellDestinatario, 0, 'L', false);
			$pdf->Ln(1);

			$pdf->Cell(101, 0, '', 'B', 2, 'C');
			$pdf->SetFont('Calibri Bold', '', 10);
			$pdf->setTextColor(255, 255, 255);
			$pdf->Cell(33.66, 5, 'REMETENTE', 0, 0, 'L', 1);
			$pdf->Cell(66.132, 5, '', 0, 2, 'C');
			$pdf->Ln(1);

$StringMultiCellRemetente = 'Remetente: %s
%s, %s - %s
%s/%s - %s';
			$pdf->SetFont('Calibri', '', 10);
			$pdf->setTextColor(0, 0, 0);
			$MultiCellRemetente = sprintf($StringMultiCellRemetente, $CONFIG['nome_fantasia'], $CONFIG['endereco'], $CONFIG['numero'], $CONFIG['bairro'], $CONFIG['cidade'], $CONFIG['uf'], $CONFIG['cep']);
			$pdf->MultiCell(101.5, 5, $MultiCellRemetente, 0, 'L', false);
			$pdf->Ln(1);

			$x1 = $pdf->GetX();
			$y1 = $pdf->GetY();
			$pdf->Cell(101, 1, '', 'B', 2, 'C');

			$pdf->SetFont('Calibri Bold', '', 10);
			$pdf->setTextColor(255, 255, 255);
			$pdf->Cell(33.66, 5, 'ShipmentID', 0, 0, 'L', 1);
			$pdf->Cell(66.132, 5, '', 0, 2, 'C');
			$pdf->Ln(1);

			$pdf->SetFont('Calibri', '', 10);
			$pdf->Code128(13, $y1 + 8, $rs['shipment_id'], 75, 15, 'C');
		endfor;
	endif;
	// output the result
}

$fileName = $CONFIG['lojas']['dominio'] . '-' . (int)$GET['etiquetas_id'] . '.pdf';
$pdf->Output($fileName);
unset($pdf);

try {
	$ImprovedFPDF = new \PhpSigep\Pdf\ImprovedFPDF('P', 'mm', 'Letter');
	$ImprovedFPDF->SetTitle(sprintf('Etiqueta JadLog - %s', $CONFIG['nome_fantasia']), 'UTF-8');
	$ImprovedFPDF->AddPage();
	$ImprovedFPDF->SetFillColor(0,0,0);
	$ImprovedFPDF->SetFont('Arial', 'B', 18);
	$pageCount = $ImprovedFPDF->setSourceFile($fileName);

	for($i = 1; $i <= $pageCount; $i++) 
	{
		$tplIdx = $ImprovedFPDF->importPage( $i, '/MediaBox');

		$mod = $i % 4;

		switch ($mod) 
		{
			case 0:
				//A4: 210(x) × 297(y)
				//Letter: 216 (x) × 279 (y)
				$ImprovedFPDF->useTemplate($tplIdx, 105, 110, 105, 115, true);

				if ($i !== $pageCount) {
					$ImprovedFPDF->AddPage();
					$ImprovedFPDF->SetFillColor(0,0,0);
				}
				break;
			case 1:
				$ImprovedFPDF->useTemplate($tplIdx, 2, 2, 105, 115, true);
				break;
			case 2:
				$ImprovedFPDF->useTemplate($tplIdx, 105, 2, 105, 115, true);
				break;
			case 3:
				$ImprovedFPDF->useTemplate($tplIdx, 2, 110, 105, 115, true);
				break;
		}
	}
	$ImprovedFPDF->Output();

} catch(Exception $e) {
	echo 'Entrar em contato com DataControl Informatica';
}

unset($fileName);
