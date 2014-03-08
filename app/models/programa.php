<?php
	
	class Programa extends AppModel
	{
		var $name='Programa'; // nombre del modelo
		var $primaryKey='cod_programa';
		var $hasMany=array(	'Voluntario' => array('foreignKey' => 'cod_programa'),
							'Tipocaso' => array('foreignKey' => 'cod_programa'));
		
		var $validate=array('nom_programa'=> VALID_NOT_EMPTY,
							'num_maxllamadas'=> VALID_NUMBER,
							'num_maxvisitas'=> VALID_NUMBER,
							'num_frecuencia'=> VALID_NUMBER);
							
		var $jsFeedback=array('nom_programa' => 'Ingrese un Nombre',
							'num_maxllamadas'=> 'Ingrese un número máximo de llamadas valido',
							'num_maxvisitas'=> 'Ingrese un número máximo de visitas valido',
							'num_frecuencia'=> 'Ingrese una frecuencia valida');
							
		//funcion util para obtener los valores para los formularios
		function getAllAsArray(){
			$programas=$this->findAll("", "", "", "", "", -1);
			$array_programas=array();
			foreach($programas as $v){
				$array_programas+=array($v['Programa']['cod_programa'] => $v['Programa']['nom_programa']);
			}
			return $array_programas;
		}
		
	}
?>
