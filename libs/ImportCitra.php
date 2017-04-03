<?
error_reporting(E_ALL);

class ImportCitra
{
	var $error = false;
	var $debug = false;
	var $temp_dir = "temp/";

	var $LOG = array();
	
	var $m_diff = 0;
	var $m_start = 0;
	var $m_stop = 0;
	
	// ---
	function ImportCitra($temp_dir = "temp/")
	{
		$this->temp_dir = $temp_dir;
	}
	// ---
	function _error($msg)
	{
		$this->error = $msg;
		return false;
	}
	// ---
	function go()
	{
		$this->traitement_temp_files();
	}
	// ---
    function time_start() 
    { 
        $parts = explode(" ",microtime()); 
        $this->m_diff  = 0; 
        $this->m_start = $parts[1].substr($parts[0],1); 
    } 
	// ---
    function time_stop() 
    { 
        $parts  = explode(" ",microtime()); 
        $m_stop = $parts[1].substr($parts[0],1); 
        $this->m_diff  = ($m_stop - $this->m_start); 
        $this->m_start = 0; 
    } 
	// ---
    function duration($decimals=4) 
    { 
        return number_format($this->m_diff,$decimals); 
    } 
	
	/* -------------------------
	* TRAITEMENT DES FICHIERS
	*/
	function traitement_temp_files()
	{
		$this->time_start();
		
		$compt = 0;
		foreach( glob($this->temp_dir . "*.*") as $item)
		{
			$file_extension = strtolower(substr(strrchr($item,"."),1));
			$class = "ImportCitraFichier_" . strtoupper($file_extension);
			if(class_exists($class) && file_exists($item))
			{
				$traitement = new $class($item, &$this);
				$this->LOG[] = $traitement->LOG;
				$compt++;
			}
		}
		
		$this->time_stop();
		$this->LOG[] = "Traitement des fichiers (".$compt." fichiers - ".$this->duration()." secondes)";
	}
}



class ImportCitraFichier
{
	var $origine = false;
	var $agence = false;
	var $error = false;
	var $LOG;
	var $OPERATION = "";
	var $reference = "";
	var $saved;
	var $fichier;
	var $extension;
	var $TRAITEMENT;
	// ---
	function ImportCitraFichier($fichier, &$traitement)
	{
		$this->fichier = $fichier;
		$this->extension = strtolower(substr(strrchr($fichier,"."),1));
		$this->TRAITEMENT = &$traitement;
	}
	// ---
	function _erreur($code, $log=true)
	{
		$this->error = $code;
		if($log) $this->_log();
		return false;
	}
	// ---
	function write_file_log($msg)
	{
		$filename = TMP . "citra-import-log.txt";
		if(!$handle = fopen($filename, 'a')) return;
		fwrite($handle,date("d/m/Y - H:m:i") . chr(9) . $msg . "\n");
		fclose($handle);
	}
	// ---
	function _log()
	{
		$this->LOG .= "<div style='color:".( empty($this->error) ? "green" : "red") ."'>";
		$this->LOG .= "Référence : <b>" . $this->reference . "</b> <br> " . $this->OPERATION . "<BR>";
		$this->LOG .= "<B>".(empty($this->error) ? "OK" : "ERREUR (".$this->error.")")."</B>";
		$this->LOG .= "</div>";
		
		if(!empty($this->error))
			$this->write_file_log("ref ".$this->reference.chr(9).$this->OPERATION.chr(9).$this->error);
	}
	// ---
	function _nettoyage()
	{
		if($this->TRAITEMENT->debug) return;
		if(file_exists($this->fichier)) {
			if(empty($this->error))
				rename($this->fichier, $this->TRAITEMENT->temp_dir ."_archive/". date("YmdHis") . "." . basename($this->fichier) . ".old");
			else
				rename($this->fichier, $this->fichier.".error");
		}
	}
}

// ---
global $CHAUFFAGE;
$CHAUFFAGE = array(
	21 => "Collectif",
	22 => "Individuel",
	23 => "Central",
	24 => "Oui sans précision",
	25 => "Pompe a chaleur",
	26 => "Climatisation",
	// ---
	27 => "Radiateur",
	28 => "Au sol",
	29 => "Air pulsé",
	30 => "Convecteurs",
	// ---
	31 => "Gaz",
	32 => "Electrique",
	33 => "Fuel",
	34 => "Autres"
);
// ---
global $ETAT_GENERAL;
$ETAT_GENERAL = array (
	68 => "Habitable",
	69 => "Travaux à prévoir",
	70 => "Très bon état"
);

// ---
global $EXPOSITION;
$EXPOSITION = array (
	7 	=> "Est",
	8 	=> "Nord",
	9 	=> "Nord-est",
	10 	=> "Nord-ouest",
	11 	=> "Ouest",
	12 	=> "Sud",
	13 	=> "Sud-ouest",
	14 	=> "Sud-est"
);
/* -------------------- */
/* Traitement des XML
*/
class ImportCitraFichier_XML extends ImportCitraFichier
{
	/* CONSTRUCTEUR */
	function ImportCitraFichier_XML($fichier, &$traitement)
	{
		vendor("XMLSerializer/Unserializer");
				
		parent::ImportCitraFichier($fichier, &$traitement);
		$content = file_get_contents($this->fichier);
		$options = array(
			'parseAttributes' => true, 
			'attributesArray' => '_params',  
			'forceEnum'       => array("BIEN", "IMG", "BIEN_SUPP"),
			'encoding'		  => 'ISO-8859-1'
		);
		$unserializer = &new XML_Unserializer($options);
		$unserializer->unserialize($content);
		$rss = $unserializer->getUnserializedData();
		
		$this->origine = $rss["_params"]["origine"];
		
		if(isset($rss["BIEN"]) && is_array($rss["BIEN"])) 
		{
			foreach($rss["BIEN"] as $bien)
			{
				$this->error = false;
				$error = $this->_traiter_bien($bien);
				if(!empty($error)) $this->error = $error;
				$this->_log();
			}
		}
		
		/* ---------------------- RAJOUT SUITE MODIFICATION CITRE 08/2006 --------------------- */
		if(isset($rss["SUPPRESSION"]["BIEN_SUPP"]) && is_array($rss["SUPPRESSION"]["BIEN_SUPP"]) ) {
			foreach($rss["SUPPRESSION"]["BIEN_SUPP"] as $INFOS) {
				$this->error = false;
				$INFOS["ACTION"] = "DELETE";
				$this->reference = $INFOS["AFF_NUM"];
				if(class_exists("ImportCitraSpecifique_XML")) {
					$go = new ImportCitraSpecifique_XML(&$this, $INFOS);
					if(!empty($go->error)) $this->error = $go->error;
				}
				$this->_log();
			}
		}
		/* ---------------------- RAJOUT SUITE MODIFICATION CITRE 08/2006 --------------------- */
		
		$this->_nettoyage();
	}

	/*
	* _prepare_bien()
	*/
	function _prepare_bien($bien)
	{
		$INFOS = array();
		$INFOS = array_merge( $INFOS, $bien["INFO_GENERALES"] );
		
		if( isset($bien["_params"]) && strtolower($bien["_params"]["action"]) == "s" )
			$INFOS["ACTION"] = "DELETE";
		else
			$INFOS["ACTION"] = "INSERT/UPDATE";
		
		if(isset($bien["VENTE"]))
			$INFOS = array_merge( $INFOS, array("TRANSACTION" => "VENTE" ), $bien["VENTE"] );
		elseif(isset($bien["LOCATION"]))
			$INFOS = array_merge( $INFOS, array("TRANSACTION" => "LOCATION" ), $bien["LOCATION"] );
		elseif( $INFOS["ACTION"] != "DELETE" )
			return "IL MANQUE LE TYPE DE TRANSACTION (VENTE, LOCATION)";
		
		if(isset($bien["APPARTEMENT"]))
			$INFOS = array_merge( $INFOS, array("TYPE_BIEN" => "APPARTEMENT" ), $bien["APPARTEMENT"] );
		elseif(isset($bien["MAISON"]))
			$INFOS = array_merge( $INFOS, array("TYPE_BIEN" => "MAISON" ), $bien["MAISON"] );
		elseif(isset($bien["PARKING"]))
			$INFOS = array_merge( $INFOS, array("TYPE_BIEN" => "PARKING" ), $bien["PARKING"] );
		elseif( $INFOS["ACTION"] != "DELETE" )
			return "IL MANQUE LE TYPE DE BIEN (APPARTEMENT, MAISON, PARKING)";	
		
		$INFOS = array_merge( $INFOS, $bien["LOCALISATION"] );
		$INFOS = array_merge( $INFOS, array("COMMENTAIRES" => $bien["COMMENTAIRES"]["FR"] ) );
		
		$INFOS["IMAGES"] = array();
		if( isset($bien["IMAGES"]) && is_array($bien["IMAGES"]) ) {
			foreach($bien["IMAGES"]["IMG"] as $image)
				$INFOS["IMAGES"][$image["_params"]["num"]] = $image["_params"]["nom"];
		}
		
		return $INFOS;
	}
	
	/*
	* _traiter_biens_infos()
	*/
	function _traiter_bien($bien)
	{	
		if(!$this->agence)
			$this->agence = $bien["INFO_GENERALES"]["ADH_NUM"];
		$this->reference = $bien["INFO_GENERALES"]["AFF_NUM"];
		$bien = $this->_prepare_bien($bien);
		if(! is_array($bien))
			return $bien;	
				
		if(class_exists("ImportCitraSpecifique_XML")) {
			$go = new ImportCitraSpecifique_XML(&$this, $bien);
			return $go->error;
		} 
		
		return "Classe spécifique 'ImportCitraSpecifique_XML' non trouvée";
	}
	
}


class ImportCitraSpecifique
{
	var $traitement;
	var $error;
	
	function ImportCitraSpecifique(&$traitement)
	{
		$this->traitement 	= &$traitement;
		$this->error 		= "";
	}
}
?>