<?
@define( "SOCGEN_MERCHANT_ID", 	"014213245611111" );
//define( "SOCGEN_MERCHANT_ID", 	"043316898600019" );
@define( "SOCGEN_PATH_BIN", 		dirname(__FILE__) . "/bin/" );
@define( "SOCGEN_PATH_FILE", 	APP_DIR . "/sogenactif/pathfile" );


class AcreatSogenactif
{   
	var $params = array();
	
	var $merchant_id = null;
	var $path_bin_request = null;
	var $path_bin_response = null;
	var $pathfile 	= null;
	
	function AcreatSogenactif() {
		$this->merchant_id 			= SOCGEN_MERCHANT_ID;
		$this->path_bin_request 	= SOCGEN_PATH_BIN . "request";
		$this->path_bin_response 	= SOCGEN_PATH_BIN . "response";
		$this->pathfile 			= SOCGEN_PATH_FILE;
	}
	
	/* ---
	* set
	* */
	function set($var, $value) { 
		$this->params[$var] = $value; 
	} 

	/* ---
	* _build_params
	* */
	function _build_params()
	{
		$params = "";
		foreach($this->params as $key => $value) {
			$value = preg_replace("/&/","\\\\\\0",$value);
			$params .= " {$key}={$value}";
		}
		return $params;
	} 	
	
	/* ---
	* request
	* */
	function request()
	{
		$result = exec( $this->path_bin_request." ".$this->_build_params() );
		$tableau = explode ("!", "$result");
		
		$RESULT["code"] 	= @$tableau[1];
		$RESULT["error"] 	= @$tableau[2];
		$RESULT["message"] 	= @$tableau[3];
		
		return $RESULT;
	} 
	
	/* ---
	* response
	* */
	function response()
	{
		$response = exec( $this->path_bin_response." ".$this->_build_params() );
		$tableau = explode ("!", "$response");
		
		$RESULT["code"] 	= $tableau[1];
		$RESULT["error"] 	= $tableau[2];
		$RESULT["merchant_id"] = $tableau[3];
		$RESULT["merchant_country"] = $tableau[4];
		$RESULT["amount"] = $tableau[5];
		$RESULT["transaction_id"] = $tableau[6];
		$RESULT["payment_means"] = $tableau[7];
		$RESULT["transmission_date"] = $tableau[8];
		$RESULT["payment_time"] = $tableau[9];
		$RESULT["payment_date"] = $tableau[10];
		$RESULT["response_code"] = $tableau[11];
		$RESULT["payment_certificate"] = $tableau[12];
		$RESULT["authorisation_id"] = $tableau[13];
		$RESULT["currency_code"] = $tableau[14];
		$RESULT["card_number"] = $tableau[15];
		$RESULT["cvv_flag"] = $tableau[16];
		$RESULT["cvv_response_code"] = $tableau[17];
		$RESULT["bank_response_code"] = $tableau[18];
		$RESULT["complementary_code"] = $tableau[19];
		
		$i=0;
		if( count($tableau) > 32 ) {
			$RESULT["complementary_info"] = $tableau[20];
			$i=1;
		}
			
		$RESULT["return_context"] 		= $tableau[20+$i];
		$RESULT["caddie"] 				= $tableau[21+$i];
		$RESULT["receipt_complement"] 	= $tableau[22+$i];
		$RESULT["merchant_language"] 	= $tableau[23+$i];
		$RESULT["language"] 			= $tableau[24+$i];
		$RESULT["customer_id"] 			= $tableau[25+$i];
		$RESULT["order_id"] 			= $tableau[26+$i];
		$RESULT["customer_email"] 		= $tableau[27+$i];
		$RESULT["customer_ip_address"] 	= $tableau[28+$i];
		$RESULT["capture_day"]			= $tableau[29+$i];
		$RESULT["capture_mode"] 		= $tableau[30+$i];
		$RESULT["data"] 				= $tableau[31+$i];
		
		return $RESULT;
	} 	
}

/* ------------------------------------------------------
*
*/

class AcreatSogenactifRequest extends AcreatSogenactif
{
	/* ---
	* Contructeur
	* */
	function AcreatSogenactifRequest()
	{
		parent::AcreatSogenactif();
		$this->params = array_merge(array(
			"pathfile"			=> $this->pathfile,
			"merchant_id" 		=> $this->merchant_id,
			"merchant_country" 	=> "fr",
			"currency_code"		=> 978,
			"payment_means"		=> "CB,2,VISA,2,MASTERCARD,2,AMEX,2"
		), $this->params);
	} 
	
	function call() 
	{
		return $this->request();
	}
}

/* ------------------------------------------------------
*
*/

class AcreatSogenactifResponse extends AcreatSogenactif
{
	/* ---
	* Contructeur
	* */
	function AcreatSogenactifResponse($INFOS = false)
	{
		if(!$INFOS) $INFOS = @$_POST["DATA"];
		parent::AcreatSogenactif();
		$this->params = array_merge(array(
			"pathfile"			=> $this->pathfile,
			"message"			=> $INFOS 
		), $this->params);
	} 
	
	function call() 
	{
		return $this->response();
	}
}
?>