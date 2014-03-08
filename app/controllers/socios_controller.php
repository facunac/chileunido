<?php
class SociosController extends AppController {

        var $name = 'Socios';
        //Para poder usar otras tablas dentro de este controlador
        var $uses = array( "Comuna", "Socio", "Pago" );

        var $helpers = array('Html', 'Form', 'Time', 'Excel');

        function index($mensaje=null) {

                // Variable para el layout
                $this->escribirHeader("Gestión de Socios");

                $this->Socio->recursive = 0;
                $this->set('socios', $this->Socio->findAll());
                $this->set('msg_for_layout',$mensaje);
        }

        function ver() {
                // Variables para el layout
                $this->escribirHeader("Gestión de Socios");

                $comunas=$this->Comuna->getAllAsArray();
                $this->set('comunas', $comunas);

                if (!$this->data['Socio']['cod_socio']) {
                        $this->Session->setFlash('Id inv&aacute;lido para Socio.');
                        $this->redirect('/socios/index');
                }
                $this->set('socio', $this->Socio->read(null, $this->data['Socio']['cod_socio']));
        }

        

        function crear() {

        		// Variables para el layout
                $this->escribirHeader("Gestión de Socios");

                $comunas=$this->Comuna->getAllAsArray();
                $this->set('comunas', $comunas);

                $regiones=$this->Comuna->getRegiones();
                $this->set('regiones', $regiones);

                if (empty($this->data)) {
                        $this->render();
                } else {
                        $this->cleanUpFields();

						$validos = true;
                        
						
						if( !eregi('^([0-9]{6,9}-)[0-9]{1}$',$this->data['Socio']['nom_rut']) ) 
						{
                        	$validos = false;
                        	$msg = 'RUT inv&aacute;lido';
						}
                        
                        if( strlen($this->data['Socio']['nom_nombre']) < 1 )
                        {
                        	$validos = false;
                        	$msg = 'Nombre inv&aacute;lido';
                        }
                        
                        if( strlen($this->data['Socio']['nom_appat']) < 1 )
                        {
                        	$validos = false;
                        	$msg = 'Apellido paterno inv&aacute;lido';
                        }
                        
                        if( strlen($this->data['Socio']['nom_apmat']) < 1 )
                        {
                        	$validos = false;
                        	$msg = 'Apellido materno inv&aacute;lido';
                        }
                        
                        if( !eregi('^[0-9]{4}-[0-9]{2}-[0-9]{2}$',$this->data['Socio']['fec_nacimiento']) )
                        {
                        	$validos = false;
                        	$msg = 'Fecha de nacimiento inv&aacute;lida';
                        }
                        
                        if( strlen($this->data['Socio']['nom_direccion']) < 1 )
                        {
                        	$validos = false;
                        	$msg = 'Direcci&oacute;n inv&aacute;lida';
                        }
                        
                        if( strlen($this->data['Socio']['cod_comuna']) < 1 )
                        {
                        	$validos = false;
                        	$msg = 'Comuna inv&aacute;lida';
                        }
                        
                        if( strlen($this->data['Socio']['num_telefono1']) < 1 )
                        {
                        	$validos = false;
                        	$msg = 'Tel&eacute;fono 1 inv&aacute;lido';
                        }

                        if( strlen($this->data['Socio']['nom_email']) > 0 )
                        {
                        	if( !eregi('^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*$', $this->data['Socio']['nom_email']) )
                        	{
                        		$validos = false;
                        		$msg = 'E-mail inv&aacute;lido';
                        	}
                        }
                        
                        
                        /*
                        nom_rut V
                        nom_nombre V
                        nom_appat V
                        nom_apmat V
                        fec_nacimiento V
                        nom_direccion V
                        cod_region 
                        cod_comuna V
                        num_telefono1 V
                        num_telefono2 V
                        nom_email V
                        */
                        
                        if( $validos )
                        {
                        	// d-m-y -> y-m-d 
                        	//list($d, $m, $y) = split( "-", $this->data['Socio']['fec_nacimiento']);                              
                        	//$this->data['Socio']['fec_nacimiento'] = $y."-".$m."-".$d;
                        	if ($this->Socio->save($this->data)) {
                        	 	//$this->Session->setFlash('Se ha creado un nuevo socio');
                        	 	
                        	    // Don't try this at home
                        		//$_SESSION['success']=true;
                        		//$_SESSION['msg']='Se ha creado un nuevo socio.';
                        		$mensaje=100;
                                $this->redirect('/socios/index/'.$mensaje);
                        	 } else {
                        	 	//$this->Session->setFlash('No se pudo realizar la operaci&oacute;n, int&eacute;ntelo nuevamente');
                        	 	
                        	    // Don't try this at home
                        		//$_SESSION['error']=true;
                        		//$_SESSION['msg']='No se pudo realizar la operaci&oacute;n, int&eacute;ntelo nuevamente.';
                        	 	$mensaje=101;
                        	 }
                        } else {
                                //$this->Session->setFlash('Debe corregir la informaci&oacute;n: '.$msg);
                                // Don't try this at home
                        		//$_SESSION['success']=true;
                        		//$_SESSION['msg']='Debe corregir la informaci&oacute;n: '.$msg;
                        		$mensaje=102;
                        }
                }
                
                $this->set('msg_for_layout',$mensaje);
        }
                

        
        function editar() {

                // Variables para el layout
                $this->escribirHeader("Gestión de Socios");

                $comunas=$this->Comuna->getAllAsArray();
                $this->set('comunas', $comunas);

                $regiones=$this->Comuna->getRegiones();
                $this->set('regiones', $regiones);

				if(isset($this->data['Socio']['enviado'])) 
				{
                	if (!$this->data['Socio']['cod_socio']) 
                	{
                    	$this->Session->setFlash('Id inv&aacute;lido para Socio');
                    	$this->redirect('/socios/index');
                	}
                	$this->data = $this->Socio->read(null, $this->data['Socio']['cod_socio']);
                        
                } else {
                        $this->cleanUpFields();

                		$validos = true;
                        						
						if( !eregi('^([0-9]{6,9}-)[0-9]{1}$',$this->data['Socio']['nom_rut']) ) 
						{
                        	$validos = false;
                        	$msg = 'RUT inv&aacute;lido';
						}
                        
                        if( strlen($this->data['Socio']['nom_nombre']) < 1 )
                        {
                        	$validos = false;
                        	$msg = 'Nombre inv&aacute;lido';
                        }
                        
                        if( strlen($this->data['Socio']['nom_appat']) < 1 )
                        {
                        	$validos = false;
                        	$msg = 'Apellido paterno inv&aacute;lido';
                        }
                        
                        if( strlen($this->data['Socio']['nom_apmat']) < 1 )
                        {
                        	$validos = false;
                        	$msg = 'Apellido materno inv&aacute;lido';
                        }
                        
                        if( !eregi('^[0-9]{4}-[0-9]{2}-[0-9]{2}$',$this->data['Socio']['fec_nacimiento']) )
                        {
                        	$validos = false;
                        	$msg = 'Fecha de nacimiento inv&aacute;lida';
                        }
                        
                        if( strlen($this->data['Socio']['nom_direccion']) < 1 )
                        {
                        	$validos = false;
                        	$msg = 'Direccion inv&aacute;lida';
                        }
                        
                        if( strlen($this->data['Socio']['cod_comuna']) < 1 )
                        {
                        	$validos = false;
                        	$msg = 'Comuna inv&aacute;lida';
                        }
                        
                        if( strlen($this->data['Socio']['num_telefono1']) < 1 )
                        {
                        	$validos = false;
                        	$msg = 'Telefono 1 inv&aacute;lido';
                        }

                        if( strlen($this->data['Socio']['nom_email']) > 0 )
                        {
                        	if( !eregi('^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*$', $this->data['Socio']['nom_email']) )
                        	{
                        		$validos = false;
                        		$msg = 'E-mail inv&aacute;lido';
                        	}
                        }
                        
                        if( $validos )
                        {
                        	// d-m-y -> y-m-d 
                        	//list($d, $m, $y) = split( "-", $this->data['Socio']['fec_nacimiento']);                              
                        	//$this->data['Socio']['fec_nacimiento'] = $y."-".$m."-".$d;
                        	if ($this->Socio->save($this->data)) 
                        	{
                        		//$this->Session->setFlash('Se ha editado satisfactoriamente la informaci&oacute;n.');
                        		
                        		// Don't try this at home
                        		//$_SESSION['success']=true;
                        		//$_SESSION['msg']='Se ha editado satisfactoriamente la informaci&oacute;n.';
                        		$mensaje=103;
								$this->redirect('/socios/index/'.$mensaje);
                        	} else {
                        		//$this->Session->setFlash('No se pudo realizar la operaci&oacute;n, int&eacute;ntelo nuevamente.');
                        		
                        	    // Don't try this at home
                        		//$_SESSION['error']=true;
                        		//$_SESSION['msg']='No se pudo realizar la operaci&oacute;n, int&eacute;ntelo nuevamente.';
                        		$mensaje=104;
                        	}
                        
                        } else {
							//$this->Session->setFlash('Debe corregir la informaci&oacute;n: '.$msg);
							// Don't try this at home
                        	//$_SESSION['error']=true;
                        	//$_SESSION['msg']='Debe corregir la informaci&oacute;n: '.$msg;
                        	$mensaje=105;
                        }

                }
                
                $socio = $this->Socio->read(null, $this->data['Socio']['cod_socio']);
                
                // d-m-y <- y-m-d 
                //list($y, $m, $d) = split( "-", $socio['Socio']['fec_nacimiento']);                  
                //$socio['Socio']['fec_nacimiento'] = $d."-".$m."-".$y;
	            $this->set('socio', $socio);
	            $this->set('msg_for_layout',$mensaje);
        }

        
        	
        function borrar() {


                if (!$this->data['Socio']['cod_socio']) {
                        //$this->Session->setFlash('Id inv&aacute;lido para socio '.$this->data['Socio']['cod_socio']);
                        
                        // Don't try this at home
                        //$_SESSION['error']=true;
                        //$_SESSION['msg']='Id inv&aacute;lido para socio '.$this->data['Socio']['cod_socio'];
                        $mensaje=106;
                        $this->redirect('/socios/index/'.$mensaje);
                }
                
                
                $aux=$this->Pago->findByCodSocio($this->data['Socio']['cod_socio']);
                
                if(!$aux)
                {           
	                if ($this->Socio->del($this->data['Socio']['cod_socio'])) {
	                        //$this->Session->setFlash('Socio borrado');
	                        // Don't try this at home
	                        //$_SESSION['success']=true;
	                        //$_SESSION['msg']='Socio borrado exitosamente.';
	                        $mensaje=107;
	                        $this->redirect('/socios/index/'.$mensaje);
	                } else {
	                        //$this->Session->setFlash('No se logr&oacute; completar la operaci&oacute;n, intentelo nuevamente');
	                        // Don't try this at home
	                        //$_SESSION['error']=true;
	                        //$_SESSION['msg']='No se logr&oacute; completar la operaci&oacute;n, int&eacute;ntelo nuevamente.';
	                		$mensaje=108;
	                		$this->redirect('/socios/index/'.$mensaje);
	                }
        		}
        		else
        		{
        			//$_SESSION['error']=true;
	                //$_SESSION['msg']='El socio tiene pagos activos, debe desactivarlos antes de eliminar al socio';
	                $mensaje=109;
        			$this->redirect('/socios/index/'.$mensaje);
	            
        		}
        		$this->set('msg_for_layout',$mensaje);
        }
        
        
        
		function excel(){	
			$socios = unserialize($this->data['Excel']['Hoja']);
			$this->set('socios',$socios);
			$this->set('type','socios');
			$this->render('excel', 'excel'); 
		}
        
		
		
        private function test() {
        
        }

}
?>