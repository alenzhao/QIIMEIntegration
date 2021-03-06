<?php
/*
 * Copyright (C) 2014 Aaron Sharp
 * Released under GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007
 */

namespace Models\Scripts\Parameters;
use \Models\Scripts\ScriptException;

class HelpParameter extends DefaultParameter {

	public function __construct() {
		$this->name = "--help";
	}
	public function renderForOperatingSystem() {
		return "";
	}
	public function renderForForm($disabled, \Models\Scripts\ScriptI $script) {
		return "<a href=\"manual/{$script->getHtmlId()}.txt\" target=\"_blank\" class=\"button\">See manual page</a>";
	}
}
