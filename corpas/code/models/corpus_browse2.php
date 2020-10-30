<?php

namespace models;

class corpus_browse2 // models a corpus text or subtext
{

  private $_id; // the id number for the text in the corpus (obligatory)
	private $_parentText; // the parent text of this text (optional) – an instance of models\corpus_browse2
  private $_title; // the title of the text (optional)
  private $_date;
  private $_filepath; // the path to the text XML (simple texts only)
  private $_transformedText; // simple texts only
  private $_writers = array();  //array of models\writer objects

	private $_db;   // an instance of models\database

	public function __construct($id) {
		$this->_db = isset($this->_db) ? $this->_db : new database();
		$this->_id = $id;
		if ($id != "0") { // not the root corpus node, i.e. a text
			$this->_load();
		}
	}

	/**
	 * Populates the object from the DB
	 */
	private function _load() {
		$sql = <<<SQL
			SELECT title, partOf, filepath, date
				FROM text
				WHERE id = :id
SQL;
		$results = $this->_db->fetch($sql, array(":id" => $this->getId()));
		$textData = $results[0];
		$this->_setTitle($textData["title"]);
		if ($parentTextId = $textData["partOf"]) {    // create a parent text
			$this->_setParentText($parentTextId);
		}
		else {
			$this->_setParentText("0"); // the root corpus
		}
		if ($filepath = $textData["filepath"]) {
			$this->_setFilepath($filepath);
		}
		if ($date = $textData["date"]) {
			$this->_setDate($date);
		}
		$this->_setWriters();
	}

	// SETTERS

	/**
	 * Creates a new text instance for parent text
	 * @param $id
	 */
	private function _setParentText($id) {
		$this->_parentText = new corpus_browse2($id);
	}

	private function _setTitle($title) {
		$this->_title = $title;
	}

	private function _setFilepath($filepath) {
		$this->_filepath = $filepath;
	}

	private function _setDate($date) {
		$this->_date = $date;
	}

	/**
	 * Populates the array of models\writer objects for this text
	 */
	private function _setWriters() {
		$sql = <<<SQL
			SELECT writer_id
				FROM text_writer
				WHERE text_id = :id
SQL;
		$results = $this->_db->fetch($sql, array(":id" => $this->getId()));
		foreach ($results as $result) {
			$this->_writers[] = new writer2($result["writer_id"]);
		}
	}

	// GETTERS

	public function getId() {
		return $this->_id;
	}

	public function getTitle() {
		return $this->_title;
	}

	public function getDate() {
		return $this->_date;
	}

	/**
	 * @return models\text object
	 */
	public function getParentText() {
		return $this->_parentText;
	}

  public function getWriters() {
    return $this->_writers;
  }

  public function getFilepath() {
    return $this->_filepath;
  }

  public function getTransformedText() {
    $this->_transformedText = $this->_applyXSLT();
    return $this->_transformedText;
  }

  private function _applyXSLT() {
    if ($this->getFilepath() != '') {
      $text = new \SimpleXMLElement("../xml/" . $this->getFilepath(), 0, true);
      $xsl = new \DOMDocument;
      $xsl->load('corpus.xsl');
      $proc = new \XSLTProcessor;
      $proc->importStyleSheet($xsl);
      return $proc->transformToXML($text);
    }
  }

	/**
	 * Get child text info (on the fly to cut down on memory overhead)
	 * @return array of associative info ("id" => "title")
	 */
	public function getChildTexts() {
		$childTexts = array();
		$sql = <<<SQL
			SELECT id, title FROM text WHERE partOf = :id ORDER BY id ASC
SQL;
		$results = $this->_db->fetch($sql, array(":id" => $this->getId()));
		foreach ($results as $result) {
			$childTexts[$result["id"]] = $result["title"];
		}
		return $childTexts;
	}

  /**
   * Queries the DB for a list of text info
   * @return array of text and writer information
   */
  public function getTextList() {
    $sql = <<<SQL
      SELECT * FROM text WHERE partOf = '' ORDER BY CAST(id AS UNSIGNED) ASC
SQL;
    foreach ($this->_db->fetch($sql) as $textResult) {
      $textsInfo[$textResult["id"]] = $textResult;
      $sql = <<<SQL
        SELECT * FROM writer
          JOIN text_writer ON writer_id = id
          WHERE text_id = :textId
SQL;
      $writerResults = $this->_db->fetch($sql, array(":textId" => $textResult["id"]));
      $textsInfo[$textResult["id"]]["writers"] = $writerResults;
    }
    return $textsInfo;
  }




}
