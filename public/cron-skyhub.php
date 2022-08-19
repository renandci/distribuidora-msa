<?php
defined('PATH_ROOT') || define('PATH_ROOT', realpath($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR);
include PATH_ROOT . 'app/vendor/autoload.php';
include PATH_ROOT . 'app/settings.php';
include PATH_ROOT . 'app/settings-config.php';

$curl_array = [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_HTTPHEADER => [
      'X-User-Email: '          . $CONFIG['skyhub']['user'],
      'X-Api-Key: '             . $CONFIG['skyhub']['api_key'],
      'X-Accountmanager-Key: '  . $CONFIG['skyhub']['account'],
      'Content-Type: application/json; charset=UTF-8'
    ]
];

$curl_order = ($curl_array + [
    CURLOPT_URL => 'https://api.skyhub.com.br/queues/orders',
    CURLOPT_CUSTOMREQUEST => 'GET'
]);

// Captura os pedidos das orders
$curl = curl_init();
curl_setopt_array($curl, $curl_order);
$response = json_decode(curl_exec($curl));
curl_close($curl);
unset($curl);

if(!isset($response->code)) return;

if(isset($response->items)) {
    foreach ( $response->items as $items ) {
        // Recuperar o produto 'SKYHUB'
        $curl_product = curl_init();
        curl_setopt_array($curl_product, ($curl_array + [
            CURLOPT_URL => sprintf('https://api.skyhub.com.br/variations/%s', $items->id),
            CURLOPT_CUSTOMREQUEST => 'GET'
        ]));
        $product = json_decode(curl_exec($curl_product));
        curl_close($curl_product);
        unset($curl_product);
        
        // Altera o estoque do produto 'SKYHUB'
        $curl_product_put = curl_init();
        curl_setopt_array($curl_product_put, ($curl_array + [
            CURLOPT_URL => sprintf('https://api.skyhub.com.br/variations/%s', $items->id),
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS => json_encode([
                'variation' => [
                    'qty' => ($product->variation->qty - $items->qty)
                ]
            ])
        ]));
        $product = json_decode(curl_exec($curl_product_put));
        curl_close($curl_product_put);
        unset($curl_product_put);
    }
}

$curl_delete = ($curl_array + [
    CURLOPT_URL => sprintf('https://api.skyhub.com.br/queues/orders/%s', $response->code),
    CURLOPT_CUSTOMREQUEST => 'DELETE'
]);

// Captura os pedidos das orders
$curl = curl_init();
curl_setopt_array($curl, $curl_delete);
$response = curl_exec($curl);
print_r($response);
curl_close($curl);
