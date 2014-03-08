<?php 
class FichasHelper extends Helper
{
	var $helpers=array('Html');
	
	function desplegarFormulario($cod_formulario, $nom_form, $num_evento=null,$canEdit=false){
		
		//[Ignacio] obtencion de los datos del formulario
		$formulario=$this->view->controller->Formulario->find(array('Formulario.cod_formulario'=>$cod_formulario));
		//[Ignacio] Impresiï¿½n del nombre del formulario
		$html_desplegado='<fieldset class="form_horiz"><legend><span>'.$formulario['Formulario']['nom_formulario'].'</span></legend><ol>';
		//[Ignacio] Para cada secciï¿½n se realiza el despliegue
		foreach($formulario['Seccion'] as $v){
			$html_desplegado.="<li>".$this->desplegarSeccion($v['cod_seccion'], $nom_form,$num_evento,$canEdit)."</li>";
		}
		$html_desplegado.="</ol></fieldset>";
		return $html_desplegado;
	}
	function compararPreguntarPorOrden($a,$b)
	{
		var_dump($a);
		return $a['orden'] - $b['orden'];
	}
	function desplegarSeccion($cod_seccion, $nom_form, $num_evento=null,$canEdit=false){
		//[Ignacio] Obtenciï¿½n de datos de la seccion
		$seccion=$this->view->controller->Seccion->find(array('Seccion.cod_seccion'=>$cod_seccion));
		//[Ignacio] Impresiï¿½n del nombre de la seccion
		$html_desplegado='<fieldset class="form_horiz_seccion"><legend><span>'.$seccion['Seccion']['nom_seccion'].'</span></legend><ol>';
		//[Ignacio] Para cada pregunta se realiza el despliegue
		foreach($seccion['Pregunta'] as $v){
			$html_desplegado.="<li>".$this->desplegarPregunta($v['cod_pregunta'], $nom_form,$num_evento, $v['num_columna'],$canEdit)."</li>";
		}
		$html_desplegado.="</ol></fieldset>";
		return $html_desplegado;
	}
	function desplegarPregunta($cod_pregunta, $nom_form, $num_evento=null, $columna,$canEdit=false){
		//[Ignacio] Obtenciï¿½n de datos de la pregunta
		$pregunta=$this->view->controller->Pregunta->find(array('Pregunta.cod_pregunta'=>$cod_pregunta));
		$cont=0;
		$html_desplegado="";
		
		//[Ignacio] Se realiza el conteo de las dimensiones. Si es mï¿½s de 1 se muestra el texto de la pregunta. En caso contrario el texto va a nivel de subpregunta.
		foreach($pregunta['Dimension'] as $dimension)
		{
			$cont++;
		}
		if($cont!=1)
			$html_desplegado="<strong><p>".$pregunta['Pregunta']['nom_pregunta']."</p></strong>";
		
		$html_desplegado.="<table><tr><th></th>";
		//[Ignacio] Arreglo de dos dimensiones (num_fila,cod_dimension) con los codigos de subpregunta
		$subpreguntas=array();
		//[Ignacio] Arreglo unidimensional (en el numero de fila) con los textos de las subpreguntas
		$textos=array();
		//[Ignacio] Arreglo de dos dimensiones (num_fila, cod_dimension) con los tipos de subpregunta
		$tipos=array();
		//[Ignacio] Arreglo de dos dimensiones (num_fila, cod_dimension) con los codigo de grupo (para radios)
		$grupos=array();
		
		//[Ignacio] Este loop imprime los textos de las dimensiones y carga los arreglos subpreguntas, textos y tipos
		foreach($pregunta['Dimension'] as $dimension){
			$html_desplegado.="<th>".$dimension['nom_dimension']."</th>";
			//[Ignacio] Obtenciï¿½n de los datos de las subpreguntas para la dimension actual
			//$subpregs=$this->view->controller->Subpregunta->findAll(array('Subpregunta.cod_dimension'=>$dimension['cod_dimension']));
			$subpregs=$this->view->controller->Subpregunta->findAll("Subpregunta.cod_dimension = ".$dimension['cod_dimension']." AND NOT tip_tipoinput='norender'",null,"num_fila");
			foreach($subpregs as $subpregunta){
				$textos+=array($subpregunta['Subpregunta']['num_fila']=>$subpregunta['Subpregunta']['nom_subpregunta']);
				if(!isset($subpreguntas[$subpregunta['Subpregunta']['num_fila']])){
					$subpreguntas+=array($subpregunta['Subpregunta']['num_fila'] => array());
					$tipos+=array($subpregunta['Subpregunta']['num_fila'] => array());
					$grupos+=array($subpregunta['Subpregunta']['num_fila'] => array());
				}
				
				//[Ignacio] Se cargan los datos de la subpregunta
				$subpreguntas[$subpregunta['Subpregunta']['num_fila']]+=array(
					$dimension['cod_dimension'] =>$subpregunta['Subpregunta']['cod_subpregunta'], 
					'cod_subpregunta'=> $subpregunta['Subpregunta']['cod_subpregunta'],
					'nom_subpregunta'=> $subpregunta['Subpregunta']['nom_subpregunta'], 
					'cod_dimension'=> $subpregunta['Subpregunta']['cod_dimension']);
				
				$tipos[$subpregunta['Subpregunta']['num_fila']]+=array($dimension['cod_dimension'] =>$subpregunta['Subpregunta']['tip_tipoinput']);
				$grupos[$subpregunta['Subpregunta']['num_fila']]+=array($dimension['cod_dimension'] =>$subpregunta['Subpregunta']['cod_grupo']);
			}
		}
		
		//$respuestas=$this->view->controller->Respuestaficha->findAll();
		
		$html_desplegado.="</tr>";
		//para cada fila
		foreach($subpreguntas as $i => $v){
			
			$html_desplegado.="<tr>";
			$html_desplegado.="<td>";
			$html_desplegado.=$textos[$i];
			$html_desplegado.="</td>";
			//para cada dimension dentro de una fila
			foreach($pregunta['Dimension'] as $v2){
				$html_desplegado.="<td>";
				if(isset($v[$v2['cod_dimension']]))
				{
					if($num_evento==null) {
						$disabled=array();
						$disabledtxt="";
						$checked='';
						$value=array('value'=>'');
						$value2="";
						$codigo="";
					}
					else {
						$disabled=array('disabled' => true);
						$disabledtxt='disabled="disabled"';
						if($canEdit)
						{
							$disabled=array();
							$disabledtxt="";
						}
						//[Ignacio] aca va la respuesta de la ficha, de acuerdo al num_evento asociado
						$respuesta=$this->view->controller->Respuestaficha->find(
							array('Respuestaficha.cod_subpregunta' =>$subpreguntas[$i][$v2['cod_dimension']],
									'Respuestaficha.num_evento' =>$num_evento));
						$codigo=$respuesta['Respuestaficha']['cod_respuesta'];
						
						switch($tipos[$i][$v2['cod_dimension']]){
							case 'datefield': 
							case 'text':
								if(isset($respuesta['Respuestaficha']['nom_respuesta']))
									$value=array('value'=>$respuesta['Respuestaficha']['nom_respuesta']);
								else $value=array();
							break;
							case 'radio':
							case 'checkbox':
								if(isset($respuesta['Respuestaficha']['nom_respuesta']))
									$checked=($respuesta['Respuestaficha']['nom_respuesta']=="1")?'checked="checked"':'';
								else $checked='';
							break;
							case 'textarea':
								if(isset($respuesta['Respuestaficha']['nom_respuesta']))
									$value2=$respuesta['Respuestaficha']['nom_respuesta'];
								else $value2="";
							break;
						}
					}
					
					$maxLengthAttribute = array('maxlength'=>'500');
					$dateField = array('class'=> 'format-d-m-y divider-dash disable-days-67 no-transparency','readonly'=>'readonly');
					//[Ignacio] impresiï¿½n de los input's con el helper Html
					if($tipos[$i][$v2['cod_dimension']]=="text")
					{
						$html_desplegado.=$this->Html->input("Respuestaficha/".$subpreguntas[$i][$v2['cod_dimension']],
										$value+$disabled+$maxLengthAttribute );
						
					}
					else if($tipos[$i][$v2['cod_dimension']]=="datefield")
					{
						$html_desplegado.=$this->Html->input("Respuestaficha/".$subpreguntas[$i][$v2['cod_dimension']],
										$value+$disabled+$maxLengthAttribute+$dateField);
					}
					else if($tipos[$i][$v2['cod_dimension']]=="checkbox")
						$html_desplegado.='<input type="hidden" name="data[Respuestaficha]['.$subpreguntas[$i][$v2['cod_dimension']].']" value="0"/>'.
											'<input type="checkbox" name="data[Respuestaficha]['.$subpreguntas[$i][$v2['cod_dimension']].']" value="1" '.$checked.' '.$disabledtxt.'/>';
					else if($tipos[$i][$v2['cod_dimension']]=="radio")
						$html_desplegado.='<input type="radio" name="data[Respuestaficha][g'.$grupos[$i][$v2['cod_dimension']].']" value="'.$subpreguntas[$i][$v2['cod_dimension']].'" '.$checked.' '.$disabledtxt.'/>';
					
					else if($tipos[$i][$v2['cod_dimension']]=="textarea")
						$html_desplegado.='<span style="display:'. (($disabled['disabled'] == false)?"inline":"none") .'">M&aacute;ximo 500 caracteres</span><br/><textarea name="data[Respuestaficha]['.$subpreguntas[$i][$v2['cod_dimension']].']" '.$disabledtxt.' cols="60" rows="5" onKeyDown="if(this.value.length>500) this.value = this.value.substring(0,500)">'.$value2.'</textarea>';
					
						
					$html_desplegado.="\n";
					$html_desplegado.='<input type="hidden" name="data[codigo]['.$subpreguntas[$i][$v2['cod_dimension']].']" value="'.$codigo.'"/>';
					
					
				}
				$html_desplegado.="</td>";
			}
			
			$html_desplegado.="</tr>";
		}
		

		$html_desplegado.="</table>";

		//print_r($subpreguntas);
		return $html_desplegado;
	}
	
	function getArrayFormulario($cod_formulario)
	{
		//[Gabriela] este mï¿½todo genera un arreglo con todos las subpreguntas asociadas al formulario, y el tipo, para poder guardar la informaciï¿½n
		
		$formulario=$this->view->controller->Formulario->find(array('Formulario.cod_formulario'=>$cod_formulario));
		$arreglo=array();
		
		foreach($formulario['Seccion'] as $v){
			$arreglo+=$this->getArraySeccion($v['cod_seccion'],$num_evento);
		}
		$html_desplegado.="</ol></fieldset>";
		return $arreglo;
		
	}
	
	function getArraySeccion($cod_seccion, $num_evento)
	{
		$seccion=$this->view->controller->Seccion->find(array('Seccion.cod_seccion'=>$cod_seccion));
		$arreglo=array();
		foreach($seccion['Pregunta'] as $v){
			$arreglo+=$this->desplegarPregunta($v['cod_pregunta'], $num_evento);
		}
		
		return $arreglo;
	}
	
	function getArrayPregunta($cod_pregunta, $num_evento)
	{
		$pregunta=$this->view->controller->Pregunta->find(array('Pregunta.cod_pregunta'=>$cod_pregunta));
		
		$arreglo=array();
		$subpreguntas=array();
		$textos=array();
		$tipos=array();
		$fila=0;
		$l=0;
		foreach($pregunta['Dimension'] as $dimension){
			
			$subpregs=$this->view->controller->Subpregunta->findAll(array('Subpregunta.cod_dimension'=>$dimension['cod_dimension']));
			
			foreach($subpregs as $subpregunta){
				$textos+=array($subpregunta['Subpregunta']['num_fila']=>$subpregunta['Subpregunta']['nom_subpregunta']);
				
				if(!isset($subpreguntas[$subpregunta['Subpregunta']['num_fila']])){
					$subpreguntas+=array($subpregunta['Subpregunta']['num_fila'] => array());
					$tipos+=array($subpregunta['Subpregunta']['num_fila'] => array());
					
				}
				
				$subpreguntas[$subpregunta['Subpregunta']['num_fila']]+=array(
					$dimension['cod_dimension'] =>$subpregunta['Subpregunta']['cod_subpregunta'], 
					'cod_subpregunta'=> $subpregunta['Subpregunta']['cod_subpregunta'],
					'nom_subpregunta'=> $subpregunta['Subpregunta']['nom_subpregunta'], 
					'cod_dimension'=> $subpregunta['Subpregunta']['cod_dimension']);
				
				$tipos[$subpregunta['Subpregunta']['num_fila']]+=array($dimension['cod_dimension'] =>$subpregunta['Subpregunta']['tip_tipoinput']);
				
			}
		}
		
		foreach($subpreguntas as $i => $v){
			
			//$html_desplegado.=$textos[$i];
			
			foreach($pregunta['Dimension'] as $v2){
				$html_desplegado.="<td>";
				if(isset($v[$v2['cod_dimension']]))
				{
					if($num_evento==null) {
						//$disabled=array();
						//[Ignacio] por mientras se pone el codigo de subpregunta, pero debiera ser ''
						$value=array('value'=>$subpreguntas[$i][$v2['cod_dimension']]);
					}
					else {
						//$disabled=array('disabled' => true);
						$value=array('value'=>$subpreguntas[$i][$v2['cod_dimension']]);
					}
					
					//[Ignacio] impresiï¿½n de los input's con el helper Html
						
					$arreglo+=array($subpreguntas[$i][$v2['cod_dimension']]=>$subpreguntas[$i][$v2['cod_dimension']], 'tipo'=>$$tipos[$i][$v2['cod_dimension']]);
						
					
				}
				
			}
			
		}
		
		
		//print_r($subpreguntas);
		return $arreglo;
	}
}
?>
