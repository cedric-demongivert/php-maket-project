<?php

abstract class Controller {
	
	/* ------------------------------------ */
	/*		ATTRIBUT(S) :					*/
	/* ------------------------------------ */
	protected $bdd;
	protected $post;
	protected $session;
	protected $get;
	
	/* ------------------------------------ */
	/*		CONSTRUCTEUR(S) :				*/
	/* ------------------------------------ */
	public function __construct($bdd, $post, $session, $get) {
		$this->bdd = $bdd;
		$this->post = $post;
		$this->session = $session;
		$this->get = $get;
	}
	
	/* ------------------------------------ */
	/*		METHODE(S) :					*/
	/* ------------------------------------ */
	abstract public function getWebPageData();
	
}

?>