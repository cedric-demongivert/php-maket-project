<?php
require_once "./controllers/Categories.class.php";

class Articles extends Controller {
	
	/* -------------------------------------------------------- */
	/*			FIELD(S)										*/
	/* -------------------------------------------------------- */
	private $form;
	private $ariane;
	private $name;
	
	/* -------------------------------------------------------- */
	/*			CONSTRUCTOR(S)									*/
	/* -------------------------------------------------------- */
	public function __construct() {
		parent::__construct("articles","Articles.template.html");
		$controller = new Categories();
		$this->includeController($controller);
		$this->title="Gestion des stocks";
		$this->name = $controller->title;
		$this->ariane = new Ariane("index.php?service=Categories");
		$this->includeController($this->ariane);
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
				$this->controllerTemplate = "Categories.template.html";
			}
			else {
				$this->form['name'] = $_POST["name"];
				$this->form['price'] = $_POST["price"];
				$this->form['stock'] = $_POST["stock"];
				$this->form['parent'] = $_POST["parent"];
				
				/* Formulaire */
				$this->controllerTemplate = "Articles_Create.template.html";
				$this->title = "Créer un article";
				$this->ariane->setFunction("Créer un article", "index.php?service=Articles&function=create&id_category={$_GET['id_category']}");
			
			}
			
		}
		else {
			/* Formulaire */
			$this->controllerTemplate = "Articles_Create.template.html";
			$this->title = "Créer un article";
			$this->ariane->setFunction("Créer un article", "index.php?service=Articles&function=create&id_category={$_GET['id_category']}");
		}
		
	}
	
	public function ruptureStock() {
		$this->stock = true;
	}
	
	public function see() {
		
		$this->controllerTemplate = "Articles_See.template.html";
		
	}
	
	/* Modifier une catégorie : */
	public function modify() {
		
		$this->ariane->setFunction("Modifier un article", "index.php?service=Articles&function=modify&id_article={$_GET['id_article']}&id_category={$_GET['id_category']}");
		$this->title="Modifier un article";
		
		if(isset($_GET['id_article'])) {
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
						
						$this->controllerTemplate = "Categories.template.html";
						$this->title = $this->name;
						$this->info = "L'article {$article->getNom()} a bien été modifié !";
						$this->ariane->clearFunction();
						
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
				$this->controllerTemplate = "Categories.template.html";
				$this->title = $this->name;
			}
			
		}// if(isset($_GET['id']))
		else {
			$this->controllerTemplate = "Categories.template.html";
			$this->title = $this->name;
			$this->error = "Erreur lors de la tentative de modification de l'article.";	
		}
		
	}
	
	public function reappro() {
		
		if(isset($_GET['id_article'])) {
			
			$this->title = "Supprimer un article";
			$this->ariane->setFunction("Supprimer un article","index.php?service=Articles&function=delete&id_article={$_GET['id_article']}");
			
			if(isset($_POST['nombre']) && preg_match("/[0-9]+/", $_POST['nombre']) == 1) {
				
				$article = $this->getArticle();
				
				if($article == null) {
					$this->error = "Impossible de réapprovisionner l'article demandé";
				}
				else {
					$article->setNombre($article->getNombre()+$_POST['nombre']);
					$article->update();
					
					$this->info = "L'article demandé a été réapprovisionné comme demandé ";
					return;
				}
				
			}
			else {
				$this->error = "Impossible de réapprovisionner l'article demandé";
			}
			
		}
		else {
			$this->error = "Impossible de réapprovisionner l'article demandé";
		}

	}

	public function delete() {
		
		if(isset($_GET['id_article'])) {
			
			$this->title = "Supprimer un article";
			$this->ariane->setFunction("Supprimer un article","index.php?service=Articles&function=delete&id_category={$_GET['id_category']}&id_article={$_GET['id_article']}");
			
			if(isset($_GET['force'])) {
				
				$article = $this->getArticle();
				
				if($article == null) {
					$this->error = "L'article que vous souhaitez supprimer n'existe pas en base.";
					$this->controllerTemplate = "Categories.template.html";
					$this->title = $this->name;	
					$this->ariane->clearFunction();
				}
				else {
					$article->remove();
					$this->controllerTemplate = "Categories.template.html";
					$this->title = $this->name;	
					$this->ariane->clearFunction();
					$this->info = "L'article {$article->getNom()} a bien été supprimé !";
					return;
				}
				
			}
			else {
				$this->controllerTemplate = "Articles_Delete.template.html";
			}
			
		}
		else {
			$this->controllerTemplate = "Categories.template.html";
			$this->title = $this->name;
			$this->error = "Erreur lors de la tentative de suppression de l'article.";	
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
		
		$datas = Model::toData($articles->select("nombre <= 0"));
		
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
	
	public function getWarnArticles() {
		
		$articles = new Article();
		
		$datas = Model::toData($articles->select("nombre <= 5 AND nombre != 0"));
		
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
		
		if(isset($_GET["id_article"])) {
			
			$article = new Article();
			$article = $article->selectById($_GET["id_article"]);
			
			return $article;
			
		}
		else {
			return null;
		}
		
	}
	
}

?>