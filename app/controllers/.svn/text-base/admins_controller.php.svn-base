<?php
class AdminsController extends AppController {
    var $name = "Admins";
    
    function index() {

        $this->escribirHeader("Comunicaciones");

        //Un arreglo con los links de cada modulo
        //Son en la forma de mapa: titulo => url
        $admins_noticias = array(
            "Crear nueva noticia" => "/noticias/add",
            "Ver noticias" => "/noticias/index"
        );

        $admins_avisos = array(
            "Crear nuevo aviso" => "/avisos/crear",
            "Ver avisos" => "/avisos/listar"
        );

        $admins_encuestas = array(
			"Crear Encuesta" =>"/encuestas",
			"Ver Encuestas" =>"/encuestas",
			"Modificar Encuestas" =>"/encuestas"
        );

        $admins_links = array(

        );

        $lista_admins = array(
          "Noticias" => $admins_noticias,
          "Avisos" => $admins_avisos,
          "Encuestas" => $admins_encuestas
        );

        $this->set('lista_admins',$lista_admins);
    }



}
?>