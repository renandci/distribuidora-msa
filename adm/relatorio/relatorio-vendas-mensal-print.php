<?php
// die();
defined('PATH_ROOT') || define('PATH_ROOT', realpath($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR);
require_once PATH_ROOT. '/app/settings.php';
require_once PATH_ROOT . '/app/vendor/autoload.php';
require_once PATH_ROOT . '/app/settings-config.php';
require_once PATH_ROOT . '/assets/' . ASSETS .  '/settings.php';
require_once PATH_ROOT . '/app/includes/bibli-funcoes.php';

$ano = $_POST['data_ini'];

// print_r($_POST);
// die();
// extract para os dados da loja;
extract($CONFIG);
// busca 
$loop = Lojas::connection()->query('SELECT  '
                                    . 'sum(valor_compra) as valor_compra,'
                                    . 'sum(frete_valor) as frete_valor, '
                                    . 'sum(desconto_boleto) as desconto_boleto, '
                                    . 'sum(desconto_cupom) as desconto_cupom, '
                                    . 'sum(valor_compra * (desconto_boleto / 100)) as desconto_boleto, '
                                    . 'count(id) as totalpedidos, '
                                    . 'DATE_FORMAT(data_venda, "%m") as mes, '
                                    . 'DATE_FORMAT(data_venda, "%Y") as ano from pedidos '
                                    . 'WHERE status = 9 and id > 0 '
                                    . 'and DATE_FORMAT(data_venda, "%Y") = "'.$ano.'" '
                                    . 'GROUP BY DATE_FORMAT(data_venda, "%m-%Y")');
						
// $somar = Lojas::connection()->query('SELECT  '
//                                     . 'valor_compra,'
//                                     . 'frete_valor, '
//                                     . 'desconto_cupom, '
//                                     . 'desconto_boleto, '
//                                     . 'DATE_FORMAT(data_venda, "%m") as mes, '
//                                     . 'DATE_FORMAT(data_venda, "%Y") as ano from pedidos '
//                                     . 'WHERE status = 9 and id > 0 '
//                                     . 'and DATE_FORMAT(data_venda, "%Y") = "'.$ano.'" ');
						
// foreach ($somar as $cli){
//     $TOTAL = valor_pagamento($cli['valor_compra'], $cli['frete_valor'], $cli['desconto_cupom'], '$', $cli['desconto_boleto']);
// }



class RelVendas extends MyFpdf {

}

// print_r($loop);
// die();

$pdf = new RelVendas('P', 'mm', 'A4');


$pdf->SetMargins(5, 10, 0);

$pdf->AddPage();

$pdf->SetAutoPageBreak(true);

$pdf->AddFont('Calibri', '', 'Calibri.php');

$pdf->AddFont('Titillium Web', '', 'titilliumweb-regular.php');

$pdf->AddFont('Titillium Web Bold', '', 'titilliumweb-bold.php');

// relatório de vendas
$pdf->SetFont('Titillium Web', '', 9);

$pdf->Cell(100, 5, $CONFIG['razao_social'], 0, 0, 'L', 0);
$pdf->Cell(100, 5, date('d/m/Y'), 0, 1, 'R', 0);

$pdf->SetFont('Titillium Web Bold', '', 18);

$pdf->Ln(6);
$pdf->Cell(200, 5, 'RELATÓRIO DE VENDAS MENSAL', 0, 2, 'C', 0);

$pdf->SetFont('Titillium Web', '', 12);

$pdf->Ln(5);
$pdf->Cell(200, 5, 'Ano: ' . $ano, 0, 2, 'C', 0);

$pdf->Ln(5);
$pdf->SetFont('Calibri', '', 9);
$pdf->setFillColor(5, 100, 200);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(30, 5, 'Mês: ' , 'B', 0, 'L', 1);
$pdf->Cell(25, 5, 'Total Pedidos: ' , 'B', 0, 'R', 1);
$pdf->Cell(30, 5, 'Total Produtos: ' , 'B', 0, 'R', 1);
// $pdf->SetTextColor(0, 0, 0);
// $pdf->setFillColor(0, 0, 0);
$pdf->setFillColor(252, 99, 99);
$pdf->Cell(30, 5, 'Total Desc Cupom: ' , 'B', 0, 'R', 1);
$pdf->Cell(30, 5, 'Total Desc Boleto: ' , 'B', 0, 'R', 1);
$pdf->Cell(30, 5, 'Total Frete: ' , 'B', 0, 'R', 1);
$pdf->setFillColor(5, 100, 200);
$pdf->Cell(30, 5, 'Valor Total : ' , 'B', 2, 'R', 1);
// $pdf->Cell(30, 5, 'Valor Produtos: ' , 'B', 0, 'L', 0);
$pdf->Ln(1);

$pdf->SetTextColor(0, 0, 0);
$pdf->setFillColor(255, 255, 255);

foreach($loop as $rs){
    

    $TOTAL = valor_pagamento($rs['valor_compra'], $rs['frete_valor'], $rs['desconto_cupom'], '$', $rs['desconto_boleto']);
    $TOTAL2 = $rs['valor_compra'] - $rs['desconto_cupom'] - $rs['desconto_boleto'] + $rs['frete_valor'];
    // print_r($TOTAL);
    // die();
    $month = $rs['mes']; // 4 == April, 5 = May, etc  // no leading zero
    switch( $month ){
        case 1 :        $mes = 'Janeiro';
        break;
        case 2 :        $mes = 'Fevereiro';
        break;
        case 3 :        $mes = 'Março';
        break;
        case 4 :        $mes = 'Abril';
        break;
        case 5 :        $mes = 'Maio';
        break;
        case 6 :        $mes = 'Junho';
        break;
        case 7 :        $mes = 'Julho';
        break;
        case 8 :        $mes = 'Agosto';
        break;
        case 9 :        $mes = 'Setembro';
        break;
        case 10:        $mes = 'Outubro';
        break;
        case 11:        $mes = 'Novembro';
        break;
        case 12:        $mes = 'Dezembro';
        break;
    }

    $pdf->Cell(30, 5, $rs['mes'] . ' - ' . $mes, 0, 0, 'L', 0);
    $pdf->Cell(25, 5, $rs['totalpedidos'], 0, 0, 'R', 0);
    $pdf->Cell(30, 5, 'R$: ' . number_format($rs['valor_compra'], 2, ',', '.'), 0, 0, 'R', 0);
    $pdf->Cell(30, 5, 'R$: ' . number_format($rs['desconto_cupom'], 2, ',', '.'), 0, 0, 'R', 0);
    $pdf->Cell(30, 5, 'R$: ' . number_format($rs['desconto_boleto'], 2, ',', '.'), 0, 0, 'R', 0);
    $pdf->Cell(30, 5, 'R$: ' . number_format($rs['frete_valor'], 2, ',', '.'), 0, 0, 'R', 0);
    $pdf->Cell(30, 5, 'R$: ' . number_format($TOTAL2, 2, ',', '.'), 0, 1, 'R', 0);
    // $pdf->Cell(200, 8, $rs['valor_compra'], 0, 2, 'L', 0);
    // $pdf->Cell(200, 8, $rs['valor_compra'], 0, 2, 'L', 0);
    $pdf->Ln(1);
}







$pdf->Output();
