<?php

require_once './src/bdd.php';

/* Récupération du controleur : */
$controller_name = "Categories"; /* Controleur par défaut */

if(isset($_GET) && isset($_GET['service'])) {

	$controller_name = $_GET['service'];

}

/* Instanciation du controleur : */
require_once "./controllers/$controller_name.class.php";

die("test");

$controller = new $controller_name(bdd_connect());

/* Vues : */
require_once './lib/Twig/Autoloader.php';

Twig_Autoloader::register();

$loader = new Twig_Loader_Filesystem('./templates');
$twig = new Twig_Environment($loader, array(
   'debug' => true
));

echo $twig->render("$controller_name.template.html", 
$controller->getData());

?>
