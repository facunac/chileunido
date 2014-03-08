<?php
class Socio extends AppModel {

        var $name = 'Socio';
        var $primaryKey = 'cod_socio';
        var $belongsTo=array('Comuna' => array('foreignKey' => 'cod_comuna'));
        
        var $validate = array(		       
		'nom_rut' => '/([0-9]{6,8}[\-][0-9]{1})/',
        'nom_nombre' => '/^([a-zA-ZáÁéÉíÍóÓúÚñÑ]+)([\sa-zA-Z áÁéÉíÍóÓúÚñÑ])*$/',
        'nom_appat' => '/^([a-zA-ZáÁéÉíÍóÓúÚñÑ]+)([\sa-zA-Z áÁéÉíÍóÓúÚñÑ])*$/',
        'nom_apmat' => '/^([a-zA-ZáÁéÉíÍóÓúÚñÑ]+)([\sa-zA-Z áÁéÉíÍóÓúÚñÑ])*$/',
      //  'fec_nacimiento' => '/[0-9]{4}[\-][0-9]{2}[\-][0-9]{2}$/',
        'nom_direccion' => '/^([0-9a-zA-Z áÁéÉíÍóÓúÚñÑ.,]+)*$/',
        'num_telefono1' => '/([0-9]{6,7})/'
		);
	
		var $jsFeedback=array(	
		'nom_rut' => 'Ingrese rut correctamente',
		'nom_nombre' => 'Ingrese nombre correctamente',
		'nom_appat' => 'Ingrese apellido paterno correctamente',
		'nom_apmat' => 'Ingrese apellido materno correctamente',
	//	'fec_nacimiento' => 'Ingrese la fecha de nacimiento correctamente',
		'nom_direccion' => 'Ingrese su direccion correctamente',
		'num_telefono1' => 'Ingrese solo numeros para el telefono de contacto 1 (entre 6 y 7 digitos)'			
		);
	
/*	
	
	nom_rut 	 	
	nom_nombre 
	nom_appat 	 	
	nom_apmat 		
	fec_nacimiento 
	nom_direccion 	
	num_telefono1 	
	num_telefono2 	
	nom_email 	
	
*/
}
?>
