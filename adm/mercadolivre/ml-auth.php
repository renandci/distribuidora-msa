<?php
include '../topo.php';

if(isset($_GET['code']) || isset($_SESSION['access_token'])) {

	// If code exist and session is empty
	if($GET['code'] && !($_SESSION['access_token'])) {
		// If the code was in get parameter we authorize
		$user = $meli->authorize($GET['code'],  URL_BASE_HTTPS . 'adm/mercadolivre/ml-auth.php');
		
		// Now we create the sessions with the authenticated user
		$_SESSION['access_token'] = $user['body']->access_token;
		$_SESSION['access_token_id'] = substr($user['body']->refresh_token, -9);
		$_SESSION['expires_in'] = time() + $user['body']->expires_in;
		$_SESSION['refresh_token'] = $user['body']->refresh_token;
	} else {
		// We can check if the access token in invalid checking the time
		if($_SESSION['expires_in'] < time()) {
			try {
				// Make the refresh proccess
				$refresh = $meli->refreshAccessToken();

				// Now we create the sessions with the new parameters
				$_SESSION['access_token'] = $refresh['body']->access_token;
                $_SESSION['access_token_id'] = substr($user['body']->refresh_token, -9);
				$_SESSION['expires_in'] = time() + $refresh['body']->expires_in;
				$_SESSION['refresh_token'] = $refresh['body']->refresh_token;
			} catch (Exception $e) {
			  	echo 'Exception: ' .  $e->getMessage();
			}
		}
	}
    if( $_SESSION['expires_in'] > time() ){
        header('Location: /adm/mercadolivre/ml-produtos.php');
        return;
    }
//    echo date('d/m/Y H:i', 1504663977);
	printf('<pre>%s</pre>', print_r($_SESSION, true));
	
	
} else {
	echo ''
    . '<div class="clearfix mt15 text-center">'
    . '<a href="' . $meli->getAuthUrl( URL_BASE_HTTPS . 'adm/mercadolivre/ml-auth.php',  Meli::$AUTH_URL['MLB']) . '" class="btn btn-primary btn-lg">'
    . 'fazer login no mercado livre'
    . '</a>'
    . '</div>';
}

include '../rodape.php';