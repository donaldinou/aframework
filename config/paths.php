<?php
/* SVN FILE: $Id: paths.php 1614 2005-12-24 07:56:48Z phpnut $ */

/**
 * Short description for file.
 * In this file you set paths to different directories used by the Framework.
 */

define ('FRAMEWORK_DIR',   basename(dirname(dirname(__FILE__))));

define ('WEBROOT_DIR',   'www');

define ('APP_DIR', '..');
 
/**
 * Path to the application's directory.
 */
define ('APP',         APP_DIR.DS);

/**
 * Path to the application's services directory.
 */
define ('SERVICES',          APP.'services'.DS);

/**
 * Path to the application's models directory.
 */
define ('MODELS',          APP.'models'.DS);

/**
 * Path to the application's models directory.
 */
define ('REPOSITORIES',          APP.'repositories'.DS);

/**
 * Path to the application's controllers directory.
 */
define ('CONTROLLERS',     APP.'controllers'.DS);

/**
 * Path to the application's controllers directory.
 */
define ('COMPONENTS',     CONTROLLERS.'components'.DS);

/**
 * Path to the application's views directory.
 */
define ('VIEWS',           APP.'views'.DS);

/**
 * Path to the application's helpers directory.
 */
define ('HELPERS',         VIEWS.'helpers'.DS);

/**
 * Path to the application's view's layouts directory.
 */
define ('LAYOUTS',         VIEWS.'layouts'.DS);

/**
 * Path to the application's view's elements directory.
 * It's supposed to hold pieces of PHP/HTML that are used on multiple pages
 * and are not linked to a particular layout (like polls, footers and so on).
 */
define ('ELEMENTS',        VIEWS.'elements'.DS);

/**
 * Path to the configuration files directory.
 */
define ('CONFIGS',     APP.'config'.DS);

/**
 * Path to the libs directory.
 */
define ('LIBS',        FRAMEWORK_DIR.DS.'libs'.DS);

/**
 * Path to the logs directory.
 */
define ('LOGS',        FRAMEWORK_DIR.DS.'logs'.DS);

/**
 * Path to the modules directory.
 */
define ('MODULES',     FRAMEWORK_DIR.DS.'modules'.DS);

/**
 * Path to the public directory.
 */
define ('WWW_ROOT',    APP.WEBROOT_DIR.DS);

/**
 * Path to the public directory.
 */
define ('CSS',            WWW_ROOT.'css'.DS);

/**
 * Path to the public directory.
 */
define ('JS',            WWW_ROOT.'js'.DS);

/**
 * Path to the temporary files directory.
 */
define ('TMP',     APP.'tmp'.DS);

/**
 * Path to the cache files directory. It can be shared between hosts in a multi-server setup.
 */
define('CACHE', TMP.'cache'.DS);

/**
 * Path to the vendors directory.
 */
define ('VENDORS',     FRAMEWORK_DIR.DS.'vendors'.DS);

/**
 * Path to the Pear directory
 */
define ('PEAR',  VENDORS.'Pear'.DS);

/**
 *  Full url prefix
 */
$s = null;
if ( (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] =='on' )) { 
    $s ='s';
}
if (isset($_SERVER['HTTP_HOST'])) {
    define('FULL_BASE_URL', 'http'.$s.'://'.$_SERVER['HTTP_HOST']);
}

/**
 * Web path to the public images directory.
 */
define ('IMAGES_URL',          'img/');

/**
 * Web path to the CSS files directory.
 */
define ('CSS_URL',            'css/');

/**
 * Web path to the js files directory.
 */
define ('JS_URL',            'js/');


?>
