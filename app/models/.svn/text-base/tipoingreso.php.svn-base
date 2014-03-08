<?php
	
	class Tipoingreso extends AppModel
	{
		var $name='Tipoingreso'; // nombre del modelo
		var $primaryKey='cod_tipoingreso';
		var $hasMany=array('Caso' => array('foreignKey' => 'cod_tipoingreso'));
		
		
		//funcion util para obtener los valores para los formularios
		function getAllAsArray($medio=null){
			$filtro=array();
			if($medio!=null) $filtro=array("Tipoingreso.cod_medio" => $medio);
			
			$comunas=$this->findAll($filtro, "", "Tipoingreso.nom_tipoingreso asc", "", "", -1);
			$array_comunas=array();
			foreach($comunas as $v){
				$array_comunas+=array($v['Tipoingreso']['cod_tipoingreso'] => $v['Tipoingreso']['nom_tipoingreso']);
			}
			return $array_comunas;
		}
		
		function getMedio(){
			$regiones=$this->query("SELECT distinct tipoingresos.cod_medio, tipoingresos.nom_medio from tipoingresos");
			$array_regiones=array();
			foreach($regiones as $v){
				$array_regiones+=array($v['tipoingresos']['cod_medio'] => $v['tipoingresos']['nom_medio']);
			}
			return $array_regiones;
		}
		
	}
?>
