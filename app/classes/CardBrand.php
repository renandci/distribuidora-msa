<?php
/**
 * Retornar os nomes dos respectivos das Bandeiras dos cartões que há no mercado
 * @link https://gist.github.com/claudiosanches/26d9668f21dbdc787472 link do exemplo
 * @author renan henrique <renan@dcisuporte.com.br/>
 */
class CardBrand 
{
    // Brands regex
    public static $brands = array(
        'Visa'       => '/^4\d{12}(\d{3})?$/',
        'Master'     => '/^(5[1-5]\d{4}|677189)\d{10}$/',
        'Diners'     => '/^3(0[0-5]|[68]\d)\d{11}$/',
        'Discover'   => '/^6(?:011|5[0-9]{2})[0-9]{12}$/',
        'Elo'        => '/^((((636368)|(438935)|(504175)|(451416)|(636297))\d{0,10})|((5067)|(4576)|(4011))\d{0,12})$/',
        'Amex'       => '/^3[47]\d{13}$/',
        'JCB'        => '/^(?:2131|1800|35\d{3})\d{11}$/',
        'Aura'       => '/^(5078\d{2})(\d{2})(\d{11})$/',
        'Hipercard'  => '/^(606282\d{10}(\d{3})?)|(3841\d{15})$/',
        'Maestro'    => '/^(?:5[0678]\d\d|6304|6390|67\d\d)\d{8,15}$/',
    );

    private function __construct() {} 
    
    public static function test($cards = '') {
        // Run test
        // // $brand = 'undefined';
        foreach ( CardBrand::$brands as $_brand => $regex ) :
            if ( preg_match( $regex, preg_replace( '/\D/', '', $cards ) ) ) :
                return $_brand;
            endif;
        endforeach;
    }
}
// use
//$rr = \CardBrand::test( preg_replace( '/\D/', '', '5453010000066167' ) );
//echo $rr;