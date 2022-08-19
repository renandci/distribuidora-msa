<?php

/**
 * Pagamento via API PagSeguro
 * @param string $mode sandbox ou production
 * @param string $email Email do pagseguro
 * @param string $token Token do pagseguro
 * @param string $pagamento_id Id do pagamento para consultar status
 */
class PagSeguro 
{
    /**
     * Retorna as bandeiras no pagseguro
     * @param type $object
     * @return string
     */
    public static function getPaymentMethodsBrands($object = ''){
        switch( $object )
        {
            case '101' : $str['pagamento'] = 'Visa'; break;
            case '102' : $str['pagamento'] = 'MasterCard'; break;
            case '103' : $str['pagamento'] = 'American Express'; break;
            case '104' : $str['pagamento'] = 'Diners'; break;
            case '105' : $str['pagamento'] = 'Hipercard'; break;
            case '106' : $str['pagamento'] = 'Aura'; break;
            case '107' : $str['pagamento'] = 'Elo'; break;
            case '108' : $str['pagamento'] = 'PLENOCard'; break;
            case '109' : $str['pagamento'] = 'PersonalCard'; break;
            case '110' : $str['pagamento'] = 'JCB'; break;
            case '111' : $str['pagamento'] = 'Discover'; break;
            case '112' : $str['pagamento'] = 'BrasilCard'; break;
            case '113' : $str['pagamento'] = 'FORTBRASIL'; break;
            case '114' : $str['pagamento'] = 'CARDBAN'; break;
            case '115' : $str['pagamento'] = 'VALECARD'; break;
            case '116' : $str['pagamento'] = 'Cabal'; break;
            case '117' : $str['pagamento'] = 'Mais!'; break;
            case '118' : $str['pagamento'] = 'Avista'; break;
            case '119' : $str['pagamento'] = 'GRANDCARD'; break;
            case '120' : $str['pagamento'] = 'Sorocred'; break;
            case '201' : $str['pagamento'] = 'Boleto Bradesco'; break;
            case '202' : $str['pagamento'] = 'Boleto Santander'; break;
            case '301' : $str['pagamento'] = 'Débito Bradesco'; break;
            case '302' : $str['pagamento'] = 'Débito Itaú'; break;
            case '303' : $str['pagamento'] = 'Débito Unibanco'; break;
            case '304' : $str['pagamento'] = 'Débito BB'; break;
            case '305' : $str['pagamento'] = 'Débito Banco Real'; break;
            case '306' : $str['pagamento'] = 'Débito Banrisul'; break;
            case '307' : $str['pagamento'] = 'Débito HSBC'; break;
            case '401' : $str['pagamento'] = 'Saldo PagSeguro'; break;
            case '501' : $str['pagamento'] = 'Oi Paggo'; break;
            case '701' : $str['pagamento'] = 'Depósito BB'; break;
            case '702' : $str['pagamento'] = 'Depósito HSBC'; break;
        }
        return $str;
    }
}