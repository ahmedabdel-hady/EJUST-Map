<?php

/**
 * @author flexphperia
 *
 */
class Model_MapMapper extends Flexphperia_MapperBase
{

    public static function getTableName()
    {
        return 'sm_maps';
    }

    protected $_columns = array(
        'id',
        'name',
        'enabled',
        'showLegend',
        'zoom',
        '_parentId' => 'parentId',
        '_position' => 'position'
    );

    /**
     * Holds raw tree data, used to cache retrieving raw data tree
     *
     * @var array
     */
    protected static $_rawTree;

    /**
     * Get one node
     *
     * @param int $id            
     * @param bool $widthMapInfo
     *            - inlcude map info or not
     * @return Vo_TreeNode
     */
    public function fetchNode($id, $widthMapInfo = false)
    {
        $select = $this->_db->select()
            ->from(self::getTableName(), $this->_columns)
            ->where('id = ?', (int) $id); // automatically quoted;
        
        $stmt = $this->_db->query($select);
        
        $obj = $stmt->fetchObject('Vo_TreeNode');
        
        if ($obj && $widthMapInfo) {
            $fo = new Model_FileOperator();
            $obj->mapImage = $fo->getMapSize($obj->id);
        }
        
        return $obj;
    }

    /**
     * Fetches all nodes into array indexed by node id
     * 
     * @return array
     */
    public function fetchNodes()
    {
        $select = $this->_db->select()
            ->from(self::getTableName(), $this->_columns)
            ->order(array(
            'parentId',
            'position'
        ));
        
        $stmt = $this->_db->query($select);
        $result = array();
        
        while (($obj = $stmt->fetchObject('Vo_TreeNode')) != false) {
            $result[$obj->id] = $obj;
        }
        
        return $result;
    }

    /**
     * Returns map object used to describe map for html form fields (with
     * parents in array sorted)
     *
     * @return Vo_TreeNode
     */
    public function getMap($id)
    {
        if (! $id)
            return;
        
        $rawTree = $this->fetchTree(false, true);
        
        $obj = $rawTree[$id];
        $obj->toMapObj(); // modify object
        
        return $obj;
    }

    /**
     * Get tree of maps
     *
     * @param bool $toJSTree
     *            - unset some vars and set some for jstree
     * @param bool $raw
     *            returns raw array, key is map id
     * @param bool $skipDisabled
     *            ommmit maps without map or disabled
     * @return array
     */
    public function fetchTree($toJSTree = true, $raw = false, $skipDisabled = false)
    {
        if ($raw && self::$_rawTree) // if previously called
            return self::$_rawTree;
        
        $res = array();
        $nodes = $this->fetchNodes();
        if (! $nodes)
            return $res;
        
        $fo = new Model_FileOperator();
        
        /* @var $node Vo_TreeNode */
        foreach ($nodes as $key => $node) {
            $node->_noMap = $fo->getMapSize($node->id) ? false : true; // fill
                                                                       // that
                                                                       // is map
                                                                       // created
            
            if ($skipDisabled && ! $node->enabled || $skipDisabled && $node->_noMap)
                continue;
            
            if ($node->_parentId != null) {
                $node->_parent = $nodes[$node->_parentId];
                $parent = $nodes[$node->_parentId];
                array_push($parent->_children, $node);
            } else {
                array_push($res, $node);
            }
        }
        
        if ($raw) {
            self::$_rawTree = $nodes;
            return self::$_rawTree;
        }
        
        if ($toJSTree)
            $this->_toJSTree($res);
        
        return $res;
    }

    /**
     * Delete node and all chilren nodes
     * 
     * @param int $id            
     */
    public function deleteNode($id)
    {
        $node = $this->fetchNode($id);
        
        if (! $node)
            return;
        
        $tree = $this->fetchTree(false);
        $ids = $this->_findChildIds($tree, $node->id);
        
        // delete all children files
        if ($ids)         // if has children
        {
            $fo = new Model_FileOperator();
            foreach ($ids as $id) {
                $fo->deleteMap($id);
            }
        }
        
        $this->_db->delete(self::getTableName(), 'id = ' . $this->_db->quote($id, Zend_Db::INT_TYPE));
        $this->_updateOrder($node->_parentId, $node->_position, true);
    
    }

    /**
     * Moves node to another parent or change only position
     *
     * @param int $id            
     * @param id|null $newParentId            
     * @param int $position            
     */
    public function moveNode($id, $newParentId, $position)
    {
        $node = $this->fetchNode($id);
        
        if (! $node)
            return;
        
        if ($newParentId == $node->_parentId && $position == $node->_position)
            return; // no move
        
        $this->_updateOrder($node->_parentId, $node->_position, true); // remove
                                                                       // gap
        $this->_updateOrder($newParentId, $position, false); // prepare gap for
                                                             // new node
        
        $data = array(
            'parentId' => $newParentId,
            'position' => (int) $position
        );
        
        $this->_db->update(self::getTableName(), $data, 'id = ' . $this->_db->quote($id, Zend_Db::INT_TYPE));
    
    }

    /**
     * Updates details about node
     */
    public function saveMap(Vo_TreeNode $vo)
    {
        /* @var $obj Vo_TreeNode */
        $data = array(
            'name' => $vo->name,
            'enabled' => $vo->enabled,
            'showLegend' => $vo->showLegend,
            'zoom' => $vo->zoom
        );
        
        if ($vo->id)
            $this->_db->update(self::getTableName(), $data, 'id = ' . $this->_db->quote($vo->id, Zend_Db::INT_TYPE));
        else {
            $data['position'] = $this->findNextChildPosition(null);
            
            $this->_db->insert(self::getTableName(), $data);
        }
    }

    /**
     * Find next childs position for specified map
     *
     * @param int $mapId            
     * @return number
     */
    public function findNextChildPosition($mapId)
    {
        $select = $this->_db->select()->from(self::getTableName(), 
            array(
                'res' => new Zend_Db_Expr('COUNT(id)')
            ));
        
        if (! $mapId)
            $select->where('parentId IS NULL');
        else
            $select->where('parentId = ?', $mapId);
        
        return (int) $this->_db->fetchOne($select);
    }

    /**
     * Updates sibling nodes position after removing or adding one sibling
     *
     * @param int $parentId            
     * @param int $nodePos            
     */
    private function _updateOrder($parentId, $nodePos, $up)
    {
        $data = array(
            'position' => new Zend_Db_Expr($up ? 'position - 1' : 'position + 1'));
            // up or down
        
        $condition = array(
            'parentId ' .
                 ($parentId == null ? new Zend_Db_Expr('IS NULL') : '=' .
                 $this->_db->quote($parentId, Zend_Db::INT_TYPE)),
                'position >' . (! $up ? '=' : '') . $this->_db->quote($nodePos, Zend_Db::INT_TYPE)
        );
    
    // update all siblings postions
    $this->_db->update(self::getTableName(), $data, $condition);
}

/**
 * Find all children and descendants ids from tree for specified node.
 * Recursive function
 *
 * @param array $tree            
 * @param int $nodeId            
 * @param array $res            
 * @return array
 */
private function _findChildIds($tree, $nodeId = null, &$res = array())
{
    foreach ($tree as $key => $node) {
        if ($nodeId == null || $nodeId == $node->id) {
            array_unshift($res, $node->id);
            
            if (isset($node->_children) && count($node->_children) > 0)
                $this->_findChildIds($node->_children, null, $res);
        }
    }
    
    return $res;
}

/**
 * Converts all objects to jstree nodes
 * 
 * @param array $res            
 */
private function _toJSTree($res)
{
    foreach ($res as $key => $node) {
        $node->toJStree();
        if (isset($node->_children) && count($node->_children) > 0) // nested
            $this->_toJSTree($node->_children);
    }
}
}