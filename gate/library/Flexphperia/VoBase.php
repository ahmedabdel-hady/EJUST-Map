<?php

/**
 * Base class for all value objects.
 * 
 * All underscored variables are not sended to client.
 * 
 * @author flexphperia
 *
 */
class Flexphperia_VoBase
{

    /**
     *
     * @param object $data            
     */
    public function __construct($data = null)
    {
        if (! $data)
            return;
            
            // fill from data to object
        foreach ($data as $name => $value) {
            $this->$name = $value;
        }
    }
}
