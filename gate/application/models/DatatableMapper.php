<?php

/**
 * Class responsible for returning proper response for jquery datatables
 * 
 * @author flexphperia
 *
 */
class Model_DatatableMapper extends Flexphperia_MapperBase
{

    protected $_columns;

    /**
     *
     * @var Zend_Db_Select
     */
    protected $_select;

    /**
     * post data
     * 
     * @var array
     */
    protected $_data;

    protected $_isFiltered = false;

    /**
     * Function used by datatables call - marker list
     *
     * @param array $data            
     * @return datatable specific response
     */
    public function fetchMarkers($data)
    {
        $this->_data = $data;
		// var_dump($this->_data);
		// die;
        
        $this->_columns = array(
            'id',
            'title',
            'markerTypeId',
            'mapId',
            'enabled'
        );
        
        $this->_select = $this->_db->select()->from(Model_MarkerMapper::getTableName(), 
            array_merge(array(
                new Zend_Db_Expr('SQL_CALC_FOUND_ROWS ' . Model_MarkerMapper::getTableName() . '.id')
            ), $this->_columns));
        
        $this->_select->joinLeft(Model_MarkerTypeMapper::getTableName(), 
            $this->_db->quoteIdentifier(Model_MarkerMapper::getTableName()) . '.markerTypeId = ' .
                 $this->_db->quoteIdentifier(Model_MarkerTypeMapper::getTableName()) . '.id', 
                array(
                    'markerTypeName' => 'name'
                ));
        
        $this->_pagination();
        $this->_ordering();
        $this->_filtering();
        
        $allCount = $this->_countAll(Model_MarkerMapper::getTableName());
        
        $iFilteredTotal = 0;
        if ($allCount) {
            $stmt = $this->_db->query($this->_select);
            $iFilteredTotal = $this->_db->fetchOne('SELECT FOUND_ROWS()');
        }
        
        $result = array();
        if ($allCount && $iFilteredTotal)         // if there are any result
        {
            $mm = new Model_MapMapper();
            $rawTree = $mm->fetchTree(false, true); // get tree nodes
            
            while (($row = $stmt->fetch()) != false) {
                $mapBreadcrumb = $this->_view->mapBreadcrumb($rawTree[$row['mapId']], true);
                $row['markerTypeName'] = $this->_view->escape($row['markerTypeName']); // escape
                                                                                        // type
                $result[] = array(
                    $row['id'],
                    $this->_view->escape($row['title']),
                    $row['markerTypeName'],
                    $mapBreadcrumb,
                    $row['enabled']
                );
            }
        }
        
        return $this->_response($result, $allCount, $iFilteredTotal);
    }

    /**
     * Function used by datatables call
     *
     * @param array $data            
     * @return datatable specific response
     */
    public function fetchLabels($data)
    {
        $this->_data = $data;
        
        $this->_columns = array(
            'id',
            'typeValue',
            'type',
            'mapId',
            'enabled'
        );
        
        $this->_select = $this->_db->select()->from(Model_LabelMapper::getTableName(), 
            array_merge(array(
                new Zend_Db_Expr('SQL_CALC_FOUND_ROWS ' . Model_LabelMapper::getTableName() . '.id')
            ), $this->_columns));
        
        $this->_pagination();
        $this->_ordering();
        $this->_filtering();
        
        $allCount = $this->_countAll(Model_LabelMapper::getTableName());
        
        $iFilteredTotal = 0;
        if ($allCount) {
            $stmt = $this->_db->query($this->_select);
            $iFilteredTotal = $this->_db->fetchOne('SELECT FOUND_ROWS()');
        }
        
        $result = array();
        if ($allCount && $iFilteredTotal)         // if there are any result
        {
            $mm = new Model_MapMapper();
            $fo = new Model_FileOperator();
            $rawTree = $mm->fetchTree(false, true); // get tree nodes
            
            while (($row = $stmt->fetch()) != false) {
                $mapBreadcrumb = $this->_view->mapBreadcrumb($rawTree[$row['mapId']], true);
                
                if ($row['type'] == 'icon') {
                    $io = $fo->getIconObject($row['typeValue']);
                    $row['typeValue'] = '<img src="' . $io->url . '" />'; // skip
                                                                      // helper
                } else {
                    // escape text
                    $row['typeValue'] = $this->_view->escape($row['typeValue']);
                }
                
                $result[] = array(
                    $row['id'],
                    $row['typeValue'],
                    $row['type'],
                    $mapBreadcrumb,
                    $row['enabled']
                );
            }
        }
        return $this->_response($result, $allCount, $iFilteredTotal);
    }

    /**
     * Prepares response
     *
     * @param
     *            $result
     * @param int $allCount            
     * @param bool $iFilteredTotal            
     * @return array
     */
    protected function _response($result, $allCount, $iFilteredTotal)
    {
        return array(
            "data" => $result,
            // "sEcho" => intval($this->_data['sEcho']),
            "recordsTotal" => $allCount,
            "recordsFiltered" => $this->_isFiltered ? $iFilteredTotal : $allCount
        );
    }

    protected function _countAll($tableName)
    {
        return $this->_db->fetchOne(
            $this->_db->select()
                ->from($tableName, array(
                new Zend_Db_Expr('COUNT(' . $this->_columns[0] . ')')
            )));
    }

    protected function _pagination()
    {
        if (isset($this->_data['start']) && $this->_data['length'] != '-1')
            $this->_select->limit($this->_data['length'], $this->_data['start']);
    }

    /**
     * Find order
     * 
     * @param Zend_Db_Select $select            
     * @param array $data            
     */
    protected function _ordering()
    {
        /*
         * Ordering
         */
        if (isset($this->_data['order'])) {
            // sorting only on one column
            // if ($this->_data['bSortable_' . intval($this->_data['iSortCol_0'])] == "true") {
                $this->_select->order($this->_columns[$this->_data['order'][0]['column']] . ' ' . $this->_data['order'][0]['dir']);
            // }
        }
    }

    protected function _filtering()
    {
        /*
         * Filtering
         */
    	/* Individual column filtering */
    	for ($i = 1; $i < count($this->_columns); $i ++) {
            if ($this->_data['columns'][$i]['searchable'] == "true" && $this->_data['columns'][$i]['search']['value'] != '') {
                $this->_select->where($this->_columns[$i] . ' LIKE ?', '%' . $this->_data['columns'][$i]['search']['value'] . '%');
                $this->_isFiltered = true;
            }
        }
    }
}