<?php
/*
[CFOP] I01 <prod> - [item 1] Código Fiscal de Operações e Prestações,Preenchimento Obrigatório!
[uCom] I01 <prod> - [item 1] Unidade Comercial do produto,Preenchimento Obrigatório!
[uTrib] I01 <prod> - [item 1] Unidade Tributável do produto"
*/
use NFePHP\NFe\Make;
use NFePHP\NFe\Tools;
use NFePHP\Common\Certificate;
use NFePHP\NFe\Common\Standardize;

defined('PATH_ROOT') || define('PATH_ROOT', realpath($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR);
require_once PATH_ROOT . '/app/settings.php';
require_once PATH_ROOT . '/app/vendor/autoload.php';
require_once PATH_ROOT . '/app/settings-config.php';
require_once PATH_ROOT . '/assets/' . ASSETS .  '/settings.php';
require_once PATH_ROOT . '/app/includes/bibli-funcoes.php';
require_once PATH_ROOT . '/app/includes/ajax-emails.php';

header('Content-Type: application/json');

$id_usuario = $_SESSION['admin']['id_usuario'];
$id_emitente = filter_input(INPUT_POST, 'emitente', FILTER_SANITIZE_NUMBER_INT);
// $cliente = filter_input(INPUT_POST, 'cliente', FILTER_SANITIZE_STRING);
// $endereco = filter_input(INPUT_POST, 'endereco', FILTER_SANITIZE_STRING);

$id_pedido = 0;
$mod_frete = 1;

// echo json_encode($cliente);
// die();

$NfeEmitentes = NfeEmitentes::first(['conditions' => ['id=?', $id_emitente]]);

$Endereco = (object)$_POST['endereco'];
$ListaProdutos = (object)$_POST['produtos'];

$nfe_nrnota = $NfeEmitentes->nrnfe;

$nfe_ufemitente = $NfeEmitentes->cuf;
$nfe_aamm 		= date('ym');
$nfe_cnpj 		= soNumero($NfeEmitentes->cnpj);
$nfe_mod 		= $NfeEmitentes->modelo;
$nfe_serie 		= str_pad($NfeEmitentes->serie, 3, '0', STR_PAD_LEFT);
$nfe_num 		= str_pad($nfe_nrnota, 9, '0', STR_PAD_LEFT);
$nfe_cn 		= substr(str_pad( date("dmYHs", strtotime($_POST['data_venda'])), 8, '0', STR_PAD_LEFT), -8);
$nfe_natop 		= "VENDA";
$nfe_tpemis 	= '1';
$nfe_dhemi 		= str_replace(' ', 'T', date('Y-m-d H:i:sP'));

try {
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
		
		// Tipo de Operação - 0-entrada / 1-saída int 1-1
		$std->tpNF = 1; 
		
		// Código de município precisa ser válido - int 7
		$std->cMunFG = $NfeEmitentes->cmunfg; 
		$std->cUF = $NfeEmitentes->cuf; 
		$std->idDest = $_POST['cliente_uf'] == 'SP' ? 1 : 2;
		$std->tpImp = 1;
		$std->tpEmis = 1;
		$std->cDV = $chave_new_dv;
		// $std->cDV = null;
		// Se deixar o tpAmb como 2 você emitirá a nota em ambiente de homologação(teste) e as notas fiscais aqui não tem valor fiscal
		$std->tpAmb = (int)$NfeEmitentes->tpamb; 
		$std->finNFe = 1;
		$std->indFinal = 1;
		$std->indPres = 0;
		$std->procEmi = 0;
		$std->verProc = 2;
	$nfe->tagide($std);

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
		$std->xNome = converter_texto($_POST['cliente_nome'], ' ');
		$std->indIEDest = '9';
		$std->IE = '';
		$std->ISUF = '';
		$std->IM = '';
		$std->email = $_POST['cliente_email'];
		// indicar apenas um CNPJ ou CPF ou idEstrangeiro
		$CNPJ = soNumero($_POST['cliente_cpf']);
		$std->CNPJ = strlen($CNPJ) > 11 ? $CNPJ: '';
		$std->CPF = strlen($CNPJ) <= 11 ? $CNPJ: '';
		$std->idEstrangeiro = '';
	$nfe->tagdest($std);

	// Destinatário
		$std = new \stdClass();
		$std->xLgr = converter_texto($Endereco->street, ' ');
		$std->nro = $Endereco->number;
		$std->xBairro = converter_texto($Endereco->neighborhood, ' ');
		// $std->cMun = $Endereco->cod_ibge->cod_ibge;
		$std->cMun = $_POST['cod_ibge'];

		$std->xMun = converter_texto($Endereco->city, ' ');
		$std->UF = $_POST['cliente_uf'];
		$std->CEP = soNumero($Endereco->postcode);
		$std->cPais = '1058';
		$std->xPais = 'BRASIL';
	$nfe->tagenderDest($std);

	$ProdutosKits = null;
	// percorre um novo loop
	$NewPedidos = [];
	
	foreach($ListaProdutos as $a => $rs)
	{
		$rs = (object)$rs;
		$NewPedidos[$a]['prod_id'] = $rs->prod_id;
		$NewPedidos[$a]['codigo_id'] = $rs->codigo_id;
		$NewPedidos[$a]['prod_cod'] = $rs->prod_cod;
		$NewPedidos[$a]['prod_nome'] = $rs->prod_nome;
		$NewPedidos[$a]['prod_csosn'] = $rs->prod_csosn;
		$NewPedidos[$a]['prod_unid'] = $rs->prod_unid;
		$NewPedidos[$a]['prod_cest'] = $rs->prod_cest;
		$NewPedidos[$a]['prod_cfop'] = $rs->prod_cfop;
		$NewPedidos[$a]['prod_ncm'] = $rs->prod_ncm;
		$NewPedidos[$a]['prod_cst'] = $rs->prod_cst;
		$NewPedidos[$a]['prod_cor'] = $rs->prod_cor;
		$NewPedidos[$a]['prod_tam'] = $rs->prod_tam;
		$NewPedidos[$a]['prod_price'] = $rs->prod_price;
		$NewPedidos[$a]['prod_unitario'] = $rs->prod_unitario;
		$NewPedidos[$a]['prod_desc'] = $rs->prod_desc;
		$NewPedidos[$a]['prod_qtde'] = $rs->prod_qtde;
	}

	// for loop
	$item = 1;
	$vDesc = 0;
	$vProd = 0;
	$vFrete = 0;
	$vUnCom = 0;
	$vTotDesc = 0;
	$vTotTrib = 0;
	$vTotProd = 0; 
	$vTotTribSum = 0;
	$TotProdLoop = count( $NewPedidos ); 
	$TotalFrete = $_POST['valor_frete'];
	
	foreach ( $NewPedidos as $rws ) 
	{
		$ncm = NfeNcm::connection()->query(sprintf('select distinct nfe_ncm.ncm as ncm_padrao, nfe_ncm.aliqnac as ncm_aliqnac from nfe_ncm where nfe_ncm.ncm = "%s" limit 1', $rws['prod_ncm']))->fetch();
		if( empty( $ncm['ncm_padrao'] ) ) {
			Produtos::new_save(['id' => $rws['prod_id'], 'ncm' => '']);
			throw new Exception("Produtos sem dados fiscais");
			// throw new Exception(sprintf('%s - select distinct nfe_ncm.ncm as ncm_padrao, nfe_ncm.aliqnac as ncm_aliqnac from nfe_ncm where nfe_ncm.ncm = "%s" limit 1', $rws['prod_id'], $rws['prod_ncm']));
		}
		
		$vUnCom = $rws['prod_unitario'];

		// valor total bruto
		$vProd = (number_format($vUnCom, 2, '.', '') * $rws['prod_qtde']);
		
		$vFrete = $mod_frete == 1 ? $TotalFrete : 0.00;
		
		// valor unitário do desconto
		$vDesc = $rws['prod_desc'];
				
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
			if($_POST['cliente_uf'] == 'SP' && $rws['prod_cfop'] == 5101) {
				$cfop = 5101;
			} 
			else if($_POST['cliente_uf'] != 'SP' && $rws['prod_cfop'] == 5101) {
				$cfop = 6101;
			} 
			else if($_POST['cliente_uf'] == 'SP' && $rws['prod_cfop'] == 5102) {
				$cfop = 5102;
			} 
			else if($_POST['cliente_uf'] != 'SP' && $rws['prod_cfop'] == 5102) {
				$cfop = 6102;
			}

			// incluido no layout 4.00
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
			$std->indTot = '1';
			$std->xPed = $rws['codigo'];
			$std->nItemPed = $rws['prod_qtde'];
		$nfe->tagprod($std);

		$vTotTrib = ($vProd * ($ncm['ncm_aliqnac'] / 100));

		$std = new \stdClass();
			$std->item = $item;
			$std->vTotTrib = number_format($vTotTrib, 2, '.', '');
		$nfe->tagimposto($std);
		
		// N - ICMS Normal e ST
		$std = new stdClass();
			$std->item = $item; //item da NFe
			$std->orig = 0;
			$std->CSOSN = $rws['prod_csosn'];
		$nfe->tagICMSSN($std);
		
		$std = new \stdClass();
			$std->item = $item;
			$std->CST = '07';
		$nfe->tagPIS($std);

		$std = new \stdClass();
			$std->item = $item;
			$std->CST = '07';
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

		$std = new \stdClass();
		$std->vTroco = null;
	$nfe->tagpag($std);

		$std = new \stdClass();
		$std->indPag = '0';
		$std->tPag = '01';
		$std->vPag = $vNF;
	$nfe->tagdetPag($std);
	
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
	if( ! is_dir( $dir ) ) {
		if( ! mkdir($dir, 0775, true) ) {
			throw new Exception("Não foi possível criar o diretório principal do xml", 1);
		}
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
	$nrnfe = ($std->infNFe->ide->nNF + 1);

	// Gerar o contador do Nf-e
	@NfeEmitentes::new_save(['id' => $id_emitente, 'nrnfe' => $nrnfe]);

	// Salva a nota do db
	$NfeNotas = @NfeNotas::new_save([
		'id' => null, 
		'id_usuario' => $id_usuario, 
		'id_pedido' => $id_pedido,
		'id_emitentes' => $NfeEmitentes->id, 
		'chavenfe' => $chave, 
		'dhemi' => $dhemi, 
		'motivo' => null, 
		'status' => 1
	]); 

	echo json_encode([
		'erro' => null,
		'chave' => $chave,
		'dhemi' => $dhemi
	]);
	return;
} 
catch (\RuntimeException $a) {
	echo json_encode([
		'erro' => $a->getMessage(),
		'chave' => "",
		'dhemi' => null
	]);
	return;
} 
catch (\Exception $e) {
	echo json_encode([
		'erro' => $e->getMessage(),
		'chave' => "",
		'dhemi' => null
	]);
	return;
}