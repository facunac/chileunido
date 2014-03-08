<?php

	class Noticia extends AppModel
	{
		var $name='Noticia'; // nombre del modelo
		var $primaryKey='cod_noticia';
		var $useTable='noticias';

		var $belongsTo=array('Voluntario' => array('foreignKey' => 'cod_persona'));
/*
		function beforeValidate () {
			$date_tokens = explode ('-', $this->data['Noticia']['fecha_creacion']);
			if (!checkdate($date_tokens[1], $date_tokens[2], $date_tokens[0])) {
				$this->invalidate('Noticia/fecha_creacion');
				return false;
			}

			return true;
		}
*/
		var $validate = array (
				'titulo' => '/^.+$/',
				'bajada' => '/.+/',
				'contenido' => '/.+/'
            //,
				//'fecha_creacion' => '/^\d{4}-\d{2}-\d{2}( \d{2}:\d{2}:\d{2})?$/' // Additional checks are performed in the controller
				);

		var $jsFeedback = array (
				'titulo' => 'El titulo no puede ser vacío',
				'bajada' => 'La bajada no puede ser vacia',
				'contenido' => 'El contenido no puede ser vacío',
				'fecha_creacion' => 'La fecha tiene que seguir el formato AAAA-MM-DD o AAAA-MM-DD HH:MM (por ejamplo \"2009-10-18 20:43\")'
				);

	}
?>
