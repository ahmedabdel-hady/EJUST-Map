<?php

/**
 * @author flexphperia
 *
 */
class Vo_Dictionary extends Flexphperia_VoBase
{

    public $id;

    public $name;

    public $entries;

    public function __construct($data = null)
    {
        parent::__construct($data);
        
        // cast to integers, beter performance than setters
        $this->id = (int) $this->id;
    }
}