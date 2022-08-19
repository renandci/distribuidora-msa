<?php

use PhpSigep\Model\Exception;

require_once '../settings.php';
require_once PATH_ROOT . 'app/vendor/autoload.php';
require_once PATH_ROOT . 'app/settings-config.php';
require_once PATH_ROOT . 'assets/' . ASSETS .  '/settings.php';
require_once PATH_ROOT . 'adm/correios/correios-bootstrap.php';
require_once PATH_ROOT . 'app/includes/bibli-funcoes.php';

try {
  $Cliente = new stdClass;
  $Frete = new stdClass;
  $Frete->altura = 0;
  $Frete->largura = 0;
  $Frete->comprimento = 0;
  $Frete->peso = 0;
  $ClienteIdEndereco = 0;
  if (isset($_SESSION['cliente']['id_cliente']) && $_SESSION['cliente']['id_cliente'] != null) {
    $Cliente = Clientes::first(['conditions' => ['md5(id)=?', $_SESSION['cliente']['id_cliente']]]);
    $ClienteCep = $Cliente->endereco->cep;
    $ClienteIdEndereco = $Cliente->endereco->id;
  }

  $CEP = soNumero(filter_input(INPUT_GET, 'produto_cep') ?? $ClienteCep ?? '15905-088');
  $ID_PRODUTO[] = filter_input(INPUT_GET, 'produto_id') ?? 0;
  $AcaoCalcularFrete = filter_input(INPUT_GET, 'acao') ?? null;
  $AcaoCalcularFrete = $AcaoCalcularFrete == 'CalcularFrete' ? 'CalcularFrete' : 'CalcularFreteCarrinho';
  // Implements
  // Isso é para o usuario burro que diz que não está selecionando o frete
  $ImplementsFunction = $AcaoCalcularFrete == 'CalcularFreteCarrinho' ? 'onclick="Checkout.atualizar_carrinho(this);"' : '';

  $FRETE = null;
  $SIGLA = null;
  $RETIRA = null;
  $GRATIS = false;
  $FRETE_VL = 0;
  $VALOR_FRETE = 0;
  $TOTAL_CARRINHO = 0;
  $POSTAGEMS = null;
  $qtde = null;

  // Carregar os produtos
  if ($AcaoCalcularFrete == 'CalcularFreteCarrinho') {
    $ID_PRODUTO = null;
    $Carrinho = Carrinho::all(['conditions' => ['loja_id=? and id_session=?', $CONFIG['loja_id'], session_id()]]);
    if ($CarrinhoCount = count($Carrinho) > 0) {
      foreach ($Carrinho as $cart) {
        $ID_PRODUTO[] = $cart->id_produto;
        $qtde[$cart->id_produto] = $cart->quantidade;
      }
    }
  }

  // Carregar os produtos ou apenas 1 unico produto
  $Produtos = Produtos::all(['conditions' => ['id in(?)', $ID_PRODUTO]]);
  foreach ($Produtos as $prod) {
    // Capturar a classe do produto
    $frete = $prod->freteproduto;

    // Soma os pesos
    $Frete->peso += $frete->peso;

    // Verifica os dados para pegar os maiores
    if ($frete->altura > $Frete->altura)
      $Frete->altura = $frete->altura;

    // Verifica os dados para pegar os maiores
    if ($frete->largura > $Frete->largura)
      $Frete->largura = $frete->largura;

    // Verifica os dados para pegar os maiores
    if ($frete->comprimento > $Frete->comprimento)
      $Frete->comprimento = $frete->comprimento;

    // Dados das postagens
    if (
      !empty($prod->marca->disponib_entrega) &&
      empty($prod->postagem)
    )
      $POSTAGEMS .= ', ' . $prod->marca->disponib_entrega;
    else
      $POSTAGEMS .= ', ' . $prod->postagem;

    // Soma Total
    $qtde_test = isset($qtde[$prod->id]) ? $qtde[$prod->id] : 1;
    $TOTAL_CARRINHO += $prod->preco_promo * $qtde_test;
  }

  // $PRAZOS = implode(',', $POSTAGEMS);
  $PRAZOS = ltrim($POSTAGEMS, ',');
  $PRAZOS = preg_replace('/[^0-9]+/', ',', $PRAZOS);
  $PRAZOS = preg_replace('/(,)\1+/', '$1', $PRAZOS);
  $PRAZOS = explode(',', $PRAZOS);
  $PRAZOS = array_filter($PRAZOS);
  $PRAZOS = array_slice($PRAZOS, -2);
  asort($PRAZOS);

  $PRAZO_DE = isset($PRAZOS[0], $PRAZOS[1]) ? $PRAZOS[0] : 1;
  $PRAZO_ATE = isset($PRAZOS[1]) ? $PRAZOS[1] : $PRAZOS[0];

  // printf('<pre>PRAZO_DE %s</pre>', print_r($PRAZO_DE, 1));
  // printf('<pre>PRAZO_ATE %s</pre>', print_r($PRAZO_ATE, 1));
  // printf('<pre>%s</pre>', print_r($PRAZOS, 1));
  // printf('<pre>%s</pre>', print_r($POSTAGEMS, 1));

  // Carregar os dados da JadLog
  $JadLog = JadLog::first(['conditions' => ['loja_id=?', $CONFIG['loja_id']]]);

  // Carregar os fretes
  $ConfiguracoesFretesEnvios = ConfiguracoesFretesEnvios::first(['conditions' => ['loja_id=?', $CONFIG['loja_id']]]);

  // Carregar os dados para frete gratis
  $ConfiguracoesFretesGratis = ConfiguracoesFretesGratis::all(['conditions' => ['loja_id=?', $CONFIG['loja_id']]]);


  $ConsultaCep = new PhpSigep\Services\SoapClient\Real();
  $ResultConsultaCep = $ConsultaCep->consultaCep($CEP);
  // print_r($ResultConsultaCep);
  // die();
  if ($ResultConsultaCep->getErrorMsg())
    throw new Exception($ResultConsultaCep->getErrorMsg());


  $ENDERECO = @$ResultConsultaCep->getResult()->getEndereco();
  $BAIRRO = @$ResultConsultaCep->getResult()->getBairro();
  $CIDADE = $ResultConsultaCep->getResult()->getCidade();
  $UF = $ResultConsultaCep->getResult()->getUf();

  $FRETE_RETIRADA = null;
  // $FRETE['PAC'] = [
  // 	'prazo' => '1',
  // 	'valor' => '20.00'
  // ];

  // Verificacao para o frete ser gratis no caso
  $ConfiguracoesFretesGratisCoun = (int)count($ConfiguracoesFretesGratis);
  if ($ConfiguracoesFretesGratisCoun > 0) {
    foreach ($ConfiguracoesFretesGratis as $rws) {
      if (($CEP >= $rws->cep_ini && $CEP <= $rws->cep_fin) && ($rws->frete_valor <= $TOTAL_CARRINHO) && $rws->retirada != 2) {
        $VALOR_FRETE = $rws->frete_valor;
        $GRATIS = true;
        if ($rws->retirada) $RETIRA = $rws->descricao;
        break;
      } else if ($rws->uf == $UF && ($rws->frete_valor <= $TOTAL_CARRINHO) && $rws->retirada != 2) {
        $VALOR_FRETE = $rws->frete_valor;
        $GRATIS = true;
        if ($rws->retirada) $RETIRA = $rws->descricao;
        break;
      } else {
        $VALOR_FRETE = $rws->frete_valor;
      }
    }

    // Para frete de personalizado
    foreach ($ConfiguracoesFretesGratis as $rws2) {
      if ($rws2->retirada == 2 && ($CEP >= $rws2->cep_ini && $CEP <= $rws2->cep_fin)) {
        $FRETE['ENTREGA-ESPECIAL'] = [
          'prazo' => $rws2->dias,
          'valor' => $rws2->frete_valor
        ];
        $FRETE_RETIRADA = true;
      }
    }
  }

  $Cubagem = round(($Frete->altura * $Frete->largura * $Frete->comprimento) ** (1 / 3), 1);

  // Adiciona os dados somente para os correios
  $envios_correios = null;
  if ($CountCorreios = count($ConfiguracoesFretesEnvios->envios_correios) > 0)
    foreach ($ConfiguracoesFretesEnvios->envios_correios as $int) {
      if ($int > 0)
        $envios_correios[] = new \PhpSigep\Model\ServicoDePostagem($int);
    }

  $count_correios = (int)count($envios_correios);

  if ($count_correios == 0 && $FRETE_RETIRADA == false) {
    $FRETE_ALL = calcular_preco_frete('PAC|SEDEX', $CONFIG['cep'], $CEP, $Frete->peso, $Cubagem, $Cubagem, $Cubagem);
    foreach ($FRETE_ALL as $k => $servico) {
      $codigo   = trim($servico['Codigo']);
      // $descricao 	= $servico->modalidade;
      $valor_br     = number_format($servico['valor'], 2, ',', '.');
      $valor_us     = $servico['valor'];
      $entrega      = $servico['prazo'];
      $entrega_1    = $entrega + $PRAZO_DE;
      $entrega_2    = $entrega + $PRAZO_ATE;
      $entrega_text = sprintf('Prazo de entrega: de %u à %u dia(s) úteis', $entrega_1, $entrega_2);

      // $SIGLA = strtoupper((explode(' ', $descricao))[0]);

      $FRETE[$k] = [
        'prazo' => $entrega_text,
        'prazo_int' => $servico['valor'],
        'valor' => $valor_us
      ];
    }
  }

  $CountCorreios = (int)count($ConfiguracoesFretesEnvios->envios_correios);

  if ($CountCorreios > 0 && $FRETE_RETIRADA == false) {

    $Dimensao = new \PhpSigep\Model\Dimensao();
    $Dimensao->setTipo(\PhpSigep\Model\Dimensao::TIPO_PACOTE_CAIXA);
    $Dimensao->setAltura($Cubagem); // em centímetros
    $Dimensao->setLargura($Cubagem); // em centímetros
    $Dimensao->setComprimento($Cubagem); // em centímetros


    $CalcPrecoPrazo = new \PhpSigep\Model\CalcPrecoPrazo();
    $CalcPrecoPrazo->setAccessData($AccessDataCorreios);

    $CalcPrecoPrazo->setCepOrigem(soNumero($CONFIG['cep']));
    $CalcPrecoPrazo->setCepDestino(soNumero($CEP));

    $CalcPrecoPrazo->setServicosPostagem($envios_correios);
    $CalcPrecoPrazo->setAjustarDimensaoMinima(true);
    $CalcPrecoPrazo->setDimensao($Dimensao);
    $CalcPrecoPrazo->setPeso($Frete->peso);

    $CalcPrecoPrazoReal = new PhpSigep\Services\SoapClient\Real();
    $ResultCalcPrecoPrazo = $CalcPrecoPrazoReal->calcPrecoPrazo($CalcPrecoPrazo);
    $ResultCalcPrecoPrazo = $ResultCalcPrecoPrazo->getResult();
    $ResultCalcPrecoPrazoCount = (int)count($ResultCalcPrecoPrazo);

    if ($ResultCalcPrecoPrazoCount > 0) {
      foreach ($ResultCalcPrecoPrazo as $servico) {
        $codigo = trim($servico->getServico()->getCodigo());
        $descricao = trim($servico->getServico()->getNome());
        $valor_br = number_format($servico->getValor(), 2, ',', '.');
        $valor_us = $servico->getValor();
        $entrega = $servico->getPrazoEntrega();

        $entrega_1 = $entrega + $PRAZO_DE;
        $entrega_2 = $entrega + $PRAZO_ATE;
        $entrega_text = sprintf('Prazo de entrega: de %u à %u dia(s) úteis', $entrega_1, $entrega_2);

        $SIGLA = strtoupper((explode(' ', $descricao))[0]);

        $FRETE[$SIGLA] = [
          'prazo' => $entrega_text,
          'prazo_int' => $entrega,
          'valor' => $valor_us
        ];
      }
    }
    unset($ResultCalcPrecoPrazo);
  }

  $CountJadLog = (int)count($ConfiguracoesFretesEnvios->envios_jadlog);
  $envios_jadlog = null;
  if ($CountJadLog > 0 && $FRETE_RETIRADA == false) {
    foreach ($ConfiguracoesFretesEnvios->envios_jadlog as $modalidade) {
      if (!empty($modalidade))
        $envios_jadlog[] = [
          'cepori' => soNumero($CONFIG['cep']),
          'cepdes' => soNumero($CEP),
          'frap' => null,
          'peso' => $Frete->peso,
          'conta' => $JadLog->contacorrente,
          'contrato' => $JadLog->nrcontrato,
          'modalidade' => $modalidade,
          'tpentrega' => 'D',
          'tpseguro' => 'N',
          'vldeclarado' => 0,
          'vlcoleta' => $JadLog->vlcoleta
        ];
    }
  }

  $count_jadlog = (int)count($envios_jadlog);
  if ($count_jadlog > 0 && $FRETE_RETIRADA == false) {

    $JadLogApi = new JadLogNew($CONFIG['jadlog']['token']);
    $ResultCalcPrecoPrazo = $JadLogApi->post('/frete/valor', ['frete' => $envios_jadlog]);
    $ReturnCalcPrecoPrazoLoop = $ResultCalcPrecoPrazo['body']->frete;

    if (count($ReturnCalcPrecoPrazoLoop) > 0)
      foreach ($ReturnCalcPrecoPrazoLoop as $servico) {

        // $codigo 	= trim($servico->getServico()->getCodigo());
        $descricao   = $servico->modalidade;
        $valor_br   = number_format($servico->vltotal, 2, ',', '.');
        $valor_us   = $servico->vltotal;
        $entrega   = $servico->prazo;
        $entrega_1   = $entrega + $PRAZO_DE;
        $entrega_2   = $entrega + $PRAZO_ATE;
        $entrega_text = sprintf('Prazo de entrega: de %u à %u dia(s) úteis', $entrega_1, $entrega_2);

        $SIGLA = strtoupper((explode(' ', $descricao))[0]);

        $FRETE[$SIGLA] = [
          'prazo' => $entrega_text,
          'prazo_int' => $entrega,
          'valor' => $valor_us
        ];
      }
  }

  $GRATIS_MSG = ($VALOR_FRETE - $TOTAL_CARRINHO) >= 0
    ? sprintf('Falta apenas <b class="color-004">R$: %s</b> ', number_format(($VALOR_FRETE - $TOTAL_CARRINHO), 2, ',', '.'))
    . sprintf('para você ter frete grátis, <a href="/produtos?sc=%s" class="color-004 text-underline font-bold">clique aqui</a> para continuar comprando', session_id()) : '';
?>
  <div id="recarregar-frete" class="table-responsive">
    <style>
      .icon-by-frete {
        vertical-align: bottom;
        background-color: transparent;
        display: inline-block;
        height: 35px;
        line-height: 40px;
        font-size: 80px;
        overflow: hidden;
      }

      #formulario-frete .input-radio+label:hover:before,
      #formulario-frete .input-radio:checked+label:before {
        content: '\f058';
      }
    </style>
    <table cellpadding="5" cellspacing="0" border="0" width="100%">
      <?php if ($AcaoCalcularFrete == 'CalcularFrete') { ?>
        <thead>
          <tr>
            <th colspan="2">
              <strong class="ft20px show"><?php echo $CIDADE ?> - <?php echo $UF ?></strong>
              <small><?php echo $ENDERECO ?></small>
            </th>
          </tr>
        </thead>
      <?php } ?>
      <tbody>

        <!--[SOMENTE PARA FRETE GRÁTIS]-->
        <?php if (!$RETIRA && !$CONFIG['atacadista'] && $GRATIS > 0) { ?>
          <tr style="border-top: dotted 1px #ccc;">
            <td nowrap="nowrap" width="1%">
              <input type="radio" name="frete" id="GRATIS" value="<?php echo $POST['id'] ?>" class="input-radio" data-valor="0.00" data-gratis="<?php echo htmlspecialchars($GRATIS_MSG, ENT_QUOTES) ?>" <?php echo $ImplementsFunction ?> />
              <label for="GRATIS" class="fa ft22px"></label>
              <label class="imagens-frete frete-gratis"></label>
            </td>
            <td align="right">
              <span class="show color-004 ft18px">Frete Grátis</span>
              <span class="show black-30 ft14px">
                <?php
                $prazo_test = end(array_filter($FRETE, function ($r) {
                  return ($r['valor'] != 0);
                }));

                $gratis_text = !empty($FRETE['PAC']['prazo']) ? $FRETE['PAC']['prazo'] : $prazo_test;
                echo $gratis_text;
                ?>
              </span>
            </td>
          </tr>
        <?php } ?>

        <!--[FRETE GRÁTIS COM RETIRADA EM MÃOS]-->
        <?php if ($RETIRA != null && $GRATIS > 0 && !$CONFIG['atacadista']) { ?>
          <tr style="border-top: dotted 1px #ccc;">
            <td nowrap="nowrap" width="1%">
              <input type="radio" name="frete" id="RETIRADA-EM-MAOS" value="<?php echo $POST['id'] ?>" class="input-radio" data-valor="0.00" data-gratis="<?php echo $RETIRA ?>" <?php echo $ImplementsFunction ?> />
              <label for="RETIRADA-EM-MAOS" class="fa ft22px"></label>
              <label class="imagens-frete frete-retirada"></label>
            </td>
            <td align="right">
              <span class="show color-004 ft18px">RETIRADA EM MÃOS</span>
              <span class="show black-30 ft14px text-left">
                <?php echo $RETIRA ?>
              </span>
            </td>
          </tr>
        <?php } ?>


        <!-- <?php if (($GRATIS > 0 && !$CONFIG['atacadista']) || ($CONFIG['cep'] == $CEP)) { ?>
          <tr style="border-top: dotted 1px #ccc;">
            <td nowrap="nowrap" width="1%">
              <input type="radio" name="frete" id="GRATIS" value="<?php echo $POST['id'] ?>" class="input-radio" data-valor="0.00" data-gratis="<?php echo htmlspecialchars($GRATIS_MSG, ENT_QUOTES) ?>" <?php echo $ImplementsFunction ?> />
              <label for="GRATIS" class="fa ft22px"></label>
              <label class="imagens-frete frete-gratis"></label>
            </td>
            <td align="right">
              <span class="show color-004 ft18px">Frete Grátis</span>
              <span class="show black-30 ft14px">
                <?php
                $gratis_text = !empty($FRETE['PAC']['prazo']) ? $FRETE['PAC']['prazo'] : null;
                echo $gratis_text;
                ?>
              </span>
            </td>
          </tr>
        <?php } ?> -->

        <!-- NOTA: string $GRATIS é para mostrar frete, sim ou não, no caso está para geral -->
        <?php /* if($GRATIS == 0) */
        if (count($FRETE) > 0)
          foreach ($FRETE as $key => $values) { ?>
          <?php
            // verifica a existencia de subsidiar o valor sobre o total final
            print_r('teste  '  . $CONFIG['fretes_sob_vl']);
            if ($CONFIG['fretes_sob_vl'] == 1) {
              if ($CONFIG['fretes_tipo'] == '%') {
                $FRETE_VL = $TOTAL_CARRINHO - desconto_boleto($TOTAL_CARRINHO, $CONFIG['fretes_valor']);
              } else {
                $FRETE_VL = ($TOTAL_CARRINHO - $CONFIG['fretes_valor']);
              }

              $FRETE_VL = $values['valor'] - $FRETE_VL;
            } else {
              $FRETE_VL = ($CONFIG['fretes_tipo'] == '%' ? desconto_boleto($values['valor'], $CONFIG['fretes_valor']) : ($values['valor'] - $CONFIG['fretes_valor']));
            }
            // print_r($FRETE_VL);
            // die();


            // Somente jadolog
            $key = str_replace(['3', '5', '9', '40'], ['JADLOG-PACKAGE', 'JADLOG', 'JADLOG-COM', 'JADLOG-ECONOMICO'], $key);
          ?>
          <tr style="border-top: dotted 1px #ccc;<?php echo (empty($FRETE_VL) ? 'display:none' : '') ?>">
            <td nowrap="nowrap" width="1%">
              <input type="radio" name="frete" id="<?php echo $key ?>" value="<?php echo $POST['id'] ?>" class="input-radio" data-valor="<?php echo $FRETE_VL ?>" data-gratis="<?php echo htmlspecialchars($GRATIS_MSG, ENT_QUOTES) ?>" <?php echo $ImplementsFunction ?> />
              <label for="<?php echo $key ?>" class="fa ft22px"></label>
              <label class="imagens-frete frete-<?php echo strtolower($key) ?>"></label>
              <!-- <i class="icon-by-frete icon-<?php echo strtolower($key) ?>"></i> -->
            </td>
            <td align="right">
              <span class="show color-004 ft18px">Valor R$: <?php echo number_format($FRETE_VL, 2, ',', '.') ?></span>
              <span class="show black-30 ft14px">
                <?php echo $values['prazo'] ?>
              </span>
            </td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
    <script>
      console.log("Selecione os fretes!");
      $("#finalizar-pedido").removeClass("hidden").fadeIn(0);
      $("input[data-frete]").val("");
    </script>
  </div>
<?php
} catch (Exception $e) {
?>
  <div id="recarregar-frete" class="table-responsive">
    Desculpe, não foi possivel calcular o frete, tente novamente ou tente recarregar a página.
    <script>
      console.log("Selecione os fretes Error!", "<?php echo print_r($e, 1) ?>");
      $("#finalizar-pedido").removeClass("hidden").fadeIn(0);
      $("input[data-frete]").val("");
    </script>
  </div>
<?php
  // printf('<pre>%s</pre>', print_r($e, 1));
}
