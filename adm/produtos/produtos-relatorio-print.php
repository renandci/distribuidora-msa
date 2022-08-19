<?php
defined('PATH_ROOT') || define('PATH_ROOT', realpath($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR);
require_once PATH_ROOT. '/app/settings.php';
require_once PATH_ROOT . '/app/vendor/autoload.php';
require_once PATH_ROOT . '/app/settings-config.php';
require_once PATH_ROOT . '/assets/' . ASSETS .  '/settings.php';
require_once PATH_ROOT . '/app/includes/bibli-funcoes.php';

$pdf = new MyFpdf();

$pdf->SetTitle('Relatório de Estoque', 'UTF8');

$pdf->AddFont('Calibri', '', 'Calibri.php');

$pdf->SetMargins(5, 5, 5);

$pdf->SetAutoPageBreak(true, 10);

// add new page
$pdf->AddPage('P', [ 210, 296 ]);

// Set logo
$img_logo = Imgs::src('logo.png', 'imgs');

// set font to arial, bold, 11pt
// $pdf->Cell(200, 22, $pdf->Image($img_logo, $pdf->GetX(), 6, 40) . 'DECLARAÇAO DE CONTEUDO', 'LTRB', 2, 'C');
$pdf->SetFont('Calibri', '', 12);
$pdf->Cell(15 , 8, 'Cód.'     , 'TB', 0, 'L');
$pdf->Cell(90 , 8, 'Descrição', 'TB', 0, 'L');
$pdf->Cell(30 , 8, 'Cor'      , 'TB', 0, 'L');
$pdf->Cell(30 , 8, 'Tam'      , 'TB', 0, 'L');
$pdf->Cell(10 , 8, 'Qtde'     , 'TB', 0, 'R');
$pdf->Cell(25 , 8, 'Valor'    , 'TB', 2, 'R');
$pdf->Ln(0);

$pdf->SetFont('Calibri', '', 9);

$i = 0;
$i_cor = 0;
$i_tam = 0;
$i_cor_tam = 0;
$i_estoque = 0;

extract( $GET );

$myorder = null;
if( strlen( $order ) && 'codigo_produto' == $order )
	$myorder = 'codigo_produto asc';
if( strlen( $order ) && 'nome_produto' == $order )
	$myorder = 'nome_produto asc';
if( strlen( $order ) && 'estoque' == $order )
	$myorder = 'estoque desc';


$Produtos = Produtos::all([ 'conditions' => [ 'excluir=? and status=?', 0, 0 ], 'order' => $myorder ]);

foreach( $Produtos as $rws )
{
	
	if ( $i % 2 )
		$pdf->SetFillColor(255, 255, 255);
	else
		$pdf->SetFillColor(233, 233, 233);
	
	
	$pdf->Cell(15 , 6, CodProduto($rws->nome_produto, $rws->id, $rws->codigo_produto), '', 0, 'L', 1);
	$pdf->Cell(90 , 6, $rws->nome_produto											 , '', 0, 'L', 1);
	$pdf->Cell(30 , 6, $rws->cor->nomecor											 , '', 0, 'L', 1);
	$pdf->Cell(30 , 6, $rws->tamanho->nometamanho									 , '', 0, 'L', 1);
	$pdf->Cell(10 , 6, $rws->estoque												 , '', 0, 'R', 1);
	$pdf->Cell(25 , 6, 'R$ '.number_format( $rws->preco_promo, 2, ',', '.' )		 , '', 2, 'R', 1);
	$pdf->Ln(0);
	
	$i++;
	$i_cor += ( $rws->id_cor > 0 ? 1 : 0 );
	$i_tam += ( $rws->id_tamanho > 0 ? 1 : 0 );
	$i_cor_tam += ( $rws->id_cor > 0 && $rws->id_tamanho > 0 ? 1 : 0 );
	$i_estoque += $rws->estoque;
}

$pdf->Ln(5);

$pdf->Cell(200, 0, '', 'B', 2, 'L');

$pdf->Ln(5);

$pdf->SetFont('Calibri', '', 9);
$pdf->Cell(155, 8, '', '', 0, 'R');
$pdf->Cell(45, 5, 'Total de Referências', 'B', 2, 'R');
$pdf->Ln(1);

$pdf->SetFont('Calibri', '', 11);
$pdf->Cell(155, 8, '', '', 0, 'R');
$pdf->Cell(35, 5, 'Total:', 0, 0, 'R');
$pdf->Cell(10, 5, $i, 0, 2, 'C');

// $pdf->Ln(2);

// $pdf->Cell(105, 0, '', '', 0, 'R');
// $pdf->Cell(95, 0, '', 'B', 2, 'R');
$pdf->Ln(1);

$pdf->SetFont('Calibri', '', 14);

$pdf->Cell(155, 8, '', '', 0, 'R');
$pdf->Cell(45, 8, 'Total em Estoque', 'B', 2, 'R');
$pdf->Ln(1);
$pdf->Cell(185, 8, 'Total:', 0, 0, 'R');
$pdf->Cell(15, 8, $i_estoque, 0, 0, 'R');


//output the result
$pdf->Output();