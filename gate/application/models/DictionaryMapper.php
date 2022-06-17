<?php

/**
 * @author flexphperia
 */
class Model_DictionaryMapper extends Flexphperia_MapperBase
{

    public static function getTableName()
    {
        return 'sm_dictionaries';
    }

    public static function getEntriesTableName()
    {
        return 'sm_dictionaries-entries';
    }

    /**
     * Returns simple list
     *
     * @return array
     */
    public function fetchAll()
    {
        $select = $this->_db->select()
            ->from(self::getTableName(), array(
            'id',
            'name'
        ))
            ->order('name');
        
        return $this->_db->fetchAll($select);
    }

    public function fetch($id)
    {
        $select = $this->_db->select()
            ->from(self::getTableName(), array(
            'id',
            'name'
        ))
            ->where('id = ?', (int) $id); // automatically quoted;
        
        $stmt = $this->_db->query($select);
        
        $obj = $stmt->fetchObject('Vo_Dictionary');
        
        if ($obj) {
            $obj->entries = $this->fetchEntries(array(
                $obj->id
            ), false);
            $obj->entries = $obj->entries[$obj->id];
        }
        
        return $obj ? $obj : null;
    }

    /**
     * Fetches entries for dictionaries
     *
     * @param array $dictIds            
     * @param bool $keyIsEntryId
     *            dictionary array keys are id of entry
     * @return array of arrays <Vo_DictionaryEntry>
     */
    public function fetchEntries($dictIds = false, $keyIsEntryId = true)
    {
        $select = $this->_db->select()
            ->from(self::getEntriesTableName(), 
            array(
                'id',
                'value',
                '_dictionaryId' => 'dictionaryId'
            ))
            ->order(array(
            'dictionaryId',
            'value'
        ));
        
        if ($dictIds)
            $select->where('dictionaryId IN (' . implode(',', $dictIds) . ')');
        
        $stmt = $this->_db->query($select);
        
        $res = array();
        
        /* @var $obj Vo_DictionaryEntry */
        while (($obj = $stmt->fetchObject('Vo_DictionaryEntry')) != false) {
            if (! isset($res[$obj->_dictionaryId]))
                $res[$obj->_dictionaryId] = array();
            
            if ($keyIsEntryId)
                $res[$obj->_dictionaryId][$obj->id] = $obj;
            else
                $res[$obj->_dictionaryId][] = $obj;
        }
        
        return $res ? $res : null;
    }

    /**
     * Checks whenevere enry for specified dictionary exists
     *
     * @param int $dictId            
     * @param int $entryId            
     * @return boolean
     */
    public function entryForDictExists($dictId, $entryId)
    {
        $select = $this->_db->select()
            ->from(self::getEntriesTableName())
            ->where('dictionaryId = ?', $dictId)
            ->where('id = ?', $entryId);
        
        $res = $this->_db->fetchOne($select);
        
        return $res ? true : false;
    }

    /**
     * Update entries
     *
     * @param Vo_Dictionary $dictVo            
     * @param unknown_type $entryData            
     */
    public function save(Vo_Dictionary $dictVo, $entryData)
    {
        if (! $dictVo->id)         // only push
        {
            $this->_db->insert(self::getTableName(), array(
                'name' => $dictVo->name
            ));
            $dictId = $this->_db->lastInsertId();
        } else         // update name if was changed
        {
            $this->_db->update(self::getTableName(), array(
                'name' => $dictVo->name
            ), 'id = ' . $this->_db->quote($dictVo->id, Zend_Db::INT_TYPE));
            $dictId = $dictVo->id;
        }
        
        // update
        if (isset($entryData['remove'])) {
            $mtm = new Model_MarkerTypeMapper();
            foreach ($entryData['remove'] as $obj) {
                // nulify all marker values that uses that entry
                $mtm->nullifyDictinaryUsagers($dictId, $obj['id']);
                $this->_db->delete(self::getEntriesTableName(), 
                    'id = ' . $this->_db->quote($obj['id'], Zend_Db::INT_TYPE));
            }
        }
        
        if (isset($entryData['add']))         // adding
        {
            foreach ($entryData['add'] as $value) {
                $this->_db->insert(self::getEntriesTableName(), 
                    array(
                        'dictionaryId' => $dictId,
                        'value' => $value['value']
                    ));
            }
        }
        
        if (isset($entryData['edit'])) {
            foreach ($entryData['edit'] as $value) {
                $this->_db->update(self::getEntriesTableName(), array(
                    'value' => $value['value']
                ), 'id = ' . $this->_db->quote($value['id'], Zend_Db::INT_TYPE));
            }
        }
    }

    /**
     * Deletes dictionary with all entries
     *
     * @param int $id            
     */
    public function delete($id)
    {
        // nullify all markers that type params uses that dictionary
        $mtm = new Model_MarkerTypeMapper();
        $mm = new Model_MarkerMapper();
        $mtm->nullifyDictinaryUsagers($id);
        
        // delete dictionary
        $this->_db->delete(self::getTableName(), 'id = ' . $this->_db->quote($id, Zend_Db::INT_TYPE));
        
        // disable all params using that dictionary
        $mo = new Model_MarkerTypeMapper();
        $mo->disableDictionaryUsagers($id);
    }

}