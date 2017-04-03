<?php 
define( "ACREATLOGIN_MSG_FAILED", "identification inconnue !" );
define( "ACREATLOGIN_MSG_BANNED", "votre accès n'est pas actif !" );

class AcreatLogin
{
	var $id = "AcreatLogin";
	
	var $query = array(
		"login"=>"admin", 
		"password"=>"admin"
	);
	
	var $logged 	= false; 
	var $db 		= false;
	var $last_error = NULL;
	var $infos 		= NULL;
	var $login_keys = NULL;
	var $session	= NULL;
	/*
	var $loginVarName 	= "login";
	var $passVarName 	= "password";
	*/
	var $template 		= "/errors/login";
	
	function AcreatLogin($REQUETE=false, $ID=NULL, $db=null)
	{ 
		$this->id = !empty($ID) ? $ID : md5(dirname($_SERVER["SCRIPT_FILENAME"]));
		$this->db = $db ? $db : $GLOBALS["DB"];
		if( !empty($REQUETE) ) 
			$this->query = $REQUETE;
		$this->session = &$_SESSION['ACREAT.SECU'][$this->id];
	}
	
	/* ---------------- */
	/* set_infos() 
	/*/
	function set_infos($items) {
		$this->infos = $items;
		foreach($this->infos as $key => $value)
			$this->$key = $value;
	}
	
	/* ---------------- */
	/* check() 
	/* verification
	/*/
	function check() 
	{ 
		if(isset($_GET["logout"])) 
			$this->logout();
		
		if($this->logged) 
			return true;
		
		if($this->logged = $this->try_login()) {
			header("Location: ".call_user_func_array("get_clean_url", array_merge(array("logout"), $this->login_keys)));
			exit;
		}

		if(isset($_SESSION['ACREAT.SECU'][$this->id])) { 
			$this->set_infos($_SESSION['ACREAT.SECU'][$this->id]);
			return ($this->logged = true);
		}
		
		return false;
	}
	
	/* ----------------- */
	/* try_login() 
	/* tentative d'identification
	/*/
	function try_login() {
		$_PARAMS = array_merge($_GET, $_POST);
		$PARAMS = array();
		// ---
		$this->login_keys = array();
		
		/* compatibilités */
		switch(get_class($this->query)) {
			case "AcreatDBSelect": 	$this->query = $this->query->toString(); 	break;
			case "SQLBuilder": 		$this->query = $this->query->build(); 		break;
		}
		
		if( is_array($this->query) ) {
			$this->login_keys = array_keys($this->query);
			if($this->login_keys[0] == 0) $this->login_keys[0] = "login";
			if($this->login_keys[1] == 1) $this->login_keys[1] = "password";
		} else {
			preg_match_all("/\{(\w+)\}/is", $this->query, $matches);
			$this->login_keys = $matches[1];
		}
		// ---
		foreach($this->login_keys as $key) {
			if( array_search($key, array_keys($_PARAMS)) === false )
				return false;
			$PARAMS[$key] = $_PARAMS[$key];
		}
		// ---	
		if( is_array($this->query) ) { 
			// C'est un tableau
			if( count(array_diff($PARAMS, $this->query)) == 0 ) {
				$_SESSION['ACREAT.SECU'][$this->id] = array('logged' => true);
				return true;
			}
		} else {
			if(!$this->db) return false;
			$REQ = $this->query;
			foreach( $PARAMS as $key=>$value)
				$REQ = preg_replace("/\{$key\}/si", $this->db->Quote(stripslashes($value)), $REQ);
			$rs = $this->db->Execute($REQ);

			if($rs->RowCount() > 0) { 
				$user = $rs->FetchRow();
				if(!empty($user["BANNED"])) { 
					$this->error( ACREATLOGIN_MSG_BANNED );
					return false; 
				}
				$_SESSION['ACREAT.SECU'][$this->id] = $user;
				$this->set_infos($_SESSION['ACREAT.SECU'][$this->id]);
				return true;
			}
		}
		
		$this->error( ACREATLOGIN_MSG_FAILED );
		return false;
	}
	
	/* ----------------- */
	/* error($msg) 
	/* Gestion des erreurs
	/*/
	function error($msg) { 
		$this->last_error = ucfirst($msg); 
	}
	
	/* ----------------- */
	/* logout()
	/* Deconnexion
	/*/
	function logout() 
	{ 
		unset($_SESSION['ACREAT.SECU'][$this->id]);
		return ($this->logged_in = false); 
	} 
	
	/* ---
	* LockController
	* Sert à bloquer l'ensemble d'un controller
	* AcreatLogin::LockController
	*/
	function LockController(&$controller, $REQUETE="", $id=false, $template=false, $layout=false) 
	{
		$ACREATLOGIN = new AcreatLogin($REQUETE, $id);
		
		if($template) 
			$ACREATLOGIN->template = $template;
			
		if(!$ACREATLOGIN->check()) { 
			if($layout)
				$controller->layout = $layout;
			if($ACREATLOGIN->last_error )
				$controller->message = $ACREATLOGIN->last_error;
			$controller->render($ACREATLOGIN->template);
			exit;
		} elseif(isset($ACREATLOGIN->infos)) {
			foreach( $ACREATLOGIN->infos as $key=>$value)
				$ACREATLOGIN->$key = $value;
		}
		$controller->AcreatLogin = $ACREATLOGIN;
		$controller->set("ACREATLOGIN", $ACREATLOGIN);
	}
}


?>