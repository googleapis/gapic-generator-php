#!/bin/bash

# Usage: ./run_bazel_tests.sh

# This runs the bazel test commands, ex:
# bazel test //tests/Integration:asset && \
# bazel test //tests/Integration:compute_small
# etc

echo "Running bazel tests"

BAZELISK_CMD="bazelisk"

$BAZELISK_CMD test //tests/Integration:asset && \
$BAZELISK_CMD test //tests/Integration:compute_small && \
$BAZELISK_CMD test //tests/Integration:container && \
$BAZELISK_CMD test //tests/Integration:dataproc && \
$BAZELISK_CMD test //tests/Integration:functions && \
$BAZELISK_CMD test //tests/Integration:kms && \
$BAZELISK_CMD test //tests/Integration:iam && \
$BAZELISK_CMD test //tests/Integration:logging && \
$BAZELISK_CMD test //tests/Integration:redis && \
$BAZELISK_CMD test //tests/Integration:retail && \
$BAZELISK_CMD test //tests/Integration:speech && \
$BAZELISK_CMD test //tests/Integration:securitycenter && \
$BAZELISK_CMD test //tests/Integration:talent && \
$BAZELISK_CMD test //tests/Integration:videointelligence
