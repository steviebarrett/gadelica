<?php

namespace views;
use models;

class sources {

	private $_model;   // an instance of models\sources

	public function __construct($model) {
		$this->_model = $model;
	}

	public function show() {
		$html = '<div class="list-group list-group-flush">';
		foreach ($this->_model->getSources() as $nextSource) {
			$url = '?m=source&id=' . $nextSource;
			$html .= '<a href="' . $url . '" class="list-group-item list-group-item-action">' . models\Sources::getEmoji($nextSource) . ' ' . models\Sources::getRef($nextSource) . '</a>';
		}
		$html .= "<div class=\"list-group-item\"><small><a href=\"#\">[add]</a></small></div>";
		$html .= '</div>';
		echo $html;
	}

}
