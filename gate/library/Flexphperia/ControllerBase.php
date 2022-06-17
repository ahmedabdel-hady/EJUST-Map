<?php

/**
 * Base class of all controllers
 * 
 * @author flexphperia
 *
 */
class Flexphperia_ControllerBase extends Flexphperia_Escapifier
{

    /**
     *
     * @var Zend_Controller_Request_Http
     */
    protected $_request;

    /**
     *
     * @var Zend_Cache_Frontend_Class
     */
    protected $_cache;

    function __construct(Zend_Controller_Request_Http $request)
    {
        parent::__construct();
        $this->_request = $request;
        
        $registry = Zend_Registry::getInstance();
        $manager = $registry->bootstrap->cachemanager;
        $this->_cache = $manager->getCache('front');
        $this->_cache->setCachedEntity($this); // set cache
    }

    /**
     * Method used by router to call proper action method from controller.
     * If action name is login than method from controller will be loginAction
     * Sub classes should override this method to call appropiate function.
     *
     * @param string $actionName            
     * @return string
     */
    public function caller($actionName)
    {
        $actionName = $actionName . 'Action';
        if (! method_exists($this, $actionName))
            $actionName = 'indexAction';
        
        return $actionName;
    }

    /**
     * Simply shortcut to creating Flexphperia_JsonResponse method from
     * controller
     *
     * @param int $code            
     * @param object $data            
     * @param bool $escape            
     */
    public function response($code, $data = null, $escape = false)
    {
        echo new Flexphperia_JsonResponse($code, $data, $escape);
    }

}