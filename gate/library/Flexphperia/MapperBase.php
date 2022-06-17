<?php

/**
 * Mappers are responsible for mapping object to db layer
 * @author flexphperia
 *
 */
class Flexphperia_MapperBase extends Flexphperia_Escapifier
{

    /**
     *
     * @var Zend_Db_Adapter_Pdo_Mysql
     */
    protected $_db;

    public function __construct()
    {
        parent::__construct();
        $this->_db = Zend_Db_Table::getDefaultAdapter();
    }

    public static function getTableName()
    {
        throw new Exception('To implement in sub classes');
    }

}
