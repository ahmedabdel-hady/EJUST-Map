<?php

/**
 * Class that holds reference to Zend View
 * 
 * @author flexphperia
 *
 */
class Flexphperia_Escapifier
{

    /**
     *
     * @var Zend_View
     */
    protected $_view;

    public function __construct()
    {
        $registry = Zend_Registry::getInstance();
        $this->_view = $registry->bootstrap->view;
    }

}
