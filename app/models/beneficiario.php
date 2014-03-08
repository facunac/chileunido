<?php
	
	class Beneficiario extends AppModel
	{
		var $name='Beneficiario'; // nombre del modelo
		var $primaryKey='cod_persona';
		var $belongsTo=array('Persona' => array('foreignKey' => 'cod_persona'),
							 'Convenio' => array('foreignKey' => 'cod_convenio'));
		
		var $hasMany=array('Caso' => array('foreignKey' => 'cod_beneficiario', 'dependent' => true));

		var $validate=array(//'fec_ingreso' => '/[0-9]{2}[\-][0-9]{2}[\-][0-9]{2,4}$/i',
							'tip_sexo' => VALID_NOT_EMPTY,
							'tip_rolfamilia' => VALID_NOT_EMPTY);
		
		var $jsFeedback=array(//'fec_ingreso' =>'Ingrese la fecha correctamente',
							  'tip_sexo' => 'Ingrese el Sexo',
							  'tip_rolfamilia' => 'Ingrese rol familia');
							
		function getRolesFamilia()
		{
			$array_nombres=array();
			$ingresos1=$this->getColumnType('tip_rolfamilia');
			
			$off = strpos($ingresos1,"(");
			$enum = substr($ingresos1, $off+1, strlen($ingresos1)-$off-2);
			$values = explode(",",$enum);
			
			foreach($values as $v)
			{	
				
				$v1=strpos($v,"'");
				$v2=substr($v, $v1+1, strlen($v)-$v1-2);
				$array_nombres+=array($v2 => $v2);
			}

			return $array_nombres;
		}
	
	}
?>
