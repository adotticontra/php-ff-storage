<?php
require_once("../lib/ff-storage.php");

class ff_storageBrokenTest extends PHPUnit_Framework_TestCase {

	public function testCreateWrongType() {
		$dummy = new ff_storage("dummy","dummy.txt");
		$this->assertFalse($dummy->type());
	}

	public function testInvalidProperties() {
		$dummy = new ff_storage(ff_storage::OBJECTS,"dummy.txt","-1");
		$this->assertFalse($dummy->type());
	}

	public function testAddStringToObjects() {
		$dummy = new ff_storage(ff_storage::OBJECTS,"dummy.txt","2");
		$this->assertFalse($dummy->add('lemon'));
	}

	public function testAddObjectToStrings() {
		$dummy = new ff_storage(ff_storage::STRINGS,"dummy.txt");
		$o = array("one","two");
		$this->assertFalse($dummy->add($o));
	}

	public function testAddMalformedObject() {
		$dummy = new ff_storage(ff_storage::OBJECTS,"dummy.txt","2");
		$o = array("one");
		$this->assertFalse($dummy->add($o));
	}

	public function testUpdateMalformedOldObject() {
		$dummy = new ff_storage(ff_storage::OBJECTS,"dummy.txt","2");
		$o = array("one");
		$n = array("one","two");
		$this->assertFalse($dummy->update($o,$n));
	}

	public function testUpdateMalformedNewObject() {
		$dummy = new ff_storage(ff_storage::OBJECTS,"dummy.txt","2");
		$o = array("one","two");
		$n = array("one");
		$this->assertFalse($dummy->update($o,$n));
	}

	public function testRemoveMalformedObject() {
		$dummy = new ff_storage(ff_storage::OBJECTS,"dummy.txt","2");
		$o = array("one");
		$this->assertFalse($dummy->remove($o));
	}

	public function testFindMalformedObject() {
		$dummy = new ff_storage(ff_storage::OBJECTS,"dummy.txt","2");
		$o = array("one");
		$this->assertFalse($dummy->find($o));
	}
}
?>
