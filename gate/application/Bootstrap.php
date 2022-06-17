<?php

/**
 * extends Zend_Application_Bootstrap_BootstrapAbstract not Zend_Application_Bootstrap_Bootstrap
 * to disable frontcontroller resource from being automatically initialzized
 * 
 * @author flexphperia
 *
 */
class Bootstrap extends Zend_Application_Bootstrap_BootstrapAbstract
{

    /**
     * Resource method that adds default bootstrap container (Zend_Registry) to
     * Zend_Registry
     * with this method we have on key "bootstrap" in Zend_Registry all the
     * resources used with
     * Bootstrap class
     */
    protected function _initRegistry()
    {
        $registry = Zend_Registry::getInstance();
        $registry->set('bootstrap', $this->getContainer());
    }

    /**
     * Resource method that sets application environment string into registry
     */
    protected function _initEnvironment()
    {
        $registry = Zend_Registry::getInstance();
        $registry->set('environment', $this->getEnvironment());
    }

    /**
     * Resource autoloaders are intended to manage namespaced library code that
     * follow Zend Framework coding standard guidelines, but which do not have a
     * 1:1
     * mapping between the class name and the directory structure.
     */
    protected function _initAutoloader()
    {
        $autoloader = $this->getApplication()->getAutoloader();
        
        $resourceLoader = new Zend_Loader_Autoloader_Resource(
            array(
                'basePath' => APPLICATION_PATH,
                'namespace' => null
            ));
        
        $resourceLoader->addResourceType('configs', 'configs/', 'Config')
            ->addResourceType('model', 'models/', 'Model')
            ->addResourceType('filter', 'filters/', 'Filter')
            ->addResourceType('controller', 'controllers/', 'Controller')
            ->addResourceType('router', 'router/', 'Router')
            ->addResourceType('validators', 'validators/', 'Validator')
            ->addResourceType('vo', 'vo/', 'Vo');
        $autoloader->pushAutoloader($resourceLoader);
    }

    /**
     * initialize uncaughtExceptionHandler
     * 
     * @throws Exception
     */
    protected function _initExceptionHandler()
    {
        $options = $this->getOptions();
        $path = $options['resources']['log']['stream']['writerParams']['stream'];
        if (! is_writable($path))
            throw new Exception('Bootsraper: Log file path not writable');
        
        if ($this->getEnvironment() == 'production')
            set_exception_handler(array(
                $this,
                'uncaughtExceptionHandler'
            ));
    }

    /**
     * Method required by abstract class
     */
    public function run()
    {
        // dispatch router
        $pseudoController = new Router_SuperMap();
        $pseudoController->dispatch();
    }

    /**
     * Log uncaught exceptions to file
     *
     * @param Exception $e            
     */
    public function uncaughtExceptionHandler($e)
    {
        $this->bootstrap('log');
        $logger = $this->getResource('log');
        $logger->err('Uncaught exception: ' . $e);
    }
}