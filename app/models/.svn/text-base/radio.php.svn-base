<?php
	
	class Radio extends AppModel
	{
		var $name='Radio'; 
		var $primaryKey='cod_radio';
		
		
		
		//funcion util para obtener los valores para los formularios
		function getAllAsArray(){
			
			$radios=$this->findAll("", "", "Radio.nom_radio asc", "", "", -1);
			$array_radios=array();
			foreach($radios as $v){
				$array_radios+=array($v['Radio']['cod_radio'] => $v['Radio']['nom_radio']);
			}
			return $array_radios;
		}
		
	}
?>