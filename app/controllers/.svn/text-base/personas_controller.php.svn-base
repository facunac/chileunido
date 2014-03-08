<?php
	class PersonasController extends AppController
	{
		var $name = "Persona";
		//Para poder usar otras tablas dentro de este controlador
		var $uses = array("Persona", "Voluntario", "Permisovoluntario", "Programa", "Comuna", "Perfil", "Permisoperfil", "Sesion", "Turno","Seguimiento","Caso","Comentario","Role");
		var $helpers = array('Html', 'Form', 'Time', 'Excel', 'Comentario'); 
		
		function index($exito=null)
		{
			//Se mandan algunas variables al layout para que se vea bien COPIAR SIEMPRE Y CAMBIAR NOMBRE PAGINA
			$this->escribirHeader("Gestión de Cuentas");
			
			$this->set('msg_for_layout',$exito);
			
			//[Ignacio]  Se envian algunas variables a la vista para el uso adecuado de los formularios
			$programas=$this->Programa->getAllAsArray();
			$this->set('programas', $programas);
			$comunas=$this->Comuna->getAllAsArray();
			$this->set('comunas', $comunas);
			
			//[Ignacio] se obtienen los valores posibles de los estados del voluntario como un arreglo
			$estados=$this->Voluntario->getPossibleValues('est_voluntario');
			$this->set('estados', $estados);
			
			//[Ignacio] para el select de atencion clinica
			$si_no=array('0' => 'No', '1' => 'Si');
			$this->set('si_no', $si_no);
			
			
			$roles=$this->Voluntario->getPossibleValues('est_rol');
			$this->set('roles', $roles);	
		}
		
		function crear()
		{
			$this->escribirHeader("Crear Nueva Cuenta");
			
			//[Ignacio]  Se envian algunas variables a la vista para el uso adecuado de los formularios
			$programas=$this->Programa->getAllAsArray();
			$this->set('programas', $programas);
			
			$comunas=$this->Comuna->getAllAsArray(13);
			$this->set('comunas', $comunas);
			
			$regiones=$this->Comuna->getRegiones();
			$this->set('regiones', $regiones);
			
			$perfiles=$this->Perfil->getAllAsArray();
			$this->set('perfiles', $perfiles);
			
			$roles = array('Voluntario', 'Administrativo', 'Voluntario no psicologo');
			$this->set('roles',$roles);
			
			$si_no=array('0' => 'No', '1' => 'Si');
			$this->set('si_no', $si_no);
		}
		
		function crear2()
		{
			$this->escribirHeader("Crear Nueva Cuenta");
			//[Ignacio] se verifica que las dos passwords entregadas sean iguales
			if($this->data['Voluntario']['pas_voluntario']==$this->data['Voluntario']['pas_voluntario2']){
				//[Ignacio] se anexa el codigo verificador
				$this->data['Persona']['nom_rut'].=("-".$this->data['Persona']['num_rutcodver']);
				//[Ignacio] se une el codigo de ciudad y el numero de telefono a los dos telefonos
				$this->data['Persona']['num_telefono1']=$this->data['Persona']['num_telefono1_pre']."-".$this->data['Persona']['num_telefono1_post'];
				$this->data['Persona']['num_telefono2']=$this->data['Persona']['num_telefono2_pre']."-".$this->data['Persona']['num_telefono2_post'];
				
				
				//[Ignacio] guardar la persona
				if($this->Persona->save($this->data['Persona'])){
					//[Ignacio] obtencion del id ingresado en la persona y grabar al voluntario
					$cod_persona=$this->Persona->getLastInsertId();
					$creationData = array( 'nom_comentario' => 'Usuario Ingresado al Sistema',
										'cod_persona' => $cod_persona,
										'cod_creador' => -10000);
					$this->Comentario->save($creationData);
					$this->data['Voluntario']+=array('cod_persona' => $cod_persona);
					//[Ignacio] cambiar password a md5
					$this->data['Voluntario']['pas_voluntario']=md5($this->data['Voluntario']['pas_voluntario']);
					
					$this->Voluntario->save($this->data['Voluntario']);
					
					//[Ignacio] se obtienen los permisos asociados al perfil, y se asignan al voluntario
					$permisos=$this->Permisoperfil->findAllByCodPerfil($this->data['Voluntario']['cod_perfil']);
					foreach($permisos as $v){
						$this->Permisovoluntario->create(array('cod_permiso' => $v['Permisoperfil']['cod_permiso'], 
															'cod_voluntario' => $this->data['Voluntario']['cod_persona'],
															'bit_modifica' => 1));
						$this->Permisovoluntario->save(array('cod_permiso' => $v['Permisoperfil']['cod_permiso'], 
															'cod_voluntario' => $this->data['Voluntario']['cod_persona'],
															'bit_modifica' => 1));
					}
					
					//[Ignacio] inserción de los turnos
					if(isset($this->data['Dia'])){
						foreach($this->data['Dia'] as $i => $v){
							$turno=array('cod_voluntario' => $cod_persona,
									'nom_dia' => $v,
									'hor_inicio' => $this->data['HoraInicio_hora'][$i].":".$this->data['HoraInicio_min'][$i],
									'hor_fin' => $this->data['HoraFin_hora'][$i].":".$this->data['HoraFin_min'][$i],
									'bit_clinico' => $this->data['BitClinico'][$i]);
							$this->Turno->create($turno);
							$this->Turno->save($turno);
						}
					}
					$msg=22; //[Ignacio] exitoso
				}
				else {
					$msg=23; //[Ignacio] fallido
				}
			}
			else {
				$msg=23; //[Ignacio] fallido
			}
			$this->redirect('/personas/index/'.$msg);
			exit();
		}
		
		function buscar()
		{
			$this->escribirHeader("Búsqueda de Cuentas");
			
			$nom_rut=$this->data['FormBuscar']['nom_rut']."-".$this->data['FormBuscar']['num_rutcodver'];
			$nom_nombre=$this->data['FormBuscar']['nom_nombre'];
			$nom_appat=$this->data['FormBuscar']['nom_appat'];
			$nom_apmat=$this->data['FormBuscar']['nom_apmat'];
			
			if(isset($this->data['FormBuscar']['cod_programa']))
				$cod_programa=$this->data['FormBuscar']['cod_programa'];
			else $cod_programa="";
			if(isset($this->data['FormBuscar']['est_voluntario']))
				$est_voluntario=$this->data['FormBuscar']['est_voluntario'];
			else $est_voluntario="";
			if(isset($this->data['FormBuscar']['bit_clinico']))
				$bit_clinico=$this->data['FormBuscar']['bit_clinico'];
			else $bit_clinico="";
			if(isset($this->data['FormBuscar']['est_rol']))
				$est_rol=$this->data['FormBuscar']['est_rol'];
			else $est_rol="";


			//[Ignacio] si el estado es no vacío, se fitra, sino, no
			$filtroestado=$est_voluntario?array("Voluntario.est_voluntario"=>$est_voluntario):array();
			//[Ignacio] si el programa es no vacío, se filtra, sino, no
			$filtroprograma=$cod_programa?array("Voluntario.cod_programa"=>$cod_programa):array();
			//[Ignacio] si el programa es no vacío, se filtra, sino, no
			$filtroclinico=($bit_clinico!="")?array("Voluntario.bit_clinico"=>$bit_clinico):array();
			
			// lo mismo para rol
			$filtrorol=($est_rol!="")?array("Voluntario.est_rol"=>$est_rol):array();
			
			$personas=$this->Voluntario->findAll(array("Persona.nom_rut" => "like %$nom_rut%", 
										"Persona.nom_nombre" => "like %$nom_nombre%", 
										"Persona.nom_appat" => "like %$nom_appat%",
										"Persona.nom_apmat" => "like %$nom_apmat%")+
										$filtroestado+$filtroprograma+$filtroclinico+$filtrorol, "",
										array("Persona.nom_nombre" => "asc",
										"Persona.nom_appat" => "asc",
										"Persona.nom_apmat" => "asc"));
			
			if(count($personas)==0)
			{
				$mensaje="No existen resultados para los criterios ingresados";
				$this->set("mensaje", $mensaje);
			}
			$this->set('personas', $personas);
			
			$comunas=$this->Comuna->getAllAsArray();
			$this->set('comunas', $comunas);
		}
		
		
		
		function modificar()
		{
			$this->escribirHeader("Modificar Cuenta");
			
			//[Ignacio]  Se envian algunas variables a la vista para el uso adecuado de los formularios
			$programas=$this->Programa->getAllAsArray();
			$this->set('programas', $programas);
			
			$comunas=$this->Comuna->getAllAsArray();
			$this->set('comunas', $comunas);
			
			$perfiles=$this->Perfil->getAllAsArray();
			$this->set('perfiles', $perfiles);
			
			$estados=$this->Voluntario->getPossibleValues('est_voluntario');
			$this->set('estados', $estados);
			
			$roles = array('Voluntario', 'Administrativo', 'Voluntario no psicologo');
			$this->set('roles',$roles);
			
			$si_no=array('0' => 'No', '1' => 'Si');
			$this->set('si_no', $si_no);
			
			//[Ignacio] datos de la persona a modificar
			$cod_persona=$this->data['Persona']['cod_persona'];
			$this->set('cod_persona', $cod_persona);
			//[Ignacio] datos del volunario que modifica
			$cod_voluntario=$this->Session->read('cod_voluntario');
			$this->set('cod_voluntario', $cod_voluntario);
			
			//[Ignacio] se obtienen los datos de la cuenta
			$voluntario=$this->Voluntario->find(array("Persona.cod_persona"=>$cod_persona));
			$voluntario['Persona']['fec_nacimiento']=$this->Persona->toDate($voluntario['Persona']['fec_nacimiento']);
			//[Ignacio] separacion del rut
			$ruts=explode("-",$voluntario['Persona']['nom_rut']);
			$voluntario['Persona']['nom_rut']=$ruts[0];
			$voluntario['Persona']['num_rutcodver']=$ruts[1]?$ruts[1]:"";
			//[Ignacio] separacion de los telefonos
			$tel1=explode("-",$voluntario['Persona']['num_telefono1']);
			$voluntario['Persona']['num_telefono1']=(isset($tel1[1]))?$tel1[1]:$tel1[0];
			$voluntario['Persona']['num_telefono1_pre']=(isset($tel1[1]))?$tel1[0]:"";
			$tel2=explode("-",$voluntario['Persona']['num_telefono2']);
			$voluntario['Persona']['num_telefono2']=(isset($tel2[1]))?$tel2[1]:$tel2[0];
			$voluntario['Persona']['num_telefono2_pre']=(isset($tel2[1]))?$tel2[0]:"";
			
			$this->set('persona', $voluntario);
			//[Ignacio] de aca se sacan los permisos
			$this->set('voluntario', $this->Voluntario->find(array("Voluntario.cod_persona"=>$cod_persona)));
			
					
		}
		function modificar2(){
			$this->escribirHeader("Modificar Cuenta");
			
			$error=false;
			$error=$error || ($this->data['Voluntario']['pas_voluntario1']!=$this->data['Voluntario']['pas_voluntario2']);
			$cod_persona=$this->data['Persona']['cod_persona'];
			
			//[Ignacio] si se pide la password anterior y se quiere cambiar la password, debe ser la correcta
			if(isset($this->data['Voluntario']['pas_voluntario3']) && $this->data['Voluntario']['pas_voluntario1']!="") {
				$pass=$this->Voluntario->findByCodPersona($cod_persona);
				$error=$error || (md5($this->data['Voluntario']['pas_voluntario3'])!=$pass['Voluntario']['pas_voluntario']);
			}
			
			//[Ignacio] si hay error no se hace nada
			if(!$error){
				//[Ignacio] union del rut y digito verificador
				$this->data['Persona']['nom_rut'].=("-".$this->data['Persona']['num_rutcodver']);
				//[Ignacio] se une el codigo de ciudad y el numero de telefono a los dos telefonos
				$this->data['Persona']['num_telefono1']=$this->data['Persona']['num_telefono1_pre']."-".$this->data['Persona']['num_telefono1_post'];
				$this->data['Persona']['num_telefono2']=$this->data['Persona']['num_telefono2_pre']."-".$this->data['Persona']['num_telefono2_post'];
				//print_r($this->data['Persona']);
				if($this->Persona->save($this->data['Persona'])){

					//[Ignacio] encripta la password
					if($this->data['Voluntario']['pas_voluntario1']!="")
						$this->data['Voluntario']['pas_voluntario']=md5($this->data['Voluntario']['pas_voluntario1']);
					
					//[RAFA] se buscan los datos del voluntario antes de ser modificados
					$voluntario = $this->Voluntario->find(array("Persona.cod_persona"=>$this->data['Persona']['cod_persona']));
						
					//[RAFA] si se modifica el programa o el estado del voluntario se buscan los seguimientos que ese voluntario debe realizar y se dejan abiertos para cualquier voluntario y los casos asociados a ese voluntario se les quita el solo yo
					if($voluntario['Voluntario']['cod_programa']!=$this->data['Voluntario']['cod_programa']||$voluntario['Voluntario']['est_voluntario']!=$this->data['Voluntario']['est_voluntario'])
					{
						$seguimientos=$this->Seguimiento->findAll(array("Seguimiento.cod_voluntarioproxrevision" => $voluntario['Voluntario']['cod_persona']));
						
						foreach ($seguimientos as $seguimiento){
							$seguimiento['Seguimiento']['cod_voluntarioproxrevision']= NULL;
							$timestamp = explode("-", $seguimiento['Seguimiento']['fec_proxrevision']);
							$dia = $timestamp[2];
							$mes = $timestamp[1];
							$year = $timestamp[0];
			   				$seguimiento['Seguimiento']['fec_proxrevision']=$dia."-".$mes."-".$year;
							
							$this->Seguimiento->save($seguimiento['Seguimiento']);
						}
						
						$casos=$this->Caso->findall(array("Caso.cod_voluntario"=> $voluntario['Voluntario']['cod_persona']));
						
						foreach ($casos as $caso){
							$caso['Caso']['bit_soloyo']=0;
							$this->Caso->save($caso['Caso']);
							
						}
						
						
					}
						
						
						
					//[Ignacio] guardar los datos del voluntario
					$this->data['Voluntario']['cod_persona']=$this->data['Persona']['cod_persona'];
					if($this->Voluntario->save($this->data['Voluntario'])){
						$msg=22;
					}
					else $msg=23;
				}
				else $msg=23;
			}
			else $msg=23;
			
				
			$this->redirect('/personas/ver/'.$cod_persona."/".$msg);
			exit();
			
			
		}
		function excel(){
			
			$personas = unserialize($this->data['Excel']['Hoja']);
			$this->set('personas',$personas);
			$this->set('type','cuentas');
			$this->render('excel', 'excel'); 
		}
		
		function ver_libro($mes,$agno)
		{
			$this->escribirHeader("Libro de Asistencia");
			
			//[Ignacio] en el servidor de explotación se debe cambiar el idioma a español, dependiendo de su configuracion. Si es Linux, se puede ejecutar locale -a para obtener el listado de idiomas disponibles
			setlocale(LC_TIME,'es-ES');

			//[Ignacio]  Obtenemos el código del voluntario a modificar
			$cod_persona=$this->data['Voluntario']['cod_persona'];
			
			//[Ignacio] Obtenemos las sesiones del voluntario
			$sesiones=$this->Sesion->findAll(array("Sesion.cod_voluntario"=>$cod_persona, "MONTH(fec_inicio)"=>$mes, "YEAR(fec_inicio)"=>$agno));
			
			//[Ignacio] primer día del mes
			$iniciodemes=mktime(null, null, null, $mes, 1, $agno);
			//[Ignacio] el día de la semana (lunes=0... domingo=6)
			$diadelasemana=date('N', $iniciodemes)-1;
			//[Ignacio] la fecha de la primera posición del calendario
			$ahora=mktime(null,null,null,$mes,1-$diadelasemana,$agno);
			
			//[Ignacio] numero de dias al principio que no son del mes (para pintarlas de otro color)
			$pintar=$diadelasemana;
			
			//[Ignacio] llenado de las variables dia (numero de dia que tiene el calendario), y calendario como un arreglo de 6x7 vacío
			$calendario=array();
			$dia=array();
			$colores=array();
			
			//[Ignacio] variables para pintar el calendario
			$termino_de_mes=0;
			$dia_anterior=$dia;
			$pintar="td_gris_dia_no_mes";
			
			//[Ignacio] mes anterior (aprovechando que si uno pone 0 en dia si pone en el último día del mes anterior)
			$dias_mes_anterior=date("d", mktime(0, 0, 0, $mes, 0, $agno));
			//[Ignacio] si es que no muestra dias del mes anterior (o sea, el primer dia del mes es Lunes)
			if($diadelasemana==0){
				$dias_mes_anterior=1;
				$pintar="td_gris_dia";
				}
			//[Ignacio] dias en este mes
			$dias_este_mes=date("d", mktime(0, 0, 0, $mes + 1, 0, $agno));
			
			for($i=0; $i<6; $i++){
				$calendario+=array($i => array());
				$dia+=array($i => array());
				$colores+=array($i => array());
				for($j=0; $j<7; $j++){
					$dia[$i]+=array($j => date('d', $ahora));
					$ahora=mktime(null,null,null,date('m', $ahora),date('d', $ahora)+1,date('Y', $ahora));
					$calendario[$i]+=array($j => "");
					if($dia_anterior==$dias_mes_anterior && $termino_de_mes==0){
						$termino_de_mes=$termino_de_mes+1;
						$pintar="td_gris_dia";
						}
					else if($dia_anterior==$dias_este_mes && $termino_de_mes==1){
						$termino_de_mes=$termino_de_mes+1;
						$pintar="td_gris_dia_no_mes";
						}
					$colores[$i]+=array($j =>$pintar);
					$dia_anterior=$dia[$i][$j];
					}
					$dia_anterior=$dia[$i][6];
			}
			$termino_de_mes=0;
			
			foreach($sesiones as $i => $v){
				//[Ignacio] obtengo la hora de inicio
				$ini=explode(" ", $sesiones[$i]['Sesion']['fec_inicio']);
				$hora_temp=explode(":", $ini[1]);
				$sesiones[$i]['Sesion']['hora_inicio']=date('h:i', mktime($hora_temp[0],$hora_temp[1]));
				//[Ignacio] obtengo el día inicial (porque ya se el mes y el año)
				$ini2=explode("-", $ini[0]);
				$sesiones[$i]['Sesion']['dia_inicio']=$ini2[2];
				//[Ignacio] obtengo la hora de fin
				$fin=explode(" ", $sesiones[$i]['Sesion']['fec_fin']);
				$hora_temp=explode(":", $fin[1]);
				$sesiones[$i]['Sesion']['hora_fin']=date('h:i', mktime($hora_temp[0],$hora_temp[1]));
				//[Ignacio] obtengo el día final (porque ya se el mes y el año)
				$fin2=explode("-", $fin[0]);
				$sesiones[$i]['Sesion']['dia_fin']=$fin2[2];
				
				//[Ignacio] si el día de término es distinto al de inicio, lo dejamos como indefinido
				if($sesiones[$i]['Sesion']['dia_inicio']!=$sesiones[$i]['Sesion']['dia_fin'])
					$sesiones[$i]['Sesion']['hora_fin']='>';
				
				$fecha=mktime(null, null, null, $mes, $sesiones[$i]['Sesion']['dia_inicio'], $agno);
				
				//[Ignacio] 0=lunes... 6=domingo
				$diadelasemana=date('N', $fecha)-1;
				//[Ignacio] el numero de semana (del año) de la fecha menos el numero de semana del primer dia del mes
				$numerodesemana=date('W', $fecha)-date('W', $iniciodemes);
				
				if($calendario[$numerodesemana][$diadelasemana] != "") $calendario[$numerodesemana][$diadelasemana].="<br />";
				$calendario[$numerodesemana][$diadelasemana].=$sesiones[$i]['Sesion']['hora_inicio']."-".$sesiones[$i]['Sesion']['hora_fin'];
			}
			$fecha_mes_sigte=mktime(null, null, null, $mes+1, 1, $agno);
			$fecha_mes_ant=mktime(null, null, null, $mes-1, 1, $agno);
			$mes_sigte=date('m', $fecha_mes_sigte);
			$agno_sigte=date('Y', $fecha_mes_sigte);
			$mes_ant=date('m', $fecha_mes_ant);
			$agno_ant=date('Y', $fecha_mes_ant);
			
			
			$this->set('cod_persona', $cod_persona);
			$this->set('calendario', $calendario);
			$this->set('dia', $dia);
			$this->set('colores', $colores);
			$this->set('mes_sigte', $mes_sigte);
			$this->set('agno_sigte', $agno_sigte);
			$this->set('mes_ant', $mes_ant);
			$this->set('agno_ant', $agno_ant);
			$this->set('nom_mes', date('F', $iniciodemes));
			$this->set('nom_mes_ant', date('F', $fecha_mes_ant));
			$this->set('nom_mes_sigte', date('F', $fecha_mes_sigte));
		}
		function ver($cod_persona, $msg=null)
		{
			$this->escribirHeader("Modificar Cuenta");
			
			$this->set('msg_for_layout',$msg);
			
			//[Ignacio] se obtienen los datos de la cuenta
			$voluntario=$this->Persona->find(array("Persona.cod_persona"=>$cod_persona));
			$voluntario['Persona']['fec_nacimiento']=$this->Persona->toDate($voluntario['Persona']['fec_nacimiento']);
			if($voluntario['Voluntario']['bit_clinico']=="1") $voluntario['Voluntario']['bit_clinico']="checked";
			else $voluntario['Voluntario']['bit_clinico']="";
			$this->set('persona', $voluntario);
			//[Ignacio] de aca se sacan los permisos
			$this->set('voluntario', $this->Voluntario->find(array("Voluntario.cod_persona"=>$cod_persona)));
			
			$si_no=array('0' => 'No', '1' => 'Si');
			$this->set('si_no', $si_no);
		}
		function unique($modelo, $atributo){
			$valor=$this->data[$modelo][$atributo];
			$fila=$this->{$modelo}->find(array($modelo.".".$atributo => $valor));
			if(is_array($fila))
				$this->flash("El valor ya existe","");
			else $this->flash("El valor es v&aacute;lido","");
		}
		function agregar_comentario()
		{
			
		}
	}
	
?>
