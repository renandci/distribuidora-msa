<?php
defined('PATH_ROOT') || define('PATH_ROOT', realpath($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR);
require_once PATH_ROOT . '/app/settings.php';
require_once PATH_ROOT . '/app/vendor/autoload.php';
require_once PATH_ROOT . '/app/settings-config.php';
require_once PATH_ROOT . '/assets/' . ASSETS .  '/settings.php';
require_once PATH_ROOT . '/app/includes/bibli-funcoes.php';
// error_reporting(E_ALL);

// extract para os dados da loja;
extract($CONFIG);

$tipo_relatorio = filter_input(INPUT_POST, 'tipo_relatorio');

$data_ini = filter_input(INPUT_POST, 'data_ini', FILTER_CALLBACK, ['options' => 'converterDatas']);

$data_fin = filter_input(INPUT_POST, 'data_fin', FILTER_CALLBACK, ['options' => 'converterDatas']);

$estados = filter_input(INPUT_POST, 'estados');

$forma_pagamento = filter_input(INPUT_POST, 'forma_pagamento');

$MostrarClientes = filter_input(INPUT_POST, 'clientes');

$MostrarProdutos = filter_input(INPUT_POST, 'produtos');

$ordem = filter_input(INPUT_POST, 'ordem');

$cupom = filter_input(INPUT_POST, 'cupom');

$STATUS = array();

$STATUS = filter_input(INPUT_POST, 'pedidos', FILTER_DEFAULT, FILTER_FORCE_ARRAY);

$IN = '';
$array_in = null;
if (is_array($STATUS) && $STATUS[0] != '20') {
  foreach ($STATUS as $status)
    $array_in[] = (int)$status;

  $IN = ' and pedidos.status in(' . implode(',', $array_in) . ') ';
}


$status_ativos = '';
if (is_array($STATUS) && $STATUS[0] != '') {
  if ($STATUS[0] != '20') {
    $status_array = null;
    foreach ($POST['pedidos'] as $statusAtivos)
      $status_array[] = text_status_vendas($statusAtivos);

    $status_ativos = implode(' | ', $status_array);
  } else {
    $status_ativos = text_status_vendas($STATUS[0]);
  }
}

$periodo_data = '';
$periodo_data .= $data_ini != '' && $data_fin == '' ? "{$POST['data_ini']} - {$POST['data_fin']}" : '';
$periodo_data .= $data_ini == '' && $data_fin != '' ? date('01/m/Y') . " - {$POST['data_fin']}" : '';
$periodo_data .= $data_ini != '' && $data_fin != '' ? "{$POST['data_ini']} - {$POST['data_fin']}" : '';
$order_search_site = $ordem == 'data_venda' ? 'Data' : 'Estado';

// $WHERE = ' 0 = 0 ';
$WHERE = sprintf('pedidos.loja_id=%u ', $loja_id);
$WHERE .= !empty($cupom) ? ' and pedidos.id_cupom > 0 ' : '';
$WHERE .= $estados != '' ? sprintf(' and pedidos_enderecos.uf = "%s" ', $estados) : '';
$WHERE .= $data_ini != '' && $data_fin == '' ? sprintf(" and pedidos.data_venda between '%s 00:00:00' and '%s 23:59:59' ", $data_ini, $data_ini) : '';
$WHERE .= $data_ini == '' && $data_fin != '' ? sprintf(" and pedidos.data_venda between '%s 00:00:00' and '%s 23:59:59' ", date('Y-m-01'), $data_fin) : '';
$WHERE .= $data_ini != '' && $data_fin != '' ? sprintf(" and pedidos.data_venda between '%s 00:00:00' and '%s 23:59:59' ", $data_ini, $data_fin) : '';
$WHERE .= $forma_pagamento != '' ? sprintf(" and pedidos.forma_pagamento = '%s' ", $forma_pagamento) : '';
$WHERE .= $STATUS != '' && $STATUS > 0 ? sprintf(" %s ", $IN) : '';

$ORDER = $ordem == 'data_venda' ? 'pedidos.data_venda asc ' : 'pedidos_enderecos.uf asc, pedidos.data_venda asc ';

// busca
$loop = Lojas::connection()->query('SELECT '
  . 'pedidos.id as id_pedido, '
  . 'pedidos.id_cliente, '
  // . 'produtos.codigo_id, '
  // . 'produtos_kits.codigo_id_produtos as prod_kit, '
  . 'produtos.nome_produto, '
  . 'produtos.codigo_produto, '
  . 'cores.nomecor, '
  . 'tamanhos.nometamanho, '
  . 'A.tipo as tipo_a, '
  . 'B.tipo as tipo_b, '
  . 'pedidos_vendas.valor_pago, '
  . 'pedidos_vendas.id_produto, '
  . 'pedidos_vendas.quantidade, '
  // . 'dados_frete.peso, '
  . 'clientes.nome as p_nome, '
  . 'clientes.email as p_email, '
  . 'clientes.cpfcnpj as p_cpfcnpj, '
  . 'pedidos_enderecos.endereco as p_endereco, '
  . 'pedidos_enderecos.numero as p_numero, '
  . 'pedidos_enderecos.bairro as p_bairro, '
  . 'pedidos_enderecos.cidade as p_cidade, '
  . 'pedidos_enderecos.uf as p_uf, '
  . 'pedidos_enderecos.cep as p_cep, '
  . 'pedidos.data_venda, '
  . 'pedidos.valor_compra, '
  . 'pedidos.frete_tipo, '
  . 'pedidos.frete_valor, '
  . 'pedidos.desconto_cupom, '
  . 'cupons.cupom_codigo, '
  . 'pedidos.desconto_boleto, '
  . 'pedidos.id_cupom, '
  . 'pedidos.forma_pagamento, '
  . 'pedidos.codigo, '
  . 'pedidos.status, '
  . '(SELECT count(id) FROM pedidos_vendas WHERE id_pedido = pedidos.id) as total_prod, '
  . 'clientes_indicacoes.indicacao, '
  . 'sum(case when pedidos.id = clientes_indicacoes.id_pedido then 1 else 0 end) as primeira_compra, '
  . 'sum(case when pedidos.id = clientes_indicacoes.id_pedido then 0 else 1 end) as retorno '
  . 'FROM pedidos_vendas '
  . 'INNER JOIN produtos ON pedidos_vendas.id_produto = produtos.id '
  . 'INNER JOIN cores ON produtos.id_cor = cores.id '
  . 'INNER JOIN tamanhos ON produtos.id_tamanho = tamanhos.id '
  . 'INNER JOIN opcoes_tipo A ON cores.opcoes_id = A.id '
  . 'INNER JOIN opcoes_tipo B ON tamanhos.opcoes_id = B.id '

  . 'INNER JOIN pedidos ON pedidos.id = pedidos_vendas.id_pedido '
  . 'INNER JOIN pedidos_enderecos ON pedidos_enderecos.id_pedido = pedidos.id '
  . 'INNER JOIN cupons ON cupons.id = pedidos.id_cupom '
  . 'INNER JOIN clientes ON clientes.id = pedidos.id_cliente '

  . 'LEFT JOIN clientes_indicacoes on clientes_indicacoes.id_cliente = clientes.id '
  // . 'LEFT JOIN produtos_kits ON produtos.codigo_id = produtos_kits.codigo_id '

  . 'WHERE ' . $WHERE
  . 'GROUP BY pedidos_vendas.id '
  // . 'GROUP BY pedidos.id, pedidos.id_cliente, pedidos_vendas.id_produto '
  . 'ORDER BY ' . $ORDER);


// return;
$rs = [];
foreach ($loop as $rws) {
  array_push($rs, $rws);
}
// echo '<pre>';
// print_r($rs);
// echo '</pre>';
// die();
class RelVendas extends MyFpdf
{
  // var $periodo_data;
  // var $order_search_site;
  // var $estados;
  // var $forma_pagamento;
  // var $status_ativos;

  // function Header() {

  // $this->AddFont('Titillium Web', '', 'titilliumweb-regular.php');
  // $this->AddFont('Titillium Web Bold', '', 'titilliumweb-bold.php');
  // $this->SetFont('Titillium Web Bold', '', 13);
  // $this->Cell(200, 5, 'RELATÓRIO DE VENDAS', 0, 2, 'C', 0);
  // $this->Ln(1);

  // $this->SetFont('Titillium Web', '', 9);
  // $this->Cell(200, 5, 'Período: ' . $this->periodo_data, 'B', 2, 'L', 0);
  // $this->Cell(200, 5, 'Ordem: ' . $this->order_search_site, 'B', 2, 'L', 0);
  // $this->Cell(200, 5, 'Estado(s): ' . ($this->estados ? $this->estados : 'Todos'), 'B', 2, 'L', 0);
  // $this->Cell(200, 5, 'Forma pagamento: ' . ($this->forma_pagamento ? $this->forma_pagamento : 'Todos'), 'B', 2, 'L', 0);
  // $this->Cell(200, 5, 'Status pedidos: ' . ($this->status_ativos ? $this->status_ativos : 'Todos'), 'B', 2, 'L', 0);
  // $this->Ln(1);
  // }
}

$pdf = new RelVendas('P', 'mm', 'A4');

// $pdf->periodo_data = $periodo_data;
// $pdf->order_search_site = $order_search_site;
// $pdf->estados = $estados;
// $pdf->forma_pagamento = $forma_pagamento;
// $pdf->status_ativos = $status_ativos;

$pdf->SetMargins(5, 2, 0);

$pdf->AddPage();

$pdf->SetAutoPageBreak(true);

$pdf->AddFont('Calibri', '', 'Calibri.php');

$pdf->AddFont('Titillium Web', '', 'titilliumweb-regular.php');

$pdf->AddFont('Titillium Web Bold', '', 'titilliumweb-bold.php');

// relatório de vendas
if ($tipo_relatorio == 'V') {
  $i = 0;
  $row = 0;
  $sum = 1;
  $nummer = 297;
  $group_uf = null;
  $group_cli = null;
  $group_sum = null;
  $TOTAL = null;

  $sum_all_qtde = 0;
  $sum_all_vlcupom = 0;
  $sum_all_vlfrete = 0;
  $sum_all_desvbol = 0;
  $sum_all_mercadoria = 0;
  $sum_all_relatorio = 0;

  $pdf->SetTitle('RELATÓRIO DE VENDAS', 'UTF8');

  $pdf->SetFont('Calibri', '', 13);

  $pdf->Cell(200, 5, 'RELATÓRIO DE VENDAS', 0, 2, 'C', 0);

  $pdf->Ln(1);
  $pdf->SetFont('Calibri', '', 9);
  $pdf->Cell(200, 5, 'Período: ' . $periodo_data, 'B', 2, 'L', 0);
  $pdf->Cell(200, 5, 'Ordem: ' . $order_search_site, 'B', 2, 'L', 0);
  $pdf->Cell(200, 5, 'Estado(s): ' . ($estados ? $estados : 'Todos'), 'B', 2, 'L', 0);
  $pdf->Cell(200, 5, 'Forma pagamento: ' . ($forma_pagamento ? $forma_pagamento : 'Todos'), 'B', 2, 'L', 0);
  $pdf->Cell(200, 5, 'Status pedidos: ' . ($status_ativos ? $status_ativos : 'Todos'), 'B', 2, 'L', 0);
  $pdf->Ln(1);

  foreach ($rs as $cli) {
    // echo '<pre>';
    // print_r($cli);
    // echo '</pre>';
    // die();
    if ($order_search_site == 'Estado' && $group_uf != strtoupper($cli['p_uf'])) {
      $group_uf = strtoupper($cli['p_uf']);
      $pdf->SetFont('Calibri', '', 14);
      $pdf->setFillColor(292, 292, 192);
      $pdf->Cell(200, 8, $group_uf, 0, 2, 'L', 1);
      $pdf->Ln(1);
    }

    if ($group_cli != $cli['id_cliente'] . $cli['id_pedido']) {
      $group_cli = $cli['id_cliente'] . $cli['id_pedido'];

      $pdf->setFillColor(102, 102, 102);
      $pdf->SetFont('Calibri', '', 10);
      $pdf->Cell(20, 5, 'Data', 0, 0, 'L', 1);

      $pdf->Cell(20, 5, 'N.Ped', 0, 0, 'L', 1);
      if ($MostrarClientes)
        $pdf->Cell(105, 5, 'Cliente', 0, 0, 'L', 1);
      else
        $pdf->Cell(105, 5, '', 0, 0, 'L', 1);
      $pdf->Cell(30, 5, 'Forma Pagamento', 0, 0, 'C', 1);
      $pdf->Cell(25, 5, 'Frete', 0, 2, 'C', 1);
      $pdf->Ln(1);

      $pdf->SetFont('Calibri', '', 9);
      $pdf->setFillColor(204, 204, 204);
      $pdf->Cell(20, 5, date('d.m.Y', strtotime($cli['data_venda'])), 0, 0, 'L', 1);
      $pdf->Cell(20, 5, $cli['codigo'], 0, 0, 'L', 1);
      if ($MostrarClientes)
        $pdf->Cell(105, 5, $cli['p_nome'], 0, 0, 'L', 1);
      else
        $pdf->Cell(105, 5, '', 0, 0, 'L', 1);
      $pdf->Cell(30, 5, $cli['forma_pagamento'], 0, 0, 'C', 1);
      $pdf->Cell(25, 5, implode(' - ', [$cli['frete_tipo'], 'R$: ' . number_format($cli['frete_valor'], 2, ',', '.')]), 0, 2, 'C', 1);
      $pdf->Ln(1);


      if ($MostrarProdutos) {
        $pdf->setFillColor(241, 241, 241);
        $pdf->Cell(55, 5, '', 0, 0, 'L', 1);
        $pdf->Cell(110, 5, 'NOME PRODUTO', 0, 0, 'L', 1);
        $pdf->Cell(10, 5, 'QTDE', 0, 0, 'C', 1);
        $pdf->Cell(25, 5, 'VALOR', 0, 2, 'R', 1);
        $pdf->Ln(0);
        $pdf->Cell(200, 0, '', 1, 2, 'L', 0);
        $pdf->Ln(0);
      }
      $sum_all_qtde = $sum_all_qtde + 1;
    }

    if ($MostrarProdutos) {
      $pdf->SetFont('Calibri', '', 9);
      $pdf->Cell(55, 5, '', 0, 0, 'L', 0);
      $pdf->Cell(110, 5, implode(' - ', [CodProduto($cli['nome_produto'], $cli['id_produto'], $cli['codigo_produto']), $cli['nome_produto']]), 0, 0, 'L', 0);
      $pdf->Cell(10, 5, $cli['quantidade'], 0, 0, 'C', 0);
      $pdf->Cell(25, 5, number_format($cli['valor_pago'], 2, ',', '.'), 0, 2, 'R', 0);
      $pdf->Ln(0);

      // Se tiver Cores
      if ($cli['tipo_a']) {
        $pdf->SetFont('Calibri', '', 7);
        $pdf->Cell(65, 3, implode(': ', [substr($cli['tipo_a'], 0, 3), null]), 0, 0, 'R', 0);
        $pdf->Cell(135, 3, $cli['nomecor'], 0, 2, 'L', 0);
        $pdf->Ln(0);
      }

      // Se tiver Tamanhos
      if ($cli['tipo_b']) {
        $pdf->SetFont('Calibri', '', 7);
        $pdf->Cell(65, 3, implode(': ', [substr($cli['tipo_b'], 0, 3), null]), 0, 0, 'R', 0);
        $pdf->Cell(135, 3, $cli['nometamanho'], 0, 2, 'L', 0);
        $pdf->Ln(1);
      }

      $pdf->Cell(55, 0, '', 0, 0, 'L', 0);
      $pdf->Cell(145, 0, '', 1, 2, 'L', 0);
      $pdf->Ln(0);
    }

    $TOTAL = valor_pagamento($cli['valor_compra'], $cli['frete_valor'], $cli['desconto_cupom'], '$', $cli['desconto_boleto']);

    // $sum_all_mercadoria += ($cli['valor_pago'] * $cli['quantidade']);
    if ($group_sum != $cli['valor_compra']) {
      $group_sum = $cli['valor_compra'];
      $sum_all_mercadoria += $cli['valor_compra'];
    }

    if ($sum == $cli['total_prod']) {
      $pdf->Ln(1);
      $pdf->SetFont('Calibri', '', 10);
      $pdf->Cell(162, 4, 'Status: ', 0, 0, 'R', 0);
      $pdf->Cell(38, 4, text_status_vendas($cli['status']), 0, 1, 'R', 0);

      $pdf->Cell(162, 4, 'Valor da(s) mercadoria(s): ', 0, 0, 'R', 0);
      $pdf->Cell(38, 4, 'R$: ' . number_format($TOTAL['TOTAL'], 2, ',', '.'), 0, 1, 'R', 0);

      $pdf->Cell(162, 4, 'Valor do frete: ', 0, 0, 'R', 0);
      $pdf->Cell(38, 4, 'R$: ' . number_format($TOTAL['TOTAL_FRETE'], 2, ',', '.'), 0, 1, 'R', 0);

      $pdf->Cell(162, 4, 'Desconto Boleto: ', 0, 0, 'R', 0);
      $pdf->Cell(38, 4, number_format($TOTAL['desconto_boleto'], 0) . '%', 0, 1, 'R', 0);

      if ($cli['id_cupom'] > 0) {
        $pdf->Cell(162, 4, 'Cupom: ', 0, 0, 'R', 0);
        $pdf->Cell(38, 4, $cli['cupom_codigo'], 0, 1, 'R', 0);

        $pdf->Cell(162, 4, 'Desconto Cupom: ', 0, 0, 'R', 0);
        $pdf->Cell(38, 4, 'R$: ' .  number_format((soNumero($TOTAL['TOTAL_CUPOM']) / 100), 2, ',', '.'), 0, 1, 'R', 0);
      }

      $pdf->setFillColor(255, 199, 199);
      $pdf->SetFont('Calibri', '', 11);
      $pdf->Cell(162, 6, 'VALOR TOTAL DO PEDIDO: ', 0, 0, 'R', 0);
      $pdf->Cell(38, 6,  'R$: ' . number_format($TOTAL['TOTAL_COMPRA_C_BOLETO'], 2, ',', '.'), 0, 2, 'R', 1);
      $pdf->Ln(3);
      $sum = 0;

      $sum_all_vlfrete += $cli['frete_valor'];
      $sum_all_desvbol += $cli['desconto_boleto'];
      $sum_all_vlcupom += (soNumero($TOTAL['TOTAL_CUPOM']) / 100);
      $sum_all_relatorio += $TOTAL['TOTAL_COMPRA_C_BOLETO'];
    }

    ++$sum;
    ++$i;
  }

  $pdf->SetFont('Titillium Web', '', 8);
  $pdf->Cell(200, 1, str_pad('', 328, '.', STR_PAD_RIGHT), 0, 2, 'R', 0);
  $pdf->Ln(2);

  $pdf->setFillColor(199, 199, 199);
  $pdf->SetFont('Calibri', '', 10);
  $pdf->Cell(162, 8, 'TOTAL DE PEDIDOS: ', 0, 0, 'R', 0);
  $pdf->SetFont('Calibri', '', 15);
  $pdf->Cell(38, 8,  $sum_all_qtde, 0, 2, 'R', 1);
  $pdf->Ln(0);

  $pdf->SetFont('Calibri', '', 10);
  $pdf->Cell(162, 8, 'TOTAL DOS FRETES: ', 0, 0, 'R', 0);
  $pdf->SetFont('Calibri', '', 15);
  $pdf->Cell(38, 8,  'R$: ' . number_format($sum_all_vlfrete, 2, ',', '.'), 0, 2, 'R', 1);
  $pdf->Ln(0);

  // Verifica se o cupom está em ativo
  if (!empty($cupom)) {
    $pdf->SetFont('Calibri', '', 10);
    $pdf->Cell(162, 8, 'TOTAL CUPOM DESCONTO: ', 0, 0, 'R', 0);
    $pdf->SetFont('Calibri', '', 15);
    $pdf->Cell(38, 8,  'R$: ' . number_format($sum_all_vlcupom, 2, ',', '.'), 0, 2, 'R', 1);
    $pdf->Ln(0);
  }

  $pdf->SetFont('Calibri', '', 10);
  $pdf->Cell(162, 8, 'TOTAL DOS PRODUTOS S/ FRETE: ', 0, 0, 'R', 0);
  $pdf->SetFont('Calibri', '', 15);
  $pdf->Cell(38, 8,  'R$: ' . number_format(($sum_all_relatorio - $sum_all_vlfrete), 2, ',', '.'), 0, 2, 'R', 1);
  $pdf->Ln(0);

  $pdf->SetFont('Calibri', '', 12);
  $pdf->Cell(162, 8, 'VALOR TOTAL DO RELATÓRIO: ', 0, 0, 'R', 0);

  $pdf->setFillColor(255, 199, 199);
  $pdf->SetFont('Calibri', '', 22);
  $pdf->Cell(38, 8,  'R$: ' . number_format($sum_all_relatorio, 2, ',', '.'), 0, 2, 'R', 1);
}

// relatório de vendas - formas de pagamento
if ($tipo_relatorio == 'F') {
  $i = 0;
  $row = 0;
  $sum = 0;
  $nummer = 297;
  $group_pgto = null;
  $group_sum = null;
  $TOTAL = null;

  $pdf->SetTitle('RELATÓRIO DE VENDAS - FORMAS DE PAGAMENTO', 'UTF8');

  $pdf->SetFont('Calibri', '', 13);

  $pdf->Cell(200, 5, 'RELATÓRIO DE VENDAS - FORMAS DE PAGAMENTO', 0, 2, 'C', 0);
  $pdf->Ln(1);
  $pdf->SetFont('Calibri', '', 9);
  $pdf->Cell(200, 5, 'Período: ' . $periodo_data, 'B', 2, 'L', 0);
  $pdf->Cell(200, 5, 'Ordem: ' . $order_search_site, 'B', 2, 'L', 0);
  $pdf->Cell(200, 5, 'Estado(s): ' . ($estados ? $estados : 'Todos'), 'B', 2, 'L', 0);
  $pdf->Cell(200, 5, 'Forma pagamento: ' . ($forma_pagamento ? $forma_pagamento : 'Todos'), 'B', 2, 'L', 0);
  $pdf->Cell(200, 5, 'Status pedidos: ' . ($stat ? $forma_pagamento : 'Todos'), 'B', 2, 'L', 0);
  $pdf->Ln(5);

  $group = [];
  $TOTAL = null;
  foreach ($rs as $cli) {
    $TOTAL = valor_pagamento($cli['valor_compra'], $cli['frete_valor'], $cli['desconto_cupom'], '$', $cli['desconto_boleto']);
    $group[$cli['forma_pagamento']]['vlprod'] += ($cli['valor_pago'] * $cli['quantidade']);
    $group[$cli['forma_pagamento']]['vlfret'] += $cli['frete_valor'];
    $group[$cli['forma_pagamento']]['vltotl'] += $TOTAL['TOTAL_COMPRA_C_BOLETO'];
  }
  // print_r($group);

  $pdf->setFillColor(204, 204, 204);
  $pdf->SetFont('Calibri', '', 11);
  $pdf->Cell(104, 5, 'Forma de Pagamento', 'B', 0, 'L', 1);
  $pdf->Cell(32, 5, 'Valor Produtos', 'B', 0, 'C', 1);
  $pdf->Cell(32, 5, 'Valor Frete', 'B', 0, 'C', 1);
  $pdf->Cell(32, 5, 'Total', 'B', 2, 'R', 1);
  $pdf->Ln(1);

  $i = 0;
  $pdf->SetFont('Calibri', '', 10);

  $vlprod = 0;
  $vlfret = 0;
  $vltotl = 0;
  foreach ($group as $k => $grp) {
    $pdf->setFillColor(241, 241, 241);
    $pdf->Cell(104, 5, $k, '0', 0, 'L', ($i % 2 ? 1 : 0));
    $pdf->Cell(32, 5, 'R$: ' . number_format($grp['vlprod'], 2, ',', '.'), '0', 0, 'C', ($i % 2 ? 1 : 0));
    $pdf->Cell(32, 5, 'R$: ' . number_format($grp['vlfret'], 2, ',', '.'), '0', 0, 'C', ($i % 2 ? 1 : 0));
    $pdf->Cell(32, 5, 'R$: ' . number_format($grp['vltotl'], 2, ',', '.'), '0', 2, 'R', ($i % 2 ? 1 : 0));
    $pdf->Ln(1);
    $vlprod += $grp['vlprod'];
    $vlfret += $grp['vlfret'];
    $vltotl += $grp['vltotl'];
    $i++;
  }
  $pdf->setFillColor(198, 198, 198);
  $pdf->SetFont('Calibri', '', 13);
  $pdf->Cell(104, 7, 'Forma de Pagamento', 'T', 0, 'L', 1);
  $pdf->setFillColor(255, 199, 199);
  $pdf->Cell(32, 7, 'R$: ' . number_format($vlprod, 2, ',', '.'), 'T', 0, 'C', 1);
  $pdf->setFillColor(255, 179, 179);
  $pdf->Cell(32, 7, 'R$: ' . number_format($vlfret, 2, ',', '.'), 'T', 0, 'C', 1);
  $pdf->setFillColor(255, 159, 159);
  $pdf->Cell(32, 7, 'R$: ' . number_format($vltotl, 2, ',', '.'), 'T', 2, 'R', 1);
}

// relatório de vendas - como nos conheceu
if ($tipo_relatorio == 'I') {
  $i = 0;
  $row = 0;
  $sum = 0;
  $nummer = 297;
  $group_pgto = null;
  $group_sum = null;
  $TOTAL = null;

  $pdf->SetTitle('RELATÓRIO DE VENDAS - COMO NOS CONHECEU', 'UTF8');

  $pdf->SetFont('Calibri', '', 13);

  $pdf->Cell(200, 5, 'RELATÓRIO DE VENDAS - COMO NOS CONHECEU', 0, 2, 'C', 0);
  $pdf->Ln(1);
  $pdf->SetFont('Calibri', '', 9);
  $pdf->Cell(200, 5, 'Período: ' . $periodo_data, 'B', 2, 'L', 0);
  $pdf->Cell(200, 5, 'Ordem: ' . $order_search_site, 'B', 2, 'L', 0);
  $pdf->Cell(200, 5, 'Estado(s): ' . ($estados ? $estados : 'Todos'), 'B', 2, 'L', 0);
  $pdf->Cell(200, 5, 'Forma pagamento: ' . ($forma_pagamento ? $forma_pagamento : 'Todos'), 'B', 2, 'L', 0);
  $pdf->Cell(200, 5, 'Status pedidos: ' . ($stat ? $forma_pagamento : 'Todos'), 'B', 2, 'L', 0);
  $pdf->Ln(5);
  $group = [];
  foreach ($rs as $cli) {
    $cli['indicacao'] = empty($cli['indicacao']) ? 'NÃO INFORMADO' : $cli['indicacao'];
    $group[$cli['indicacao']]['primeira_compra'] += $cli['primeira_compra'];
    $group[$cli['indicacao']]['retorno'] += $cli['retorno'];
    $group[$cli['indicacao']]['total'] = '';
  }
  // print_r($group);

  $pdf->setFillColor(204, 204, 204);
  $pdf->SetFont('Calibri', '', 11);
  $pdf->Cell(104, 5, 'Indicação', 'B', 0, 'L', 1);
  $pdf->Cell(32, 5, 'Primeira Compra', 'B', 0, 'C', 1);
  $pdf->Cell(32, 5, 'Retorno', 'B', 0, 'C', 1);
  $pdf->Cell(32, 5, 'Total', 'B', 2, 'R', 1);
  $pdf->Ln(1);

  $i = 0;
  $pdf->SetFont('Calibri', '', 10);

  $vltotl = 0;
  $retorno = 0;
  $primeira_compra = 0;
  foreach ($group as $k => $grp) {
    $pdf->setFillColor(241, 241, 241);
    $pdf->Cell(104, 5, $k, '0', 0, 'L', ($i % 2 ? 1 : 0));
    $pdf->Cell(32, 5, $grp['primeira_compra'], '0', 0, 'C', ($i % 2 ? 1 : 0));
    $pdf->Cell(32, 5, $grp['retorno'], '0', 0, 'C', ($i % 2 ? 1 : 0));
    $pdf->Cell(32, 5, $grp['retorno'] + $grp['primeira_compra'], '0', 2, 'R', ($i % 2 ? 1 : 0));
    $pdf->Ln(1);
    $primeira_compra += $grp['primeira_compra'];
    $retorno += $grp['retorno'];
    $vltotl += ($grp['retorno'] + $grp['primeira_compra']);
    $i++;
  }
  $pdf->setFillColor(198, 198, 198);
  $pdf->SetFont('Calibri', '', 13);
  $pdf->Cell(104, 7, 'TOTAL', 'T', 0, 'L', 1);
  $pdf->setFillColor(255, 199, 199);
  $pdf->Cell(32, 7, $primeira_compra, 'T', 0, 'C', 1);
  $pdf->setFillColor(255, 179, 179);
  $pdf->Cell(32, 7, $retorno, 'T', 0, 'C', 1);
  $pdf->setFillColor(255, 159, 159);
  $pdf->Cell(32, 7, $vltotl, 'T', 2, 'R', 1);

  // die();
}

if ($tipo_relatorio == 'R') {
  $i = 0;
  $row = 0;
  $sum = 0;
  $nummer = 297;
  $group_pgto = null;
  $group_sum = null;
  $TOTAL = null;

  $pdf->SetTitle('RELATÓRIO DE RECOMPRAS POR CLIENTES', 'UTF8');

  $pdf->setFillColor(204, 204, 204);
  $pdf->SetFont('Calibri', '', 16);
  $pdf->Cell(200, 8, 'RELATÓRIO DE RECOMPRAS POR CLIENTES', 0, 2, 'C', 1);
  $pdf->Ln(1);
  $pdf->SetFont('Calibri', '', 9);
  $pdf->Cell(200, 5, 'Período: ' . $periodo_data, 'B', 2, 'L', 0);
  $pdf->Cell(200, 5, 'Ordem: ' . $order_search_site, 'B', 2, 'L', 0);
  $pdf->Cell(200, 5, 'Estado(s): ' . ($estados ? $estados : 'Todos'), 'B', 2, 'L', 0);
  $pdf->Cell(200, 5, 'Forma pagamento: ' . ($forma_pagamento ? $forma_pagamento : 'Todos'), 'B', 2, 'L', 0);
  $pdf->Cell(200, 5, 'Status pedidos: ' . ($stat ? $forma_pagamento : 'Todos'), 'B', 2, 'L', 0);
  $pdf->Ln(5);

  $pdf->setFillColor(204, 204, 204);
  $pdf->SetFont('Calibri', '', 12);
  $pdf->Cell(60, 7, 'Cliente', 'B', 0, 'L', 1);
  $pdf->Cell(35, 7, 'Data 1° Compra', 'B', 0, 'C', 1);
  $pdf->Cell(35, 7, 'Data Ult. Compra', 'B', 0, 'C', 1);
  $pdf->Cell(35, 7, 'Total Compras', 'B', 0, 'R', 1);
  $pdf->Cell(35, 7, 'Média de Compra', 'B', 2, 'R', 1);
  $pdf->Ln(1);

  $i = 0;
  $pdf->SetFont('Calibri', '', 10);
  $arrw = [];
  foreach ($rs as $cli) {
    if ($group_cli != $cli['id_cliente'] . $cli['id_pedido']) {
      $group_cli = $cli['id_cliente'] . $cli['id_pedido'];

      if ($zuzim_peludo != $cli['id_cliente']) {
        $zuzim_peludo = $cli['id_cliente'];
        $sql = Lojas::connection()->query('SELECT min(created_at) as primeira_compra, max(created_at) as ultima_compra, sum(valor_compra) as total_compras, avg(valor_compra) as media FROM pedidos WHERE id_cliente =' . $peludo_zuzim);

        foreach ($sql as $arrw) {
          array_push($arrw, $rws);
        }

        $pdf->Cell(60, 5, $cli['p_nome'], '0', 0, 'L', ($i % 2 ? 1 : 0));
        $pdf->Cell(35, 5, date("d/m/Y", strtotime($arrw['primeira_compra'])), '0', 0, 'C', ($i % 2 ? 1 : 0));
        $pdf->Cell(35, 5, date("d/m/Y", strtotime($arrw['ultima_compra'])), '0', 0, 'C', ($i % 2 ? 1 : 0));
        $pdf->Cell(35, 5, 'R$: ' . number_format($arrw['total_compras'], 2, ',', '.'), '0', 0, 'R', ($i % 2 ? 1 : 0));
        $pdf->Cell(35, 5, 'R$: ' . number_format($arrw['media'], 2, ',', '.'), '0', 1, 'R', ($i % 2 ? 1 : 0));
        // echo '<br>'. $cli['id_cliente'];
      }
    }
    $i++;
  }
  // die();
}
// die();
ob_start();
$pdf->Output();
ob_end_flush();
