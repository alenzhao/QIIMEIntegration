<?php
/*
 * Copyright (C) 2014 Aaron Sharp
 * Released under GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007
 */

namespace Utils;

class Roster {
	private static $roster = NULL;
	public static function setDefaultRoster(Roster $roster = NULL) {
		Roster::$roster = $roster;
	}
	public static function getRoster() {
		return Roster::$roster;
	}


	private $database;
	private $operatingSystem;
	public function __construct(\Database\DatabaseI $database, \Models\OperatingSystemI $operatingSystem) {
		$this->database = $database;
		$this->operatingSystem = $operatingSystem;
	}
	public function createUser($username) {
		$this->database->startTakingRequests();
		$userId = $this->database->createUser($username);
		if (!$userId) {
			$this->database->forgetAllRequests();
			throw new \Exception("Unable to store new user in database");
		}

		try {
			$this->operatingSystem->createDir("u" . $userId);
			$this->database->executeAllRequests();
			return true;
		}
		catch (\Exception $ex) {
			$this->database->forgetAllRequests();
			throw $ex;
		}
	}
	public function userExists($username) {
		return $this->database->userExists($username);
	}
}
