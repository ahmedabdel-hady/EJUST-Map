<?php

class Model_MarkerTypeMapper extends Flexphperia_MapperBase
{

    const PARAMS_NUM = 5;

    public static function getTableName()
    {
        return 'sm_marker-types';
    }

    public static function getParamsTableName()
    {
        return 'sm_marker-types-params';
    }

    protected $_columns = array(
        '_id' => 'id',
        '_markerTypeId' => 'markerTypeId',
        'enabled',
        'number',
        'type',
        'typeValue',
        'label',
        'showLabel',
        'searchable',
        'alwaysVisible',
        '_position' => 'position'
    );

    /**
     * Returns all params for specified marker type (ALL params, disabled too)
     *
     * @return array <Vo_MarkerTypeParam>
     */
    public function fetchParams($markerTypeId)
    {
        $select = $this->_db->select()
            ->from(self::getParamsTableName(), $this->_columns)
            ->where('markerTypeId = ?', $markerTypeId);
        
        $stmt = $this->_db->query($select);
        $params = array();
        
        while (($obj = $stmt->fetchObject('Vo_MarkerTypeParam')) != false) {
            $params[$obj->number] = $obj;
        }
        return $params;
    }

    /**
     * Returns one marker type.
     *
     * @param int $id            
     * @param bool $onlyEnabledParams            
     * @param bool $onlySearchableParams            
     * @return NULL Vo_MarkerType
     */
    public function fetchOne($id, $onlyEnabledParams = true, $onlySearchableParams = false)
    {
        $arrRes = $this->fetchAll(false, array(
            $id
        ), $onlyEnabledParams, $onlySearchableParams);
        if (! $arrRes)
            return null;
        
        return $arrRes[key($arrRes)]; // return first result
    }

    /**
     * Returns complete array with all marker types with params ordered
     * Array kesy are marker type ids
     *
     * @param bool $simpleList            
     * @param array $whereIds            
     * @param bool $onlyEnabledParams            
     * @param bool $onlySearchableParams            
     * @param bool $withoutParams            
     * @return NULL array
     */
    public function fetchAll($simpleList = false, $whereIds = array(), $onlyEnabledParams = true, $onlySearchableParams = false, 
        $withoutParams = false)
    {
        if (! $simpleList && ! $withoutParams) {
            // get all params
            $select = $this->_db->select()
                ->from(self::getParamsTableName(), $this->_columns)
                ->order(array(
                'markerTypeId',
                'position'
            ));
            
            if ($whereIds)
                $select->where('markerTypeId IN (' . implode(',', $whereIds) . ')');
            
            if ($onlyEnabledParams)
                $select->where('enabled = 1');
            
            if ($onlySearchableParams)
                $select->where('searchable = 1');
            
            $stmt = $this->_db->query($select);
            $paramsByMarkerTypeId = array();
            
            while (($obj = $stmt->fetchObject('Vo_MarkerTypeParam')) != false) {
                $paramsByMarkerTypeId[$obj->_markerTypeId][] = $obj;
            }
        }
        
        $columns = array(
            'id',
            'name'
        ); // columns for simple list
        
        if (! $simpleList) {
            $columns = array_merge($columns, 
                array(
                    'cssName',
                    'defaultIcon',
                    'markerColor',
                    'markerHoveredColor',
                    'showOnLegend'
                ));
        }
        
        $select = $this->_db->select()
            ->from(self::getTableName(), $columns)
            ->order(array(
            'name'
        ));
        
        if ($simpleList) {
            $res = $this->_db->fetchAll($select);
            return $res ? $res : null;
        }
        
        if ($whereIds)
            $select->where('id IN (' . implode(',', $whereIds) . ')');
        
        $stmt = $this->_db->query($select);
        $result = array();
        
        $fo = new Model_FileOperator();
        while (($obj = $stmt->fetchObject('Vo_MarkerType')) != false) {
            $result[$obj->id] = $obj;
            $obj->params = isset($paramsByMarkerTypeId[$obj->id]) ? $paramsByMarkerTypeId[$obj->id] : null;
            $obj->defaultIcon = $fo->getIconObject($obj->defaultIcon);
        }
        
        return $result;
    }

    /**
     * Saves marker type
     *
     * @param Vo_MarkerType $vo            
     * @param
     *            $editedParams
     * @param
     *            $reordered
     */
    public function save(Vo_MarkerType $vo, $editedParams, $reorderedData)
    {
        $data = array(
            'name' => $vo->name,
            'cssName' => $vo->cssName,
            'defaultIcon' => $vo->defaultIcon,
            'markerColor' => $vo->markerColor,
            'markerHoveredColor' => $vo->markerHoveredColor,
            'showOnLegend' => $vo->showOnLegend
        );
        
        if (! $vo->id)         // insert
        {
            $this->_db->insert(self::getTableName(), $data);
            $mtId = $this->_db->lastInsertId();
            
            $newParamsData = array(
                'markerTypeId' => $mtId
            );
            
            // create 5 params and insert
            for ($i = 1; $i <= self::PARAMS_NUM; $i ++) {
                $newParamsData['number'] = $i;
                $newParamsData['position'] = $i - 1;
                $this->_db->insert(self::getParamsTableName(), $newParamsData);
            }
        } else {
            // update
            $this->_db->update(self::getTableName(), $data, 'id = ' . $this->_db->quote($vo->id, Zend_Db::INT_TYPE));
            $mtId = $vo->id;
        }
        
        if ($editedParams || $reorderedData) {
            // get params for edited marker type id)
            $params = $this->fetchParams($mtId);
        }
        
        if ($editedParams) {
            foreach ($editedParams as $value) {
                $updParamVo = new Vo_MarkerTypeParam($value);
                $oldParamVo = $params[$updParamVo->number];
                $data = array(
                    'enabled' => $updParamVo->enabled,
                    'type' => $updParamVo->type,
                    'label' => $updParamVo->label,
                    'showLabel' => $updParamVo->showLabel,
                    'searchable' => $updParamVo->searchable,
                    'alwaysVisible' => $updParamVo->alwaysVisible,
                    'typeValue' => $updParamVo->typeValue && $updParamVo->type == 'dictionary' ? $updParamVo->typeValue : new Zend_Db_Expr(
                        'null')
                );
                
                $where = array(
                    'markerTypeId = ?' => $mtId,
                    'number = ?' => $updParamVo->number
                );
                
                $this->_db->update(self::getParamsTableName(), $data, $where);
                
                // we have to nullify marker "paramsvalues" if typeValue
                // (dicitonary id) or type is changing
                // casting to string is needed coz in db we have null and
                // nothing in obj
                if ($oldParamVo->type != $updParamVo->type ||
                     (string) $oldParamVo->typeValue != (string) $updParamVo->typeValue) {
                        $mm = new Model_MarkerMapper();
                        $mm->nullifyParamValue($mtId, $updParamVo->number);
                    }
                }
            }
            
            if ($reorderedData) {
                foreach ($reorderedData as $value) {
                    $paramVo = $params[$value['number']];
                    
                    $this->_updateOrder($mtId, $paramVo->_position, true); // remove
                                                                           // gap
                                                                           // after
                                                                           // removing
                    $this->_updateOrder($mtId, $value['position'], false); // prepare
                                                                            // new
                                                                            // position
                                                                            // gap
                                                                            
                    // update position
                    $this->_db->update(self::getParamsTableName(), array(
                        'position' => $value['position']
                    ), array(
                        'number = ?' => $value['number'],
                        'markerTypeId = ?' => $mtId
                    ));
                }
            }
        
        }

        /**
         * Used to update params position
         *
         * @param int $markerTypeId            
         * @param int $newPos            
         * @param bool $up            
         */
        private function _updateOrder($markerTypeId, $newPos, $up)
        {
             // up or down
            $data = array(
                'position' => new Zend_Db_Expr($up ? 'position - 1' : 'position + 1')
            );
            
            $where = array(
                'markerTypeId = ?' => $markerTypeId,
                'position >' . (! $up ? '=' : '') . ' ?' => $newPos
            );
            
            // update all siblings postions
            $this->_db->update(self::getParamsTableName(), $data, $where);
        }

        /**
         * Deletes marker type with all params
         * 
         * @param int $id            
         */
        public function delete($id)
        {
            $this->_db->delete(self::getTableName(), 'id = ' . $this->_db->quote($id, Zend_Db::INT_TYPE));
        }

        /**
         * Finds and nullifies all marker values that uses founded marker type
         * that
         * that have params that type are 'dictionary' and specified dictionary
         * id.
         *
         * @param int $dictionaryId            
         * @param int $dictEntryId
         *            - if specified will only nullify
         */
        public function nullifyDictinaryUsagers($dictionaryId, $dictEntryId = false)
        {
            $select = $this->_db->select()
                ->from(self::getParamsTableName(), array(
                'markerTypeId',
                'number'
            ))
                ->where('type = ?', 'dictionary')
                ->where('typeValue = ?', $dictionaryId); // automatically
                                                         // quoted;
            
            $stmt = $this->_db->query($select);
            
            $mm = new Model_MarkerMapper();
            while (($obj = $stmt->fetchObject()) != false) {
                $mm->nullifyParamValue($obj->markerTypeId, $obj->number, $dictEntryId);
            }
        }

        /**
         * Disables and sets to null typeValue column in all parameters that
         * uses dictionary id.
         *
         * @param int $dictionaryId            
         */
        public function disableDictionaryUsagers($dictionaryId)
        {
            $data = array(
                'typeValue' => new Zend_Db_Expr('NULL'),
                'enabled' => 0
            );
            
            $where = array(
                'type = ?' => 'dictionary',
                'typeValue = ?' => $dictionaryId
            );
            
            $this->_db->update(self::getParamsTableName(), $data, $where);
        }
    
    }