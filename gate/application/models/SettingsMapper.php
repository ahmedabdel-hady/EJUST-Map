<?php

/**
 * @author flexphperia
 *
 */
class Model_SettingsMapper extends Flexphperia_MapperBase
{

    public static function getTableName()
    {
        return 'sm_settings';
    }

    /**
     * Fetch settings
     *
     * @return Vo_Settings
     */
    public function fetch()
    {
        $select = $this->_db->select()->from(self::getTableName(), 
            array(
                'panelOpened',
                'disableViewTab',
                'defaultMarkerType'
            ));
        
        $stmt = $this->_db->query($select);
        $result = array();
        
        $obj = $stmt->fetchObject('Vo_Settings');
        
        return $obj;
    }

    /**
     * Save settings
     * 
     * @param Vo_Settings $settings            
     */
    public function update(Vo_Settings $settings)
    {
        $data = array(
            'panelOpened' => $settings->panelOpened,
            'disableViewTab' => $settings->disableViewTab,
            'defaultMarkerType' => $settings->defaultMarkerType ? $settings->defaultMarkerType : new Zend_Db_Expr('NULL')
        );
        
        $this->_db->update(self::getTableName(), $data);
    }

    /**
     * Update password
     *
     * @param string $hash            
     * @param string $salt            
     */
    public function updatePassword($hash, $salt)
    {
        // automatically quoted
        $data = array(
            'password' => $hash,
            'salt' => $salt
        );
        $this->_db->update(self::getTableName(), $data);
    }
}