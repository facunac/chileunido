<?php
class Donarstack extends AppModel {

	var $name = 'Donarstack';
	var $primaryKey = 'cod_donarstack';
	var $belongsTo=array('Comuna' => array('foreignKey' => 'cod_comuna'));
  
	
	
	var $validate = array(
		'nom_rut' => '/([0-9]{6,8}[\-][0-9]{1})/',
		'nom_nombre' => '/^([a-zA-ZáÁéÉíÍóÓúÚñÑ]+)([\sa-zA-Z áÁéÉíÍóÓúÚñÑ])*$/',
		'nom_appa' =>'/^([a-zA-ZáÁéÉíÍóÓúÚñÑ]+)([\sa-zA-Z áÁéÉíÍóÓúÚñÑ])*$/',
		'nom_apma' => '/^([a-zA-ZáÁéÉíÍóÓúÚñÑ]+)([\sa-zA-Z áÁéÉíÍóÓúÚñÑ])*$/',
		'fec_nacimiento' => VALID_NOT_EMPTY,
		'nom_direccion' => '/^([a-zA-ZáÁéÉíÍóÓúÚñÑ]+)([\sa-zA-Z áÁéÉíÍóÓúÚñÑ0-9])*$/',
		'num_telefono1' => '/([0-9]{6,7})/',
	
		'num_monto' => VALID_NUMBER,
		//'nom_mediopago' => '/^([a-zA-ZáÁéÉíÍóÓúÚñÑ]+)([\sa-zA-Z áÁéÉíÍóÓúÚñÑ])*$/',
		'num_idpago' => VALID_NUMBER,
		);

		var $jsFeedback=array(	
			'nom_rut' => 'Ingrese el rut',	
			'nom_nombre' => 'Ingrese el nombre',
			'nom_appa' => 'Ingrese el Apellido Paterno',
			'nom_apma' => 'Ingrese el Apellido Materno',
			'fec_nacimiento' => 'Ingrese fecha',
			'nom_direccion' => 'Ingrese direccion',
			'num_telefono1' => 'Ingrese telefono1',
			'nom_mail' => 'Ingrese el mail',
			'num_monto' => 'Ingrese el monto',
		//	'nom_mediopago' => 'Ingrese el medio pago',
			'num_idpago' => 'Ingrese el numero del medio de pago');
	
	
	
}
?>