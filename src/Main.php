<?php declare(strict_types=1);

namespace Google\Generator;

require __DIR__ . '/../vendor/autoload.php';
error_reporting(E_ALL);

// TODO: Support running as protoc plugin.
// TODO: Provide help/usage if incorrect command-line args provided.
// Read commend-line args.
$opts = getopt('', ['descriptor:', 'package:']);
$descBytes = stream_get_contents(fopen($opts['descriptor'], 'rb'));
$package = $opts['package'];

// Generate PHP code.
// At the moment $files is just the file content.
// TODO: Change this to be file location and content
$files = CodeGenerator::GenerateFromDescriptor($descBytes, $package);
foreach ($files as $file) {
    // TODO: Later this won't just print out the generated file content.
    print($file . "\n");
}
