<?php

require_once 'src/bdd.php';

/* Récupération du controleur : */
$controller_name = "Categories"; /* Controleur par défaut : */

if(isset($_GET) && isset($_GET['service'])) {

	$controller_name = $_GET['service'];

}

/* Instanciation du controleur : */
require_once "controllers/$controller_name.class.php";

$controller = new $controller_name(bdd_connect());

print_r($controller);
die("$controller_name");

/* Vues : */
require_once 'lib/Twig/Autoloader.php';

Twig_Autoloader::register();

$loader = new Twig_Loader_Filesystem('templates');
$twig = new Twig_Environment($loader, array(
   // 'cache' => 'cache',
));

$template = $twig->loadTemplate("$controller_name.template.html");

echo $template->render($controller->getData());
