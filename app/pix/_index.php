<?php
$GET = filter_input_array(INPUT_GET);
include '../vendor/autoload.php';
include '../settings.php';
include '../settings-config.php';
include '../includes/bibli-funcoes.php';

$id_pedido = filter_input(INPUT_GET, 'id');
if ( ! $id_pedido )
    exit('Sem Permissão de momento!');


$Pedidos = Pedidos::find($id_pedido);

$endereco = array_filter([$CONFIG['endereco'], $CONFIG['numero'], join('/', [$CONFIG['cidade'], $CONFIG['uf']]), $CONFIG['cep']]);
$pdf = new MyFpdf('P', 'mm', 'A4');
$pdf->AddFont('Calibri', '', 'Calibri.php');
$pdf->AddFont('Titillium Web', '', 'titilliumweb-regular.php');
$pdf->AddFont('Titillium Web Bold', '', 'titilliumweb-bold.php');
$pdf->SetTitle('Impressão de transferência - ' . $CONFIG['nome_fantasia'], 'UTF8');
$pdf->SetMargins(5, 5, 5, 5);
$pdf->AddPage();


if(!empty($CONFIG['logo_desktop'])) {
    $img_logo = sprintf('%sassets/%s/imgs/%s', PATH_ROOT, ASSETS, $CONFIG['logo_desktop']);
    list($width, $height) = getimagesize($img_logo);
    
    $width = $width / 4; 
    $height = $height / 4;
    
    $pdf->SetFont('Titillium Web Bold', '', 18);
    $pdf->Cell($width, $height, $pdf->Image($img_logo, 5, 5, $width, $height, '', '', 'C'), 0, 0, 'C', 0);
    $pdf->Cell(200 - $width, $height + 18/4, sprintf('Transferência Via Pix - %s', $CONFIG['nome_fantasia']), 0, 1, 'C', 0);
    $pdf->Cell(200, 1, '', 'B', 2, 'L', 0);
	$pdf->Ln(2);
}

$TOTAL = valor_pagamento($Pedidos->valor_compra, $Pedidos->frete_valor, $Pedidos->desconto_cupom, '$', $Pedidos->desconto_boleto);

//INSTANCIA PRINCIPAL DO PAYLOAD PIX

$obPayload = (new \App\Pix\Payload)->setPixKey($CONFIG['pagamentos']['pix_key'])
                                   ->setDescription(sprintf('Pgto. Refer: %s', $Pedidos->codigo))
                                   ->setMerchantName($CONFIG['pagamentos']['pix_name'])
                                   ->setMerchantCity($CONFIG['pagamentos']['pix_city'])
                                   ->setAmount($TOTAL['TOTAL_COMPRA_C_BOLETO'])
                                   ->setTxid($Pedidos->codigo);

//CÓDIGO DE PAGAMENTO PIX
$payloadQrCode = $obPayload->getPayload();

$pdf->SetFont('Calibri', '', 10);
$pdf->Cell(200, 5, '1. Abra seu app de pagamentos ou Internet Banking.', 0, 2, 'C', 0);
$pdf->Cell(200, 5, '2. Busque pela opção de pagamento via Pix.', 0, 2, 'C', 0);
$pdf->Cell(200, 5, '3. Copie e cole o código abaixo:', 0, 2, 'C', 0);

$pdf->SetFont('Titillium Web Bold', '', 12);
$pdf->Cell(200, 5, 'Valor  a Pagar R$: ' . number_format($TOTAL['TOTAL_COMPRA_C_BOLETO'], 2, ',', '.'), 0, 2, 'C', 0);
$pdf->SetFont('Titillium Web Bold', '', 10);
$pdf->Cell(200, 5, 'Pagamento para ' . $CONFIG['pagamentos']['pix_name'], 0, 2, 'C', 0);

$img_bank = (new \chillerlan\QRCode\QRCode)->render($payloadQrCode);
$pdf->Image($img_bank, 50, null, 100, 100, 'png', '', 'C');

// $pdf->SetFont('Titillium Web Bold', '', 10);
// $pdf->Cell(200, 5, 'Escaneie ou cópie o código de pagamento abaixo, e efetue seu pagamento no app do banco de sua preferência', 0, 2, 'C', 0);

$pdf->SetFont('Calibri', '', 12);
$pdf->Ln(1);
$pdf->MultiCell(200, 5, $payloadQrCode, 0, 'C');
$pdf->Ln(5);

$GetY = $pdf->GetY();
$pdf->SetY($GetY + 5);

$pdf->SetFont('Calibri', '', 10);
$pdf->Cell(200, 5, join(', ', $endereco), 0, 2, 'L', 0);
$pdf->MultiCell(200, 5, sprintf('Para agilizar o processo de postagem, favor enviar a cópia do comprovante de pagamento pelo WhatsApp %s ou pelo e-mail %s', $CONFIG['telefone'], $CONFIG['email_contato']));
$pdf->Cell(200, 5, 'Obs: Após 2 dias úteis sem o pagamento, o pedido será cancelado', 0, 2, 'L', 0);
$pdf->Output();
