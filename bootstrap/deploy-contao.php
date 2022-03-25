<?php

namespace Deployer;

$recipes = [
    'common',
    'symfony4',
    'vendor/deployer/recipes/recipe/rsync',
    'vendor/duncrow-gmbh/deployer-recipes/recipe/contao',
    'vendor/duncrow-gmbh/deployer-recipes/recipe/database',
    'vendor/duncrow-gmbh/deployer-recipes/recipe/deploy',
    'vendor/duncrow-gmbh/deployer-recipes/recipe/gulp',
    'vendor/duncrow-gmbh/deployer-recipes/recipe/maintenance',
];

// Require the recipes
foreach ($recipes as $recipe) {
    if (!str_contains($recipe, '/')) {
        require_once sprintf('recipe/%s.php', $recipe);
        continue;
    }

    require_once sprintf('%s/%s.php', getcwd(), $recipe);
}

// Load the hosts
inventory('deploy-hosts.yml');

/**
 * ===============================================================
 * Configuration
 *
 * Define the deployment configuration. Each of the variables
 * can be overridden individually per each host.
 * ===============================================================
 */
// Environment
set('symfony_env', 'prod');

// Enable SSH multiplexing
set('ssh_multiplexing', true);

// Number of releases to keep
set('keep_releases', 1);

// Disable anonymous stats
set('allow_anonymous_stats', false);

// Initial directories
add('initial_dirs', ['assets', 'system', 'var', 'web']);

// Writable dirs
add('writable_dirs', ['var']);

// Console options
set('console_options', function () {
    return '--no-interaction --env={{symfony_env}}';
});

set('shared_dirs', [
    'assets/images',
    'files/uploads',
    'var/logs',
    'web/share',
]);
add('shared_files', [
    'app/config/parameters.yml',
    'config/parameters.yml',
    'system/config/localconfig.php',
    '.env.local'
]);

// Exclude files
add('exclude', [
    '/README.md',

    'composer.json~',
    '/phpunit.*',

    '/app/config/parameters.yml',
    '/app/config/parameters.yml.dist',
    '/config/parameters.yml',
    '/config/parameters.yml.dist',
    '/tests',
    '/var',
    '/vendor',

    '/themes/*/assets',

    '/app/Resources/contao/config/runonce*',
    '/assets',
    '/files/themes/*/src',
    '/files/uploads',
    '/system/themes',
    '/web/bundles',
    '/web/assets',
    '/web/files',
    '/web/share',
    '/web/system',
    '/web/app.php',
    '/web/app_dev.php',
    '/web/index.php',
    '/web/preview.php',
    '/web/robots.txt',
]);

// Rsync
set('rsync_src', __DIR__);
set('rsync', function () {
    return [
        'exclude' => array_unique(get('exclude', [])),
        'exclude-file' => false,
        'include' => [],
        'include-file' => false,
        'filter' => [],
        'filter-file' => false,
        'filter-perdir' => false,
        'flags' => 'rz',
        'options' => [],
        'timeout' => 300,
    ];
});

/**
 * ===============================================================
 * Tasks
 * ===============================================================
 */
// Main task
task('deploy', [
    // Prepare
    'contao:validate',
    'gulp:compile',

    // Deploy
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'rsync',
    'deploy:create_initial_dirs',
    'deploy:shared',
    'deploy:vendors',
    'deploy:entry_points',

    // Release
    'maintenance:enable',
    'contao:lock_install_tool',
    'deploy:symlink',
    'database:backup',
    'contao:migrate',
    'maintenance:disable',

    // Cleanup
    'deploy:unlock',
    'cleanup',
    'success',
])->desc('Deploy your project');
