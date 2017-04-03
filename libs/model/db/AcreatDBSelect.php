<?php
/**
 * Acreat Framework
 */
 

/**
 * Class for SQL SELECT generation and results.
 */
 
class AcreatDBSelect 
{

    var $_adodb;
    /**
     * The component parts of a SELECT statement.
     * @var array
     */
    var $_parts = array(
        'distinct'    => false,
        'forUpdate'   => false,
        'cols'        => array(),
        'from'        => array(),
        'join'        => array(),
        'where'       => array(),
        'group'       => array(),
        'having'      => array(),
        'order'       => array(),
        'limitCount'  => null,
        'limitOffset' => null,
    );


    /**
     * Class constructor
     * @param ADOConnection $adodb
     */
    function AcreatDBSelect($adodb = NULL)
    {
		if( $adodb != null)
			$this->_adodb = $adodb;
		elseif( isset($GLOBALS["DB"]) )
			$this->_adodb = $GLOBALS["DB"];
    }
	
    /**
	*initFromObject
           * @param AcreatDBSelect $obj
          */
    function initFromObject($obj) {
		$vars = get_object_vars ( $obj );
		foreach( $vars as $key=>$var ) {
			$this->$key = $var;
		}
    }

    /**
     * Converts this object to an SQL SELECT string.
     * @return string This object as a SELECT string.
     */
   	function toString()
    {
        // initial SELECT [DISTINCT] [FOR UPDATE]
        $sql = "SELECT";
        if ($this->_parts['distinct']) {
            $sql .= " DISTINCT";
        }
        if ($this->_parts['forUpdate']) {
            $sql .= " FOR UPDATE";
        }
        $sql .= "\n\t";

        // add columns
        if ($this->_parts['cols']) {
            $sql .= implode(",\n\t", $this->_parts['cols']) . "\n";
        }

        // from these tables
        if ($this->_parts['from']) {
            $sql .= "FROM ";
            $sql .= "(".implode(", ", $this->_parts['from']). ")" . "\n";
        }

        // joined to these tables
        if ($this->_parts['join']) {
            $list = array();
            foreach ($this->_parts['join'] as $join) {
                $tmp = '';
                // add the type (LEFT, INNER, etc)
                if (! empty($join['type'])) {
                    $tmp .= strtoupper($join['type']) . ' ';
                }
                // add the table name and condition
                $tmp .= 'JOIN ' . $join['name'];
                $tmp .= ' ON ' . $join['cond'];
                // add to the list
                $list[] = $tmp;
            }
            // add the list of all joins
            $sql .= implode("\n", $list) . "\n";
        }

        // with these where conditions
        if ($this->_parts['where']) {
            $sql .= "WHERE\n\t";
            $sql .= implode("\n\t", $this->_parts['where']) . "\n";
        }

        // grouped by these columns
        if ($this->_parts['group']) {
            $sql .= "GROUP BY\n\t";
            $sql .= implode(",\n\t", $this->_parts['group']) . "\n";
        }

        // having these conditions
        if ($this->_parts['having']) {
            $sql .= "HAVING\n\t";
            $sql .= implode("\n\t", $this->_parts['having']) . "\n";
        }

        // ordered by these columns
        if ($this->_parts['order']) {
            $sql .= "ORDER BY\n\t";
            $sql .= implode(",\n\t", $this->_parts['order']) . "\n";
        }

        // determine count
        $count = ! empty($this->_parts['limitCount'])
            ? (int) $this->_parts['limitCount']
            : 0;

        // determine offset
        $offset = ! empty($this->_parts['limitOffset'])
            ? (int) $this->_parts['limitOffset']
            : 0;
		
		//return $sql . ( $count>0 ? " LIMIT $count" : ( $offset>0 ? ", $offset" : "") );
		//return $sql . ( $count>0 ? " LIMIT $offset" . ( $offset>0 ? ", $offset" : "")  : "");
		return $sql . ( $offset>0 ? "LIMIT $offset, $count " : ( $count > 0 ? "LIMIT $count" : "" ) );
		
		
        // add limits, and done
        //return trim($this->_adodb->SelectLimit($sql, $count, $offset));
    }


    /**
     * Makes the query SELECT DISTINCT.
     * @param bool $flag Whether or not the SELECT is DISTINCT (default true).
     * @return Zend_Db_Select This Zend_Db_Select object.
     */
    function distinct($flag = true)
    {
        $this->_parts['distinct'] = (bool) $flag;
        return $this;
    }


    /**
     * Makes the query SELECT FOR UPDATE.
     * @param bool $flag Whether or not the SELECT is DISTINCT (default true).
     * @return Zend_Db_Select This Zend_Db_Select object.
     */
    function forUpdate($flag = true)
    {
        $this->_parts['forUpdate'] = (bool) $flag;
        return $this;
    }


    /**
     * Adds a FROM table and optional columns to the query.
     * @param string $name The table name.
     * @param array|string $cols The columns to select from this table.
     * @return Zend_Db_Select This Zend_Db_Select object.
     */
    function from($name, $cols = '*')
    {
        // add the table to the 'from' list
        $this->_parts['from'] = array_merge(
            $this->_parts['from'],
            (array) $name
        );

        // add to the columns from this table
        $this->col($cols);
        return $this;
    }

    /**
     * Populate the {@link $_parts} 'join' key
     *
     * Does the dirty work of populating the join key.
     *
     * @access protected
     * @param null|string $type Type of join; inner, left, and null are
     * currently supported
     * @param string $name Table name
     * @param string $cond Join on this condition
     * @param array|string $cols The columns to select from the joined table
     * @return Zend_Db_Select This Zend_Db_Select object
     */
    function _join($type, $name, $cond, $cols) 
    {
        if (!in_array($type, array('left', 'inner'))) {
            $type = null;
        }

        $this->_parts['join'][] = array(
            'type' => $type,
            'name' => $name,
            'cond' => $cond
        );

        // add to the columns from this joined table
        $this->col($cols);
        return $this;
    }

    /**
     * Adds a JOIN table and columns to the query.
     * @param string $name The table name.
     * @param string $cond Join on this condition.
     * @param array|string $cols The columns to select from the joined table.
     * @return Zend_Db_Select This Zend_Db_Select object.
     */
    function join($name, $cond, $cols = null)
    {
        return $this->_join(null, $name, $cond, $cols);
    }


    /**
     * Add a LEFT JOIN table and colums to the query
     *
     * @param string $name The table name.
     * @param string $cond Join on this condition.
     * @param array|string $cols The columns to select from the joined table.
     * @return Zend_Db_Select This Zend_Db_Select object.
     */
    function joinLeft($name, $cond, $cols = null) 
    {
        return $this->_join('left', $name, $cond, $cols);
    }
	
	/**
     * Add a RIGTH JOIN table and colums to the query
     *
     * @param string $name The table name.
     * @param string $cond Join on this condition.
     * @param array|string $cols The columns to select from the joined table.
     * @return Zend_Db_Select This Zend_Db_Select object.
     */
    function joinRight($name, $cond, $cols = null) 
    {
        return $this->_join('right', $name, $cond, $cols);
    }

    /**
     * Add an INNER JOIN table and colums to the query
     *
     * @param string $name The table name.
     * @param string $cond Join on this condition.
     * @param array|string $cols The columns to select from the joined table.
     * @return Zend_Db_Select This Zend_Db_Select object.
     */
    function joinInner($name, $cond, $cols = null) 
    {
        return $this->_join('inner', $name, $cond, $cols);
    }


    /**
     * Adds a WHERE condition to the query by AND.
     *
     * If a value is passed as the second param, it will be quoted
     * and replaced into the condition wherever a question-mark
     * appears.
     *
     * Array values are quoted and comma-separated.
     *
     * <code>
     * // simplest but non-secure
     * $select->where("id = $id");
     *
     * // secure
     * $select->where('id = ?', $id);
     *
     * // equivalent security with named binding
     * $select->where('id = :id');
     * $select->bind('id', $id);
     * </code>
     *
     * @param string $cond The WHERE condition.
     * @param string $val A single value to quote into the condition.
     * @return void
     */
    function where($cond)
    {
		if(is_array($cond)) {
			foreach( $cond as $_ ) 
				$this->where($_);
			return $this;
		}
		
		$args = func_get_args();
		$cond = $this->quote_func_args($cond, $args);
		
        if ($this->_parts['where']) {
            $this->_parts['where'][] = "AND ($cond)";
        } else {
            $this->_parts['where'][] = "($cond)";
        }

        return $this;
    }

    /**
     * Adds a WHERE condition to the query by OR.
     *
     * Otherwise identical to where().
     *
     * @param string $cond The WHERE condition.
     * @param string $val A value to quote into the condition.
     * @return void
     *
     * @see where()
     */
    function orWhere($cond)
    {
		$args = func_get_args();
		$cond = $this->quote_func_args($cond, $args);

        if ($this->_parts['where']) {
            $this->_parts['where'][] = "OR ($cond)";
        } else {
            $this->_parts['where'][] = "($cond)";
        }

        return $this;
    }


    /**
     * Adds grouping to the query.
     *
     * @param string|array $spec The column(s) to group by.
     * @return void
     */
    function group($spec)
    {
        if (is_string($spec)) {
            $spec = explode(',', $spec);
        } else {
            settype($spec, 'array');
        }

        foreach ($spec as $val) {
            $this->_parts['group'][] = trim($val);
        }

        return $this;
    }


    /**
     * Adds a HAVING condition to the query by AND.
     *
     * If a value is passed as the second param, it will be quoted
     * and replaced into the condition wherever a question-mark
     * appears.
     *
     * Array values are quoted and comma-separated.
     *
     * <code>
     * // simplest but non-secure
     * $select->having("COUNT(id) = $count");
     *
     * // secure
     * $select->having('COUNT(id) = ?', $count);
     *
     * // equivalent security with named binding
     * $select->having('COUNT(id) = :count');
     * $select->bind('count', $count);
     * </code>
     *
     * @param string $cond The HAVING condition.
     * @param string $val A single value to quote into the condition.
     * @return void
     */
    function having($cond)
    {
		$args = func_get_args();
		$cond = $this->quote_func_args($cond, $args);

        if ($this->_parts['having']) {
            $this->_parts['having'][] = "AND ($cond)";
        } else {
            $this->_parts['having'][] = "($cond)";
        }

        return $this;
    }


    /**
     * Adds a HAVING condition to the query by OR.
     *
     * Otherwise identical to orHaving().
     *
     * @param string $cond The HAVING condition.
     * @param string $val A single value to quote into the condition.
     * @return void
     *
     * @see having()
     */
    function orHaving($cond)
    {
		$args = func_get_args();
		$cond = $this->quote_func_args($cond, $args);

        if ($this->_parts['having']) {
            $this->_parts['having'][] = "OR ($cond)";
        } else {
            $this->_parts['having'][] = "($cond)";
        }

        return $this;
    }

  /**
     * whereSearch
     *
     * @param string|array $spec The column(s) and direction to order by.
     * @return void
     */
	 
	function whereSearch($column, $string)
    {
		$array = is_array($column) ? $column : array($column);
		$strings = explode("+",$string);
		foreach($strings as $string) 
		{
        	$string = '%'.str_replace(' ', '%', strtolower(trim($string))).'%';
			$final = array();
			reset($array);
			foreach($array as $column)
				$final[] = $this->quoteInto("LOWER($column) LIKE ?", $string);
			$this->where(implode(" OR ",$final));
		}
		return true;		
    }
	
 	/**
     * havingSearch
     *
     * @param string|array $spec The column(s) and direction to order by.
     * @return void
     */
	function havingSearch($column, $string)
    {
		$array = is_array($column) ? $column : array($column);
		$strings = explode("+",$string);
		foreach($strings as $string) 
		{
        	$string = '%'.str_replace(' ', '%', strtolower(trim($string))).'%';
			$final = array();
			reset($array);
			foreach($array as $column)
				$final[] = $this->quoteInto("LOWER($column) LIKE ?", $string);
			$this->having(implode(" OR ",$final));
		}
		return true;		
    }

    /**
     * Adds a row order to the query.
     *
     * @param string|array $spec The column(s) and direction to order by.
     * @return void
     */
    function order($spec, $first=true)
    {
        if (is_string($spec)) 
            $spec = explode(',', $spec);
		else
            settype($spec, 'array');
				
		if($first) 
			$spec = array_reverse($spec);
			
        // force 'ASC' or 'DESC' on each order spec, default is ASC.
        foreach ($spec as $key => $val) {
            $asc  = (strtoupper(substr($val, -4)) == ' ASC');
            $desc = (strtoupper(substr($val, -5)) == ' DESC');
            if (! $asc && ! $desc) { $val .= ' ASC'; }
			if( !$first )
				array_push ($this->_parts['order'], trim($val) );
			else
				array_unshift($this->_parts['order'], trim($val) );
        }

        return $this;
    }


    /**
     * Sets a limit count and offset to the query.
     *
     * @param int $count The number of rows to return.
     * @param int $offset Start returning after this many rows.
     * @return void
     */
    function limit($count = null, $offset = null)
    {
        $this->_parts['limitCount']  = (int) $count;
        $this->_parts['limitOffset'] = (int) $offset;
        return $this;
    }


    /**
     * Sets the limit and count by page number.
     *
     * @param int $page Limit results to this page number.
     * @param int $rowCount Use this many rows per page.
     * @return void
     */
    function limitPage($page, $rowCount)
    { 
        $page     = ($page > 0)     ? $page     : 1;
        $rowCount = ($rowCount > 0) ? $rowCount : 1;
        $this->_parts['limitCount']  = (int) $rowCount;
        $this->_parts['limitOffset'] = (int) $rowCount * ($page - 1);
        return $this;
    }


   /**
     * Adds to the internal table-to-column mapping array.
     *
     * @param string $tbl The table/join the columns come from.
     * @param string|array $cols The list of columns; preferably as
     * an array, but possibly as a comma-separated string.
     * @return void
     */
    function col($cols, $replace=false)
    {
        if (is_string($cols))
			$cols = explode(',', $cols); 
		else 
            settype($cols, 'array');
			
		$cols = array_map("trim", $cols);
	
		if( $replace ) 
			$this->_parts['cols'] = $cols;
		else
			$this->_parts['cols'] = array_merge( $this->_parts['cols'], $cols);

    }
	
	/* --- 
	* QUOTE
	*/
	function quote($value)
    {
        if (is_array($value)) {
            foreach ($value as $key=>$val) {
                $value[$key] = $this->quote($val);
            }
            return implode(', ', $value);
        } else {
            return $this->_adodb->Quote($value);
        }
    }
	
	/* --- 
	* quoteInto
	*/
	function quoteInto($text, $value)
    {
        return preg_replace('/\?/is', $this->quote($value), $text, 1);
    }
	
	/* --- 
	* quote_func_args
	*/
	function quote_func_args($cond, $func_args) {
		if(!count($func_args)) return $cond;
		unset($func_args[0]);
		foreach($func_args as $val) 
			$cond = $this->quoteInto($cond, $val);
		return $cond;
	}

}
