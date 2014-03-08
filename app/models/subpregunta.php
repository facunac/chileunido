<?php
	
	class Subpregunta extends AppModel
	{
		var $name='Subpregunta'; // nombre del modelo
		var $primaryKey='cod_subpregunta';
		
		var $belongsTo=array('Dimension' => array('foreignKey' => 'cod_dimension'));
		//var $hasMany=array('Respuestaficha' => array('foreignKey' => 'cod_subpregunta'));
		
	}
?>
