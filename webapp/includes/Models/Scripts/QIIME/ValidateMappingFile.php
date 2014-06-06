<?php

namespace Models\Scripts\QIIME;
use Models\Scripts\DefaultScript;
use Models\Scripts\Parameters\VersionParameter;
use Models\Scripts\Parameters\HelpParameter;
use Models\Scripts\Parameters\TextArgumentParameter;
use Models\Scripts\Parameters\TrueFalseParameter;
use Models\Scripts\Parameters\TrueFalseInvertedParameter;
use Models\Scripts\Parameters\NewFileParameter;
use Models\Scripts\Parameters\OldFileParameter;
use Models\Scripts\Parameters\ChoiceParameter;
use Models\Scripts\Parameters\Label;

class ValidateMappingFile extends DefaultScript {

	private $scriptName;
	public function __construct(\Models\ProjectI $project) {
		$this->scriptName = "validate_mapping_file.py";
		parent::__construct($project);
	}

	public function initializeParameters() {
		$mappingFp = new OldFileParameter("--mapping_fp", $this->project);
		$mappingFp->requireIf();

		$verboseParameter = new TrueFalseInvertedParameter("--verbose");

		array_push($this->parameters,
			 new Label("Required Parameters"),
			 $mappingFp,
			 new Label("Optional Parameters"),
			 new TextArgumentParameter("--char_replace", "_", "/^.$/"),
			 new TrueFalseParameter("--not_barcoded"),
			 new TrueFalseParameter("--variable_len_barcodes"),
			 new TrueFalseParameter("--disable_primer_check"),
			 new TextArgumentParameter("--added_demultiplex_field", "", "/.*/"),// TODO same as split_libraries or run header 
			 new Label("Output Options"),
			 $verboseParameter,
			 new NewFileParameter("--output_dir", ""),
			 new TrueFalseParameter("--suppress_html")
		);
	}
	public function getScriptName() {
		return $this->scriptName;
	}
	public function getScriptTitle() {
		return "Validate map file";
	}
	public function getHtmlId() {
		return "validate_mapping_file";
	}
	public function renderHelp() {
		ob_start();
		echo "<p>{$this->getScriptTitle()}</p><p>The purpose of this script is to take your map file, and tell you if you did it right.</p>";
		include "views/{$this->getHtmlId()}.html";
		return ob_get_clean();
	}

}
