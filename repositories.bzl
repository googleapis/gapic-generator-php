# Copyright 2021 Google LLC
#
# Licensed under the Apache License, Version 2.0 (the "License");
# you may not use this file except in compliance with the License.
# You may obtain a copy of the License at
#
#      https://www.apache.org/licenses/LICENSE-2.0
#
# Unless required by applicable law or agreed to in writing, software
# distributed under the License is distributed on an "AS IS" BASIS,
# WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
# See the License for the specific language governing permissions and
# limitations under the License.

load("@bazel_tools//tools/build_defs/repo:http.bzl", "http_archive")
load("@bazel_tools//tools/build_defs/repo:utils.bzl", "maybe")
load("//rules_php_gapic:php_repo.bzl", "php", "php_composer_install")

def gapic_generator_php_repositories():
    _rules_gapic_version = "0.5.4"
    maybe(
        http_archive,
        name = "rules_gapic",
        strip_prefix = "rules_gapic-%s" % _rules_gapic_version,
        urls = ["https://github.com/googleapis/rules_gapic/archive/v%s.tar.gz" % _rules_gapic_version],
    )

    _php_version = "8.1.13"
    maybe(
        php,
        name = "php_micro",
        #prebuilt_phps = [
        #    "@gapic_generator_php//:rules_php_gapic/resources/php-%s_linux_x86_64.tar.gz" % _php_version,
        #],
        urls = ["https://www.php.net/distributions/php-%s.tar.gz" % _php_version ],
        strip_prefix = "php-%s" % _php_version,
    )
    maybe(
        php_composer_install,
        name = "php_gapic_generator_composer_install",
        composer_json = "@gapic_generator_php//:composer.json",
    )

    # Import Bazel-only dependencies.
    _protobuf_version = "3.13.0"
    maybe(
        http_archive,
        name = "com_google_protobuf",
        urls = ["https://github.com/protocolbuffers/protobuf/archive/v%s.zip" % _protobuf_version],
        strip_prefix = "protobuf-%s" % _protobuf_version,
    )

    maybe(
        http_archive,
        name = "com_google_googleapis",
        strip_prefix = "googleapis-ebe079453350f4cd5afea5091522cf97e25f01a1",
        urls = [
            "https://github.com/googleapis/googleapis/archive/ebe079453350f4cd5afea5091522cf97e25f01a1.zip",
        ],
    )
