<?php 
class AcreatExport
{
	var $_id;
	var $_datas;
	var $_cols;
	var $_rs;
	
	/* ---
	* CONTRUCTEUR
	*/
	function AcreatExport($id=null, $datas=false, $cols=false) 
	{
		if( $id === null )
			$id = md5(time());
			
		if(is_string($id) && isset($_SESSION["ACREATEXPORT"][$id]) )
			$id = $_SESSION["ACREATEXPORT"][$id];
	
		$class = get_class($id);
		if( strtolower($class) == "acreatexport" || ($class == "__PHP_Incomplete_Class" && $id->__PHP_Incomplete_Class_Name = "AcreatExport") ) {
			// Construction a partir d'une class fille
			$vars = get_object_vars($id);
			foreach($vars as $key=>$var)
				$this->$key = $var;
		}
		
		if(!$this->_id && is_string($id)) 
			$this->_id = $id;
			
		if( get_parent_class($datas) == "AcreatModel" ) {
			$SQL = $datas;
			$datas = $SQL->_lastSelectObj;
			if(!$cols) $cols = $SQL->_headers;
		}
		
		$this->_rs 		= null;		
		if($datas)	
			$this->_datas = $datas;
		if($cols)	
			$this->_cols = $cols;
			
		$_SESSION["ACREATEXPORT"][$this->_id] = &$this;
	}
	
	
	/* ---
	* _fetch
	* Retourne les datas
	*/	
	function _fetch($nonext=false) 
	{
		if(!$this->_datas) return false;
	
		if( is_array($this->_datas) ) {
			$item = & current($this->_datas);
			if(!$nonext) next($this->_datas);
			return $item;
		}
		
		elseif( $this->_rs ) {
			$item = & $this->_rs->FetchRow();
			if($nonext) $this->_rs->MoveLast();
			return $item;
		}
		
		elseif( strtolower(get_class($this->_datas)) == "acreatdbselect" ) {
			$db = AcreatDB::getInstance();
			if(!$db) return false;
			$this->_datas->limit(null, null);	
			$sql = $this->_datas->toString();
			$this->_rs = $db->Execute($sql);
			return $this->_fetch($nonext);
		}
		return false;
	}
	
	/* ---
	* _loadCols
	* Charge les colonnes a partir du tableau
	*/	
	function _loadCols() 
	{
		if(!$this->_cols)
			$this->_cols = array();
		
		$first = $this->_fetch(true);
		if(!$first) return false;
		
		$real_keys = array_keys($first);
			
		if( !$this->_cols ) {
			// Aucune colonne n'a été définie
			$this->_cols = array();
			foreach($real_keys as $key)
				$this->_cols[$key] = $key;
		} else{
		
			// Suppression des entete de table (table.colonne)
			foreach( $this->_cols as $key=>$col ) {
				if( preg_match("/([^\.]*)\.([^\.]*)/", $key) ) {
					unset( $this->_cols[$key] );
					$key = preg_replace("/^([^\.]*)\./","", $key);
					$this->_cols[$key] = $col;
				}
			}
		
			// Suppression des colonnes non existantes
			foreach( $this->_cols as $key=>$col ) {
				// Traitements des champs déclarés comme non exportable
				if( preg_match("/^_/", $col) || array_search($key, $real_keys) === false )
					unset($this->_cols[$key]);
			}
		}
	}
	
	/* ---
	*
	*/
	function export($format="xls") 
	{
		$this->_loadCols();
		
		$format = strtoupper($format);
		$className = "AcreatExport".$format;
		
		$classFile = dirname(__FILE__)."/types/".strtolower($format).".php";
		$classFileExists = file_exists($classFile);
		
		if($classFileExists) {
			require_once($classFile);
			$classFileExists = class_exists($className);
		}
			
		if(!$classFileExists) 
			return user_error("Ce format n'est pas pris en charge : $format");
		
		$objet = new $className(&$this);
		
		$args = func_get_args();
		unset($args[0]); 
		return call_user_func_array( array(&$objet, "export"), $args);
	}
	
}
?>