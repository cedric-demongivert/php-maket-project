<?php
require_once "./src/data/Category.class.php";

class Categories extends Controller {
	
	public function __construct() {
		parent::__construct("categoriesList");
		$this->title = "Navigation";
	}
	
	public function init() {
		
	}
	
	public function getCategories() {
		$category = new Category();
		return Model::toData($category->selectAll());
	}
	
}

?>