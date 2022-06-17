<?php

/**
 * @author flexphperia
 *
 */
class Vo_MarkerType extends Flexphperia_VoBase
{

    public $id;

    public $name;

    public $cssName;

    public $defaultIcon;

    public $markerColor;

    public $markerHoveredColor;

    public $showOnLegend;

    public $params;

    public function __construct($data = null)
    {
        parent::__construct($data);
        
        $this->id = (int) $this->id;
        $this->showOnLegend = (int) $this->showOnLegend;
    }
}