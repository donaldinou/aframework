<?
class _AcreatController
{
	var $dispatcher		= null;
    var $action 		= "index";
	var $model			= null;
	var $name 			= null;
	var $params			= array();
	var $layout 		= 'default';
    var $autoLayout 	= true;
	var $autoRender 	= true;
    var $viewPath;
	var $_viewVars		= array();
	var $_viewClass		= null;
	var $message		= null;
	var $pageTitle		= "Document sans titre";
	var $metaTags		= array();
	var $db				= false;
    var $helpers 		= array();

	function _AcreatController (&$dispatcher)
    {
		// rattachement au dispatcher
		if( $dispatcher ) {
			$this->dispatcher = &$dispatcher;
			$this->params = $dispatcher->params;
		}

		// chargements des paramètres éventuels
		$this->message = !empty($this->params["message"]) ? $this->params["message"] : $this->message;
		if( empty($this->params["action"]) && !empty($this->params["url"]["action"]))
			$this->params["action"] = $this->params["url"]["action"];
		if( !empty($this->params['action']) )
			$this->action  = $this->params['action'];

		// chargement du nom et des chemins d'inclusion
		if(!$this->name) {
            $r = null;
            if (!preg_match('/(.*)Controller/i', get_class($this), $r))
           		die("AcreatController::__construct() : Impossible de parser mon propre nom.");
          	$this->name = ucfirst($r[1]);
        }

		if(!$this->viewPath)
				$this->viewPath = strtolower($this->name);

		// Base de donnée
		if( isset( $GLOBALS["DB"] ) )
			$this->db = $GLOBALS["DB"];

		// --- INCLUSION DU MODEL ---
		if( !empty($this->model) && $this->db) {
			$modelClass = ucfirst($this->model)."Model";
			if( !class_exists($modelClass))
				die("AcreatController::__construct() : Impossible de trouver le modele : $modelClass");

			$model =  new $modelClass($this);
			eval("\$this->".$model->_name." = \$model;");
		}

	}

	/**
	 * Affiche un template
	 */
	function render($action=null, $layout=null, $file=null)
	{
		$this->_viewClass = new AcreatView($this);
		$this->autoRender = false;
		return  $this->_viewClass->render($action, $layout, $file);
	}

	/* -------------
	* renderView
	*/
	function renderView($action=null, $layout=null, $file=null, $params=array()) {
		// comptabilité ancienne version de la méthode
		if(is_array($layout)) $params = $layout;
		$old_layout = $this->layout;
		$old_autorender = $this->autoRender;
		$this->layout = false;
		$old_viewVars = $this->_viewVars;
		$this->_viewVars = array_merge($this->_viewVars, $params);
		ob_start();
		$this->render($action,$layout,$file);
		$this->layout = $old_layout;
		$this->_viewVars = $old_viewVars;
		$this->autoRender = $old_autorender;
		return ob_get_clean();
	}

	/**
	 * Redirige vers une autre page
	 */
	function redirect($url = "", $msg = "")
	{
		$this->autoRender = false;
        if (function_exists('session_write_close'))
            session_write_close();
		$op = preg_match("/\\?/",$url) ? "&" : "?";
        header ('Location: '.$url.(!empty($msg) ? $op."_m=".base64_encode($msg) : "")  );
		exit;
	}

	/**
	 * Saves a variable to use inside a template.
	 */
	function set($one, $two=null)
    {
        return $this->_setArray(is_array($one)? $one: array($one=>$two));
    }


	/**
	 * Sets data for this view. Will set title if the key "title" is in given $data array.
	 */
	function _setArray($data)
	{
		foreach ($data as $name => $value)
		{
			if ($name == 'title')
			$this->_setTitle($value);
			else
			$this->_viewVars[$name] = $value;
		}
	}

	/**
	* Set the title element of the page.
	*/
 	function _setTitle($pageTitle)
    {
        $this->pageTitle = $pageTitle;
    }


	/**
	* Shows a message to the user $time seconds, then redirects to $url
	* Uses flash.thtml as a layout for the messages
	*
	* @param string $message Message to display to the user
	* @param string $url Relative URL to redirect to after the time expires
	* @param int $time Time to show the message
	*/
	function flash($message, $url, $pause = 1)
	{
		$this->autoRender = false;
		$this->autoLayout = false;

		$this->set('url', $url);
		$this->set('message', $message);
		$this->set('pause', $pause);
		$this->set('page_title', $message);

		if(file_exists(VIEWS.'layouts'.DS.'flash.thtml'))
			$flash = VIEWS.'layouts'.DS.'flash.thtml';
		elseif(file_exists(LIBS.'view'.DS.'templates'.DS."layouts".DS.'flash.thtml'))
			$flash = LIBS.'view'.DS.'templates'.DS."layouts".DS.'flash.thtml';

		$this->render(null, false, $flash);
		exit;
	}

	/**
	*  Initialise une valeur d'action
	*/
	function setAction ($action)
    {
        $this->action = $action;
        $args = func_get_args();
        call_user_func_array(array(&$this, $action), $args);
    }

	/**
	* erreur
	*/
	function error ($message="La page demandée n'existe pas", $code="404", $name="Erreur *")
	{
		$this->autoRender = false;
		$name = preg_replace("/\\*/", $code, $name);
		return $this->dispatcher->error($this, $code, $name, $message);
	}

	/* ---
	* Récupère la valeur base 64 du tableau GET ou autre
	*/
	function base64($array = NULL)
	{
		if($array == NULL) $array = $_GET;
		return 	preg_replace("/=*$/", "", base64_encode(serialize($array)));
	}
}

?>
