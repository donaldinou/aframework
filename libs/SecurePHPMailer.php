<?
vendor("PHPMailer" . DS . "class.phpmailer");
function has_newlines($text) { return preg_match("/(%0A|%0D|\n+|\r+)/i", $text); }
function has_emailheaders($text) { return preg_match("/(%0A|%0D|\n+|\r+|\b)(content-type:|to:|cc:|bcc:)/si", $text); }


class SecurePHPMailer extends PHPMailer
{
	/* ----------------------
	* Send
	*/
	function Send() {
		$this->check_injection();
		return parent::Send();
	}

	/* ----------------------
	* check_header_injection
	*/
	function check_injection() {
	
		if( has_newlines($this->From) || has_newlines($this->FromName) || has_newlines($this->Subject) || has_emailheaders($this->Body) || has_emailheaders($this->AltBody) ) {
			header("HTTP/1.0 403 Forbidden");
			/*$deny = '# ' . date("D M j G:i:s T Y") . "\n";
			$deny .= 'Deny from ' . $_SERVER['REMOTE_ADDR'] . "\n";
			@fwrite(fopen('.htaccess', 'a'),$deny);  */
			print "You've been detected trying to do stream injection and blocked from further access to this mail form.";  
			exit;
		}
		return true;
	}
}






?>