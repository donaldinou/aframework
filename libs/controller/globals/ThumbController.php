<?php
/* ---
* ThumbController
* Affiche un thumb IMG
*/

class ThumbController extends AcreatController 
{	
	var $PHPTHUMB_CONFIG = array();
	var $PHPTHUMB_DEFAULTS = array();
	/**
	* Displays a view
	*/
	function index()
	{
		global $phpThumb, $PHPTHUMB_CONFIG, $PHPTHUMB_DEFAULTS;
		unset($_GET["controller"]);
		
		if(is_array($PHPTHUMB_CONFIG))
			$this->PHPTHUMB_CONFIG = $PHPTHUMB_CONFIG;
			
		if(is_array($PHPTHUMB_DEFAULTS))
			$this->PHPTHUMB_DEFAULTS = $PHPTHUMB_DEFAULTS;
		
		// NETTOYAGES DES PARAMETRES INTERDITS
		$allowedGETparameters = array('src', 'new', 'w', 'h', 'wp', 'hp', 'wl', 'hl', 'ws', 'hs', 'f', 'q', 'sx', 'sy', 'sw', 'sh', 'zc', 'bc', 'bg', 'bgt', 'fltr', 'xto', 'ra', 'ar', 'aoe', 'far', 'iar', 'maxb', 'down', 'phpThumbDebug', 'hash', 'md5s', 'sfn', 'dpi', 'sia');
		foreach($_GET as $key=>$value) {
			if( !in_array($key, $allowedGETparameters) )
				unset($_GET[$key]);
		}
		
		require_once VENDORS . DS . "phpThumb" . DS . "phpThumb.config.php";
		
		$this->_setConfig("cache_directory", 			CACHE);
		$this->_setConfig("temp_directory", 			TMP);
		$this->_setConfig("disable_debug", 				!isset($this->params["url"]["enable_debug"]));	
		$this->_setConfig("allow_src_above_docroot", 	true);	
		
		$PHPTHUMB_CONFIG 	= array_merge($PHPTHUMB_CONFIG, $this->PHPTHUMB_CONFIG);
		$PHPTHUMB_DEFAULTS 	= $this->PHPTHUMB_DEFAULTS;
		
		
		require_once VENDORS . DS . "phpThumb" . DS . "phpThumb.php";
		//$phpThumb->CleanUpCacheDirectory();
	}
	
	/**
	* _setDefault
	*/	
	function _setDefault($var, $value) {
		if( !isset($this->PHPTHUMB_DEFAULTS[$var]) )
			$this->PHPTHUMB_DEFAULTS[$var] = $value;
	}
	
	/**
	* _setConfig
	*/	
	function _setConfig($var, $value) {
		if( !isset($this->PHPTHUMB_CONFIG[$var]) )
			$this->PHPTHUMB_CONFIG[$var] = $value;
	}
}
?>