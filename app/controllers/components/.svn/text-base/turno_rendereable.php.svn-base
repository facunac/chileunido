<?php

// Aquí están contenidos todos los componentes "rendereables" para la generación de calendarios.

// Componente base

class TurnoRendereableComponent extends Object
{
	var $mi_tipo = null;
	var $turno = null;			// Datos del turno
	var $voluntario = null;		// Datos del voluntario asociado al turno, si lo hay
	var $caso = null;			// Datos del caso asociado al turno, si lo hay
	var $beneficiario = null;
	var $bit_clinico;			// Es turno clínico?
	var $bit_nopsicologo;
	var $bit_permisoturnos;		// Podemos modificar y borrar turnos?
	
	var $css_class = null;		// Clase CSS usada para renderizar
	
	var $horas_clinico = array('9:00-10:00','10:00-11:00','11:00-12:00','12:00-13:00','13:00-14:00',
		                       '14:00-15:00','15:00-16:00','16:00-17:00','17:00-18:00');
	
	var $html;					// HTMLHelper
	var $base_url;				// URL base de la aplicación (debe inicializarse manualmente con Dispatcher::baseUrl()
	var $admin;					// Método que inicializa manualmente lo anterior necesita esto...
	
	function __construct() {
		loadHelper('Html');
		$this->html = new HtmlHelper();	// Helper HTML debe crearse manualmente
		$this->base_url = Dispatcher::baseUrl();
	}
}

// Utilizado en calendario para derivación clínica

class TurnoDerivacionRendereable extends TurnoRendereableComponent {
	
	var $cod_programa;
	
	function render() {
		$string = "";
		$js_prompt = 'return confirm(\'¿Desea realizar la asignación?\')';
		
		if ($this->css_class != null) {
			$string = '<div class="'. $this->css_class . '">';
		} else {
			$string = '<div>';
		}
		
		if ($this->turno != null) {
			$nom_voluntario = $this->voluntario['Persona']['nom_nombre'] . ' ' . $this->voluntario['Persona']['nom_appat'];
			$nom_beneficiario = $this->beneficiario['Persona']['nom_nombre'] . ' ' . $this->beneficiario['Persona']['nom_appat'];
			
			$string .= '<div class="cal_datos">';
			
			$string .= $nom_voluntario . '<br />';
			$string .= '<br/><i>'.$nom_beneficiario.'</i>';
			
			$string .= '</div><div class="cal_derivar">';
			
			$string .= $this->html->formTag(''.$this->base_url.'/derivaciones/derivar2', 'post', array('style' => 'display:inline;'));
			$string .= $this->html->hidden('Turno/cod_turno', array('value' => $this->turno['Turno']['cod_turno']));
			$string .= $this->html->hidden('Turno/cod_caso', array('value' => $this->caso['Caso']['cod_caso']));
			$string .= '<input type="image" src="'.$this->base_url.'/img/turno_agendar.png" alt="Derivar" title="Derivar" onClick="'.$js_prompt.'">';
			$string .= '</form>';
			
			$string .= '</div>';
		}
		
		$string .= '</div>';
		
		return $string;
	}
}

// Utilizado en calendario de turnos para un voluntario específico

class TurnoLocalRendereable extends TurnoRendereableComponent {

	var $nom_dia = null;
	var $num_hora = null;
	
	function render() {
		$string = "";
		$js_prompt = 'return confirm(\'¿Está seguro de que desea borrar este turno?\')';
			
		if ($this->css_class != null) {
			$string = '<div class="'. $this->css_class . '">';
		} else {
			$string = '<div>';
		}
		
		if ($this->turno != null) {
			// Si hay datos asociados, mostrar voluntario y box y botones para
			// modificar y borrar turno
			
			$string .= '<div class="cal_datos">';
			
			$nom_voluntario = $this->voluntario['Persona']['nom_nombre'] . ' ' . $this->voluntario['Persona']['nom_appat'];
			$string .= $nom_voluntario . '<br />';
			$string .= '<b><i>' . $this->turno['Box']['nom_box'] . '</i></b>';
			if ($this->caso != null) {
				$nom_beneficiario = $this->beneficiario['Persona']['nom_nombre'] . ' ' . $this->beneficiario['Persona']['nom_appat'];
				$string .= '<br/><i>'.$nom_beneficiario.'</i>';
			}
			
			$string .= '</div><div class="cal_botones">';
			
			if ($this->bit_permisoturnos) {
				$string .= $this->html->formTag(''.$this->base_url.'/turnos/modificar', 'post', array('style' => 'display:inline;'));
				$string .= $this->html->hidden('Turno/cod_turno', array('value' => $this->turno['Turno']['cod_turno']));
				$string .= $this->html->hidden('Turno/cod_voluntario', array('value' => $this->voluntario['Voluntario']['cod_persona']));
				$string .= $this->html->hidden('Turno/nom_dia', array('value' => $this->turno['Turno']['nom_dia']));
				$string .= $this->html->hidden('Extra/nom_voluntario', array('value' => $nom_voluntario));
				$string .= $this->html->hidden('Turno/num_hora', array('value' => $this->turno['Turno']['num_hora']));
				$string .= $this->html->hidden('Turno/cod_box', array('value' => $this->turno['Turno']['cod_box']));
				
				if ($this->bit_clinico) {
					$string .= $this->html->hidden('Extra/nom_hora', array('value' => $this->horas_clinico[$this->turno['Turno']['num_hora'] - 9]));
				} else {
					$string .= $this->html->hidden('Extra/nom_hora', array('value' => ($this->turno['Turno']['num_hora'] < 14 ? 'Mañana' : 'Tarde')));
				}
				
				$string .= $this->html->hidden('Extra/bit_clinico', array('value' => $this->bit_clinico));
				$string .= $this->html->hidden('Extra/bit_nopsicologo', array('value' => $this->bit_nopsicologo));
				$string .= $this->html->hidden('Extra/nom_origenusuario', array('value' => 'ver'));
				$string .= $this->html->hidden('Extra/cod_programa', array('value' => '')); // Ignorar en este caso
				$string .= '<input type="image" src="'.$this->base_url.'/img/turno_modificar.png" alt="Modificar" title="Modificar">';
				$string .= '</form>';
				
				if ($this->caso == null) {
					$string .= '&nbsp;';
					$string .= $this->html->formTag(''.$this->base_url.'/turnos/eliminar', 'post', array('style' => 'display:inline;'));
					$string .= $this->html->hidden('Turno/cod_turno', array('value' => $this->turno['Turno']['cod_turno']));
					$string .= $this->html->hidden('Turno/cod_voluntario', array('value' => $this->voluntario['Voluntario']['cod_persona']));
					$string .= $this->html->hidden('Extra/nom_origenusuario', array('value' => 'ver'));
					$string .= '<input type="image" src="'.$this->base_url.'/img/turno_eliminar.png" alt="Eliminar" title="Eliminar" onClick="'.$js_prompt.'">';
					$string .= '</form>';
				}
			}
			
			$string .= '</div>';
		} else {
			if ($this->bit_permisoturnos) {
				// Si no hay datos asociados a este turno, renderizar botones para agregar
				// turno en este horario
				
				$string .= $this->html->formTag(''.$this->base_url.'/turnos/crear', 'post', array('style' => 'display:inline;'));
				$string .= $this->html->hidden('Turno/cod_voluntario', array('value' => $this->voluntario['Voluntario']['cod_persona']));
				$string .= $this->html->hidden('Turno/nom_dia', array('value' => $this->nom_dia));
				$string .= $this->html->hidden('Extra/nom_voluntario', array('value' =>
					$this->voluntario['Persona']['nom_nombre'] . ' ' . $this->voluntario['Persona']['nom_appat']));
				$string .= $this->html->hidden('Turno/num_hora', array('value' => $this->num_hora));
				
				if ($this->bit_clinico) {
					$string .= $this->html->hidden('Extra/nom_hora', array('value' => $this->horas_clinico[$this->num_hora - 9]));
				} else {
					$string .= $this->html->hidden('Extra/nom_hora', array('value' => ($this->num_hora < 14 ? 'Mañana' : 'Tarde')));
				}
				
				$string .= $this->html->hidden('Extra/bit_clinico', array('value' => $this->bit_clinico));
				$string .= $this->html->hidden('Extra/bit_nopsicologo', array('value' => $this->bit_nopsicologo));
				$string .= $this->html->hidden('Extra/nom_origenusuario', array('value' => 'ver'));
				$string .= $this->html->hidden('Extra/cod_programa', array('value' => '')); // Ignorar en este caso
				$string .= '<div class="cal_nuevoturno"><input type="image" src="'.$this->base_url.'/img/turno_asignar.png" alt="Asignar Turno" title="Asignar Turno" /></div>';
				$string .= '</form>';
			}
		}
		
		$string .= '</div>';
		
		return $string;
	}
	
}

// Utilizado en calendario de todos los turnos

class TurnoGlobalRendereable extends TurnoRendereableComponent {
	
	var $cod_programa;
	
	function render() {
		$string = "";
		$js_prompt = 'return confirm(\'¿Está seguro de que desea borrar este turno?\')';
			
		if ($this->css_class != null) {
			$string = '<div class="'. $this->css_class . '">';
		} else {
			$string = '<div>';
		}
		
		if ($this->turno != null) {
			// Si hay datos asociados, mostrar voluntario y box y botones para
			// modificar y borrar turno
			$nom_voluntario = $this->voluntario['Persona']['nom_nombre'] . ' ' . $this->voluntario['Persona']['nom_appat'];
			
			$string .= '<div class="cal_datos">';
			$string .= '<b>' . $nom_voluntario . '</b>';
			
			$string .= '<br /><b><i>' . $this->turno['Box']['nom_box'] . '</i></b>';
			
			if ($this->caso != null) {
				$nom_beneficiario = $this->beneficiario['Persona']['nom_nombre'] . ' ' . $this->beneficiario['Persona']['nom_appat'];
				$string .= '<br/><i>'.$nom_beneficiario.'</i>';
			}
			
			$string .= '</div><div class="cal_botones">';
			
			if ($this->bit_permisoturnos) {
				$string .= $this->html->formTag(''.$this->base_url.'/turnos/modificar', 'post', array('style' => 'display:inline;'));
				$string .= $this->html->hidden('Turno/cod_turno', array('value' => $this->turno['Turno']['cod_turno']));
				$string .= $this->html->hidden('Turno/cod_voluntario', array('value' => $this->voluntario['Voluntario']['cod_persona']));
				$string .= $this->html->hidden('Turno/nom_dia', array('value' => $this->turno['Turno']['nom_dia']));
				$string .= $this->html->hidden('Extra/nom_voluntario', array('value' => $nom_voluntario));
				$string .= $this->html->hidden('Turno/num_hora', array('value' => $this->turno['Turno']['num_hora']));
				$string .= $this->html->hidden('Turno/cod_box', array('value' => $this->turno['Turno']['cod_box']));
				
				if ($this->bit_clinico) {
					$string .= $this->html->hidden('Extra/nom_hora', array('value' => $this->horas_clinico[$this->turno['Turno']['num_hora'] - 9]));
				} else {
					$string .= $this->html->hidden('Extra/nom_hora', array('value' => ($this->turno['Turno']['num_hora'] < 14 ? 'Mañana' : 'Tarde')));
				}
				
				$string .= $this->html->hidden('Extra/bit_clinico', array('value' => $this->bit_clinico));
				$string .= $this->html->hidden('Extra/bit_nopsicologo', array('value' => $this->bit_nopsicologo));
				$string .= $this->html->hidden('Extra/cod_programa', array('value' => $this->cod_programa));
				$string .= $this->html->hidden('Extra/nom_origenusuario', array('value' => 'index'));
				$string .= '<input type="image" src="'.$this->base_url.'/img/turno_modificar.png" alt="Modificar" title="Modificar">';
				$string .= '</form>';
				
				if ($this->caso == null) {
					$string .= '&nbsp;';
					$string .= $this->html->formTag(''.$this->base_url.'/turnos/eliminar', 'post', array('style' => 'display:inline;'));
					$string .= $this->html->hidden('Turno/cod_turno', array('value' => $this->turno['Turno']['cod_turno']));
					$string .= $this->html->hidden('Turno/cod_voluntario', array('value' => $this->voluntario['Voluntario']['cod_persona']));
					$string .= $this->html->hidden('Extra/nom_origenusuario', array('value' => 'index'));
					$string .= '<input type="image" src="'.$this->base_url.'/img/turno_eliminar.png" alt="Eliminar" title="Eliminar" onClick="';
					if ($this->caso != null) {
						$string .= $js_prohibido;
					} else {
						$string .= $js_prompt;
					}
					$string .= '">';
				}
				$string .= '</form>';
			}
			
			$string .= '</div>';
		} else {
			if ($this->bit_permisoturnos) {
				// Si no hay datos asociados a este turno, renderizar botones para agregar
				// turno en este horario (si es que hay permiso para eso)
				
				$string .= $this->html->formTag(''.$this->base_url.'/turnos/crear', 'post', array('style' => 'display:inline;'));
				
				if ($this->voluntario != null) { // No hay voluntario asociado
					$string .= $this->html->hidden('Turno/cod_voluntario', array('value' => $this->voluntario['Voluntario']['cod_persona']));
					$string .= $this->html->hidden('Extra/cod_programa', array('value' => $this->voluntario['Voluntario']['cod_programa']));
					$string .= $this->html->hidden('Extra/nom_voluntario', array('value' =>
						$this->voluntario['Persona']['nom_nombre'] . ' ' . $this->voluntario['Persona']['nom_appat']));
				} else { // Hay voluntario asociado
					$string .= $this->html->hidden('Turno/cod_voluntario', array('value' => ''));
					$string .= $this->html->hidden('Extra/cod_programa', array('value' => $this->cod_programa));
				}
				
				$string .= $this->html->hidden('Turno/nom_dia', array('value' => $this->nom_dia));
				$string .= $this->html->hidden('Turno/num_hora', array('value' => $this->num_hora));
		
				if ($this->bit_clinico) { // Turno clínico
					$string .= $this->html->hidden('Extra/nom_hora', array('value' => $this->horas_clinico[$this->num_hora - 9]));
				} else { // Turno telefónico o genérico
					$string .= $this->html->hidden('Extra/nom_hora', array('value' => ($this->num_hora < 14 ? 'Mañana' : 'Tarde')));
				}
				
				$string .= $this->html->hidden('Extra/bit_clinico', array('value' => $this->bit_clinico));
				$string .= $this->html->hidden('Extra/bit_nopsicologo', array('value' => $this->bit_nopsicologo));
				$string .= $this->html->hidden('Extra/nom_origenusuario', array('value' => 'index'));
				$string .= '<div class="cal_nuevoturno"><input type="image" src="'.$this->base_url.'/img/turno_asignar.png" alt="Asignar Turno" title="Asignar Turno" /></div>';
				$string .= '</form>';
			}
		}
		
		$string .= '</div>';
		
		return $string;
	}
	
}

// Utilizado en calendario de todos los turnos, genera mini-botón para crear nuevo turno

class TurnoMiniNuevoRendereable extends TurnoRendereableComponent {
	
	var $cod_programa;
	
	function render() {
		$string = "";
			
		if ($this->css_class != null) {
			$string = '<div class="'. $this->css_class . '">';
		} else {
			$string = '<div>';
		}
		
		// Si no hay datos asociados a este turno, renderizar botones para agregar
		// turno en este horario
		
		$string .= $this->html->formTag(''.$this->base_url.'/turnos/crear', 'post', array('style' => 'display:inline;'));
		
		if ($this->voluntario != null) { // No hay voluntario asociado
			$string .= $this->html->hidden('Turno/cod_voluntario', array('value' => $this->voluntario['Voluntario']['cod_persona']));
			$string .= $this->html->hidden('Extra/cod_programa', array('value' => $this->voluntario['Voluntario']['cod_programa']));
			$string .= $this->html->hidden('Extra/nom_voluntario', array('value' =>
				$this->voluntario['Persona']['nom_nombre'] . ' ' . $this->voluntario['Persona']['nom_appat']));
		} else { // Hay voluntario asociado
			$string .= $this->html->hidden('Turno/cod_voluntario', array('value' => ''));
			$string .= $this->html->hidden('Extra/cod_programa', array('value' => $this->cod_programa));
		}
		
		$string .= $this->html->hidden('Turno/nom_dia', array('value' => $this->nom_dia));
		$string .= $this->html->hidden('Turno/num_hora', array('value' => $this->num_hora));

		if ($this->bit_clinico) { // Turno clínico
			$string .= $this->html->hidden('Extra/nom_hora', array('value' => $this->horas_clinico[$this->num_hora - 9]));
		} else { // Turno telefónico
			$string .= $this->html->hidden('Extra/nom_hora', array('value' => ($this->num_hora < 14 ? 'Mañana' : 'Tarde')));
		}
		
		$string .= $this->html->hidden('Extra/bit_clinico', array('value' => $this->bit_clinico));
		$string .= $this->html->hidden('Extra/bit_nopsicologo', array('value' => $this->bit_nopsicologo));
		$string .= $this->html->hidden('Extra/nom_origenusuario', array('value' => 'index'));
		$string .= '<input type="image" class="cal_mininuevoturno" src="'.$this->base_url.'/img/turno_miniasignar.png" alt="Agregar Turno" title="Agregar Turno" />';
		$string .= '</form>';
		
		$string .= '</div>';
		
		return $string;
	}
	
}

?>