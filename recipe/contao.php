<?php

namespace Deployer;

use Deployer\Exception\RuntimeException;

/**
 * ===============================================================
 * Configuration
 * ===============================================================
 */

// Console bin
set('bin/console', function () {
    return '{{release_path}}/vendor/bin/contao-console';
});

/**
 * ===============================================================
 * Tasks
 * ===============================================================
 */

// Validate local setup
task('contao:validate', function () {
    run('./vendor/bin/contao-console contao:version');
})->desc('Validate local Contao setup')->local();

// Update database
task('contao:update_database', function () {
    // First try native update command (Contao >= 4.9)
    try {
        if (version_compare(run('{{bin/php}} {{bin/console}} contao:version'), '4.9.0', '>=')) {
            run('{{bin/php}} {{bin/console}} contao:migrate --schema-only {{console_options}}');

            writeln('<comment>Please use the new contao:migrate task in your deploy.php!</comment>');

            return;
        }
    } catch (RuntimeException $e) {}

    // Then try command provided by contao-database-commands-bundle
    try {
        run('cd {{release_path}} && {{bin/composer}} show fuzzyma/contao-database-commands-bundle');
    } catch (RuntimeException $e) {
        writeln("\r\033[1A\033[39C … skipped");

        /** @noinspection PhpUndefinedMethodInspection */
        output()->setWasWritten(false);

        return;
    }

    run('{{bin/php}} {{bin/console}} contao:database:update -d {{console_options}}');
})->desc('Update database');


// Run Contao migrations and database update
task('contao:migrate', function () {
    run('{{bin/php}} {{bin/console}} contao:migrate --with-deletes {{console_options}}');
})->desc('Run Contao migrations ');

// Download Contao Manager
task('contao:download_manager', function () {
    run('cd {{release_path}} && curl -LsO https://download.contao.org/contao-manager/stable/contao-manager.phar && mv contao-manager.phar web/contao-manager.phar.php');
})->desc('Download the Contao Manager');

// Lock the Contao Install Tool
task('contao:lock_install_tool', function () {
    run('{{bin/php}} {{bin/console}} contao:install:lock');
})->desc('Lock the Contao Install Tool');
