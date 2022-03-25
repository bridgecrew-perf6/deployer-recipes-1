<?php

namespace Deployer;

/**
 * ===============================================================
 * Tasks
 * ===============================================================
 */

// Enable maintenance mode
task('maintenance:enable', function () {
    run('{{bin/php}} {{bin/console}} contao:maintenance-mode {{console_options}} enable');
})->desc('Enable maintenance mode');

// Disable maintenance mode
task('maintenance:disable', function () {
    run('{{bin/php}} {{bin/console}} contao:maintenance-mode {{console_options}} disable');
})->desc('Disable maintenance mode');
