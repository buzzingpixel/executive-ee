# System Requirements

## General requirements

- ExpressionEngine 3.5.10 or greater
- PHP 5.3.10 or newer
- MySQL 5.0.3 or newer

# Installing Or Updating

Installing and updating Executive is very easy.

1. Download the Executive zip file and unzip it
2. Copy `system/user/addons/executive` to the same directory location in your EE instance
    - If you are updating, replace the existing `executive` directory
3. Copy `themes/user/executive` to the same directory location in your EE instance
    - If you are updating, replace the existing `executive` directory
4. Copy `executive` to a convenient location for you to use on the command line
    - Update the `$system_path` in `executive` to point to the correct location of your system path
    - Ideally you would store this above webroot so you can only access it from the command line
        - Executive takes certain precautions to keep regular web requests from accessing the CLI, but it can only do so much so keeping it out of your public directory is recommended!

You can now either install or update Executive from the EE control panel or from the command line.

## From the command line:

### Installing

1. Run `php executive install`

### Updating

- Run `php executive executive runAddonUpdates`

## From the EE Control Panel

1. Log in to your EE control panel and navigate to the Add-on Manager
2. Scroll down to the "Third Party" section, locate "Executive" in the list and click "Install" or "Update"

Executive is now installed or updated.
