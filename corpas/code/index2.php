<?php

require_once "includes/htmlHeader.php";

include_once 'controllers/TextController.php';
include_once 'controllers/SearchCorpusController.php';
include_once 'models/CorpusModel.php';
include_once 'models/TextModel.php';
include_once 'models/SearchCorpusModel.php';
include_once 'views/CorpusView.php';
include_once 'views/TextView.php';

$action = isset($_GET['action']) ? $_GET['action'] : 'index';
$module = isset($_GET['module']) ? $_GET['module'] : '';
//$id =

switch($module) {
    case 'browseCorpus':
        $controller = new TextController('https://dasg.ac.uk/corpus/_0');
        break;
    case 'viewText':
        $controller = new TextController(); // start here
        break;
    case 'searchCorpus':
        $controller = new SearchCorpusController();
        break;
    default:
      echo <<<HTML
        <div class="list-group list-group-flush">
          <a class="list-group-item list-group-item-action" href="index2.php?module=browseCorpus">browse corpus</a>
          <a class="list-group-item list-group-item-action" href="index2.php?module=searchCorpus&action=newSearch">search corpus</a>
        </div>
HTML;
}

require_once "includes/htmlFooter.php";
