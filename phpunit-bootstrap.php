<?php

/*
 * Load the necessary PHPCS files.
 */
// Get the PHPCS dir from an environment variable.
$phpcsDir = \getenv('PHPCS_DIR');
$composerPHPCSPath = __DIR__ . '/vendor/squizlabs/php_codesniffer';

if ($phpcsDir === false && \is_dir($composerPHPCSPath)) {
    // PHPCS installed via Composer.
    $phpcsDir = $composerPHPCSPath;
}
elseif ($phpcsDir !== false) {
    /*
     * PHPCS in a custom directory.
     * For this to work, the `PHPCS_DIR` needs to be set in a custom `phpunit.xml` file.
     */
    $phpcsDir = \realpath($phpcsDir);
}

// Try and load the PHPCS autoloader.
if ($phpcsDir !== false
    && \file_exists($phpcsDir . '/autoload.php')
    && \file_exists($phpcsDir . '/tests/bootstrap.php')
) {
    require_once $phpcsDir . '/autoload.php';
    require_once $phpcsDir . '/tests/bootstrap.php'; // PHPUnit 6.x+ support.
} else {
    echo 'Uh oh... can\'t find PHPCS.

If you use Composer, please run `composer install`.
Otherwise, make sure you set a `PHPCS_DIR` environment variable in your phpunit.xml file
pointing to the PHPCS directory and that PHPCSUtils is included in the `installed_paths`
for that PHPCS install.
';

    exit(1);
}

// Alias the PHPCS 3.x test case to the PHPCS 4.x name.
if (class_exists('PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest') === true
    && class_exists('PHP_CodeSniffer\Tests\Standards\AbstractSniffTestCase') === false
) {
    class_alias(
        'PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest',
        'PHP_CodeSniffer\Tests\Standards\AbstractSniffTestCase'
    );
}
