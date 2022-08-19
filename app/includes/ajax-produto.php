<?php

$ACAO = isset($POST['acao']) ? $POST['acao'] : null;
$ACAO = isset($GET['acao']) ? $GET['acao'] : $ACAO;

switch ($ACAO) {
    // 	case 'CalcularFrete' :

    // 		$str['corpo-frete'] = '';
    //         $ID = (int)$GET['produto_id'];
    //         $CEP = soNumero($GET['produto_cep']);

    //         if( $CEP == '' ) {
    //             $str['msgerro'] = '<font color="#ff5b5b">Digite seu cep!</font>';
    //         }
    // 		else if( $ID == '0' ) {
    //             $str['msgerro'] = '<font color="#ff5b5b">Selecione um tamanho!</font>';
    //         }
    // 		else {

    // 			$CorreiosCepReal = new PhpSigep\Services\SoapClient\Real();
    // 			$CorreiosCep= $CorreiosCepReal->consultaCep($CEP);
    // 			$CIDADE 	= $CorreiosCep->getResult()->getCidade();
    // 			$UF         = $CorreiosCep->getResult()->getUf();

    // 			$query = ''
    // 					. 'select '
    // 					// . 'case '
    // 					// . 'when (select count(fg.id) from configuracoes_fretes_gratis fg where fg.uf="%s" and fg.frete_valor <= p.preco_promo and f.loja_id = fg.loja_id) > 0 or p.frete > 0 then 1 '
    // 					// . 'when (select count(fg.id) from configuracoes_fretes_gratis fg where "%s" between fg.cep_ini and fg.cep_fin and fg.frete_valor <= p.preco_promo and f.loja_id = fg.loja_id) > 0 or p.frete > 0 then 2 '
    // 					// . 'else 0 end as gratis, '
    // 					. 'p.preco_promo, '
    // 					// . 'if( f.altura >= 5, f.altura, 5 ) as altura, '
    // 					// . 'if( f.largura >= 11, f.largura, 11 ) as largura, '
    // 					// . 'if( f.comprimento >= 16, f.comprimento, 16 ) as comprimento , '
    // 					. 'f.altura, '
    // 					. 'f.largura, '
    // 					. 'f.comprimento, '
    // 					. 'f.peso, '
    // 					. 'p.postagem, '
    // 					. 'm.disponib_entrega '
    // 					. 'from dados_frete f '
    // 					. 'inner join produtos p on f.id = p.id_frete '
    // 					. 'inner join marcas m on m.id = p.id_marca '
    // 					. 'where p.id = %u';

    //             $r = DadosFrete::connection()->query(sprintf($query, $ID))->fetch();

    // 			$VALOR_FRETE = 0;
    // 			$r['gratis'] = false;
    // 			$TOTAL_CAR = $r['preco_promo'];
    // 			// 74550-020
    // 			$ConfiguracoesFretesGratis = ConfiguracoesFretesGratis::all(['conditions' => ['loja_id=?', $CONFIG['loja_id']]]);

    // 			if(count($ConfiguracoesFretesGratis) > 0)
    // 				foreach( $ConfiguracoesFretesGratis as $rws )
    // 				{
    // 					if($rws->cep_ini <= $CEP && $rws->cep_fin >= $CEP && $rws->frete_valor <= $TOTAL_CAR) {
    // 						$VALOR_FRETE = $rws->frete_valor;
    // 						$r['gratis'] = true;
    // 						break;
    // 					}
    // 					else if($rws->uf == $UF && $rws->frete_valor <= $TOTAL_CAR) {
    // 						$VALOR_FRETE = $rws->frete_valor;
    // 						$r['gratis'] = true;
    // 						break;
    // 					}
    // 					else {
    // 						$VALOR_FRETE = $rws->frete_valor;
    // 					}
    // 				}

    // 			// $str['corpo-frete'] .= DadosFrete::connection()->last_query;

    // 			// DISPONIB_ENTREGA PARA AS MARCAS
    //             // $STRING_STRING = isset($r['disponib_entrega']) && $r['disponib_entrega'] != '' ? $r['disponib_entrega'] : null;
    //             // $STRING_STRING = isset($r['postagem']) && $r['postagem'] != '' ? $r['postagem'] : $STRING_STRING;
    // 			// $STRING_PRAZOS = isset($STRING_STRING) && $STRING_STRING != '' ? $STRING_STRING : '1 a 5 dias úteis';

    // 			$POSTAGEMS[] = '1 a 5 dias';
    // 			$POSTAGEMS[] = $r['postagem'];
    // 			$POSTAGEMS[] = $r['disponib_entrega'];

    // 			// STRING PRAZOS - CONTEM UM ARRAY NUMERICOS DOS PRAZOS DOS PRODUTOS
    //             $PRAZOS = array_filter(explode(',', preg_replace('/(.)\1+/', '$1', preg_replace('/[^0-9]/', ',', implode(',', $POSTAGEMS)))));

    //             $prazoDe = min($PRAZOS);
    //             $prazoAte = max($PRAZOS);


    // 			// $FRETE = calcular_preco_frete($conexao, $STORE['config']['correios'], $CEP, $r['peso']);
    // //			$FRETE = $WebService->calcular_preco_frete($STORE['config']['correios'], $CEP, $r['peso']);
    // 			$FRETE = [];

    // 			$SomaDimensoes = $r['altura'] + $r['largura'] + $r['comprimento'];

    // 			if( $SomaDimensoes <= 200 && $r['altura'] <= 105 && $r['largura'] <= 105 && $r['comprimento'] <= 105 )
    // 				$FRETE = $FRETE + calcular_preco_frete($STORE['config']['correios'], $CONFIG['cep'], $CEP, $r['peso'], $r['altura'], $r['largura'], $r['comprimento']);

    // 			// calcular_fretejalog($pesoCubagem = 1, $cepLocal = 14900000, $cepDestino = 14900000, $Servico = 'JADLOG')

    // 			if( ! empty( $STORE['config']['jadlog'] ) ) {

    // 				$cubagem = (($r['altura'] * $r['largura'] * $r['comprimento']) / 6000);

    // 				$r['peso'] = $r['peso'] < dinheiro($cubagem) ? dinheiro($cubagem) : $r['peso'];

    // 				$FRETE = $FRETE + calcular_fretejadlog($r['peso'], $CONFIG['cep'], $CEP, $STORE['config']['jadlog']);

    // 				if( $CONFIG['dominio'] == 'realambiente' && isset($FRETE['JADLOG']) )
    // 				{
    // 					$na_faixa = false;
    // 					$Zuzim = $FRETE['JADLOG'];
    // 					$CepDest = substr( $Zuzim['cepdes'], 0, 5 );

    // 					if( $CepDest >= 1000 && $CepDest <= 5999 )
    // 						$na_faixa = true;

    // 					if( $CepDest >= 8000 && $CepDest <= 8499 )
    // 						$na_faixa = true;

    // 					if( $CepDest >= 6000 && $CepDest <= 9999 )
    // 						$na_faixa = true;

    // 					if($na_faixa)
    // 					{
    // 						$z['Zeni'] = [];
    // 						$z['Zeni']['valor'] = $Zuzim['valor'];
    // 						$z['Zeni']['prazo'] = $Zuzim['prazo'];
    // 						$FRETE = $FRETE + $z;
    // 					}

    // 				}
    // 			}

    // 			$str['corpo-frete'] .= ''
    //                     . '<table cellpadding="0" cellspacing="0" border="0" width="100%" class="text-left">'
    //                     . '<thead>'
    //                     . '<tr>'
    //                     . sprintf('<th colspan="3"><span class=" black-40 ft18px mb5 show">%s - %s</span></th>', $CIDADE, $UF)
    //                     . '</tr>'
    //                     . '</thead>'
    //                     . '<tbody>';

    // 			$str['corpo-frete'] .= !empty($r['gratis']) ? ''
    // 								 . '<tr class="mb5 border-bottom-dotted">'
    // 									 . '<td class="ft16px"><label class="imagens-frete frete-gratis"></label></td>'
    // 									 . '<td align="right" nowrap="nowrap" width="2%" class="">'
    // 										. '<b class="ft16px">Frete Grátis</b>'
    // 									 . '</td>'
    // 								 . '</tr>'
    // 								 . '<tr>'
    // 									 . '<td colspan="2">'
    // 										 . '<span class="show">'
    // 										 . 'Prazo de entrega: de '.($FRETE['PAC']['prazo'] + $prazoDe) . ' até ' . ($FRETE['PAC']['prazo'] + $prazoAte) . ' dia(s) úteis'
    // 										 . '</span>'
    // 									 . '</td>'
    // 								 . '</tr>' : '';

    // 			$frete_vl = 0;
    // 			$Teste = [];
    // 			if( empty( $r['gratis'] ) ) foreach( $FRETE as $key => $values ) {
    // 				$Liberado[$key] = true;
    // 				// verifica a existencia de subsidiar o valor sobre o total final
    // 				if( $CONFIG['fretes_sob_vl'] == 1 ) {

    // 					if($CONFIG['fretes_tipo'] == '%')
    // 						$frete_vl = $TOTAL_CAR - desconto_boleto($TOTAL_CAR, $CONFIG['fretes_valor']);
    // 					else
    // 						$frete_vl = ($TOTAL_CAR - $CONFIG['fretes_valor']);

    // 					$frete_vl = $FRETE[$key]['valor'] - $frete_vl;
    // 				}
    // 				else {
    // 					$frete_vl = ($CONFIG['fretes_tipo'] == '%' ? desconto_boleto($FRETE[$key]['valor'], $CONFIG['fretes_valor']) : ($FRETE[$key]['valor'] - $CONFIG['fretes_valor']));
    // 				}

    // 				if($key == "Zeni")
    // 					$frete_vl = $frete_vl * 0.6;

    // 				$Teste[]= "A: ".$r['altura'] . " L:" . $r['largura'] . " C:". $r['comprimento'] . " P:" .$r['peso'];

    // 				$frete_vl = $frete_vl <= 0 ? 0.00 : $frete_vl;

    // 				if( isset($FRETE[$key]['modalidade']) && $FRETE[$key]['modalidade'] == 40 ){
    // 					if( $r['altura'] > 80 || $r['largura'] > 80 || $r['comprimento'] > 80 || $r['peso'] > 10 )
    // 						$Liberado[$key] = false;
    // 				}

    // 				$txtFreteVl = $frete_vl = 0 ? "Grátis" : number_format($frete_vl, 2, ',', '.');

    // 				if($Liberado[$key]) {
    // 					$str['corpo-frete'] .= !empty($frete_vl) && $frete_vl > 0 ? ''
    // 						. '<tr class="mb5 border-bottom-dotted">'
    // 							. '<td class="ft16px"><label class="imagens-frete frete-' . strtolower( $key ) . '"></label></td>'
    // 							. '<td align="right" nowrap="nowrap" width="2%" class="">'
    // 							. 'R$: <b class="ft16px">' . $txtFreteVl . '</b>'
    // 							. '</td>'
    // 						. '</tr>'
    // 						. '<tr>'
    // 							. '<td colspan="2">'
    // 								. '<span class="show">'
    // 								. 'Prazo de entrega: de ' . ($FRETE[$key]['prazo'] + $prazoDe) . ' até ' . ($FRETE[$key]['prazo'] + $prazoAte) . ' dia(s) úteis'
    // 								. '</span>'
    // 							. '</td>'
    // 						. '</tr>' : '';
    // 				}

    // 			}
    // 			$str['teste'] = $Teste;
    //             $str['corpo-frete'] .= ''
    //                     . '</tbody>'
    //                     . '</table>';
    // 		}

    // 		// $str['msgerro'] = json_encode($r);
    //         die(json_encode($str));
    //     break;

  case 'AviseMeCadastroMe':

    $nome     = isset($POST['nome']) && $POST['nome'] != '' ? (string)$POST['nome'] : null;
    $email     = isset($POST['email']) && $POST['email'] != '' ? (string)$POST['email'] : null;
    $produtos_id = isset($POST['id_produto']) && $POST['id_produto'] != '' ? (int)$POST['id_produto'] : null;

    $New = new ProdutosAviseMe();
    $New->nome = $nome;
    $New->email = $email;
    $New->produtos_id = $produtos_id;
    $New->ip = retornaIpReal();

    if (!empty($nome)  && !empty($email)) {

      if (!$New->save()) {
        $str['aviseme']['error'] = 0;
      } else {
        $str['aviseme']['error'] = 1;
      }
    } else {
      $str['aviseme']['error'] = 0;
    }
    // ENVIAR EMAIL

    exit(json_encode($str));

    break;

  case 'CriarComentario':
    $str['msg'] = '<div class="text-center">';

    $_u  = $POST['_u'];

    $rs = null;
    if (isset($_SESSION['cliente']['id_cliente']) && $_SESSION['cliente']['id_cliente'] != '')
      $rs = (Clientes::first(['conditions' => ['md5(id)=?', $_SESSION['cliente']['id_cliente']]]))->to_array();

    $ProdComentarios = new ProdutosComentarios();
    $ProdComentarios->id_produto = (int)$POST['produto'];
    $ProdComentarios->id_cliente = (int)$rs['id'];
    $ProdComentarios->id_session = SESSION_ID;
    $ProdComentarios->titulocomentario = addslashes($POST['titulo']);
    $ProdComentarios->comentario = nl2br(addslashes($POST['comentario']));
    $ProdComentarios->nota = $POST['nota'];
    $ProdComentarios->data = date('Y-m-d H:i:s');
    $ProdComentarios->save();

    if ($ProdComentarios->id > 0) {
      if (empty($rs['id']) && $rs['id'] == '') {
        $str['msg'] .= ''
          . '<h5>Redirecionando para o login...</h5>'
          . sprintf('<script>window.location.href="%s/identificacao/login?_u=%s"</script>', URL_BASE, $_u);
      } else {
        $str['msg'] .= ''
          . "<div>"
          . "<b>Obrigado por deixar seu comentário<br />Aguarde até que analisamos seu comentário para ser postado no site</b>"
          . "</div>"
          . "<span class='mt15 btn btn-primary' onclick='window.location.reload();'>OK</span>";
      }
    } else {
      $str['msg'] .= 'Desculpe tente novamente!';
    }
    $str['msg'] .= '</div>';
    exit(json_encode($str));
    break;

    // case 'ViewCarrinhoJson' :
    // $json = [];
    // $produtos = [];
    // $Carrinho = Carrinho::all(['conditions' => ['id_session=?', SESSION_ID], 'select' => 'sum(quantidade) as quantidade, id_produto', 'group' => 'id_produto']);
    // foreach( $Carrinho as $rws ) {
    // $produtos = [
    // $rws->id_produto => [
    // 'produto' => $rws->produto->nome_produto,
    // 'cor' => ($rws->produto->nomecor ? sprintf('<span class="mb5 show">%s: <strong>%s</strong></span>', $rws->produto->opc_tipo_a, $rws->produto->nomecor) : null),
    // 'tam' => ($rws->produto->nometamanho ? sprintf('<span class="mb5 show">%s: <strong>%s</strong></span>', $rws->produto->opc_tipo_a, $rws->produto->nometamanho) : null),
    // 'preco_promo' => $rws->produto->preco_promo,
    // 'quantidade' => $rws->quantidade
    // ],
    // ];
    // array_push($json, $produtos);
    // }
    // $data = [
    // 'ninjas' => [
    // 'naruto' => ['name'=>'naruto', 'skills'=>['rasengan', 'kage bunshin']],
    // 'sasuke' => ['name'=>'sasuke', 'skills'=>['chidori', 'sharingan']],
    // 'lee' => ['name'=>'lee', 'skills'=>['leaf hurricane', '5 gates']]
    // ]
    // ];

    // // exit(json_encode($data));

    // exit( json_encode($json) );
    // break;

  case 'ViewCarrinho':

    $str_html .= '<div class="row">';
    $group_nome_produto = '';
    $Carrinho = Carrinho::all(['conditions' => ['id_session=?', SESSION_ID]]);
    foreach ($Carrinho as $rws) {
      if ($group_nome_produto != $rws->produto->nome_produto) {
        $group_nome_produto = $rws->produto->nome_produto;
        $str_html .= '</div><div class="row">';
        $str_html .= sprintf('<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 h5 text-center" data-count="true">%s</div>', $group_nome_produto);
      }

      $str_html .= ''
        . '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ft10px">'
        . '<div class="row">'
        . sprintf('<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><img src="%s" class="img-responsive"></div>', Imgs::src($rws->produto->imagem, 'smalls'))
        . '<div class="col-lg-4 col-md-8 col-sm-6 col-xs-12">'
        . sprintf('<span class="show">%s: <strong>%s</strong></span>', $rws->produto->opc_tipo_a, $rws->produto->nomecor)
        . sprintf('<span class="show">%s: <strong>%s</strong></span>', $rws->produto->opc_tipo_b, $rws->produto->nometamanho)
        . sprintf('<span class="show">QTDE: <strong>%s</strong></span>', $rws->quantidade)
        . '</div>'
        . '</div>'
        . '</div>';
    }
    $str_html .= '</div>';
    $str_html .= !empty($group_nome_produto) ? ''
      . '<div class="row mt15">'
      . '<div class="col-md-12 col-xs-12">'
      . sprintf('<a href="%sidentificacao/carrinho" class="btn btn-primary btn-block">', URL_BASE)
      . '<i class="fa fa-credit-card"></i> '
      . '<span>finalizar compra</span>'
      . '</a>'
      . '</div>'
      . '<div class="col-md-12 col-xs-12 mt5">'
      . '<a href="javascript: void(0);" onclick="return Carrinho.limparCarrinho();" class="btn btn-danger btn-block">'
      . '<i class="fa fa-trash"></i> '
      . '<span>limpar</span>'
      . '</a>'
      . '</div>'
      . '</div>' : '';

    exit($str_html);
    break;

  case 'InserirCarrinho':
  case 'inserirCarrinho':

    Carrinho::delete_all(['conditions' => ['created_at <= (NOW() - intERVAL 180 DAY) and lista_desejos != 1']]);

    $ID = !empty($GET['id']) ? (int)$GET['id'] : 0;
    $ID = !empty($POST['id']) ? (int)$POST['id'] : $ID;

    $ID = !empty($GET['produto_id']) ? (int)$GET['produto_id'] : $ID;
    $ID = !empty($POST['produto_id']) ? (int)$POST['produto_id'] : $ID;

    $QTDE = !empty($GET['estoque_min']) ? (int)$GET['estoque_min'] : 1;
    $QTDE = !empty($POST['estoque_min']) ? (int)$POST['estoque_min'] : $QTDE;

    $CART_DIRECT = !empty($GET['cart_direct']) ? (int)$GET['cart_direct'] : 0;
    $CART_DIRECT = !empty($POST['cart_direct']) ? (int)$POST['cart_direct'] : $CART_DIRECT;

    $PERSONALIZADO_ID = !empty($GET['personalizado_id']) ? (int)$GET['personalizado_id'] : null;
    $PERSONALIZADO_ID = !empty($POST['personalizado_id']) ? (int)$POST['personalizado_id'] : $PERSONALIZADO_ID;

    if (Produtos::count(['conditions' => ['estoque < ? and id = ?', $QTDE, $ID]])) {
      $str['mensage'] = 'Produto indisponivel no momento!<script>AviseMe.tela();</script>';
      echo json_encode($str);
      exit;
    }

    // SETOR ATACADISTA
    $ATACADISTA_QTDE = isset($_SESSION['atacadista']) ? $_SESSION['atacadista'] : null;
    if (count($ATACADISTA_QTDE) > 0) {

      $str['mensage'] = '';
      $rws = new stdClass;
      $group_nome_produto = '';
      $str['mensage'] .= '<div class="row">';
      foreach ($ATACADISTA_QTDE as $id_produto => $qtde) {
        $id = Carrinho::my_save([
          'id' => (Carrinho::first(['conditions' => ['id_session=? and id_produto=?', SESSION_ID, $id_produto]]))->id,
          'id_produto' => $id_produto,
          'id_session' => SESSION_ID,
          'quantidade' => $qtde,
          'cliente_ip' => retornaIpReal(),
          'id_cupom' => 0,
          'frete_tipo' => '',
          'frete_valor' => '0.00',
          'cep' => ''
        ]);

        $rws = Carrinho::find($id['id']);

        if ($group_nome_produto != $rws->produto->nome_produto) {
          $group_nome_produto = $rws->produto->nome_produto;
          $str['mensage'] .= '</div><div class="row">';
          $str['mensage'] .= sprintf('<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 h3 text-center">%s</div>', $group_nome_produto);
        }

        $str['mensage'] .= ''
          . '<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 ft12px">'
          . '<div class="row">'
          . sprintf('<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12"><img src="%s" class="img-responsive"></div>', Imgs::src($rws->produto->imagem, 'smalls'))
          . '<div class="col-lg-8 col-md-8 col-sm-6 col-xs-12">'
          . sprintf('<span class="mb5 show">%s: <strong>%s</strong></span>', $rws->produto->opc_tipo_a, $rws->produto->nomecor)
          . sprintf('<span class="mb5 show">%s: <strong>%s</strong></span>', $rws->produto->opc_tipo_b, $rws->produto->nometamanho)
          . sprintf('<span class="mb5 show">QTDE: <strong>%s</strong></span>', $rws->quantidade)
          . '</div>'
          . '</div>'
          . '</div>';
      }
      $str['mensage'] .= '</div>';
      $str['mensage'] .= ''
        . '<div class="clearfix">'
        . sprintf('<a href="%sprodutos" class="btn btn-primary-default btn-xs btn-block mb15">', URL_BASE)
        . '<i class="fa fa-2x fa-shopping-cart"></i> '
        . '<span class="ft28px">continuar comprando</span>'
        . '</a>'
        . sprintf('<a href="%sidentificacao/carrinho" class="btn btn-primary btn-xs btn-block">', URL_BASE)
        . '<i class="fa fa-2x fa-credit-card"></i> '
        . '<span class="ft28px">finalizar compra</span>'
        . '</a>'
        . '</div>';

      exit(json_encode($str));
    }

    if ($ID == 0) {
      $str['mensage'] = '<font color="#ff5b5b" class="show mt15 text-center">SELECIONE UM TAMANHO</font>';
      exit(json_encode($str));
    }

    /**
     * Cria um json com dados da personalizacao do produto
     */
    $SqlPersonalizado = ''
      . 'SELECT * FROM produtos_personalizado '
      . 'WHERE '
      . 'EXISTS( '
      . 'SELECT 1 FROM produtos '
      . 'WHERE produtos.id=? AND produtos.codigo_id = produtos_personalizado.codigo_id) '
      . 'ORDER BY produtos_personalizado.input_type DESC';

    $data = [];
    $ArrayInputName = $POST;

    $ProdutosPersonalizados = ProdutosPersonalizados::find_by_sql($SqlPersonalizado, [$ID]);
    $ProdutosPersonalizadosCount = (int)count($ProdutosPersonalizados);
    if ($ProdutosPersonalizadosCount > 0) {
      foreach ($ProdutosPersonalizados as $personalize) {
        $personalize = $personalize->to_array();

        // $ArrayKeys = array_keys($personalize);
        $ArrayValue = array_values($personalize);

        foreach ($ArrayValue as $v) {
          if (!empty($ArrayInputName[$v])) {
            $data[$v] = [
              $personalize['input_description'],
              $ArrayInputName[$v]
            ];
          }
        }
      }
    }

    $PERSONALIZADO = null;
    if (!empty($data)) {
      $PERSONALIZADO = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }


    $CountCart = Carrinho::count(['conditions' => ['id_session=? and id_produto=?', SESSION_ID, $ID]]);

    if ($CountCart == 0) {
      $Cart = new Carrinho();
      $Cart->id_session = SESSION_ID;
      $Cart->id_produto = $ID;
      $Cart->quantidade = ($QTDE > 1 ? $QTDE : 1);
      $Cart->cliente_ip = retornaIpReal();
      $Cart->personalizado = $PERSONALIZADO;
      if (!$Cart->save()) {
        $str['mensage'] = 'Não foi possivél inserir o produto no carrinho.';
      }
    } else {
      // $QTDE = (Carrinho::first(['conditions' => ['id_session=? and id_produto=?', SESSION_ID, $ID]]))->quantidade + $QTDE;
      $Cart = Carrinho::first(['conditions' => ['id_session=? and id_produto=?', SESSION_ID, $ID]]);
      $Cart->id_session = SESSION_ID;
      $Cart->id_produto = $ID;
      $Cart->quantidade = $Cart->quantidade + ($QTDE > 1 ? $QTDE : 1);
      $Cart->cliente_ip = retornaIpReal();
      $Cart->personalizado = $PERSONALIZADO;
      if (!$Cart->save()) {
        $str['mensage'] = 'Não foi possivél inserir o produto no carrinho.';
      }
    }

    $rws = Carrinho::find(['conditions' => ['id_produto=?', $ID]]);
    // $uri = isset($rws->carrinho_prod->id_subgrupo) && $rws->carrinho_prod->id_subgrupo > 0
    //   ? sprintf('%sprodutos/%s/%u/%s/%u', URL_BASE, converter_texto($rws->carrinho_prod->grupo), $rws->carrinho_prod->id_grupo, converter_texto($rws->carrinho_prod->subgrupo), $rws->carrinho_prod->id_subgrupo)
    //   : sprintf('%sprodutos/%s/%u', URL_BASE, converter_texto($rws->carrinho_prod->grupo), $rws->carrinho_prod->id_grupo);

    $str['mensage'] .= ''
      . '<div class="clearfix">'
      . '<div class="row mb15">'
      . sprintf('<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bold mb15 text-center"><h3 class="title-produto mt0">%s</h3><p>O que deseja fazer agora!</p></div>', $rws->carrinho_prod->nome_produto)
      . sprintf('<div class="col-lg-5 col-md-5 col-sm-5 col-xs-12"><img src="%s" class="img-responsive center-block"></div>', Imgs::src($rws->carrinho_prod->capa->imagem, 'smalls'))
      . '<div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">'
      . (!empty($rws->carrinho_prod->nomecor) ? sprintf('<span class="show">%s</span>', implode(': ', [$rws->carrinho_prod->opc_tipo_a, $rws->carrinho_prod->nomecor])) : null)
      . (!empty($rws->carrinho_prod->nometamanho) ? sprintf('<span class="show">%s</span>', implode(': ', [$rws->carrinho_prod->opc_tipo_b, $rws->produto->nometamanho])) : null)
      . sprintf('<span class="show">QTDE: <strong>%s</strong></span>', $rws->quantidade)
      . '</div>'
      . '</div>'
      . sprintf('<a href="%s" class="btn btn-primary-default btn-lg btn-block mb5">', URL_BASE . 'produtos')
      . '<i class="fa fa-2x fa-shopping-cart"></i> '
      . '<span class="ft28px">continuar comprando</span>'
      . '</a>'
      . sprintf('<a href="%sidentificacao/carrinho" class="btn btn-primary btn-lg btn-block">', URL_BASE)
      . '<i class="fa fa-2x fa-credit-card"></i> '
      . '<span class="ft28px">finalizar a compra</span>'
      . '</a>'
      . '</div>';


    // Carrinho direct
    if (!empty($CART_DIRECT) && $CART_DIRECT == '1') {
      $str['mensage'] = sprintf(''
        . '<p>Redirecionando para o carrinho...</p>'
        . '<script>'
        . '$(function(){ '
        . 'window.location.href="%sidentificacao/carrinho";'
        . '})'
        . '</script>', URL_BASE);
    } else {
      $str['mensage'] .= sprintf(''
        // . '<p>Produto adicionado ao carrinho...</p>'
        . '<script>'
        . '/*$(function(){ '
        . 'console.log("' . @var_export($CART_DIRECT, true) . '"); '
        . 'setTimeout(function(){ ModalSite.modal("hide"); },550);'
        . 'window.location.href="%sidentificacao/carrinho";'
        . '})*/'
        . '</script>', URL_BASE);
    }

    exit(json_encode($str, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

    break;

  case 'addCarrinho':
    $ID = isset($POST['id']) ? (int)$POST['id'] : '';

    $Carrinho = Carrinho::update_all(
      array(
        'set' => array(
          'quantidade' => (current(Carrinho::all(array('conditions' => array('id_session=? and id_produto=?', SESSION_ID, $ID))))->quantidade + 1),
          'personalizado' => '',
          'cliente_ip' => retornaIpReal(),
          'id_cupom' => 0,
          'frete_tipo' => '',
          'frete_valor' => '0.00',
          'cep' => ''
        ),
        'conditions' => array('id_session=? and id_produto=?', SESSION_ID, $ID)
      )
    );

    if ($Carrinho == 0) {
      $Cart = new Carrinho();
      $Cart->id_session = SESSION_ID;
      $Cart->id_produto = $ID;
      $Cart->quantidade = (current(Carrinho::all(array('conditions' => array('id_session=? and id_produto=?', SESSION_ID, $ID))))->quantidade + 1);
      $Cart->cliente_ip = retornaIpReal();
      $Cart->personalizado = '';
    }

    $str['itens-carrinho'] = 0;
    $str['valor-carrinho'] = 0;
    $str['itens-carrinho-text'] = '0 itens';

    exit(json_encode($str));
    break;

  case 'limparCarrinho':

    $str['carrinho'] = array();

    $str['produto'] = array();

    $str['carrinho']['itens-carrinho'] = '0';

    $str['carrinho']['itens-carrinho-text'] = '0 itens';

    $str['carrinho']['valor-carrinho'] = '0,00';

    $result = Produtos::find_by_sql('select id_produto from carrinho where id_session = ?', array(SESSION_ID));

    foreach ($result as $r) {
      array_push($str['produto'], $r);
    }

    if (!empty($_SESSION['atacadista']))
      unset($_SESSION['atacadista']);

    Carrinho::delete_all(array('conditions' => array('id_session=?', SESSION_ID)));

    exit(json_encode($str));

    break;

    // case 'InserirClicks':
    // $href = end(explode("/","{$POST['href']}"));
    // if( mysqli_query($conexao,sprintf("insert into clientes_click (id_session, id_produto, ip, data_criacao) values ('%s','%u','%s', now())",
    // string_escape(SESSION_ID),
    // string_escape($href),
    // string_escape(retornaIpReal())
    // )) )
    // $str['erro'] = "0";
    // else
    // $str['erro'] = "1";
    // die(json_encode($str));
    // break;
}
