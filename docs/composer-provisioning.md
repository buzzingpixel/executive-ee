# Composer provisioning

One of the really cool features new in Executive 3.x is Composer provisioning. Executive 3.x fully embraces Composer. As such a new command available in Executive provides some Composer provisioning.

## What is this provisioning?

EE expects its directories to be in certain locations. And it expects add-ons to have directories in certain locations as well. If we want to manage these as composer dependencies, then we'll need to do a little symlinking from Composer's `vendor` directory into the correct locations. Executive calls that "Composer Provisioning".

## The command

The command to run is simple. And once you've composer required Executive and have the front controller set up, you can (and will need to) run the provisioning command before Executive is installed. The command will symlink Executive's directories into the place ExpressionEngine expects them to be. The command to run is:

```shell
php ee executive composerProvision
```

## Add-on support for provisioning

Executive, as an add-on, is provisioned by the same command seen above (obviously). And it does so the same way any add-on can: by setting the composer.json `type` key to "ee-add-on" and by setting up some configuration in its composer.json `extra` object. The keys `handle` and `systemPath` are required. `themePath` is optional, and should be set if the add-on has front-end theme assets that should be available. Here's a sample:

```json
{
    "name": "somevendor/cooladdon",
    "version": "2.2.4",
    "description": "Some unique add-on",
    "type": "ee-add-on",
    "require": {
        "php": ">=7.1",
        "symfony/filesystem": "^4.1",
        "symfony/finder": "^4.1"
    },
    "extra": {
        "handle": "executive",
        "systemPath": "src/cooladdon",
        "themePath": "src/cooladdon_themes"
    }
}
```

## Provisioning ExpressionEngine Itself

Yes, you can use Composer to keep the entirety of ExpressionEngine's codebase out of your codebase! This is optional, but it's so great.

### Using the official open source repository

To provision using the open source repository, all you need to do is provide a release tag name in the composer.json `extra` object like this:

```json
{
    "name": "myproject",
    "require": {
        "buzzingpixel/executive-ee": "^3.0"
    },
    "require-dev": {
        "roave/security-advisories": "dev-master"
    },
    "extra": {
        "provisionEEVersionTag": "5.0.1"
    }
}
```

As long as the tag is a valid tag name from the official ExpressionEngine repository, Executive will download EE from that tag and provision EE at that tag version. Here is the EE tags page for you to reference: https://github.com/ExpressionEngine/ExpressionEngine/tags

### Provisioning from your own fork of EE

You can also provision from you own repository of EE. Here are some notes on doing that:

Any environment that needs to run `composer install` or `composer update` will need to have access to the repository. If it is a private repo, this can be done with ssh keys or GitHub api keys.

`composer require` EE from your repository. And in that private repository, the composer.json file should have the following configurations set in the `extra` object.

- `systemEEDir`
- `themesEEDir`

And it must have `type` set to "ee". Here's an example of a composer.json file for ExpressionEngine:

```json
{
    "name": "myuri/expressionengine",
    "description": "ExpressionEngine - dependency managed",
    "version": "4.3.4",
    "type": "ee",
    "extra": {
        "systemEEDir": "src/system/ee",
        "themesEEDir": "src/themes/ee"
    }
}
```

## Configuring

There are three configuration settings that you can add to your project's composer.json file. All are optional. All go in composer.json's `extra` object. If your composer.json doesn't have an `extra` key, you can simply add it.

### `publicDir`

Default: public.

If your public directory is named something other than `public`, let Executive know about it by adding `publicDir`. It should be relative to the root of your project.

Example composer.json:

```json
{
    "name": "My Project",
    "description": "My Project",
    "require": {
        "php": ">=7.1.0"
    },
    "config": {
        "optimize-autoloader": true
    },
    "extra": {
        "publicDir": "public_html"
    }
}
```

### `eeAddOns`

This configuration options allows add-ons to be installed and symlinked that are either registered with packagist, or otherwise are in a public repo and have a composer.json file, but do not have the settings in the composer.json file to support provisioning. You should provide a `handle` and `systemPath` relative to the project root for each addon. You can optionally provide a `themePath` also relative to project root.

Example:

```json
{
    "name": "My Project",
    "description": "My Project",
    "require": {
        "php": ">=7.1.0",
        "croxton/stash": "^v3.0.5",
        "some/addon": "^1.0"
    },
    "config": {
        "optimize-autoloader": true
    },
    "repositories": [
        {
            "type": "vcs",
            "url": "git@github.com:croxton/Stash.git"
        },
        {
            "type": "vcs",
            "url": "git@bitbucket.org:some/addon.git"
        }
    ],
    "extra": {
        "eeAddOns": [
            {
                "handle": "stash",
                "systemPath": "vendor/croxton/stash/system/user/addons/stash"
            },
           
            {
                "handle": "someaddon",
                "systemPath": "vendor/some/addon/src/someaddon",
                "themePath": "vendor/some/addon/src/someaddon_themes"
            }
        ]
    }
}
```

### `installFromDownload`

Some add-ons may be out on Github, but may not have a composer.json file. Or they may be available as a public zip download somewhere else. This headache inducing situation can be cured with this configuration option. As long as it's a zip file at a public URL, Executive can handle managing it for you. It will download the zip, unzip it into the `vendor` directory, and symlink the directories you specify. `systemPath` and `themePath` should be relative to the expanded zip contents so you may want to download the file, unzip and inspect it, then set the configuration for Executive. Here's an example of the composer configuration.

```json
{
    "name": "My Project",
    "description": "My Project",
    "require": {
        "php": ">=7.1.0"
    },
    "config": {
        "optimize-autoloader": true
    },
    "extra": {
        "installFromDownload": [
            {
                "url": "https://github.com/rsanchez/resource_router/archive/master.zip",
                "handle": "resource_router",
                "systemPath": "resource_router-master/system/expressionengine/third_party/resource_router"
            }
        ]
    }
}
```
