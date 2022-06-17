<?php

/**
 * @author flexphperia
 *
 */
class Vo_Marker extends Flexphperia_VoBase
{

    public $id;

    public $title;

    public $markerTypeId;

    public $enabled;

    public $map;

    /**
     *
     * @var Vo_Region
     */
    public $region;

    public $x;

    public $y;

    public $icon;

    public $image;

    public $param1Value;

    public $param2Value;

    public $param3Value;

    public $param4Value;

    public $param5Value;
    
    // not sended
    public $_mapId;

    public $_regionId;

    /**
     * Used for generating html only in ViewDataMApper
     * 
     * @var Vo_MarkerType
     */
    public $_type;

    public function __construct($data = null)
    {
        parent::__construct($data);
        
        // cast to integers, beeter performance than setters
        $this->id = (int) $this->id;
        $this->markerTypeId = (int) $this->markerTypeId;
        $this->enabled = (int) $this->enabled;
        $this->x = (int) $this->x;
        $this->y = (int) $this->y;
        $this->_mapId = (int) $this->_mapId;
        $this->_regionId = (int) $this->_regionId;
    }
	
	



}