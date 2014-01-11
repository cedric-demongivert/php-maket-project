<?php

class Categories extends Controller {
	
	public function __construct() {
		parent::__construct("categoriesController","Categories.template.html");
	}
	
	public function create() {
		
	}
	
	public function modify() {
		
	}
	
	public function listCategories() {
		
	}
	
	public function getCategories() {
		
		$categories = new Category();
		return $categories->selectAll();
		
	}
	
	public function getCategory() {
		
	}
	
}

?>