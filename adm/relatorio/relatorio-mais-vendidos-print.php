<?php
// include 'topo.php';

defined('PATH_ROOT') || define('PATH_ROOT', realpath($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR);
require_once PATH_ROOT. '/app/settings.php';
require_once PATH_ROOT . '/app/vendor/autoload.php';
require_once PATH_ROOT . '/app/settings-config.php';
require_once PATH_ROOT . '/assets/' . ASSETS .  '/settings.php';
require_once PATH_ROOT . '/app/includes/bibli-funcoes.php';

$pdf = new MyFpdf('P', 'mm', 'A4');
$pdf->SetTitle('Relatório - Produtos mais vendidos', 'UTF8');
$pdf->AddFont('Calibri', '', 'Calibri.php');
$pdf->SetFont('Calibri', '', 9);

$pdf->SetMargins(5, 5, 5);

// add new page
$pdf->AddPage();

$data_inicial = isset($POST['dataInicial']) && $POST['dataInicial'] != '' ? converterDatas($POST['dataInicial']) : null;
$data_final = isset($POST['dataFinal']) && $POST['dataFinal'] != '' ? converterDatas($POST['dataFinal']) : null;
$exibir = $POST['exibir'];

$PediodoData = '';
$PediodoData .= $data_inicial != '' && $data_final == '' ? implode(' - ', [$POST['dataInicial'], $POST['dataInicial']]) : '';
$PediodoData .= $data_inicial == '' && $data_final != '' ? implode(' - ', [date('01/m/Y'), $POST['dataInicial']]) : '';
$PediodoData .= $data_inicial != '' && $data_final != '' ? implode(' - ', [$POST['dataInicial'], $POST['dataInicial']]) : '';

$MostrarProdutos = isset($POST['produtos']) && $POST['produtos'] == 'true' ? true : false;
$MostrarCores = isset($POST['cores']) && $POST['cores'] == 'true' ?  true : false;
$MostrarTamanhos = isset($POST['tamanhos']) && $POST['tamanhos'] == 'true' ? true : false;

$campos = $MostrarCores == 'true' ? 'cor.nomecor, ' : '';
$campos .= $MostrarTamanhos == 'true' ? 'tam.nometamanho, ' : '';

$status = array();
$status = isset($POST['pedidos']) ? $POST['pedidos'] : '';

$final_string = implode(', ', array_map( function( $item ) {
    return text_status_vendas( $item );
}, $status));

if( in_array('20', $status) ) {
	$final_string = 'Todos';
}

$where = '';

$where .= $data_inicial != '' && $data_final == '' 
	? sprintf(" and p.data_venda between '%s 00:00:00' and '%s 23:59:59' ", $data_inicial, $data_inicial) : '';

$where .= $data_inicial == '' && $data_final != '' 
	? sprintf(" and p.data_venda between '%s 00:00:00' and '%s 23:59:59' ", date('Y-m-01'), $data_final) : '';

$where .= $data_inicial != '' && $data_final != '' 
	? sprintf(" and p.data_venda between '%s 00:00:00' and '%s 23:59:59' ", $data_inicial, $data_final) : '';

$pdf->SetFont('Calibri', '', 18);
$pdf->Cell(200, 5, 'RELATÓRIO DE PRODUTOS MAIS VENDIDOS', 0, 2, 'C');
$pdf->Ln(5);
$pdf->SetFont('Calibri', '', 8);
$pdf->Cell(200, 2, sprintf('Período: %s', $PediodoData), 0, 2, 'L');
$pdf->Ln(1);
$pdf->MultiCell(200, 2, sprintf('Status: %s', $final_string), '0', 'L');
$pdf->Ln(1);

$pdf->SetFont('Calibri', '', 9);

$query = '' 
	. sprintf('select prod.id, prod.codigo_produto, prod.codigo_id, prod.nome_produto, sum(pd.quantidade) as quantidade, %s prod.id_marca, m.marcas ', $campos) 
	. 'from produtos prod  ' 
	. 'inner join pedidos_vendas pd on prod.id = pd.id_produto ' 
	. 'inner join marcas m on m.id = prod.id_marca ' 
	. 'inner join cores cor on cor.id = prod.id_cor ' 
	. 'inner join tamanhos tam on tam.id = prod.id_tamanho ' 
	. 'where prod.excluir = 0 ' 
	. sprintf('and exists(select 1 from pedidos p where p.id = pd.id_pedido and p.status in("%s") %s) ', implode( '","', $status), $where);

switch( $exibir ) 
{
	case 'M' :
		$queryMarcas = '' 
			. $query
			. 'group by prod.id_marca, m.marcas ' 
			. 'order by 3 desc';
		
		$resultMarcas = Lojas::connection()->query( $queryMarcas );

		$i = 1;
		$TOTAL_ITENS = 0;
		
		while ($rsMarcas = $resultMarcas->fetch(PDO::FETCH_OBJ) ) 
		{ 
			$pdf->SetFont('Calibri', '', 12);
			$pdf->SetFillColor(68, 112, 150);
			$pdf->SetTextColor(255, 255, 255);
			$pdf->Cell(180, 5, implode(': ', ['MARCA', $rsMarcas->marcas]), 0, 0, 'L', 1);
			$pdf->Cell(20, 5, implode(': ', ['QTDE', $rsMarcas->quantidade]), 0, 2, 'C', 1);
			
			$pdf->Ln(0);
			
			if ( $MostrarProdutos == true ) 
			{
				$queryProdutos = '' 
					. $query
					. sprintf('and prod.id_marca = %u ', $rsMarcas->id_marca)
					. sprintf('group by prod.codigo_id, %s prod.nome_produto, prod.id_marca, m.marcas ', $campos)
					. 'order by 4 desc';
				
				$resultProdutos = Lojas::connection()->query($queryProdutos);
				$pdf->SetFont('Calibri', '', 9);
				
				$pdf->SetFillColor(203, 211, 226);
				$pdf->SetTextColor(0, 0, 0);
				$pdf->Cell(180, 5, 'NOME PRODUTO', 0, 0, 'L', 1);
				$pdf->Cell(20, 5, 'QTDE', 0, 2, 'C', 1);
				$pdf->SetFont('Calibri', '', 8);
				$pdf->Ln(0);
				
				$x = 0;
				while ($rsProd = $resultProdutos->fetch(PDO::FETCH_OBJ) ) 
				{
					if ($x % 2) :
						$pdf->SetFillColor(255, 255, 255);
					else :
						$pdf->SetFillColor(233, 233, 233);
					endif;
					
					$pdf->Cell(10, 5, '', 'T', 0, 'L', 1);
					$pdf->Cell(170, 5, 
						implode(' - ', [CodProduto($rsProd->nome_produto, $rsProd->id, $rsProd->codigo_produto), $rsProd->nome_produto]) . 
						implode('<br/>COR: ', [($MostrarCores != null ? $rsProd->nomecor: null)]) . 
						implode('<br/>TAM: ', [($MostrarTamanhos != null ? $rsProd->nometamanho: null)]), 'T', 0, 'L', 1);
					
					$pdf->Cell(20, 5, $rsProd->quantidade, 'T', 2, 'C', 1);
					$pdf->Ln(0);
					$x++;
				}
				$pdf->Ln(2);
			}
			
			$TOTAL_ITENS += $rsMarcas->quantidade;
			++$i;
		}
	break;
	
	default :
	
		$queryAll = '' 
			. $query
			. sprintf('group by prod.codigo_id, prod.nome_produto, %s m.marcas having sum(pd.quantidade) >= 0 ', $campos) 
			. 'order by 3 desc';
		
		$result = Lojas::connection()->query( $queryAll );
		
		$pdf->SetFillColor(68, 112, 150);
		$pdf->SetTextColor(255, 255, 255);
		$pdf->Cell(160, 5, 'PRODUTO', 0, 0, 'L', 1);
		$pdf->Cell(20, 5, 'MARCA', 0, 0, 'C', 1);
		$pdf->Cell(20, 5, 'QTDE', 0, 2, 'C', 1);
		$pdf->Ln(0);
		$pdf->SetTextColor(0, 0, 0);
		$x = 0;
		$i = 1;
		$TOTAL_ITENS = 0;		
		while ($rws = $result->fetch(PDO::FETCH_OBJ) ) 
		{ 
			if ($x % 2) :
				$pdf->SetFillColor(255, 255, 255);
			else :
				$pdf->SetFillColor(233, 233, 233);
			endif;
			
			
			$pdf->Cell(160, 5, 
				implode(' - ', [CodProduto($rws->nome_produto, $rws->id, $rws->codigo_produto), $rws->nome_produto]) . 
				implode('<br/>COR: ', [($MostrarCores != null ? $rws->nomecor: null)]) . 
				implode('<br/>TAM: ', [($MostrarTamanhos != null ? $rws->nometamanho: null)]), 'T', 0, 'L', 1);
			$pdf->Cell(20, 5, $rws->marcas, 'T', 0, 'C', 1);
			$pdf->Cell(20, 5, $rws->quantidade, 'T', 2, 'C', 1);
			$pdf->Ln(0);
			
			$TOTAL_ITENS += $rws->quantidade;
			$x++;
			$i++;
		}
	break;
}


$pdf->Ln(0);
$pdf->SetFont('Calibri', '', 14);
$pdf->SetFillColor(203, 211, 226);
$pdf->Cell(180, 10, 'QUANTIDADE TOTAL', 0, 0, 'R', 1);
$pdf->Cell(20, 10, $TOTAL_ITENS, 0, 2, 'R', 1);
$pdf->Ln(5);

$pdf->Output();
