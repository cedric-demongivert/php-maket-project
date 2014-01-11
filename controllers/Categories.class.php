<?php
require_once "./src/data/Category.class.php";

class Categories extends Controller {
	
	public function init() {
		$this->title = "Accueil";
	}
	
	public function getCategories() {
		$category = new Category();
		return $category->selectAll();
	}
	
}

?>