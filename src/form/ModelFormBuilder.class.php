<?php

class ModelFormBuilder {
	
	/* -------------------------------------------------------- */
	/*			FIELD(S)										*/
	/* -------------------------------------------------------- */
	private $model;
	
	/* -------------------------------------------------------- */
	/*			CONSTRUCTOR(S)									*/
	/* -------------------------------------------------------- */
	public function __construct() {
		$this->model = null;
		$this->form = null;
	}
	
	/* -------------------------------------------------------- */
	/*			METHOD(S)										*/
	/* -------------------------------------------------------- */
	/**
	 * 
	 * Construire un formulaire celon un modèle passé en paramètre
	 * 
	 * @return Form formulaire
	 *
	 */
	public function buildForm() {
		
		if($this->model != null) {
			
			$form = new Form();
			
			foreach($this->model->getData() as $key => $value) {
				$form->addField($key);
				$form->setValue($key, $value);
			}
			
			return $form;
			
		}
		else {
			return new Form();
		}
		
	}
	
	/**
	 * 
	 * Complète un modèle avec un formulaire
	 * @param Form $form le formulaire contenant les informations
	 * @param <? extends Model> $model
	 *
	 */
	public function buildModel($form, $model) {
		
		foreach($form->getValues() as $key => $value) {
			$model->__set($key, $value);
		}

		return $model;
		
	}
	
	/* -------------------------------------------------------- */
	/*			GETTER(S) & SETTER(S)							*/
	/* -------------------------------------------------------- */
	public function setModel($model) {
		$this->model = $model;
	}
	
	public function getModel() {
		return $this->model;
	}
	
	public function setForm($form) {
		$this->form = $form;
	}
	
	public function getForm() {
		return $this->form;
	}
	
}

?>