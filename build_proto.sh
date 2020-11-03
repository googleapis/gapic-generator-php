#!/bin/sh

# Build PHP code from protos; for protos where PHP files are not currently available.

./tools/protoc -I./protobuf/src -I./googleapis -I./grpc-proto --php_out=./src ./grpc-proto/grpc/service_config/service_config.proto
