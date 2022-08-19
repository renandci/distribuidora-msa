<?php
defined('PATH_ROOT') || define('PATH_ROOT', realpath($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR);
require_once PATH_ROOT . 'app/settings.php';
require_once PATH_ROOT . 'app/vendor/autoload.php';
require_once PATH_ROOT . 'app/settings-config.php';
require_once PATH_ROOT . 'assets/' . ASSETS .  '/settings.php';
require_once PATH_ROOT . 'adm/correios/correios-bootstrap.php';
require_once PATH_ROOT . 'app/includes/bibli-funcoes.php';

$pdf = new MyFpdf('P', 'mm', 'A4');
$pdf->AddFont('Calibri', '', 'Calibri.php');
$pdf->AddFont('Calibri Bold', '', 'calibri-bold-2.php');
$pdf->SetTitle('Relatório de Postagem JadLog', 'UTF8');
$pdf->SetFont('Calibri', '', 10);
$pdf->SetMargins(5, 5, 5);

$pdf->AddPage();

$pdf->SetFont('Calibri', '', 18);
$pdf->Cell(200, 5, 'Relatório de Postagem JadLog', 0, 2, 'C');
$pdf->Ln(5);

$pdf->SetFont('Calibri Bold', '', 12);
$pdf->SetTextColor(255,255,255);
$pdf->SetFillColor(16, 65, 108);
$pdf->Cell(35, 6, 'Nr.Pedido', 0, 0, 'L', 1);
$pdf->Cell(70, 6, 'Nome do Cliente', 0, 0, 'L', 1);
$pdf->Cell(25, 6, 'Data Gerada', 0, 0, 'C', 1);
$pdf->Cell(30, 6, 'Nr.Postagem', 0, 0, 'C', 1);
$pdf->Cell(20, 6, 'QTDE Prod.', 0, 0, 'C', 1);
$pdf->Cell(20, 6, 'QTDE Vol.', 0, 1, 'C', 1);
$pdf->SetTextColor(0,0,0);


$query = ""
    . "select "
    . "cli.nome, "
    . "ped.codigo,  "
    . "etiq.codigo,  "
    . "etiq.volumes, "
    . "etiq.created_at, "
    . "(select sum(pdv.quantidade) from pedidos_vendas pdv where pdv.id_pedido = ped.id) as totalpedidos "
    . "from jadlog_etiqueta etiq "
    . "join pedidos ped on ped.id = etiq.id_pedido "
    . "join clientes cli on cli.id = ped.id_cliente "
    . "where "
    . (!empty($GET['date_group']) && $GET['date_group'] > 0 ? sprintf('date_format(etiq.created_at, "%%Y%%m%%d")="%u" ', $GET['date_group']):null)
    . "group by etiq.id";


$i            = 1;
$x            = 0;
$total        = 0;
$totalVol     = 0;
$totalPedidos = 0;

$conditions['group'] = 'jadlog_etiqueta.id';
$conditions['conditions'] = 'excluir = 0 ';
$conditions['conditions'] .= (!empty($GET['etiquetas_id']) && $GET['etiquetas_id'] > 0 ? sprintf('and pedidos.id=%u ', $GET['etiquetas_id']):null);
$conditions['conditions'] .= (!empty($GET['date_group']) && $GET['date_group'] > 0 ? sprintf('and date_format(jadlog_etiqueta.created_at, "%%Y%%m%%d")="%u" ', $GET['date_group']):null);


$result = JadLogEtiqueta::all($conditions);

$pdf->SetFont('Calibri', '', 10);
foreach ($result as $rs) 
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
					'pedido_cliente',
					'pedidos_vendas'
				]
			]
		]
	]);

    // $id 			= !empty($rs['skyhub_order']['id']) ? $rs['id'] : $rs['id'];
	$nome 			= !empty($rs['skyhub_order']['id']) ? $rs['skyhub_order']['nome_cliente'] : $rs['pedido']['pedido_cliente']['nome'];
	// $email 			= !empty($rs['skyhub_order']['id']) ? $rs['skyhub_order']['email'] : $rs['pedido']['pedido_cliente']['email'];
	// $telefone 		= !empty($rs['skyhub_order']['id']) ? $rs['skyhub_order']['telefone'] : $rs['pedido']['pedido_cliente']['telefone'];	
	// $endereco 		= !empty($rs['skyhub_order']['id']) ? $rs['skyhub_order']['endereco'] : $rs['pedido']['pedido_endereco']['endereco'];
	// $numero 		= !empty($rs['skyhub_order']['id']) ? $rs['skyhub_order']['numero'] : $rs['pedido']['pedido_endereco']['numero'];
	// $bairro 		= !empty($rs['skyhub_order']['id']) ? $rs['skyhub_order']['bairro'] : $rs['pedido']['pedido_endereco']['bairro'];
	// $complemento 	= !empty($rs['skyhub_order']['id']) ? $rs['skyhub_order']['complemento'] : $rs['pedido']['pedido_endereco']['complemento'];
	// $referencia 	= !empty($rs['skyhub_order']['id']) ? $rs['skyhub_order']['referencia'] : $rs['pedido']['pedido_endereco']['referencia'];
	// $cidade 		= !empty($rs['skyhub_order']['id']) ? $rs['skyhub_order']['cidade'] : $rs['pedido']['pedido_endereco']['cidade'];
	// $uf				= !empty($rs['skyhub_order']['id']) ? $rs['skyhub_order']['uf'] : $rs['pedido']['pedido_endereco']['uf'];
	// $cep 			= !empty($rs['skyhub_order']['id']) ? $rs['skyhub_order']['cep'] : $rs['pedido']['pedido_endereco']['cep'];
	$volumes 		= $rs['volumes'];
	$modalidadeInt 	= $rs['modalidade'];
	$shipment_id 	= $rs['shipment_id'];
    $codigo_venda	= !empty($rs['skyhub_order']['id']) ? $rs['skyhub_order']['cod_venda'] : $rs['pedido']['codigo'];
    $pedidos_vendas = !empty(count($rs['skyhub_order']['skyhub_produto'])) ? $rs['skyhub_order']['skyhub_produto'] : $rs['pedido']['pedidos_vendas'];

	// $nrnfe 			= substr((!empty($rs['skyhub_order']['id']) ? $rs['skyhub_order']['chave_nfe'] : $rs['pedido']['nfe_notas']['chavenfe']), 8, -18);

    if ($x % 2)
        $pdf->SetFillColor(255, 255, 255);
    else
        $pdf->SetFillColor(233, 233, 233);

    $pdf->Cell(35, 5, $codigo_venda, 0, 0, 'L', 1);
    $pdf->Cell(70, 5, $nome, 0, 0, 'L', 1);
    $pdf->Cell(25, 5, date('d/m/Y H:i', strtotime($rs['created_at'])), 0, 0, 'C', 1);
    $pdf->Cell(30, 5, $shipment_id, 0, 0, 'C', 1);
    $pdf->Cell(20, 5, count($pedidos_vendas), 0, 0, 'C', 1);
    $pdf->Cell(20, 5, $volumes, 0, 1, 'C', 1);

    $total += count($pedidos_vendas);
    $totalVol += $volumes;
    $totalPedidos += $i;

    $x++;
}

$pdf->Ln(5);
$pdf->SetFont('Calibri Bold', '', 18);

$pdf->Cell(170, 10, 'Total de Produtos', 'T', 0, 'R', 0);
$pdf->SetFillColor(233, 233, 233);
$pdf->Cell(30, 10, $total, 'T', 1, 'R', 1);

$pdf->Cell(170, 10, 'Total de Pedidos', 0, 0, 'R', 0);
$pdf->SetFillColor(233, 233, 233);
$pdf->Cell(30, 10, $totalPedidos, 0, 1, 'R', 1);

$pdf->Cell(170, 10, 'Total de Volumes', 0, 0, 'R', 0);
$pdf->SetFillColor(233, 233, 233);
$pdf->Cell(30, 10, $totalVol, 0, 1, 'R', 1);

$pdf->Output();