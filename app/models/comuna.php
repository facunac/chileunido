<?php
	
	class Comuna extends AppModel
	{
		var $name='Comuna'; // nombre del modelo
		var $primaryKey='cod_comuna';
		var $hasMany=array('Persona' => array('foreignKey' => 'cod_comuna'));
		
		
		//funcion util para obtener los valores para los formularios
		function getAllAsArray($region=null){
			$filtro=array();
			if($region!=null) $filtro=array("Comuna.cod_region" => $region);
			
			$comunas=$this->findAll($filtro, "", "Comuna.nom_comuna asc", "", "", -1);
			$array_comunas=array();
			foreach($comunas as $v){
				$array_comunas+=array($v['Comuna']['cod_comuna'] => $v['Comuna']['nom_comuna']);
			}
			return $array_comunas;
		}
		
		function getRegiones(){
			$regiones=$this->query("SELECT distinct comunas.cod_region, comunas.nom_region from comunas");
			$array_regiones=array();
			foreach($regiones as $v){
				$array_regiones+=array($v['comunas']['cod_region'] => $v['comunas']['nom_region']);
			}
			return $array_regiones;
		}
		
	}
?>
