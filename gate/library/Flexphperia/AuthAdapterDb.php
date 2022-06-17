<?php

/**
 * Authentication adapter used to authenticate user with password stored in db
 * 
 * @author flexphperia
 *
 */
class Flexphperia_AuthAdapterDb implements Zend_Auth_Adapter_Interface
{

    /**
     * Singleton instance
     *
     * @var Flexphperia_AuthAdapterDb
     */
    protected static $_instance = null;

    /**
     * Database Connection
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_zendDb = null;

    /**
     *
     * @var Zend_Db_Select
     */
    protected $_dbSelect = null;

    /**
     * $_tableName - the table name to check
     *
     * @var string
     */
    protected $_tableName = null;

    /**
     * $_passwordHashColumn - columns to be used as the credentials
     *
     * @var string
     */
    protected $_passwordHashColumn = null;

    /**
     * $_saltColumn - columns to be used as the credentials
     *
     * @var string
     */
    protected $_saltColumn = null;

    /**
     * $_resultRow - Results of database authentication query
     *
     * @var array
     */
    protected $_resultRow = null;

    protected $_password;

    public function setPassword($password)
    {
        $this->_password = $password;
    }

    /**
     * __construct() - Sets configuration options
     *
     * @param Zend_Db_Adapter_Abstract $zendDb
     *            If null, default database adapter assumed
     * @param string $tableName            
     * @param string $passwordHashColumn            
     * @param string $saltColumn            
     * @return void
     */
    public function __construct(Zend_Db_Adapter_Abstract $zendDb = null, $tableName = null, $passwordHashColumn = null, 
        $saltColumn = null)
    {
        $this->_setDbAdapter($zendDb);
        
        if (null !== $tableName) {
            $this->setTableName($tableName);
        }
        
        if (null !== $passwordHashColumn) {
            $this->setPasswordHashColumn($passwordHashColumn);
        }
        
        if (null !== $saltColumn) {
            $this->setSaltColumn($saltColumn);
        }
        
        self::$_instance = $this;
    }

    /**
     * Returns an instance of Flexphperia_AuthAdapterDb
     *
     * Singleton pattern implementation
     *
     * @return Flexphperia_AuthAdapterDb Provides a fluent interface
     */
    public static function getInstance()
    {
        return self::$_instance;
    }

    /**
     * _setDbAdapter() - set the database adapter to be used for quering
     *
     * @param
     *            Zend_Db_Adapter_Abstract
     * @throws Zend_Auth_Adapter_Exception
     * @return Flexphperia_Auth_Adapter_DbTable
     */
    protected function _setDbAdapter(Zend_Db_Adapter_Abstract $zendDb = null)
    {
        $this->_zendDb = $zendDb;
        
        /**
         * If no adapter is specified, fetch default database adapter.
         */
        if (null === $this->_zendDb) {
            require_once 'Zend/Db/Table/Abstract.php';
            $this->_zendDb = Zend_Db_Table_Abstract::getDefaultAdapter();
            if (null === $this->_zendDb) {
                require_once 'Zend/Auth/Adapter/Exception.php';
                throw new Zend_Auth_Adapter_Exception('No database adapter present');
            }
        }
        
        return $this;
    }

    /**
     * setTableName() - set the table name to be used in the select query
     *
     * @param string $tableName            
     * @return Flexphperia_Auth_Adapter_DbTable Provides a fluent interface
     */
    public function setTableName($tableName)
    {
        $this->_tableName = $tableName;
        return $this;
    }

    /**
     * setPasswordHashColumn() - set the column name to be used as the
     * credential column
     *
     * @param string $passwordHashColumn            
     * @return Flexphperia_Auth_Adapter_DbTable Provides a fluent interface
     */
    public function setPasswordHashColumn($value)
    {
        $this->_passwordHashColumn = $value;
        return $this;
    }

    /**
     * setSaltColumn() - set the column name to be used as the credential column
     *
     * @param string $saltColumn            
     * @return Flexphperia_Auth_Adapter_DbTable Provides a fluent interface
     */
    public function setSaltColumn($value)
    {
        $this->_saltColumn = $value;
        return $this;
    }

    /**
     * Gets from db details
     *
     * @return false if not found | stdclass [id_key, passwordHash, salt]
     */
    protected function getPassDetailsFromDb()
    {
        $select = $this->_zendDb->select()
            ->from($this->_tableName, 
            array(
                'passwordHash' => $this->_passwordHashColumn,
                'salt' => $this->_saltColumn
            ))
            ->limit(1);
        
        $this->_zendDb->setFetchMode(Zend_Db::FETCH_OBJ);
        
        return $this->_zendDb->fetchRow($select);
    
    }
    
    // zwraca false jezeli nie udalo sie zalogowac
    // returns phpstdclass if success
    public function preAuthentciate($password)
    {
        $passDetailsDb = $this->getPassDetailsFromDb();
        
        // pass not found
        if ($passDetailsDb == false)
            return false;
            
            // generate hash for a passed password, use salt from db
        $newPassHash = $this->generateHash($password, $passDetailsDb->salt);
        
        if ($newPassHash != $passDetailsDb->passwordHash)
            return false;
        else
            return true;
    }

    /**
     * authenticate() - defined by Zend_Auth_Adapter_Interface.
     * This method is called to
     * attempt an authentication. Previous to this call, this adapter would have
     * already
     * been configured with all necessary information to successfully connect to
     * a database
     * table and attempt to find a record matching the provided identity.
     *
     * @throws Zend_Auth_Adapter_Exception if answering the authentication query
     *         is impossible
     * @return Zend_Auth_Result
     */
    public function authenticate()
    {
        $this->_authenticateSetup();
        
        $this->_authenticateResultInfo = array(
            'code' => Zend_Auth_Result::FAILURE,
            'messages' => array()
        );
        
        $identity = $this->preAuthentciate($this->_password);
        
        if ($identity == false) {
            return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, null, 
                array(
                    'Authentication failed'
                ));
        } else {
            return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $identity);
        }
    }

    /**
     * _authenticateSetup() - This method abstracts the steps involved with
     * making sure that this adapter was indeed setup properly with all
     * required pieces of information.
     *
     * @throws Zend_Auth_Adapter_Exception - in the event that setup was not
     *         done properly
     * @return true
     */
    protected function _authenticateSetup()
    {
        $exception = null;
        
        if ($this->_tableName == '') {
            $exception = 'A tableName must be supplied for the Flexphperia_AuthAdapterDb authentication adapter.';
        } elseif ($this->_passwordHashColumn == '') {
            $exception = 'A passwordHashColumn column must be supplied for the Flexphperia_AuthAdapterDb authentication adapter.';
        } elseif ($this->_saltColumn == '') {
            $exception = 'A saltColumn column must be supplied for the Flexphperia_AuthAdapterDb authentication adapter.';
        }
        
        if (empty($this->_password)) {
            require_once 'Zend/Auth/Adapter/Exception.php';
            throw new Zend_Auth_Adapter_Exception('password should be set');
        }
        
        if (null !== $exception) {
            /**
             *
             * @see Zend_Auth_Adapter_Exception
             */
            require_once 'Zend/Auth/Adapter/Exception.php';
            throw new Zend_Auth_Adapter_Exception($exception);
        }
        
        return true;
    }

    /**
     * Generate salt
     *
     * @return string
     */
    public static function generateSalt()
    {
        $key = '!@#$%^&*()_+=-{}][;";/?<>.,';
        return substr(hash('sha512', uniqid(rand(), true) . $key . microtime()), 0, 16);
    }

    /**
     * Hashes passed phrase with salt
     *
     * @param string $phrase            
     * @param string $salt            
     * @return string
     */
    public static function generateHash($phrase, $salt)
    {
        // var_dump($phrase);
        return hash('sha512', $salt . $phrase);
    }

}
