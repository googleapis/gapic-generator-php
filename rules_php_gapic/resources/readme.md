## How to update prebuilt PHP interpreter

There are no specific requirements on how to build the PHP interpreter, a typical build should work.

Note, the prebuilt PHP distributions are optional, if no appropriate prebuilt distribution is specified during the build, PHP will be built on the fly. The prebuilt distributions should be treated as a way to optimize the overall `bazel build` speed.

The recommended (tested) way of doing it is as follows:

1. Do the build on the oldest OS distribution you want to support (you can add prebuilt binaries for any number of operating systems and their distributions, the first one in the list which is able to complete `bin/php --version` command without errors will be picked during the `bazel build`). For example `Ubuntu 16.04 LTS`.
2. Make sure that `libxml2-dev` and `build-essential` packages are installed on the machine:
    ```
    sudo apt-get install \
        libxml2-dev \
        build-essential
   ```
3. Download the specific PHP distribution sources from https://www.php.net/distributions:
    ```
    curl https://www.php.net/distributions/php-8.1.8.tar.gz -o php-8.1.8.tar.gz
    ```
4. Unpack the downloaded archive:
    ```
    tar -xzpf php-8.1.8.tar.gz
    ```
5. Go to the unpacked directory:
    ```
    cd php-8.1.8
    ```
6. Run the build config:
    ```
    ./configure \
        --enable-static \
        --disable-all \
        --without-pear \
        --enable-phar \
        --enable-json \
        --enable-filter \
        --enable-tokenizer \
        --with-curl \
        --with-libxml \
        --enable-xml \
        --enable-dom \
        --enable-xmlwriter \
        --enable-mbstring \
        --disable-mbregex \
        --with-openssl \
        --enable-bcmath \
        --prefix=/tmp/php-8.1.8
    ```
    Please make sure that the `--prefix` destination folder has the same name as the root folder of your unpacked archive (i.e. the one you `cd` in step 3; it is usually `php-<version>`, e.g. `php-8.1.8`).
7. Run the build:
    ```
    make -j10
    ```
    The `-j` argument determines how many files can be compiled in parallel to speed up the build process, please use the value appropriate for your hardware (i.e. it should approximately be equal to the number of CPUs you have).
8. Install the built binaries (installation simply means that the built binaries will be copied do the directory specified as `--prefix=` during the configuration step):
    ```
    make install
    ```
9. Go to the parent directory of the directory specified as `--prefix` on configuration step:
    ```
    cd /tmp
    ```
10. Pack the relevant binaries in the archive, using `php-<version>_platform.tar.gz` name format:
    ```
    tar -zchpf php-8.1.8_linux_x86_64.tar.gz php-8.1.8/bin php-8.1.8/lib
    ```
11. Copy the created archive to the `rules_gapic/php/resources` folder in this repository and post a PR.

## Updating composer

When generating client libraries, this generator is bundled together with a version of composer. Occasionally composer needs to be updated in order for bazel to build successfully.

To update, please run `rules_php_gapic/resources/composer.phar self-update --stable` (or you can install a specific version from https://getcomposer.org/download/) in the generator. This will generate a new binary for the newest version of composer this generator is bundled with. Once you merge this change, the generator should use the latest version of composer.