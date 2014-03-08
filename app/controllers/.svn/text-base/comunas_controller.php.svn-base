<?php
	class ComunasController extends AppController
	{
		var $name = "Comuna";
		var $uses = array("Comuna");
		var $layout = 'vacio';
		
		function listacomunas($region){
			$comunas=$this->Comuna->getAllAsArray($region);
			$this->set('comunas', $comunas);
			
		}
	}
?>