<?php

class Categories extends Controller {
	
	public function __construct() {
		parent::__construct("categoriesController","Categories.template.html");
	}
	
	/* Créer une nouvelle catégorie */
	public function create() {
		
		/* Formulaire */
		$this->controllerTemplate = "Categories_Create.template.html";
		
		/* Vérification si vérification nécéssaire */
		if(isset($_POST) && !empty($_POST)) {
			
			if(empty($_POST['name'])) {
				$this->error = "Veuillez préciser un nom pour votre catégorie";
				return;
			}
			
			$category = new Category();
			$category->setNom($_POST['name']);
			$category->setIdParent($_POST['parent']);
			$category->insert();
			
			$this->info = "La catégorie {$_POST['name']} a été créée avec succès !";
			$this->controllerTemplate = "Categories.template.html";
			
		}
		
	}
	
	public function see() {
		
		$this->controllerTemplate = "Categories_See.template.html";
		
	}
	
	/* Modifier une catégorie : */
	public function modify() {
		
		if(isset($_GET['id'])) {
			
			$this->controllerTemplate = "Categories_Modify.template.html";
			
			/* C'est partit : */
			$category = new Category();
			$category = $category->selectById($_GET['id']);
			
			$this->modifiedCategory = $category;
			
			/* Vérification si vérification nécéssaire */
			if(isset($_POST) && !empty($_POST)) {
				
				if(empty($_POST['name'])) {
					$this->error = "Veuillez préciser un nom pour votre catégorie";
					return;
				}
				
				$category->setNom($_POST['name']);
				$category->setIdParent($_POST['parent']);
				$category->update();
				
				$this->info = "La catégorie {$_POST['name']} a été mise à jour !";
				$this->controllerTemplate = "Categories.template.html";
				
			}
			
		}
		else {
			$this->error = "Erreur lors de la tentative de modification de la catégorie.";	
		}
		
	}

	public function delete() {
		
		if(isset($_GET['id'])) {
			
			if(isset($_GET['force'])) {
				
				$category = new Category();
				$category = $category->selectById($_GET['id']);
				
				$category->remove();
				
				$this->info = "La catégorie {$category->getNom()} à bien été supprimée. ";
				
			}
			else {
				$this->controllerTemplate = "Categories_Delete.template.html";
			}
			
		}
		
	}
	
	public function getCategories() {
		
		$categories = new Category();
		return Model::toData($categories->selectAll());
		
	}
	
	public function getCategory() {
		if(isset($_GET['id'])) {
			
			$category = new Category();
			$category = $category->selectById($_GET['id']);
			
			return Model::toData($category);
			
		}
		else {
			return null;
		}
	}
	
	public function getSubCategories() {
		if(isset($_GET['id'])) {
			
			$category = new Category();
			$category = $category->selectById($_GET['id']);
			
			return Model::toData($category->getSubCategories());
			
		}
		else {
			return null;
		}
	}
	
	public function getCategoryItems() {
		if(isset($_GET['id'])) {
			
			$category = new Category();
			$category = $category->selectById($_GET['id']);
			
			return Model::toData($category->getSpecificArticles());
			
		}
		else {
			return null;
		}
	}
	
	public function getAllCategoryItems() {
		if(isset($_GET['id'])) {
			
			$category = new Category();
			$category = $category->selectById($_GET['id']);
			
			return Model::toData($category->getArticles());
			
		}
		else {
			return null;
		}
	}
	
	public function getAllSubCategories() {
	if(isset($_GET['id'])) {
			
			$category = new Category();
			$category = $category->selectById($_GET['id']);
			
			return Model::toData($category->getAllSubCategories());
			
		}
		else {
			return null;
		}
	}
	
	public function getParentCategory() {
		if(isset($_GET['id'])) {
			
			$category = new Category();
			$category = $category->selectById($_GET['id']);
			
			return Model::toData($category->getParent());
			
		}
		else {
			return null;
		}
	}
	
}

?>