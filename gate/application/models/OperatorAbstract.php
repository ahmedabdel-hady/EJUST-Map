<?php

/**
 * Abstract class of application operators
 * @author flexphperia
 */
abstract class Model_OperatorAbstract extends Flexphperia_Escapifier
{

    /**
     *
     * @var Config_SuperMap
     */
    protected $_config;

    function __construct()
    {
        parent::__construct();
        $registry = Zend_Registry::getInstance();
        $this->_config = $registry->bootstrap->config;
    }
}
