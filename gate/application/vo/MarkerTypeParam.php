<?php

/**
 * @author flexphperia
 *
 */
class Vo_MarkerTypeParam extends Flexphperia_VoBase
{

    public $_id;

    public $_markerTypeId;

    public $enabled;

    public $number;

    public $type;

    public $typeValue;

    public $label;

    public $showLabel;

    public $searchable;

    public $alwaysVisible;

    public $_position;

    public function __construct($data = null)
    {
        parent::__construct($data);
        
        // cast to integers, beeter performance than setters
        $this->_id = (int) $this->_id;
        $this->_markerTypeId = (int) $this->_markerTypeId;
        $this->enabled = (int) $this->enabled;
        $this->number = (int) $this->number;
        $this->showLabel = (int) $this->showLabel;
        $this->searchable = (int) $this->searchable;
        $this->alwaysVisible = (int) $this->alwaysVisible;
        $this->_position = (int) $this->_position;
    }
	

}