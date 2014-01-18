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

		if(isset($_GET['id_category'])) {
			/* Vérification si vérification nécéssaire */
			if(isset($_POST) && !empty($_POST)) {
				
				/* Création de la vérification */
				$formFactory = new ModelFormBuilder();
				$formFactory->setModel(new Article());
				$this->form = $formFactory->buildForm();
				$this->form->addCheck('nom', new NoEmptyCheck());
				$this->form->addCheck('idCategorie', new NoEmptyCheck());
				$this->form->addCheck('idCategorie', new IntegerCheck());
				$this->form->addCheck('prix', new NoEmptyCheck());
				$this->form->addCheck('prix', new PositiveFloatCheck());
				$this->form->addCheck('nombre', new IntegerCheck());
				$this->form->addCheck('remise', new PositiveFloatCheck());		
				
				/* Si l'évaluation réussi, on crée l'article */
				if(($errors = $this->form->evaluate($_POST)) === true) {
					
					$this->form->complete($_POST);
					$article = $formFactory->buildModel($this->form, new Article());
					$article->setImage("null.jpg");
					$article->insert();	
					
					$this->setInfo("L'article {$_POST['nom']} a bien été crée !");
					$this->controllerTemplate = "Categories.template.html";
				
					if(($fileName = $this->tmpFile($article->getId())) !== false) {
						$article->setImage($fileName);
						$article->update();
					}
					else {
						$this->addError("<br/> Erreur lors de l'upload de l'image");
					}
					
				} 
				else {
					$this->setError(Form::toStringErrors($errors));
					$this->form->complete($_POST);
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
		else {
			$this->setError("Erreur url");
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
				
				/* Création de la vérification */
				$formFactory = new ModelFormBuilder();
				$formFactory->setModel($article);
				$this->form = $formFactory->buildForm();
				$this->form->addCheck('nom', new NoEmptyCheck());
				$this->form->addCheck('idCategorie', new NoEmptyCheck());
				$this->form->addCheck('idCategorie', new IntegerCheck());
				$this->form->addCheck('prix', new NoEmptyCheck());
				$this->form->addCheck('prix', new PositiveFloatCheck());
				$this->form->addCheck('nombre', new IntegerCheck());
				$this->form->addCheck('remise', new PositiveFloatCheck());	
			
				/* Vérification si vérification nécéssaire */
				if(isset($_POST) && !empty($_POST)) {
					
					/* Si l'évaluation réussi, on met à jour l'article */
					if(($errors = $this->form->evaluate($_POST)) === true) {
						
						if(($fileName = $this->tmpFile("article_".$article->getId())) !== false) {
							$this->form->complete($_POST);
							$article = $formFactory->buildModel($this->form, $article);
							$article->setImage($fileName);
							$article->update();
							
							$this->controllerTemplate = "Categories.template.html";
							$this->title = $this->name;
							$this->setInfo("L'article {$article->getNom()} a bien été modifié !");
							$this->ariane->clearFunction();
						
						}
						else {
							$this->form->complete($_POST);
							$this->setError("<br/> Erreur lors de l'upload de l'image");
							$this->controllerTemplate = "Articles_Modify.template.html";
						}
						
					} 
					else {
						$this->form->complete($_POST);
						$this->setError(Form::toStringErrors($errors));
						/* Formulaire */
						$this->controllerTemplate = "Articles_Modify.template.html";
					}
					
				} // if(isset($_POST) && !empty($_POST))
				else {
					$this->form->complete($article->getData());
						
					$this->controllerTemplate = "Articles_Modify.template.html";
					
				
				}
			}// if(!empty($article))
			else {
				$this->setError("L'article à modifier n'existe pas en base !");
				$this->controllerTemplate = "Categories.template.html";
				$this->title = $this->name;
			}
			
		}// if(isset($_GET['id']))
		else {
			$this->controllerTemplate = "Categories.template.html";
			$this->title = $this->name;
			$this->setError("Erreur lors de la tentative de modification de l'article.");	
		}
		
	}
	
	public function reappro() {
		
		if(isset($_GET['id_article'])) {
			
			$this->title = "Supprimer un article";
			$this->ariane->setFunction("Supprimer un article","index.php?service=Articles&function=delete&id_article={$_GET['id_article']}");
			
			if(isset($_POST['nombre']) && preg_match("/[0-9]+/", $_POST['nombre']) == 1) {
				
				$article = $this->getArticle();
				
				if($article == null) {
					$this->setError("Impossible de réapprovisionner l'article demandé");
				}
				else {
					$article->setNombre($article->getNombre()+$_POST['nombre']);
					$article->update();
					
					$this->setInfo("L'article demandé a été réapprovisionné comme demandé ");
					return;
				}
				
			}
			else {
				$this->setError("Impossible de réapprovisionner l'article demandé");
			}
			
		}
		else {
			$this->setError("Impossible de réapprovisionner l'article demandé");
		}

	}

	public function delete() {
		
		if(isset($_GET['id_article'])) {
			
			$this->title = "Supprimer un article";
			$this->ariane->setFunction("Supprimer un article","index.php?service=Articles&function=delete&id_category={$_GET['id_category']}&id_article={$_GET['id_article']}");
			
			if(isset($_GET['force'])) {
				
				$article = $this->getArticle();
				
				if($article == null) {
					$this->setError("L'article que vous souhaitez supprimer n'existe pas en base.");
					$this->controllerTemplate = "Categories.template.html";
					$this->title = $this->name;	
					$this->ariane->clearFunction();
				}
				else {
					$article->setRemoved(1);
					$article->update();
					$this->controllerTemplate = "Categories.template.html";
					$this->title = $this->name;	
					$this->ariane->clearFunction();
					$this->setInfo("L'article {$article->getNom()} a bien été supprimé !");
					return;
				}
				
			}
			else {
				$this->controllerTemplate = "Articles_Delete.template.html";
			}
			
		}
		else {
			$this->controllerTemplate = "Categories.template.html";
			$this->setTitle($this->name);
			$this->setError("Erreur lors de la tentative de suppression de l'article.");	
		}
		
	}
	
	public function tmpFile($name) {
		
		if(!isset($_FILES) || !isset($_FILES['image']) || $_FILES['image']['size'] == 0) {
			return "null.jpg";
		}
		
		$maxSize = 500000;
		$size = filesize($_FILES['image']['tmp_name']);
		
		if($size > $maxSize)
		{
			$this->addError("Image trop volumineuse : $size > $maxSize");
			return false;
		}
		
		$extension = strrchr($_FILES['image']['name'], '.'); 
		
		if(!in_array($extension, array('.png', '.gif', '.jpg', '.jpeg', '.svg'))) {
			$this->addError("Le type d'image n'est pas correct : .png, .gif, .jpg, .svg ou .jpeg autorisés");
			return false;
		}
		
		if(move_uploaded_file($_FILES['image']['tmp_name'], "../img/".$name.$extension)) {
			return $name.$extension;
		}
		else {
			$this->addError("Erreur lors de l'upload de l'image");
			return false;
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
		
		$datas = Model::toData($articles->select("nombre <= 0 AND removed = 0"));
		
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
		
		$datas = Model::toData($articles->select("nombre <= 5 AND nombre != 0 AND removed = 0"));
		
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