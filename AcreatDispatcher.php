<?
class AcreatDispatcher
{
	var $params;
	var $controller 	= "pages";
	/* -------------
	*
	*/
	function AcreatDispatcher()
	{
		// Variables GET cryptés
		if( isset($_GET["_data64"]) ) { $_GET = @array_merge(unserialize(base64_decode($_GET["_data64"])), $_GET); unset($_GET["_data64"]); }
		// Variables POST cryptés
		if( isset($_POST["_data64"]) ) { $_POST = @array_merge(unserialize(base64_decode($_POST["_data64"])), $_GET); unset($_POST["_data64"]); }
	}

	/* -------------
	* DISPATCH
	*/
	function dispatch($additionalParams = array())
	{
		if( isset($this->defaultController))
			$this->controller = $this->defaultController;

        $this->params = array_merge($this->parseParams(), $additionalParams);

		if(isset($this->params["url"]["_m"]) && !$_POST)
			$this->params["message"] = base64_decode($this->params["url"]["_m"]);

		if( empty($this->params["controller"] ))
			$this->params["controller"] = !empty($this->params["url"]["controller"]) ? $this->params["url"]["controller"] : $this->controller;

		$ctrlName = $this->params["controller"];
	 	$ctrlClass = ucfirst($ctrlName).'Controller';

		if(!class_exists($ctrlClass)) {
			 $missingController = true;
			 $this->error404 ("Le contrôleur est introuvable.");
			 return 0;
		}

		$controller = new $ctrlClass($this);
		$classMethods = get_class_methods($controller);
      	$classVars = get_object_vars($controller);

		// Si l'action est dans la liste, et qu'elle ne commence pas par _
		if(preg_match("/^_/",$controller->action) || (!in_array($controller->action, $classMethods) && !in_array(strtolower($controller->action), $classMethods))) {
          $missingAction = true;
		  $this->error404 ("L'action est actuellement introuvable.", $controller);
		  return 0;
      	}

     	return $this->_invoke($controller, $this->params );
	}


	/**
	 * Invokes given controller's render action if autoRender option is set. Otherwise the contents of the operation are returned as a string.
	 *
	 * @param object $controller
	 * @param array $params
	 * @return string
	 */
	function _invoke (&$controller, $params )
	{
	   $output = call_user_func_array(array($controller, $controller->action), empty($params['pass'])? array(): $params['pass']);
	   if ($controller->autoRender)
		   return $controller->render();
	   return $output;
	}


	/**
	* Returns array of GET and POST parameters. GET parameters are taken from given URL.
	*
	* @param string $from_url    URL to mine for parameter information.
	* @return array Parameters found in POST and GET.
	*/
	function parseParams()
	{
	   $params = array();

	   // add submitted form data
	   $params['form'] = (ini_get('magic_quotes_gpc') == 1)?$this->stripslashes_deep($_POST) : $_POST;

	   /*if (isset($_POST['data']))
		   $params['data'] = (ini_get('magic_quotes_gpc') == 1)?
		   $this->stripslashes_deep($_POST['data']) : $_POST['data'];*/

	   if (isset($_GET) && count($_GET))
	   {
		   $params['url'] = $this->urldecode_deep($_GET);
		   $params['url'] = (ini_get('magic_quotes_gpc') == 1)?
		   $this->stripslashes_deep($params['url']) : $params['url'];
	   }

	   foreach ($_FILES as $name => $data)
	   {
		   $params['form'][$name] = $data;
	   }

	   return $params;
	}

	/**
	 * Displays an error page (e.g. 404 Not found).
	 *
	 * @param int $code     Error code (e.g. 404)
	 * @param string $name     Name of the error message (e.g. Not found)
	 * @param string $message
	 * @return unknown
	 */
   function error ($controller = null, $code, $name, $message)
    {
		if($code) header(' ', true, $code);
		$view = new AcreatView ($controller);
		$view->_viewVars = array_merge((array)$view->_viewVars, array('code'=>$code, 'name'=>$name, 'message'=>$message));
        $view->pageTitle = $code.' '. $name;
		$view->layout = "default";
    	$view->render('/errors/error404');
		exit;
    }
	function error404 ($message, $controller = null) { $this->error($controller, '404', 'Erreur 404', $message); }

	/**
	* Recursively strips slashes from given array.
	*
	*/
	function stripslashes_deep($val)
	{
	  return (is_array($val)) ?
		array_map(array('AcreatDispatcher','stripslashes_deep'), $val) : stripslashes($val);
	}

	/**
	* Recursively performs urldecode on given array.
	*
	*/
	function urldecode_deep($val)
	{
	  return (is_array($val)) ?
		array_map(array('AcreatDispatcher','urldecode_deep'), $val) : urldecode($val);
	}
}

?>
