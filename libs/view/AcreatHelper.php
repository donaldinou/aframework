<?php
/* SVN FILE: $Id: helper.php 1652 2005-12-28 17:23:12Z phpnut $ */

/**
 * Backend for helpers.
 * Internal methods for the Helpers.
 */

/**
 * Backend for helpers.
 */
class AcreatHelper
{
 /*************************************************************************
    * Public variables
    *************************************************************************/

 /**#@+
    * @access public
    */


 /**
 * Holds tag templates.
 *
 * @access public
 * @var array
 */
    var $tags = array();

 /**#@-*/

 /*************************************************************************
    * Public methods
    *************************************************************************/

 /**#@+
    * @access public
    */

 /**
 * Constructor.
 *
 * Parses tag templates into $this->tags.
 *
 * @return void
 */
    function AcreatHelper()
    {
    }

    function loadConfig()
    {
       return $this->readConfigFile(FRAMEWORK_DIR.DS.'config'.DS.'tags.ini.php');
    }

 /**
 * Decides whether to output or return a string.
 *
 * Based on AUTO_OUTPUT and $return's value, this method decides whether to
 * output a string, or return it.
 *
 * @param  string  $str    String to be output or returned.
 * @param  boolean $return Whether this method should return a value or
 *                         output it. This overrides AUTO_OUTPUT.
 * @return mixed   Either string or boolean value, depends on AUTO_OUTPUT
 *                 and $return.
 */
    function output($str, $return = false)
    {
        if ($return === true)
        {
            if (print $str)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return $str;
        }
    }

 /**
 * Assigns values to tag templates.
 *
 * Finds a tag template by $keyName, and replaces $values's keys with
 * $values's keys.
 *
 * @param  string $keyName Name of the key in the tag array.
 * @param  array  $values  Values to be inserted into tag.
 * @return string Tag with inserted values.
 */
    function assign($keyName, $values)
    {
        return str_replace('%%'.array_keys($values).'%%', array_values($values),
        $this->tags[$keyName]);
    }

/**
 * Returns an array of settings in given INI file.
 *
 * @param string $fileName
 * @return array
 */
    function readConfigFile ($fileName)
    {
        $fileLineArray = file($fileName);

        foreach ($fileLineArray  as $fileLine)
        {
            $dataLine = trim($fileLine);
            $firstChar = substr($dataLine, 0, 1);
            if ($firstChar != ';' && $dataLine != '')
            {
                if ($firstChar == '[' && substr($dataLine, -1, 1) == ']')
                {
                    // [section block] we might use this later do not know for sure
                    // this could be used to add a key with the section block name
                    // but it adds another array level
                }
                else
                {
                    $delimiter = strpos($dataLine, '=');
                    if ($delimiter > 0)
                    {
                        $key = strtolower(trim(substr($dataLine, 0, $delimiter)));
                        $value = trim(substr($dataLine, $delimiter + 1));
                        if (substr($value, 0, 1) == '"' && substr($value, -1) == '"')
                        {
                            $value = substr($value, 1, -1);
                        }
                        $iniSetting[$key] = stripcslashes($value);
                    }
                    else
                    {
                        $iniSetting[strtolower(trim($dataLine))]='';
                    }
                }
            }
            else
            {
            }
        }
        return $iniSetting;
    }

 /**#@-*/
}

?>