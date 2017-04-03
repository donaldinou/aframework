<?php
class PagesController extends AcreatController 
{	
	var $page = "home";
	/**
	* Displays a view
	*/
	function index()
	{
		if(isset($this->params["url"]["page"]))
			$this->page = basename($this->params["url"]["page"]);
			
		$this->set("PAGE", $this->page);
		
		$relative = preg_replace("/\./","/",$this->page);
		if( file_exists(VIEWS."/pages/".$relative.".thtml") )
			return $this->render($relative);
		
		$this->render($this->page);
	}
}

?>