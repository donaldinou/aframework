<?
class AcreatPagination {
	
	var $nbPage = 1;
	var $currentPage = 1;
	var $nbMaxItem = 10;
	var $varGetName = "page";
	var $currentPageTxt = "Page <span class='currentPage'>%d</span> sur <span class='nbPage'>%d</span>";
	var $backSign = "&#60;";
	var $nextSign = "&#62;";
	var $expandSign = "...";
	
	
	var $isFirst = false;
	var $isLast = false;
	
	/* ---------------------------
	* CONSTRUCTEUR 
	*/
	function AcreatPagination($datas) {
		$class = strtolower(get_class($datas));
		switch($class) {
			// ---
			case "acreatliste":
				$this->AcreatListe = $datas;
				$this->currentPage = round($this->AcreatListe->page);
				$this->nbPage = ceil($this->AcreatListe->count / $this->AcreatListe->limit);
				$this->varGetName = $this->AcreatListe->var_page_name;
			break;
			// ---
			case "adorecordset_mysql":
				$this->currentPage = $datas->AbsolutePage();
				$this->nbPage = $datas->LastPageNo();
			break;				
		}
	}
	
	function getLink($page) {
		$link = get_clean_url(array($this->varGetName=>$page));
		if( $this->link_model ) 
			return sprintf($this->link_model, $link);
		return $link;
	}
	
	function isFirst() { return ($this->currentPage == 1); }
	function isLast() { return ($this->currentPage == $this->nbPage); }
		
	/* ---------------------------
	*  HTML
	*/
	function html() 
	{
		$elems = array();
		if($this->currentPageTxt)
			$elems[] = "<li class='currentPage'>".sprintf($this->currentPageTxt, $this->currentPage, $this->nbPage)."</li>";
		
		// ---
		// Page précédente
		if($this->nbPage > 1 && !empty($this->backSign) && !$this->isFirst() )
			$elems[] = "<li class='previous'><a href=\"".$this->getLink($this->currentPage-1)."\">".$this->backSign."</a></li>";
		
		$start = $this->currentPage - floor($this->nbMaxItem/2);
		$stop = $start + $this->nbMaxItem - 1;
		if($start < 1) { $stop += -$start+1; $start = 1; }
		if($stop > $this->nbPage ) { $start -= $stop - $this->nbPage; $stop = $this->nbPage; }
		if($start < 1) $start=1;
			
		if($start > 1) {
			$start++;
			$elems[] = "<li><a href=\"".$this->getLink(1)."\">1</a></li>";
			$elems[] = "<li class='expand'>".$this->expandSign."</li>";
		}
	
		if($this->nbPage > $stop) $stop--;
		
		for($i=$start; $i <= $stop; $i++) { 
			if( $i == $this->currentPage) {
				$elems[] = "<li class=\"active\"><a>$i</a></li>";
			} else {
				$elems[] = "<li><a href=\"".$this->getLink($i)."\">$i</a></li>";
			}
		}
			
		if($this->nbPage > $stop) {
			$elems[] = "<li class='expand'>".$this->expandSign."</li>";
			$elems[] = "<li><a href=\"".$this->getLink($this->nbPage)."\">".$this->nbPage."</a></li>";
		}
	
		// ---
		// Page suivante
		if($this->nbPage > 1 && !empty($this->nextSign) && !$this->isLast()) {
			$elems[] = "<li class='next'><a href=\"".$this->getLink($this->currentPage+1)."\">".$this->nextSign."</a></li>";
		}
		
		return "<ul>".implode("", $elems)."</ul>";
	}
	
	
	
	
}
?>