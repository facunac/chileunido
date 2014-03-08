<?php
	
	class Seccion extends AppModel
	{
		var $name='Seccion'; // nombre del modelo
		var $primaryKey='cod_seccion';
		var $useTable='secciones';
		
		var $belongsTo=array('Formulario' => array('foreignKey' => 'cod_formulario'));
		var $hasMany=array('Pregunta' => array('foreignKey' => 'cod_seccion'));
		
	}
?>