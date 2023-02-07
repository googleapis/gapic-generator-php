#!/usr/bin/env php
<?php

require __DIR__ . '/../vendor/autoload.php';

use Google\Generator\Utils\AddFragmentToClass;

if ($argc !== 3) {
    print("Usage: write_fragment_to_class.php path/to/fragment.txt path/to/ClassFile.php\n");
    exit(1);
}

list($_, $methodFragmentFile, $classFile) = $argv;

// the method to insert into another class
$methodContent = file_get_contents($methodFragmentFile);
$classContent = file_get_contents($classFile);

// the class / method to insert into
$addFragmentUtil = new AddFragmentToClass($classContent);

// Insert the fragment before the method
// if no method is provided, the fragment is inserted before the first method
// ("__construct", for instance)
$addFragmentUtil->insert($methodContent);
$contents = $addFragmentUtil->getContents();

// write the new contents to the file
file_put_contents($classFile, $contents);
print("New method content written to $classFile\n");
