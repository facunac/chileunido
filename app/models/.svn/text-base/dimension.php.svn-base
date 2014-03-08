<?php
	
	class Dimension extends AppModel
	{
		var $name='Dimension'; // nombre del modelo
		var $primaryKey='cod_dimension';
		var $useTable='dimensiones';
		
		var $belongsTo=array('Pregunta' => array('foreignKey' => 'cod_pregunta'));
		var $hasMany=array('Subpregunta' => array('foreignKey' => 'cod_dimension'));
		
	}
?>