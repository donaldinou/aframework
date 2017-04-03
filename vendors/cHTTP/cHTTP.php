<?
/************************************************************    
    GET and POSTS REQUESTS TO HTTP SERVER
    CREATED BY: Tiago Serafim
    DATE      : 04/05/2003
    EMAIL     : TIAGO at HEX dot COM dot BR
************************************************************/
/*
    Date        Update


*/

class cHTTP {
	
	var $referer;
	var $postStr;
	
	var $retStr;
	var $theData;

	var $theCookies;



	function clsHTTP(){
	

	
	}
	

	function setReferer($sRef){
		$this->referer = $sRef;
	}


	function addField($sName, $sValue){
		$this->postStr .= $sName . "=" . $this->HTMLEncode($sValue) . "&";
	}
	function clearFields(){
		$this->postStr = "";
	}
	


	function checkCookies(){
		$cookies = explode("Set-Cookie:", $this->theData );
		$i = 0;
		if ( count($cookies)-1 > 0 ) {
			while(list($foo, $theCookie) = each($cookies)) {
				if (! ($i == 0) ) {
					@list($theCookie, $foo) = explode(";", $theCookie);
					list($cookieName, $cookieValue) = explode("=", $theCookie);
					@list($cookieValue, $foo) = explode("\r\n", $cookieValue);
					$this->setCookies(trim($cookieName), trim($cookieValue));
				}
				$i++;
			}
		}

	}

	function setCookies($sName, $sValue){

		$total = count(explode($sName, $this->theCookies));

		if ( $total > 1 ) {
			list($foo, $cValue)  = explode($sName, $this->theCookies);
			list($cValue, $foo)  = explode(";", $cValue);
			
			$this->theCookies = str_replace($sName . $cValue . ";", "", $this->theCookies);
		}
		$this->theCookies .= $sName . "=" . $this->HTMLEncode($sValue) . ";";
	}

	function getCookies($sName){
		list($foo, $cValue)  = explode($sName, $this->theCookies);
		list($cValue, $foo)  = explode(";", $cValue);
			return substr($cValue, 1);
	}
	
	function clearCookies(){
		$this->theCookies = "";
	}


	function getContent(){
		list($header, $foo)  = explode("\r\n\r\n", $this->theData);
		list($foo, $content) = explode($header, $this->theData);
			return substr($content, 4);
	}

	function getHeaders(){
		list($header, $foo)  = explode("\r\n\r\n", $this->theData);
		list($foo, $content) = explode($header, $this->theData);
			return $header;
	}

	function getHeader($sName){
		list($foo, $part1) = explode($sName . ":", $this->theData);
		list($sVal, $foo)  = explode("\r\n", $part1);
			return trim($sVal);
	}


	function postPage($sURL){
		
		$sInfo = $this->parseRequest($sURL);
			$request = $sInfo['request'];
			$host    = $sInfo['host'];
			$port    = $sInfo['port'];

		$this->postStr = substr($this->postStr, 0, -1); //retira a ultima &

		$httpHeader  = "POST $request HTTP/1.0\r\n";
		$httpHeader .= "Host: $host\r\n";
		$httpHeader .= "Connection: Close\r\n";
		$httpHeader .= "User-Agent: cHTTP/0.1b (incompatible; M$ sucks; Open Source Rulez)\r\n";
		$httpHeader .= "Content-type: application/x-www-form-urlencoded\r\n";
		$httpHeader .= "Content-length: " . strlen($this->postStr) . "\r\n";
		$httpHeader .= "Referer: " . $this->referer . "\r\n";

			$httpHeader .= "Cookie: " . $this->theCookies . "\r\n";

		$httpHeader .= "\r\n";
		$httpHeader .= $this->postStr;
		$httpHeader .= "\r\n\r\n";
			
		$this->theData = $this->downloadData($host, $port, $httpHeader); // envia os dados para o servidor
		
		$this->checkCookies();

	}

	function getPage($sURL){
		
		$sInfo = $this->parseRequest($sURL);
			$request = $sInfo['request'];
			$host    = $sInfo['host'];
			$port    = $sInfo['port'];

		$httpHeader  = "GET $request HTTP/1.0\r\n";
		$httpHeader .= "Host: $host\r\n";
		$httpHeader .= "Connection: Close\r\n";
		$httpHeader .= "User-Agent: cHTTP/0.1b (incompatible; M$ sucks; Open Source Rulez)\r\n";
		$httpHeader .= "Referer: " . $this->referer . "\r\n";
		
			$httpHeader .= "Cookie: " . substr($this->theCookies, 0, -1) . "\r\n";

		$httpHeader .= "\r\n\r\n";
		
		$this->theData = $this->downloadData($host, $port, $httpHeader); // envia os dados para o servidor

	}
	

	
	function parseRequest($sURL){

		list($protocol, $sURL) = explode('://', $sURL); // separa o resto
		list($host, $foo)      = explode('/',   $sURL); // pega o host
		list($foo, $request)   = explode($host, $sURL); // pega o request
		@list($host, $port)     = explode(':',   $host); // pega a porta
			
			if ( strlen($request) == 0 ) $request = "/";
			if ( strlen($port) == 0 )    $port = "80";
		
		$sInfo = Array();
		$sInfo["host"]     = $host;
		$sInfo["port"]     = $port;
		$sInfo["protocol"] = $protocol;
		$sInfo["request"]  = $request;

			return $sInfo;

	}

			/* changed 06/30/2003 */
	function HTMLEncode($sHTML){
		$sHTML = urlencode($sHTML);
			return $sHTML;
	}

	function downloadData($host, $port, $httpHeader){
		$fp = fsockopen($host, $port);
		$retStr = "";
		if ( $fp ) {
			fwrite($fp, $httpHeader);
				while(! feof($fp)) {
					$retStr .= fread($fp, 1024);
				}
			fclose($fp);
		}
		return $retStr;
	}




} // class 