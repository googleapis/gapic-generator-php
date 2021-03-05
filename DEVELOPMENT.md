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
    
    2.  Install `php-cs-fixer` globally.

    ```
    > curl -L https://cs.symfony.com/download/php-cs-fixer-v2.phar -o php-cs-fixer
    > sudo chmod a+x php-cs-fixer
    > sudo mv php-cs-fixer /usr/local/bin/php-cs-fixer
    ```

    3.  Optional: Enable PHP-CS-Fixer linting in your IDE.

        1.  Vim: Set up [vim-php-cs-fixer](https://github.com/stephpy/vim-php-cs-fixer), and apply [this fix](https://github.com/stephpy/vim-php-cs-fixer/pull/47)

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
    cd integration_tests
    php Main.php
    ```
