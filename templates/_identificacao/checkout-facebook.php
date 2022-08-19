<?php

$fb = new Facebook\Facebook([
  'app_id' => '2014358052200582',
  'app_secret' => 'b9235e66c29903d1310efc5a3c6b65dc',
  'default_graph_version' => 'v2.2',
]);

$helper = $fb->getCanvasHelper();

try {
  $accessToken = $helper->getAccessToken();
} catch (Facebook\Exceptions\FacebookResponseException $e) {
  // When Graph returns an error
  echo 'Graph returned an error: ' . $e->getMessage();
  exit;
} catch (Facebook\Exceptions\FacebookSDKException $e) {
  // When validation fails or other local issues
  echo 'Facebook SDK returned an error: ' . $e->getMessage();
  exit;
}

if (!isset($accessToken)) {
  echo 'No OAuth data could be obtained from the signed request. User has not authorized your app yet.';
  exit;
}

// Logged in
echo '<h3>Signed Request</h3>';
var_dump($helper->getSignedRequest());

echo '<h3>Access Token</h3>';
var_dump($accessToken->getValue());

// $fb = new \Facebook\Facebook([
  // 'app_id' => '2014358052200582',
  // 'app_secret' => 'b9235e66c29903d1310efc5a3c6b65dc',
  // 'default_graph_version' => 'v2.10',
  // //'default_access_token' => '{access-token}', // optional
// ]);


// // Use one of the helper classes to get a Facebook\Authentication\AccessToken entity.
// if( isset($_GET['_fb']) && $_GET['_fb'] == 'callback' ){
	// $helper = $fb->getRedirectLoginHelper();
	// $permissions = ['email'];
	// $loginUrl = $helper->getLoginUrl( URL_BASE . '/identificacao/facebook-login?_fb=callback', $permissions);
	// header('location: ' . $loginUrl);
	// return;
// }


// // $helper = $fb->getJavaScriptHelper();
// // $helper = $fb->getCanvasHelper();
// // $helper = $fb->getPageTabHelper();
