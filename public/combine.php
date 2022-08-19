<?php
// require '../app/settings.php';
// require '../app/vendor/autoload.php';

$ASSETS = str_replace(['www.', '.dev', '.com', '.br', 'imagens.', 'static.', '.dci', '.test'], ['', '', '', '', '', '', '', ''], $_SERVER['SERVER_NAME']);
define('ASSETS', $ASSETS);
define('PATH_ROOT', realpath($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR);

require PATH_ROOT . '/app/vendor/autoload.php';

/**
 *
 * GzipIt 1.2
 *
 * Single file solution for CSS and JavaScript combination,
 * minimization, gzipping and caching.
 *
 * For documentation, requirements, updates and support please visit:
 * http://code.google.com/p/gzipit/
 *
 * Inspired by CSS and Javascript Combinator by Niels Leenheer
 * (http://rakaz.nl/code/combine)
 *
 * See copyright and licences below for bundled components.
 *
 * --
 * Copyright (c) 2010-2012 Artem Volk (www.artvolk.sumy.ua)
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
 * of the Software, and to permit persons to whom the Software is furnished to do
 * so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 * --
 *
 * @package gzipit
 * @author Artem Volk <artvolk@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @version 1.0 ($Id$)
 * @link http://code.google.com/p/gzipit/
 */


/**
 * Configuration section
 * *****************************************************************************************************************
 */

// Use gzip compression
if (!defined('GZIPIT_COMPRESSION'))
	define('GZIPIT_COMPRESSION', true);

// IE6 is buggy with gzip, you can turn gzip for this browser completely using this parameter
if (!defined('GZIPIT_COMPRESSION_FOR_IE6'))
	define('GZIPIT_COMPRESSION_FOR_IE6', true);

// Compresion level (from 0 to 9)
if (!defined('GZIPIT_GZIP_LEVEL'))
	define('GZIPIT_GZIP_LEVEL', 9);

// Cache files on disk (with minimizing enabled this should be enabled)
if (!defined('GZIPIT_DISK_CACHE'))
	define('GZIPIT_DISK_CACHE', true);

// Minimize CSS files
if (!defined('GZIPIT_CSSMIN'))
	define('GZIPIT_CSSMIN', true);

// Minimize JavaScript files
if (!defined('GZIPIT_JSMIN'))
	define('GZIPIT_JSMIN', true);

// Include filename into combined output (useful for debug)
if (!defined('GZIPIT_INCLUDE_FILENAME'))
	define('GZIPIT_INCLUDE_FILENAME', true);

// Directory where output files will be cached (can be placed outside of document root)
if (!defined('GZIPIT_DIR_CACHE'))
	define('GZIPIT_DIR_CACHE', dirname(dirname(__FILE__)) . '/cache');

// Directory where original CSS files are stored (sub directories are accessible too)
if (!defined('GZIPIT_DIR_CSS'))
	define('GZIPIT_DIR_CSS', dirname(dirname(__FILE__)) . '/');

// Directory where original CSS files are stored (sub directories are accessible too)
if (!defined('GZIPIT_DIR_JS'))
	define('GZIPIT_DIR_JS', dirname(dirname(__FILE__)) . '/');

// Send 'ETag' header (calculated automatically)
if (!defined('GZIPIT_HEADER_ETAG'))
	define('GZIPIT_HEADER_ETAG', true);

// Send 'Last-Modified' header (calculated automatically)
if (!defined('GZIPIT_HEADER_LAST_MODIFIED'))
	define('GZIPIT_HEADER_LAST_MODIFIED', true);

// Send 'Cache-Control' header
if (!defined('GZIPIT_HEADER_CACHE_CONTROL'))
	define('GZIPIT_HEADER_CACHE_CONTROL', true);

// Value for the 'Cache-Control' header
if (!defined('GZIPIT_HEADER_CACHE_CONTROL_VALUE'))
	define('GZIPIT_HEADER_CACHE_CONTROL_VALUE', 'max-age=315360000');

// Send 'Expires' header
if (!defined('GZIPIT_HEADER_EXPIRES'))
	define('GZIPIT_HEADER_EXPIRES', true);

// Value for the 'Expires' header
if (!defined('GZIPIT_HEADER_EXPIRES_VALUE'))
	define('GZIPIT_HEADER_EXPIRES_VALUE', 'Thu, 31 Dec 2037 23:55:55 GMT');

// NOTE: Specify name of the asset file OR assets array, but not the two at the same time
if (!defined('GZIPIT_ASSETS_FILE'))
	define('GZIPIT_ASSETS_FILE', 'assets.php');
/*
	Example of $GZIPIT_ASSETS

	$GZIPIT_ASSETS = array(
		'css-default' => array(
			'type' => 'css',
			'files' => array(
				'file1.css',
				'file2.css',
				...
			)
		),
		'js-default' => array(
			'type' => 'javascript',
			'files' => array(
				'file1.js',
				'file2.js',
				...
			)
		),
	);

*/
$GZIPIT_ASSETS = array();


/**
 * Just code below
 * No user-serviceable parts inside :)
 * *****************************************************************************************************************
 */
// Other constants and parameters
define('GZIPIT_FILELIST_DELIMITER', ',');

define('GZIPIT_ENCODING_NONE', 'none');
define('GZIPIT_ENCODING_GZIP', 'gzip');
$GZIPIT_ENCODING_TYPES = array(
	GZIPIT_ENCODING_NONE,
	GZIPIT_ENCODING_GZIP
);

define('GZIPIT_TYPE_CSS', 'css');
define('GZIPIT_TYPE_JS', 'javascript');
$GZIPIT_TYPES = array(
	GZIPIT_TYPE_CSS,
	GZIPIT_TYPE_JS
);
$GZIPIT_CONTENT_TYPES = array(
	GZIPIT_TYPE_CSS => 'text/css',
	GZIPIT_TYPE_JS => 'text/javascript'
);
$GZIPIT_EXTENSIONS = array(
	GZIPIT_TYPE_CSS	=> 'css',
	GZIPIT_TYPE_JS	=> 'js'
);
$GZIPIT_PATHES = array(
	GZIPIT_TYPE_CSS	=> GZIPIT_DIR_CSS,
	GZIPIT_TYPE_JS	=> GZIPIT_DIR_JS
);

if (GZIPIT_ASSETS_FILE != NULL && GZIPIT_ASSETS_FILE != '' && GZIPIT_ASSETS_FILE !== false) {
	require_once(GZIPIT_ASSETS_FILE);
}
// echo '<pre>';
// print_r($GZIPIT_ASSETS);
// return;

ob_start();

/**
 * Parse GET parameters
 */
$type = get_param('type', true);
$files = get_param('files');
$asset = get_param('asset');

// Check if asset name specified
if ($asset != NULL) {
	if (isset($GZIPIT_ASSETS[$asset])) {
		$files = $GZIPIT_ASSETS[$asset]['files'];
		$type = $GZIPIT_ASSETS[$asset]['type'];
	} else {
		give_404('Incorrect asset name');
		exit;
	}
}

// Get files list and type
if ($files !== NULL && $type !== NULL) {
	if (in_array($type, $GZIPIT_TYPES)) {
		if ($asset === NULL) {
			$elements = explode(GZIPIT_FILELIST_DELIMITER, $files);
		} else {
			$elements = $GZIPIT_ASSETS[$asset]['files'];
		}
	} else {
		give_404('Incorrect type specified');
		exit;
	}
} else {
	if ($asset == NULL) {
		give_404('Incorrect files and type parameters');
	} else {
		give_404('Incorrect asset definition');
	}
	exit;
}


/**
 * Determine supported compression
 *
 */
if (GZIPIT_COMPRESSION) {
	$temp = getAcceptedEncoding();

	if ($temp[0] == GZIPIT_ENCODING_GZIP) {
		$encoding = GZIPIT_ENCODING_GZIP;
		$encoding_header = $temp[1];
	} else {
		$encoding = GZIPIT_ENCODING_NONE;
		$encoding_header = NULL;
	}
} else {
	$encoding = GZIPIT_ENCODING_NONE;
	$encoding_header = NULL;
}


/**
 * Find last date and time of last modification of files
 */
$last_modified = 0;

$base_path = realpath($GZIPIT_PATHES[$type]);
$ext = $GZIPIT_EXTENSIONS[$type];
foreach ($elements as $element) {
	$path = realpath($base_path . DIRECTORY_SEPARATOR . $element);

	if (
		$path === false ||
		substr($path, -1 * strlen($ext)) != $ext ||
		substr($path, 0, strlen($base_path)) != $base_path ||
		!file_exists($path)
	) {
		$message = sprintf('File "%s" not found', htmlspecialchars($element));
		give_404($message);
		exit;
	}

	$last_modified = max($last_modified, filemtime($path));
}


/**
 * Construct and send ETag if enabled
 */
$etag = sprintf('%s-%s', $last_modified, md5(implode(GZIPIT_FILELIST_DELIMITER, $elements) . $type . (string)(GZIPIT_CSSMIN || GZIPIT_JSMIN) . $encoding_header));
if (GZIPIT_HEADER_ETAG) {
	header('Etag: "' . $etag . '"');
}


/**
 * Let's do it!
 */
// Check Etag
if (
	GZIPIT_HEADER_ETAG && isset($_SERVER['HTTP_IF_NONE_MATCH']) &&
	stripslashes($_SERVER['HTTP_IF_NONE_MATCH']) == '"' . $etag . '"'
) {
	header("HTTP/1.0 304 Not Modified");
	ob_end_clean();
	exit;
} else // No Etag specified
{
	// Send headers
	header('Content-Type: ' . $GZIPIT_CONTENT_TYPES[$type]);

	if (GZIPIT_HEADER_LAST_MODIFIED) {
		header('Last-Modified: ' . gmdate("D, d M Y H:i:s", $last_modified) . " GMT");
	}

	if (GZIPIT_HEADER_EXPIRES) {
		header('Expires: ' . GZIPIT_HEADER_EXPIRES_VALUE);
	}

	if (GZIPIT_HEADER_CACHE_CONTROL) {
		header('Cache-Control: ' . GZIPIT_HEADER_CACHE_CONTROL_VALUE);
	}


	$cached_file =
		realpath(GZIPIT_DIR_CACHE) .
		DIRECTORY_SEPARATOR .
		sprintf(
			'cache-%s%s.%s%s',
			$etag,
			(($type == GZIPIT_TYPE_CSS &&  GZIPIT_CSSMIN) || ($type == GZIPIT_TYPE_JS && GZIPIT_JSMIN)) ? '-min' : '',
			$GZIPIT_EXTENSIONS[$type],
			($encoding != GZIPIT_ENCODING_NONE) ? '.' . $encoding : ''
		);

	// If we have cached file, return it to the client
	if (GZIPIT_DISK_CACHE && file_exists($cached_file)) {
		if ($fp = fopen($cached_file, 'rb')) {
			if ($encoding_header != NULL)
				header('Content-Encoding: ' . $encoding);
			header('Content-Length: ' . filesize($cached_file));
			fpassthru($fp);
			fclose($fp);
			ob_end_flush();
			exit;
		} else {
			give_404('Error reading cached file');
			exit;
		}
	}

	// Perform combining, minimization and compression
	$content = '';
	foreach ($elements as $element) {
		$path = realpath($base_path . DIRECTORY_SEPARATOR . $element);
		$temp = file_get_contents($path);

		$content .= "\n";

		if (GZIPIT_INCLUDE_FILENAME) {
			$content .= sprintf("/*! %s */\n", $element);
		}
		$content .= $temp;
	}

	if ($type == GZIPIT_TYPE_CSS && GZIPIT_CSSMIN) {
		$Minify = new MatthiasMullie\Minify\CSS();
		$Minify->add(preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $content));
		$content = $Minify->minify();
		// $content = \Mini::css(preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $content));
	}

	if ($type == GZIPIT_TYPE_JS && GZIPIT_JSMIN) {
		// $MinifyJS = new MatthiasMullie\Minify\JS();
		// $MinifyJS->add(preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $content));
		// $content = $MinifyJS->minify();
		$JSqueeze = new Patchwork\JSqueeze();
		$content = $JSqueeze->squeeze($content, true, false, false);
		// $content = \Mini::js(preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $content));
	}

	if ($encoding != GZIPIT_ENCODING_NONE) {
		$content = gzencode($content, GZIPIT_GZIP_LEVEL, FORCE_GZIP);
		header('Content-Encoding: ' . $encoding_header);
	}

	header('Content-Length: ' . strlen($content));
	echo $content;

	if (GZIPIT_DISK_CACHE) {
		if ($fp = fopen($cached_file, 'wb')) {
			fwrite($fp, $content);
			fclose($fp);
		}
	}
} //else (no Etag)


/**
 * The End
 */
ob_end_flush();
exit;

/**
 * Utility functions
 */

/**
 * Renders 404 error to client
 *
 * @param string $message Detailed error message
 * @return void
 */
function give_404($message)
{
	printf('
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html>
	<head>
		<title>404 Not Found</title>
	</head>
	<body>
		<h1>Not Found</h1>
		<p>%s</p>
	</body>
</html>
', $message);
	header("HTTP/1.0 404 Not Found");
	ob_end_flush();
}

/**
 * Parses HTTP GET params
 *
 * @param string $param Parameter name
 * @param bool $trim Convert parameter value to lowercase and trim it
 * @return string|NULL Returns NULL if parameter doesn't exist
 */
function get_param($param, $trim = false)
{
	return
		isset($_GET[$param]) ?
		($trim ? strtolower(trim($_GET[$param])) : $_GET[$param]) :
		NULL;
}

/**
 * Returns client's accepted encoding
 * Code taken from Minify (http://code.google.com/p/minify/)
 *
 * @return void bool If client supports gzip
 */
function getAcceptedEncoding()
{
	// @link http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html

	if (
		!isset($_SERVER['HTTP_ACCEPT_ENCODING'])
		|| isBuggyIe()
	) {
		return array('', '');
	}
	$ae = $_SERVER['HTTP_ACCEPT_ENCODING'];
	// gzip checks (quick)
	if (
		0 === strpos($ae, 'gzip,')             // most browsers
		|| 0 === strpos($ae, 'deflate, gzip,') // opera
	) {
		return array('gzip', 'gzip');
	}
	// gzip checks (slow)
	if (preg_match(
		'@(?:^|,)\\s*((?:x-)?gzip)\\s*(?:$|,|;\\s*q=(?:0\\.|1))@',
		$ae,
		$m
	)) {
		return array('gzip', $m[1]);
	}
}

/**
 * Detect IE with buggy compression support (version earlier than 6 SP2)
 * Code taken from Minify (http://code.google.com/p/minify/)
 *
 * @link http://code.google.com/p/minify/
 * @return bool If client uses IE with buggy gzip support
 */
function isBuggyIe()
{
	$ua = $_SERVER['HTTP_USER_AGENT'];
	// quick escape for non-IEs
	if (
		0 !== strpos($ua, 'Mozilla/4.0 (compatible; MSIE ')
		|| false !== strpos($ua, 'Opera')
	) {
		return false;
	}
	// no regex = faaast
	$version = (float)substr($ua, 30);
	return GZIPIT_COMPRESSION_FOR_IE6
		? ($version < 6 || ($version == 6 && false === strpos($ua, 'SV1')))
		: ($version < 7);
}

class Mini
{
	public static function css($b)
	{
		return preg_replace(
			array(
				// Remove comment(s)
				'#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')|\/\*(?!\!)(?>.*?\*\/)|^\s*|\s*$#s',
				// Remove unused white-space(s)
				'#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/))|\s*+;\s*+(})\s*+|\s*+([*$~^|]?+=|[{};,>~+]|\s*+-(?![0-9\.])|!important\b)\s*+|([[(:])\s++|\s++([])])|\s++(:)\s*+(?!(?>[^{}"\']++|"(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')*+{)|^\s++|\s++\z|(\s)\s+#si',
				// Replace `0(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)` with `0`
				'#(?<=[\s:])(0)(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)#si',
				// Replace `:0 0 0 0` with `:0`
				'#:(0\s+0|0\s+0\s+0\s+0)(?=[;\}]|\!important)#i',
				// Replace `background-position:0` with `background-position:0 0`
				'#(background-position):0(?=[;\}])#si',
				// Replace `0.6` with `.6`, but only when preceded by `:`, `,`, `-` or a white-space
				'#(?<=[\s:,\-])0+\.(\d+)#s',
				// Minify string value
				'#(\/\*(?>.*?\*\/))|(?<!content\:)([\'"])([a-z_][a-z0-9\-_]*?)\2(?=[\s\{\}\];,])#si',
				'#(\/\*(?>.*?\*\/))|(\burl\()([\'"])([^\s]+?)\3(\))#si',
				// Minify HEX color code
				'#(?<=[\s:,\-]\#)([a-f0-6]+)\1([a-f0-6]+)\2([a-f0-6]+)\3#i',
				// Replace `(border|outline):none` with `(border|outline):0`
				'#(?<=[\{;])(border|outline):none(?=[;\}\!])#',
				// Remove empty selector(s)
				'#(\/\*(?>.*?\*\/))|(^|[\{\}])(?:[^\s\{\}]+)\{\}#s'
			),
			array(
				'$1',
				'$1$2$3$4$5$6$7',
				'$1',
				':0',
				'$1:0 0',
				'.$1',
				'$1$3',
				'$1$2$4$5',
				'$1$2$3',
				'$1:0',
				'$1$2'
			),
			$b
		);
	}
	public static function js($b)
	{
		return preg_replace(
			array(
				// Remove comment(s)
				'#\s*("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')\s*|\s*\/\*(?!\!|@cc_on)(?>[\s\S]*?\*\/)\s*|\s*(?<![\:\=])\/\/.*(?=[\n\r]|$)|^\s*|\s*$#',
				// Remove white-space(s) outside the string and regex
				'#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/)|\/(?!\/)[^\n\r]*?\/(?=[\s.,;]|[gimuy]|$))|\s*([!%&*\(\)\-=+\[\]\{\}|;:,.<>?\/])\s*#s',
				// Remove the last semicolon
				'#;+\}#',
				// Minify object attribute(s) except JSON attribute(s). From `{'foo':'bar'}` to `{foo:'bar'}`
				'#([\{,])([\'])(\d+|[a-z_][a-z0-9_]*)\2(?=\:)#i',
				// --ibid. From `foo['bar']` to `foo.bar`
				'#([a-z0-9_\)\]])\[([\'"])([a-z_][a-z0-9_]*)\2\]#i'
			),
			array(
				'$1',
				'$1$2',
				'}',
				'$1$3',
				'$1.$3'
			),
			$b
		);
	}
}
