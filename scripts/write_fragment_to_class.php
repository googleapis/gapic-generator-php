#!/usr/bin/env php
<?php

require __DIR__ . '/../vendor/autoload.php';

use Google\Generator\Utils\AddFragmentToClass;

if ($argc !== 3) {
    print("Usage: write_fragment_to_class.php path/to/fragment.txt path/to/ClassFile.php\n");
    exit(1);
}

list($_, $methodFragmentFile, $classFile) = $argv;

// The fragment to insert into another class.
$methodContent = file_get_contents($methodFragmentFile);

// The class to insert the fragment into.
$classContent = file_get_contents($classFile);
$addFragmentUtil = new AddFragmentToClass($classContent);

// Insert the fragment into the class.
// If no method is provided, the fragment is inserted before the first method
// ("__construct", for instance), or before the end of the class.
$addFragmentUtil->insert($methodContent);

// Write the new contents to the class file.
file_put_contents($classFile, $addFragmentUtil->getContents());
print("New method content written to $classFile\n");
