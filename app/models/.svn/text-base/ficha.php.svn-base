<?php
	
	class Ficha extends AppModel
	{
		var $name='Ficha'; // nombre del modelo
		var $primaryKey='num_evento';
		var $belongsTo=array('Seguimiento' => array('foreignKey' => 'num_evento'),
							'Formulario' => array('foreignKey' => 'cod_formulario'));
		var $hasMany=array('Respuestaficha' => array('foreignKey' => 'cod_subpregunta'));
		
	}
?>