<?php

$token_newsletter = sha1( 'Newsletter_Cadastrar' );

if( isset( $POST['acao'] ) && $POST['acao'] == $token_newsletter ) {
	
	unset( $POST['acao'] );
	
	$return = Newsletter::my_save( $POST );
	
	echo json_encode( $return );
	
}
