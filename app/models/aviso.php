<?php

	class Aviso extends AppModel
	{
    var $name='Aviso'; // nombre del modelo
    var $primaryKey='cod_aviso';
    var $useTable='avisos';

    var $belongsTo = array('Voluntario' => array('foreignKey' => 'cod_persona'), 'Persona' => array('foreignKey' => 'cod_persona'));

    var $validate = array (
                            'titulo' => VALID_NOT_EMPTY,
                            'texto' => VALID_NOT_EMPTY,
                            'fecha_caducacion' => '/^\d{4}-\d{2}-\d{2}/'
                        );
    var $jsFeedback = array (
				'titulo' => 'El titulo no puede ser vacío',
				'texto' => 'El texto no puede ser vacio',
				'fecha_caducacion' => 'La fecha de caducacion no puede ser vacia y debe seguir el formato AAAA-MM-DD'
				);
}
?>