<?php

/**
 *  File: calendar.php | (c) dynarch.com 2004
 *  Distributed as part of "The Coolest DHTML Calendar"
 *  under the same terms.
 *  -----------------------------------------------------------------
 *  This file implements a simple PHP wrapper for the calendar.  It
 *  allows you to easily include all the calendar files and setup the
 *  calendar by instantiating and calling a PHP object.
 */

define('NEWLINE', "\n");

class DHTMLCalendar {
	
    var $calendar_lib_path;
    var $calendar_file;
    var $calendar_lang_file;
    var $calendar_setup_file;
    var $calendar_theme_file;
    var $calendar_options;

    function DHTMLCalendar($calendar_lib_path = 'js/DHTMLCalendar/',
                            $lang              = 'fr',
                            $theme             = 'calendar-system',
                            $stripped          = true) {
        if ($stripped) {
            $this->calendar_file = 'calendar_stripped.js';
            $this->calendar_setup_file = 'calendar-setup_stripped.js';
        } else {
            $this->calendar_file = 'calendar.js';
            $this->calendar_setup_file = 'calendar-setup.js';
        }
        $this->calendar_lang_file = 'lang/calendar-' . $lang . '.js';
        $this->calendar_theme_file = $theme.'.css';
        $this->calendar_lib_path = preg_replace('/\/+$/', '/', $calendar_lib_path);
        $this->calendar_options = array('ifFormat' => '%Y/%m/%d',
                                        'daFormat' => '%Y/%m/%d');
    }

    function set_option($name, $value) {
        $this->calendar_options[$name] = $value;
    }

    function load_js_files() {
		if(defined("DHTMLCALENDAR_REQUIRED_FILES_INCLUDED")) return;
        echo $this->get_load_files_code();
		define("DHTMLCALENDAR_REQUIRED_FILES_INCLUDED", 1);
    }

    function get_load_files_code() {
        $code  = ( '<link rel="stylesheet" type="text/css" media="all" href="' .
                   $this->calendar_lib_path . $this->calendar_theme_file .
                   '" />' . NEWLINE );
        $code .= ( '<script type="text/javascript" src="' .
                   $this->calendar_lib_path . $this->calendar_file .
                   '"></script>' . NEWLINE );
        $code .= ( '<script type="text/javascript" src="' .
                   $this->calendar_lib_path . $this->calendar_lang_file .
                   '"></script>' . NEWLINE );
        $code .= ( '<script type="text/javascript" src="' .
                   $this->calendar_lib_path . $this->calendar_setup_file .
                   '"></script>' );
        return $code;
    }	

    function _make_calendar($other_options = array()) {
        $js_options = $this->_make_js_hash(array_merge($this->calendar_options, $other_options));
        $code  = ( '<script type="text/javascript">Calendar.setup({' .
                   $js_options .
                   '});</script>' );
        return $code;
    }

    function make_input_field($cal_options = array(), $field_attributes = array()) {
        $id = $this->_gen_id();
        $attrstr = $this->_make_html_attr(array_merge($field_attributes,
                                                      array('id'   => $this->_field_id($id),
                                                            'type' => 'text')));
        echo '<input ' . $attrstr .'/>';
        echo '<a href="#" id="'. $this->_trigger_id($id) . '">' .
            '<img align="middle" border="0" src="' . $this->calendar_lib_path . 'img.gif" alt="" /></a>';

        $options = array_merge($cal_options,
                               array('inputField' => $this->_field_id($id),
                                     'button'     => $this->_trigger_id($id)));
        echo $this->_make_calendar($options);
    }

    /// PRIVATE SECTION

    function _field_id($id) { return 'f-calendar-field-' . $id; }
    function _trigger_id($id) { return 'f-calendar-trigger-' . $id; }
    function _gen_id() { static $id = 0; return ++$id; }

    function _make_js_hash($array) {
        $jstr = '';
        reset($array);
        while (list($key, $val) = each($array)) {
            if (is_bool($val))
                $val = $val ? 'true' : 'false';
            else if (!is_numeric($val) && !in_array($key, array("onSelect", "onUpdate", "flatCallback", "onClose")) )
                $val = '"'.$val.'"';
            if ($jstr) $jstr .= ',';
            $jstr .= '"' . $key . '":' . $val;
        }
        return $jstr;
    }

    function _make_html_attr($array) {
        $attrstr = '';
        reset($array);
        while (list($key, $val) = each($array)) {
            $attrstr .= $key . '="' . $val . '" ';
        }
        return $attrstr;
    }
	
	/* ------------------
	* make_simple_input
	* 2006 - ACREAT 
	*/
	function make_simple_input($nomVariable,$valeurInit="",$mask="%d/%m/%Y",$timeFormat=24,$options=NULL, $moreAttr="")
	{
		$id = $nomVariable;
		
		$optionsCalendar = array(
			"button"	    => $this->_trigger_id($id),
			"inputField"	=> $id,
			"ifFormat" 		=> $mask,   	  		// format of the input field
			"align" 	 	=> "bL",
			"mondayFirst" 	=> true,
			"firstDay" 		=> 1
		);
		
		if(is_array($options))
			$optionsCalendar = array_merge($optionsCalendar, $options);
		
		if( preg_match('/\d{4}-\d{1,2}-\d{1,2}/',$valeurInit) )
			$valeurInit = format_mysql_date($valeurInit, $mask);
		
		echo '<a href="#" id="'. $this->_trigger_id($id) . '" class="dhtmlcalendar_trigger">' . '<img align="absmiddle" border="0" src="' . $this->calendar_lib_path . 'img.gif" alt="" /></a> ';
		echo '<input type="text" id="'.$id.'" name="'.$id.'" value="'.$valeurInit.'" class="date" '.$moreAttr.'>';
		
		echo $this->_make_calendar($optionsCalendar);
	}
	
	function include_require()
	{ 
		if(defined("DHTMLCALENDAR_REQUIRED_FILES_INCLUDED")) return;
		print('<link rel="stylesheet" type="text/css" media="all" href="js/DHTMLCalendar/calendar-system.css" />');
		print('<script type="text/javascript" src="js/DHTMLCalendar/calendar_stripped.js"></script>');
		print('<script type="text/javascript" src="js/DHTMLCalendar/lang/calendar-fr.js"></script>');
		print('<script type="text/javascript" src="js/DHTMLCalendar/calendar-setup_stripped.js"></script>');
		define("DHTMLCALENDAR_REQUIRED_FILES_INCLUDED", 1);
	}
};

?>