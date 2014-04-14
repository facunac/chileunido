<?php
	
	class Persona extends AppModel
	{
		var $name='Persona'; // nombre del modelo
		var $primaryKey='cod_persona';
		var $belongsTo=array('Comuna' => array('foreignKey' => 'cod_comuna'), 'Creencia'=>array('foreignKey'=>'cod_creencia'));
		var $hasOne=array('Voluntario' => array('foreignKey' => 'cod_persona'),// 'Creencia'=>array('foreignKey'=>'cod_creencia'),
					'Beneficiario' => array('foreignKey' => 'cod_persona', 'dependent' => true));
		var $hasMany=array('Comentario' => array('className'=>'Comentario',
												'order' => 'Comentario.fec_creado DESC',
												'limit' => '10',
												'foreignKey' => 'cod_persona'
												));
		//'nom_rut' => '/^(\d{6,8})-([K|0-9])$/i'
		//'fec_nacimiento' => '/[0-9]{2}[\-\/\.][0-9]{2}[\-\/\.][0-9]{2,4}$/i'
		//'nom_apmat' => VALID_NOT_EMPTY,
		///[0-9]{6,7}$/i
		///[a-zA-Z]{1,50}$/i
		
		//[Gabriela] Se ha sacado la validaciï¿½n del rut mientras se arreglan unos problemas con los forms
		var $validate=array(//'nom_rut' => VALID_NOT_EMPTY,
							'nom_nombre' => "/(^([a-zA-Zï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½]+)([\sa-zA-Z ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½])*$)/",
							//'nom_appat' => "/(([a-zA-Z ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½]+)([\sa-zA-Z ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½])*$)|(^$)/",
							//'nom_apmat' => "/(([a-zA-Z ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½]*)$)|(^$)/",
							//'fec_nacimiento' => '/([0-9]{2}[\-][0-9]{2}[\-][0-9]{2,4}$)|(^$)/i',
							//'cod_comuna' => VALID_NOT_EMPTY,
							//'num_telefono1' => '/(([0-9]{1,3}[\-][0-9]{6,7})$)|(^$)/',
							//'num_telefono1_pre' => '/(^([0-9]{1,3})$)|(^$)/',
							'num_telefono1_post' => '/([0-9]{6,8})/',
							'num_telefono2_post' => '/([0-9]{6,8})|(^$)/'
							//'nom_email' => "/([xA1-xFEa-z0-9_.-]+)@([xA1-xFEa-z0-9_-]+.[xA1-xFEa-z0-9-._-]+[.]*[a-z0-9]??[xA1-xFEa-z0-9=]*)|(^$)/"
							);
		
		var $jsFeedback=array(//'nom_rut' => 'Ingrese un RUT vÃ¡lido (sin puntos y con guiÃ³n)',
							'nom_nombre' => 'Ingrese el nombre sin numeros ni simbolos',
							//'nom_appat' => 'Ingrese el apellido paterno sin numeros ni simbolos',
							//'nom_apmat' => 'Ingrese un apellido materno valido',
							//'fec_nacimiento' => 'Ingrese la fecha correctamente',
							//'cod_comuna' => 'Ingrese la comuna',
							//'num_telefono1' => 'Ingrese el telefono correctamente',
							//'num_telefono1_pre' => 'Ingrese el codigo de ciudad del telefono con numeros',
							'num_telefono1_post' => 'Ingrese el telefono 1 con un numero adecuado de simbolos numericos',
							'num_telefono2_post' => 'Ingrese el telefono 2 con un numero adecuado de simbolos numericos'
							//'nom_email' => 'Ingrese un e-mail valido'
							);
		
		function beforeSave() 
		{ 
			//print_r($this->data);
			//die();
			// Get month day and year from date string 
			if(isset($this->data['Persona']['fec_nacimiento'])&& $this->data['Persona']['fec_nacimiento']){
				if($this->data['Persona']['fec_nacimiento']!=null&&$this->data['Persona']['fec_nacimiento']!="00-00-0000"){
					
					$timestamp = explode("-", $this->data['Persona']['fec_nacimiento']);
					$month = $timestamp[1];
					$day = $timestamp[0];
					$year = $timestamp[2];
					   
					$this->data['Persona']['fec_nacimiento'] = date('Y-m-d', mktime(null, 
																					null, 
																					null, 
																					$month, 
																					$day, 
																					$year));
				} 
			}
			return true; 
		}
		
		function toDate($fecha){
			$fec=explode("-", $fecha);
			if(count($fec)==3) return $fec[2]."-".$fec[1]."-".$fec[0];
			else return "";
		}
		
	}
?>
