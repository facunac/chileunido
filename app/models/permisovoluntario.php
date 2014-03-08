<?php
	
	class Permisovoluntario extends AppModel
	{
		var $name='Permisovoluntario'; // nombre del modelo
		var $primaryKey='cod_permisovoluntario'; //cod_permiso
		var $belongsTo=array('Voluntario' => array('foreignKey' => 'cod_voluntario'),
							'Permiso' => array('foreignKey' => 'cod_permiso'));
		
	}
?>
