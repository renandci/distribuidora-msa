<?php
// ob_start();
set_time_limit(-1);
// Includes
include '../app/settings.php';
include PATH_ROOT . 'app/vendor/autoload.php';
include PATH_ROOT . 'app/settings-config.php';
include PATH_ROOT . 'assets/' . ASSETS .  '/settings.php';
include PATH_ROOT . 'app/includes/bibli-funcoes.php';

/**
 * Produtos
 * @description Gerado uma camada no mysql com uma view
 * @bkp $Produtos = Produtos::all( $conditions );
 */
// $Produtos = ProdutosViewsTemp::all( $conditions );

$Conditions = [];
$Conditions['select'] = ''
  . 'SQL_CACHE produtos.id, '
  . 'produtos.loja_id, '
  . 'produtos.id_cor, '
  . 'produtos.id_marca, '
  . 'produtos.id_tamanho, '
  . 'produtos.codigo_id,'
  . 'produtos.codigo_produto, '
  . 'produtos.nome_produto, '
  . 'produtos.subnome_produto, '
  . 'produtos.postagem, '
  . 'produtos.estoque, '
  . 'produtos.preco_venda, '
  . 'produtos.preco_promo, '
  . 'produtos.placastatus, '
  . 'produtos.categoria, '
  . 'produtos.utilidades,'
  . 'produtos.frete, '
  . 'produtos.status, '
  . 'produtos.excluir, '
  . 'produtos.ordem,'
  . 'marcas.marcas, '
  . 'marcas.disponib_entrega, '
  . 'cores.nomecor, '
  . 'cores.cor1, '
  . 'cores.cor2, '
  . 'tamanhos.nometamanho, '
  . 'tamanhos.hex1, '
  . 'tamanhos.hex2,'
  . 'opca.tipo AS opc_tipo_a, '
  . 'opcb.tipo AS opc_tipo_b, 	'
  . 'grupos.id AS id_grupo, '
  . 'grupos.grupo, '
  . 'subgrupos.id AS id_subgrupo, '
  . 'subgrupos.subgrupo, '
  . 'produtos_imagens.imagem ';

// $Conditions['conditions'] = ' 1 = 1 ';
// $ConditionsFilters['conditions'] = ' 1 = 1 ';
// Busca somente os dados da loja ativa no dominio principal

$Conditions['joins'] = ''
  . 'INNER JOIN produtos ON produtos_menus.codigo_id = produtos.codigo_id '
  . 'INNER JOIN marcas ON produtos.id_marca = marcas.id '
  . 'INNER JOIN cores ON produtos.id_cor = cores.id '
  . 'INNER JOIN tamanhos ON produtos.id_tamanho = tamanhos.id '
  . 'INNER JOIN opcoes_tipo opca ON opca.id = cores.opcoes_id '
  . 'INNER JOIN opcoes_tipo opcb ON opcb.id = tamanhos.opcoes_id '
  . 'INNER JOIN grupos ON produtos_menus.id_grupo = grupos.id '
  . 'INNER JOIN subgrupos ON produtos_menus.id_subgrupo = subgrupos.id '
  . 'INNER JOIN produtos_imagens ON produtos_imagens.codigo_id = produtos.codigo_id ';

$Conditions['conditions'] = ''
  . 'produtos.status = 0 '
  . 'AND produtos.excluir = 0 '
  . 'AND produtos.estoque > 0 '
  . 'AND marcas.excluir = 0 '
  . 'AND produtos.id_cor = produtos_imagens.cor_id '
  . 'AND produtos_imagens.capa = 1 ';

$Conditions['conditions'] .= sprintf('AND produtos.loja_id=%u ', $CONFIG['loja_id']);

/**
 * conditions de Pesquisa no site
 */
if (!empty($GET['search']) && $GET['search'] != '') {
  $A = sprintf('%s', $GET['search']);
  $B = implode('%" AND produtos.nome_produto like "%', explode(' ', sprintf('%s', str_replace([' de', ' para', ' com', ' a', ' o', ' da'], "", $GET['search']))));

  $Conditions['conditions'] .= 'AND(produtos.nome_produto like "%s" OR(produtos.nome_produto like %s OR (produtos.codigo_produto like "%s"))) ';
  $Conditions['conditions'] = sprintf($Conditions['conditions'], $A, "\"{$B}\"", $A);
}

/**
 * conditions para Grupos ou SubGrupos
 */
if (!empty($GET['grupo']) && $GET['grupo'] != '') {
  $Conditions['conditions'] .= sprintf('AND produtos_menus.id_grupo IN(%s)', implode('", "', explode(',', str_replace(['[', ']'], '"', $GET['grupo']))));
}

if (!empty($GET['subgrupo']) && $GET['subgrupo'] != '') {
  $Conditions['conditions'] .= sprintf('AND produtos_menus.id_subgrupo IN(%s)', implode('", "', explode(',', str_replace(['[', ']'], '"', $GET['subgrupo']))));
}

/**
 * conditions para Categoria (Generos)
 */
if (!empty($GET['genero']) && $GET['genero'] != '') {
  $loop_genero = null;
  $GET_GENERO = explode(',', str_replace(['[', ']'], "", $GET['genero']));
  foreach ($GET_GENERO as $V_GET_GENERO) {
    $loop_genero[] = checkCategoria($V_GET_GENERO);
  }
  // New genero
  $GET_GENERO = '[' . implode(',', $loop_genero) . ']';

  $Conditions['conditions'] .= sprintf('AND produtos.categoria IN(%s)', implode('", "', explode(',', str_replace(['[', ']'], '"', $GET_GENERO))));
}

/**
 * conditions para Busca de Cores
 */
if (!empty($GET['cores']) && $GET['cores'] != '') {
  $Conditions['conditions'] .= sprintf('AND produtos.id_cor IN(%s)', implode('","', explode(',', str_replace(['[', ']'], '"', $GET['cores']))));
}

/**
 * conditions para Busca de Tamanhos
 */
if (!empty($GET['tamanhos']) && $GET['tamanhos'] != '') {
  $Conditions['conditions'] .= sprintf('AND produtos.id_tamanho IN(%s)', implode('", "', explode(',', str_replace(['[', ']'], '"', $GET['tamanhos']))));
}

// /**
//  * conditions para Busca de Marcas
//  */
// if ( ! empty( $GET['marcas'] ) && $GET['marcas'] != '') {
// 	$Conditions['conditions'] .= sprintf('AND marcas like %s', implode('" OR marcas like "', explode(',', str_replace(['[', ']'], '"', $GET['marcas']))));
// }

$Conditions['order'] = '';

if (!empty($STORE['config']['sql']['order'])) {
  $Conditions['order'] .= $STORE['config']['sql']['order'];
} else {
  $Conditions['order'] .= 'produtos.estoque DESC, id DESC';
}

if (!empty($GET['price']) && ($GET['price'] === 'asc' || $GET['price'] === 'desc')) {
  $Conditions['order'] = sprintf('produtos.preco_promo %s, id desc, produtos.estoque desc ', $GET['price']);
}

$Conditions['group'] = 'produtos.id';

$maximo = 5000;

$pag = !empty($GET['pag']) && $GET['pag'] > 0 ? (int)$GET['pag'] : 1;

$inicio = (($pag * $maximo) - $maximo);
$TotalProdutos = (int)count(ProdutosMenus::all($Conditions));

$ProdutosTotal = ceil($TotalProdutos / $maximo);

$Conditions['limit'] = $maximo;
$Conditions['offset'] = ($maximo * ($pag - 1));

/**
 * Produtos
 * @description Gerado uma camada no mysql com uma view
 * @bkp $Produtos = Produtos::all( $Conditions );
 */
$Produtos = ProdutosMenus::all($Conditions);
// echo '<pre>';
// print_r($Produtos);
// return;

/*
 * @return type numeric Retorna um numero de verificacao de tipo do produto
 * @link https://support.google.com/merchants/answer/188494?hl=pt-BR para maiores infomacoes
 *
 * 1604 ou "Vestuário e acessórios > Roupas"
 * 187 ou "Vestuário e acessórios > Sapatos"
 * 178 ou "Vestuário e acessórios > Acessórios para roupas > Óculos de sol"
 * 3032 ou "Vestuário e acessórios > Bolsas, carteiras e estojos > Bolsas"
 * 201 ou "Vestuário e acessórios > Joias > Relógios"
 * 784 ou "Mídia > Livros"
 * 839 ou "Mídia > DVDs e Vídeos"
 * 855 ou "Mídia > Música"
 * 1279 ou "Software > Software de videogame" (observação: esta categoria inclui todos os jogos para computador)
 */

function categoria_produto($str)
{
  // strcmp
  $_1604 = array('vestuário', 'acessórios', 'roupas');
  // * 187 ou "Vestuário e acessórios > Sapatos"
  // * 178 ou "Vestuário e acessórios > Acessórios para roupas > Óculos de sol"
  // * 3032 ou "Vestuário e acessórios > Bolsas, carteiras e estojos > Bolsas"
  // * 201 ou "Vestuário e acessórios > Joias > Relógios"
  // * 784 ou "Mídia > Livros"
  // * 839 ou "Mídia > DVDs e Vídeos"
  // * 855 ou "Mídia > Música"
  // * 1279 ou "Software > Software de videogame" (observação: esta categoria inclui todos os jogos para computador)
  return implode(', ', explode(' ', str_replace(' - ', ' ', $str)));
}

$xml = new DOMDocument('1.0');
$xml->formatOutput = true;
$rss = $xml->createElement('rss');

$version = $xml->createAttribute('version');
$version->value = '2.0';
$rss->appendChild($version);

$xmlns = $xml->createAttribute('xmlns:g');
$xmlns->value = 'http://base.google.com/ns/1.0';
$rss->appendChild($xmlns);

// // Personalizacao de quantos produtos há em geral
// $data_total_pag = $xml->createAttribute('data-total-pages');
// $data_total_pag->value = $ProdutosTotal;
// $rss->appendChild($data_total_pag);

// $data_total = $xml->createAttribute('data-total-products');
// $data_total->value = $TotalProdutos;
// $rss->appendChild($data_total);

$xml->appendChild($rss);

$channel = $xml->createElement('channel');
$rss->appendChild($channel);

$title = $xml->createElement('title', htmlspecialchars($CONFIG['nome_fantasia']));
$channel->appendChild($title);

$description = $xml->createElement('description', htmlspecialchars(implode(' - ', [$CONFIG['description'], $CONFIG['nome_fantasia'], ($CONFIG['cidade'] . '/' . $CONFIG['uf'])])));
$channel->appendChild($description);

$link = $xml->createElement('link', URL_BASE);
$channel->appendChild($link);

foreach ($Produtos as $r) {
  $item = $xml->createElement('item');
  $channel->appendChild($item);

  // $codigoProduto = !$r->codigo_produto ? CodProduto($r->nome_produto, $r->id) : $r->codigo_produto;
  $codigoProduto = CodProduto($r->nome_produto, $r->id);

  // $g_id = $xml->createElement('g:id', $r->id);
  $g_id = $xml->createElement('g:id', $codigoProduto);
  $item->appendChild($g_id);

  $cod_refer = $xml->createElement('g:cod_refer', 'COD: ' . CodProduto($r->nome_produto, $r->id, $r->codigo_produto));
  $item->appendChild($cod_refer);

  $title = $xml->createElement('g:title', trim($r->nome_produto));
  $item->appendChild($title);

  $description = $xml->createElement('g:description', htmlspecialchars($r->subnome_produto));
  $item->appendChild($description);

  $link = $xml->createElement('g:link', implode('/', [substr(URL_BASE, 0, -1), converter_texto($r->nome_produto), $r->id, 'p']));
  $item->appendChild($link);

  /**
   * verficar se a imagem existe
   */
  foreach ($r->produto->fotos as $img) {
    if (!file_exists(implode('/', ['/assets', $CONFIG['dominio'], 'produtos', 'smalls', $img->imagem]))) {
      $image_link = $xml->createElement(($img->capa == 1 ? 'g:image_link' : 'g:additional_image_link'), Imgs::src($img->imagem, 'large'));
      $item->appendChild($image_link);
    }
  }

  $categoria = $r->subgrupo ? tituloNomes($r->grupo) . ' &gt; ' . tituloNomes($r->subgrupo) : tituloNomes($r->grupo);
  $product_type = $xml->createElement('g:product_type', $categoria);
  $item->appendChild($product_type);

  $g_condition = $xml->createElement('g:condition', 'new');
  $item->appendChild($g_condition);

  $availability = $xml->createElement('g:availability', ($r->estoque > 0 ? 'in stock' : 'out of stock'));
  $item->appendChild($availability);

  if ($r->preco_promo > 0) {
    $g_price = $xml->createElement('g:price', number_format($r->preco_promo, 2, '.', '') . ' BRL');
    $item->appendChild($g_price);

    if (!empty($CONFIG['desconto_boleto'])) {
      $g_sale_price = $xml->createElement('g:sale_price', number_format(desconto_boleto($r->preco_promo, $CONFIG['desconto_boleto']), 2, '.', '') . ' BRL');
      $item->appendChild($g_sale_price);
    }

    $parcela = parcelamento($r->preco_promo, $CONFIG['qtde_parcelas'], $CONFIG['parcela_minima']);
    if ($parcela > 0) {
      $g_months = $xml->createElement('g:months', $parcela);
      $g_amount = $xml->createElement('g:amount', number_format(($r->preco_promo / $parcela), 2, '.', '') . ' BRL');
      $g_installment = $xml->createElement('g:installment');
      $g_installment->appendChild($g_months);
      $g_installment->appendChild($g_amount);
      $item->appendChild($g_installment);
    }
  }

  $brand = $xml->createElement('g:brand', $CONFIG['lojas']['dominio']);
  $item->appendChild($brand);

  if (!empty($r->nomecor)) {
    $color = $xml->createElement('g:color', $r->nomecor);
    $item->appendChild($color);
  }

  if (!empty($r->nometamanho)) {
    $size = $xml->createElement('g:size', $r->nometamanho);
    $item->appendChild($size);
  }

  $gtim = $xml->createElement('g:gtim');
  $item->appendChild($gtim);

  $google_product_category = $xml->createElement('g:google_product_category');
  $item->appendChild($google_product_category);
}
$xml_printf = $xml->saveXML($rss);

header('Content-type: text/xml');
echo $xml_printf;
// echo CompactarHtml( ob_get_clean() );
