<?php

/**
 * App router
 * 
 * @author flexphperia
 *
 */
class Router_SuperMap extends Flexphperia_PseudoRouter
{

    protected function getAllowedControllers()
    {
        return array(
            'front',
            'admin'
        );
    }

    protected function getDefaultController()
    {
        return 'front';
    }
}