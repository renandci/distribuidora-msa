<?php

use NFePHP\NFe\Make;
use NFePHP\NFe\Tools;
use NFePHP\Common\Certificate;
use NFePHP\NFe\Common\Standardize;

include_once dirname(__DIR__) . '/topo.php';

$id_usuario = $_SESSION['admin']['id_usuario'];

$id_nota = filter_input(INPUT_POST, 'id_nota', FILTER_SANITIZE_NUMBER_INT);

$nr_nota = filter_input(INPUT_POST, 'nrnfe', FILTER_SANITIZE_NUMBER_INT);

$id_pedido = filter_input(INPUT_POST, 'id_pedido', FILTER_SANITIZE_NUMBER_INT);

$id_emitente = filter_input(INPUT_POST, 'id_emitente', FILTER_SANITIZE_NUMBER_INT);

$porc_nota = filter_input(INPUT_POST, 'porc_nota', FILTER_SANITIZE_NUMBER_INT);

$mod_frete = filter_input(INPUT_POST, 'modFrete', FILTER_SANITIZE_NUMBER_INT);

$id_produtos = filter_input(INPUT_POST, 'id_produto', FILTER_DEFAULT, FILTER_FORCE_ARRAY);

$Pedidos = Pedidos::find($id_pedido);

$NfeEmitentes = NfeEmitentes::first(['conditions' => ['id=?', $id_emitente]]);

// Dados do cliente
$Cliente = $Pedidos->pedido_cliente;

// Dados do endereco do cliente
$Endereco = $Pedidos->pedido_endereco;

// $Pedidos = current( $Pedidoss );
// print_r($Pedidos);
// return;

// WARNING
$nfe_nrnota = $NfeEmitentes->nrnfe;
// $nfe_nrnota = (!empty($id_nota) && $id_nota > 0 ? substr($Pedidos->nfe_notas->chavenfe, -18, 8) : $NfeEmitentes->nrnfe);

// Testa e define se, caso o xml é uma correção de dados
// $nfe_nrnota = $nr_nota > $nfe_nrnota ? $nfe_nrnota : $nr_nota;


$nfe_ufemitente = $NfeEmitentes->cuf;
$nfe_aamm     = date('ym');
$nfe_cnpj     = soNumero($NfeEmitentes->cnpj);
$nfe_mod     = $NfeEmitentes->modelo;
$nfe_serie     = str_pad($NfeEmitentes->serie, 3, '0', STR_PAD_LEFT);
$nfe_num     = str_pad($nfe_nrnota, 9, '0', STR_PAD_LEFT);
$nfe_cn     = substr(str_pad($Pedidos->data_venda->format('dmYHs'), 8, '0', STR_PAD_LEFT), -8);
$nfe_natop     = filter_input(INPUT_POST, 'natOp', FILTER_SANITIZE_STRING);
$nfe_tpemis   = '1';
$nfe_dhemi     = str_replace(' ', 'T', date('Y-m-d H:i:sP'));

try {
  // Instancia da Classe
  $nfe = new Make();
  // A - Dados da Nota Fiscal eletrônica
  $std = new \stdClass();
  $std->versao = $NfeEmitentes->versao;
  // $std->Id = $chave_new;
  $std->Id = null;
  $std->pk_nItem = null;
  $nfe->taginfNFe($std);

  // B - Identificação da Nota Fiscal eletrônica
  $std = new \stdClass();

  // Código numérico que compõe a Chave de Acesso
  $std->cNF = $nfe_cn * 1;

  // Descrição da Natureza da Operação
  // Informar a natureza da operação de que decorrer a saída ou a entrada, tais como: venda, compra, transferência, etc...
  $std->natOp = $nfe_natop;

  // Tipo de Operação - 0-entrada / 1-saída int 1-1
  $tpNF = strstr($nfe_natop, ' ', true);
  $std->tpNF = $tpNF == 'Devolucao' ? 0 : 1;

  // Código do Modelo do Documento Fiscal
  $std->mod = $NfeEmitentes->modelo;

  // Série do Documento Fiscal int 1-3
  $std->serie = $NfeEmitentes->serie;

  // Número do Documento Fiscal int 1-9
  $std->nNF = $nfe_nrnota * 1;

  // Data de emissão do Documento Fiscal
  $std->dhEmi = $nfe_dhemi;

  // Data de Saída ou da Entrada da Mercadoria/Produto
  $std->dhSaiEnt = '';

  // Código de município precisa ser válido - int 7
  $std->cMunFG = $NfeEmitentes->cmunfg;

  // $std->NFref = ''; // Grupo de informação das NF/NF-e referenciadas

  // Código da UF do emitente do Documento Fiscal - coloque um código real e válido
  $std->cUF = $NfeEmitentes->cuf;
  $std->idDest = $Endereco->uf == 'SP' ? 1 : 2;
  $std->tpImp = 1;
  $std->tpEmis = 1;
  $std->cDV = null;

  // Se deixar o tpAmb como 2 você emitirá a nota em ambiente de homologação(teste) e as notas fiscais aqui não tem valor fiscal
  $std->tpAmb = (int)$NfeEmitentes->tpamb;

  $std->finNFe = ($tpNF == 'Devolucao' ? 4 : 1);

  $std->indFinal = 1;
  $std->indPres = 0;
  $std->procEmi = 0;
  $std->verProc = 'Data Control 1.00';

  $nfe->tagide($std);

  // refNFe - Utilizar esta TAG para referenciar uma Nota Fiscal Eletrônica emitida anteriormente, vinculada a NF-e atual.
  $chavenfe_ref = null;
  if ($tpNF == 'Devolucao') {
    $std = new stdClass();
    $chavenfe_ref = !empty($Pedidos->nfe_notas->chavenfe_ref) ? $Pedidos->nfe_notas->chavenfe_ref : $Pedidos->nfe_notas->chavenfe;
    $std->refNFe = $chavenfe_ref;
    $nfe->tagrefNFe($std);
  }

  // C - Identificação do Emitente da Nota Fiscal eletrônica
  $std = new \stdClass();
  $std->xNome = converter_texto($NfeEmitentes->razaosocial, ' ', 'strtoupper');
  $std->CNPJ = soNumero($NfeEmitentes->cnpj);
  $std->IE = soNumero($NfeEmitentes->inscest);
  $std->CRT = (int)$NfeEmitentes->crt;
  $nfe->tagemit($std);

  // Remetente
  $std = new \stdClass();
  $std->xLgr = converter_texto($NfeEmitentes->endereco, ' ');
  $std->nro = $NfeEmitentes->nro;
  $std->xBairro = converter_texto($NfeEmitentes->bairro, ' ');
  //Código de município precisa ser válido e igual o  cMunFG
  $std->cMun = $NfeEmitentes->cmunfg;
  $std->xMun = converter_texto($NfeEmitentes->municipio, ' ');
  $std->UF = $NfeEmitentes->uf;
  $std->CEP = soNumero($NfeEmitentes->cep);
  $std->cPais = '1058';
  $std->xPais = 'BRASIL';
  $nfe->tagenderEmit($std);

  // E - Identificação do Destinatário da Nota Fiscal eletrônica
  $std = new stdClass();
  $std->xNome = converter_texto($Cliente->nome, ' ');
  $std->indIEDest = '9';
  $std->IE = '';
  $std->ISUF = '';
  $std->IM = '';
  $std->email = $Cliente->email;
  // indicar apenas um CNPJ ou CPF ou idEstrangeiro
  $CNPJ = soNumero($Cliente->cpfcnpj);
  $std->CNPJ = strlen($CNPJ) > 11 ? $CNPJ : '';
  $std->CPF = strlen($CNPJ) <= 11 ? $CNPJ : '';
  $std->idEstrangeiro = '';
  $nfe->tagdest($std);

  // Destinatário
  $std = new \stdClass();
  $std->xLgr = converter_texto($Endereco->endereco, ' ');
  $std->nro = $Endereco->numero;
  $std->xBairro = converter_texto($Endereco->bairro, ' ');
  $std->cMun = $Endereco->cod_ibge->cod_ibge;
  $std->xMun = converter_texto($Endereco->cidade, ' ');
  $std->UF = $Endereco->uf;
  $std->CEP = soNumero($Endereco->cep);
  $std->cPais = '1058';
  $std->xPais = 'BRASIL';
  $nfe->tagenderDest($std);

  $ProdutosKits = null;
  // percorre um novo loop
  echo '<div id="emitir_nfe_xml">';
  print_r($id_produtos);

  $NewPedidos = [];
  foreach ($Pedidos->pedidos_vendas as $a => $rs) {

    // Loop para devolução de mercadorias
    if ($tpNF == 'Devolucao') // Se for devolução
      if (!in_array($rs->produto->id, $id_produtos)) continue; // dentro do array se não existir, continue

    echo 'Continue...';

    $NewPedidos[$a]['prod_id'] = $rs->produto->id;
    $NewPedidos[$a]['codigo_id'] = $rs->produto->codigo_id;
    $NewPedidos[$a]['prod_cod'] = $rs->produto->codigo_produto;
    $NewPedidos[$a]['prod_nome'] = $rs->produto->nome_produto;
    $NewPedidos[$a]['prod_csosn'] = $rs->produto->csosn;
    $NewPedidos[$a]['prod_unid'] = $rs->produto->unid;
    $NewPedidos[$a]['prod_cest'] = $rs->produto->cest;
    $NewPedidos[$a]['prod_cfop'] = $rs->produto->cfop;
    $NewPedidos[$a]['prod_ncm'] = $rs->produto->ncm;
    $NewPedidos[$a]['prod_cst'] = $rs->produto->cst;
    $NewPedidos[$a]['prod_cor'] = $rs->produto->cor->nomecor;
    $NewPedidos[$a]['prod_tam'] = $rs->produto->tamanho->nometamanho;
    $NewPedidos[$a]['prod_price'] = $rs->valor_pago;
    $NewPedidos[$a]['prod_qtde'] = $rs->quantidade;
    $NewPedidos[$a]['not_kit'] = '0';

    if (!empty($rs->produto->grid_kits)) {
      unset($NewPedidos[$a]);
      foreach ($rs->produto->grid_kits as $b => $pr) {
        $b++;
        $NewPedidos[$b]['prod_id'] = $pr->produto->id;
        $NewPedidos[$b]['codigo_id'] = $pr->produto->codigo_id;
        $NewPedidos[$b]['prod_cod'] = $pr->produto->codigo_produto;
        $NewPedidos[$b]['prod_nome'] = $pr->produto->nome_produto;
        $NewPedidos[$b]['prod_csosn'] = $pr->produto->csosn;
        $NewPedidos[$b]['prod_unid'] = $pr->produto->unid;
        $NewPedidos[$b]['prod_cest'] = $pr->produto->cest;
        $NewPedidos[$b]['prod_cfop'] = $pr->produto->cfop;
        $NewPedidos[$b]['prod_ncm'] = $pr->produto->ncm;
        $NewPedidos[$b]['prod_cst'] = $pr->produto->cst;
        $NewPedidos[$b]['prod_cor'] = $pr->produto->cor->nomecor;
        $NewPedidos[$b]['prod_tam'] = $pr->produto->tamanho->nometamanho;
        $NewPedidos[$b]['prod_price'] = $pr->produto->preco_promo;
        $NewPedidos[$b]['prod_qtde'] = $rs->quantidade;
        $NewPedidos[$b]['not_kit'] = '1';

        $TotalItensKit += number_format($pr->produto->preco_promo * $rs->quantidade, 2, '.', '');
        $TotalItens += $rs->quantidade;
      }
      $TotalKit += number_format($rs->valor_pago * $rs->quantidade, 2, '.', '');
    }
  }
  // return;
  // Venda Venda / Valor Venda - DescBoleto
  if ($TotalDescBol > 0)
    $TotalDescBol = (($TotalVenda - ($TotalDescBol / 100) * $TotalVenda) / $TotalVenda);

  // Venda Venda / Valor Venda - TotalDescCupom
  // if($TotalDescCupom > 0)
  // 	$TotalDescCupom = (($TotalVenda - $TotalDescCupom) / $TotalVenda);

  if (($TotalItensKit != $TotalKit) && $TotalItensKit > 0)
    $TotalItensKitDesc = ($TotalKit / $TotalItensKit);


  // for loop
  $item = 1;
  $item2 = 0;
  $vDesc = 0;
  $vProd = 0;
  $vFrete = 0;
  $vUnCom = 0;
  $vlTotal = 0;
  $vTotDesc = 0;
  $vTotTrib = 0;
  $vTotProd = 0;
  $vTotTribSum = 0;
  $TotProdLoop = count($NewPedidos);
  $SomaTotalFrete = 0;

  $TotalKit = 0;
  $TotalItens = 0;
  $TotalItensKit = 0;
  $TotalItensDesc = 0;
  $TotalItensKitDesc = 0;
  $TotalFrete = $Pedidos->frete_valor;
  $TotalVenda = $Pedidos->valor_compra;

  $TotalDescBol = $Pedidos->desconto_boleto > 0 ? ($Pedidos->desconto_boleto / 100) : 0;
  $TotalDescCupom = $Pedidos->desconto_cupom / $Pedidos->valor_compra;
  $TotalDescNfe = $Pedidos->porc_nota;

  foreach ($NewPedidos as $rws) {
    $ncm = NfeNcm::connection()->query(sprintf('select distinct nfe_ncm.ncm as ncm_padrao, nfe_ncm.aliqnac as ncm_aliqnac from nfe_ncm where nfe_ncm.ncm = "%s" limit 1', $rws['prod_ncm']))->fetch();
    if (empty($ncm['ncm_padrao'])) {
      Produtos::new_save(['id' => $rws['prod_id'], 'ncm' => '']);
      header(sprintf('location: /adm/nfe/nfe.php?id_pedido=%u&error=NCM %s inválido [item %s]: %s!', $id_pedido, $rws['prod_ncm'], $item, $rws['prod_nome']));
      return;
    }

    // Desconto do cupom, valor em real
    if ($TotalDescCupom > 0)
      $rws['prod_price'] = number_format($rws['prod_price'] - ($rws['prod_price'] * $TotalDescCupom), 2, '.', '');

    // Somatorio do Boleto ou Transferencia
    if ($TotalDescBol > 0)
      $rws['prod_price'] = number_format(($rws['prod_price'] - $TotalDescBol * $rws['prod_price']), 2, '.', '');

    // Desconto de kits
    if ($TotalItensKitDesc > 0 && !empty($rws['not_kit']))
      $rws['prod_price'] = number_format(($rws['prod_price'] - $TotalItensKitDesc * $rws['prod_price']), 2, '.', '');

    // Desconto da porcetagem da Nfe
    $vUnCom = $rws['prod_price'];
    if ($TotalDescNfe > 0 && $TotalDescNfe != 100) {
      $vUnCom = number_format(($rws['prod_price'] - ($TotalDescNfe / 100) * $rws['prod_price']), 2, '.', '');
    }

    // valor total bruto
    $vProd = (number_format($vUnCom, 2, '.', '') * $rws['prod_qtde']);

    // valor do frete
    $vFrete = $mod_frete == 1 ? $TotalFrete : 0.00;

    // valor unitário do desconto
    $vDesc = $rws['prod_price'] - $vUnCom;

    $std = new stdClass();
    // H - Detalhamento de Produtos e Serviços da NF-e
    // item da NFe
    $std->item = $item;

    // I - Produtos e Serviços da NF-e
    $std->cProd = CodProduto($rws['prod_nome'], $rws['prod_id']);
    $std->cEAN = 'SEM GTIN';
    $std->xProd = converter_texto($rws['prod_nome'], ' ', 'strtoupper');
    $std->NCM = $ncm['ncm_padrao'];
    $std->CEST = $rws['prod_cest'];

    $cfop = null;
    if ($tpNF != 'Devolucao' && $Endereco->uf == 'SP' && $rws['prod_cfop'] == 5101) {
      $cfop = 5101;
    } else if ($tpNF != 'Devolucao' && $Endereco->uf != 'SP' && $rws['prod_cfop'] == 5101) {
      $cfop = 6101;
    } else if ($tpNF != 'Devolucao' && $Endereco->uf == 'SP' && $rws['prod_cfop'] == 5102) {
      $cfop = 5102;
    } else if ($tpNF != 'Devolucao' && $Endereco->uf != 'SP' && $rws['prod_cfop'] == 5102) {
      $cfop = 6102;
    }
    // Para Devolucao
    else if ($tpNF == 'Devolucao' && $Endereco->uf == 'SP' && $rws['prod_cfop'] == 5101) {
      $cfop = 1202;
    } else if ($tpNF == 'Devolucao' && $Endereco->uf != 'SP' && $rws['prod_cfop'] == 5101) {
      $cfop = 2202;
    } else if ($tpNF == 'Devolucao' && $Endereco->uf == 'SP' && $rws['prod_cfop'] == 5102) {
      $cfop = 1202;
    } else if ($tpNF == 'Devolucao' && $Endereco->uf != 'SP' && $rws['prod_cfop'] == 5102) {
      $cfop = 2202;
    }

    $std->CFOP = $cfop;
    $std->uCom = $rws['prod_unid'];
    $std->qCom = $rws['prod_qtde'];
    $std->vUnCom = number_format($vUnCom, 2, '.', '');
    $std->vProd = number_format($vProd, 2, '.', '');
    $std->cEANTrib = 'SEM GTIN';
    $std->qTrib = $rws['prod_qtde'];
    $std->uTrib = $rws['prod_unid'];
    $std->vUnTrib = number_format($vUnCom, 2, '.', '');
    $std->vDesc = $vDesc > 0 ? number_format($vDesc, 2, '.', '') : null;
    $std->vFrete = $vFrete > 0 ? number_format(($vFrete / $TotProdLoop), 2, '.', '') : null;
    // $std->vSeg = '';
    // $std->vOutro = '';
    // $std->nFCI = '';
    $std->indTot = '1';
    $std->xPed = $rws['codigo'];
    $std->nItemPed = $rws['prod_qtde'];
    $nfe->tagprod($std);

    $vTotTrib = ($vProd * ($ncm['ncm_aliqnac'] / 100));

    // $std = new stdClass();
    // //item da NFe
    // $std->item = $item;
    // $std->CEST = 2805700;
    // $nfe->tagCEST($std);

    $std = new \stdClass();
    $std->item = $item;
    $std->vTotTrib = number_format($vTotTrib, 2, '.', '');
    $nfe->tagimposto($std);

    // N - ICMS Normal e ST
    $std = new stdClass();
    $std->item = $item; //item da NFe
    $std->orig = 0;
    $std->CSOSN = $rws['prod_csosn'];
    // $std->pCredSN = null;
    // $std->vCredICMSSN = null;
    // $std->modBCST = null;
    // $std->pMVAST = null;
    // $std->pRedBCST = null;
    // $std->vBCST = null;
    // $std->pICMSST = null;
    // $std->vICMSST = null;
    // $std->vBCFCPST = null; //incluso no layout 4.00
    // $std->pFCPST = null; //incluso no layout 4.00
    // $std->vFCPST = null; //incluso no layout 4.00
    // $std->vBCSTRet = null;
    // $std->pST = null;
    // $std->vICMSSTRet = null;
    // $std->vBCFCPSTRet = null; //incluso no layout 4.00
    // $std->pFCPSTRet = null; //incluso no layout 4.00
    // $std->vFCPSTRet = null; //incluso no layout 4.00
    // $std->modBC = null;
    // $std->vBC = null;
    // $std->pRedBC = null;
    // $std->pICMS = null;
    // $std->vICMS = null;
    // $std->pRedBCEfet = null;
    // $std->vBCEfet = null;
    // $std->pICMSEfet = null;
    // $std->vICMSEfet = null;
    // $std->vICMSSubstituto = null;
    $nfe->tagICMSSN($std);

    $std = new \stdClass();
    $std->item = $item;
    $std->CST = '07';
    // $std->vBC = null;
    // $std->pPIS = null;
    // $std->vPIS = null;
    // $std->qBCProd = null;
    // $std->vAliqProd = null;
    $nfe->tagPIS($std);

    // $std = new \stdClass();
    // $std->item = $item;
    // $std->cEnq = null;
    // $std->CST = null;
    // $std->vIPI = null;
    // $std->vBC = null;
    // $std->pIPI = null;
    // $nfe->tagIPI($std);

    // $std = new \stdClass();
    // $std->item = $item;
    // $std->vCOFINS = null;
    // $std->vBC = null;
    // $std->pCOFINS = null;
    // $nfe->tagCOFINSST($std);

    $std = new \stdClass();
    $std->item = $item;
    $std->CST = '07';
    // $std->vBC = null;
    // $std->pCOFINS = null;
    // $std->vCOFINS = null;
    // $std->qBCProd = null;
    // $std->vAliqProd = null;
    $nfe->tagCOFINS($std);

    $vTotProd += $vProd;
    $vTotDesc += $vDesc;
    $vTotTribSum += $vTotTrib;

    $item++;
  }

  $vTotDesc = number_format($vTotDesc, 2, '.', '');

  $vTotProd = number_format($vTotProd, 2, '.', '');

  $vFrete = number_format($vFrete, 2, '.', '');

  $vNF = number_format((($vTotProd - $vTotDesc) + $vFrete), 2, '.', '');

  $vTotTribSum = number_format($vTotTribSum, 2, '.', '');

  $std = new \stdClass();
  $std->vBC = 0.00;
  $std->vICMS = 0.00;
  $std->vICMSDeson = 0.00;
  $std->vBCST = 0.00;
  $std->vST = 0.00;
  $std->vProd = $vTotProd;
  $std->vFrete = $vFrete;
  $std->vSeg = 0.00;
  $std->vDesc = $vTotDesc;
  $std->vII = 0.00;
  $std->vIPI = 0.00;
  $std->vPIS = 0.00;
  $std->vCOFINS = 0.00;
  $std->vOutro = 0.00;
  $std->vNF = $vNF;
  $std->vTotTrib = $vTotTribSum;
  $nfe->tagICMSTot($std);

  $std = new \stdClass();
  $std->modFrete = $mod_frete;
  $nfe->tagtransp($std);

  // $std = new \stdClass();
  // $std->item = 1;
  // $std->qVol = 1;
  // $std->esp = 'caixa';
  // $std->marca = 'TESTE';
  // $std->pesoL = 10.00;
  // $std->pesoB = 11.00;
  // $nfe->tagvol($std);

  // // Y – Dados da Cobrança
  // $std = new \stdClass();
  // $std->nFat = $nfe_nrnota;
  // $std->vOrig = number_format(($vTotProd + $vFrete), 2, '.', '');
  // $std->vDesc = $vTotDesc;
  // $std->vLiq = $vNF;
  // $nfe->tagfat($std);

  // $std = new \stdClass();
  // $std->nDup = '001';
  // $std->dVenc = date('Y-m-d', strtotime('+0 month'));
  // $std->vDup = $vNF;
  // $nfe->tagdup($std);

  $std = new \stdClass();
  $std->vTroco = null;
  $nfe->tagpag($std);

  $std = new \stdClass();
  $std->indPag = '0';
  $std->tPag = $tpNF == 'Devolucao' ? '90' : '01';
  $std->vPag = $tpNF == 'Devolucao' ? '0.00' : $vNF;
  $nfe->tagdetPag($std);

  // $std = new \stdClass();
  // $std->xCampo = $Cliente->nome;
  // $std->xTexto = $Cliente->email;
  // $nfe->tagobsCont($std);

  // $std = new \stdClass();
  // $std->xCampo = 'Info';
  // $std->xTexto = 'Nota de Homologação';
  // $nfe->tagobsFisco($std);

  // echo '<pre>';
  // print_r($nfe);
  // echo '</pre>';

  $resp = $nfe->montaNFe();
  $xml = $nfe->getXML();
  $chave = $nfe->getChave();

  if (count($nfe->errors) > 0) {
    foreach ($nfe->errors as $err) {
      throw new Exception($err);
    }
  }

  $Certificate = file_get_contents(sprintf('%sassets/%s/pfx/pfx-%u.pfx', PATH_ROOT, ASSETS, $id_emitente));

  // O conteúdo do XML assinado fica armazenado na variável $xml
  $Tools = new Tools($NfeEmitentes->jsonnfe(), Certificate::readPfx($Certificate, $NfeEmitentes->senha));
  $SignNFe = $Tools->signNFe($xml);

  // Diretorio principal
  $dir = sprintf('%sassets/%s/xml/', PATH_ROOT, ASSETS);
  $filename = sprintf('%s%s.xml', $dir, $chave);
  $filename_assinado = sprintf('%s%s-assinada.xml', $dir, $chave);
  $filename_protocolo = sprintf('%s%s-protocolo.xml', $dir, $chave);
  $filename_autorizada = sprintf('%s%s-autorizada.xml', $dir, $chave);

  // Cria o diretorio
  if (!is_dir($dir)) {
    if (!mkdir($dir, 0775, true)) {
      throw new Exception("Não foi possível criar o diretório principal do xml", 1);
    }
  }

  // Caso seja uma correcao
  if ($id_nota > 0 && $tpNF != 'Devolucao') {

    $chaveold = $Pedidos->nfe_notas->chavenfe;

    $filenameold = sprintf('%s%s.xml', $dir, $chaveold);
    $filenameold_assinado = sprintf('%s%s-assinada.xml', $dir, $chaveold);
    $filenameold_protocolo = sprintf('%s%s-protocolo.xml', $dir, $chaveold);
    $filenameold_autorizada = sprintf('%s%s-autorizada.xml', $dir, $chaveold);

    @rename($filenameold, $filename);
    @rename($filenameold_assinado, $filename_assinado);
    @rename($filenameold_protocolo, $filename_protocolo);
    @rename($filenameold_autorizada, $filename_autorizada);
  }

  // Gerar o XML da NFe
  file_put_contents($filename, $xml);
  file_put_contents($filename_assinado, $SignNFe);

  chmod($filename, 0775);
  chmod($filename_assinado, 0775);

  // $std irá conter uma representação em stdClass do XML retornado
  $stdCl = new Standardize($SignNFe);
  $std = $stdCl->toStd();

  $dhemi = $std->infNFe->ide->dhEmi;
  $nrnfe = $tpNF == 'Devolucao' ? $std->infNFe->ide->nNF : $std->infNFe->ide->nNF + 1;

  // Gerar o contador do Nf-e
  NfeEmitentes::new_save(['id' => $id_emitente, 'nrnfe' => $nrnfe]);

  $parent_id = 0;
  if ($id_nota > 0 && $tpNF == 'Devolucao') {
    $parent_id = (int)$id_nota;
    $id_nota = null;
  }

  // Salva a nota do db
  $NfeNotas = NfeNotas::new_save([
    'id' => $id_nota ?? null,
    'parent_id' => $parent_id,
    'id_usuario' => $id_usuario,
    'id_pedido' => $id_pedido,
    'id_emitentes' => $NfeEmitentes->id,
    'chavenfe' => $chave,
    'dhemi' => $dhemi,
    'motivo' => null,
    'status' => $tpNF == 'Devolucao' ? 3 : 1
  ]);
  $id_nota = $NfeNotas['id'] ?? 0;

  // Salva alguns log
  PedidosLogs::logs($id_pedido, $_SESSION['admin']['id_usuario'], 'Xml gerado e assinado com sucesso');
  header('location: /adm/nfe/nfe.php?id_pedido=' . $id_pedido . '&id_emitente=' . $id_emitente . '&id_nota=' . $id_nota . '&error=Xml gerado e assinado com sucesso');
  return;
} catch (\RuntimeException $a) {
  PedidosLogs::logs($id_pedido, $_SESSION['admin']['id_usuario'], $a->getMessage());
  header('location: /adm/nfe/nfe.php?id_pedido=' . $id_pedido . '&id_emitente=' . $id_emitente . '&error=' . $a->getMessage() . '&id_nota=' . $id_nota);
  return;
} catch (\Exception $e) {
  PedidosLogs::logs($id_pedido, $_SESSION['admin']['id_usuario'], $e->getMessage());
  header('location: /adm/nfe/nfe.php?id_pedido=' . $id_pedido . '&id_emitente=' . $id_emitente . '&error=Error&id_nota=' . $id_nota);
  return;
}

include_once dirname(__DIR__) . '/rodape.php';
