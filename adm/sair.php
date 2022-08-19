<?php
include 'topo.php';

/**
 * Ativa o sair do sistema
 */
if( isset($GET['acao']) && $GET['acao'] === 'sair' ) {
    
    // Logs::CreateLogs('Efetuou Logout', $_SESSION['admin']['id_usuario']);
	Logs::my_logs(['adm' => $_SESSION['admin']['apelido']], ['adm'=> 'Efetuou Logout'], (INT)$_SESSION['admin']['id_usuario'], 'adm');

	foreach ($_SESSION as $key => $val) {
        unset($_SESSION[ $key ]);
    }

    session_destroy();
	
	if( empty( $_SESSION ) ) {
        header('Location: /adm/index.php');
		return;
	}	
}

/**
 * Ativa e sai do mercado livre marketingplace
 */
if( isset( $GET['acao'] ) && 'LogOutML' === $GET['acao'] ) {
    Logs::CreateLogs('Efetuou Logout Mercado Livre', $_SESSION['admin']['id_usuario']);
    foreach ( $_SESSION as $key => $val ) {
        if(in_array($key, array('access_token', 'access_token_id', 'expires_in', 'refresh_token')) ){
            unset ( $_SESSION[ $key ] );
        }
    }
    
    foreach ( $_SESSION as $key => $val ) {
        if( ! in_array($key, array('access_token', 'access_token_id', 'expires_in', 'refresh_token')) ) {
            if( ! empty( $_SESSION[ $key ] ) ) {
                header('Location: '. $GET['_u']);
                break;
            }
        }
    }
}

include 'rodape.php';