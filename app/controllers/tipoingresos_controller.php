<?php
	class TipoingresosController extends AppController
	{
		var $name = "Tipoingreso";
		var $uses = array("Tipoingreso");
		var $layout = 'vacio';
		
		function listatipoingreso($medio='', $seleccionado=''){
			$tipoingreso=$this->Tipoingreso->getAllAsArray($medio);
			$this->set('tipoingreso', $tipoingreso);
			$this->set('seleccionado_tipoingreso', $seleccionado);
			
		}
	}
?>