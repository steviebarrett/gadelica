<?php

class SearchController
{
  private $_db, $_dbResults, $_resultCount;

  public function __construct() {
    if (!isset($this->_db)) {
      $this->_db = new Database();
    }
    $_GET["pp"] = isset($_GET["pp"]) ? $_GET["pp"] : 10; // number of results per page
    $_GET["page"] = isset($_GET["page"]) ? $_GET["page"] : 1; // results page number

    if (!isset($_REQUEST["action"])) {
      $_REQUEST["action"] = "newSearch";
    }

    switch ($_REQUEST["action"]) {
      case "newSearch":
        $searchView = new SearchView(); // gets parameters from URL
        $minMaxDates = $this->_getMinMaxDates(); // search database for earliest and latest years
        $searchView->writeSearchForm($minMaxDates); // prints HTML for form
        break;
      case "runSearch":
        $searchView = new SearchView();
        //check if there is an existing result set, if not then run the query
        $this->_resultCount = !isset($_GET["hits"]) ? $this->_getDBSearchResultsTotal($_GET) : $_GET["hits"];
        $searchView->setHits($this->_resultCount);
        //fetch the results required for this page
        $this->_dbResults = $this->_getDBSearchResults($_GET);
        //fetch the results from file if
        $results = ($_GET["view"] == "corpus") ? $this->getFileSearchResults() : $this->_dbResults;
        $searchView->writeSearchResults($results, $this->_resultCount);
        break;
    }
  }

  /*
   * Takes an array of database results and searches through the XML corpus for matches
   */
  public function getFileSearchResults() {
    $fileResults = array();
    $i = 0;
    foreach ($this->_dbResults as $result) {
      $id = $result["id"];
      $fileResults[$i]["id"] = $id;
      $fileResults[$i]["lemma"] = $result["lemma"];
      $fileResults[$i]["pos"] = $result["pos"];
      $fileResults[$i]["date_of_lang"] = $result["date_of_lang"];
      $fileResults[$i]["filename"] = $result["filename"];
      $fileResults[$i]["auto_id"] = $result["auto_id"];
      $fileResults[$i]["title"] = $result["title"];
      $fileResults[$i]["page"] = $result["page"];
      $i++;
    }
    return $fileResults;
  }

  private function _getDBSearchResults($params) {
    $perpage = $params["pp"];
    $pagenum = $params["page"];
    $offset = $pagenum == 1 ? 0 : ($perpage * $pagenum) - $perpage;
    return array_slice($_SESSION["results"], $offset, $perpage);
  }

  /*
   * Form and return the query required for a wordform search
   * Returns an associative array with the SQL and search term
   */
  private function _getWordformQuery($params) {
    $search = $params["search"];
    $searchPrefix = "[[:<:]]";  //default to word boundary at start
    if ($params["accent"] != "sensitive") {
      $search = Functions::getAccentInsensitive($search, $params["case"] == "sensitive");
    }
    if ($params["lenition"] != "sensitive") {
      $search = Functions::getLenited($search);
      $search = Functions::addMutations($search);
    } else {
      //deal with h-, n-, t-
      $searchPrefix = "^";  //don't use word boundary at start of search, but start of string instead
    }
    $whereClause = "";
    $search = $searchPrefix . $search . "[[:>:]]";  //word boundary
    if ($params["case"] == "sensitive") {   //case sensitive
      $whereClause .= "wordform_bin REGEXP ?";
    } else {                              //case insensitive
      $whereClause .= "wordform REGEXP ?";
    }
    $selectFields =  "lemma, l.filename AS filename, l.id AS id, wordform, pos, date_of_lang, title, page, medium, s.auto_id AS auto_id";
    $sql = <<<SQL
        SELECT {$selectFields} FROM lemmas AS l
          LEFT JOIN slips s ON l.filename = s.filename AND l.id = s.id
          WHERE {$whereClause}
SQL;
    return array("sql" => $sql, "search" => $search);
  }

  /*
   * Query to get the size of the complete result set
   * Stores the query results in a SESSION variable
   * Return int: count of the size of the set
   */
  private function _getDBSearchResultsTotal($params) {
    switch ($params["date"]) {
      case "random":
        $orderBy = "RAND()";
        break;
      case "asc":
        $orderBy = "date_of_lang ASC";
        break;
      case "desc":
        $orderBy = "date_of_lang DESC";
        break;
      default:
        $orderBy = "filename, id";
    }
    if ($params["mode"] == "headword") {    //lemma
      $query["search"] = $params["search"];
      $query["sql"] = <<<SQL
        SELECT l.filename AS filename, l.id AS id, wordform, pos, lemma, date_of_lang, title, page, medium, s.auto_id as auto_id FROM lemmas AS l
            LEFT JOIN slips s ON l.filename = s.filename AND l.id = s.id
            WHERE lemma = ?
SQL;
    } else {                               //wordform
      $query = $this->_getWordformQuery($params);
    }
    if ($params["selectedDates"]) {
      $query["sql"] .= $this->_getDateWhereClause($params);
    }
    $query["sql"] .= $this->_getMediumWhereClause($params);
    $query["sql"] .= <<<SQL
        ORDER BY {$orderBy}
SQL;
    $_SESSION["results"] = $this->_db->fetch($query["sql"], array($query["search"]));
    return count($_SESSION["results"]);
  }

  private function _getDateWhereClause($params) {
    $dates = explode('-', $params["selectedDates"]);
    $whereClause = " AND date_of_lang >= {$dates[0]} AND date_of_lang <= {$dates[1]} ";
    return $whereClause;
  }

  private function _getMediumWhereClause($params) {
    $whereClause = "";
    if (count($params["medium"]) == 3) {
      return $whereClause;    //don't bother with restrictions if all selected
    }
    $whereClause = " AND (";
    foreach ($params["medium"] as $medium) {
      $mediumString[] = " medium = '{$medium}' ";
    }
    $whereClause .= implode(" OR ", $mediumString);
    $whereClause .= ") ";
    return $whereClause;
  }

  //Retrieves the minimum and maximum dates of language in the database
  private function _getMinMaxDates() {
    $sql = <<<SQL
        SELECT MIN(date_of_lang) AS min, MAX(date_of_lang) AS max FROM lemmas
            WHERE date_of_lang != ''
SQL;
    $result = $this->_db->fetch($sql, array());
    return $result[0];
  }
}
