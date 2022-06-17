<?php

/**
 * @author flexphperia
 */
class Vo_Settings extends Flexphperia_VoBase
{

    public $panelOpened;

    public $disableViewTab;

    public $defaultMarkerType;

    public function __construct($data = null)
    {
        parent::__construct($data);
        $this->panelOpened = (int) $this->panelOpened;
        $this->disableViewTab = (int) $this->disableViewTab;
    }

}