<?php
	
	class Publicidad extends AppModel
	{
		var $name='Publicidad'; 
		var $primaryKey='cod_publicidad';
		var $useTable='publicidades';
		
		
		
		//funcion util para obtener los valores para los formularios
		function getAllAsArray(){
			
			$publicidades=$this->findAll("", "", "Publicidad.nom_publicidad asc", "", "", -1);
			$array_publicidades=array();
			foreach($publicidades as $v){
				$array_publicidades+=array($v['Publicidad']['cod_publicidad'] => $v['Publicidad']['nom_publicidad']);
			}
			return $array_publicidades;
		}
		
	}
?>