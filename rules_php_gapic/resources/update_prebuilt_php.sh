#!/bin/bash

set -eo pipefail

if [[ -z "${GOOGLEAPIS_IMAGE}" ]]
then
  echo "GOOGLEAPIS_IMAGE not set, defaulting to gcr.io/gapic-images/googleapis:prod"
  GOOGLEAPIS_IMAGE="gcr.io/gapic-images/googleapis:prod"
fi

branch="update-binary-`date +%Y%m%dT%H%M%S`"
sourceRoot=$(git rev-parse --show-toplevel)
echo "sourceRoot: ${sourceRoot}"

docker run --rm \
  --volume "${sourceRoot}":/workspace \
  --workdir "/workspace" \
  --entrypoint "/workspace/rules_php_gapic/resources/prebuild.sh" \
  "${GOOGLEAPIS_IMAGE}"

git checkout -b "${branch}"
rm -rf rules_php_gapic/resources/php-*.tar.gz
cp output/php-*.tar.gz  rules_php_gapic/resources
git add rules_php_gapic/resources
git commit -m "fix: update PHP prebuilt binary"
echo "Pushing PHP branch to GitHub..."
git push --set-upstream origin "${branch}"

echo "Please create pull requests:"
echo "  https://github.com/googleapis/gapic-generator-php/pull/new/${branch}"
