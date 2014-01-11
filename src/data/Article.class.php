<?php

class Article extends Model {
	
	public function __construct() {
		parent::__construct("articles");
	}
	
	public function getCategory() {
		
		$categoryFact = new Category();
		$result = $categoryFact->select("id = {$this->getIdCategorie()}");
		return $result[0];
		
	}
	
}

?>