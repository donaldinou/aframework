<?php
/**
*   ACREAT MULTI LANG
*/
define("ACREATMULTILANG_LANGS_DIR", APP_DIR . DS . "langs/" );

vendor("phpMultiLang/class.phpMultiLang");


$GLOBALS["ACREATMULTILANG"] = new phpMultiLang(ACREATMULTILANG_LANGS_DIR ,TMP);
$GLOBALS["ACREATMULTILANG"]->_LangStringRegex = "/(?:\n|^)(?:>\s*)?([0-9a-z_\.]+)[\s\t]*\"(.+?)(?<![\\\\])\"/is";


class AcreatMultiLang {

	/* -----------------
	* install_lang
	* Instralle une langue la langue
	*/
	function install_lang($lang, $locale) {
		global $ACREATMULTILANG;
		$ACREATMULTILANG->AssignLanguage( $lang ,null,array("LC_TIME",$locale)); 
		$ACREATMULTILANG->AssignLanguageSource($lang,"$lang.lang",3600);
	}

	/* -----------------
	* set_lang
	* Sélectionne la langue
	*/
	function set_lang($lang, $cache=true) {
		global $ACREATMULTILANG; 
		return $ACREATMULTILANG->SetLanguage($lang,$cache); // TRUE pour le cache
	}

	/* -----------------
	* auto_set_lang
	* Sélectionne la langue par rapport à la variable GET et met en session
	*/
	function auto_set_lang($default='fr', $session=true, $cache=true) {
		global $ACREATMULTILANG; 
		if($_GET["lang"] && in_array($_GET["lang"], array_keys($ACREATMULTILANG->_LangIdx))) {
			$_SESSION["ACREAT.MULTILANG"] = $_GET["lang"];
			return $ACREATMULTILANG->SetLanguage($_GET["lang"],$cache);
		} 
		return $ACREATMULTILANG->SetLanguage($_SESSION["ACREAT.MULTILANG"] ? $_SESSION["ACREAT.MULTILANG"] : $default,$cache);
	}
	
	/* -----------------
	* get_lang
	* Sélectionne la langue
	*/
	function get_lang() {
		global $ACREATMULTILANG; 
		return $ACREATMULTILANG->GetLanguage();
	}
	
	/* -----------------
	* lang
	* Renvoi la correspondance de localization
	*/
	function lang($string, $default=null) {
		global $ACREATMULTILANG;
		if( $default == null ) $default = "#$string#";
		return $ACREATMULTILANG->GetString($string, $default);
	}

	/* -----------------
	* langf
	* Renvoi la correspondance de localization sprintf
	*/
	function langf($string,$args=array(),$default=null) {
		global $ACREATMULTILANG;
		if( $default == null ) $default = "#$string#";
		return $ACREATMULTILANG->GetFString( $string,$args,$default);
	}
}

function lang() { $params = func_get_args(); return call_user_func_array(array('AcreatMultiLang', 'lang'), $params); }
function langf() { $params = func_get_args(); return call_user_func_array(array('AcreatMultiLang', 'langf'), $params); }


/* -----------------------------------------------------------------
* Intégration à la classe de View
*/

if(!class_exists("AcreatView")) {
	class AcreatView extends _AcreatView
	{	
		/* --------------------
		* renderElement
		*/
		function renderElement($name, $params=array()) {
			$name = preg_replace("/\//", DS, $name);
			$fn = VIEWS . ( !preg_match("/^\//si", $name) ? "elements/" : "" ).$name.".".$this->controller->LANGUE.$this->ext;		
			if (file_exists($fn)) 
				return $this->_render($fn, array_merge($this->_viewVars, array_merge_recursive($params, $this->loaded)), true, false);
			return parent::renderElement($name, $params);
			
		}
		
		/* --------------------
		* _getViewFileName
		*/
		function _getViewFileName($action) {
			$action = preg_replace("/\//", DS, $action);
			$relative_file_path = preg_match("/^\\".DS."/", $action) ? substr($action,1) : $this->viewPath.DS.$action;
			$relative_file_path .= ".".$this->controller->LANGUE.$this->ext;
			
			$fn = VIEWS.$relative_file_path;
			if(file_exists($fn)) return $fn;
			return parent::_getViewFileName($action);
		}
		
		/* --------------------
		* _getLayoutFileName
		*/	
		function _getLayoutFileName() {
			$layout = preg_replace("/\//", DS, $this->layout);
			$fn = LAYOUTS.$layout.".".$this->controller->LANGUE.$this->ext;
			if(file_exists($fn)) return $fn;
			return parent::_getLayoutFileName();
		}
		
		/* 
		* _render
		*/
		function _render($___viewFn, $___data_for_view, $___play_safe = true, $loadHelpers = true)
		{
			$out = parent::_render($___viewFn, $___data_for_view, $___play_safe, $loadHelpers);
			$out = preg_replace("/\B(\[\[([^\]]*)\]\])\B/esi", "nl2br(lang('\\2', '\\1'))", $out);
			return $out;
		}
	}
}
?>