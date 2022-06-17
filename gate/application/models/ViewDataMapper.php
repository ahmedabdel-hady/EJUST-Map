<?php

/**
 * @author flexphperia
 *
 */
class Model_ViewDataMapper extends Model_ViewMapperBase
{

    /**
     * returns complete mm ap view data, markers, labels, regions etc.
     *
     * @param int $mapId            
     * @param bool $ignoreDisabledState
     *            - include disabled maps
     * @param bool $linkableBreadcrumb
     *            - is generated breadcrumb should by linkeable
     * @return NULL stdClass
     */
    public function fetchMap($mapId, $ignoreDisabledState = false, $linkableBreadcrumb = false)
    {
        $mm = new Model_MapMapper();
        $data = new stdClass();
        $data->map = $mm->fetchNode($mapId, true);
        if (! $data->map || ! $data->map->mapImage || (! $ignoreDisabledState && ! $data->map->enabled))
            return null; // map not have image
        
        $rm = new Model_RegionMapper();
        $data->regions = $rm->fetchForMapFull($data->map->id);
        
        $data->labels = $this->_generateLabels($data->map->id);
        $data->markers = $this->_generateMarkers($data->map->id);
        
        // $minifier = new Filter_Minify();
        
        $data->viewHtml = new stdClass();
        $data->viewHtml->breadcrumb = $this->_view->mapBreadcrumb($mm->getMap($mapId), false, $linkableBreadcrumb);
        $data->viewHtml->legend = $data->markers && $data->map->showLegend ? $this->_generateLegend() : null;
        // $data->viewHtml->elements = $minifier->filter($markersHtml);
        
        return $data;
    }

    /**
     * Get marker info
     *
     * @param int $id            
     * @return mixed
     */
    public function fetchMarkerInfo($id)
    {
        $minifier = new Filter_Minify();
        $marker = $this->_generateMarkers(false, $id);
        
        if (!$marker)
            return false;
            
        return $minifier->filter($marker[0]['html']);
    }

    /**
     * Generates html for legend
     */
    protected function _generateLegend()
    {
        $this->_view->partialLoop()->setObjectKey('vo');
        return $this->_view->partialLoop('legendEntry.phtml', $this->_usedTypes);
    }

    /**
     * Generates all markers for specified map in html
     *
     * @param int $mapId            
     * @param int $markerId
     *            - limit to one marker
     */
    protected function _generateMarkers($mapId = false, $markerId = false)
    {
        // 1. get markers
        $select = $this->_db->select()
            ->from(Model_MarkerMapper::getTableName(), Model_MarkerMapper::$columns)
            ->where('enabled = 1');
        
        if ($mapId)
            $select->where('mapId = ?', $mapId);
        
        if ($markerId)
            $select->where('id = ?', $markerId);
        
        $stmt = $this->_db->query($select);
        
        $markers = array();
        $typesNeeded = array(); // needed id types
        
        $mm = new Model_MarkerMapper();
        while (($obj = $stmt->fetchObject('Vo_Marker')) != false) {
            // collect marker types
            $markers[] = $obj;
            
            $mm->fillMarkerDetails($obj, false);
            
            $typesNeeded[$obj->markerTypeId] = true;
        }
        
        $typesNeeded = array_keys($typesNeeded);
        
        // no markers
        if (! $markers)
            return;
        
        $this->_findTypesAndDictionaries($typesNeeded);
        
        // marker loop to assign type to marker
        /* @var $markerVo Vo_Marker */
        foreach ($markers as $markerVo) {
            $markerVo->_type = $this->_usedTypes[$markerVo->markerTypeId];
        }
        
        $res = array();
        $minifier = new Filter_Minify();
        
        
        //prepare markers
        foreach ($markers as &$marker){
            $data = array(
                'id' => $marker->id, 
                'x' => $marker->x, 
                'y' => $marker->y, 
                'typeCssName'  => $marker->_type->cssName,
                'html' => $minifier->filter( $this->_view->partial('marker.phtml', array('vo' => $marker)) )
            );
        
            array_push($res, $data);    
        }
            
        return $res;
        
        // $this->_view->partialLoop()->setObjectKey('vo');
        // return $this->_view->partialLoop('marker.phtml', $markers);
    }

    /**
     * Generate labels in html
     *
     * @param int $mapId            
     */
    protected function _generateLabels($mapId)
    {
        $lm = new Model_LabelMapper();
        $labels = $lm->fetchForMapViewAll($mapId);
        
        if (!$labels)
            return;
            
        $res = array();
        $minifier = new Filter_Minify();
        
        foreach ($labels as &$label){
            $data = array(
                'id' => $label->id, 
                'x' => $label->x, 
                'y' => $label->y, 
                'clickable' => $label->_linkRegionId || $label->_linkMapId,
                'html' => $minifier->filter( $this->_view->partial('label.phtml', array('vo' => $label)) )
            );
        
            array_push($res, $data);    
        }
            
        return $res;
        //generate labels
        // $this->_view->partialLoop()->setObjectKey('vo');
        // return $this->_view->partialLoop('label.phtml', $labels);    

        // $this->_view->partialLoop()->setObjectKey('vo');
        // return $this->_view->partialLoop('label.phtml', $labels);
    }
}