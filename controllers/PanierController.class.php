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

		return $panier->getItems();
		
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
		$article;
		$panier = $_SESSION['panier'];
		if (isset($_GET['id_article'])) {
			/*foreach ($panier->getItems() as $item) {
				if ($item->getIdArticle() == $_GET['id_article']) {
					$article = $item;
					break;
				}
			}*/

			/* TODO : VERIFIER L'EXISTENCE EN BASE DE L'ID */
			/* Info : si l'article existe déjà le panier va automatiquement incrémenter pas besoin de se
			 * prendre la tête */
			$panier->addItem($_GET['id_article'],1);

		}
	}
	
}

?>