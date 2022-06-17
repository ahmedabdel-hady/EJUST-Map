<?php

/**
 * @author flexphperia
 *
 */
class Vo_DictionaryEntry extends Flexphperia_VoBase
{

    public $id;

    public $value;

    public $_dictionaryId;

    public function __construct($data = null)
    {
        parent::__construct($data);
        
        // cast to integers, beeter performance than setters
        $this->id = (int) $this->id;
        $this->_dictionaryId = (int) $this->_dictionaryId;
    }

}