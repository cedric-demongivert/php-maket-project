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
			$article = $this->getArticle();
			if(!empty($article)) {
				
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
						
						$article->setNom($_POST["name"]);
						$article->setPrix($_POST["price"]);
						$article->setNombre($_POST["stock"]);
						$article->setIdCategorie($_POST["parent"]);
						
						$article->update();
						
						$this->info = "L'article {$article->getId()} a bien été modifié !";
					
					} // if($valid)
					else {
						
						$this->form['name'] = $_POST["name"];
						$this->form['price'] = $_POST["price"];
						$this->form['stock'] = $_POST["stock"];
						$this->form['parent'] = $_POST["parent"];
						
						/* Formulaire */
						$this->controllerTemplate = "Articles_Modify.template.html";
					
					}
					
				} // if(isset($_POST) && !empty($_POST))
				else {
					
					$this->form['name'] = $article->getNom();
					$this->form['price'] = $article->getPrix();
					$this->form['stock'] = $article->getNombre();
					$this->form['parent'] = $article->getIdCategorie();
						
					$this->controllerTemplate = "Articles_Modify.template.html";
				
				}
			}// if(!empty($article))
			else {
				$this->error = "L'article à modifier n'existe pas en base !";
			}
			
		}// if(isset($_GET['id']))
		else {
			$this->error = "Erreur lors de la tentative de modification de l'article.";	
		}
		
	}

	public function delete() {
		
		if(isset($_GET['id'])) {
			
			if(isset($_GET['force'])) {
				
				$article = $this->getArticle();
				
				if($article == null) {
					$this->error = "L'article que vous souhaitez supprimer n'existe pas en base.";
				}
				else {
					$article->remove();
					$this->info = "L'article {$article->getId()} a bien été supprimé !";
				}
				
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
		for($i = 0; $i < sizeof($datas); $i++) {
			if($datas[$i]["idCategorie"] >= 0) {
				$categoryData = Model::toData($categories->selectById($datas[$i]["idCategorie"]));
				$datas[$i]["categorie"] = $categoryData["nom"];
			}
			else {
				$datas[$i]["categorie"] = "Aucune catégorie";
			}
		}

		return $datas;
		
	}
	
	public function getForm() {
		return $this->form;
	}
	
	public function getArticle() {
		
		if(isset($_GET["id"])) {
			
			$article = new Article();
			$article = $article->selectById($_GET["id"]);
			
			return $article;
			
		}
		else {
			return null;
		}
		
	}
	
	
}

?>