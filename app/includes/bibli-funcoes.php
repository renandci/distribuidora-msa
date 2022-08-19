<?php

/**
 * @return chaves_tipo string
 */
function chaves_tipo($int = 0)
{

  switch ($int) {
    case '1':
      $return = 'CPF ou CNPJ';
      break;
    case '2':
      $return = 'Celular';
      break;
    case '3':
      $return = 'Email';
      break;
    case '4':
      $return = 'Agência e conta';
      break;
    case '5':
      $return = 'Chave aleatória';
    default:
      $return = 'Selecione um tipo de chave';
  }
  return $return;
}

/**
 * function test_array_replace
 * @param arr_a Array dados de entrada
 * @param arr_b Array dados de entrada
 * @return array Modifica os array de $arr_a para $arr_b a partir de suas chaves
 */
function test_array_replace(array $arr_a, array $arr_b)
{
  foreach ($arr_b as $k => $v) {
    if (is_array($arr_a[$k])) {
      $arr_a[$k] = test_array_replace($arr_a[$k], $arr_b[$k]);
    } else {
      $arr_a[$k] = $v;
    }
  }
  return $arr_a;
}

/**
 * function buildTree
 * @param array $elements
 * @param array $options['parent_id_column_name', 'children_key_name', 'id_column_name']
 * @param int $parentId
 * @return array
 */
function buildTree(array $elements, $options = [
  'parent_id_column_name' => 'parent_id',
  'children_key_name'   => 'children',
  'id_column_name'     => 'id'
], $parentId = 0)
{

  $branch = array();
  foreach ($elements as $element) {
    if ($element[$options['parent_id_column_name']] == $parentId) {
      $children = buildTree($elements, $options, $element[$options['id_column_name']]);
      if ($children) {
        $element[$options['children_key_name']] = $children;
      }
      $branch[] = $element;
    }
  }
  return $branch;
}

/**
 * A UTF-8 issue I've encountered is that of reading a URL with a non-UTF-8 encoding that is later displayed improperly since file_get_contents() related to it as UTF-8.
 * This small function should show you how to address this issue:
 * @link: http://php.net/manual/en/function.file-get-contents.php#85008
 */
function file_get_contents_utf8($fn)
{

  $content = @file_get_contents($fn, false, stream_context_create(['http' => ['ignore_errors' => true]]));

  if (!strpos($http_response_header[0], "200")) {
    throw new Exception("Cannot access to read contents.");
  }

  return mb_convert_encoding($content, 'UTF-8', mb_detect_encoding($content, 'UTF-8, ISO-8859-1', true));
}

function show_files($local)
{
  if (!$local) {
    return false;
  }
  $data = null;
  if (!is_dir($local)) {
    $data = $local;
  } else {
    $dir = opendir($local);
    while ($file = readdir($dir)) {
      if ($file != "." && $file != ".." && $file != ".htaccess" && $file != "Thumbs.db") {
        $data .= show_files(($local . "/" . $file));
        unset($file);
      }
    }
    closedir($dir);
    unset($dir);
  }

  return $data . ',';
}

/**
 * creates a compressed zip file
 */
// Sample Usage
// $files_to_zip = array(
// 'preload-images/1.jpg',
// 'preload-images/2.jpg',
// 'preload-images/5.jpg',
// 'kwicks/ringo.gif',
// 'rod.jpg',
// 'reddit.gif'
// );
// //if true, good; if false, zip creation failed
// $result = create_zip($files_to_zip,'my-archive.zip');

function create_zip($files = array(), $destination = '', $removedir = false, $overwrite = false)
{
  //if the zip file already exists and overwrite is false, return false
  if (file_exists($destination) && !$overwrite) {
    return false;
  }
  //vars
  $valid_files = array();
  //if files were passed in...
  if (is_array($files)) {
    //cycle through each file
    foreach ($files as $file) {
      //make sure the file exists
      if (file_exists($file)) {
        $valid_files[] = $file;
      }
    }
  }

  //if we have good files...
  if (count($valid_files)) {
    //create the archive
    $zip = new ZipArchive();
    if ($zip->open($destination, $overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
      return false;
    }
    //add the files
    foreach ($valid_files as $file) {
      if (!empty($removedir)) {
        $explode = explode('/', $file);
        $newfile = end($explode);
      } else {
        $newfile = str_replace(['./../../', './../',], [null, null], $newfile);
      }

      $zip->addFile($file, $newfile);
    }
    //debug
    //echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;

    //close the zip -- done!
    $zip->close();

    //check to make sure the file exists
    return file_exists($destination);
  } else {
    return false;
  }
}

// /**
//  * Cria um popup no site, com textos informativos
//  * @param data $data_ini Data inicial em que o popup deve aparecer
//  * @param data $data_fin Data final que o popup deve desaparecer
//  * @param imagem $img Imagen que deve aparecer no popup
//  */
// function popup_site( $data_ini = '', $data_fin='', $img='' )
// {
//     if( ! empty( $img ) ){
//        list($width, $height, $type, $attr) = getimagesize( $img );
//     }
// }

/**
 * Tradutor do status do mercado livre
 * @param string Texto de entrada <br/>Ex: active|paused|closed
 * @return string Texto de saída <br/>Ex: ativo|paudado|finalizado
 */
function StatusML($str = '')
{
  switch ($str) {
    case 'active':
      return 'ativo';
    case 'paused':
      return 'pausado';
    case 'closed':
      return 'finalizado';
  }
}

/**
 * Permite acesso ao mercado livre
 */
function AcessoML($session = '')
{
  if ($session['expires_in'] < time() && substr($_SERVER['REQUEST_URI'], -8) != 'ml-auth.php') {
    header('Location: /adm/mercadolivre/ml-auth.php');
    return;
  }
}

/**
 * Verificar a permissao de acesso ao usuario do sistema
 * @param type $session
 * @param type $pagina
 * @return type
 */
function AcessoPagSession($pagina = 'index', $id_adm = 0)
{
  /**
   * Verificar se SESSION existe!
   * Verficar se Usuario existe!
   */
  if (empty($id_adm) && substr($_SERVER['REQUEST_URI'], -9) != 'login.php') {
    foreach ($_SESSION as $key => $val) {
      unset($_SESSION[$key]);
    }

    session_destroy();

    header('Location: /adm/login.php');
    return;
  }

  if (!in_array($pagina, ['index', 'sair', 'topo', 'rodape'])) {
    $AdmPermissoes = AdmPermissoes::first([
      'conditions' => ['adm_permissoes.id_adm=? and adm_permissoes.pagina=?', (int)$id_adm, $pagina],
      'joins' => ['adm'],
    ]);
    $AdmPermissoesCount = count($AdmPermissoes);
    if ($AdmPermissoesCount > 0 && $AdmPermissoes->adm->permissao != 0 && $AdmPermissoes->acessar == 0) {
      printf("<!--[$pagina %s %s]-->", $AdmPermissoes->adm->permissao, $AdmPermissoes->acessar);
      foreach ($_SESSION as $key => $val) {
        unset($_SESSION[$key]);
      }
      session_destroy();
      header('Location: /adm/login.php');
      return;
    }
  }
}

/**
 *
 * @param type $f
 * @return boolean
 */
function init_fontface($f)
{
  if (!is_array($f)) return;

  $css_line = null;
  foreach ($f as $filename) {
    $css_line .= preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', !file_exists($filename) ? $filename : file_get_contents($filename));
  }
  $Minify = new \MatthiasMullie\Minify\CSS($css_line);
  return sprintf('<style>%s</style>', $Minify->minify());
}

/**
 * Adicionar paginas na adm permissao.
 * Todas as paginas visitadas estão na guia permissao
 * @param $conexao Conexão do banco de dados
 * @param $pagina Pagina a ser visitada
 * @param $usuario Usuario logado
 * @param $permissao Sempre '0' para não ter acesso
 */
function AdicionarVerificaPermissao($pagina = 'index', $id_adm = 0, $permissao = 0)
{

  // echo AdmPermissoes::count(['conditions' => ['adm_permissoes.id_adm=? and adm_permissoes.pagina=? and adm.permissao != 0', $id_adm, $pagina], 'joins' => ['adm']]);

  // if(AdmPermissoes::count(['conditions' => ['id_adm=? and pagina=?', $id_adm, $pagina]]) == 0) {
  // 	if( ! in_array($pagina, ['topo', 'rodape', 'index', 'sair']) ) {
  // 		echo "<!--[{$pagina}]-->" . PHP_EOL;
  // 	}
  // }
}

/**
 * Verificar o nivel de permissao do usuario logado no sistema
 * @param $conexao Conexão do banco de dados
 * @param $pagina Pagina a ser visitada
 * @param $usuario Usuario logado
 * @param $nivel Nivel da pagina a ser visitada|editada|excluida|alterada
 * @return boolean html com dados de nivel
 */
function _P($pagina = 'index', $id_adm = 0, $nivel = 'acessar|incluir|alterar|excluir')
{
  global $CONFIG;

  $pagina = converter_texto($pagina);

  $conditions['joins'] = ['adm'];
  $conditions['select'] = sprintf('adm.permissao, adm_permissoes.pagina, adm_permissoes.%s', implode(', adm_permissoes.', explode('|', $nivel)));
  $conditions['conditions'] = sprintf('adm_permissoes.id_adm = %u AND adm_permissoes.pagina = "%s" ', $id_adm, $pagina);

  $AdmPermissoes = AdmPermissoes::first($conditions);
  $AdmPermissoesCount = count($AdmPermissoes);

  if ($AdmPermissoesCount == 0) {
    return sprintf('acessar="%u" ', 0);
  }

  if ($AdmPermissoes->permissao == 0) {
    return sprintf('acessar="%u" ', 1);
  }

  $AdmPermissoes = $AdmPermissoes->to_array();

  $Lojas = Lojas::first(['conditions' => ['loja_id=?', $CONFIG['loja_id']]]);
  $TOTAL_PRODUTOS = Produtos::count(['conditions' => ['excluir=0'], 'group' => 'codigo_id, id_cor, id_tamanho']);

  // $max_cadastros = (int)($Lojas->max_cadastros <= $TOTAL_PRODUTOS && $AdmPermissoes['pagina'] === $pagina);
  $max_cadastros = (int)($Lojas->plano->produtos <= $TOTAL_PRODUTOS && $AdmPermissoes['pagina'] === $pagina);

  $html = null;
  $html .= (in_array('acessar', array_keys($AdmPermissoes)) ? sprintf('acessar="%u" ', $AdmPermissoes['acessar']) : null);
  $html .= (in_array('incluir', array_keys($AdmPermissoes)) ? sprintf('incluir="%u" ', ($max_cadastros == 0 ? $AdmPermissoes['incluir'] : 0)) : null);
  $html .= (in_array('alterar', array_keys($AdmPermissoes)) ? sprintf('alterar="%u" ', $AdmPermissoes['alterar']) : null);
  $html .= (in_array('excluir', array_keys($AdmPermissoes)) ? sprintf('excluir="%u" ', $AdmPermissoes['excluir']) : null);

  return $html;
}

// function _P($pagina = '', $usuario = 0, $nivel='acessar|incluir|alterar|excluir')
// {
//     $html = '';
// 	$sql = sprintf( ''
// 						 . 'SELECT adm_permissoes.pagina, adm_permissoes.%s '
// 							. 'FROM adm_permissoes '
// 								. 'WHERE adm_permissoes.id_adm = %u AND adm_permissoes.pagina = "%s" AND EXISTS('
// 									. 'SELECT 1 FROM adm '
// 										. 'WHERE adm.permissao != 0 AND adm_permissoes.id_adm = adm.id)', implode(',', explode( '|', $nivel ) ), $usuario, $pagina );

// 	$result = AdmPermissoes::find_by_sql( $sql );
// 	$AdmPermissoesCount = count($result);
// 	if( $AdmPermissoesCount === 0 )
//         return;

// 		$TOTAL_PRODUTOS = Produtos::find_num_rows('SELECT id FROM produtos WHERE excluir = 0 GROUP BY codigo_id');
// 		$RESULT_MAXCADAS = Lojas::find_by_sql('SELECT max_cadastros FROM lojas WHERE dominio=?', [ ASSETS ]);
// 		$TOTAL_MAXCADAS = current($RESULT_MAXCADAS)->to_array();
// 		foreach( $result as $r ) :
//             $r = $r->to_array();
// 			switch( $r ) :
// 				case in_array( 'acessar', array_keys( $r ) ) :
// 					$html .= sprintf('acessar="%u" ', $r['acessar']);
// 				break;
// 				case in_array( 'incluir', array_keys( $r ) ) :
// 					if( $TOTAL_MAXCADAS['max_cadastros'] <= $TOTAL_PRODUTOS && $r['pagina'] === 'produtos' ) {
// 						$html .= 'incluir="0" ';
// 					}
// 					else {
// 						$html .= sprintf('incluir="%u" ', $r['incluir']);
// 					}
// 				break;
// 				case in_array( 'alterar', array_keys( $r ) ):
// 					$html .= sprintf('alterar="%u" ', $r['alterar']);
// 				break;
// 				case in_array( 'excluir', array_keys( $r ) ):
// 					$html .= sprintf('excluir="%u" ', $r['excluir']);
// 				break;
// 			endswitch;
// 		endforeach;
// 	return $html;
// }

/**
 * Calcula o digito veficador na nota NFe
 */
function calcula_dv($chave43)
{
  $i = 42;
  $soma_ponderada = 0;
  $multiplicadores = array(2, 3, 4, 5, 6, 7, 8, 9);
  while ($i >= 0) {
    for ($m = 0; $m < count($multiplicadores) && $i >= 0; $m++) {
      $soma_ponderada += $chave43[$i] * $multiplicadores[$m];
      $i--;
    }
  }
  $resto = $soma_ponderada % 11;
  if ($resto == '0' || $resto == '1') {
    return 0;
  } else {
    return (11 - $resto);
  }
}

/**
 * Compacta os arquivos css se necessario
 * @param string Arquivo de entrada
 * @return boolean
 */

function compress_css($str)
{
  return (new MatthiasMullie\Minify\CSS($str))->minify();
}

function comprimirCss($css)
{
  $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
  $css = preg_replace('#(\r|\n|\t)#', '', $css);
  $css = preg_replace('#[ ]*([,:;\{\}])[ ]*#', '$1', $css);
  $css = strtr($css, array(';}' => '}'));
  return $css;
}

function CompactarHtml($b)
{
  $b = preg_replace_callback('#<([^\/\s<>!]+)(?:\s+([^<>]*?)\s*|\s*)(\/?)>#s', function ($matches) {
    return '<' . $matches[1] . preg_replace('#([^\s=]+)(\=([\'"]?)(.*?)\3)?(\s+|$)#s', ' $1$2', $matches[2]) . $matches[3] . '>';
  }, str_replace("\r", "", $b));
  if (strpos($b, ' style=') !== false) {
    $b = preg_replace_callback('#<([^<]+?)\s+style=([\'"])(.*?)\2(?=[\/\s>])#s', function ($matches) {
      return '<' . $matches[1] . ' style=' . $matches[2] . compress_css($matches[3]) . $matches[2];
    }, $b);
  }
  return preg_replace(array('/<!--[^\[](.*?)[^\]]-->/s', "/[[:blank:]]+/sim"), array('', ' '), str_replace(array("\n", "\r", "\t"), '', $b));
}

function CompactarHtmlAdm($b)
{
  $b = preg_replace_callback('#<([^\/\s<>!]+)(?:\s+([^<>]*?)\s*|\s*)(\/?)>#s', function ($matches) {
    return '<' . $matches[1] . preg_replace('#([^\s=]+)(\=([\'"]?)(.*?)\3)?(\s+|$)#s', ' $1$2', $matches[2]) . $matches[3] . '>';
  }, str_replace("\r", "", $b));

  if (strpos($b, ' style=') !== false) {
    $b = preg_replace_callback('#<([^<]+?)\s+style=([\'"])(.*?)\2(?=[\/\s>])#s', function ($matches) {
      return '<' . $matches[1] . ' style=' . $matches[2] . compress_css($matches[3]) . $matches[2];
    }, $b);
  }
  return preg_replace('/<!--[^\[](.*?)[^\]]-->/s', '', $b);
}

/**
 * @param $color_code
 * @param int $percentage_adjuster
 * @return array|string
 * @author Jaspreet Chahal
 */
function adjustColorLightenDarken($color_code, $percentage_adjuster = 0)
{
  $percentage_adjuster = round($percentage_adjuster / 100, 2);
  if (is_array($color_code)) :
    $r = $color_code["r"] - (round($color_code["r"]) * $percentage_adjuster);
    $g = $color_code["g"] - (round($color_code["g"]) * $percentage_adjuster);
    $b = $color_code["b"] - (round($color_code["b"]) * $percentage_adjuster);

    return array(
      "r" => round(max(0, min(255, $r))),
      "g" => round(max(0, min(255, $g))),
      "b" => round(max(0, min(255, $b)))
    );
  elseif (preg_match("/#/", $color_code)) :
    $hex = str_replace("#", "", $color_code);
    $r = (strlen($hex) == 3) ? hexdec(substr($hex, 0, 1) . substr($hex, 0, 1)) : hexdec(substr($hex, 0, 2));
    $g = (strlen($hex) == 3) ? hexdec(substr($hex, 1, 1) . substr($hex, 1, 1)) : hexdec(substr($hex, 2, 2));
    $b = (strlen($hex) == 3) ? hexdec(substr($hex, 2, 1) . substr($hex, 2, 1)) : hexdec(substr($hex, 4, 2));
    $r = round($r - ($r * $percentage_adjuster));
    $g = round($g - ($g * $percentage_adjuster));
    $b = round($b - ($b * $percentage_adjuster));

    return "#" . str_pad(dechex(max(0, min(255, $r))), 2, "0", STR_PAD_LEFT)
      . str_pad(dechex(max(0, min(255, $g))), 2, "0", STR_PAD_LEFT)
      . str_pad(dechex(max(0, min(255, $b))), 2, "0", STR_PAD_LEFT);

  endif;
}

/**
 * Convereter string em apenas numeros inteiros
 * @param type string $str String simple que contenha numeros
 * @return type string|int Retorna somente numeros
 */
function soNumero($str, $count = false)
{
  if (empty($str)) return;
  $str = preg_replace("/[^0-9]/", '', $str);
  return !empty($count) ? strlen($str) : $str;
}

function download($arquivo)
{
  try {
    $tamanho = @filesize("$arquivo");

    if (empty($tamanho))
      throw new Exception('Arquivo Inválido!');

    // pega extensão do arquivo
    $ext = explode(".", $arquivo);

    // aqui bloqueia downloads indevido
    if ($ext[1] == "php" || $ext[1] == "htaccess")
      throw new Exception("Arquivo não autorizado para download!");

    // envia todos cabecalhos HTTP para o browser (tipo, tamanho, etc..)
    header("Content-Type: application/save");
    header("Content-Length: $tamanho");
    header("Content-Disposition: attachment; filename=$arquivo");
    header("Content-Transfer-Encoding: binary");

    // nesse momento ele le o arquivo e envia
    $fp = fopen("$arquivo", "r");
    fpassthru($fp);
    fclose($fp);
  } catch (Exception $e) {
    return $e->getMessage();
  }
}

/**
 * Apenas uma pequena função que imita o string_escape original, mas que não precisa de uma conexão mysql ativa.
 * Poderia ser implementado como uma função estática em uma classe de banco de dados.
 * @link http://php.net/manual/pt_BR/function.mysql-real-escape-string.php
 * @param string Entrada da query
 * @return boolean query com tratamento de string injection
 */
function my_escape($inp)
{
  if (is_array($inp))
    return array_map(__METHOD__, $inp);

  if (!empty($inp) && is_string($inp)) {
    return str_replace(['\\', "\0", "\n", "\r", "'", '"', "\x1a"], ['\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'], $inp);
  }

  return $inp;
}

function queryInjection($query)
{
  $args = func_get_args();
  $query = array_shift($args);

  foreach ($args as $key => $arg) {
    if (is_string($arg)) {
      $args[$key] = htmlspecialchars(my_escape($arg), ENT_COMPAT | ENT_XHTML, 'UTF-8');
    }
  }

  array_unshift($args, $query);
  $query = call_user_func_array('sprintf', $args);
  return $query;
}

/**
 * Gerar mascara para inputs como cpf, cnpj, telefone, etc..
 */
function mask($val, $mask)
{
  $k = 0;
  $maskared = '';
  for ($i = 0; $i <= strlen($mask) - 1; $i++) {
    if ($mask[$i] == '#') {
      if (isset($val[$k])) {
        $maskared .= $val[$k++];
      }
    } else {
      if (isset($mask[$i])) {
        $maskared .= $mask[$i];
      }
    }
  }
  return $maskared;
}

function mask_tel_cel($string)
{
  $string = soNumero($string);
  $mascara = strlen($string) == 10 ? "(##) ####-####" : "(##) #####-####";
  return mask($string, $mascara);
}

/**
 * Retorna os generos dos produtos
 * @param string
 * @param reverse
 * @return boolean com verificacao da categoria Masculino|Feminino|Neutro
 */
function checkCategoria($str, $reverse = false)
{
  switch ($str) {
    case 'M':
    case 'Menino':
      if (!$reverse)
        return 'M';

      return 'Menino';

    case 'F':
    case 'Menina':
      if (!$reverse)
        return 'F';

      return 'Menina';

    case 'N':
    case 'Neutro':
      if (!$reverse)
        return 'N';

      return 'Neutro';
    default:
      return false;
  }
}

/**
 * Gera uma string somente de uma unico caracter
 * @param string Adicionar uma string ao valor
 * @return string Unico caracter maiusculo
 */
function StringLetra($string)
{
  if (!empty($string))
    return strtoupper(substr(trim(converter_texto($string)), 0, 1));
}

/**
 * Gera um novo <b>código</b> para o produto e verfica se exite um e o retorna<br/>
 * O <b>código</b> é composto por 2 letras com as iniciais do <br/>
 * <b>nome do produto</b> seguido de zero e um número inteiro '<b>Caso não haja o campo preenchido</b>'
 * @param string Duas Letras do nome do produto
 * @param int Id do produto ou qualquer numero que deseje caso seja preenchido o campo
 * @param string Código do Produto se já existir
 * @return boolean Retorna o código do produto
 */
function CodProduto($letra = '', $id = '', $codigo = '')
{
  if (!empty($codigo))
    return $codigo;

  return strtoupper(substr(converter_texto($letra), 0, 2) . str_pad($id, 5, '0', STR_PAD_LEFT));
}

/**
 *
 * @param type $val String de esntrada
 * @return boolean
 */
function is_not_null($val)
{
  return !is_null($val);
}

/**
 * @param type $A String como [2,3,65,45,8]
 * @param type $B string que deseja ser veirifcado (int)1
 * @return boolean checar string Retorna um true|false para os paramentros abaixo
 */
function checked($A, $B)
{
  $array = explode(',', str_replace(array('[', ']'), null, $A));
  return in_array($B, $array) ? true : false;
}

/**
 * Selecionar|Checar|Comparar
 * @param string String que será verificada
 * @param array|string Array de comparacao ou string para comparcao
 * @param element Element html checked|selected
 * @return boolean
 */
function checked_html($string, $array, $type = 'checked')
{
  if (is_array($array))
    return in_array($string, $array) ? " {$type} " : '';

  if (!empty($string) && !empty($array))
    if ($string == $array)
      return " {$type} ";
}

function checked_comparation($string, $procurar)
{
  return (strlen(stristr($string, $procurar)) > 0);
}

/**
 * Checar se produto é frete gratis
 * @param string Preco do produto a ser verificado
 * @param array Dados com cadastro de fretes
 * @return boolean false|true
 */
function CheckFreteGratis($price, $array)
{
  if (empty($array)) {
    return;
  }
  foreach ($array as $prices) {
    if ($prices <= $price && $prices > 0) {
      return true;
    }
  }
}

/**
 *
 * @return type String do IP da Maquina do Usuario
 */
function retornaIpReal()
{
  foreach (array(
    'HTTP_CLIENT_IP',
    'HTTP_X_FORWARDED_FOR',
    'HTTP_X_FORWARDED',
    'HTTP_X_CLUSTER_CLIENT_IP',
    'HTTP_FORWARDED_FOR',
    'HTTP_FORWARDED',
    'REMOTE_ADDR'
  ) as $key) :
    if (array_key_exists($key, $_SERVER) === true) :
      foreach (explode(',', $_SERVER[$key]) as $ip) :
        if (filter_var($ip, FILTER_VALIDATE_IP) !== false) :
          return $ip;
        endif;
      endforeach;
    endif;
  endforeach;
}

/**
 * Converte as string em url amigaveis
 * @param string Entrada a ser convertida
 * @return string Saida como uma url amigavel
 */
function converter_texto($str, $split = '-', $type = 'strtolower')
{
  $str = strip_tags($str);
  $str = trim($str);
  $str = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
  $str = preg_replace("/[^a-zA-Z0-9\/_| -]/", '', $str);
  switch ($type) {
    case 'strtolower':
      $str = strtolower($str);
      break;
    case 'strtoupper':
      $str = strtoupper($str);
      break;
  }
  $str = trim($str, '-');
  $str = preg_replace("/[\/_| -]+/", $split, $str);
  return $str;
}

function boas_vindas()
{
  $hora = date('G');
  switch (date('G')) {
    case (($hora >= 0) and ($hora < 6)):
      $mensagem = "Boa madrugada";
      break;
    case (($hora >= 6) and ($hora < 12)):
      $mensagem = "Bom dia";
      break;
    case (($hora >= 12) and ($hora < 18)):
      $mensagem = "Boa tarde";
      break;
    default:
      $mensagem = "Boa noite";
      break;
  }
  return $mensagem;
}


function dinheiro($get_valor)
{
  if (strpos("[" . $get_valor . "]", ",")) {
    $source = array('.', ',');
    $replace = array('', '.');
    // remove os pontos e substitui a virgula pelo ponto
    $get_valor = str_replace($source, $replace, $get_valor);
    // retorna o valor formatado para gravar no banco
  }
  return $get_valor;
}

// function dinheiro($valor) {
// $valor = str_ireplace(".","",$valor);
// $valor = str_ireplace(",",".",$valor);
// return $valor;
// }

function text_status_vendas($status)
{
  $var = null;
  switch ($status) {
    case 0:
      $var   = 'Pedido excluido';
      break;
    case 1:
      $var   = 'Pedido realizado';
      break;
    case 2:
      $var   = 'Aguardando pagamento';
      break;
    case 3:
      $var   = 'Pagamento aprovado';
      break;
    case 4:
      $var   = 'Pagamento não aprovado';
      break;
    case 5:
      $var   = 'Pagamento não efetuado';
      break;
    case 6:
      $var   = 'Em produção';
      break;
    case 7:
      $var   = 'Em separação de estoque';
      break;
    case 8:
      $var   = 'Em transporte';
      break;
    case 9:
      $var   = 'Pedido entregue';
      break;
    case 10:
      $var   = 'Pedido cancelado';
      break;
    case 11:
      $var   = 'Pagamento em análise';
      break;
    case 20:
      $var   = 'Todos';
      break;
  }
  return $var;
}

/**
 * Alterar o estoque de venda e produtos
 * @param $conexao Conexao do banco de dados
 * @param $pedido_id id do pedido
 * @return boolean Caso não consiga fazer as alterações, retorna um erro.
 */
function NormalizeEstoque($pedido_id = '')
{
  // global $conexao;
  // if(
  // mysqli_num_rows(
  // mysqli_query( $conexao, sprintf( 'SELECT COUNT(*) FROM pedidos_logs WHERE status=3 AND id_pedido=%u', $pedido_id ) ) ) == 0 ) {
  // echo
  // $sql = ''
  // . 'UPDATE produtos '
  // . 'JOIN pedidos_vendas ON pedidos_vendas.id_produto = produtos.id '
  // . 'SET produtos.estoque = (produtos.estoque - pedidos_vendas.quantidade) '
  // . sprintf( 'WHERE pedidos_vendas.id_pedido=%u AND produtos.estoque >= 0', $pedido_id );
  // mysqli_query( $conexao, $sql );
  // if( mysqli_affected_rows( $conexao ) == 0 )
  // throw new Exception('Não foi possivel baixar o estoque do produto!');
  // }
  // else if(
  // mysqli_num_rows(
  // mysqli_query( $conexao, sprintf( 'SELECT COUNT(*) FROM pedidos_logs WHERE status NOT IN(10,5,4) AND id_pedido=%u', $pedido_id ) ) ) == 0 ) {
  // $sql = ''
  // . 'UPDATE produtos '
  // . 'JOIN pedidos_vendas ON pedidos_vendas.id_produto=produtos.id '
  // . 'SET produtos.estoque = (produtos.estoque + pedidos_vendas.quantidade) '
  // . sprintf( 'WHERE pedidos_vendas.id_pedido=%u AND produtos.estoque >= 0', $pedido_id );
  // mysqli_query( $conexao, $sql );
  // if( mysqli_affected_rows( $conexao ) == 0 )
  // throw new Exception('Não foi possivel aumentar o estoque do produto!');
  // }
}

function logs($descricao, $usuario = '', $conexao = '')
{
  // global $conexao;
  //    if( ! $conexao ) return false;
  // $usuarioid = $usuario != '' ? $usuario : $_SESSION['admin']['id_usuario'];
  // mysqli_query($conexao,"DELETE FROM logs WHERE data < DATE_SUB(NOW(), INTERVAL 90 DAY)");
  // mysqli_query($conexao,
  // queryInjection("INSERT INTO logs (acao, adm_id, ip, data) VALUES ('%s', '%s', '%s', now())",
  // $descricao, $usuarioid, retornaIpReal()));
}

function pedidos_logs($conexao, $id_pedido = '', $id_adm = '', $descricao = '', $status = '')
{
  // mysqli_query($conexao,
  // queryInjection(
  // "INSERT INTO pedidos_logs (id_pedido, id_adm, descricao, status, data_envio) VALUES (%u, %u, '%s', %u, now())", $id_pedido, $id_adm, $descricao, $status ) );
  // if( mysqli_affected_rows( $conexao ) == 0 )
  // throw new Exception('Não foi possivel fazer alteração de status do pedido!');
}

/**
 * Gerar desconto para o boleto
 * @param $preco Entrada do Preco
 * @param $desconto Entrada do desconto para o boleto
 */
function desconto_boleto($preco = 0.00, $desconto = 0.00)
{
  if (!empty($preco) && $preco > 0)
    return number_format(boleto_desconto($preco, $desconto), 2, '.', '');
  // return number_format(($preco - ($desconto/100) * $preco), 2, '.', '');
}

/**
 * Gerar Desconto para boleto
 * @param $vl Valor total sem frete
 * @param $desconto Valor do desconto
 */
function boleto_desconto($vl = 0.00, $desconto = 0.00)
{
  return ($vl - ($desconto / 100) * $vl);
}

function tituloNomes(
  $string,
  $delimiters = array(" ", "-", ".", "'", "O'", "Mc"),
  $exceptions = array("de", "se", "da", "em", "dos", "das", "a", "do", "I", "II", "III", "IV", "V", "VI")
) {
  /*
	 * Exceptions in lower case are words you don't want converted
	 * Exceptions all in upper case are any words you don't want converted to title case
	 *   but should be converted to upper case, e.g.:
	 *   king henry viii or king henry Viii should be King Henry VIII
	 */
  $string = mb_convert_case($string, MB_CASE_TITLE, "UTF-8");
  foreach ($delimiters as $dlnr => $delimiter) {
    $words = explode($delimiter, $string);
    $newwords = array();
    foreach ($words as $wordnr => $word) {
      if (in_array(mb_strtoupper($word, "UTF-8"), $exceptions)) {
        // check exceptions list for any words that should be in upper case
        $word = mb_strtoupper($word, "UTF-8");
      } elseif (in_array(mb_strtolower($word, "UTF-8"), $exceptions)) {
        // check exceptions list for any words that should be in upper case
        $word = mb_strtolower($word, "UTF-8");
      } elseif (!in_array($word, $exceptions)) {
        // convert to uppercase (non-utf8 only)
        $word = ucfirst($word);
      }
      array_push($newwords, $word);
    }
    $string = join($delimiter, $newwords);
  }
  return $string;
}

/**
 * Conversor de datas para mysql
 * @param type $data
 * @return type
 */
function converterDatas($data)
{
  $data = str_replace(array('\'', '-', '.', ',', ' '), '/', $data);
  $data = implode("-", array_reverse(explode("/", $data)));
  return $data;
}

function validaEmail($email)
{
  $conta = "^[a-zA-Z0-9\._-]+@";
  $domino = "[a-zA-Z0-9\._-]+.";
  $extensao = "([a-zA-Z]{2,4})$";
  $pattern = $conta . $domino . $extensao;
  if (@preg_replace($pattern, $email))
    return true;
  else
    return false;
}

function media_produto($MEDIA)
{
  switch ($MEDIA) {
    case 0:
      return 'background-position: 0 0;'; // 0
    case ($MEDIA >= 0.5 && $MEDIA <= 0.9):
      return 'background-position: 0 -22px';   // meio
    case ($MEDIA >= 1.0 && $MEDIA <= 1.4):
      return 'background-position: 0 -47px';   // 1
    case ($MEDIA >= 1.5 && $MEDIA <= 1.9):
      return 'background-position: 0 -68px';  // 1 e meio
    case ($MEDIA >= 2.0 && $MEDIA <= 2.4):
      return 'background-position: 0 -92px';  // 2
    case ($MEDIA >= 2.5 && $MEDIA <= 2.9):
      return 'background-position: 0 -114px';  // 2 e meio
    case ($MEDIA >= 3.0 && $MEDIA <= 3.4):
      return 'background-position: 0 -137px';  // 3
    case ($MEDIA >= 3.5 && $MEDIA <= 3.9):
      return 'background-position: 0 -160px';  // 3 e meio
    case ($MEDIA >= 4.0 && $MEDIA <= 4.4):
      return 'background-position: 0 -183px';  // 4
    case ($MEDIA >= 4.5 && $MEDIA <= 4.9):
      return 'background-position: 0 -206px';  // 4 e meio
    case ($MEDIA >= 5.0):
      return 'background-position: 0 -229px';           // 5 a superior
  }
}

/**
 * Define se cliente está logando e redirecionado para outra url
 * @param string Sessao do cliente
 * @param string Url para redirecionamento web<br/> Prefenciamente definir a url do site total
 * @exemple Ex: http://www.benditavaidade.dev/identificacao/foto/
 */
function login_existe($SESSION = '', $URL_BASE = '')
{
  if (empty($SESSION) || Clientes::count(['conditions' => ['md5(id)=?', $SESSION]]) === 0) :
    header('Location: ' . URL_BASE . 'identificacao/login/?_u=' . (!empty($URL_BASE) ? $URL_BASE : $_SERVER['HTTP_REFERER']));
    return;
  endif;
}
//                    5     12            35
function parcelamento($total, $parcelas_max, $parcela_min)
{ //                       5    /   35  = 0,41
  $parcelas = floor($total / $parcela_min);

  //
  if ($parcelas > $parcelas_max) {
    $parcelas = $parcelas_max;
  } elseif ($parcelas < 1) {
    $parcelas = 1; // se a parcela minima nao for atingida
  }

  return $parcelas;
}

/**
 *
 * @param type $data_inicial Data Inicial Ex: 00/00/0000
 * @param type $data_final Data Final Ex: 00/00/0000
 */
function intervalo_data($data_inicial = '', $data_final = '')
{
  // Cria uma função que retorna o timestamp de uma data no formato DD/MM/AAAA
  function geraTimestamp($data)
  {
    $partes = explode('/', $data);
    return mktime(0, 0, 0, $partes[1], $partes[0], $partes[2]);
  }
  // Usa a função criada e pega o timestamp das duas datas:
  $time_inicial = geraTimestamp($data_inicial);
  $time_final = geraTimestamp($data_final);
  // Calcula a diferença de segundos entre as duas datas:
  $diferenca = $time_final - $time_inicial; // 19522800 segundos
  // Calcula a diferença de dias
  $dias = (int)floor($diferenca / (60 * 60 * 24)); // 225 dias
  return $dias;
}

/**
 * Calcula o valor do frete
 * @param $tipos Array SEDEX|PAC|JADLOG
 * @param $cepini CEP de postagem
 * @param $cepfin CEP do destinatario
 * @param $peso Valor do peso produto
 */
//function calcular_preco_frete($conexao = '', $tipos='SEDEX|PAC|JADLOG', $cep='14900000', $peso='0.300')
//{
//    global $conexao;
//    if( ! $conexao )
//		throw new Exception("Conexão não inicializada");
//
//	$sql = ''
//	     . 'SELECT '
//	     . '*,'
//         . '(valor + valor_extra) as valor '
//	     . 'FROM fretes '
//	     . 'WHERE '
//	     . 'cep_ini <= %u AND cep_fini >= %u AND '
//	     . 'peso_ini <= %s AND peso_fini >= %s AND (tipo="' . implode( '" OR tipo="', explode( '|', $tipos ) ) . '") LIMIT ' . count( explode( '|', $tipos ) );
//
//	$result = mysqli_query( $conexao, queryInjection( $sql, $cep, $cep, $peso, $peso ) );
//	while( $rs = mysqli_fetch_assoc( $result ) )
//	{
//		$str[ $rs['tipo'] ] = $rs;
//	}
//	mysqli_free_result( $result );
//	return $str;
//}

/**
 * Calcular o frete a partir dos correios
 * @param string $Servico
 * @param int $CepOrigem
 * @param int $CepDestino
 * @param double $Peso
 * @param double $Altura
 * @param double $Largura
 * @param double $Comprimento
 * @return array
 */
function calcular_preco_frete($Servico = 'PAC|SEDEX', $CepOrigem = '14900000', $CepDestino = '15905088', $Peso = '0.300', $Altura = '5', $Largura = '11', $Comprimento = '16', $nCdEmpresa = '08082650', $sDsSenha = '564321')
{
  $ReturnServicos = explode('|', $Servico);
  $NewServicos    = implode(',', explode('|', str_replace(explode('|', $Servico), ['40010', '41106'], $Servico)));
  // $NewServicos    = implode(',', explode('|', str_replace( explode( '|', $Servico ), array('04014', '04510'), $Servico )));
  // $NewServicos    = implode(',', explode('|', str_replace( explode( '|', $Servico ), array('04553', '04596'), $Servico )));
  $Altura     = $Altura >= 5 ? $Altura : 5;
  $Largura     = $Largura >= 11 ? $Largura : 11;
  $Comprimento   = $Comprimento >= 16 ? $Comprimento : 16;
  $Peso       = $Peso < 0 ? $Peso : 1;

  $CepOrigem     = soNumero($CepOrigem);
  $CepDestino   = soNumero($CepDestino);

  usort($ReturnServicos, function ($a, $b) {
    if ($a == $b) return 0;
    return ($a > $b ? 0 : 1);
  });

  $explode = explode(',', $NewServicos);
  usort($explode, function ($a, $b) {
    if ($a == $b) return 0;
    return ($a > $b ? 0 : 1);
  });

  $x = 0;
  // $correios_url = "http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?nCdEmpresa={$nCdEmpresa}&sDsSenha={$sDsSenha}&sCepOrigem={$CepOrigem}&sCepDestino={$CepDestino}&nVlPeso={$Peso}&nCdFormato=1&nVlComprimento={$Comprimento}&nVlAltura={$Altura}&nVlLargura={$Largura}&sCdMaoPropria=n&nVlValorDeclarado=0&sCdAvisoRecebimento=n&nCdServico={$NewServicos}&nVlDiametro=0&StrRetorno=xml&nIndicaCalculo=3";

  $correios_url = "http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?nCdEmpresa={$nCdEmpresa}&sDsSenha={$sDsSenha}&sCepOrigem={$CepOrigem}&sCepDestino={$CepDestino}&nVlPeso={$Peso}&nCdFormato=1&nVlComprimento={$Comprimento}&nVlAltura={$Altura}&nVlLargura={$Largura}&sCdMaoPropria=n&nVlValorDeclarado={$Valor}&sCdAvisoRecebimento=n&nCdServico={$Servico}&nVlDiametro=0&StrRetorno=xml&nIndicaCalculo=3";
  try {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $correios_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    $curl_exec = curl_exec($ch);
    $curl_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($curl_code != 200) {
      throw new Exception('Conexão temporariamente fora do ar!');
    }

    $return = simplexml_load_string($curl_exec);

    foreach (@$return->cServico as $cKey => $cServico) {
      $r[$ReturnServicos[$x]]['Codigo'] =  current($cServico->Codigo);
      $r[$ReturnServicos[$x]]['valor'] = dinheiro(current($cServico->Valor));
      $r[$ReturnServicos[$x]]['prazo'] = current($cServico->PrazoEntrega);
      $r[$ReturnServicos[$x]]['valorSemAdicionais'] =  dinheiro(current($cServico->ValorSemAdicionais));
      $r[$ReturnServicos[$x]]['valorMaoPropria'] = dinheiro(current($cServico->ValorMaoPropria));
      $r[$ReturnServicos[$x]]['valorAvisoRecebimento'] = dinheiro(current($cServico->ValorAvisoRecebimento));
      $r[$ReturnServicos[$x]]['valorvalorDeclarado'] = dinheiro(current($cServico->ValorvalorDeclarado));
      $r[$ReturnServicos[$x]]['EntregaDomiciliar'] = dinheiro(current($cServico->EntregaDomiciliar));
      $r[$ReturnServicos[$x]]['EntregaSabado'] = current($cServico->EntregaSabado);
      $r[$ReturnServicos[$x]]['Erro'] = current($cServico->Erro);
      $r[$ReturnServicos[$x]]['MsgErro'] = current($cServico->MsgErro);
      $r[$ReturnServicos[$x]]['obsFim'] =  current($cServico->obsFim);

      if (!empty(current($cServico->Erro))) {
        if ($cServico->Erro == "010" or $cServico->Erro == "011") {
          $r[$ReturnServicos[$x]]['prazo'] .= ' ( ' . $cServico->MsgErro . ' )';
          $r[$ReturnServicos[$x]]['Erro'] = '';
        } else
          $r[$ReturnServicos[$x]]['Erro'] = 'Cep ' . mask($CepDestino, '#####-###') . ' inválido ou incorreto.';
      }

      $x++;
    }
  } catch (Exception $e) {
    for ($x = 0; $x <= count($ReturnServicos); $x++) {
      $r[$ReturnServicos[$x]]['Codigo'] =  null;
      $r[$ReturnServicos[$x]]['valor'] = 0.00;
      $r[$ReturnServicos[$x]]['Erro'] = 'Cep ' . mask($CepDestino, '#####-###') . ' inválido ou incorreto. ' . $e->getMessage();
      $r[$ReturnServicos[$x]]['prazo'] = 'Não foi possível calcular o frete ' . mask($CepDestino, '#####-###');
      $r[$ReturnServicos[$x]]['erro_frete'] = true;
    }
  }

  return $r;
}

// function calcular_fretejadlog($pesoCubagem = 1, $cepLocal = 14900000, $cepDestino = 14900000, $Servico = 'JADLOG') {
// global $CONFIG;
// $return = null;
// try {
// $JadLogNew = new JadLogNew($CONFIG['jadlog']['token']);

// $frete = [
// "frete" => [
// [
// "cepori" => $cepLocal,
// "cepdes" => $cepDestino,
// "frap" => null,
// "peso" => $pesoCubagem,
// "conta" => null,
// "contrato" => $CONFIG['jadlog']['nrContrato'],
// "modalidade" => 3,
// "tpentrega" => "D",
// "tpseguro" => "N",
// "vldeclarado" => 0,
// "vlcoleta" => 1.50
// ]
// ]
// ];
// $frete_valor = $JadLogNew->post('/frete/valor', $frete);
// $frete_valor = $frete_valor['body']->frete[0]->vltotal;

// $JagLogCidades = JagLogCidades::first(['conditions' => ['cepini <= ? and cepfin >= ?', $cepDestino, $cepDestino]]);

// // $return['prazo_entrega'] = (INT)$JagLogCidades->prazo;

// // $return['valor_frete'] = number_format($frete_valor, 2, ',', '.');

// $return[$Servico]['Codigo'] = null;
// $return[$Servico]['valor'] = dinheiro($frete_valor);
// $return[$Servico]['prazo'] = $JagLogCidades->prazo;
// $return[$Servico]['valorSemAdicionais'] = null;
// $return[$Servico]['valorMaoPropria'] = null;
// $return[$Servico]['valorAvisoRecebimento'] = null;
// $return[$Servico]['valorvalorDeclarado'] = null;
// $return[$Servico]['EntregaDomiciliar'] = null;
// $return[$Servico]['EntregaSabado'] = null;
// $return[$Servico]['Erro'] = null;
// $return[$Servico]['MsgErro'] = null;
// $return[$Servico]['obsFim'] = null;

// } catch(Exception $e) {
// return;
// }
// return $return;
// }

function array_columns(array $arr, array $keysSelect)
{
  $keys = array_flip($keysSelect);
  $filteredArray = array_map(function ($a) use ($keys) {
    return array_intersect_key($a, $keys);
  }, $arr);

  return $filteredArray;
}

// function calcular_jadlog($pesoCubagem = 1, $cepLocal = 14900000, $cepDestino = 14900000, $Servico = 'JADLOG|JADLOG-ECONOMICO') {
function calcular_fretejadlog($pesoCubagem = 1, $cepLocal = 14900000, $cepDestino = 14900000, $Servico = 'JADLOG', $VlColeta = 1.50)
{
  global $CONFIG;
  $return = [];

  try {
    $Servicos = explode('|', $Servico);
    $ServicosCount = count($Servicos);

    usort($Servicos, function ($a, $b) {
      if ($a == $b) return 0;
      return ($a < $b ? 0 : 1);
    });

    list($jadlog, $jadlogeconomico) = $Servicos;

    $JadLogNew = new JadLogNew($CONFIG['jadlog']['token']);

    $frete = [
      'frete' => [
        [
          'cepori' => $cepLocal,
          'cepdes' => $cepDestino,
          'frap' => null,
          'peso' => $pesoCubagem,
          'conta' => $CONFIG['jadlog']['contaCorrente'],
          'contrato' => $CONFIG['jadlog']['nrContrato'],
          'modalidade' => 3,
          'tpentrega' => 'D',
          'tpseguro' => 'N',
          'vldeclarado' => 0,
          'vlcoleta' => $VlColeta
        ], [
          'cepori' => $cepLocal,
          'cepdes' => $cepDestino,
          'frap' => null,
          'peso' => $pesoCubagem,
          'conta' => $CONFIG['jadlog']['contaCorrente'],
          'contrato' => $CONFIG['jadlog']['nrContrato'],
          'modalidade' => 4,
          'tpentrega' => 'D',
          'tpseguro' => 'N',
          'vldeclarado' => 0,
          'vlcoleta' => $VlColeta
        ], [
          'cepori' => $cepLocal,
          'cepdes' => $cepDestino,
          'frap' => null,
          'peso' => $pesoCubagem,
          'conta' => $CONFIG['jadlog']['contaCorrente'],
          'contrato' => $CONFIG['jadlog']['nrContrato'],
          'modalidade' => 5,
          'tpentrega' => 'D',
          'tpseguro' => 'N',
          'vldeclarado' => 0,
          'vlcoleta' => $VlColeta
        ]
      ]
    ];

    // Calcula os valores
    $ReturnFrete = $JadLogNew->post('/frete/valor', $frete);

    // Captura possiveis erros
    if ($ReturnFrete['httpCode'] !== 200)
      throw new Exception('Sem conexão');

    // Captura possiveis erros
    if ($ReturnFrete['body']->error->id !== null)
      throw new Exception($ReturnFrete['body']->error->descricao);

    // Trata todo o stdClass para um array
    $ReturnFreteNew = json_decode(json_encode($ReturnFrete['body']->frete), true);

    // Captura a coluna do array para comparacao
    $ReturnFreteKeys = array_column($ReturnFreteNew, 'vltotal');

    // Ordena o array com sua coluna comparada
    array_multisort($ReturnFreteKeys, SORT_ASC, $ReturnFreteNew);

    // Adiciona o serviço no array com sua primeira ocorrencia 'current'
    $ReturnFreteNew = current($ReturnFreteNew);
    $ReturnFreteNew['valor'] = $ReturnFreteNew['vltotal'];
    unset($ReturnFreteNew['vltotal']);
    $return[$jadlog] = $ReturnFreteNew;

    // adicao dos servicos complementares
    if ($ServicosCount > 1) {
      $econonic = [
        'frete' => [
          [
            'cepori' => $cepLocal,
            'cepdes' => $cepDestino,
            'frap' => null,
            'peso' => $pesoCubagem,
            'conta' => $CONFIG['jadlog']['contaCorrente'],
            'contrato' => $CONFIG['jadlog']['nrContrato'],
            'modalidade' => 9,
            'tpentrega' => 'D',
            'tpseguro' => 'N',
            'vldeclarado' => 0,
            'vlcoleta' => 1.50
          ], [
            'cepori' => $cepLocal,
            'cepdes' => $cepDestino,
            'frap' => null,
            'peso' => $pesoCubagem,
            'conta' => $CONFIG['jadlog']['contaCorrente'],
            'contrato' => $CONFIG['jadlog']['nrContrato'],
            'modalidade' => 40,
            'tpentrega' => 'R',
            'tpseguro' => 'N',
            'vldeclarado' => 0,
            'vlcoleta' => 1.50
          ]
        ]
      ];

      $ReturnEcomonic = $JadLogNew->post('/frete/valor', $econonic);

      if ($ReturnEcomonic['body']->error->id !== null)
        throw new Exception($ReturnEcomonic['body']->error->descricao);

      // Trata todo o stdClass para um array
      $ReturnFreteEcomonic = json_decode(json_encode($ReturnEcomonic['body']->frete), true);

      // Captura a coluna do array para comparacao
      $ReturnFreteEcomonicKeys = array_column($ReturnFreteEcomonic, 'vltotal');

      // Ordena o array com sua coluna comparada
      array_multisort($ReturnFreteEcomonicKeys, SORT_ASC, $ReturnFreteEcomonic);

      // Adiciona o serviço no array com sua primeira ocorrencia 'current'
      $ReturnFreteEcomonic = current($ReturnFreteEcomonic);
      $ReturnFreteEcomonic['valor'] = $ReturnFreteEcomonic['vltotal'];
      unset($ReturnFreteEcomonic['vltotal']);
      $return[$jadlogeconomico] = $ReturnFreteEcomonic;
    }

    return $return;
  } catch (Exception $e) {
    return [];
    return $e->getMessage();
  }
}


// exit(sprintf('<pre>%s</pre>', print_r(calcular_fretejadlog(1, 14900000, 15900000, 'JADLOG|JADLOG-ECONOMICO'), true)));

/**
 *
 * @param type $json
 * @return type
 */
function form_safe_json($json)
{
  $json = empty($json) ? '[]' : $json;
  $search = array('\\', "\n", "\r", "\f", "\t", "\b", "'");
  $replace = array('\\\\', "\\n", "\\r", "\\f", "\\t", "\\b", "&#039");
  $json = str_replace($search, $replace, $json);
  return $json;
}

/**
 * Cria valores para o pagamento
 * @param $vlProdutos Valor das mercadorias
 * @param $vlFrete Valor do frete
 * @param $vlCupom Valor do desconto para o cupom (Real|Porcentagem)
 * @param $tipoCupom Tipo que será o desconto (Real|Porcentagem)
 * @param $vlDescontoBoleto Valor do desconto para boleto e transferencia
 * @return string [TOTAL_CUPOM, TOTAL_FRETE, TOTAL_COMPRA, TOTAL_CUPOM_REAL, TOTAL_COMPRA_C_BOLETO] Dados com os valore das mercadorias
 */
function valor_pagamento($vlProdutos = '0.00', $vlFrete = '0.00', $vlCupom = '0.00', $tipoCupom = '$', $vlDescontoBoleto = '0')
{
  $str['TOTAL'] = $vlProdutos;
  switch ($tipoCupom) {
    case '%':
      $str['TOTAL_CUPOM'] = round($vlCupom) . '%';
      $str['TOTAL_FRETE'] = $vlFrete;
      $str['TOTAL_COMPRA'] = (($vlProdutos - ($vlCupom / 100) * $vlProdutos) + $vlFrete);
      $str['TOTAL_CUPOM_REAL'] = ($vlProdutos + $vlFrete) - (($vlProdutos - ($vlCupom / 100) * $vlProdutos) + $vlFrete);

      if ($vlDescontoBoleto > '0') $vlProdutos = $vlProdutos - ($vlDescontoBoleto / 100) * $vlProdutos;

      $str['TOTAL_COMPRA_C_BOLETO'] = (($vlProdutos - ($vlCupom / 100) * $vlProdutos) + $vlFrete);
      break;

    case '$':
      $str['TOTAL_CUPOM'] = 'R$: ' . number_format($vlCupom, 2, ',', '.');
      $str['TOTAL_FRETE'] = $vlFrete;
      $str['TOTAL_COMPRA'] = (($vlProdutos - $vlCupom) + $vlFrete);
      $str['TOTAL_CUPOM_REAL'] = $vlCupom;

      if ($vlDescontoBoleto > '0') $vlProdutos = ($vlProdutos - $vlCupom) - ($vlDescontoBoleto / 100) * ($vlProdutos - $vlCupom);

      $str['TOTAL_COMPRA_C_BOLETO'] = ($vlProdutos + $vlFrete);
      break;

    default:
      $str['TOTAL_CUPOM'] = 'R$: ' . number_format($vlCupom, 2, ',', '.');
      $str['TOTAL_FRETE'] = $vlFrete;
      $str['TOTAL_COMPRA'] = $vlProdutos + $vlFrete;
      $str['TOTAL_CUPOM_REAL'] = $vlCupom;

      if ($vlDescontoBoleto > '0') $vlProdutos = $vlProdutos - ($vlDescontoBoleto / 100) * $vlProdutos;

      $str['TOTAL_COMPRA_C_BOLETO'] = $vlProdutos + $vlFrete;
      break;
  }
  return $str;
}

/**
 * Remove array <b>duplicados</b><br/>
 * O dados devem sempre em está como um array
 * @link https://secure.php.net/manual/pt_BR/function.array-unique.php#97285
 * @param array Entrada de vetores
 * @return array com valores unicos
 */
function super_unique($array)
{
  $result = array_map("unserialize", array_unique(array_map("serialize", $array)));

  foreach ($result as $key => $value) {
    if (is_array($value)) {
      $result[$key] = super_unique($value);
    }
  }

  return $result;
}

/**
 * Remover valores duplicados das url do sistema
 * @param type $url
 * @param type $key
 * @return type
 */
function remove_querystring_var($url, $key)
{
  $url = preg_replace('/(.*)(?|&)' . $key . '=[^&]+?(&)(.*)/i', '$1$2$4', $url . '&');
  $url = substr($url, 0, -1);
  return $url;
}

/**
 * Fusao de vetores<br/>
 * Unir os vetores de forma unica
 * @link http://php.net/manual/pt_BR/function.array-merge-recursive.php#102379
 * @param type $Arr1
 * @param type $Arr2
 * @return type Array unidos
 */
function MergeArrays($Arr1, $Arr2)
{
  if (empty($Arr2)) return false;
  foreach ($Arr2 as $key => $Value) {
    if (array_key_exists($key, $Arr1) && is_array($Value)) {
      $Arr1[$key] = MergeArrays($Arr1[$key], $Arr2[$key]);
    } else {
      $Arr1[$key] = $Value;
    }
  }
  return $Arr1;
}

/**
 * Gerar cores aleatorias
 */
function random_color($start = 0x000000, $end = 0xFFFFFF)
{
  return sprintf('#%06x', mt_rand($start, $end));
}

function hex2RGB($hex)
{
  $hex = str_replace("#", "", $hex);
  if (strlen($hex) == 3) {
    $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
    $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
    $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
  } else {
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
  }
  $rgb = array($r, $g, $b);
  return $rgb;
}

/**
 * Exemplo tirado
 * http://php.net/manual/pt_BR/function.hexdec.php#66780
 * MultiColorFade(array hex-colors, int steps)
 *
 * @param type $hex_array
 * @param type $steps
 * @return type
 */
function MultiColorFade($hex_array, $steps)
{

  $tot = count($hex_array);
  $gradient = array();
  $fixend = 2;
  $passages = $tot - 1;
  $stepsforpassage = floor($steps / $passages);
  $stepsremain = $steps - ($stepsforpassage * $passages);

  for ($pointer = 0; $pointer < $tot - 1; $pointer++) {

    $hexstart = $hex_array[$pointer];
    $hexend = $hex_array[$pointer + 1];

    if ($stepsremain > 0) {
      if ($stepsremain--) {
        $stepsforthis = $stepsforpassage + 1;
      }
    } else {
      $stepsforthis = $stepsforpassage;
    }

    if ($pointer > 0) {
      $fixend = 1;
    }

    $start['r'] = hexdec(substr($hexstart, 0, 2));
    $start['g'] = hexdec(substr($hexstart, 2, 2));
    $start['b'] = hexdec(substr($hexstart, 4, 2));

    $end['r'] = hexdec(substr($hexend, 0, 2));
    $end['g'] = hexdec(substr($hexend, 2, 2));
    $end['b'] = hexdec(substr($hexend, 4, 2));

    $step['r'] = ($start['r'] - $end['r']) / ($stepsforthis);
    $step['g'] = ($start['g'] - $end['g']) / ($stepsforthis);
    $step['b'] = ($start['b'] - $end['b']) / ($stepsforthis);

    for ($i = 0; $i <= $stepsforthis - $fixend; $i++) {

      $rgb['r'] = floor($start['r'] - ($step['r'] * $i));
      $rgb['g'] = floor($start['g'] - ($step['g'] * $i));
      $rgb['b'] = floor($start['b'] - ($step['b'] * $i));

      $hex['r'] = sprintf('%02x', ($rgb['r']));
      $hex['g'] = sprintf('%02x', ($rgb['g']));
      $hex['b'] = sprintf('%02x', ($rgb['b']));

      $gradient[] = strtoupper(implode(NULL, $hex));
    }
  }

  $gradient[] = $hex_array[$tot - 1];

  return $gradient;
}


function make_cmp(array $sortValues)
{
  return function ($a, $b) use (&$sortValues) {
    foreach ($sortValues as $column => $sortDir) {
      $diff = strcmp($a[$column], $b[$column]);
      if ($diff !== 0) {
        if ('asc' === $sortDir) {
          return $diff;
        }
        return $diff * -1;
      }
    }
    return 0;
  };
}

function make_comparer()
{
  $criteriaNames = func_get_args();
  $comparer = function ($first, $second) use ($criteriaNames) {
    // Do we have anything to compare?
    while (!empty($criteriaNames)) {
      // What will we compare now?
      $criterion = array_shift($criteriaNames);

      // Do the actual comparison
      if ($first[$criterion] < $second[$criterion]) {
        return -1;
      } else if ($first[$criterion] > $second[$criterion]) {
        return 1;
      }
    }

    // Nothing more to compare with, so $first == $second
    return 0;
  };

  return $comparer;
}

/**
 * Retornar os status do mercado pago para o sistema interno
 * @param type $var
 * @return string
 */
function StatusPagtoMP($var)
{
  switch ($var['status']):
    case 'pending':
      $str['status'] = '5';
      $str['mensagem'] = 'O usuário ainda não completou o processo de pagamento.';
      break;
    case 'approved':
      $str['status'] = '3';
      $str['mensagem'] = 'O pagamento foi aprovado e acreditado.';
      break;
    case 'authorized':
      $str['status'] = '11';
      $str['mensagem'] = 'O pagamento foi autorizado, mas ainda não capturado.';
      break;
    case 'in_process':
      $str['status'] = '11';
      $str['mensagem'] = 'O pagamento estão em revisão.';
      break;
    case 'in_mediation':
      $str['status'] = '11';
      $str['mensagem'] = 'Os usuários tem começada uma disputa.';
      break;
    case 'rejected':
      $str['status'] = '4';
      $str['mensagem'] = 'O pagamento foi rejeitado. O usuário pode tentar novamente.';
      break;
    case 'cancelled':
      $str['status'] = '10';
      $str['mensagem'] = 'O pagamento foi cancelado por uma das parte ou porque o tempo expirou.';
      break;
    case 'refunded':
      $str['status'] = '10';
      $str['mensagem'] = 'O pagamento foi devolvido ao usuário.';
      break;
    case 'charged_back':
      $str['status'] = '11';
      $str['mensagem'] = 'Foi feito um chargeback no cartão do comprador.';
      break;
  endswitch;
  return $str;
}

/**
 * Trata os erros do mercado pago
 * @param type $var
 * @return string
 */
function HTTPStatus400MP($var)
{
  switch ($var['code']) {
    case '106':
      $str['mensagem'] = 'Você não pode fazer pagamentos para usuários em outros países.';
      break;
    case '109':
      $str['mensagem'] = '<b>' . $var['payment_method_id'] . '</b> id não processa pagamentos em <b>' . $var['installments'] . '</b> parcelas. Use outro cartão ou um outro meio de pagamento.';
      break;
    case '126':
      $str['mensagem'] = 'Não foi possível processar o pagamento.';
      break;
    case '129':
      $str['mensagem'] = 'Seu <b>' . $var['payment_method_id'] . '</b> não processa pagamentos com do valor selecionado. Use outro cartão ou outro meio de pagamento.';
      break;
    case '145':
      $str['mensagem'] = 'Não foi possível processar o pagamento.';
      break;
    case '150':
      $str['mensagem'] = 'Você não pode fazer pagamentos.';
      break;
    case '151':
      $str['mensagem'] = 'Você não pode fazer pagamentos.';
      break;
    case '160':
      $str['mensagem'] = 'Não foi possível processar o pagamento.';
      break;
    case '204':
      $str['mensagem'] = 'Desculpe, Seu <b>' . $var['payment_method_id'] . '</b> não está disponível neste momento. Tente mais tarde.';
      break;
    case '801':
      $str['mensagem'] = 'Desculpe, Você realizou um pagamento similar há pouco tempo.';
      break;
    default:
      $str['mensagem'] = 'Desculpe, não estamos disponível neste momento. Tente mais tarde.';
      break;
  }
  return $str;
}

function HTTPStatusMP($var)
{
  switch ($var['status_detail']) {
    case 'accredited':
      $str['mensagem'] = 'Obrigado, o seu pagamento foi aprovado com sucesso!';
      $str['status'] = 3;
      break;
    case 'pending_contingency':
      $str['mensagem'] = 'Estamos processando o pagamento. Em menos de 1 hora enviaremos o resultado por e-mail.';
      $str['status'] = 11;
      break;
    case 'pending_review_manual':
      $str['mensagem'] = 'Estamos processando o pagamento. '
        . 'Em menos de 2 dias úteis você será avisado por e-mail se o pagamento foi aprovado ou se precisamos de mais informações.';
      $str['status'] = 11;
      break;
    case 'cc_rejected_bad_filled_card_number':
      $str['mensagem'] = 'Verifique o número do cartão.';
      $str['status'] = 4;
      break;
    case 'cc_rejected_bad_filled_date':
      $str['mensagem'] = 'Verifique a data de validade.';
      $str['status'] = 4;
      break;
    case 'cc_rejected_bad_filled_other':
      $str['mensagem'] = 'Revise os dados.';
      $str['status'] = 4;
      break;
    case 'cc_rejected_bad_filled_security_code':
      $str['mensagem'] = 'Revise o código de segurança.';
      $str['status'] = 4;
      break;
    case 'cc_rejected_blacklist':
      $str['mensagem'] = 'Não foi possível processar o pagamento.';
      $str['status'] = 4;
      break;
    case 'cc_rejected_call_for_authorize':
      $str['mensagem'] = 'Você precisa autorizar seu <b>' . $var['payment_method_id'] . '</b> para o pagamento de R$: <b>' . $var['amount'] . '</b> ao MercadoPago';
      $str['status'] = 11;
      break;
    case 'cc_rejected_card_disabled':
      $str['mensagem'] = 'Ligue para operadora do seu cartão. O telefone está no verso do seu cartão de crédito.';
      $str['status'] = 11;
      break;
    case 'cc_rejected_card_error':
      $str['mensagem'] = 'Não foi possível processar o pagamento.';
      $str['status'] = 4;
      break;
    case 'cc_rejected_duplicated_payment':
      $str['mensagem'] = 'Você já fez um pagamento desse valor. Se você precisa pagar novamente, use outro cartão ou outro meio de pagamento.';
      $str['status'] = 4;
      break;
    case 'cc_rejected_high_risk':
      $str['mensagem'] = 'O seu pagamento foi recusado. Recomendamos que você pague com outros meios de pagamento oferecidos, preferencialmente à vista.';
      $str['status'] = 4;
      break;
    case 'cc_rejected_insufficient_amount':
      $str['mensagem'] = 'Desculpe, O seu <b>' . $var['payment_method_id'] . '</b> não tem limite suficiente.';
      $str['status'] = 4;
      break;
    case 'cc_rejected_invalid_installments':
      $str['mensagem'] = 'Seu <b>' . $var['payment_method_id'] . '</b> não processa pagamentos em <b>' . $var['installments'] . '</b> parcelas.';
      $str['status'] = 4;
      break;
    case 'cc_rejected_max_attempts':
      $str['mensagem'] = 'Você atingiu o limite de tentativas permitidas. Use outro cartão ou outro meio de pagamento.';
      $str['status'] = 4;
      break;
    case 'cc_rejected_other_reason':
      $str['mensagem'] = 'Seu <b>' . $var['payment_method_id'] . '</b> não processou o pagamento.';
      $str['status'] = 4;
      break;
  }
  return $str;
}

function pagseguro_object2array($object)
{
  return @json_decode(@json_encode($object), 1);
}

function pagseguro_errors($code = 0)
{
  if ($code == "53020" || $code == "53021") {
    return ("Verifique telefone inserido");
  } else if ($code == "53010" || $code == "53011" || $code == "53012") {
    return ("Verifique o e-mail inserido");
  } else if ($code == "53017") {
    return ("Verifique o CPF inserido");
  } else if ($code == "53018" || $code == "53019") {
    return ("Verifique o DDD inserido");
  } else if ($code == "53013" || $code == "53014" || $code == "53015") {
    return ("Verifique o nome inserido");
  } else if ($code == "53029" || $code == "53030") {
    return ("Verifique o bairro inserido");
  } else if ($code == "53022" || $code == "53023") {
    return ("Verifique o CEP inserido");
  } else if ($code == "53024" || $code == "53025") {
    return ("Verifique a rua inserido");
  } else if ($code == "53026" || $code == "53027") {
    return ("Verifique o número inserido");
  } else if ($code == "53033" || $code == "53034") {
    return ("Verifique o estado inserido");
  } else if ($code == "53031" || $code == "53032") {
    return ("Verifique a cidade informada");
  } else if ($code == "10001") {
    return ("Verifique o número do cartão inserido");
  } else if ($code == "10002" || $code == "30405") {
    return ("Verifique a data de validade do cartão inserido");
  } else if ($code == "10004") {
    return ("É obrigatorio informar o código de segurança, que se encontra no verso, do cartão");
  } else if ($code == "10006" || $code == "10003" || $code == "53037") {
    return ("Verifique o código de segurança do cartão informado");
  } else if ($code == "30404") {
    return ("Ocorreu um erro. Atualize a página e tente novamente!");
  } else if ($code == "53047") {
    return ("Verifique a data de nascimento do titular do cartão informada");
  } else if ($code == "53053" || $code == "53054") {
    return ("Verifique o CEP inserido");
  } else if ($code == "53055" || $code == "53056") {
    return ("Verifique a rua inserido");
  } else if ($code == "53042" || $code == "53043" || $code == "53044") {
    return ("Verifique o nome inserido");
  } else if ($code == "53057" || $code == "53058") {
    return ("Verifique o número inserido");
  } else if ($code == "53062" || $code == "53063") {
    return ("Verifique a cidade informada");
  } else if ($code == "53045" || $code == "53046") {
    return ("Verifique o CPF inserido");
  } else if ($code == "53060" || $code == "53061") {
    return ("Verifique o bairro inserido");
  } else if ($code == "53064" || $code == "53065") {
    return ("Verifique o estado inserido");
  } else if ($code == "53051" || $code == "53052") {
    return ("Verifique telefone inserido");
  } else if ($code == "53049" || $code == "53050") {
    return ("Verifique o código de área informado");
  } else if ($code == "53122") {
    return ("Enquanto na sandbox do PagSeguro, o e-mail deve ter o domínio @sandbox.pagseguro.com.br (ex.: comprador@sandbox.pagseguro.com.br)");
  } else if ($code == "53140") {
    return ("Selecione a forma de parcelamento");
  } else if ($code == "53041") {
    return ("Valor da parcela valor inválido");
  } else if ($code == "53048") {
    return ("Verifique sua data de nascimento");
  } else if ($code == "53150") {
    return ("sender hash is required");
  } else {
    return ("Entrar em contato com desenvolvedor do site");
  }
}


/**
 * Funcao para carregar o frete do pedido
 * @global type $settings
 * @global type $WebService
 * @global type $str
 * @global type $CONFIG
 * @param type $SessionSistema
 * @param type $SessionCliente
 * @return string
 */
function AtualizarFrete($SessionSistema = null, $SessionCliente = null, $CepInit = 14900000)
{

  global $CONFIG, $settings;

  $html = '';

  try {
    $CEP = soNumero((isset($CepInit) && $CepInit != '' ? $CepInit : null));

    $CorreiosCepReal = new PhpSigep\Services\SoapClient\Real();
    $CorreiosCep   = $CorreiosCepReal->consultaCep($CEP);

    $UF          = $CorreiosCep->getResult()->getUf();
    // $str['endereco'] = $CorreiosCep->getResult()->getEndereco();
    // $str['bairro'] 	 = $CorreiosCep->getResult()->getBairro();
    // $str['cidade'] 	 = $CorreiosCep->getResult()->getCidade();
    // $str['uf'] 		 = $CorreiosCep->getResult()->getUf();
    // $str['cep'] 	 = $CorreiosCep->getResult()->getCep();

    if (empty($UF))
      throw new Exception('Cep incorreto ou inválido!');

    $postagens = array();
    $resultPrazos = Produtos::all(['conditions' => ['produtos.id in(select carrinho.id_produto from carrinho where carrinho.id_session=?)', $SessionSistema]]);

    foreach ($resultPrazos as $rr) {
      if (!empty($rr->postagem) && $rr->postagem != '') {
        $postagens[] = $rr->postagem;
      }
      if (!empty($rr->marca->disponib_entrega) && $rr->marca->disponib_entrega != '') {
        $postagens[] = $rr->marca->disponib_entrega;
      }
    }

    $postagem = (count($postagens) > 0 ? implode(',', $postagens) : '1 a 5 dias');

    // string prazos - contem um array numericos dos prazos dos produtos
    $PRAZOS = array_filter(explode(',', preg_replace('/(.)\1+/', '$1', preg_replace('/[^-0-9]/', ',', (string)$postagem))));

    $prazoDe = min($PRAZOS);
    $prazoAte = max($PRAZOS);

    $QueryBase = ''
      . 'SELECT '
      . 'sum( produtos.preco_promo * carrinho.quantidade ) as total_car, '
      . '%s'
      . 'max( dados_frete.largura ) as largura, '
      . 'max( dados_frete.comprimento ) as comprimento, '
      . 'sum( dados_frete.peso * carrinho.quantidade) as peso '
      . 'FROM carrinho '
      . 'INNER JOIN produtos on carrinho.id_produto = produtos.id '
      . 'INNER JOIN dados_frete on dados_frete.id = produtos.id_frete '
      . 'WHERE carrinho.id_session="%s"';

    $query = sprintf($QueryBase, "sum( dados_frete.altura ) / sum(carrinho.quantidade) as altura, ", $SessionSistema);
    $Carrinho = Carrinho::connection()->query($query)->fetch();

    $queryVerificaTamanho = sprintf($QueryBase, "max( dados_frete.altura ) as altura, ", $SessionSistema);
    $carVerificaTamanho = Carrinho::connection()->query($queryVerificaTamanho)->fetch();

    $i = 1;

    $altura = ($Carrinho['altura'] < 2 || $Carrinho['altura'] > 105) ? 5 : $Carrinho['altura'];

    $largura = ($Carrinho['largura'] < 11 || $Carrinho['largura'] > 105) ? 16 : $Carrinho['largura'];

    $comprimento = ($Carrinho['comprimento'] < 16 || $Carrinho['comprimento'] > 105) ? 20 : $Carrinho['comprimento'];

    $PESO = $Carrinho['peso'] > 0 &&  $Carrinho['peso'] < 30 ? $Carrinho['peso'] : 1;

    $TOTAL_CAR = ($CONFIG['atacadista'] ? ($Carrinho['total_car'] - ($CONFIG['atacadista'] / 100) * $Carrinho['total_car']) : $Carrinho['total_car']);

    $FRETE = [];

    $SomaDimensoes = $carVerificaTamanho['altura'] + $carVerificaTamanho['largura'] + $carVerificaTamanho['comprimento'];

    // Verifica se é atacado, porém seja falso ou null
    if (!$CONFIG['atacadista']) {
      if ($SomaDimensoes <= 200 && $carVerificaTamanho['altura'] <= 105 && $carVerificaTamanho['largura'] <= 105 && $carVerificaTamanho['comprimento'] <= 105)
        $FRETE = calcular_preco_frete($STORE['config']['correios'], $CONFIG['cep'], $CEP, $PESO, $altura, $largura, $comprimento);
    }

    // Verifica se é atacado, porém seja falso ou null
    if (!$CONFIG['atacadista'])
      if (!empty($STORE['config']['jadlog'])) {

        $cubagem = (($Carrinho['altura'] * $Carrinho['largura'] * $Carrinho['comprimento']) / 6000);

        $PESO = $Carrinho['peso'] < dinheiro($cubagem) ? dinheiro($cubagem) : $Carrinho['peso'];

        $FRETE = $FRETE + calcular_fretejadlog($PESO, $CONFIG['cep'], $CEP, $STORE['config']['jadlog']);

        if ($CONFIG['dominio'] == 'realambiente' && isset($FRETE['JADLOG'])) {
          $na_faixa = false;
          $Zuzim = $FRETE['JADLOG'];
          $CepDest = substr($Zuzim['cepdes'], 0, 5);

          /**
           * 08226021
           * substr( $Zuzim['cepdes'], 0, 8 )
           * 01000 a 05999
           * 08000 a 08499
           * 06000 a 09999
           */

          if ($CepDest >= 1000 && $CepDest <= 5999)
            $na_faixa = true;

          if ($CepDest >= 8000 && $CepDest <= 8499)
            $na_faixa = true;

          if ($CepDest >= 6000 && $CepDest <= 9999)
            $na_faixa = true;

          if ($na_faixa) {
            $r['Zeni'] = [];
            $r['Zeni']['valor'] = $Zuzim['valor'] * 0.6;
            $r['Zeni']['prazo'] = $Zuzim['prazo'];
            $FRETE = $FRETE + $r;
          }
        }
      }
    // print_r($CONFIG);
    // print_r($FRETE['JADLOG']);
    $VALOR_FRETE = 0;
    $Carrinho['gratis'] = false;

    $ConfiguracoesFretesGratis = ConfiguracoesFretesGratis::all(['conditions' => ['loja_id=?', $CONFIG['loja_id']]]);

    foreach ($ConfiguracoesFretesGratis as $rws) {
      if ($rws->cep_ini <= $CEP && $rws->cep_fin >= $CEP && $rws->frete_valor <= $TOTAL_CAR) {
        $VALOR_FRETE = $rws->frete_valor;
        $Carrinho['gratis'] = true;
        break;
      } else if ($rws->uf == $UF && $rws->frete_valor <= $TOTAL_CAR) {
        $VALOR_FRETE = $rws->frete_valor;
        $Carrinho['gratis'] = true;
        break;
      } else {
        $VALOR_FRETE = $rws->frete_valor;
      }
    }

    $GRATIS = ($Carrinho['gratis'] and (($TOTAL_CAR >= $VALOR_FRETE && $CONFIG['atacadista']) ? 0 : 1));

    $GRATIS_MSG = ($VALOR_FRETE - $TOTAL_CAR) >= 0
      ? sprintf('Falta apenas <b class="color-004">R$: %s</b> ', number_format(($VALOR_FRETE - $TOTAL_CAR), 2, ',', '.'))
      . sprintf('para você ter frete grátis, <a href="/produtos/?sc=%s" ', session_id())
      . 'class="color-004 text-underline font-bold">clique aqui</a> para continuar comprando' : '';

    // $html = '';
    ob_start();
?>
<div id="recarregar-frete" class="table-responsive" style="border: none;">
  <table cellpadding="5" cellspacing="0" border="0" width="100%">
    <tbody>

      <?php if (($GRATIS > 0 && !$CONFIG['atacadista']) || ($CONFIG['cep'] == $CEP)) : ?>
      <tr style="border-top: dotted 1px #ccc;">
        <td nowrap="nowrap" width="1%">
          <input type="radio" name="frete" id="GRATIS" value="<?= $POST['id'] ?>" class="input-radio" data-valor="0.00"
            data-gratis="<?= htmlspecialchars($GRATIS_MSG, ENT_QUOTES) ?>"
            onclick="Checkout.atualizar_carrinho( this );" />
          <label for="GRATIS" class="fa ft22px"></label>
          <label class="imagens-frete frete-gratis"></label>
        </td>
        <td align="right">
          <span class="show color-004 ft18px">Frete Grátis</span>
          <span class="show black-30 ft13px">
            <?php
                  $gratis_text = !empty($FRETE['PAC']['prazo']) ? $FRETE['PAC']['prazo'] : null;
                  $gratis_text = !empty($FRETE['JADLOG']['prazo']) ? $FRETE['JADLOG']['prazo'] : $gratis_text;
                  ?>
            Prazo de entrega: de <?= str_replace('-', '', (($gratis_text) + $prazoDe)) ?> à
            <?= (($gratis_text) + $prazoAte) ?> dia(s) úteis
          </span>
        </td>
      </tr>
      <?php endif; ?>

      <?php $frete_vl = 0;
          if ($GRATIS == 0) foreach ($FRETE as $key => $values) : ?>
      <?php if (!empty($FRETE[$key]['Erro'])) { ?>
      <tr>
        <td align="right" colspan="2">
          <?= sprintf('<span class="show">%s</span>', $FRETE[$key]['Erro']) ?>
        </td>
      </tr>
      <?php
              break;
            }

            // verifica a existencia de subsidiar o valor sobre o total final
            if ($CONFIG['fretes_sob_vl'] == 1) {

              if ($CONFIG['fretes_tipo'] == '%')
                $frete_vl = $TOTAL_CAR - desconto_boleto($TOTAL_CAR, $CONFIG['fretes_valor']);
              else
                $frete_vl = ($TOTAL_CAR - $CONFIG['fretes_valor']);

              $frete_vl = $FRETE[$key]['valor'] - $frete_vl;
            } else {
              $frete_vl = ($CONFIG['fretes_tipo'] == '%' ? desconto_boleto($FRETE[$key]['valor'], $CONFIG['fretes_valor']) : ($FRETE[$key]['valor'] - $CONFIG['fretes_valor']));
            }

            $frete_vl = $frete_vl <= 0 ? 0.00 : $frete_vl;

            $Liberado = true;
            if (isset($FRETE[$key]['modalidade']) && $FRETE[$key]['modalidade'] == 40) {
              if ($carVerificaTamanho['altura'] > 80 || $carVerificaTamanho['largura'] > 80 || $carVerificaTamanho['comprimento'] > 80 || $carVerificaTamanho['peso'] > 10)
                $Liberado = false;
            }
            if ($Liberado) {
            ?>
      <tr style="border-top: dotted 1px #ccc;<?= (empty($frete_vl) ? 'display:none' : '') ?>">
        <td nowrap="nowrap" width="1%">
          <input type="radio" name="frete" id="<?= $key ?>" value="<?= $POST['id'] ?>" class="input-radio"
            data-valor="<?= $frete_vl ?>" data-gratis="<?= htmlspecialchars($GRATIS_MSG, ENT_QUOTES) ?>"
            onclick="Checkout.atualizar_carrinho( this );" />
          <label for="<?= $key ?>" class="fa ft22px"></label>
          <label class="imagens-frete frete-<?= strtolower($key) ?>"></label>
        </td>
        <td align="right">
          <span class="show color-004 ft18px">Valor R$: <?= number_format($frete_vl, 2, ',', '.') ?></span>
          <span class="show black-30 ft13px">
            Prazo de entrega: de <?= str_replace('-', '', ($FRETE[$key]['prazo'] + $prazoDe)) ?> à
            <?= ($FRETE[$key]['prazo'] + $prazoAte) ?> dia(s) úteis
          </span>
        </td>
      </tr>

      <?php
              if ($FRETE[$key]['obsFim'] != '') { ?>
      <tr>
        <td colspan="2" style="padding: 10px 0 10px 0">
          <span class="show black-30 ft14px">
            Obs: <?= $FRETE[$key]['obsFim'] ?>
          </span>
        </td>
      </tr>
      <?php
              }
            }
            ?>

      <?php endforeach; ?>
      <script>
      console.log("Selecione os fretes!");
      $("#finalizar-pedido").removeClass("hidden").fadeIn(0);
      $("input[data-frete]").val("");
      </script>
    </tbody>
  </table>
</div>
<?php
    $html .= ob_get_clean();
  } catch (Exception $e) {
    ob_start();
  ?>
<script>
console.log("Selecione os fretes!");
$("#finalizar-pedido").removeClass("hidden").fadeIn(0);
$("input[data-frete]").val("");
</script>
<?php
    echo $e->getMessage();
    $html .= ob_get_clean();
  }

  // $html .= date('Y/m/d H:i:s') <= '2017/01/15 23:59:59' ? '<hr/><div class="ft18px text-center color-004">INFORMAMOS: alguns dos pedidos só serão enviados a partir do dia <b>15/01/2017</b> devido aos nossos fornecedores estarem em Férias coletivas. Agradecemos a compreenção de todos, qualquer dúvida entrar em contato com nossa central de atendimento.</div>' : '';

  return $html;
}