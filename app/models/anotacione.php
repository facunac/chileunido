<?php


class Anotacione extends AppModel {

	var $name = 'Anotacione';
	var $primaryKey = 'cod_anotacion';
	
	var $validate = array(
		'fecha_inicio' => VALID_NOT_EMPTY,
		'fecha_termino' => VALID_NOT_EMPTY,
		'comentario' => VALID_NOT_EMPTY
		);

	var $jsFeedback=array(	
		'fecha_inicio' => 'Ingrese fecha de inicio',
		'fecha_termino' => 'Ingrese fecha de termino',
		'comentario' => 'Ingrese comentario'
		);
}
?>