<?php
defined('PATH_ROOT') || define('PATH_ROOT', realpath($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR);
require_once PATH_ROOT . '/app/settings.php';
require_once PATH_ROOT . '/app/vendor/autoload.php';
require_once PATH_ROOT . '/app/settings-config.php';
require_once PATH_ROOT . '/assets/' . ASSETS .  '/settings.php';

$plp_id = filter_input(INPUT_GET, 'plp_id');

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
      'accept: application/pdf',
    //   'Content-Accept: application/pdf',
    //   'Content-Type: application/json',
    //   'Content-Type: application/pdf'
    ]
];

$curl_order = ($curl_array + [
    CURLOPT_URL => 'https://api.skyhub.com.br/shipments/b2w/view?plp_id=' . $plp_id,
    CURLOPT_CUSTOMREQUEST => 'GET'
]);

// Captura os pedidos das orders
$curl = curl_init();
curl_setopt_array($curl, $curl_order);
$response = (curl_exec($curl));
curl_close($curl);
unset($curl);

header('Content-Type: application/pdf');
header('Content-Length: '.strlen( $response ));
header('Content-disposition: inline; filename="new.pdf"');
header('Cache-Control: public, must-revalidate, max-age=0');
header('Pragma: public');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
echo $response;

