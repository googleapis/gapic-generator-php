#!/bin/bash

set -eo pipefail

env
pwd
bazel --version

mkdir -p output
OUTPUT_DIR=$(realpath output)

bazel build //rules_php_gapic:php_gapic_generator_binary

PHP_DIRECTORY=$(ls -d "${HOME}"/.cache/bazel/*/*/external/php_micro)
PHP_VERSION=$(echo '<? echo phpversion(); ?>' | "${PHP_DIRECTORY}/bin/php")

echo "PHP version: $PHP_VERSION, packing..."
PHP_ARCHIVE_DIR="php-$PHP_VERSION"
PHP_TARBALL_FILENAME="php-${PHP_VERSION}_linux_x86_64.tar.gz"
mkdir -p "$PHP_ARCHIVE_DIR"
cp -r "$PHP_DIRECTORY"/bin "$PHP_DIRECTORY"/include "$PHP_DIRECTORY"/lib "$PHP_ARCHIVE_DIR"
tar cfz "${OUTPUT_DIR}/${PHP_TARBALL_FILENAME}" "$PHP_ARCHIVE_DIR"
echo "Done: $PHP_TARBALL_FILENAME"
ls -alh "${OUTPUT_DIR}/${PHP_TARBALL_FILENAME}"
