<?php
require_once("../lib/ff-storage.php");

print "***** FF-STORAGE TEST UNIT\n";
print "===> Basic tests\n";

// Create a storage (strings)
$strings = new ff_storage(ff_storage::STRINGS,"strings.txt");
if($strings->type() === ff_storage::STRINGS) {
	print "[OK] ";
} else {
	print "[FAILED] ";
}
print "Create a storage (strings)\n";

// Create a storage (objects)
$objects = new ff_storage(ff_storage::OBJECTS,"objects.txt",4);
if($objects->type() === ff_storage::OBJECTS) {
	print "[OK] ";
} else {
	print "[FAILED] ";
}
print "Create a storage (objects)\n";

// Add a string
if($strings->add("apple")) {
	print "[OK] ";
} else {
	print "[FAILED] ";
}
print "Add a string\n";

// Add an object
$o = array("one","two","three","four");
if($objects->add($o)) {
	print "[OK] ";
} else {
	print "[FAILED] ";
}
print "Add an object\n";

//Update string
$strings->add("pineapple");
$strings->add("lemon");
if($strings->update("/^.*apple$/","orange") == 2) {
	print "[OK] ";
} else {
	print "[FAILED] ";
}
print "Update strings\n";

//Update object
$o = array("uno","due","tre","quattro");
$objects->add($o);
$o = array("nessuno","due","tre","cinque");
$objects->add($o);
$o_old = array("/.+uno/",false,false,"/cinque/");
$o_new = array("uno",false,false,false);
if($objects->update($o_old,$o_new) == 1) {
	print "[OK] ";
} else {
	print "[FAILED] ";
}
print "Update objects\n";

//Remove strings
if($strings->remove("/.*orange$/") == 2) {
	print "[OK] ";
} else {
	print "[FAILED] ";
}
print "Remove strings\n";

//Remove objects
$o = array(FALSE,FALSE,FALSE,"/cinque/");
if($objects->remove($o) == 1) {
	print "[OK] ";
} else {
	print "[FAILED] ";
}
print "Remove objects\n";

print "[TODO] Find strings\n";
print "[TODO] Find objects\n";
print "[TODO] Count elements in storage (strings)\n";
print "[TODO] Count elements in storage (objects)\n";
print "===> Failure tests\n";

// Create a storage (wrong type)
$dummy = new ff_storage("dummy","dummy.txt");
if(!$dummy->type()) {
	print "[OK] ";
} else {
	print "[FAILED] ";
}
print "Create a storage (wrong type)\n";
print "\terror = " . $dummy->error() . "\n";

// Create a storage (objects, invalid properties number).
$dummy = new ff_storage(ff_storage::OBJECTS,"dummy.txt","-1");
if(!$dummy->type()) {
	print "[OK] ";
} else {
	print "[FAILED] ";
}
print "Create a storage (objects, invalid properties number)\n";
print "\terror = " . $dummy->error() . "\n";

// Add item (string -> objects)
if(!$objects->add("lemon")) {
	print "[OK] ";
} else {
	print "[FAILED] ";
}
print "Add item (string -> objects)\n";
print "\terror = " . $objects->error() . "\n";

// Add item (object -> strings)
$o = array("one","two");
if(!$strings->add($o)) {
	print "[OK] ";
} else {
	print "[FAILED] ";
}
print "Add item (object -> strings)\n";
print "\terror = " . $strings->error() . "\n";

// Add object (malformed object)
if(!$objects->add($o)) {
	print "[OK] ";
} else {
	print "[FAILED] ";
}
print "Add object (malformed object)\n";
print "\terror = " . $objects->error() . "\n";

// Update object (malformed old)
$o_old = array("uno","due");
if($objects->update($o_old,$o_new) === false) {
	print "[OK] ";
} else {
	print "[FAILED] ";
}
print "Update object (malformed old)\n";
print "\terror = " . $objects->error() . "\n";

// Update object (malformed new)
$o_old = array("uno","due","tre","quattro");
$o_new = array("uno","due");
if($objects->update($o_old,$o_new) === false) {
	print "[OK] ";
} else {
	print "[FAILED] ";
}
print "Update object (malformed new)\n";
print "\terror = " . $objects->error() . "\n";

// Remove objects (malformed object)
$o = array("uno","due");
if($objects->remove($o) === false) {
	print "[OK] ";
} else {
	print "[FAILED] ";
}
print "Remove objects (malformed object)\n";
print "\terror = " . $objects->error() . "\n";

print "[TODO] Find objects (malformed object)\n";

unlink("strings.txt");
unlink("objects.txt");
?>
