<?php
define('PATH_ROOT', realpath($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR);
define('URL_BASE_HTTPS', 'https://' . $_SERVER['SERVER_NAME'] . '/');

require_once PATH_ROOT . '/app/vendor/autoload.php';
require_once PATH_ROOT . '/app/settings.php';
require_once PATH_ROOT . '/app/settings-config.php';
require_once PATH_ROOT . '/app/includes/bibli-funcoes.php';
// require_once PATH_ROOT . '/app/php-sigep/PhpSigepFPDF/MyFpdf.php';
// require_once PATH_ROOT . '/app/php-sigep/PhpSigepFPDF/makefont/makefont.php';

function convert_date( $date = null, $time = null ) 
{
	if( ! empty( $date )  )
		$date = implode('-', array_reverse( explode( '/', $date ) ) );
	
	if( ! empty( $time ) )
		$date = $date . ' ' . $time;
	
	return addslashes( $date );
}

$vTotTrib = 0;

$vTotProds = 0;

$vTotQCom = 0;

$vTotVDesc = 0;

$dir = sprintf('%sassets/%s/xml/', PATH_ROOT, ASSETS);
$filename = '%s%s-autorizada.xml';

$id_emitentes = filter_input(INPUT_POST, 'id_emitentes', FILTER_SANITIZE_NUMBER_INT);

$status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_NUMBER_INT);

$is_detalhed = filter_input(INPUT_POST, 'is_detalhed', FILTER_SANITIZE_NUMBER_INT);

$data_ini = filter_input(INPUT_POST, 'date_ini', FILTER_SANITIZE_STRING);
 
$data_fim = filter_input(INPUT_POST, 'date_fin', FILTER_SANITIZE_STRING);

$date_ini = filter_input(INPUT_POST, 'date_ini', FILTER_CALLBACK, ['options' => 'convert_date']);

$date_fin = filter_input(INPUT_POST, 'date_fin', FILTER_CALLBACK, ['options' => 'convert_date']);

$emitentes = NfeEmitentes::connection()->query(sprintf('select * from nfe_emitentes where id=%u', $id_emitentes))->fetch();


// echo sprintf('select * from nfe_notas where loja_id = %u and created_at between "%s" and "%s" and id_emitentes = %u and status = %u group by SUBSTRING(chavenfe, -18, 8) AND id DESC, id DESC', $CONFIG['loja_id'], $date_ini, $date_fin, $id_emitentes, $status);
// return;

$sql = sprintf('' 
    . 'SELECT nfe_notas.chavenfe, IF(skyhub_orders.id > "0", skyhub_orders.cod_venda, pedidos.codigo) as codigo ' 
    . 'FROM nfe_notas ' 
    . 'JOIN pedidos ON pedidos.id = nfe_notas.id_pedido '
    . 'JOIN skyhub_orders ON skyhub_orders.id = nfe_notas.id_skyhub_orders '
    . 'WHERE (nfe_notas.id_pedido > 0 OR nfe_notas.id_skyhub_orders > 0) AND nfe_notas.loja_id=%u AND nfe_notas.created_at between "%s" AND "%s" AND nfe_notas.id_emitentes=%u AND nfe_notas.status=%u ' 
    . 'GROUP BY SUBSTRING(nfe_notas.chavenfe, -18, 8) AND nfe_notas.id_pedido, nfe_notas.id DESC', 
    $CONFIG['loja_id'], "$date_ini 00:00:00", "$date_fin 23:59:59", $id_emitentes, $status);
// echo $sql;
// return;
$nfes = NfeNotas::connection()->query($sql);

$st = new NFePHP\NFe\Common\Standardize();

$pdf = new MyFpdf('P', 'mm', 'A4');

$pdf->SetTitle('Relatório de NF-e', 'UTF8');

$pdf->AddFont('Titillium Web', '', 'titilliumweb-regular.php');
$pdf->AddFont('Titillium Web Bold', '', 'titilliumweb-bold.php');

$pdf->SetMargins(5, 5, 5, 5);
// add new page
$pdf->AddPage();

// $pdf->SetAutoPageBreak(true);

$pdf->SetAutoPageBreak(true);

$pdf->SetFont('Titillium Web Bold', '', 14);

$pdf->Cell(200, 10, 'RELATÓRIO DE NF-e', 0, 0, 'C');

$pdf->Ln();

$pdf->SetFont('Titillium Web', '', 9);

$pdf->Cell(71.66, 5, strtoupper('EMITENTE: ' . $emitentes['razaosocial']), '0', 0, 'C');
$pdf->Cell(71.66, 5, strtoupper('Periodo de ' . $data_ini . ' a ' . $data_fim), 0, 0, 'C');
$pdf->Cell(71.66, 5, strtoupper(( $status == 1 ? 'Notas Emitidas' : 'Notas Canceladas' )), 0, 0, 'C');

$pdf->Ln();
$xmlCount = 0;
while( $rws = $nfes->fetch() )
{
	$pdf->SetFont('Titillium Web Bold', '', 10);

	$pdf->Cell(20, 5, 'Nr.Nota', 'LT', 0, 'C');
	$pdf->Cell(25, 5, 'Cód.Venda', 'LTR', 0, 'C');	
	$pdf->Cell(125, 5, 'Nome', 'LTR', 0);	
	$pdf->Cell(30, 5, 'Data Emissão', 'LTR', 1, 'R');

	$pdf->SetFont('Titillium Web', '', 9);
	
	$xml = sprintf($filename, $dir, $rws['chavenfe']);
	if( file_exists( $xmls ) )
		return;

	$xml = file_get_contents( $xml );
	$std = $st->toStd( $xml );
	
	$dest = $std->NFe->infNFe->dest;
	
	$pdf->SetFont('Titillium Web', '', 9);
	
	$pdf->Cell(20, 5, $std->NFe->infNFe->ide->nNF, 'LT', 0, 'R');	
	
	// $pdf->Cell(25, 5, ( count( $std->NFe->infNFe->det ) > 1 ? $std->NFe->infNFe->det[0]->prod->xPed : $std->NFe->infNFe->det->prod->xPed ), 'LTR', 0, 'C');
	$pdf->Cell(25, 5, $rws['codigo'], 'LTR', 0, 'C');
	
	$pdf->Cell(125, 5, $dest->xNome, 'LTR', 0);
	
	$pdf->Cell(30, 5, date( 'd/m/Y H:i', strtotime( $std->NFe->infNFe->ide->dhEmi ) ), 'LTR', 0, 'R');
	
	$pdf->Ln();
	
	if( $is_detalhed )
		$pdf->SetFont('Titillium Web Bold', '', 9);
	
	if( $is_detalhed )
		$pdf->Cell(20, 5, ' ', 'LTR', 0, 'L', 0);
	
	if( $is_detalhed )
		$pdf->Cell(25, 5, 'CÓD.', 1, 0, 'L', 0);
	
	if( $is_detalhed )
		$pdf->Cell(115, 5, 'DESCRIÇÃO', 1, 0, 'L', 0);
	
	if( $is_detalhed )
		$pdf->Cell(10, 5, 'QTDE', 1, 0, 'C', 0);
	
	if( $is_detalhed )
		$pdf->Cell(30, 5, 'VALOR', 1, 0, 'R', 0);
	
	if( $is_detalhed )
		$pdf->Ln();
	
	if( $is_detalhed )
		$pdf->SetFont('Titillium Web', '', 9);
	
	$vlFrete = 0;
	$vlQtdeProdutos = 0;
	$vlPriceProdutos = 0;
	
	if( count( $std->NFe->infNFe->det ) > 1 )  
	{		
		foreach( $std->NFe->infNFe->det as $i => $prods ) 
		{
			if( $is_detalhed )
				$pdf->Cell(20, 5, '', 'LR', 0, 'L', 0);   
			if( $is_detalhed )
				$pdf->Cell(25, 5, $prods->prod->cProd, 1, 0, 'L', 0);
			if( $is_detalhed )
				$pdf->Cell(115, 5, $prods->prod->xProd, 1, 0, 'L', 0);
			if( $is_detalhed )
				$pdf->Cell(10, 5, $prods->prod->qCom, 1, 0, 'R', 0);
			if( $is_detalhed )
				$pdf->Cell(30, 5, 'R$: ' . number_format($prods->prod->vProd, 2, ',', '.'), 1, 0, 'R', 0);
			if( $is_detalhed )
				$pdf->Ln();
			$vlFrete = $prods->prod->vFrete;
			$vlQtdeProdutos += $prods->prod->qCom;
			$vlPriceProdutos += ( $prods->prod->vProd * $prods->prod->qCom );
		}
	} 
	else 
	{
		if( $is_detalhed )
			$pdf->Cell(20, 5, '', 'LR', 0, 'L', 0);   
		if( $is_detalhed )
			$pdf->Cell(25, 5, $std->NFe->infNFe->det->prod->cProd, 1, 0, 'L', 0);
		if( $is_detalhed )
			$pdf->Cell(115, 5, $std->NFe->infNFe->det->prod->xProd, 1, 0, 'L', 0);
		if( $is_detalhed )
			$pdf->Cell(10, 5, $std->NFe->infNFe->det->prod->qCom, 1, 0, 'R', 0);
		if( $is_detalhed )
			$pdf->Cell(30, 5, 'R$: ' . number_format($std->NFe->infNFe->det->prod->vProd, 2, ',', '.'), 1, 0, 'R', 0);
		if( $is_detalhed )
			$pdf->Ln();
		$vlFrete = $std->NFe->infNFe->det->prod->vFrete;
		$vlQtdeProdutos += $std->NFe->infNFe->det->prod->qCom;
		$vlPriceProdutos += ( $std->NFe->infNFe->det->prod->vProd * $std->NFe->infNFe->det->prod->qCom );
	}

	$pdf->SetFont('Titillium Web Bold', '', 8);
	// $pdf->setFillColor(226, 228, 230);

	// Total de Itens para o frete
	// $pdf->Cell(170, 5, 'TOTAL DESC.', 'LTRB', 0, 'R', 0);
	// $pdf->Cell(30, 5, number_format($std->NFe->infNFe->total->ICMSTot->vDesc, 2, ',' , '.'), 'LTRB', 0, 'R');
	
	// $pdf->Ln();
	
	// Total de Itens para o frete
	$pdf->Cell(140, 5, 'TOTAL ITENS.', 'LTRB', 0, 'R', 0);
	$pdf->Cell(10, 5, $vlQtdeProdutos, 'LTRB', 0, 'R');
	
	// $pdf->Ln();
	
	// Valore para o produtos
	$pdf->Cell(20, 5, 'TOTAL PROD.', 'LTRB', 0, 'R', 0);
	$pdf->Cell(30, 5, 'R$: ' . number_format($vlPriceProdutos, 2, ',' , '.'), 'LTRB', 0, 'R');
	
	$pdf->Ln();
	
	// Valore para o frete
	$pdf->Cell(170, 5, 'TOTAL FRETE', 'LTRB', 0, 'R', 0);
	$pdf->Cell(30, 5, 'R$: ' . number_format($vlFrete, 2, ',' , '.'), 'LTRB', 0, 'R');
	
	$pdf->Ln();
	
	// Valore para o frete
	$pdf->setFillColor(187, 187, 187);
	$pdf->Cell(170, 5, 'TOTAL', 'LTRB', 0, 'R', 1);
	$pdf->Cell(30, 5, 'R$: ' . number_format($std->NFe->infNFe->total->ICMSTot->vNF, 2, ',' , '.'), 'LTRB', 0, 'R', 1);
	
	$pdf->Ln();
	$pdf->Ln();
	
	// echo '<pre>';
	// print_r($std);
	// echo '</pre>';
	
	// Soma vTotTrib;
	$vTotTrib += $std->NFe->infNFe->total->ICMSTot->vTotTrib;
	$vTotVDesc += $std->NFe->infNFe->total->ICMSTot->vDesc;
	$vTotProds += $std->NFe->infNFe->total->ICMSTot->vNF;
	
	$vTotQCom += $qCom;
	// echo $pdf->PageNo();
    ++$xmlCount;
}

$pdf->PageNo();

$pdf->SetFont('Titillium Web Bold', '', 12);
$pdf->setFillColor(191, 225, 251);

$pdf->Cell(160, 10, 'TOTAL XML.', 'LTRB', 0, 'R', 0);
$pdf->Cell(40, 10, $xmlCount, 'LTRB', 0, 'R', 1);

$pdf->Ln();

$pdf->Cell(160, 10, 'TOTAL DESC.', 'LTRB', 0, 'R', 0);
$pdf->Cell(40, 10, 'R$: ' . number_format($vTotVDesc, 2, ',' , '.'), 'LTRB', 0, 'R', 1);

$pdf->Ln();

$pdf->setFillColor(195, 255, 224);
$pdf->Cell(160, 10, 'TOTAL TRIB.', 'LTRB', 0, 'R', 0);
$pdf->Cell(40, 10, 'R$: ' . number_format($vTotTrib, 2, ',' , '.'), 'LTRB', 0, 'R', 1);

$pdf->Ln();

$pdf->setFillColor(187, 187, 187);
$pdf->Cell(160, 10, 'TOTAL NOTA', 'LTRB', 0, 'R', 0);
$pdf->Cell(40, 10, 'R$: ' . number_format($vTotProds, 2, ',' , '.'), 'LTRB', 0, 'R', 1);

$pdf->Output();