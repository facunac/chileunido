<?php
class AnotacionesController extends AppController {

	var $name = 'Anotaciones';
	var $helpers = array('Html', 'Form', 'Anotacion' );

	function index($mensaje=null) {
		$this->escribirHeader("Anotaciones");
			
		$busqueda="";
		
		if($this->data['FormBuscar']['tipo_busqueda']=="fecha"){
			//despejamos campo para poxima busqueda
			$this->data['FormBuscar']['tipo_busqueda']="";
			
			if($this->data['FormBuscar']['fec_inicio_periodo']==""||$this->data['FormBuscar']['fec_termino_periodo']==""){
				$mensaje=38;
			} else {
				$busqueda.=	"	(	Anotacione.fecha_inicio >='".$this->data['FormBuscar']['fec_inicio_periodo']."' 
								AND Anotacione.fecha_inicio <='".$this->data['FormBuscar']['fec_termino_periodo']."' 
								) 
							OR	
								(	Anotacione.fecha_termino>='".$this->data['FormBuscar']['fec_inicio_periodo']."' 
								AND Anotacione.fecha_termino<='".$this->data['FormBuscar']['fec_termino_periodo']."' 
							    )
							OR
								(	Anotacione.fecha_inicio <='".$this->data['FormBuscar']['fec_inicio_periodo']."' 
								AND Anotacione.fecha_termino>='".$this->data['FormBuscar']['fec_inicio_periodo']."' 
								)
							OR	
								(	Anotacione.fecha_inicio <='".$this->data['FormBuscar']['fec_termino_periodo']."' 
								AND Anotacione.fecha_termino>='".$this->data['FormBuscar']['fec_termino_periodo']."' 
							    )
							";	
				$mensaje=39;
			}
		}
		
		if($this->data['FormBuscar']['tipo_busqueda']=="comentario"){
			//despejamos campo para poxima busqueda
			$this->data['FormBuscar']['tipo_busqueda']="";
			
			if($this->data['FormBuscar']['comentario']==""){
				$mensaje=38;
			} else {
				$aux="";
				$aux.=$this->data['FormBuscar']['comentario'];
				$busqueda=array("Anotacione.comentario" => "like %$aux%");	
				$mensaje=39;
			}
		}
		
		
		$this->Anotacione->recursive = 0;
		$this->set('anotaciones', $this->Anotacione->findAll($busqueda));
		
		
		
		//para desplegar los mensajes definidos en las otras funciones
		$this->set('msg_for_layout',$mensaje);
	}





	function ingresar() {
		$this->escribirHeader("Anotaciones");
				
		if (empty($this->data)) {
			$this->render();
		} else {
			$this->cleanUpFields();
			
			//Arregla el formato para que coincida con date.
			list($d, $m, $y) = split( "-", $this->data['Anotacione']['fecha_inicio']);                              
            $this->data['Anotacione']['fecha_inicio'] = $y."-".$m."-".$d;			
			
			list($d, $m, $y) = split( "-", $this->data['Anotacione']['fecha_termino']);                              
            $this->data['Anotacione']['fecha_termino'] = $y."-".$m."-".$d;			
			
			$mensaje="";
			if ($this->Anotacione->save($this->data)) {
				$mensaje=31;
			} else {
				$mensaje=32;
			}
			$this->redirect('/anotaciones/index/'.$mensaje);
		}
	}

	function modificar($id = null) {
		$this->escribirHeader("Anotaciones");


		$mensaje="";

		if (empty($this->data)) {
			if (!$id) {
				$mensaje=33;
				$this->redirect('/anotaciones/index/'.$mensaje);
			}
			$this->data = $this->Anotacione->read(null, $id);
			
			list($d, $m, $y) = split( "-", $this->data['Anotacione']['fecha_inicio']);                              
            $this->data['Anotacione']['fecha_inicio'] = $y."-".$m."-".$d;			
			
			list($d, $m, $y) = split( "-", $this->data['Anotacione']['fecha_termino']);                              
            $this->data['Anotacione']['fecha_termino'] = $y."-".$m."-".$d;			
			
		} else {
			$this->cleanUpFields();
			
			//Arregla el formato para que coincida con date.
			list($d, $m, $y) = split( "-", $this->data['Anotacione']['fecha_inicio']);                              
            $this->data['Anotacione']['fecha_inicio'] = $y."-".$m."-".$d;			
			
			list($d, $m, $y) = split( "-", $this->data['Anotacione']['fecha_termino']);                              
            $this->data['Anotacione']['fecha_termino'] = $y."-".$m."-".$d;			
			
			$mensaje="";
			if ($this->Anotacione->save($this->data)) {
				$mensaje=36;
			} else {
				$mensaje=37;
			}
			$this->redirect('/anotaciones/index/'.$mensaje);
			
		}
	}

	function eliminarDesdeCalendario($id = null)
	{
		$this->escribirHeader("Calendario");
		if (!$id) {
			$this->redirect('/calendarios/index/35');
			exit();
		}
		if ($this->Anotacione->del($id)) {
			$this->redirect('/calendarios/index/34');
			exit();
		}
		$this->redirect('/calendarios/index/36');
	}
	
	function eliminar($id = null) {
		$this->escribirHeader("Anotaciones");

		if (!$id) {
			$this->redirect('/anotaciones/index/35');
			exit();
		}
		if ($this->Anotacione->del($id)) {
			$this->redirect('/anotaciones/index/34');
			exit();
		}
		$this->redirect('/anotaciones/index/36');
	}

}
?>