<?php

/**
 * @author flexphperia
 *
 */
class Model_LabelMapper extends Flexphperia_MapperBase
{

    public static function getTableName()
    {
        return 'sm_labels';
    }

    /**
     * Returns all label object for specified mapId prepared for generating view
     *
     * @param int $mapId            
     * @return array <Vo_Label>
     */
    public function fetchForMapViewAll($mapId)
    {
        $select = $this->_db->select()
            ->from(self::getTableName(), 
            array(
                'id',
                'type',
                '_typeValue' => 'typeValue',
                'x',
                'y',
                '_linkMapId' => 'linkMapId',
                '_linkRegionId' => 'linkRegionId'
            ))
            ->where('mapId = ?', (int) $mapId)
            ->where('enabled = 1'); // automatically quoted;
        
        $stmt = $this->_db->query($select);
        $result = array();
        
        while (($obj = $stmt->fetchObject('Vo_Label')) != false) {
            $result[$obj->id] = $this->_fillLabelDetails($obj, true);
        }
        
        return ! $result ? null : $result;
    }

    /**
     * Returns one label object
     *
     * @param int $id            
     * @return NULL Vo_Label
     */
    public function fetchOne($id)
    {
        $select = $this->_db->select()
            ->from(self::getTableName(), 
            array(
                'id',
                'type',
                'enabled',
                '_mapId' => 'mapId',
                '_typeValue' => 'typeValue',
                'x',
                'y',
                '_linkMapId' => 'linkMapId',
                '_linkRegionId' => 'linkRegionId'
            ))
            ->where('id = ?', (int) $id); // automatically quoted;
        
        $stmt = $this->_db->query($select);
        
        $obj = $stmt->fetchObject('Vo_Label');
        
        if (! $obj)
            return null;
        
        return $this->_fillLabelDetails($obj);
    }

    /**
     * Fills label object details
     *
     * @param Vo_Label $vo            
     * @param bool $skipLinkObj
     *            skip or not link map and region objects
     * @return Vo_Label
     */
    protected function _fillLabelDetails(Vo_Label $vo, $skipLinkObj = false)
    {
        if ($vo->type == 'icon') {
            $fo = new Model_FileOperator();
            $vo->icon = $fo->getIconObject($vo->_typeValue);
        } else {
            $vo->text = $vo->_typeValue;
        }
        
        if (! $skipLinkObj) {
            $mm = new Model_MapMapper();
            $vo->map = $mm->getMap($vo->_mapId);
            $vo->linkMap = $mm->getMap($vo->_linkMapId);
            
            $rm = new Model_RegionMapper();
            $vo->linkRegion = $rm->fetchOne($vo->_linkRegionId, true);
        }
        
        return $vo;
    }

    /**
     * Saves positions of labels
     *
     * @param obj $array            
     */
    public function savePositions($array)
    {
        if (! $array)
            return;
        
        foreach ($array as $obj) {
            $data = array(
                'x' => $obj['x'],
                'y' => $obj['y']
            );
            
            $this->_db->update(self::getTableName(), $data, 'id = ' . $this->_db->quote($obj['id'], Zend_Db::INT_TYPE));
        }
    }

    /**
     * Save label details
     *
     * @param Vo_Label $vo            
     * @return string number of label
     */
    public function save(Vo_Label $vo)
    {
        $data = array(
            'type' => $vo->type,
            'enabled' => $vo->enabled,
            'typeValue' => $vo->type == 'text' ? $vo->text : $vo->icon,
            'x' => $vo->x,
            'y' => $vo->y,
            'mapId' => $vo->map,
            'linkMapId' => $vo->linkMap ? $vo->linkMap : new Zend_Db_Expr('NULL'),
            'linkRegionId' => $vo->linkRegion ? $vo->linkRegion : new Zend_Db_Expr('NULL')
        );
        
        if (! $vo->id)         // insert
        {
            $this->_db->insert(self::getTableName(), $data);
            return $this->_db->lastInsertId();
        } else {
            $this->_db->update(self::getTableName(), $data, 'id = ' . $this->_db->quote($vo->id, Zend_Db::INT_TYPE));
            return $vo->id;
        }
    }

    /**
     * Delete label
     *
     * @param int $id            
     */
    public function delete($id)
    {
        $this->_db->delete(self::getTableName(), 'id = ' . $this->_db->quote($id, Zend_Db::INT_TYPE));
    }
}