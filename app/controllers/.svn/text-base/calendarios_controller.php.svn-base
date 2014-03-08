<?php
class CalendariosController extends AppController {

        var $name = 'Calendarios';
        var $uses = array("Calendario","Voluntario","Persona","Anotacione","Caso");
        var $helpers = array('Html', 'Form', 'Time', 'Excel', 'Matrix', 'Anotacion');

        function index($cod_calendario=0,$fecha_entrada=null) {
        
        		// #########################################
        		// #########################################
        		$tiempo_inicial = microtime(true); 
        		//echo (microtime(true)-$tiempo_inicial)."<br>";
        
        
        		if( stristr( $_SERVER['REQUEST_URI'], "/index/" ) === false )
        			$this->redirect('/calendarios/index/');

        		if( $fecha_entrada == null || !ereg("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})", $fecha_entrada) )
        			$fecha_entrada	= date('Y-m-d');
        		
        		                // Variable para el layout
                $this->escribirHeader("Calendario");
        			
        			
        		list($Y,$M,$D) = split('-', $fecha_entrada );
        		
        		// domingo = 0, sabado = 6
        		$dia = date('w', mktime(0,0,0,$M,$D,$Y));
        		       		
        		if( $dia == 0 )
        		{
        			for($n=0; $n<5; $n++)
        				$fecha[$n] = date('Y-m-d', mktime(0,0,0,$M,$D+1+$n,$Y));
        		}
        		if( $dia > 0 )
        		{
        			for($n=0; $n<5; $n++)
        				$fecha[$n] = date('Y-m-d', mktime(0,0,0,$M,$D-($dia-1)+$n,$Y));
        		}
        		
        		$calendariosnombre = $this->Calendario->query("SELECT * FROM boxes AS Box WHERE bit_vigente=1 ORDER BY Box.nom_box ASC");	
        		
                if( $n == 0 ) $calendariosnombre = array();	
				
				$encontrado = false;
        		$contador = 0;
        		$box_telefonico = false;
        		foreach($calendariosnombre as $a)
        		{
        			if( $a['Box']['cod_box'] == $cod_calendario )
        			{        			
        				$encontrado = true;
        				if( strcmp($a['Box']['tip_box'], 'Telefonico') == 0 )
        					$box_telefonico = true;
        			}
        			$contador++;
        		}
        		if( !$encontrado && $contador>0 )
        			$cod_calendario=$calendariosnombre[0]['Box']['cod_box'];
        		else
        		if( !$encontrado )
        			$cod_calendario=-1;

        		// ###########################################
        		//echo (microtime(true)-$tiempo_inicial)."<br>";
        		
                //$calendario = $this->Calendario->query("SELECT * FROM calendarios AS Calendario WHERE fec_fecha >= '".$fecha[0]."' AND fec_fecha <= '".$fecha[4]."'");
                $calendario = $this->Calendario->query("SELECT Calendario.cod_id, Calendario.cod_calendario, Calendario.cod_modulohorario, Calendario.cod_caso, Calendario.fec_fecha, Calendario.cod_dia, Voluntario.nom_login, Persona.nom_nombre, Persona.nom_appat, Persona.nom_apmat, Beneficiario.nom_nombre, Beneficiario.nom_appat, Beneficiario.nom_apmat FROM calendarios AS Calendario left join personas as Persona on Calendario.cod_persona = Persona.cod_persona left join casos as Caso on Calendario.cod_caso = Caso.cod_caso left join personas as Beneficiario on Caso.cod_beneficiario = Beneficiario.cod_persona left join voluntarios as Voluntario on Calendario.cod_persona = Voluntario.cod_persona WHERE Calendario.fec_fecha >= '".$fecha[0]."' AND Calendario.fec_fecha <= '".$fecha[4]."'");
                
                
                // ###########################################
        		//echo (microtime(true)-$tiempo_inicial)."<br>";
        		               
                if( !$calendario ) $calendario = array();

                //$voluntarios = $this->Voluntario->findAll("",array("Voluntario.cod_persona"),"", null, null, -1);
				$voluntarios = $this->Voluntario->findAll("",array("Voluntario.cod_persona","Persona.nom_nombre","Persona.nom_appat","Persona.nom_apmat"),"", null, null, 0);
                
                $indice=0;
                foreach($voluntarios as $voluntario)
                {
                	//$persona = $this->Persona->read(null, $voluntario['Voluntario']['cod_persona']);
                	//$voluntario['Voluntario']['nom_persona'] = $persona['Persona']['nom_nombre']." ".$persona['Persona']['nom_appat']." ".$persona['Persona']['nom_apmat'];
                	
                	$voluntario['Voluntario']['nom_persona'] = $voluntario['Persona']['nom_nombre']." ".$voluntario['Persona']['nom_appat']." ".$voluntario['Persona']['nom_apmat'];
                	
                	if( strlen(trim($voluntario['Voluntario']['nom_persona']))>0 )
                		$aux[$indice++] = $voluntario;
                }

                $voluntarios_aux = $aux;
           
                // Ordenar
  
                $aux = array();
                $voluntarios = array();
                
                for($n=0; $n<count($voluntarios_aux); $n++)
                   	$aux[$n] = $voluntarios_aux[$n]['Voluntario']['nom_persona']."#".$n;
                
                sort($aux);
                
                for($n=0; $n<count($aux); $n++)
                {
                	$tmp = split('#',$aux[$n]);
                	$voluntarios[$n]['Voluntario']['nom_persona'] = $voluntarios_aux[$tmp[1]]['Voluntario']['nom_persona'];
                	$voluntarios[$n]['Voluntario']['cod_persona'] = $voluntarios_aux[$tmp[1]]['Voluntario']['cod_persona'];
                }
				
                // los boxes telefï¿½nicos tienen sï¿½lo mï¿½dulo maï¿½ana, y tarde
                if( $box_telefonico )
                	$num_modulos = 2;
                else
                	$num_modulos = 9;
	
                // ###########################################
        		//echo (microtime(true)-$tiempo_inicial)."<br>";
	
                $indice = 0;
                $registros = array();
                
                foreach($calendario as $tupla)
                {
                	if( $tupla['Calendario']['cod_calendario'] == $cod_calendario )
                	{
                		//$voluntario = $this->Voluntario->read(null, $tupla['Calendario']['cod_persona']);

                		//$tupla['Calendario']['nom_login'] = $voluntario['Voluntario']['nom_login'];
                		$tupla['Calendario']['nom_login'] = $tupla['Voluntario']['nom_login'];
               		
                		//$caso = $this->Caso->read(null, $tupla['Calendario']['cod_caso']);
                		
                		//$tupla['Calendario']['caso'] = $caso;
                		
                		//$persona = $this->Persona->read(null, $tupla['Calendario']['cod_persona']);
                		
                		$tupla['Calendario']['nom_persona'] = trim( $tupla['Persona']['nom_nombre']." ".$tupla['Persona']['nom_appat']." ".$tupla['Persona']['nom_apmat'] );
                		
                		//$persona = $this->Persona->read(null, $caso['Caso']['cod_beneficiario']);
                		
                		$tupla['Calendario']['nom_beneficiario'] = trim( $tupla['Beneficiario']['nom_nombre']." ".$tupla['Beneficiario']['nom_appat']." ".$tupla['Beneficiario']['nom_apmat'] );
                		
                		$fecha_aux = $tupla['Calendario']['fec_fecha'];
                		
                		list($fecha_auxY,$fecha_auxM,$fecha_auxD)					= split('-', $fecha_aux );
                		list($fechaY,$fechaM,$fechaD) = split('-', $fecha[0] );
                		
                		$tupla['Calendario']['cod_dia'] = (int)( mktime(0,0,0,$fecha_auxM,$fecha_auxD,$fecha_auxY) - mktime(0,0,0,$fechaM,$fechaD,$fechaY) )/(60*60*24);
						
                		// En caso de modulo horario maï¿½ana-tarde, verificar que no se repitan
                		if( $num_modulos == 2 )
                		{
                			$encontrado = false;
                			for($n=0; $n<count($registros); $n++)
                			{
                				if( strcmp($registros[$n]['Calendario']['nom_persona'],$tupla['Calendario']['nom_persona']) == 0 && strcmp($registros[$n]['Calendario']['fec_fecha'],$tupla['Calendario']['fec_fecha']) == 0 && ((int)$registros[$n]['Calendario']['cod_modulohorario']) < 4 )
                					$encontrado = true;
                			}
          
                			if( !$encontrado )
                				$registros[$indice++] = $tupla;
                		}
                		else
                		{
                			$registros[$indice++] = $tupla;
                		}
						
                	}
                }

                // ###########################################
        		//echo (microtime(true)-$tiempo_inicial)."<br>";
                
        		//die();
                
                //$casos = $this->Caso->findAll();
                $casos = $this->Caso->findAll("",array("Caso.cod_beneficiario","Caso.cod_caso","Persona.nom_nombre","Persona.nom_appat","Persona.nom_apmat"),"", null, null, 0);
                
                // ###########################################
        		//echo (microtime(true)-$tiempo_inicial)."<br>";
                
				$tupla = array(array(), array(), array());
				$n=0;
                foreach($casos as $caso)
                {
                	$tupla[$n] = $caso;
                	//$persona = $this->Persona->read(null, $caso['Caso']['cod_beneficiario']);
                	//$tupla[$n]['Caso']['nom_beneficiario'] = trim( $persona['Persona']['nom_nombre']." ".$persona['Persona']['nom_appat']." ".$persona['Persona']['nom_apmat'] );
                	$tupla[$n]['Caso']['nom_beneficiario'] = trim( $caso['Persona']['nom_nombre']." ".$caso['Persona']['nom_appat']." ".$caso['Persona']['nom_apmat'] );
                	$n++;
                }	
                
                // ###########################################
        		//echo (microtime(true)-$tiempo_inicial)."<br>";
				
                $casos = $tupla;
                
                // Ordenar
                
  				$casos_aux = $casos;
                $aux = array();
                $casos = array();
                
                for($n=0; $n<count($casos_aux); $n++)
                   	$aux[$n] = $casos_aux[$n]['Caso']['nom_beneficiario']."#".$n;
                
                sort($aux);
                
                for($n=0; $n<count($aux); $n++)
                {
                	$tmp = split('#',$aux[$n]);
                	$casos[$n]['Caso']['nom_beneficiario'] = $casos_aux[$tmp[1]]['Caso']['nom_beneficiario'];
                	$casos[$n]['Caso']['cod_caso'] = $casos_aux[$tmp[1]]['Caso']['cod_caso'];
                }
 
                $dias = array('Lunes'.'<br><span style="font-size:8pt">'.(substr($fecha[0],8)).'</span>','Martes'.'<br><span style="font-size:8pt">'.(substr($fecha[1],8)).'</span>','Mi&eacute;rcoles'.'<br><span style="font-size:8pt">'.(substr($fecha[2],8)).'</span>','Jueves'.'<br><span style="font-size:8pt">'.(substr($fecha[3],8)).'</span>','Viernes'.'<br><span style="font-size:8pt">'.(substr($fecha[4],8)).'</span>');
                
                $this->set('registros', $registros);
                $this->set('voluntarios', $voluntarios);
                $this->set('cod_calendario', $cod_calendario);
                $this->set('calendariosnombre', $calendariosnombre);
                $this->set('casos', $casos);
                $this->set('fecha', $fecha);
                $this->set('dias', $dias);
                $this->set('num_modulos',$num_modulos);
                                
                // ###########################################
        		//echo (microtime(true)-$tiempo_inicial)."<br>";
        		
                //die();
        }
        
        
        
        
        
        
        function agregar() {

        	$cod_persona = $_POST['select_'.$_POST['form_code']];
        	
        	if( isset($_POST['select2_'.$_POST['form_code']]) )
        		$cod_caso = $_POST['select2_'.$_POST['form_code']];
        	else
        		$cod_caso = "";
        	
        	
        	//die();
        	list($cod_calendario, $fec_fecha, $cod_modulohorario) = split("_",$_POST['form_code']);

          	$fecha = substr($fec_fecha,0,4)."-".substr($fec_fecha,4,2)."-".substr($fec_fecha,6,2);
        
        	$calendarios = $this->Calendario->query("SELECT * FROM calendarios WHERE cod_calendario='".$cod_calendario."' AND fec_fecha='".$fecha."' AND cod_modulohorario='".$cod_modulohorario."' AND cod_persona='".$cod_persona."' ");
        
        	$n = 0;
        	foreach( $calendarios as $calendario )
        		$n++;
		
        	// Evitamos duplicaciones
        	if( $n == 0 )
        	{
        		$this->data['Calendario']['cod_calendario']		= $cod_calendario;
        		$this->data['Calendario']['fec_fecha']			= substr($fec_fecha,0,4)."-".substr($fec_fecha,4,2)."-".substr($fec_fecha,6,2);
        		$this->data['Calendario']['cod_modulohorario']	= $cod_modulohorario;
        		$this->data['Calendario']['cod_estado']			= 1;
        		$this->data['Calendario']['cod_persona'] 		= $cod_persona;
        		$this->data['Calendario']['cod_caso'] 			= $cod_caso;
        		$this->Calendario->save($this->data);
	
        	}

        	$this->redirect('/calendarios/index/'.$cod_calendario.'/'.$this->data['Calendario']['fec_fecha']);
        
        }
        
        
        
        
        
        
        
        function activar($cod_id=null) {
        	if( $cod_id != null )
        	{
        		$calendario = $this->Calendario->read(null, $cod_id);
        		
        		$this->data['Calendario']['cod_id']				= $cod_id;
        		$this->data['Calendario']['cod_calendario']		= $calendario['Calendario']['cod_calendario'];
        		$this->data['Calendario']['fec_fecha']			= $calendario['Calendario']['fec_fecha'];
        		$this->data['Calendario']['cod_modulohorario']	= $calendario['Calendario']['cod_modulohorario'];
        		$this->data['Calendario']['cod_estado']			= "1";
        		$this->data['Calendario']['cod_persona'] 		= $calendario['Calendario']['cod_persona'];
        		$this->data['Calendario']['cod_caso']			= $calendario['Calendario']['cod_caso'];
        		
        		$this->Calendario->save($this->data);
        	}
        	
        	$this->redirect('/calendarios/index/'.$calendario['Calendario']['cod_calendario']."/".$this->data['Calendario']['fec_fecha']);
        }
        
        
        
        
        
        
        
        
        
        
        
        
        
        function desactivar($cod_id=null) {
        	if( $cod_id != null )
        	{
        		$calendario = $this->Calendario->read(null, $cod_id);
        		
        		$this->data['Calendario']['cod_id']				= $cod_id;
        		$this->data['Calendario']['cod_calendario']		= $calendario['Calendario']['cod_calendario'];
        		$this->data['Calendario']['fec_fecha']			= $calendario['Calendario']['fec_fecha'];
        		$this->data['Calendario']['cod_modulohorario']	= $calendario['Calendario']['cod_modulohorario'];
        		$this->data['Calendario']['cod_estado']			= "0";
        		$this->data['Calendario']['cod_persona'] 		= $calendario['Calendario']['cod_persona'];
        		$this->data['Calendario']['cod_caso']			= $calendario['Calendario']['cod_caso'];
        		
        		$this->Calendario->save($this->data);
        	}
        	
        	$this->redirect('/calendarios/index/'.$calendario['Calendario']['cod_calendario']."/".$this->data['Calendario']['fec_fecha']);
        }
        
        
        
        
        
        
        
        
        
        
        
        
        
        function borrar($cod_id=null, $cod_calendario) {
        	
        	//die();
        
        	if( $cod_id != null )
        	{
        		$calendario = $this->Calendario->read(null, $cod_id);
        		$this->data['Calendario']['fec_fecha']			= $calendario['Calendario']['fec_fecha'];
        		$this->Calendario->del($cod_id);
        	}
        	
        	$this->redirect('/calendarios/index/'.$cod_calendario."/".$this->data['Calendario']['fec_fecha']);
        
        }
        
        
        
        
        
        
        
        function limpiar() {
        	list($cod_calendario, $fec_fecha) = split("_",$_POST['form_code']);
          	$fecha_entrada = substr($fec_fecha,0,4)."-".substr($fec_fecha,4,2)."-".substr($fec_fecha,6,2);
          	
    		list($Y,$M,$D) = split('-', $fecha_entrada );
        		
        	// domingo = 0, sabado = 6
        	$dia = date('w', mktime(0,0,0,$M,$D,$Y));
        		       		
        	if( $dia == 0 )
        	{
        		for($n=0; $n<5; $n++)
        			$fecha[$n] = date('Y-m-d', mktime(0,0,0,$M,$D+1+$n,$Y));
        	}
        	if( $dia > 0 )
        	{
        		for($n=0; $n<5; $n++)
        			$fecha[$n] = date('Y-m-d', mktime(0,0,0,$M,$D-($dia-1)+$n,$Y));
        	}
          	
          	
          	
          	
          	// validar datos
        	if( 1 ) 
        	{
        		$this->Calendario->query("DELETE FROM calendarios WHERE cod_calendario='".$cod_calendario."' AND fec_fecha >= '".$fecha[0]."' AND fec_fecha <= '".$fecha[4]."'");
        		$this->redirect('/calendarios/index/'.$cod_calendario.'/'.$fecha[0]);
        	}
        	else
        	{
        		$this->redirect('/calendarios/index/'.$cod_calendario);
        	}
          	
        }
        
        
        
        
        function replicar() {
        
        
        

          	list($cod_calendario, $fec_fecha) = split("_",$_POST['form_code']);
          	$fecha_entrada = substr($fec_fecha,0,4)."-".substr($fec_fecha,4,2)."-".substr($fec_fecha,6,2);
        
          	// convertimos código calendario de acuerdo al usado en turnos
          	/*	Calendarios:
          	 * 	1 - Clínico
          	 * 	6 - Telefónico (Acoge)
          	 * 	7 - Telefónico (Comunícate)
          	 * 	8 - No psicológico
          	 * 
          	 * 	Turnos:
          	 * 	1 - Clínico
          	 *  7 - Telefónico (Acoge)
          	 *  7 - Telefónico (Comunícate)
          	 *  0 - No psicológico
          	 */
          	
          	if( $cod_calendario == 1 )
          		$cod_box = 1;
          	if( $cod_calendario == 6 )
          		$cod_box = 7;
          	if( $cod_calendario == 7 )
          		$cod_box = 7;
          	if( $cod_calendario == 8 )
          		$cod_box = 0;
          		
          	

            // validar datos
        	if( 1 ) 
        	{
        		// CUIDADO con SQL INJECTION
        		if( ereg("^[0-9]+$",$cod_calendario) == false  )
        			$this->redirect('/calendarios/index/'.$cod_calendario."/".$fecha_entrada);

        			
        	    list($Y,$M,$D) = split('-', $fecha_entrada );
        		
        	   // die();
        	    
        		// domingo = 0, sabado = 6
        		$dia = date('w', mktime(0,0,0,$M,$D,$Y));
        		       		
        		if( $dia == 0 )
        		{
        			for($n=0; $n<5; $n++)
        				$fecha[$n] = date('Y-m-d', mktime(0,0,0,$M,$D+1+$n,$Y));
        		}
        		if( $dia > 0 )
        		{
        			for($n=0; $n<5; $n++)
        				$fecha[$n] = date('Y-m-d', mktime(0,0,0,$M,$D-($dia-1)+$n,$Y));
        		}

       		
        		//$resultados = $this->Calendario->query("SELECT * FROM turnos WHERE cod_box='".$cod_calendario."'");
        		//$casos = $this->Caso->findAll("",array("Caso.cod_beneficiario","Caso.cod_caso","Persona.nom_nombre","Persona.nom_appat","Persona.nom_apmat"),"", null, null, 0);
        		
        		//$resultados = $this->Calendario->query("SELECT * FROM turnos as Turno LEFT JOIN voluntarios as Voluntario on Turno.cod_voluntario = Voluntario.cod_persona WHERE Turno.cod_box='".$cod_box."'");
        		
        		$query = "SELECT * FROM turnos as Turno LEFT JOIN voluntarios as Voluntario on Turno.cod_voluntario = Voluntario.cod_persona WHERE Turno.cod_box='".$cod_box."'";
        		if( $cod_calendario == 6 )
        			$query .= " AND Voluntario.cod_programa = '1'";
          		else
        		if( $cod_calendario == 7 )
        			$query .= " AND Voluntario.cod_programa = '2'";					
					
        		$resultados = $this->Calendario->query($query);	
        		
        		$contador=0;
        		foreach($resultados as $resultado)
        		{
        		
        			//$this->data['Calendario']['cod_calendario']		= $cod_calendario;
        			
        			$num_dia = 0;
//        			if( strcmp($resultado['turnos']['nom_dia'],'Lunes') == 0 )
//        				$num_dia = 10;
        			if( strcmp($resultado['Turno']['nom_dia'],'Martes') == 0 )
        				$num_dia = 1;
        			if( strcmp($resultado['Turno']['nom_dia'],'Miercoles') == 0 )
        				$num_dia = 2;
        			if( strcmp($resultado['Turno']['nom_dia'],'Jueves') == 0 )
        				$num_dia = 3;
        			if( strcmp($resultado['Turno']['nom_dia'],'Viernes') == 0 )
        				$num_dia = 4;
        			
        			$fec_fecha = date('Y-m-d', mktime(0,0,0,substr($fecha[0],5,2),((int)substr($fecha[0],8,2))+$num_dia,substr($fecha[0],0,4)));	      			      			
        			
        			$this->data['Calendario']['fec_fecha']			= $fec_fecha;

        			$this->data['Calendario']['cod_modulohorario']	= ($resultado['Turno']['num_hora']-9);
        			$this->data['Calendario']['cod_estado']			= 1;
        			
        			$this->data['Calendario']['cod_persona'] 		= $resultado['Turno']['cod_voluntario'];

    				$this->data['Calendario']['cod_caso'] 			= $resultado['Turno']['cod_caso'];

        			
        			//$cod_calendario 	= $this->data['Calendario']['cod_calendario'];
        			        			
        			$cod_modulohorario 	= $this->data['Calendario']['cod_modulohorario'];
        			$cod_estado 		= $this->data['Calendario']['cod_estado'];
        			$cod_persona 		= $this->data['Calendario']['cod_persona'];
        			$fec_fecha 			= $this->data['Calendario']['fec_fecha'];
					$cod_caso 			= $this->data['Calendario']['cod_caso'];

        			// Evitamos duplicados
        			$contador2=0;
        			$tmp = $this->Calendario->query("SELECT * FROM calendarios WHERE cod_calendario='".$cod_calendario."' AND cod_modulohorario='".$cod_modulohorario."' AND cod_persona='".$cod_persona."' AND fec_fecha='".$fec_fecha."'");
        			foreach($tmp as $a)
        				$contador2++;
        			
        			$query = "INSERT INTO calendarios ( cod_calendario, cod_modulohorario, cod_estado, cod_persona, fec_fecha, cod_caso) VALUES ('".$cod_calendario."','".$cod_modulohorario."','".$cod_estado."','".$cod_persona."','".$fec_fecha."','".$cod_caso."')";
        			//echo $query."<br>";
        			if($contador2==0)
        				$this->Calendario->query($query);
        			
        			$contador++;
        		}      	
        		
        		//echo $contador;
        		//die();
        		
        		$this->redirect('/calendarios/index/'.$cod_calendario."/".$fecha_entrada);
        	}
        	else
        	{
        		$this->redirect('/calendarios/index/'.$cod_calendario);
        	}
        
        }
        

        
}
?>