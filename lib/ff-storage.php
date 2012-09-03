<?php
/*
 * ff-storage
 *
 * Copyright (C) Alessandro Dotti Contra <alessandro@hyboria.org>
 *
 * Released under the terms of the GNU LGPL v.3 or later.
 * See doc/COPYING.txt for details.
 *
 * version: 1.0.0
 */

class ff_storage {

	const STRINGS = 0x01;
	const OBJECTS = 0x02;

	private $type 		= FALSE;		// Type of storage (STRINGS or OBJECTS)
	private $file 		= FALSE;		// The file
	private $error		= FALSE;		// The last error
	private $properties = FALSE;		// The number of properties (objects only)

	public $delimiter	= "|";			// The delimiter used to concatenate object properties

	private function is_supported($item) {
		//Check if $item is supported by storage
		if(($this->type === self::STRINGS) && is_string($item)) {
			return TRUE;
		}
		if(($this->type === self::OBJECTS) && is_array($item)) {
			return TRUE;
		}
		$this->error = "Storage does not support type";
		return FALSE;
	}

	private function append_to_file($item) {
		// Append $item to storage file
		$f = fopen($this->file, "a");
		if(!$f) {
			$this->error = "Can't open storage file: $this->file\n";
			return FALSE;
		}
		if(!flock($f, LOCK_EX | LOCK_NB)) {
			$this->error = "Can't lock storage file: $this->file\n";
			return FALSE;
		}
		if(fwrite($f,$item."\n")) {
			flock($f,LOCK_UN);
			fclose($f);
			return TRUE;
		}
		$this->error = "Can't add item $item to storage\n";
		flock($f,LOCK_UN);
		fclose($f);
		return FALSE;
	}

	public function __construct($type,$file,$properties = 1) {
		// Check that we have supplied a valid storage type. If not,
		// just keep $this->type set to FALSE.
		switch($type) {
			case self::STRINGS: 
				$this->type = self::STRINGS;
				break;
			case self::OBJECTS:
				// Check if the number of properties is valid.
				if(is_int($properties) && $properties > 0) {
					$this->type = self::OBJECTS;
					$this->properties = $properties;
				} else {
					$this->error = "Invalid properties number";
				}
				break;
			default:
				$this->error = "Invalid storage type";
		}
		$this->file = $file;
		return $this;
	}

	public function type() {
		return $this->type;	
	}

	public function error() {
		return $this->error;
	}

	public function properties() {
		return $this->properties;
	}

	public function add($item) {
		// Add a string to a strings storage and add an object to an
		// objects storage.
		if(!$this->is_supported($item)) {
			return FALSE;
		}
		if(is_string($item)) {
			return $this->append_to_file($item);
		}
		if(is_array($item)) {
			if(count($item) != $this->properties) {
				$this->error = "Malformed object";
				return FALSE;
			}
			$item = implode($this->delimiter,$item);
			return $this->append_to_file($item);
		}
	}
}
?>
