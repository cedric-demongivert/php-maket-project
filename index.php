<?php
session_start();

/* Connexion bdd */
require_once './src/bdd.php';
require_once "./src/Controller.class.php";
require_once "./src/Model.class.php";

Model::$bdd = bdd_connect();

/* Importation models */
$dir = opendir("./src/data");

while(($file = readdir($dir)) !== false) {
	if(!($file == ".") && !($file == "..")) {
		require_once "./src/data/$file";
	}
}

/* Récupération du controleur : */
$controller_name = "Categories"; /* Controleur par défaut */

if(isset($_GET) && isset($_GET['service'])) {

	$controller_name = $_GET['service'];

}

/* Instanciation du controleur : */
require_once "./controllers/$controller_name.class.php";

$controller = new $controller_name();

if(isset($_GET) && isset($_GET["function"])) {

		$functionName = $_GET["function"];
		$controller->$functionName();
	
}


/* Vues : */
require_once './lib/Twig/Autoloader.php';

/* Paramétrage Twig (moteur de templates) */
Twig_Autoloader::register();

/* Indiquer le chemin vers le dossier contenant les templates */
$loader = new Twig_Loader_Filesystem('./templates');

/* Création de l'environment (options) */
$twig = new Twig_Environment($loader, array(
   'debug' => true
));

/* Tableau de controllers */
$data = array();
$data['controller'] = $controller;
$data[$controller->getName()] = $controller;

foreach ($controller->getIncludedControllers() as $includedController) {
	$data[$includedController->getName()] = $includedController;
}

if(isset($_SESSION['user'])) {
	$data['user'] = $_SESSION['user'];
}

/* Affichage de la page */
echo $twig->render($controller->getTemplate(), $data);

?>
