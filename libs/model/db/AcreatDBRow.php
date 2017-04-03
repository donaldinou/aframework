<?php
/**
 *  +----------------------------------------------------------------------+
 *  | Zend Framework                                                       |
 *  +----------------------------------------------------------------------+
 *  | Copyright (c) 2005-2006 Zend Technologies Inc. (http://www.zend.com) |
 *  +----------------------------------------------------------------------+
 *  | This source file is subject to version 1.0 of the Zend Framework     |
 *  | license, that is bundled with this package in the file LICENSE, and  |
 *  | is available through the world-wide-web at the following url:        |
 *  | http://www.zend.com/license/framework/1_0.txt.                       |
 *  | If you did not receive a copy of the Zend license and are unable to  |
 *  | obtain it through the world-wide-web, please send a note to          |
 *  | license@zend.com so we can mail you a copy immediately.              |
 *  +----------------------------------------------------------------------+
 *
 * @package    Zend_Db
 * @subpackage Table
 * @copyright  Copyright (c) 2005-2006 Zend Technologies Inc. (http://www.zend.com)
 * @license    Zend Framework License version 1.0
 */

/**
 * @package    Zend_Db
 * @subpackage Table
 * @copyright  Copyright (c) 2005-2006 Zend Technologies Inc. (http://www.zend.com)
 * @license    Zend Framework License version 1.0
 */
class AcreatDBRow
{
    /**
     * The data for each column in the row (underscore_words => value).
     */
    var $_data = array();
  
    /**
     * AdoDB object from the table interface.
     */
    var $_db;
    
    /**
     * AcreatModel interface (the row "parent").
     */
    var $_model;
    
    /**
     * AcreatModel info (name, cols, primary, etc).
     * 
     * @var array
     */
    var $_info = array();
    
    /**
     * Constructor.
     */
	 
	function AcreatDBRow($config = array())
    {
        $this->_db    = $config['db'];  
        $this->_model = $config['model'];
        $this->_info  = $this->_model->info();
		$this->_data  = ($config['data'] === false ? array() : (array) $config['data']);

		$this->__dataToProp();
    }
	
	/** 
	__dataToProp
	*/
	function __dataToProp() {
		foreach( $this->_data as $key=>$value)
			$this->$key = $value;
		$this->__load_attached_models();
	}
	
    /** 
	__propToData
	*/
	function __propToData() {
	
		$vars = get_object_vars ( $this );
		foreach( $vars as $col => $value) {
			if(!preg_match("/^_/", $col) && get_parent_class($value) != "AcreatModel" )
				$this->_data[$col] = $this->$col;
		}
	}
	
	/** 
	__load_attached_models
	*/
	function __load_attached_models() {
	
		// Rattachement des parents
		if( count($this->_model->_models["_parent"]) > 0 ) {
			foreach( $this->_model->_models["_parent"] as $object ) {
				$object = $object->_clone();
				$object->_id = $this->_data[$object->_primary];
				$name = $object->_name;
				$this->$name = $object;
			}
		}
		
		// Rattachement des enfants
		if( count($this->_model->_models["_child"]) > 0 ) {
			foreach( $this->_model->_models["_child"] as $object ) {
				$object = $object->_clone();
				$name = $object->_name;
				if(isset($this->_data[$this->_model->_primary])) {
					$object->add_where( $this->_model->_name.".".$this->_model->_primary." = ".$this->_data[$this->_model->_primary] );
					$object->_default[$this->_model->_primary] = $this->_data[$this->_model->_primary];
				}
				$this->$name = $object;	
			}
		}
	}
	
    /**
     * Getter for camelCaps properties mapped to underscore_word columns.
     * 
     * @param string $camel The camelCaps property name; e.g., 'columnName'
     * maps to 'column_name'.
     * @return string The mapped column value.
     */
    function get($camel)
    {
		if ( isset($this->_data[$under])) 
           return $this->_data[$under];
		return false;
    }
    
    /**
     * Setter for camelCaps properties mapped to underscore_word columns.
     * 
     * @param string $camel The camelCaps property name; e.g., 'columnName'
     * maps to 'column_name'.
     * @param mixed $value The value for the property.
     * @return void
     */
    function set($camel, $value=false)
    {
		// AcreatFormulaire
		if( strtolower(get_class($camel)) == "acreatformulaire") {
			$camel->__propToData();
			$camel = $camel->_datas;
			if(isset($camel[$this->_model->_primary])) 
				unset($camel[$this->_model->_primary]);
		}
		
		// AcreatDBRow
		if( strtolower(get_class($camel)) == "acreatdbrow") {
			$camel->__propToData();
			$camel = $camel->_data;
		}
		
	
		if( is_array($camel) ) {
			// Array
			$erase_primary_key = ($value);
			foreach( $camel as $key=>$value) { 
				if( $key == $this->_model->_primary && !$erase_primary_key ) 
					continue;
				$this->set($key, $value);
			}
		}
		else {
			// Normal
			$this->_data[$camel] = $value;
			$this->__dataToProp();
		}
    }
    function setFromArray($data, $bool=false) { $this->set($data, $bool); }
    
	   
	
    /**
     * Saves the properties to the database.
     * 
     * This performs an intelligent insert/update, and reloads the 
     * properties with fresh data from the table on success.
     * 
     * @return int 0 on failure, 1 on success.
     */
    function save()
    {
		$this->__propToData();
		
        // convenience var for the primary key name
        $primary = $this->_info['primary'];
		
        // check the primary key value for insert/update
        if (empty($this->_data[$primary])) {

            // no primary key value, must be an insert.
            // make sure it's null.
            $this->_data[$primary] = null;
            
            // attempt the insert.
            $result = $this->_model->insert($this->_data); 
            if (is_numeric($result)) {
                // insert worked, refresh with data from the table
                $this->_data[$primary] = $result;
                $this->_refresh();
            }
            
            // regardless of success return the result
            return $result;
            
        } else {
            
            // has a primary key value, update only that key.
			$sql = $this->_db->select();
            $where = $sql->quoteInto(
                "$primary = ?",
                $this->_data[$primary]
            );
			
            // return the result of the update attempt,
            // no need to update the row object.
            $result = $this->_model->update($this->_data, $where);
            if (is_int($result)) {
                // update worked, refresh with data from the table
                $this->_refresh();
            }
			
			return $result;
        }
    }
    
	
	/**
     * delete
     * 
     * @return array
     */
	function delete() 
	{
		$this->__propToData();
		
        // convenience var for the primary key name
        $primary = $this->_info['primary'];
		
		if (empty($this->_data[$primary])) return false;
		// has a primary key value, delete only that key.
		$sql = $this->_db->select();
        $where = $sql->quoteInto( "$primary = ?", $this->_data[$primary] );
		return $this->_model->delete($where);
	}
	
	
    /**
     * Returns the column/value data as an array.
     * 
     * @return array
     */
    function toArray()
    {
		$this->__propToData();
        return $this->_data;
    }
 
    
    /**
     * Refreshes properties from the database.
     */
    function _refresh()
    {
        $fresh = $this->_model->find($this->_data[$this->_info['primary']]);
        // we can do this because they're both Zend_Db_Table_Row objects
		if($fresh) {
			$this->_data = $fresh->_data;
			$this->__dataToProp();
		}
    }
}

