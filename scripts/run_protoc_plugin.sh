#!/bin/bash
# This runs the protoc plugin in a similar way to how it is run using the bazel rules
php -d display_errors=stderr -d error_reporting=0 src/Main.php
