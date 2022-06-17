<?php

class Model_RegionMapper extends Flexphperia_MapperBase
{

    public static function getTableName()
    {
        return 'sm_regions';
    }

    protected $_columnsSimple = array(
        'id',
        'name',
        'mapId'
    );

    protected $_columnsFull = array(
        'id',
        'name',
        'mapId',
        'x',
        'y',
        'zoom'
    );

    /**
     * Returns one region details
     *
     * @param bool $id            
     * @param bool $simple
     *            if simple, unset some vo vars
     * @return NULL Vo_Region
     */
    public function fetchOne($id, $simple = false)
    {
        $select = $this->_db->select()
            ->from(self::getTableName(), $this->_columnsFull)
            ->where('id = ?', (int) $id); // automatically quoted;
        
        $stmt = $this->_db->query($select);
        
        $obj = $stmt->fetchObject('Vo_Region');
        
        if ($obj && $simple)
            $obj->toSimply();
        
        return $obj ? $obj : null;
    }

    /**
     * Fetches all regions or regions with specified ids.
     *
     * @param int $ids            
     * @return NULL array<Vo_Region>
     */
    public function fetch($ids = array())
    {
        if (! $ids)
            return null;
        
        $select = $this->_db->select()->from(self::getTableName(), $this->_columnsSimple);
        
        $select->where('id IN (' . implode(',', $ids) . ')');
        
        $stmt = $this->_db->query($select);
        $result = array();
        
        while (($obj = $stmt->fetchObject('Vo_Region')) != false) {
            $obj->toSimply();
            $result[$obj->id] = $obj;
        }
        return ! $result ? null : $result;
    }

    /**
     * Fetches regions for specified maps, used to display region list in admin
     * panel
     *
     * @param int $mapId            
     * @return null array
     */
    public function fetchForMapSimple($mapId)
    {
        $select = $this->_db->select();
        
        $select->from(self::getTableName(), $this->_columnsSimple);
        $select->order('name')->where('mapId = ?', (int) $mapId); // automatically quoted;
        
        $res = $this->_db->fetchAll($select);
        
        return $res ? $res : null;
    }

    /**
     * Returns regions with full details for specified map id.
     *
     * @return array
     */
    public function fetchForMapFull($mapId)
    {
        $select = $this->_db->select();
        
        $select->from(self::getTableName(), $this->_columnsFull);
        
        $select->where('mapId = ?', (int) $mapId); // automatically quoted;
        
        $select->order('name');
        
        $stmt = $this->_db->query($select);
        $result = array();
        
        while (($obj = $stmt->fetchObject()) != false) {
            $result[] = $obj;
        }
        return ! $result ? null : $result;
    }

    /**
     * Checks that region exists for specified map
     *
     * @param int $mapId            
     * @param int $regionId            
     * @return boolean
     */
    public function isRegionForMapExists($mapId, $regionId)
    {
        $select = $this->_db->select()
            ->from(self::getTableName())
            ->where('mapId = ?', $mapId)
            ->where('id = ?', $regionId);
        
        $res = $this->_db->fetchOne($select);
        
        return $res ? true : false;
    }

    /**
     * Saves region
     *
     * @param Vo_Region $vo            
     * @param bool $positionOnly
     *            - save only position and zoom
     * @return number id
     */
    public function save(Vo_Region $vo, $positionOnly = false)
    {
        $data = array(
            'id' => $vo->id,
            'x' => $vo->x,
            'y' => $vo->y,
            'zoom' => $vo->zoom
        );
        
        if (! $positionOnly)
            $data['name'] = $vo->name;
        
        if (! $vo->id && ! $positionOnly)         // insert
        {
            $data['mapId'] = $vo->mapId;
            $this->_db->insert(self::getTableName(), $data);
            return $this->_db->lastInsertId();
        } else {
            $this->_db->update(self::getTableName(), $data, 'id = ' . $this->_db->quote($vo->id, Zend_Db::INT_TYPE));
            return $vo->id;
        }
    }

    /**
     * Delete region
     *
     * @param int $id            
     */
    public function delete($id)
    {
        $this->_db->delete(self::getTableName(), 'id = ' . $this->_db->quote($id, Zend_Db::INT_TYPE));
    }

}