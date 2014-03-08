<?php
class AppModel extends Model {

	function __construct($id = false, $table = null, $ds = null) {
      static $utf8Enabled = array();
      if (!isset($utf8Enabled[$this->useDbConfig])) {
         $db =& ConnectionManager::getDataSource($this->useDbConfig);
         if (low(get_class($db)) == 'dbomysql') {
            $db->execute('SET NAMES utf8');
         }
         $utf8Enabled[$this->useDbConfig] = true;
      }
      parent::__construct($id, $table, $ds);
   }

	function toDate($fecha){
		$fec=explode("-", $fecha);
		return $fec[2]."-".$fec[1]."-".$fec[0];
	}
	
	function getPossibleValues($colname){
		$tabla=$this->useTable;
		$sql="SHOW COLUMNS FROM $tabla LIKE '$colname'";
		$res=$this->query($sql);
		$valores=$res[0]['COLUMNS']['Type'];
		$valores=explode("','",preg_replace("/(enum|set)\('(.+?)'\)/","\\2",$valores));
		$retorno=array();
		foreach($valores as $v){
			$retorno+=array($v => str_replace("_", " ",$v));
		}
		return $retorno;
	}
}
?>