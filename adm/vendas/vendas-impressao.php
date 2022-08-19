<?php
defined('PATH_ROOT') || define('PATH_ROOT', realpath($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR);
require_once PATH_ROOT. '/app/settings.php';
require_once PATH_ROOT . '/app/vendor/autoload.php';
require_once PATH_ROOT . '/app/settings-config.php';
require_once PATH_ROOT . '/assets/' . ASSETS .  '/settings.php';
require_once PATH_ROOT . '/app/includes/bibli-funcoes.php';


$PEDIDO_ID = !empty($_GET['id']) && $_GET['id'] != '' ? $_GET['id'] : null;

$Pedido = Pedidos::find($PEDIDO_ID);
$TOTAL = valor_pagamento($Pedido->valor_compra, $Pedido->frete_valor, $Pedido->desconto_cupom, '$', $Pedido->desconto_boleto);

$pdf = new MyFpdf('P', 'mm', 'A4');
$pdf->SetTitle('Pedido: ' . $Pedido->codigo, 'UTF8');
$pdf->AddFont('Calibri', '', 'Calibri.php');
$pdf->AddFont('Titillium Web', '', 'titilliumweb-regular.php');
$pdf->AddFont('Titillium Web Bold', '', 'titilliumweb-bold.php');

$pdf->SetMargins(5, 5, 5);

// add new page
$pdf->AddPage();
$pdf->SetFont('Titillium Web Bold', '', 14);
$pdf->Cell(130, 5, 'Pedido: ' . $Pedido->codigo, 0, 0, 'L', 0);
$pdf->Cell(80, 5, 'Data Venda: ' . $Pedido->data_venda->format('d/m/Y H:i'), 0, 2, 'L', 0);
$pdf->Ln(1);

$pdf->setFillColor(16, 65, 108);
$pdf->setTextColor(255, 255, 255);
$pdf->SetFont('Titillium Web Bold', '', 12);
$pdf->Cell(200, 7, 'STATUS PEDIDO', 0, 2, 'L', 1);
$pdf->Ln(1);

$pdf->setTextColor(0, 0, 0);
$pdf->SetFont('Calibri', '', 8);

$status = [
	1 => 'Pedido realizado',
	2 => 'Aguardando pagamento',
	11 => 'Pgto em análise',
	3 => 'Pagamento aprovado',
	4 => 'Pgto não aprovado',
	5 => 'Pgto não efetuado',
	6 => 'Em produção',
	7 => 'Separação de estoque',
	8 => 'Em transporte',
	9 => 'Pedido entregue',
	10 => 'Pedido cancelado',
];

$w = 200 / count($status);
$y = $pdf->y;
$push_right = 4;

for( $x = 1; $x < count($status); $x++ ) 
{
	
	if($Pedido->status == $x) {
		$pdf->setFillColor(95, 186, 125);
		$pdf->setTextColor(4, 66, 25);
	} 
	else {
		$pdf->setFillColor(255, 255, 255);
		$pdf->setTextColor(0, 0, 0);
	}
	
	$pdf->SetXY($x + $push_right, $y);
	$pdf->MultiCell($w, 5, $status[$x], 0, 'C', 1);
	$push_right += $w;
}
$pdf->Ln(5);

$pdf->setFillColor(16, 65, 108);
$pdf->setTextColor(255, 255, 255);
$pdf->SetFont('Titillium Web Bold', '', 12);
$pdf->Cell(96, 7, 'DADOS DO CLIENTE', 0, 0, 'L', 1);
$pdf->Cell(8, 7, '', 0, 0, 'L', 0);

$pdf->setFillColor(16, 65, 108);
$pdf->setTextColor(255, 255, 255);
$pdf->SetFont('Titillium Web Bold', '', 12);
$pdf->Cell(96, 7, 'ENDEREÇO DE ENTREGA', 0, 1, 'L', 1);

$pdf->setTextColor(0, 0, 0);
$pdf->SetFont('Calibri', '', 11);
$pdf->Cell(95, 5, 'Nome: ' . $Pedido->cliente->nome, 0, 0, 'L', 0);
$pdf->Cell(8, 7, '', 0, 0, 'L', 0);
$pdf->Cell(95, 5, 'Endereço: ' . $Pedido->pedido_endereco->endereco . ', ' . $Pedido->pedido_endereco->numero, 0, 1, 'L', 0);

$pdf->SetFont('Calibri', '', 10);
$pdf->Cell(95, 5, 'Telefone: ' . $Pedido->cliente->telefone, 0, 0, 'L', 0);
$pdf->Cell(8, 7, '', 0, 0, 'L', 0);
$pdf->Cell(95, 5, 'Bairro: ' . $Pedido->pedido_endereco->bairro, 0, 1, 'L', 0);

$pdf->Cell(95, 5, 'Celular: ' . $Pedido->cliente->celular, 0, 0, 'L', 0);
$pdf->Cell(8, 7, '', 0, 0, 'L', 0);
$pdf->Cell(95, 5, 'Complemento: ' . $Pedido->pedido_endereco->complemento, 0, 1, 'L', 0);

$pdf->Cell(95, 5, 'CPF/CNPJ: ' . $Pedido->cliente->cpfcnpj, 0, 0, 'L', 0);
$pdf->Cell(8, 7, '', 0, 0, 'L', 0);
$pdf->Cell(95, 5, 'Referências: ' . $Pedido->pedido_endereco->referencia, 0, 1, 'L', 0);

$pdf->Cell(95, 5, '', 0, 0, 'L', 0);
$pdf->Cell(8, 7, '', 0, 0, 'L', 0);
$pdf->Cell(95, 5, 'Cidade/UF: ' . sprintf('%s/%s', $Pedido->pedido_endereco->cidade, $Pedido->pedido_endereco->uf), 0, 1, 'L', 0);

$pdf->Cell(95, 5, '', 0, 0, 'L', 0);
$pdf->Cell(8, 7, '', 0, 0, 'L', 0);
$pdf->Cell(95, 5, 'CEP: ' . $Pedido->pedido_endereco->cep, 0, 1, 'L', 0);

$pdf->Cell(200, 0, '', 0, 2, 'L', 0);
$pdf->Ln(1);

$pdf->setFillColor(16, 65, 108);
$pdf->setTextColor(255, 255, 255);
$pdf->SetFont('Titillium Web Bold', '', 12);
$pdf->Cell(96, 7, 'FORMAS DE ENVIO', 0, 0, 'L', 1);
$pdf->Cell(8, 7, '', 0, 0, 'L', 0);

$pdf->setFillColor(16, 65, 108);
$pdf->setTextColor(255, 255, 255);
$pdf->SetFont('Titillium Web Bold', '', 12);
$pdf->Cell(96, 7, 'FORMAS DE PAGAMENTO', 0, 2, 'L', 1);

$pdf->Cell(200, 0, '', 0, 2, 'L', 0);
$pdf->Ln(1);

$pdf->setTextColor(0, 0, 0);
$pdf->SetFont('Calibri', '', 12);
$pdf->Cell(95, 5, $Pedido->frete_tipo . ' - R$: ' . number_format($Pedido->frete_valor, 2, ',' ,'.'), 0, 0, 'L', 0);
$pdf->Cell(8, 7, '', 0, 0, 'L', 0);
$pdf->Cell(95, 5, $Pedido->forma_pagamento . ' - R$: ' . number_format($TOTAL['TOTAL_COMPRA_C_BOLETO'], 2, ',', '.' ), 0, 2, 'L', 0);
$pdf->Ln(2);

$pdf->setFillColor(16, 65, 108);
$pdf->setTextColor(255, 255, 255);
$pdf->SetFont('Titillium Web Bold', '', 12);
$pdf->Cell(200, 7, 'PRODUTOS ADQUIRIDOS', 0, 2, 'L', 1);
$pdf->Ln(1);

$pdf->setTextColor(0, 0, 0);
$pdf->SetFont('Titillium Web Bold', '', 12);
$pdf->Cell(165, 7, 'Produto', 0, 0, 'L', 0);
$pdf->Cell(15, 7, 'Qtde', 0, 0, 'C', 0);
$pdf->Cell(20, 7, 'Valor', 0, 2, 'R', 0);
$pdf->Ln(0);

$pdf->SetFont('Calibri', '', 8);

$i = 0; 
$QTDE = 0;
$VALOR_PRODUTOS = 0;
foreach($Pedido->pedidos_vendas as $rr) 
{
	$dir_base_file = sprintf('%sassets/%s/imgs/produtos/%s', PATH_ROOT, ASSETS, $rr->produto->capa->imagem);
	$pdf->Cell(20, 20, $pdf->Image($dir_base_file, $pdf->GetX(), $pdf->GetY(), 13.78, 13.78), 0, 0, 'L', 0);
	$pdf->Cell(145, 13.78, $rr->produto->nome_produto, 0, 0, 'L', 0);
	$pdf->Cell(15, 13.78, $rr->quantidade, 0, 0, 'C', 0);
	$pdf->Cell(20, 13.78, 'R$: ' . number_format($rr->valor_pago * $rr->quantidade, 2, ',', '.' ), 0, 2, 'R', 0);
	$pdf->Ln(1);
	$QTDE+= $rr->quantidade;  
	$VALOR_PRODUTOS += $rr->valor_pago; 
	$i++;
}

$pdf->setFillColor(16, 65, 108);
$pdf->setTextColor(255, 255, 255);
$pdf->SetFont('Titillium Web Bold', '', 12);
$pdf->Cell(200, 7, 'TOTAL GERAL', 0, 2, 'L', 1);
$pdf->Ln(1);


$pdf->setTextColor(0, 0, 0);
$pdf->SetFont('Calibri', '', 10);
$pdf->Cell(100, 5,' ','0',0,'L',0);   // empty cell with left,top, and right borders
$pdf->Cell(100, 5, 'Frete: ' . $Pedido->frete_tipo, 0, 0, 'R', 0);
	$pdf->Ln();

$pdf->Cell(100, 5, '', '0', 0, 'C', 0);  // cell with left and right borders
$pdf->Cell(100, 5,'SubTotal R$: ' . number_format($VALOR_PRODUTOS, 2, ',', '.'), 0, 0, 'R', 0);
	$pdf->Ln();

$pdf->Cell(100, 5, (!empty($Pedido->frete_prazo) ? $Pedido->frete_prazo : ''), 0, 0, 'L', 0);  // cell with left and right borders
$pdf->Cell(100, 5, 'Total de Itens: ' . $QTDE, 0, 0, 'R', 0);
	$pdf->Ln();
	
if( $Pedido->id_cupom > 0 )
{	
	$pdf->Cell(100, 5, '', 0, 0, 'C', 0);  // cell with left and right borders
	$pdf->Cell(100, 5, 'Cupom: ' . $Pedido->pedido_cupom->cupom_codigo, 0, 0, 'R', 0);
	$pdf->Ln();

	$pdf->Cell(100, 5, '', 0, 0, 'C', 0);  // cell with left and right borders
	$pdf->Cell(100, 5, 'Desconto R$: ' . number_format($Pedido->desconto_cupom, 2, ',' ,'.'), 0, 0, 'R', 0);
	$pdf->Ln();
}

if( $Pedido->desconto_boleto > 0 )
{	
	$pdf->Cell(100, 5, '', 0, 0, 'C', 0);  // cell with left and right borders
	$pdf->Cell(100, 5, 'Desconto no boleto: ' . $Pedido->desconto_boleto . '%', 0, 0, 'R', 0);
		$pdf->Ln();
}

$pdf->Cell(100, 5, '', 0, 0, 'C', 0);  // cell with left and right borders
$pdf->Cell(100, 5, 'Valor Frete R$: ' . number_format($Pedido->frete_valor, 2, ',', '.'), 0, 0, 'R', 0);
	$pdf->Ln();

$pdf->SetFont('Titillium Web Bold', '', 14);	
$pdf->Cell(100, 5, '', 0, 0, 'C', 0);  // cell with left and right borders
$pdf->Cell(100, 5, 'Total da compra R$: ' . number_format( $TOTAL['TOTAL_COMPRA_C_BOLETO'], 2, ',', '.' ), 0, 0, 'R', 0);
	$pdf->Ln();

$pdf->Output();