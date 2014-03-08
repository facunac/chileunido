<?php
class DonarstacksController extends AppController {

	var $name = 'Donarstacks';
	var $uses = array( "Comuna", "Donarstack","Socio","Pago" );
	var $helpers = array('Html', 'Form', 'Time', 'Excel'); 

	function index() {
		$this->escribirHeader("Gestor de Altas de Donaciones");			
		$this->Donarstack->recursive = 0;
		$this->set('donarstacks', $this->Donarstack->findAll());
	}

	function view($id = null) {
	
		$id = $this->data['Donarstack']['cod_donarstack'];
	
		$this->escribirHeader("Gestor de Altas de Donaciones");
		if (!$id) {
			$this->Session->setFlash('Invalid id for Donarstack.');
			$this->redirect('/donarstacks/index');
		}
		$this->set('donarstack', $this->Donarstack->read(null, $id));
	}

	
	
	function add() {
		$this->escribirHeader("Gestor de Altas de Donaciones");
		$comunas=$this->Comuna->getAllAsArray();
        $this->set('comunas', $comunas);
        $regiones=$this->Comuna->getRegiones();
        $this->set('regiones', $regiones);
        
        $mediosDePago = array('TB->Visa','TB->Mastercard','TB->Dinners','TB->Amex');
		//sort($mediosDePago);
        $this->set('mediosDePago', $mediosDePago);
		
        $estados= array('En Agregacion','En Borrado','En Modificacion');
		sort($estados);
        $this->set('estados', $estados);
		
        

        if (empty($this->data)) {
			$this->render();
		} else {
			$this->cleanUpFields();
			
			$validos = true;
		
			/*
			'nom_rut' => 'Ingrese el rut',	
			'nom_nombre' => 'Ingrese el nombre',
			'nom_appa' => 'Ingrese el Apellido Paterno',
			'nom_apma' => 'Ingrese el Apellido Materno',
			'fec_nacimiento' => 'Ingrese fecha',
			'nom_direccion' => 'Ingrese direccion',
			'num_telefono1' => 'Ingrese telefono1',
			'nom_mail' => 'Ingrese el mail',
			'num_monto' => 'Ingrese el monto',
			'nom_mediopago' => 'Ingrese el medio pago',
			'num_idpago' => 'Ingrese el numero del medio de pago');
			*/
           						
			if( !eregi('^([0-9]{6,9}-)[0-9]{1}$',$this->data['Donarstack']['nom_rut']) ) 
			{
               	$validos = false;
                $msg = 'RUT inv&aacute;lido';
			}
                        
            if( strlen($this->data['Donarstack']['nom_nombre']) < 1 )
            {
            	$validos = false;
            	$msg = 'Nombre inv&aacute;lido';
            }
                        
            if( strlen($this->data['Donarstack']['nom_appa']) < 1 )
            {
            	$validos = false;
                $msg = 'Apellido paterno inv&aacute;lido';
            }
                        
            if( strlen($this->data['Donarstack']['nom_apma']) < 1 )
            {
            	$validos = false;
            	$msg = 'Apellido materno inv&aacute;lido';
            }
                        
          //  if( !eregi('^([0-9]{2}-){2}[0-9]{4}$',$this->data['Donarstack']['fec_nacimiento']) )
            //{
            	//$validos = false;
                //$msg = 'Fecha de nacimiento inv&aacute;lida';
            //}

                        
            if( strlen($this->data['Donarstack']['nom_direccion']) < 1 )
            {
            	$validos = false;
                $msg = 'Direccion inv&aacute;lida';
            }
                        
            if( strlen($this->data['Donarstack']['cod_comuna']) < 1 )
            {
               	$validos = false;
               	$msg = 'Comuna inv&aacute;lida';
			}
                        
            if( strlen($this->data['Donarstack']['num_telefono1']) < 1 )
            {
               	$validos = false;
               	$msg = 'Telefono 1 inv&aacute;lido';
            }

			if( strlen($this->data['Donarstack']['nom_mail']) > 0 )
            {
              	if( !eregi('^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*$', $this->data['Donarstack']['nom_mail']) )
               	{
               		$validos = false;
               		$msg = 'E-mail inv&aacute;lido';
               	}
            }

            if( strlen($this->data['Donarstack']['num_monto']) < 1 )
            {
               	$validos = false;
               	$msg = 'Monto inv&aacute;lido';
            }

            if( strlen($this->data['Donarstack']['nom_mediopago']) < 1 )
            {
               	$validos = false;
               	$msg = 'Medio de pago inv&aacute;lido';
            }
            
            if( $validos )
            {



				if ($this->Donarstack->save($this->data)) {
					$_SESSION['success']=true;
					$_SESSION['msg']='Se ha agregado satisfactoriamente la informaci&oacute;n.';				
					$this->redirect('/donarstacks/index');
				}
				//var_dump($this->data['Donarstack']);
				
			
            } else {
					$this->set('donarstack', $this->Donarstack->read(null, $id));
					$_SESSION['error']=true;
					$_SESSION['msg']='Debe corregir la informaci&oacute;n: '.$msg;
			}
		}
	}
	
	

	function edit($id = null) {
		$this->escribirHeader("Gestor de Altas de Donaciones");

		$id = $this->data['Donarstack']['cod_donarstack'];
		
		$mediosDePago = array('TB->Visa','TB->Mastercard','TB->Dinners','TB->Amex');
		//sort($mediosDePago);
        $this->set('mediosDePago', $mediosDePago);
		
		if( !isset($_POST['editar']) )
		{
			$donarstack = $this->Donarstack->read(null, $id);
			//list($y, $m, $d) = split( "-", $donarstack['Donarstack']['fec_nacimiento']);                  
            //$donarstack['Donarstack']['fec_nacimiento'] = $d."-".$m."-".$y;
			$this->set('donarstack', $donarstack);
		}
		else
		{
			$this->cleanUpFields();
			
			$validos = true;
			
			/*
			'nom_rut' => 'Ingrese el rut',	
			'nom_nombre' => 'Ingrese el nombre',
			'nom_appa' => 'Ingrese el Apellido Paterno',
			'nom_apma' => 'Ingrese el Apellido Materno',
			'fec_nacimiento' => 'Ingrese fecha',
			'nom_direccion' => 'Ingrese direccion',
			'num_telefono1' => 'Ingrese telefono1',
			'nom_mail' => 'Ingrese el mail',
			'num_monto' => 'Ingrese el monto',
			'nom_mediopago' => 'Ingrese el medio pago',
			'num_idpago' => 'Ingrese el numero del medio de pago');
			*/
           						
			if( !eregi('^([0-9]{6,9}-)[0-9]{1}$',$this->data['Donarstack']['nom_rut']) ) 
			{
               	$validos = false;
                $msg = 'RUT inv&aacute;lido';
			}
                        
            if( strlen($this->data['Donarstack']['nom_nombre']) < 1 )
            {
            	$validos = false;
            	$msg = 'Nombre inv&aacute;lido';
            }
                        
            if( strlen($this->data['Donarstack']['nom_appa']) < 1 )
            {
            	$validos = false;
                $msg = 'Apellido paterno inv&aacute;lido';
            }
                        
            if( strlen($this->data['Donarstack']['nom_apma']) < 1 )
            {
            	$validos = false;
            	$msg = 'Apellido materno inv&aacute;lido';
            }
                        

           // if( !eregi('^([0-9]{2}-){2}[0-9]{4}$',$this->data['Donarstack']['fec_nacimiento']) )
           // {
            //	$validos = false;
              //  $msg = 'Fecha de nacimiento inv&aacute;lida';
            //}

                        
            if( strlen($this->data['Donarstack']['nom_direccion']) < 1 )
            {
            	$validos = false;
                $msg = 'Direccion inv&aacute;lida';
            }
                        
            if( strlen($this->data['Donarstack']['cod_comuna']) < 1 )
            {
               	$validos = false;
               	$msg = 'Comuna inv&aacute;lida';
			}
                        
            if( strlen($this->data['Donarstack']['num_telefono1']) < 1 )
            {
               	$validos = false;
               	$msg = 'Telefono 1 inv&aacute;lido';
            }

			if( strlen($this->data['Donarstack']['nom_mail']) > 0 )
            {
              	if( !eregi('^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*$', $this->data['Donarstack']['nom_mail']) )
               	{
               		$validos = false;
               		$msg = 'E-mail inv&aacute;lido';
               	}
            }

            if( strlen($this->data['Donarstack']['num_monto']) < 1 )
            {
               	$validos = false;
               	$msg = 'Monto inv&aacute;lido';
            }

            if( strlen($this->data['Donarstack']['nom_mediopago']) < 1 )
            {
               	$validos = false;
               	$msg = 'Medio de pago inv&aacute;lido';
            }

            if( strlen($this->data['Donarstack']['num_idpago']) < 1 )
            {
               	$validos = false;
               	$msg = 'ID pago inv&aacute;lido';
            }			
            
            if( $validos )
            {

				// d-m-y -> y-m-d 
           		//list($d, $m, $y) = split( "-", $this->data['Donarstack']['fec_nacimiento']);                              
            	//$this->data['Donarstack']['fec_nacimiento'] = $y."-".$m."-".$d;

				if ($this->Donarstack->save($this->data)) {
					$_SESSION['success']=true;
					$_SESSION['msg']='Se ha editado satisfactoriamente la informaci&oacute;n.';				
					$this->redirect('/donarstacks/index');
				}
			
            } else {
					$this->set('donarstack', $this->Donarstack->read(null, $id));            		
					$_SESSION['error']=true;
					$_SESSION['msg']='Debe corregir la informaci&oacute;n: '.$msg;
			}
		}
	}
	
	
	

	function delete($id = null) {
	
		$id = $this->data['Donarstack']['cod_donarstack'];
	
		$this->escribirHeader("Gestor de Altas de Donaciones");

		if ($this->Donarstack->del($id)) {
			$_SESSION['success']=true;
			$_SESSION['msg']='El mandato '.$id.' fue eliminado satisfactoriamente.';				
			$this->redirect('/donarstacks/index');
					
			//$this->Session->setFlash('The Donarstack deleted: id '.$id.'');
		}
	}

	function agregar($id=null) {
	
		$id = $this->data['Donarstack']['cod_donarstack'];
		$miStack=$this->Donarstack->read(null, $id);	
		//var_dump($miStack);
	    //die();
		//list($d, $m, $y) = split( "-", $miStack['Donarstack']['fec_nacimiento']);                              
	    //        	$miStack['Donarstack']['fec_nacimiento'] = $y."-".$m."-".$d;
		
		$this->data['Socio']['nom_rut']=$miStack['Donarstack']['nom_rut'];
	  	$this->data['Socio']['nom_nombre']=$miStack['Donarstack']['nom_nombre'];
		$this->data['Socio']['nom_appat']=$miStack['Donarstack']['nom_appa'];
		$this->data['Socio']['nom_apmat']=$miStack['Donarstack']['nom_apma'];
		$this->data['Socio']['fec_nacimiento']=$miStack['Donarstack']['fec_nacimiento'];
		$this->data['Socio']['nom_direccion']=$miStack['Donarstack']['nom_direccion'];
		$this->data['Socio']['cod_comuna']=$miStack['Donarstack']['cod_comuna'];
		$this->data['Socio']['num_telefono1']=$miStack['Donarstack']['num_telefono1'];
		$this->data['Socio']['num_telefono2']=$miStack['Donarstack']['num_telefono2'];
		$this->data['Socio']['nom_email']=$miStack['Donarstack']['nom_mail'];

		if($miStack['Donarstack']['bit_estado']=='En Agregacion')
		{
			
		if($this->Socio->save($this->data))
			{
		
				$cod_socio=$this->Socio->getLastInsertId();
				$this->data['Pago']['cod_socio']=$cod_socio;
				$this->data['Pago']['nom_nombrecompleto']=$miStack['Donarstack']['nom_nombre'].$miStack['Donarstack']['nom_appa'].$miStack['Donarstack']['nom_apma'];
				$this->data['Pago']['num_monto']=$miStack['Donarstack']['num_monto'];
				$this->data['Pago']['nom_mediopago']=$miStack['Donarstack']['nom_mediopago'];
				$this->data['Pago']['bit_ajusteipc']=$miStack['Donarstack']['bit_ajusteipc'];
				
				
				if($this->Pago->save($this->data))
				{	
					if ($this->Donarstack->del($id))
					 {
						$_SESSION['success']=true;
						$_SESSION['msg']=('El socio fue agregado satisfactoriamente.');				
						$this->redirect('/donarstacks/index');
					 }
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
		}
		if($miStack['Donarstack']['bit_estado']=='En Borrado')
		{
			if ($this->Donarstack->del($id))
					 {
						$_SESSION['success']=true;
						$_SESSION['msg']=('El pago fue eliminado definitivamente.');				
						$this->redirect('/donarstacks/index');
					 }
		
		}
		
	}
	
	
	function excel(){	
		$registros = unserialize($this->data['Excel']['Hoja']);
		$this->set('registros',$registros);
		$this->set('type','pila_pagos');
		$this->render('excel', 'excel'); 
	}
}
?>