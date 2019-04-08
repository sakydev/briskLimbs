<?php

ini_set('display_errors', 'On');
error_reporting(-1);

if (!file_exists(__DIR__ . '/configs/db.php'))
{
    header("Location: installer/install.php");
    exit;
}
require 'configs/constants.php';
require 'configs/db.php';
require 'helpers/devFunctions.php';
require 'helpers/videoFunctions.php';
require 'helpers/functions.php';

spl_autoload_register(function ($class)
{
    $class = ucfirst($class);
    if (file_exists(MODEL_DIRECTORY . '/' . $class . '.php'))
    {
        require_once MODEL_DIRECTORY . '/' . $class . '.php';
    }
    else
    {
        $class = strtolower($class);
        if (file_exists(HELPERS_DIRECTORY . '/' . $class . '.php'))
        {
            require_once HELPERS_DIRECTORY . '/' . $class . '.php';
        }
        else
        {
            exit(MODEL_DIRECTORY . '/' . $class . '.php');
            exit('Error Loading Class File |' . $class . '| core:' . CORE_DIRECTORY);
        }
    }
});

session_start();
$database = new Database($DATABASE_CONFIGS['host'], $DATABASE_CONFIGS['username'], $DATABASE_CONFIGS['password'], $DATABASE_CONFIGS['database']);
