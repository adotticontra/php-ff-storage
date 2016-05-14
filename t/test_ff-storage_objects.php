<?php
require_once("../lib/ff-storage.php");

class ff_storageObjectsTest extends PHPUnit_Framework_TestCase {

	protected function setUp() {
		touch("objects.txt");
		$this->objects = new ff_storage(ff_storage::OBJECTS,"objects.txt",4);
	}

	protected function tearDown() {
		unlink('objects.txt');
	}

	public function testAddObject() {
		$o = array('one','two','three','four');
		$this->assertTrue($this->objects->add($o));	
	}

	/**
	 * @depends testAddObject
	 */
	public function testUpdateObject() {
		$o = array('uno','due','tre','quattro');
		$this->objects->add($o);
		$o_old = array('/^uno$/',false,false,false);
		$o_new = array('zero',false,'zero',false);
		$this->assertEquals($this->objects->update($o_old,$o_new),1);	
	}

	/**
	 * @depends testAddObject
	 */
	public function testRemoveObject() {
		$o = array('uno','due','tre','quattro');
		$this->objects->add($o);
		$o = array(FALSE,FALSE,FALSE,'/quattro/');
		$this->assertEquals($this->objects->remove($o),1);	
	}

	/**
	 * @depends testAddObject
	 */
	public function testFindObjects() {
		$o = array('uno','due','tre','quattro');
		$this->objects->add($o);
		$this->objects->add($o);
		$o = array(FALSE,FALSE,FALSE,'/quattro/');
		$this->assertEquals(count($this->objects->find($o)),2);	
	}

	/**
	 * @depends testAddObject
	 */
	public function testCountObjects() {
		$o = array('uno','due','tre','quattro');
		$this->objects->add($o);
		$this->assertEquals($this->objects->count(),1);	
	}

	public function testTemplate() {
		$o = $this->objects->template();
		$this->assertEquals(count($o),$this->objects->properties());	
	}
}
?>
