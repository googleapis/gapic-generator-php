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
    bazelisk clean --expunge
fi

echo "Running bazel update scripts"

BAZEL_CMD="bazelisk"

$BAZEL_CMD run //tests/Integration:asset_update && \
$BAZEL_CMD run //tests/Integration:compute_small_update && \
$BAZEL_CMD run //tests/Integration:container_update && \
$BAZEL_CMD run //tests/Integration:dataproc_update && \
$BAZEL_CMD run //tests/Integration:functions_update && \
$BAZEL_CMD run //tests/Integration:kms_update && \
$BAZEL_CMD run //tests/Integration:iam_update && \
$BAZEL_CMD run //tests/Integration:logging_update && \
$BAZEL_CMD run //tests/Integration:redis_update && \
$BAZEL_CMD run //tests/Integration:retail_update && \
$BAZEL_CMD run //tests/Integration:spanner_update && \
$BAZEL_CMD run //tests/Integration:speech_update && \
$BAZEL_CMD run //tests/Integration:securitycenter_update && \
$BAZEL_CMD run //tests/Integration:talent_update && \
$BAZEL_CMD run //tests/Integration:videointelligence_update
