<?php 
class AcreatDB {  }
require_once(VENDORS . "/AdoDB/adodb.inc.php");
$GLOBALS["DB"] = ADONewConnection(DB_DRIVER);
$GLOBALS["DB"]->Connect(DB_HOST, DB_USER, DB_PASS, DB_BASE);
$GLOBALS["DB"]->SetFetchMode(ADODB_FETCH_ASSOC);
?>