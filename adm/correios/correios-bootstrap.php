<?php

if (!class_exists('PhpSigepFPDF')) {
    throw new RuntimeException(
        'Não encontrei a classe PhpSigepFPDF. Execute "php composer.phar install" ou baixe o projeto ' .
        'https://github.com/stavarengo/php-sigep-fpdf manualmente e adicione a classe no seu path.'
    );
}

$config = new \PhpSigep\Config();

$config->setCacheOptions([
	'storageOptions' => [
		// Qualquer valor setado neste atributo será mesclado ao atributos das classes 
		// "\PhpSigep\Cache\Storage\Adapter\AdapterOptions" e "\PhpSigep\Cache\Storage\Adapter\FileSystemOptions".
		// Por tanto as chaves devem ser o nome de um dos atributos dessas classes.
		'ttl' => 10, // "time to live" de 10 segundos
		'enabled' => false,
		'cacheDir' => sys_get_temp_dir(), // Opcional. Quando não informado é usado o valor retornado de "sys_get_temp_dir()"
	],
]);

if( ! empty( $CONFIG['correios']['setting_mode'] ) ) {
	$AccessDataCorreios = new \PhpSigep\Model\AccessData();
	$AccessDataCorreios->setUsuario( $CONFIG['correios']['usuario'] );
	$AccessDataCorreios->setSenha( $CONFIG['correios']['senha'] );
	$AccessDataCorreios->setCnpjEmpresa( $CONFIG['correios']['cnpj_empresa'] );
	$AccessDataCorreios->setCodAdministrativo( $CONFIG['correios']['cod_admin'] );
	$AccessDataCorreios->setNumeroContrato( $CONFIG['correios']['nro_contrato'] );
	$AccessDataCorreios->setCartaoPostagem( $CONFIG['correios']['cartao_postagem'] );
	$AccessDataCorreios->setDiretoria( new \PhpSigep\Model\Diretoria($CONFIG['correios']['diretoria']) );
	$config->setAccessData($AccessDataCorreios);
	$config->setEnv(\PhpSigep\Config::ENV_PRODUCTION);	
	if( strpos( URL_BASE, '.test' ) ) {
		$config->setWsdlAtendeCliente(sprintf('%spublic/LocalAtendeCliente.xml', PATH_ROOT));
	}
} 
else {
	$AccessDataCorreios = new \PhpSigep\Model\AccessDataHomologacao();
	$config->setAccessData($AccessDataCorreios);
	$config->setEnv(\PhpSigep\Config::ENV_DEVELOPMENT);
	$config->setWsdlAtendeCliente(sprintf('%spublic/LocalAtendeCliente.xml', PATH_ROOT));
}

$config->setWsdlCalPrecoPrazo(sprintf('%spublic/LocalCalcPrecoPrazo.xml', PATH_ROOT));
$config->setWsdlRastrearObjetos(sprintf('%spublic/LocalRastro.xml', PATH_ROOT));

\PhpSigep\Bootstrap::start($config);
// print_r($config);