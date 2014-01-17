<?php

function includeLib($rac) {
	
	/* Importation classes principales */
	require_once "$rac/src/bdd.php";
	require_once "$rac/src/Controller.class.php";
	require_once "$rac/src/Model.class.php";
	require_once "$rac/src/Panier.class.php";
	require_once "$rac/src/PanierItem.class.php";
	
	/* Importation formulaires */
	require_once "$rac/src/form/Form.class.php";
	require_once "$rac/src/form/FormCheck.class.php";
	require_once "$rac/src/form/ModelFormBuilder.class.php";
	
	/* Importation modèles */
	$dir = opendir("$rac/src/data");
	
	while(($file = readdir($dir)) !== false) {
		if(!($file == ".") && !($file == "..")) {
			require_once "$rac/src/data/$file";
		}
	}

}

?>