<?php

function bdd_connect() {
	return new PDO("mysql:host=localhost;dbname=catalogue","","");
}