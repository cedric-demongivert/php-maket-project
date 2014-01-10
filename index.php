<?php
session_start();

/* Connexion bdd */
require_once './src/bdd.php';
require_once "./src/Controller.class.php";

/* Récupération du controleur : */
$controller_name = "Categories"; /* Controleur par défaut */

if(isset($_GET) && isset($_GET['service'])) {

	$controller_name = $_GET['service'];

}

/* Instanciation du controleur : */
require_once "./controllers/$controller_name.class.php";

$controller = new $controller_name(bdd_connect());
$controller->init();

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

/* Affichage de la page */
echo $twig->render("$controller_name.template.html", 
$controller);

?>
