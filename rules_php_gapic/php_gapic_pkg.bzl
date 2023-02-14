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

load("@rules_gapic//:gapic.bzl", "CustomProtoInfo")

def _construct_package_dir_paths(attr_package_dir, out_pkg, label_name):
    if attr_package_dir:
        package_dir = attr_package_dir
        package_dir_expr = "../{}/".format(package_dir)
        tar_cd_suffix = ".."
        tar_prefix = attr_package_dir
    else:
        package_dir = label_name
        package_dir_expr = "./"
        tar_cd_suffix = "."
        tar_prefix = "."

    # We need to include label in the path to eliminate possible output files duplicates
    # (labels are guaranteed to be unique by bazel itself)
    package_dir_path = "%s/%s/%s" % (out_pkg.dirname, label_name, package_dir)
    return struct(
        package_dir = package_dir,
        package_dir_expr = package_dir_expr,
        package_dir_path = package_dir_path,
        package_dir_sibling_parent = out_pkg,
        package_dir_sibling_basename = label_name,
        tar_cd_suffix = tar_cd_suffix,
        tar_prefix = tar_prefix,
    )

def _php_gapic_src_pkg_impl(ctx):
    proto_grpc_srcs = []
    gapic_srcs = []

    for dep in ctx.attr.deps:
        if ProtoInfo in dep or CustomProtoInfo in dep:
            proto_grpc_srcs.extend(dep.files.to_list())
        else:
            gapic_srcs.extend(dep.files.to_list())

    paths = _construct_package_dir_paths(ctx.attr.package_dir, ctx.outputs.pkg, ctx.label.name)

    script = """
    set -e
    for proto_grpc_src in {proto_grpc_srcs}; do
        mkdir -p {package_dir_path}/proto/src
        unzip -q -o $proto_grpc_src -d {package_dir_path}/proto/src
    done
    for gapic_src in {gapic_srcs}; do
        mkdir -p {package_dir_path}/proto
        unzip -q -o $gapic_src -d {package_dir_path}
    done
    {post_processor} --input {package_dir_path}
    cd {package_dir_path}/{tar_cd_suffix}
    tar -zchpf {tar_prefix}/{package_dir}.tar.gz {tar_prefix}/*
    cd -
    mv {package_dir_path}/{package_dir}.tar.gz {pkg}
    rm -rf {package_dir_path}
    """.format(
        proto_grpc_srcs = " ".join(["'%s'" % f.path for f in proto_grpc_srcs]),
        gapic_srcs = " ".join(["'%s'" % f.path for f in gapic_srcs]),
        package_dir_path = paths.package_dir_path,
        package_dir = paths.package_dir,
        pkg = ctx.outputs.pkg.path,
        tar_cd_suffix = paths.tar_cd_suffix,
        tar_prefix = paths.tar_prefix,
        post_processor = ctx.executable._post_processor.path,
    )

    ctx.actions.run_shell(
        inputs = proto_grpc_srcs + gapic_srcs,
        command = script,
        outputs = [ctx.outputs.pkg],
        tools = [ctx.executable._post_processor],
    )

_php_gapic_src_pkg = rule(
    implementation = _php_gapic_src_pkg_impl,
    attrs = {
        "deps": attr.label_list(allow_files = True, mandatory = True),
        "package_dir": attr.string(mandatory = True),
        "_post_processor": attr.label(
            executable = True,
            mandatory = False,
            default = Label("//rules_php_gapic:php_post_processor_binary"),
            cfg = "exec",
        )
    },
    outputs = {"pkg": "%{name}.tar.gz"},
)

def php_gapic_assembly_pkg(name, deps, assembly_name = None, **kwargs):
    package_dir = name
    if assembly_name:
        package_dir = "google-cloud-%s-%s" % (assembly_name, name)
    _php_gapic_src_pkg(
        name = name,
        deps = deps,
        package_dir = package_dir,
        **kwargs
    )
