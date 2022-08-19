<?php

abstract class Store 
{
	public $view_pg;
	
	public function __construct($view_pg = '' ) {
		$this->view_pg = ! empty( $view_pg ) ? $view_pg : 'index';
	}
	
	protected final function view($_name, $_vars = []) 
	{
		global $LOJA, $CONFIG, $STORE, $UA_INFO, $MobileDetect, $WebService, $settings, $str, $Images;
		
        $_filename = sprintf('./templates/%s/%s.php', ASSETS, $_name);
        if( ! file_exists($_filename))
            die("View {$_name} not found!");
        
		extract( $_vars );
		
		ob_start();
			include_once $_filename;
		$html = ob_get_contents();
        ob_end_clean();
		
		if ( ! empty( $CONFIG['compact_html'] ) ) {
            echo( CompactarHtml( $html ) );
        } else {
            echo( CompactarHtmlAdm( $html ) );
        }
    }
	
}