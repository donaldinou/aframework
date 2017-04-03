<?
/* ***
* execute_time
* Renvoi le temps d'execution
* en paramètre
*/
function execute_time() {
   return (microtime(true) - $_SESSION['start_time']);
}

/* ***
* GET_CLEAN_URL
* Renvoi l'url actuelle débarassées des arguments spécifiés
* en paramètre,  en version relative
*/
function get_clean_url() {
	$uri = $_SERVER["REQUEST_URI"];
	if(!$uri) 
		$uri = $_SERVER["PHP_SELF"].($_SERVER["QUERY_STRING"] ? "?".$_SERVER["QUERY_STRING"] : "");
	$parts = explode("?", $uri);
	$url = $parts[0];
	
	/*$vars = array();
	if($query_string = $parts[1]) {
		$items = explode("&", $query_string);
		foreach($items as $item) {
			$infos = explode("=", $item);
			$vars[$infos[0]] = $infos[1];
		}		
	}*/
	$vars = $_GET;
		
	$args = func_get_args();
	foreach( $args as $arg ) {
		if(is_array($arg)) {
			foreach($arg as $key=>$item) 
				$vars[$key] = $item;
		} else 
			unset($vars[$arg]);
	}
	
	$query = array();
	
	$query = array();
	_get_clean_url_generate_vars($query, $vars);
	
	return $url.(count($query) ? "?".implode("&",$query) : "");
}

// fonction récursive pour la constitution de l'url de get_clean_url
// @see get_clean_url
function _get_clean_url_generate_vars(&$query, $vars, $parent=false) {
	foreach($vars as $key=>$var) {
		if(is_array($var))
			_get_clean_url_generate_vars($query, $var, $parent ? $parent."[$key]" : "$key");
		else
			$query[] = ($parent ? $parent."[$key]" : $key) . "=$var";
	}
}

/* ***
* GET_CLEAN_ABSOLUTE_URL
* Renvoi l'url actuelle débarassées des arguments spécifiés
* en paramètre, en version absolue
*/
function get_clean_absolute_url() {
	$args = func_get_args();
	$chemin = explode("/",$_SERVER["PHP_SELF"]);
	array_pop ($chemin);
	$clean_url = call_user_func_array("get_clean_url", $args);
	
	if(!preg_match("/^\//si", $clean_url))
		$clean_url = implode($chemin, "/")."/".$clean_url;
	
	return "http://".$_SERVER["HTTP_HOST"].( $_SERVER["SERVER_PORT"] != 80 ? ":".$_SERVER["SERVER_PORT"] : "").$clean_url;
}

/* ***
* make_absolute_url
*/
function make_absolute_url($rel_uri, $base, $REMOVE_LEADING_DOTS = true) {
	if (preg_match("'^[^:]+://'", $rel_uri)) 
		return $rel_uri; 
	preg_match("'^([^:]+://[^/]+)/'", $base, $m);
	$base_start = $m[1];
	if (preg_match("'^/'", $rel_uri))
		return $base_start . $rel_uri;
	$base = preg_replace("{[^/]+$}", '', $base);
	$base .= $rel_uri;
	$base = preg_replace("{^[^:]+://[^/]+}", '', $base);
	$base_array = explode('/', $base);
	if (count($base_array) and!strlen($base_array[0]))	
		array_shift($base_array);
	$i = 1;
	while ($i < count($base_array)) {
		if ($base_array[$i - 1] == ".") {		
			array_splice($base_array, $i - 1, 1);		
			if ($i > 1) $i--;	
		} elseif ($base_array[$i] == ".." and $base_array[$i - 1]!= "..") {		
			array_splice($base_array, $i - 1, 2);		
			if ($i > 1) {	
				$i--;	
				if ($i == count($base_array)) 
					array_push($base_array, "");		
			}	
		} else {	
			$i++;
		}
	}
	if (count($base_array) and $base_array[-1] == ".")	
		$base_array[-1] = "";	
	
	if ($REMOVE_LEADING_DOTS) {
		while (count($base_array) and preg_match("/^\.\.?$/", $base_array[0])) 		
			array_shift($base_array);	
	}
	
	return($base_start . '/' . implode("/", $base_array));
}

/* ***
* FORMAT_MYSQL_DATE
* Formate une date MySQL
*/
function format_mysql_date($date, $format="%d/%m/%Y", $vide=null)
{
	//http://fr3.php.net/manual/en/function.strftime.php
	if(empty($date) || preg_match("/^0000-00-00/", $date)) return $vide;
	list($y, $m, $d, $h, $i, $s) = sscanf($date, "%d-%d-%d %d:%d:%d");
	$time = mktime($h,$i,$s,$m,$d,$y);
	return strftime($format, $time);
}

/* ***
* date_to_mysql
* Formate une date FR en date MySQL
*/
function date_to_mysql($date, $original_format="%d/%d/%d %d:%d:%d")
{
	if(empty($date)) return false;

	// JOUR MOIS ANNEE
	preg_match("/(?:(\d{1,2})\/)?(?:(\d{1,2})\/)?(\d{2,4})/", $date, $matches);
	$day = 1; $month = 1; $year = date("Y");
	if(empty($matches[1]) && empty($matches[2])) {
		$year = $matches[3];
	} elseif(empty($matches[2]) && strlen($matches[3]) == 2 && $matches[3]<13) {
		$day = $matches[1];
		$month = $matches[3];
	} elseif( empty($matches[2]) ) {
		$month = $matches[1];
		$year = $matches[3];
	} else {
		$day = $matches[1];
		$month = $matches[2];
		$year = $matches[3];
	}
	
	// HEURES MINUTES SECONDES
	preg_match("/(\d{1,2}):(\d{1,2})(?::(\d{1,3}))?/", $date, $matches);
	$hour 	= (int)$matches[1];
	$minute = (int)$matches[2];
	$seconds = @(int)$matches[3];
	
	$time = mktime ($hour, $minute, $seconds, $month, $day, $year);
	$date = date("Y-m-d H:i:s", $time);
	if( preg_match("/1970-01-01 00:00:00/",$date) )
		return false;
	
	return $date;
}

/**
 * Return the relative path between two paths / Retourne le chemin relatif entre 2 chemins
 *
 * If $path2 is empty, get the current directory (getcwd).
 * @return string
 */
function relativePath($path1, $path2='')
{
   if ($path2 == '') {
       $path2 = $path1;
       $path1 = getcwd();
   }

   //Remove starting, ending, and double / in paths
   $path1 = trim($path1,'/');
   $path2 = trim($path2,'/');
   while (substr_count($path1, '//')) $path1 = str_replace('//', '/', $path1);
   while (substr_count($path2, '//')) $path2 = str_replace('//', '/', $path2);

   //create arrays
   $arr1 = explode('/', $path1);
   if ($arr1 == array('')) $arr1 = array();
   $arr2 = explode('/', $path2);
   if ($arr2 == array('')) $arr2 = array();
   $size1 = count($arr1);
   $size2 = count($arr2);

   //now the hard part :-p
   $path='';
   for($i=0; $i<min($size1,$size2); $i++)
   {
       if ($arr1[$i] == $arr2[$i]) continue;
       else $path = '../'.$path.$arr2[$i].'/';
   }
   if ($size1 > $size2)
       for ($i = $size2; $i < $size1; $i++)
           $path = '../'.$path;
   else if ($size2 > $size1)
       for ($i = $size1; $i < $size2; $i++)
           $path .= $arr2[$i].'/';

   return $path;
}

/**
 * uses
 */
function uses ()
{
	$args = func_get_args();
	foreach ($args as $arg)
	{
		if(file_exists(APP.'libs'.DS.$arg.'.php'))
			require_once(APP.'libs'.DS.$arg.'.php');
		else
			require_once(LIBS.$arg.'.php');
    }
}


/**
 * vendor($name)
 */
function vendor($name)
{
    $args = func_get_args();
    foreach ($args as $arg)
    {
        if(file_exists(APP.'vendors'.DS.$arg.'.php'))
            require_once(APP.'vendors'.DS.$arg.'.php');
        else
            require_once(VENDORS.$arg.'.php');
    }
}

/**
* sentence_fragment
* Coupe une phrase au bout de $n caractère sans couper les mots
*/
function sentence_fragment($s,$n, $suite = "...", $coupe_mot = false) 
{
	$scan=0;
	while(!$coupe_mot && (substr($s,$n,1) != ' ' ) && (substr($s,$n,1) != ',' ) && $n < strlen($s)) 
		$n++;
	return substr($s,0,$n) . ($n < strlen($s) ? $suite : "");
}


/**
* number_format_fr
* Transforme un float/int au format 'US' en float format 'FR'
* Ex : 1500.21 => 1 500,21
*/
function number_format_fr( $num, $dec = null, $currency=false, $currency_pos=false, $empty_return=false) 
{
	if(empty($num) && ( ($num != 0  && $num != "0") || $empty_return ))
		return false;
	
	if($dec === null) 
		$dec = preg_match('/\.|,/',$num) ? 2 : 0;
		
	$nb = number_format( $num, $dec, ",", " ");
	
	if(!empty($currency)) {
		switch($currency_pos)
		{
			case false:
			case "RIGHT": $nb = $nb.$currency; break;
			case "LEFT": $nb = $currency.$nb; break;
		}
	}
	
	return $nb;
}

/**
* number_format_us
* Transforme un float/int au format 'FR' en float format 'US'
* Ex : 1 500,21 € => 1500.21
*/
function number_format_us($array) 
{
	if($array === NULL) 	return NULL;
	if($array === false) 	return false;
	
	$args = func_get_args();
	unset($args[0]);
	
	if( is_array($array) && count($args) > 0) {
		foreach($args as $arg) {
			if(isset($array[$arg]))
				$array[$arg] = number_format_us($array[$arg]); 
		}
	} elseif ( is_array($array) ) {
		foreach($array as $key=>$item) {
			$array[$key] = number_format_us($array[$key]); 
		}
	} else {
		$array = preg_replace("/,/si",".",$array);
		$array = preg_replace("/[^\d\.-]/si","",$array);
		return $array;
	}
}


/**
* heuretotime
* Transforme un string en represention heure/minute/seconde
*/
function heure_to_time($str) 
{
	if(!preg_match("/(\d*)(?:[:h](\d*))?(?:[:m](\d*))?/si", $str, $matches)) return $str;
	$heure = 	!empty($matches[1]) ? $matches[1] : "";
	$min = 		!empty($matches[2]) ? $matches[2] : "";
	$sec = 		!empty($matches[3]) ? $matches[3] : "";
	$str = str_pad($heure, 2, "0", STR_PAD_LEFT). ":" .str_pad($min, 2, "0", STR_PAD_LEFT). ":" .str_pad($sec, 2, "0", STR_PAD_LEFT);
	return $str;
}

/**
* array_map_keys
* Applique une fonction a un nombre limite de membre d'un tableau
*/
function array_map_keys( $func, $array ) 
{
	$args = func_get_args();
	if( $args <= 2 ) return array_map($func, $array);
	unset($args[0]);
	unset($args[1]);
	foreach( $args as $key ) {
		if(isset($array[$key])){
			$array[$key] = call_user_func($func, $array[$key]);
		}
	}
	return $array;
}

/**
* array_map_recursive
* array_map mais recursif
*/
function array_map_recursive( $func, $arr )
{
    $newArr = array();
    foreach( $arr as $key => $value ) {
        $newArr[ $key ] = ( is_array( $value ) ? array_map_recursive( $func, $value ) : $func( $value ) );
    }
    return $newArr;
}

/**
 * Sums the values of the arrays be there keys (PHP 4, PHP 5)
 * array array_sum_values ( array array1 [, array array2 [, array ...]] )
 */
function array_sum_values() {
    $return = array();
    $intArgs = func_num_args();
    $arrArgs = func_get_args();
    if($intArgs < 1) trigger_error('Warning: Wrong parameter count for array_sum_values()', E_USER_WARNING);
   
    foreach($arrArgs as $arrItem) {
        if(!is_array($arrItem)) trigger_error('Warning: Wrong parameter values for array_sum_values()', E_USER_WARNING);
        foreach($arrItem as $k => $v) {
			if( is_array($v) )
				@$return[$k] = array_sum_values($return[$k], $v);
			else
				@$return[$k] += $v;
        }
    }
    return $return;
}

/**
* ascii_encode
* Encode une phrase en ascii
*/
function ascii_encode($string, $mask="%d", $join=false)  {
	$encoded = array();
	for ($i=0; $i < strlen($string); $i++)  {
		$encoded[] = sprintf($mask, ord(substr($string,$i)));
	}
	if($join !== false)
		$encoded = join($join, $encoded);
	return $encoded;
}

/**
* encode_email
* Encode une adresse mail
*/
function encode_email($address, $text=true) 
{ 
	$address_encode = ''; 
	for ($x=0; $x < strlen($address); $x++) 
	{ 
		if(preg_match('!\w!',$address[$x])) { 
			$address_encode .= '%' . bin2hex($address[$x]); 
		} else { 
			$address_encode .= $address[$x]; 
		} 
	} 

	// REMOVE SPACES in $mailto variables value. 
	$mailto = "&#109;&#97;&#105;&#108;&#116;&#111;&#58;"; 

	if($text) {
		if(!is_string($text)) 
			$text = $address;
		$text_encode = ''; 
		for ($x=0; $x < strlen($text); $x++) { 
			$text_encode .= '&#x' . bin2hex($text[$x]).';'; 
		}
		return "<a href=".$mailto.$address_encode.">".$text_encode."</a>"; 
	}
	
	return $address_encode;
} 

/**
* empty_null
* Si une variable est vide, elle devient 
*/
function empty_null($var)  {
	if(empty($var))	return null;
	return $var;
}

/**
* sprintf_empty
* Format une chaine de la même manière que sprintf, mais renvoi une chaine vide si la valeur à formater est vide
*/
function sprintf_empty($format, $value)  {
	if(empty($value) || preg_match("/^0000-/si", $value)) return "";
	$param_arr = func_get_args();
	return call_user_func_array ( "sprintf", $param_arr );
}

/**
* no_accent
* Enlève tous les accents 
*/
function no_accent($chaine)
{
   $accent  ="ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿ";
   $noaccent="aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuyyby";
   return strtr(trim($chaine),$accent,$noaccent);
} 

/**
* strtourl
* transforme un string pour l'url
*/
function strtourl($str, $space="-") {
	$str = strtolower(no_accent($str));
	$str = preg_replace("/[^\w]+/si",$space,$str);
	return $str;
}

/**
* count_recursive
* Comptage récurssif du nombre de membre d'un tableau
*/
function count_recursive($array)
{
	if(!is_array($array)) return false;
    foreach ($array as $id => $_array) {
        if (is_array ($_array) ) 
			$count += count_recursive ($_array); 
		else 
			$count += 1;
    }
    return $count;
}

/**
* utf8_decode_euro
* Decode l'UTF8 + l'euro
*/
function utf8_decode_euro($value, $html=true) {
	return utf8_decode(trim(preg_replace("/â‚¬/si", $html ? "&euro;" : "€", $value)));
}

/**
* include_jquery_plugin
* inclus un ou plusieurs plugin jquery dans la page
*/
function include_jquery_plugin() {
	if(!defined("JQUERY_PLUGINS_PATH")) define("JQUERY_PLUGINS_PATH", "js/jquery_plugins/");
	$args = func_get_args();
	foreach( $args as $plugin )
		print "<script type=\"text/javascript\" language=\"javascript\" src=\"".JQUERY_PLUGINS_PATH."jquery.$plugin.js\"></script>";
}


?>