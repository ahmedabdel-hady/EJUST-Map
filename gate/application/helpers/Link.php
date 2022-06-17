<?php

/**
 * View helper used to create  link
 * 
 * @author flexphperia
 *
 */
class Helper_Link extends Zend_View_Helper_Abstract
{

    public function link($value)
    {
		$array = explode('||||', $value);
		
		return '<a href="' . $array[1] . '" ' .($array[2] ? 'target="_blank"' : '' ). '>'. $this->view->escape($array[0]) .'</a>';
	}
	

    
}
