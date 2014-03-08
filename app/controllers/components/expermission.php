<?php
class ExpermissionComponent extends Object
{
	function allowedAction($controller, $action='index')
	{
		$cod_voluntario=$this->view->controller->Session->read('cod_voluntario');
		$filter=array('Permisovoluntario.cod_voluntario' => $cod_voluntario, 
						'Permiso.nom_controller' => $controller,
						'Permiso.nom_action' => $action);
		return is_array($this->view->controller->Permisovoluntario->find($filter));
	}
	function allowedExecution($controller,$action='index')
	{
		if(!$this->allowedAction($controller,$action))
		{
			$msg_for_layout = -1000;
			$this->View->Controller->redirect("/menu/moreoptions");
			exit();
		}
	}
}
?>