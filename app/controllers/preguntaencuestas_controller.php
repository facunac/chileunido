<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class PreguntaEncuestasController extends AppController{
    var $name = "PreguntaEncuestas";
    var $helpers = array('Html', 'Form');
    var $uses = array('PreguntaEncuesta','Opcion', 'Encuesta');

    function index () {
        $this->escribirHeader("Preguntaencuestas");
    }

    function add(){
        $this->escribirHeader("Nueva Pregunta");		

        if(!empty($this->data)){
            $datosPregunta = array('encuesta_id'=>$this->data['PreguntaEncuesta']['id_encuesta'],
                            'numero'=>$this->data['PreguntaEncuesta']['n'],
                            'titulo'=>$this->data['PreguntaEncuesta']['titulo'],
                            'tipo'=>$this->data['PreguntaEncuesta']['tipo'],
                            'requerido'=>$this->data['PreguntaEncuesta']['requerido']
                            );
            $pregunta = $this->PreguntaEncuesta->save($datosPregunta);
			$last_id = $this->PreguntaEncuesta->getInsertId();
			
			if($this->data['PreguntaEncuesta']['tipo'] != "text"){
				for($i=1;$i<=$this->data['PreguntaEncuesta']['numOpciones']; $i++){
					$datos = array('pregunta_id'=>$last_id, 'titulo'=>$this->params['form']['opcion'.$i.'text']);
					$this->Opcion->create();
					$this->Opcion->save($datos);
				}
			}
			
			if($this->data['PreguntaEncuesta']['habilitada'] == 1){
				$this->Encuesta->id = $this->data['PreguntaEncuesta']['id_encuesta'];
				$encuesta = $this->Encuesta->read();
				$dataEncuesta = array('habilitada'=>'1');
				$this->Encuesta->save($dataEncuesta);
				$this->Session->setFlash('La encuesta ha sido creada');
				$this->redirect('encuestas');
				exit();
			}
            $this->Session->setFlash('La pregunta ha sido creada');
            $this->redirect('preguntaencuestas/add?id_encuesta='.$this->data['PreguntaEncuesta']['id_encuesta'].'&n='.($this->data['PreguntaEncuesta']['n']+1));
			
        }
    }
	
	function addedit(){
        $this->escribirHeader("Nueva Pregunta");		

        if(!empty($this->data)){
            $datosPregunta = array('encuesta_id'=>$this->data['PreguntaEncuesta']['id_encuesta'],
                            'numero'=>$this->data['PreguntaEncuesta']['n'],
                            'titulo'=>$this->data['PreguntaEncuesta']['titulo'],
                            'tipo'=>$this->data['PreguntaEncuesta']['tipo'],
                            'requerido'=>$this->data['PreguntaEncuesta']['requerido']
                            );
            $pregunta = $this->PreguntaEncuesta->save($datosPregunta);
			$last_id = $this->PreguntaEncuesta->getInsertId();
			
			if($this->data['PreguntaEncuesta']['tipo'] != "text"){
				for($i=1;$i<=$this->data['PreguntaEncuesta']['numOpciones']; $i++){
					$datos = array('pregunta_id'=>$last_id, 'titulo'=>$this->params['form']['opcion'.$i.'text']);
					$this->Opcion->create();
					$this->Opcion->save($datos);
				}
			}
			
            $this->Session->setFlash('La pregunta ha sido creada');
            $this->redirect('encuestas/editar?id='.$this->data['PreguntaEncuesta']['id_encuesta']);
			
        }
    }

    function bajarPregunta(){
        $this->escribirHeader("Encuestas");
        $idPregunta= $this->params['url']['idPregunta'];
        //obtengo pregunta a bajar
        $params = array('conditions' => array('Preguntaencuesta.id' => $idPregunta));
	$preguntaArriba = $this->PreguntaEncuesta->find($params);
        $idEncuesta = $preguntaArriba['Encuesta']['id'];

        //busco la pregunta que viene a continuacion y la subo
        $params = array('conditions' => array('Preguntaencuesta.encuesta_id' => $preguntaArriba['Preguntaencuesta']['encuesta_id'],
            'Preguntaencuesta.numero' => $preguntaArriba['Preguntaencuesta']['numero']+1));
	$preguntaAbajo = $this->PreguntaEncuesta->find($params);

        $datosPreguntaAbajo = array('numero'=>$preguntaAbajo['Preguntaencuesta']['numero']-1);
        $this->PreguntaEncuesta->id=$preguntaAbajo['Preguntaencuesta']['id'];
        $preguntaAbajo = $this->PreguntaEncuesta->Save($datosPreguntaAbajo);

        //Guardo pregunta que queria bajar
        $datosPreguntaArriba = array('numero'=>$preguntaArriba['Preguntaencuesta']['numero']+1);
        $this->PreguntaEncuesta->id=$preguntaArriba['Preguntaencuesta']['id'];
        $this->PreguntaEncuesta->Save($datosPreguntaArriba);

        $this->redirect('encuestas/editar?id='.$idEncuesta);
        exit();
    }

    function subirPregunta(){
        $this->escribirHeader("Encuestas");
        $idPregunta= $this->params['url']['idPregunta'];
        //obtengo pregunta a subir
        $params = array('conditions' => array('Preguntaencuesta.id' => $idPregunta));
	$preguntaArriba = $this->PreguntaEncuesta->find($params);
        $idEncuesta = $preguntaArriba['Encuesta']['id'];

        //busco la pregunta que viene antes y la bajo
        $params = array('conditions' => array('Preguntaencuesta.encuesta_id' => $preguntaArriba['Preguntaencuesta']['encuesta_id'],
            'Preguntaencuesta.numero' => $preguntaArriba['Preguntaencuesta']['numero']-1));
	$preguntaAbajo = $this->PreguntaEncuesta->find($params);

        $datosPreguntaAbajo = array('numero'=>$preguntaAbajo['Preguntaencuesta']['numero']+1);
        $this->PreguntaEncuesta->id=$preguntaAbajo['Preguntaencuesta']['id'];
        $preguntaAbajo = $this->PreguntaEncuesta->Save($datosPreguntaAbajo);

        //Guardo pregunta que queria subir
        $datosPreguntaArriba = array('numero'=>$preguntaArriba['Preguntaencuesta']['numero']-1);
        $this->PreguntaEncuesta->id=$preguntaArriba['Preguntaencuesta']['id'];
        $this->PreguntaEncuesta->Save($datosPreguntaArriba);

        $this->redirect('encuestas/editar?id='.$idEncuesta);
        exit();
    }

    function eliminarPregunta(){

        $this->escribirHeader("Encuestas");
        $idPregunta= $this->params['url']['idPregunta'];
        $params = array('conditions' => array('Preguntaencuesta.id' => $idPregunta));
	$preguntaBorrar = $this->PreguntaEncuesta->find($params);

        $numPregunta = $preguntaBorrar['Preguntaencuesta']['numero'];
        $idEncuesta = $preguntaBorrar['Encuesta']['id'];

        $busqueda=array("encuesta_id ='".$idEncuesta."' and numero >'".$numPregunta."'", "", "", 0, 0);
        $preguntas = $this->PreguntaEncuesta->findAll($busqueda);
        
        //a cada pregunta posterior a la que voy a borrar, le decremento en uno, su numero de pregunta
        foreach($preguntas as $pregunta){
            $datosPregunta = array('numero'=>$pregunta['Preguntaencuesta']['numero']-1);
            $this->PreguntaEncuesta->id=$pregunta['Preguntaencuesta']['id'];
            $preguntaAbajo = $this->PreguntaEncuesta->Save($datosPregunta);
        }

        $this->PreguntaEncuesta->id=$preguntaBorrar['Preguntaencuesta']['id'];
        $this->PreguntaEncuesta->delete($preguntaBorrar['Preguntaencuesta']['id']);
        $this->redirect('encuestas/editar?id='.$idEncuesta);
        exit();
    }

    function eliminarOpcion(){

        $this->escribirHeader("Encuestas");
        $idOpcion= $this->params['url']['idOpcion'];
        $idEncuesta= $this->params['url']['idEncuesta'];

        $this->Opcion->id=$idOpcion;
        $this->Opcion->delete($idOpcion);
        $this->redirect('encuestas/editar?id='.$idEncuesta);
        exit();
    }


    function mostrarEditarPregunta(){
        $this->escribirHeader("Encuestas");
        $idPregunta= $this->params['url']['idPregunta'];
        $params = array('conditions' => array('Preguntaencuesta.id' => $idPregunta));
		$pregunta = $this->PreguntaEncuesta->find($params);
        $this->set("pregunta", $pregunta);
    }

    function editarPregunta(){

        if(!empty($this->data)){

            //falta editar requerido
            $datosPregunta = array('encuesta_id'=>$this->data['PreguntaEncuesta']['encuesta_id'],
                            'titulo'=>$this->data['PreguntaEncuesta']['titulo'],
                            );
            $this->PreguntaEncuesta->id=$this->data['PreguntaEncuesta']['id'];
            $pregunta = $this->PreguntaEncuesta->save($datosPregunta);

            if($this->data['PreguntaEncuesta']['tipo'] != "text"){
                for($i = 1; $i<= count($this->data['Opcion'])/2; $i++){
                    $titulo = $this->data['Opcion']['opcion'.$i.'text'];
                    $idOpcion = $this->data['Opcion']['id'.$i];
                    $datos = array('titulo'=>$titulo);
                    $this->Opcion->id = $idOpcion;
                    $this->Opcion->save($datos);
                }
            }

           $this->Session->setFlash('La pregunta ha sido actualizada');
           $this->redirect('encuestas/editar?id='.$this->data['PreguntaEncuesta']['encuesta_id']);
           exit();
        }
    }

}
?>
