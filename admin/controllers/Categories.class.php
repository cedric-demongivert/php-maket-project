<?php

class Categories extends Controller {
	
	private $modifiedCategory;
	
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
	
	/* Modifier une catégorie : */
	public function modify() {
		
		if(isset($_GET['id'])) {
			
			$this->controllerTemplate = "Categories_Modify.template.html";
			
			/* C'est partit : */
			$category = new Category();
			$category = $category->select("id = {$_GET['id']}");
			$category = $category[0];
			
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

	public function getCategories() {
		
		$categories = new Category();
		return Model::toData($categories->selectAll());
		
	}
	
	public function getCategory() {
		return Model::toData($this->modifiedCategory);
	}
	
}

?>