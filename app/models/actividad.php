<?php
	
	class Actividad extends AppModel
	{
		var $name='Actividad'; // nombre del modelo
		var $primaryKey='cod_actividad';
		var $useTable='actividades';
		
		var $belongsTo=array('Programa' => array('foreignKey' => 'cod_programa'));
		var $hasOne=array('Formulario' => array('foreignKey' => 'cod_formulario'));
		
	}
?>