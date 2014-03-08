<?php

	class NoticiasController extends AppController
	{
		var $name = "Noticias";

		var $uses = array('Noticia','Voluntario'); //MODELOS
		var $components = array ('Pagination','Session'); //COMPONENTES

        var $helpers = array('Html','Text','Pagination','Ajax'); //HELPERS

        function beforeFilter(){
        	$conditions = array("cod_persona" => $this->Session->read("cod_voluntario"));
			$voluntario = $this->Voluntario->find($conditions,null,null,-1);
			if($voluntario["Voluntario"]["est_rol"]=="Administrativo")
			{
				$this->set("permitir_editar",true);
				$this->set("permitir_eliminar",true);
				$this->set("permitir_agregar",true);
			}
			else
			{
				$this->set("permitir_editar",false);
				$this->set("permitir_eliminar",false);
				$this->set("permitir_agregar",false);
			}
        }

        function autocomplete ()
		{
			//echo $this->data['Buscador1']['search_text'];
		    $this->set('noticias',
		        $this->Noticia->findAll(
		           array("Noticia.titulo"=> "LIKE ".$this->data['Buscador']['search_text']."%") )
		        );


		    $this->layout = "ajax";
		}

        /**
         * Index del controlador
         *
         */
		function index(){
			//PONER SIEMPRE
			$this->set("desde","index");
            $this->escribirHeader("Noticias");
            $conditions = null;
            list($order,$limit,$page) = $this->Pagination->init($conditions); //INICIALIZACION DEL PAGINADOR
            $order = "Noticia.fecha_creacion DESC";

            $this->set('noticias',$this->Noticia->findAll($conditions, null, $order, $limit, $page,2));

        }
        /**
         * Funcion que verifica si una fecha ingresada en un formulario es valida
         *
         * @param unknown_type $fecha
         * @param unknown_type $nombre
         * @return array()
         * Donde
         * 	El primer valor es un string_type
         *  El segundo es int_type, numero de errores
         *
         */
        private function _isDate($fecha,$nombre){
        	$patron = "/^\d{2}-\d{2}-\d{4}$/";
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
             			if(checkdate($arrayFecha[1],$arrayFecha[0],$arrayFecha[2]))
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
         * Busqueda basica por un campo
         *
         */
        function search($render_index = true){
        	//PONER SIEMPRE
        	$this->escribirHeader("Noticias");
            $this->set("desde","search");
            //verificamos si exist o no una busqueda

        	if($this->data==null)
            {
            	//si no es por que se accede a la pagina a traves del paginador
            	$conditions = null;
            	//cargamos las condiciones
            	if($this->Session->check("noticias_conditions"))
            	{
            		$conditions = $this->Session->read("noticias_conditions");
            	}
            	//iniciamos el paginador
            	list($order,$limit,$page) = $this->Pagination->init($conditions); // Added
                $order = "Noticia.fecha_creacion DESC";
            	//setiamos las noticias
                $this->set('noticias',$this->Noticia->findAll($conditions, null, $order, $limit, $page,2));
            	$this->set("no_mostrar_feedback",true);
            }
            else{
            	//Vemos si el post coincide
	        	if(array_key_exists("Buscador", $this->data)){
	                if(array_key_exists("search_text", $this->data["Buscador"])){
	             		$this->set("search_text",$this->data["Buscador"]['search_text']);
	                	$this->set("fecha1",$this->data["Buscador"]['fecha_1']);
	                	$this->set("fecha2",$this->data["Buscador"]['fecha_2']);
	                	//echo 1;
	                	//creamos las condiciones de busqueda
	             		//busqueda por titulo, bajada y contenido


	             		$fecha1 = $this->data["Buscador"]['fecha_1'];
	             		$fecha2 = $this->data["Buscador"]['fecha_2'];
	             		$result1 = $this->_isDate($fecha1,'desde');
	             		$result2 = $this->_isDate($fecha2,'hasta');

	             		$errores = $result1[1]+$result2[1];
	             		$error = $result1[0]." ".$result2[0];
	             		if($errores==0)
	             		{
	             			$conditions =
		                        array("or"=>
		                            array(
		                                "Noticia.titulo"=>"LIKE %".$this->data["Buscador"]['search_text']."%",
		                                "Noticia.bajada"=>"LIKE %".$this->data["Buscador"]['search_text']."%",
		                                "Noticia.contenido"=>"LIKE %".$this->data["Buscador"]['search_text']."%"
		                                )
		                            )
		                    	;
	                    	$arrayFecha1 = explode("-",$fecha1);

	                    	$arrayFecha2 = explode("-",$fecha2);

	                    	if($fecha1!="")
	             				$conditions["Noticia.fecha_creacion"] = ">=". $arrayFecha1[2]."-".$arrayFecha1[1]."-".$arrayFecha1[0]." 00:00:00";
	             			if($fecha2!="")
	             				$conditions["Noticia.fecha_creacion"] = "<=". $arrayFecha2[2]."-".$arrayFecha2[1]."-".$arrayFecha2[0]." 23:59:59";
							if($fecha1!="" && $fecha2!=""){
								$conditions["and"] = array("Noticia.fecha_creacion"
												=>">=". $arrayFecha1[2]."-".$arrayFecha1[1]."-".$arrayFecha1[0]." 00:00:00");
								$conditions["Noticia.fecha_creacion"] =
												"<=". $arrayFecha2[2]."-".$arrayFecha2[1]."-".$arrayFecha2[0]." 23:59:59";
							}


	             		}
	             		else{
	             			$this->Session->setFlash ($error);
	             			$conditions = null;
	             		}

	                    //la almacenamos en la sesion
	                    $this->Session->write("noticias_conditions",$conditions);
	                    //inciamos el paginador
	                    list($order,$limit,$page) = $this->Pagination->init($conditions); //
	                    $order = "Noticia.fecha_creacion DESC";
	                    //setiamos las noticias
	                    //print_r($conditions) ;
	                    $this->set('noticias',$this->Noticia->findAll($conditions,null,$order,$limit,$page,2));

	                }
	            }
            }
            //usamos la vista del index
            if( $render_index )
            {
	            $this->render("index");
        	}
        }

        /**
         * View noticia
         *
         */
        function view($desde,$cod_noticia){
        	$this->escribirHeader ("Noticias");
        	$this->set("desde",$desde);

        	$conditions = array("cod_noticia"=>$cod_noticia);
        	$noticia = $this->Noticia->find($conditions);
        	$this->set("noticia",$noticia);
        }
        /**
         * Editar Noticia
         *
         */
        function edit($index = null) {
        	$this->escribirHeader ('Editar noticia');

			if (empty ($this->data)) {
				if ($index == null) {
					$this->Session->setFlash ('Index de noticia invalido');
				} else {
					$this->data = $this->Noticia->read (null, $index);
					$this->render ();
				}
			} else {
				$this->cleanUpFields ();
				$this->data['Noticia']['cod_persona'] = $this->Session->read('cod_voluntario');

				if ($this->Noticia->save($this->data)) {
					$this->Session->setFlash ('Noticia guardada');
					$this->redirect ('/noticias/index');
				} else {
					$this->Session->setFlash ('Por favor corrija los errores abajo');
					$this->render ();
				}
			}
        }

        /**
         * Agregar noticia
         *
         */

		function add () {
			$this->escribirHeader ('Gestión de noticias');
			if (empty ($this->data)) {
				$this->render ();
			} else {
				$this->cleanUpFields ();
				$this->data['Noticia']['cod_persona'] = $this->Session->read('cod_voluntario');

				if ($this->Noticia->save ($this->data)) {
					$this->Session->setFlash ('Noticia guardada');
					$this->redirect ('/noticias/index');
				} else {
					$this->Session->setFlash ('Por favor corrija los errores abajo');
					$this->render ();
				}
			}
		}

		function delete ($index = null) {
			$this->escribirHeader ('Eliminar noticia');
			if($index == null) {
				$this->Session->setFlash ('Index de noticia invalido');
			} else {
				if ($this->Noticia->del($index)) {
					$this->Session->setFlash ('Noticia eliminada');
					$this->redirect ('/noticias/index');
				} else {
					$this->Session->setFlash ('Impossible de eliminar la noticia');
				}
			}
		}

        /*
         * Envia las ultimas noticias al la página de inicio o  a la que la llame
         */
        function mostrarInicio()
        {
            $this->set("desde","home");
            $this->escribirHeader("Noticias");
            $conditions = null;
            $limit = 4;
            $order = "Noticia.fecha_creacion DESC";
            $noticias = $this->Noticia->findAll($conditions, null, $order, $limit, null,2);
            $ultima_noticia = Array();
            $noticias_siguientes = Array();
            if(count($noticias)!=0)
            {
                $ultima_noticia = $noticias[0];
                if(count($noticias)>1)
                {
                    for($i = 1;$i < count($noticias);$i++)
                    {
                        $noticias_siguientes[$i-1] = $noticias[$i];
                    }
                }
                $this->set('ultima_noticia',$ultima_noticia);
                $this->set('noticias_siguientes',$noticias_siguientes);
            }

            $this->set('noticias',$noticias);

        }

        }


?>
