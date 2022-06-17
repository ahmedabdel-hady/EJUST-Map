<?php

/**
 * 
 * @author flexphperia
 *
 */
class Flexphperia_PseudoRouter
{

    /**
     * Controller name
     * 
     * @var string
     */
    protected $_controller;

    /**
     * Action name
     * 
     * @var string
     */
    protected $_action;

    /**
     *
     * @var Zend_Controller_Request_Http
     */
    protected $_request;

    function __construct()
    {
        $this->_request = new Zend_Controller_Request_Http();
    }

    /**
     * Returns array of allowed controller names
     */
    protected function getAllowedControllers()
    {
        return array(
            'index'
        );
    }

    protected function getDefaultController()
    {
        return 'index';
    }

    /**
     * Returns default action name
     */
    protected function getDefaultAction()
    {
        return 'index';
    }

    /**
     *
     * @return Zend_Controller_Request_Http
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Route action to proper controller
     */
    public function dispatch()
    {
        $this->_controller = $this->getRequest()->getQuery('c', $this->getDefaultController());
        $this->_action = $this->getRequest()->getQuery('a', $this->getDefaultAction());
        
        if ($this->_controller != $this->getDefaultController()) {
            if (array_search($this->_controller, $this->getAllowedControllers()) === false)
                $this->_controller = $this->getDefaultController();
        }
        
        $className = 'Controller_' . ucfirst(strtolower($this->_controller));
        
        $object = new $className($this->getRequest());
        
        if (! $object instanceof Flexphperia_ControllerBase)
            throw new Exception('Controller object must extend Flexphperia_ControllerBase class.');
            
            // call action by caller method
        $object->caller($this->_action);
    }

}