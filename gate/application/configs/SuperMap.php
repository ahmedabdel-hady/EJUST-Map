<?php

/**
 * Class that stores all additional configuration data used in app.
 * It must have properties named exactly the same as in config.ini
 * Flexphperia_Config resource class uses it to register in bootstrap file
 *
 * @author flexphperia
 *
 */
final class Config_SuperMap
{

    protected $_uploadsPath;

    public function setUploadsPath($value)
    {
        $this->_uploadsPath = $value;
    }

    public function getUploadsPath()
    {
        return $this->_uploadsPath;
    }

    public function __construct(array $options = null)
    {
        if (is_array($options)) {
            $this->setOptions($options);
        }
    }

    public function setOptions(array $options)
    {
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
        }
        return $this;
    }

    /**
     * Maps every setting properties into setters
     *
     * @param string $name            
     * @throws Exception
     */
    public function __set($name, $value)
    {
        $method = 'set' . $name;
        if (! method_exists($this, $method)) {
            throw new Exception('Invalid ' . __CLASS__ . ' property');
        }
        
        $this->$method($value);
    }

    /**
     * Maps every call for a properties into getters
     *
     * @param string $name            
     * @throws Exception
     */
    public function __get($name)
    {
        $method = 'get' . $name;
        if (! method_exists($this, $method)) {
            throw new Exception('Invalid ' . __CLASS__ . ' property');
        }
        return $this->$method();
    }

}
