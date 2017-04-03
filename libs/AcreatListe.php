<?
	uses("SQLBuilder");
	class AcreatListe
	{
		var $id;
		var $REQ;
		
		// Valeurs par défaut
		var $tri_default;		//nom de la colonne
		var $ordre_default;		// ASC - DESC
		
		// Valeurs en cours
		var $tri;
		var $ordre;
		var $page;
		
		// Nom des variables
		var $var_tri_name;
		var $var_ordre_name;
		var $var_page_name;
		
		// Liste des colonnes enregistrée
		var $COLONNES;
		
		// Objet de BDD lié
		var $DB;
		var $nb_page;
		var $nb_results;
		var $limit;
		
		/* --------------------------
		* AcreatListe
		* Constructeur de la classe
		*/
		function AcreatListe($id,&$REQ,$tri_default="",$ordre_default="ASC")
		{
			$this->id = $id;
			$this->REQ = &$REQ;
			
			$this->var_ordre_name = $id."-ordre";
			$this->var_tri_name = $id."-tri";
			$this->var_page_name = $id."-page";
			
			$this->tri_default = $tri_default;
			$this->ordre_default = $ordre_default;		
			
			$this->_initListe();
		}
		
		/* --------------------------
		* initListe
		* Initialise les informations de la liste
		*/
		function _initListe()
		{
			// Récupération des variable de GET
			$this->ordre = $this->_initValue($this->var_ordre_name, $this->ordre_default);
			$this->tri = $this->_initValue($this->var_tri_name, $this->tri_default);
			$this->page = $this->_initValue($this->var_page_name, 1);
			
			// Verification de la viabilité de l'order
			if(!array_search($this->ordre,array("","ASC","DESC")))
				$this->ordre = "";
			
			// Répercution sur l'objet de requête
			if(!empty($this->tri)) {
				if( method_exists ( $this->REQ, "addOrder" ) )
					$this->REQ->addOrder($this->tri, $this->ordre == "DESC");
				elseif( method_exists ( $this->REQ, "order" ) )
					$this->REQ->order($this->tri." ".$this->ordre);
			}
			
			// Mise en mémoire des infos
			$_SESSION["ACREAT.LISTES"][$this->var_ordre_name] = $this->ordre;
			$_SESSION["ACREAT.LISTES"][$this->var_tri_name] = $this->tri;
		}
		
		/* --------------------------
		* _initValue
		* Initialise les informations d'une valeur par défaut
		*/
		function _initValue($idInique, $default)
		{
			// Récupération des variable de GET
			if(isset($_GET[$idInique]))
				return $_GET[$idInique];
			elseif(isset($_SESSION["ACREAT.LISTES"][$idInique]))
				return $_SESSION["ACREAT.LISTES"][$idInique];
			else
				return $default;
		}


		/* --------------------------
		* _getNewLink
		* Récupère l'adresse de la page et ajoute les informations
		*/
		function _getNewLink($tri="",$ordre="",$page=0)
		{
			$chemin = get_clean_url($this->var_tri_name, $this->var_ordre_name, $this->var_page_name);
			if(!empty($tri))
				$chemin.="&".$this->var_tri_name."=".$tri;
			if(!empty($ordre))
				$chemin.="&".$this->var_ordre_name."=".$ordre;
			if(!empty($page))
				$chemin.="&".$this->var_page_name."=".$page;
			else
				$chemin.="&".$this->var_page_name."=".$this->page;
			return $chemin;
		}
		
		/* --------------------------
		* _registerHeader
		* Enregistre l'entête de colonne
		*/
		
		function _registerHeader($titreColonne,$varColonne,$AGGREGATION=false)
		{
			$pos=0;
			while(isset($this->COLONNES[strtolower(substr($titreColonne,$pos,1))]))
				$pos++;
			if($pos < strlen($titreColonne))
			{
				$char = substr($titreColonne,$pos,1);
				$this->COLONNES[strtolower($char)]["var"] = $varColonne;
				$this->COLONNES[strtolower($char)]["agr"] = $AGGREGATION;
				return preg_replace("/".$char."/","<u>$char</u>",$titreColonne,1);
			} 
			return  $titreColonne;
		}
		
		/* --------------------------
		* header
		* Génére une entête de liste avec lien pour le tri
		*/
		function header($titreColonne,$varColonne,$AGGREGATION=false,$registerColonne=false,$showOrder=true)
 		{
			// Aggregation = true si le nom de la variable commence par *
			if(substr($varColonne,0,1) == "*") { $varColonne = substr($varColonne,1); $AGGREGATION = true; }
			
			if($registerColonne)
				$titreColonne = $this->_registerHeader($titreColonne,$varColonne,$AGGREGATION);
			
			if(empty($this->tri))
				$this->tri = $varColonne;
			
			//$titreColonne = registerColHeader($titreColonne,$varColonne,$AGGREGATION,$LISTE_ID);
			if($this->tri == $varColonne) 
				$newOrdre = ($this->ordre == "ASC")?"DESC":"ASC";
			else 
				$newOrdre = $this->ordre;
	
			$link = "javascript:document.location.replace('".$this->_getNewLink($varColonne,$newOrdre)."')";
			
			$return = "<a href=\"".$link."\"";
			if($this->tri == $varColonne && $showOrder) 
				$return.= "class='liste-header-".$this->ordre."'";
			$return.=">".$titreColonne."</a>";
			
			return $return;
		}
		
		/* --------------------------
		* navigation
		* Retourne un panneau de navigation
		*/
		function navigation($nbPage=1, $currentPage=1, $nbAffiche=10, $backSign = "&#60;", $nextSign = "&#62;", $expandSign = "...")
 		{
			$currentPage = round($currentPage);
		 	$elems = array();
			
			$elems[] = "<li class='currentPage'>Page <span class='currentPage'>$currentPage</span> sur <span class='nbPage'>$nbPage</span></li>";
			
			// ---
			// Page précédente
			if($nbPage > 1 && !empty($backSign) && $currentPage > 1 ) {
				$elems[] = "<li class='previous'><a href=\"".get_clean_url(array($this->var_page_name=>$currentPage-1))."\">{$backSign}</a></li>";
			}
			
			$start = $currentPage - floor($nbAffiche/2);
			$stop = $start + $nbAffiche - 1;
			if($start < 1) { $stop += -$start+1; $start = 1; }
			if($stop > $nbPage ) { $start -= $stop - $nbPage; $stop = $nbPage; }
			if($start < 1) $start=1;
				
			if($start > 1) {
				$start++;
				$elems[] = "<li><a href=\"".get_clean_url(array($this->var_page_name=>1))."\">1</a></li>";
				$elems[] = "<li>{$expandSign}</li>";
			}
			
			if($nbPage > $stop) $stop--;
			
			for($i=$start; $i <= $stop; $i++) { 
				if( $i == $currentPage) {
					$elems[] = "<li class=\"active\"><a>$i</a></li>";
				} else {
					$elems[] = "<li><a href=\"".get_clean_url(array($this->var_page_name=>$i))."\">$i</a></li>";
				}
			}
			
			if($nbPage > $stop) {
				$elems[] = "<li>{$expandSign}</li>";
				$elems[] = "<li><a href=\"".get_clean_url(array($this->var_page_name=>$nbPage))."\">".$nbPage."</a></li>";
			}
			
			// ---
			// Page suivante
			if($nbPage > 1 && !empty($nextSign) && $currentPage < $nbPage) {
				$elems[] = "<li class='next'><a href=\"".get_clean_url(array($this->var_page_name=>$currentPage+1))."\">{$nextSign}</a></li>";
			}
			
			return "<ul>".implode("", $elems)."</ul>";
		}
		/* --------------------------*/
		


	}
?>