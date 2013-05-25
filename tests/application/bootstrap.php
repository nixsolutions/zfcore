<?php
ini_set("memory_limit", "512M");
date_default_timezone_set('Europe/Kiev');
defined('PHPUNIT')
    || define('PHPUNIT', true);
// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV')?getenv('APPLICATION_ENV'):'testing'));

// Define path to public directory
defined('PUBLIC_PATH')
    || define('PUBLIC_PATH', APPLICATION_PATH . '/../public/');

// Define short alias for DIRECTORY_SEPARATOR
defined('DS')
    || define('DS', DIRECTORY_SEPARATOR);

// Define short alias for DIRECTORY_SEPARATOR
defined('START_TIMER')
    || define('START_TIMER', microtime(true));

    $_SERVER['HTTP_HOST'] = 'localhost';
    $_SERVER['REQUEST_URI'] = '/';
    $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

// Ensure library/ is on include_path
set_include_path(
    implode(
        PATH_SEPARATOR,
        array(
            realpath(APPLICATION_PATH . '/../library'),
            get_include_path(),
        )
    )
);

require_once 'ControllerTestCase.php';
require_once 'TestListener.php';

ControllerTestCase::appInit();
