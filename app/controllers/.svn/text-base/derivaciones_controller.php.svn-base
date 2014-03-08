<?php

class DerivacionesController extends AppController
{
	var $name = 'Derivaciones';
	var $uses = array('Persona', 'Caso', 'Turno', 'Tipocaso', 'Beneficiario', 'Seguimiento');
	var $components = array('Rendereable', 'TurnoRendereable');
	var $helpers = array('Html', 'Matrix', 'Form');
	
	// [Diego Jorquera] Mostrar fila de derivaciones (sólo Bernardita ve esta página)
	
	
	function index($exito = null) {
		$this->escribirHeader("Casos por Derivar");
			
		$this->Caso->recursive = 1;
		$pendientes = $this->Caso->findAllByEstCaso('Pendiente');

		$tupla = array();
		$n = 0;
		
		foreach($pendientes as $pendiente)
		{
			$aux = $pendiente;
			
			$resultado = $this->Tipocaso->findByCodTipocaso($pendiente['Caso']['cod_tipocaso']);
			$aux['Caso']['nom_tipocaso'] = $resultado['Tipocaso']['nom_tipocaso'];
			
			
			$resultado = $this->Persona->findByCodPersona($pendiente['Caso']['cod_beneficiario']);
			$aux['Caso']['cod_beneficiario'] = $pendiente['Caso']['cod_beneficiario'];
			$aux['Caso']['nom_beneficiario'] = $resultado['Persona']['nom_nombre']." ".$resultado['Persona']['nom_appat']." ".$resultado['Persona']['nom_apmat'];
			$aux['Caso']['num_telefono'] = $resultado['Persona']['num_telefono1']." ".$resultado['Persona']['num_telefono2'];
			/*
			$resultado2 = $this->Beneficiario->findByCodPersona($pendiente['Caso']['cod_beneficiario']);
		
			$aux['Caso']['nom_infobeneficiario'] = '
			Fecha Ingreso: '.$resultado2['Beneficiario']['fec_ingreso'].'
			Nombre: '.$aux['Caso']['nom_beneficiario'].'
			Tel&eacute;fono 1: '.$resultado['Persona']['num_telefono1'].'
			Tel&eacute;fono 2: '.$resultado['Persona']['num_telefono2'].'
			Comentario: '.$resultado2['Beneficiario']['nom_comentario'].'
			';
			*/
			$tupla[$n++] = $aux;
		}
		
		$pendientes = $tupla;
		
		$this->set('pendientes', $pendientes);
		
		// Mostrar mensaje de éxito, si lo hay
		
		$this->set('msg_for_layout',$exito);
	}
	
	// [Diego Jorquera] Muestra página para derivar caso a turno clínico
	// (El parámetro $id para indicar caso es temporal, la idea es que esta información
	// se pase a través de un formulario)
	
	function derivar() {
		$this->escribirHeader("Derivación");
		
		// Buscar caso cuyo código recibimos como parámetro
		
		$caso = $this->Caso->findByCodCaso($this->data['Caso']['cod_caso']);
		$this->set('cod_caso', $this->data['Caso']['cod_caso']);
		
		// Buscar sólo turnos clínicos, y sólo aquellos que no tengan ya un caso asignado
		
        $turnos_clinicos = $this->Turno->findAll(array('Box.tip_box' => 'Clínico',
        	'Turno.cod_caso' => null, 'Voluntario.bit_clinico' => '1',
        	'Voluntario.est_voluntario' => 'Activo'));

		// Generar calendario clínico
		
		$horas = array('9', '10', '11', '12', '13', '14', '15', '16', '17');
		$horas_a_indices = array_combine($horas, range(0, 8));
		
		$rendereables_clinico = array(null, null, null, null, null, null, null, null, null);
		$calendario_clinico = array('Lunes' => $rendereables_clinico, 'Martes' => $rendereables_clinico,
									'Miercoles' => $rendereables_clinico, 'Jueves' => $rendereables_clinico,
									'Viernes' => $rendereables_clinico);
		
		foreach (array_keys($calendario_clinico) as $nom_dia) {
			foreach ($horas as $num_hora) {
				$calendario_clinico[$nom_dia][$horas_a_indices[$num_hora]] = new RendereableComponent();
				$calendario_clinico[$nom_dia][$horas_a_indices[$num_hora]]->libre = 1;
				$nrd = new TurnoDerivacionRendereable();
				$nrd->nom_dia = $nom_dia;
				$nrd->num_hora = $num_hora;
				$nrd->bit_clinico = 1;
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
			$nrd = new TurnoDerivacionRendereable();
			$nrd->turno = $turno;
			$nrd->caso = $caso;
			$nrd->bit_clinico = 1;
			$nrd->voluntario = $this->Turno->Voluntario->findByCodPersona($turno['Turno']['cod_voluntario']);
			$nrd->css_class = 'cal_asignado';
			$rendereable->add_rendereable($nrd);
		}
		
		$filas_clinico = array('9:00-10:00', '10:00-11:00', '11:00-12:00', '12:00-13:00', '13:00-14:00',
							   '14:00-15:00', '15:00-16:00', '16:00-17:00', '17:00-18:00');
		foreach ($filas_clinico as $i => $v) {
			$filas_clinico[$i] = '<span style="font-size: x-small">'.$filas_clinico[$i].'</span>';
		}
		$this->set('filas_clinico', $filas_clinico);
		$this->set('calendario_clinico', $calendario_clinico);
		
		// Preparar elementos para helper
		
		$columnas_calendario = array('Lunes','Martes','Miércoles','Jueves','Viernes');
		$this->set('columnas_calendario', $columnas_calendario);
	}

	// [Diego Jorquera] Deriva caso a turno clínico
	
	function derivar2() {
		$cod_voluntario=$this->Session->read('cod_voluntario');
		$cod_actividad_solderivacion = 17;
		
		// Cambiar estado del caso de "Derivacion"
		
		$this->data['Caso'] = array('cod_caso' => $this->data['Turno']['cod_caso'], 'est_caso' => "Derivacion");
		$this->Caso->save($this->data['Caso']);
		
		// Asignar caso al turno
		
		$this->Turno->save($this->data['Turno']);
		$mensaje = 44;
		
		// Obtener nombre del voluntario a quien "pertenece" el turno
		
		$turno = $this->Turno->findByCodTurno($this->Turno->getLastInsertID());
		$voluntario = $this->Persona->findByCodPersona($turno['Turno']['cod_voluntario']);
		$nom_voluntario = $voluntario['Persona']['nom_nombre'] . ' ' . $voluntario['Persona']['nom_appat'];
		
		// Agregar seguimiento de derivación (para fines estadísticos), incluyendo comentario
		// indicando a quién fue derivado el paciente
		
		$data_seg_derivacion = array('Seguimiento' => array());
		$data_seg_derivacion['Seguimiento']['cod_caso'] = $this->data['Caso']['cod_caso'];
		$data_seg_derivacion['Seguimiento']['cod_voluntario'] = $cod_voluntario;
		$data_seg_derivacion['Seguimiento']['cod_actividad'] = $cod_actividad_solderivacion;
		$data_seg_derivacion['Seguimiento']['fec_proxrevision'] = 0;
		$data_seg_derivacion['Seguimiento']['nom_comentario'] = 'Derivado a ' . $nom_voluntario;
		
		$this->Seguimiento->save($data_seg_derivacion);
		
		// TODO Redirigir (a dónde? posiblemente a la fila de derivaciones)
		
		$this->redirect('/derivaciones/index/' . $mensaje);
	}

	// La reactivación y el cierre de casos se hacen en BeneficiariosController (reactivar3 y retirar)
	
}

?>