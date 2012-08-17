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
}
?>
