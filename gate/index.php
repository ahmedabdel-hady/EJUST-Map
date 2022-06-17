<?php
// Define path to application directory
defined('APPLICATION_PATH') || define('APPLICATION_PATH', dirname(__FILE__) . '/application');

// Define application environment
defined('APPLICATION_ENV') || define('APPLICATION_ENV', 
    (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// include library path
set_include_path(implode(PATH_SEPARATOR, array(
    dirname(__FILE__) . '/library'
)));

/**
 * Zend_Application
 */
require_once 'Zend/Application.php';

// Create application, bootstrap, preparing all resources
$application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/config.ini');

$application->bootstrap()->run();