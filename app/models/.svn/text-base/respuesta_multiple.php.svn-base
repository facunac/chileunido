<?php

class RespuestaMultiple extends AppModel
{
	var $name='RespuestaMultiple'; // nombre del modelo
	var $primaryKey='id';
	var $useTable='respuesta_multiples';

	var $belongsTo= array('RespuestaEncuesta' => array('className'=>'RespuestaEncuesta','foreignKey' => 'respuesta_encuesta_id'),
		'Opcion' => array('className'=>'Opcion','foreignKey' => 'opcion_id'));

}
?>
