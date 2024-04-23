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

load("@rules_gapic//:gapic.bzl", "GapicInfo", "proto_custom_library")

def php_proto_library(name, deps, plugin_args = [], **kwargs):
    srcjar_target_name = name
    proto_custom_library(
        name = srcjar_target_name,
        deps = deps,
        output_type = "php",
        output_suffix = ".srcjar",
        plugin_args = plugin_args,
        extra_args = [
            "--include_source_info",
        ],
        **kwargs
    )

def php_grpc_library(name, srcs, deps, plugin_args = [], **kwargs):
    srcjar_target_name = name

    # `deps` is not used now but may be used if php_grpc_library ever tries to "compile" its output
    proto_custom_library(
        name = srcjar_target_name,
        deps = srcs,
        plugin = Label("@com_github_grpc_grpc//src/compiler:grpc_php_plugin"),
        plugin_args = ["class_suffix=GrpcClient"] + plugin_args,
        output_type = "grpc",
        output_suffix = ".srcjar",
        extra_args = [
            "--include_source_info",
        ],
        **kwargs
    )

def php_gapic_srcjar(
        name,
        srcs,
        gapic_yaml = None,
        service_yaml = None,
        grpc_service_config = None,
        transport = None,
        rest_numeric_enums = False,
        generate_snippets = True,
        # Supported values validated and specified in src/Utils/MigrationMode.php.
        migration_mode = "PRE_MIGRATION_SURFACE_ONLY", 
        generator_binary = Label("//rules_php_gapic:php_gapic_generator_binary"),
        **kwargs):
    plugin_file_args = {}
    if gapic_yaml:
        plugin_file_args[gapic_yaml] = "gapic_yaml"
    if service_yaml:
        plugin_file_args[service_yaml] = "service_yaml"
    if grpc_service_config:
        plugin_file_args[grpc_service_config] = "grpc_service_config"

    # Transport.
    if transport == None:
        transport = "grpc+rest"
    if transport != "grpc+rest" and transport != "rest" and transport != "grpc":
        fail("Error: Only 'grpc+rest' or 'rest' transports are supported")

    # Set plugin arguments.
    plugin_args = ["metadata"]  # Generate the gapic_metadata.json file.
    plugin_args.append("transport=%s" % transport)
    plugin_args.append("migration-mode=%s" % migration_mode)
    
    # Generate REGAPIC param for requesting response enums be JSON-encoded as numbers, not strings.
    if rest_numeric_enums:
        plugin_args.append("rest-numeric-enums")

    # Dictates whether snippets should be generated.
    if generate_snippets:
        plugin_args.append("generate-snippets")

    proto_custom_library(
        name = name,
        deps = srcs,
        plugin = generator_binary,
        plugin_args = plugin_args,
        plugin_file_args = plugin_file_args,
        output_type = "gapic",
        output_suffix = ".srcjar",
        **kwargs
    )

def _php_gapic_library_add_gapicinfo_impl(ctx):
    return [
        DefaultInfo(files = depset(direct = [ctx.file.output])),
        GapicInfo(),
    ]

_php_gapic_library_add_gapicinfo = rule(
    implementation = _php_gapic_library_add_gapicinfo_impl,
    attrs = {
        "output": attr.label(allow_single_file = True),
    },
)

def php_gapic_library(
        name,
        srcs,
        deps = [],
        gapic_yaml = None,
        service_yaml = None,
        grpc_service_config = None,
        transport = None,
        rest_numeric_enums = False,
        generate_snippets = True,
        generator_binary = Label("//rules_php_gapic:php_gapic_generator_binary"),
        **kwargs):
    srcjar_name = "%s_srcjar" % name

    php_gapic_srcjar(
        name = srcjar_name,
        srcs = srcs,
        gapic_yaml = gapic_yaml,
        service_yaml = service_yaml,
        grpc_service_config = grpc_service_config,
        transport = transport,
        rest_numeric_enums = rest_numeric_enums,
        generate_snippets = generate_snippets,
        generator_binary = generator_binary,
        **kwargs
    )

    _php_gapic_library_add_gapicinfo(
        name = name,
        output = ":{srcjar_name}".format(srcjar_name = srcjar_name),
    )
