<?php

class Articles extends Controller {
	
	/* -------------------------------------------------------- */
	/*			FIELD(S)										*/
	/* -------------------------------------------------------- */
	private $form;
	
	/* -------------------------------------------------------- */
	/*			CONSTRUCTOR(S)									*/
	/* -------------------------------------------------------- */
	public function __construct() {
		parent::__construct("articlesController","Articles.template.html");
	}
	
	/* -------------------------------------------------------- */
	/*			METHOD(S)										*/
	/* -------------------------------------------------------- */
	/* Créer un nouvel article */
	public function create() {

		/* Vérification si vérification nécéssaire */
		if(isset($_POST) && !empty($_POST)) {
			
			$valid = true;
			
			if(empty($_POST['name'])) {
				$this->error = "Veuillez saisir le nom de l'article.";
				$valid = false;
			}
			else if(empty($_POST['price'])) {
				$this->error = "Veuillez saisir un prix pour l'article.";
				$valid = false;
			}
			else if(empty($_POST['stock'])) {
				$_POST['stock'] = 0;
			}
			
			if($valid) {
				$article = new Article();
				$article->setNom($_POST["name"]);
				$article->setPrix($_POST["price"]);
				$article->setNombre($_POST["stock"]);
				$article->setIdCategorie($_POST["parent"]);
				
				$article->insert();
				
				$this->info = "L'article {$_POST['name']} a bien été crée !";
			}
			else {
				$this->form['name'] = $_POST["name"];
				$this->form['price'] = $_POST["price"];
				$this->form['stock'] = $_POST["stock"];
				$this->form['parent'] = $_POST["parent"];
				
				/* Formulaire */
				$this->controllerTemplate = "Articles_Create.template.html";
			
			}
			
		}
		else {
			/* Formulaire */
			$this->controllerTemplate = "Articles_Create.template.html";
		}
		
	}
	
	public function see() {
		
		$this->controllerTemplate = "Articles_See.template.html";
		
	}
	
	/* Modifier une catégorie : */
	public function modify() {
		
		if(isset($_GET['id'])) {
			
			$this->controllerTemplate = "Articles_Modify.template.html";
			
			/* Vérification si vérification nécéssaire */
			if(isset($_POST) && !empty($_POST)) {
				
			}
			
		}
		else {
			$this->error = "Erreur lors de la tentative de modification de l'article.";	
		}
		
	}

	public function delete() {
		
		if(isset($_GET['id'])) {
			
			if(isset($_GET['force'])) {
				
				
			}
			else {
				$this->controllerTemplate = "Articles_Delete.template.html";
			}
			
		}
		
	}
	
	/* -------------------------------------------------------- */
	/*			GETTER(S) & SETTER(S)							*/
	/* -------------------------------------------------------- */
	public function getCategories() {
		
		$categories = new Category();
		return Model::toData($categories->selectAll());
		
	}
	
	public function getArticles() {
		
		$articles = new Article();
		
		$datas = Model::toData($articles->selectAll());
		
		$categories = new Category();
		foreach($datas as $data) {
			if($data["idCategorie"] >= 0) {
				$categoryData = Model::toData($categories->selectById($data["idCategorie"]));
				$data["categorie"] = $categoryData["nom"];
			}
			else {
				$data["categorie"] = "Aucune catégorie";
			}
		}

		return $datas;
		
	}
	
	public function getForm() {
		return $this->form;
	}
	
	
}

?>