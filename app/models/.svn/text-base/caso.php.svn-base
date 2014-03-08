<?php
	
	class Caso extends AppModel
	{
		var $name='Caso'; // nombre del modelo
		var $primaryKey='cod_caso';
		
		var $belongsTo=array('Persona' => array('foreignKey' => 'cod_beneficiario'),
						'Voluntario' => array('foreignKey' => 'cod_voluntario'),
						'Beneficiario' => array('foreignKey' => 'cod_beneficiario'),
						'Tipocaso' => array('foreignKey' => 'cod_tipocaso'),
						'Tipoingreso' => array('foreignKey' => 'cod_tipoingreso'));
		
		var $hasMany=array('Seguimiento' => array('foreignKey' => 'cod_caso', 'dependent' => true),
						   'Turno' => array('foreignKey' => 'cod_caso'));
		
				
		// Variables para validaciï¿½n
		
		var $validate=array(
							'nom_comentario'=>VALID_NOT_EMPTY,
							'cod_tipoingreso' => '/([1-9][0-9]*)/');
		
		var $jsFeedback=array(
								'nom_comentario' => 'Ingrese comentario para el caso',
								'cod_tipoingreso' => 'Seleccione un tipo de ingreso correcto');
							
		function getPrioridades(){
			
			$array_nombres=array();
			$ingresos1=$this->getColumnType('tip_prioridad');
			
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