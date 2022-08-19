<?php
defined('PATH_ROOT') || define('PATH_ROOT', realpath($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR);
require_once PATH_ROOT . '/app/settings.php';
require_once PATH_ROOT . '/app/vendor/autoload.php';
require_once PATH_ROOT . '/app/settings-config.php';
require_once PATH_ROOT . '/assets/' . ASSETS .  '/settings.php';
require_once PATH_ROOT . '/app/includes/bibli-funcoes.php';

// extract para os dados da loja;
extract($CONFIG);

$rel_lucro = filter_input(INPUT_POST, 'rel_lucro');

$produto   = filter_input(INPUT_POST, 'produto');
$fotos     = filter_input(INPUT_POST, 'fotos');
$estoque   = filter_input(INPUT_POST, 'estoque');
$marca     = filter_input(INPUT_POST, 'marca');
$preco     = filter_input(INPUT_POST, 'preco');
$custo     = filter_input(INPUT_POST, 'custo');
$grupos    = filter_input(INPUT_POST, 'grupos');
$fiscais   = filter_input(INPUT_POST, 'fiscais');
$frete     = filter_input(INPUT_POST, 'frete');

// outher actions
$produto_id   = filter_input(INPUT_POST, 'produto_id');
$produto_ordem   = filter_input(INPUT_POST, 'produto_ordem');

$estoques    = filter_input(INPUT_POST, 'estoques', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
$preco_promo = filter_input(INPUT_POST, 'preco_promo', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
$preco_lucro = filter_input(INPUT_POST, 'preco_lucro', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
$preco_custo = filter_input(INPUT_POST, 'preco_custo', FILTER_DEFAULT, FILTER_FORCE_ARRAY);

$marcas_id     = filter_input(INPUT_POST, 'marca_id');
$marcas_ordem = filter_input(INPUT_POST, 'marca_ordem');

$grupos_id    = filter_input(INPUT_POST, 'grupos_id');
$grupos_ordem = filter_input(INPUT_POST, 'grupos_ordem');

// Campos Fiscais
$csosn = filter_input(INPUT_POST, 'csosn');
$unid = filter_input(INPUT_POST, 'unid');
$cfop = filter_input(INPUT_POST, 'cfop');
$cest = filter_input(INPUT_POST, 'cest');
$ncm = filter_input(INPUT_POST, 'ncm');

// define as condições do where
$where = sprintf('where produtos.loja_id=%u and produtos.excluir = 0 and produtos.status = 0 ', $loja_id);

$where .= $frete == 1 ? sprintf('and produtos.id_frete = 0 ') : null;

// $where .= $produto == 1 && $produto_id == -1 ? sprintf('and produtos.nome_produto <> "" ') : null;
$where .= $produto == 1 && $produto_id == 0  ? sprintf('and produtos.nome_produto = "" ') : null;
$where .= $produto == 1 && $produto_id > 0   ? sprintf('and produtos.codigo_id = %u ', $produto_id) : null;

$where .= $marca == 1 && $marcas_id == 0  ? sprintf('and produtos.id_marca = 0 ') : null;
$where .= $marca == 1 && $marcas_id == -1 ? sprintf('and produtos.id_marca > 0 ') : null;
$where .= $marca == 1 && $marcas_id > 0   ? sprintf('and produtos.id_marca = %u ', $marcas_id) : null;

$where .= $grupos == 1 && $grupos_id == 0  ? sprintf('and grupos.id is null ') : null;
$where .= $grupos == 1 && $grupos_id == -1 ? sprintf('and grupos.id > 0 ') : null;
$where .= $grupos == 1 && $grupos_id > 0   ? sprintf('and grupos.id = %u ', $grupos_id) : null;

$where .= $estoque == 1 && ($estoques[0] == 0 && $estoques[1] == 0) ? sprintf('and produtos.estoque = 0 ') : null;
$where .= $estoque == 1 && ($estoques[0] > 0 || $estoques[1] > 0)   ? sprintf('and produtos.estoque between %u and %u ', $estoques[0], $estoques[1]) : null;

$where .= $preco == 1 && (dinheiro($preco_promo[0]) == 0 && dinheiro($preco_promo[1]) == 0) ? sprintf('and produtos.preco_promo = 0 ') : null;
$where .= $preco == 1 && (dinheiro($preco_promo[0]) > 0 || dinheiro($preco_promo[1]) > 0) ? sprintf('and produtos.preco_promo between %u and %u ', dinheiro($preco_promo[0]), dinheiro($preco_promo[1])) : null;

$where .= $custo == 1 && (dinheiro($preco_custo[0]) == 0 && dinheiro($preco_custo[1]) == 0) ? sprintf('and produtos.preco_custo = 0 ') : null;
$where .= $custo == 1 && (dinheiro($preco_custo[0]) > 0 || dinheiro($preco_custo[1]) > 0) ? sprintf('and produtos.preco_custo between %u and %u ', dinheiro($preco_custo[0]), dinheiro($preco_custo[1])) : null;

$where .= $fiscais == 1 && $csosn == 1 ? sprintf('and produtos.csosn <> "" ') : null;
$where .= $fiscais == 1 && $csosn == 0 ? sprintf('and produtos.csosn = "" ') : null;

$where .= $fiscais == 1 && $unid == 1  ? sprintf('and produtos.unid <> "" ') : null;
$where .= $fiscais == 1 && $unid == 0  ? sprintf('and produtos.unid = "" ') : null;

$where .= $fiscais == 1 && $cfop == 1  ? sprintf('and produtos.cfop <> "" ') : null;
$where .= $fiscais == 1 && $cfop == 0  ? sprintf('and produtos.cfop = "" ') : null;

$where .= $fiscais == 1 && $ncm == 1  ? sprintf('and produtos.ncm <> "" ') : null;
$where .= $fiscais == 1 && $ncm == 0  ? sprintf('and produtos.ncm = "" ') : null;

$where .= $fiscais == 1 && $cest == 1  ? sprintf('and produtos.cest <> "" ') : null;
$where .= $fiscais == 1 && $cest == 0  ? sprintf('and produtos.cest = "" ') : null;
$where .= $fotos == 1 ? sprintf('and produtos_imagens.id is null ') : null;

// ordernção basica
$order = $grupos == 1 || $marca == 1 || $produto == 1 ? 'order by ' : null;
$order .= $grupos == 1 && $grupos_ordem == 'grupos_asc' ? sprintf('grupos.grupo asc, ') : null;
$order .= $grupos == 1 && $grupos_ordem == 'grupos_desc' ? sprintf('grupos.grupo desc, ') : null;
$order .= $produto == 1 && $produto_ordem == 'produto_asc' ? sprintf('produtos.nome_produto asc, ') : null;
$order .= $produto == 1 && $produto_ordem == 'produto_desc' ? sprintf('produtos.nome_produto desc, ') : null;
$order .= $marca == 1 && $marcas_ordem == 'marca_asc' ? sprintf('marcas.marcas asc, ') : null;
$order .= $marca == 1 && $marcas_ordem == 'marca_desc' ? sprintf('marcas.marcas desc, ') : null;
$order = rtrim($order, ', ');

// sql de busca
$loop = Lojas::connection()->query(
  'select '

    . 'produtos.codigo_id, '
    . 'produtos.nome_produto, '
    . 'produtos.codigo_produto, '
    . 'produtos.id_marca, '
    . 'produtos.estoque, '
    . 'produtos.preco_custo, '
    . 'produtos.preco_promo, '
    . 'produtos.preco_venda, '
    . 'produtos.csosn, '
    . 'produtos.unid, '
    . 'produtos.cfop, '
    . 'produtos.ncm, '
    . 'produtos.cest, '
    . 'cores.nomecor, '
    . 'tamanhos.nometamanho, '
    . 'marcas.marcas, '
    . 'grupos.grupo, '
    . 'produtos_imagens.id as imagens_id, '
    . 'A.tipo as tipo_a, '
    . 'B.tipo as tipo_b '

    . 'from produtos '
    . 'join cores on produtos.id_cor = cores.id '
    . 'join tamanhos on produtos.id_tamanho = tamanhos.id '
    . 'join marcas on produtos.id_marca = marcas.id '
    . 'join opcoes_tipo A on cores.opcoes_id = A.id '
    . 'join opcoes_tipo B on tamanhos.opcoes_id = B.id '
    . 'left join produtos_imagens on produtos_imagens.codigo_id = produtos.codigo_id '
    . 'left join produtos_menus on produtos_menus.codigo_id = produtos.codigo_id '
    . 'left join grupos on grupos.id = produtos_menus.id_grupo '

    . $where
    . ' group by produtos.id '
    . $order
);

// echo Lojas::connection()->last_query;
// print($loop->rowCount());
// return;

class RelVendas extends MyFpdf
{
  // var $periodo_data;
  // var $order_search_site;
  // var $estados;
  // var $forma_pagamento;
  // var $status_ativos;
  public $lucro;

  function RelLucro($boolean = null)
  {
    $this->lucro = $boolean;
  }

  function Header()
  {
    $this->AddFont('Calibri', '', 'Calibri.php');
    $this->AddFont('Titillium Web', '', 'titilliumweb-regular.php');
    $this->AddFont('Titillium Web Bold', '', 'titilliumweb-bold.php');

    $this->SetTitle('RELATÓRIOS DE PRODUTOS', 'UTF8');
    $this->SetFont('Calibri', '', 13);
    $this->setFillColor(180, 180, 180);
    $this->Cell(285, 5, 'RELATÓRIOS DE PRODUTOS', 0, 2, 'C', 0);
    $this->Ln(5);

    $this->SetFont('Calibri', '', 10);
    // $this->Cell(200, 5, 'Marcas: ' . (!empty($m) ? implode(' | ', $m) : 'Todos'), 'B', 2, 'L', 0);

    $this->setFillColor(199, 199, 199);
    $this->Cell((!$this->lucro ? 125 : 175), 7, 'Nome Produto', 0, 0, 'L', 1);

    if (!$this->lucro) $this->Cell(10, 7, 'Foto', 0, 0, 'C', 1);
    if (!$this->lucro) $this->Cell(20, 7, 'Marca', 0, 0, 'C', 1);
    if (!$this->lucro) $this->Cell(20, 7, 'Grupos', 0, 0, 'C', 1);

    $this->Cell(15, 7, 'Estoque', 0, 0, 'C', 1);
    if ($this->lucro) $this->Cell(20, 7, 'Pr. Custo', 0, 0, 'C', 1);
    if ($this->lucro) $this->Cell(20, 7, 'Pr. De', 0, 0, 'C', 1);
    if ($this->lucro) $this->Cell(20, 7, '%Desc', 0, 0, 'C', 1);
    $this->Cell(20, 7, 'Pr. Venda', 0, 0, 'C', 1);
    if ($this->lucro) $this->Cell(20, 7, 'Lucro Venda', 0, 2, 'C', 1);

    if (!$this->lucro) $this->Cell(15, 7, 'CSOSN', 0, 0, 'C', 1);
    if (!$this->lucro) $this->Cell(15, 7, 'Unidade', 0, 0, 'C', 1);
    if (!$this->lucro) $this->Cell(15, 7, 'CFOP', 0, 0, 'C', 1);
    if (!$this->lucro) $this->Cell(15, 7, 'NCM', 0, 0, 'C', 1);
    if (!$this->lucro) $this->Cell(15, 7, 'CEST', 0, 2, 'C', 1);



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
  }
}

$pdf = new RelVendas('L', 'mm', 'A4');

$pdf->RelLucro($rel_lucro);
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

$i = 0;
$row = 0;
$sum = 1;
$nummer = 297;
$group_uf = null;
$group_ped = null;
$group_cli = null;
$group_mar = null;
$group_sum = null;
$TOTAL = null;

$sum_all_qtde = 0;
$sum_all_vlfrete = 0;
$sum_all_desvbol = 0;
$sum_all_mercadoria = 0;
$sum_all_relatorio = 0;

// $pdf->SetTitle('RELATÓRIOS DE PRODUTOS', 'UTF8');

// $pdf->SetFont('Calibri', '', 13);
// $pdf->setFillColor(180, 180, 180);
// $pdf->Cell(285, 5, 'RELATÓRIOS DE PRODUTOS (incompletos)', 0, 2, 'C', 0);
// $pdf->Ln(5);

// $pdf->SetFont('Calibri', '', 10);
// // $pdf->Cell(200, 5, 'Marcas: ' . (!empty($m) ? implode(' | ', $m) : 'Todos'), 'B', 2, 'L', 0);

// $pdf->setFillColor(199, 199, 199);
// $pdf->Cell(125, 7, 'Nome Produto', 0, 0, 'L', 1);
// $pdf->Cell(10, 7, 'Foto', 0, 0, 'C', 1);
// $pdf->Cell(20, 7, 'Marca', 0, 0, 'C', 1);
// $pdf->Cell(20, 7, 'Grupos', 0, 0, 'C', 1);
// $pdf->Cell(15, 7, 'Estoque', 0, 0, 'C', 1);
// $pdf->Cell(20, 7, 'Pr. Venda', 0, 0, 'C', 1);

// $pdf->Cell(15, 7, 'CSOSN', 0, 0, 'C', 1);
// $pdf->Cell(15, 7, 'Unidade', 0, 0, 'C', 1);
// $pdf->Cell(15, 7, 'CFOP', 0, 0, 'C', 1);
// $pdf->Cell(15, 7, 'NCM', 0, 0, 'C', 1);
// $pdf->Cell(15, 7, 'CEST', 0, 2, 'C', 1);

$pdf->Ln(1);

$pdf->SetFont('Calibri', '', 9);

$lucro = 0;

$lucrodepor = 0;

$m = [];
$rs = [];

// Percorrer o vetor analisando os valores
$v_a = dinheiro($preco_lucro[0]);
$v_b = dinheiro($preco_lucro[1]);

$preco_custo = 0;
$preco_promo = 0;
foreach ($loop as $rws) {

  // $m[$rws['id_marca']] = $rws['marcas'];

  $lucro = $rws['preco_custo'] > 0 ? (($rws['preco_promo'] * 100 / $rws['preco_custo']) - 100) : 0;
  
  $lucrodepor = $rws['preco_venda'] > 0 ? (($rws['preco_promo'] * 100 / $rws['preco_venda']) - 100) : 0;

  if ($rel_lucro && $v_a > 0 && $v_b > 0) {
    if ($v_a <= $lucro && $v_b >= $lucro) {
      array_push($rs, $rws);
    }
  } else {
    array_push($rs, $rws);
  }
}

foreach ($rs as $rws) {

  $lucro = $rws['preco_custo'] > 0 ? (($rws['preco_promo'] * 100 / $rws['preco_custo']) - 100) : 0;

  $lucrodepor = $rws['preco_venda'] > 0 ? (($rws['preco_promo'] * 100 / $rws['preco_venda']) - 100) : 0;


  if (($i % 2) == 0)
    $pdf->setFillColor(243, 243, 243);
  else
    $pdf->setFillColor(255, 255, 255);

  $pdf->Cell((!$rel_lucro ? 125 : 175), 7, implode(' - ', array_filter([$rws['nome_produto'], $rws['tipo_a'], $rws['nomecor'], $rws['tipo_b'], $rws['nometamanho']])), 0, 0, 'L', 1);

  if (!$rel_lucro) $pdf->Cell(10, 7, $rws['imagens_id'] ? 'Sim' : '--', 0, 0, 'C', 1);
  if (!$rel_lucro) $pdf->Cell(20, 7, $rws['marcas'] ? $rws['marcas'] : '--', 0, 0, 'C', 1);
  if (!$rel_lucro) $pdf->Cell(20, 7, $rws['grupo'] ? $rws['grupo'] : '--', 0, 0, 'C', 1);

  $pdf->Cell(15, 7, $rws['estoque'], 0, 0, 'C', 1);

  // Preco Custo
  if ($rel_lucro) $pdf->Cell(20, 7, sprintf('R$: %s', number_format($rws['preco_custo'], 2, ',', '.')), 0, 0, 'C', 1);

  // Preco de
  if ($rel_lucro) $pdf->Cell(20, 7, sprintf('R$: %s', number_format($rws['preco_venda'], 2, ',', '.')), 0, 0, 'C', 1);


  // % em cima de em cima por
  if ($rel_lucro) $pdf->Cell(20, 7, sprintf('%s%%', round($lucrodepor, 2)), 0, 0, 'C', 1);

  $pdf->Cell(20, 7, sprintf('R$: %s', number_format($rws['preco_promo'], 2, ',', '.')), 0, 0, 'C', 1);

  // Lucro
  if ($rel_lucro) $pdf->Cell(20, 7, sprintf('%s%%', round($lucro, 2)), 0, 2, 'C', 1);

  if (!$rel_lucro) $pdf->Cell(15, 7, $rws['csosn'] ? $rws['csosn'] : '--', 0, 0, 'C', 1);
  if (!$rel_lucro) $pdf->Cell(15, 7, $rws['unid'] ? $rws['unid'] : '--', 0, 0, 'C', 1);
  if (!$rel_lucro) $pdf->Cell(15, 7, $rws['cfop'] ? $rws['cfop'] : '--', 0, 0, 'C', 1);
  if (!$rel_lucro) $pdf->Cell(15, 7, $rws['ncm']  ? $rws['ncm']  : '--', 0, 0, 'C', 1);
  if (!$rel_lucro) $pdf->Cell(15, 7, $rws['cest'] ? $rws['cest'] : '--', 0, 2, 'C', 1);

  $pdf->Ln(0);

  $preco_custo += $rws['preco_custo'];
  $preco_promo += $rws['preco_promo'];

  $i++;
}

$pdf->SetFont('Calibri', '', 17);

if ($rel_lucro) {
  $pdf->Ln(3);

  $pdf->setFillColor(255, 255, 255);
  $pdf->Cell(235, 10, 'PREÇO CUSTO', 0, 0, 'R', 1);
  $pdf->setFillColor(205, 205, 205);
  $pdf->Cell(50, 10, 'R$: ' . number_format($preco_custo, 2, ',', '.'), 0, 1, 'R', 1);

  $pdf->setFillColor(255, 255, 255);
  $pdf->Cell(235, 10, 'PREÇO VENDA', 0, 0, 'R', 1);
  $pdf->setFillColor(205, 205, 205);
  $pdf->Cell(50, 10, 'R$: ' . number_format($preco_promo, 2, ',', '.'), 0, 1, 'R', 1);


  $pdf->setFillColor(255, 255, 255);
  $pdf->Cell(235, 10, 'TOTAL DE LUCRO', 0, 0, 'R', 1);
  $pdf->setFillColor(205, 205, 205);
  $pdf->Cell(50, 10, round((($preco_promo * 100) / $preco_custo - 100), 2) . '%', 0, 2, 'R', 1);
}

// $pdf->SetFont('Titillium Web', '', 8);
// $pdf->Cell(200, 1, str_pad('', 328, '.', STR_PAD_RIGHT) , 0, 2, 'R', 0);
// $pdf->Ln(2);

// $pdf->setFillColor(199, 199, 199);
// $pdf->SetFont('Calibri', '', 10);
// $pdf->Cell(162, 8, 'TOTAL DE PEDIDOS: ', 0, 0, 'R', 0);

// $pdf->SetFont('Calibri', '', 15);
// $pdf->Cell(38, 8,  $sum_all_qtde, 0, 2, 'R', 1);
// $pdf->Ln(0);
// $pdf->SetFont('Calibri', '', 10);
// $pdf->Cell(162, 8, 'TOTAL DOS FRETES: ', 0, 0, 'R', 0);

// $pdf->SetFont('Calibri', '', 15);
// $pdf->Cell(38, 8,  'R$: ' . number_format($sum_all_vlfrete, 2, ',', '.'), 0, 2, 'R', 1);
// $pdf->Ln(0);
// $pdf->SetFont('Calibri', '', 10);
// $pdf->Cell(162, 8, 'TOTAL DOS PRODUTOS S/ FRETE: ', 0, 0, 'R', 0);

// $pdf->SetFont('Calibri', '', 15);
// // $pdf->Cell(38, 8,  'R$: ' . number_format($sum_all_mercadoria, 2, ',', '.'), 0, 2, 'R', 1);
// $pdf->Cell(38, 8,  'R$: ' . number_format(($sum_all_relatorio - $sum_all_vlfrete), 2, ',', '.'), 0, 2, 'R', 1);
// $pdf->Ln(0);
// $pdf->SetFont('Calibri', '', 10);
// $pdf->Cell(162, 8, 'VALOR TOTAL DO RELATÓRIO: ', 0, 0, 'R', 0);

// $pdf->SetFont('Calibri', '', 15);
// $pdf->Cell(38, 8,  'R$: ' . number_format($sum_all_relatorio, 2, ',', '.'), 0, 2, 'R', 1);

$pdf->Output();
