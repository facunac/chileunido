<?php
	
	class Permiso extends AppModel
	{
		var $name='Permiso'; // nombre del modelo
		var $primaryKey='cod_permiso';
		var $hasMany=array('Permisovoluntario' => array('foreignKey' => 'cod_permiso'),
							'Permisoperfil' => array('foreignKey' => 'cod_permiso'));
		
		//funcion util para obtener los valores para los formularios
		function getAllAsArray(){
			$permisos=$this->findAll();
			$array_permisos=array();
			foreach($permisos as $v){
				$array_permisos+=array($v['Permiso']['cod_permiso'] => $v['Permiso']['nom_permiso']);
			}
			return $array_permisos;
		}
	}
?>
