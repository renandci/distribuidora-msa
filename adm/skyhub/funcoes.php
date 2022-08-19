<?php
defined('PATH_ROOT') || define('PATH_ROOT', realpath($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR);
require_once PATH_ROOT . '/app/settings.php';
require_once PATH_ROOT . '/app/vendor/autoload.php';
require_once PATH_ROOT . '/app/settings-config.php';
require_once PATH_ROOT . '/assets/' . ASSETS .  '/settings.php';
require_once PATH_ROOT . '/app/includes/bibli-funcoes.php';
require_once PATH_ROOT . '/app/includes/ajax-emails.php';
require_once PATH_ROOT . '/adm/correios/correios-bootstrap.php';

header('Content-Type: application/json');

switch($_POST['acao'])
{
    case 'lista_produtos':
        $options['conditions'] = sprintf('loja_id = %u and excluir = 0 and NOT EXISTS ( SELECT 1 FROM skyhub_log WHERE produtos.codigo_id = skyhub_log.sku_principal LIMIT 1)', $CONFIG['loja_id']);
        $options['order'] = 'id desc';

        if( isset($_POST['pesquisa']) && strlen($_POST['pesquisa']) > 0 )
            $options['conditions'] .= sprintf(' and nome_produto LIKE "%%%s%%"', $_POST['pesquisa']);
        
        $lista = [];
        $Produtos = Produtos::all($options);

        foreach($Produtos as $rs)
        {
            if(!isset($lista[$rs->codigo_id]['variacoes']))
                $lista[$rs->codigo_id]['variacoes'] = [];

            $imagens = [];
            $categorias = [];

            foreach($rs->produtos_menus as $rsm)
                $categorias[] = $rsm->grupo->grupo;

            foreach($rs->fotos as $rsf)
                $imagens[] = Imgs::src($rsf->imagem, 'large');

            $preco = $rs->preco_venda > 0 ? $rs->preco_venda : $rs->preco_promo;
            $preco_promo = $rs->preco_venda > 0 ? $rs->preco_promo : 0;

            if( count($imagens) > 0 )
            {
                $lista[$rs->codigo_id]['id'] = $rs->id;
                $lista[$rs->codigo_id]['sku'] = $rs->codigo_id;
                $lista[$rs->codigo_id]['nome'] = $rs->nome_produto;
                $lista[$rs->codigo_id]['categorias'] = array_unique($categorias);
                $lista[$rs->codigo_id]['marca'] = $rs->marca->marcas;
                $lista[$rs->codigo_id]['descricao'] = $rs->descricao->descricao;
                $lista[$rs->codigo_id]['ncm'] = $rs->ncm;
                $lista[$rs->codigo_id]['preco_custo'] = $rs->preco_custo;
                $lista[$rs->codigo_id]['preco'] = $preco;
                $lista[$rs->codigo_id]['preco_promo'] = $preco_promo;
                $lista[$rs->codigo_id]['altura'] = $rs->freteproduto->altura;
                $lista[$rs->codigo_id]['largura'] = $rs->freteproduto->largura;
                $lista[$rs->codigo_id]['comprimento'] = $rs->freteproduto->comprimento;
                $lista[$rs->codigo_id]['peso'] = $rs->freteproduto->peso;
                $lista[$rs->codigo_id]['variacoes'][] = [
                    'sku' => sprintf("%u-%u", $rs->codigo_id, $rs->id),
                    'cor' => strlen($rs->cor->nomecor) > 0 ? $rs->cor->nomecor : "Única",
                    'tamanho' => strlen($rs->tamanho->nometamanho) > 0 ? $rs->tamanho->nometamanho : "Único",
                    'imagens' => $imagens
                ];
            }
        }
        
        echo json_encode($lista);
    break;

    case 'transferencia_estoque':
        $Produto = Produtos::find($_POST['id_produto']);

        $Log = new SkyhubLog();
        $Log->loja_id = $CONFIG['loja_id'];
        $Log->id_produtos = $_POST['id_produto'];
        $Log->sku_principal = $Produto->codigo_id;
        $Log->estoque_local = $_POST['estoque_local'];
        $Log->estoque_trans = $_POST['estoque_trans'];
        $Log->estoque_sky = $_POST['estoque_sky'];
        $Log->save();

        if( $_POST['estoque_trans'] > 0 )
        {
            $Produto->estoque = $Produto->estoque - $_POST['estoque_trans'];
            $Produto->save();
        }

        echo json_encode(['sku_principal' => $Produto->codigo_id]);
    break;

    case 'get_produto':
        $options['conditions'] = sprintf('loja_id = %u and excluir = 0 and codigo_id = %u ', $CONFIG['loja_id'], $_POST['codigo_id']);
        $options['order'] = 'id asc';
        
        $result = [];
        $Produtos = Produtos::all($options);
        foreach($Produtos as $rs)
        {
            if(!isset($result['variacoes']))
                $result['variacoes'] = [];

            $imagens = [];
            $categorias = [];

            foreach($rs->produtos_menus as $rsm)
                $categorias[] = $rsm->grupo->grupo;

            foreach($rs->fotos as $rsf)
                $imagens[] = Imgs::src($rsf->imagem, 'large');

            $preco = $rs->preco_venda > 0 ? $rs->preco_venda : $rs->preco_promo;
            $preco_promo = $rs->preco_venda > 0 ? $rs->preco_promo : 0;

            if( count($imagens) > 0 )
            {
                $result['id'] = $rs->id;
                $result['sku'] = $rs->codigo_id;
                $result['nome'] = $rs->nome_produto;
                $result['categorias'] = array_unique($categorias);
                $result['marca'] = $rs->marca->marcas;
                $result['descricao'] = $rs->descricao->descricao;
                $result['ncm'] = $rs->ncm;
                $result['preco_custo'] = $rs->preco_custo;
                $result['preco'] = $preco;
                $result['preco_promo'] = $preco_promo;
                $result['altura'] = $rs->freteproduto->altura;
                $result['largura'] = $rs->freteproduto->largura;
                $result['comprimento'] = $rs->freteproduto->comprimento;
                $result['peso'] = $rs->freteproduto->peso;
                $result['variacoes'][] = [
                    'sku' => sprintf("%u-%u", $rs->codigo_id, $rs->id),
                    'cor' => strlen($rs->cor->nomecor) > 0 ? $rs->cor->nomecor : "Única",
                    'tamanho' => strlen($rs->tamanho->nometamanho) > 0 ? $rs->tamanho->nometamanho : "Único",
                    'imagens' => $imagens
                ];
            }
        }

        echo json_encode($result);
    break;

    case 'get_estoque':
        $sku = explode("-", $_POST['sku']);
        $rs = Produtos::find($sku[1]);

        $result = [
            'id' => $rs->id,
            'estoque' => $rs->estoque
        ];

        echo json_encode($result);
    break;

    case 'get_emitentes':

        $options['conditions'] = sprintf('loja_id = %u', $CONFIG['loja_id']);
        $options['order'] = 'id asc';

        $id = 0;
        $ids_produtos = [];
        $sky_produtos = [];
        $lista_produtos = [];
        $produtos_com_erro = [];
        foreach($_POST['produtos'] as $rs)
        {
            $strpos = strpos($rs['id'], '-');
            if($strpos === false) {
                $Produtos = Produtos::first(['conditions' => ['excluir = 0 and codigo_id=?', $rs['id']]]);
            } 
            else {
                $rs['id'] = substr($rs['id'], $strpos + 1);
                $Produtos = Produtos::first(['conditions' => ['excluir = 0 and id=?', $rs['id']]]);
            }
            
            $sky_produtos[$Produtos->id]['id'] = $Produtos->id;
            $sky_produtos[$Produtos->id]['preco_venda'] = $rs['special_price'];
            $sky_produtos[$Produtos->id]['original_price'] = $rs['original_price'];
            $sky_produtos[$Produtos->id]['qty'] = $rs['qty'];

            $lista_produtos[] = [
                'prod_id' => $Produtos->id,
                'codigo_id' => $Produtos->codigo_id,
                'prod_cod' => $Produtos->codigo_produto,
                'prod_nome' => $Produtos->nome_produto,
                'prod_csosn' => $Produtos->csosn,
                'prod_unid' => $Produtos->unid,
                'prod_cest' => $Produtos->cest,
                'prod_cfop' => $Produtos->cfop,
                'prod_ncm' => $Produtos->ncm,
                'prod_cst' => $Produtos->cst,
                'prod_cor' => $Produtos->cor->nomecor,
                'prod_tam' => $Produtos->tamanho->nometamanho,
                'prod_price' => $sky_produtos[$Produtos->id]['preco_venda'],
                'prod_unitario' => $sky_produtos[$Produtos->id]['original_price'],
                'prod_desc' => $sky_produtos[$Produtos->id]['original_price'] - $sky_produtos[$Produtos->id]['preco_venda'],
                'prod_qtde' => $sky_produtos[$Produtos->id]['qty']
            ];

            if( empty( $Produtos->nfe_ncm ) && $Produtos->nfe_ncm->ncm == '' ) {
                Produtos::new_save(['id' => $Produtos->id, 'ncm' => '']);
                $produtos_com_erro[] = [
                    'sku' => sprintf("%s-%s", $Produtos->codigo_id, $Produtos->id),
                    'codigo_id' => $Produtos->codigo_id,
                    'nome' => $Produtos->nome_produto
                ];
            }
        }
        
        // exit(json_encode($lista_produtos));

        $lista['produtos_com_erro'] = $produtos_com_erro;
        $lista['produtos'] = $lista_produtos;
        $lista['emitentes'] = [];
        $lista['cidades'] = [];
        $Emitentes = NfeEmitentes::all($options);
        foreach( $Emitentes as $rs )
        {
            $lista['emitentes'][] = [
                'id' => $rs->id,
                'nome' => $rs->razaosocial
            ];
        }

        $city = $_POST['cidade_cliente'];
        $uf = strtoupper($_POST['uf_cliente']);

        $Cidades = NfeCidades::all(['conditions' => sprintf("nome LIKE '%%%s%%'", $city) ]);
        $CidadesCount = (int)count($Cidades);
        if($CidadesCount > 0) {
            foreach($Cidades as $cid)
                $lista['cidades'][] = [
                    'nome' => sprintf("%s / %s", $cid->nome, $cid->uf),
                    'uf' => $cid->uf,
                    'cod_ibge' => $cid->cod_ibge
                ];
        } 
        else {
            $Cidades = NfeCidades::all(['order' => "nome asc" ]);                
            foreach($Cidades as $cid)
                $lista['cidades'][] = [
                    'nome' => sprintf("%s / %s", $cid->nome, $cid->uf),
                    'uf' => $cid->uf,
                    'cod_ibge' => $cid->cod_ibge
                ];
        }
        
        echo json_encode($lista);
    break;

    case 'alterar_num_ncm':
        // csosn=&unid=&cfop=&ncm=&cest=&codigo_id=1612375183&acao=alterar_num_ncm

        $codigo_id 	= filter_input(INPUT_POST, 'codigo_id', FILTER_SANITIZE_NUMBER_INT);
        $csosn 	    = filter_input(INPUT_POST, 'csosn', FILTER_SANITIZE_NUMBER_INT);
        $unid 	    = filter_input(INPUT_POST, 'unid', FILTER_SANITIZE_STRING);
        $cfop 	    = filter_input(INPUT_POST, 'cfop', FILTER_SANITIZE_NUMBER_INT);
        $ncm 	    = filter_input(INPUT_POST, 'ncm', FILTER_SANITIZE_NUMBER_INT);
        $cest 	    = filter_input(INPUT_POST, 'cest', FILTER_SANITIZE_NUMBER_INT);

        $ProdutosAll = Produtos::all(['conditions' => ['codigo_id=?', $codigo_id]]);
        foreach($ProdutosAll as $rws) 
        {
            $result = Produtos::new_save([
                'id' => $rws->id, 
                'csosn' => $csosn,
                'unid' => $unid,
                'cfop' => $cfop,
                'ncm' => $ncm,
                'cest' => $cest,
            ]);
        }

        echo json_encode(['status' => "ok"]);
    break;
    

    case 'CodigoCorreios':
        $cod = [];

        $servico[0] = \PhpSigep\Model\ServicoDePostagem::SERVICE_PAC_CONTRATO_AGENCIA_TA;
        $servico[1] = \PhpSigep\Model\ServicoDePostagem::SERVICE_SEDEX_CONTRATO_AGENCIA_TA;
        for($i = 0; $i <= 1; $i++) 
        {
            $SolicitaEtiquetas = new \PhpSigep\Model\SolicitaEtiquetas();
            $SolicitaEtiquetas->setQtdEtiquetas(1);
            $SolicitaEtiquetas->setServicoDePostagem($servico[$i]);
            $SolicitaEtiquetas->setAccessData($AccessDataCorreios);

            $phpSigep = new PhpSigep\Services\SoapClient\Real();
            $SolicitaEtiquetas = $phpSigep->solicitaEtiquetas($SolicitaEtiquetas);

            if( $SolicitaEtiquetas->getErrorCode() === null ) 
            {
                $SolicitaEtiquetas = $SolicitaEtiquetas->getResult();

                $GeraDigitoVerificadorEtiquetas = new \PhpSigep\Model\GeraDigitoVerificadorEtiquetas();
                $GeraDigitoVerificadorEtiquetas->setAccessData($AccessDataCorreios);
                $GeraDigitoVerificadorEtiquetas->setEtiquetas($SolicitaEtiquetas);
                $Etiquetas = $phpSigep->geraDigitoVerificadorEtiquetas($GeraDigitoVerificadorEtiquetas);

                if($Etiquetas->getErrorCode() === null) 
                {
                    $r = $Etiquetas->getResult()[0];
                    $codigo = $r->getEtiquetaSemDv();
                    $dv = $r->getDv();
                    
                    $cod[$i]['servico'] = $i == 0 ? 'PAC':'SEDEX';
                    $cod[$i]['codigo'] = mask($codigo, "##########{$dv}##");
                }
            }
        }

        echo json_encode([
            'erro' => null,
            'codigo' => $cod,
            'carrier' => 'Correios',
            'url' => 'www.correios.com.br'
        ]);

        // echo json_encode($r);
    break;
    

    case 'info-correios': 
        $json = [];
        
        $ConfiguracoesFretesEnvios = ConfiguracoesFretesEnvios::first(['conditions' => ['loja_id=?', $CONFIG['loja_id']]]);
        $params = new \PhpSigep\Model\CalcPrecoPrazo();

        $params->setAccessData($AccessDataCorreios);
        $params->setCepOrigem($CONFIG['cep']);
        $params->setCepDestino($_POST['cep_destino'] == "90000000" ? "14900000" : $_POST['cep_destino']);

        $dimensao = new \PhpSigep\Model\Dimensao();
        $dimensao->setTipo(\PhpSigep\Model\Dimensao::TIPO_PACOTE_CAIXA);
        $dimensao->setAltura($_POST['cubagem']['altura']); // em centímetros
        $dimensao->setLargura($_POST['cubagem']['largura']); // em centímetros
        $dimensao->setComprimento($_POST['cubagem']['comprimento']); // em centímetros

        $servico_by = null;
        foreach( $ConfiguracoesFretesEnvios->envios_correios as $int )
            $servico_by[] = new \PhpSigep\Model\ServicoDePostagem($int);

        $params->setAjustarDimensaoMinima(true);
        $params->setServicosPostagem($servico_by);
        $params->setDimensao($dimensao);
        $params->setPeso($_POST['cubagem']['peso']);

        $phpSigep = new PhpSigep\Services\SoapClient\Real();
        $result = $phpSigep->calcPrecoPrazo($params);

        if (!$result->hasError() || ( $result->hasError() && $result->getErrorCode() == 11 ) )
        {
            $servicos = $result->getResult();
            foreach ($servicos as $servico)
            {
                $json[] = [
                    'codigo' => trim($servico->getServico()->getCodigo()),
                    'nome' => trim($servico->getServico()->getNome()),
                    'valor' => $servico->getValor(),
                    'descricao' => sprintf("%s | Valor: R$ %s", trim($servico->getServico()->getNome()), number_format($servico->getValor(), 2, ',', '.') )
                ];
            }
        }

        echo json_encode($json);
    break;

    case 'gerar-etiqueta-correios':
        try{
            $CodigoRastreio = '';
            $Pedido = $_POST['pedido'];
            $Produtos = $_POST['produtos'];
            // echo json_encode($_POST);
            // die();

            $SolicitaEtiquetas = new \PhpSigep\Model\SolicitaEtiquetas();
            $SolicitaEtiquetas->setQtdEtiquetas($POST['volumes'] > 0 ? $POST['volumes'] : 1);
            $SolicitaEtiquetas->setAccessData($AccessDataCorreios);
            $SolicitaEtiquetas->setServicoDePostagem((string)$POST['frete_servico']);

            $phpSigep = new PhpSigep\Services\SoapClient\Real();
            $EtiquetasResult = $phpSigep->solicitaEtiquetas($SolicitaEtiquetas);
            
            if( is_object($EtiquetasResult) && $EtiquetasResult->getErrorCode() != null )
                throw new Exception('Erro ao solicitar Etiquetas');

            $PhpSigepParamsDv = new \PhpSigep\Model\GeraDigitoVerificadorEtiquetas();
            $PhpSigepParamsDv->setAccessData($AccessDataCorreios);
            $PhpSigepParamsDv->setEtiquetas($EtiquetasResult->getResult());

            $EtiquetasDv = $phpSigep->geraDigitoVerificadorEtiquetas($PhpSigepParamsDv);

            if( is_object($EtiquetasDv) && $EtiquetasDv->getErrorCode() != null )
                throw new Exception('Erro ao solicitar Etiquetas Dv');

            $SkyhubOrders = new SkyhubOrders();
            $SkyhubOrders->loja_id = $CONFIG['loja_id'];
            $SkyhubOrders->cod_venda = $Pedido['code'];
            $SkyhubOrders->nome_cliente = $Pedido['shipping_address']['full_name'];
            $SkyhubOrders->email = $Pedido['customer']['email'];
            $SkyhubOrders->telefone = $Pedido['shipping_address']['phone'];
            $SkyhubOrders->endereco = $Pedido['shipping_address']['street'];
            $SkyhubOrders->numero = $Pedido['shipping_address']['number'];
            $SkyhubOrders->bairro = $Pedido['shipping_address']['neighborhood'];
            $SkyhubOrders->complemento = $Pedido['shipping_address']['detail'];
            $SkyhubOrders->cidade = $Pedido['shipping_address']['city'];
            $SkyhubOrders->uf = $Pedido['shipping_address']['region'];
            $SkyhubOrders->cep = $Pedido['shipping_address']['postcode'];
            $SkyhubOrders->nr_nfe = $Pedido['invoices'][0]['number'];
            $SkyhubOrders->chave_nfe = $Pedido['invoices'][0]['key'];
            $SkyhubOrders = $SkyhubOrders->save_log();

            // Tenta adicionar uma chave para gerar a nfe corretamente
            $NfeNotas = NfeNotas::find(['conditions' => ['chavenfe=?', $Pedido['invoices'][0]['key']]]);
            $NfeNotas->id_skyhub_orders = $SkyhubOrders['id'];
            $NfeNotas->save_log();

            foreach( $Produtos as $rs_prod )
            {
                $SkyhubProdutos = new SkyhubProdutos();
                $SkyhubProdutos->id_skyhub_orders = $SkyhubOrders['id'];
                $SkyhubProdutos->nome = $rs_prod['name'];
                $SkyhubProdutos->quantidade = $rs_prod['qty'];
                $SkyhubProdutos->valor = $rs_prod['promotional_price'] > 0 ? $rs_prod['promotional_price'] : $rs_prod['price'];
                $SkyhubProdutos->altura = $rs_prod['height'];
                $SkyhubProdutos->largura = $rs_prod['width'];
                $SkyhubProdutos->comprimento = $rs_prod['length'];
                $SkyhubProdutos->peso = $rs_prod['weight'];
                $SkyhubProdutos->save_log();
            }

            foreach ($EtiquetasDv->getResult() as $etiqueta) {
                $CorreiosEtiquetas = new CorreiosEtiquetas();
                $CorreiosEtiquetas->id_pedidos = 0;
                $CorreiosEtiquetas->id_skyhub_orders = $SkyhubOrders['id'];
                $CorreiosEtiquetas->servico = $POST['frete_servico']; 
                $CorreiosEtiquetas->etiqueta = $etiqueta->getEtiquetaSemDv();
                $CorreiosEtiquetas->dv = $etiqueta->getDv();
                $CorreiosEtiquetas->seguro = $POST['frete_seguro'];
                $CorreiosEtiquetas->save_log();

                $CodigoRastreio = $etiqueta->getEtiquetaSemDv();
            }
            
            echo json_encode([
                'erro' => null,
                'codigo' => $CodigoRastreio,
                'url' => sprintf('http://lojascorreios.dcisuporte.com.br/?__token=$2a$08$MTU2OTA3MjY3ODU3ZWU3N.Uu9OcgDALScZpMFazKl2ZnwBO/2zGNK&__cod__rastreio=%s&__url__return=false', $CodigoRastreio)
            ]);
        } 
        catch( Exception $e ) {
            echo json_encode([
                'erro' => $e->getMessage(),
                'codigo' => null,
                'url' => null
            ]);
        }
    break;

    case 'info-jadlog':
        $json = [];
        $JadLogNew = new JadLogNew($CONFIG['jadlog']['token']);
        $cepLocal = soNumero($CONFIG['cep']);
		$cepDestino = soNumero($_POST['cep_destino']);
        $cepDestino = $cepDestino == "90000000" ? "14900000" : $cepDestino;
        $pesoCubagem = $_POST['cubagem']['peso'];
		
		$fretea = [
			"frete" => [ 
				[
					"cepori" => $cepLocal,
					"cepdes" => $cepDestino,
					"frap" => null,
					"peso" => $pesoCubagem,
					"conta" => $CONFIG['jadlog']['contacorrente'],
					"contrato" => $CONFIG['jadlog']['nrcontrato'],
					"modalidade" => 3,
					"tpentrega" => "D",
					"tpseguro" => "N",
					"vldeclarado" => 0,
					"vlcoleta" => 1.50
				], [
					"cepori" => $cepLocal,
					"cepdes" => $cepDestino,
					"frap" => null,
					"peso" => $pesoCubagem,
					"conta" => $CONFIG['jadlog']['contacorrente'],
					"contrato" => $CONFIG['jadlog']['nrcontrato'],
					"modalidade" => 4,
					"tpentrega" => "D",
					"tpseguro" => "N",
					"vldeclarado" => 0,
					"vlcoleta" => 1.50
				], [
					"cepori" => $cepLocal,
					"cepdes" => $cepDestino,
					"frap" => null,
					"peso" => $pesoCubagem,
					"conta" => $CONFIG['jadlog']['contacorrente'],
					"contrato" => $CONFIG['jadlog']['nrcontrato'],
					"modalidade" => 9,
					"tpentrega" => "D",
					"tpseguro" => "N",
					"vldeclarado" => 0,
					"vlcoleta" => 1.50
				]
			]
		];

		$freteb = [
			"frete" => [[
					"cepori" => $cepLocal,
					"cepdes" => $cepDestino,
					"frap" => null,
					"peso" => $pesoCubagem,
					"conta" => $CONFIG['jadlog']['contacorrente'],
					"contrato" => $CONFIG['jadlog']['nrcontrato'],
					"modalidade" => 40,
					"tpentrega" => "R",
					"tpseguro" => "N",
					"vldeclarado" => 0,
					"vlcoleta" => 1.50			
				]
			]
		];

        try {
            $vlfrete_a = $JadLogNew->post('/frete/valor', $fretea);
            $vlfrete_b = $JadLogNew->post('/frete/valor', $freteb);
			
			$json = [ 
                [
					'text' => 'Packpage',
					'modalidade' => $vlfrete_a['body']->frete[0]->modalidade,
					'valor' => number_format(($CONFIG['fretes_tipo'] == '%' ? desconto_boleto($vlfrete_a['body']->frete[0]->vltotal, $CONFIG['fretes_valor']) : ($vlfrete_a['body']->frete[0]->vltotal - $CONFIG['fretes_valor'])), 2, ',', '.')
				], [
					'text' => 'Rodoviário',
					'modalidade' => $vlfrete_a['body']->frete[1]->modalidade,
					'valor' => number_format(($CONFIG['fretes_tipo'] == '%' ? desconto_boleto($vlfrete_a['body']->frete[1]->vltotal, $CONFIG['fretes_valor']) : ($vlfrete_a['body']->frete[1]->vltotal - $CONFIG['fretes_valor'])), 2, ',', '.')
				], [
					'text' => '.COM',
					'modalidade' => $vlfrete_a['body']->frete[2]->modalidade,
					'valor' => number_format(($CONFIG['fretes_tipo'] == '%' ? desconto_boleto($vlfrete_a['body']->frete[2]->vltotal, $CONFIG['fretes_valor']) : ($vlfrete_a['body']->frete[2]->vltotal - $CONFIG['fretes_valor'])), 2, ',', '.')
				], [
					'text' => 'PICKUP',
					'modalidade' => $vlfrete_b['body']->frete[0]->modalidade,
					'valor' => number_format(($CONFIG['fretes_tipo'] == '%' ? desconto_boleto($vlfrete_b['body']->frete[0]->vltotal, $CONFIG['fretes_valor']) : ($vlfrete_b['body']->frete[0]->vltotal - $CONFIG['fretes_valor'])), 2, ',', '.')
				], 
			];

            echo json_encode([
                'erro' => null,
                'result' => $json
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'erro' => $e->getMessage(),
                'result' => null,
            ]);
        }

    break;

    case 'gerar-etiqueta-jadlog':
        $JadLogNew = new JadLogNew($CONFIG['jadlog']['token']);
        $Pedido = $_POST['pedido'];
        $Produtos = $_POST['produtos'];

        $frete_qtde = ($POST['volumes'] > 0 ? $POST['volumes'] : 1);
		$modalidade = $POST['frete_servico'];
		$nrDoc = $Pedido['invoices'][0]['number'];
		$serie = $Pedido['invoices'][0]['line'];
		$danfeCte = $Pedido['invoices'][0]['key'];
		$tpDocumento = 2;
		$cfop = (isset($_POST['cfop']) && $_POST['cfop'] != '' ? $_POST['cfop'] : null);

        $cepDestino = soNumero($Pedido['shipping_address']['postcode']);
        $cepDestino = $cepDestino == "90000000" ? "14900000" : $cepDestino;

        $TOTAL = $Pedido['total_ordered'];
        $PESO = $_POST['cubagem']['peso'];

        $json = [
			'conteudo' => $CONFIG['jadlog']['fantasia'],
			'pedido' => [ $Pedido['code'] ],
			'totPeso' => $PESO,
			'totValor' => number_format($TOTAL, 2, '.', ''),
			'obs' => '',
			'modalidade' => $modalidade,
			'contaCorrente' => $CONFIG['jadlog']['contacorrente'],
			'tpColeta' => 'K',
			'tipoFrete' => 0,
			'cdUnidadeOri' => $CONFIG['jadlog']['ponto'],
			'cdUnidadeDes' => null,
			'cdPickupOri' => null,
			'cdPickupDes' => null, // ##### PUDO_ID #####
			'nrContrato' => null,
			'shipmentId' => null,
			'vlColeta' => null,
			'servico' => 1,
			'rem' => [
				'nome' => $CONFIG['jadlog']['remet'],
				'cnpjCpf' => soNumero($CONFIG['jadlog']['cnpjcpf']),
				'ie' => soNumero($CONFIG['jadlog']['ie']),
				'endereco' => $CONFIG['jadlog']['endereco'],
				'numero' => $CONFIG['jadlog']['numero'],
				'compl' => null,
				'bairro' => $CONFIG['jadlog']['bairro'],
				'cidade' => $CONFIG['jadlog']['cidade'],
				'uf' => $CONFIG['jadlog']['uf'],
				'cep' => soNumero($CONFIG['jadlog']['cep']),
				'fone' => soNumero($CONFIG['jadlog']['fone']),
				'cel' => null,
				'email' => $CONFIG['jadlog']['email'],
				'contato' => $CONFIG['jadlog']['contato']
			],
			'des' => [
				'nome' => $Pedido['shipping_address']['full_name'],
				'cnpjCpf' => soNumero($Pedido['customer']['vat_number']),
				'ie' => null,
				'endereco' => $Pedido['shipping_address']['street'],
				'numero' => $Pedido['shipping_address']['number'],
				'compl' => $Pedido['shipping_address']['detail'],
				'bairro' => $Pedido['shipping_address']['neighborhood'],
				'cidade' => $Pedido['shipping_address']['city'],
				'uf' => $Pedido['shipping_address']['region'],
				'cep' => $cepDestino,
				'fone' => soNumero($Pedido['shipping_address']['phone']),
				'cel' => null,
				'email' => $Pedido['customer']['email'], 
				'contato' => $Pedido['shipping_address']['full_name']
			],
			'dfe' => [ [
					'danfeCte' => $danfeCte,
					'cfop' => $cfop,
					'serie' => $serie,
					'nrDoc' => $nrDoc,
					'tpDocumento' => $tpDocumento,
					'valor' => number_format($TOTAL, 2, '.', '')
				]
			],
			'volume' => [ [
					'identificador' => soNumero($Pedido['import_info']['remote_code']),
					'altura' => round($_POST['cubagem']['altura']),
					'largura' => round($_POST['cubagem']['largura']),
					'comprimento' => round($_POST['cubagem']['comprimento']),
					'peso' => $PESO
				]
			]
		];

        try {
			$ReturnJadLogNew = $JadLogNew->post('/pedido/incluir', $json);
			$json = $ReturnJadLogNew['body'];
			
			if( !empty($json->erro->descricao) )
				throw new Exception($json->erro->descricao);

            $SkyhubOrders = new SkyhubOrders();
            $SkyhubOrders->loja_id = $CONFIG['loja_id'];
            $SkyhubOrders->cod_venda = $Pedido['code'];
            $SkyhubOrders->nome_cliente = $Pedido['shipping_address']['full_name'];
            $SkyhubOrders->email = $Pedido['customer']['email'];
            $SkyhubOrders->telefone = $Pedido['shipping_address']['phone'];
            $SkyhubOrders->endereco = $Pedido['shipping_address']['street'];
            $SkyhubOrders->numero = $Pedido['shipping_address']['number'];
            $SkyhubOrders->bairro = $Pedido['shipping_address']['neighborhood'];
            $SkyhubOrders->complemento = $Pedido['shipping_address']['detail'];
            $SkyhubOrders->cidade = $Pedido['shipping_address']['city'];
            $SkyhubOrders->uf = $Pedido['shipping_address']['region'];
            $SkyhubOrders->cep = $Pedido['shipping_address']['postcode'];
            $SkyhubOrders->nr_nfe = $Pedido['invoices'][0]['number'];
            $SkyhubOrders->chave_nfe = $Pedido['invoices'][0]['key'];
            $SkyhubOrders = $SkyhubOrders->save_log();

            // Tenta adicionar uma chave para gerar a nfe corretamente
            $NfeNotas = NfeNotas::find(['conditions' => ['chavenfe=?', $Pedido['invoices'][0]['key']]]);
            $NfeNotas->id_skyhub_orders = $SkyhubOrders['id'];
            $NfeNotas->save_log();

            foreach( $Produtos as $rs_prod )
            {
                $SkyhubProdutos = new SkyhubProdutos();
                $SkyhubProdutos->id_skyhub_orders = $SkyhubOrders['id'];
                $SkyhubProdutos->nome = $rs_prod['name'];
                $SkyhubProdutos->quantidade = $rs_prod['qty'];
                $SkyhubProdutos->valor = $rs_prod['promotional_price'] > 0 ? $rs_prod['promotional_price'] : $rs_prod['price'];
                $SkyhubProdutos->altura = $rs_prod['height'];
                $SkyhubProdutos->largura = $rs_prod['width'];
                $SkyhubProdutos->comprimento = $rs_prod['length'];
                $SkyhubProdutos->peso = $rs_prod['weight'];
                $SkyhubProdutos->save();
            }
			
			$JadLogEtiqueta = new JadLogEtiqueta();
			$JadLogEtiqueta->id_pedido = 0;
            $JadLogEtiqueta->id_skyhub_orders = $SkyhubOrders['id'];
			$JadLogEtiqueta->nrnfe = $nrDoc;
			$JadLogEtiqueta->codigo = $json->codigo;
			$JadLogEtiqueta->shipment_id = $json->shipmentId;
			$JadLogEtiqueta->modalidade = $modalidade;
			$JadLogEtiqueta->volumes = $frete_qtde;
			$JadLogEtiqueta->save_log();
            
            echo json_encode([
                'erro' => null,
                'codigo' => $json->codigo,
                'url' => sprintf('http://lojascorreios.dcisuporte.com.br/?__token=$2a$08$MTU2OTA3MjY3ODU3ZWU3N.Uu9OcgDALScZpMFazKl2ZnwBO/2zGNK&__cod__rastreio=%s&__url__return=false', $json->codigo)
            ]);
		} 
		catch(exception $e) {
            echo json_encode([
                'erro' => $e->getMessage(),
                'codigo' => null,
                'url' => null
            ]);
		}
        
    break;
}
