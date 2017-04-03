<?
define( "ACREATFORMULAIRE_ERROR_TEMPLATE", "<div class='form_error'><span>%s</span></div>" );

class AcreatFormulaire
{
	var $_id;
	var $_errors = array();
	var $_errorMask = ACREATFORMULAIRE_ERROR_TEMPLATE;
	
	var $_datas = array();
	
	/* --- 
	* Constructeur 
	*/
	function AcreatFormulaire($ID="formulaire", $elms = null) {
		$this->_id = $ID;
		$this->set($elms);
	}

	/** 
	__dataToProp
	*/
	function __dataToProp() {
		foreach( $this->_datas as $key=>$value)
			$this->$key = $value;
	}
	
    /** 
	__propToData
	*/
	function __propToData() {
	
		$vars = get_object_vars ( $this );
		foreach( $vars as $col => $value) {
			if(!preg_match("/^_/", $col) && get_parent_class($value) != "AcreatModel" )
				$this->_datas[$col] = $this->$col;
		}
	}

	/* --- 
	* toArray 
	*/
	function toArray() {
		$this->__propToData();
		return $this->_datas;
	}
	
	/* --- 
	* load_from_session 
	*/
	function load_from_session() {
		if(isset($_SESSION["AcreatFormulaire"][$this->_id]))
			return $this->setElems($_SESSION["AcreatFormulaire"][$this->_id]);
		else
			return false;
	}
		
	/* --- 
	* save_to_session 
	*/
	function save_to_session() {
		$_SESSION["AcreatFormulaire"][$this->_id] = $this->_datas;
	}
	
	/* --- 
	* save_to_session 
	*/
	function delete_from_session() {
		unset($_SESSION["AcreatFormulaire"][$this->_id]);
	}
	
	
				
	/* --- 
	* set
	*/
	function set($name=false, $value=null) {
		
		if(!$name) return false;
		// AcreatFormulaire
		if( strtolower(get_class($name)) == "acreatformulaire") {
			$name->__propToData();
			$name = $name->_datas;
		}
		// AcreatDBRow
		if( strtolower(get_class($name)) == 'acreatdbrow' ) { 
			$name->__propToData();
			$name = $name->toArray(); 
		}
		
		// Array
		if(is_array($name)) {
			$args = func_get_args();
			if(count($args) > 1) { 
				for($i=1; $i<count($args); $i++) { 
					$value = NULL;
					$item = $args[$i];
					if(is_array($item)) { $value = $item[1]; $item = $item[0]; }
					if(!isset($name[$item])) $name[$item] = $value; 
				} 
			}
			$this->_datas = array_merge($this->_datas,$name);
		} else  {
			// Normal
			$this->_datas[$name] = $value;
		}
		
		$this->__dataToProp();
		return true;
	}
	function setElems() { $args = func_get_args(); return call_user_func_array(array(&$this, "set"), $args); }
				
	/* --- 
	* unset
	*/
	function remove() {
		$args = func_get_args();
		foreach($args as $name) {
			unset($this->_datas[$name]);
			unset($this->$name);
		}
	}
	
	/* --- 
	* get
	*/
	function get($name) {
		if(!isset($this->_datas[$name]))
			return false;
		else
			return $this->_datas[$name]; 
	}
	
	/* --- 
	* isOk
	*/
	function isOk() {
		return (count($this->_errors) == 0);
	}
		
	/* --- 
	* check
	* Vérifie si un champ est valide
	*/
	function check($name, $message = "Le champ %s n'est pas valide", $check_regex=1) 
	{
		$erreur = false;
		$value = @$this->_datas[$name];
		switch($check_regex) {
			// ---
			default: $erreur = (!$value || empty($value) || @preg_match("/^0000-00-00/", $value) || !$check_regex); break;	
			case "mail": 	$check_regex = '/^([a-zA-Z0-9_\-])+(\.([a-zA-Z0-9_\-])+)*@((\[(((([0-1])?([0-9])?[0-9])|(2[0-4][0-9])|(2[0-5][0-5])))\.(((([0-1])?([0-9])?[0-9])|(2[0-4][0-9])|(2[0-5][0-5])))\.(((([0-1])?([0-9])?[0-9])|(2[0-4][0-9])|(2[0-5][0-5])))\.(((([0-1])?([0-9])?[0-9])|(2[0-4][0-9])|(2[0-5][0-5]))\]))|((([a-zA-Z0-9])+(([\-])+([a-zA-Z0-9])+)*\.)+([a-zA-Z])+(([\-])+([a-zA-Z0-9])+)*))$/'; break;
			case "url": 	$check_regex = '/(((https?|ftp):\/\/(w{3}\.)?)(?<!www)(\w+-?)*\.([a-z]{2,4}))/'; break;
			case "cp": 		$check_regex = '/\d{5}/'; break;
			case "int": 	$check_regex = '/\d+/'; break;
			// ---
		}
		
		if(preg_match('/^\/.*\//', $check_regex))
			$erreur = !preg_match($check_regex ,$value);
		
		if($erreur) {
			$this->set_error($name, $message);
			return false;
		}
		
		return true;
	}
	
	/* --- 
	* set_error
	*/
	function set_error($name, $message = "Le champ %s n'est pas valide") {
		$this->_errors[$name] = sprintf($message, $name);
	}
	function setError() { $args = func_get_args(); return call_user_method_array("set_error", $this, $args); }
	
	/* --- 
	* isError
	*/
	function is_error($name) {
		return isset($this->_errors[$name]);
	}
	
	/* ---
	* Récupération des valeurs
	*/	
	function value($var, $type = "html", $compare = "")
	{
		$value = !empty($this->_datas[$var]) ? $this->_datas[$var] : "";
		switch($type)
		{
			case "text": return $value; break;
			// ---
			case "html": return htmlentities($value); break;
			// ---
			case "select":	return ($value == $compare) ? " selected " : ""; break;
			// ---
			case "checkbox": case "radio": 
				if(!is_array($value)) { if($value == $compare) return " checked "; return false; } 
				foreach( $value as $item ) {
					if($item == $compare) return " checked ";
				} 
			break;
		}
		return;
	}
	
	
	/* ---
	* Récupération d'un objet item de formulaire
	*/	
	function item($var, $type = "text")
	{
		return new AcreatFormulaireItem(&$this, $var, $type);
	}
	
	/* ---
	* Affiche de l'erreur
	*/	
	function error($name, $mask=null)
	{
		if(empty($this->_errors[$name])) return false;
		$mask = (!empty($mask) ? preg_replace("/%mask/", $this->_errorMask, $mask): $this->_errorMask );
		$mask = preg_replace("/%name/", $name, $mask);
		$mask = preg_replace("/%msg/", $this->_errors[$name], $mask);
		return sprintf($mask, $this->_errors[$name]);
	}
	
	/* ---
	* Affiche une version serialisée des paramètres
	* On passe en paramètre les elements qui ne doivent pas apparaitres dans l'objet serialisé
	* <input name="_data64" value="<?=$FORMULAIRE->serialize()?>">
	*/	
	function serialize() { 
		$elems = $this->_datas;
		$removes = func_get_args();
		foreach($removes as $key) unset($elems[$key]);
		return base64_encode(serialize($elems));
	}
	
	function _serialize() { $args = func_get_args(); return call_user_func_array(array(&$this, "serialize"), $args); }
	
	
	
	/* ---
	* Fabrications d'item de formulaire
	* INPUT TYPE HIDDEN
	*/	
	function hidden($name, $moreAttr="")
	{
		$value = $this->value($name);
		$model = '<input type="hidden" class="inputHidden hidden" name="%s" id="%s" value="%s" %s>';
		return sprintf($model, $name, $name, $value, $moreAttr);
	}
	
	/* ---
	* Fabrications d'item de formulaire
	* INPUT TYPE TEXT
	*/	
	function text($name, $moreAttr="")
	{ 
		$value = $this->value($name);
		$model = '<input type="text" class="inputText" name="%s" id="%s" value="%s" %s>';
		return sprintf($model, $name, $name, $value, $moreAttr);
	}
	
	/* ---
	* Fabrications d'item de formulaire
	* INPUT TYPE PASSWORD
	*/	
	function password($name, $moreAttr="")
	{
		$value = $this->value($name);
		$model = '<input type="password" class="inputPassword" name="%s" id="%s" value="%s" %s>';
		return sprintf($model, $name, $name, $value, $moreAttr);
	}
	
	/* ---
	* Fabrications d'item de formulaire
	* INPUT TYPE TEXT (number)
	*/	
	function number($name, $precision=0, $moreAttr="")
	{
		$value = $this->get($name) != "" ? number_format($this->get($name), $precision, ',', ' ') : '';
		$model = '<input type="text" name="%s" id="%s" value="%s" %s class="number inputNumber">';
		return sprintf($model, $name, $name, $value, $moreAttr);
	}
	
	/* ---
	* Fabrications d'item de formulaire
	* FILE
	*/	
	function file($name, $moreAttr="")
	{
		$model = '<input type="file" name="%s" id="%s" class="file inputFile" %s>';
		return sprintf($model, $name, $name, $moreAttr );
	}
	
	/* ---
	* Fabrications d'item de formulaire
	* CHECKBOX
	*/	
	function checkbox($name, $value=1)
	{
		$name_noarray = preg_replace("/\[\]$/si","",$name);
		$actual_value = $this->get($name_noarray);
		$CHECKED = is_array($actual_value) ? (array_search($value, $actual_value) !== false) : ($value == $actual_value);
		$model = '<input type="checkbox" name="%s" id="%s" value="%s" class="checkbox inputCheckbox" %s>';
		return sprintf($model, $name, $name_noarray."_".$value, $value, ( $CHECKED ? " CHECKED " : "") );
	}

	/* ---
	* Fabrications d'item de formulaire
	* RADIO
	*/	
	function radio($name, $value)
	{
		$name_noarray = preg_replace("/\[\]$/si","",$name);
		$actual_value = $this->get($name_noarray);
		$CHECKED = is_array($actual_value) ? (array_search($value, $actual_value) !== false) : ($value == $actual_value);
		$model = '<input type="radio" name="%s" id="%s" value="%s" class="radio inputRadio" %s>';
		return sprintf($model, $name, $name_noarray."_".$value, $value, ( $CHECKED ? " CHECKED " : "") );
	}
	
	/* ---
	* Fabrications d'item de formulaire
	* SELECT
	*/	
	function select($name, $array=null, $blank1stItem=true, $moreAttr="")
	{
		$class = get_class($name);
		if(preg_match("/model$/si", $class)) {
			$args = func_get_args(); 
			return call_user_func_array(array(&$this, 'selectFromModel'), $args);
		}
		
		$selected = $this->get($name);
		$model = '<select class="select selectOne" name="%s" id="%s" %s>';
		if( $blank1stItem ) $model .= "<option></option>";
		foreach($array as $key=>$value)
			$model .= "<option value=\"$key\"".( $selected == $key ? " SELECTED" : "").">$value</option>";
		$model .= "</select>";
		return sprintf($model, $name, $name, $moreAttr);
	}
	
	/* ---
	* Fabrications d'item de formulaire
	* SELECT FROM MODEL
	*/	
	function selectFromModel($model, $blank1stItem=true, $name=null, $moreAttr="")
	{
		if(!$name) $name = $model->_primary;
		return $model->fetchMenu($name, $this->get($name),$blank1stItem, $multiple_select=false, "id='".$name."' ".$moreAttr);
	}
	
	/* ---
	* Fabrications d'item de formulaire
	* TEXTAREA
	*/	
	function textarea($name, $rows=3, $moreAttr="")
	{
		$value = $this->value($name);
		$model = '<textarea class="textarea" name="%s" id="%s" rows="%d" %s>%s</textarea>';
		return sprintf($model, $name, $name, $rows, $moreAttr, $value);
	}
	
	/* ---
	* Fabrications d'item de formulaire
	* DATE
	*/	
	function date($name,$mask="%d/%m/%Y", $options=NULL, $moreAttr="")
	{
		global $CALENDAR_LOADED;
		vendor("DHTMLCalendar/calendar");
		ob_start();
		$CALENDRIER = new DHTMLCalendar();
		$CALENDRIER->include_require();
		$CALENDRIER->make_simple_input($name,$this->get($name), $mask, 24, $options, $moreAttr);
		return ob_get_clean();
	}
	
	/* ---
	* Fabrications d'item de formulaire
	* DATETIME
	*/	
	function datetime($name,$mask="%d/%m/%Y %H:%M:%S", $options=false) { 
		$optionCalendar = array("showsTime"=>"true");
		if( $options ) $optionCalendar = array_merge(array("showsTime"=>"true"), $optionCalendar);
		return $this->date($name,$mask, $optionCalendar);
	}
}

class AcreatFormulaireItem
{
	var $var;
	var $type;
	var $formulaire;
	
	/* ---
	* AcreatFormulaireItem
	*/
	function AcreatFormulaireItem($formulaire, $var, $type = "text") {
		$this->var = $var;
		$this->type = $type;
		$this->formulaire = $formulaire;
	}
	
	/* ---
	* Récupération de la valeur
	*/	
	function value($compare = "") {
		return $this->formulaire->value($this->var, $this->type, $compare);
	}
	
}
?>