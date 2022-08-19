<?php
defined('PATH_ROOT') || define('PATH_ROOT', realpath($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR);
require_once PATH_ROOT . 'app/settings.php';
require_once PATH_ROOT . 'app/vendor/autoload.php';
require_once PATH_ROOT . 'app/settings-config.php';
require_once PATH_ROOT . 'assets/' . ASSETS .  '/settings.php';
require_once PATH_ROOT . 'adm/correios/correios-bootstrap.php';
require_once PATH_ROOT . 'app/includes/bibli-funcoes.php';

$id_plp = filter_input(INPUT_GET, 'id_plp') ?? 0;
$imprimir_tipo = filter_input(INPUT_GET, 'imprimir_tipo');
$etiquetas_id = filter_input(INPUT_GET, 'etiquetas_id') ?? 0;
$ResultAll = [];
$a = 0;
$b = 0;
$c = 0;
$CorreiosPlp = CorreiosPlp::find([
	'joins' => ['etiquetas'],
	'conditions' => ($id_plp == 0 ? ['correios_etiquetas.id=? and correios_etiquetas.loja_id=?', $etiquetas_id, $CONFIG['loja_id']]:['correios_plp.id=?', $id_plp])
]);
// echo CorreiosPlp::connection()->last_query;
// print_r($CorreiosPlp->etiquetas);
// return;
foreach($CorreiosPlp->etiquetas as $etiquetas) 
{
	if( $etiquetas->pedido->id > 0 )
	{
		$a_alt = 0;
		$a_lar = 0;
		$a_com = 0;
		foreach($etiquetas->pedido->pedidos_vendas as $rs) 
		{
			if( $a_alt < $rs->produto->freteproduto->altura )
				$a_alt = $rs->produto->freteproduto->altura;

			if( $a_lar < $rs->produto->freteproduto->largura )
				$a_lar = $rs->produto->freteproduto->largura;

			if( $a_com < $rs->produto->freteproduto->comprimento )
				$a_com = $rs->produto->freteproduto->comprimento;

			$ResultAll[$a]['a'] = $rs->produto->nome_produto;
			$ResultAll[$a]['codigo'] = $etiquetas->pedido->codigo;
			$ResultAll[$a]['nome'] = $etiquetas->pedido->cliente->nome;
			$ResultAll[$a]['email'] = $etiquetas->pedido->cliente->email;
			$ResultAll[$a]['telefone'] = $etiquetas->pedido->cliente->telefone;
			
			$ResultAll[$a]['endereco'] = $etiquetas->pedido->pedido_endereco->endereco;
			$ResultAll[$a]['numero'] = $etiquetas->pedido->pedido_endereco->numero;
			$ResultAll[$a]['bairro'] = $etiquetas->pedido->pedido_endereco->bairro;
			$ResultAll[$a]['complemento'] = $etiquetas->pedido->pedido_endereco->complemento;
			$ResultAll[$a]['cidade'] = $etiquetas->pedido->pedido_endereco->cidade;
			$ResultAll[$a]['uf'] = $etiquetas->pedido->pedido_endereco->uf;
			$ResultAll[$a]['cep'] = $etiquetas->pedido->pedido_endereco->cep;
			
			$ResultAll[$a]['nrnfe'] = substr((!empty($etiquetas->pedido->nfe_notas->chavenfe) ? $etiquetas->pedido->nfe_notas->chavenfe:null), -18, 8);
			$ResultAll[$a]['quantidade'] = $rs->quantidade;
			$ResultAll[$a]['valor_pago'] = $rs->valor_pago;

			$ResultAll[$a]['altura'] += (float)($rs->produto->freteproduto->altura > 0 ? $rs->produto->freteproduto->altura / $rs->quantidade : 0);
			$ResultAll[$a]['largura'] = $a_lar;
			$ResultAll[$a]['comprimento'] = $a_com;			
			$ResultAll[$a]['peso'] += (float)($rs->produto->freteproduto->peso * $rs->quantidade);
			
			$ResultAll[$a]['seguro'] = $etiquetas->seguro;
			$ResultAll[$a]['servico'] = $etiquetas->servico;
			$ResultAll[$a]['etiqueta'] = $etiquetas->etiqueta;
			$ResultAll[$a]['dv'] = $etiquetas->dv;
			$ResultAll[$a]['plp_nr'] = $CorreiosPlp->plp_nr;
			$ResultAll[$a]['id_etiqueta'] = $etiquetas->id;

			if( ! empty($rs->produto->grid_kits) )
			{
				$b_alt = 0;
				$b_lar = 0;
				$b_com = 0;
				unset($ResultAll[$a]);
				foreach ($rs->produto->grid_kits as $pr ) 
				{
					$ResultAll[$b]['a'] = $pr->produto->nome_produto;
					$ResultAll[$b]['codigo'] = $etiquetas->pedido->codigo;
					$ResultAll[$b]['nome'] = $etiquetas->pedido->cliente->nome;
					$ResultAll[$b]['email'] = $etiquetas->pedido->cliente->email;
					$ResultAll[$b]['telefone'] = $etiquetas->pedido->cliente->telefone;
					
					$ResultAll[$b]['endereco'] = $etiquetas->pedido->pedido_endereco->endereco;
					$ResultAll[$b]['numero'] = $etiquetas->pedido->pedido_endereco->numero;
					$ResultAll[$b]['bairro'] = $etiquetas->pedido->pedido_endereco->bairro;
					$ResultAll[$b]['complemento'] = $etiquetas->pedido->pedido_endereco->complemento;
					$ResultAll[$b]['cidade'] = $etiquetas->pedido->pedido_endereco->cidade;
					$ResultAll[$b]['uf'] = $etiquetas->pedido->pedido_endereco->uf;
					$ResultAll[$b]['cep'] = $etiquetas->pedido->pedido_endereco->cep;
					
					$ResultAll[$b]['nrnfe'] = substr((!empty($etiquetas->pedido->nfe_notas->chavenfe) ? $etiquetas->pedido->nfe_notas->chavenfe:null), -18, 8);
					$ResultAll[$b]['quantidade'] = $rs->quantidade;
					$ResultAll[$b]['valor_pago'] = $rs->produto->preco_promo;
					
					$ResultAll[$b]['altura'] += (float)($pr->produto->freteproduto->altura > 0 ? $pr->produto->freteproduto->altura / $rs->quantidade : 0);
					$ResultAll[$b]['largura'] = $b_lar;
					$ResultAll[$b]['comprimento'] = $b_com;					
					$ResultAll[$b]['peso'] += (float)($pr->produto->freteproduto->peso * $rs->quantidade);
					
					$ResultAll[$b]['seguro'] = $etiquetas->seguro;
					$ResultAll[$b]['servico'] = $etiquetas->servico;
					$ResultAll[$b]['etiqueta'] = $etiquetas->etiqueta;
					$ResultAll[$b]['dv'] = $etiquetas->dv;
					$ResultAll[$b]['plp_nr'] = $CorreiosPlp->plp_nr;
					$ResultAll[$b]['id_etiqueta'] = $etiquetas->id;
					$b++;
				}	
			}
			$a++;
		}
	}

	// Pedidos SkyHub
	if( $etiquetas->skyhub_order->id > 0 )
	{
		$c_alt = 0;
		$c_lar = 0;
		$c_com = 0;
		$c = $a;
		foreach($etiquetas->skyhub_order->skyhub_produto as $rs) 
		{
			if( $c_alt < $rs->altura )
				$c_alt = $rs->altura;

			if( $c_lar < $rs->largura )
				$c_lar = $rs->largura;

			if( $c_com < $rs->comprimento )
				$c_com = $rs->comprimento;

			$ResultAll[$c]['c'] = $rs->nome;
			$ResultAll[$c]['codigo'] = $etiquetas->skyhub_order->cod_venda;
			$ResultAll[$c]['nome'] = $etiquetas->skyhub_order->nome_cliente;
			$ResultAll[$c]['email'] = $etiquetas->skyhub_order->email;
			$ResultAll[$c]['telefone'] = $etiquetas->skyhub_order->telefone;
			
			$ResultAll[$c]['endereco'] = $etiquetas->skyhub_order->endereco;
			$ResultAll[$c]['numero'] = $etiquetas->skyhub_order->numero;
			$ResultAll[$c]['bairro'] = $etiquetas->skyhub_order->bairro;
			$ResultAll[$c]['complemento'] = $etiquetas->skyhub_order->complemento;
			$ResultAll[$c]['cidade'] = $etiquetas->skyhub_order->cidade;
			$ResultAll[$c]['uf'] = $etiquetas->skyhub_order->uf;
			$ResultAll[$c]['cep'] = $etiquetas->skyhub_order->cep;
			
			$ResultAll[$c]['nrnfe'] = substr((!empty($etiquetas->skyhub_order->chave_nfe) ? $etiquetas->skyhub_order->chave_nfe:null), -18, 8);
			$ResultAll[$c]['quantidade'] = $rs->quantidade;
			$ResultAll[$c]['valor_pago'] = $rs->valor;

			$ResultAll[$c]['altura'] += (float)($rs->altura > 0 ? $rs->altura / $rs->quantidade : 0);
			$ResultAll[$c]['largura'] = $a_lar;
			$ResultAll[$c]['comprimento'] = $a_com;			
			$ResultAll[$c]['peso'] += (float)($rs->peso > 0 ? $rs->peso * $rs->quantidade:0);
			
			$ResultAll[$c]['seguro'] = $etiquetas->seguro;
			$ResultAll[$c]['servico'] = $etiquetas->servico;
			$ResultAll[$c]['etiqueta'] = $etiquetas->etiqueta;
			$ResultAll[$c]['dv'] = $etiquetas->dv;
			$ResultAll[$c]['plp_nr'] = $CorreiosPlp->plp_nr;
			$c++;
		}
	}
}

usort($ResultAll, function($a, $b) {
	return $a['codigo'] > $b['codigo'];
});

// printf('<pre>%s</pre>', print_r($ResultAll, 1));
// return;

$ObjetosPostal = null;
try{
	foreach( $ResultAll as $rs ) 
	{
		// DADOS DA ENCOMENDA QUE SERÁ DESPACHADA
		$Dimensao = new \PhpSigep\Model\Dimensao();
		$Dimensao->setAltura( $rs['altura'] );
		$Dimensao->setLargura( $rs['largura'] );
		$Dimensao->setComprimento( $rs['comprimento'] );
		$Dimensao->setDiametro(0);
		$Dimensao->setTipo(\PhpSigep\Model\Dimensao::TIPO_PACOTE_CAIXA);
		
		$Destinatario = new \PhpSigep\Model\Destinatario();
		$Destinatario->setNome($rs['nome']);
		$Destinatario->setEmail($rs['email']);
		$Destinatario->setTelefone($rs['telefone']);
		$Destinatario->setLogradouro(utf8_encode($rs['endereco']));
		$Destinatario->setNumero($rs['numero']);
		if( ! empty( $rs['complemento'] ) )
			$Destinatario->setComplemento($rs['complemento']);
		
		$DestinoNacional = new \PhpSigep\Model\DestinoNacional();
		$DestinoNacional->setBairro($rs['bairro']);
		$DestinoNacional->setCidade($rs['cidade']);
		$DestinoNacional->setUf($rs['uf']);
		$DestinoNacional->setCep($rs['cep']);
		if( ! empty( $rs['nrnfe'] ) && $rs['nrnfe'] != null ) {
			$DestinoNacional->setNumeroNotaFiscal($rs['nrnfe']);
		}
		$DestinoNacional->setNumeroPedido($rs['codigo']);
		
		// Estamos criando uma etique falsa, mas em um ambiente real voçê deve usar o método
		// {@link \PhpSigep\Services\SoapClient\Real::solicitaEtiquetas() } para gerar o número das etiquetas
		$etiqueta = new \PhpSigep\Model\Etiqueta();
		$etiqueta->setEtiquetaSemDv($rs['etiqueta']);
		$etiqueta->setDv($rs['dv']);

		// Se não tiver valor declarado informar 0 (zero)
		$ServicoAdicional = new \PhpSigep\Model\ServicoAdicional();
		$ServicoAdicional->setCodigoServicoAdicional(\PhpSigep\Model\ServicoAdicional::SERVICE_REGISTRO);
		$ServicoAdicional->setValorDeclarado( ! empty( $rs['seguro'] ) ? $rs['valor_pago'] : 0 );
		
		$ObjetoPostal = new \PhpSigep\Model\ObjetoPostal();
		$ObjetoPostal->setServicosAdicionais(array($ServicoAdicional));
		$ObjetoPostal->setDestinatario($Destinatario);
		$ObjetoPostal->setDestino($DestinoNacional);
		$ObjetoPostal->setDimensao($Dimensao);
		$ObjetoPostal->setEtiqueta($etiqueta);
		$ObjetoPostal->setPeso($rs['peso']);
		// $ObjetoPostal->setServicoDePostagem(new \PhpSigep\Model\ServicoDePostagem(\PhpSigep\Model\ServicoDePostagem::SERVICE_SEDEX_40096));	
		$ObjetoPostal->setServicoDePostagem(new \PhpSigep\Model\ServicoDePostagem( $rs['servico'] ));	
		
		$ObjetosPostal[ $rs['codigo'] ] = $ObjetoPostal;
		
		// Pega o código da plp gerada
		$plp_nr = ! empty($rs['plp_nr']) ? $rs['plp_nr'] : substr(time(), 0, 8);
		
		// Remove os dados duplicados
		if( $etiquetas_id > 0 && $etiquetas_id != $rs['id_etiqueta'] ) {
			unset($ObjetosPostal[ $rs['codigo'] ]);
		}
	}

	$date = date('y-m-d H:i:s');

	// DADOS DO REMETENTE
	$remetente = new \PhpSigep\Model\Remetente();
	$remetente->setNome($CONFIG['nome_fantasia']);
	$remetente->setTelefone( $CONFIG['telefone'] );
	$remetente->setEmail( $CONFIG['email_contato'] );
	$remetente->setLogradouro($CONFIG['endereco']);
	$remetente->setNumero($CONFIG['numero']);

	if( ! empty( $CONFIG['bairro'] ) )
		$remetente->setBairro($CONFIG['bairro']);

	$remetente->setCep($CONFIG['cep']);
	$remetente->setCidade($CONFIG['cidade']);
	$remetente->setUf($CONFIG['uf']);

	$remetente->setNumeroContrato( $CONFIG['correios']['nro_contrato'] );;
	$remetente->setCodigoAdministrativo( $CONFIG['correios']['cod_admin'] );
	$remetente->setDiretoria( $CONFIG['correios']['diretoria'] );

	$plp = new \PhpSigep\Model\PreListaDePostagem();
	$plp->setAccessData( $AccessDataCorreios );
	$plp->setEncomendas( $ObjetosPostal );
	$plp->setRemetente( $remetente );

	// print_r($AccessDataCorreios);
	// return;

	$logoFile = sprintf('%s/assets/%s/imgs/%s', PATH_ROOT, ASSETS, $CONFIG['correios']['logo_loja']);

	$ListaDePostagem  = new \PhpSigep\Pdf\ListaDePostagem($plp, $plp_nr);
	$CartaoDePostagem2018 = new \PhpSigep\Pdf\CartaoDePostagem2018($plp, $plp_nr, $logoFile, []);
	$fileName = tempnam(sys_get_temp_dir(), 'phpsigep') . '.pdf';

	// Define o tipo de impressão para os correios
	if( ! empty ( $GET['imprimir_tipo'] ) && $GET['imprimir_tipo'] == 'etiquetas_a4'  ) 
	{
		$CartaoDePostagem2018->render('F', $fileName);
		unset($CartaoDePostagem2018);

		$ImprovedFPDF = new \PhpSigep\Pdf\ImprovedFPDF('P', 'mm', 'Letter');
		$ImprovedFPDF->SetTitle(sprintf('Etiqueta Correios - %s', $CONFIG['nome_fantasia']), 'UTF-8');
		$ImprovedFPDF->AddPage();
		$ImprovedFPDF->SetFillColor(0,0,0);
		$ImprovedFPDF->SetFont('Arial', 'B', 18);
		$pageCount = $ImprovedFPDF->setSourceFile($fileName);

		for($i = 1; $i <= $pageCount; $i++) 
		{
			$tplIdx = $ImprovedFPDF->importPage($i, '/MediaBox');

			$mod = $i % 4;
			
			switch ($mod) 
			{
				case 0:
					//A4: 210(x) × 297(y)
					//Letter: 216 (x) × 279 (y)
					$ImprovedFPDF->useTemplate($tplIdx, 110, 140, 105, 138, true);

					if ($i !== $pageCount) {
						$ImprovedFPDF->AddPage();
						$ImprovedFPDF->SetFillColor(0,0,0);
					}
					break;
				case 1:
					$ImprovedFPDF->useTemplate($tplIdx, 1, 1, 105, 138, true);
					break;
				case 2:
					$ImprovedFPDF->useTemplate($tplIdx, 110, 1, 105, 138, true);
					break;
				case 3:
					$ImprovedFPDF->useTemplate($tplIdx, 1, 140, 105, 138, true);
					break;
			}
		}
		
		$ImprovedFPDF->Output();
		unset($fileName);
		return;
	}

	// Define o tipo de impressão para os correios
	if( ! empty ( $GET['imprimir_tipo'] ) && $GET['imprimir_tipo'] == 'etiquetas'  ) {
		$CartaoDePostagem2018->render();
		unset($CartaoDePostagem2018);
		return;
	}

	if( ! empty ( $GET['imprimir_tipo'] ) && $GET['imprimir_tipo'] == 'plp' ) {
		$ListaDePostagem->render('I');
		unset($ListaDePostagem);
		return;
	}
} catch (Exception $a){
	print_r($a);
}