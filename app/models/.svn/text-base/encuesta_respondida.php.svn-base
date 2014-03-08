<?php

class EncuestaRespondida extends AppModel
    {
        var $name='EncuestaRespondida'; // nombre del modelo
        var $primaryKey='id';
        var $useTable='encuesta_respondidas';

        var $belongsTo= array('Encuesta' => array('className'=>'Encuesta','foreignKey' => 'encuesta_id'),
            'Voluntario'=>array('className'=>'Voluntario','foreignKey' => 'usuario_id'));
    }
?>
