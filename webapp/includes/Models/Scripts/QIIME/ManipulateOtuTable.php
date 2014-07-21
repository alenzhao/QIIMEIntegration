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

class ManipulateOtuTable extends DefaultScript {

	public function initializeParameters() {
		parent::initializeParameters();

		$inputFp = new OldFileParameter("--input-fp", $this->project);
		$inputFp->requireIf();
		$outputFp = new NewFileParameter("--output-fp", "");
		$outputFp->requireIf();

		// action
		$action = new ChoiceParameter("action", "summarize-table", 
			array("summarize-table", "convert"));

		// summarize-table
		$qualitative = new TrueFalseParameter("--qualitative");
		$qualitative->excludeButAllowIf($action, "summarize-table");
		$suppressMd5 = new TrueFalseParameter("--suppress-md5");
		$suppressMd5->excludeButAllowIf($action, "summarize-table");

		//convert
		$sparseToDense = new TrueFalseParameter("--sparse-biom-to-dense-biom");
		$denseToSparse = new TrueFalseParameter("--dense-biom-to-sparse-biom");
		$biomToClassic = new TrueFalseParameter("--biom-to-classic-table");
		$biomConversionDirection = $sparseToDense->linkTo($denseToSparse, "Sparse to dense or vice versa");
		$conversionType = $biomToClassic->linkTo($biomConversionDirection, "Conversion type");
		$sampleMetadataFp = new OldFileParameter("--sample-metadata-fp", $this->project);
		$matrixType = new ChoiceParameter("--matrix-type", "sparse",
			array("sparse", "dense"));
		$headerKey = new TextArgumentParameter("--header-key", "", "/.*/");
		$outputMetadataId = new TextArgumentParameter("--output-metadata-id", "", TextArgumentParameter::PATTERN_NO_WHITE_SPACE);
		$processObsMetadata = new ChoiceParameter("--process-obs-metadata", "naive",
			array("taxonomy", "naive", "sc_separated"));
		$tableType = new ChoiceParameter("--table-type", "",
			array("metabolite table", "gene table", "otu table", "pathway table",
			"function table", "ortholog table", "taxon table"));

		$conversionType->excludeButAllowIf($action, "convert");
		$sampleMetadataFp->excludeButAllowIf($action, "convert");
		$matrixType->excludeButAllowIf($action, "convert");
		$processObsMetadata->excludeButAllowIf($action, "convert");
		$tableType->excludeButAllowIf($action, "convert");

		$sampleMetadataFp->excludeIf($conversionType);
		$matrixType->excludeIf($conversionType);
		$headerKey->excludeButAllowIf($conversionType, $biomToClassic->getName());
		$outputMetadataId->excludeButAllowIf($conversionType, $biomToClassic->getName());
		$processObsMetadata->excludeIf($conversionType);
		$tableType->excludeIf($conversionType);
		$tableType->requireIf($conversionType, false);
		$tableType->dismissIf($action, "summarize-table");

		array_push($this->parameters,
			new Label("Required Parameters"),
			$inputFp,
			$outputFp,
			new Label("Optional parameters"),
			$action,
			// convert
			$conversionType,
			$matrixType,
			$sampleMetadataFp,
			$headerKey,
			$outputMetadataId,
			$processObsMetadata,
			$tableType,

			new Label("Output options"),
			// summarize-table
			$qualitative,
			$suppressMd5
		);
	}

	public function renderCommand() {
		$args = "";
		$command = $this->getScriptName() . " ";
		foreach ($this->getParameters() as $parameter) {
			if ($parameter->getName() != "action") {
				$args .= $parameter->renderForOperatingSystem() . " ";
			}
			else {
				$command .= $parameter->getValue() . " ";
			}
		}
		return $command . $args;
	}

	public function renderVersionCommand() {
		$action = "";
		foreach ($this->getParameters() as $parameter) {
			if ($parameter->getName() == "action") {
				$action = $parameter->getValue();
				break;
			}
		}
		if (!$action) {
			return "true";
		}
		return $this->getScriptName() . " " . $action . " --version";
	}

	public function getScriptName() {
		return "biom";
	}
	public function getScriptTitle() {
		return "Manipulate OTU table";
	}
	public function getHtmlId() {
		return "manipulate_table";
	}
}
