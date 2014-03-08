<?php
	class PermisovoluntariosController extends AppController
	{
		var $name = "Permisovoluntarios";
		var $uses = array("Permiso", "Permisovoluntario", "Voluntario", "Persona");
		
		function index()
		{
			// Se mandan algunas variables al layout para que se vea bien
			$this->escribirHeader("Ver Permisos");
			
			//traspaso del codigo de voluntario
			$cod_persona=$this->data['Voluntario']['cod_persona'];
			$this->set('cod_persona', $cod_persona);
			$persona=$this->Persona->findByCodPersona($cod_persona);
			$this->set('nom_voluntario', $persona['Persona']['nom_nombre']." ".$persona['Persona']['nom_appat']." ".$persona['Persona']['nom_apmat']);
			//obtención del listado de todos los permisos (marcando los del voluntario)
			$permisos=$this->Permiso->getAllAsArray();
			$this->set('permisos', $permisos);
			$permisovoluntario=$this->Permisovoluntario->findAllByCodVoluntario($cod_persona);
			//aca pongo una marca en los permisos que tenga el voluntario
			$permisovoluntarios=array();
			$bit_modifica=array();
			
			foreach($permisovoluntario as $v){
				$permisovoluntarios+=array($v['Permisovoluntario']['cod_permiso'] => "1");
				$bit_modifica+=array($v['Permisovoluntario']['cod_permiso'] => $v['Permisovoluntario']['bit_modifica']);
			}
			//aca le pongo una marca negativa a los que no
			foreach($permisos as $i => $v){
					if(!isset($permisovoluntarios[$i])){
						$permisovoluntarios+=array($i => "0");
						$bit_modifica+=array($i => "0");
					}
			}
			$this->set('permisovoluntarios', $permisovoluntarios);
			$this->set('bit_modifica', $bit_modifica);
			
			$this->set('opc_permisos', array("0" => "Denegar", "1" => "Permitir"));
			$this->set('opc_modifica', array("0" => "Solo lectura", "1" => "Modifica"));
		}

		function modificar(){
						// Se mandan algunas variables al layout para que se vea bien
			$this->escribirHeader("Modificar Permisos");
			
			//traspaso del codigo de voluntario
			$cod_persona=$this->data['Voluntario']['cod_persona'];
			$this->set('cod_persona', $cod_persona);
			$persona=$this->Persona->findByCodPersona($cod_persona);
			$this->set('nom_voluntario', $persona['Persona']['nom_nombre']." ".$persona['Persona']['nom_appat']." ".$persona['Persona']['nom_apmat']);
			//obtención del listado de todos los permisos (marcando los del voluntario)
			$permisos=$this->Permiso->getAllAsArray();
			$this->set('permisos', $permisos);
			$permisovoluntario=$this->Permisovoluntario->findAllByCodVoluntario($cod_persona);
			//aca pongo una marca en los permisos que tenga el voluntario
			$permisovoluntarios=array();
			$bit_modifica=array();
			
			foreach($permisovoluntario as $v){
				$permisovoluntarios+=array($v['Permisovoluntario']['cod_permiso'] => "1");
				$bit_modifica+=array($v['Permisovoluntario']['cod_permiso'] => $v['Permisovoluntario']['bit_modifica']);
			}
			//aca le pongo una marca negativa a los que no
			foreach($permisos as $i => $v){
					if(!isset($permisovoluntarios[$i])){
						$permisovoluntarios+=array($i => "0");
						$bit_modifica+=array($i => "0");
					}
			}
			$this->set('permisovoluntarios', $permisovoluntarios);
			$this->set('bit_modifica', $bit_modifica);
			
			$this->set('opc_permisos', array("0" => "Denegar", "1" => "Permitir"));
			$this->set('opc_modifica', array("0" => "Solo lectura", "1" => "Modifica"));
		}
		
		function modificar2(){
			// Se mandan algunas variables al layout para que se vea bien
			$this->escribirHeader("Modificar Permisos");
			
			//flag que debe continuar en true hasta el final para dar por exitosa la modificacion
			$exito=true;
			
			//codigo de persona para los filtros
			$cod_persona=$this->data['Voluntario']['cod_persona'];
			
			//se hace un loop para cada select
			foreach($this->data['FormPermiso'] as $i => $v){
				//se revisa si antes existía el permiso
				$res=$this->Permisovoluntario->find("Permisovoluntario.cod_voluntario=$cod_persona and Permisovoluntario.cod_permiso=$i");
				switch($v){
					//caso en el que el permiso debe estar
					case "1":
						//si el permiso no estaba asignado, se agrega
						if(!isset($res['Permisovoluntario'])) {
							$exito=$exito&&$this->Permisovoluntario->save(array('cod_voluntario' => $cod_persona, 'cod_permiso' => $i, 'bit_modifica' => $this->data['FormModifica'][$i]));
							//para evitar que se reemplace en vez de agregar uno nuevo
							$this->Permisovoluntario->id=null;
						}
						//si estaba, puede haber cambiado el bit_modifica, se actualiza
						else {
							$exito=$exito&&$this->Permisovoluntario->save(
									array('cod_permisovoluntario' => $res['Permisovoluntario']['cod_permisovoluntario'],
										'cod_voluntario' => $cod_persona,
										'cod_permiso' => $i,
										'bit_modifica' => $this->data['FormModifica'][$i]));
							//para evitar que se reemplace en vez de agregar uno nuevo
							$this->Permisovoluntario->id=null;
						}
					break;
					//caso en el que el permiso no debe estar
					case "0":
						//si el permiso estaba asignado, se elimina
						if(isset($res['Permisovoluntario'])) {
							$exito=$exito&&$this->Permisovoluntario->del($res['Permisovoluntario']['cod_permisovoluntario']);
						}
					break;
				}
			}
			$msg=$exito?"22":"23";
			$this->redirect('/personas/index/'.$msg);
			exit();
		}
	}
?>