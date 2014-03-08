<?php
	
	class Perfil extends AppModel
	{
		var $name='Perfil'; // nombre del modelo
		var $useTable='perfiles';
		var $primaryKey='cod_perfil';
		var $hasMany=array('Permisoperfil' => array('foreignKey' => 'cod_perfil'));
		
		//funcion util para obtener los valores para los formularios
		function getAllAsArray(){
			$perfiles=$this->findAll();
			$array_perfiles=array();
			foreach($perfiles as $v){
				$array_perfiles+=array($v['Perfil']['cod_perfil'] => $v['Perfil']['nom_perfil']);
			}
			return $array_perfiles;
		}
		
	}
?>
