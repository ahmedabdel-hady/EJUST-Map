<?php

/**
 * @author flexphperia
 *
 */
class Vo_Label extends Flexphperia_VoBase
{

    public $id;

    public $type;

    public $enabled;

    public $icon;

    public $text;

    public $x;

    public $y;

    public $map; // mapObject
    public $linkMap; // mapObject
    public $linkRegion; // region simple object
                        
    // not sended
    public $_typeValue; // icon object or text value
    public $_mapId;

    public $_linkmapId;

    public $_linkRegionId;

    public function __construct($data = null)
    {
        parent::__construct($data);
        
        // cast to integers, beeter performance than setters
        $this->id = (int) $this->id;
        $this->enabled = (int) $this->enabled;
        $this->x = (int) $this->x;
        $this->y = (int) $this->y;
        $this->_linkmapId = (int) $this->_linkmapId;
        $this->_linkRegionId = (int) $this->_linkRegionId;
    }
}