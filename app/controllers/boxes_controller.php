<?php
class BoxesController extends AppController {

	var $name = 'Boxes';
	var $uses = array('Box', 'Turno');
	var $helpers = array('Html', 'Form' );

	function index() {
		$this->escribirHeader("Gestiï¿½n de Boxes");
		$this->Box->recursive = 0;
		$this->set('boxes', $this->Box->findAll());
	}

	function add() {
		$this->escribirHeader("Gestiï¿½n de Boxes");
		if (empty($this->data)) 
		{
			$tip_box = $this->Box->getPossibleValues('tip_box');
			$this->set('tip_box', $tip_box );
			$this->render();
		} else {
			$this->cleanUpFields();
			if ($this->Box->save($this->data)) {
				$this->Session->setFlash('The Box has been saved');
				$this->redirect('/boxes/index');
			} else {
				$this->Session->setFlash('Please correct errors below.');
			}
		}
	}

	function edit($id = null) {
		$this->escribirHeader("Gestiï¿½n de Boxes");
		if (empty($this->data)) {
			if (!$id) {
				$this->Session->setFlash('Invalid id for Box');
				$this->redirect('/boxes/index');
			}
			$this->data = $this->Box->read(null, $id);
			$tip_box = $this->Box->getPossibleValues('tip_box');
			$this->set('tip_box', $tip_box );
		} else {
			$this->cleanUpFields();
			if ($this->Box->save($this->data)) {
				$this->Session->setFlash('The Box has been saved');
				$this->redirect('/boxes/index');
			} else {
				$this->Session->setFlash('Please correct errors below.');
			}
		}
	}

	function cambiar($id = null) { 	                    
 	    if (!$id) {
 	            $this->Session->setFlash('Id invalido para Box');
 	            $this->redirect('/boxes/index');
 	    }
 	    $this->data = $this->Box->read(null, $id);
 	    $this->data['Box']['bit_vigente']=1-$this->data['Box']['bit_vigente'];
 	        
 	    $this->Box->save($this->data);
 	    $this->redirect('/boxes/index');       
 	}

}
?>