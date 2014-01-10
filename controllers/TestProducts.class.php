<?php
require_once "./src/Controller.class.php";
require_once "./src/data/Product.class.php";

class TestProducts extends Controller {
	
	public function getData() {
		
		$product = Product::create("Test",20,0,"null.png",0);
		$product->save($this->bdd);
		
		return array();
		
	}
	
}

?>