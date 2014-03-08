<?php
	
	class Formulario extends AppModel
	{
		var $name='Formulario'; // nombre del modelo
		var $primaryKey='cod_formulario';
		
		var $belongsTo=array('Actividad' => array('foreignKey' => 'cod_actividad'));
		var $hasMany=array('Seccion' => array('foreignKey' => 'cod_formulario'),
							'Ficha'=> array('foreignKey' => 'cod_formulario'));
		
		
		function getSubpreguntas($cod_formulario){
			//arreglo con las subpreguntas asociadas al formulario, al principio vacío
			$subp=array();
			
			$secciones=$this->Seccion->findAll(array('Seccion.cod_formulario'=>$cod_formulario),"","cod_orden ASC","","",-1);
				
			//[Gabriela] Se tienen todas las secciones del formulario
			// Se hacen varios foreach ya que la consulta anterior no me entrega directamente esta información
			foreach($secciones as $seccion)	{
				//Se buscan todas las preguntas en esta seccion
				$preguntas=$this->Seccion->Pregunta->findAll(array('Pregunta.cod_seccion'=>$seccion['Seccion']['cod_seccion']),"","","","",-1);
				
				foreach($preguntas as $pregunta){
					//Se buscan todas las dimensiones asociadas a la pregunta
					$dimensiones=$this->Seccion->Pregunta->Dimension->findAll(array('Dimension.cod_pregunta'=>$pregunta['Pregunta']['cod_pregunta']),"","","","",-1);
					
					foreach($dimensiones as $dimension)	{
						$subpreguntas=$this->Seccion->Pregunta->Dimension->Subpregunta->findAll(array('Subpregunta.cod_dimension'=>$dimension['Dimension']['cod_dimension']),"","","","",-1);
						foreach($subpreguntas as $subpregunta){
							$subp+=array(count($subp) => $subpregunta);
						}
					}
				}
			}
			return $subp;
		}
	}
?>
