<?php

/**
 * Class deve se implementar botoes dentro da view
 * Buttons definidos <b>Compar|Espiar|Avise me</b>
 *
 */
class HelperHtml
{

  public static function make_cmp(array $sortValues)
  {
    return function ($a, $b) use (&$sortValues) {
      foreach ($sortValues as $column => $sortDir) {
        $diff = strcmp($a[$column], $b[$column]);
        if ($diff !== 0) {
          if ('asc' === $sortDir) {
            return $diff;
          }
          return $diff * -1;
        }
      }
      return 0;
    };
  }

  // public static function make_menus($array = null) {
  //     $gp   = null;
  //     $sbgp = null;
  //     $trtgp = null;

  //     $countArray = (int)count($array);
  //     if($countArray > 0) foreach( $array as $rws )
  // 	{
  //         if( $rws['sbgp']['parent']['parent_id'] > 0 )
  //         {
  //             foreach( $rws['sbgp']['parent']['children'] as $test )
  //             {
  //                 $trtgp[$rws['subgrupos_id']][$test['id']] = [
  //                     'subgrupos_id' 		    => $test['sbgp']['parent']['parent_id'],
  //                     'tetragrupos_id' 		=> $test['id'],
  //                     'tetragrupos' 			=> $test['subgrupo'],
  //                     'tetragrupos_description'=> $test['subgrupo_description'],
  //                     'tetragrupos_keywords'	=> $test['subgrupo_keywords'],
  //                     'tetragrupos_icon'		=> null,
  //                     'tetragrupos_ordem'		=> $test['ordem'],
  //                     'tetragrupos_excluir'	=> $test['excluir'],
  //                     'produto_subgrupo_id'	=> $test['produto_subgrupo_id'],
  //                 ];
  //             }

  //             if( ! empty( $trtgp[$rws['subgrupos_id']] ) ) {
  //                 uasort($trtgp[$rws['subgrupos_id']], make_cmp(['tetragrupos' => 'asc', 'tetragrupos_ordem' => 'asc']));
  //             }

  //             $sbgp[$rws['grupo_id']][$rws['sbgp']['parent']['id']] = [
  //                 'grupo_id' 			    => $rws['grupo_id'],
  //                 'subgrupos_id' 		    => $rws['sbgp']['parent']['id'],
  //                 'parent_id' 		    => $rws['sbgp']['parent']['parent_id'],
  //                 'subgrupos' 			=> $rws['sbgp']['parent']['subgrupo'],
  //                 'subgrupo_description'  => $rws['sbgp']['parent']['subgrupo_description'],
  //                 'subgrupo_keywords'	    => $rws['sbgp']['parent']['subgrupo_keywords'],
  //                 'subgrupo_icon'		    => null,
  //                 'subgrupo_ordem'		=> $rws['sbgp']['parent']['ordem'],
  //                 'subgrupo_excluir'		=> $rws['sbgp']['parent']['excluir'],
  //                 'produto_subgrupo_id'	=> $rws['sbgp']['parent']['produto_subgrupo_id'],
  //                 'tetragrupo' 		    => $trtgp[$rws['sbgp']['id']]
  //             ];
  //         }
  //         else
  //         {
  //             if( $rws['subgrupos_id'] > 0 )
  //             {
  //                 $sbgp[$rws['grupo_id']][$rws['subgrupos_id']] = [
  //                     'grupo_id' 			    => $rws['grupo_id'],
  //                     'subgrupos_id' 		    => $rws['subgrupos_id'],
  //                     'parent_id' 		    => $rws['sbgp']['parent_id'],
  //                     'subgrupos' 			=> $rws['sbgp']['subgrupo'],
  //                     'subgrupos_description' => $rws['sbgp']['subgrupo_description'],
  //                     'subgrupos_keywords'	=> $rws['sbgp']['subgrupo_keywords'],
  //                     'subgrupos_icon'		=> null,
  //                     'subgrupos_ordem'		=> $rws['sbgp']['ordem'],
  //                     'subgrupos_excluir'		=> $rws['sbgp']['excluir'],
  //                     'produto_subgrupo_id'	=> $rws['sbgp']['produto_subgrupo_id'],
  //                     'tetragrupo' 		    => $trtgp[$rws['subgrupos_id']]
  //                 ];
  //             }
  //         }

  //         if( ! empty($sbgp[$rws['grupo_id']]) ) {
  //             uasort($sbgp[$rws['grupo_id']], make_cmp(['subgrupos' => 'asc', 'subgrupos_ordem' => 'asc']));
  //         }

  //         $gp[$rws['grupo_id']] = [
  //             'grupo_id' 			=> $rws['grupo_id'],
  //             'subgrupos_id' 		=> $rws['subgrupos_id'],
  //             'grupo' 			=> $rws['gp']['grupo'],
  //             'grupo_description' => $rws['gp']['grupo_description'],
  //             'grupo_keywords'	=> $rws['gp']['grupo_keywords'],
  //             'grupo_icon'		=> $rws['gp']['grupo_icon'],
  //             'grupo_ordem'		=> $rws['gp']['ordem'],
  //             'grupo_excluir'		=> $rws['gp']['excluir'],
  //             'produto_grupo_id'	=> $rws['gp']['produto_grupo_id'],
  //             'subgrupos' 		=> $sbgp[$rws['grupo_id']]
  // 		];
  // 		// remove menus
  // 		if( $gp[$rws['grupo_id']]['grupo_excluir'] == 1 ) {
  // 			unset($gp[$rws['grupo_id']]);
  // 		}
  //     }

  //     if( count($gp) == 0 ) return [];

  //     uasort($gp, make_cmp(['grupo_ordem' => 'asc', 'grupo' => 'asc']));

  //     $a['grupos'] = !empty($gp) ? $gp:[];
  //     return $a;
  // }

  public static function make_menus($array = null)
  {
    global $CONFIG;

    $ProdutosMenus = ProdutosMenus::all([
      'conditions' => [
        'produtos_menus.loja_id=?', $CONFIG['loja_id']
        // 'produtos_menus.loja_id=? and produtos_menus.codigo_id in(SELECT DISTINCT p.codigo_id FROM produtos p WHERE p.loja_id=? and p.excluir=0 AND p.status=0 AND p.id_marca IN(SELECT m.id FROM marcas m WHERE m.loja_id=? and m.excluir=0))',
        // $CONFIG['loja_id'],
        // $CONFIG['loja_id'],
        // $CONFIG['loja_id']
      ],
      'select' => ''
        . 'produtos_menus.loja_id, '
        . 'produtos_menus.id_grupo, '
        . 'produtos_menus.id_subgrupo, '

        . 'grupos.produto_grupo_id, '
        . 'grupos.grupo, '
        . 'grupos.grupo_description, '
        . 'grupos.grupo_keywords, '
        . 'grupos.grupo_icon, '
        . 'grupos.ordem, '
        . 'grupos.excluir, '

        . 'subgrupos.parent_id, '
        . 'subgrupos.subgrupo, '
        . 'subgrupos.subgrupo_description, '
        . 'subgrupos.subgrupo_keywords, '
        . 'subgrupos.produto_subgrupo_id, '
        . 'subgrupos.ordem as ordem2 '
        . '',

      'joins' => ''
        . 'JOIN grupos ON grupos.id = produtos_menus.id_grupo and grupos.excluir = 0 '
        . 'JOIN subgrupos ON subgrupos.id = produtos_menus.id_subgrupo '
        . 'JOIN produtos ON produtos.codigo_id = produtos_menus.codigo_id and produtos.excluir = 0 and produtos.status = 0 '
        . 'JOIN marcas ON marcas.id = produtos.id_marca and marcas.excluir = 0 '
    ]);

    // echo ProdutosMenus::connection()->last_query;
    // die();
    $gp   = null;
    $sbgp = null;
    $trtgp = null;

    $ProdutosMenusCount = (int)count($ProdutosMenus);
    if ($ProdutosMenusCount > 0)
      foreach ($ProdutosMenus as $rws) {
        // if ($rws->subgrupo->parent_id > 0) {
        //   foreach ($rws->subgrupo->parent->children as $test) {
        //     $trtgp[$rws->id_subgrupo][$test->id] = [
        //       'subgrupos_id'         => $test->subgrupo->parent->parent_id,
        //       'tetragrupos_id'     => $test->id,
        //       'tetragrupos'       => $test->subgrupo,
        //       'tetragrupos_description' => $test->subgrupo_description,
        //       'tetragrupos_keywords'  => $test->subgrupo_keywords,
        //       'tetragrupos_icon'    => null,
        //       'tetragrupos_ordem'    => $test->ordem,
        //       'tetragrupos_excluir'  => $test->excluir,
        //       'produto_subgrupo_id'  => $test->produto_subgrupo_id,
        //     ];
        //   }

        //   if (!empty($trtgp[$rws->id_subgrupo])) uasort($trtgp[$rws->id_subgrupo], make_cmp(['tetragrupos' => 'asc', 'tetragrupos_ordem' => 'asc']));

        //   $sbgp[$rws->id_grupo][$rws->subgrupo->parent->id] = [
        //     'grupo_id'           => $rws->id_grupo,
        //     'subgrupos_id'         => $rws->subgrupo->parent->id,
        //     'parent_id'         => $rws->subgrupo->parent->parent_id,
        //     'subgrupos'       => $rws->subgrupo->parent->subgrupo,
        //     'subgrupo_description'  => $rws->subgrupo->parent->subgrupo_description,
        //     'subgrupo_keywords'      => $rws->subgrupo->parent->subgrupo_keywords,
        //     'subgrupo_icon'        => null,
        //     'subgrupo_ordem'    => $rws->subgrupo->parent->ordem,
        //     'subgrupo_excluir'    => $rws->subgrupo->parent->excluir,
        //     'produto_subgrupo_id'  => $rws->subgrupo->parent->produto_subgrupo_id,
        //     'tetragrupo'         => $trtgp[$rws->id_subgrupo]
        //   ];
        // } else {
        $sbgp[$rws->id_grupo][$rws->id_subgrupo] = [
          'grupo_id' => $rws->id_grupo,
          'subgrupos_id' => $rws->id_subgrupo,
          'parent_id' => $rws->parent_id,
          'subgrupos' => $rws->subgrupo,
          'subgrupos_description' => $rws->subgrupo_description,
          'subgrupos_keywords' => $rws->subgrupo_keywords,
          'subgrupos_icon' => null,
          'subgrupos_ordem' => $rws->ordem,
          'subgrupos_excluir' => $rws->excluir,
          'produto_subgrupo_id' => $rws->produto_subgrupo_id,
          'tetragrupo' => $trtgp[$rws->id_subgrupo]
        ];

        if ($sbgp[$rws->id_grupo][$rws->id_subgrupo]['subgrupos_excluir'] == 1 || $rws->id_subgrupo == 0) unset($sbgp[$rws->id_grupo][$rws->id_subgrupo]);

        if (!empty($sbgp[$rws->id_grupo])) uasort($sbgp[$rws->id_grupo], self::make_cmp(['subgrupos' => 'asc', 'subgrupos_ordem' => 'asc']));
        // }

        $gp[$rws->id_grupo] = [
          'grupo_id' => $rws->id_grupo,
          'subgrupos_id' => $rws->id_subgrupo,
          'grupo' => $rws->grupo,
          'grupo_description' => $rws->grupo_description,
          'grupo_keywords' => $rws->grupo_keywords,
          'grupo_icon' => $rws->grupo_icon,
          'grupo_ordem' => $rws->ordem,
          'grupo_excluir' => $rws->excluir,
          'produto_grupo_id' => $rws->produto_grupo_id,
          'subgrupos' => $sbgp[$rws->id_grupo]
        ];

        if ($gp[$rws->id_grupo]['grupo_excluir'] == 1 || $rws->id_grupo == 0) unset($gp[$rws->id_grupo]);
      }

    $gpCount = (int)count($gp);
    if ($gpCount == 0) return [];

    uasort($gp, self::make_cmp(['grupo_ordem' => 'asc', 'grupo' => 'asc']));

    $a['grupos'] = $gpCount > 0 ? $gp : [];

    return $a;
  }

  /**
   * Modelo de Menus contendo até 3 grupos referenciado com o subgrupo do site.
   */
  public static function template_menus($menus = null, $at = false)
  {

    $count = (int)count($menus['grupos']);

    $width = $count > 0 ? sprintf('%s%%', (100 / $count)) : 'auto';

    foreach ($menus['grupos'] as $grupo) { ?>

      <li class="lista-menu-topo" style="width: <?= $width ?>;">
        <a href="/produtos/<?= converter_texto($grupo['grupo']); ?>/<?= $grupo['grupo_id'] ?>" class="link-menu-topo<?= (!empty($at) && $at == $grupo['id']) ? ' add-link-menu-topo' : ''; ?>" vitrine-id="<?= $grupo['id_grupos_produtos'] ?>">
          <?= $grupo['grupo_icon'] ? sprintf('<font class="%s"></font>', $grupo['grupo_icon']) : ''; ?>
          <?= $grupo['grupo_icon'] ? sprintf('<font>%s</font>', $grupo['grupo']) : $grupo['grupo']; ?>
        </a>
        <?php
        $subgrupos_count = (int)count($grupo['subgrupos']);
        if ($subgrupos_count > 0) { ?>
          <ul class="lista-submenus-topo">
            <li class="col-lg-9 col-md-9 col-sm-8">
              <div class="row">
                <?php
                $total_submenus = count($grupo['subgrupos']);
                if ($total_submenus > 12) {
                  $lg = 4;
                  $md = 4;
                  $sm = 4;
                  array_chunk($grupo['subgrupos'], 4);
                } else if ($total_submenus > 5 && $total_submenus < 12) {
                  $lg = 6;
                  $md = 6;
                  $sm = 6;
                  array_chunk($grupo['subgrupos'], 2);
                } else {
                  $lg = 12;
                  $md = 12;
                  $sm = 12;
                  array_chunk($grupo['subgrupos'], 1);
                }

                foreach ($grupo['subgrupos'] as $subgrupo) { ?>
                  <div class="col-lg-<?= $lg ?> col-md-<?= $md ?> col-sm-<?= $sm ?>">
                    <?php if (!empty($subgrupo['subgrupos_id'])) { ?>

                      <a class="item-menus show" vitrine-id="<?= $subgrupo['id_produtos'] ?>" href="/produtos/<?= converter_texto($grupo['grupo']) ?>/<?= $grupo['grupo_id'] ?>/<?= converter_texto($subgrupo['subgrupos']) ?>/<?= $subgrupo['subgrupos_id'] ?>">
                        <?= $subgrupo['subgrupos'] ?>
                      </a>

                      <?php
                      $tetragrupo_count = count($subgrupo['tetragrupo']);
                      if ($tetragrupo_count > 0) {
                        if ($tetragrupo_count > 12) {
                          $lg = 4;
                          $md = 4;
                          $sm = 4;
                          array_chunk($subgrupo['tetragrupo'], 4);
                        } else if ($tetragrupo_count > 5 && $tetragrupo_count < 12) {
                          $lg = 6;
                          $md = 6;
                          $sm = 6;
                          array_chunk($subgrupo['tetragrupo'], 2);
                        } else {
                          $lg = 12;
                          $md = 12;
                          $sm = 12;
                          array_chunk($subgrupo['tetragrupo'], 1);
                        }

                        foreach ($subgrupo['tetragrupo'] as $tetragrupo) { ?>
                          <?php if (!empty($subgrupo['subgrupos_id'])) { ?>
                            <div class="col-lg-<?= $lg ?> col-md-<?= $md ?> col-sm-<?= $sm ?>">
                              <a class="item-menus show w100" vitrine-id="<?= $tetragrupo['id_produtos'] ?>" href="/produtos/<?= converter_texto($grupo['grupo']) ?>/<?= $grupo['grupo_id'] ?>/<?= converter_texto($subgrupo['subgrupos']) ?>/<?= $subgrupo['subgrupos_id'] ?>/<?= converter_texto($tetragrupo['tetragrupos']) ?>/<?= $tetragrupo['tetragrupos_id'] ?>">
                                <?= $tetragrupo['tetragrupos'] ?>
                              </a>
                            </div>
                          <?php } ?>
                        <?php } ?>

                      <?php } ?>

                    <?php } ?>

                  </div>
                <?php } ?>
              </div>
            </li>
            <li class="col-lg-3 col-md-3 col-sm-4 hidden-xs" data-vitrine="yes"></li>
          </ul>
        <?php } ?>
      </li>
    <?php }
  }

  /**
   * Botao de espiar o produto em tela radipa
   * @param string $linkProduto
   * @param string $textButton
   * @param string $classButton
   * @param string $classFa
   * @return boolean Button html
   */
  public static function button_espiar($linkproduto = '', $textbutton = 'espiar', $classbutton = 'btn btn-espiar', $classfa = 'fa-eye')
  {
    $button = '<button btn-espiar="%s" class="%s"><i class="fa %s"></i> %s</button>';
    return sprintf($button, $linkproduto, $classbutton, $classfa, $textbutton);
  }

  /**
   * Button de compra rapidao
   * @param type $linkProduto
   * @param type $textButton
   * @param type $classButton
   * @param type $classFa
   * @return type
   */
  public static function button_comprar($linkproduto = '', $textbutton = 'comprar', $classbutton = 'btn btn-comprar', $classfa = 'fa-shopping-cart')
  {
    $button = '<button btn-comprar="%s" class="%s"><i class="fa %s"></i> %s</button>';
    return sprintf($button, $linkproduto, $classbutton, $classfa, $textbutton);
  }

  /**
   *
   * @param type $linkProduto
   * @param type $textButton
   * @param type $classButton
   * @param type $classFa
   */
  public static function button_avise_me($linkproduto = '', $textbutton = 'avise-me', $classbutton = 'btn btn-aviseme', $classfa = 'fa-paper-plane-o')
  {
    $button = '<button btn-aviseme="%s" class="%s" onclick="javascript: return AviseMe.tela(\'%s\');"><i class="fa %s"></i> %s</button>';
    return sprintf($button, $linkproduto, $classbutton, $linkproduto, $classfa, $textbutton);
  }

  /**
   * Caixa de Cores
   * @param type $codigo_id Codigo de edicao do produto
   * @param type $init Define se ira existir ou não a caixa de cor
   * @return boolean Html de caixa de cores em base no produto ativo
   */
  public static function caixa_cores($id = 0, $str = null)
  {
    $html = '';
    $loop = Produtos::find($id)->to_array(['include' => ['produtos_all' => ['include' => ['capa', 'cor']]]]);
    foreach ($loop['produtos_all'] as $c) {
      if ($c['id_cor'] > 0) {
        $html .= ''
          . sprintf('<li onclick="javascript: $(this).caixa_cores({\'id\': \'%u\', \'data-original\': \'%s\'}); return false;">', $c['id'], Imgs::src($c['capa']['imagem'], 'medium'))
          . sprintf('<span style="background-color: #%s">', $c['cor']['cor1'])
          . sprintf('<span style="border-bottom-color: #%s"></span>', $c['cor']['cor2'])
          . '</span>'
          . '</li>';
      }
    }
    return !empty($html) ? sprintf('<ol class="product-group">%s</ol>', $html) : null;
  }

  /**
   * @return template_set_price padrao lojas
   */
  public static function template_set_price($pr_venda = array(), $pr_promo = array(), $pr_parcelas = array())
  {
    $html = $pr_venda['preco_venda'] > 0
      ? sprintf('<span class="price_venda show %s"><s class="%s">DE R$: %s</s> por </span>', $pr_venda['preco_size'], $pr_venda['preco_color'], $pr_venda['preco_venda']) : '';

    $html .= $pr_promo['preco_promo'] > 0
      ? sprintf('<span class="price_promo %s %s show">R$: %s A VISTA</span>', $pr_promo['preco_size'], $pr_promo['preco_color'], (!empty($pr_promo['preco_boleto']) ? $pr_promo['preco_boleto'] : $pr_promo['preco_promo'])) : '';

    $html .= $pr_parcelas['preco_promo'] > 0
      ? sprintf('<span class="price_parcelamento mt5 mb15 show">ou em <span class="%s"> %sx de R$: %s </span> sem juros</span>', $pr_parcelas['preco_color'], $pr_parcelas['preco_parcela_x'], $pr_parcelas['preco_parcela_price']) : '';

    return $html;
  }

  /**
   * @return template_set_price padrao lojas
   */
  public static function template_set_view_product_price($pr_venda = array(), $pr_promo = array(), $pr_parcelas = array())
  {
    $html = $pr_venda['preco_venda'] > 0
      ? sprintf('<span class="show %s"><s class="%s">DE R$: %s</s> por </span>', $pr_venda['preco_size'], $pr_venda['preco_color'], $pr_venda['preco_venda']) : '';

    $html .= $pr_promo['preco_promo'] > 0
      ? sprintf('<span class="%s %s show">R$: %s A VISTA</span>', $pr_promo['preco_size'], $pr_promo['preco_color'], (!empty($pr_promo['preco_boleto']) ? $pr_promo['preco_boleto'] : $pr_promo['preco_promo'])) : '';

    $html .= $pr_parcelas['preco_promo'] > 0
      ? sprintf('<span class="mt5 mb15 show">ou em <span class="%s"> %sx de R$: %s </span> sem juros</span>', $pr_parcelas['preco_color'], $pr_parcelas['preco_parcela_x'], $pr_parcelas['preco_parcela_price']) : '';

    return $html;
  }

  public static function template_blackfriday($prod = null)
  {
    global $CONFIG;
    $return = null;
    if (empty($prod->placastatus)) return;
    foreach (explode(',', $prod->placastatus) ?? [] as $placas) {
      if (strstr($placas, '<span')) {
        if (!$CONFIG['atacadista'])
          $return .= sprintf('%s</span>', $placas);
      } else {
        $return .= sprintf('<span class="placa-%s">%s</span>', converter_texto($placas), $placas);
      }
    }
    return sprintf('<span class="placas-status">%s</span>', $return);
  }

  /**
   * Retorna template para html
   * @param string $fone Número do telefone celular
   * @param string $style_help Css em style
   * @param string $store Dados da loja (Nome Fantasia ou Titulo da Loja)
   * @return boolean Retorna o html com icone do WhatsApp
   */
  public static function whatsapp($fone = '551632621365', $style_help = '', $store = '', $text = 'Oi! Estou entrando em contato pelo chat Whatsapp da')
  {
    return sprintf('<a href="https://wa.me/%s?text=%s %s!" target="_blank" style="%s"></a>', $fone, $text, $store, $style_help);
  }

  /**
   * Temporizador para contagem regrassiva para promoções no site
   */
  public static function template_countdown($date = null, $text = 'BLACK FRIDAY', $color = '#fff', $bg = '#000')
  {
    ob_start(); ?>
    <style>
      .time {
        top: 0;
        left: 0;
        float: left;
        width: 200px;
        background-color: <?= $bg ?>;
        text-align: center;
        position: absolute;
        padding: 5px 3px 7px;
        box-sizing: border-box;
        -webkit-border-bottom-right-radius: 5px;
        -webkit-border-bottom-left-radius: 0px;
        -moz-border-radius-bottomright: 5px;
        -moz-border-radius-bottomleft: 5px;
        border-bottom-right-radius: 5px;
        border-bottom-left-radius: 0px;
        -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, .5);
        -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, .5);
        box-shadow: 0 5px 10px rgba(0, 0, 0, .5);
        z-index: 55;
      }

      .time .titulo,
      .time-1 .tituloTimer {
        float: left;
        width: 100%;
        font-size: 12px;
        color: <?= $color ?>;
        margin-bottom: 3px;
        font-family: inherit
      }

      .time .timer,
      .time-1 .timer {
        color: <?= $color ?>;
        font-size: 25px;
        line-height: 100%;
        letter-spacing: 2px;
        display: inline-block;
        font-family: inherit
      }

      .time .timer span,
      .time-1 .timer span {
        float: left
      }

      .time.mobile {
        width: 175px !important;
        right: 10px !important;
        padding-bottom: 5px !important
      }

      .time.mobile .tituloTimer {
        font-size: 8px
      }

      .time.mobile .timer {
        font-size: 20px
      }

      .placas-status {
        display: none;
      }
    </style>
    <span class="time">
      <span class="titulo"><?= $text ?> TERMINA EM:</span>
      <span class="timer" id="time"></span>
    </span>
    <script>
      var data = '<?= $date ?>';
      var falta = (new Date(data).getTime() - new Date().getTime()) / 1000;
      var seg = Math.round(falta % 60);
      var min = Math.round(falta / 60 % 60);
      var hr = Math.round(falta / 60 / 60 % 24);
      var d = Math.round(falta / 60 / 60 / 24);
      var divs = document.querySelector("#time");

      function contagem() {
        if (hr == 0 && min == 0 && seg <= 0) clearTimeout(timerID);

        seg--;
        if (seg < 0) {
          seg = 59;
          min--;
        }
        if (min < 0) {
          min = 59;
          hr--;
        }
        if (hr < 0) {
          hr = 0;
          d--;
        }
        // var contador = [d, hr, min, seg].forEach(function (parcela, i) {
        // });

        var text = d > 0 ? d + "d" : '';
        text += hr > 0 ? ("00" + hr).slice(-2) + "h" : '';
        text += min > 0 ? ("00" + min).slice(-2) + "m" : '';
        text += seg > 0 ? ("00" + seg).slice(-2) + "s" : '00s';
        divs.innerHTML = text;

      }
      timerID = setInterval("contagem()", 1000);
      // contagem();
    </script>
  <?php
    return ob_get_clean();
  }

  /**
   * @param int $current Página atual
   * @param int $total Total da Paginação
   * @param int $limit Limite de links exibidos
   * @param int $start_at Valor inicial. Geralmente o Padrão é 1
   * @return \Generator
   */
  public static function range_limit($current, $total, $limit = 10, $start_at = 1)
  {
    $ini = ((($current - $limit) > 1) ? $current - $limit : 1);
    $fim = ((($current + $limit) < $total) ? $total + $limit : $total);

    for ($i = $ini; $i <= $fim; $i++) yield $i;
  }

  /**
   * Função para gerar um popup de demostração do frete que há no site
   * @param $image string Nome da imagem a ser carregado
   * @param $w int Largura da imagem
   * @param $h int Altura da imagem
   * @param $r int Borda radius
   * @return Html em forma de popup
   */
  public static function popup_frete($image = 'regras-frete', $w = '616', $h = '281', $r = '10')
  {

    $html_popup = null;

    $dir = sprintf('%s/imgs/', URL_VIEWS_BASE_PUBLIC_UPLOAD);
    $dir_array = glob(sprintf('%s%s{*.jpg,*.png}', $dir, $image), GLOB_BRACE);
    $dir_img_frete = ($dir_array[0] ?? 'false');
    $image = ltrim(substr($dir_img_frete, -17), '/');
    // Verificar se a imagem existe, e tenta pegar suas dimensões
    if (file_exists($dir_img_frete)) {
      list($w, $h) = getimagesize($dir_img_frete);
    }

    ob_start();
  ?>
    <script>
      $("body").on("click", "[data-frete=regras]", function() {
        $("<div/>", {
          id: "clear-remove",
          style: "position: fixed; z-index: 100; width: 100%; height: 100%; top: 0; left: 0; margin: 0; background-image: url(<?= Imgs::src('overlay-box.png', 'imgs') ?>); background-repeat: repeat;",
          append: [
            $("<style/>", {
              html: [
                ".__a1 { " +
                "-webkit-border-radius: <?= $r ?>px; " +
                "-moz-border-radius: <?= $r ?>px; " +
                "border-radius: <?= $r ?>px; " +
                "overflow: hidden; " +
                "position: fixed; " +
                "z-index: 101; " +
                "width: <?= $w ?>px; " +
                "height: <?= $h ?>px; " +
                "top: 50%; " +
                "left: 50%; " +
                "margin: -<?= str_replace(',', '.', ($h / 2)) ?>px 0 0 -<?= str_replace(',', '.', ($w / 2)) ?>px; " +
                "text-align: center; " +
                "} " +
                ".__b1 { " +
                "z-index: 102; " +
                "display: table; " +
                "vertical-align: middle; " +
                "width: <?= $w ?>px; " +
                "height: <?= ($h - 50) ?>px; " +
                "position: absolute; " +
                "bottom: 0; " +
                "left: 0; " +
                "margin: 0; " +
                "background-color: #fff;" +
                "} " +
                "@media (max-width: 768px) { " +
                ".__a1 { " +
                "margin: 0 ; " +
                "left: 0; " +
                "top: 0; " +
                "width: 100%; " +
                "height: 100%; " +
                "padding: 15px" +
                "} " +
                ".__a1 > img { " +
                "position: absolute; " +
                "width: <?= (320) ?>px;" +
                "height: <?= (146) ?>px;" +
                "top: 50%;" +
                "left: 50%;" +
                "margin-top: -<?= (146 / 2) ?>px;" +
                "margin-left: -<?= (320 / 2) ?>px;" +
                "} " +
                ".__b1 { " +
                "display: table; " +
                "vertical-align: middle; " +
                "width: <?= (320) ?>px; " +
                "height: <?= (146) ?>px; " +
                "top: 50%; " +
                "left: 50%; " +
                // "margin-top: -<?= (146 / 2) ?>px; " +
                "margin-top: -45px; " +
                "margin-left: -<?= (320 / 2) ?>px;" +
                "background-color: #fff;" +
                "} " +
                "}",
              ]
            }),
            $("<div/>", {
              class: "__a1",
              append: [
                $("<img/>", {
                  src: "<?= Imgs::src($image, "imgs") ?>",
                  alt: "Regras de frete grátis",
                  click: function() {
                    $("#clear-remove").remove();
                  }
                }),
                $("<a/>", {
                  style: "display: table; width: 100%; height: 100%; padding: 100px 0;",
                  href: "javascript:void(0)",
                  click: function() {
                    $("#clear-remove").remove();
                  }
                })
              ]
            })
          ]
        }).appendTo("body");
      });
    </script>
<?php
    return ob_get_clean();
  }
}
