<?php

class RespuestaEncuesta extends AppModel
{
	var $name='RespuestaEncuesta'; // nombre del modelo
	var $primaryKey='id';
	var $useTable='respuesta_encuestas';

	//var $belongsTo= array('PreguntaEncuesta' => array('className'=>'PreguntaEncuesta','foreignKey' => 'pregunta_encuesta_id'), 'Voluntario'=>array('className'=>'Voluntario','foreignKey' => 'cod_persona'));

	// var $hasMany = array('RespuestaMultiple' =>array('className'=>'RespuestaMultiple', 'order' => 'ranking DESC', 'foreignKey' => 'respuesta_encuesta_id'));

	var $validate = array ('fecha' => '/^\d{4}-\d{2}-\d{2}$/');
	var $jsFeedback = array ('fecha' => 'La fecha tiene que seguir el formato AAAA-MM-DD (por ejemplo "2009-10-18")');
}

?>
