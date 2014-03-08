<?php
class PagosController extends AppController {

	var $name = 'Pagos';
	var $uses = array('Pago', 'Socio', 'Donarstack');
	var $helpers = array('Html', 'Form', 'Time', 'Excel'); 

	function index() {
		// Variables para el layout
		$this->escribirHeader("Gestión de Pagos");
		
		$this->Pago->recursive = 0;
		$this->set('pagos', $this->Pago->findAll());
	}

	function view($id = null) {
		// Variables para el layout
		$this->escribirHeader("Gestión de Pagos");
		
		$id = $this->data['Pago']['cod_pago']; 
	
		
		$pago = $this->Pago->read(null, $id);
		//list($y, $m, $d) = split( "-", $pago['Pago']['fec_inicio']);                  
        //$pago['Pago']['fec_inicio'] = $d."-".$m."-".$y;
		$this->set('pago', $pago);
	}

	function add() {
		// Variables para el layout
		$this->escribirHeader("Gestión de Pagos");
		
		$mediosDePago = array('CMR','Presto','PAC','Paris','Visa','Mastercard');
		sort($mediosDePago);
        $this->set('mediosDePago', $mediosDePago);
        

		// A la mierda con los metodos de pastelphp
		$tmp = $this->Socio->query("SELECT * FROM socios;");
		for($n=0; $n<count($tmp); $n++)
			$socios[$n] = $tmp[$n]['socios']['cod_socio'];
        sort($socios);
        $this->set('socios', $socios);
        
			
			
		if (empty($this->data)) {
			$this->render();
		} else {
			$this->cleanUpFields();
			
			if ($this->Pago->save($this->data)) {
					$_SESSION['success']=true;
					$_SESSION['msg']='Se ha agregado satisfactoriamente la informaci&oacute;n.';				
					$this->redirect('/pagos/index');
			} else {
					$_SESSION['error']=true;
                    $_SESSION['msg']='No se pudo realizar la operaci&oacute;n, int&eacute;ntelo nuevamente.';
			}
		}
	}

	
	
	function edit($id = null) {
		// Variables para el layout
		$this->escribirHeader("Gestión de Pagos");
		
		$mediosDePago = array('CMR','Presto','PAC','Paris','Visa','Mastercard');
		sort($mediosDePago);
        $this->set('mediosDePago', $mediosDePago);
        

		// A la mierda con los metodos de pastelphp
		$tmp = $this->Socio->query("SELECT * FROM socios;");
		for($n=0; $n<count($tmp); $n++)
			$socios[$n] = $tmp[$n]['socios']['cod_socio'];
        sort($socios);
        $this->set('socios', $socios);

        
		$id = $this->data['Pago']['cod_pago']; 

		//if (empty($this->data) || !isset($this->data['Pago']['enviado'])) {
		if( !isset($_POST['editar']) )
		{	
			$pago = $this->Pago->read(null, $id);
			//list($y, $m, $d) = split( "-", $pago['Pago']['fec_inicio']);                  
        	//$pago['Pago']['fec_inicio'] = $d."-".$m."-".$y;
			$this->set('pago', $pago);
		} 
		else 
		{
			$this->cleanUpFields();
			// d-m-y -> y-m-d 
            //list($d, $m, $y) = split( "-", $this->data['Pago']['fec_inicio']);                              
            //$this->data['Pago']['fec_inicio'] = $y."-".$m."-".$d;
			
			if ($this->Pago->save($this->data)) {
				//$this->flash('Pago saved.', '/pagos/index');
				$_SESSION['success']=true;
				$_SESSION['msg']='Se ha editado satisfactoriamente la informaci&oacute;n.';				
				$this->redirect('/pagos/index');
			} else {
				$_SESSION['error']=true;
                $_SESSION['msg']='No se pudo realizar la operaci&oacute;n, int&eacute;ntelo nuevamente.';
			}
		}
	}
	
	

	function delete($id = null) {
		// Variables para el layout
		$this->escribirHeader("Gestión de Pagos");
		$id = $this->data['Pago']['cod_pago']; 
		$miStack=$this->Pago->read(null, $id);	
		$cod_socio=$miStack['Pago']['cod_socio']; 
		$miSocio=$this->Socio->read(null, $cod_socio);
		
	
		
		
		
		$this->data['Donarstack']['nom_rut']=$miSocio['Socio']['nom_rut'];
		$this->data['Donarstack']['nom_nombre']=$miSocio['Socio']['nom_nombre'];
		$this->data['Donarstack']['nom_appa']=$miSocio['Socio']['nom_appat'];
		$this->data['Donarstack']['nom_apma']=$miSocio['Socio']['nom_apmat'];
		$this->data['Donarstack']['nom_direccion']=$miSocio['Socio']['nom_direccion'];
		$this->data['Donarstack']['cod_comuna']=$miSocio['Socio']['cod_comuna'];
		$this->data['Donarstack']['num_telefono1']=$miSocio['Socio']['num_telefono1'];
		$this->data['Donarstack']['num_telefono2']=$miSocio['Socio']['num_telefono2'];
		$this->data['Donarstack']['nom_mail']=$miSocio['Socio']['nom_email'];
		$this->data['Donarstack']['bit_genero']=$miSocio['Socio']['bit_genero'];
		
		$this->data['Donarstack']['num_monto']=$miStack['Pago']['num_monto'];
		$this->data['Donarstack']['nom_mediopago']=$miStack['Pago']['nom_mediopago'];
		$this->data['Donarstack']['bit_ajusteipc']=$miStack['Pago']['bit_ajusteipc'];
		$this->data['Donarstack']['bit_estado']=('En Borrado');
		
		

		if($this->Donarstack->save($this->data))
			{
		
				if ($this->Donarstack->del($id))
					 {
						$_SESSION['success']=true;
						$_SESSION['msg']=('El pago fue agregado a la pila.');				
						$this->redirect('/pagos/index');
					 }
			}
	
			
			else
			{
			//NO IMPLEMENTADO
			$this->data['Pago']['cod_socio']=$cod_socio;
			//$this->data['Pago']['nom_nombrecompleto']=$miStack['Donarstack']['nom_nombre'].$miStack['Donarstack']['nom_appa'].$miStack['Donarstack']['nom_apma'];
			$this->data['Pago']['num_monto']=$miStack['Donarstack']['num_monto'];
			$this->data['Pago']['nom_mediopago']=$miStack['Donarstack']['nom_mediopago'];
			$this->data['Pago']['bit_ajusteipc']=$miStack['Donarstack']['bit_ajusteipc'];
			$this->Pago->save($this->data);
			$this->redirect('/donarstacks/index');
			}
		
		
		
		
		
		
		if ($this->Pago->del($id)) {
			$_SESSION['success']=true;
			$_SESSION['msg']='El pago '.$id.' fue eliminado satisfactoriamente.';				
			$this->redirect('/pagos/index');
		}
	}
	
	
	function excel(){	
		$pagos = unserialize($this->data['Excel']['Hoja']);
		$this->set('pagos',$pagos);
		$this->set('type','nomina_pagos');
		$this->render('excel', 'excel'); 
	}

}
?>