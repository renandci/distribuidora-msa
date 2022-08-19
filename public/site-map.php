<?php
header('Content-type: text/xml');
header('Pragma: public');
header('Cache-control: private');
header('Expires: -1');

include '../app/settings.php';
include '../app/vendor/autoload.php';
include '../app/settings-config.php';
include '../app/includes/bibli-funcoes.php';

$xml = new DOMDocument('1.0');
$xml->formatOutput = true;
$rss = $xml->createElement('urlset');

$xmLns = $xml->createAttribute('xmlns');
$xmLns->value = 'https://www.sitemaps.org/schemas/sitemap/0.9';
$rss->appendChild($xmLns);

// $xmLns = $xml->createAttribute('xmlns:image');
// $xmLns->value = 'http://www.google.com/schemas/sitemap-image/1.1';
// $rss->appendChild($xmLns);

$url = $xml->createElement('url');
$rss->appendChild($url);

$loc = $xml->createElement('loc', URL_BASE);
$url->appendChild($loc);

$changeFreq = $xml->createElement('changefreq', 'always');
$url->appendChild($changeFreq);

$priority = $xml->createElement('priority', '1.0');
$url->appendChild($priority);

$sqlGrupo = ''
  . 'select sql_cache '
  . 'g.id, '
  . 'g.grupo, '
  . 'g.grupo_description, '
  . 'g.produto_id as id_produto '
  . 'from produtos_menus m '
  . 'left outer join grupos g on g.id = m.id_grupo '
  . 'left outer join produtos p on p.codigo_id = m.codigo_id '
  . 'where g.excluir = 0 and (p.excluir = 0 and p.status = 0) '
  . 'group by g.id '
  . 'order by g.ordem asc';

$respMenu = Lojas::connection()->query($sqlGrupo);
while ($menu = $respMenu->fetch()) {
  $url = $xml->createElement('url');
  $rss->appendChild($url);

  $loc = $xml->createElement('loc', URL_BASE . 'produtos/' . converter_texto($menu['grupo']) . '/' . $menu['id']);
  $url->appendChild($loc);

  $changeFreq = $xml->createElement('changefreq', 'always');
  $url->appendChild($changeFreq);

  $priority = $xml->createElement('priority', '0.9');
  $url->appendChild($priority);

  $sqlSubmenu = ''
    . 'select '
    . 'sub.id, '
    . 'sub.subgrupo, '
    . 'sub.subgrupo_description '
    . 'from subgrupos sub '
    . 'left outer join produtos_menus menus on menus.id_subgrupo = sub.id '
    . 'left outer join produtos p on p.codigo_id = menus.codigo_id '
    . sprintf('where (menus.id_grupo = %u and sub.excluir = 0 and (p.excluir = 0 and p.status = 0)) ', $menu['id'])
    . 'group by sub.id '
    . 'order by sub.subgrupo asc';
  $respSubMenus = Lojas::connection()->query($sqlSubmenu);

  while ($submenu = $respSubMenus->fetch()) {
    $url = $xml->createElement('url');
    $rss->appendChild($url);

    $loc = $xml->createElement('loc', URL_BASE . 'produtos/' . converter_texto($menu['grupo']) . '/' . $menu['id'] . '/' . converter_texto($submenu['subgrupo']) . '/' . $submenu['id']);

    $url->appendChild($loc);

    $changeFreq = $xml->createElement('changefreq', 'always');
    $url->appendChild($changeFreq);

    $priority = $xml->createElement('priority', '0.8');
    $url->appendChild($priority);
  }
}

$sqlProdutos = ''
  . 'select '
  . 'produtos.id, '
  . 'produtos.nome_produto, '
  . 'COUNT( pedidos_vendas.id_produto ) as prioridade, '
  . 'produtos.codigo_id, '
  . 'produtos.id_cor as cor_id, '
  . 'pedidos_vendas.quantidade, '
  . '((pedidos_vendas.quantidade)/COUNT(pedidos_vendas.id_produto)) as media '
  . 'from produtos '
  . 'left outer join pedidos_vendas on pedidos_vendas.id_produto = produtos.id '
  . 'where produtos.status = 0 and produtos.excluir = 0 and '
  . 'exists( select 1 from produtos_menus where produtos.codigo_id = produtos_menus.codigo_id ) '
  . 'group by produtos.id '
  . 'order by 3 desc, produtos.codigo_id desc';

$products = Lojas::connection()->query($sqlProdutos);
while ($r = $products->fetch()) {
  $url = $xml->createElement('url');
  $rss->appendChild($url);
  $loc = $xml->createElement('loc', URL_BASE . converter_texto($r['nome_produto']) . '/' . $r['id']);
  $url->appendChild($loc);
  $lastmod = $xml->createElement('lastmod', date('Y-m-d', $r['codigo_id']));
  $url->appendChild($lastmod);
  $changeFreq = $xml->createElement('changefreq', 'always');
  $url->appendChild($changeFreq);
  $priority = $xml->createElement('priority', '1.0');
  $url->appendChild($priority);

  // $sqlImages = sprintf('select imagem from produtos_imagens where codigo_id=%u and cor_id=%u order by capa desc', $r['codigo_id'], $r['cor_id']);
  // $productsImages = Lojas::connection()->query($sqlImages);
  // while ($img = $productsImages->fetch()) {
  //   $imageImage = $xml->createElement('image:image');
  //   $imageloc = $xml->createElement('imageloc', Imgs::src($img['imagem'], 'large'));
  //   $imageImage->appendChild($imageloc);
  //   $url->appendChild($imageImage);
  // };
}

echo $xml->saveXML($rss);
