<?php

/**
 * @author flexphperia
 *
 */
class Vo_Region extends Flexphperia_VoBase
{

    public $id;

    public $mapId;

    public $name;

    public $x;

    public $y;

    public $zoom;

    public function __construct($data = null)
    {
        parent::__construct($data);
        
        // cast to integers, beeter performance than setters
        $this->id = (int) $this->id;
        $this->mapId = (int) $this->mapId;
        $this->x = (int) $this->x;
        $this->y = (int) $this->y;
    }

    public function toSimply()
    {
        unset($this->x);
        unset($this->y);
        unset($this->zoom);
        
        return $this;
    }
}