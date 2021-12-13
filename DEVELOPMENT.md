# PHP GAPIC Generator Development

## Setting Up

1.  Clone the directory.

    ```
    git clone git@github.com:googleapis/gapic-generator-php.git
    cd gapic-generator-php
    ```

2.  [Download Composer](https://getcomposer.org/download/)

3.  Install dependencies.

    1.  On Linux:

    ```
    sudo apt-get install php-curl php7.4-mbstring libxml2-dev
    ```

4.  Run `php composer.phar install`

    1.  Alternatively, install composer globally.

    ```
    > php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    > php -r "if (hash_file('sha384', 'composer-setup.php') === \
         '756890a4488ce9024fc62c56153228907f1545c228516cbf63f885e036d37e9a59d27d63f46af1d4d07ee0f76181c7d3') { \
         echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); \
         } echo PHP_EOL;"
    Installer verified
    > sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
    ```

    1.  Install `php-cs-fixer` globally.

    ```
    > curl -L https://cs.symfony.com/download/php-cs-fixer-v2.phar -o php-cs-fixer
    > sudo chmod a+x php-cs-fixer
    > sudo mv php-cs-fixer /usr/local/bin/php-cs-fixer
    ```

    1.  Optional: Enable PHP-CS-Fixer linting in your IDE.

        1.  Vim: Set up
            [vim-php-cs-fixer](https://github.com/stephpy/vim-php-cs-fixer), and
            apply
            [this fix](https://github.com/stephpy/vim-php-cs-fixer/pull/47)

5.  Initialize the submodules

    ```
    git submodule update --init --recursive
    ```

6.  Set up pre-commit checks.

    ```
    cp .githooks/pre-commit .git/hooks/pre-commit
    ```

## Running tests

-   Unit tests

    ```
    ./vendor/bin/phpunit --bootstrap tests/Unit/autoload.php tests/Unit
    ```

    If you do not have `protoc` installed, run with `USE_TOOLS_PROTOC=true`.

    ```
    USE_TOOLS_PROTOC=true ./vendor/bin/phpunit --bootstrap tests/unit/autoload.php tests/Unit
    ```

    This uses the Linux-only `protoc` binary checked into the repository.

    If you run into an error: `Error: Call to undefined function Google\Protobuf\Internal\bccomp()`, that is because the [BC Math](https://www.php.net/manual/en/book.bc.php) extension is not always included by default (see tracking bug here: https://github.com/protocolbuffers/protobuf/issues/4465). You can get around this by installing BC Math with the command `sudo apt install php-bcmath`.

    Updating unit test goldens:

    ```
    php tests/Unit/ProtoTests/GoldenUpdateMain.php
    ```

    Then follow the prompts for which tests to update.

    If a new unit test case is added, make sure to add it to the `UNIT_TESTS` list in [GoldenUpdateMain.php](tests/Unit/ProtoTests/GoldenUpdateMain.php).

-   Monolith integration tests. These may take 5 minutes or so to run.

    ```
    cd tests/Integration
    php Main.php
    ```

-   Bazel integration tests.

    -   Running:

    ```
    bazel test tests/Integration:asset
    ```

    -   Updating goldens:

    ```
    bazel run tests/Integration:asset_update
    ```

## Updating the `googleapis` submodule

To update the `googleapis` submodule, change into the directory and pull:

```
pushd googleapis
git pull origin main
popd
```

Then update the commit hash of `googleapis` in [repositories.bzl](./repositories.bzl)
to match.

Finally, the test golden files need to be udpated with the latest
version of the protos from `googleapis`. See the instructions in [Running tests](#running-tests)
for the exact commands.

Submit all of this in standalone pull request.

## Adding new Protobuf annotations/types

Ensure that the `googleapis` submodule is pinned to a commit recent enough to
include the annotation(s)/type(s) targeted for generation. If the `googleapis`
submodule needs to be updated, see [Updating the `googleapis` submodule](#updating-the-googleapis-submodule).

From the `googleapis` submodule, generate the PHP Protobuf bindings with
`protoc`:

    protoc -I. --php_out=../generated relative/path/to/my.proto

Note: If the newly generated file belongs to a new package, ensure that it is
in scope of the existing autoload rules in the [composer.json](./composer.json),
adding it if it's not.

## How to update composer for use in bazel

See steps under [Updating composer](rules_php_gapic/resources/readme.md).