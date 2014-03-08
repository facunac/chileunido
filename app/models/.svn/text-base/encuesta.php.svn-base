<?php

class Encuesta extends AppModel
{
	var $name='Encuesta';
	var $primaryKey='id';
	var $useTable='encuestas';


	var $hasMany=array('PreguntaEncuesta' =>array(	'className'=>'PreguntaEncuesta',
			'order' => 'PreguntaEncuesta.numero ASC',
			'foreignKey' => 'encuesta_id'));

	var $validate = array ('titulo' => '/^.+$/',
			'fecha_inicio' => '/^\d{4}-\d{2}-\d{2}$/',
			'fecha_fin' => '/^\d{4}-\d{2}-\d{2}$/'
			);

	var $jsFeedback = array ('titulo' => 'El título no puede ser vacio',
			'fecha_inicio' => 'La fecha tiene que seguir el formato AAAA-MM-DD (por ejemplo 2009-10-18)',
			'fecha_fin' => 'La fecha tiene que seguir el formato AAAA-MM-DD (por ejemplo 2009-10-18)'
	);

}

?>
