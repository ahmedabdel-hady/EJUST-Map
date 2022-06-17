<?php

/**
 * View helper used to create long text link
 * 
 * @author flexphperia
 *
 */
class Helper_LongTextLink extends Zend_View_Helper_Abstract
{

    public function longTextLink($longText, $label)
    {
        return '<a href="#" data-cfm-long="' . str_replace('"', '\'', $longText) . '">' . $this->view->escape($label) .
             '</a>';
        }
    
}
