# System Requirements

## General requirements

- Composer 1.6.5 or newer
- PHP 7.1.0 or newer
- ExpressionEngine 4.0.0 or greater
- MySQL 5.0.3 or newer

## Installing Or Updating

Executive 3.x and newer is designed to work only with Composer dependency management. If you know what you're doing (and you probably do if you're using Executive), you can modify these instructions or the sample front controller files to suite your purposes. However, this is a guide to installing Executive with Composer will be your starting point:

1. If you do not yet have composer set up in your project, copy the `composer.json` file from `install-support` to the top level of your project
    - You should modify some of the obvious sample values in the `composer.json` file
    - If you already have composer set up for your project you can run: `composer require buzzingpixel/executive-ee`
2. Copy `install-support/admin.php` and `install-support/index.php` to your project's public directory, overwriting the existing files
    - If you have any custom stuff going on in these files, you can either copy it over to Executive's files or figure out a better way to handle it
3. Copy `install-support/EEFrontController.php` to the root of your project (which is hopefully one level above your public directory).
4. Copy `install-support/ee` to the root of your project, which will be in the same directory level as `EEFrontController.php`
5. Copy `install-support/.env` to the root of your project, which will be the same directory level as `ee`
    - You are strongly encouraged to add the `.env` file to your `.gitignore` as it is an environment file and can and should be different per environment
    - You are also strongly encouraged to store all your environment config in this file and use `getenv('MY_ENV_KEY')` in your ExpressionEngine config file (and possibly elsewhere) to get those values
    - One trick is to commit a `.env.example` file with the correct values for a local dev environment so developers can duplicate this file to get started quickly
6. CD to your project's root directory and run `php ee executive composerProvision`
    - This will symlink Executive's files into the locations EE expects them to be
7. Now run `php ee install` to install Executive
    - You could also install it from the EE control panel as any other add-on in EE, but where's the fun in that?

To update Executive to the latest point release of the same major version:

1. From the root of your project, run `composer update buzzingpixel/executive-ee`
    - Of course, you could also just run `composer update` to update any and all of your project's composer dependencies
2. From the root of your project, run `php ee executive runAddonUpdates`

To update Executive to a new major release, say from 2.x to 3.x, update your composer.json requirements for Executive from `^3.0` to `^4.0` then run through the update instructions from above.

## Note

Because ExpressionEngine expects add-on files to be in a particular location, you have to run `php ee executive composerProvision` whenever you deploy to new environments or any time the symlinks to Executive's directories in the `vendor` directory are not where EE expects them to be.
