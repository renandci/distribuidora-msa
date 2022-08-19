<?php

if (isset($GET['pedido'], $GET['acao']) && (int)$GET['pedido'] == 0)
  exit('Não há pedido disponivel');



$search = $GET['pedido'];
$Pedidos = current(array_filter(
  $CONFIG['cliente_session']['pedidos'],
  function ($value) use ($search) {
    return ($value['id'] == $search);
  }
));

foreach ($Pedidos['pedidos_logs'] as $status) {
  $Status[] = sprintf('<i class="fa fa-check"></i> %s %s', date('d/m/Y H:i', strtotime($status['data_envio'])), strip_tags($status['descricao']));
}

$corpo .= "
<div id='verpedido' class='row table-responsive'>

		<div class='table-responsive'>
			<span id='numPedido' class='hidden'>" . $Pedidos['codigo'] . "</span>
			<table class='mb15 table'>
				" . (str_replace('td align=', 'td colspan="4" align=', mail_status($Pedidos))) . "
				" . (($Pedidos['status'] == 4 or $Pedidos['status'] == 5) ? "<tr><td colspan='4' align='center' style='border-top:1px solid #ccc;'>" . nl2br($Pedidos['motivos']) . "</td></tr>" : '') . "
				<tr>
					<td colspan='2'><b>Produtos Adquiridos</b></td>
					<td align='center'><b>Qtde</b></td>
					<td align='center'><b>Valor</b></td>
				</tr>";
$i = 0;
foreach ($Pedidos['pedidos_vendas'] as $rr) {
  $corpo .= ""
    . "<tr>"
    . "<td width='70px' nowrap='nowrap' " . (($i > 0) ? "class='class-border-top'" : '') . ">"
    . "<img src='" . Imgs::src($rr['produto']['capa']['imagem'], 'smalls') . "' style='width:70px;vertical-align: middle;'/>"
    . "</td>"
    . "<td " . (($i > 0) ? "class='class-border-top'" : '') . ">"
    . "<b>{$rr['produto']['nome_produto']}</b>"
    . (($rr['produto']['cor']['nomecor'] != '') ? "<br />{$rr['produto']['cor']['nomecor']}" : '')
    . (($rr['produto']['tamanho']['nometamanho'] != '') ? "<br />{$rr['produto']['tamanho']['nometamanho']}" : '');

  // if( ! empty( $rr['personalizado'] ) && $rr['personalizado'] != '' ) {

  // $personalizado = html_entity_decode( $rr['personalizado'] );
  // $personalizado = json_decode( $personalizado, true );

  // $corpo .= sprintf('<span style="display: block; font-size: 16px; color: red; font-weight: 600">%s</span> ', 'Produto Personalizado');
  // foreach( (array)$personalizado as $key => $value )
  // {
  // $val = '';
  // $corpo .= '<span style="display: block; color: red;">';
  // foreach( (array)$value as $key2 => $value2 )
  // {
  // $val .= "$value2: ";
  // }
  // $corpo .= sprintf('<span style="font-size: 14px;">%s</span> ', rtrim($val, ': '));
  // $corpo .= '</span>';
  // }
  // }

  $corpo .= ""
    . "</td>"
    . "<td align='center' " . (($i > 0) ? "class='class-border-top'" : '') . ">"
    . "{$rr['quantidade']}"
    . "</td>"
    . "<td width='1%' nowrap='nowrap' align='center' " . (($i > 0) ? "class='class-border-top color-001 ft18px'" : 'class="color-001 ft18px"') . ">"
    . "R$: " . number_format($rr['valor_pago'], 2, ',', '.') . ""
    . "</td>"
    . "</tr>";
  // $group = '';
  // $json = html_entity_decode( $rr['personalizado'] );
  // $personalizado = json_decode( $json, true );
  // if( count( $personalizado ) > 0 ) {
  // 	$corpo .= '<tr><td colspan="4">';
  // 	foreach (current($personalizado) as $key1 => $value1 ) :
  // 		if( is_array( $value1 ) ) :
  // 		$corpo .= '<div class="clearfix fieldset mb10">'
  // 				. '<h3 class="text-left bold">' . str_replace(['_'], [' '], $key1) . '</h3>'
  // 				. '<table width="100%" border="0" cellspacing="0" cellpadding="0" >'
  // 				. '<tr>';

  // 				$count_tr = 0;
  // 				$count_tr_array = count($value1);
  // 				foreach ($value1 as $key2 => $value2) :
  // 					$corpo .= ( ($count_tr % 3) == 0 ) ? '</tr><tr>' : '';

  // 					$corpo .= '<td width="'; if($count_tr_array >= 3) :
  // 												$corpo .= '33%';
  // 												elseif($count_tr_array >= 2) :
  // 												$corpo .= '50%';
  // 												else :
  // 												$corpo .= '100%';
  // 												endif; $corpo .= '">';
  // 						if( ctype_digit($value2) ) :

  // 							$r = Produtos::find($value2);
  // 							// verfica se os hexadecimais existem
  // 							if( $r->tamanho->hex1 != '' && $r->tamanho->hex2 != '' ) :
  // 								$hex = MultiColorFade([ $r->tamanho->hex1, $r->tamanho->hex2 ], 15);
  // 								$tot = count($hex);
  // 								$corpo .= $r->tamanho->nometamanho;

  // 								$corpo .= '<div class="clearfix" style="width: 100%">';
  // 								for ($i = 0; $i < $tot; $i++) :
  // 									$corpo .= '<span style="'
  // 											. 'float: left;'
  // 											. 'width: ' . str_replace(',', '.', (100/$tot)) . '%;'
  // 											. 'height: 35px; '
  // 											. 'background-color: #' . $hex[$i] .'"></span>';
  // 								endfor;
  // 								$corpo .= '</div>';

  // 							else :
  // 								// imprimi os dados simples
  // 								$corpo .= '<span class="_b">' . $r->tamanho->nometamanho . '</span>';

  // 							endif;
  // 						else :
  // 							preg_match('/[\w\-]+\.(jpg|png|gif|jpeg)/', $value2, $matches);
  // 							if( count( $matches ) > 0 ) :
  // 								$img = str_replace( 'imagepersonalize_' . ASSETS, '', $matches[0] );
  // 								$corpo .= ''
  // 										. '<img src="'
  // 										. Imgs::src( $img, 'imagepersonalize') . '?v=' . substr(time(), 0, -2)
  // 										. '" class="center-block" width="100%"/>';
  // 							else :
  // 								$corpo .= $value2;
  // 							endif;
  // 						endif;
  // 					$corpo .= '</td>';
  // 				$count_tr++;
  // 				endforeach;

  // 				$corpo .= '</tr>'
  // 						. '</table>'
  // 						. '</div>';

  // 		endif;
  // 	endforeach;
  // 	$corpo .= '</td></tr>';
  // }
  $QTDE += $rr['quantidade'];
  $VALOR_PRODUTOS += $rr['valor_pago'];
  ++$i;
}

$TOTAL = valor_pagamento($Pedidos['valor_compra'], $Pedidos['frete_valor'], $Pedidos['desconto_cupom'], '$', $Pedidos['desconto_boleto']);
$corpo .= ""
  . "<tr>"
  . "<td align='left' nowrap='nowrap' width='50%' style='border-top:1px solid #ccc;' colspan='2'>"
  . "<h4 class='mb5'>Status do Pedido</h4>"
  . (is_array($Status) ? '<p>' . implode('</p><p>', $Status) . '</p>' : '');

if (in_array($Pedidos['status'], [1, 2]) && $Pedidos['forma_pagamento'] == 'Pix') {
  $corpo .= sprintf('<a href="/pix/index.php?id=%s" class="btn btn-primary btn-lg btn-block center-block mb15 mt5" target="_blank" style="max-width: 320px">imprimir pix</a>', $Pedidos['id']);
}

if (in_array($Pedidos['status'], [1, 2]) && $Pedidos['forma_pagamento'] == 'Transferência') {
  $corpo .= sprintf('<a href="/transferencia/index.php?id=%s" class="btn btn-primary btn-lg btn-block center-block mb15 mt5" target="_blank" style="max-width: 320px">imprimir transferencia</a>', $Pedidos['id']);
}

if (in_array($Pedidos['status'], [1, 2]) && $Pedidos['forma_pagamento'] == 'Boleto') {
  $corpo .= sprintf('<a href="/boleto/index.php?id=%s" class="btn btn-primary btn-lg btn-block center-block mb15 mt5" target="_blank" style="max-width: 320px">imprimir boleto</a>', $Pedidos['id']);
}

$corpo .=    ""
  . "</td>"
  . "<td align='left' nowrap='nowrap' width='50%' style='border-top:1px solid #ccc;' colspan='2'>"
  . "<h4 class='mb5'>Endereço de Entrega</h4>"
  . ($Pedidos['pedido_endereco']['id'] ? "Endereço: {$Pedidos['pedido_endereco']['endereco']}, {$Pedidos['pedido_endereco']['numero']}" : '')
  . ($Pedidos['pedido_endereco']['id'] ? "<br/>Bairro: {$Pedidos['pedido_endereco']['bairro']}" : '')
  . ($Pedidos['pedido_endereco']['id'] ? "<br/>Complemento: {$Pedidos['pedido_endereco']['complemento']}" : '')
  . ($Pedidos['pedido_endereco']['id'] ? "<br/>Referência: {$Pedidos['pedido_endereco']['referencia']}" : '')
  . ($Pedidos['pedido_endereco']['id'] ? "<br/>Cidade/UF: {$Pedidos['pedido_endereco']['cidade']}/{$Pedidos['pedido_endereco']['uf']}" : '')
  . ($Pedidos['pedido_endereco']['id'] ? "<br/>CEP: {$Pedidos['pedido_endereco']['cep']}" : '')
  . "</td>"
  . "</tr>"
  . "<tr>"
  . "<td align='left' style='border-top:1px solid #ccc;' colspan='2'>"
  . (!empty($Pedidos['frete_prazo']) ? $Pedidos['frete_prazo'] : '')
  . "</td>"
  . "<td align='right' nowrap='nowrap' width='200px' style='border-top:1px solid #ccc; background-color: #f3f3f3' colspan='2'>"
  . "<span class='show'>Frete: {$Pedidos['frete_tipo']}</span>"
  . "<span class='show'>SubTotal: <font color='#a20000'>R$: " . number_format($VALOR_PRODUTOS, 2, ',', '.') . "</font></span>"
  . "<span class='show'>Total de Itens: <font color='#a20000'>{$QTDE}</font></span>"
  . ($Pedidos['desconto_cupom'] > 0 ? "<span class='show'>Cupom desconto R$: -" . number_format($Pedidos['desconto_cupom'], 2, ',', '.') . "</span>" : '')
  . ($Pedidos['desconto_boleto'] > 0 ? "<span class='show'>Desconto no boleto -{$Pedidos['desconto_boleto']}%</span>" : '')
  . "<span class='show'>Valor Frete: <font color='#a20000'>R$: " . number_format($Pedidos['frete_valor'], 2, ',', '.') . "</font></span>"
  . "<span class='show'>"
  . "<b>Total da compra</b>: "
  . "<font color='#a20000' size='6'>R$: " . ($Pedidos['desconto_boleto']
    ? number_format($TOTAL['TOTAL_COMPRA_C_BOLETO'], 2, ',', '.')
    : number_format($TOTAL['TOTAL_COMPRA'], 2, ',', '.'))
  . "</font></span>"
  . "</td>"
  . "</tr>"
  . "</table>
	</div>
</div>";
echo $corpo;
