<?php

/**
 * @author flexphperia
 *
 */
class Vo_Search extends Flexphperia_VoBase
{

    public $mapId;

    public $markerTypeId;

    public $title;

    public $param1Value;

    public $param2Value;

    public $param3Value;

    public $param4Value;

    public $param5Value;

    public function __construct($data = null)
    {
        parent::__construct($data);
        
        // cast to integers, beeter performance than setters
        $this->mapId = (int) $this->mapId;
    }
}