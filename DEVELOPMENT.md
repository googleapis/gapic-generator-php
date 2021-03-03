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
    ./vendor/bin/phpunit --bootstrap tests/autoload.php tests
    ```

-   Integration tests. These may take 5 minutes or so to run.

    ```
    php Main.php
    ```
