<?php
include '../settings.php';
include '../vendor/autoload.php';
include '../settings-config.php';
/**
 * URI Base
 * Sandbox - Produção
 */
$url = ($CONFIG['pagamentos']['boleto_mode'] == 0 ? 'https://sandbox.boletobancario.com' : 'https://api.juno.com.br');

/**
 * Authentication
 * BASIC-CREDENTIALS
 */
$credentials = base64_encode(sprintf('%s:%s', $CONFIG['pagamentos']['boleto_client'], $CONFIG['pagamentos']['boleto_secret']));

$ch_tkn = curl_init();
curl_setopt_array($ch_tkn, [
  CURLOPT_URL => $url . '/authorization-server/oauth/token',
  CURLOPT_RETURNTRANSFER => true,
  // CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => 'grant_type=client_credentials',
  CURLOPT_HTTPHEADER => [
    'Content-Type: application/x-www-form-urlencoded',
    sprintf('Authorization: Basic %s', $credentials)
  ]
]);

$info = curl_getinfo($ch_tkn);
$err = curl_error($ch_tkn);
$response = curl_exec($ch_tkn);
curl_close($ch_tkn);

if ($err)
  throw new Exception('Não foi possivel gerar o token');

$jwt = json_decode($response);
print_r($jwt);
exit;

// /**
//  * Lista as WebHooks
//  */
// $curl = curl_init();

// curl_setopt_array($curl, array(
//   CURLOPT_URL => 'https://api.juno.com.br/api-integration/notifications/webhooks',
//   CURLOPT_RETURNTRANSFER => true,
//   CURLOPT_ENCODING => '',
//   CURLOPT_MAXREDIRS => 10,
//   CURLOPT_TIMEOUT => 0,
//   CURLOPT_FOLLOWLOCATION => true,
//   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//   CURLOPT_CUSTOMREQUEST => 'GET',
//   CURLOPT_HTTPHEADER => array(
//     'X-Api-Version: 2',
//     sprintf('X-Resource-Token: %s', $CONFIG['pagamentos']['boleto_token']),
//     sprintf('Authorization: Bearer %s', $jwt->access_token),
//   ),
// ));

// $response = curl_exec($curl);

// curl_close($curl);
// echo $response;

// /**
//  * Deleta as WebHooks
//  */
// $curl = curl_init();

// curl_setopt_array($curl, array(
//   CURLOPT_URL => 'https://api.juno.com.br/api-integration/notifications/webhooks/' . $response->_embedded->webhooks[0]->id,
//   CURLOPT_RETURNTRANSFER => true,
//   CURLOPT_ENCODING => '',
//   CURLOPT_MAXREDIRS => 10,
//   CURLOPT_TIMEOUT => 0,
//   CURLOPT_FOLLOWLOCATION => true,
//   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//   CURLOPT_CUSTOMREQUEST => 'DELETE',
//   CURLOPT_HTTPHEADER => array(
//     'X-Api-Version: 2',
//     sprintf('X-Resource-Token: %s', $CONFIG['pagamentos']['boleto_token']),
//     sprintf('Authorization: Bearer %s', $jwt->access_token),
//   ),
// ));

// $response = curl_exec($curl);

// curl_close($curl);
// echo $response;


// /**
//  * Cria os WebHooks
//  */
// $curl = curl_init();

// curl_setopt_array($curl, array(
//   CURLOPT_URL => 'https://api.juno.com.br/api-integration/notifications/webhooks',
//   CURLOPT_RETURNTRANSFER => true,
//   CURLOPT_ENCODING => '',
//   CURLOPT_MAXREDIRS => 10,
//   CURLOPT_TIMEOUT => 0,
//   CURLOPT_FOLLOWLOCATION => true,
//   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//   CURLOPT_CUSTOMREQUEST => 'POST',
//   CURLOPT_POSTFIELDS => json_encode(array(
//     "url" => URL_BASE . "boleto-status/2.0",
//     "eventTypes" => [
//         "PAYMENT_NOTIFICATION"
//     ]
//   )),

//   CURLOPT_HTTPHEADER => array(
//     'X-Api-Version: 2',
//     'Content-Type: application/json',
//     sprintf('X-Resource-Token: %s', $CONFIG['pagamentos']['boleto_token']),
//     sprintf('Authorization: Bearer %s', $jwt->access_token),

//   ),
// ));

// $response = curl_exec($curl);

// curl_close($curl);
// echo $response;
