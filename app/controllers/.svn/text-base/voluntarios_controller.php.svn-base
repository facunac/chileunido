<?php
	class VoluntariosController extends AppController
	{
		var $name = "Voluntario";
		var $uses = array("Programa", "Comuna", "Voluntario", "Turno","Role", "Comentario", "Caso", "Seguimiento");
		
		function index()
		{
			$value=$this->Session->read('nombre_completo');
			$this->set('name_for_layout',$value);
			$this->set('titulo_pagina',"Gestiï¿½n de Voluntarios");
			
			//$this->set('programas', $this->Programa->findAll());
			//$this->set('comunas', $this->Comuna->findAll());
			$programas=$this->Programa->getAllAsArray();
			$this->set('programas', $programas);
			$comunas=$this->Comuna->getAllAsArray();
			$this->set('comunas', $comunas);
			
			//se obtienen los valores posibles de los estados del voluntario como un arreglo
			$estados=$this->Voluntario->getPossibleValues('est_voluntario');
			$this->set('estados', $estados);
			
			
		}
		
		function buscar()
		{
			$value=$this->Session->read('nombre_completo');
			$this->set('name_for_layout',$value);
			$this->set('titulo_pagina',"Bï¿½squeda de Voluntarios");
			
			$nom_nombre=$this->data['Voluntario']['nom_nombre'];
			$nom_appat=$this->data['Voluntario']['nom_appat'];
			$nom_apmat=$this->data['Voluntario']['nom_apmat'];
			$num_telefono1=$this->data['Voluntario']['num_telefono1'];
			$est_voluntario=$this->data['Voluntario']['est_voluntario'];
			$cod_comuna=$this->data['Voluntario']['cod_comuna'];
			$nom_login=$this->data['Voluntario']['nom_login'];
			$cod_programa=$this->data['Voluntario']['cod_programa'];
			
			
			
			//si la comuna es no vacï¿½a, se filtra, sino, no
			$filtrocomuna=$cod_comuna?"and Persona.cod_comuna=$cod_comuna":"";
			
			//si el programa es no vacï¿½o, se filtra, sino, no
			$filtroprograma=$cod_programa?"and Voluntario.cod_programa=$cod_programa":"";
			
			$personas=$this->Voluntario->findAll("Persona.nom_nombre like '%$nom_nombre%' 
										and Persona.nom_appat like '%$nom_appat%' 
										and Persona.nom_apmat like '%$nom_apmat%'
										and Persona.num_telefono1 like '%$nom_appat%' 
										and Voluntario.est_voluntario like '%$est_voluntario%'
										and Voluntario.nom_login like '%$nom_login%'".
										$filtrocomuna."".$filtroprograma, "", "", "", "", 2);
			
			$comunas=$this->Comuna->getAllAsArray();
			$this->set('comunas', $comunas);
			
			$programas=$this->Programa->getAllAsArray();
			$this->set('programas', $programas);
			
			$this->set('personas', $personas);
		}
		
		function modificar()
		{
			$value=$this->Session->read('nombre_completo');
			$this->set('name_for_layout',$value);
			$this->set('titulo_pagina',"Modificar Voluntario");
			
			// Obtenemos el cï¿½digo del voluntario a modificar
			$codigo= $this->data['Voluntario']['cod_persona'];
			
			$persona=$this->Voluntario->findByCodPersona($codigo);
			$this->set('persona',$persona);
			
			$comunas=$this->Comuna->getAllAsArray();
			$this->set('comunas', $comunas);
			
			$turnos_dias=$this->Turno->getDias();
			$this->set('turnos_dias',$turnos_dias);
			
			//$roles = array('Voluntario', 'Administrativo', 'Voluntario no psicologo');
			$roles = $this->Voluntarios->getPossibleValues('est_rol');
			$this->set('roles',$roles);
		}
		
		function resultado_modificar()
		{
			$value=$this->Session->read('nombre_completo');
			$this->set('name_for_layout',$value);
			$this->set('titulo_pagina',"Resultado Modificar Voluntario");
			
			// Obtenemos el cï¿½digo del voluntario a modificar
			$codigo= $this->data['Voluntario']['cod_persona'];
			$num_turnos=$this->data['Voluntario']['num_turnos'];
			
			for($i=1;$i<=$num_turnos;$i++)
			{
				$t_dia=$this->Turno->numeroDias($this->data['Voluntario']['nom_turno_dia_'.$i]);
				$t_inicio=$this->data['Voluntario']['nom_turno_inicio_'.$i];
				$t_fin=$this->data['Voluntario']['nom_turno_fin_'.$i];
				$cod_turno=$this->data['Voluntario']['cod_turno_'.$i];
				
				//echo $t_dia," ",$t_inicio," ", $t_fin," ",$cod_turno,"<br/>";
				
				$res= $this->Turno->query("UPDATE turnos SET nom_dia='".$t_dia."', hor_inicio='".$t_inicio."', hor_fin='".$t_fin."' WHERE cod_turno='".$cod_turno."';");
				
				
			}
		}
		function confirmar_baja($codPersona)
		{
			$this->escribirHeader("Desactivar Voluntario");
			$persona=$this->Voluntario->findByCodPersona($codPersona);
			$query_soloyo="SELECT nom_nombre, nom_appat, cod_persona FROM personas INNER JOIN casos ON casos.cod_beneficiario=personas.cod_persona WHERE est_caso='Activo' AND cod_soloyo=".$codPersona;
			$SoloYos=$this->Caso->query($query_soloyo);

			$cantTurnos=$this->Turno->findCount("Turno.cod_voluntario=".$codPersona);
			$this->set('SoloYos',$SoloYos);
			$this->set('cantTurnos',$cantTurnos);
			$this->set('persona',$persona['Persona']);
			$this->set('nombreVoluntario',$persona['Persona']['nom_nombre']." ".$persona['Persona']['nom_appat']." ".$persona['Persona']['nom_apmat']);
		}
		function desasignarsoloyo($codpersona)
		{
			// 24-abr-2009
			//$this->Caso->execute("UPDATE casos SET cod_soloyo = NULL WHERE cod_voluntario=".$codpersona);
			$this->Caso->execute("UPDATE casos SET cod_soloyo = NULL WHERE cod_soloyo=".$codpersona);

			$this->Seguimiento->execute("UPDATE seguimientos SET cod_voluntarioproxrevision = NULL WHERE cod_voluntarioproxrevision=".$codpersona);
			$this->Session->setFlash("Se han desasiganos los solo yo del voluntario");
			$this->redirect('/voluntarios/confirmar_baja/'.$codpersona);
		}
		
		
		function confirmar_alta($codPersona)
		{
			$this->escribirHeader("Reactivar a Voluntario");
			$persona=$this->Voluntario->findByCodPersona($codPersona);
			$this->set('persona',$persona['Persona']);
			$this->set('nombreVoluntario',$persona['Persona']['nom_nombre']." ".$persona['Persona']['nom_appat']." ".$persona['Persona']['nom_apmat']);
		}
		function subir()
		{
			$comentario = $this->data['Alta']['comentario'];
			$meSelf = $this->Session->read('cod_voluntario');
			
			$persona = $this->Voluntario->Persona->read(null,$meSelf);
			
			$comentario = $persona['Persona']['nom_nombre'] . " " . $persona['Persona']['nom_appat'] . " lo ha activado debido a que: " . $comentario;
			
			$cod_persona = $this->data['Voluntario']['cod_persona'];
			
			$subject = $this->Voluntario->findByCodPersona($cod_persona);
			
			$subject['Voluntario']['est_voluntario'] = "Activo";
			
			$this->Voluntario->save($subject);

			
			$this->data['Comentario'] = array(
											'nom_comentario' => $comentario,
											'cod_persona' => $cod_persona,
											'cod_creador' => -10000,
										);
			
			$this->Comentario->save($this->data['Comentario']);
			
			
			$msg_for_layout = 1002;
			$this->redirect('/personas/ver/'.$cod_persona);
		}
		function bajar()
		{	
			$comentario = $this->data['Baja']['comentario'];
			$meSelf = $this->Session->read('cod_voluntario');
			
			$persona = $this->Voluntario->Persona->read(null,$meSelf);
			
			$comentario = $persona['Persona']['nom_nombre'] . " " . $persona['Persona']['nom_appat'] . " lo ha desactivado debido a:" . $comentario;
			
			$cod_persona = $this->data['Voluntario']['cod_persona'];
			
			$subject = $this->Voluntario->findByCodPersona($cod_persona);
			
			$subject['Voluntario']['est_voluntario'] = "Inactivo";
			
			// [Diego Jorquera] Esto llama al mÃ©todo en el controlador de turnos que elimina todos
			// los turnos del voluntario y deja en fila de derivaciÃ³n los casos que pudiera
			// tener asociados
			$this->requestAction('/turnos/baja_voluntario/' . $cod_persona);
			
			$this->Voluntario->save($subject);
			
			$this->data['Comentario'] = array(
											'nom_comentario' => $comentario,
											'cod_persona' => $cod_persona,
											'cod_creador' => -10000,
										);
			
			$this->Comentario->save($this->data['Comentario']);
			
			$msg_for_layout = 1001;
			$this->redirect('/personas/ver/'.$cod_persona);
		}
		function ver_libro()
		{
			$value=$this->Session->read('nombre_completo');
			$this->set('name_for_layout',$value);
			$this->set('titulo_pagina',"Libro de Asistencia");
			
			// Obtenemos el cï¿½digo del voluntario a modificar
			
		}
	}
?>
