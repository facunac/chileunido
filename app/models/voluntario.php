<?php
	
	class Voluntario extends AppModel
	{
		var $name='Voluntario'; // nombre del modelo
		var $primaryKey='cod_persona';
		var $belongsTo=array('Persona' => array('foreignKey' => 'cod_persona'),
						'Programa' => array('foreignKey' => 'cod_programa'));
		var $hasMany=array('Sesion' => array('foreignKey' => 'cod_voluntario'),
							'Turno' => array('foreignKey' => 'cod_voluntario'),
							'Permisovoluntario' => array('foreignKey' => 'cod_voluntario'));

		var $validate=array('cod_programa' => VALID_NOT_EMPTY,
							'nom_login' => VALID_NOT_EMPTY,
							'pas_voluntario' => VALID_NOT_EMPTY);
		
		var $jsFeedback=array('cod_programa' => 'Ingrese un programa',
							'nom_login' => 'Ingrese un nombre de usuario',
							'pas_voluntario' => 'Debe ingresar una password');
		//var $hasOne = array('Role' => array('foreignKey' => 'cod_rol'));
		
		function getAllAsArray($cod_programa)
		{
			$voluntarios=$this->findAll("est_voluntario='Activo' AND Voluntario.cod_programa=".$cod_programa, "Persona.cod_persona, Persona.nom_nombre, Persona.nom_appat", "nom_appat", "", "", 1);
			$array_voluntarios=array();
			//$array_voluntarios+=array(0 => 'CUALQUIERA');
			//$array_voluntarios+=array($this->Session->read('cod_voluntario') => $this->Session->read('nom_voluntario'));
			foreach($voluntarios as $v){
				$array_voluntarios+=array($v['Persona']['cod_persona'] => $v['Persona']['nom_nombre'].' '.$v['Persona']['nom_appat']);
			}
			return $array_voluntarios;
		}
	}

?>
