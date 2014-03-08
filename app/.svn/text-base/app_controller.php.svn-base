<?php
class AppController extends Controller {

// este controlador usa el modelo Voluntario (si quiero mï¿½s de uno puedo incluirlo como arreglo).
// ej: var $uses = array('modelo1','modelo2');

    var $uses = array('Sesion', 'Permisovoluntario', 'Permiso', 'Voluntario', 'Encuesta', 'EncuestaRespondida');
    var $components = array('Session');
    var $helpers = array('Javascript', 'Html', 'Ajax', 'Form', 'Jsvalid', 'Permisoschecker', 'Fichas', 'Textimage');

    function beforeFilter() {

    // Primer filtro: CheckSession. Si pasa continua. Sino se va al login.

        $this->checkSession();

        // Segundo filtro: CheckPermiso. Si pasa continua. Sino se va al home.

        $this->checkPermiso();
    }


    // FUNCION GENERICA QUE CHEQUEA QUE EFECTIVAMENTE EL USUARIO ESTE LOGUEADO:

    function checkSession() {


    // poner en $username la data de la sesion

        $username = $this->Session->read('user');

        // Si $username estaï¿½ vacio redirecciono al login:

        if (!$username) {
            $this->redirect('/?non_fill=1');
            exit();

        } else {

        // si el $username no esta vacio,
        // reviso que estacorrecto. Si esta OK simplemente pasa. Sino, destruye la sesion y deriva al login:

            $results = $this->Voluntario->findByNomLogin($username, "", "", "", "", -1);

            // si no estï¿½ correcto, redirecciono al login:
            if(!$results) {
                $this->Session->destroy();
                $this->redirect('/?sess_err=1');
                exit();
            }


            // si esta correcto, le cuento a la BD que hubo movimiento del usuario.
            // de esta forma, cada vez que chequee sesion, guardara en el campo fec_fin la fecha y hora de
            // la ultima actividad del usuario. Con esto, mantengo un registro de las actividades del usuario.
            // bonito, o no?
            $cod_voluntario = $this->Session->read('cod_voluntario');
            $cod_sesion = $this->Session->read('cod_sesion');

            $sql =  "UPDATE `sesiones`
				  	SET `fec_fin` = CURRENT_TIMESTAMP
				  	WHERE `sesiones`.`cod_voluntario` = '$cod_voluntario'
				  	AND  `sesiones`.`cod_sesion` = '$cod_sesion'
					LIMIT 1
                ";

            $this->Sesion->query($sql);
        }
    }


    //funcion que valida que el usuario tiene los permisos para acceder a una pagina

    function checkPermiso() {
    //busco al voluntario
        $cod_voluntario = $this->Session->read('cod_voluntario');
        //obtencion del controlador
        $controller=$this->params['controller'];
        //obtencion del action
        $action=$this->params['action'];

        //booleano que dice si este action es o no de modificacion
        $modifica=false;

        //filtros dependiendo si se busca por controller/action, controller/action de modificacion o controller y cualquier action

        $filtro_action=array('Permiso.nom_controller'=>$controller, 'Permiso.nom_action'=>$action);
        $filtro_modificar=array('Permiso.nom_controller'=>$controller, 'Permiso.nom_actionmodifica'=>$action);
        $filtro_controller=array('Permiso.nom_controller'=>$controller, 'Permiso.nom_action'=>'');

        //busca el permiso especifico del action (ver)

        $permiso=$this->Permiso->find($filtro_action, "", "", "", "", -1);


        //si no lo encontro busca un permiso de modificacion con el controller/action asociado

        if(!is_array($permiso)) {
            $permiso=$this->Permiso->find($filtro_modificar, "", "", "", "", -1);
            if(is_array($permiso)) {
                $modifica=true;
            }
        }

        //si no hay uno especifico, busca uno general para el controller

        if(!is_array($permiso)) {
        //coincidencia del controller
            $permiso=$this->Permiso->find($filtro_controller, "", "", "", "", -1);
        }
        //si en una de las dos busquedas encontro un permiso hace la validacion con el usuario
        if(is_array($permiso)) {
        //filtro para el permiso encontrado
            $filtro_permiso=array('Permisovoluntario.cod_permiso'=>$permiso['Permiso']['cod_permiso'], 'Permisovoluntario.cod_voluntario'=>$cod_voluntario);

            //si el permiso era de controller/action de modificacion debe estar en 1 el bit_modifica
            if($modifica) {
                $permisos=$this->Permisovoluntario->find($filtro_permiso+array('bit_modifica'=>1), "", "", "", "", -1);
            }
            else {
                $permisos=$this->Permisovoluntario->find($filtro_permiso, "", "", "", "", -1);
            }
            //si no encuentra el permiso, o no es alguna de las vistas de Mi Perfil
            if(!is_array($permisos) &&
                !($controller=="personas" &&
                (($action=="ver" && $cod_voluntario==$this->params['pass'][0]) ||
                $action=="modificar" || $action=="modificar2")) ) {
                $this->redirect('/menu/error');
                exit();
            }
        }

        // Revisar si el usuario tiene encuestas por responder y molestarlo
        if(strpos($controller, 'encuestas')===false) {
            $encuestas_respondidas = $this->EncuestaRespondida->findAll(array("usuario_id='$cod_voluntario'"), array("encuesta_id"));
            $ids = '';
            foreach($encuestas_respondidas as $encuesta) {
                $ids .= $encuesta["EncuestaRespondida"]['encuesta_id'] . ',';
            }

            if($ids == '') {
                $encuestas = $this->Encuesta->findAll(array("habilitada=1 AND fecha_fin <= CURRENT_DATE"), array("id"));
            } else {
                $ids = substr($ids, 0, -1);
                $encuestas = $this->Encuesta->findAll(array("id NOT IN ($ids) AND habilitada=1 AND fecha_fin <= CURRENT_DATE"), array("id"));
            }

            if($encuestas) {
            // Tiene que responder...
                $this->redirect('/respuestaencuestas/mostrar?id_encuesta='.$encuestas[0]["Encuesta"]['id'].'&redirigido=true');
            }
        }
    }

    function escribirHeader($titulo_pagina) {

        $value=$this->Session->read('nombre_completo');
        $value2=$this->Session->read('nrovisitas');
        $value3=$this->Session->read('programa');

        $this->set('name_for_layout',$value);
        $this->set('nrovisitas_for_layout',$value2);
        $this->set('programa_for_layout',$value3);
        $this->set('titulo_pagina',$titulo_pagina);
    }
}
?>
