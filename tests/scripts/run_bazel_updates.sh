#!/bin/bash

# Usage: ./run_bazel_updates.sh [--expunge]

# This runs the bazel update commands, ex:
# bazel run //tests/Integration:asset_update && \
# bazel run //tests/Integration:compute_small_update
# etc
#
# `bazel clean --expunge` is ran before the commands if the --expunge
# argument is passed

if [ "$1" == "--expunge" ];
then
    echo "Running bazel clean --expunge"
    bazel clean --expunge
fi

echo "Running bazel update scripts"

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
