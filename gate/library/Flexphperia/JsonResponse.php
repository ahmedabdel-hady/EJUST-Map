<?php

/**
 * Class used to send response in json
 * 
 * Every json response has code and data fields.
 * 
 * Codes:
 * 1 - ok
 * 2 - async validation error (wrong password, not unique cssname , etc.)
 * 3 - wrong parameters passed
 * 4 - resource not found
 * 5 - not logged or session expired
 * 
 * 
 * @author flexphperia
 *
 */
class Flexphperia_JsonResponse extends Flexphperia_Escapifier
{

    public $code;

    public $data;

    public function __construct($code, $data, $escape = false)
    {
        parent::__construct();
        $this->code = $code;
        $this->data = $data;
        
        if (is_object($this->data) || is_array($this->data))
            $this->unsetUnderscored($this->data, $escape);
        else {
            if ($escape)
                $this->data = $this->_view->escape($this->data);
        }
    }

    /**
     * This function unsets all object variables that begins with _
     * and escapes all values if needed
     *
     * @param
     *            $obj
     */
    protected function unsetUnderscored(&$obj, $escape)
    {
        if (! $obj)
            return;
        
        foreach ($obj as $name => &$value) {
            if ($name[0] == '_') {
                unset($obj->$name);
                continue;
            }
            
            if (is_object($value) || is_array($value)) {
                $this->unsetUnderscored($value, $escape);
                continue;
            }
            
            if ($escape) {
                // array or object
                if (is_array($obj))
                    $obj[$name] = $this->_view->escape($value);
                else
                    $obj->$name = $this->_view->escape($value);
            }
        }
    }

    public function __toString()
    {
        return Zend_Json::encode($this);
    }
}
