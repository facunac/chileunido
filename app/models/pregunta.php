<?php
	
	class Pregunta extends AppModel
	{
		var $name='Pregunta'; // nombre del modelo
		var $primaryKey='cod_pregunta';
		
		var $belongsTo=array('Seccion' => array('foreignKey' => 'cod_seccion'));
		var $hasMany=array('Dimension' => array('foreignKey' => 'cod_pregunta'));
		
	}
?>