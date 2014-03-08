<?php
class ConveniosController extends AppController {

	var $name = 'Convenios';
	var $uses = array('Convenio', 'Beneficiario', 'Persona', 'Subpregunta');
	var $helpers = array('Html', 'Form' );

	function index() {
		$this->escribirHeader("Gestión de Covenios");
		$this->Convenio->recursive = 1;
		$this->set('convenios', $this->Convenio->findAll());
	}

	function view($id = null) {
		$this->escribirHeader("Gestión de Covenios");
		if (!$id) {
			$this->Session->setFlash('Invalid id for Convenio.');
			$this->redirect('/convenios/index');
		}
		$this->Convenio->recursive = 1;
		$this->set('convenio', $this->Convenio->findAll($id));
	}

	function add() {
		$this->escribirHeader("Gestión de Covenios");
		if (empty($this->data)) {
			$this->render();
		} else {
			$this->cleanUpFields();
			$this->data['Convenio']['bit_vigente']=1;
			if ($this->Convenio->save($this->data)) {
				$cod_convenio=$this->Convenio->getLastInsertId();
				$this->Subpregunta->create(array('cod_dimension' => 116, 'nom_subpregunta' => $this->data['Convenio']['nom_convenio'], 'tip_tipoinput' => 'radio', 'cod_grupo' => 24, 'num_fila' => $cod_convenio));
				$this->Subpregunta->save(array('cod_dimension' => 116, 'nom_subpregunta' => $this->data['Convenio']['nom_convenio'], 'tip_tipoinput' => 'radio', 'cod_grupo' => 24, 'num_fila' => $cod_convenio));
				$this->Session->setFlash('El Convenio ha sido ingresado');				
				$this->redirect('/convenios/index');
			} else {
				$this->Session->setFlash('Por favor corrija los errores abajo');
			}
		}
	}

	function edit($id = null) {
		$this->escribirHeader("Gestión de Convenios");
		
		if (empty($this->data)) {
			if (!$id) {
				$this->Session->setFlash('Invalid id for Convenio');
				$this->redirect('/convenios/index');
			}
			$this->data = $this->Convenio->read(null, $id);
		} else {
			$this->cleanUpFields();
			if ($this->Convenio->save($this->data)) {
				$this->Session->setFlash('El Convenio ha sido Guardado');
				$this->redirect('/convenios/index');
			} else {
				$this->Session->setFlash('Por favor corrija los errores abajo');
			}
		}
	}

	function cambiar($id = null) {
		$this->escribirHeader("Gestión de Covenios");
			
		if (!$id) {
			$this->Session->setFlash('Invalid id for Convenio');
			$this->redirect('/convenios/index');
		}
		$this->data = $this->Convenio->read(null, $id);
		$subPreguntaId = $this->Subpregunta->field("cod_subpregunta", "cod_grupo = 24 AND num_fila=".$id, "");
		$subpregunta = $this->Subpregunta->findByCodSubpregunta($subPreguntaId);
		
		if($this->data['Convenio']['bit_vigente']) 
		{
			$this->data['Convenio']['bit_vigente'] = 0;
			$subpregunta['Subpregunta']['tip_tipoinput'] = "norender";
			
		}
		else
		{
			$this->data['Convenio']['bit_vigente'] = 1;
			$subpregunta['Subpregunta']['tip_tipoinput'] = "radio";
		}
		
		$this->Convenio->save($this->data);
		$this->Subpregunta->save($subpregunta);
		$this->redirect('/convenios/index');
		
	}

}
?>