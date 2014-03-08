<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class OpcionesController extends AppController{
    var $name = "Opciones";
    var $helpers = array('Html', 'Form');


    function agregar($id_pregunta, $titulo){

        if(!empty($id_pregunta) && !empty($titulo)){
            $datos = array('pregunta_id'=>$id_pregunta, 'titulo'=>$titulo);
            $this->Opcion->save($datos);

        }
    }

}
?>
