#!/bin/sh

# Build PHP code from protos; for protos where PHP files are not currently available.

p="protoc"
if [[ ! -z ${USE_TOOLS_PROTOC+x} ]]; then
    p="./tools/protoc"
fi

$p -I./protobuf/src -I./googleapis -I./grpc-proto --php_out=./src ./grpc-proto/grpc/service_config/service_config.proto
$p --php_out=./src ./plugin.proto
