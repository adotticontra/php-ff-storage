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
$objects = new ff_storage(ff_storage::OBJECTS,"objects.txt");
if($objects->type() === ff_storage::OBJECTS) {
	print "[OK] ";
} else {
	print "[FAILED] ";
}
print "Create a storage (objects)\n";

print "[TODO] Add a string\n";
print "[TODO] Add an object\n";
print "[TODO] Update strings\n";
print "[TODO] Update objects\n";
print "[TODO] Remove strings\n";
print "[TODO] Remove objects\n";
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
print "error = " . $dummy->error() . "\n";

// Create a storage (objects, invalid properties number).
$dummy = new ff_storage(ff_storage::OBJECTS,"dummy.txt","-1");
if(!$dummy->type()) {
	print "[OK] ";
} else {
	print "[FAILED] ";
}

print "Create a storage (objects, invalid properties number)\n";
print "error = " . $dummy->error() . "\n";

print "[TODO] Add item (string -> objects)\n";
print "[TODO] Add item (object -> strings)\n";
print "[TODO] Add object (malformed object)\n";
print "[TODO] Update objects (malformed old)\n";
print "[TODO] Update objects (malformed new)\n";
print "[TODO] Remove objects (malformed object)\n";
print "[TODO] Find objects (malformed object)\n";
?>
