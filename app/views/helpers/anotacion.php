<?php 
class AnotacionHelper extends Helper
{
	//[Dawes] En el controller-vista que lo va a usar, hay que incluir a Anotacione como modelo usar.
	var $helpers=array('Html');
	
	function desplegarAnotaciones($anotaciones){
		$html_desplegado="";
		foreach ($anotaciones as $anotacione)
		{	
			//damos vuelta la fecha a normal chileno para que se vea bien
			$fecha = explode("-", $anotacione['Anotacione']['fecha_inicio']); //$fecha[2]=dia, $fecha[1]=mes, $fecha[0]=aÃ¯Â¿Â½o
			$anotacione['Anotacione']['fecha_inicio'] = $fecha[2]."-".$fecha[1]."-".$fecha[0];
			$fecha = explode("-", $anotacione['Anotacione']['fecha_termino']); //$fecha[2]=dia, $fecha[1]=mes, $fecha[0]=aÃ¯Â¿Â½o
			$anotacione['Anotacione']['fecha_termino'] = $fecha[2]."-".$fecha[1]."-".$fecha[0];
			unset($fecha);
			
			$html_desplegado.="<div class=\"anot_comentario\" id=\"rojo\">";
			
			$html_desplegado.="<div class=\"anot_data\">";
			//$html_desplegado.="<textarea disabled=\"disabled\" cols=\"30\" rows=\"5\" onKeyDown=\"if(this.value.length>500) this.value = this.value.substring(0,500)\">".$anotacione['Anotacione']['Comentario']."</textarea>";
			$html_desplegado.="<span>".$anotacione['Anotacione']['Comentario']."</span>";
			$html_desplegado.="</div>";
	
			$html_desplegado.="<div class=\"anot_acciones\"><br/>";
			//$html_desplegado.=$this->Html->formTag('modificar/'.$anotacione['Anotacione']['cod_anotacion'],'post', array('style'=>'display:inline'));
			//$html_desplegado.="<input type=\"image\" src=\"".$this->webroot.'img/modificar.png'."\" alt=\"Modificar\" title=\"Modificar anotacion\" />";
			//$html_desplegado.="</form>";							
			$html_desplegado.=$this->Html->formTag('/anotaciones/eliminarDesdeCalendario/'.$anotacione['Anotacione']['cod_anotacion'],'post', array('style'=>'display:inline'));
			$html_desplegado.="<input type=\"image\" src=\"".$this->webroot.'img/cruz.png'."\" alt=\"Eliminar\" title=\"Eliminar anotacion\" onClick=\"return confirm('¿Esta seguro que desea eliminar esta anotacion?')\"/>";
			$html_desplegado.="</form></div>\n";
			
			$html_desplegado.="<div class=\"anot_fecha\">";
			$html_desplegado.="Fecha Inicio: ".$anotacione['Anotacione']['fecha_inicio']."<br/>";
			$html_desplegado.="Fecha Termino: ".$anotacione['Anotacione']['fecha_termino'];
			$html_desplegado.="</div>";
			
			$html_desplegado.="</div>";	
		}
		$html_desplegado.="<br/>";
		return $html_desplegado;
	}
	
	function anotacionesCalendario($fecha_inicio=null, $fecha_termino=null){
		
		$busqueda="";
		
		
		if(!$fecha_inicio || !$fecha_termino){
			$mensaje=38;
		} else {
			$busqueda.=	"	(	Anotacione.fecha_inicio >='".$fecha_inicio."' 
							AND Anotacione.fecha_inicio <='".$fecha_termino."' 
							) 
						OR	
							(	Anotacione.fecha_termino>='".$fecha_inicio."' 
							AND Anotacione.fecha_termino<='".$fecha_termino."' 
						    )
						OR
							(	Anotacione.fecha_inicio <='".$fecha_inicio."' 
							AND Anotacione.fecha_termino>='".$fecha_inicio."' 
							)
						OR	
							(	Anotacione.fecha_inicio <='".$fecha_termino."' 
							AND Anotacione.fecha_termino>='".$fecha_termino."' 
						    )
						";	
			
			$mensaje=39;
		}
				
		$this->view->controller->Anotacione->recursive = 0;
		$anotaciones1=$this->view->controller->Anotacione->findAll($busqueda);
		
		// [Diego Jorquera] Informar si no hay anotaciones para el período indicado
		if (count($anotaciones1) > 0) {
			return $this->desplegarAnotaciones($anotaciones1);
		} else {
			$str = '<table class="table_tablagris"><tr><td class="td_gris">';
			$str .= 'No hay anotaciones para este período';
			$str .= '</td></tr></table>';
			return $str;
		}
		
	}
	

}
?>
