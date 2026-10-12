def _php_diff_integration_goldens_impl(ctx):
    # Extract the source files from the generated srcjar from API bazel target,
    # and put them in the temporary folder `codegen_tmp`.
    # Compare the `codegen_tmp` with the goldens folder (e.g tests/Integration/goldens/asset)
    # and save the differences in output file `diff_output.txt`.

    diff_output = ctx.outputs.diff_output
    check_diff_script = ctx.outputs.check_diff_script
    gapic_library = ctx.attr.gapic_library
    srcs = ctx.files.srcs
    api_name = ctx.attr.name

    script = """
    mkdir codegen_tmp
    unzip {input_srcs} -d codegen_tmp
    diff -r codegen_tmp/ tests/Integration/goldens/{api_name}/ > {diff_output}
    exit 0   # Avoid a build failure.
    """.format(
        diff_output = diff_output.path,
        input_srcs = gapic_library[DefaultInfo].files.to_list()[0].path,
        api_name = api_name,
    )
    ctx.actions.run_shell(
        inputs = srcs + [
            gapic_library[DefaultInfo].files.to_list()[0],
        ],
        outputs = [diff_output],
        command = script,
    )

    # Check the generated diff_output file, if it is empty, that means there is no difference
    # between generated source code and goldens files, test should pass. If it is not empty, then
    # test will fail by exiting 1.

    check_diff_script_content = """
    # This will not print diff_output to the console unless `--test_output=all` option
    # is enabled, it only emits the comparison results to the test.log.
    # We could not copy the diff_output.txt to the test.log ($XML_OUTPUT_FILE) because that
    # file is not existing at the moment. It is generated once test is finished.
    cat $PWD/tests/Integration/{api_name}_diff_output.txt
    if [ -s $PWD/tests/Integration/{api_name}_diff_output.txt ]
    then
        exit 1
    fi
    """.format(
        api_name = api_name,
    )

    ctx.actions.write(
        output = check_diff_script,
        content = check_diff_script_content,
    )
    runfiles = ctx.runfiles(files = [ctx.outputs.diff_output])
    return [DefaultInfo(executable = check_diff_script, runfiles = runfiles)]

php_diff_integration_goldens_test = rule(
    attrs = {
        "gapic_library": attr.label(),
        "srcs": attr.label_list(
            allow_files = True,
            mandatory = True,
        ),
    },
    outputs = {
        "diff_output": "%{name}_diff_output.txt",
        "check_diff_script": "%{name}_check_diff_script.sh",
    },
    implementation = _php_diff_integration_goldens_impl,
    test = True,
)

def php_integration_test(name, target, data):
    # php_gapic_library generates only one srcjar.
    php_diff_integration_goldens_test(
        name = name,
        gapic_library = target,
        srcs = data,
    )
