<?php

require_once "./controllers/Ariane.class.php";

class PanierController extends Controller {
	
	public function __construct() {
		parent::__construct("panier", "Panier.template.html");
		$this->title = "Panier";
		
		if (!isset($_SESSION['panier'])) {
			$_SESSION['panier'] = new Panier();
		} 

	}
	
	public function getArticles() {
		
		$panier = $_SESSION['panier'];
		
		$results = array();
		
		$article = new Article();
		
		foreach($panier->getItems() as $item) {
			
			$results[] = $article->selectById($item->getIdArticle());
			
		}
		
		$results = Model::toData($results);
		
		$i = 0;
		foreach($panier->getItems() as $item) {
				
			$results[$i]['nombre'] = $item->getQuantity();
			$i++;
			
		}
		 
		return $results;
		
	}
	
	public function getArticle($id) {
		$article = new Article();
		if (isset($_SESSION['panier'])) {
			$article->selectById($id);
		} else {
			$article->setId($id);
		}
		return $article;
	}
	
	public function ajouter() {
		$article = new Article();
		$panier = $_SESSION['panier'];
		if (isset($_GET['id_article'])) {
			if (($article->selectById($_GET['id_article'])) != null) {
				$panier->addItem($_GET['id_article'],1);
				header('Location:./index.php?service=PanierController');
				exit();
			}
		}
	}
	
	public function remove() {
		if (isset($_SESSION['panier']) && isset($_GET['id_article'])) {
			$_SESSION['panier']->remove($_GET['id_article']);
		}
	}
	
	public function addQuantity() {
		$panier = $_SESSION['panier'];
		if (isset($_GET['id_article'])) {
			$panier->addItem($_GET['id_article'], $_POST['nombre']);
		}
	}
	
	public function validate() {
		if (isset($_SESSION['user'])) {
			$article = new Article();
			$commande = new Commande();
			$commande->setIdUser($_SESSION['user']->getId());
			$commande->insert();
			$reservation = new Reservation();
			foreach ($_SESSION['panier']->getItems() as $item) {
				$article->selectById($item->getIdArticle());
				if ($item->getQuantity() > $article->getNombre()) {
					header('Location: ');
					exit();
				}
				$reservation->setIdArticle($article->getId());
				$reservation->setIdCommande($commande->getId());
				$reservation->setNombre($item->getQuantity());
				$reservation->setPrix($item->getQuantity()*$article->getPrix());
				$reservation->insert();
			}
		} else {
			header('Location: index.php?service=Users&function=connect');
			exit();
		}
	}
	
}

?>