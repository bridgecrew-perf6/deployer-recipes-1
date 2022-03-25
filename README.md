# duncrow-gmbh deployer recipes

This repository contains recipes to integrate with [deployer](https://github.com/deployphp/deployer).

## Installing

```
composer require deployer/recipes duncrow-gmbh/deployer-recipes --dev
```

## Usage

### Bootstrap file

Copy [`deploy-hosts.yml`](bootstrap/deploy-hosts.yml) to your project root and one of
the [bootstrap files](bootstrap) as your `deploy.php` file:

1. [`deploy-contao.php`](bootstrap/deploy-contao.php) – Contao 4 setup with Gulp for assets management

## Pro Tips

### Disable releases

If you would like to disable the releases (e.g. for a dev system) you can do it simply by including the recipe:

```php
require 'recipe/disable-releases.php';
``` 

### Contao Manager

Although Contao Manager seems to be redundant if the system can be deployed, you may still want to install it
e.g. for [trakked.io](https://www.trakked.io). To do that, simply add the following task to the list:

```diff
task('deploy', [
    // …
    'maintenance:enable',
+   'contao:download_manager'
    // …
])->desc('Deploy your project');
```

### Database Helpers (Restore and release)

This collection provides a tasks to easily restore/release the database `dev <-> live` unidirectionally.

First, include the `database-helpers.php` recipe.

You can use the command `dep database:retrieve example.com` to download a database dump from remote (example.com) and overwrite the local database.

You can use the command `dep database:release example.com` to overwrite the remote (example.com) datbase with the local one.
