<?
class AcreatXMLConfig
{   
	var $file = false;
	var $datas = array();
	var $encoding = "ISO-8859-1";
	var $options_unserializer = array();
	var $options_serializer = array(
		"rootName" 	=> "configuration",
		"mode"		=> "simplexml"
	);
	
	/* ----------
	* AcreatXMLConfig
	*/
	function AcreatXMLConfig($xml_file, $options=null, $autoload=true) {
		$this->file = $xml_file;	
		if($options) {
			$this->options_unserializer = array_merge($this->options_unserializer, $options);
			$this->options_serializer = array_merge($this->options_serializer, $options);
		}
		if($autoload) $this->load();
	}
	
	/* ----------
	* load
	*/
	function load() {
		vendor("XMLSerializer/Unserializer");
		if(!file_exists($this->file)) return 0;
		$options = array_merge(
			array("encoding"=>$this->encoding), 
			$this->options_unserializer
		);
		$unserializer = &new XML_Unserializer($options);
		$unserializer->unserialize($this->file, true);
		$this->datas = $unserializer->getUnserializedData();
		
		return 1;
	}
	
	/* ----------
	* get
	*/
	function get($query, $forceArray=false) {
		$road = split("/", $query);
		$item = $this->datas;
		foreach($road as $step) {
			if(!isset($item[$step])) 
				return $forceArray ? array() : false;
			$item = $item[$step];
		}
		
		if(!is_array($item) && $forceArray)
			$item = array($item);
			
		return $item;
	}
	
	/* ----------
	* set
	*/
	function set($query, $value, $merge=false) {
		$road = split("/", $query);
		$item = &$this->datas;
		foreach($road as $step) {
			$parent = &$item;
			$item = &$parent[$step];
		}
		if(is_array($value) && is_array($item) && $merge)
			return $item = array_merge($item, $value);
		if(is_null($value)) 
			unset($parent[$step]);
		return $item = $value;
	}
	
	/* ----------
	* save
	*/
	function save() {
		//header("Content-Type:text/xml; charset=" . $this->encoding);
		vendor("XMLSerializer/Serializer");
		$serializer = new XML_Serializer($this->options_serializer);
		//$datas = array_map_recursive ("utf8_encode", $this->datas);
		if(!$serializer->serialize($this->datas)) 
			return false;
		$datas = $serializer->getSerializedData();
		$f = fopen($this->file, "w");
		if(!$f) return false;
		fwrite($f,"<?xml version='1.0' encoding='".$this->encoding."'?>\n" . $datas);
		fclose($f);
		return true;
	}
	
	/* ----------
	* toArray
	*/
	function toArray() {
		return $this->datas;
	}
	
	
	
}
?>