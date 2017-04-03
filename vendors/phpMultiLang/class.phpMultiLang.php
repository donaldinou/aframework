<?php
/**
*     class phpMultiLang
*     @version 2.0.2
*     @package  phpMultiLang
*     @author   Konstantin S. Budylov <nimous@nightworks.ru>
*/

/**
* Class for maintenance of opportunities multi-language support.
*
*
* The class is intended for the organization of multilanguage support in web application.
* Provides all basic necessary functionality for reception and a conclusion of the language data,
* and some additional opportunities:
*
* - As sources of the language data you can use of files and-or arrays.
* - In case of use of files - processing of their any amount , and also caching results of processing is supported.
* - Correct processing of files with magic_quotes_runtime == TRUE.
* - Probably dynamic (i.e. after data processing) addition of the new language data from arrays.
* - Probably dynamic change of already processed data.
* - In the language data any HTML-marking is allowable.
* - Probably dynamic formatting of the language data with use sprintf() modifiers.
*
*     @package     phpMultiLang
*     @version  2.0
*     @author Konstantin S. Budylov <nimous@nightworks.ru>
*     @copyright Konstantin S. Budylov {@link http://nightworks.ru}
*     @license http://opensource.org/licenses/gpl-license.php GNU Public License.
*
*/

function check_file_expire($fname="",$expire=0) {return (file_exists($fname))?((time()<(filemtime($fname)+$expire))?true:false):false;}

class phpMultiLang
{
    /**
    *    @access private
    */
    var $_LangRoot;

    /**
    *    @access private
    */
    var $_LangCacheEnabled;

    /**
    *    @access private
    */
    var $_LangCachePath;

    /**
    *    @access private
    */
    var $_LangCacheExpire;

    /**
    *    @access private
    */
    var $_LangIdx  = array();

    /**
    *    @access private
    */
    var $_LangCurrent=null;

    /**
    *    @access private
    */
    var $_LangLocale =null;

    /**
    *    @access private
    */
    var $__LANGDATA=array();

    /**
    *    @access private
    */
    var $_flag_magic_quotes_runtime=false;

    /**
    *    @access private
    */
    var $_LangStringRegex = "/>([0-9a-z_\.]+)[\s\t]*\"(.+?)(?<![\\\\])\"/is";

    /**
    *    @access private
    */
    var $_LangFormatRegex = "/%[0-9]*[\.]*[0-9]*[a-z]/i";

    /**
    *    @access private
    */
    var $args_format=array();

    /**
    *    @access private
    */
    var $num_format=0;


   /**
   *     Here we can set the language root directory, and the directory for cache:
   *
   *     @access public
   *
   *     @param string $lang_root    language root directory (CWD by default)
   *     @param string $cache_path   directory for cache files. Required, if the caching will be used. CWD by default.
   *     @return void
   */
    function phpMultiLang( $lang_root=null, $cache_path=null )
    {
        $this->_flag_magic_quotes_runtime = ini_get("magic_quotes_runtime");
        $cwd = getcwd();
        if(!is_null($lang_root)){
            if(is_dir($path = realpath($lang_root)) && is_readable($path)){
                $this->_LangRoot = $path;
            } else {
                $this->Error("Invalid language root. The directory '<i>".$lang_root."</i>' not exists, or not readable. Language root switched to CWD.");
                $this->_LangRoot = $cwd;
            }
        } else {
            $this->_LangRoot = $cwd;
        }
        if(!is_null($cache_path)){
            if(is_dir($path = realpath($cache_path)) && is_readable($path)){
                if(is_writeable($path)){
                    $this->_LangCachePath = $path;
                } else {
                    $this->Error("Invalid cache directory. The directory '<i>".$cache_path."</i>' is not writeable. Cache directory switched to CWD.");
                    $this->_LangCachePath = $cwd;
                }
            } else {
                $this->Error("Invalid cache directory. The directory '<i>".$cache_path."</i>' not exists, or not readable. Cache directory switched to CWD.");
                $this->_LangCachePath = $cwd;
            }
        } else {
            $this->_LangCachePath = $cwd;
        }
    }


    /**
    *   Assign a new language with its options.
    *
    *   @param  string $lang_idx   index for language (EN, RU, FR, ..., whatever you'd like)
    *   @param  string $lang_path  Language directory(relative to current 'lang_root') By default - '_LangRoot' will be used.
    *   @param  array $locale   locale settings for current language.<br>
    *                                The array must have two elements: <br>
    *                                    - <b>0</b> is the NAME OF CONSTANT (string!), required for setlocale(),<br>
    *                                       one of LC_ALL, LC_COLLATE, LC_CTYPE, LC_MONETARY, LC_NUMERIC, or LC_TIME.<br>
    *                                    - <b>1</b> is the second argument for setlocale(). Locale name.<br>
    *                                See manual of setlocale() on the {@link http://php.net} for details.
    *   @return bool TRUE on success, or FALSE on errors
    */
    function AssignLanguage( $lang_idx=null, $lang_path=null, $locale=null )
    {
        if(is_string($lang_idx) && $lang_idx){
            if (!isset($this->_LangIdx[$lang_idx])) {
                $this->_LangIdx[$lang_idx]['dir'] = $this->_LangRoot;
                $this->_LangIdx[$lang_idx]['parse']=$this->_LangIdx[$lang_idx]['cache']=array();
                if(!is_null($lang_path)) $this->SetLanguageDir($lang_idx,$lang_path);
                if(is_array($locale)){
                    if(isset($locale[0]) && isset($locale[1])){
                        $this->_LangIdx[$lang_idx]['locale']['LC_MODE']=$locale[0];
                        $this->_LangIdx[$lang_idx]['locale']['LC_OPT'] =$locale[1];
                        return true;
                    }
                    $this->Error("Invalid locale options for language '<i>".$lang_idx."</i>'.");
                    return false;
                }
                return true;
            }
            $this->Error("Attempt of double-assign language. Language '<i>".$lang_idx."</i>' was already assigned.");
            return false;
        }
        $this->Error("Invalid language assigned. Language index must be a valid string.");
        return false;
    }


    /**
    *   Set the directory for language by its index.
    *
    *   @access public
    *   @param string $idx        Language index (one of what we've assign by calling AssignLanguage)
    *   @param string $lang_dir    Language directory
    *   @return bool   TRUE on success, FALSE-on errors
    */
    function SetLanguageDir( $lang_idx=null, $lang_dir=null )
    {
        if(isset($this->_LangIdx[$lang_idx])){
            if($dir = realpath($this->_LangRoot."/".$lang_dir)){
                  $this->_LangIdx[$lang_idx]['dir'] = $dir;
                  return true;
            }
            $this->Error("Invalid language '<i>".$lang_idx."</i>' directory. The directory  '<i>".$this->_LangRoot."/".$lang_dir."</i>'.");
            return false;
        }
        $this->Error("Invalid language index. Language '<i>".$lang_idx."</i>' not yet assigned.");
        return false;
    }


    /**
    *   Assign a source data for language by its index.
    *
    *   @access public
    *   @param  string  $lang_idx  index of language
    *   @param  mixed   $source    source language data.<br>
    *                               It must be an array, or valid filename, relative to language directory.
    *   @param  integer $cache_expire - The cache expire. It matters only when 'source' is a filename.<br>
    *                                   The 'expire' value will be used for cache expire checking,<br>
    *                                   if the caching is enabled for this language.<br>
    *   @return bool  TRUE on success, FALSE if some errors occurs.
    */
    function AssignLanguageSource( $lang_idx="", $source=array(), $cache_expire=0 )
    {
        if(isset($this->_LangIdx[$lang_idx])){
            if(is_string($source)){
                if($this->_LangCurrent) {
                    $this->Error("You can assign the non-array source only before you call \$this->SetLanguage(). Source will not be processed.");
                    return false;
                }
                if(is_file($this->_LangIdx[$lang_idx]['dir']."/".$source) && is_readable($this->_LangIdx[$lang_idx]['dir']."/".$source)){
                    if($cache_expire > 0){
                        $this->_LangIdx[$lang_idx]['cache'][$this->_LangIdx[$lang_idx]['dir']."/".$source]=$cache_expire;
                        return true;
                    }
                    $this->_LangIdx[$lang_idx]['parse'][]=$this->_LangIdx[$lang_idx]['dir']."/".$source;
                    return true;
                }
                $this->Error("Invalid language source filename. The file '<i>".$this->_LangIdx[$lang_idx]['dir']."/".$source."</i>' not exists, or not readable.");
                return false;
            }
            if(is_array($source)){
                if(!$source) return true;
                if($this->_LangCurrent){
                    if($this->_LangCurrent == $lang_idx){
                        $this->__LANGDATA=array_merge($this->__LANGDATA,$source);
                    }
                    return true;
                }
                $this->_LangIdx[$lang_idx]['parse'][]=$source;
                return true;
            }
            $this->Error("Invalid language '<i>".$lang_idx."</i>' source type. The source must be an array, or valid filename relative to '<i>".$this->_LangIdx[$lang_idx]['dir']."</i>'.");
            return false;
        }
        $this->Error("Invalid language index. Language '<i>".$lang_idx."</i>' not yet assigned.");
        return false;
    }


    /**
    *   Initialize the current language, using its index.
    *   After calling this method, all the source data, has been assigned for this language<br>
    *   will be processed and will be available for using.<br>
    *   <br>
    *   <b>NOTE:</b> This method can be called only once.
    *
    *   @access public
    *   @param string  $lang_idx        Language index
    *   @param bool    $cache_enabled   Enable or disable caching for this language.
    *   @return bool  TRUE on success, or FALSE if error occurs.
    */
    function SetLanguage( $lang_idx="", $cache_enabled = false )
    {
        if($this->_LangCurrent)return true; 
        if(isset($this->_LangIdx[$lang_idx])){
            $this->_LangCurrent = $lang_idx;
            $this->_LangCacheEnabled = $cache_enabled;
            $this->ProcessLanguageData();
            if(isset($this->_LangIdx[$this->_LangCurrent]['locale']))$this->SetLocale();
            return true;
        }
        $this->Error("Invalid language index. Language '<i>".$lang_idx."</i>' not yet assigned.");
        return false;
    }
	
	
    /**
    *   Returns a string from a processed language data.
    *
    *   @access public
    *   @param string $idx       Index of a string.
    *   @param string $default   Default value, which will be returned, if no $idx string will be founded.
    *   @return string           Processed language data string, if it has been founded,<br>
    *                            or $default, if it has been missing.
    */
    function GetString( $idx="",$default="" )
    {
        return (isset($this->__LANGDATA[$idx]))?$this->__LANGDATA[$idx]:$default;
    }


    /**
    *   Returns a string from a processed language data, formatted by sprintf()
    *
    *   @access public
    *   @param mixed   $idx    string index
    *   @param array   $args   numeric array of values for substitution.<br>
    *                           It's working as sprintf(), behind that exception,<br>
    *                           that if it is less than parameters, than modifiers,<br>
    *                           the error will not be generated.<br>
    *                           In this case modifier will be returned as is.
    *   @param string $default  Default value, which will be returned,<br> if no '$idx' string will be founded.
    *   @return string       string
    */
    function GetFString( $idx="",$args=array(),$default="")
    {
        if(isset($this->__LANGDATA[$idx])){
            $this->args_format = (array)$args;
            $this->num_format = 0;
            return  preg_replace_callback($this->_LangFormatRegex,array(&$this,'FormatString'),$this->__LANGDATA[$idx]);
        }
        return $default;
    }


    /**
    *   Return the reference to a string from language data array.
    *   <b>NOTE</b>: you should call this function only by reference
    *
    *   @access public
    *   @param string $idx   Index of a string.
    *   @return  string      Reference to a string, or NULL, if no such string founded.
    */
    function &GetStringReference($idx=null)
    {
        if(isset($this->__LANGDATA[$idx])){
            return $idx=&$this->__LANGDATA[$idx];
        }
        $missing=null;
        return $idx=&$missing;
    }


    /**
    *   Returns an current language index
    *
    *   @access public
    *   @return mixed   - Language index on success, or NULL, if the no current active language.
    */
    function GetLanguage()
    {
        return $this->_LangCurrent;
    }


    /**
    *   Returns an current locale value if it has been set.
    *   @access public
    *   @return mixed   - Locale value, or NULL if the locale has not been defined.
    */
    function GetLocale()
    {
        return $this->_LangLocale;
    }


    /**
    *   Takes the given sprintf()-mofifier, and replaces it with the current argument from $this->_args_format.
    *
    *   @access private
    *   @param  array $modifier Current modifier, given from '<b>preg_replace_callback</b>'.
    *   @return string formatted value.
    */
    function FormatString($modifier=array())
    {
        if(!isset($this->args_format[$this->num_format]))return $modifier[0];
        return sprintf($modifier[0],$this->args_format[$this->num_format++]);
    }


    /**
    *   Processing the data for current language
    *   @access private
    *   @return bool TRUE on success, FALSE on errors
    */
    function ProcessLanguageData()
    {

        $__TMP = $__CACHE = array();

        //Processing data wich is not in cache list
        foreach($this->_LangIdx[$this->_LangCurrent]['parse'] as $k=>$v){
            if(is_array($v)){
                $__TMP = array_merge($__TMP,$v);
                continue;
            }
            $__TMP = array_merge($__TMP,$this->ProcessLanguageFile($v));
        }

        //If caching is enabled, we processing the cache list
        if($this->_LangCacheEnabled){
            if(!empty($this->_LangIdx[$this->_LangCurrent]['cache'])){

                foreach($this->_LangIdx[$this->_LangCurrent]['cache'] as $file=>$expire){

                    $cachefile=$this->_LangCachePath."/".md5($file).".dump";

                    if($this->CheckCache($cachefile,$expire)){
                        //Cache file is valid
                        if(!is_array($data=$this->GetFileFromCache($cachefile))){
                            $data = $data = $this->ProcessLanguageFile($file);
                            $this->Error("Unable read cache for language file '<i>".$file."</i>'.");
                        }
                    } else {
                        //Cache is expired, or not exists:
                        //Processing the language file, and writing cache for it.
                        $data = $this->ProcessLanguageFile($file);
                        $this->WriteToCache($cachefile,$data);
                    }
                    $__CACHE = array_merge($__CACHE,$data);
                }

            } else {
                //The cache list is empty.
                //It means that all the files are processed.
                $this->__LANGDATA = $__TMP;
                return;
            }
            $__TMP = array_merge($__TMP,$__CACHE);
        } else {
            //Caching is disabled.
            //Process all the files wich is in the cache-list
            foreach($this->_LangIdx[$this->_LangCurrent]['cache'] as $file=>$expire){
                $data = $this->ProcessLanguageFile($file);
                $__TMP = array_merge($__TMP,$data);
            }
        }
        $this->__LANGDATA = $__TMP;
    }


    /**
    *   Validate the cache file with given expire
    *   @access private
    *   @param string $fname Filename
    *   @param int    $expire Expire for the given file
    */
    function CheckCache($fname="",$expire=0)
    {
        return (file_exists($fname) && is_readable($fname) && check_file_expire($fname,$expire))?true:false;
    }


    /**
    *   Process the language file
    *
    *   Search the language strings in the given file,<br>
    *   using _LangStringRegex regular expression,<br>
    *   and returns the array 'string_index'=>'string_value'.
    *
    *   @access private
    *   @param string $fname The name of file to process
    *   @return array  The result array.
    */
    function ProcessLanguageFile($fname="")
    {
        preg_match_all($this->_LangStringRegex, ($this->_flag_magic_quotes_runtime)?stripslashes(file_get_contents($fname)):file_get_contents($fname), $strings, PREG_SET_ORDER);
        $array=array();
        foreach ($strings as $v) $array[$v[1]]=stripslashes($v[2]);
        return $array;
    }

    /**
    *   Read the data from cache file.
    *   @access private
    *   @param string $cachefile name of cache file.
    *   @return mixed Returns the 'array' unserialized data on success,<br>or FALSE on errors.
    */
    function GetFileFromCache($cachefile="")
    {
        return unserialize(($this->_flag_magic_quotes_runtime)?stripslashes(file_get_contents($cachefile)):file_get_contents($cachefile));
    }

    /**
    *   Write given array to cache.
    *   @access private
    *   @param string $cachefile Filename of cache file.
    *   @param array $data Data to caching.
    */
    function WriteToCache($cachefile="",$data=array())
    {
        if($fp=fopen($cachefile,"w")){
            if(!fwrite($fp,serialize($data))){
                 $this->Error("Unable to cache file '<i>".$file."</i>'.");
                 return false;
            }
            fclose($fp);
            return true;
        }
        $this->Error("Unable to open cache file for writing for language file '<i>".$file."</i>'.");
        return false;
    }


    /**
    *   Set the locale for current language
    *   @access private
    *   @param string $lang_idx The index of language.
    *   @return bool TRUE on success, or FALSE on errors.
    */
    function SetLocale($lang_idx="")
    {
        if(($LC = constant($this->_LangIdx[$this->_LangCurrent]['locale']['LC_MODE'])) !== false){
            if($this->_LangLocale=setlocale($LC,$this->_LangIdx[$this->_LangCurrent]['locale']['LC_OPT'])){
                return true;
            }
            $this->Error("Unable to set locale for language '<i>".$this->_LangCurrent."</i>'.");
            return false;
        }
        $this->Error("Invalid locale mode for language '<i>".$this->_LangCurrent."</i>'. Unable to set locale.");
        return false;
    }


    /**
    *   Handle the error messages
    *
    *   Sample 'echo' of error messages.<br>
    *   You can change this function if you want other handling of errors.
    *   @access private
    *   @param string $message The error message
    *   @param mixed $code    The error code
    *   @return void
    */
    function Error($message, $code=E_USER_WARNING)
    {
        echo $message."; Code: ".$code."<br />";
    }
}

?>