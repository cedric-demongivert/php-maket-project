<?php
require_once './src/data/Article.class.php';

class Category extends Model {
	
	public function __construct() {
		parent::__construct("categories");
	}
	
	public function getSubCategories() {
		
		return $this->select("id_parent = ".$this->getId());
		
	}
	
	public function getSpecificArticles() {
		
		$articleFact = new Article();
		return $articleFact->select("id_categorie = ".$this->getId());
		
	}
	
	public function getArticles() {
		
		$articleFact = new Article();
		
		/* Les articles de la catégorie */
		$results = $articleFact->select("id_categorie = ".$this->getId());
		
		/* Et les articles des sous catégories */
		for($category : $this->getSubCategories) {
			
			$articles = $category->getArticles();
			
			foreach($articles as $article) {
				$results[] = $article;
			}
			
		}
		
		return $results;
		
	}
	
}

?>