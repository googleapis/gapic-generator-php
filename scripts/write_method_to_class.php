#!/usr/bin/env php
<?php

require __DIR__ . '/../vendor/autoload.php';

use Google\Generator\Utils\CodeReplaceMethod;

if ($argc !== 3) {
    print("Usage: write_method_to_class.php path/to/method_fragment.txt path/to/ClassFile.php\n");
    exit(1);
}

list($_, $methodFragmentFile, $classFile) = $argv;

// the method to insert into another class
$methodContent = file_get_contents($methodFragmentFile);
$classContent = file_get_contents($classFile);

// the class / method to insert into
// if no method is defined, the first method is used ("__construct", for instance)
$insertBeforeMethod = new CodeReplaceMethod($classContent);

// Insert the fragment before the method
$insertBeforeMethod->insertBefore($methodContent);
$contents = $insertBeforeMethod->getContents();

// verify PHP syntax before writing the file
passthru(sprintf('echo %s | php -l 2>&1', escapeshellarg($contents)), $returnVar);

if (0 !== $returnVar) {
    exit($returnVar);
}

// write the new contents to the file
file_put_contents($classFile, $contents);
print("New method content written to $classFile\n");
