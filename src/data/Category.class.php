<?php

class Category extends Model {
	
	public function __construct() {
		parent::__construct("categories");
	}
	
	public function getSubCategories() {
		
		return $this->select("idParent = ".$this->getId());
		
	}
	
	public function getAllSubCategories() {
		
		$categories = $this->getSubCategories();
		
		$results = array();
		
		foreach($categories as $category) {
			
			$subs = $category->getAllSubCategories();
			
			foreach($subs as $sub) {
				$results[] = $sub;
			}
			
			$results[] = $category;
			
		}
		
		return $results;
		
	}
	
	public function getSpecificArticles() {
		
		$articleFact = new Article();
		return $articleFact->select("idCategorie = ".$this->getId()." AND removed = 0");
		
	}
	
	public function getArticles() {
		
		$articleFact = new Article();
		
		/* Les articles de la catégorie */
		$results = $articleFact->select("idCategorie = ".$this->getId()." AND removed = 0");
		
		/* Et les articles des sous catégories */
		foreach($this->getSubCategories() as $category) {
			
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
		
		$categoryFact = new Category();
		
		/* Les articles de la catégorie */
		$results = $categoryFact->select("id = ".$this->getIdParent());
		
		if(empty($results)) {
			return null;
		}
		
		return $results[0];
		
	}
	
	public function remove() {
		
		foreach($this->getAllSubCategories() as $category) {
			$category->remove();
		}
		
		foreach($this->getSpecificArticles() as $article) {
			$article->setRemoved(1);
			$article->update();
		}
		
		parent::remove();
		
	}
	
}

?>