# System Requirements

## General requirements

- Composer 1.6.5 or newer
- PHP 7.1.0 or newer
- ExpressionEngine 5.0.0 or greater
- MySQL 5.0.3 or newer

## Installing Or Updating

Executive 3.x and newer is designed to work only with [Composer](https://getcomposer.org/) dependency management. If you know what you're doing (and you probably do if you're using Executive), you can modify these instructions or the sample front controller files to suite your purposes. However, this guide to installing Executive with Composer will be your starting point:

1. If you do not yet have composer set up in your project, copy the `composer.json` file from `install-support` to the top level of your project
    - You should modify some of the obvious sample values in the `composer.json` file
    - If you already have composer set up for your project you can run: `composer require buzzingpixel/executive-ee` (And take a look at the sample composer file to see what other things you might want like the dev dependencies)
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
7. Now run `php ee executive install` to install Executive
    - You could also install it from the EE control panel as any other add-on in EE, but where's the fun in that?

To update Executive to the latest point release of the same major version:

1. From the root of your project, run `composer update buzzingpixel/executive-ee`
    - Of course, you could also just run `composer update` to update any and all of your project's composer dependencies
2. From the root of your project, run `php ee executive runAddonUpdates`

To update Executive to a new major release, say from 3.x to (a mythical) 4.x, update your composer.json requirements for Executive from `^3.0` to `^4.0` then run through the update instructions from above.

## Note

Because ExpressionEngine expects add-on files to be in a particular location, you have to run `php ee executive composerProvision` whenever you deploy to new environments or any time the symlinks to Executive's directories in the `vendor` directory are not where EE expects them to be.

## Also note

The `EEFrontController.php` file looks for an environment variable of `EE_INSTALL_MODE` set to `true` to enable EE install mode. Otherwise EE install mode is disabled. In order to install EE or update it, you must temporarily set that environment variable to `true`.

## Additional Items

### Running processes on the server

To take full advantage of Executive, you need to set up a couple things to run on the server. That's because Executive has a task scheduler and a queue. The scheduler is great for running a command at specified intervals and the queue is great for breaking up long and/or intensive tasks into steps that will be run by the queue runner.

#### Scheduler

The cliff notes is that in order for the scheduler to work, you need to run the scheduler on a server cron every minute. Here's an example cron command to add to your server:

```shell
* * * * * /user/bin/php /path/to/projet/ee executive runSchedule >> /dev/null 2>&1
```

Learn more about the scheduler [here](schedule.md)

#### Queue

Executive has a queue that you can put tasks into. In order to take advantage of it, you need to have your server running the queue. The cliff notes of that is you need to have a process running the `runQueue` command on your server every second and that will restart that process if it dies unexpectedly. For Linux servers (that's basically every web server), there's a program called Supervisor that is great for this.

There's a `queueRunner.sh` shell script in the `install-support` directory that you can have Supervisor run. That shell script uses a `while` loop to create an infinite loop to run the queue again after 1 second once the command completes. And if anything happens to it, Supervisor will start it right up again. Look in the script for more config and Supervisor examples.

Learn more about the queue [here](queue.md)
