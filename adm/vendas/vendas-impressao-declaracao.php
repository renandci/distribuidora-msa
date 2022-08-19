<?php
defined('PATH_ROOT') || define('PATH_ROOT', realpath($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR);
require_once PATH_ROOT. '/app/settings.php';
require_once PATH_ROOT . '/app/vendor/autoload.php';
require_once PATH_ROOT . '/app/settings-config.php';
require_once PATH_ROOT . '/assets/' . ASSETS .  '/settings.php';
require_once PATH_ROOT . '/app/includes/bibli-funcoes.php';
// require_once PATH_ROOT . '/app/php-sigep/PhpSigepFPDF/MyFpdf.php';
// require_once PATH_ROOT . '/app/php-sigep/PhpSigepFPDF/makefont/makefont.php';

// extract para os dados da loja;
extract($CONFIG);

// busca cliente/endereco
$result_cliente = Lojas::query('SELECT '
					    . 'clientes.nome as p_nome, '
					    . 'clientes.email as p_email, '
					    . 'clientes.cpfcnpj as p_cpfcnpj, '
					    . 'pedidos_enderecos.endereco as p_endereco, '
					    . 'pedidos_enderecos.numero as p_numero, '
					    . 'pedidos_enderecos.bairro as p_bairro, '
					    . 'pedidos_enderecos.cidade as p_cidade, '
					    . 'pedidos_enderecos.uf as p_uf, '
					    . 'pedidos_enderecos.cep as p_cep, '
					    . 'pedidos.valor_compra, '
					    . 'pedidos.frete_valor, '
					    . 'pedidos.desconto_cupom, '
					    . 'pedidos.desconto_boleto '
					    . 'FROM pedidos '
					    . 'INNER JOIN clientes ON clientes.id = pedidos.id_cliente '
					    . 'INNER JOIN pedidos_enderecos ON pedidos_enderecos.id_pedido = pedidos.id '
					    . sprintf('WHERE pedidos.id=%u', (INT)$_GET['id']));

$cliente = $result_cliente->fetch();

// extract para os dados do cliente
extract($cliente);

$TOTAL = valor_pagamento( $valor_compra, $valor_frete, $desconto_cupom, '$', $desconto_boleto );
extract($TOTAL);

$pdf = new MyFpdf('P', 'mm', 'A4');
$pdf->SetTitle('Declaração de Conteúdo', 'UTF8');
$pdf->AddFont('Titillium Web', '', 'titilliumweb-regular.php');
$pdf->AddFont('Titillium Web Bold', '', 'titilliumweb-bold.php');

$pdf->SetMargins(5, 5, 5);

// add new page
$pdf->AddPage();

$pdf->SetFont('Titillium Web Bold', '', 12);

// $start_x = $pdf->GetX(); //initial x (start of column position)
// $current_y = $pdf->GetY();
// $current_x = $pdf->GetX();

// Set logo
$img_logo = sprintf('%s/assets/%s/imgs/%s', PATH_ROOT, ASSETS, $CONFIG['logo_desktop']);

// set font to arial, bold, 11pt
$pdf->Cell(200, 22, $pdf->Image($img_logo, $pdf->GetX() + 5, $pdf->GetY()+1, (100 * (22/100))) . 'DECLARAÇAO DE CONTEUDO', 'LTRB', 1, 'C');

// Cell(width, height, text, border, end line, [align])
// $pdf->Cell(200, 10, 'DECLARAÇAO DE CONTEUDO', 1, 1, 'C');
$pdf->Ln(5);

$pdf->SetFont('Titillium Web Bold', '', 11);

$pdf->Cell(100, 5, 'REMETENTE', 'LTR', 0, 'C');

$pdf->SetFont('Titillium Web Bold', '', 11);

$pdf->Cell(100, 5, 'DESTINATÁRIO', 'LTR', 1, 'C');

$pdf->SetFont('Titillium Web', '', 9);

$pdf->Cell(100, 5, 'NOME: ' . $razao_social, 'LTR', 0); // REMETENTE
$pdf->Cell(100, 5, 'NOME: ' . $p_nome, 'LTR', 1); // DESTINATÁRIO

$pdf->Cell(100, 5, 'ENDEREÇO: ' . $endereco . ', ' . $numero. ', ' . $bairro, 'LTR', 0); // REMETENTE
$pdf->Cell(100, 5, 'ENDEREÇO: ' . $p_endereco. ', ' . $p_numero. ', ' . $p_bairro, 'LTR', 1); // DESTINATÁRIO
$pdf->Cell(100, 5, '', 'LTR', 0);
$pdf->Cell(100, 5, '', 'LTR', 1);

$pdf->Cell(80, 5, 'CIDADE: ' . $cidade, 'LTR', 0); // REMETENTE
$pdf->Cell(20, 5, 'UF: ' . $uf, 'LTR', 0); // REMETENTE

$pdf->Cell(80, 5, 'CIDADE: ' . $p_cidade, 'LTR', 0); // DESTINATÁRIO
$pdf->Cell(20, 5, 'UF: ' . $p_uf, 'LTR', 1); // DESTINATÁRIO

$pdf->Cell(40, 5, 'CEP: ' . $cep, 1, 0); // REMETENTE
$pdf->Cell(60, 5, 'CPF/CNPJ: ' . $cnpj, 1, 0); // REMETENTE

$pdf->Cell(40, 5, 'CEP: ' . $p_cep, 1, 0); // REMETENTE
$pdf->Cell(60, 5, 'CPF/CNPJ: ' . $p_cpfcnpj, 1, 1); // REMETENTE

$pdf->Ln(5);

// busca cliente/endereco
$result_vendas = Lojas::query('SELECT '
					    . 'produtos.id, '
					    . 'produtos.nome_produto, '
					    . 'produtos.codigo_produto, '
					    . 'pedidos_vendas.valor_pago, '
					    . 'pedidos_vendas.quantidade, '
					    . 'dados_frete.peso '
					    . 'FROM pedidos_vendas '
					    . 'INNER JOIN produtos ON pedidos_vendas.id_produto = produtos.id '
					    . 'INNER JOIN dados_frete ON dados_frete.id = produtos.id_frete '
					    . sprintf('WHERE pedidos_vendas.id_pedido=%u', (INT)$_GET['id']));

$count = 17;
$peso_count = 0;
$price_count = 0;
$quantidade_count = 0;
while( $rws = $result_vendas->fetch() ) {
	
	extract($rws);
	
	$pdf->Cell(20, 5,  CodProduto( $nome_produto, $id, $codigo_produto ), 'L,T', 0, 'C');	
	$pdf->Cell(140, 5, $nome_produto, 'LTR', 0);	
	$pdf->Cell(20, 5, $quantidade, 'LTR', 0, 'C');	
	$pdf->Cell(20, 5, 'R$: ' . number_format($valor_pago, 2, ',', '.'), 'LTR', 1, 'R');
	
	$quantidade_count += $quantidade;
	$price_count += $valor_pago * $quantidade;
	$peso_count += $peso * $quantidade;
	$count--;
}

if( $count > 0 )
{
	for( $y = 0; $y < $count; $y++ ) 
	{
		$pdf->Cell(20, 5, '', 'L,T', 0);	
		$pdf->Cell(140, 5, '', 'LTR', 0);	
		$pdf->Cell(20, 5, '', 'LTR', 0);	
		$pdf->Cell(20, 5, '', 'LTR', 1);
	}	
}

$pdf->SetFont('Titillium Web Bold', '', 9);
$pdf->setFillColor(226, 228, 230);

// $pdf->Cell(160, 5, 'PESO TOTAL (kg)', 1, 0, 'R', 1);
// $pdf->Cell(40, 5, number_format($peso_count, 3), 1, 1, 'C');

// Array ( [TOTAL] => 307.80 [TOTAL_CUPOM] => R$: 0,00 [TOTAL_FRETE] => 0.00 [TOTAL_COMPRA] => 307.8 [TOTAL_CUPOM_REAL] => 0.00 [TOTAL_COMPRA_C_BOLETO] => 261.63 )

$pdf->Cell(160, 5, 'TOTAL', 'LTR', 0, 'R', 1);
$pdf->Cell(20, 5, $quantidade_count, 'LTR', 0, 'C');
$pdf->Cell(20, 5, 'R$: ' . number_format($price_count, 2, ',' , '.'), 'LTR', 1, 'R');

if( ! empty( $TOTAL_FRETE ) && $TOTAL_FRETE > 0 ) {
	$pdf->Cell(160, 5, 'VALOR FRETE', 1, 0, 'R', 1);
	$pdf->Cell(40, 5, 'R$: ' . number_format($TOTAL_FRETE, 2, ',' , '.'), 1, 1, 'R');
}

if( ! empty( $desconto_boleto ) && $desconto_boleto > 0 ) {
	$pdf->Cell(160, 5, 'DESCONTOS', 1, 0, 'R', 1);
	$pdf->Cell(40, 5, $desconto_boleto. '%', 1, 1, 'R');
}

$pdf->Cell(160, 5, 'VALOR', 1, 0, 'R', 1);
$pdf->Cell(40, 5, 'R$: ' . number_format($TOTAL_COMPRA_C_BOLETO, 2, ',' , '.'), 1, 1, 'R');

$pdf->Ln(5);

$pdf->SetFont('Titillium Web Bold', '', 11);
$pdf->Cell(200, 10, 'DECLARAÇAO', 1, 1, 'C');

$text = <<<EOT
Declaro que não me enquadro no conceito de contribuinte previsto no art. 4º da Lei Complementar nº 87/1996, uma vez que não realizo, com habitualidade ou em volume que caracterize intuito comercial, operações de circulação de mercadoria, ainda que se iniciem no exterior, ou estou dispensado da emissão da nota fiscal por força da legislação tributária vigente, responsabilizando-me, nos termos da lei e a quem de direito, por informações inverídicas.
Declaro ainda que não estou postando conteúdo inflamável, explosivo, causador de combustão espontânea, tóxico, corrosivo, gás ou qualquer outro conteúdo que constitua perigo, conforme o art. 13 da Lei Postal nº 6.538/78.
EOT;

$pdf->SetFont('Titillium Web', '', 9);

$pdf->MultiCell(200, 6, $text, 'TLR', 'C', false);
$pdf->Cell(200, 15, '', 'LR', 1, 'C');

$text = <<<EOT
_________________________, ___ de ___________________ de ______ ______________________________________
EOT;

$pdf->Cell(200, 5, $text, 'LR', 1, 'C');

$text = str_pad('Assinatura do Declarante/Remetente', 212, ' ', STR_PAD_LEFT);

$pdf->Cell(200, 5, $text, 'BLR', 1);

$text = <<<EOT
OBSERVAÇÃO: 
Constitui crime contra a ordem tributária suprimir ou reduzir tributo, ou contribuição social e qualquer acessório (Lei 8.137/90 Art. 1º, V).
EOT;
$pdf->Ln(5);
$pdf->MultiCell(200, 5, $text, 1, 'L', false);

//output the result
$pdf->Output();