<?php
class Calendario extends AppModel {

	var $name = 'Calendario';
	var $primaryKey = 'cod_id';
	
	var $belongsTo = array('Voluntario' => array('foreignKey' => 'cod_persona'),
						'Caso' => array('foreignKey' => 'cod_caso'));
       
}
?>
