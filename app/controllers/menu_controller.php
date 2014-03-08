<?php
class MenuController extends AppController {
    var $name = "Menu";
    var $uses = array('Voluntario', 'Persona', 'Sesion', 'Programa');

    //se pone la funcion en blanco para sobreescribir la de app_controller
    //NO BORRAR
    function beforeFilter() {
    }

    function index() {
    // Primer filtro: CheckSession. Si pasa continÃºa. Sino se va al login.
        $this->checkSession();

        // Se mandan algunas variables al layout para que se vea bien COPIAR SIEMPRE Y CAMBIAR NOMBRE PAGINA
        $this->escribirHeader("Inicio");

        $this->set('noticias',$this->requestAction('/Noticias/mostrarInicio', array('return')));
        $this->set('foro', $this->requestAction('/Forums/mostrarInicio', array('return')));
        $this->set('avisos',$this->requestAction('/Avisos/mostrarInicio', array('return')));
        $this->set('indicadores',$this->requestAction('/Indicador/MostrarInicio'));
		$this->set('encuestas', $this->requestAction('/Encuestas/mostrarInicio', array('return')));
    }

    function login() {
        $this->escribirHeader("Inicio");
        $this->set('titulo_pagina',"Inicio");
        $this->set('prueba',"Inicio");

        $login = $this->data['FormLogin']['nom_login'];
        $pass = $this->data['FormLogin']['pas_voluntario'];

        // [Javier] chequeo el nombre de usuario agregado y lo comparo con la BD
        $results = $this->Voluntario->findByNomLogin($login);

        // [Javier] Si el voluntario no estÃ¡ activo, no permito la entrada al sistema
        if( $results['Voluntario']['est_voluntario'] != 'Activo' ) {
            $this->redirect('/?inact=1');
            $this->exit();
        }

        // [Javier] chequeo que $results no sea nula y comparo el pass ingresado en
        // el formulario con el de la tupla de la BD


        if ($results && $results['Voluntario']['pas_voluntario'] == md5($pass)) {

            require_once WWW_ROOT.'/PHPBB_Login.php';

            // seteo la sesiï¿½n de usuario con el nombre de usuario:
            $this->Session->write('user', $login);
            $phpbbLogin = new PHPBB_Login();
            $phpbbLogin->login($results['Voluntario']['cod_persona'],$login ,$pass, $results['Voluntario']['est_rol']);

            // *** INSERCION DE TUPLA DE SESION ACï¿½ ***

            // obtengo el mï¿½ximo nï¿½mero del cï¿½digo de la sesiï¿½n y le
            // incremento uno para insertarlo nuevamente.
            $sql = "SELECT MAX(cod_sesion) AS max
						FROM sesiones";
            $resultado = $this->Sesion->query($sql);

            $cod_voluntario = $results['Voluntario']['cod_persona'];
            $cod_programa = $results['Voluntario']['cod_programa'];

            $cod_sesion = $resultado[0][0]['max'] + 1;

            // JAVIER DICE: DADO QUE NO HE PODIDO APRENDER Cï¿½MO INSERTAR TUPLAS
            // NUEVAS CON SAVE() LO HAGO A LA MALA
            // Ojo: la hora de logout la asigno como la hora actual (CURRENT_TIMESTAMP) mï¿½s
            // 1 segundo. Esto quedarï¿½ asï¿½ si es que el usuario no hizo absolutamente nada
            // una vez logueado y se saliï¿½ del sistema
            // de alguna forma no convencional (yï¿½ndose a otra web por URL, cerrando la ventana
            // pelusonamente u otras fechorï¿½as de ese calibre).

            $sql = "
						INSERT INTO `sesiones` (
						`cod_sesion` ,
						`cod_voluntario` ,
						`fec_inicio` ,
						`fec_fin`
						)
						VALUES (
						'$cod_sesion', '$cod_voluntario',
						CURRENT_TIMESTAMP , ADDTIME(CURRENT_TIMESTAMP, '00:00:01')
						);
                ";

            $this->Sesion->query($sql);


            //guardo en Session el cï¿½digo de la persona y de sesiï¿½n.
            $this->Session->write('cod_voluntario', $cod_voluntario);
            $this->Session->write('cod_sesion', $cod_sesion);

            // Para poner el nombre completo de la persona en la variable session, busco en la BD la tupla:
            $results2 = $this->Persona->findByCodPersona($cod_voluntario);

            if($results2) {
            // Concateno el nombre completo con los datos obtenidos
                $nombre_completo = $results2['Persona']['nom_nombre']." ".
                    $results2['Persona']['nom_appat']." ".
                    $results2['Persona']['nom_apmat'] ;

                // Grabo todo en la variable Session, para no perder la info mientras navego:
                $this->Session->write('nombre_completo', $nombre_completo);


                // Agarro la cantidad de veces que estï¿½ conectada: count() where cod_voluntario = ...
                $nrovisitas = $this->Sesion->findCount('cod_voluntario='.$cod_voluntario,0);

                $this->Session->write('nrovisitas', $nrovisitas);
                $this->set('nrovisitas_for_layout',$nrovisitas);
                $this->set('name_for_layout',$nombre_completo);

                // Info de programa

                $programa = $this->Programa->find(array("Programa.cod_programa"=> $cod_programa ));
                $this->Session->write('programa', $programa['Programa']['nom_programa']);

                $this->set('programa_for_layout', $programa['Programa']['nom_programa']);
                $this->redirect('/menu');
            }

            else {
                $this->Session->write('name_for_layout', "Error: persona desconocida");
            }

        // Una vez aprobado, termina y se va al index
        // (definido como destino en el formulario de display.thtml):

        }

        else {

        // Si estoy aquÃ­ es porque el login no se encuentra en la BD o bien la clave es incorrecta

            if($login=="" && $pass=="") { $this->redirect('/?non_fill=1'); }
            else $this->redirect('/?inc_user=1');

            $this->exit();
        }

    }

    function moreoptions() {
    // Primer filtro: CheckSession. Si pasa continï¿½a. Sino se va al login.
        $this->checkSession();

        // Se mandan algunas variables al layout para que se vea bien COPIAR SIEMPRE Y CAMBIAR NOMBRE PAGINA
        $this->escribirHeader("MÃ¡s Opciones");
    }
    // Funciï¿½n para desloguearme del sistema:
    function logout() {



    // guardo en la tupla de la sesiï¿½n la hora de logout (que es el CURRENT_TIMESTAMP):

        $cod_voluntario = $this->Session->read('cod_voluntario');
        $cod_sesion = $this->Session->read('cod_sesion');

        // [Javier] Se desasignan todos los seguimientos agendados para hoy:
			/*
			$sql_desagendar = "
				
				UPDATE `seguimientos`
				SET  cod_voluntarioproxrevision = NULL
				WHERE fec_proxrevision = date(CURRENT_TIMESTAMP)
				AND tip_proxrevision = 'Llamada'
				AND cod_voluntarioproxrevision = ".$cod_voluntario.";
			
			";
			
			$this->Sesion->query($sql_desagendar);
			*/

        // actualizo la tupla correspondiente al login del usuario en la tabla sesiones
        $sql =  "UPDATE `sesiones`
				  	SET `fec_fin` = CURRENT_TIMESTAMP 
				  	WHERE `sesiones`.`cod_voluntario` = '$cod_voluntario'
				  	AND  `sesiones`.`cod_sesion` = '$cod_sesion'
					LIMIT 1
            ";

        $this->Sesion->query($sql);

        // Mato la sesiï¿½n...
        $this->Session->destroy();

        require_once WWW_ROOT.'/PHPBB_Login.php';
        $phpbbLogin = new PHPBB_Login();

        $phpbbLogin->login('-1','Anonymous' ,'','' );




        // ... y redirecciono a pages/display con mensaje de logout.
        $this->redirect('pages/display/?logout=1', null, true);

    }

    function error() {
    // Se mandan algunas variables al layout para que se vea bien COPIAR SIEMPRE Y CAMBIAR NOMBRE PAGINA
        $this->escribirHeader("Error de Permisos");
    }
}
?>
