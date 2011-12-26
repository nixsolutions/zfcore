<?php
/**
 * @category Public
 * @package  Bootstrap
 */

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Define path to public directory
defined('PUBLIC_PATH')
    || define('PUBLIC_PATH', dirname(__FILE__));

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

register_shutdown_function('errorHandler');

function errorHandler() {
    $error = error_get_last();
    if (!is_array($error)
        || !in_array($error['type'], array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR))) {
        return;
    }

    include_once 'error.php';
}

/** Zend_Application */
require_once 'Zend/Application.php';

try {
    $config = APPLICATION_PATH . '/configs/application.yaml';

    if (realpath($config)) {
        require_once 'Zend/Cache.php';
        $frontendOptions = array("lifetime" => 60*60*24,
                                 "automatic_serialization" => true,
                                 "automatic_cleaning_factor" => 1,
                                 "ignore_user_abort" => true);

        $backendOptions  = array("file_name_prefix" => APPLICATION_ENV . "_config",
                                 "cache_dir" =>  APPLICATION_PATH ."/../data/cache",
                                 "cache_file_umask" => 0644);

        // getting a Zend_Cache_Core object
        $cache = Zend_Cache::factory(
            'Core',
            'File',
            $frontendOptions,
            $backendOptions
        );

        if (!$result = $cache->load('application')) {
            require_once 'Zend/Config/Yaml.php';
            require_once 'Core/Config/Yaml.php';

            $result = new Core_Config_Yaml($config, APPLICATION_ENV);
            $result = $result->toArray();
            $cache->save($result, 'application');
        }
        $config = $result;
    } else {
        $config .= '.dist';
    }

    // Create application, bootstrap, and run
    $application = new Zend_Application(
        APPLICATION_ENV,
        $config
    );

    $application->bootstrap()
                ->run();
} catch (Exception $exception) {
    include_once 'error.php';
}