<?php

/**
 * @author flexphperia
 *
 */
class Model_SearchMapper extends Model_ViewMapperBase
{

    /**
     * Searches for markers
     *
     * @param Vo_Search $vo            
     * @return null array
     */
    public function search(Vo_Search $vo)
    {
        if ($vo->markerTypeId) {
            $mtm = new Model_MarkerTypeMapper();
            $searchMarkerType = $mtm->fetchOne($vo->markerTypeId, true); // get
                                                                         // all
                                                                         // enabled
                                                                         // not
                                                                         // sdearch
                                                                         // only,
                                                                         // needed
                                                                         // when
                                                                         // rendering
                                                                         // result
        }
        
        // 1. get markers
        $select = $this->_db->select()
            ->from(Model_MarkerMapper::getTableName(), Model_MarkerMapper::$columns)
            ->where('enabled = 1');
        
        if ($vo->mapId)
            $select->where('mapId = ?', $vo->mapId);
        
        if ($vo->title) {
            $select->where('title LIKE ?', '%' . $vo->title . '%');
            // if there is a space flip by it
            $spaceIndex = strpos($vo->title, ' ');
            if ($spaceIndex !== false && $spaceIndex > 0) {
                $flipped = substr($vo->title, $spaceIndex + 1) . ' ' . substr($vo->title, 0, $spaceIndex);
                $select->orWhere('title LIKE ?', '%' . $flipped . '%');
            }
        }
        
        if ($vo->markerTypeId)
            $select->where('markerTypeId = ?', $vo->markerTypeId);
            
            // if searching for specified marker type, construct where
        if ($vo->markerTypeId && $searchMarkerType->params) {
            /* @var $paramVo Vo_MarkerTypeParam */
            foreach ($searchMarkerType->params as $paramVo) {
                $varName = 'param' . $paramVo->number . 'Value';
                
                if (empty($vo->$varName))
                    continue;
                
                if ($paramVo->type == 'dictionary')
                    $select->where($varName . ' = ?', $vo->$varName);
                else
                    $select->where($varName . ' LIKE ?', '%' . $vo->$varName . '%');
            }
        }
        
        $stmt = $this->_db->query($select);
        
        $markers = array();
        $typesNeeded = array(); // needed id types
        $regionsNeeded = array(); // needed id of regions
        
        $mm = new Model_MarkerMapper();
        while (($obj = $stmt->fetchObject('Vo_Marker')) != false) {
            $markers[] = $obj;
            
            $mm->fillMarkerDetails($obj, true);
            
            if ($obj->_regionId) // find region object
                $regionsNeeded[$obj->_regionId] = true;
            
            $typesNeeded[$obj->markerTypeId] = true;
        }
        
        $typesNeeded = array_keys($typesNeeded);
        $regionsNeeded = array_keys($regionsNeeded);
        
        // no markers
        if (! $markers)
            return array(
                'count' => 0
            );
        
        $this->_findTypesAndDictionaries($typesNeeded);
        
        if ($regionsNeeded) {
            $rm = new Model_RegionMapper();
            $regions = $rm->fetch($regionsNeeded);
        }
        
        // marker loop to assign types to markers
        /* @var $markerVo Vo_Marker */
        foreach ($markers as $markerVo) {
            if (! $vo->markerTypeId)
                $markerVo->_type = $this->_usedTypes[$markerVo->markerTypeId];
            else
                $markerVo->_type = $searchMarkerType;
            
            if ($markerVo->_regionId) // find region object
                $markerVo->region = $regions[$markerVo->_regionId];
        }
        
        $this->_view->partialLoop()->setObjectKey('vo');
        $res = $this->_view->partialLoop('searchEntry.phtml', $markers);
        $resCount = count($markers);
        if ($resCount > 2)
            $res .= '<div style="height: 100px;"></div>'; // add spacer after
                                                          // results if there
                                                          // are many resulsts
        
        $minifier = new Filter_Minify();
        
        return array(
            'count' => $resCount,
            'result' => $minifier->filter($res)
        );
    }
}