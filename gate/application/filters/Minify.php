<?php

/**
 * Simple minifier filter
 * Removes all not needed space, new lines and tabs
 * 
 * @author flexphperia
 *
 */
class Filter_Minify implements Zend_Filter_Interface
{

    public function filter($string)
    {
        return preg_replace(array(
            '/>\s+/',
            '/\s+</',
            '/[\r\n]+/'
        ), array(
            '>',
            '<',
            ' '
        ), $string);
    }
}
