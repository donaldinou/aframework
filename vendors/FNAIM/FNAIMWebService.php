<?php
// Set error reporting to ignore notices
define('FNAIM_SOAP_WSDL' , "http://62.23.140.25/plg/services/ConnectiqueService.asmx?WSDL" );
define('FNAIM_SOAP_NAMESPACE' , "http://www.fnaim.org/");
define('FNAIM_SOAP_SSII_LOGIN' , "acreat");

vendor("NuSoap/nusoap");

class FNAIMWebService
{
	var $agence;
	var $TOKEN;
	var $SSII;
	var $lastResult;
	var $lastError;
	var $lastSuccess;
	var $DEBUG;
	// ---------------
	// CFNAIMWebService
	// constructeur de la classe
	// Prend en parametre le numero de serie du dd et la cle distante
	function FNAIMWebService($codeAgence,$cleDistante="",$cleLocale="", $DEBUG = false)
	{ 
		$this->DEBUG = $DEBUG;
		$this->agence = $codeAgence;
		if(empty($cleDistante))
		{
			$this->lastError = "FNAIM : Le numero de license IRIS n'est pas renseigné";
			return false;
		}
		if(!empty($cleDistante) && !empty($cleLocale))
			return $this->authenticateUser($cleDistante, $cleLocale);
		return true;
	}
	// ---------------
	// authenticateUser
	// Lance la procédure d'authentification pour la récupération du Token
	function authenticateUser($cleDistante, $cleLocale)
	{
		$REPONSE = $this->_call('AuthenticateUser',array("CleLocal" => $cleLocale,"CleDistante" => $cleDistante),false);
		if(!isset($REPONSE["AuthenticateUserResult"]))
		{
			$this->lastError = "FNAIM : Impossible de s'identifier";
			return false;
		}
		else
		{
			$this->TOKEN = $REPONSE["AuthenticateUserResult"];
			$this->getSSIIIdent();
			return true;
		}
	}
	// ---------------
	// getSSIIIdent
	// Récupére l'identifiant SSII a partir du login
	function getSSIIIdent()
	{
		$REPONSE = $this->_call("GetSSIIAuth",array("LogSSII"=>FNAIM_SOAP_SSII_LOGIN));
		$this->SSII = (isset($REPONSE["GetSSIIAuthResult"])) ? $REPONSE["GetSSIIAuthResult"] : 0;
	}
	// ---------------
	// _serializeBien
	// Serialize un bien FNAIM
	// Prend en paramètre un objet FNAIM_BIEN (voir CFNAIMIris)
	function _serializeBien($BIEN=NULL)
	{ 
		$options = array(
			'indent'			=> "",        // indent with tabs
			'mode'               => 'simplexml',  
			'linebreak' 		=> "",        // use UNIX line breaks
			'rootName'			=> 'BIEN',   // root tag
			'defaultTagName'	=> 'item',       // tag for values with numeric keys
			'attributesArray'	=> 'PARAMS',                     
			'encoding' 			=> 'ISO-8859-1',
   		);
			
		//if(isset($BIEN->PARAMS) && count($BIEN->PARAMS) > 0) 
			//$options['rootAttributes'] = array_merge($options['rootAttributes'],$BIEN->PARAMS);
		 
		$serializer = new XML_Serializer($options);
 	  	$serializer->serialize($BIEN);
		$XML_DATAS = $serializer->getSerializedData();
		return $XML_DATAS;
	}
	// ---------------
	// majBien
	// Met à jour un bien sur le serveur FNAIM
	// Prend en paramètre un objet FNAIM_BIEN (voir CFNAIMIris)
	function majBien($BIEN=NULL)
	{
		$XMLBien = $this->_serializeBien($BIEN);
		//header("Content-type: text/xml");
		//print($XMLBien); die;
		return $this->_call("SetAffaire",array("XmlBien"=>$XMLBien,"Orig"=>$this->SSII,"DateEnvoi"=>date("Y-m-d")));
	}
	// ---------------
	// registerNewKey($cleDistante)
	// Enregistre une clé de license IRIS pour l'utilisation du système
	function registerLicense($cleDistante="")
	{
		$cleDistante = ereg_replace("[^[:alnum:]]","",$cleDistante);
		srand((double)microtime()*1000000);
		$clePoste = rand(1000000000,9999999999); 
		$reponse = $this->_call("SetCleLibre",array("AgenceId"=>$this->agence,"ClePoste"=>$clePoste,"CleDistante"=>$cleDistante),false);
		if($this->lastError == "")
			return $clePoste;
		else
			return false;
	}
	// ---------------
	// call
	// Appelle une procedure WebService $operation avec les paramètres associés
	// et renvoi la réponse.
	function _call($operation,$params=NULL,$addToken=true, $timeout=5)
	{ 
		$soapAction = "http://www.fnaim.org/".$operation;
		$client = new soapclient(FNAIM_SOAP_WSDL);
		$client->operation = $operation;
		
		// Définition du token
		if($addToken && !is_string($addToken))
			$addToken = "TokenString";
		
		// Fabrication de l'enveloppe
		$ENVELOPPE = '<'.$operation.' xmlns="'.FNAIM_SOAP_NAMESPACE.'">';
		if(!empty($addToken))
			$ENVELOPPE .= '<'.$addToken.'>'.$this->TOKEN.'</'.$addToken.'>';
		if(is_array($params))
		{
			foreach($params as $key=>$value)
				$ENVELOPPE .= '<'.$key.'>'.$value.'</'.$key.'>';
		}
		$ENVELOPPE .= '</'.$operation.'>';
		
		// Envoi de l'enveloppe
		$mySoapMsg = $client->serializeEnvelope($ENVELOPPE,'',array(),'document', 'literal');
		$response = $client->send($mySoapMsg, $soapAction, $timeout);
		if($this->DEBUG)
		{
			echo '<h2>REQUETE '.$operation.'</h2><pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre><hr>';
			echo '<h2>REPONSE '.$operation.'</h2><pre>' . htmlspecialchars($client->response, ENT_QUOTES) . '</pre><hr>';
		}
		$this->lastResult = $response;
		if($this->_analyseError($operation, $response))
			return false;
		return $response;
	}
	// ---------------
	// call
	// Appelle une procedure WebService $operation avec les paramètres associés
	// et renvoi la réponse.
	function _analyseError($operation, $reponse)
	{ 
		$this->lastSuccess = "";
		$this->lastError = "";
		if(is_array($reponse) && isset($reponse["faultstring"]))
		{
			$this->lastError = ereg_replace("Server was unable to process request. --> ","",$reponse["faultstring"]);
			return true;
		}
		// --
		if(is_array($reponse) && isset($reponse[$operation."Result"]))
		{
			$this->lastSuccess = $reponse[$operation."Result"];
			return false;
		}
		// --
		return false;
	}
	// ---------------
}
?>