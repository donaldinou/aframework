<?
/************************************************************    
    GET, POSTS and Multipat Posts ( Upload files and etc..)
    CREATED BY: Tiago Serafim
    DATE      : 30/08/2003
    EMAIL     : TIAGO at HEX dot COM dot BR

    This class Extends cHTTP Class
    http://www.phpclasses.org/browse.html/package/1119.html
************************************************************/
/*
    Date        Update


*/
require dirname(__FILE__."/cHTTP.php");

class cHTTPMultipart extends cHTTP {
	
	var $boundary;
	
	var $multiPartFields = "";

	function cHTTPMultipart() {
		
		$this->boundary = "hereitgoes->>" . md5(time()) . "-hereitgone->>";
		

	}
		
	function addMultipart($field) {
		
		$this->multiPartFields.= $field;

	}

	function addMultipartField($sName, $sValue) {
		
		$temp  = "Content-Disposition: form-data; name=\"$sName\"";
		$temp .= "\r\n\r\n";
		$temp .= $sValue;
		$temp .= "\r\n";
		$temp .= "--" . $this->getBoundary();

		$this->addMultipart($temp);
					
	}

	function addMultipartFile($sName, $sFileName) {
		
		$fileExt = substr($sFileName, strrpos($sFileName, ".")+1);
		$theFile = implode(file($sFileName), "");

		$temp = "Content-Disposition: form-data; name=\"$sName\"; filename=\"$sFileName\"";
		$temp .= "\r\n";
		$temp .= "Content-Type: " . $this->getContentType($fileExt) . "";
		$temp .= "\r\n\r\n";
		$temp .= $theFile;
		$temp .= "\r\n";
		$temp .= "--" . $this->getBoundary() . "\r\n";

		$this->addMultipart($temp);

	}
	
	function postPageMultipart($sURL) {
	/*
		IMPORTANT - multipart/form-data Especification
			
		RFC: Nebel, E. and L. Masinter, "Form-based File Upload in HTML", RFC 1867, November 1995.

		URL: http://www.faqs.org/rfcs/rfc1867.html

	*/

		$sInfo = $this->parseRequest($sURL);
			$request = $sInfo['request'];
			$host    = $sInfo['host'];
			$port    = $sInfo['port'];

		$httpHeader  = "POST $request HTTP/1.0\r\n";
		$httpHeader .= "Host: $host\r\n";
		$httpHeader .= "Connection: Close\r\n";
		$httpHeader .= "User-Agent: cHTTPMultipart/0.1b (incompatible; M$ sucks; Open Source Rulez)\r\n";
		
		$httpHeader .= "Content-Type: multipart/form-data; boundary=" . $this->getBoundary() . "\r\n";;

		$httpHeader .= "Content-length: " . strlen($this->multiPartFields) . "\r\n";
		$httpHeader .= "Referer: " . $this->referer . "\r\n";

		$httpHeader .= "Cookie: " . $this->theCookies . "\r\n";

		$httpHeader .= "\r\n";

		$httpHeader .= "--" . $this->getBoundary();

		$httpHeader .= "\r\n";
		
		$httpHeader .= $this->multiPartFields;
		
		$httpHeader .= "--\r\n\r\n";
		

		$this->theData = $this->downloadData($host, $port, $httpHeader); // envia os dados para o servidor


	}




	function getBoundary() {
		return $this->boundary;;
	}

	function getContentType($ext) {
		
		switch($ext) {
			
			case "txt":  return "text/plain";
			case "rar":  return "application/rar";
			case "zip":  return "application/zip";
			case "exe":  return "application/octet-stream";
			case "jpg":
			case "jpge": return "image/jpeg";
			case "gif":  return "image/jpeg";
			case "htm":
			case "html": return "text/html";
			case "css":  return "text/css";
			case "pdf":  return "application/pdf";
			case "ppt":  return "application/ms-powerpoint";
			case "doc":  return "application/msword";
			case "xls":  return "application/ms-excel";

			default: return "application/unkown";
			
		}

	}




} // class
    
?> 