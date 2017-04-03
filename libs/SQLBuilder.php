<?
class SQLBuilder
{   
	var $_type 		= "SELECT";
	var $_select 	= "*";
	var $_table 	= array("table");
	var $_where 	= array();
	var $_having 	= array();
	var $_join 		= array();
    var $_group 	= array();
	var $_limit 	= array(0,0);
	var $_data		= array();
	var $_order		= null;
	
	/* ---
	*
	*/
	function SQLBuilder($type = NULL, $table = NULL)
	{
		if($type) 	$this->setType($type);
		if($table) 	$this->setTable($table);
	}
	
	/* ---
	*
	*/
	function setType($type = "SELECT")
	{
		$this->_type = trim(strtoupper($type));
	}
	
	/* ---
	*
	*/
	function setSelect($cols = "*")
	{
		$this->_select = $cols;
	}
		
	/* ---
	*
	*/
	function addTable($table, $reset = false)
	{
		if($reset) $this->_table = array();
		$tables = explode(",",$table);
		foreach($tables as $table)
			$this->_table[] = $table;
	}
	function setTable($table) { return $this->addTable($table, true); }
	
	/* ---
	*
    */
    function addJoin($table, $where, $type='default')
    {
		$type = strtolower($type);
        if (!isset($this->_join[$type]) || !$this->_join[$type])
            $this->_join[$type] = array();
        $this->_join[$type][$table] = $where;
    }
	
	/* ---
	*
	*/
	function addData($col, $value = NULL, $masque = NULL)
	{
		if(is_array($col)) {
			foreach($col as $col_ => $value)
				$this->addData($col_, $value, $masque);
			return;
		}
		$this->_data[$col] = array($masque, $value);
	}
		
	/* ---
	*
	*/
	function addWhere($where, $condition='AND')
	{
		$condition = (strtoupper($condition) == "OR") ? strtoupper($condition) : "AND";
		$this->_where[] = array($where, $condition);
	}	
		
	/* ---
	*
	*/
	function addHaving($having, $condition='AND')
	{
		$condition = (strtoupper($condition) == "OR") ? strtoupper($condition) : "AND";
		$this->_having[] = array($having, $condition);
	}	
	
	/* ---
	*
	*/
	function addHavingSearch($column, $string, $condition='AND')
    {
		$array = is_array($column) ? $column : array($column);
        $string = addslashes('%'.str_replace(' ', '%', strtolower($string)).'%');
		$strings = array();
		foreach($array as $column)
			$strings[] = "LOWER($column) LIKE '$string'";
        $this->addHaving(implode(" OR ",$strings), $condition);
    }
	
	/* ---
	*
	*/
	function addOrder($orderCondition='', $desc = false, $first = false)
	{
		$_new = array($orderCondition,($desc === true || $desc == "DESC") ? 'DESC' : 'ASC');
		if(!$first)
			$this->_order[] = $_new;
		else 
			array_push_before($this->_order, $_new);			
	}
	
	/* ---
	*
	*/
	function addGroup()
	{
		$cols = func_get_args();
		foreach($cols as $col)
			$this->_group[] = $col;
	}
	
	/* ---
	*
	*/
	function setLimit()
    {
		$args = func_get_args();
		if(count($args) == 0) return;
		$from = (count($args) == 1) ? 0 : $args[0];
		$count = (count($args) == 1) ? $args[0] : $args[1];
		$this->_limit = array($from, $count);
    }
	
	//
    //  methods for building the query
    //
	
	/* ---
	* _prepareValue
	* Préparation d'une valeur pour son utilisation dans la requête
	* (UPDATE et INSERT)
    */
	
	function _prepareData($item)
    {
		$masque = $item[0];
		$valeur = $item[1];
		
		if(empty($masque)) $masque = "?";

		// ---
		$new = $valeur;
		$type = gettype($valeur);
		if($type == "string")
			$new = "'".addslashes($valeur)."'";
		elseif($type == 'double')
			$new = str_replace(',','.',$valeur); // locales fix so 1.1 does not get converted to 1,1
		elseif($valeur == 'boolean')
			$new = $valeur ? "TRUE" : "FALSE";
		elseif ($valeur === null)
			$new = "NULL";
		// ---
		
		return str_replace("?",$new,$masque);
	}
	
	/* ---
	*
    */
	function _buildSelect()
    {
		return $this->_select;
	}
	
	/* ---
	*
    */
	function _buildTable($onlyone = false)
    {
		$tables = ($onlyone) ? array($this->_table[0]) : $this->_table;
		return  "(".join($tables, ",").")";
	}
	
	
	/* ---
	*
    */
	function _buildFrom()
    {
        $join = $this->_join;
		// handle the standard join thingy
        if (isset($join['default']) && count($join['default'])) {
            foreach($join['default'] as $table => $condition) $this->_table[] = $table;
			unset($join['default']);
        }
        $from = "FROM " . $this->_buildTable();
	 
        if (!$join) return $from;
       
        // handle left/right/inner joins
        foreach (array('left', 'right', 'inner') as $joinType) {
            if (isset($join[$joinType]) && count($join[$joinType])) {
				foreach($join[$joinType] as $table => $condition)
					$from .= " ".strtoupper($joinType)." JOIN $table ON ($condition) ";
			}		
		}
                
        return $from;
    }
	
	/* ---
	*
    */
	function _buildWhere()
    {
		// insertion des condition des jointures classiques
		$join = $this->_join;
        if (isset($join['default']) && count($join['default'])) {
            foreach($join['default'] as $table => $condition) $this->addWhere($condition,'AND');
        }
		
		$where = "";
		foreach($this->_where as $infos)
			$where .= " " . ( $where ? trim($infos[1]) : "WHERE" ) . " ( " . $infos[0] . " ) ";
			
		return $where;
	}
	
	/* ---
	*
    */
	function _buildGroup()
    {
		if(!$this->_group) return false;
		return "GROUP BY " . implode(",",$this->_group);
	}
	
	/* ---
	*
	*/
	function _buildHaving()
    {	
		$having = "";
		foreach($this->_having as $infos)
			$having .= " " . ( $having ? trim($infos[1]) : "HAVING" ) . " ( " . $infos[0] . " ) ";
		return $having;
	}
	
	/* ---
	*
    */
	function _buildOrder()
    {
		if(!$this->_order) return false;
		$parts = array();
		foreach( $this->_order as $ordre)
			$parts[] = $ordre[0]." " . $ordre[1];	
		return "ORDER BY " . implode(",",$parts);
	}
	
	/* ---
	*
    */
	function _buildLimit()
    {
		if(!$this->_limit[0] && !$this->_limit[1])
			return false;
		if(!$this->_limit[0]) 
			unset($this->_limit[0]);
		return "LIMIT " . implode(",",$this->_limit);
	}
		
	/* ---
	*
	*/
	function _buildSelectQuery()
    {
		$SQL = "SELECT" 				. " ";
		$SQL .= $this->_buildSelect() 	. " ";
		$SQL .= $this->_buildFrom()		. " ";
		$SQL .= $this->_buildWhere()	. " ";
		$SQL .= $this->_buildGroup()	. " ";
		$SQL .= $this->_buildHaving()	. " ";
		$SQL .= $this->_buildOrder()	. " ";
		$SQL .= $this->_buildLimit()	. " ";
		return $SQL;
	}
	
	/* ---
	*
	*/
	function _buildUpdateQuery()
    {
		$SQL = "UPDATE" 						. " ";
		$SQL .= $this->_buildTable()			. " ";
		// --- DATA PART
		$PARTS = array();
		foreach($this->_data as $col => $data)
			$PARTS[] = $col . " = " . $this->_prepareData($data);
		$SQL .= "SET " . implode(",",$PARTS)	. " ";
		// ---
		$SQL .= $this->_buildWhere()			. " ";
		return $SQL;
	}
	
	
	/* ---
	*
	*/
	function build()
    {
		switch(strtoupper($this->_type))
		{
			// ---
			case "INSERT":
				$SQL = $this->_buildInsertQuery();
			break;
			// ---
			case "UPDATE":
				$SQL = $this->_buildUpdateQuery();
			break;
			// ---
			case "SELECT":
			default:
				$SQL = $this->_buildSelectQuery();
			break;
			// ---
		}
		$SQL = preg_replace("/\s+/"," ",$SQL);
		return $SQL;
	}
	function toString() { return $this->build(); }
}

function array_push_before(&$array) {
	if(!is_array($array)) $array = array();
	$params = func_get_args();
	unset($params[0]);
	$old = $array;
	$array = $params;
	foreach($old as $item) $array[] = $item;
}
/*
$test = new SQLBuilder("SELECT","communes");
$test->addJoin("region","communes.idRegion = region.idRegion","left");
$test->addWhere("test.test = 1");
$test->addWhereSearch("test.test", "test toust");
$test->addGroup("communes.idRegion");
$test->addOrder("communes.idRegion",true);
$test->setLimit(1);
//print_r($test);

$test = new SQLBuilder("UPDATE","communes");
$_R["idContenu"] = 1;
$_R["labelContenu"] = "test de --(q_sdç_qsdè'erzekrmlk')";
$test->addData($_R);
$test->addWhere("test.test = 1");
echo $test->build();
*/
?>