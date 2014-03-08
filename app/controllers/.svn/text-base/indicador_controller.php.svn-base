<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of indicadores_controller
 *
 * @author pancha
 */
class IndicadorController extends AppController {
    //put your code here
    var $name = "Indicador";
    var $uses = array('Voluntario', 'Persona', 'Sesion', 'Programa','Seguimiento');
    var $helpers = array('Html','Text','Pagination','Ajax'); //HELPERS
    var $cod_voluntario;
    
    function index()
    {
        $this->escribirHeader("Indicadores");
        $cod_voluntario = $this->Session->read('cod_voluntario');

        $this->set('max',3);
       
        $this->CuentaLlamadas(1);
         $this->set('texto',"HOY");
    }


    function MostrarInicio()
    {
        return "3";

    }
     function  view($id) {
		$this->set('max',3);
		$cod_voluntario = $this->Session->read('cod_voluntario');
        if($id == 0){
	       
	        /* apertura usuario caso comunicate*/
	        $this->set('apertura_u', $this->Seguimiento->CuentaLlamadasUsuario($cod_voluntario,2,1));
	
	        /* seguimiento usuario caso comunicate*/
	        $this->set('seguimiento_u', $this->Seguimiento->CuentaLlamadasUsuario($cod_voluntario,4,1));
	
	         /* seguimiento fallido usuario caso comunicate*/
	        $this->set('fallido_u', $this->Seguimiento->CuentaLlamadasUsuario($cod_voluntario,6,1));
	
	         /*cierre usuario caso comunicate*/
	        $this->set('cierre_u', $this->Seguimiento->CuentaLlamadasUsuario($cod_voluntario,8,1));
	
	        /**** Todos los usuarios ***/
	        /* apertura caso comunicate*/
	        $this->set('apertura', $this->Seguimiento->CuentaTodasLasLlamadas(2,1));
	
	        /* seguimiento caso comunicate*/
	        $this->set('seguimiento', $this->Seguimiento->CuentaTodasLasLlamadas(4,1));
	
	         /* seguimiento fallido caso comunicate*/
	        $this->set('fallido', $this->Seguimiento->CuentaTodasLasLlamadas(6,1));
	
	         /*cierre usuario caso comunicate*/
	        $this->set('cierre', $this->Seguimiento->CuentaTodasLasLlamadas(8,1));
            $this->set('texto',"HOY");
            }
        if($id == 1){
            /* apertura usuario caso comunicate*/
	        $this->set('apertura_u', $this->Seguimiento->CuentaLlamadasMesUsuario($cod_voluntario,2));
	
	        /* seguimiento usuario caso comunicate*/
	        $this->set('seguimiento_u', $this->Seguimiento->CuentaLlamadasMesUsuario($cod_voluntario,4));
	
	         /* seguimiento fallido usuario caso comunicate*/
	        $this->set('fallido_u', $this->Seguimiento->CuentaLlamadasMesUsuario($cod_voluntario,6));
	
	         /*cierre usuario caso comunicate*/
	        $this->set('cierre_u', $this->Seguimiento->CuentaLlamadasMesUsuario($cod_voluntario,8));
	
	        /**** Todos los usuarios ***/
	        /* apertura caso comunicate*/
	        $this->set('apertura', $this->Seguimiento->CuentaTodasLasLlamadasMes(2));
	
	        /* seguimiento caso comunicate*/
	        $this->set('seguimiento', $this->Seguimiento->CuentaTodasLasLlamadasMes(4));
	
	         /* seguimiento fallido caso comunicate*/
	        $this->set('fallido', $this->Seguimiento->CuentaTodasLasLlamadasMes(6));
	
	         /*cierre usuario caso comunicate*/
	        $this->set('cierre', $this->Seguimiento->CuentaTodasLasLlamadasMes(8));
            $this->set('texto',"MES");
        }
        if($id == 2){
            /* apertura usuario caso comunicate*/
	        $this->set('apertura_u', $this->Seguimiento->CuentaLlamadasAnualUsuario($cod_voluntario,2));
	
	        /* seguimiento usuario caso comunicate*/
	        $this->set('seguimiento_u', $this->Seguimiento->CuentaLlamadasAnualUsuario($cod_voluntario,4));
	
	         /* seguimiento fallido usuario caso comunicate*/
	        $this->set('fallido_u', $this->Seguimiento->CuentaLlamadasAnualUsuario($cod_voluntario,6));
	
	         /*cierre usuario caso comunicate*/
	        $this->set('cierre_u', $this->Seguimiento->CuentaLlamadasAnualUsuario($cod_voluntario,8));
	
	        /**** Todos los usuarios ***/
	        /* apertura caso comunicate*/
	        $this->set('apertura', $this->Seguimiento->CuentaTodasLasLlamadasAnual(2));
	
	        /* seguimiento caso comunicate*/
	        $this->set('seguimiento', $this->Seguimiento->CuentaTodasLasLlamadasAnual(4));
	
	         /* seguimiento fallido caso comunicate*/
	        $this->set('fallido', $this->Seguimiento->CuentaTodasLasLlamadasAnual(6));
	
	         /*cierre usuario caso comunicate*/
	        $this->set('cierre', $this->Seguimiento->CuentaTodasLasLlamadasAnual(8));
            
            $this->set('texto',"AÑO");
        }
		
        $this->render('index', 'ajax');
     }
     function test(){
     	
     	$returnVar =  "Llamadas anyo ".$this->Seguimiento->CuentaTodasLasLlamadasAnual(4);
     	$returnVar .=  "Llamadas mes ".$this->Seguimiento->CuentaTodasLasLlamadasMes(4);
     	$returnVar .=  "<br/>";
     	echo  $returnVar;
     }
    function CuentaLlamadas($time)
    {
        
        $cod_voluntario = $this->Session->read('cod_voluntario');
        /* apertura usuario caso comunicate*/
        $this->set('apertura_u', $this->Seguimiento->CuentaLlamadasUsuario($cod_voluntario,2,$time));

        /* seguimiento usuario caso comunicate*/
        $this->set('seguimiento_u', $this->Seguimiento->CuentaLlamadasUsuario($cod_voluntario,4,$time));

         /* seguimiento fallido usuario caso comunicate*/
        $this->set('fallido_u', $this->Seguimiento->CuentaLlamadasUsuario($cod_voluntario,6,$time));

         /*cierre usuario caso comunicate*/
        $this->set('cierre_u', $this->Seguimiento->CuentaLlamadasUsuario($cod_voluntario,8,$time));

        /**** Todos los usuarios ***/
        /* apertura caso comunicate*/
        $this->set('apertura', $this->Seguimiento->CuentaTodasLasLlamadas(2,$time));

        /* seguimiento caso comunicate*/
        $this->set('seguimiento', $this->Seguimiento->CuentaTodasLasLlamadas(4,$time));

         /* seguimiento fallido caso comunicate*/
        $this->set('fallido', $this->Seguimiento->CuentaTodasLasLlamadas(6,$time));

         /*cierre usuario caso comunicate*/
        $this->set('cierre', $this->Seguimiento->CuentaTodasLasLlamadas(8,$time));
    }

}
?>
