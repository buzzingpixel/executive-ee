<?php

// Set up autoloading
spl_autoload_register(function ($class) {
    // Turn the namespace into an array
    $ns = explode('\\', $class);

    // Put the file path together
    $ns = implode(DIRECTORY_SEPARATOR, $ns);

    // Load the file
    $file = "./tests/{$ns}.php";
    if (file_exists($file)) {
        include_once $file;
    }
});
