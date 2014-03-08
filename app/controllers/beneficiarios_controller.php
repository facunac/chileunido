<?php
	class BeneficiariosController extends AppController
	{
		var $name = "Beneficiario";
		var $uses = array("Caso","Tipocaso","Seguimiento","Beneficiario","Persona", "Voluntario", "Permisovoluntario", "Programa", "Comuna", "Perfil", "Permisoperfil", "Actividad", "Formulario", "Seccion", "Pregunta", "Dimension", "Subpregunta", "Respuestaficha", "Tipoingreso", "Turno", "Convenio");
		var $components = array ('Pagination','Expermission'); // [Gabriela] para paginacion
		var $helpers = array('Pagination','Excel','Permisoschecker'); // [Gabriela] para paginacion
		
		
		//#####################################################
		//#####################################################
		//#####################################################
		//#####################################################
		function index($exito=null)
		{
			// Se mandan algunas variables al layout para que se vea bien COPIAR SIEMPRE Y CAMBIAR NOMBRE PAGINA
			$this->escribirHeader("Beneficiarios");			
			
			// [Javier] InformaciÃ¯Â¿Â½n de despliegue en el panel de tareas:
			
			// [Javier] --- MI PERFIL ---
			
			// [Javier] Se obtiene la informaciÃ¯Â¿Â½n del perfil
			$cod_voluntario = $this->Session->read('cod_voluntario');
			$this->set('cod_voluntario', $cod_voluntario);
			
			// [Javier] Info de persona
			$persona = $this->Persona->find(array("Persona.cod_persona"=> $cod_voluntario  ));
			
			$this->set('nombre_completo', $persona['Persona']['nom_nombre']." ".$persona['Persona']['nom_appat']." ".$persona['Persona']['nom_apmat'] );
			$this->set('nom_email', $persona['Persona']['nom_email']);

			// [Javier] Info de voluntario
			$voluntario=$this->Voluntario->find(array("Voluntario.cod_persona"=> $cod_voluntario));
			
			// [Diego Jorquera] Incluimos si el voluntario es clÃ­nico, para ver si desplegamos
			// o no la secciÃ³n "Mis PÃ¤cientes ClÃ­nicos"
			$this->set('bit_clinico', $voluntario['Voluntario']['bit_clinico']);
			$this->set('fec_ingreso', $voluntario['Voluntario']['fec_ingreso'] );
			$this->set('est_voluntario', $voluntario['Voluntario']['est_voluntario'] );

			// [Javier] Info de programa
			$cod_programa = $voluntario['Voluntario']['cod_programa'];
			$programa = $this->Programa->find(array("Programa.cod_programa"=> $cod_programa ));
			$this->set('programa', $voluntario['Programa']['nom_programa'] );
	
			// [Javier] --- FIN DE MI PERFIL ---
			
			$comunas=$this->Comuna->getAllAsArray();
			$this->set('comunas', $comunas);
			
			$convenios=$this->Convenio->getAllAsArray();
			$this->set('convenios', $convenios);
			
			
			$msg = '';
			$this->set('msg', $msg);
			
			$fecha_actual = date("Y-m-d");
			$estado = "Activo";
			$seguimientos = array();
			$seguimientos_atrasados = array();
			$seguimientos2 = array();
			$seguimientos3 = array();
			$aux = array();
			
			// Buscar todos los casos activos
			
			$seguimientos=$this->Seguimiento->query("SELECT seguimientos.*, casos.cod_caso, beneficiarios.tip_rolfamilia, personas.*, tipocasos.nom_tipocaso 
				FROM seguimientos INNER JOIN casos ON casos.cod_caso=seguimientos.cod_caso 
				INNER JOIN tipocasos ON tipocasos.cod_tipocaso=casos.cod_tipocaso 
				INNER JOIN programas ON tipocasos.cod_programa=programas.cod_programa 
				NATURAL JOIN (SELECT max(num_evento) AS num_evento  FROM seguimientos GROUP BY cod_caso) AS t 
				INNER JOIN beneficiarios ON beneficiarios.cod_persona=casos.cod_beneficiario NATURAL JOIN personas
				WHERE fec_proxrevision = CURDATE() AND est_caso='Activo' AND seguimientos.cod_voluntarioproxrevision=".$cod_voluntario." AND programas.cod_programa=".$cod_programa);
			
			$seguimientos_atrasados=$this->Seguimiento->query("SELECT seguimientos.*, casos.cod_caso, beneficiarios.tip_rolfamilia, personas.*, tipocasos.nom_tipocaso 
				FROM seguimientos INNER JOIN casos ON casos.cod_caso=seguimientos.cod_caso 
				INNER JOIN tipocasos ON tipocasos.cod_tipocaso=casos.cod_tipocaso 
				INNER JOIN programas ON tipocasos.cod_programa=programas.cod_programa 
				NATURAL JOIN (SELECT max(num_evento) AS num_evento  FROM seguimientos GROUP BY cod_caso) AS t 
				INNER JOIN beneficiarios ON beneficiarios.cod_persona=casos.cod_beneficiario NATURAL JOIN personas
				WHERE fec_proxrevision < CURDATE() AND est_caso='Activo' AND seguimientos.cod_voluntarioproxrevision=".$cod_voluntario." AND programas.cod_programa=".$cod_programa);
		/*	
			$seguimientos2=$this->Seguimiento->query("SELECT seguimientos.*, casos.cod_caso, beneficiarios.tip_rolfamilia, personas.*, tipocasos.nom_tipocaso 
				FROM seguimientos INNER JOIN casos ON casos.cod_caso=seguimientos.cod_caso 
				INNER JOIN tipocasos ON tipocasos.cod_tipocaso=casos.cod_tipocaso 
				INNER JOIN programas ON tipocasos.cod_programa=programas.cod_programa 
				NATURAL JOIN (SELECT max(num_evento) AS num_evento  FROM seguimientos GROUP BY cod_caso) AS t 
				INNER JOIN beneficiarios ON beneficiarios.cod_persona=casos.cod_beneficiario NATURAL JOIN personas
				WHERE fec_proxrevision = CURDATE() AND est_caso='Activo' AND seguimientos.cod_voluntarioproxrevision IS NULL AND programas.cod_programa=".$cod_programa);
		*/	
			$seguimientos2b=$this->Seguimiento->query("SELECT seguimientos.*, casos.*, beneficiarios.tip_rolfamilia, personas.*, tipocasos.nom_tipocaso 
				FROM seguimientos INNER JOIN casos ON casos.cod_caso=seguimientos.cod_caso 
				INNER JOIN tipocasos ON tipocasos.cod_tipocaso=casos.cod_tipocaso 
				INNER JOIN programas ON tipocasos.cod_programa=programas.cod_programa 
				NATURAL JOIN (SELECT max(num_evento) AS num_evento  FROM seguimientos GROUP BY cod_caso) AS t 
				INNER JOIN beneficiarios ON beneficiarios.cod_persona=casos.cod_beneficiario NATURAL JOIN personas
				WHERE (casos.cod_soloyo IS NULL OR casos.cod_soloyo=".$cod_voluntario.") AND fec_proxrevision = CURDATE() AND est_caso='Activo' AND seguimientos.cod_voluntarioproxrevision IS NULL AND programas.cod_programa=".$cod_programa);

			$seguimientos2c=$this->Seguimiento->query("SELECT seguimientos.*, casos.*, beneficiarios.tip_rolfamilia, personas.*, tipocasos.nom_tipocaso, actividades.*
				FROM seguimientos INNER JOIN casos ON casos.cod_caso=seguimientos.cod_caso 
				INNER JOIN tipocasos ON tipocasos.cod_tipocaso=casos.cod_tipocaso 
				INNER JOIN programas ON tipocasos.cod_programa=programas.cod_programa 
				NATURAL JOIN (SELECT max(num_evento) AS num_evento  FROM seguimientos GROUP BY cod_caso) AS t 
				INNER JOIN beneficiarios ON beneficiarios.cod_persona=casos.cod_beneficiario NATURAL JOIN personas
				INNER JOIN actividades ON seguimientos.cod_actividad=actividades.cod_actividad
				WHERE (casos.cod_soloyo IS NULL OR casos.cod_soloyo=".$cod_voluntario.") AND fec_proxrevision = CURDATE() AND est_caso='Activo' AND seguimientos.cod_voluntarioproxrevision IS NULL AND programas.cod_programa=".$cod_programa);
			
			$seguimientos2=$seguimientos2c;
		/*	
			$seguimientos3=$this->Seguimiento->query("SELECT seguimientos.*, casos.cod_caso, beneficiarios.tip_rolfamilia, personas.*, tipocasos.nom_tipocaso 
				FROM seguimientos INNER JOIN casos ON casos.cod_caso=seguimientos.cod_caso 
				INNER JOIN tipocasos ON tipocasos.cod_tipocaso=casos.cod_tipocaso 
				INNER JOIN programas ON tipocasos.cod_programa=programas.cod_programa 
				NATURAL JOIN (SELECT max(num_evento) AS num_evento  FROM seguimientos GROUP BY cod_caso) AS t 
				INNER JOIN beneficiarios ON beneficiarios.cod_persona=casos.cod_beneficiario NATURAL JOIN personas
				WHERE fec_proxrevision < CURDATE() AND est_caso='Activo' AND seguimientos.cod_voluntarioproxrevision IS NULL AND programas.cod_programa=".$cod_programa);
		*/
			$seguimientos3b=$this->Seguimiento->query("SELECT seguimientos.*, casos.*, beneficiarios.tip_rolfamilia, personas.*, tipocasos.nom_tipocaso 
				FROM seguimientos INNER JOIN casos ON casos.cod_caso=seguimientos.cod_caso 
				INNER JOIN tipocasos ON tipocasos.cod_tipocaso=casos.cod_tipocaso 
				INNER JOIN programas ON tipocasos.cod_programa=programas.cod_programa 
				NATURAL JOIN (SELECT max(num_evento) AS num_evento  FROM seguimientos GROUP BY cod_caso) AS t 
				INNER JOIN beneficiarios ON beneficiarios.cod_persona=casos.cod_beneficiario NATURAL JOIN personas
				WHERE (casos.cod_soloyo IS NULL OR casos.cod_soloyo=".$cod_voluntario.") AND fec_proxrevision < CURDATE() AND est_caso='Activo' AND seguimientos.cod_voluntarioproxrevision IS NULL AND programas.cod_programa=".$cod_programa);

			$seguimientos3c=$this->Seguimiento->query("SELECT seguimientos.*, casos.*, beneficiarios.tip_rolfamilia, personas.*, tipocasos.nom_tipocaso, actividades.*
				FROM seguimientos INNER JOIN casos ON casos.cod_caso=seguimientos.cod_caso 
				INNER JOIN tipocasos ON tipocasos.cod_tipocaso=casos.cod_tipocaso 
				INNER JOIN programas ON tipocasos.cod_programa=programas.cod_programa 
				NATURAL JOIN (SELECT max(num_evento) AS num_evento  FROM seguimientos GROUP BY cod_caso) AS t 
				INNER JOIN beneficiarios ON beneficiarios.cod_persona=casos.cod_beneficiario NATURAL JOIN personas
				INNER JOIN actividades ON seguimientos.cod_actividad=actividades.cod_actividad
				WHERE (casos.cod_soloyo IS NULL OR casos.cod_soloyo=".$cod_voluntario.") AND fec_proxrevision < CURDATE() AND est_caso='Activo' AND seguimientos.cod_voluntarioproxrevision IS NULL AND programas.cod_programa=".$cod_programa);

			$seguimientos3=$seguimientos3c;
			
			/*$casosactivos = $this->Caso->findAll(array("Caso.est_caso" => $estado, "Tipocaso.cod_programa" => $cod_programa), "Caso.cod_caso",null,null,null,0);
			//var_dump($casosactivos);
			$i1 = $i2 = 0;
			
			foreach ($casosactivos as $i => $v) {
				// Para cada caso activo, recuperar el seguimiento mÃ¡s reciente (permite determinar si el caso
				// estÃ¡ al dÃ­a o no)
				$seguimiento_aux = $this->Seguimiento->find(array("Seguimiento.cod_caso" => $v['Caso']['cod_caso']), "", array("Seguimiento.num_evento" => "desc"), "", "", -1);
				if ($seguimiento_aux != null) {
		*/			/*if($v['Caso']['cod_soloyo']!=null) // codigo para arreglar retroactivamente los solo yo
					{
						$seguimiento_aux['Seguimiento']['cod_voluntarioproxrevision']=$v['Caso']['cod_soloyo'];
						$this->Seguimiento->save($seguimiento_aux);
					}*/
		/*			if ($seguimiento_aux['Seguimiento']['cod_voluntarioproxrevision'] == $cod_voluntario) {
					 	if ($seguimiento_aux['Seguimiento']['fec_proxrevision'] == $fecha_actual)
					 		$seguimientos[$i1++] = $seguimiento_aux;
						else if ($seguimiento_aux['Seguimiento']['fec_proxrevision'] < $fecha_actual)
							$seguimientos_atrasados[$i2++] = $seguimiento_aux;
					}
				}
			}
			
			// Almacenar seguimientos al dÃ­a (concatenando datos de beneficiario y tipo de caso)

			foreach($seguimientos as $i => $v) {
				$seguimientos[$i] += $this->Beneficiario->find(array("Persona.cod_persona" => $seguimientos[$i]['Caso']['cod_beneficiario']));
				$seguimientos[$i] += $this->Tipocaso->find(array("Tipocaso.cod_tipocaso" => $seguimientos[$i]['Caso']['cod_tipocaso']));
				//$seguimientos[$i]['Seguimiento']['fec_ejecucion']=$this->Seguimiento->toFecha($seguimientos[$i]['Seguimiento']['fec_ejecucion']);
			}
		*/	
			$this->set('seguimientos', $seguimientos);
			
			// Almacenar seguimientos atrasados (concatenando datos extra)
			
		/*	foreach($seguimientos_atrasados as $i => $v) {
				$seguimientos_atrasados[$i] += $this->Beneficiario->find(array("Persona.cod_persona" => $seguimientos_atrasados[$i]['Caso']['cod_beneficiario']));
				$seguimientos_atrasados[$i] += $this->Tipocaso->find(array("Tipocaso.cod_tipocaso" => $seguimientos_atrasados[$i]['Caso']['cod_tipocaso']));
				$seguimientos_atrasados[$i]['Seguimiento']['fec_ejecucion']=$this->Seguimiento->toFecha($seguimientos_atrasados[$i]['Seguimiento']['fec_ejecucion']);
			}
		*/	
			$this->set('seguimientos_atrasados', $seguimientos_atrasados);
			
			// [Stephanie]--- FIN DE MI MIS PACIENTES DE HOY ---
			
			//[Stephanie] Pacientes en general
			
		/*	$k = 0;
			
			foreach ($casosactivos as $i => $v) {
				$seguimiento_aux = $this->Seguimiento->find(array("Seguimiento.cod_caso" => $v['Caso']['cod_caso']),"", array("Seguimiento.num_evento"=> "desc"), "", "", 0);
				if ($seguimiento_aux != null) {
					if ($seguimiento_aux['Seguimiento']['cod_voluntarioproxrevision'] == NULL &&
					 	$seguimiento_aux['Seguimiento']['fec_proxrevision'] == $fecha_actual)
						$seguimientos2[$k++] = $seguimiento_aux;
				}
			}
			
			foreach ($seguimientos2 as $j => $v) {
                $seguimientos2[$j] += $this->Beneficiario->find(array("Persona.cod_persona" => $seguimientos2[$j]['Caso']['cod_beneficiario']));
                $seguimientos2[$j] += $this->Tipocaso->find(array("Tipocaso.cod_tipocaso" => $seguimientos2[$j]['Caso']['cod_tipocaso']));
				$seguimientos2[$j]['Seguimiento']['fec_ejecucion'] = $this->Seguimiento->toFecha($seguimientos2[$j]['Seguimiento']['fec_ejecucion']);								
			}
		*/				
			$this->set('seguimientos2', $seguimientos2);
			
			// [Stephanie]--- FIN DE MI Pacientes en general ---
			
			// [Stephanie] Pendientes
			
			// [Stephanie] Se buscan los seguimientos con fecha menor a la actual
			
		/*	$k = 0;
			
			foreach ($casosactivos as $i => $v) {
				$seguimiento_aux = $this->Seguimiento->find(array("Seguimiento.cod_caso" => $v['Caso']['cod_caso']),"", array("Seguimiento.num_evento"=> "desc"), "", "", 0);
				if ($seguimiento_aux != null) {
					if($seguimiento_aux['Seguimiento']['cod_voluntarioproxrevision'] == NULL &&
				 		$seguimiento_aux['Seguimiento']['fec_proxrevision'] < $fecha_actual)
						$seguimientos3[$k++] = $seguimiento_aux;
				}
			}
			
			foreach ($seguimientos3 as $j => $v) {
                $seguimientos3[$j] += $this->Beneficiario->find(array("Persona.cod_persona" => $seguimientos3[$j]['Caso']['cod_beneficiario']));
                $seguimientos3[$j] += $this->Tipocaso->find(array("Tipocaso.cod_tipocaso" => $seguimientos3[$j]['Caso']['cod_tipocaso']));
                $seguimientos3[$j]['Seguimiento']['fec_ejecucion'] = $this->Seguimiento->toFecha($seguimientos3[$j]['Seguimiento']['fec_ejecucion']);							
			}
		*/	
			$this->set('seguimientos3', $seguimientos3);
			
			// [Stephanie]--- FIN DE Pendientes ---
			
			$estado_der = "Derivacion";

			// [Diego Jorquera] Buscar turnos clÃ­nicos asociados a este voluntario
			
			$turnos = $this->Turno->findAll(array("Turno.cod_voluntario" => $cod_voluntario));
			$casos_derivados = array();
			
			// [Diego Jorquera] Tomamos todos los casos derivados (no se distinguen ya entre llamados
			// y no llamados)
			
			$k = 0;
			
			foreach ($turnos as $turno) {
				if ($turno['Turno']['cod_caso'] != null) {
					$caso = $this->Caso->find(array('Caso.cod_caso' => $turno['Turno']['cod_caso'], 'Caso.est_caso' => $estado_der));
					$casos_derivados[$k++] = $caso;
				}
			}

			foreach ($casos_derivados as $j => $v) {
				$casos_derivados[$j] += $this->Beneficiario->find(array("Persona.cod_persona" => $casos_derivados[$j]['Beneficiario']['cod_persona']));
				$casos_derivados[$j] += $this->Tipocaso->find(array("Tipocaso.cod_tipocaso" => $casos_derivados[$j]['Caso']['cod_tipocaso']));
			}
				
			// [Javier] Traspaso de casos llamados y no llamados a la vista
			
			if (count($casos_derivados) > 0) {
				$this->set('casos_derivados', $casos_derivados);
			}
			
			$this->set('msg_for_layout',$exito);
			
			
			/******************************/

			/******************************/
			
		}
		
		
		//#####################################################
		//#####################################################
		//#####################################################
		//#####################################################
		function buscar_beneficiario($inicial=null)
		{
			
			
			// Se mandan algunas variables al layout para que se vea bien COPIAR SIEMPRE Y CAMBIAR NOMBRE PAGINA
			$this->escribirHeader("Resultados Busqueda Beneficiario");

			$nom_nombre= $this->data['FormBuscar']['nom_nombre'];
			$nom_rut1=$this->data['Persona']['nom_rut'];
			$num_rutcodver=$this->data['Persona']['num_rutcodver'];
			$nom_rut=$nom_rut1.'-'.$num_rutcodver;
			
			$nom_appat= $this->data['FormBuscar']['nom_appat'];
			$nom_apmat= $this->data['FormBuscar']['nom_apmat'];
			$cod_comuna=$this->data['FormBuscar']['cod_comuna'];
			$cod_convenio=$this->data['FormBuscar']['cod_convenio'];
			
			$soloyo=$this->data['FormBuscar']['soloyo'];
			$cod_voluntario = $this->Session->read('cod_voluntario');
			
			$mensaje="";
			
			
			if($nom_nombre=="" && $nom_appat=="" && $nom_apmat=="" && $cod_comuna=="" && $cod_convenio=="" && $nom_rut1.$num_rutcodver=="" && $inicial==null && $soloyo!=1)
			{
				$mensaje= 27;
				$this->redirect('/beneficiarios/index/'.$mensaje);
			}
			else
			{

				$contador_resultados=0;
				
				//[Gabriela]El programa es aquÃ¯Â¿Â½l al que pertecece el voluntario
				
				$cod_persona=$this->Session->read('cod_voluntario');
				$voluntario= $this->Voluntario->find(array("Voluntario.cod_persona" => $cod_persona), array(), array(), "", "", -1);
				$cod_programa=$voluntario['Voluntario']['cod_programa'];
				$programa=$this->Programa->findByCodPrograma($cod_programa);
												
				if($inicial==null)
					{
					if($cod_convenio=="")
					{				
						if($cod_comuna!="") {
							$personas=$this->Persona->findAll(array("Persona.nom_rut" => "like %$nom_rut%", 
													"Persona.nom_nombre" => "like %$nom_nombre%", 
													"Persona.nom_appat" => "like %$nom_appat%",
													"Persona.nom_apmat" => "like %$nom_apmat%",
													"Persona.cod_comuna" => $cod_comuna));						
						}
						else
						{
							$personas=$this->Persona->findAll("Persona.nom_rut like '%$nom_rut%' 
													and Persona.nom_nombre like '%$nom_nombre%' 
													and Persona.nom_appat like '%$nom_appat%'
													and Persona.nom_apmat like '%$nom_apmat%'");						
						}
					}
					else
					{
						$personas=$this->Persona->findAll(array("Persona.nom_rut" => "like %$nom_rut%", 
													"Persona.nom_nombre" => "like %$nom_nombre%", 
													"Persona.nom_appat" => "like %$nom_appat%",
													"Persona.nom_apmat" => "like %$nom_apmat%",
													"Persona.cod_comuna" => "like %$cod_comuna%",
													"Beneficiario.cod_convenio" => $cod_convenio));
								
						/*$personas=$this->Persona->query("SELECT * FROM personas as Persona NATURAL JOIN beneficiarios 
															WHERE Persona.nom_rut like '%$nom_rut%' 
															and Persona.nom_nombre like '%$nom_nombre%' 
															and Persona.nom_appat like '%$nom_appat%'
															and Persona.nom_apmat like '%$nom_apmat%'
															and beneficiarios.cod_convenio=$cod_convenio");
							*/								
					}
					//print_r($personas);
					$mensaje=4;
					
					if($personas=="")
					{	
						$res=0;
						$this->set('resultado',$res);					
					}
					else
					{	
						if($inicial!=null)
						{
							$array_activo=array();							
							$array_retiro=array();		
							$array_pendiente=array();					
							$array_derivacion=array();
							
							$mensaje="";
						}
						else
						{	
							$res=1;
							$this->set('resultado',$res);
							$this->set('personas',$personas);
							
							// Dividimos los beneficiarios por aquÃ¯Â¿Â½llos que tienen casos activos, pendientes y en retiro
							
							//[Stephanie] PaginaciÃ¯Â¿Â½n
							$rowsPerPage = 10;
							$pageNum = 1;

							if(isset($_GET['page'])) 
								{$pageNum = $_GET['page'];}

							$offset = ($pageNum - 1) * $rowsPerPage;
												
							//[Stephanie] Fin PaginaciÃ¯Â¿Â½n
							
							$array_activo=array();
							$index1=0;
							$array_retiro=array();
							$index2=0;
							$array_pendiente=array();
							$index3=0;
							$array_derivacion=array();
							$index4=0;
																					
							foreach($personas as $p)
							{
								$codigo= $p['Persona']['cod_persona'];
																
								if( $soloyo != 1 )
								{
									$casos_activo= $this->Caso->query("
									SELECT casos.est_caso
									FROM casos,tipocasos 
									WHERE casos.cod_tipocaso=tipocasos.cod_tipocaso 
									AND tipocasos.cod_programa=".$cod_programa." 
									AND casos.cod_beneficiario=".$codigo."
									ORDER BY casos.est_caso
									LIMIT 0,1;"
									);
								} 
								else
								{
									$casos_activo= $this->Caso->query("
									SELECT casos.est_caso
									FROM casos,tipocasos 
									WHERE casos.cod_tipocaso=tipocasos.cod_tipocaso 
									AND tipocasos.cod_programa=".$cod_programa." 
									AND casos.cod_beneficiario=".$codigo."
									AND casos.cod_soloyo=".$cod_voluntario."
									ORDER BY casos.est_caso
									LIMIT 0,1;"
									);
								}
					
								
								
								// Se le agrega el nombre de la comuna a la persona, para poder mostrarlo adecuadamente en la vista
								$p['Persona']+=array('nom_comuna' => $p['Comuna']['nom_comuna']); 
								
								// Se le agrega el rol familiar que tiene
								$p['Persona']+=array('rol_familiar' => $p['Beneficiario']['tip_rolfamilia']);
								
								//[Gabriela] Se le agrega la edad estimada
								if($p['Persona']['ano_nacimiento']!=null)
									$edad= (int)date('Y')-$p['Persona']['ano_nacimiento'];
								else 
									$edad='-';
								$p['Persona']+=array('edad'=>$edad);
								if(count($casos_activo)) {
									if($casos_activo[0]['casos']['est_caso']=='Activo')
									{
										// Tiene un caso activo
										//$datos=$this->Persona->query("SELECT * FROM personas WHERE cod_persona='".$codigo."';");
										
										$array_activo+=array($index1 => $p['Persona']);
										$mensaje="";
										$index1++;
										$contador_resultados++;
										
									}
									else if($casos_activo[0]['casos']['est_caso']=='Retiro')
									{	
										$array_retiro+=array($index2 =>$p['Persona']);
										$mensaje="";
										$contador_resultados++;
										$index2++;
									}
									//[Gabriela] podrÃ¯Â¿Â½a tener casos en derivacion
									else if($casos_activo[0]['casos']['est_caso']=='Derivacion')
									{	
										$array_derivacion+=array($index4 =>$p['Persona']);
										$mensaje="";
										$contador_resultados++;
										$index4++;
									}
									// [Diego Jorquera] PodrÃ­an hacer casos pendientes de ser derivados
									else if($casos_activo[0]['casos']['est_caso']=='Pendiente')
									{	
										$array_pendiente+=array($index3 =>$p['Persona']);
										$mensaje="";
										$contador_resultados++;
										$index3++;
									}
								}
							}
						}
						
						//[Gabriela] Se ordenan los arrays para paginacion
												
						$this->set('array_activo',$array_activo);
						$this->set('array_retiro',$array_retiro);
						$this->set('array_derivacion',$array_derivacion);
						$this->set('array_pendiente', $array_pendiente);
					}				
				}
				else
				{
					$cod_region="";
					$array_activo=array();
					$array_retiro=array();
					$array_derivacion=array();
					$array_pendiente=array();
					$this->set('array_activo',$array_activo);
					$this->set('array_retiro',$array_retiro);
					$this->set('array_derivacion',$array_derivacion);
					$this->set('array_pendiente',$array_pendiente);
					$this->set('cod_region',13);
					$this->set('cod_comuna',null);
				}
				//[Gabriela] Se busca el formulario correcto para desplegar
				
				$this->set('contador_resultados',$contador_resultados);
				$this->set('msg_for_layout', $mensaje);				
			}								
		}
		
		//#####################################################
		//#####################################################
		//#####################################################
		//#####################################################
		function ingresar_nuevo0()
		{
			$this->escribirHeader("Ingresar Nuevo Beneficiario");
			
			$cod_persona=$this->Session->read('cod_voluntario');
			$voluntario= $this->Voluntario->find(array("Voluntario.cod_persona" => $cod_persona), array(), array(), "", "", -1);
			$cod_programa=$voluntario['Voluntario']['cod_programa'];
			$programa=$this->Programa->findByCodPrograma($cod_programa);
			
			$this->set('canRenderCaseClosure',$cod_programa>1);
			if($cod_programa == 1)
			{
				$this->set('defaultStyle','display:none;');
			}
			$this->set('sexoDefault',$cod_programa==1?'Femenino':null);
			$this->set('famDefault',$cod_programa==1?'Madre':'');
			$this->set('whomDefault',$cod_programa==1?"Por si mismo":null);
			
			//max numero de visitas diarias
			$max=$programa['Programa']['num_maxllamadas'];
			
			//Consulta que devuelva el listado de las fechas copadas 
			$fecha= $this->Seguimiento->query("
				SELECT `s`.`fec_proxrevision` AS `fec`
				FROM `seguimientos` AS `s` , `voluntarios` AS `v` 
				WHERE `v`.`cod_persona` = `s`.`cod_voluntario` 
				AND `v`.cod_programa = '".$cod_programa."'
				GROUP BY `s`.`fec_proxrevision` 
				HAVING COUNT( `s`.`fec_proxrevision` ) >= $max
				AND `s`.`fec_proxrevision` > now()
				ORDER BY `s`.`fec_proxrevision` 
			");
			
			//Calcula la fecha en la que deberia tener seguimiento segun la frecuencia			
			$hoy = date('j-n-Y');
			$fecha_aux=explode("-", $hoy); //$fecha[0]=dia, $fecha[1]=mes, $fecha[2]=aÃ¯Â¿Â½o
			$seguimiento= date('j-m-Y', mktime(0, 0, 0, $fecha_aux[1], $fecha_aux[0]+$programa['Programa']['num_frecuencia'],  $fecha_aux[2]));
					
			$this->set('seguimiento',$seguimiento);
			$this->set('hoy',date('j-m-Y'));
			$fechas_deshabilitadas="";
			
			foreach ($fecha as $fec)
			{	
				if($fechas_deshabilitadas!="") $fechas_deshabilitadas.=",";			
				
				//Convierte la fecha al formato deseado ej:20080528
				$aux1=$fec['s']['fec'];
				$aux2=explode("-", $aux1);
				$desabilitar= date('Ymd', mktime(0, 0, 0, $aux2[1], $aux2[2],  $aux2[0]));		
				
				//Concatena las fechas
				$fechas_deshabilitadas.="'".$desabilitar."'";				
			}
				
			//Pasa el arreglo con las fechas que hay de desabilitar
			$this->set('script', $fechas_deshabilitadas);
			
			//despliega el formulario correcto
			$formulario=$this->Formulario->find(array('Actividad.cod_programa'=>$cod_programa, 'Actividad.tip_actividad'=>'Inicial'));
				if(isset($formulario['Formulario']['cod_formulario']))
					$cod_formulario=$formulario['Formulario']['cod_formulario'];
				else 
					$cod_formulario=0;
				
			$this->set('cod_formulario',$cod_formulario);
			
			$regiones=$this->Comuna->getRegiones();
			$this->set('regiones', $regiones);
				
			$cod_region=13;
			$this->set('cod_region',$cod_region);
			
			$comunas=$this->Comuna->getAllAsArray($cod_region);
			$this->set('comunas', $comunas);
			
			/***********/
			
			// Via de acceso: Cual?
			$tipoingreso=$this->Tipoingreso->getAllAsArray(1);
			$this->set('tipoingreso', $tipoingreso);
				
			// Via de acceso: Tipo ingreso
			$medio=$this->Tipoingreso->getMedio();
			$this->set('medio', $medio);
				
			/*******/
			
			
			$programas=$this->Programa->getAllAsArray();
			$this->set('programas', $programas);
				
			$num_maxvisitas= $this->Programa->findByCodPrograma($cod_programa); 
			$this->set('num_visitas',$num_maxvisitas['Programa']['num_maxvisitas']);
				
			$prioridades=$this->Caso->getPrioridades();
			$this->set('prioridades', $prioridades);
				
			
			if($cod_programa == 2)
			{
				$tipos_casos=$this->Tipocaso->getAllAsArray($cod_programa);
				$this->set('tipos_casos', $tipos_casos);
			}
			else
			{
				$tipos_casos=$this->Tipocaso->findAll();
				$aux=array();
				for($n=0; $n<count($tipos_casos); $n++)
					if( $tipos_casos[$n]['Tipocaso']['cod_tipocaso']==3 || $tipos_casos[$n]['Tipocaso']['cod_tipocaso']==4 )
						$aux += array($tipos_casos[$n]['Tipocaso']['cod_tipocaso']=>$tipos_casos[$n]['Tipocaso']['nom_tipocaso']);
				$this->set('tipos_casos', $aux);
			
				$aux=array();
				for($n=0; $n<count($tipos_casos); $n++)
					if( $tipos_casos[$n]['Tipocaso']['cod_programa']==1 && $tipos_casos[$n]['Tipocaso']['bit_aborto']==0 && $tipos_casos[$n]['Tipocaso']['cod_tipocaso']!=3 && $tipos_casos[$n]['Tipocaso']['cod_tipocaso']!=4)
						$aux += array($tipos_casos[$n]['Tipocaso']['cod_tipocaso']=>$tipos_casos[$n]['Tipocaso']['nom_tipocaso']);
				$this->set('tipos_casos_noaborto', $aux);
			
				$aux=array();
				for($n=0; $n<count($tipos_casos); $n++)
					if( $tipos_casos[$n]['Tipocaso']['cod_programa']==1 && $tipos_casos[$n]['Tipocaso']['bit_aborto']==1 && $tipos_casos[$n]['Tipocaso']['cod_tipocaso']!=3 && $tipos_casos[$n]['Tipocaso']['cod_tipocaso']!=4)
						$aux += array($tipos_casos[$n]['Tipocaso']['cod_tipocaso']=>$tipos_casos[$n]['Tipocaso']['nom_tipocaso']);
				$this->set('tipos_casos_aborto', $aux);
			}
			
			
			//$tipos_casos=$this->Tipocaso->getAllAsArray($cod_programa);
			//var_dump($tipos_casos);
			//die();
			

			
			
				
			$tip_rolfamilia= $this->Beneficiario->getRolesFamilia();
			$this->set('tip_rolfamilia',$tip_rolfamilia);
			
			
			$voluntarios = array(0 => 'CUALQUIERA');
			$voluntarios+= array($this->Session->read('cod_voluntario') => '*** YO ***');
			$voluntarios+= $this->Voluntario->getAllAsArray($cod_programa);
			$this->set('voluntarios', $voluntarios);
				
			$estado="Derivacion";
				
			$numero_casos= $this->Caso->query("
									SELECT * 
									FROM casos
									WHERE casos.est_caso='".$estado."';"
								);
								
			$cont3=0;
			foreach($numero_casos as $caso)
				$cont3++;
							
			$num_maxvisitas=$programa['Programa']['num_maxvisitas'];
				
			$cupos =  $num_maxvisitas - $cont3;
			
			if($cupos>0) 
				$tip_proxrevision= $this->Seguimiento->getPossibleValues('tip_proxrevision');
							
			else 
				$tip_proxrevision =	array('Llamada');
		
			$this->set('tip_proxrevision',$tip_proxrevision);
			
			//$porquien = array( '1'=>'Por si mismo', '2'=>'Pareja', '3'=>'Hijo/a', '4'=>'Otro');
			
			
			/*********/
			// Tipo ingreso: Por qui�n llama?
			$porquien = $this->Caso->getPossibleValues('est_porquien');
			$this->set('porquien',$porquien);
			/********/
		
		}

		//#####################################################
		//#####################################################
		//#####################################################
		//#####################################################
		
		
		//****
		//********
		//************
		//****************
		//********************
		
		function ingresar_nuevo()
		{
				
			// Se mandan algunas variables al layout para que se vea bien COPIAR SIEMPRE Y CAMBIAR NOMBRE PAGINA
			$this->escribirHeader("Ingreso de Caso");	
			
			// Se rescata la informaciÃ¯Â¿Â½n desde el formulario
			$nom_nombre=$this->data['Persona']['nom_nombre'];
			$this->data['FormCrear']+=array('nom_nombre' => $nom_nombre);
			$nom_appat=$this->data['Persona']['nom_appat'];
			$this->data['FormCrear']+=array('nom_appat' => $nom_appat);
			$nom_apmat=$this->data['Persona']['nom_apmat'];
			$this->data['FormCrear']+=array('nom_apmat' => $nom_apmat);
			$nom_rut=$this->data['Persona']['nom_rut'];
			$num_rutcodver=$this->data['Persona']['num_rutcodver'];
			$this->data['FormCrear']+=array('nom_rut' => $nom_rut.'-'.$num_rutcodver);
			$nom_direccion=$this->data['Persona']['nom_direccion'];
			$this->data['FormCrear']+=array('nom_direccion' => $nom_direccion);
			$cod_comuna=$this->data['Persona']['cod_comuna'];
			$this->data['FormCrear']+=array('cod_comuna' => $cod_comuna);

			$contador = 0;
			// EST: por quien llama??
			$porquien = $this->Caso->getPossibleValues('est_porquien');
			$cod_porquien = 0;
			foreach($porquien as $por)
			{
				$contador++;
				/*
				 * XXXXXXXXXXXXXXXXXXXXXXXX
				 */
				if( strcmp(trim($this->data['Caso']['est_porquien']),trim($por)) == 0 )
				{
					$cod_porquien = $contador;
				}
			}

			$this->data['FormCrear']+=array('cod_porquien' => $cod_porquien);

			$num_telefono1_pre=$this->data['Persona']['num_telefono1_pre'];
			$num_telefono1_post=$this->data['Persona']['num_telefono1_post'];
			
			if($num_telefono1_pre!="")
				$num_telefono1_pre=$num_telefono1_pre.'-';
			$this->data['FormCrear']+=array('num_telefono1' => $num_telefono1_pre.$num_telefono1_post);
			
			$num_telefono2_pre=$this->data['Persona']['num_telefono2_pre'];
			$num_telefono2_post=$this->data['Persona']['num_telefono2_post'];
			
			if($num_telefono2_pre!="")
				$num_telefono2_pre=$num_telefono2_pre.'-';
			$this->data['FormCrear']+=array('num_telefono2' => $num_telefono2_pre.$num_telefono2_post);
			
			$cod_actividad_solderivacion = 16;

			//[Gabriela] Se toma la edad ingresada, y se convierte a un determinado aÃ¯Â¿Â½o
			
			
			$edad=$this->data['Persona']['ano_nacimiento'];
			
			if($edad==null || $edad<=0 || $edad>=100){				
				$anio_nac= null;
			} else {
				$anio_actual= (int)date('Y');
				$anio_nac= $anio_actual-$edad;
			}
			
			
			$this->data['FormCrear']+=array('ano_nacimiento' => $anio_nac);
				
			$tip_prioridad=$this->data['FormCrear']['tip_prioridad'];
			
			
			// Seleccionar de acuerdo a si existe riesgo de aborto o no
			//$cod_tipocaso=$this->data['FormCrear']['cod_tipocaso'];
			
			//El codigo del voluntario que estÃ¯Â¿Â½ en la sesion
			$cod_voluntario=$this->Session->read('cod_voluntario');
						
			$vol=$this->Voluntario->findByCodPersona($cod_voluntario);
			$cod_programa=$vol['Voluntario']['cod_programa'];
			
			if( $cod_programa == 1 ) // Sólo para Acoge
			{
				// Riesgo de aborto
				if($this->data['FormCrear']['cod_tipocaso'] == 3)
					$cod_tipocaso = $this->data['FormCrear']['cod_tipocaso_aborto'];
				else
					$cod_tipocaso = $this->data['FormCrear']['cod_tipocaso_noaborto'];
			}
			else
			{
				$cod_tipocaso=$this->data['FormCrear']['cod_tipocaso'];
			}
			
			///################################################
			///################################################
			///################################################
			///################################################
			///################################################
			///################################################
			///################################################
					
			// cual via de acceso a la fundacion
			/*
			 * XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
			 * XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
			 * XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
			 */
			$cod_tipoingreso=$this->data['Caso']['cod_tipoingreso'];
			
			
			$nom_comentario=$this->data['Caso']['nom_comentario'];
			$this->data['FormCrear']+=array('nom_comentario' => $nom_comentario);
			$cod_soloyo=$this->data['FormCrear']['cod_soloyo'];
			$tip_sexo= $this->data['Beneficiario']['tip_sexo'];
			$tip_proxrevision= $this->data['FormCrear']['tip_proxrevision'];
			
			//[Diego Jorquera] Si debe o no efectuarse un seguimiento, o una derivaciÃ³n
			$bit_nocerrar = $this->data['FormCrear']['bit_nocerrar'];
			$bit_derivar = ($this->data['FormCrear']['bit_derivar'] > -1);
			
			//El programa es aquÃ¯Â¿Â½l al que pertecece el voluntario
			
			$cod_persona=$this->Session->read('cod_voluntario');
			$voluntario= $this->Voluntario->findByCodPersona($cod_persona);
			$cod_programa=$voluntario['Voluntario']['cod_programa'];
			$programa=$this->Programa->findByCodPrograma($cod_programa);
			
			//Calcula la fecha en la que deberia tener seguimiento segun la frecuencia			
			$hoy = date('j-n-Y');
			$fecha_aux=explode("-", $hoy); //$fecha[0]=dia, $fecha[1]=mes, $fecha[2]=aÃ¯Â¿Â½o
			$seguimiento= date('j-m-Y', mktime(0, 0, 0, $fecha_aux[1], $fecha_aux[0]+$programa['Programa']['num_frecuencia'],  $fecha_aux[2]));
			
			$fec_proxrevision= $this->data['FormCrear']['fec_proxrevision'];
			if($fec_proxrevision==NULL)
				$this->data['FormCrear']['fec_proxrevision']=$seguimiento;
			if($this->data['FormCrear']['fec_ingreso'] == NULL || $this->data['FormCrear']['fec_ingreso'] == "")
			{
				$this->data['FormCrear']['fec_ingreso'] = date('Y-m-d H:i:s');
			}
			else
			{	
				$faux = explode("-",$this->data['FormCrear']['fec_ingreso']);
				$this->data['FormCrear']['fec_ingreso'] = date('Y-m-d H:i:s', mktime(0, 0, 0, $faux[1], $faux[0],  $faux[2]));
			
			}
			//$num_frecuencia=$this->data['FormCrear']['num_frecuencia'];
			
			$se_creo_caso=0;
			$se_creo_seguimiento=0;
			
			//[Gonzalo]: Hay que ver que no exista alguien con el mismo nombre, rut, telefono y edad.
			$conditions="";
			$conditions.="nom_rut ='".$this->data['FormCrear']['nom_rut']."' AND ";
			$conditions.="nom_nombre ='".$this->data['FormCrear']['nom_nombre']."' AND ";
			$conditions.="ano_nacimiento ='".$this->data['FormCrear']['ano_nacimiento']."' AND ";
			$conditions.="num_telefono1 ='".$this->data['FormCrear']['num_telefono1']."'";
			$busca=$this->Persona->find($conditions);
			if($busca!=null)
			{
				$this->Session->setFlash('Ya existe un beneficiario con ese nombre, rut, telefono y edad');
				$this->redirect('/beneficiarios/ver/'.$busca['Persona']['cod_persona']);
				return;
			}
			
			//Se crea la persona con la informaciÃ¯Â¿Â½n bÃ¯Â¿Â½sica
			if($this->Persona->save($this->data['FormCrear']))
			{
					
				// Se rescata el cÃ¯Â¿Â½digo de la Ã¯Â¿Â½ltima tupla ingresada
				$cod_persona=$this->Persona->getLastInsertId();
				$this->data['FormCrear']+=array('cod_persona' => $cod_persona);
				//$this->data['FormCrear']+=array('fec_ingreso' => 'CURRENT_TIMESTAMP');
				$this->data['FormCrear']+=array('tip_sexo' => $tip_sexo);
				//$this->data['FormCrear']+=array('tip_rolfamiliar' => 'Hijo');
				$tip_rolfamilia = $this->data['Beneficiario']['tip_rolfamilia'];
				$this->data['FormCrear']+=array('tip_rolfamilia' => $tip_rolfamilia);
				

				if(isset($this->data['Respuestaficha']['g24']))
				{
					$codSubpregunta = $this->data['Respuestaficha']['g24'];
					$sp = $this->Subpregunta->read(null, $codSubpregunta);
					$this->data['FormCrear']+=array('cod_convenio' => $sp['Subpregunta']['num_fila']);
				}
				else{
					$this->data['FormCrear']+=array('cod_convenio' => 8);
					$this->data['Respuestaficha']+=array('g24' => 243);
				}
					
										
				if($this->Beneficiario->save($this->data['FormCrear']))
				{
					//Se ingresa el caso
									
					//Se ingresa el cÃ¯Â¿Â½digo del beneficiario del caso
					$cod_beneficiario=$this->Beneficiario->getLastInsertId();
					$this->data['FormCrear']+=array('cod_beneficiario' => $cod_beneficiario);
									
					//Se ingresa el cÃ¯Â¿Â½digo del voluntario que atendiÃ¯Â¿Â½ el caso
					$this->data['FormCrear']+=array('cod_voluntario' => $this->Session->read('cod_voluntario'));
					
					//[Ignacio] se agrega el tipo de ingreso
					$this->data['FormCrear']['cod_tipoingreso']=$cod_tipoingreso;
					
					/*
					 * XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
					 */
					$this->data['FormCrear']+=array('est_porquien' => $cod_porquien);
					
					//[Diego Jorquera] Indicar si el caso va a estar en derivaciÃ³n
					if($bit_derivar) {
						$est_caso = "Pendiente";
						$this->data['FormCrear']+=array('est_caso' => $est_caso);
					}
					
					//[Gonzalo]
					if($cod_soloyo==0)
						$this->data['FormCrear']['cod_soloyo']=null;
					
					if($this->Caso->save($this->data['FormCrear']))
					{							
						$se_creo_caso=1;
						
						
						// Se crea el primer seguimiento, que corresponde a la creaciÃ¯Â¿Â½n de la ficha
						$cod_caso=$this->Caso->getLastInsertId();
						
						$this->data['FormCrear']+=array('cod_caso' => $cod_caso);
						//$this->data['FormCrear']+=array('fec_proxrevision' => $cod_caso);
						$this->data['FormCrear']+=array('tip_ingreso' => 'Llamada entrante');
						//$this->data['FormCrear']+=array('tip_ingreso' => 'Llamada entrante');
						
						//La actividad depende de a quÃ¯Â¿Â½ programa pertecece el voluntario
						$cod_persona=$this->Session->read('cod_voluntario');
						$voluntario= $this->Voluntario->findByCodPersona($cod_persona);
						$cod_programa=$voluntario['Voluntario']['cod_programa'];
						if($cod_programa=='1')
							//Es del programa Acoge
							$this->data['FormCrear']+=array('cod_actividad' => '1');
						else
							// Es del programa Comunicate
							$this->data['FormCrear']+=array('cod_actividad' => '2');
						if($cod_soloyo != 0)
							$this->data['FormCrear']+=array('cod_voluntarioproxrevision' => $cod_soloyo);
						else
							$this->data['FormCrear']+=array('cod_voluntarioproxrevision' => null);
						
						if($this->Seguimiento->save($this->data['FormCrear'])) {
							$se_creo_seguimiento=1;
							$mensaje=26;
						} else {
							$mensaje=5;
						}
					}
					else
						$mensaje=5;
				}
				else
					$mensaje=5;
			}
			else
			{
				$mensaje=5;
			}
			
			$this->set('msg_for_layout',$mensaje);
			
			//[Gabriela] Se guardan las respuestas del formulario estÃ¯Â¿Â½ndar
			
			if($se_creo_caso==1 && $se_creo_seguimiento==1)
			{
				//[Ignacio] Se busca el codigo de formulario
				$formulario=$this->Formulario->find(array('Actividad.cod_programa'=>$cod_programa, 'Actividad.tip_actividad'=>'Inicial'));
				if(isset($formulario['Formulario']['cod_formulario']))
					$cod_formulario=$formulario['Formulario']['cod_formulario'];
				else $cod_formulario=0;
				$cod_caso=$this->Caso->getLastInsertId();
				$cod_seguimiento= $this->Seguimiento->getLastInsertId();
				//variable para saber si se ingresaron las respuestas bien, cuando se conveirte en false es porque algo fallÃ¯Â¿Â½
				$exito=true;				
				
				$subpreguntas=$this->Formulario->getSubpreguntas($cod_formulario);

				
				//[Ignacio] la funcion anterior trae todas las subpreguntas del formulario
				foreach($subpreguntas as $subpregunta)
				{
					$tipo=$subpregunta['Subpregunta']['tip_tipoinput'];
					switch($tipo){
						case 'text':
						case 'checkbox':
							$dato=$this->data['Respuestaficha'][$subpregunta['Subpregunta']['cod_subpregunta']]?$this->data['Respuestaficha'][$subpregunta['Subpregunta']['cod_subpregunta']]:"";
						break;
						case 'radio':
							$dato=(isset($this->data['Respuestaficha']["g".$subpregunta['Subpregunta']['cod_grupo']]) && $this->data['Respuestaficha']["g".$subpregunta['Subpregunta']['cod_grupo']]==$subpregunta['Subpregunta']['cod_subpregunta'])?"1":"0";
						break;
						case 'textarea';
							$dato=$this->data['Respuestaficha'][$subpregunta['Subpregunta']['cod_subpregunta']]?$this->data['Respuestaficha'][$subpregunta['Subpregunta']['cod_subpregunta']]:"";

						break;
					}
						
					//[Gabriela] Se crea una respuestaficha para almacenar la respuesta a esta subpregunta
					
					$arreglo_respuesta=array('num_evento'=>$cod_seguimiento, 'cod_subpregunta'=>$subpregunta['Subpregunta']['cod_subpregunta'], 'nom_respuesta' => $dato);
					$this->Respuestaficha->create($arreglo_respuesta);
					if(!$this->Respuestaficha->save($arreglo_respuesta))	{
						$exito=false;
					}
				}
				
				if($exito) {
					$mensaje2="";
					// [Diego Jorquera] Si el beneficiario va a la fila de derivaciÃ³n, agregar seguimiento especial
					// que indique que se hizo esto (para fines estadÃ­sticos)
					if ($bit_derivar) {
						// Ojo, es necesario generar manualmente la clave primaria del nuevo seguimiento... por
						// alguna razÃ³n no se hace automÃ¡ticamente y eso hace que se sobreescriba el seguimiento
						// inicial, o algo asÃ­...
						$nuevo_cod_seguimiento = $cod_seguimiento + 1;
						$data_seg_derivacion = array('Seguimiento' => array());
						$data_seg_derivacion['Seguimiento']['num_evento'] = $nuevo_cod_seguimiento;
						$data_seg_derivacion['Seguimiento']['cod_caso'] = $cod_caso;
						$data_seg_derivacion['Seguimiento']['cod_voluntario'] = $voluntario['Voluntario']['cod_persona'];
						$data_seg_derivacion['Seguimiento']['cod_actividad'] = $cod_actividad_solderivacion;
						$data_seg_derivacion['Seguimiento']['fec_proxrevision'] = 0;
						$this->Seguimiento->save($data_seg_derivacion);
					}
				} else {
					$mensaje2=7;
				}
			}

			else $mensaje2="";
			
			// [Diego Jorquera] Si se desea cerrar el caso al cual el seguimiento estÃƒÂ¡ asociado, ir
			// al formulario de retiro de caso
			if (!$bit_nocerrar && $exito) {
				$this->redirect('/beneficiarios/retirar/'.$cod_caso.'/29');
			} else if ($bit_derivar && $exito) {
				$this->redirect('/beneficiarios/ver/'.$cod_beneficiario.'/110');
			} else {
				$mensaje=$mensaje.". ".$mensaje2;
				if($se_creo_caso==1 && $se_creo_seguimiento==1 && $exito && $cod_beneficiario)
					$this->redirect('/beneficiarios/ver/'.$cod_beneficiario);
				else
					$this->redirect('/beneficiarios/index/'.$mensaje);
					
			}			
		}
		
		//#####################################################
		//#####################################################
		//#####################################################
		//#####################################################
		function ingresar_nuevo2()
		{
			// Se mandan algunas variables al layout para que se vea bien COPIAR SIEMPRE Y CAMBIAR NOMBRE PAGINA
			$this->escribirHeader("Resultados BÃ¯Â¿Â½squeda Beneficiario");

			$this->set('nom_nombre', '');
			$this->set('nom_appat', '');
			$this->set('nom_apmat', '');
			$this->set('nom_rut', '');
				
			//El programa es aquÃ¯Â¿Â½l al que pertecece el voluntario
			
			$cod_persona=$this->Session->read('cod_voluntario');
			$voluntario= $this->Voluntario->findByCodPersona($cod_persona);
			$cod_programa=$voluntario['Voluntario']['cod_programa'];
			
			$programa= $this->Programa->findByCodPrograma($cod_programa);
			$num_maxvisitas=$programa['Programa']['num_maxvisitas'];
			
			$comunas=$this->Comuna->getAllAsArray();
			$this->set('comunas', $comunas);
			
			$programas=$this->Programa->getAllAsArray();
			$this->set('programas', $programas);
			
			$ingresos=$this->Caso->getIngresos();
			$this->set('ingresos', $ingresos);
			
			$prioridades=$this->Caso->getPrioridades();
			$this->set('prioridades', $prioridades);
			
			$tipos_casos=$this->Tipocaso->getAllAsArray($cod_programa);
			$this->set('tipos_casos', $tipos_casos);
			
			$tip_rolfamilia= $this->Beneficiario->getRolesFamilia();
			$this->set('tip_rolfamilia',$tip_rolfamilia);
			
			
			//[STEPHANIE] Permite o no derivar 
			$estado="Derivacion";
			
			$numero_casos= $this->Caso->query("
								SELECT count(*) 
								FROM casos
								WHERE casos.est_caso='".$estado."';"
							);
		
			
			$cupos = $numero_casos - $num_maxvisitas;
			
			if($cupos>0) {
			
				$tip_proxrevision= $this->Seguimiento->getPossibleValues('tip_proxrevision');
				$this->set('tip_proxrevision',$tip_proxrevision);}
				
				else {
				$tip_proxrevision="Llamada";
			
			}
			
			$voluntarios = array(0 => 'CUALQUIERA');
			$voluntarios+= array($this->Session->read('cod_voluntario') => '*** YO ***');
			$voluntarios+= $this->Voluntario->getAllAsArray($cod_programa);
			$this->set('voluntarios', $voluntarios);
			
			$this->set('tip_proxrevision',$tip_proxrevision);
		}
		
		//#####################################################
		//#####################################################
		//#####################################################
		//#####################################################
		function agregar_caso()
		{
			// Se mandan algunas variables al layout para que se vea bien COPIAR SIEMPRE Y CAMBIAR NOMBRE PAGINA
			$this->escribirHeader("Agregar Nuevo Caso A Beneficiario");
			
			//[Diego] Revisamos que no tenga casos abiertos, arreglando el bug de apretar atrÃ¯Â¿Â½s y volver a agregar caso. (redirecciona al index)
			$cod_beneficiario=$this->data['Caso']['cod_beneficiario'];
			
			$estado="Activo";
			
			$numero_casos= $this->Caso->query("
								SELECT cod_beneficiario
								FROM casos
								WHERE est_caso='".$estado."'
								AND cod_beneficiario='".$cod_beneficiario."';"
							);
			$casos_activos=count($numero_casos);			
			if($casos_activos>0){
				$msg=24;
				$this->redirect('/beneficiarios/index/'.$msg);
				exit();
				}
			
			//Obtenemos los Ingresos, Prioridades y Tipos de Casos para mostrarlos en los Select
			$cod_persona=$this->Session->read('cod_voluntario');
			$voluntario= $this->Voluntario->findByCodPersona($cod_persona);
			$cod_programa=$voluntario['Voluntario']['cod_programa'];

			$tipoingreso=$this->Tipoingreso->getAllAsArray(1);
			$this->set('tipoingreso', $tipoingreso);
			
			$medio=$this->Tipoingreso->getMedio();
			$this->set('medio', $medio);
			
			$prioridades=$this->Caso->getPossibleValues('tip_prioridad');
			$this->set('prioridades', $prioridades);
			
			$tipos_casos=$this->Tipocaso->getAllAsArray($cod_programa);
			$this->set('tipos_casos', $tipos_casos);
			
			$cod_benef=$this->data['Caso']['cod_beneficiario'];
			$this->set('cod_beneficiario', $cod_benef);
			
			$voluntarios = array(0 => 'CUALQUIERA');
			$voluntarios+= array($this->Session->read('cod_voluntario') => '*** YO ***');
			$voluntarios+= $this->Voluntario->getAllAsArray($cod_programa);
			$this->set('voluntarios', $voluntarios);
			
			//datos de la actividad (inicial)
			$formulario=$this->Formulario->find(array('Actividad.cod_programa' => $cod_programa, 'Actividad.tip_actividad'=>'Inicial'));
			$this->set('cod_formulario', $formulario['Formulario']['cod_formulario']);
		}
		
		//#####################################################
		//#####################################################
		//#####################################################
		//#####################################################
		function agregar_caso1()
		{
			// Se mandan algunas variables al layout para que se vea bien COPIAR SIEMPRE Y CAMBIAR NOMBRE PAGINA
			$this->escribirHeader("Ingreso de Caso");
			
			//[Diego] Revisamos que no tenga casos abiertos, arreglando el bug de apretar atrÃ¯Â¿Â½s y volver a agregar caso. (redirecciona al index)
			$cod_beneficiario=$this->data['FormCrear']['cod_beneficiario'];
			
			$estado="Activo";
			
			$numero_casos= $this->Caso->query("
								SELECT cod_beneficiario
								FROM casos
								WHERE est_caso='".$estado."'
								AND cod_beneficiario='".$cod_beneficiario."';"
							);
			$casos_activos=count($numero_casos);			
			if($casos_activos>0){
				$msg=24;
				$this->redirect('/beneficiarios/index/'.$msg);
				exit();
				}			
				
			// Se rescata la informaciÃ¯Â¿Â½n desde el formulario
			$cod_tipoingreso=$this->data['Caso']['cod_tipoingreso'];
			$tip_prioridad=$this->data['FormCrear']['tip_prioridad'];
			$cod_tipocaso=$this->data['FormCrear']['cod_tipocaso'];
			$cod_soloyo=$this->data['FormCrear']['cod_soloyo'];
			$cod_beneficiario=$this->data['FormCrear']['cod_beneficiario'];
			
			//Se ingresa el cÃ¯Â¿Â½digo del voluntario que atendiÃ¯Â¿Â½ el caso
			$this->data['FormCrear']+=array('cod_voluntario' => $this->Session->read('cod_voluntario'));
			$this->data['FormCrear']+=array('cod_tipoingreso' => $cod_tipoingreso);
			$cod_voluntario=$this->Session->read('cod_voluntario');
			
			//obtenemos el programa al que pertenece este caso
			$tupla = $this->Voluntario->findByCodPersona($cod_voluntario);
			if($tupla){
				$cod_programa = $tupla['Voluntario']['cod_programa'];
			}
			
			if($cod_soloyo==0)
				$this->data['FormCrear']['cod_soloyo']=null;
			
			$prog=$this->Programa->findByCodPrograma($cod_programa);
			$num_frecuencia=$prog['Programa']['num_frecuencia'];
			$se_creo_caso=0;
			$se_creo_seguimiento=0;
						
			//Creamos el caso
			if($this->Caso->save($this->data['FormCrear'])){
				$se_creo_caso=1;
				//Creamos informaciÃ¯Â¿Â½n de un nuevo seguimiento
				$tipodeingreso='Llamada_entrante';
				
				//echo "tip ingreso es".$tipodeingreso;
				//obtenemos fecha del prÃ¯Â¿Â½ximo seguimiento (segÃ¯Â¿Â½n frecuencia)
				$this->data['FormCrear']+=array('cod_caso' => $this->Caso->getLastInsertId());
				$hoy = date('j-n-Y');
				$fecha=explode("-", $hoy); //$fecha[0]=dia, $fecha[1]=mes, $fecha[2]=aÃ¯Â¿Â½o
				$seguimiento= date('n-j-Y', mktime(0, 0, 0, $fecha[1], $fecha[0]+$num_frecuencia,  $fecha[2]));
				
				$this->data['FormCrear']+=array('fec_proxrevision' => $seguimiento);
				$this->data['FormCrear']+=array('tip_proxrevision' => 'Llamada');
				
				$this->data['FormCrear']['tip_ingreso']=$tipodeingreso;
				
				$this->data['FormCrear']+=array('cod_actividad' => $cod_programa);
				
				$this->data['FormCrear']+=array('nom_comentario' => 'Agregado nuevo caso');
				
				if($cod_soloyo != 0)
					$this->data['FormCrear']+=array('cod_voluntarioproxrevision' => $cod_soloyo);
				else
					$this->data['FormCrear']+=array('cod_voluntarioproxrevision' => null);
				
				if($this->Seguimiento->save($this->data['FormCrear']))
				{	
					$mensaje=6;
					$se_creo_seguimiento=1;
				}
				else {
				
				$mensaje=5;}
			}
			else $mensaje=5;
			
			if($se_creo_caso==1 && $se_creo_seguimiento==1)
			{
				//[Ignacio] Se busca el codigo de formulario
				$formulario=$this->Formulario->find(array('Actividad.cod_programa'=>$cod_programa, 'Actividad.tip_actividad'=>'Inicial'));
				if(isset($formulario['Formulario']['cod_formulario']))
					$cod_formulario=$formulario['Formulario']['cod_formulario'];
				else $cod_formulario=0;
				$cod_caso=$this->Caso->getLastInsertId();
				$cod_seguimiento= $this->Seguimiento->getLastInsertId();
				//variable para saber si se ingresaron las respuestas bien, cuando se conveirte en false es porque algo fallÃ¯Â¿Â½
				$exito=true;				
				
				$subpreguntas=$this->Formulario->getSubpreguntas($cod_formulario);
				
				//[Ignacio] la funcion anterior trae todas las subpreguntas del formulario
				foreach($subpreguntas as $subpregunta)
				{
					$tipo=$subpregunta['Subpregunta']['tip_tipoinput'];
					switch($tipo){
						case 'text':
						case 'checkbox':
							$dato=$this->data['Respuestaficha'][$subpregunta['Subpregunta']['cod_subpregunta']]?$this->data['Respuestaficha'][$subpregunta['Subpregunta']['cod_subpregunta']]:"";
						break;
						case 'radio':
							$dato=(isset($this->data['Respuestaficha']["g".$subpregunta['Subpregunta']['cod_grupo']]) && $this->data['Respuestaficha']["g".$subpregunta['Subpregunta']['cod_grupo']]==$subpregunta['Subpregunta']['cod_subpregunta'])?"1":"0";
						break;
						case 'textarea';
							$dato=$this->data['Respuestaficha'][$subpregunta['Subpregunta']['cod_subpregunta']]?$this->data['Respuestaficha'][$subpregunta['Subpregunta']['cod_subpregunta']]:"";

						break;
					}
						
					//[Gabriela] Se crea una respuestaficha para almacenar la respuesta a esta subpregunta
					
					$arreglo_respuesta=array('num_evento'=>$cod_seguimiento, 'cod_subpregunta'=>$subpregunta['Subpregunta']['cod_subpregunta'], 'nom_respuesta' => $dato);
					$this->Respuestaficha->create($arreglo_respuesta);
					if(!$this->Respuestaficha->save($arreglo_respuesta))	{
						$exito=false;
					}
				}
				if($exito) $mensaje2=7;
				else $mensaje2="";
			}

			else $mensaje2="";
			
			$mensaje=$mensaje.". ".$mensaje2;
			$this->redirect('/beneficiarios/index/'.$mensaje);

		}
		
		function terminoEmbarazo($cod_caso = null, $mensaje = null)
		{
		
			
			$this->escribirHeader("Termino de Embarazo");
			$cod_voluntario=$this->Session->read('cod_voluntario');
			$this->set('cod_voluntario',$cod_voluntario);
			
			if ($cod_caso == null) {
				$codigo=$this->data['Seguimiento']['cod_caso'];
			} else {
				$codigo = $cod_caso;
				if ($mensaje != null) {
					$this->set('msg_for_layout', $mensaje);
				}
			}
			$this->set('cod_caso',$codigo);
			
			//[Diego]  obtenemos el programa al que pertenece este caso
			$tupla = $this->Voluntario->findByCodPersona($cod_voluntario);
			if($tupla){
				$cod_programa = $tupla['Voluntario']['cod_programa'];
			}
			
			//[Gabriela] Busco la actividad que sea de cierre de caso y que sea del programa actual
			$actividad = $this->Actividad->find(array('Actividad.cod_programa'=>$cod_programa, 'Actividad.tip_actividad'=>'Termino Embarazo'));
			
			$formulario=$this->Formulario->find(array('Formulario.cod_actividad'=>$actividad['Actividad']['cod_actividad']));

			$prox_seguimiento = mktime(0, 0, 0, date("n")+1, date("j"),   date("Y"));
			$this->set('prox_seguimiento',date('j-n-Y',$prox_seguimiento));

			$this->set('posible_nacimiento',date('j-n-Y'));
			
			
			if(isset($formulario['Formulario']['cod_formulario']))
					$cod_formulario=$formulario['Formulario']['cod_formulario'];
				else $cod_formulario=0;
			
			$this->set('cod_formulario',$cod_formulario);
		}
		function terminoEmbarazo1()
		{
					
			// Se mandan algunas variables al layout para que se vea bien COPIAR SIEMPRE Y CAMBIAR NOMBRE PAGINA
			$this->escribirHeader("Ingreso del Seguimiento");
			
			$cod_caso= $this->data['Seguimiento']['cod_caso'];
			
			
			
			
			if(isset($this->data['Respuestaficha'][1059]) &&  $this->data['Respuestaficha'][1059]!= "")
				$nom_comentario = $this->data['Respuestaficha'][1059] . " " . $this->data['Respuestaficha'][1060];
			else
				if(!isset($this->data['Seguimiento']['nom_comentario']))
				$nom_comentario="";
			else
				$nom_comentario= $this->data['Seguimiento']['nom_comentario'];
			
			
			
			
			$se_creo_seguimiento=0;
			$tip_actividad="";
			$cod_programa=1;
	
			//Se ingresa el cÃ¯Â¿Â½digo del voluntario que atendiÃ¯Â¿Â½ el caso
			$cod_voluntario=$this->Session->read('cod_voluntario');
			$this->data['Seguimiento']+=array('cod_voluntario' => $cod_voluntario);
						
			
			//obtenemos el programa al que pertenece este caso
			$tupla = $this->Voluntario->findByCodPersona($cod_voluntario);
			if($tupla){
				//[Gabriela] Se busca el cÃ¯Â¿Â½digo de la actividad adecuada
				$tip_actividad="Termino Embarazo";
			
				//[Gabriela] Se busca el cÃ¯Â¿Â½digo del programa actual y el cÃ¯Â¿Â½digo de la actividad
				
				$cod_programa=$tupla['Voluntario']['cod_programa'];
				$actividad=$this->Actividad->find(array('Actividad.tip_actividad'=>$tip_actividad, 'Actividad.cod_programa'=>$cod_programa));
				$cod_actividad=$actividad['Actividad']['cod_actividad'];
				
			}
			
			$this->data['Seguimiento']+=array('nom_comentario' => $nom_comentario);
			$this->data['Seguimiento']+=array('cod_caso' => $cod_caso);
			$this->data['Seguimiento']+=array('cod_actividad' => $cod_actividad);

			$fpr = $this->data['Seguimiento']['fec_proxrevision'];
			$fpr = split('-',$fpr);
			$fpr = $fpr[1] .'-'. $fpr[0] . '-'. $fpr[2]; 
			$this->data['Seguimiento']['fec_proxrevision'] = $fpr;
				
			if($this->Seguimiento->save($this->data['Seguimiento']))
			{
			
				$se_creo_seguimiento=1;
				$mensaje=12;
			
			}
			else {
				$mensaje=13;
			}
			
			
			//[Gabriela] Se guardan las respuestas del formulario estÃ¯Â¿Â½ndar
			
			if($se_creo_seguimiento==1)
			{
				//[Ignacio] Se busca el codigo de formulario
				$formulario=$this->Formulario->find(array('Actividad.cod_programa'=>$cod_programa, 'Actividad.tip_actividad'=>$tip_actividad));
				if(isset($formulario['Formulario']['cod_formulario']))
					$cod_formulario=$formulario['Formulario']['cod_formulario'];
				else $cod_formulario=0;
				//$cod_caso=$this->Caso->getLastInsertId();
				$cod_seguimiento= $this->Seguimiento->getLastInsertId();
				//variable para saber si se ingresaron las respuestas bien, cuando se conveirte en false es porque algo fallÃ¯Â¿Â½
				$exito=true;				
				
				$subpreguntas=$this->Formulario->getSubpreguntas($cod_formulario);
				
				//[Ignacio] la funcion anterior trae todas las subpreguntas del formulario
				foreach($subpreguntas as $subpregunta)
				{
					$tipo=$subpregunta['Subpregunta']['tip_tipoinput'];
					switch($tipo){
						case 'text':
						case 'datefield':
						case 'checkbox':
							$dato=$this->data['Respuestaficha'][$subpregunta['Subpregunta']['cod_subpregunta']]?$this->data['Respuestaficha'][$subpregunta['Subpregunta']['cod_subpregunta']]:"";
						break;
						case 'radio':
							$dato=(isset($this->data['Respuestaficha']["g".$subpregunta['Subpregunta']['cod_grupo']]) && $this->data['Respuestaficha']["g".$subpregunta['Subpregunta']['cod_grupo']]==$subpregunta['Subpregunta']['cod_subpregunta'])?"1":"0";
						break;
						case 'textarea';
							$dato=$this->data['Respuestaficha'][$subpregunta['Subpregunta']['cod_subpregunta']]?$this->data['Respuestaficha'][$subpregunta['Subpregunta']['cod_subpregunta']]:"";

						break;
					}
						
					
					
					
					//[Gabriela] Se crea una respuestaficha para almacenar la respuesta a esta subpregunta
					
					
					
					$arreglo_respuesta=array('num_evento'=>$cod_seguimiento, 'cod_subpregunta'=>$subpregunta['Subpregunta']['cod_subpregunta'], 'nom_respuesta' => $dato);
					$this->Respuestaficha->create($arreglo_respuesta);
					if(!$this->Respuestaficha->save($arreglo_respuesta))	{
						$exito=false;
					}
				}
				if($exito) $mensaje2="";
				else $mensaje2=7;
			}

			else $mensaje2="";
			
			$mensaje=$mensaje.". ".$mensaje2;
			
			if($this->data['accionASeguir'] == "cerrar")
				$this->redirect('/beneficiarios/cierreDefinitivo/'.$cod_caso);
			else
				$this->redirect('/beneficiarios/index/'.$mensaje);
		
		}
		
		//#####################################################
		//#####################################################
		//#####################################################
		//#####################################################
		function retirar($cod_caso = null, $mensaje = null)
		{
			$this->escribirHeader("Retiro de cuentas");
			
			//[Diego] Obtenemos el voluntario
			$cod_voluntario=$this->Session->read('cod_voluntario');
			$this->set('cod_voluntario',$cod_voluntario);
			
			//[Diego] obtenemos el caso
			//[Diego Jorquera] Vemos si hay o no un argumento indicando cuÃƒÂ¡l es el caso a retirar
			if ($cod_caso == null) {
				$codigo=$this->data['Seguimiento']['cod_caso'];
			} else {
				$codigo = $cod_caso;
				if ($mensaje != null) {
					$this->set('msg_for_layout', $mensaje);
				}
			}
			$this->set('cod_caso',$codigo);
			
			//[Diego]  obtenemos el programa al que pertenece este caso
			$tupla = $this->Voluntario->findByCodPersona($cod_voluntario);
			if($tupla){
				$cod_programa = $tupla['Voluntario']['cod_programa'];
			}
			
			//[Gabriela] Busco la actividad que sea de cierre de caso y que sea del programa actual
			$formulario=$this->Formulario->find(array('Actividad.cod_programa'=>$cod_programa, 'Actividad.tip_actividad'=>'Cierre'));
			if(isset($formulario['Formulario']['cod_formulario']))
					$cod_formulario=$formulario['Formulario']['cod_formulario'];
				else $cod_formulario=0;
			
			$this->set('cod_formulario',$cod_formulario);
		}
		
		//#####################################################
		//#####################################################
		//#####################################################
		//#####################################################
		function retirar1()
		{
			$se_cerro_caso=0;
			$se_creo_seguimiento=0;
			
			$this->escribirHeader("Retiro de cuentas");
			
			//[Diego] obtenemos el caso
			$codigo=$this->data['Seguimiento']['cod_caso'];
			
			//[Diego] Obtenemos el voluntario
			$cod_voluntario=$this->Session->read('cod_voluntario');
			$this->data['Caso']+=array('cod_voluntario' => $cod_voluntario);
			$this->data['Seguimiento']+=array('cod_voluntario' => $cod_voluntario);
			
			//[Diego]  Hacemos que ahora este retirado
			$this->data['Caso']+=array('est_caso' => 'Retiro');
			
			//[Diego Jorquera] Si el caso estaba asociado a turnos, eliminamos las asociaciones, para
			// que los turnos correspondientes queden libres
			$turnos = $this->Turno->findAllByCodCaso($codigo);
			foreach ($turnos as $turno) {
				$this->data['Turno'] = array();
				$this->data['Turno'] += array('cod_turno' => $turno['Turno']['cod_turno']);
				$this->data['Turno'] += array('cod_caso' => null);
				$this->Turno->save($this->data['Turno']);
			}
			
			//[Diego]  obtenemos el programa al que pertenece este caso
			$tupla = $this->Voluntario->findByCodPersona($cod_voluntario);
			if($tupla){
				$cod_programa = $tupla['Voluntario']['cod_programa'];
			}
			
			//[Diego]  obtenemos la frecuencia
			$prog=$this->Programa->findByCodPrograma($cod_programa);
			$num_frecuencia=$prog['Programa']['num_frecuencia'];
			
			//[Diego]  Calculamos la actividad
			$actividad=$cod_programa+6;
			
			//[Diego]  Revisamos la fecha de cierre de caso
			$hoy = date("Y-d-m-H-i-s");
			$fecha=explode("-", $hoy); //$fecha[0]=aÃ¯Â¿Â½o, $fecha[1]=dia, $fecha[2]=mes, $fecha[3]=Hora, $fecha[4]=Minuto, $fecha[5]=Segundo
			$cierre=date('Y-m-d H:i:s', mktime($fecha[3], $fecha[4], $fecha[5], $fecha[2], $fecha[1],  $fecha[0]));
			$this->data['Caso']+=array('fec_retiro' => $cierre);
			
			//[Diego]  Finalmente, hacemos UPDATE en la tabla.
			
			if ($this->Caso->save($this->data['Caso'])){
			
				$se_cerro_caso=1;
				//[Diego]  Creamos informaciÃ¯Â¿Â½n de un nuevo seguimiento
				
				//[Diego]  El tipo de seguimiento para retiro se asume 'Otro'
				$tip_ingreso='Otro';
				
				//[Diego]  obtenemos fecha del prÃ¯Â¿Â½ximo seguimiento (segÃ¯Â¿Â½n frecuencia)
				$hoy = date('j-n-Y');
				$fecha=explode("-", $hoy); //$fecha[0]=dia, $fecha[1]=mes, $fecha[2]=aÃ¯Â¿Â½o
				$seguimiento= date('Y-m-d', mktime(0, 0, 0, $fecha[1], $fecha[0]+$num_frecuencia,  $fecha[2]));
	
				$this->data['Seguimiento']+=array('fec_proxrevision' => $seguimiento);
				$this->data['Seguimiento']+=array('nom_comentario' => 'Retiro de caso');
				$this->data['Seguimiento']+=array('tip_ingreso' => $tip_ingreso);
				$this->data['Seguimiento']+=array('cod_actividad' => $actividad);
				
				if($this->Seguimiento->save($this->data['Seguimiento']))
				{	
					$se_creo_seguimiento=1;
					$mensaje=8;
				}
				else $mensaje=9;
				}
			else $mensaje=9;
			
			if($se_cerro_caso==1 && $se_creo_seguimiento==1)
			{
				//[Ignacio] Se busca el codigo de formulario
				$formulario=$this->Formulario->find(array('Actividad.cod_programa'=>$cod_programa, 'Actividad.tip_actividad'=>'Cierre'));
				if(isset($formulario['Formulario']['cod_formulario']))
					$cod_formulario=$formulario['Formulario']['cod_formulario'];
				else $cod_formulario=0;
				$cod_caso=$codigo;
				$cod_seguimiento= $this->Seguimiento->getLastInsertId();
				//variable para saber si se ingresaron las respuestas bien, cuando se conveirte en false es porque algo fallÃ¯Â¿Â½
				$exito=true;				
				
				$subpreguntas=$this->Formulario->getSubpreguntas($cod_formulario);
				
				//[Ignacio] la funcion anterior trae todas las subpreguntas del formulario
				foreach($subpreguntas as $subpregunta)
				{
					$tipo=$subpregunta['Subpregunta']['tip_tipoinput'];
					switch($tipo){
						case 'datefield':
						case 'text':
						case 'checkbox':
							$dato=$this->data['Respuestaficha'][$subpregunta['Subpregunta']['cod_subpregunta']]?$this->data['Respuestaficha'][$subpregunta['Subpregunta']['cod_subpregunta']]:"";
						break;
						case 'radio':
							$dato=(isset($this->data['Respuestaficha']["g".$subpregunta['Subpregunta']['cod_grupo']]) && $this->data['Respuestaficha']["g".$subpregunta['Subpregunta']['cod_grupo']]==$subpregunta['Subpregunta']['cod_subpregunta'])?"1":"0";
						break;
						case 'textarea';
							$dato=$this->data['Respuestaficha'][$subpregunta['Subpregunta']['cod_subpregunta']]?$this->data['Respuestaficha'][$subpregunta['Subpregunta']['cod_subpregunta']]:"";

						break;
					}
						
					//[Gabriela] Se crea una respuestaficha para almacenar la respuesta a esta subpregunta
					
					$arreglo_respuesta=array('num_evento'=>$cod_seguimiento, 'cod_subpregunta'=>$subpregunta['Subpregunta']['cod_subpregunta'], 'nom_respuesta' => $dato);
					$this->Respuestaficha->create($arreglo_respuesta);
					if(!$this->Respuestaficha->save($arreglo_respuesta))	{
						$exito=false;
					}
				}
				if($exito) $mensaje2="";
				else $mensaje2=7;
			}

			else $mensaje2="";
			
			$mensaje=$mensaje.". ".$mensaje2;
			
			$this->redirect('/beneficiarios/index/'.$mensaje);
			
		}
		
		//#####################################################
		//#####################################################
		//#####################################################
		//#####################################################
		function reactivar()
		{
			//[Gabriela]  El caso pasa desde la derivaciÃ¯Â¿Â½n a la reactivaciÃ¯Â¿Â½n
			// Se mandan algunas variables al layout para que se vea bien COPIAR SIEMPRE Y CAMBIAR NOMBRE PAGINA
			$this->escribirHeader("Reactivar Caso");
			$cod_caso=$this->data['Caso']['cod_caso'];
			$tip_actividad='Cierre Clinico';
			
			///////////////////////////////////////////////
			//[Gabriela] se busca el formulario adecuado
			
			$cod_voluntario=$this->Session->read('cod_voluntario');
			$voluntario=$this->Voluntario->findByCodPersona($cod_voluntario);
			$cod_programa=$voluntario['Voluntario']['cod_programa'];
			$programa=$this->Programa->findByCodPrograma($cod_programa);
				$nom_programa=$programa['Programa']['nom_programa'];
			$this->set('nom_programa',$nom_programa);
			
			$this->set('cod_caso',$cod_caso);
			
			//[Gabriela] se obtiene el codigo de formulario asociado al seguimiento
			$formulario=$this->Formulario->find(array('Actividad.cod_programa' => $cod_programa, 'Actividad.tip_actividad'=>'Cierre Clinico'));
			$this->set('cod_formulario', $formulario['Formulario']['cod_formulario']);
			$this->set('tip_actividad','Cierre Clinico');
		}
		
		//#####################################################
		//#####################################################
		//#####################################################
		//#####################################################
		//cierre clinico con posibilidad de mandar devuelta a telefonos.
		function reactivar2()
		{
			$this->escribirHeader("ReactivaciÃ¯Â¿Â½n de cuentas");
			
			//[Diego] obtenemos el caso
			$codigo=$this->data['Seguimiento']['cod_caso'];
			$se_creo_seguimiento=0;
			//[Diego] Obtenemos el voluntario
			$cod_voluntario=$this->Session->read('cod_voluntario');
			$this->data['Caso']+=array('cod_voluntario' => $cod_voluntario);
			$this->data['Seguimiento']+=array('cod_voluntario' => $cod_voluntario);
			
			//[Diego]  Hacemos que ahora este activo
			$this->data['Caso']+=array('est_caso' => 'Activo');
			
			//[Diego Jorquera] Si el caso estaba asociado a turnos, eliminamos las asociaciones, para
			// que los turnos correspondientes queden libres
			$turnos = $this->Turno->findAllByCodCaso($codigo);
			foreach ($turnos as $turno) {
				$this->data['Turno'] = array();
				$this->data['Turno'] += array('cod_turno' => $turno['Turno']['cod_turno']);
				$this->data['Turno'] += array('cod_caso' => null);
				$this->Turno->save($this->data['Turno']);
			}
			
			//[Diego]  obtenemos el programa al que pertenece este caso
			$tupla = $this->Voluntario->findByCodPersona($cod_voluntario);
			if($tupla){
				$cod_programa = $tupla['Voluntario']['cod_programa'];
			}
			
			//[Diego]  obtenemos la frecuencia
			$prog=$this->Programa->findByCodPrograma($cod_programa);
			$num_frecuencia=$prog['Programa']['num_frecuencia'];
			 
			//[Diego] Calculamos la actividad
			$actividad=$cod_programa+13;
			
			//[Diego]  Revisamos la fecha de cierre de caso
			$this->data['Caso']+=array('fec_retiro' => NULL);
			
			//[Diego]  Finalmente, hacemos UPDATE en la tabla.
			if ($this->Caso->save($this->data['Caso'])){
			// [Diego]  Creamos informaciÃ¯Â¿Â½n de un nuevo seguimiento
				
				//[Diego]  El tipo de seguimiento para reactivaciones se asume llamada entrande
				$tip_ingreso='Llamada_entrante';
				
				//[Diego]  obtenemos fecha del prÃ¯Â¿Â½ximo seguimiento (segÃ¯Â¿Â½n frecuencia)
				$hoy = date('j-n-Y');
				$fecha=explode("-", $hoy); //$fecha[0]=dia, $fecha[1]=mes, $fecha[2]=aÃ¯Â¿Â½o
				$seguimiento= date('Y-m-d', mktime(0, 0, 0, $fecha[1], $fecha[0]+$num_frecuencia,  $fecha[2]));
	
				$this->data['Seguimiento']+=array('fec_proxrevision' => $seguimiento);
				$this->data['Seguimiento']+=array('nom_comentario' => 'Retiro de caso');
				$this->data['Seguimiento']+=array('tip_ingreso' => $tip_ingreso);
				$this->data['Seguimiento']+=array('cod_actividad' => $actividad);
				
				if($this->Seguimiento->save($this->data['Seguimiento']))
				{	
					$mensaje=10;
					$se_creo_seguimiento=1;
				}
				else $mensaje= 11;
				}
			else $mensaje=11;
			
			if($se_creo_seguimiento==1)
			{
				//[Ignacio] Se busca el codigo de formulario
				$formulario=$this->Formulario->find(array('Actividad.cod_programa'=>$cod_programa, 'Actividad.tip_actividad'=>$this->data['Seguimiento']['tip_actividad']));
				if(isset($formulario['Formulario']['cod_formulario']))
					$cod_formulario=$formulario['Formulario']['cod_formulario'];
				else $cod_formulario=0;
				//$cod_caso=$this->Caso->getLastInsertId();
				$cod_seguimiento= $this->Seguimiento->getLastInsertId();
				//variable para saber si se ingresaron las respuestas bien, cuando se conveirte en false es porque algo fallÃ¯Â¿Â½
				$exito=true;				
				
				$subpreguntas=$this->Formulario->getSubpreguntas($cod_formulario);
				
				//[Ignacio] la funcion anterior trae todas las subpreguntas del formulario
				foreach($subpreguntas as $subpregunta)
				{
					$tipo=$subpregunta['Subpregunta']['tip_tipoinput'];
					switch($tipo){
						case 'text':
						case 'checkbox':
							$dato=$this->data['Respuestaficha'][$subpregunta['Subpregunta']['cod_subpregunta']]?$this->data['Respuestaficha'][$subpregunta['Subpregunta']['cod_subpregunta']]:"";
						break;
						case 'radio':
							$dato=(isset($this->data['Respuestaficha']["g".$subpregunta['Subpregunta']['cod_grupo']]) && $this->data['Respuestaficha']["g".$subpregunta['Subpregunta']['cod_grupo']]==$subpregunta['Subpregunta']['cod_subpregunta'])?"1":"0";
						break;
						case 'textarea';
							$dato=$this->data['Respuestaficha'][$subpregunta['Subpregunta']['cod_subpregunta']]?$this->data['Respuestaficha'][$subpregunta['Subpregunta']['cod_subpregunta']]:"";

						break;
					}
						
					//[Gabriela] Se crea una respuestaficha para almacenar la respuesta a esta subpregunta
					
					$arreglo_respuesta=array('num_evento'=>$cod_seguimiento, 'cod_subpregunta'=>$subpregunta['Subpregunta']['cod_subpregunta'], 'nom_respuesta' => $dato);
					$this->Respuestaficha->create($arreglo_respuesta);
					if(!$this->Respuestaficha->save($arreglo_respuesta))	{
						$exito=false;
					}
				}
				if($exito) $mensaje2="";
				else $mensaje2=7;
			}

			else $mensaje2="";
			
			// si ==AP, se mando devuelta a telefonos... sino se retira completamente.
			$hacia_donde=$this->data['Respuestaficha']['Cierre'];
			if($hacia_donde=='AP')
				$this->redirect('/beneficiarios/index/'.$mensaje);
			else
			{
				//[Gabriela] ahora se necesita cerrar el caso completamente
				
				//[Gabriela] Busco la actividad que sea de cierre de caso y que sea del programa actual
				$formulario=$this->Formulario->find(array('Actividad.cod_programa'=>$cod_programa, 'Actividad.tip_actividad'=>'Cierre'));
				if(isset($formulario['Formulario']['cod_formulario']))
					$cod_formulario=$formulario['Formulario']['cod_formulario'];
				else $cod_formulario=0;
			
				$this->set('cod_formulario',$cod_formulario);
				$this->set('cod_caso',$codigo);
				
			}
			
		}
		
		//#####################################################
		//#####################################################
		//#####################################################
		//#####################################################
		function reactivar3()
		{
			$this->escribirHeader("ReactivaciÃ¯Â¿Â½n de cuentas");
			
			//[Diego] obtenemos el caso
			$codigo=$this->data['Seguimiento']['cod_caso'];
			$se_creo_seguimiento=0;
			
			//[Diego Jorquera] CÃƒÂ³digo del beneficiario (persona)
			$cod_persona = $this->data['Caso']['cod_beneficiario'];
			
			//[Diego] Obtenemos el voluntario
			$cod_voluntario=$this->Session->read('cod_voluntario');
			$this->data['Caso']+=array('cod_voluntario' => $cod_voluntario);
			$this->data['Seguimiento']+=array('cod_voluntario' => $cod_voluntario);
			
			//[Diego]  Hacemos que ahora este activo
			$this->data['Caso']+=array('est_caso' => 'Activo');
			
			//[Diego]  obtenemos el programa al que pertenece este caso
			$tupla = $this->Voluntario->findByCodPersona($cod_voluntario);
			if($tupla){
				$cod_programa = $tupla['Voluntario']['cod_programa'];
			}
			
			//[Diego]  obtenemos la frecuencia
			$prog=$this->Programa->findByCodPrograma($cod_programa);
			$num_frecuencia=$prog['Programa']['num_frecuencia'];
			 
			//[Diego] Calculamos la actividad
			$actividad=$cod_programa+9;
			
			//[Diego]  Revisamos la fecha de cierre de caso
			$this->data['Caso']+=array('fec_retiro' => null);
			
			//[Diego]  Finalmente, hacemos UPDATE en la tabla.

			if ($this->Caso->save($this->data['Caso'])){
			
			// [Diego]  Creamos informaciÃ¯Â¿Â½n de un nuevo seguimiento
				
				//[Diego]  El tipo de seguimiento para reactivaciones se asume llamada entrande
				$tip_ingreso='Llamada_entrante';
				
				//[Diego]  obtenemos fecha del prÃ¯Â¿Â½ximo seguimiento (segÃ¯Â¿Â½n frecuencia)
				$hoy = date('j-n-Y');
				$fecha=explode("-", $hoy); //$fecha[0]=dia, $fecha[1]=mes, $fecha[2]=aÃ¯Â¿Â½o
				
				// [CORRECCI�N BUG 25-12-2008]
				//$seguimiento= date('Y-m-d', mktime(0, 0, 0, $fecha[1], $fecha[0]+$num_frecuencia,  $fecha[2]));
				$seguimiento= date('d-m-Y', mktime(0, 0, 0, $fecha[1], $fecha[0]+$num_frecuencia,  $fecha[2]));
				
				//echo $seguimiento;
				//die();
				
				$this->data['Seguimiento']+=array('fec_proxrevision' => $seguimiento);
				$this->data['Seguimiento']+=array('nom_comentario' => 'Reactivacion de Caso');
				$this->data['Seguimiento']+=array('tip_ingreso' => $tip_ingreso);
				$this->data['Seguimiento']+=array('cod_actividad' => $actividad);
				
				
				if($this->Seguimiento->save($this->data['Seguimiento']))
				{	
					$mensaje=10;
					$se_creo_seguimiento=1;
				}
				
				else $mensaje= 11;
				
				
			}
			
			else $mensaje=11;

			
			// [Diego Jorquera] Redireccionar a la pÃƒÂ¡gina del mismo beneficiario, ahora activo
			$this->redirect('/beneficiarios/ver/'.$cod_persona.'/'.$mensaje);			
		}
		
		
		//#####################################################
		//#####################################################
		//#####################################################
		//#####################################################
		//Para ver casos activos o pendientes de derivación (en fila de derivación)
		function ver($inicio=null,$exito=null)
		{
			
			// Se mandan algunas variables al layout para que se vea bien COPIAR SIEMPRE Y CAMBIAR NOMBRE PAGINA
			$this->escribirHeader("Datos del beneficiario");
			
			// Rescatamos el cod_persona del beneficiario que tenemos que desplegar
			if($inicio!=null)
				$persona=$this->Beneficiario->findByCodPersona($inicio);
			else
				$persona=$this->Beneficiario->findByCodPersona($this->data['Persona']['cod_persona']); 
			
			$this->set('persona',$persona);
			
			$codigo=$persona['Persona']['cod_persona'];
			
			$comunas=$this->Comuna->getAllAsArray();
			$this->set('comunas', $comunas);
			
			//calculamos edad del beneficiario
			
			//$formulario=$this->Formulario->find(array('Actividad.cod_programa' => $cod_programa, 'Formulario.cod_actividad'=>$cod_actividad));
			
			
			if($persona['Persona']['ano_nacimiento']!=null)
				$edad= (int)date('Y')-$persona['Persona']['ano_nacimiento'];
			else 
				$edad='-';
			$persona['Persona']+=array('edad'=>$edad);
			$this->set('edad',$persona['Persona']['edad']);
			
			//[Diego Jorquera] Buscar caso del beneficiario
		
			$caso=$this->Caso->find("est_caso = 'Activo' and cod_beneficiario = $codigo", "", "", "", "", 0);
			if ($caso == null) {
				$caso=$this->Caso->find("est_caso = 'Pendiente' and cod_beneficiario = $codigo", "", "", "", "", 0);
			}
			$this->set('caso_activo', $caso);
			
			
			$soloyo=$this->Persona->findByCodPersona($caso['Caso']['cod_soloyo']);
			$this->set('soloyo', $soloyo);
			
			$cod_caso=$caso['Caso']['cod_caso'];
			
			$seguimientos=$this->Seguimiento->findAll(array("Seguimiento.cod_caso"=>$cod_caso));
			
			//[Diego] El programa es aquÃ¯Â¿Â½l al que pertecece el voluntario
			
			$cod_persona=$this->Session->read('cod_voluntario');
			$voluntario= $this->Voluntario->findByCodPersona($cod_persona);
			$cod_programa=$voluntario['Voluntario']['cod_programa'];
			$programa=$this->Programa->findByCodPrograma($cod_programa);
			
			$caseStatus = 0;
			foreach($seguimientos as $i => $v){
				$seguimientos[$i]+=$this->Voluntario->find(array("Persona.cod_persona"=>$v['Seguimiento']['cod_voluntario']),"","","","",-1);
				
				$seguimientos[$i]+=$this->Actividad->find(array("Actividad.cod_actividad"=>$v['Seguimiento']['cod_actividad']),"","","","",-1);
				
				$seguimientos[$i]['Seguimiento']['fec_ejecucion']=$this->Seguimiento->toFecha($seguimientos[$i]['Seguimiento']['fec_ejecucion']);
				//[LEO] Si es un formulario de ACOGE de Termino de Embarazo				 
				$actividad = $this->Actividad->findByCodActividad($seguimientos[$i]['Seguimiento']['cod_actividad']);
				$actividad = $actividad['Actividad'];

				//echo $actividad['tip_actividad'];
				if($actividad['tip_actividad'] == 'Termino Embarazo')
				{
					$caseStatus= 1;
				}
				//Para luego poder determinar que tipo de formulario es al momento de desplegar opciones.
				$seguimientos[$i]['Seguimiento']['formulario']=$this->Formulario->find(array('Actividad.cod_programa' => $cod_programa, 'Formulario.cod_actividad'=>$v['Seguimiento']['cod_actividad']));
				
				//[Diego] Obtenemos la descripciÃ¯Â¿Â½n del seguimiento, para cada tipo de seguimiento, para cada programa
				//[Diego] Primero obtenemos el codigo de la subpregunta que imprimiremos (para obtener siempre la descripcion del seguimiento)
				$cod_subpregunta=$this->Actividad->find(array("Actividad.cod_actividad"=> $seguimientos[$i]['Seguimiento']['cod_actividad']),"","","","",-1);
				
				//[Diego] Luego buscamos e imprimimos la descripciÃ¯Â¿Â½n del seguimiento.
				$resp=$this->Respuestaficha->find(array("Respuestaficha.num_evento"=> $seguimientos[$i]['Seguimiento']['num_evento'], "Respuestaficha.cod_subpregunta" => $cod_subpregunta['Actividad']['cod_subpreguntadescripcion']),"","","","",-1);
				
				
				if($resp['Respuestaficha']['nom_respuesta'] != "" )
				{
					$resp=array('nom_respuesta' => $resp['Respuestaficha']['nom_respuesta']);					
				}
				else
				{
					$resp = array('nom_respuesta' => $seguimientos[$i]['Seguimiento']['nom_comentario']);
				}				
				$seguimientos[$i]+=array('DescripcionSeguimiento' => $resp);
				//print_r($seguimientos[$i]);				
			}
			
			$this->set('seguimientos', $seguimientos);
			$this->set('infoPrograma', $programa);
			$this->set('caseStatus',$caseStatus);
			//obtiene la ultima vez que se le hizo un seguimiento
			$ultimo=$this->Seguimiento->find("Seguimiento.cod_caso = $cod_caso", "", "fec_ejecucion desc", "", "", 0);
					
			//se pone la fecha ultimo contacto en el orden deseado
			$fecha_ultimo=$this->Seguimiento->toFecha($ultimo['Seguimiento']['fec_ejecucion']);
			$this->set('ultimo', $fecha_ultimo);
		
			$personas=$this->Persona->findByCodPersona($codigo);
			$this->set('personas', $personas);
			
			$beneficiarios=$this->Beneficiario->findByCodPersona($codigo);
			$this->set('beneficiarios', $beneficiarios);
					
			//se pone la fecha proxima revision en el orden deseado
			$fecha=$this->Persona->toDate($ultimo['Seguimiento']['fec_proxrevision']);
			$this->set('fecha', $fecha);
			
			// [Diego Jorquera] Mostrar mensaje de Ã©xito
			$this->set('msg_for_layout',$exito);
		}
		
		
		function cierreDefinitivo($cod_caso = null)
		{
			$formulario=$this->Formulario->find(array('Actividad.cod_programa'=>1, 'Actividad.tip_actividad'=>'Cierre'));
				if(isset($formulario['Formulario']['cod_formulario']))
					$cod_formulario=$formulario['Formulario']['cod_formulario'];
				else 
					$cod_formulario=0;
			$this->set('cod_formulario',$cod_formulario);
			if($cod_caso == null)
			{
				$cod_caso = $this->data['Caso']['cod_caso'];
			}
			$this->set('cod_caso',$cod_caso);
		  $this->escribirHeader("Cierre Definitivo");
		}
		//#####################################################
		//#####################################################
		//#####################################################
		//#####################################################
		function modificar()
		{
			$this->escribirHeader("Modificar Beneficiario");
			
			// Rescatamos el cod_persona del beneficiario que tenemos que desplegar
			$persona=$this->Beneficiario->findByCodPersona($this->data['Persona']['cod_persona']);
			
									
			$comunas=$this->Comuna->getAllAsArray();
			$this->set('comunas', $comunas);
			$tip_rolfamilia= $this->Beneficiario->getRolesFamilia();
			$this->set('tip_rolfamilia',$tip_rolfamilia);
			
			$codigo=$persona['Persona']['cod_persona'];
			
			$ruts=explode("-",$persona['Persona']['nom_rut']);
			$persona['Persona']['nom_rut']=$ruts[0];
			$persona['Persona']['num_rutcodver']=isset($ruts[1])?$ruts[1]:"";
			
			$tel1=explode("-", $persona['Persona']['num_telefono1']);
			$tel2=explode("-", $persona['Persona']['num_telefono2']);
				
			
			$persona['Persona']['num_telefono1']=(isset($tel1[1]))?$tel1[1]:$tel1[0];
			$persona['Persona']['num_telefono1_pre']=(isset($tel1[1]))?$tel1[0]:"";
			$persona['Persona']['num_telefono2']=(isset($tel2[1]))?$tel2[1]:$tel2[0];
			$persona['Persona']['num_telefono2_pre']=(isset($tel2[1]))?$tel2[0]:"";
			
			$this->set('persona',$persona);
			
			$estado="Activo";  //solo ve los casos activos del beneficiario
			$fingreso =explode(" ",$persona['Beneficiario']['fec_ingreso']);
			$faux = explode("-",$fingreso[0]);

			$fingreso = $faux[2]. "-". $faux[1]."-". $faux[0];
			
			
			//Busca casos activos que sean de ese beneficiario
			
			$caso_activo=$this->Caso->find(array("Caso.est_caso" => $estado,"Caso.cod_beneficiario" => $codigo), "", "", "", "", 0);
			$this->set('caso_activo', $caso_activo);
			
			$cod_caso=$caso_activo['Caso']['cod_caso'];
			
			$seguimientos=$this->Seguimiento->findAll(array( "Seguimiento.cod_caso"=>$cod_caso));
			
			foreach($seguimientos as $i => $v){
				$seguimientos[$i]+=$this->Voluntario->find(array("Persona.cod_persona"=>$v['Seguimiento']['cod_voluntario']),"","","","",-1);
			}
			
			$this->set('seguimientos', $seguimientos);
			$this->set('fingreso',$fingreso);
						
			$ultimo=$this->Seguimiento->find(array("Seguimiento.cod_caso" => $cod_caso), "", array("Seguimiento.fec_ejecucion"=>"desc"), "", "", 0);
			$this->set('ultimo', $ultimo);
			
			//se pone la fecha en el orden deseado
			$fecha=$this->Persona->toDate($ultimo['Seguimiento']['fec_proxrevision']);
			$this->set('fecha', $fecha);
			
			//El codigo del voluntario que estÃ¯Â¿Â½ en la sesion
			$cod_voluntario=$this->Session->read('cod_voluntario');
						
			//Maximas visitas por dia del programa
			$vol=$this->Voluntario->findByCodPersona($cod_voluntario);
			$cod_programa=$vol['Voluntario']['cod_programa'];
			$prog=$this->Programa->findByCodPrograma($cod_programa);
			$max=$prog['Programa']['num_maxllamadas'];
			
			//Consulta que devuelva el listado de las fechas copadas 
			$fecha= $this->Seguimiento->query("
				SELECT `s`.`fec_proxrevision` AS `fec`
				FROM `seguimientos` AS `s` , `voluntarios` AS `v` 
				WHERE `v`.`cod_persona` = `s`.`cod_voluntario` 
				AND `v`.cod_programa = '".$cod_programa."'
				GROUP BY `s`.`fec_proxrevision` 
				HAVING COUNT( `s`.`fec_proxrevision` ) >= $max
				AND `s`.`fec_proxrevision` > now()
				ORDER BY `s`.`fec_proxrevision` 
			");
			
			$fechas_deshabilitadas="";
			
			foreach ($fecha as $fec)
			{	
				if($fechas_deshabilitadas!="") $fechas_deshabilitadas.=",";			
				
				//Convierte la fecha al formato deseado ej:20080528
				$aux1=$fec['s']['fec'];
				$aux2=explode("-", $aux1);
				$desabilitar= date('Ymd', mktime(0, 0, 0, $aux2[1], $aux2[2],  $aux2[0]));		
				
				//Concatena las fechas
				$fechas_deshabilitadas.="'";
				$fechas_deshabilitadas.=$desabilitar;
				$fechas_deshabilitadas.="'";
			}
				
			//Pasa el arreglo con las fechas que hay de desabilitar
			$this->set('script', $fechas_deshabilitadas);
		}
		
		//#####################################################
		//#####################################################
		//#####################################################
		//#####################################################
		function modificar2(){
			$this->escribirHeader("Modificar Beneficiario");
			
			// [Diego Jorquera] Obtener cÃƒÂ³digo de persona (beneficiario)
			$cod_persona = $this->data['Persona']['cod_persona'];
			// [Diego Jorquera] Unir RUT con el cÃƒÂ³digo verificador
			$this->data['Persona']['nom_rut'].=("-".$this->data['Persona']['num_rutcodver']);
				
			$num_telefono1_pre=$this->data['Persona']['num_telefono1_pre'];
			$num_telefono1_post=$this->data['Persona']['num_telefono1_post'];
			
			if($num_telefono1_pre!="")
				$num_telefono1_pre=$num_telefono1_pre.'-';
			$this->data['Persona']['num_telefono1']=$num_telefono1_pre.$num_telefono1_post;
			
			$num_telefono2_pre=$this->data['Persona']['num_telefono2_pre'];
			$num_telefono2_post=$this->data['Persona']['num_telefono2_post'];
			
			if($num_telefono2_pre!="")
				$num_telefono2_pre=$num_telefono2_pre.'-';
			$this->data['Persona']['num_telefono2']=$num_telefono2_pre.$num_telefono2_post;
	
			
			$faux = explode("-",$this->data['Beneficiario']['fec_ingreso']);
			
				$this->data['Beneficiario']['fec_ingreso'] = date('Y-m-d H:i:s', mktime(0, 0, 0, $faux[1], $faux[0],  $faux[2]));			

			$edad = $this->data['Persona']['num_edad'];
			$this->data['Persona']['ano_nacimiento'] = date('Y') - $edad;
			
			if($this->Persona->save($this->data['Persona'])){
							
				//guardar los datos del beneficiario
				$this->data['Beneficiario']['cod_persona']=$this->data['Persona']['cod_persona'];
				if($this->Beneficiario->save($this->data['Beneficiario'])){
					$this->Seguimiento->save($this->data['Seguimiento']);
					$msg=1;
				}
				else $msg=2;
			}
			else $msg=3;
			
			// [Diego Jorquera] Redireccionar a la pÃ¡gina del mismo beneficiario; a cuÃ¡l
			// pÃ¡gina depende de sus casos activos
			
			$cod_voluntario = $this->Session->read('cod_voluntario');
			$voluntario = $this->Voluntario->find(array("Voluntario.cod_persona" => $cod_voluntario), array(), array(), "", "", -1);
			$cod_programa = $voluntario['Voluntario']['cod_programa'];
			$casos_activos = $this->Caso->query("
				SELECT casos.est_caso
				FROM casos, tipocasos 
				WHERE casos.cod_tipocaso = tipocasos.cod_tipocaso 
				AND tipocasos.cod_programa = " . $cod_programa." 
				AND casos.cod_beneficiario = " . $cod_persona . "
				ORDER BY casos.est_caso
				LIMIT 0,1;"
			);
			
			$url_ver = "";
			
			if (count($casos_activos)) {
				if ($casos_activos[0]['casos']['est_caso'] == 'Activo') {
					$url_ver = "ver";
				} else if ($casos_activos[0]['casos']['est_caso'] == 'Retiro') {	
					$url_ver = "ver2";
				} else if ($casos_activos[0]['casos']['est_caso'] == 'Derivacion') {	
					$url_ver = "ver3";
				}
			}
			
			$this->redirect('/beneficiarios/'.$url_ver.'/'.$cod_persona.'/'.$msg);
			exit();
		}
		
		//#####################################################
		//#####################################################
		//#####################################################
		//#####################################################		
		//Para ver casos cerrados
		function ver2($inicio=null, $exito=null)
		{
			// Se mandan algunas variables al layout para que se vea bien COPIAR SIEMPRE Y CAMBIAR NOMBRE PAGINA
			$this->escribirHeader("Datos del beneficiario");
			
			// Rescatamos el cod_persona del beneficiario que tenemos que desplegar
			if($inicio!=null)
				$persona=$this->Beneficiario->findByCodPersona($inicio);
			else
				$persona=$this->Beneficiario->findByCodPersona($this->data['Persona']['cod_persona']); 
		
					
			$this->set('persona',$persona);
			
			
			
			$codigo=$persona['Persona']['cod_persona'];
						
			$this->set('cod_persona', $codigo);
			$estado="Retiro";
			
			$comunas=$this->Comuna->getAllAsArray();
			$this->set('comunas', $comunas);
			
			//calculamos edad del beneficiario
			if($persona['Persona']['ano_nacimiento']!=null)
				$edad= (int)date('Y')-$persona['Persona']['ano_nacimiento'];
			else 
				$edad='-';
			$persona['Persona']+=array('edad'=>$edad);
			$this->set('edad',$persona['Persona']['edad']);
			
			//Busca casos activos que sean de ese beneficiario
			
			$caso_retiro=$this->Caso->findAll("est_caso = '$estado' and cod_beneficiario = $codigo", "", "", "", "", 0);
			$this->set('caso_retiro', $caso_retiro);
			
			foreach($caso_retiro as $i => $v){
			$cod_caso[$i]=$v['Caso']['cod_caso'];
			
			}
			
			/*$seguimientos=$this->Seguimiento->findAll(array( "Seguimiento.cod_caso"=>$cod_caso));
			
		
			foreach($seguimientos as $i => $v){
				$seguimientos[$i]+=$this->Voluntario->find(array("Persona.cod_persona"=>$v['Seguimiento']['cod_voluntario']),"","","","",-1);
				$seguimientos[$i]['Seguimiento']['fec_ejecucion']=$this->Seguimiento->toFecha($seguimientos[$i]['Seguimiento']['fec_ejecucion']);
			}
			
			$this->set('seguimientos', $seguimientos);
			
			//obtiene la ultima vez que se le hizo un seguimiento
			$ultimo=$this->Seguimiento->find("Seguimiento.cod_caso = $cod_caso", "", "fec_ejecucion desc", "", "", 0);
					
			//se pone la fecha ultimo contacto en el orden deseado
			$fecha_ultimo=$this->Seguimiento->toFecha($ultimo['Seguimiento']['fec_ejecucion']);
			$this->set('ultimo', $fecha_ultimo);
		*/
			$personas=$this->Persona->findByCodPersona($codigo);
			$this->set('personas', $personas);
			
			$beneficiarios=$this->Beneficiario->findByCodPersona($codigo);
			$this->set('beneficiarios', $beneficiarios);
					
			//se pone la fecha proxima revision en el orden deseado
		//	$fecha=$this->Persona->toDate($ultimo['Seguimiento']['fec_proxrevision']);
			//$this->set('fecha', $fecha);
						
			// [Diego Jorquera] Mostrar mensaje de Ã©xito
			$this->set('msg_for_layout',$exito);
		}
		
		//#####################################################
		//#####################################################
		//#####################################################
		//#####################################################
		function excel(){
			
			if(isset($this->data['Excel']['Hoja1']))
			{
				$array = unserialize($this->data['Excel']['Hoja1']);
				
				$array2 = array();
				foreach($array as $a){
					
					$beneficiario = $this->Beneficiario->find(array("Persona.cod_persona"=>$a["cod_persona"]));
					$comuna =$this->Comuna->find(array("cod_comuna"=>$beneficiario["Persona"]["cod_comuna"]));
				
					$datos["Nombre"]=$beneficiario["Persona"]["nom_nombre"]." ".$beneficiario["Persona"]["nom_appat"];
					$datos["Rol"]=$beneficiario["Beneficiario"]["tip_rolfamilia"]; //falta codificacion fundacion (no compatible con base de datos)
					if(isset($beneficiario["Persona"]["ano_nacimiento"]))$datos["Edad"]= date('Y')-$beneficiario["Persona"]["ano_nacimiento"];
					else $datos["Edad"] = "";
					$datos["Telefono"]= $beneficiario["Persona"]["num_telefono1"];
					$datos["Comuna"]= $comuna["Comuna"]["nom_comuna"];
					
					$array2 = array_merge($array2,array($datos));
				}
				
				$this->set('array_activo',$array2);
			}
			
			if(isset($this->data['Excel']['Hoja2']))
			{
				$array = unserialize($this->data['Excel']['Hoja2']);
				$array2 = array();
				foreach($array as $a){
					
					$beneficiario = $this->Beneficiario->find(array("Persona.cod_persona"=>$a["cod_persona"]));
					$comuna =$this->Comuna->find(array("cod_comuna"=>$beneficiario["Persona"]["cod_comuna"]));
				
					$datos["Nombre"]=$beneficiario["Persona"]["nom_nombre"]." ".$beneficiario["Persona"]["nom_appat"];
					$datos["Rol"]=$beneficiario["Beneficiario"]["tip_rolfamilia"]; //falta codificacion fundacion (no compatible con base de datos)
					if(isset($beneficiario["Persona"]["ano_nacimiento"]))$datos["Edad"]= date('Y')-$beneficiario["Persona"]["ano_nacimiento"];
					else $datos["Edad"] = "";
					$datos["Telefono"]= $beneficiario["Persona"]["num_telefono1"];
					$datos["Comuna"]= $comuna["Comuna"]["nom_comuna"];
					
					$array2 = array_merge($array2,array($datos));
				}
				
				$this->set('array_derivacion',$array2);
			}
			
			if(isset($this->data['Excel']['Hoja3']))
			{
				$array = unserialize($this->data['Excel']['Hoja3']);
				$array2 = array();
				foreach($array as $a){
					
					$beneficiario = $this->Beneficiario->find(array("Persona.cod_persona"=>$a["cod_persona"]));
					$comuna =$this->Comuna->find(array("cod_comuna"=>$beneficiario["Persona"]["cod_comuna"]));
				
					$datos["Nombre"]=$beneficiario["Persona"]["nom_nombre"]." ".$beneficiario["Persona"]["nom_appat"];
					$datos["Rol"]=$beneficiario["Beneficiario"]["tip_rolfamilia"]; //falta codificacion fundacion (no compatible con base de datos)
					if(isset($beneficiario["Persona"]["ano_nacimiento"]))$datos["Edad"]= date('Y')-$beneficiario["Persona"]["ano_nacimiento"];
					else $datos["Edad"] = "";
					$datos["Telefono"]= $beneficiario["Persona"]["num_telefono1"];
					$datos["Comuna"]= $comuna["Comuna"]["nom_comuna"];
					
					$array2 = array_merge($array2,array($datos));
				}
				
				$this->set('array_retiro',$array2);
				
			}
			$this->set('type','beneficiarios');
			$this->render('excel', 'excel'); 
		}
		
		//#####################################################
		//#####################################################
		//#####################################################
		//#####################################################
		function ver_seguimientos()
		
		{
		// Se mandan algunas variables al layout para que se vea bien COPIAR SIEMPRE Y CAMBIAR NOMBRE PAGINA
		$this->escribirHeader("Seguimientos del Caso");
			
		$cod_caso=$this->data['Seguimiento']['cod_caso'];		
		
		$caso_retiro=$this->Caso->findByCodCaso($cod_caso);
			$this->set('caso_retiro', $caso_retiro);
		
		$seguimientos=$this->Seguimiento->findAll(array( "Seguimiento.cod_caso"=>$cod_caso));
			
		
			foreach($seguimientos as $i => $v){
				$seguimientos[$i]+=$this->Voluntario->find(array("Persona.cod_persona"=>$v['Seguimiento']['cod_voluntario']),"","","","",-1);
				$seguimientos[$i]['Seguimiento']['fec_ejecucion']=$this->Seguimiento->toFecha($seguimientos[$i]['Seguimiento']['fec_ejecucion']);
				
				$seguimientos[$i]+=$this->Actividad->find(array("Actividad.cod_actividad"=>$v['Seguimiento']['cod_actividad']),"","","","",-1);
				
				//[Diego] Obtenemos la descripciÃ¯Â¿Â½n del seguimiento, para cada tipo de seguimiento, para cada programa
				//[Diego] Primero obtenemos el codigo de la subpregunta que imprimiremos (para obtener siempre la descripcion del seguimiento)
				$cod_subpregunta=$this->Actividad->find(array("Actividad.cod_actividad"=> $seguimientos[$i]['Seguimiento']['cod_actividad']),"","","","",-1);
				
				//[Diego] Luego buscamos e imprimimos la descripciÃ¯Â¿Â½n del seguimiento.
				$resp=$this->Respuestaficha->find(array("Respuestaficha.num_evento"=> $seguimientos[$i]['Seguimiento']['num_evento'], "Respuestaficha.cod_subpregunta" => $cod_subpregunta['Actividad']['cod_subpreguntadescripcion']),"","","","",-1);
				
				if($resp['Respuestaficha']['nom_respuesta'] != "" )
				{
					$resp=array('nom_respuesta' => $resp['Respuestaficha']['nom_respuesta']);
					
				}
				else
				{
					$resp = array('nom_respuesta' => $seguimientos[$i]['Seguimiento']['nom_comentario']);
				}
				
				$seguimientos[$i]+=array('DescripcionSeguimiento' => $resp);
			}
			
			$this->set('seguimientos', $seguimientos);
			
			//obtiene la ultima vez que se le hizo un seguimiento
			$ultimo=$this->Seguimiento->find("Seguimiento.cod_caso = $cod_caso", "", "fec_ejecucion desc", "", "", 0);
			
			$beneficiario=$this->Persona->findByCodPersona($caso_retiro['Caso']['cod_beneficiario']);
			$this->set('beneficiario', $beneficiario);
		
		}
		
		//#####################################################
		//#####################################################
		//#####################################################
		//#####################################################
		function ingresar_seguimiento1()
		{
			// Se mandan algunas variables al layout para que se vea bien COPIAR SIEMPRE Y CAMBIAR NOMBRE PAGINA
			$this->escribirHeader("Ingreso del Seguimiento");
			
			//se obtienen los valores posibles del tipo de prox seguimiento como un arreglo
			
			$tipo_ingreso=$this->Seguimiento->getPossibleValues('tip_ingreso');
			$this->set('tipo_ingreso', $tipo_ingreso);
			
			// Rescatamos el cod_caso del beneficiario que tenemos que desplegar
			$caso=$this->Caso->findByCodCaso($this->data['Caso']['cod_caso']);
			$this->set('caso',$caso);
			
			//El codigo del voluntario que estÃ¯Â¿Â½ en la sesion
			$cod_voluntario=$this->Session->read('cod_voluntario');
						
			//Maximas visitas por dia del programa
			$vol=$this->Voluntario->findByCodPersona($cod_voluntario);
			$cod_programa=$vol['Voluntario']['cod_programa'];
			$prog=$this->Programa->findByCodPrograma($cod_programa);
			$max=$prog['Programa']['num_maxllamadas'];
			
			$this->set('canRenderCaseClosure',$cod_programa>1);
			
			//[STEPHANIE] Calcula si puede hacer un seguimiento de tipo visita

			$cod_persona=$this->Session->read('cod_voluntario');
			$voluntario= $this->Voluntario->findByCodPersona($cod_persona);
			$cod_programa=$voluntario['Voluntario']['cod_programa'];
			$programa= $this->Programa->findByCodPrograma($cod_programa);
			
			$num_maxvisitas=$programa['Programa']['num_maxvisitas'];
		
		
			$estado="Derivacion";
			
			$numero_casos= $this->Caso->query("
								SELECT * 
								FROM casos
								WHERE casos.est_caso='".$estado."';"
							);
							
							$cont3=0;
							foreach($numero_casos as $caso)
								$cont3++;
						
			$num_maxvisitas=$programa['Programa']['num_maxvisitas'];
			
						
				//$num_maxvisitas=2	
					
			$cupos =  $num_maxvisitas - $cont3;
		
			if($cupos>0) {
			
			$tip_proxrevision= $this->Seguimiento->getPossibleValues('tip_proxrevision');
						}
			
			else {
				 $tip_proxrevision =
						array('Llamada');
	
			}			
			
			$this->set('tip_proxrevision',$tip_proxrevision);
			
			//[STEPHANIE] FIN de definir tipo
			
			
			//Calcula la fecha en la que deberia tener seguimiento segun la frecuencia			
			$hoy = date('j-n-Y');
			$fecha=explode("-", $hoy); //$fecha[0]=dia, $fecha[1]=mes, $fecha[2]=aÃ¯Â¿Â½o
			$seguimiento= date('j-m-Y', mktime(0, 0, 0, $fecha[1], $fecha[0]+$prog['Programa']['num_frecuencia'],  $fecha[2]));
			
			/*  TENEMOS:
				$max= numero maximo de llamadas por dia
				$cod= codigo del programa en el que estoy
				$vol= arreglo de todos los voluntarios pertenecientes al programa en el que estamos */
			
			//Consulta que devuelva el listado de las fechas copadas 
			$fecha= $this->Seguimiento->query("
				SELECT `s`.`fec_proxrevision` AS `fec`
				FROM `seguimientos` AS `s` , `voluntarios` AS `v` 
				WHERE `v`.`cod_persona` = `s`.`cod_voluntario` 
				AND `v`.cod_programa = '".$cod_programa."'
				GROUP BY `s`.`fec_proxrevision` 
				HAVING COUNT( `s`.`fec_proxrevision` ) >= $max
				AND `s`.`fec_proxrevision` > now()
				ORDER BY `s`.`fec_proxrevision` 
			");
			
			$fechas_deshabilitadas="";
			
			foreach ($fecha as $fec)
			{	
				if($fechas_deshabilitadas!="") $fechas_deshabilitadas.=",";			
				
				//Convierte la fecha al formato deseado ej:20080528
				$aux1=$fec['s']['fec'];
				$aux2=explode("-", $aux1);
				$desabilitar= date('Ymd', mktime(0, 0, 0, $aux2[1], $aux2[2],  $aux2[0]));		
				
				//Concatena las fechas
				$fechas_deshabilitadas.="'";
				$fechas_deshabilitadas.=$desabilitar;
				$fechas_deshabilitadas.="'";
			}
			
			$this->set('seguimiento',$seguimiento);
					
			//Pasa el arreglo con las fechas que hay de desabilitar
			$this->set('script', $fechas_deshabilitadas);
			
			//obtencion del codigo de formulario asociado al seguimiento
			$formulario=$this->Formulario->find(array('Actividad.cod_programa' => $cod_programa, 'Actividad.tip_actividad'=>$this->data['Seguimiento']['tip_actividad']));
			$this->set('cod_formulario', $formulario['Formulario']['cod_formulario']);
			$this->set('tip_actividad',$this->data['Seguimiento']['tip_actividad']);
			
			//[Gonzalo] Codigo solo yo
			$voluntarios = array(0 => 'CUALQUIERA');
			$voluntarios+= array($this->Session->read('cod_voluntario') => '*** YO ***');
			$voluntarios+= $this->Voluntario->getAllAsArray($cod_programa);

			$this->set('voluntarios', $voluntarios);
	
		}
		
		//#####################################################
		//#####################################################
		//#####################################################
		//#####################################################
		function ingresar_seguimiento2()
		{
			// Se mandan algunas variables al layout para que se vea bien COPIAR SIEMPRE Y CAMBIAR NOMBRE PAGINA
			$this->escribirHeader("Ingreso del Seguimiento");
			
			$cod_caso= $this->data['Seguimiento']['cod_caso'];
			if(!isset($this->data['Seguimiento']['nom_comentario']))
				$nom_comentario="";
			else
				$nom_comentario= $this->data['Seguimiento']['nom_comentario'];
			$tip_proxrevision= $this->data['Seguimiento']['tip_proxrevision'];
			// [Diego Jorquera] Indica si hay que cerrar el caso
			$bit_nocerrar = $this->data['Seguimiento']['bit_nocerrar'];
			
			$se_creo_seguimiento=0;
			$tip_actividad="";
			$cod_programa=1;
			$cod_actividad_solderivacion = 16;
	
			//Si se selecciono Derivacion.
			
			if($this->data['Seguimiento']['bit_derivar'] > -1){
						
				// [Diego Jorquera] Si el tipo de revisiÃ³n es "Visita", pasa a la fila de derivaciones
				// y queda en estado "Pendiente" (previamente "Derivacion")
				$est_caso="Pendiente";
				$query = "
				UPDATE `casos` 
				SET `est_caso` = '".$est_caso."'
				WHERE `cod_caso` = ".$cod_caso."
				LIMIT 1 ;
				";
				
				$cambiar_est_caso = $this->Caso->query($query);
				//die();
				
			}
		
			//Se ingresa el cÃ¯Â¿Â½digo del voluntario que atendiÃ¯Â¿Â½ el caso
			$cod_voluntario=$this->Session->read('cod_voluntario');
			$this->data['Seguimiento']+=array('cod_voluntario' => $cod_voluntario);
						
			
			//obtenemos el programa al que pertenece este caso
			$tupla = $this->Voluntario->findByCodPersona($cod_voluntario);
			if($tupla){
				//[Gabriela] Se busca el cÃ¯Â¿Â½digo de la actividad adecuada
				$tip_actividad=$this->data['Seguimiento']['tip_actividad'];
				
				//[Gabriela] Se busca el cÃ¯Â¿Â½digo del programa actual y el cÃ¯Â¿Â½digo de la actividad
				
				$cod_programa=$tupla['Voluntario']['cod_programa'];
				$actividad=$this->Actividad->find(array('Actividad.tip_actividad'=>$tip_actividad, 'Actividad.cod_programa'=>$cod_programa));
				$cod_actividad=$actividad['Actividad']['cod_actividad'];
			}
			
			$this->data['Seguimiento']+=array('nom_comentario' => $nom_comentario);
			$this->data['Seguimiento']+=array('cod_caso' => $cod_caso);
			$this->data['Seguimiento']+=array('cod_actividad' => $cod_actividad);
			
			$cod_soloyo=$this->data['Caso']['cod_soloyo'];
			if($cod_soloyo!=0)
			{
				$this->data['Seguimiento']+=array('cod_voluntarioproxrevision' => $cod_soloyo);
				$this->Caso->execute("UPDATE casos SET cod_soloyo=".$cod_soloyo." WHERE cod_caso=".$cod_caso);
			}
			else
			{
				$this->data['Seguimiento']+=array('cod_voluntarioproxrevision' => null);
				$this->Caso->execute("UPDATE casos SET cod_soloyo=NULL WHERE cod_caso=".$cod_caso);
			}			
			
			if($this->Seguimiento->save($this->data['Seguimiento'])){
			
				$se_creo_seguimiento=1;
				$mensaje=12;
			
			}
			else {
				$mensaje=13;
			}
			
			//[Gabriela] Se guardan las respuestas del formulario estÃ¯Â¿Â½ndar
			
			if($se_creo_seguimiento==1)
			{
				//[Ignacio] Se busca el codigo de formulario
				$formulario=$this->Formulario->find(array('Actividad.cod_programa'=>$cod_programa, 'Actividad.tip_actividad'=>$tip_actividad));
				if(isset($formulario['Formulario']['cod_formulario']))
					$cod_formulario=$formulario['Formulario']['cod_formulario'];
				else $cod_formulario=0;
				//$cod_caso=$this->Caso->getLastInsertId();
				$cod_seguimiento= $this->Seguimiento->getLastInsertId();
				//variable para saber si se ingresaron las respuestas bien, cuando se conveirte en false es porque algo fallÃ¯Â¿Â½
				$exito=true;				
				
				$subpreguntas=$this->Formulario->getSubpreguntas($cod_formulario);
				
				//[Ignacio] la funcion anterior trae todas las subpreguntas del formulario
				foreach($subpreguntas as $subpregunta)
				{
					$tipo=$subpregunta['Subpregunta']['tip_tipoinput'];
					switch($tipo){
						case 'text':
						case 'checkbox':
							$dato=$this->data['Respuestaficha'][$subpregunta['Subpregunta']['cod_subpregunta']]?$this->data['Respuestaficha'][$subpregunta['Subpregunta']['cod_subpregunta']]:"";
						break;
						case 'radio':
							$dato=(isset($this->data['Respuestaficha']["g".$subpregunta['Subpregunta']['cod_grupo']]) && $this->data['Respuestaficha']["g".$subpregunta['Subpregunta']['cod_grupo']]==$subpregunta['Subpregunta']['cod_subpregunta'])?"1":"0";
						break;
						case 'textarea';
							$dato=$this->data['Respuestaficha'][$subpregunta['Subpregunta']['cod_subpregunta']]?$this->data['Respuestaficha'][$subpregunta['Subpregunta']['cod_subpregunta']]:"";

						break;
					}
						
					//[Gabriela] Se crea una respuestaficha para almacenar la respuesta a esta subpregunta
					
					$arreglo_respuesta=array('num_evento'=>$cod_seguimiento, 'cod_subpregunta'=>$subpregunta['Subpregunta']['cod_subpregunta'], 'nom_respuesta' => $dato);
					$this->Respuestaficha->create($arreglo_respuesta);
					if(!$this->Respuestaficha->save($arreglo_respuesta))	{
						$exito=false;
					}
				}
				if($exito) {
					$mensaje2="";
					// [Diego Jorquera] Si el beneficiario va a la fila de derivaciÃ³n, agregar seguimiento
					// especial que indique que se hizo esto (para fines estadÃ­sticos)
					
					if ($this->data['Seguimiento']['bit_derivar'] > -1) {
						$data_seg_derivacion = array('Seguimiento' => array());
						$data_seg_derivacion['Seguimiento']['num_evento'] = $cod_seguimiento + 1;
						$data_seg_derivacion['Seguimiento']['cod_caso'] = $cod_caso;
						$data_seg_derivacion['Seguimiento']['cod_voluntario'] = $cod_voluntario;
						$data_seg_derivacion['Seguimiento']['cod_actividad'] = $cod_actividad_solderivacion;
						$data_seg_derivacion['Seguimiento']['fec_proxrevision'] = 0;
						$this->Seguimiento->save($data_seg_derivacion);
					}
				}
				else $mensaje2=7;
			}

			else $mensaje2="";
			
			// [Diego Jorquera] Ir al formulario de retiro de caso si es que se desea
			// cerrar el caso inmediatamente
			if (!$bit_nocerrar && $exito) {
				$this->redirect('/beneficiarios/retirar/'.$cod_caso.'/30');
			} else {
				$mensaje=$mensaje.". ".$mensaje2;
				$this->redirect('/beneficiarios/index/'.$mensaje);
			}
						
		}
		
		//#####################################################
		//#####################################################
		//#####################################################
		//#####################################################
		function modificar_fichaseguimiento()
		{
			$this->ver_fichaseguimiento();
			
			$comunas=$this->Comuna->getAllAsArray();
			$this->set('comunas', $comunas);
			
			$cod_persona=$this->Session->read('cod_voluntario');
			$voluntario= $this->Voluntario->find(array("Voluntario.cod_persona" => $cod_persona), array(), array(), "", "", -1);
			$cod_programa=$voluntario['Voluntario']['cod_programa'];
			if($cod_programa == 1)
			{
				$this->set('defaultStyle','display:none;');
			}
			$porquien = $this->Caso->getPossibleValues('est_porquien');
			$this->set('porquien',$porquien);
			$programa=$this->Programa->findByCodPrograma($cod_programa);
			$tip_rolfamilia= $this->Beneficiario->getRolesFamilia();
			$this->set('tip_rolfamilia',$tip_rolfamilia);
			//$tipos_casos=$this->Tipocaso->getAllAsArray($cod_programa);
			//$this->set('tipos_casos', $tipos_casos);	

			
			
			
			
			if($cod_programa == 2)
			{
				$tipos_casos=$this->Tipocaso->getAllAsArray($cod_programa);
				$this->set('tipos_casos', $tipos_casos);
			}
			else
			{
				$tipos_casos=$this->Tipocaso->findAll();
				$aux=array();
				for($n=0; $n<count($tipos_casos); $n++)
					if( $tipos_casos[$n]['Tipocaso']['cod_tipocaso']==3 || $tipos_casos[$n]['Tipocaso']['cod_tipocaso']==4 )
						$aux += array($tipos_casos[$n]['Tipocaso']['cod_tipocaso']=>$tipos_casos[$n]['Tipocaso']['nom_tipocaso']);
				$this->set('tipos_casos', $aux);
			
				$aux=array();
				for($n=0; $n<count($tipos_casos); $n++)
					if( $tipos_casos[$n]['Tipocaso']['cod_programa']==1 && $tipos_casos[$n]['Tipocaso']['bit_aborto']==0 && $tipos_casos[$n]['Tipocaso']['cod_tipocaso']!=3 && $tipos_casos[$n]['Tipocaso']['cod_tipocaso']!=4)
						$aux += array($tipos_casos[$n]['Tipocaso']['cod_tipocaso']=>$tipos_casos[$n]['Tipocaso']['nom_tipocaso']);
				$this->set('tipos_casos_noaborto', $aux);
			
				$aux=array();
				for($n=0; $n<count($tipos_casos); $n++)
					if( $tipos_casos[$n]['Tipocaso']['cod_programa']==1 && $tipos_casos[$n]['Tipocaso']['bit_aborto']==1 && $tipos_casos[$n]['Tipocaso']['cod_tipocaso']!=3 && $tipos_casos[$n]['Tipocaso']['cod_tipocaso']!=4)
						$aux += array($tipos_casos[$n]['Tipocaso']['cod_tipocaso']=>$tipos_casos[$n]['Tipocaso']['nom_tipocaso']);
				$this->set('tipos_casos_aborto', $aux);
			}
			$this->set('cod_programa', $cod_programa);
			
			
			
			
			
			// XXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
			// XXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
			// XXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
			// XXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
			// 02-May-2009
			
			// GET seguimiento
			// GET caso
			// GET tipoingreso
			$num_evento = $this->data['Seguimiento']['num_evento'];
			$seguimiento= $this->Seguimiento->find(array("Seguimiento.num_evento" => $num_evento), array(), array(), "", "", -1);
			
			$cod_caso	= $seguimiento['Seguimiento']['cod_caso'];
			$caso 		= $this->Caso->find(array("Caso.cod_caso" => $cod_caso), array(), array(), "", "", -1); 
			
			$cod_tipoingreso = $caso['Caso']['cod_tipoingreso'];
			$tipoingreso	 = $this->Tipoingreso->find(array("Tipoingreso.cod_tipoingreso" => $cod_tipoingreso), array(), array(), "", "", -1); 
			
			//die($tipoingreso['Tipoingreso']['cod_tipoingreso']);
			
			// Via de acceso: Cu�l?
			$tipoingreso_all=$this->Tipoingreso->getAllAsArray(1);
			$this->set('tipoingreso', $tipoingreso_all);
			$this->set('seleccionado_tipoingreso', $tipoingreso['Tipoingreso']['cod_tipoingreso']);
			// Incluir actual
				
			//var_dump($seguimiento);
			//die();
			
			//die($tipoingreso['Tipoingreso']['cod_medio']);
			
			// Via de acceso: Tipo ingreso
			$medio=$this->Tipoingreso->getMedio();
			$this->set('medio', $medio);
			$this->set('seleccionado_medio', $tipoingreso['Tipoingreso']['cod_medio']);

			// Incluir actual
			
			//die($tipoingreso['Tipoingreso']['cod_tipoingreso']);
			
			// Tipo ingreso: Por qui�n llama?
			$porquien = $this->Caso->getPossibleValues('est_porquien');
			$this->set('porquien',$porquien);
			$this->set('seleccionado_porquien', $caso['Caso']['est_porquien']);
			// Incluir actual
			
			
			
		}
		
		//#####################################################
		//#####################################################
		//#####################################################
		//#####################################################
		function modificar_fichaseguimiento2()
		{
			$this->escribirHeader("Modificar Beneficiario");
			
			// [Diego Jorquera] Obtener cÃƒÂ³digo de persona (beneficiario)
			$cod_persona = $this->data['Persona']['cod_persona'];
			
			//// [Diego Jorquera] Redireccionar a la pÃƒÂ¡gina del mismo beneficiario, ahora activo
			//falta cachar como saber a donde redirecionar... :S  ver1 ver2 o ver3
			//ver2 es para beneficiarios retirados
			//ver1 es para beneficiarios con casos en seguimiento telefonoco
			//ver3 es para beneficiarios con casos en seguiminto clinico.
			$this->redirect('/beneficiarios/ver/'.$cod_persona.'/');
			exit();
		
		}
		
		//#####################################################
		//#####################################################
		//#####################################################
		//#####################################################
		function modificar_fichaseguimiento_persona()
		{
			
			$this->escribirHeader("Modificar Persona");
			
			// [Diego Jorquera] Obtener cÃƒÂ³digo de persona (beneficiario)
			$cod_persona = $this->data['Persona']['cod_persona'];
			// [Diego Jorquera] Unir RUT con el cÃƒÂ³digo verificador
			$this->data['Persona']['nom_rut'].=("-".$this->data['Persona']['num_rutcodver']);
				
			$num_telefono1_pre=$this->data['Persona']['num_telefono1_pre'];
			$num_telefono1_post=$this->data['Persona']['num_telefono1_post'];
			
			if($num_telefono1_pre!="")
				$num_telefono1_pre=$num_telefono1_pre.'-';
			$this->data['Persona']['num_telefono1']=$num_telefono1_pre.$num_telefono1_post;
			
			$num_telefono2_pre=$this->data['Persona']['num_telefono2_pre'];
			$num_telefono2_post=$this->data['Persona']['num_telefono2_post'];
			
			if($num_telefono2_pre!="")
				$num_telefono2_pre=$num_telefono2_pre.'-';
			$this->data['Persona']['num_telefono2']=$num_telefono2_pre.$num_telefono2_post;

			//[DAWES]se pide la edad pero se guarda la fech nacimiento
			$edad=$this->data['Persona']['ano_nacimiento'];
			$anio_actual= (int)date('Y');
			$anio_nac= $anio_actual-$edad;
			$this->data['Persona']['ano_nacimiento']=$anio_nac;
	
			$cod_voluntario = $this->Session->read('cod_voluntario');
			$voluntario = $this->Voluntario->find(array("Voluntario.cod_persona" => $cod_voluntario), array(), array(), "", "", -1);
			$cod_programa = $voluntario['Voluntario']['cod_programa'];
			
			
			$contador = 0;
			// EST: por quien llama??
			$porquien = $this->Caso->getPossibleValues('est_porquien');
			$cod_porquien = 0;
			foreach($porquien as $por)
			{
				$contador++;
				/*
				 * XXXXXXXXXXXXXXXXXXXXXXXX
				 */
				if( strcmp(trim($this->data['Caso']['est_porquien']),trim($por)) == 0 )
				{
					$cod_porquien = $contador;
				}
			}
			
			//$est_porquien = $cod_porquien;
			
			$this->data['Persona']['cod_porquien'] = $cod_porquien;
			
			$cod_tipoingreso=$this->data['Caso']['cod_tipoingreso'];
			
			
			
					
			if( $cod_programa == 1 ) // Sólo para Acoge
			{
				// Riesgo de aborto
				if($this->data['Caso']['cod_tipocaso'] == 3)
					$cod_tipocaso = $this->data['Caso']['cod_tipocaso_aborto'];
				else
					$cod_tipocaso = $this->data['Caso']['cod_tipocaso_noaborto'];
			}
			else
			{
				$cod_tipocaso=$this->data['Caso']['cod_tipocaso'];
			}
			
			//var_dump( $this->data['Persona'] );
			//die();
			
			if($this->Persona->save($this->data['Persona'])){
							
				//guardar los datos del beneficiario y seguimiento
				$this->data['Beneficiario']['cod_persona']=$this->data['Persona']['cod_persona'];
				if($this->Beneficiario->save($this->data['Beneficiario'])){
					$this->Seguimiento->save($this->data['Seguimiento']);
					
					$num_evento = $this->data['Seguimiento']['num_evento'];
					$seguimiento= $this->Seguimiento->find(array("Seguimiento.num_evento" => $num_evento), array(), array(), "", "", -1);
					
					$cod_caso	= $seguimiento['Seguimiento']['cod_caso'];
					$caso 		= $this->Caso->find(array("Caso.cod_caso" => $cod_caso), array(), array(), "", "", -1); 
					
					//$caso = $this->Caso->read(null, $this->data['Seguimiento']['cod_caso']);
					//$caso['Caso']['cod_tipocaso'] = $this->data['Caso']['cod_tipocaso'];
					$caso['Caso']['cod_tipocaso'] = $cod_tipocaso;
					
					/*
					 * XXXXXXXXXXXXXXXXXXXXXXX
					 * 03-May-2009
					 */
					
					$caso['Caso']['est_porquien']	 = $this->data['Caso']['est_porquien'];
					$caso['Caso']['cod_tipoingreso'] = $cod_tipoingreso;
					
					//var_dump($this->data);
					
					//die();
					
					$this->Caso->save( $caso );
					$msg=1;
					
				}
				else $msg=2;
			}
			else $msg=3;
			
			// [Diego Jorquera] Redireccionar a la pÃ¡gina del mismo beneficiario; a cuÃ¡l
			// pÃ¡gina depende de sus casos activos
			
			$cod_voluntario = $this->Session->read('cod_voluntario');
			$voluntario = $this->Voluntario->find(array("Voluntario.cod_persona" => $cod_voluntario), array(), array(), "", "", -1);
			$cod_programa = $voluntario['Voluntario']['cod_programa'];
			$casos_activos = $this->Caso->query("
				SELECT casos.est_caso
				FROM casos, tipocasos 
				WHERE casos.cod_tipocaso = tipocasos.cod_tipocaso 
				AND tipocasos.cod_programa = " . $cod_programa." 
				AND casos.cod_beneficiario = " . $cod_persona . "
				ORDER BY casos.est_caso
				LIMIT 0,1;"
			);
			
			$url_ver = "";
			
			if (count($casos_activos)) {
				if ($casos_activos[0]['casos']['est_caso'] == 'Activo') {
					$url_ver = "ver";
				} else if ($casos_activos[0]['casos']['est_caso'] == 'Retiro') {	
					$url_ver = "ver2";
				} else if ($casos_activos[0]['casos']['est_caso'] == 'Derivacion') {	
					$url_ver = "ver3";
				}
			}
			
			$this->redirect('/beneficiarios/'.$url_ver.'/'.$cod_persona.'/'.$msg);
			exit();			
		}
			
		//#####################################################
		//#####################################################
		//#####################################################
		//#####################################################
		function modificar_fichaseguimiento_fichainicial()
		{
			// Se mandan algunas variables al layout para que se vea bien COPIAR SIEMPRE Y CAMBIAR NOMBRE PAGINA
			$this->escribirHeader("Modificacion de ficha dinamica");
			
						
			//[Dawes] Se guardan las respuestas del formulario inicia (es dinamico)
			//--------------
			//Se obtiene el numero de persona del beneficiario.
			$numero_persona_beneficiario=$this->data['Persona']['cod_persona'];
			//Se obtiene el numero de evento que identifica al seguimiento
			$cod_seguimiento=$this->data['Seguimiento']['num_evento'];
			
			//Se obtiene el codigo del programa, el cual lo podemos obtener del voluntario que esta usando la aplicacion actualente.
			$cod_persona=$this->Session->read('cod_voluntario');
			$voluntario= $this->Voluntario->findByCodPersona($cod_persona);
			$cod_programa=$voluntario['Voluntario']['cod_programa'];
			
			//Se busca el codigo de formulario inicial, dependiendo de quÃ© programa es.
			$formulario=$this->Formulario->find(array('Actividad.cod_programa'=>$cod_programa, 'Actividad.tip_actividad'=>'Inicial'));
			if(isset($formulario['Formulario']['cod_formulario']))
				$cod_formulario=$formulario['Formulario']['cod_formulario'];
			else $cod_formulario=0;
			//variable para saber si se ingresaron las respuestas bien, cuando se conveirte en false es porque algo fallÃ¯Â¿Â½
			$exito=true;				
			
			//[Ignacio] Se traen todas las subpreguntas del formulario
			$subpreguntas=$this->Formulario->getSubpreguntas($cod_formulario);
			
			//[Dawes] Se rescatan las respuestas ingresadas
			foreach($subpreguntas as $subpregunta)
			{			
				$tipo=$subpregunta['Subpregunta']['tip_tipoinput'];
				switch($tipo){
					case 'text':
					case 'checkbox':
						$dato=$this->data['Respuestaficha'][$subpregunta['Subpregunta']['cod_subpregunta']]?$this->data['Respuestaficha'][$subpregunta['Subpregunta']['cod_subpregunta']]:"";
					break;
					case 'radio':
						$dato=(isset($this->data['Respuestaficha']["g".$subpregunta['Subpregunta']['cod_grupo']]) && $this->data['Respuestaficha']["g".$subpregunta['Subpregunta']['cod_grupo']]==$subpregunta['Subpregunta']['cod_subpregunta'])?"1":"0";
					break;
					case 'textarea';
						$dato=$this->data['Respuestaficha'][$subpregunta['Subpregunta']['cod_subpregunta']]?$this->data['Respuestaficha'][$subpregunta['Subpregunta']['cod_subpregunta']]:"";

					break;
				}
				//[Dawes] se rescata la clave primaria de la Respuesta_Ficha
				$codigo=$this->data['codigo'][$subpregunta['Subpregunta']['cod_subpregunta']];
				/*
				$resp_id=$cod_seguimiento;
				$this->flash('num_evento: '.$resp_id.' ok?',2);
				$resp_id=$subpregunta['Subpregunta']['cod_subpregunta'];
				$this->flash('subpregunta: '.$resp_id.' ok?',2);
				$resp_id=$dato;
				$this->flash('dato '.$resp_id.' ok?',2);
				$this->flash('          ',2);
				$resp_id=$codigo;
				$this->flash('cod_respuesta traido desde ficha: '.$resp_id.' ok?',2);
				$this->flash('          ',2);
				*/
				//[Dawes] se sobre-escribe la vieja tupla o en su defecto se escribe una nueva si no habia antes.
				$arreglo_respuesta=array('cod_respuesta'=>$codigo, 'num_evento'=>$cod_seguimiento, 'cod_subpregunta'=>$subpregunta['Subpregunta']['cod_subpregunta'], 'nom_respuesta'=>$dato);
				if(!$this->Respuestaficha->save($arreglo_respuesta))	{
					$exito=false;
				}
								
			}
			// [GONZALO]			
			$codSubpregunta = $this->data['Respuestaficha']['g24'];
			$sp = $this->Subpregunta->read(null, $codSubpregunta);
			$ben = $this->Beneficiario->read(null,$numero_persona_beneficiario);
			$ben['Beneficiario']['cod_convenio']= $sp['Subpregunta']['num_fila'];
			$this->Beneficiario->save($ben, true);
			
			
			if($exito) $mensaje2="";
			else $mensaje2=7;
						
			$this->redirect('/beneficiarios/');
			exit();			
		}
			
		//#####################################################
		//#####################################################
		//#####################################################
		//#####################################################
		function ver_fichaseguimiento()
		{
			// Se mandan algunas variables al layout para que se vea bien COPIAR SIEMPRE Y CAMBIAR NOMBRE PAGINA
			$this->escribirHeader("Ficha del Seguimiento");
			
			$cod_persona=$this->Session->read('cod_voluntario');
			$voluntario= $this->Voluntario->findByCodPersona($cod_persona);
			$cod_programa=$voluntario['Voluntario']['cod_programa'];
			
			$cod_actividad=$this->data['Seguimiento']['cod_actividad'];
			$num_evento=$this->data['Seguimiento']['num_evento'];
			
			$formulario=$this->Formulario->find(array('Actividad.cod_programa' => $cod_programa, 'Formulario.cod_actividad'=>$cod_actividad));
			
			if($formulario!=null)
			{	
				$this->set("cod_formulario",$formulario['Formulario']['cod_formulario']);
				$this->set('num_evento', $num_evento);
				
				//[Gabriela] se revisa el tipo de actividad del seguimiento a mostrar
				
				if($formulario['Actividad']['tip_actividad']=='Inicial')
				{
					//[Gabriela] Se manda ademÃ¯Â¿Â½s informaciÃ¯Â¿Â½n bÃ¯Â¿Â½sica del beneficiario y del caso
					
					$cod_beneficiario= $this->data['Beneficiario']['cod_persona'];
					$informacion_personal= $this->Persona->findByCodPersona($cod_beneficiario);
					$informacion_beneficiario=$this->Beneficiario->findByCodPersona($cod_beneficiario);
					$informacion_seguimiento=$this->Seguimiento->findByNumEvento($num_evento);
					$informacion_caso=$this->Caso->findByCodCaso($informacion_seguimiento['Seguimiento']['cod_caso']);
					$tipocaso=$this->Tipocaso->findByCodTipocaso($informacion_caso['Caso']['cod_tipocaso']);
					
					
					//Arreglo para poder ver el numero de tel separado de su prefijo en las vistas de modificacion.
					$tel1=explode("-", $informacion_personal['Persona']['num_telefono1']);
					$tel2=explode("-", $informacion_personal['Persona']['num_telefono2']);
					$informacion_personal['Persona']['num_telefono1_post']=(isset($tel1[1]))?$tel1[1]:$tel1[0];
					$informacion_personal['Persona']['num_telefono1_pre']=(isset($tel1[1]))?$tel1[0]:"";
					$informacion_personal['Persona']['num_telefono2_post']=(isset($tel2[1]))?$tel2[1]:$tel2[0];
					$informacion_personal['Persona']['num_telefono2_pre']=(isset($tel2[1]))?$tel2[0]:"";
					
					//Arreglo para poder ver el RUT separado de su cod verificador en las vistas de modificacion.
					$ruts=explode("-",$informacion_personal['Persona']['nom_rut']);
					$informacion_personal['Persona']['nom_rut_pre']=$ruts[0];
					$informacion_personal['Persona']['num_rutcodver']=$ruts[1]?$ruts[1]:"";
					
					if($informacion_personal['Persona']['ano_nacimiento']!="")
						$edad= (int)date('Y')-$informacion_personal['Persona']['ano_nacimiento'];
					else
						$edad="-";
					
					$porquienvalue = $informacion_personal['Persona']['cod_porquien'];
					
						
					$this->set('informacion_personal',$informacion_personal);
					$this->set('informacion_beneficiario', $informacion_beneficiario);
					$this->set('informacion_caso', $informacion_caso);
					$this->set('informacion_seguimiento', $informacion_seguimiento);
					$this->set('tipocaso',$tipocaso['Tipocaso']['nom_tipocaso']);
					$this->set('edad', $edad);
					
					$this->set("formulario",1);
				}
				
				else if($formulario['Actividad']['tip_actividad']=='Seguimiento')
				{
					
					$informacion_seguimiento=$this->Seguimiento->findByNumEvento($num_evento);
					$this->set('informacion_seguimiento',$informacion_seguimiento);
					$this->set('informacion', 'Seguimiento Exitoso');
					$this->set("formulario",2);
				}
				else if($formulario['Actividad']['tip_actividad']=='Seguimiento_Fallido')
				{
					
					$informacion_seguimiento=$this->Seguimiento->findByNumEvento($num_evento);
					$this->set('informacion_seguimiento',$informacion_seguimiento);
					$this->set('informacion', 'Seguimiento Fallido');
					$this->set("formulario",3);
				}
				
				else if($formulario['Actividad']['tip_actividad']=='Cierre')
				{
					
					$informacion_seguimiento=$this->Seguimiento->findByNumEvento($num_evento);
					$this->set('informacion', 'Seguimiento de Cierre');
					$this->set("formulario",4);
				}
				else if($formulario['Actividad']['tip_actividad']=='Seguimiento Clinico')
				{
					
					$informacion_seguimiento=$this->Seguimiento->findByNumEvento($num_evento);
					$this->set('informacion_seguimiento',$informacion_seguimiento);
					$this->set('informacion', 'Seguimiento Clinico');
					$this->set("formulario",5);
				}
				else if($formulario['Actividad']['tip_actividad']=='Cierre Clinico')
				{
					
					$informacion_seguimiento=$this->Seguimiento->findByNumEvento($num_evento);
					$this->set('informacion_seguimiento',$informacion_seguimiento);
					$this->set('informacion', 'Cierre Clinico');
					$this->set("formulario",6);
				}
			}
			else
				$this->set("formulario",-1);
		}
		
		
		//#####################################################
		//#####################################################
		//#####################################################
		//#####################################################
		// [Javier] FunciÃ¯Â¿Â½n que asigna para hoy a un voluntario un caso no agendado
		function agendar_para_hoy(){
		
			// [Javier] Se capturan los datos provenientes del formulario:
			$cod_voluntario = $this->data['Voluntario']['cod_voluntario'];
			$cod_caso = $this->data['Caso']['cod_caso'];
			$num_evento = $this->data['Seguimiento']['num_evento'];
			
			// [Javier] Para evitar que hayan agendamientos concurrentes entre voluntarios que operan en el mismo pool,
			// se verifica que efectivamente no exista alguien que lo agendÃ¯Â¿Â½ hace algunos segundos atrÃ¯Â¿Â½s:
			$consultar_agendamiento = $this->Seguimiento->query("
			
				SELECT cod_voluntarioproxrevision 
				FROM `seguimientos` 
				WHERE `cod_caso` = ".$cod_caso."
				AND `num_evento` = ".$num_evento."
				LIMIT 1;
				
			");
			
			if($consultar_agendamiento[0]['seguimientos']['cod_voluntarioproxrevision'] == NULL)
			{
				// [Javier] Se estÃ¯Â¿Â½ chato con las malditas consultas de Cake que no funcionan. Por ahora, pecado mortal:
				$cambiar_valores_tupla = $this->Seguimiento->query("
				
				UPDATE `seguimientos` 
				SET `cod_voluntarioproxrevision` = '".$cod_voluntario."'
				WHERE `cod_caso` = ".$cod_caso."
				AND `num_evento` = ".$num_evento."
				LIMIT 1 ;
					
				");
				
			}
			
			else
			{
				// aquÃ¯Â¿Â½ seteamos el mensaje de error.
			}

			// [Javier] Se redirecciona al index:
			$this->redirect('beneficiarios/index/');
			exit();

		}
		
		
		
		//#####################################################
		//#####################################################
		//#####################################################
		//#####################################################
		// [Javier] FunciÃ¯Â¿Â½n que quita la asignaciÃ¯Â¿Â½n para hoy
		function desagendar(){
		
			// [Javier] Se capturan los datos provenientes del formulario:
			$cod_voluntario = $this->data['Voluntario']['cod_voluntario'];
			$cod_caso = $this->data['Caso']['cod_caso'];
			$num_evento = $this->data['Seguimiento']['num_evento'];
			$nombre = $this->data['Beneficiario']['nombre'];
			
			// [Javier] La fecha de hoy (segÃ¯Â¿Â½n el servidor, no el SGBD):
			$hoy = date('Y-d-m');
	
			// [Javier] Se estÃ¯Â¿Â½ chato con las malditas consultas de Cake que no funcionan. Por ahora, pecado mortal:
			$cambiar_valores_tupla = $this->Seguimiento->query("
			
				UPDATE `seguimientos` 
				SET `cod_voluntarioproxrevision` = NULL
				WHERE `cod_caso` = ".$cod_caso."
				AND `num_evento` = ".$num_evento."
				LIMIT 1 ;
				
			");
			
			// [Javier] Al desagendar al beneficiario se estÃ¯Â¿Â½ liberando su seguimiento al pool general, por lo
			// que requiero dejar el bit "sÃ¯Â¿Â½lo_yo" de la tabla "casos" en cero. Doble pecado mortal:
			$limpiar_solo_yo = $this->Seguimiento->query("
			
				UPDATE `casos` 
				SET `cod_soloyo` = NULL
				WHERE `cod_caso` = ".$cod_caso."
				LIMIT 1 ;
				
			");
			
			//$this->set('msg','El beneficiario '.$nombre.' ha sido desagendado correctamente');
			
			// [Javier] Se redirecciona al index:
			$this->redirect('beneficiarios/index/');
			exit();
			
		}
		
		
		//#####################################################
		//#####################################################
		//#####################################################
		//#####################################################
		// [Javier] FunciÃ¯Â¿Â½n que registra en el sistema que se tuvo contacto telefÃ¯Â¿Â½nico con el beneficiario derivado:
		function registrar_llamada_derivado(){
			
			// [Javier] Se captura el cÃ¯Â¿Â½digo del caso
			$cod_caso = $this->data['Caso']['cod_caso'];
			
			$cambiar_bit_llamada = $this->Caso->query("
				UPDATE `casos` 
				SET `bit_llamada` = 1
				WHERE `cod_caso` = ".$cod_caso."
				LIMIT 1 ;
			");
			
			// [Javier] Se redirecciona al index:
			$this->redirect('beneficiarios/index/');
			exit();
			
		}
		
		
		//#####################################################
		//#####################################################
		//#####################################################
		//#####################################################
		// [Javier] FunciÃ¯Â¿Â½n que des-registra en el sistema que se tuvo contacto telefÃ¯Â¿Â½nico con el beneficiario derivado:
		function deshacer_llamada_derivado(){
			
			// [Javier] Se captura el cÃ¯Â¿Â½digo del caso
			$cod_caso = $this->data['Caso']['cod_caso'];
			
			$cambiar_bit_llamada = $this->Caso->query("
				UPDATE `casos` 
				SET `bit_llamada` = 0
				WHERE `cod_caso` = ".$cod_caso."
				LIMIT 1 ;
			");
			
			// [Javier] Se redirecciona al index:
			$this->redirect('beneficiarios/index/');
			exit();
			
		}
		
		//#####################################################
		//#####################################################
		//#####################################################
		//#####################################################
		//Para ver casos en atencion Clinica.
		function ver3($inicio=null,$exito=null)
		{
			// Se mandan algunas variables al layout para que se vea bien COPIAR SIEMPRE Y CAMBIAR NOMBRE PAGINA
			$this->escribirHeader("Datos del beneficiario");
			
			// Rescatamos el cod_persona del beneficiario que tenemos que desplegar
			if($inicio!=null)
				$persona=$this->Beneficiario->findByCodPersona($inicio);
			else
				$persona=$this->Beneficiario->findByCodPersona($this->data['Persona']['cod_persona']); 
			
			$this->set('persona',$persona);
			
			$codigo=$persona['Persona']['cod_persona'];
						
			$estado="Derivacion";  //solo ve los casos derivados del beneficiario
			
			$comunas=$this->Comuna->getAllAsArray();
			$this->set('comunas', $comunas);
			
			//calculamos edad del beneficiario
			if($persona['Persona']['ano_nacimiento']!=null)
				$edad= (int)date('Y')-$persona['Persona']['ano_nacimiento'];
			else 
				$edad='-';
			$persona['Persona']+=array('edad'=>$edad);
			$this->set('edad',$persona['Persona']['edad']);
			
			//[Stephanie]Busca casos activos que sean de ese beneficiario	
		
			$caso_derivado=$this->Caso->find("est_caso = '$estado' and cod_beneficiario = $codigo", "", "", "", "", 0);
			$this->set('caso_derivado', $caso_derivado);
			
			$cod_caso=$caso_derivado['Caso']['cod_caso'];
			
			$seguimientos=$this->Seguimiento->findAll(array( "Seguimiento.cod_caso"=>$cod_caso));
			
			foreach($seguimientos as $i => $v){
				$seguimientos[$i]+=$this->Voluntario->find(array("Persona.cod_persona"=>$v['Seguimiento']['cod_voluntario']),"","","","",-1);
				$seguimientos[$i]['Seguimiento']['fec_ejecucion']=$this->Seguimiento->toFecha($seguimientos[$i]['Seguimiento']['fec_ejecucion']);
				
				
				$seguimientos[$i]+=$this->Actividad->find(array("Actividad.cod_actividad"=>$v['Seguimiento']['cod_actividad']),"","","","",-1);
				//[Diego] Obtenemos la descripciÃ¯Â¿Â½n del seguimiento, para cada tipo de seguimiento, para cada programa
				//[Diego] Primero obtenemos el codigo de la subpregunta que imprimiremos (para obtener siempre la descripcion del seguimiento)
				$cod_subpregunta=$this->Actividad->find(array("Actividad.cod_actividad"=> $seguimientos[$i]['Seguimiento']['cod_actividad']),"","","","",-1);
				
				//[Diego] Luego buscamos e imprimimos la descripciÃ¯Â¿Â½n del seguimiento.
				$resp=$this->Respuestaficha->find(array("Respuestaficha.num_evento"=> $seguimientos[$i]['Seguimiento']['num_evento'], "Respuestaficha.cod_subpregunta" => $cod_subpregunta['Actividad']['cod_subpreguntadescripcion']),"","","","",-1);
				
				if (is_array($resp) && strlen($resp['Respuestaficha']['nom_respuesta']) > 0) {
					$seguimientos[$i]+=$resp;
				} else {
					$resp=array('nom_respuesta' => $seguimientos[$i]['Seguimiento']['nom_comentario']);
					$seguimientos[$i]+=array('Respuestaficha' => $resp);
				}
			}
			
			$this->set('seguimientos', $seguimientos);
			
			//obtiene la ultima vez que se le hizo un seguimiento
			$ultimo=$this->Seguimiento->find("Seguimiento.cod_caso = $cod_caso", "", "fec_ejecucion desc", "", "", 0);
					
			//se pone la fecha ultimo contacto en el orden deseado
			$fecha_ultimo=$this->Seguimiento->toFecha($ultimo['Seguimiento']['fec_ejecucion']);
			$this->set('ultimo', $fecha_ultimo);
		
			$personas=$this->Persona->findByCodPersona($codigo);
			$this->set('personas', $personas);
			
			$beneficiarios=$this->Beneficiario->findByCodPersona($codigo);
			$this->set('beneficiarios', $beneficiarios);
					
			//se pone la fecha proxima revision en el orden deseado
			//$fecha=$this->Persona->toDate($ultimo['Seguimiento']['fec_proxrevision']);
			//$this->set('fecha', $fecha);
		
			// [Diego Jorquera] Mostrar mensaje de Ã©xito
			$this->set('msg_for_layout',$exito);
		}
		
		
		//#####################################################
		//#####################################################
		//#####################################################
		//#####################################################
		function ingresar_seguimiento_clinico()
		{
			// Se mandan algunas variables al layout para que se vea bien COPIAR SIEMPRE Y CAMBIAR NOMBRE PAGINA
			$this->escribirHeader("Seguimiento ClÃ¯Â¿Â½nico");
			
			
			$cod_voluntario=$this->Session->read('cod_voluntario');
			$voluntario=$this->Voluntario->findByCodPersona($cod_voluntario);
			$cod_programa=$voluntario['Voluntario']['cod_programa'];
			
			//[Gabriela] se toman los datos desde la vista anterior
			
			$cod_caso= $this->data['Caso']['cod_caso'];
			$this->set('cod_caso',$cod_caso);
			$tip_actividad=$this->data['Seguimiento']['tip_actividad'];
			
			
			
			//[Gabriela] se obtiene el codigo de formulario asociado al seguimiento
			$formulario=$this->Formulario->find(array('Actividad.cod_programa' => $cod_programa, 'Actividad.tip_actividad'=>$tip_actividad));
			$this->set('cod_formulario', $formulario['Formulario']['cod_formulario']);
			$this->set('tip_actividad',$tip_actividad);
		}
		
		
		//#####################################################
		//#####################################################
		//#####################################################
		//#####################################################
		function ingresar_seguimiento_clinico2()
		{
			// Se mandan algunas variables al layout para que se vea bien COPIAR SIEMPRE Y CAMBIAR NOMBRE PAGINA
			$this->escribirHeader("Reactivar Caso");
			
			//[Gabriela] se obtiene el cÃ¯Â¿Â½digo del programa
			$cod_voluntario=$this->Session->read('cod_voluntario');
			$voluntario=$this->Voluntario->findByCodPersona($cod_voluntario);
			$cod_programa=$voluntario['Voluntario']['cod_programa'];
			
			//[Gabriela] se crea el seguimiento clÃ¯Â¿Â½nico
			
			$cod_caso=$this->data['Caso']['cod_caso'];
			
			//////////////////////////////////////////////
			
			$nom_comentario= "Seguimiento Clinico";
			$tip_proxrevision= "Visita";
			$se_creo_seguimiento=0;
			$tip_actividad="Seguimiento Clinico";
		
		
			//Se ingresa el cÃ¯Â¿Â½digo del voluntario que atendiÃ¯Â¿Â½ el caso
			$this->data['Seguimiento']+=array('cod_voluntario' => $cod_voluntario);
			
			
			$actividad=$this->Actividad->find(array('Actividad.tip_actividad'=>$tip_actividad, 'Actividad.cod_programa'=>$cod_programa));
			$cod_actividad=$actividad['Actividad']['cod_actividad'];
			
			
			$this->data['Seguimiento']+=array('nom_comentario' => $nom_comentario);
			$this->data['Seguimiento']+=array('cod_caso' => $cod_caso);
			$this->data['Seguimiento']+=array('cod_actividad' => $cod_actividad);
			$this->data['Seguimiento']+=array('fec_proxrevision'=>'12-08-2008');
			$this->data['Seguimiento']+=array('tip_proxrevision'=>'Visita');
			
			if($this->Seguimiento->save($this->data['Seguimiento'])){
			
				$se_creo_seguimiento=1;
				$mensaje=12;
			
			}
			else {
				$mensaje=13;
			}
			
			$this->set('mensaje',$mensaje);
			/////////////////////////////////////////////
			
			//[Gabriela] Se guardan las respuestas del formulario estÃ¯Â¿Â½ndar
			
			if($se_creo_seguimiento==1)
			{
				//[Ignacio] Se busca el codigo de formulario
				$formulario=$this->Formulario->find(array('Actividad.cod_programa'=>$cod_programa, 'Actividad.tip_actividad'=>$tip_actividad));
				if(isset($formulario['Formulario']['cod_formulario']))
					$cod_formulario=$formulario['Formulario']['cod_formulario'];
				else $cod_formulario=0;
				//$cod_caso=$this->Caso->getLastInsertId();
				$cod_seguimiento= $this->Seguimiento->getLastInsertId();
				//variable para saber si se ingresaron las respuestas bien, cuando se conveirte en false es porque algo fallÃ¯Â¿Â½
				$exito=true;				
				
				$subpreguntas=$this->Formulario->getSubpreguntas($cod_formulario);
				
				//[Ignacio] la funcion anterior trae todas las subpreguntas del formulario
				foreach($subpreguntas as $subpregunta)
				{
					$tipo=$subpregunta['Subpregunta']['tip_tipoinput'];
					switch($tipo){
						case 'text':
						case 'checkbox':
							$dato=$this->data['Respuestaficha'][$subpregunta['Subpregunta']['cod_subpregunta']]?$this->data['Respuestaficha'][$subpregunta['Subpregunta']['cod_subpregunta']]:"";
						break;
						case 'radio':
							$dato=(isset($this->data['Respuestaficha']["g".$subpregunta['Subpregunta']['cod_grupo']]) && $this->data['Respuestaficha']["g".$subpregunta['Subpregunta']['cod_grupo']]==$subpregunta['Subpregunta']['cod_subpregunta'])?"1":"0";
						break;
						case 'textarea';
							$dato=$this->data['Respuestaficha'][$subpregunta['Subpregunta']['cod_subpregunta']]?$this->data['Respuestaficha'][$subpregunta['Subpregunta']['cod_subpregunta']]:"";

						break;
					}
						
					//[Gabriela] Se crea una respuestaficha para almacenar la respuesta a esta subpregunta
					
					$arreglo_respuesta=array('num_evento'=>$cod_seguimiento, 'cod_subpregunta'=>$subpregunta['Subpregunta']['cod_subpregunta'], 'nom_respuesta' => $dato);
					$this->Respuestaficha->create($arreglo_respuesta);
					if(!$this->Respuestaficha->save($arreglo_respuesta))	{
						$exito=false;
					}
				}
				if($exito) $mensaje=12;
				else $mensaje=13;
			}

			else $mensaje=13;
			
			//$this->set('mensaje2',$mensaje2);
			
			
			$this->redirect('/beneficiarios/index/'.$mensaje);
			
		}
		
		
		//#####################################################
		//#####################################################
		//#####################################################
		//#####################################################
		function reactivar_desde_derivacion()
		{
			//[Gabriela]  El caso pasa desde la derivaciÃ¯Â¿Â½n a la reactivaciÃ¯Â¿Â½n
			// Se mandan algunas variables al layout para que se vea bien COPIAR SIEMPRE Y CAMBIAR NOMBRE PAGINA
			$this->escribirHeader("Reactivar Caso");
			$cod_caso=$this->data['Caso']['cod_caso'];
			$tip_actividad=$this->data['Seguimiento']['tip_actividad'];
			
			///////////////////////////////////////////////
			//[Gabriela] se busca el formulario adecuado
			
			$cod_voluntario=$this->Session->read('cod_voluntario');
			$voluntario=$this->Voluntario->findByCodPersona($cod_voluntario);
			$cod_programa=$voluntario['Voluntario']['cod_programa'];
			
			
			$this->set('cod_caso',$cod_caso);
			
			
			
			
			//[Gabriela] se obtiene el codigo de formulario asociado al seguimiento
			$formulario=$this->Formulario->find(array('Actividad.cod_programa' => $cod_programa, 'Actividad.tip_actividad'=>'Cierre Clinico'));
			$this->set('cod_formulario', $formulario['Formulario']['cod_formulario']);
			$this->set('tip_actividad','Cierre Clinico');
			
			
		}
		
		
		//#####################################################
		//#####################################################
		//#####################################################
		//#####################################################
		function reactivar_desde_derivacion2()
		{
			/*
			// Se mandan algunas variables al layout para que se vea bien COPIAR SIEMPRE Y CAMBIAR NOMBRE PAGINA
			$this->escribirHeader("Reactivar Caso");
			
			
			//[Gabriela] Se cambia el estado del caso
			$cod_caso=$this->data['Caso']['cod_caso'];
			$tip_actividad=$this->data['Seguimiento']['tip_actividad'];
			$se_creo_seguimiento=0;
			$reactivo_caso = $this->Caso->query("
			
				UPDATE `casos` 
				SET `est_caso` = 'Activo'
				WHERE `cod_caso` = ".$cod_caso."
				LIMIT 1 ;
				
			");
			 //[Gabriela] se genera un nuevo seguimiento donde se almacena la informaciÃ¯Â¿Â½n de la vista
			 
			$cod_voluntario=$this->Session->read('cod_voluntario');
			$voluntario=$this->Voluntario->findByCodPersona($cod_voluntario);
			$cod_programa=$voluntario['Voluntario']['cod_programa'];
			
			//[Gabriela] Busco la actividad de cierre de derivaciÃ¯Â¿Â½n clÃ¯Â¿Â½nica del programa actual
			$actividad=$this->Actividad->find(array('Actividad.tip_actividad'=>$tip_actividad, 'Actividad.cod_programa'=>$cod_programa));
			$cod_actividad=$actividad['Actividad']['cod_actividad'];
			
			//[Gabriela] se crea un seguimiento
			$this->data['Seguimiento']+= array('cod_caso'=>$cod_caso,'cod_actividad'=>$cod_actividad ,'nom_comentario'=> 'Cierre Derivacion a Fundacion', 'fec_proxrevision'=>'00-00-0000');
			if($this->Seguimiento->save($this->data['Seguimiento'])){
			
				$se_creo_seguimiento=1;
				//$mensaje=12;
				$mensaje="Seguimiento y ReactivaciÃ¯Â¿Â½n exitosa";
			}
			else {
				//$mensaje=13;
				$mensaje="Reactivacion fallida";
			}
			
			$this->set('mensaje',$mensaje);
			*/
			
			$this->escribirHeader("Reactivar Caso");
			$se_creo_seguimiento=0;
			//[Diego] obtenemos el caso
			$codigo=$this->data['Caso']['cod_caso'];
			
			//[Diego] Obtenemos el voluntario
			$cod_voluntario=$this->Session->read('cod_voluntario');
			$this->data['Caso']+=array('cod_voluntario' => $cod_voluntario);
			$this->data['Seguimiento']+=array('cod_voluntario' => $cod_voluntario);
			
			//[Diego]  Hacemos que ahora este activo
			$this->data['Caso']+=array('est_caso' => 'Activo');
			
			//[Diego]  obtenemos el programa al que pertenece este caso
			$tupla = $this->Voluntario->findByCodPersona($cod_voluntario);
			if($tupla){
				$cod_programa = $tupla['Voluntario']['cod_programa'];
			}
			
			//[Diego]  obtenemos la frecuencia
			$prog=$this->Programa->findByCodPrograma($cod_programa);
			$num_frecuencia=$prog['Programa']['num_frecuencia'];
			$tip_actividad=$this->data['Seguimiento']['tip_actividad'];
			 
			//[Gabriela] Busco la actividad de cierre de derivaciÃ¯Â¿Â½n clÃ¯Â¿Â½nica del programa actual
			$actividad=$this->Actividad->find(array('Actividad.tip_actividad'=>$tip_actividad, 'Actividad.cod_programa'=>$cod_programa));
			$cod_actividad=$actividad['Actividad']['cod_actividad'];			
			
			
			//[Diego]  Revisamos la fecha de cierre de caso
			$this->data['Caso']+=array('fec_retiro' => NULL);
			
			//[Diego]  Finalmente, hacemos UPDATE en la tabla.
			if ($this->Caso->save($this->data['Caso'])){
			// [Diego]  Creamos informaciÃ¯Â¿Â½n de un nuevo seguimiento
				
				//[Diego]  El tipo de seguimiento para reactivaciones se asume llamada entrande
				$tip_ingreso='Llamada_entrante';
				
				//[Diego]  obtenemos fecha del prÃ¯Â¿Â½ximo seguimiento (segÃ¯Â¿Â½n frecuencia)
				$hoy = date('j-n-Y');
				$fecha=explode("-", $hoy); //$fecha[0]=dia, $fecha[1]=mes, $fecha[2]=aÃ¯Â¿Â½o
				$seguimiento= date('Y-m-d', mktime(0, 0, 0, $fecha[1], $fecha[0]+$num_frecuencia,  $fecha[2]));
	
				$this->data['Seguimiento']+=array('fec_proxrevision' => $seguimiento);
				$this->data['Seguimiento']+=array('nom_comentario' => 'Retiro de caso');
				$this->data['Seguimiento']+=array('tip_ingreso' => $tip_ingreso);
				$this->data['Seguimiento']+=array('cod_actividad' => $cod_actividad);
				
				if($this->Seguimiento->save($this->data['Seguimiento']))
				{	
					$mensaje=10;
					$se_creo_seguimiento=1;
				}
				else $mensaje= 11;
				}
			else $mensaje=11;
			
			//[Gabriela] se guarda lo que salÃ¯Â¿Â½a en el formulario de la vista
			/*
			if($se_creo_seguimiento==1)
			{
				//[Ignacio] Se busca el codigo de formulario
				$formulario=$this->Formulario->find(array('Actividad.cod_programa'=>$cod_programa, 'Actividad.tip_actividad'=>$tip_actividad));
				if(isset($formulario['Formulario']['cod_formulario']))
					$cod_formulario=$formulario['Formulario']['cod_formulario'];
				else $cod_formulario=0;
				//$cod_caso=$this->Caso->getLastInsertId();
				$cod_seguimiento= $this->Seguimiento->getLastInsertId();
				//variable para saber si se ingresaron las respuestas bien, cuando se conveirte en false es porque algo fallÃ¯Â¿Â½
				$exito=true;				
				
				$subpreguntas=$this->Formulario->getSubpreguntas($cod_formulario);
				
				//[Ignacio] la funcion anterior trae todas las subpreguntas del formulario
				foreach($subpreguntas as $subpregunta)
				{
					$tipo=$subpregunta['Subpregunta']['tip_tipoinput'];
					switch($tipo){
						case 'text':
						case 'checkbox':
							$dato=$this->data['Respuestaficha'][$subpregunta['Subpregunta']['cod_subpregunta']]?$this->data['Respuestaficha'][$subpregunta['Subpregunta']['cod_subpregunta']]:"";
						break;
						case 'radio':
							$dato=(isset($this->data['Respuestaficha']["g".$subpregunta['Subpregunta']['cod_grupo']]) && $this->data['Respuestaficha']["g".$subpregunta['Subpregunta']['cod_grupo']]==$subpregunta['Subpregunta']['cod_subpregunta'])?"1":"0";
						break;
						case 'textarea';
							$dato=$this->data['Respuestaficha'][$subpregunta['Subpregunta']['cod_subpregunta']]?$this->data['Respuestaficha'][$subpregunta['Subpregunta']['cod_subpregunta']]:"";

						break;
					}
						
					//[Gabriela] Se crea una respuestaficha para almacenar la respuesta a esta subpregunta
					
					$arreglo_respuesta=array('num_evento'=>$cod_seguimiento, 'cod_subpregunta'=>$subpregunta['Subpregunta']['cod_subpregunta'], 'nom_respuesta' => $dato);
					$this->Respuestaficha->create($arreglo_respuesta);
					if(!$this->Respuestaficha->save($arreglo_respuesta))	{
						$exito=false;
					}
				}
				if($exito) $mensaje2="";
				else $mensaje2=7;
			}

			else $mensaje2="";
			
			*/
			
			
			
			$this->redirect('/beneficiarios/index/'.$mensaje);
		}
		
		//#####################################################
		//#####################################################
		//#####################################################
		//#####################################################
		function index_eliminar() {
			$this->escribirHeader("Gestion de Beneficiarios");
			$this->Beneficiario->recursive = 2;
			if(!$this->data['Buscar']['str'])
				$this->set('beneficiarios', $this->Beneficiario->findAll());
			
			else
			{
				$str=$this->data['Buscar']['str'];
				$this->set('beneficiarios', $this->Beneficiario->findAll("nom_nombre LIKE '%".$str."%' OR nom_appat LIKE '%".$str."%' OR nom_apmat LIKE '%".$str."%'"));
			}
		}
		
		
		//#####################################################
		//#####################################################
		//#####################################################
		//#####################################################
		function eliminar($id = null) {
			$this->escribirHeader("GestiÃ³n de Beneficiarios");
				
			if (!$id) {
				$this->Session->setFlash('Id invalido para Beneficiario');
				$this->redirect('/beneficiarios/index_eliminar');
			}

			
			
			$this->Persona->del($id, true);
							
			$this->redirect('/beneficiarios/index_eliminar');	
		}
		
		
		//#####################################################
		//#####################################################
		//#####################################################
		//#####################################################
		function eliminarSeguimiento()
		{
			$id=$this->data['Seguimiento']['num_evento'];
			$ben=$this->data['Beneficiario']['cod_persona'];
			
			$this->Seguimiento->del($id, true);
			$this->Session->setFlash('Se ha eliminado el seguimiento');
			$this->redirect('/beneficiarios/ver/'.$ben);
					
		
		}
		
		function derivarCaso()
		{
			if( isset( $this->data['Caso']['cod_caso'] ) )
			{
				$cod_caso = $this->data['Caso']['cod_caso'];
				
				$reactivo_caso = $this->Caso->query("
				UPDATE `casos` 
				SET `est_caso` = 'Pendiente'
				WHERE `cod_caso` = ".$cod_caso."
				LIMIT 1 ;
				");
				
				if( $reactivo_caso === false )
				{
					$msj = -1001;
					$this->redirect('/beneficiarios/index/'.$msj);
				}
				else
				{
					$msj = 1003;
					$this->redirect('/derivaciones/index/'.$msj);
				}
			}
			else
				$this->redirect('/beneficiarios/');
		}
	}
	
?>
