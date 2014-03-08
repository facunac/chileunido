<?php

    class Preguntaencuesta extends AppModel
    {
        var $name='Preguntaencuesta'; // nombre del modelo
        var $primaryKey='id';
        var $useTable='preguntas_encuestas';

        var $belongsTo=array('Encuesta' =>
                                    array(
                                                    'className'=>'Encuesta',
                                                    'foreignKey' => 'encuesta_id'
                                            )
							);
        var $hasMany=array('Opcion' => array(	'className'=>'Opcion',
                                                    'order' => 'Opcion.id ASC',
                                                    'foreignKey' => 'pregunta_id'
                                        ));
        var $validate = array ('titulo' => '/^.+$/');

        var $jsFeedback = array ('titulo' => 'El titulo no puede ser vacio');


    }

?>
