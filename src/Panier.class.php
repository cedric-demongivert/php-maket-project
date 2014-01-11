<?php

class Panier {
	
	private $items[];
	
	public function __construct() {
		
	}
	
	public function addItem($newItem, $quantity) {
		
		foreach($this->items as $item) {
			if($item->isArticle($newItem)) {
				$item->add($quantity);
				return;
			}
		}
		
		$this->items[] = new PanierItem($newItem, $quantity);
		
	}
	
	public function remove($toDelete) {

		$i = 0;
		
		foreach($this->items as $item) {
			if($item->isArticle($toDelete)) {
				break;
			}
			$i++;
		}
		
		array_splice($this->items, $i, 1);
		
	}
	
	public function getItem($id) {
		return $this->items[$id];
	}
	
	public function getItems() {
		return $this->items;
	}
	
}

?>