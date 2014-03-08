<?php
	
	class Sesion extends AppModel
	{
		var $name='Sesion'; // nombre del modelo
		var $useTable='sesiones';
		var $primaryKey='cod_voluntario'; // no es cod_sesion para poder relacionarlo con Voluntario
		var $belongsTo=array('Voluntario' => array('foreignKey' => 'cod_voluntario'));
		
	}
?>
