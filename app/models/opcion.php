<?php

    class Opcion extends AppModel
    {
        var $name='Opcion'; // nombre del modelo
        var $primaryKey='id';
        var $useTable='opciones';

        var $belongsTo=array('Preguntaencuesta' =>array('className'=>'Preguntaencuesta','foreignKey' => 'pregunta_id'));
        var $validate = array ('titulo' => '/^.+$/');
        var $jsFeedback = array ('titulo' => 'El titulo no puede ser vacio');
		
		
    }

?>
