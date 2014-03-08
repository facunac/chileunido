<?php
	
	class Diario extends AppModel
	{
		var $name='Diario'; 
		var $primaryKey='cod_diario';
		
		
		
		//funcion util para obtener los valores para los formularios
		function getAllAsArray(){
			
			$diarios=$this->findAll("", "", "Diario.nom_diario asc", "", "", -1);
			$array_diarios=array();
			foreach($diarios as $v){
				$array_diarios+=array($v['Diario']['cod_diario'] => $v['Diario']['nom_diario']);
			}
			return $array_diarios;
		}
		
	}
?>