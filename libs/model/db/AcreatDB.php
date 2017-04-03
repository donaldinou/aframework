<?php 
vendor("AdoDB/adodb.inc");
uses("model/db/AcreatDBSelect");

function AcreatDB($config, $driver="mysql") 
{
	global $ADODB_FORCE_TYPE;
	$ADODB_FORCE_TYPE = ADODB_FORCE_EMPTY;
	
	if(is_string($config)) {
		$_adodb = NewADOConnection($config);
	} else {
		$config["dbname"] 	= (isset($config["dbname"]) ? $config["dbname"] : "");
		$config["host"] 	= (isset($config["host"]) ? $config["host"] : "localhost");
		$config["username"] = (isset($config["username"]) ? $config["username"] : "root");
		$config["password"] = (isset($config["password"]) ? $config["password"] : "");
		$_adodb = ADONewConnection($driver);
		$_adodb->Connect($config['host'], $config['username'], $config["password"], $config["dbname"]);
	}
	
	$_adodb->SetFetchMode(ADODB_FETCH_ASSOC);
	
	if(!isset($GLOBALS["ACREATDB"]))
		$GLOBALS["ACREATDB"] = &$_adodb;
	
	return $_adodb;
}

/* ---------------------
* CLASS ACREATDB 
*/

class AcreatDB
{
	/* --- 
	* AcreatDB
	*/
	function AcreatDB() { die("AcreatDB : La classe ne s'utilise pas directement."); }
	
	/* --- 
	* AcreatDB::getInstance
	*/
	function getInstance() { 
		if( isset($GLOBALS["ACREATDB"]) )
			return $GLOBALS["ACREATDB"];
		else
			return false;
	}
	
	/* --- 
	* select
	*/
	function select() {
		return new AcreatDBSelect($this);
	}

}

?>