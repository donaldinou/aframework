<?
uses("model/db/AcreatDBSelect");
define( "ACREATLISTE_DEFAULT_LIMIT", 10 );

class AcreatListe
{
	var $model;
	var $id;
	
	// Valeurs en cours
	var $col	= false;
	var $tri	= "ASC";
	var $page	= 1;
	var $limit = ACREATLISTE_DEFAULT_LIMIT;
	var $count;
	var $nbpage;
	var $items	= array();
	
	var $order = null;
	
	// Nom des variables
	var $var_col_name;
	var $var_tri_name;
	var $var_page_name;
	
	var $headers 			= array();
	var $filter_cols 		= array();
	var $filter_cols_bridge = array();
	
	var $link_model = "javascript:document.location.replace('%s')";
	
	// Liste des colonnes enregistrée
	var $COLONNES;
	
	var $navigationOptions = array();
	
	
	/* --------------------------
	* AcreatListe
	* Constructeur de la classe
	*/
	function AcreatListe($model=null, $id=null, $col=false, $tri=false)
	{ 
		$this->id = $id;
		if(!$this->id) 
			$this->id = substr(md5($_REQUEST["controller"].$_REQUEST["action"]),0,10);
		
		if($model) {
			$this->model 			= $model;
			$this->filter_cols		= array_merge( $model->_filter_cols, $this->filter_cols );
			if( $this->model->_order && preg_match("/\A(?:[^\.]*\.)?([^\s.]*)\s?(ASC|DESC)?/si", $this->model->_order, $matches) ) {
				$this->col = $matches[1];
				$this->tri = @$matches[2];
			}
		}
		
		foreach( $this->filter_cols as $_col) {
			if(preg_match("/([^\.]+)\.(.*)/", $_col, $matches))
				$this->filter_cols_bridge[$matches[2]] = $matches[0];
		}

		$this->var_col_name 	= "C".$this->id;
		$this->var_tri_name 	= "T".$this->id;
		$this->var_page_name 	= "P".$this->id;
		
		if( $col ) $this->col = $col;
		if( $tri ) $this->tri = $tri;
	
		$this->_initListe();
		
		if( $this->col ) {		
			$col = isset($this->filter_cols_bridge[$this->col]) ? $this->filter_cols_bridge[$this->col] : $this->col;
			$this->order = trim($col . " " . $this->tri);
		}
		
		$this->nbpage = $this->limit ? ceil($this->count / $this->limit) : 0;
	}
	
	/* --------------------------
	* fetchPagedModel
	* Applique les condition de pagination sur un model
	*/
	function fetchRowSet($where=null) {
		$DATAS = $this->limit ? $this->model->fetchPage($where, $this->order, $this->limit, $this->page) : $this->model->fetchAll($where, $this->order);
		if(!$DATAS) return false;
		$this->initFromAcreatRS($DATAS);
		return $DATAS;
	}

	/* --------------------------
	* initFromRs
	* Initialise les données a partir d'un RecordSet AdoDB
	*/
	function initFromAcreatRS($rs)
	{
		$this->page 	= $rs->_page["absolutePage"];
		$this->count 	= $rs->_page["maxRecordCount"];
		$this->limit	= $rs->_page["rowsPerPage"];
		$this->nbpage 	= $this->limit ? ceil($this->count / $this->limit) : 0;
		$this->items	= $rs->toArray();
		return true;
	}
	
	/* --------------------------
	* initListe
	* Initialise les informations de la liste
	*/
	function _initListe()
	{
		// Récupération des variable de GET
		$this->col 			= $this->_initValue($this->var_col_name, $this->col);
		$this->tri 			= $this->_initValue($this->var_tri_name, $this->tri);
		$this->page 		= $this->_initValue($this->var_page_name, $this->page);
		
		// Verification de la viabilité de l'order
		if(!array_search($this->tri,array("ASC","DESC")))
			$this->tri = "ASC";
		
		// Mise en mémoire des infos
		$_SESSION["ACREAT.LISTES"][$this->id][$this->var_col_name] = $this->col;
		$_SESSION["ACREAT.LISTES"][$this->id][$this->var_tri_name] = $this->tri;
	}
	
	/* --------------------------
	* _initValue
	* Initialise les informations d'une valeur par défaut
	*/
	function _initValue($idInique, $default=false)
	{
		// Récupération des variable de GET
		if($_GET[$idInique])
			return $_GET[$idInique];
		elseif($_SESSION["ACREAT.LISTES"][$this->id][$idInique])
			return $_SESSION["ACREAT.LISTES"][$this->id][$idInique];
		else
			return $default;
	}


	/* --------------------------
	* _getNewLink
	* Récupère l'adresse de la page et ajoute les informations
	*/
	function _getNewLink($col="",$tri="",$page=0)
	{
		$params = array();
		if(!empty($col))
			$params[$this->var_col_name] = $col;
		if(!empty($tri))
			$params[$this->var_tri_name] = $tri;
		$params[$this->var_page_name] = !empty($page) ? $page : $this->page;
	
		$chemin = get_clean_url($this->var_col_name, $this->var_tri_name, $this->var_page_name, $params);			
		return $chemin;
	}
	
	
	/* --------------------------
	* header
	* Génére une entête de liste avec lien pour le tri
	*/
	function header($varColonne,$titreColonne=null,$showOrder=true)
	{
		if( $this->model && !$titreColonne && isset($this->model->_headers[$varColonne]))
			$titreColonne = $this->model->_headers[$varColonne];	
		
		$varSearch = isset($this->filter_cols_bridge[$varColonne]) ? $this->filter_cols_bridge[$varColonne] : $varColonne;
		if( ( $char = array_search($varSearch, $this->filter_cols) ) ) 
			$titreColonne = preg_replace("/(".$char.")/si","<u>\\1</u>",$titreColonne,1);
		
		if($titreColonne === null)
			$titreColonne = $varColonne;			
		
		if($this->col == $varColonne) 
			$newOrdre = ($this->tri == "ASC" || $this->tri == null )?"DESC":"ASC";
		else 
			$newOrdre = $this->tri;

		$link = sprintf($this->link_model, $this->_getNewLink($varColonne,$newOrdre));
		
		$return = "<a href=\"".$link."\"";
		if($this->col == $varColonne && $showOrder) 
			$return.= "class='liste-header-".$this->tri."'";
		$return.=">".$titreColonne."</a>";
		
		return $return;
	}
	
	function AbsolutePage() { return $this->page; }
	
	
	/* --------------------------
	* navigation
	* Retourne un panneau de navigation
	*/
	function navigation($options = array())
	{
		uses("AcreatPagination");
		$NAVIGATION = new AcreatPagination($this);
		foreach($this->navigationOptions as $key=>$value) { $NAVIGATION->$key = $value; }
		foreach($options as $key=>$value) { $NAVIGATION->$key = $value; }
		return $NAVIGATION->html();
	}
	/* --------------------------*/
}
?>