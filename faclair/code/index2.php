<?php

namespace controllers;

require_once 'includes/htmlHeader2.php';

$module = isset($_GET["m"]) ? $_GET["m"] : "";
$action = isset($_GET["a"]) ? $_GET["a"] : "";

switch ($module) {
	case "entries":
		$controller = new entries();
		break;
	case "entry":
		$controller = new entry();
		break;
	case "sources":
		$controller = new sources();
		break;
	default:
		$controller = new home();
}

$controller->run($action);

require_once "includes/htmlFooter2.php";
