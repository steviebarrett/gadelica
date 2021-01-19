<?php

namespace models;

require_once 'includes/include.php';

switch ($_REQUEST["action"]) {
	case "getUsername":
		$user = users::getUser($_GET["email"]);
		if ($user) {
			$_SESSION["email"] = $_GET["email"];
		}
		echo json_encode(array("firstname"=>$user->getFirstName(), "lastname"=>$user->getLastName()));
		break;
  case "getContext":
    $handler = new xmlfilehandler($_GET["filename"]);
    $context = $handler->getContext($_GET["id"], $_GET["preScope"], $_GET["postScope"], true, false, true);
    echo json_encode($context);
    break;
	case "getSlips":
		$slipInfo = collection::getAllSlipInfo($_GET["offset"], $_GET["limit"], $_GET["search"], $_GET["sort"], $_GET["order"]);
		echo json_encode($slipInfo);
		break;
	case "updatePrintList":
			if ($_GET["addSlip"]) {
				$_SESSION["printSlips"][$_GET["addSlip"]] = $_GET["addSlip"];
			} else if ($_GET["removeSlip"]) {
				unset($_SESSION["printSlips"][$_GET["removeSlip"]]);
			}
			//return the size of the array
			echo json_encode(array("count" => count($_SESSION["printSlips"])));
		break;
  case "loadSlip":
    $slip = new slip($_GET["filename"], $_GET["id"], $_GET["auto_id"], $_GET["pos"], $_GET["preContextScope"], $_GET["postContextScope"]);
    $slip->updateResults($_GET["index"]); //ensure that "view slip" (and not "create slip") displays
    $filenameElems = explode('_', $slip->getFilename());
    $textId = $filenameElems[0];
    $results = array("locked"=>$slip->getLocked(), "auto_id"=>$slip->getAutoId(), "owner"=>$slip->getOwnedBy(),
	    "starred"=>$slip->getStarred(), "translation"=>$slip->getTranslation(), "notes"=>$slip->getNotes(),
      "preContextScope"=>$slip->getPreContextScope(), "postContextScope"=>$slip->getPostContextScope(),
      "wordClass"=>$slip->getWordClass(), "categories"=>$slip->getSenseCategories(),
      "lastUpdated"=>$slip->getLastUpdated(), "textId"=>$textId, "slipMorph"=>$slip->getSlipMorph()->getProps());
    //code required for modal slips
    $handler = new xmlfilehandler($_GET["filename"]);
    $context = $handler->getContext($_GET["id"], $results["preContextScope"], $results["postContextScope"]);
    $results["context"] = $context;
    $results['isOwner'] = $slip->getOwnedBy() == $_SESSION["user"];
    $user = users::getUser($_SESSION["user"]);
    $superuser = $user->getSuperuser();
    $results["canEdit"] =  $slip->getOwnedBy() == $_SESSION["user"] || $superuser || (!$slip->getIsLocked()) ? 1 : 0;
    //
    echo json_encode($results);
    break;
    //the following used for the gramar site
	case "loadSlipData":
		$result = collection::getSlipInfoBySlipId($_GET["id"]);
		$slipInfo = $result[0];
		$handler = new xmlfilehandler($slipInfo["filename"]);
		$context = $handler->getContext($slipInfo["id"], $slipInfo["preContextScope"], $slipInfo["postContextScope"]);
		$slipInfo["context"] = $context;
		echo json_encode($slipInfo);
		break;
	case "getSenseCategories":
		$categories = sensecategories::getAllUnusedCategories($_GET["slipId"],
			$_GET["headword"], $_GET["wordclass"]);
		echo json_encode($categories);
		break;
  case "saveSlip":
    $slip = new slip($_POST["filename"], $_POST["id"], $_POST["auto_id"], $_POST["pos"],
      $_POST["preContextScope"], $_POST["postContextScope"]);
    unset($_POST["action"]);
    $slip->saveSlip($_POST);
    echo "success";
    break;
  case "saveCategory":
    sensecategories::saveCategory($_POST["slipId"], $_POST["categoryName"]);
    collection::touchSlip($_POST["slipId"]);
    echo "success";
    break;
  case "deleteCategory":
    sensecategories::deleteCategory($_POST["slipId"], $_POST["categoryName"]);
    collection::touchSlip($_POST["slipId"]);
    echo "success";
    break;
	case "renameSense":
		sensecategories::renameSense($_GET["lemma"], $_GET["wordclass"], $_GET["oldName"], $_GET["newName"]);
		break;
  case "getDictionaryResults":
    $locs = $_POST["locs"];
    $locations = explode('|', $locs);
    $filename = "";
    $fileHandler = null;
    $results = array();
    foreach ($locations as $location) {
      $elems = explode(' ', $location);
      if ($filename != $elems[0]) {
        $filename = $elems[0];
        $fileHandler = new xmlfilehandler($filename);
      }
      $context = $fileHandler->getContext($elems[1], 8, 8);
      $context["date"] = $elems[2];   //return the date of language
      $context["auto_id"] = $elems[3]; //return the auto_id (slip id)
      $context["title"] = str_replace("\\", " ", $elems[4]);   //return the title
      $context["page"] = $elems[5]; //return the page no
      $results[] = $context;
    }
    echo json_encode($results);
    break;
	case "getGrammarInfo":
		$grammarInfo = lemmas::getGrammarInfo($_GET["id"], $_GET["filename"]);
		echo json_encode($grammarInfo);
		break;
	case "saveLemmaGrammar":
		echo lemmas::saveLemmaGrammar($_GET["id"], $_GET["filename"], $_GET["headwordId"],
			$_GET["slipId"], $_GET["grammar"]);
		break;
	case "requestUnlock":
			collection::requestUnlock($_GET["slipId"]);
		break;
	case "setGroup":
		users::updateGroupLastUsed($_GET["groupId"]);
		break;
	default:
		echo json_encode(array("error"=>"undefined action"));
}
