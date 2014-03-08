<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class RespuestaEncuestasController extends AppController{
    var $name = "RespuestaEncuestas";
    var $helpers = array('Html', 'Form');
	var $uses = array('RespuestaEncuesta','PreguntaEncuesta', 'Encuesta', 'RespuestaMultiple', 'Opcion', 'EncuestaRespondida');

    function index () {
       $this->escribirHeader("Responder Encuesta");

    }


    function mostrar(){
        $this->escribirHeader("Encuestas");
        $this->Encuesta->recursive = 2;
        $this->Encuesta->id = $this->params['url']['id_encuesta'];
        $this->set('encuesta',$this->Encuesta->read());
    }

	function add(){
		$id = $this->params['url']['id_encuesta'];
		$this->Encuesta->recursive = 2;
		$this->Encuesta->id = $id;
		$encuesta = $this->Encuesta->read();
		$respuesta_encuesta=Null;
		$last_id = Null;
		$cod_voluntario = $this->Session->read('cod_voluntario');
		$arreglo = $this->params['form'];
		$num_preguntas = 0;
		$num_respondidas = 0;
		foreach($encuesta["PreguntaEncuesta"] as $pregunta)
		{
			if($pregunta['requerido']==1)
				$num_preguntas++;
			if($pregunta['tipo']==1 || $pregunta['tipo']=='text') //tipo texto
			{
				if($this->params['form']['opcion'.$pregunta['id']]){
					$datosRespuestaEncuesta = array('usuario_id' =>$cod_voluntario,
												'pregunta_encuesta_id'=>$pregunta['id'],
												'fecha'=> date('Y-m-d'),
												'texto'=>$this->params['form']['opcion'.$pregunta['id']] );

					$respuesta_encuesta = $this->RespuestaEncuesta->save($datosRespuestaEncuesta);
					$this->RespuestaEncuesta->create();
					$num_respondidas++;

				}
			}
			elseif($pregunta['tipo']==2 || $pregunta['tipo']=='checkbox') // tipo checkbox
			{
				if(isset($this->params['form']['opcion'.$pregunta['id']])){
					$datosRespuestaEncuesta = array('usuario_id' =>$cod_voluntario,
											'pregunta_encuesta_id'=>$pregunta['id'],
											'fecha'=> date('Y-m-d'));


					$respuesta_encuesta = $this->RespuestaEncuesta->save($datosRespuestaEncuesta);
					$this->RespuestaEncuesta->create();
					$last_id = $this->RespuestaEncuesta->getInsertId();
					$num_respondidas++;
					foreach($this->params['form']['opcion'.$pregunta['id']] as $opcionCheck){


						$datosRespuestaMultiple = array('respuesta_encuesta_id'=>$last_id,
														'opcion_id'=>$opcionCheck);
						$this->RespuestaMultiple->save($datosRespuestaMultiple);
						$this->RespuestaMultiple->create();
					}
				}


			}
			elseif($pregunta['tipo']==3 || $pregunta['tipo']=='radio') // tipo radio
			{
				if(isset($this->params['form']['opcion'.$pregunta['id']])){
					$datosRespuestaEncuesta = array('usuario_id' =>$cod_voluntario,
											'pregunta_encuesta_id'=>$pregunta['id'],
											'fecha'=> date('Y-m-d'));


					$respuesta_encuesta = $this->RespuestaEncuesta->save($datosRespuestaEncuesta);
					$this->RespuestaEncuesta->create();
					$last_id = $this->RespuestaEncuesta->getInsertId();
					$opcion = $this->params['form']['opcion'.$pregunta['id']];
					$datosRespuestaMultiple = array('respuesta_encuesta_id'=>$last_id,
													'opcion_id'=>$opcion);
					$this->RespuestaMultiple->save($datosRespuestaMultiple);
					$this->RespuestaMultiple->create();
					$num_respondidas++;
				}

			}
			elseif($pregunta['tipo']==4 || $pregunta['tipo']=='ranking') // tipo radio
			{
				if(isset($this->params['form']['opcion'.$pregunta['id']])){
					$datosRespuestaEncuesta = array('usuario_id' =>$cod_voluntario,
											'pregunta_encuesta_id'=>$pregunta['id'],
											'fecha'=> date('Y-m-d'));


					$respuesta_encuesta = $this->RespuestaEncuesta->save($datosRespuestaEncuesta);
					$this->RespuestaEncuesta->create();
					$num_respondidas++;
					$last_id = $this->RespuestaEncuesta->getInsertId();
					$i = 0;

					$rankings = $this->params['form']['opcion'.$pregunta['id']];
					foreach($this->params['form']['opcion'.$pregunta['id'].'_v'] as $opcionCheck){


						$datosRespuestaMultiple = array('respuesta_encuesta_id'=>$last_id,
														'opcion_id'=>$opcionCheck,
														'ranking'=>$rankings[$i]);
						$this->RespuestaMultiple->save($datosRespuestaMultiple);
						$this->RespuestaMultiple->create();
						$i++;
					}
				}

			}




		}
		if($num_respondidas >= $num_preguntas){
			$datos = array('encuesta_id'=>$id,
							'usuario_id'=>$cod_voluntario);
			$this->EncuestaRespondida->save($datos);
			$this->EncuestaRespondida->create();
		}
		$this->redirect('/menu');

	}
}
?>