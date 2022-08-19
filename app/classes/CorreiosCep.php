<?php


class CorreiosCep
{
	public $CorreiosInformacoes;
	
	public $client;
	
	/**
     * @link http://php.net/manual/pt_BR/function.mb-detect-encoding.php#84592 Exemplo tirado aqui...
     * QQ: 290359552 
     * conver to Utf8 if $str is not equals to 'UTF-8' 
     */ 
    public function convToUtf8( $str ) { 
		return mb_convert_encoding($str, 'UTF-8', 'ISO-8859-1');
        if( mb_detect_encoding($str, "UTF-8, ISO-8859-1, GBK") != "UTF-8" ) { 
        } 
        else { 
            return $str; 
        } 
    }
	
	public function buscarCep( $cep ) {
		// $cep = preg_replace( '/\D/', '', $cep );
		// $ch = curl_init();
		// curl_setopt_array($ch, [
		// 	CURLOPT_URL 			=> 'http://www.buscacep.correios.com.br/sistemas/buscacep/resultadoBuscaEndereco.cfm',
		// 	CURLOPT_POST			=> true,
		// 	CURLOPT_POSTFIELDS		=> "CEP={$cep}",
		// 	CURLOPT_RETURNTRANSFER	=> true
		// ]);		
		// $response = curl_exec($ch);
		// curl_close($ch);		
		// // $response = html_entity_decode( $response, ENT_QUOTES|ENT_COMPAT, "UTF-8" );
		// $response = html_entity_decode(preg_replace("/U\+([0-9A-F]{4})/", "&#x\\1;", $response), ENT_NOQUOTES, 'UTF-8');		
		// preg_match_all('#<td[^>]*>(.*?)</td>#is', $response, $matches);		
		// // Linha da cidade e uf
		// $explode = explode( '/', strip_tags( $matches[1][2] ) );		
		// // captura a cidade
		// $localidade = $explode[0];		
		// // captura o estado
		// $uf = substr( $explode[1], 0, 2 );
		// $var[] = strip_tags( $matches[1][0] ) ? trim( strip_tags( $matches[1][0] ) ) : '';		
		// $var[] = strip_tags( $matches[1][1] ) ? trim( strip_tags( $matches[1][1] ) ) : '';		
		// $var[] = $localidade;		
		// $var[] = $uf;		
		// // $var[] = strip_tags( substr($matches[1][2], 0, -3) ) ? trim( strip_tags( substr($matches[1][2], 0, -3) ) ) : '';		
		// // $var[] = strip_tags( substr($matches[1][2], -2, 2) ) ? trim( strip_tags( substr($matches[1][2], -2, 2) ) ) : '';		
		// $var[] = strip_tags( $matches[1][3] ) ? trim( strip_tags( $matches[1][3] ) ) : '';		
		// return $var;
	}
	
	public function retornaInformacoesCep($cep)
	{
		$CorreiosInformacoes = $this->buscarCep($cep);

		$this->CorreiosInformacoes = new CorreiosInformacoes();
		
		$this->CorreiosInformacoes->setLogradouro($this->convToUtf8($CorreiosInformacoes[0]));
		$this->CorreiosInformacoes->setBairro($this->convToUtf8($CorreiosInformacoes[1]));
		$this->CorreiosInformacoes->setLocalidade($this->convToUtf8($CorreiosInformacoes[2]));
		$this->CorreiosInformacoes->setUf($this->convToUtf8($CorreiosInformacoes[3]));
		$this->CorreiosInformacoes->setCep($this->convToUtf8($CorreiosInformacoes[4]));
	}
}