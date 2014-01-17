<?php

abstract class Model {
	
	/* -------------------------------------------------------- */
	/*			FIELD(S)										*/
	/* -------------------------------------------------------- */
	public static $bdd;
	protected $tableName;
	protected $fields;
	protected $primary;
	protected $update;
	protected $data;
	protected $data_modified;
	
	/* -------------------------------------------------------- */
	/*			CONSTRUCTOR(S)									*/
	/* -------------------------------------------------------- */
	/**
	 * Constructeur de Model
	 * 
	 * @param String $tableName, le nom de la table en base
	 */
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
			/* Valeurs par défauts pour les champs : */
			$this->data[$line["Field"]] = null;
			$this->data_modified[$line["Field"]] = false;
			$this->fields[] = $line["Field"];
			
			if($line["Key"]=="PRI") {
				$this->primary = $line["Field"];
			}
		}
		
	}
	
	/* -------------------------------------------------------- */
	/*			METHOD(S)										*/
	/* -------------------------------------------------------- */
	/**
	 * 	Renvoie toute les entrées présente en base.
	 */
	public function selectAll() {
		
		return $this->select(null);
		
	}
	
	/**
	 * 
	 * Select avec clause WHERE.
	 * Attention, les paramètres internes à l'objet doivent être spécifiés de la manière suivante :nom_param
	 * et serons changés avant lancement de la requête.
	 * @param String $where conditions de la cause WHERE (si null pas de clause WHERE)
	 * @param Array $bind liste des paramètres extérieurs à l'objet "nom_param"=>valeur
	 * 
	 */
	public function select($where, $bind=array()) {
		
		$query = "";
		if(empty($where)) {
			$query = "SELECT * FROM {$this->tableName}";
		}
		else {
			$query = "SELECT * FROM {$this->tableName} WHERE ".$where;
		}
	
		return $this->queryObj($query, $bind);
		
	}
	
	/**
	 * 
	 * Mettre à jour en base l'objet actuellement manipulé
	 * 
	 * @return PDOStatement requête
	 * 
	 */
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
					$singleton = false;
				}
				
				$set .= "$field = :$field";
			
			}
		}
		
		$query .= $set." WHERE ".$this->getPrimaryKey();
		
		return $this->exec($query);
		
	}
	
	/**
	 * 
	 * Récupérer un objet par sa clef primaire
	 * @param Array $primary (éléments constituants la clef primaire)
	 * @return <T extends Model> Objet ou NULL si aucun élément trouvé
	 * 
	 */
	public function selectById($primary) {
		
		$this->data[$this->primary] = $primary;
		$datas = $this->select($this->getPrimaryKey());
		
		/* Si pas de retour renvoi null */
		if(sizeof($datas) == 0) {
			return null;
		}

		return $datas[0];
		
	}
	
	/**
	 * 
	 * Supprimer l'objet actuellement manipulé (par la clef primaire)
	 * @return PDOStatement requête
	 * 
	 */
	public function delete() {
		
		return $this->exec("DELETE FROM {$this->tableName} WHERE {$this->getPrimaryKey()}");
		
	}
	
	/**
	 * 
	 * Effectuer une requête sur la base.
	 * Attention, les paramètres doivent être spécifiés de la manière suivante :nom_param
	 * et seront changés avant lancement de la requête.
	 * @param String $query, la requête SQL
	 * @param Array $exteriorParams, paramètres non internes à l'objet manipulé "nom_param"=>value
	 * @return PDOStatement requête
	 * 
	 */
	public function exec($query, $exteriorParams=array()) {
		
		$query = str_replace(":table", $this->tableName, $query);
		
		$statement = Model::$bdd->prepare($query);
		
		$resultSet = array();
		
		/* Préparation de la requête */
		$this->bindParams($statement, $query, $exteriorParams);
		
		/* Cas d'erreur */
		if(!$statement->execute()) {
			
			$trace = debug_backtrace();
			print_r($statement->errorInfo()); 
			trigger_error(
			htmlentities(
				"Erreur lors de l'exécution de la requête $query " .
				' dans ' . $trace[0]['file'] .
				' à la ligne ' . $trace[0]['line']),
				E_USER_ERROR);
			
		}
		
		/* Renvoie l'objet PDOStatement */
		return $statement;
		
	}
	
	/**
	 * 
	 * Effectuer une requête retournant des résultats sur la base.
	 * Attention, les paramètres doivent être spécifiés de la manière suivante :nom_param
	 * et seront changés avant lancement de la requête.
	 * @param String $query, la requête SQL
	 * @param Array $exteriorParams, paramètres non internes à l'objet manipulé "nom_param"=>value
	 * @return Array résultats de la requête
	 * 
	 */
	public function query($query, $exteriorParams=array()) {
		
		$statement = $this->exec($query, $exteriorParams);
		
		$resultSet = array();
		
		/* Récupération des entrées : */
		while(($data = $statement->fetch()) !== false) {
			$resultSet[] = $data;
		}
		
		return $resultSet;
		
	}
	
	public function queryObj($query, $exteriorParams=array()) {
		
		$statement = $this->exec($query, $exteriorParams);
		
		$resultSet = array();
		
		/* Récupération des entrées : */
		while(($data = $this->build($statement)) !== false) {
			$resultSet[] = $data;
		}
		
		return $resultSet;
		
	}
	
	/**
	 * 
	 * Insérer l'objet actuel en base
	 * 
	 */
	public function insert() {
		
		/* construction de la requête */
		$fields = "";
		$values = "";
		$query = "";
		
		$singleton = true;
		
		/* pour chaque attribut */
		foreach($this->fields as $field) {
			if(!empty($this->data[$field]) && $field != $this->primary) {

				/* la petite virgule du bonheur */
				if(!$singleton) {
					$fields.=", ";
					$values .= ", ";
				}
				else {
					$singleton = false;
				}
				
				$fields .= $field;
				$values .= ":$field";
				
			}
		}
		
		$query = "INSERT INTO {$this->tableName} ($fields) VALUES ($values)";
		
		$statement = $this->exec($query);
		
		$this->data[$this->primary] = Model::$bdd->lastInsertId();
		$this->unModified();
		
		return $statement;
		
	}
	
	/**
	 * 
	 * Retourne la clause where clef primaire de cet objet.
	 * @return String where
	 * 
	 */
	private function getPrimaryKey() {
		
		return "$this->primary = :$this->primary";
		
	}
	
	/**
	 * 
	 * Créer un objet avec un résultat 
	 * 
	 * @return <? extends Model> objet
	 * 
	 */
	private function build($statement) {
		
		if(($line = $statement->fetch()) !== false) {
			
			$name = get_class($this);
			/* On instancie l'objet qui va bien */
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
	
	/**
	 * Remise à zéro des champs non-touchés
	 */
	private function unModified() {
		
		foreach($this->fields as $field) {
			$this->data_modified[$field] = false;
		}
		
	}
	
	/**
	 * 
	 * Bind Param pour tous les paramètres.
	 * @param PDOStatement $statement
	 * @param String $sql, sql de la requête
	 * @param Array $exteriorParams, paramètres extérieurs "nom_param"=>value
	 * 
	 */
	private function bindParams($statement, $sql, $exteriorParams) {
		
		$params = array();
		
		/* Récupération des paramètres */
		preg_match_all("/:\w+/", $sql, $params);

		/* Pour chaque paramètres */
		foreach($params[0] as $param) {
			if(!empty($param)) {
				$key = substr($param,1);
				
				/* Soit il est extérieur : */
				if(!empty($exteriorParams) && array_key_exists($key, $exteriorParams)) {
					$statement->bindParam("$param", $exteriorParams[$key]);
				}
				/* Soit il appartient à notre objet actuel : */
				else if(array_key_exists($key, $this->data)) {
					$statement->bindParam("$param", $this->data[$key]);
				}
				/* Soit erreur : */
				else {
					
					$trace = debug_backtrace();
					trigger_error(
					htmlentities(
						"Paramètre inconnu $pram ($sql) " .
						' dans ' . $trace[0]['file'] .
						' à la ligne ' . $trace[0]['line']),
						E_USER_ERROR);
					
				}
			}
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
	
	public static function toData($array) {
		
		if(!is_array($array)) {
			return $array->getData();
		}
		
		$results = array();
		
		foreach($array as $item) {
			if(!empty($item)) {
				$results[] = $item->getData();
			}
		}
		
		return $results;
		
	}
	
}

?>