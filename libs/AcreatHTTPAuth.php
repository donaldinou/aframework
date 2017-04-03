<?
class AcreatHTTPAuth
{   
	var $realm = "Espace protégé";
	var $login = false;
	var $password = false;
	var $message = "Espace non autorisé !";
	
	var $handler = false;
	
	/* ----------
	* AcreatHTTPAuth
	*/
	function AcreatHTTPAuth($realm=false,$login=false,$password=false) {
		if($realm) 		$this->realm = $realm;
		if($login) 		$this->login = $login;
		if($password) 	$this->password = $password;
		if(!$this->handler)
			$this->handler = array(get_class($this), '_authentify');
	}
	
	/* ----------
	* _authentify
	*/
	function _authentify($login, $password) {
		return ($login && $login == $this->login && $password = $this->password);
	}
	
	/* ----------
	* authentify
	*/
	function authentify() {
		if(!call_user_func($this->handler,$_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW'])) {
			header('WWW-Authenticate: Basic realm="'.$this->realm.'"');
			header('HTTP/1.0 401 Unauthorized');
			echo $this->message;
			exit;
		}
		return true;
	}
}
?>