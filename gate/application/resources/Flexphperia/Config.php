<?php

/**
 * Resource plugin, prepares Config_SuperMap class 
 * It's is used while bootstraping application
 * It gets all settings that are in config.ini file
 * 
 * @author flexphperia
 *
 */
class Flexphperia_Config extends Zend_Application_Resource_ResourceAbstract
{
    
    /*
     * (non-PHPdoc) @see Zend_Application_Resource_Resource::init()
     */
    public function init()
    {
        $optArray = $this->getOptions();
        
        return new Config_SuperMap($optArray);
        // save all options into strongly typed object Config_SuperMap
    }

}