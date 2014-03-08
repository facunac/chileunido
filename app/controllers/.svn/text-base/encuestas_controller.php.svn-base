<?php

class EncuestasController extends AppController{
	var $name = "Encuestas";
	var $helpers = array('Html', 'Form');
	var $uses = array('Encuesta', 'EncuestaRespondida', 'PreguntaEncuesta', 'RespuestaEncuesta', 'RespuestaMultiple', 'Persona');

	function mostrarInicio() {
		$output = "";
		$cod_voluntario = $this->Session->read('cod_voluntario');

		$output.='<h3>Encuestas</h3><table class="encuestaInicio"><th>Encuesta</th><th>Inicio</th><th>T&eacute;rmino</th><th>An&oacute;nima</th>';
		$fecha = date("Y-m-d");
		$busqueda = array("habilitada = 1 and fecha_inicio <= '$fecha' and fecha_fin >= '$fecha'", "", "", 0, 0, 1);
		$encuestas = $this->Encuesta->findAll($busqueda);
		$hay_encuestas_por_responder = false;
		foreach ($encuestas as $encuesta){
			$encuesta_respondida = $this->EncuestaRespondida->findAll(array("usuario_id=$cod_voluntario", "encuesta_id=".$encuesta['Encuesta']['id']), array("encuesta_id"));
			if(count($encuesta_respondida)==0){
				$hay_encuestas_por_responder = true;
				$output .= '<tr>
					<td><a href="/respuestaencuestas/mostrar?id_encuesta='.$encuesta['Encuesta']['id'].'" >'.$encuesta['Encuesta']['titulo'].'</a>
					</td>
					<td>'.$encuesta['Encuesta']['fecha_inicio'].'
					</td>
					<td>'.$encuesta['Encuesta']['fecha_fin'].'
					</td>
					<td>';
				$output .= $encuesta['Encuesta']['anonima']==1?'Sí':'No';
				$output .= '</td>
				</tr>';
			}
		}

		if($hay_encuestas_por_responder)
			$output.='</table>';
		else
			$output = '<h3>Encuestas</h3><p>No hay encuestas que mostrar.</p>';
		$this->set('output',$output);
	}

	function index () {
		$this->escribirHeader("Encuestas");
		$this->Encuesta->recursive = 0;
		$encuestas[] = array('tipo'=>'Activas','encuestas'=>$this->mostrar_para_responder());
		$encuestas[] = array('tipo'=>'Antiguas','encuestas'=>$this->encuestasPasadas());
		$encuestas[] = array('tipo'=>'Futuras','encuestas'=>$this->encuestasFuturas());
		$encuestas[] = array('tipo'=>'No Habilitadas','encuestas'=>$this->encuestasNoHabilitadas());
		$this->set('todasEncuestas', $encuestas);
	}

	function add(){
		$this->escribirHeader("Encuestas");
		if(!empty($this->data)){
			$encuesta = $this->Encuesta->save($this->data);
			if($encuesta){
				$last_id = $this->Encuesta->getInsertId();
				$this->Session->setFlash('La encuesta ha sido creada');
				$this->redirect('preguntaencuestas/add?id_encuesta='.$last_id.'&n=1');
				exit();
			}
		}
	}

	function deshabilitar(){
		$this->escribirHeader("Encuestas");
		$id = $this->params['url']['id'];
		$this->Encuesta->id = $id;
		$encuesta = $this->Encuesta->read();
		$datosACambiar = array('habilitada'=>'0');
		$this->Encuesta->Save($datosACambiar);
		$this->redirect('encuestas/index');
		exit();
	}

	function habilitar(){
		$this->escribirHeader("Encuestas");
		$id = $this->params['url']['id'];
		$this->Encuesta->id = $id;
		$encuesta = $this->Encuesta->read();
		$datosACambiar = array('habilitada'=>'1');
		$this->Encuesta->Save($datosACambiar);
		$this->redirect('encuestas/index');
		exit();
	}

	function eliminar($id){
		$this->escribirHeader("Encuestas");
		$this->Encuesta->del($id, true);
		$this->Session->setFlash("La encuesta $id ha sido borrada.");
		$this->redirect('encuestas/index');
		exit();
	}

	function mostrar_para_responder(){
		$this->escribirHeader("Encuestas");
		$fecha = date("Y-m-d");
		$busqueda = array("habilitada = 1 and fecha_inicio <= '$fecha' and fecha_fin >= '$fecha'", "", "", 0, 0, 1);
		$encuestas = $this->Encuesta->findAll($busqueda);
		$this->set("encuestas", $encuestas);
		return $encuestas;
	}

	function encuestasNoHabilitadas(){
		$fecha = date("Y-m-d");
		$busqueda = array("habilitada = 0", "", "", 0, 0, 1);
		$encuestas = $this->Encuesta->findAll($busqueda);
		return $encuestas;
	}

	function encuestasPasadas(){
		$fecha = date("Y-m-d");
		$busqueda = array("habilitada = 1 and fecha_fin < '$fecha'", "", "", 0, 0, 1);
		$encuestas = $this->Encuesta->findAll($busqueda);
		return $encuestas;
	}

	function encuestasFuturas(){
		$fecha = date("Y-m-d");
		$busqueda = array("habilitada = 1 and fecha_inicio > '$fecha'", "", "", 0, 0, 1);
		$encuestas = $this->Encuesta->findAll($busqueda);
		return $encuestas;
	}

	function editar(){
		$this->escribirHeader("Encuestas");
		$this->Encuesta->recursive = 2;
		$this->Encuesta->id = $this->params['url']['id'];
		$this->set('encuesta',$this->Encuesta->read());
	}

	function mostrar(){
		$this->escribirHeader("Encuestas");
		$this->Encuesta->recursive = 2;
		$this->Encuesta->id = $this->params['url']['id'];
		$this->set('encuesta',$this->Encuesta->read());
	}

	function edit(){
		$this->escribirHeader("Encuestas");
	}

	function seleccionar_resultados() {
		$this->escribirHeader("Encuestas");
		$fecha = date("Y-m-d");
		$busqueda = array("", "", "", 0, 0, 1);
		$encuestas = $this->Encuesta->findAll($busqueda);
		$this->set("encuestas", $encuestas);
	}

	function mostrar_resultados(){
		$this->escribirHeader("Resultados encuesta");

		$encuestas_respondidas = $this->EncuestaRespondida->findAll(array("encuesta_id='".(isset($id_encuesta)?$id_encuesta:$this->params['url']['id_encuesta'])."'"), array("usuario_id", "Encuesta.titulo", "Encuesta.anonima"));

		$preguntas = $this->PreguntaEncuesta->findAll(array("encuesta_id='".$this->params['url']['id_encuesta']."'"), array("Preguntaencuesta.titulo", "Preguntaencuesta.tipo"));

		$respuestas = array();
		$resultados = array();
		$resumen = array();
		$i = 0;
		$j = 0;
		foreach($encuestas_respondidas as $e){
			$this->Persona->recursive = 0;
			$persona = $this->Persona->findAll(array("Persona.cod_persona=".$e['EncuestaRespondida']['usuario_id']), array("nom_nombre", "nom_appat", "nom_apmat"));
			foreach($preguntas as $p){
				$respuestas[] = $this->RespuestaEncuesta->findAll(array("pregunta_encuesta_id='".$p['Preguntaencuesta']['id']."' AND usuario_id='".$e['EncuestaRespondida']['usuario_id']."'"), array("texto", "pregunta_encuesta_id", 'id'));
				$respuesta_multiple = $this->RespuestaMultiple->findAll(array("respuesta_encuesta_id='".$respuestas[$j][0]['RespuestaEncuesta']['id']."'"), array('Opcion.titulo', 'RespuestaMultiple.ranking'));

				$resp = $respuestas[$j][0]['RespuestaEncuesta']['texto'];
				if($resp == NULL) {
					if($i==0)
						foreach($p['Opcion'] as $opcion){
							$resumen[$p['Preguntaencuesta']['titulo']][$opcion['titulo']] = 0;
							$resumen[$p['Preguntaencuesta']['titulo']]['tipo_de_la_pregunta_42'] = $p['Preguntaencuesta']['tipo'];
						}
					$resp = '';
					if($p['Preguntaencuesta']['tipo']=='ranking'){
						$ranking=array();
						foreach ($respuesta_multiple as $key => $row) {
							$ranking[$key] = $row["RespuestaMultiple"]['ranking'];
						}
						array_multisort($ranking, SORT_ASC, $respuesta_multiple);
						$resp = '<ol>';
						$k = 1;
						foreach($respuesta_multiple as $rm){
							$resp .= '<li>' . $rm["Opcion"]['titulo'] . '</li>';
							$resumen[$p['Preguntaencuesta']['titulo']][$rm["Opcion"]['titulo']]+=$k;
							++$k;
						}
						$resp .= '</ol>';
					} else {
						foreach($respuesta_multiple as $rm)
						{
							$resp .= $rm["Opcion"]['titulo'] . '<br />';
							$resumen[$p['Preguntaencuesta']['titulo']][$rm["Opcion"]['titulo']]++;
						}
					}
				}

				if($e['Encuesta']['anonima'])
					$resultados['Usuario '.($i+1)][] = array($p['Preguntaencuesta']['titulo'], $resp);
				else
					$resultados[$persona[0]["Persona"]['nom_nombre'].' '.$persona[0]["Persona"]['nom_appat'].' '.$persona[0]["Persona"]['nom_apmat']][] = array($p['Preguntaencuesta']['titulo'], $resp);

				++$j;
			}
			++$i;
		}
		$this->set("resultados", $resultados);
		$this->set("resumen", $resumen);
	}
}
?>
