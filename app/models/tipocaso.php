<?php
	
	class Tipocaso extends AppModel
	{
		var $name='Tipocaso'; // nombre del modelo
		var $primaryKey='cod_tipocaso';
		
		var $belongsTo=array('Programa' => array('foreignKey' => 'cod_programa'));
		var $hasMany=array('Caso' => array('foreignKey' => 'cod_tipocaso'));
				
		//funcion util para obtener los valores para los formularios
		function getAllAsArray($cod_programa){
			$tipos=$this->findAllByCod_programa($cod_programa);
			$array_tipos=array();
			foreach($tipos as $v){
				$array_tipos+=array($v['Tipocaso']['cod_tipocaso'] => $v['Tipocaso']['nom_tipocaso']);
			}
			return $array_tipos;
		}
		
	}
?>
