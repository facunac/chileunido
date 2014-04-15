<?php
	
	class Creencia extends AppModel
	{
		var $name='Creencia'; // nombre del modelo
		var $primaryKey='cod_creencia';
		var $hasMany=array('Persona' => array('foreignKey' => 'cod_creencia'));
		
		
		//funcion util para obtener los valores para los formularios
		function getAllAsArray(){
			$creencias=$this->findAll(array(), "", "Creencia.nom_creencia asc", "", "", -1);
			$array_creencias=array();
			foreach($creencias as $v){
				if($v['Creencia']['cod_creencia']!=0 && $v['Creencia']['cod_creencia']!=1)
					$array_creencias+=array($v['Creencia']['cod_creencia'] => $v['Creencia']['nom_creencia']);
			}
			foreach($creencias as $v){
				if($v['Creencia']['cod_creencia']==1)
					$array_creencias+=array($v['Creencia']['cod_creencia'] => $v['Creencia']['nom_creencia']);
			}
                        foreach($creencias as $v){
                                if($v['Creencia']['cod_creencia']==0)
                                        $array_creencias+=array($v['Creencia']['cod_creencia'] => $v['Creencia']['nom_creencia']);
                        }			
			return $array_creencias;
		}
						
	}
?>
