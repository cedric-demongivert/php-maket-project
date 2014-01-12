<?php

class Category extends Model {
	
	public function __construct() {
		parent::__construct("categories");
	}
	
	public function getSubCategories() {
		
		return $this->select("idParent = ".$this->getId());
		
	}
	
	public function getSpecificArticles() {
		
		$articleFact = new Article();
		return $articleFact->select("idCategorie = ".$this->getId());
		
	}
	
	public function getArticles() {
		
		$articleFact = new Article();
		
		/* Les articles de la catégorie */
		$results = $articleFact->select("idCategorie = ".$this->getId());
		
		/* Et les articles des sous catégories */
		foreach($category as $this->getSubCategories) {
			
			$articles = $category->getArticles();
			
			foreach($articles as $article) {
				$results[] = $article;
			}
			
		}
		
		return $results;
		
	}
	
	public function getParent() {
		
		if($this->getIdParent() < 0) {
			return null;
		}
		
		$articleFact = new Article();
		
		/* Les articles de la catégorie */
		$results = $articleFact->select("id = ".$this->getIdParent());
		
		if(empty($results)) {
			return null;
		}
		
		return $results[0];
		
	}
	
}

?>