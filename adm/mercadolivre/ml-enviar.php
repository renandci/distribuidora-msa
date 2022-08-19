<?php
include '../topo.php';	

AcessoML($_SESSION, $PgAt);

function array_filter_recursive ($data) {
    $original = $data;
    $data = array_filter($data);
    $data = array_map(function ($e) {
        return is_array($e) ? array_filter_recursive($e) : $e;
    }, $data);
    return $original === $data ? $data : array_filter_recursive($data);
}

if( count($POST['produtos']) > 0 )
{
    foreach( $POST as $k => $produto )
    {
        if( empty( $produto['categoria_id'] ) || empty( $produto['listing_type_id'] ) ) { ?>
            
            <div class="text-center ft18px">
                <h3>Desculpe! Algo deu errado!</h3>
                <?php if(empty($produto['categoria_id'])) { ?>
                    <p>Selecione uma categoria</p>
                <?php } ?>
                <?php if(empty($produto['listing_type_id'])) { ?>
                    <p>Selecione uma categoria</p>
                <?php } ?>
                <a href="/adm/mercadolivre/ml-produtos.php?_ml=" class="btn btn-success mt15">
                    Voltar
                </a>
            </div>
            <?php
            break;
        } 
        else {
            $Produtos = Produtos::first(['conditions' => ['codigo_id=?', $produto['codigo_id']]]);

            /**
             * TOTAL DE CORES
             * Gera os atributos para fazer as paletas de cores e tamanhos
             */
            $pictures = [];
            $pictures_cor = [];
            $result_cor = ProdutosImagens::all(['conditions' => ['codigo_id=?', $produto['codigo_id']], 'order' => 'capa desc']);
            foreach( $result_cor as $f ) {
				$pictures[] = ['source' => Imgs::src($f->imagem, 'large')];
                $pictures_cor[$f->id] = Imgs::src($f->imagem, 'large');
            }

            /**
             * Variavel pega o ultimo preço da variação do produto
             */
            $preco_base = soNumero($POST['produtos']['preco']);
            $attributes_estoque = $POST['produtos']['estoque'];

            function shipping_costs($str) {
                foreach ($str['produtos']['costs'] as $a => $b) {
                    $cost[] = soNumero( $b ) > 0 ? dinheiro( $b ) : $b;
                }
                return $cost;
            }
            
            /**
             * Criar os paramentros para free_methods()
             * @param type $s
             */
            function free_methods($s) {
                if ( empty( $s['free_mode'] ) ) {
                    return;
                }
                if( $s['free_mode'] == 'frete_not_country' ) {
                    return array(
                        'id' => $s['id'],
                        'rule' => array(
                            'free_mode' => 'exclude_region',
                            'value' => array( 'BR-NO', 'BR-NE' )
                        )
                    );
                }
                if( $s['free_mode'] == 'frete_all_country' ) {
                    return array(
                        'id' => $s['id'],
                        'rule' => array(
                            'free_mode' => 'country',
                            'value' => 'null'
                        )
                    );
                }
            }
            
            $item = [
                'title' => ( ! empty( $produto['title'] ) ? $produto['title'] : $Produtos->nome_produto ),
                'category_id' => $produto['categoria_id'],
                'price' => (number_format(($preco_base/100), 2, '.', '')),
                'currency_id' => 'BRL',
                'available_quantity' => ( count($attributes_estoque) > 0 ? '0' : $Produtos->estoque),

                'buying_mode' => 'buy_it_now',
                'listing_type_id' => $produto['listing_type_id'],
                'condition' => 'new',
                'description' => [
                    'plain_text' => str_replace("/[\n|\r|\n\r|\r\n]{2,}/", "", strip_tags($Produtos->descricao->descricao))
                ],
                'pictures' => $pictures,
                'shipping' => [
                    'mode' => $produto['mode'],
                    'local_pick_up' => $produto['local_pick_up'],
                    'free_shipping' => ($produto['free_methods']['free_mode'] == 'frete_all_country')? "true":"false",
					'free_methods' => ( ! empty( $produto['free_methods']['free_mode'] ) ? [free_methods( $produto['free_methods'] )] : null),
                    'costs' => ( ! empty( $produto['costs'] ) ? shipping_costs( $POST ) : null )
                ]
            ];
			
			if($preco_base >= 120) {
				$item['shipping'] = [
					'tags' => [ 'mandatory_free_shipping' ] 
				];
			}
            
            $attributes = null;
            if( count($produto['variations']) > 0) 
            {
                // Loop para tratar alguns dados
                foreach($produto['variations'] as $variations_key => $variations_value )
                {
                    // foreach($variations_value as $a => $b) 
                    // {
                    //     $attributes['variations'][$variations_key][$a] = @array_values($produto['variations'][$variations_key][$a]);
                        
                    //     // if( empty( $attributes['variations'][$variations_key][$a]['value_name'] ) ) {
                    //     //     unset($attributes['variations'][$variations_key][$a]);
                    //     // }
                    // }

                    // Deve tratar o valor para o envio correto para o mercado livre
                    if( ! empty($variations_value['price']) )
                    {
                        $attributes['variations'][$variations_key]['price'] = (number_format((soNumero($variations_value['price'])/100), 2, '.', ''));
                    }
                    
                    // Valor de estoque
                    if( ! empty($variations_value['available_quantity']) )
                    {
                        $attributes['variations'][$variations_key]['available_quantity'] = $variations_value['available_quantity'];
                    }

                    // Tenta eliminar os dados nulos
                    if( is_array($variations_value['picture_ids']) && count($variations_value['picture_ids']) > 0 )
                    {
                        foreach($variations_value['picture_ids'] as $pic_key => $pic_value )
                        {
                            $attributes['variations'][$variations_key]['picture_ids'][$pic_key] = $pictures_cor[$pic_value];
                        }
                    }

                    // Tenta eliminar os dados nulos
                    if( is_array($variations_value['attributes']) && count($variations_value['attributes']) > 0 )
                    {
                        foreach($variations_value['attributes'] as $attr_key => $attr_value )
                        {
                            if( empty( $produto['variations'][$variations_key]['attributes'][$attr_key]['value_name'] ) ) {
                                unset($produto['variations'][$variations_key]['attributes'][$attr_key]);
                            }
                        }
                        
                        $attributes['variations'][$variations_key]['attributes'] = array_values($produto['variations'][$variations_key]['attributes']);

                        // Remover se não há valores
                        if(count($attributes['variations'][$variations_key]['attributes']) == 0)
                        {
                            unset($attributes['variations'][$variations_key]['attributes']);
                        }
                    }

                    // Tenta eliminar os dados nulos
                    if( is_array($variations_value['attribute_combinations']) && count($variations_value['attribute_combinations']) > 0 )
                    {
                        foreach($variations_value['attribute_combinations'] as $attr_key => $attr_value )
                        {
                            if( empty( $produto['variations'][$variations_key]['attribute_combinations'][$attr_key]['value_name'] ) ) {
                                unset($produto['variations'][$variations_key]['attribute_combinations'][$attr_key]);
                            }
                        }
                        
                        $attributes['variations'][$variations_key]['attribute_combinations'] = array_values($produto['variations'][$variations_key]['attribute_combinations']);
                        
                        // Remover se não há valores
                        if(count($attributes['variations'][$variations_key]['attribute_combinations']) == 0)
                        {
                            unset($attributes['variations'][$variations_key]['attribute_combinations']);
                        }
                    }
                }
                
                // $attributes = array_filter(array_map('array_filter', $attributes));
                
                $attributes_count = count($attributes['variations'][$variations_key]['attributes']);
                
                $combinations_count = count($attributes['variations'][$variations_key]['attribute_combinations']);

                if( $combinations_count > 0 || $attributes_count > 0 )
                    $item = $item + $attributes;
            }

            printf('<pre>%s</pre>', print_r(json_encode($item, JSON_PRETTY_PRINT), true));
            // return;

            $validate = $meli->post('/items/validate', $item, ['access_token' => $_SESSION['access_token']]);

            if ($validate['body']->error != '') 
            {
                $error = $validate['body'];
                $cause = urldecode($validate['body']->cause[0]->message);

				$mensages = [];
                foreach($validate['body']->cause as $msgs) 
                {
					$mensages[] = $msgs->message;
					// $mensages[] = 'MESSAGE: ' 		. $msgs->message;
					// $mensages[] = 'DEPARTMENT: ' 	. $msgs->department;
					// $mensages[] = 'CAUSE_ID: ' 		. $msgs->cause_id;
					// $mensages[] = 'TYPE: ' 			. $msgs->type;
					// $mensages[] = 'CODE: ' 			. $msgs->code;
					// $mensages[] = 'REFERENCES: ' 	. $msgs->references;
                }
            }
            else {
                // $response = $meli->post('/items', $item, ['access_token' => $_SESSION['access_token']]);
                // if ($response['body']->error == '') 
                // {
                //     if( $response['body']->id != '' ) 
                //     {
				// 		$MercadoLivreProdutos = MercadoLivreProdutos::find(['conditions' =>[ 'produtos_codigo_id=?', $Produtos->codigo_id ]]);
				// 		$MercadoLivreProdutos->produtos_ml_id = $response['body']->id;
				// 		$MercadoLivreProdutos->produtos_ml_status = $response['body']->status;
				// 		$MercadoLivreProdutos->produtos_ml_estoque = $estoque;
				// 		$return = $MercadoLivreProdutos->save();
				// 		Logs::create_logs('Adicionou um produto ao mercadolivre: ' . $response['body']->id, $_SESSION['admin']['id_usuario']);
                //         if($return) {
				// 			header('Location: /adm/mercadolivre/ml-produtos.php?_ml=true');
				// 			return;
				// 		}
				// 		else {
				// 			header('Location: /adm/mercadolivre/ml-produtos.php?_ml=true');
				// 			return;
				// 		}
                //     }
                // } 
                // else {
                //     $error = $response['body'];
                //     $cause = urldecode($response['body']->cause[0]->message);
                //     $mensages = [];
                //     foreach($response['body']->cause as $msgs) {
                //         $mensages[] = $msgs->message;
                //         // $mensages[] = 'MESSAGE: ' 		. $msgs->message;
                //         // $mensages[] = 'DEPARTMENT: ' 	. $msgs->department;
                //         // $mensages[] = 'CAUSE_ID: ' 		. $msgs->cause_id;
                //         // $mensages[] = 'TYPE: ' 			. $msgs->type;
                //         // $mensages[] = 'CODE: ' 			. $msgs->code;
                //         // $mensages[] = 'REFERENCES: ' 	. $msgs->references;
                //     }
                //     // printf('<h2>Atenção</h2><p>%s</p><a href="javascript:window.history.go(-1);" class="btn btn-default">voltar</a><ul><li class="mb15">%s</li></ul>', $error->message, implode('</li><li class="mb15">', $mensages));
                // }
            }
            printf('<div class="text-center"><h2>Atenção</h2><p>%s</p><a href="/adm/mercadolivre/ml-editar.php?codigo_id=%u&categoria_id=%s" class="btn btn-default">voltar</a><ul><li class="mb15">%s</li></ul></div>', $error->message, $produto['codigo_id'], $produto['categoria_id'], implode('</li><li class="mb15">', $mensages));
        }
    }
}
include '../rodape.php';