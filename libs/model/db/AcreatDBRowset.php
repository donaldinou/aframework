<?php
/*
 * Acreat Framework
 *
 */
 
 
/**
 * @package    AcreatDBRowset
 * @subpackage Table
 */
class AcreatDBRowset /*implements Iterator*/
{
    /**
     * The original data for each row.
     * 
     * @var array
     */
    var $_data = array();
    
    /**
     * AdoDB object from the table interface.
     * 
     * @var AdoDB
     */
    var $_db;
    
    /**
     * AcreatModel object.
     * 
     * @var AcreatModel
     */
    var $_model;
    
    /**
     * Iterator pointer.
     */
    var $_pointer = 0;
    
    /**
     * How many data rows there are.
     */
    var $_count;
    
    
    /**
     * Collection of instantiated Zend_Db_Table_Row objects.
     */
    var $_rows = array();
    
	var $_page = false;
    /**
     * Constructor.
     */
    function AcreatDBRowset($config = array())
    {
        $this->_db    	= $config['db'];
        $this->_model 	= $config['model'];
        $this->_data  	= $config['data'];
		if(isset($config['page']))
        	$this->_page  = $config['page'];
        
        // set the count of rows
        $this->_count = count($this->_data);
    }
    
    /**
     * Rewind the Iterator to the first element.
     * Similar to the reset() function for arrays in PHP.
     * 
     * @return void
     */
    function rewind()
    {
        $this->_pointer = 0;
    }

    /**
     * Return the current element.
     * Similar to the current() function for arrays in PHP
     * 
     * @return mixed current element from the collection
     */
    function current()
    {
        // is the pointer at a valid position?
        if (! $this->valid()) {
            return false;
        }
        
        // do we already have a row object for this position?
        if (empty($this->_rows[$this->_pointer])) {
            // create a row object
            $this->_rows[$this->_pointer] = new AcreatDBRow(array(
                'db'    => $this->_db,
                'model' => $this->_model,
                'data'  => $this->_data[$this->_pointer]
            ));
        }
        
        // return the row object
        return $this->_rows[$this->_pointer];
    }

    /**
     * Return the identifying key of the current element.
     * Similar to the key() function for arrays in PHP.
     * 
     * @return int
     */
    function key()
    {
        return $this->_pointer;
    }

    /**
     * Move forward to next element.
     * Similar to the next() function for arrays in PHP.
     * 
     * @return int The next pointer value.
     */
    function next()
    {
        return ++$this->_pointer;
    }


    /**
     * Move forward to next element.
     */
    function fetch()
    {
        $item = $this->current();
		if($item)
			$this->next();
		return $item;
    }

    /**
     * Check if there is a current element after calls to rewind() or next().
     * Used to check if we've iterated to the end of the collection.
     * 
     * @return bool False if there's nothing more to iterate over
     */
    function valid()
    {
        return $this->_pointer < $this->count();
    }

    /**
     * Returns the number of elements in the collection.
     * 
     * @return int
     */
    function count()
    {
        return $this->_count;
    }

    /**
     * Returns true if $this->count > 0, false otherwise.
     * 
     * @return bool
     */
    function exists()
    {
        return $this->_count > 0;
    }
    
    /**
     * Returns all data as an array.
     * 
     * Updates the $_data property with current row object values.
     * 
     * @return array
     */
    function toArray()
    {
        foreach ($this->_rows as $i => $row) {
            $this->_data[$i] = $row->toArray();
        }
        return $this->_data;
    }
}
