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

	private function is_well_formed($item) {
		//Check if an object is well formed
		if(count($item) != $this->properties) {
			return FALSE;
		}
		return TRUE;
	}

	private function load_from_file() {
		//Load items from storage file
		$f = fopen($this->file, "r");
		if(!$f) {
			$this->error = "Can't open storage file: $this->file\n";
			return FALSE;
		}
		$items = array();
		if(!flock($f, LOCK_EX | LOCK_NB)) {
			$this->error = "Can't lock storage file: $this->file\n";
			return FALSE;
		}
		while (($line = fgets($f, 4096)) !== FALSE) {
			if($this->type == self::STRINGS) {
				$items[] = rtrim($line);
			}
			if($this->type == self::OBJECTS) {
				$items[] = explode($this->delimiter,rtrim($line));
			}
		}
		flock($f,LOCK_UN);
		fclose($f);
		return $items;
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

	private function match_string($a,$b) {
		//Check if two strings match
		return preg_match($a,$b);
	}
	
	private function match_object($a,$b) {
		//Check if two objects match
		$matches = 0;
		foreach($a as $item) { if($item) { $matches++; } }
		$matched = 0;
		for($i = 0; $i < count($a); $i++) {
			if($a[$i]) { if(preg_match($a[$i],$b[$i])) { $matched++; } }
		}
		return ($matched == $matches);
	}

	private function dump_to_file($action,$items,$old,$new=FALSE) {
		//Dump items to storage file, perfoming the request action.
		$f = fopen($this->file, "w");
		if(!$f) {
			$this->error = "Can't open storage file: $this->file\n";
			return FALSE;
		}
		if(!flock($f, LOCK_EX | LOCK_NB)) {
			$this->error = "Can't lock storage file: $this->file\n";
			return FALSE;
		}
		$count = 0;
		$matches = 0; //Needed for objects
		foreach($items as $item) {
			if($this->type === self::STRINGS) {
				if($this->match_string($old,$item)) {
					$count++;
					switch($action) {
						case "update":
							$item = $new;
							break;
						case "delete":
							continue 2; //switch() is considered a loop
					}
				}
			}
			if($this->type == self::OBJECTS) {
				if($this->match_object($old,$item)) { 
					$count++;
					switch($action) {
					case "update":
						//Build the new object
						$new_item = array();
						for($i = 0; $i < count($old); $i++) {
							$new_item[$i] = $new[$i] ? $new[$i] : $item[$i];
						}
						$item = implode($this->delimiter,$new_item);
						break;
					case "delete":
						continue 2; //switch() is considered a loop
					}
				} else {
					$item = implode($this->delimiter,$item);
				}
			}
			if(!fwrite($f,$item."\n")) {
				$this->error = "Can't write item to file\n";
				return FALSE;
			}
		}
		flock($f,LOCK_UN);
		fclose($f);
		return $count;
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
		if($this->type == self::STRINGS) {
			return $this->append_to_file($item);
		}
		if($this->type == self::OBJECTS) {
			if(!$this->is_well_formed($item)) {
				$this->error = "Malformed object";
				return FALSE;
			}
			$item = implode($this->delimiter,$item);
			return $this->append_to_file($item);
		}
	}

	public function update($old,$new) {
		// Update a string or an object.
		// Returns the number of updated items or FALSE in case of error.
		if(!$this->is_supported($old) or !$this->is_supported($new)) {
			return FALSE;
		}
		if($this->type == self::OBJECTS) {
			//Check if objects are well formed
			if((!$this->is_well_formed($old)) or (!$this->is_well_formed($new))) {
				$this->error = "Malformed object";
				return FALSE;
			}
		}
		// load from file
		$items = $this->load_from_file();
		if($items === FALSE) { return FALSE; }
		// dump to file (update)
		return($this->dump_to_file("update",$items,$old,$new));
	}

	public function remove($item) {
		// Remove a string or an object.
		// Returns the number of removed items or FALSE in case of error.
		if(!$this->is_supported($item)) {
			return FALSE;
		}
		if($this->type == self::OBJECTS) {
			//Check if objects are well formed
			if(!$this->is_well_formed($item)) {
				$this->error = "Malformed object";
				return FALSE;
			}
		}
		// load from file
		$items = $this->load_from_file();
		if($items === FALSE) { return FALSE; }
		// dump to file (delete)
		return($this->dump_to_file("delete",$items,$item));
	}

	public function find($item) {
		// Find an item in the storage.
		// Returns an array with the strings or the objects found of FALSE in case of error.
		if(!$this->is_supported($item)) {
			return FALSE;
		}
		//Check if objects are well formed
		if(!$this->is_well_formed($item)) {
			$this->error = "Malformed object";
			return FALSE;
		}
		// load from file
		$items = $this->load_from_file();
		if($items === FALSE) { return FALSE; }
		// search for $item
		$found = array();
		foreach($items as $i) {
			if($this->type == self::STRINGS) {
				if($this->match_string($item,$i)) {
					$found[] = $i;
				}
			}
			if($this->type == self::OBJECTS) {
				if($this->match_object($item,$i)) {
					$found[] = $i;
				}
			}
		}
		return $found;
	}

	public function count() {
		// count elements in storage
		$items = $this->load_from_file();
		$count = ($items === FALSE ? FALSE : count($items));
		return $count;
	}
}
?>
