<?php
require_once "./src/data/Category.class.php";

class Categories extends Controller {
	
	public function __construct() {
		parent::__construct("nav", "Categories.template.html");
		$this->title = "Navigation";
	}
	
	public function init() {
		
	}
	
	public function getCategory() {
		$category = new Category();
		if (isset($_GET['id_category'])) {
			return $category->selectById($_GET['id_category']);
		}
	}
	
	public function getCategories() {
		$category = new Category();
		if (isset($_GET['id_category'])) {
			$categories = $category->select("idParent = ".Model::$bdd->quote($_GET['id_category']));
			
			return $categories;
		} else {
			$categories = $category->select("idParent = -1");
			
			return $categories;
		}
	}
	
	public function getArticles() {
		$category = new Category();
		$id = -1;
		if (isset($_GET['id_category'])) {
			$id = $_GET['id_category'];
			$category = $category->selectById($id);
		} else {
			$category->setId($id);
		}
		return $category->getSpecificArticles();
	}
	
}

?>