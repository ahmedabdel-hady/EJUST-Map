<?php

/**
 * @author flexphperia
 *
 */
class Model_MarkerMapper extends Flexphperia_MapperBase
{

    public static function getTableName()
    {
        return 'sm_markers';
    }

    /**
     *
     * @var Model_FileOperator
     */
    protected static $_fileOperator;

    public static function getFileOperator()
    {
        if (! self::$_fileOperator)
            self::$_fileOperator = new Model_FileOperator();
        
        return self::$_fileOperator;
    }

    /**
     *
     * @var Model_MapMapper
     */
    protected static $_mapMapper;

    public static function getMapMapper()
    {
        if (! self::$_mapMapper)
            self::$_mapMapper = new Model_MapMapper();
        
        return self::$_mapMapper;
    }
    
    // used in view mappers
    public static $columns = array(
        'id',
        'markerTypeId',
        'enabled',
        '_mapId' => 'mapId',
        '_regionId' => 'regionId',
        'title',
        'icon',
        'image',
        'x',
        'y',
        'param1Value',
        'param2Value',
        'param3Value',
        'param4Value',
        'param5Value'
    );

    /**
     * returns marker details
     *
     * @param int $id            
     * @return Vo_Marker
     */
    public function fetchOne($id)
    {
        $select = $this->_db->select()
            ->from(self::getTableName(), self::$columns)
            ->where('id = ?', (int) $id); // automatically quoted;
        
        $stmt = $this->_db->query($select);
        
        $obj = $stmt->fetchObject('Vo_Marker');
        
        if (! $obj)
            return $obj;
        
        $this->fillMarkerDetails($obj, true);
        
        // find region object
        $rm = new Model_RegionMapper();
        $obj->region = $rm->fetchOne($obj->_regionId, true);
        
        return $obj;
    }

    /**
     * Saves markers positions
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
     * Save marker
     * 
     * @param Vo_Marker $vo            
     * @return string number id of marker
     */
    public function save(Vo_Marker $vo)
    {
        $data = array(
            'markerTypeId' => $vo->markerTypeId,
            'enabled' => $vo->enabled,
            'mapId' => $vo->map,
            'regionId' => $vo->region ? $vo->region : new Zend_Db_Expr('NULL'),
            'title' => $vo->title,
            'icon' => $vo->icon ? $vo->icon : new Zend_Db_Expr('NULL'),
            'image' => $vo->image ? $vo->image : new Zend_Db_Expr('NULL'),
            'x' => $vo->x,
            'y' => $vo->y,
            'param1Value' => $vo->param1Value ? $vo->param1Value : new Zend_Db_Expr('NULL'),
            'param2Value' => $vo->param2Value ? $vo->param2Value : new Zend_Db_Expr('NULL'),
            'param3Value' => $vo->param3Value ? $vo->param3Value : new Zend_Db_Expr('NULL'),
            'param4Value' => $vo->param4Value ? $vo->param4Value : new Zend_Db_Expr('NULL'),
            'param5Value' => $vo->param5Value ? $vo->param5Value : new Zend_Db_Expr('NULL')
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
     * Deletes marker
     *
     * @param int $id            
     */
    public function delete($id)
    {
        $this->_db->delete(self::getTableName(), 'id = ' . $this->_db->quote($id, Zend_Db::INT_TYPE));
    }

    /**
     * Fills marker details (icon, image object, map object)
     * 
     * @param Vo_Marker $vo            
     * @param unknown_type $withMap            
     */
    public function fillMarkerDetails(Vo_Marker $vo, $withMap = false)
    {
        $fo = self::getFileOperator();
        
        if ($vo->icon)
            $vo->icon = $fo->getIconObject($vo->icon);
        if ($vo->image)
            $vo->image = $fo->getImageObject($vo->image);
        
        if ($withMap) {
            $mm = self::getMapMapper();
            $vo->map = $mm->getMap($vo->_mapId);
        }
    }

    /**
     * Nullifies params values for specified marker type id and specified param
     * number .
     *
     *
     * @param int $markerTypeId            
     * @param int $paramNum            
     * @param int $paramValue
     *            - if specified it will nullify only for specified value
     */
    public function nullifyParamValue($markerTypeId, $paramNum, $paramValue = false)
    {
        $columnName = 'param' . $paramNum . 'Value';
        
        $data = array(
            $columnName => new Zend_Db_Expr('NULL')
        );
        
        $where = array(
            'markerTypeId = ?' => $markerTypeId
        );
        
        if ($paramValue)
            $where[$columnName . ' = ?'] = $paramValue;
        
        $this->_db->update(self::getTableName(), $data, $where);
    }
}