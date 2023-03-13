#!/bin/bash

# Usage: ./run_bazel_tests.sh

# This runs the bazel test commands, ex:
# bazel test //tests/Integration:asset && \
# bazel test //tests/Integration:compute_small
# etc

echo "Running bazel tests"

bazel run //tests/Integration:asset_update && \
bazel run //tests/Integration:compute_small_update && \
bazel run //tests/Integration:container_update && \
bazel run //tests/Integration:dataproc_update && \
bazel run //tests/Integration:functions_update && \
bazel run //tests/Integration:kms_update && \
bazel run //tests/Integration:iam_update && \
bazel run //tests/Integration:logging_update && \
bazel run //tests/Integration:redis_update && \
bazel run //tests/Integration:retail_update && \
bazel run //tests/Integration:speech_update && \
bazel run //tests/Integration:securitycenter_update && \
bazel run //tests/Integration:talent_update && \
bazel run //tests/Integration:videointelligence_update
