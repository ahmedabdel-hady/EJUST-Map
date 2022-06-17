<?php

/**
 * Front controller
 * 
 * @author flexphperia
 *
 */
class Controller_Front extends Flexphperia_ControllerBase
{

    /**
     * Configuration data
     *
     * @var Config_SuperMap
     */
    protected $_config;

    function __construct(Zend_Controller_Request_Http $request)
    {
        parent::__construct($request);
        
        $registry = Zend_Registry::getInstance();
        $this->_config = $registry->bootstrap->config;
        
        // do not cache searches
        $this->_cache->setOption('non_cached_methods', array(
            'searchMarkersAction'
        ));
    }

    public function caller($actionName)
    {
        $methodName = parent::caller($actionName);
        
        // get from cache if exists, pass post as argument to make proper id in
        // cache
        $this->_cache->$methodName($this->_request->getPost());
    }

    /**
     * Returns tree with only enabled maps and some other details needed for
     * start
     *
     * @return boolean
     */
    public function indexAction()
    {
        $data = new stdClass();
        $sm = new Model_SettingsMapper();
        $mm = new Model_MapMapper();
        $mtm = new Model_MarkerTypeMapper();
        $data->settings = $sm->fetch();
        $data->tree = $mm->fetchTree(true, false, true);
        $data->markerTypes = $mtm->fetchAll(true);
        $data->defaultMapId = $data->tree ? $data->tree[0]->metadata->id : null;
        $this->response(1, $data, false);
        
        return true; // needed for caching
    }

    /**
     * returns complete map with markers, labels, etc.
     *
     * @return boolean
     */
    public function getMapViewAction()
    {
        $mapId = $this->_request->getPost('id');
        
        if (! $mapId)
            return $this->response(3);
        
        $mapper = new Model_ViewDataMapper();
        $data = $mapper->fetchMap($mapId, false, true);
        
        $this->response($data ? 1 : 4, $data, false);
        
        return true; // needed for caching
    }

    /**
     * Returns list of marker types.
     * Used to create appropriate search fields
     *
     * @return boolean
     */
    public function getSearchTypeAction()
    {
        $typeId = $this->_request->getPost('id');
        
        if (! $typeId)
            return $this->response(3);
        
        $mtm = new Model_MarkerTypeMapper();
        
        $data = new stdClass();
        $data->markerType = $mtm->fetchOne($typeId, true, true);
        
        if (! $data->markerType)
            return $this->response(4);
        
        if ($data->markerType->params) {
            $neededDicts = array();
            /* @var $paramVo Vo_MarkerTypeParam */
            foreach ($data->markerType->params as $paramVo) {
                if ($paramVo->type == 'dictionary')
                    $neededDicts[$paramVo->typeValue] = true;
            }
            $neededDicts = array_keys($neededDicts);
            $dm = new Model_DictionaryMapper();
            $data->dictEntries = $dm->fetchEntries($neededDicts, false);
        }
        
        $this->response(1, $data, false);
        
        return true; // needed for caching
    }

    /**
     * Serach for markers
     *
     * @return boolean
     */
    public function searchMarkersAction()
    {
        $data = $this->_request->getPost();
        if (! $data)
            return $this->response(3);
        
        $vo = new Vo_Search($data);
        if (! Validator_VoValidator::validateSearch($vo))
            return $this->response(3);
        
        $m = new Model_SearchMapper();
        
        $data = $m->search($vo);
        
        $this->response(1, $data, false);
        
        return true; // needed for caching
    }

    /**
     * Get marker details for popover in search tab
     *
     * @return boolean
     */
    public function getMarkerInfoAction()
    {
        $id = $this->_request->getPost('id');
        
        if (!$id)
            return $this->response(3);
        
        $mapper = new Model_ViewDataMapper();
        $res = $mapper->fetchMarkerInfo($id);
        
       
        $this->response($res ? 1 : 4, $res, false);
        
        return true; // needed for caching
    }
}