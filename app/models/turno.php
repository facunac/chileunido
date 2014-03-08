<?php
class Turno extends AppModel {

	var $name = 'Turno';
	var $primaryKey = 'cod_turno';

	var $belongsTo = array('Voluntario' => array('foreignKey' => 'cod_voluntario'),
						   'Box' => array('foreignKey' => 'cod_box'),
						   'Caso' => array('foreignKey' => 'cod_caso'));

	var $hasOne = array();
	
	var $validate = array(
		'cod_voluntario' => VALID_NOT_EMPTY,
		'nom_dia' => VALID_NOT_EMPTY
	);
	
	var $jsFeedback = array(
		'cod_voluntario' => 'Seleccione un voluntario',
		'nom_dia' => 'Seleccione un día de la semana',
		'cod_box' => 'Seleccione un box'
	);

}
?>