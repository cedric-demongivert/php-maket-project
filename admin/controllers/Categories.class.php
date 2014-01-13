<?php
require_once("./controllers/Ariane.class.php");

class Categories extends Controller {
	
	private $ariane;
	
	/* -------------------------------------------------------- */
	/*			CONSTRUCTOR(S)									*/
	/* -------------------------------------------------------- */
	public function __construct() {
		parent::__construct("nav","Categories.template.html");
		$this->title = "Navigation";
		$this->ariane = new Ariane("index.php?service=Categories");
		$this->includeController($this->ariane);
	}
	
	
	/* -------------------------------------------------------- */
	/*			METHOD(S)										*/
	/* -------------------------------------------------------- */
	/* Créer une nouvelle catégorie */
	public function create() {
		
		/* Formulaire */
		$this->controllerTemplate = "Categories_Create.template.html";
		$this->title = "Créer une catégorie";
		
		/* Vérification si vérification nécéssaire */
		if(isset($_POST) && !empty($_POST)) {
			
			if(empty($_POST['name'])) {
				$this->error = "Veuillez préciser un nom pour votre catégorie";
				$this->ariane->setFunction("Créer une catégorie", "index.php?service=Categories&function=create&id_category={$_GET['id_category']}");
				return;
			}
			
			$category = new Category();
			$category->setNom($_POST['name']);
			$category->setIdParent($_POST['parent']);
			$category->insert();
			
			$this->info = "La catégorie {$_POST['name']} a été créée avec succès !";
			$this->controllerTemplate = "Categories.template.html";
			
		}
		else {
			$this->ariane->setFunction("Créer une catégorie", "index.php?service=Categories&function=create&id_category={$_GET['id_category']}");
		}
		
	}
	
	public function see() {
		
		$this->controllerTemplate = "Categories_See.template.html";
		
	}
	
	/* Modifier une catégorie : */
	public function modify() {
		
		if(isset($_GET['id_category'])) {
			
			$this->controllerTemplate = "Categories_Modify.template.html";
			
			/* C'est partit : */
			$category = new Category();
			$category = $category->selectById($_GET['id_category']);
			
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
		
		if(isset($_GET['id_category'])) {
			
			if(isset($_GET['force'])) {
				
				$category = new Category();
				$category = $category->selectById($_GET['id_category']);
				
				$category->remove();
				
				$this->info = "La catégorie {$category->getNom()} à bien été supprimée. ";
				
			}
			else {
				$this->controllerTemplate = "Categories_Delete.template.html";
			}
			
		}
		
	}
	
	/* -------------------------------------------------------- */
	/*			GETTER(S) & SETTER(S)							*/
	/* -------------------------------------------------------- */
	public function getCategory() {
		$category = new Category();
		if (isset($_GET['id_category']) && $_GET['id_category'] != '-1') {
			return $category->selectById($_GET['id_category']);
		}
		else {
			$category->setId(-1);
			$category->setNom("racine");
			return $category;
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
		if (isset($_GET['id_category']) && $_GET['id_category'] != '-1') {
			$id = $_GET['id_category'];
			$category = $category->selectById($id);
		} else {
			$category->setId($id);
		}
		return $category->getSpecificArticles();
	}
	
}

?>