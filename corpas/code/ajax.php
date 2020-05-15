<?php

require_once 'includes/include.php';

switch ($_GET["action"]) {
  case "getContext":
    $handler = new XmlFileHandler($_GET["filename"]);
    $context = $handler->getContext($_GET["id"], 20);
    echo json_encode($context);
    break;
  case "getDictionaryResults":
    $locs = $_GET["locs"];
    $locations = explode('|', $locs);
    $filename = "";
    $fileHandler = null;
    $results = array();
    foreach ($locations as $location) {
      $elems = explode(' ', $location);
      if ($filename != $elems[0]) {
        $filename = $elems[0];
        $fileHandler = new XmlFileHandler($filename);
      }
      $context = $fileHandler->getContext($elems[1], 5);
      $results[] = $context;
    }
    echo json_encode($results);
    break;
}
