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

load("@bazel_tools//tools/build_defs/repo:http.bzl", "http_archive", "http_file")

def _execute_and_check_result(ctx, command, **kwargs):
    res = ctx.execute(command, **kwargs)
    if res.return_code != 0:
        fail("""
Failed to execute command: `{command}`
Exit Code: {code}
STDOUT: {stdout}
STDERR: {stderr}
        """.format(
            command = command,
            code = res.return_code,
            stdout = res.stdout,
            stderr = res.stderr,
        ))
    return res

def _php_impl(ctx):
    root_path = ctx.path(".")

    build_bazel = """
exports_files(glob(include = ["bin/*", "lib/**"], exclude_directories = 0))
     """

    os_name = ctx.os.name

    # First try using the prebuilt version
    for prebuilt_php in ctx.attr.prebuilt_phps:
        if prebuilt_php.name.find(os_name) < 0:
            continue
        tmp = "php_tmp"
        _execute_and_check_result(ctx, ["mkdir", tmp], quiet = False)
        ctx.extract(archive = prebuilt_php, stripPrefix = ctx.attr.strip_prefix, output = tmp)
        res = ctx.execute(["bin/php", "--version"], working_directory = tmp)
        _execute_and_check_result(ctx, ["rm", "-rf", tmp], quiet = False)
        if res.return_code == 0:
            ctx.extract(archive = prebuilt_php, stripPrefix = ctx.attr.strip_prefix)
            ctx.file("BUILD.bazel", build_bazel)
            return

    # If none of the prebuilt versions worked, fallback to building the php
    # interpreter from sources
    srcs_dir = "srcs"
    ctx.download_and_extract(
        url = ctx.attr.urls,
        stripPrefix = ctx.attr.strip_prefix,
        output = srcs_dir,
    )
    _execute_and_check_result(
        ctx,
        ["./configure",
            "--enable-static",
            "--disable-all",
            "--without-pear",
            "--enable-phar",
            "--enable-json",
            "--enable-filter",
            "--enable-tokenizer",
            "--with-libxml",
            "--enable-xml",
            "--enable-dom",
            "--enable-xmlwriter",
            "--enable-mbstring",
            "--disable-mbregex",
            "--with-openssl",
            "--enable-bcmath",
            "--prefix=%s" % root_path.realpath],
        working_directory = srcs_dir,
        quiet = False,
    )
    _execute_and_check_result(ctx, ["make", "-j10"], working_directory = srcs_dir, quiet = False)
    _execute_and_check_result(ctx, ["make", "install"], working_directory = srcs_dir, quiet = False)
    _execute_and_check_result(ctx, ["rm", "-rf", srcs_dir], quiet = False)

    ctx.file("BUILD.bazel", build_bazel)

php = repository_rule(
    implementation = _php_impl,
    attrs = {
        "urls": attr.string_list(),
        "strip_prefix": attr.string(),
        "prebuilt_phps": attr.label_list(allow_files = True, mandatory = False),
    },
)

def _find_ws_root(path):
    for _ in range(10):
        if path.get_child('WORKSPACE').exists or path.get_child('WORKSPACE.bazel').exists:
            return path
        path = path.dirname
    fail("Cannot find workspace root.")

def _php_composer_install_impl(ctx):
    ws_path = _find_ws_root(ctx.path(ctx.attr.composer_json).dirname)
    composer_json_relative = str(ctx.path(ctx.attr.composer_json))[len(str(ws_path)):]
    # Copy entire source workspace into the restored repo, then run `composer install`.
    # The entire workspace is required, as the php entry-point may reference
    # arbitrary other files and directories within the workspace.
    # This copy is used during the execution of the PHP, as it has all files available.
    _execute_and_check_result(ctx, ["cp", "-rHs",  "--preserve=links", str(ws_path), "."])
    _execute_and_check_result(ctx, ["mv", "./" + ws_path.basename, "./install"])
    ctx.execute(["rm", "-rf", "./install/vendor"]) # This will fail if dir doesn't exist, so don't check
    php_path = ctx.path(ctx.attr.php)
    composer_path = ctx.path(ctx.attr.composer_phar)
    _execute_and_check_result(ctx, [php_path, "-n", composer_path, "install"], working_directory="./install/")
    ctx.file("BUILD.bazel", """exports_files(["install"])""")

php_composer_install = repository_rule(
    implementation = _php_composer_install_impl,
    attrs = {
        "php": attr.label(default=Label("@php_micro//:bin/php"), allow_single_file=True, executable=True, cfg="host"),
        "composer_phar": attr.label(default="@gapic_generator_php//:rules_php_gapic/resources/composer.phar"),
        "composer_json": attr.label(allow_single_file=True),
    }
)
