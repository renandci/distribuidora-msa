<?php
include '../settings.php';
include '../vendor/autoload.php';
include '../settings-config.php';
include '../includes/bibli-funcoes.php';
include '../includes/ajax-emails.php';

try {
    $str = [];
    $status = 1;

    $_embedded = json_decode(file_get_contents('php://input'), true);;

    $_embeddedCount = (int)count($_embedded);

    if( $_embeddedCount == 0 ) {
        throw new Exception('Não Autorizado');
	}

    $charge = $_embedded['_embedded'];

    foreach ( $charge['charges'] as $loop ) 
    {
        if( $loop['status'] == 'PAID' )
        {
            $payments = $loop['payments'];
            
            $paymentsCount = (int)count($payments);
            
            if( $paymentsCount > 0 )
            {
                $payments = $payments[0];
                
                $reference = $loop['reference'];
                
                $status  = $payments['status'];
                
                $Pedidos = Pedidos::first(['conditions' => ['codigo like ? and forma_pagamento = "Boleto"',  (string)$reference]]);
                
                $PedidosCount = (int)count($Pedidos);
                
                if( $PedidosCount > 0 )
                {
                    switch( $status ) 
                    {
                        case 'CONFIRMED': 
                            $str['status'] = 3;
                            $str['mensagem'] = 'O pagamento foi aprovado e acreditado.';
                        break;
                        
                        case 'AUTHORIZED': 
                            $str['status'] = 11;
                            $str['mensagem'] = 'Pagamento autorizado (Aguardando confirmação)';
                        break;
                        
                        case 'FAILED': 
                        case 'DECLINED': 
                        case 'NOT_AUTHORIZED': 
                            $str['status'] = 5;
                            $str['mensagem'] = 'O pagamento não foi efetuado dentro da data prevista. Mas caso você ainda tenha interesse na compra, clique na opção pagar novamente.';		
                        break;
    
                        default: 
                            $str['status'] = 5;
                            $str['mensagem'] = 'O pagamento não foi efetuado dentro da data prevista. Mas caso você ainda tenha interesse na compra, clique na opção pagar novamente.';		
                        break;
                    }
                    
                    $Pedidos->status = $str['status'];
                    $Pedidos->save();
                    
                    PedidosLogs::logs($Pedidos->id, 0, $str['mensagem'], $str['status']);
                }
            }
        }
    }

} catch (\Exception $e) {
    echo $e->getMessage();
    // printf('<pre>%s</pre>', print_r($e, 1));
    //throw $th;
}
