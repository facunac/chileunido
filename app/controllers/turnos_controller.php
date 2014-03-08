<?php

class TurnosController extends AppController {

	var $name = 'Turnos';
	var $uses = array('Turno', 'Caso', 'Box', 'Voluntario', 'Beneficiario', 'Programa', 'Permisovoluntario');
	var $helpers = array('Html', 'Form', 'Matrix');
	var $components = array('Rendereable', 'TurnoRendereable');
	
	var $horas_clinico = array('9', '10', '11', '12', '13', '14', '15', '16', '17');	// Horas turnos clínicos
	var $horas_noclinico = array('9', '14');											// Horarios turnos telefónicos y genéricos
	var $etiq_horas_clinico = array('9:00-10:00', '10:00-11:00', '11:00-12:00', '12:00-13:00', '13:00-14:00',
							   '14:00-15:00', '15:00-16:00', '16:00-17:00', '17:00-18:00');
	var $etiq_horas_noclinico = array('Mañana', 'Tarde');
	var $dias = array('Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes');
	
	// Copiado de helper permisoschecker, obligado a copiar código aquí, aunque sea feo
	// (debiera ir en app_controller.php!)
	
	function _check_permission($controller, $action='index') {
		$cod_voluntario=$this->Session->read('cod_voluntario');
		$filter=array('Permisovoluntario.cod_voluntario' => $cod_voluntario, 
						'Permiso.nom_controller' => $controller,
						'or' => array('Permiso.nom_action' => $action,
								array('Permiso.nom_actionmodifica' => $action, 
										'Permisovoluntario.bit_modifica'=>1),
								array('Permiso.nom_action' => '', 
										'Permiso.nom_actionmodifica' => "<>".$action)));
		return is_array($this->Permisovoluntario->find($filter));
	}
	
	// Ver todos los turnos disponibles en el sistema
	
	function index($exito = null) {
		$this->escribirHeader("Turnos");
		
		// Separar en turnos clínicos y telefónicos (separando estos últimos por programa)
		
		$turnos = $this->Turno->findAll();
		$programas = $this->Programa->findAll();
		
		$turnos_clinicos = array(); $i_c = 0;
        $turnos_otros = array(); $i_o = 0;
        $turnos_telefonicos = array(); $i_p = array();
        
        foreach ($programas as $programa) {
        	$cod_programa = $programa['Programa']['cod_programa'];
        	$turnos_telefonicos[$cod_programa] = array();
        	$i_p[$cod_programa] = 0;
        }
        
		$horas = array('9', '10', '11', '12', '13', '14', '15', '16', '17');	// Horas turnos clínicos
		$horarios = array('Mañana', 'Tarde');									// Horarios turnos telefónicos y genéricos
		$horas_a_indices = array_combine($horas, range(0, 8));
		$horarios_a_indices = array_combine($horarios, range(0, 1));
		
		$bit_permisoturnos = $this->_check_permission('turnos', 'modificar');
        
        // Separar turnos en clínicos, telefónicos y "otros"
        
        foreach ($turnos as $turno) {
        	switch ($turno['Voluntario']['est_rol']) {
        		case 'Voluntario':
        		case 'Administrativo': { // Turnos clínicos y telefónicos por programa
        	        if ($turno['Box']['tip_box'] == 'Clinico' && $turno['Voluntario']['bit_clinico']) {
        				$turnos_clinicos[$i_c++] = $turno;
        			} else if ($turno['Box']['tip_box'] == 'Telefonico') {
        				$cod_programa = $turno['Voluntario']['cod_programa'];
        				$turnos_telefonicos[$cod_programa][$i_p[$cod_programa]++] = $turno;
        			}
        		} break;
        		case 'Voluntario no psicologo': { // Otros turnos
					$turnos_otros[$i_o++] = $turno;
        		} break;
        	} 
        }

		// CALENDARIO CLÍNICO
		
		$rendereables_clinico = array(null, null, null, null, null, null, null, null, null);
		$calendario_clinico = array('Lunes' => $rendereables_clinico, 'Martes' => $rendereables_clinico,
									'Miercoles' => $rendereables_clinico, 'Jueves' => $rendereables_clinico,
									'Viernes' => $rendereables_clinico);
		
		foreach ($calendario_clinico as $nom_dia => $v) {
			foreach ($horas as $num_hora) {
				$calendario_clinico[$nom_dia][$horas_a_indices[$num_hora]] = new RendereableComponent();
				$calendario_clinico[$nom_dia][$horas_a_indices[$num_hora]]->libre = 1;
				$nrd = new TurnoGlobalRendereable();
				$nrd->nom_dia = $nom_dia;
				$nrd->num_hora = $num_hora;
				$nrd->bit_clinico = 1;
				$nrd->bit_nopsicologo = false;
				$nrd->bit_permisoturnos = $bit_permisoturnos;
				$nrd->css_class = 'cal_vacio';
				$calendario_clinico[$nom_dia][$horas_a_indices[$num_hora]]->add_rendereable($nrd);
			}
		}
		
		// Crear rendereables para turnos clínicos
		
		foreach ($turnos_clinicos as $turno) {
			$rendereable = $calendario_clinico[$turno['Turno']['nom_dia']][$horas_a_indices[$turno['Turno']['num_hora']]];
			if ($rendereable->libre) {
				$rendereable->obliterate();
				$rendereable->libre = 0;
			}
			$nrd = new TurnoGlobalRendereable();
			$nrd->turno = $turno;
			$nrd->bit_clinico = 1;
			$nrd->bit_nopsicologo = false;
			$nrd->voluntario = $this->Turno->Voluntario->findByCodPersona($turno['Turno']['cod_voluntario']);
			$nrd->bit_permisoturnos = $bit_permisoturnos;
			if ($turno['Turno']['cod_caso'] != null) {
				$nrd->caso = $this->Caso->findByCodCaso($turno['Turno']['cod_caso']);
				$nrd->beneficiario = $this->Beneficiario->findByCodPersona($nrd->caso['Caso']['cod_beneficiario']);
				$nrd->css_class = 'cal_caso';
			} else {
				$nrd->css_class = 'cal_asignado';
			}
			$rendereable->add_rendereable($nrd);
		}
		
		// Agregar rendereables para mini-botones de crear nuevo turno
		
		if ($bit_permisoturnos) {
			foreach ($calendario_clinico as $nom_dia => $v) {
				foreach ($horas as $num_hora) {
					$rendereable = $calendario_clinico[$nom_dia][$horas_a_indices[$num_hora]];
					if (!$rendereable->libre) {
						$nnrd = new TurnoMiniNuevoRendereable();
						$nnrd->nom_dia = $nom_dia;
						$nnrd->num_hora = $num_hora;
						$nnrd->bit_clinico = 1;
						$nrd->bit_nopsicologo = false;
						$nnrd->css_class = 'cal_mininuevo';
						$rendereable->add_rendereable($nnrd);
					}
				}
			}
		}
		
		$filas_clinico = array('9:00-10:00', '10:00-11:00', '11:00-12:00', '12:00-13:00', '13:00-14:00',
							   '14:00-15:00', '15:00-16:00', '16:00-17:00', '17:00-18:00');
		foreach ($filas_clinico as $i => $v) {
			$filas_clinico[$i] = '<span style="font-size: x-small">'.$filas_clinico[$i].'</span>';
		}
		
		$this->set('filas_clinico', $filas_clinico);
		$this->set('calendario_clinico', $calendario_clinico);
		
		// CALENDARIOS TELEFÓNICOS (por programa)
		
		$calendarios_telefonicos = array();
		
		foreach ($programas as $programa) {
			$cod_programa = $programa['Programa']['cod_programa'];
			
			$rendereables = array(null, null);
			$calendarios_telefonicos[$cod_programa] = array('Lunes' => $rendereables, 'Martes' => $rendereables,
										   'Miercoles' => $rendereables, 'Jueves' => $rendereables,
										   'Viernes' => $rendereables);
			
			// Crear rendereables para turnos telefónicos
			
			foreach ($calendario_clinico as $nom_dia => $v) {
				foreach ($horarios as $horario) {
					$calendarios_telefonicos[$cod_programa][$nom_dia][$horarios_a_indices[$horario]] = new RendereableComponent();
					$calendarios_telefonicos[$cod_programa][$nom_dia][$horarios_a_indices[$horario]]->libre = 1;
					$nrd = new TurnoGlobalRendereable();
					$nrd->nom_dia = $nom_dia;
					$nrd->num_hora = ($horario == 'Mañana' ? 9 : 14);
					$nrd->bit_clinico = 0;
					$nrd->bit_nopsicologo = false;
					$nrd->bit_permisoturnos = $bit_permisoturnos;
					$nrd->css_class = 'cal_vacio';
					$nrd->cod_programa = $cod_programa;
					$calendarios_telefonicos[$cod_programa][$nom_dia][$horarios_a_indices[$horario]]->add_rendereable($nrd);
				}
			}
			
			// Modificar rendereables correspondientes a turnos existentes
			
			foreach ($turnos_telefonicos[$cod_programa] as $turno) {
				if ($turno['Turno']['num_hora'] < 14) { // Turno mañana
					$rendereable = $calendarios_telefonicos[$cod_programa][$turno['Turno']['nom_dia']][$horarios_a_indices['Mañana']];
				} else { // Turno tarde
					$rendereable = $calendarios_telefonicos[$cod_programa][$turno['Turno']['nom_dia']][$horarios_a_indices['Tarde']];
				}
				if ($rendereable->libre) {
					$rendereable->obliterate();
					$rendereable->libre = 0;
				}
				$nrd = new TurnoGlobalRendereable();
				$nrd->turno = $turno;
				$nrd->bit_clinico = 0;
				$nrd->bit_nopsicologo = false;
				$nrd->bit_permisoturnos = $bit_permisoturnos;
				$nrd->cod_programa = $cod_programa;
				$nrd->voluntario = $this->Turno->Voluntario->findByCodPersona($turno['Turno']['cod_voluntario']);
				$nrd->css_class = 'cal_asignado';
				$rendereable->add_rendereable($nrd);
			}
			
			// Agregar rendereables para mini-botones de crear nuevo turno
		
			if ($bit_permisoturnos) {
				foreach ($calendario_clinico as $nom_dia => $v) {
					foreach ($horarios as $horario) {
						$rendereable = $calendarios_telefonicos[$cod_programa][$nom_dia][$horarios_a_indices[$horario]];
						if (!$rendereable->libre) {
							$nnrd = new TurnoMiniNuevoRendereable();
							$nnrd->nom_dia = $nom_dia;
							$nnrd->num_hora = ($horario == 'Mañana' ? 9 : 14);
							$nnrd->bit_clinico = 0;
							$nrd->bit_nopsicologo = false;
							$nnrd->css_class = 'cal_mininuevo';
							$nnrd->cod_programa = $cod_programa;
							$rendereable->add_rendereable($nnrd);
						}
					}
				}
			}
		}
		        
        // CALENDARIO DE OTROS TURNOS (separados en mañana y tarde, al igual que los telefónicos)
        
		$rendereables_otros = array(null, null);
		$calendario_otros = array('Lunes' => $rendereables_otros, 'Martes' => $rendereables_otros,
									'Miercoles' => $rendereables_otros, 'Jueves' => $rendereables_otros,
									'Viernes' => $rendereables_otros);
		
		foreach ($calendario_clinico as $nom_dia => $v) {
			foreach ($horarios as $horario) {
				$calendario_otros[$nom_dia][$horarios_a_indices[$horario]] = new RendereableComponent();
				$calendario_otros[$nom_dia][$horarios_a_indices[$horario]]->libre = 1;
				$nrd = new TurnoGlobalRendereable();
				$nrd->nom_dia = $nom_dia;
				$nrd->num_hora = ($horario == 'Mañana' ? 9 : 14);
				$nrd->bit_clinico = false;
				$nrd->bit_nopsicologo = true;
				$nrd->bit_permisoturnos = $bit_permisoturnos;
				$nrd->css_class = 'cal_vacio';
				$calendario_otros[$nom_dia][$horarios_a_indices[$horario]]->add_rendereable($nrd);
			}
		}
		
		// Modificar rendereables correspondientes a turnos existentes
		
		foreach ($turnos_otros as $turno) {
			if ($turno['Turno']['num_hora'] < 14) { // Turno mañana
				$rendereable = $calendario_otros[$turno['Turno']['nom_dia']][$horarios_a_indices['Mañana']];
			} else { // Turno tarde
				$rendereable = $calendario_otros[$turno['Turno']['nom_dia']][$horarios_a_indices['Tarde']];
			}
			if ($rendereable->libre) {
				$rendereable->obliterate();
				$rendereable->libre = 0;
			}
			$nrd = new TurnoGlobalRendereable();
			$nrd->turno = $turno;
			$nrd->bit_clinico = false;
			$nrd->bit_nopsicologo = true;
			$nrd->bit_permisoturnos = $bit_permisoturnos;
			$nrd->cod_programa = $cod_programa;
			$nrd->voluntario = $this->Turno->Voluntario->findByCodPersona($turno['Turno']['cod_voluntario']);
			$nrd->css_class = 'cal_asignado';
			$rendereable->add_rendereable($nrd);
		}
		
		// Agregar rendereables para mini-botones de crear nuevo turno
	
		if ($bit_permisoturnos) {
			foreach ($calendario_clinico as $nom_dia => $v) {
				foreach ($horarios as $horario) {
					$rendereable = $calendario_otros[$nom_dia][$horarios_a_indices[$horario]];
					if (!$rendereable->libre) {
						$nnrd = new TurnoMiniNuevoRendereable();
						$nnrd->nom_dia = $nom_dia;
						$nnrd->num_hora = ($horario == 'Mañana' ? 9 : 14);
						$nnrd->bit_clinico = false;
						$nnrd->bit_nopsicologo = true;
						$nnrd->css_class = 'cal_mininuevo';
						$rendereable->add_rendereable($nnrd);
					}
				}
			}
		}
		
		$this->set('calendario_otros', $calendario_otros);
		
		// Preparar elementos para helper
		
		$filas_horarios = array('Mañana','Tarde');
		foreach ($filas_horarios as $i => $v) {
			$filas_horarios[$i] = '<span style="font-size: x-small">'.$filas_horarios[$i].'</span>';
		}
		
		$this->set('programas', $programas);
		$this->set('filas_telefonico', $filas_horarios);
		$this->set('calendarios_telefonicos', $calendarios_telefonicos);
		
		$columnas_calendario = array('Lunes','Martes','Miércoles','Jueves','Viernes');
		$this->set('columnas_calendario', $columnas_calendario);
		
		// Mostrar mensaje de éxito, si lo hay
		
		$this->set('msg_for_layout',$exito);
	}
	
	// Ver turnos de sólo un voluntario
	
	function ver($id = null, $exito = null) {
		$this->escribirHeader("Turnos");
		$this->Turno->recursive = 1;
		
		// Obtener ID de voluntario
		
		if ($id == null) {
			$cod_voluntario = $this->data['Voluntario']['cod_persona'];
			if ($cod_voluntario != null) {
				$voluntario = $this->Voluntario->findByCodPersona($cod_voluntario);
			} else {
				$voluntario = null;
			}
		} else {
			$cod_voluntario = $id;
			$voluntario = $this->Voluntario->findByCodPersona($cod_voluntario);
		}
		
		// Es este voluntario clínico o no psicológico?
		
		$bit_clinico = $voluntario['Voluntario']['bit_clinico'];
		$bit_nopsicologo = ($voluntario['Voluntario']['est_rol'] == 'Voluntario no psicologo');
		
		$bit_permisoturnos = $this->_check_permission('turnos', 'modificar');
		
		// Mostrar los turnos correspondientes al voluntario (separados en clínicos y telefónicos)

		$turnos = $this->Turno->findAllByCodVoluntario($cod_voluntario);
		$turnos_clinicos = array();
		$turnos_telefonicos = array();
		$turnos_otros = array();
		$i_c = $i_t = $i_o = 0;
		
	    foreach ($turnos as $turno) {
        	switch ($voluntario['Voluntario']['est_rol']) {
        		case 'Voluntario':
        		case 'Administrativo': { // Turnos clínicos y telefónicos
        	        if ($turno['Box']['tip_box'] == 'Clinico' && $bit_clinico) {
        				$turnos_clinicos[$i_c++] = $turno;
        			} else if ($turno['Box']['tip_box'] == 'Telefonico') {
        				$turnos_telefonicos[$i_p++] = $turno;
        			}
        		} break;
        		case 'Voluntario no psicologo': { // Otros turnos
					$turnos_otros[$i_o++] = $turno;
        		} break;
        	}
        }

		// Generar matrices para calendarios
		
		$horas = array('9', '10', '11', '12', '13', '14', '15', '16', '17');
		$horas_a_indices = array_combine($horas, range(0, 8));
		
		$rendereables_clinico = array(null, null, null, null, null, null, null, null, null);
		$calendario_clinico = array('Lunes' => $rendereables_clinico, 'Martes' => $rendereables_clinico,
									'Miercoles' => $rendereables_clinico, 'Jueves' => $rendereables_clinico,
									'Viernes' => $rendereables_clinico);
		
		$horarios = array('Mañana', 'Tarde');
		$horarios_a_indices = array_combine($horarios, range(0, 1));
		
		$rendereables_telefonico = array(null, null);
		$calendario_telefonico = array('Lunes' => $rendereables_telefonico, 'Martes' => $rendereables_telefonico,
									   'Miercoles' => $rendereables_telefonico, 'Jueves' => $rendereables_telefonico,
									   'Viernes' => $rendereables_telefonico);
		
		$rendereables_otros = array(null, null);
		$calendario_otros = array('Lunes' => $rendereables_otros, 'Martes' => $rendereables_otros,
									   'Miercoles' => $rendereables_otros, 'Jueves' => $rendereables_otros,
									   'Viernes' => $rendereables_otros);
		
		// Rendereables turnos clínicos (sólo generarlos si el voluntario en cuestión
		// es voluntario clínico)
		
		if ($i_c > 0) {
			foreach ($calendario_clinico as $nom_dia => $v) {
				foreach ($horas as $num_hora) {
					$calendario_clinico[$nom_dia][$horas_a_indices[$num_hora]] = new RendereableComponent();
					$calendario_clinico[$nom_dia][$horas_a_indices[$num_hora]]->libre = 1;
					$nrd = new TurnoLocalRendereable();
					$nrd->nom_dia = $nom_dia;
					$nrd->num_hora = $num_hora;
					$nrd->voluntario = $voluntario;
					$nrd->bit_clinico = 1;
					$nrd->bit_permisoturnos = $bit_permisoturnos;
					$nrd->css_class = 'cal_vacio';
					$calendario_clinico[$nom_dia][$horas_a_indices[$num_hora]]->add_rendereable($nrd);
				}
			}
			
			foreach ($turnos_clinicos as $turno) {
				$rendereable = $calendario_clinico[$turno['Turno']['nom_dia']][$horas_a_indices[$turno['Turno']['num_hora']]];
				if ($rendereable->libre) {
					$rendereable->obliterate();
					$rendereable->libre = 0;
				}
				$nrd = new TurnoLocalRendereable();
				$nrd->turno = $turno;
				$nrd->bit_clinico = 1;
				$nrd->bit_permisoturnos = $bit_permisoturnos;
				$nrd->voluntario = $voluntario;
				if ($turno['Turno']['cod_caso'] != null) {
					$nrd->caso = $this->Caso->findByCodCaso($turno['Turno']['cod_caso']);
					$nrd->beneficiario = $this->Beneficiario->findByCodPersona($nrd->caso['Caso']['cod_beneficiario']);
					$nrd->css_class = 'cal_caso';
				} else {
					$nrd->css_class = 'cal_asignado';
				}
				$rendereable->add_rendereable($nrd);
			}
		}
		
		// Rendereables turnos telefónicos
		
		if ($i_t > 0) {
			foreach ($calendario_clinico as $nom_dia => $v) {
				foreach ($horarios as $horario) {
					$calendario_telefonico[$nom_dia][$horarios_a_indices[$horario]] = new RendereableComponent();
					$calendario_telefonico[$nom_dia][$horarios_a_indices[$horario]]->libre = 1;
					$nrd = new TurnoLocalRendereable();
					$nrd->nom_dia = $nom_dia;
					$nrd->num_hora = ($horario == 'Mañana' ? 9 : 14);
					$nrd->voluntario = $voluntario;
					$nrd->bit_clinico = 0;
					$nrd->bit_permisoturnos = $bit_permisoturnos;
					$nrd->css_class = 'cal_vacio';
					$calendario_telefonico[$nom_dia][$horarios_a_indices[$horario]]->add_rendereable($nrd);
				}
			}
			
			foreach ($turnos_telefonicos as $turno) {
				if ($turno['Turno']['num_hora'] < 14) { // Turno mañana
					$rendereable = $calendario_telefonico[$turno['Turno']['nom_dia']][$horarios_a_indices['Mañana']];
				} else { // Turno tarde
					$rendereable = $calendario_telefonico[$turno['Turno']['nom_dia']][$horarios_a_indices['Tarde']];
				}
				if ($rendereable->libre) {
					$rendereable->obliterate();
					$rendereable->libre = 0;
				}
				$nrd = new TurnoLocalRendereable();
				$nrd->turno = $turno;
				$nrd->bit_clinico = 0;
				$nrd->bit_permisoturnos = $bit_permisoturnos;
				$nrd->voluntario = $this->Turno->Voluntario->findByCodPersona($turno['Turno']['cod_voluntario']);
				$nrd->css_class = 'cal_asignado';
				$rendereable->add_rendereable($nrd);
			}
		}
		
		// Rendereables otros turnos
		
		if ($i_o > 0) {
			foreach ($calendario_clinico as $nom_dia => $v) {
				foreach ($horarios as $horario) {
					$calendario_otros[$nom_dia][$horarios_a_indices[$horario]] = new RendereableComponent();
					$calendario_otros[$nom_dia][$horarios_a_indices[$horario]]->libre = 1;
					$nrd = new TurnoLocalRendereable();
					$nrd->nom_dia = $nom_dia;
					$nrd->num_hora = ($horario == 'Mañana' ? 9 : 14);
					$nrd->voluntario = $voluntario;
					$nrd->bit_clinico = 0;
					$nrd->bit_nopsicologo = true;
					$nrd->bit_permisoturnos = $bit_permisoturnos;
					$nrd->css_class = 'cal_vacio';
					$calendario_otros[$nom_dia][$horarios_a_indices[$horario]]->add_rendereable($nrd);
				}
			}
			
			foreach ($turnos_otros as $turno) {
				if ($turno['Turno']['num_hora'] < 14) { // Turno mañana
					$rendereable = $calendario_otros[$turno['Turno']['nom_dia']][$horarios_a_indices['Mañana']];
				} else { // Turno tarde
					$rendereable = $calendario_otros[$turno['Turno']['nom_dia']][$horarios_a_indices['Tarde']];
				}
				if ($rendereable->libre) {
					$rendereable->obliterate();
					$rendereable->libre = 0;
				}
				$nrd = new TurnoLocalRendereable();
				$nrd->turno = $turno;
				$nrd->bit_clinico = 0;
				$nrd->bit_nopsicologo = true;
				$nrd->bit_permisoturnos = $bit_permisoturnos;
				$nrd->voluntario = $this->Turno->Voluntario->findByCodPersona($turno['Turno']['cod_voluntario']);
				$nrd->css_class = 'cal_asignado';
				$rendereable->add_rendereable($nrd);
			}
		}
		
		// Preparar elementos para helper
		
		$columnas_calendario = array('Lunes','Martes','Miércoles','Jueves','Viernes');
		$filas_clinico = array('9:00-10:00','10:00-11:00','11:00-12:00','12:00-13:00','13:00-14:00',
		                          '14:00-15:00','15:00-16:00','16:00-17:00','17:00-18:00');
		$filas_telefonico = $filas_otros = array('Mañana','Tarde');
		
		foreach ($filas_clinico as $i => $v) {
			$filas_clinico[$i] = '<span style="font-size: x-small">'.$filas_clinico[$i].'</span>';
		}
		foreach ($filas_telefonico as $i => $v) {
			$filas_telefonico[$i] = '<span style="font-size: x-small">'.$filas_telefonico[$i].'</span>';
		}
		
		$this->set('nom_voluntario', $voluntario['Persona']['nom_nombre'] . ' ' . $voluntario['Persona']['nom_appat']);
		$this->set('bit_clinico', $bit_clinico);
		$this->set('bit_nopsicologo', $bit_nopsicologo);
		$this->set('columnas_calendario', $columnas_calendario);
		$this->set('filas_clinico', $filas_clinico);
		$this->set('filas_telefonico', $filas_telefonico);
		$this->set('filas_otros', $filas_otros);
		$this->set('calendario_clinico', $calendario_clinico);
		$this->set('calendario_telefonico', $calendario_telefonico);
		$this->set('calendario_otros', $calendario_otros);
		
		// Mostrar mensaje de éxito, si lo hay
		
		$this->set('msg_for_layout',$exito);
	}
    
	function crear() {
		$this->escribirHeader("Asignar Turno");
		
		$bit_clinico = $this->data['Extra']['bit_clinico'];
		$bit_nopsicologo = $this->data['Extra']['bit_nopsicologo'];
		$cod_programa = $this->data['Extra']['cod_programa'];
		$nom_origenusuario = $this->data['Extra']['nom_origenusuario'];
		$this->set('bit_clinico', $bit_clinico);
		$this->set('nom_origenusuario', $nom_origenusuario);
		$this->set('bit_nopsicologo', $bit_nopsicologo);
		
		// Si no hay voluntario definido, generar menú con la lista de todos los voluntarios
		// (Mostrar sólo voluntarios que correspondan...)
		$cod_voluntario = $this->data['Turno']['cod_voluntario'];
		if ($cod_voluntario == null) {
			// Obtener todos los voluntarios (activos)
			$lista_voluntarios = array('' => '');
			if (! $bit_nopsicologo) {
				if ($bit_clinico) {
					$voluntarios = $this->Voluntario->findAll(array('bit_clinico' => '1', 'est_voluntario' => 'Activo',
						'NOT' => array('est_rol' => 'Voluntario no psicologo')), '', 'Persona.nom_appat ASC', '', '', 1);
				} else {
					$voluntarios = $this->Voluntario->findAll(array('Voluntario.cod_programa' => $cod_programa,
						'est_voluntario' => 'Activo', 'NOT' => array('est_rol' => 'Voluntario no psicologo')), '', 'Persona.nom_appat ASC', '', '', 1);
				}
			} else {
				$voluntarios = $this->Voluntario->findAll(array('est_voluntario' => 'Activo',
					'est_rol' => 'Voluntario no psicologo'), '', 'Persona.nom_appat ASC', '', '', 1);
			}
			// Para cada voluntario generar entrada del tipo (cod_voluntario => nom_voluntario)
			foreach ($voluntarios as $voluntario) {
				$cod_vol = $voluntario['Voluntario']['cod_persona'];
				$nom_vol = $voluntario['Persona']['nom_nombre'] . ' ' . $voluntario['Persona']['nom_appat'];
				$lista_voluntarios += array($cod_vol => $nom_vol);
			}
			$this->set('voluntarios', $lista_voluntarios);
			$this->set('cod_voluntario', null);
		} else {
			$this->set('cod_voluntario', $cod_voluntario);
		}
		
		if ($bit_clinico) {
			$this->set('boxes', $this->Box->generateList(array('tip_box' => 'Clínico', 'bit_vigente' => '1'), null, null,
															    '{n}.Box.cod_box', '{n}.Box.nom_box'));
		} else {
			$this->set('boxes', $this->Box->generateList(array('tip_box' => 'Telefónico', 'bit_vigente' => '1'), null, null,
															    '{n}.Box.cod_box', '{n}.Box.nom_box'));
		}
	}
	
	function crear2() {
		$nom_origenusuario = $this->data['Extra']['nom_origenusuario'];
		$bit_crearok = false;
		
		// Debemos verificar que no haya otro turno en el mismo horario asignado
		// al mismo voluntario
		
		$turno_existente = $this->Turno->find(array('Turno.cod_voluntario' => $this->data['Turno']['cod_voluntario'],
												'Turno.nom_dia' => $this->data['Turno']['nom_dia'],
												'Turno.num_hora' => $this->data['Turno']['num_hora']
												/*,'Turno.cod_box'	=> $this->data['Turno']['cod_box']*/));
		
		if ($turno_existente == null) {
			$bit_crearok = true;
		} else {
//			$box = $this->Box->find(array('Box.cod_box' => $this->data['Turno']['cod_box']));
//			if ($turno_existente['Box']['bit_clinico'] != $box['Box']['bit_clinico']) {
//				$bit_crearok = true;
//			}
		}
		
		if ($bit_crearok) {
			var_dump($this->data['Turno']);
			if ($this->Turno->save($this->data['Turno'])) {
				$mensaje = 40;
			} else {
				$mensaje = 41;
			}
		} else {
			$mensaje = 47;
		}
		
		if ($nom_origenusuario == 'index') {
			$this->redirect('/turnos/index/' . $mensaje);
		} else if ($nom_origenusuario == 'ver') {
			$this->redirect('/turnos/ver/' .
				$this->data['Turno']['cod_voluntario'] . '/' . $mensaje);
		}
	}

	function modificar() {
		$this->escribirHeader("Modificar Turno");
		
		$bit_clinico = $this->data['Extra']['bit_clinico'];
		$bit_nopsicologo = $this->data['Extra']['bit_nopsicologo'];
		$cod_programa = $this->data['Extra']['cod_programa'];
		$nom_origenusuario = $this->data['Extra']['nom_origenusuario'];
		$this->set('bit_clinico', $bit_clinico);
		$this->set('nom_origenusuario', $nom_origenusuario);
		$this->set('bit_nopsicologo', $bit_nopsicologo);
		
		// Si venimos del índice general (lista de todos los turnos), generar menú con la lista de todos
		// los voluntarios asociados al programa indicado o que sean voluntarios clínicos,
		// según corresponda, para permitir modificar también el voluntario
		$cod_voluntario = $this->data['Turno']['cod_voluntario'];
		if ($nom_origenusuario == 'index') {
			// Obtener todos los voluntarios según los criterios que correspondan
			$lista_voluntarios = array('' => '');
			if (! $bit_nopsicologo) {
				if ($bit_clinico) {
					$voluntarios = $this->Voluntario->findAll(array('bit_clinico' => '1', 'est_voluntario' => 'Activo',
						 'NOT' => array('est_rol' => 'Voluntario no psicologo')), '', 'Persona.nom_appat ASC', '', '', 1);
				} else {
					$voluntarios = $this->Voluntario->findAll(array('Voluntario.cod_programa' => $cod_programa,
						'est_voluntario' => 'Activo', 'NOT' => array('est_rol' => 'Voluntario no psicologo')), '', 'Persona.nom_appat ASC', '', '', 1);
				}
			} else {
				$voluntarios = $this->Voluntario->findAll(array('est_voluntario' => 'Activo',
					'est_rol' => 'Voluntario no psicologo'), '', 'Persona.nom_appat ASC', '', '', 1);
			}
			// Para cada voluntario generar entrada del tipo (cod_voluntario => nom_voluntario)
			foreach ($voluntarios as $voluntario) {
				$cod_vol = $voluntario['Voluntario']['cod_persona'];
				$nom_vol = $voluntario['Persona']['nom_nombre'] . ' ' . $voluntario['Persona']['nom_appat'];
				$lista_voluntarios += array($cod_vol => $nom_vol);
			}
			$this->set('voluntarios', $lista_voluntarios);
			$this->set('cod_voluntario', $cod_voluntario);
		}
		
		// También queremos que sea posible modificar el horario del turno...
		
		$this->set('dias', array_combine($this->dias, $this->dias));
		
		if ($bit_clinico) {
			$this->set('boxes', $this->Box->generateList(array('tip_box' => 'Clínico', 'bit_vigente' => '1'), null, null,
															    '{n}.Box.cod_box', '{n}.Box.nom_box'));
			$this->set('horas', array_combine($this->horas_clinico, $this->etiq_horas_clinico));
		} else {
			$this->set('boxes', $this->Box->generateList(array('tip_box' => 'Telefónico', 'bit_vigente' => '1'), null, null,
															    '{n}.Box.cod_box', '{n}.Box.nom_box'));
			$this->set('horas', array_combine($this->horas_noclinico, $this->etiq_horas_noclinico));
		}
	}
	
	function modificar2() {
		$nom_origenusuario = $this->data['Extra']['nom_origenusuario'];
		$bit_modificarok = false;
		
		// Si estamos haciendo un cambio de horario, debemos verificar que no haya otro turno en el
		// nuevo horario asignado al mismo voluntario al cual estamos haciendo la asignación
		
		$turno_original = $this->Turno->find(array('Turno.cod_turno' => $this->data['Turno']['cod_turno']));
		
		if ($turno_original['Turno']['cod_voluntario'] != $this->data['Turno']['cod_voluntario'] ||
			$turno_original['Turno']['nom_dia'] != $this->data['Turno']['nom_dia'] ||
			$turno_original['Turno']['num_hora'] != $this->data['Turno']['num_hora']) {
			$turno_existente = $this->Turno->find(array('Turno.cod_voluntario' => $this->data['Turno']['cod_voluntario'],
				'Turno.nom_dia' => $this->data['Turno']['nom_dia'],
				'Turno.num_hora' => $this->data['Turno']['num_hora']));
			if ($turno_existente == null) {
				$bit_modificarok = true;
			} else {
				$box = $this->Box->find(array('cod_box' => $this->data['Turno']['cod_box']));
				if ($turno_existente['Box']['bit_clinico'] != $box['Box']['bit_clinico']) {
					$bit_modificarok = true;
				}
			}
		} else { // Sólo modificación de box
			$bit_modificarok = true;
		}
		
		if ($bit_modificarok) {
			if ($this->Turno->save($this->data['Turno'])) {
				$mensaje = 18;
			} else {
				$mensaje = 19;
			}
		} else {
			$mensaje = 47;
		}
		
		if ($nom_origenusuario == 'index') {
			$this->redirect('/turnos/index/' . $mensaje);
		} else if ($nom_origenusuario == 'ver') {
			$this->redirect('/turnos/ver/' .
				$this->data['Turno']['cod_voluntario'] . '/' . $mensaje);
		}
	}

	function eliminar() {
		$cod_turno = $this->data['Turno']['cod_turno'];
		$cod_voluntario = $this->data['Turno']['cod_voluntario'];
		$nom_origenusuario = $this->data['Extra']['nom_origenusuario'];
		
		// [Diego Jorquera] No permitir borrar turnos con casos clínicos asignados
		
		$turno = $this->Turno->findByCodTurno($cod_turno);
		if ($turno['Turno']['cod_caso'] == null) {
			if ($this->Turno->del($cod_turno)) {
				$mensaje = 42;
			} else {
				$mensaje = 43;
			}
		} else {
			$mensaje = 46;
		}
		
		if ($nom_origenusuario == 'index') {
			$this->redirect('/turnos/index/' . $mensaje);
		} else if ($nom_origenusuario == 'ver') {
			$this->redirect('/turnos/ver/' .
				$this->data['Turno']['cod_voluntario'] . '/' . $mensaje);
		}
	}

	function baja_voluntario($cod_voluntario) {
		// [Diego Jorquera] Buscar todos los turnos del voluntario
		$turnos = $this->Turno->findAllByCodVoluntario($cod_voluntario);
		
		// Para cada turno del voluntario, eliminarlo...
		foreach ($turnos as $turno) {
			$cod_turno = $turno['Turno']['cod_turno'];
			$cod_caso = $turno['Caso']['cod_caso'];
			
			if ($cod_caso != null) {
				// ...pero si un turno tiene asociado un caso, antes debemos pasar ese caso a
				// la fila de derivaciones, para reasignarlo a otro voluntario clínico (se
				// cambia su estado a "Derivacion")
				$data_caso = array('Caso' => array());
				$data_caso['Caso']['cod_caso'] = $cod_caso;
				$data_caso['Caso']['est_caso'] = 'Pendiente';
				$this->Caso->save($data_caso);
			}
			
			// Eliminar turno finalmente
			$this->Turno->del($cod_turno);
		}
	}
	
}
?>
