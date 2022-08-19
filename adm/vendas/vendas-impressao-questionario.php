<?php
defined('PATH_ROOT') || define('PATH_ROOT', realpath($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR);
require_once PATH_ROOT. '/app/settings.php';
require_once PATH_ROOT . '/app/vendor/autoload.php';
require_once PATH_ROOT . '/app/settings-config.php';
require_once PATH_ROOT . '/assets/' . ASSETS .  '/settings.php';
require_once PATH_ROOT . '/app/includes/bibli-funcoes.php';
require_once PATH_ROOT . '/app/php-sigep/PhpSigepFPDF/MyFpdf.php';
require_once PATH_ROOT . '/app/php-sigep/PhpSigepFPDF/makefont/makefont.php';

// extract para os dados da loja;
extract($CONFIG);

// busca cliente/endereco
$result = Pedidos::find( (INT)$_GET['id'] );

$pdf = new MyFpdf('P', 'mm', 'A4');

$pdf->SetTitle('Formulário de Satisfação do Cliente', 'UTF8');
$pdf->AddFont('Calibri', '', 'Calibri.php');
$pdf->AddFont('Calibri Bold', '', 'calibri-bold-2.php');
// $pdf->AddFont('FontAwesome', '', 'fontawesome-webfont.php');

$pdf->SetMargins(5, 5, 5);

// add new page
$pdf->AddPage();

$pdf->SetFont('Calibri Bold', '', 12);

// Set logo
$img_logo = Imgs::src('logo.png', 'imgs');

$pdf->Cell(200, 22, $pdf->Image($img_logo, $pdf->GetX(), 6, 40) . 'FORMULÁRIO DE SATISFAÇÃO DO CLIENTE', 0, 1, 'C');

$pdf->Ln(5);

$pdf->SetFont('Calibri', '', 9);

$json = json_decode( $result->questionario->json, true );

$pdf->SetFont('Calibri', '', 12);

$pdf->Cell(20, 5, 'Cliente', '0', 0, 'C');
$pdf->Cell(180, 5, $json['cliente'], '0', 2);
$pdf->Ln(0);

$pdf->Cell(20, 5, 'Pedido', '0', 0, 'C');
$pdf->Cell(180, 5, $json['codigo'], '0', 1);
$pdf->Cell(200, 5, '', 'B', 2);
$pdf->Ln(1);

foreach( $json as $i => $v ) :
	
	if( is_array( $v ) ) :
		
		if( $v['pergunta'] ) :
			$pdf->SetFont('Calibri', '', 10);
			$pdf->MultiCell(200, 5, $v['pergunta'], '0', 'L', false);
			$pdf->MultiCell(200, 5, '    ' . $v['resposta'], '0', 'L', false);
		endif;	
		
		if( $v['titulo'] ) :
			$img_prod = Imgs::src($v['imagem'], 'smalls');
			
			$XA = $pdf->GetX();
			$YA = $pdf->GetY();
			$pdf->MultiCell(20, 5, $pdf->Image($img_prod, $XA, $YA, 20), '0', 'L', false);
			$pdf->SetFont('Calibri Bold', '', 13);
			$pdf->SetX($XA + 20);
			$pdf->MultiCell(180, 5, $v['produto'], '0', 'L', false);
			
			$pdf->SetX($XA + 20);
			$pdf->SetFont('Calibri Bold', '', 10);
			$pdf->MultiCell(180, 5, sprintf('Nota: %s', $v['nota']), '0', 'L', false);

			$YB = $pdf->GetY();
			$pdf->SetY($YB + 10);

			$pdf->SetFont('Calibri Bold', '', 12);
			$pdf->MultiCell(200, 5, $v['titulo'], '0', 'C', false);

			$pdf->SetFont('Calibri', '', 9);
			$pdf->MultiCell(200, 5, $v['comentario'], '0', 'L', false);
			
			$img = '';
			$img_star = Imgs::src('star.gif', 'public');
			for( $i = 0; $i < $v['nota']; $i++ ) :
				$img .= $pdf->Image($img_star, ($pdf->GetX() + $i * (26/$v['nota'])), $pdf->GetY(), 4);
			endfor;
			
			$pdf->SetFont('Calibri Bold', '', 12);
			$pdf->MultiCell(200, 5, $img, '0', 'L', false);
			$pdf->Ln(3);
			$pdf->Cell(200, 0, '', 'B', 2);
			$pdf->Ln(3);
		endif;
		
	endif;
	
endforeach;

//output the result
$pdf->Output();