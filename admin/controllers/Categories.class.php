<?php
require_once("./controllers/Ariane.class.php");

class Categories extends Controller {
	
	private $ariane;
	
	/* -------------------------------------------------------- */
	/*			CONSTRUCTOR(S)									*/
	/* -------------------------------------------------------- */
	public function __construct() {
		parent::__construct("nav","Categories.template.html");
		$this->title = "Gestion des catégories et articles";
		$this->ariane = new Ariane("index.php?service=Categories");
		$this->includeController($this->ariane);
	}
	
	/* -------------------------------------------------------- */
	/*			METHOD(S)										*/
	/* -------------------------------------------------------- */
	/* Créer une nouvelle catégorie */
	public function create() {
		
		if(isset($_GET['id_category'])) {
		
			/* Formulaire */
			$this->controllerTemplate = "Categories_Create.template.html";
			$this->title = "Créer une catégorie";
	
			/* Si une variable POST existe, on vérifie les informations */
			if(isset($_POST) && !empty($_POST)) {
				
				/* Création de la vérification */
				$formFactory = new ModelFormBuilder();
				$formFactory->setModel(new Category());
				$form = $formFactory->buildForm();
				$form->addCheck('nom', new NoEmptyCheck());
				$form->addCheck('idParent', new NoEmptyCheck());
				$form->addCheck('idParent', new IntegerCheck());
			
				/* Si l'évaluation réussi, on crée la catégorie */
				if(($errors = $form->evaluate($_POST)) === true) {
					$form->complete($_POST);
					$formFactory->buildModel($form, new Category())->insert();
				
					$this->info = "La catégorie {$_POST['nom']} a été créée avec succès !";
					$this->controllerTemplate = "Categories.template.html";
					$this->title = "Gestion des catégories et articles";
				} 
				else {
					$this->error = $form::toStringErrors($errors);
				}
	
			}
			else {
				$this->ariane->setFunction("Créer une catégorie", "index.php?service=Categories&function=create&id_category={$_GET['id_category']}");
			}
		}
		else {
			$this->error = "Erreur url";
		}
	}
	
	/* Modifier une catégorie : */
	public function modify() {
		
		if(isset($_GET['id_category'])) {
			
			/* Récupération de la catégorie : */
			$category = new Category();
			$category = $category->selectById($_GET['id_category']);
			
			/* Existence de la catégorie en base : */
			if(empty($category)) {
				$this->error = "La catégorie que vous souhaitez modifier n'existe pas en base";
			}
			else {
				$this->controllerTemplate = "Categories_Modify.template.html";
				$this->title = "Modifier une catégorie";
				$this->ariane->setFunction("Modifier une catégorie", "index.php?service=Categories&function=modify&id_category={$_GET['id_category']}");
			}
			
			/* Vérification si vérification nécéssaire */
			if(isset($_POST) && !empty($_POST)) {
				
				/* Création de la vérification */
				$formFactory = new ModelFormBuilder();
				$formFactory->setModel(new Category());
				$form = $formFactory->buildForm();
				$form->addCheck('nom', new NoEmptyCheck());
				$form->addCheck('idParent', new NoEmptyCheck());
				$form->addCheck('idParent', new IntegerCheck());
				
				/* Si l'évaluation réussi, on crée la catégorie */
				if(($errors = $form->evaluate($_POST)) === true) {
					$form->complete($_POST);
					$formFactory->buildModel($form, $category)->update();
				
					$category->update();
					
					$_GET['id_category'] = $category->getIdParent();
					$this->title = "Gestion des catégories et articles";
					$this->info = "La catégorie {$_POST['name']} a été mise à jour !";
					$this->controllerTemplate = "Categories.template.html";
					$this->ariane->clearFunction();
				} 
				else {
					$this->error = $form::toStringErrors($errors);
				}
				
				if(empty($_POST['name'])) {
					$this->error = "Veuillez préciser un nom pour votre catégorie";
					return;
				}
			
			}
			
		}
		else {
			$this->error = "Erreur lors de la tentative de modification de la catégorie.";	
		}
		
	}

	public function delete() {
		
		if(isset($_GET['id_category'])) {
			
			if(isset($_GET['move'])) {
				
				$category = new Category();
				$category = $category->selectById($_GET['id_category']);
				$id = $category->getIdParent();
				
				foreach($category->getArticles() as $article) {
					$article->setIdCategorie($id);
					$article->update();
				}
				
				$this->info = "Les articles ont bien été déplacés. ";
				
			}
			
			if(isset($_GET['force'])) {
				
				$category = new Category();
				$category = $category->selectById($_GET['id_category']);
				
				$_GET['id_category'] = $category->getIdParent();
				$category->remove();
				
				$this->info = "La catégorie {$category->getNom()} à bien été supprimée. ";
				$this->title = "Gestion des catégories et articles";
				
			}
			else {
				$this->controllerTemplate = "Categories_Delete.template.html";
				$this->ariane->setFunction("Supprimer une catégorie","index.php?service=Categories&function=delete&id_category={$_GET['id_category']}");
				$this->title = "Supprimer une catégorie";
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
	
	public function getAllSubCategories() {
		
		$category = $this->getCategory();
		
		return $category->getAllSubCategories();
		
	}
	
	public function getAllCategoryItems() {
		
		$category = $this->getCategory();
		
		return $category->getArticles();
		
	}
	
	public function getAllCategories() {
		
		$categories = new Category();
		
		return $categories->selectAll();
		
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