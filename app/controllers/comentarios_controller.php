<?php
class ComentariosController extends AppController {

	var $name = 'Comentarios';
	var $helpers = array('Html', 'Form' , 'Pagination');

	function index() {
		$this->Comentario->recursive = 0;
		$this->set('comentarios', $this->Comentario->findAll());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalid id for Comentario.');
			$this->redirect('/comentarios/index');
		}
		$this->set('comentario', $this->Comentario->read(null, $id));
	}

	function add() {
		if (empty($this->data)) {
			$this->set('personas', $this->Comentario->Persona->generateList());
			$this->render();
		} else {
			$this->cleanUpFields();
			if ($this->Comentario->save($this->data)) {
				$this->Session->setFlash('Comentario Agregado');
				$this->redirect('/personas/ver/'.$this->data['Comentario']['cod_persona']);
			} else {
				$this->Session->setFlash('Please correct errors below.');
				$this->set('personas', $this->Comentario->Persona->generateList());
			}
		}
	}

	function edit($id = null) {
		if (empty($this->data)) {
			if (!$id) {
				$this->Session->setFlash('Invalid id for Comentario');
				$this->redirect('/comentarios/index');
			}
			$this->data = $this->Comentario->read(null, $id);
			$this->set('personas', $this->Comentario->Persona->generateList());
		} else {
			$this->cleanUpFields();
			if ($this->Comentario->save($this->data)) {
				$this->Session->setFlash('The Comentario has been saved');
				$this->redirect('/comentarios/index');
			} else {
				$this->Session->setFlash('Please correct errors below.');
				$this->set('personas', $this->Comentario->Persona->generateList());
			}
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalid id for Comentario');
			$this->redirect('/comentarios/index');
		}
		if ($this->Comentario->del($id)) {
			$this->Session->setFlash('The Comentario deleted: id '.$id.'');
			$this->redirect('/comentarios/index');
		}
	}

}
?>