<?php
class Pago extends AppModel {

	var $name = 'Pago';
	var $primaryKey = 'cod_pago';
	var $belongsTo=array('Socio' => array('foreignKey' => 'cod_socio'));

	var $validate = array(
		'nom_rut' => '/([0-9]{6,8}[\-][0-9]{1})/',
		'nom_nombre' => '/^([a-zA-ZáÁéÉíÍóÓúÚñÑ]+)([\sa-zA-Z áÁéÉíÍóÓúÚñÑ])*$/',
		'nom_appa' =>'/^([a-zA-ZáÁéÉíÍóÓúÚñÑ]+)([\sa-zA-Z áÁéÉíÍóÓúÚñÑ])*$/',
		'nom_apma' => '/^([a-zA-ZáÁéÉíÍóÓúÚñÑ]+)([\sa-zA-Z áÁéÉíÍóÓúÚñÑ])*$/',
		'fec_inicio' => '/[0-9]{4}[\-][0-9]{2}[\-][0-9]{2}$/',
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
		'fec_inicio' => 'Ingrese fecha',
		'nom_direccion' => 'Ingrese direccion',
		'num_telefono1' => 'Ingrese telefono1',
		'num_monto' => 'Ingrese el monto',
		//'nom_mediopago' => 'Ingrese el medio pago',
		'num_idpago' => 'Ingrese el numero del medio de pago'
		);
	
/*	


	$this->data['Pago']['cod_socio']=$cod_socio;
	$this->data['Pago']['num_monto']=$miStack['Donarstack']['num_monto'];
	$this->data['Pago']['nom_mediopago']=$miStack['Donarstack']['nom_mediopago'];
	$this->data['Pago']['bit_ajusteipc']=$miStack['Donarstack']['bit_ajusteipc'];
*/
}
?>