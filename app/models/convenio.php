<?php
class Convenio extends AppModel 
{

	var $name = 'Convenio';
	var $primaryKey = 'cod_convenio';
	var $validate = array(
		'nom_convenio' => '/^([a-zA-Zï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½]+)([\sa-zA-Z ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½])*$/',
		'fec_inicio' => '/[0-9]{2}[\-][0-9]{2}[\-][0-9]{2,4}$/i',
		'nom_responsable' => '/^([a-zA-Zï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½]+)([\sa-zA-Z ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½])*$/',
		'num_contacto' => '/([0-9]{6,7})/'
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $hasMany = array(
			'Beneficiario' =>
				array('className' => 'Beneficiario',
						'foreignKey' => 'cod_persona',
						'conditions' => '',
						'fields' => '',
						'order' => '',
						'limit' => '',
						'offset' => '',
						'dependent' => '',
						'exclusive' => '',
						'finderQuery' => '',
						'counterQuery' => ''
				),

	);
	
	var $jsFeedback=array(	'nom_convenio' => 'Ingrese el nombre del convenio sin numeros ni simbolos',
							'fec_inicio' => 'Ingrese la fecha correctamente',
							'nom_responsable' => 'Ingrese el nombre de contacto sin numeros ni simbolos',
							'num_contacto' => 'Ingrese solo numeros para el telefono de contacto (entre 6 y 7 digitos)');
		
	function beforeSave() 
	{ 
		// Get month day and year from date string 
		if(isset($this->data['Convenio']['fec_inicio'])){
			$timestamp = explode("-", $this->data['Convenio']['fec_inicio']);
			$month = $timestamp[1];
			$day = $timestamp[0];
			$year = $timestamp[2];
			if($day<32)
			{   
				$this->data['Convenio']['fec_inicio'] = date('Y-m-d', mktime(null, null, null, $month, $day, $year));
			} 
		}
		return true; 
	}
	
	function toDate($fecha){
		$fec=explode("-", $fecha);
		if(count($fec)==3) return $fec[2]."-".$fec[1]."-".$fec[0];
		else return "";
	}
		
	//funcion util para obtener los valores para las estadÃ­sticas
	function getAllAsArray(){
		$convenios=$this->findAll("", "", "", "", "", -1);
		$array_convenios=array();
		foreach($convenios as $v){
			$array_convenios+=array($v['Convenio']['cod_convenio'] => $v['Convenio']['nom_convenio']);
		}
		return $array_convenios;
	}		
		
}
?>