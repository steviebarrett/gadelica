<?php

namespace models;

class writer2
{

	private $_id;
	private $_surnameGD;
	private $_forenamesGD;
	private $_surnameEN;
	private $_forenamesEN;
	private $_title;
	private $_nickname;
	private $_yearOfBirth;
	private $_yearOfDeath;
	private $_origin;

  private $_db; //an instance of models\database

	public function __construct($id) {
		$this->_db = isset($this->_db) ? $this->_db : new database();
		$this->_id = $id;
		if ($id != "0") {
			$this->_load();
		}
	}

	/**
	 * Populates the object with info from the DB
	 */
	private function _load() {
		$sql = <<<SQL
			SELECT surname_gd, forenames_gd, surname_en, forenames_en, title, nickname, yob, yod, `where`
				FROM writer
				WHERE id = :id
SQL;
		$results = $this->_db->fetch($sql, array(":id" => $this->getId()));
		$writerData = $results[0];
		$this->_setSurnameGD($writerData["surname_gd"]);
		$this->_setForenamesGD($writerData["forenames_gd"]);
		$this->_setSurnameEN($writerData["surname_en"]);
		$this->_setForenamesEN($writerData["forenames_en"]);
		$this->_setTitle($writerData["title"]);
		$this->_setNickname($writerData["nickname"]);
		$this->_setYearOfBirth($writerData["yob"]);
		$this->_setYearOfDeath($writerData["yod"]);
		$this->_setOrigin($writerData["where"]);
	}

	// SETTERS

	private function _setSurnameGD($name) {
		$this->_surnameGD = $name;
	}

	private function _setForenamesGD($names) {
		$this->_forenamesGD = $names;
	}

	private function _setSurnameEN($name) {
		$this->_surnameEN = $name;
	}

	private function _setForenamesEN($names) {
		$this->_forenamesEN = $names;
	}

	private function _setTitle($title) {
		$this->_title = $title;
	}

	private function _setNickname($name) {
		$this->_nickname = $name;
	}

	private function _setYearOfBirth($year) {
		$this->_yearOfBirth = $year;
	}

	private function _setYearOfDeath($year) {
		$this->_yearOfDeath = $year;
	}

	private function _setOrigin($place) {
		$this->_origin = $place;
	}

	// GETTERS

	public function getId() {
		return $this->_id;
	}

	public function getSurnameGD() {
		return $this->_surnameGD;
	}

	public function getForenamesGD() {
		return $this->_forenamesGD;
	}

	public function getSurnameEN() {
		return $this->_surnameEN;
	}

	public function getForenamesEN() {
		return $this->_forenamesEN;
	}

	public function getTitle() {
		return $this->_title;
	}

	public function getNickname() {
		return $this->_nickname;
	}

	public function getYearOfBirth() {
		return $this->_yearOfBirth;
	}

	public function getYearOfDeath() {
		return $this->_yearOfDeath;
	}

	public function getOrigin() {
		return $this->_origin;
	}

	/**
	 * Queries the database (on the fly to aid performance),
	 *  creates and returns text objects for this writer
	 * @return array of models\text objects
	 */
	public function getTexts() {
		$texts = array();
		$sql = <<<SQL
			SELECT text_id FROM text_writer WHERE writer_id = :writerId
SQL;
		$results = $this->_db->fetch($sql, array(":writerId" => $this->getId()));
		foreach ($results as $result) {
			$texts[$result["text_id"]] = new text_sql($result["text_id"]);
		}
		return $texts;
	}

	public static function getWriters() {
		$writers = array();
		$db = new database();
		$sql = <<<SQL
			SELECT id, surname_gd FROM writer ORDER by surname_gd ASC
SQL;
		$results = $db->fetch($sql);
		foreach ($results as $result) {
			$writers[] = new writer2($result["id"]);
		}
		return $writers;
	}

}
