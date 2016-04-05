<?php
require_once("../lib/ff-storage.php");

class ff_storageStringsTest extends PHPUnit_Framework_TestCase {

	protected function setUp() {
		$this->strings = new ff_storage(ff_storage::STRINGS,"strings.txt");
	}

	protected function tearDown() {
		unlink("strings.txt");
	}

	public function testAddString() {
		$this->assertTrue($this->strings->add('apple'));	
	}

	/**
	 * @depends testAddString
	 */
	public function testUpdateStrings() {
		$this->strings->add("apple");
		$this->strings->add("pineapple");
		$this->strings->add("lemon");
		$this->assertEquals($this->strings->update('/^.*apple$/','orange'),2);
	}

	/**
	 * @depends testAddString
	 */
	public function testRemoveStrings() {
		$this->strings->add("banana");
		$this->strings->add("banana");
		$this->assertEquals($this->strings->remove('/^banana$/'),2);
	}

	/**
	 * @depends testAddString
	 */
	public function testFindStrings() {
		$this->strings->add("banana");
		$this->assertEquals(count($this->strings->find('/^banana$/')),1);
	}

	/**
	 * @depends testAddString
	 */
	public function testCountStrings() {
		$this->strings->add("banana");
		$this->strings->add("banana");
		$this->strings->add("banana");
		$this->assertEquals($this->strings->count(),3);
	}

}
