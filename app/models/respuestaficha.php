<?php
	
	class Respuestaficha extends AppModel
	{
		var $name='Respuestaficha'; // nombre del modelo
		var $primaryKey='cod_respuesta';
		
		var $belongsTo=array('Subpregunta' => array('foreignKey' => 'cod_subpregunta'),
							 'Ficha' => array('foreignKey' => 'num_evento'),
							 'Seguimiento' => array('foreignKey' => 'num_evento')
							);		
	}
?>