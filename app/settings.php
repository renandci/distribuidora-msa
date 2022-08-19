<?php
// header('Expires: Thu, 01-Jan-90 00:00:01 GMT');
// header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
// header('Cache-Control: no-store, no-cache, must-revalidate');
// header('Cache-Control: post-check=0, pre-check=0', false);
// header('Pragma: no-cache');

// Default
// ini_set('display_errors', 'Off');
// error_reporting(0);

ini_set('display_errors', 'On');
error_reporting(E_ALL & ~E_NOTICE);

setlocale(LC_ALL, 'pt_BR.utf-8');
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
setlocale(LC_NUMERIC, 'en_US.utf-8');
date_default_timezone_set('America/Sao_Paulo');

// Globals
global $LOJA, $STORE, $CONFIG, $mail, $UA_INFO, $PDO, $MobileDetect, $WebService, $settings, $str;

$GET = filter_var_array($_GET, FILTER_SANITIZE_SPECIAL_CHARS);
$_GET = filter_var_array($_GET, FILTER_SANITIZE_SPECIAL_CHARS);

$POST = filter_var_array($_POST, FILTER_SANITIZE_SPECIAL_CHARS);
$_POST = filter_var_array($_POST, FILTER_SANITIZE_SPECIAL_CHARS);

$SERVER = filter_var_array($_SERVER);
$_SERVER = filter_var_array($_SERVER);

$dominios   = ['www.', '.dev', '.com', '.br', 'imagens.', 'static.', '.dci', '.test'];
$nulos      = [null, null, null, null, null, null, null, null];
$HTTP_HTTPS = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://';
$ASSETS    = str_replace($dominios, $nulos, $_SERVER['SERVER_NAME']);

$HTTP_HOST = $_SERVER['HTTP_HOST'];
$REQUEST_URI = $_SERVER['REQUEST_URI'];
$SERVER_PORT = $_SERVER['SERVER_PORT'];
$HTTP_HOST_TEST = in_array(strstr($HTTP_HOST, '.', true), ['imagens', 'static', 'store', 'imgx']);

// // Redireciona para www
// if (substr($HTTP_HOST, 0, 3) != 'www' && ! $HTTP_HOST_TEST) {
//     header('HTTP/1.1 301 Moved Permanently');
//     header(sprintf('Location: http://www.%s%s', $HTTP_HOST, $REQUEST_URI), true, 301);
//     exit;
// }

// // Redireciona para https
// if ($SERVER_PORT == 80 && ! $HTTP_HOST_TEST) {
// 	header('HTTP/1.1 301 Moved Permanently');
// 	header(sprintf('Location: https://%s%s', $HTTP_HOST, $REQUEST_URI), true, 301);
// 	exit;
// }

// Define o caminho base do aplicativo
defined('PATH_ROOT') || define('PATH_ROOT', realpath($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR);
defined('HOST') || define('HOST', 'localhost');
defined('USER') || define('USER', 'ecommerce');
defined('PASS') || define('PASS', 'ecommerce');
defined('DB') || define('DB', 'ecommerce_db');

defined('ASSETS') || define('ASSETS', $ASSETS);
defined('URL_BASE') || define('URL_BASE', $HTTP_HTTPS . $_SERVER['SERVER_NAME'] . '/');
defined('URL_BASE_HTTPS') || define('URL_BASE_HTTPS', 'https://' . $_SERVER['SERVER_NAME'] . '/');
defined('URL_STATIC') || define('URL_STATIC', URL_BASE);
defined('URL_IMAGENS') || define('URL_IMAGENS', URL_BASE);
defined('SERVER_NAME') || define('SERVER_NAME', $_SERVER['SERVER_NAME']);

defined('URL_VIEWS_BASE') || define('URL_VIEWS_BASE', sprintf('%stemplates/%s/', PATH_ROOT, ASSETS));
defined('URL_VIEWS_BASE_PUBLIC') || define('URL_VIEWS_BASE_PUBLIC', sprintf('%sassets/%s', URL_BASE, ASSETS));
defined('URL_VIEWS_BASE_PUBLIC_IMAGENS') || define('URL_VIEWS_BASE_PUBLIC_IMAGENS', sprintf('%sassets/%s', URL_IMAGENS, ASSETS));
defined('URL_VIEWS_BASE_PUBLIC_UPLOAD') || define('URL_VIEWS_BASE_PUBLIC_UPLOAD', sprintf('%sassets/%s', PATH_ROOT, ASSETS));

define('PHP_ACTIVERECORD_AUTOLOAD_DISABLE', true);

$session_start = [
  // Session cookie settings
  // 'name'           => "_{$ASSETS}",
  'name'           => md5(sprintf('%s.%s.%s', $ASSETS, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'])),
  'lifetime'       => (24 * 60),
  'path'           => '/',

  // Converte a url em 'todos dominios '
  'domain'         => str_replace(['www.', 'static.', 'imagens.'], ['www.', 'www.', 'www.'], $_SERVER['HTTP_HOST']),
  'secure'         => false,
  'httponly'       => true,

  // Set session cookie path, domain and secure automatically
  'cookie_autoset' => true,

  // Path where session files are stored, PHP's default path will be used if set null
  'save_path'      => null,

  // Session cache limiter
  'cache_limiter'  => 'nocache',

  // Extend session lifetime after each user activity
  'autorefresh'    => false,

  // Encrypt session data if string is string is set
  'encryption_key' => null,

  // Session namespace
  'namespace'      => "_init{$ASSETS}"
];

// Enable strict mode
ini_set('session.use_strict_mode', 0);

// Use cookies and only cookies to store session id
ini_set('session.use_cookies', 1);
ini_set('session.use_only_cookies', 1);

// Disable inserting session id into links automatically
ini_set('session.use_trans_sid', 0);

if (is_string($session_start['lifetime'])) {
  // if lifetime is string, convert it to seconds
  $session_start['lifetime'] = strtotime($session_start['lifetime']) - time();
} else {
  // if lifetime is minutes, convert it to seconds
  $session_start['lifetime'] *= 60;
}

// Set number of seconds after which data will be seen as garbage
if ($session_start['lifetime'] > 0) {
  ini_set('session.gc_maxlifetime', $session_start['lifetime']);
}

// Set path where session cookies are saved
if (is_string($session_start['save_path'])) {
  if (!is_writable($session_start['save_path'])) {
    throw new RuntimeException('Session save path is not writable.');
  }
  ini_set('session.save_path', $session_start['save_path']);
}

// Set session cache limiter
session_cache_limiter($session_start['cache_limiter']);

// Set session cookie name
session_name($session_start['name']);

// Set session cookie parameters
session_set_cookie_params(
  $session_start['lifetime'],
  $session_start['path'],
  $session_start['domain'],
  $session_start['secure'],
  $session_start['httponly']
);

// Start session
session_start();

// Extend session lifetime
if ($session_start['autorefresh'] === true && isset($_COOKIE[$session_start['name']])) {
  setcookie(
    $session_start['name'],
    $_COOKIE[$session_start['name']],
    time() + $session_start['lifetime'],
    $session_start['path'],
    $session_start['domain'],
    $session_start['secure'],
    $session_start['httponly']
  );
}
