<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of avisos_controller
 *
 * @author Mario Lavandero
 */
class AvisosController extends AppController {
    var $name = 'Avisos';
    //Para poder usar otras tablas dentro de este controlador
    var $uses = array("Aviso");
    var $components = array ('Pagination','Session'); //COMPONENTES
    var $helpers = array('Html','Pagination' ,'Form', 'Time', 'Ajax');

    /*function index() {

        //Formato
        
        $this->set('name_for_layout',$this->Session->read('nombre_completo'));
        $this->set('titulo_pagina','Avisos recientes');
        //$this->set('titulo_pagina',$i);
        $this->set('programa_for_layout', $this->Session->read('programa'));
        $this->set('nrovisitas_for_layout',$this->Session->read('nrovisitas'));

        //Obtención de datos. Obtiene los avisos que no han caducado aún, y las tablas asociadas (voluntarios, personas).
        $this->set('avisos', $this->Aviso->findAll("fecha_caducacion > NOW()", "", "", 0,0,1));
        /*  Parámetros de findAll
            * string $conditions
            * array $fields
            * string $order
            * int $limit
            * int $page
            * int $recursive
        //*/
    //}
    function crear() {
    // Variables para el layout
        $this->escribirHeader("Crear Nuevo Aviso");

        if (empty($this->data)) {
            $this->render();
        } else {
            Controller::cleanUpFields();
            $this->cleanUpFields();
            $this->data['Aviso']['cod_persona'] = $this->Session->read("cod_voluntario");
            if ($this->Aviso->save($this->data)) {
                $this->Session->setFlash('Se ha creado un nuevo aviso');
                $this->redirect('/avisos/crear/');
            }
            else {
                $this->Session->setFlash ('Por favor corrija los errores abajo');
                $this->render ();
            }
        }
    }

        private function _isDate($fecha,$nombre){
        	$patron = "/^\d{4}-\d{2}-\d{2}$/";
	        $arrayFecha = explode("-",$fecha);
        	$error ="";
        	$errores = 0;
        	if($fecha!=""){
     			if(!preg_match($patron, $fecha))
     			{
     				$errores++;
     				$error .= "Fecha '".$nombre."' mal formada";
     			}
     			else
         		{
             		if(count($arrayFecha) == 3){
             			if(checkdate($arrayFecha[1],$arrayFecha[2],$arrayFecha[0]))
	             		{
	             			//BIEN!!
	             		}
	             		else
	             		{
	             			$errores++;
	             			$error .= "Fecha '".$nombre."' fuera de rangos";
	             		}
             		}
             		else
             		{
             			$errores++;
             			$error .= "Fecha '".$nombre."' mal formada, dd-mm-yyyy";
             		}
     			}
     		}
     		return array($error,$errores);
        }
     /**
     * Listar Avisos
     *
     */
    function listar() {
    // Variables para el layout

        $this->escribirHeader("Listado de Avisos");
        $currentdate = date("Y-m-d");
        $this->escribirHeader("Lista de avisos existentes");
        $conditions = null;
        list($order,$limit,$page) = $this->Pagination->init($conditions);
        $order = "Aviso.fecha_creacion DESC";
        $this->set('avisos', $this->Aviso->findAll("", null,$order, $limit,$page));
        $this->set('fechaactual', $currentdate);
    }
     /**
     * Filtrar Avisos por fecha de creacion
     *
     */
    function filtrar() {
        
        $fecha1=$this->data['FormFiltrar']['fecha_inicial'];
        $fecha2=$this->data['FormFiltrar']['fecha_final'];
        $this->escribirHeader("Listado de Avisos");
        $currentdate = date("Y-m-d");
        $this->escribirHeader("Lista de avisos existentes");

        $result1 = $this->_isDate($fecha1,'desde');
	$result2 = $this->_isDate($fecha2,'hasta');

        $errores = $result1[1]+$result2[1];
	$error = $result1[0]." ".$result2[0];

        if($errores==0)
	{
            $arrayFecha1;
            $arrayFecha2;
            $conditions = "";

            $arrayFecha1 = explode("-",$fecha1);
            $arrayFecha2 = explode("-",$fecha2);

            if($fecha1!="")
                $conditions["Aviso.fecha_creacion"] = ">=". $arrayFecha1		[0]."-".$arrayFecha1[1]."-".$arrayFecha1[2]." 00:00:00";
            if($fecha2!="")
                $conditions["Aviso.fecha_creacion"] = "<=". $arrayFecha2		[0]."-".$arrayFecha2[1]."-".$arrayFecha2[2]." 23:59:59";
            if($fecha1!="" && $fecha2!="") {
                $conditions["and"] = array("Aviso.fecha_creacion"
                    =>">=". $arrayFecha1[0]."-".$arrayFecha1			[1]."-".$arrayFecha1[2]." 00:00:00");
                $conditions["Aviso.fecha_creacion"] =
                    "<=". $arrayFecha2[0]."-".$arrayFecha2[1]."-".		$arrayFecha2[2]." 23:59:59";
            }
        }
        else
        {
            $this->Session->setFlash ($error);
	    $conditions = null;
        }
        list($order,$limit,$page) = $this->Pagination->init($conditions);
        $order = "Aviso.fecha_creacion DESC";
        $this->set('avisos', $this->Aviso->findAll($conditions,null,$order, $limit,$page));
        $this->set('fechaactual', $currentdate);
    }
     /**
     * Eliminar Aviso
     *
     */
    function delete ($index = null) {
			$this->escribirHeader ('Eliminar aviso');
			if($index == null) {
				$this->Session->setFlash ('Index de aviso invalido');
			} else {
				if ($this->Aviso->del($index)) {
					$this->Session->setFlash ('Aviso eliminado satisfactoriamente');
					$this->redirect ('/avisos/listar');
				} else {
					$this->Session->setFlash ('Impossible de eliminar el aviso');
				}
			}
		}

    function mostrarInicio() {
            /*TODO:
             * Armar el Div desde acá mismo, y tirarlo armado hasta el home
             */
        $output = "<div class='avisos' id='scroll-contenedor'>";
        $output .= "<div class='items'>";


        $avisos = $this->Aviso->findAll("fecha_caducacion > NOW()", "", "ORDER BY fecha_creacion DESC", 0,0,1);
        foreach ($avisos as $thisaviso):
            $output.="<div>";
            $output.="<p><span class='avisosTitulo'>".$thisaviso["Aviso"]["titulo"]."</span><br/>";
            $output.="<span class='avisosTexto'>".$thisaviso["Aviso"]["texto"]."</span><br/>";
            $output.="<span class='avisosAutor'>Publicado el ".$thisaviso["Aviso"]["fecha_creacion"]."</span>";
            $output.="</p></div>";


        endforeach;
        $output.="</div>";
        $output.="</div>";


        //return $output;
        $this->set('output',$output);

    }
}
?>
