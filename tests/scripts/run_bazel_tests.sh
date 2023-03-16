#!/bin/bash

# Usage: ./run_bazel_tests.sh

# This runs the bazel test commands, ex:
# bazel test //tests/Integration:asset && \
# bazel test //tests/Integration:compute_small
# etc

echo "Running bazel tests"

bazel test //tests/Integration:asset && \
bazel test //tests/Integration:compute_small && \
bazel test //tests/Integration:container && \
bazel test //tests/Integration:dataproc && \
bazel test //tests/Integration:functions && \
bazel test //tests/Integration:kms && \
bazel test //tests/Integration:iam && \
bazel test //tests/Integration:logging && \
bazel test //tests/Integration:redis && \
bazel test //tests/Integration:retail && \
bazel test //tests/Integration:speech && \
bazel test //tests/Integration:securitycenter && \
bazel test //tests/Integration:talent && \
bazel test //tests/Integration:videointelligence
