<?
// Utilisation des helpers
uses(DS.'view'.DS.'AcreatHelper');

class _AcreatView
{
	var $controller			= null;

	var $ext 				= ".thtml";
    var $loaded 			= array();
    var $viewPath			= null;
	var $layout				= "default";
	var $pageTitle			= "Document sans titre";
	var $metaTags			= array();
    var $helpers 			= array('html');

	var $autoLayout    		= true;
    var $hasRendered		= false;

	/**
	 * Constructor
	 */
	function _AcreatView (&$controller)
	{
		if( $controller ) {
			$this->controller    	= &$controller;
	        $this->viewPath      	= &$controller->viewPath;
			$this->_viewVars     	= &$controller->_viewVars;
			$this->action        	= &$controller->action;
			$this->autoLayout    	= &$controller->autoLayout;
			$this->autoRender    	= &$controller->autoRender;
	        $this->helpers       	= &$controller->helpers;
			$this->layout        	= &$controller->layout;
			$this->name          	= &$controller->name;
			$this->pageTitle     	= &$controller->pageTitle;
			$this->metaTags     	= &$controller->metaTags;
			$this->params		 	= &$controller->params;
			$this->message		 	= &$controller->message;
		}
	}

	/**
	* Renders view for given action and layout. If $file is given, that is used
	* for a view filename (e.g. customFunkyView.thtml).
	*/
	function render($action=null, $layout=null, $file=null)
	{
		if ($this->hasRendered) return true;

		if (!$action)
			$action = $this->action;

		if ($layout === null && $this->autoLayout)
			$layout = $this->layout;

		if(!$file) $file = $this->_getViewFileName($action);

		if (!is_file($file))
			return $this->error("La vue est manquante ($action).");

		$data_for_render = array_merge($this->_viewVars, array(
			'PAGE_TITLE'=>$this->pageTitle,
			'META_TAGS'=>$this->metaTags
		));
		$out = $this->_render($file, $data_for_render, false);
		$layout = $this->layout;

		if ($out !== false) {
			print $layout ? $this->renderLayout($out) : $out;
			$this->hasRendered = true;
		}

		return true;

	}


	/**
	 * Renders a piece of PHP with provided parameters and returns HTML, XML, or any other string.
	 *
	 * This realizes the concept of Elements, (or "partial layouts")
	 * and the $params array is used to send data to be used in the
	 * Element.
	 *
	 * @param string $name Name of template file in the /app/views/elements/ folder
	 * @param array $params Array of data to be made available to the for rendered view (i.e. the Element)
	 * @return string Rendered output
	 */
	function renderElement($name, $params=array())
	{
		$name = preg_replace("/\//", DS, $name);
		$fn = ( preg_match("/^\//si", $name) ? VIEWS : ELEMENTS ).$name.$this->ext;
		if (!file_exists($fn))
			return "(Erreur : Element {$name} introuvable)";
		$params = array_merge_recursive($params, $this->loaded);
		return $this->_render($fn, array_merge($this->_viewVars, $params), true, false);
	}

	/* ---
	* renderController
	*/
	function renderController($ctrlName, $ctrlAction = false, $url_params = false, $layout = false)
	{
		if( empty($ctrlName) ) return;
	 	$ctrlClass = ucfirst($ctrlName).'Controller';
		if(!class_exists($ctrlClass)) return "(Erreur : Controlleur {$ctrlName} introuvable)";

		$controller = new $ctrlClass($this->controller->dispatcher);
		$controller->globalPath	= $this->controller->globalPath;
		$controller->params 	= $this->controller->params;
		if($ctrlAction)			$controller->action = $ctrlAction;
		if($url_params)
			$controller->params["url"] = array_merge($controller->params["url"], $url_params);
		$controller->_viewVars 	= $this->_viewVars;
		$controller->layout  	= $layout;

		$output = call_user_func_array(array(&$controller, $controller->action), null);
		ob_start();
		$controller->render();
		return ob_get_clean();
	}


	/**
	* Renders view for given action and layout. If $file is given, that is used
	* for a view filename (e.g. customFunkyView.thtml).
	*/
	function _getViewFileName($action)
	{
		$action = preg_replace("/\//", DS, $action);
		$relative_file_path = preg_match("/^\\".DS."/", $action) ? substr($action,1).$this->ext : $this->viewPath.DS.$action.$this->ext;

		$viewFileName = false;
		if(file_exists(VIEWS.$relative_file_path))
			$viewFileName = VIEWS.$relative_file_path;
		elseif(file_exists(LIBS.'view'.DS.'templates'.DS.$relative_file_path))
			$viewFileName = LIBS.'view'.DS.'templates'.DS.$relative_file_path;
		if(!$viewFileName)
			return false;

		$viewPath = explode(DS, $viewFileName);
		while($i = array_search('..', $viewPath)) {
			unset($viewPath[$i-1]);
			unset($viewPath[$i]);
		}

		return implode(DS, $viewPath);
	}

	/**
	*
	*/
	function _getLayoutFileName()
	{
		$layout = preg_replace("/\//", DS, $this->layout);
		$layoutFileName = LAYOUTS."$layout$this->ext";
        if(file_exists(LAYOUTS."$layout$this->ext"))
            $layoutFileName = LAYOUTS."$layout$this->ext";
        elseif(file_exists(LIBS.'view'.DS.'templates'.DS."layouts".DS."$layout.thtml"))
            $layoutFileName = LIBS.'view'.DS.'templates'.DS."layouts".DS."$layout.thtml";
        return $layoutFileName;
	}


	/**
	 * Renders and returns output for given view filename with its
	 * array of data.
	 */
	function _render($___viewFn, $___data_for_view, $___play_safe = true, $loadHelpers = true)
	{
		// Load des HELPERS
		if ($this->helpers != false && $loadHelpers === true)
        {
            $loadedHelpers =  array();
            $loadedHelpers = $this->_loadHelpers($loadedHelpers, $this->helpers);

            foreach(array_keys($loadedHelpers) as $helper)
            {
                $replace = strtolower(substr($helper, 0, 1));
                $camelBackedHelper = preg_replace('/\\w/', $replace, $helper, 1);

                ${$camelBackedHelper} =& $loadedHelpers[$helper];

                if(isset(${$camelBackedHelper}->helpers) && is_array(${$camelBackedHelper}->helpers))
                {
                    foreach(${$camelBackedHelper}->helpers as $subHelper)
                    {
                        ${$camelBackedHelper}->{$subHelper} =& $loadedHelpers[$subHelper];
                    }
                }
                $this->loaded[$camelBackedHelper] = (${$camelBackedHelper});
            }
        }

		extract($___data_for_view, EXTR_SKIP); # load all view variables
		/**
		* Local template variables.
		*/
		$params     = &$this->params;
		$page_title = $this->pageTitle;

		/**
		* Start caching output (eval outputs directly so we need to cache).
		*/
		ob_start();

		/**
		* Include the template.
		*/
		include($___viewFn);
		$out = ob_get_clean();

		return $out;
	}

   /**
	* Renders a layout. Returns output from _render(). Returns false on error.
	*/
	function renderLayout($content_for_layout)
	{
	  $layout_fn = $this->_getLayoutFileName();
	  $data_for_layout = array_merge($this->_viewVars, array('LAYOUT_TITLE'=>$this->pageTitle !== false? $this->pageTitle: Inflector::humanize($this->viewPath),'LAYOUT_CONTENT'=>$content_for_layout));

	  if (is_file($layout_fn))
	  {
		$data_for_layout = array_merge($data_for_layout,$this->loaded); # load all view variables)

		$old_layout = $this->layout;
		$out = $this->_render($layout_fn, $data_for_layout, true, false);

		if( $old_layout != $this->layout )
			$out = $this->renderLayout($out);

		return $out;
	  }
	  else
	  {
	  	 $this->error (sprintf("Le layout est introubale : %s", $this->layout));
		 return false;
	  }
	}

	/* ---
	* RECUPERATION D'UN DIV D'ERREUR
	* Si une message existe, affiche le div avec le message dedans
	*/
	function message($mask = "<div class='MESSAGE'>%s</div>")
	{
		if(!empty($this->message))
			return sprintf($mask, nl2br($this->message));
	}


	/**
	 * Displays an error page to the user. Uses layouts/error.html to render the page.
	 */
	function error ($message, $code="404") {
		if( $this->controller )
			return $this->controller->error($message, $code);
		else
			return trigger_error($message, E_USER_ERROR);
	}

	/**
	* Loads helpers, with their dependencies.
	*
	* @param array $loaded List of helpers that are already loaded.
	* @param array $helpers List of helpers to load.
	* @return array
	*/
    function &_loadHelpers(&$loaded, $helpers)
    {
        $helperTags = new AcreatHelper();
        $tags = $helperTags->loadConfig();
        foreach ($helpers as $helper)
        {
            $helperCn = $helper.'Helper';

            if(in_array($helper, array_keys($loaded)) !== true)
            {
                if(!class_exists($helperCn))
                {
                    $helperFn = strtolower($helper).'.php';
                    if(file_exists(HELPERS.$helperFn))
                    {
                        $helperFn = HELPERS.$helperFn;
                    }
                    else if(file_exists(LIBS.'view'.DS.'helpers'.DS.$helperFn))
                    {
                        $helperFn = LIBS.'view'.DS.'helpers'.DS.$helperFn;
                    }
                    if (is_file($helperFn))
                    {
                        require_once $helperFn;
                    }
                    else
                    {
						/*
						$this->autoRender = false;
						$error =& new AcreatController ($this->controller->dispatcher);
						$error->autoLayout = true;
                    	$error->base = $this->base;
						$error->render('errors/missing_helper_file');
						*/
						print "Il manque un fichier helper : " . $helper;
						exit;
                    }
                }

                $replace = strtolower(substr($helper, 0, 1));
                $camelBackedHelper = preg_replace('/\\w/', $replace, $helper, 1);

                if(class_exists($helperCn))
                {
                    ${$camelBackedHelper}                       = new $helperCn;
                    //${$camelBackedHelper}->base                 = $this->base;
                    //${$camelBackedHelper}->webroot              = $this->webroot;
                    //${$camelBackedHelper}->here                 = $this->here;
                    ${$camelBackedHelper}->params               = $this->params;
                    ${$camelBackedHelper}->action               = $this->action;
                    //${$camelBackedHelper}->data                 = $this->data;
                    ${$camelBackedHelper}->tags                 = $tags;

                    if(!empty($this->validationErrors))
                    {
                        ${$camelBackedHelper}->validationErrors = $this->validationErrors;
                    }
                    $loaded[$helper] =& ${$camelBackedHelper};
                    if (isset(${$camelBackedHelper}->helpers) && is_array(${$camelBackedHelper}->helpers))
                    {
                        $loaded =& $this->_loadHelpers($loaded, ${$camelBackedHelper}->helpers);
                    }
                }
                else
                {
					/*
					$this->autoRender = false;
					$error =& new AcreatController ($this->controller->dispatcher);
					$error->autoLayout = true;
                    $error->base = $this->base;
					$error->render('errors/missing_helper_class');
					*/
                 	print "Il manque un fichier helper : " . $helper;
					exit;
                }
            }
        }
        return $loaded;
    }

}

?>
