<?php

define( "USE_SQL_CALC_FOUND_ROWS", true );

/*
 * Acreat Framework
 */
uses("model/db/AcreatDBRow");
uses("model/db/AcreatDBRowset");

/**
 * Class for SQL table interface.
 *
 * @package    Zend_Db
 * @subpackage Table
 * @copyright  Copyright (c) 2005-2006 Zend Technologies Inc. (http://www.zend.com)
 * @license    Zend Framework License version 1.0
 */

class _AcreatModel
{
	var $_controller	= null;
    var $_db			= null;

	/* --- PUBLIC --- */
	var $_name;
	var $_table;
	var $_primary;
	var $_foreign;
	var $_label;
	var $_id;
	var $_headers 		= array();
	var $_aggregates	= array();
	var $_filter_cols 	= array();

	var $_select;
	var $_where;
	var $_order;
	var $_group;

	var $_parents 		= array();
	var $_childs 		= array();
	var $_joins			= array();
	/* --- / PUBLIC --- */

	var $_limit_join 	= array();
	var $SQL 			= null;
	var $_selectObj;
	var $_lastSelectObj;

	var $_models;
	var $_default 		= array();


  /*
     * Constructor
    * @parent : soit un contructeur, soit un autre model
     */
    function _AcreatModel(&$controller, $autoload=true, $options = null)
    {
		$this->_controller = &$controller;

		if(isset($this->_controller->db))
			$this->_db = $this->_controller->db;
        // continue with automated setup

		// CHARGEMENT DES OPTIONS EVENTUELLES
		if( is_array($options) ) {
			foreach( $options as $k=>$v)
				eval("\$this->_$k = \$v;");
		}

		if(isset($this->_parent)) 	$this->_parents = $this->_parent;
		if(isset($this->_child)) 	$this->_childs = $this->_child;
		if(isset($this->_join)) 	$this->_joins = $this->_join;

		// SETUP
		$this->SQL = $this->_db->select();
		$this->_setup($autoload);
    }

  /*
     * Clonage de l'objet
     */
	function _clone()
    {
		$class = get_class($this);
		$new_obj = new $class($this->_controller, false);
		$vars = get_object_vars ($this);
		foreach($vars as $var=>$value)
			$new_obj->$var = $value;
		return $new_obj;
    }

     /**
     * _error
     */
	function _error($msg) {
		return user_error("AcreatModel : " . $msg);
	}

    /**
     * Populate static properties for this table module.
     *
     * @return void
     */
    function _setup($autoload=true)
    {
        // get the database adapter

        if (!$this->_db && isset($GLOBALS["DB"]) ) {
			$this->_db = $GLOBALS["DB"];
        }

        // get the object name
		if( empty( $this->_name ) ) {
			if (!preg_match('/(.*)Model/i', get_class($this), $r))
			   die("AcreatModel::__construct() : Impossible de parser mon propre nom.");
			$this->_name = ucfirst($r[1]);
		}

		// get the table name
		if( empty( $this->_table ) ) {
			$this->_table = strtolower( $this->_name );
		}


		// get the table name
		if( empty( $this->_select ) ) {
			$this->_select = "*, ".$this->_name.".*";
		}

		if( $this->_primary === null) {
			$this->_primary = "id".$this->_name;
		}

		if( $this->_foreign === null) {
			$this->_foreign = $this->_primary;
		}

		if( $this->_group === null) {
			$this->_group = $this->_name.".".$this->_primary;
		}

        // label
		if( !$this->_label && $this->_primary) {
			$this->_label = preg_replace("/^[a-z]*/","label",$this->_primary);
		}

		if( $autoload )
			$this->load();

    }

  /**
    * Rattache les Models Parents et Enfants
    */
	function load($type = null)
	{
		$this->_models = array("_parent"=>array(), "_child"=>array());

		if( $this->_parents )
		{
			foreach( $this->_parents as $key=>$joinModel )
			{
				$options = array();
				if( is_string($key) ) {
					$options = $joinModel;
					$joinModel = $key;
					if(!is_array($options)) $options = array("name"=>$options);
				}

				if( $object = & $this->_loadModel($joinModel, false, $options)) {
					$name = $object->_name;
					$this->$name = $object;
					$this->_models["_parent"][] = $object;
				}
			}
		}

		if( $this->_childs )
		{
			foreach( $this->_childs as $key=>$joinModel )
			{
				$options = array();
				if( is_string($key) ) {
					$options = $joinModel;
					$joinModel = $key;
					if(!is_array($options)) $options = array("name"=>$options);
				}

				if( $object = & $this->_loadModel($joinModel, false, $options)) {
					$object->add_where($this->_where);
					$name = $object->_name;
					$this->$name = $object;
					$this->_models["_child"][] = $object;
				}
			}
		}

		if( $this->_headers ) {
			$new_headers = array();
			foreach( $this->_headers as $key=>$header ) {
				if( preg_match("/^model:(.*)/si", $header, $matches) ) {
					$model = & $this->_loadModel($matches[1]);
					if( $model && is_array($model->_headers) ) {
						foreach( $model->_headers as $childKey => $childName ) {
							if( !preg_match("/^(model:)/", $childName ) && !preg_match("/^_/", $childName) && ( array_search($childKey, $model->_aggregates) === false || array_search($childKey, $this->_aggregates) ) )
								$new_headers[$childKey] = $childName;
						}
					}
				}
				else
					$new_headers[$key] = $header;
			}
			$this->_headers = $new_headers;
		}

		return $this;
	}

  /**
    * Charge un objet d'un autre model
    */
	function &_loadModel($model, $autoload=false, $options=array())
	{
		$class = ucfirst($model) . "Model";
		$model = false;
		if(class_exists($class)) {
			$model =  new $class($this->_controller, $autoload, $options);
		} else {
			$tables = $this->_db->MetaTables();
			$exists = array_search($model, $tables);
			if( $exists !== false) {
				eval("class $class extends AcreatModel { }");
				$model =  $class($this->_controller, $autoload, $options);
			}
		}

		if(!$model) {
			$this->_error("Le modele '$class' n'existe pas");
			return false;
		}

		// Si le model est bon, on applique la limite de jointure de l'objet en cours
		//if($this->_limit_join)
			//$model->_limit_join = array_unique(array_merge($model->_limit_join, $this->_limit_join));

		//if($this->_limit_join)
			//$model->_limit_join = $this->_limit_join;

		if($this->_limit_join && !$model->_limit_join)
			$model->_limit_join = array_unique(array_merge($model->_limit_join, $this->_limit_join));

		/*print "<pre><hr>".$model->_name;
		print_r( $model->_limit_join );*/

		//$model->_limit_join = $this->_limit_join;
		//if(!in_array(strtolower($this->_name), $model->_limit_join))
			//array_push($model->_limit_join, $this->_name);

		/*
		// --- on m?ange les headers
		$_headers = $model->_headers;
		foreach($_headers as $key=>$header) {
			if(in_array($key,$model->_aggregates))
				unset($_headers[$key]);
		}
		$this->_headers = array_merge($_headers, $this->_headers);

		// --- on m?ange les headers
		$_headers = $this->_headers;
		foreach($_headers as $key=>$header) {
			if(in_array($key,$this->_aggregates))
				unset($_headers[$key]);
		}
		$model->_headers = array_merge($_headers, $model->_headers);
		*/

		return $model;
	}



    /**
     * Returns table information.
     *
     * @return array
     */
    function info()
    {
        return array(
            'name' => $this->_name,
            'table' => $this->_table,
            'primary' => $this->_primary,
        );
    }


    // -----------------------------------------------------------------
    //
    // Manipulation
    //
    // -----------------------------------------------------------------

    /**
     * Inserts a new row.
     *
     * Columns must be in underscore format.
     *
     * @param array $data Column-value pairs.
     * @param string $where An SQL WHERE clause.
     * @return int The last insert ID.
     */
    function insert($data)
    {
        $this->_db->AutoExecute($this->_table,$data,'INSERT');
        return $this->_db->Insert_ID();
    }

    /**
     * Updates existing rows.
     *
     * Columns must be in underscore format.
     *
     * @param array $data Column-value pairs.
     * @param string $where An SQL WHERE clause.
     * @return int The number of rows updated.
     */
    function update($data, $where)
    {
        return $this->_db->AutoExecute($this->_table,$data,'UPDATE', $where);
    }

    /**
     * Deletes existing rows.
     *
     * The WHERE clause must be in native (underscore) format.
     *
     * @param string $where An SQL WHERE clause.
     * @return int The number of rows deleted.
     */
    function delete($where) {
        return $this->_db->Execute('DELETE FROM '.$this->_table.' WHERE ' . $where);
    }


    // -----------------------------------------------------------------
    //
    // Retrieval
    //
    // -----------------------------------------------------------------

    /**
     * Fetches rows by primary key.
     *
     * @param scalar|array $val The value of the primary key.
     * @return array Row(s) which matched the primary key value.
     */
    function find($val=null)
    {
		if( $val===null && $this->_id )
			$val = $this->_id;

		if($val===null) return false;

        $val = (array) $val;
        $key = $this->_name.".".$this->_primary;
        if (count($val) > 1) {
            $where = array(
                "$key IN(?)" => $val,
            );
            $order = "$key ASC";
            return $this->fetchAll($where, $order);
        } else {
            $where = array(
                "$key = ?" => (isset($val[0]) ? $val[0] : ''),
            );
            return $this->fetchRow($where);
        }
    }

    /**
     * Fetches all rows.
     *
     * Honors the Zend_Db_Adapter fetch mode.
     *
     * @param string|array $where An SQL WHERE clause.
     * @param string|array $order An SQL ORDER clause.
     * @param int $count An SQL LIMIT count.
     * @param int $offset An SQL LIMIT offset.
     * @return mixed The row results per the Zend_Db_Adapter fetch mode.
     */
    function fetchAll($where = null, $order = null, $count = null, $offset = null)
    {
        return new AcreatDBRowset(array(
            'db'    => $this->_db,
            'model' => $this,
            'data'  => $this->_fetch('GetAll', $where, $order, $count, $offset),
        ));
    }

    /**
     * Fetches one row.
     *
     * Honors the Zend_Db_Adapter fetch mode.
     *
     * @param string|array $where An SQL WHERE clause.
     * @param string|array $order An SQL ORDER clause.
     * @param int $count An SQL LIMIT count.
     * @param int $offset An SQL LIMIT offset.
     * @return mixed The row results per the Zend_Db_Adapter fetch mode.
     */
    function fetchRow($where = null, $order = null)
    {
		$data = $this->_fetch('GetRow', $where, $order, 1);
		if(!$data) return false;
        return new AcreatDBRow(array(
            'db'    => $this->_db,
            'model' => $this,
            'data'  => $data,
        ));
    }

    /**
	* Fetches one col.
	*/
    function fetchCol($where = null, $order = null, $count = null, $offset = null)
    {
        return new AcreatDBRowset(array(
            'db'    => $this->_db,
            'model' => $this,
            'data'  => $this->_fetch('GetCol', $where, $order, $count, $offset),
        ));
    }

    /**
	* Fetches one row-col.
	*/
    function fetchOne($where = null, $order = null)
    {
		return $this->_fetch('GetOne', $where, $order, 1);
    }
    /**
     * Fetches a new blank row (not from the database).
     *
     * @return AcreatDBRow
     */
    function fetchNew()
    {
        return new AcreatDBRow(array(
            'db'    => $this->_db,
            'model' => $this,
            'data'  => $this->_default,
        ));
    }

	/**
	* getSelect
	*/
	function getSelect()
	{
		if( $this->_selectObj )
			return $this->_selectObj;

		// selection tool
        $select = $this->_db->select();
		$select->initFromObject($this->SQL);

		$from = $this->_select;
		if( USE_SQL_CALC_FOUND_ROWS )
			$from = "SQL_CALC_FOUND_ROWS " . $from;

        // the FROM clause
        $select->from($this->_table.' AS '.$this->_name, $from);

		// the JOIN clause : PARENTS, ENFANTS, JOINS
		$this->_joinModel($select);
		foreach($this->_joins as $key => $value) { $select->joinLeft($key, $value); }

		if( $this->_group )
			$select->group($this->_group);

        // the WHERE clause
		if( $this->_where )
			$select->where($this->_where);

		if( $this->_order )
			$select->order($this->_order);

		$this->_selectObj = &$select;

		return $this->_selectObj;
	}

	/**
	*  limitSelect
	*/
	function limitSelect($select = null)
	{
		if( $select !== null )
			$this->_select = $select;

		$args = func_get_args();
		if( count($args) > 1 ) {
			unset($args[0]);
			$this->_limit_join = $args;
		}

		$this->load();
	}

	/**
	* _joinModel
	*/
	function _joinModel(&$select, $joined = null, $limit_join=false)
	{
		if(!$limit_join) $limit_join = $this->_limit_join;
		if( $joined == null ) $joined = array($this->_name);
		if( !$this->_models ) $this->load();
		$limit_join = array_map("strtolower", $limit_join);

		$next = array();

		foreach( $this->_models as $type => $models) {
			foreach( $models as $model ) {
				if( array_search($model->_name, $joined) !== false ) 	continue;
				if($just_type !== null && $just_type !=  $type) 		continue;
				if( count($limit_join) && !in_array(strtolower($model->_name), $limit_join) ) continue;

				switch( $type ) {
					/* --- */
					case "_parent":
						$select->joinLeft($model->_table.' AS '.$model->_name, $model->_name.".".$model->_primary."=".$this->_name.".".$model->_foreign);
						if( $model->_where )
							$select->where($model->_where);
					break;
					/* --- */
					case "_child":
						$select->joinLeft($model->_table.' AS '.$model->_name, $model->_name.".".$this->_primary."=".$this->_name.".".$this->_foreign);
					break;
					/* --- */
				}

				array_push($joined, $model->_name);
				$next[] = array($model, $type);
			}
		}

		foreach( $next as $model ) {
			$model[0]->_joinModel($select, $joined, $limit_join);
		}
	}



    /**
     * Support method for fetching rows.
     *
     * @param string $type Whether to fetch 'all' or 'row'.
     * @param string|array $where An SQL WHERE clause.
     * @param string|array $order An SQL ORDER clause.
     * @param int $count An SQL LIMIT count.
     * @param int $offset An SQL LIMIT offset.
     * @return mixed The row results per the Zend_Db_Adapter fetch mode.
     */
    function _fetch($method, $where = null, $order = null, $count = null, $offset = null)
    {
		// selection tool
        $select = $this->_db->select();
		$select->initFromObject( $this->getSelect() );

        $where = (array) $where;
        foreach ($where as $key => $val) {
            // is $key an int?
            if (is_int($key)) {
                // $val is the full condition
                $select->where($val);
            } else {
                // $key is the condition with placeholder,
                // and $val is quoted into the condition
                $select->where($key, $val);
            }
        }

        // the ORDER clause
		if( $order ) {
			if(!is_array($order)) $order = array($order);
			$select->order($order, true);
		}

        // the LIMIT clause
        if($count)
			$select->limit($count, $offset);

        // return the results
		$this->_lastSelectObj = $select;
        return $this->_db->$method($select->toString());
    }

	 /**
     * Support method for fetching rows.
     *
     * @param string $type Whether to fetch 'all' or 'row'.
     * @param string|array $where An SQL WHERE clause.
     * @param string|array $order An SQL ORDER clause.
     * @param int $count An SQL LIMIT count.
     * @param int $offset An SQL LIMIT offset.
     * @return mixed The row results per the Zend_Db_Adapter fetch mode.
     */
    function fetchPage($where = null, $order = null, $rowCount = null, $page = 1)
    {
		$page     = ($page > 0)     ? $page     : 1;
        $rowCount = ($rowCount > 0) ? $rowCount : 1;

        $limitCount  = (int) $rowCount;
        $limitOffset = (int) $rowCount * ($page - 1);

		$RowSet = $this->fetchAll($where, $order, $limitCount, $limitOffset);

		if( USE_SQL_CALC_FOUND_ROWS )
			$nbTotal = $this->_db->GetOne("SELECT FOUND_ROWS()");
		else {
			$sql = $this->_lastSelectObj;
			$sql->limit(null);
			$sql = $sql->toString();
			$rs = $this->_db->Execute($sql);
			$nbTotal = $rs->_numOfRows;
		}

		$maxPage = ceil($nbTotal/$rowCount);
		if( $page > $maxPage && $nbTotal > 0)
			return $this->fetchPage($rowCount, $maxPage, $order);

		$RowSet->_page = array('maxRecordCount' => $nbTotal, 'absolutePage' => $page, 'rowsPerPage' => $rowCount, 'maxPage' => $maxPage);
		return $RowSet;
	}


	 /**
     * Permet de retourner un menu
     *
     * @param string $type Whether to fetch 'all' or 'row'.
     * @param string|array $where An SQL WHERE clause.
     * @param string|array $order An SQL ORDER clause.
     * @param int $count An SQL LIMIT count.
     * @param int $offset An SQL LIMIT offset.
     * @return mixed The row results per the Zend_Db_Adapter fetch mode.
     */
    function fetchMenu($name=null, $value=0, $blank1stItem=true, $multiple_select=false, $moreAttr="")
    {
		if(!$name) $name = $this->_primary;
		$select = $this->getSelect();
		$select->_parts['cols'] = array($this->_label, $this->_primary);
		$select->_parts['join'] = array();
		$select->_parts['group'] = array();
		$select->_parts['where'] = array();
		$rs = $this->_db->query($select->toString());
		if(!$rs) return false;
		return $rs->GetMenu2($name, $value, $blank1stItem, $multiple_select, 0, "id='".$name."' ".$moreAttr);
	}


    /**
     * Fetches rows by primary key.
     *
     * @param scalar|array $val The value of the primary key.
     * @return array Row(s) which matched the primary key value.
     */
    function findFromUrlParam($id = "id", $var=false)
    {
		$OCCURENCE = false;
		$id = ( !empty($this->_controller->params["url"][$id]) ? $this->_controller->params["url"][$id] : 0);

		if(!empty($id))
			$OCCURENCE = $this->find($id);
		if(!$OCCURENCE)
			$OCCURENCE = $this->fetchNew();

		if( $var)
			$this->_controller->set($var,$OCCURENCE);

		return $OCCURENCE;
    }

	/**
     * addWhere
     * add where condition
     */
	function add_where($cond)
	{
		if(empty($cond)) return;
		if( is_array($cond) ) { foreach($cond as $condition) $this->add_where($condition); return; }
		if(!$this->_where) 				$this->_where = array();
		if( !is_array($this->_where)) 	$this->_where = array($this->_where);
		$this->_where[] = $cond;
		return;
	}

	/* -------------------------------------------------------------------------
	* METHODE DE FILTRAGE DES DONNEES
	* --------------------------------------------------------------------------/

	/**
     * filter
     * Ajour au SELECT un filtre par colonne
     */
	function filter($col, $value="")
	{
		if(empty($value)) return false;
		$select = $this->getSelect();
		if( preg_match("/^\*/", $col ) )
			$select->having( substr($col,1)." = ?", $value);
		else
			$select->where("$col = ?", $value);
		return true;

	}

	/**
     * filterAll
     * Ajour au SELECT un filtre par texte.
     */
	function filterAll($filtre="", $where=true)
	{
		if(empty($filtre)) return false;
		$select = $this->getSelect();

		$filter_keys = $this->_headers;

		if( $where ) {
			foreach( $filter_keys as $key => $value) {
				if( array_search($key, $this->_aggregates) !== false )
					unset($filter_keys[$key]);
			}
		}

		// DATES : remplacement de dd/mm/yyyy par yyyy-mm-dd
		$filtre = preg_replace("/(\d{1,2})\/(\d{1,2})\/(\d{4})/", "$3-$2-$1", $filtre);
		$parts = explode(" ", $filtre);

		foreach( $parts as $part) {
			if( preg_match("/(.+)(=|<|>|!=|<>|<=|>=|~)(.+)/", $part, $matches) && isset($this->_filter_cols[$matches[1]]) )
			{
				// Recherche par raccourci de colonne
				$col =  $this->_filter_cols[$matches[1]];
				if($matches[2] == "=") { $matches[2] = "LIKE"; }
				if(preg_match("/NULL?/",$matches[3])) { $matches[3] = " "; }
				$requete = sprintf("%s %s ?", $col, $matches[2]);

				// DATES
				//if( preg_match("/(\d{2})\/(\d{2})\/(\d{4})/", $matches[3], $date_matches)) {
					//$matches[3] = sprintf("%s-%s-%s", $date_matches[3], $date_matches[2], $date_matches[1]);
				//}

				// NULL
				if( $matches[2] == "LIKE" && $matches[3] == " " ) {
					$requete = sprintf("%s IS NULL OR %s = ?", $col, $col);
				}

				// OUI/NOM
				if( $matches[3] == "oui" || $matches[3] == "non" ) {
					$matches[3] = ($matches[3] == "oui");
				}

				if( array_search($col, $this->_aggregates) !== false )
					$select->having( $requete, $matches[3]);
				else
					$select->where( $requete, $matches[3]);
			}
			else
			{
				if( $where )
					$select->whereSearch(array_keys($filter_keys), $part);
				else
					$select->havingSearch(array_keys($filter_keys), $part);
			}
		}
		$this->_selectObj = $select;
		return true;
	}
}
