<?php

abstract class Model {
	
	/* -------------------------------------------------------- */
	/*			FIELD(S)										*/
	/* -------------------------------------------------------- */
	protected $tableName;
	public static $bdd;
	protected $fields;
	protected $update;
	protected $data;
	protected $data_modified;
	
	/* -------------------------------------------------------- */
	/*			CONSTRUCTOR(S)									*/
	/* -------------------------------------------------------- */
	public function __construct($tableName) {

		$this->tableName = $tableName;
		$this->data = array();
		$this->fields = array();
		
		/* Récupération des informations concernant la table en base : */
		$statement = Model::$bdd->prepare("DESCRIBE $tableName");
		
		/* Cas d'erreur */
		if(!$statement->execute()) {
			print_r($statement->errorInfo()); 
			die("Model : impossible d'obtenir la description de la table : $tableName");
		}
		
		/* Récupération du nom des champs : */
		while(($line = $statement->fetch()) !== false) {
			$this->fields[] = $line["Field"];
		}
		
		foreach($this->fields as $field) {
			$this->data[$field] = null;
			$this->data_modified[$field] = false;
		}
		
	}
	
	/* -------------------------------------------------------- */
	/*			METHOD(S)										*/
	/* -------------------------------------------------------- */
	public function selectAll() {
		
		return $this->select(null);
		
	}
	
	public function select($where) {
		
		if(empty($where)) {
			$statement = Model::$bdd->prepare("SELECT * FROM {$this->tableName}");
		}
		else {
			$statement = Model::$bdd->prepare("SELECT * FROM {$this->tableName} WHERE ".$where);
		}
		
		$resultSet = array();
		
		/* Cas d'erreur */
		if(!$statement->execute()) {
			print_r($statement->errorInfo()); 
			die("Model : impossible d'exécuter la requête sur la table : $tableName");
		}
		
		/* Récupération des entrées : */
		while(($data = $this->build($statement)) !== false) {
			$resultSet[] = $data;
		}
		
		return $resultSet;
		
	}
	
	public function update() {
		
		/* construction de la requête */
		$query = "UPDATE {$this->tableName} ";
		
		$set = "SET ";
		$singleton = true;
		
		/* pour chaque champ */
		foreach($this->fields as $field) {
			if($this->data_modified[$field]) {
				
				/* plus d'un élément ajouté */
				if(!$singleton) {
					$set .= ", ";
				}
				else {
					$singleton = true;
				}
				
				$set .= "$field = :$field";
			
			}
		}
		
		$query .= $set." WHERE id = :id";
		
		$statement = Model::$bdd->prepare($query);
		
		/* Ajout des paramètres */
		foreach($this->fields as $field) {
			if($this->data_modified[$field]) {
				$statement->bindParam(":$field", $this->data[$field]);
				$this->data_modified[$field] = false;
			}
		}
		
		$statement->bindParam(":id", $this->getId());
		
		if(!$statement->execute()) {
			echo $query;
			print_r($statement->errorInfo()); 
			die("Model : Erreur lors de la mise à jour dans : $tableName");
		}
		
	}
	
	public function exec($query) {
		
		$statement = Model::$bdd->prepare($query);
		
		/* éxecution */
		if(!$statement->execute()) {
			echo $query;
			print_r($statement->errorInfo()); 
			die("Model : Erreur lors de l'execution dans : $tableName");
		}
		
	}
	
	public function get($query) {
		
		$statement = Model::$bdd->prepare($query);
		
		$resultSet = array();
		
		/* Cas d'erreur */
		if(!$statement->execute()) {
			echo($query);
			print_r($statement->errorInfo()); 
			die("Model : impossible d'exécuter la requête sur la table : $tableName");
		}
		
		/* Récupération des entrées : */
		while(($data = $this->build($statement)) !== false) {
			$resultSet[] = $data;
		}
		
		return $resultSet;
		
	}
	
	public function insert() {
		
		/* construction de la requête */
		$query = "INSERT INTO {$this->tableName} (";
		$singleton = true;
		
		/* pour chaque attribut */
		foreach($this->fields as $field) {
			if(!empty($this->data[$field])) {

				/* la petite virgule du bonheur */
				if(!$singleton) {
					$query.=", ";
				}
				else {
					$singleton = false;
				}
				
				$query .= $field;
				
			}
		}
		
		$query .= ") VALUES (";
		
		$singleton = true;
		
		/* pour chaque attribut */
		foreach($this->fields as $field) {
			if(!empty($this->data[$field])) {

				/* la petite virgule du bonheur */
				if(!$singleton) {
					$query.=", ";
				}
				else {
					$singleton = false;
				}
				
				$query .= ":$field";
				
			}
		}
		
		$query .= ")";
		
		$statement = Model::$bdd->prepare($query);
		
		/* maintenant les paramètres... */
		foreach($this->fields as $field) {
			if(!empty($this->data[$field])) {
				$statement->bindParam(":$field", $this->data[$field]);
			}
		}
		
		/* éxecution */
		if(!$statement->execute()) {
			echo $query;
			print_r($statement->errorInfo()); 
			die("Model : Erreur lors de l'insertion dans : $tableName");
		}
		
		$this->setId(Model::$bdd->lastInsertId());
		$this->unModified();
		
	}
	
	private function build($statement) {
		
		if(($line = $statement->fetch()) !== false) {
			
			$name = get_class($this);
			/* On instencie l'objet qui va bien */
			$data = new $name();
			
			/* on parcourt les champs et ont les set */
			foreach($line as $field => $value) {
				if(!is_int($field)) {
					$data->__set($field, $value);
				}
			}
			
			/* on déclare que l'objet n'a pas été modifié */
			$data->unModified();
			
			/* on livre le paquet */
			return $data;
			
		}
		else {
			return false;
		}
		
	}
	
	private function unModified() {
		
		foreach($this->fields as $field) {
			$this->data_modified[$field] = false;
		}
		
	}
	
	/* -------------------------------------------------------- */
	/*			MAGIC METHOD(S)									*/
	/* -------------------------------------------------------- */
	/* Setters : */
	public function __set($name, $value) {
		
		/* Si la propriétée existe alors on la set */
		if(array_key_exists($name, $this->data)) {
			$this->data[$name] = $value;
			$this->data_modified[$name] = true;
		}
		else {
			/* Sinon erreur : */
			$trace = debug_backtrace();
	        trigger_error(
		        htmlentities(
		            "Impossible de modifier l'attribut $name ($value), car celui-ci n'existe pas : " .
		            ' dans ' . $trace[0]['file'] .
		            ' à la ligne ' . $trace[0]['line']),
		            E_USER_ERROR);
		}
		
	}
	
	/* Getters : */
	public function __get($name) {

		/* Si la propriétée existe alors on la retourne */
		if(array_key_exists($name, $this->data)) {
			return $this->data[$name];
		}
		else {

			/* Sinon erreur : */
			$trace = debug_backtrace();
	        trigger_error(
		        htmlentities(
		            "Impossible de récupérer l'attribut $name, car celui-ci n'existe pas : " .
		            ' dans ' . $trace[0]['file'] .
		            ' à la ligne ' . $trace[0]['line']),
		            E_USER_ERROR);
		            
		}
		
	}
	
	/* Methodes : */
	public function __call($name, $arguments) {
		
		/* getters */
		if(substr($name,0,3) == "get") {
			$varName = strtolower(substr($name,3,1)).substr($name,4);
			return $this->__get($varName);
		}
		
		/* setters */
		if(substr($name,0,3) == "set") {
			$varName = strtolower(substr($name,3,1)).substr($name,4);
			return $this->__set($varName, $arguments[0]);
		}
		
	}
	
	/* -------------------------------------------------------- */
	/*			GETTER(S) & SETTER(S)							*/
	/* -------------------------------------------------------- */
	public function getData() {
		return $this->data;
	}
	
	public static function toData($models) {
		
		if(!is_array($models)) {
			return $models->getData();
		}
		
		$return = array();
		
		foreach($models as $model) {
			$return[] = $model->getData();
		}
		
		return $return;
		
	}
	
}

?>