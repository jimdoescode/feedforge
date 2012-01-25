<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');

error_log(print_r($_SERVER, true));
//Attempt to determine the correct base url to resolve to.
$base_url = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') ? 'https' : 'http';
$base_url .= isset($_SERVER['HTTP_HOST']) ? "://{$_SERVER['HTTP_HOST']}" : '://localhost';
if(isset($_SERVER['REDIRECT_URL']))
{
    $base_url .= $_SERVER['REDIRECT_URL'];
    if(isset($_SERVER['REDIRECT_QUERY_STRING']))$base_url = substr($base_url, 0, strpos($base_url, $_SERVER['REDIRECT_QUERY_STRING']));
}
elseif(isset($_SERVER['REQUEST_URI']))
{
    $uri = substr($_SERVER['REQUEST_URI'], -1) == '/' ? $_SERVER['REQUEST_URI'] : $_SERVER['REQUEST_URI'].'/';
    $script = substr($_SERVER['SCRIPT_NAME'], -9) == 'index.php' ? substr($_SERVER['SCRIPT_NAME'], 0, -9) : $_SERVER['SCRIPT_NAME'];
    error_log($uri);
    if($uri != $script && strpos($uri, $script) === 0)$uri = substr($uri, 0, strlen($script));
    error_log($uri);
    $base_url .= $uri;

    unset($script);
    unset($uri);
}
if(substr($base_url, -9) == 'index.php')$base_url = substr($base_url, 0, strlen($base_url)-9); //If we end in index.php remove it
$base_url .= substr($base_url, -1) != '/' ? '/' : ''; //If we don't end in a slash add one.
error_log($base_url);
//Remove trailing index.php if it is there.

define('BASE_URL', $base_url);
unset($base_url);

/* End of file constants.php */
/* Location: ./application/config/constants.php */