<?php

require_once "./controllers/Arianle.class.php";

class PanierController extends Controller {
	
	public function __construct() {
		parent::__construct("paniercontroller", "Panier.template.html");
		$this->title = "Panier";
	}
	
	public function getArticles() {
		$panier = new Panier();
		if (isset($_SESSION['panier'])) {
			return $_SESSION['panier']->items;
		} else {
			$_SESSION['panier'] = $panier;
			return $_SESSION['panier'];
		}
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
	
	public function ajouter($article) {
		$panier = new Panier();
		if (isset($_SESSION['panier'])) {
			$panier.addItem($article);
			$_SESSION['panier'].add($article);
		} else {
			$panier.addItem($article);
			$_SESSION['panier'] = $panier;
		}
	}
	
}

?>