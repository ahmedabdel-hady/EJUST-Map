<?php

/**
 * Admin controller
 * 
 * 
 * @author flexphperia
 *
 */
class Controller_Admin extends Flexphperia_ControllerBase
{

    /**
     * Which methods causes to clean front controller cache
     *
     * @var array
     */
    protected $_cleanCacheActions = array(
        'deleteDictionary',
        'deleteLabel',
        'deleteMap',
        'deleteMarker',
        'deleteMarkerType',
        'deleteRegion',
        'saveDictionary',
        'saveElementsPositions',
        'saveLabel',
        'saveMap',
        'saveMarker',
        'saveMarkerType',
        'saveRegion',
        'saveRegionPosition',
        'saveSettings',
        'saveTreeOrder',
        'uploadMap'
    );

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
        
        // create aut adapter
        new Flexphperia_AuthAdapterDb(null, Model_SettingsMapper::getTableName(), 'password', 'salt');
    }

    public function caller($actionName)
    {
        $methodName = parent::caller($actionName);
        
        if (in_array($actionName, $this->_cleanCacheActions))
            $this->_cache->clean();
            
            // check authentication
        if ($actionName != 'login' && $actionName != 'logout') {
            $identity = Zend_Auth::getInstance()->getIdentity();
            
            if ( empty( $identity ) )
                return $this->response( 5 );
        }
            
        // call method
        $this->$methodName();
    }

    /**
     * Returns maps tree with all maps (enabled, disabled and without map image)
     */
    public function indexAction()
    {
        $tm = new Model_MapMapper();
        $this->response(1, $tm->fetchTree(), false); // do not escape tree
    }

    /**
     * Logs user
     *
     * Code 2 in response = wrong password
     */
    public function loginAction()
    {
        $pass = $this->_request->getPost('pass');
        
        if (! $pass || ! Validator_VoValidator::validatePass($pass))
            return $this->response(2);
        
        $identity = Zend_Auth::getInstance()->getIdentity();
        
        if (! empty($identity))
            Zend_Auth::getInstance()->clearIdentity();
        
        $adapter = Flexphperia_AuthAdapterDb::getInstance();
        $adapter->setPassword($pass);
        $result = Zend_Auth::getInstance()->authenticate($adapter);
        
        if ($result->getCode() == Zend_Auth_Result::SUCCESS)
            return $this->response(1);
        else
            return $this->response(2);
    }

    /**
     * Logout
     */
    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        return $this->response(1);
    }

    /**
     * Changes user password
     *
     * Code 2 in response = wrong old password
     */
    public function changePasswordAction()
    {
        $passVo = new Vo_ChangePassword($this->_request->getPost());
        
        // validate that passwords are correct texts (match, regexp)
        if (! Validator_VoValidator::validateChangePasswordVo($passVo))
            return $this->response(3);
        
        $authAdapter = Flexphperia_AuthAdapterDb::getInstance();
        
        // check passed password, we're using here auth adapter method
        $adapterResult = $authAdapter->preAuthentciate($passVo->oldPassword);
        
        if ($adapterResult === false)
            return $this->response(2);
            
            // generate new salt and new password hash, store to db
        $newSalt = $authAdapter->generateSalt();
        $newPasswordHash = $authAdapter->generateHash($passVo->newPassword1, $newSalt);
        $sm = new Model_SettingsMapper();
        $sm->updatePassword($newPasswordHash, $newSalt);
        return $this->response(1);
    }

    /**
     * Clear front controller cache
     */
    public function clearCacheAction()
    {
        $this->_cache->clean();
        return $this->response(1);
    }

    /**
     * Returns map with markers, labels, etc.
     * It can return disabled map
     */
    public function getMapViewAction()
    {
        $mapId = $this->_request->getPost('id');
        
        if (! $mapId)
            return $this->response(3);
        
        $mapper = new Model_ViewDataMapper();
        $data = $mapper->fetchMap($mapId, true);
        
        return $this->response($data ? 1 : 4, $data, false);
    }

    /**
     * Saves elements positions.
     */
    public function saveElementsPositionsAction()
    {
        $markers = $this->_request->getPost('markers');
        $labels = $this->_request->getPost('labels');
        
        if (! $labels && ! $markers)
            return $this->response(3);
        
        if (! Validator_VoValidator::validateElementsPositions($labels, $markers))
            return $this->response(3);
        
        if ($markers) {
            $mm = new Model_MarkerMapper();
            $mm->savePositions($markers);
        }
        
        if ($labels) {
            $lm = new Model_LabelMapper();
            $lm->savePositions($labels);
        }
        
        $this->response(1);
    }

    /**
     * Saves reordered tree nodes
     */
    public function saveTreeOrderAction()
    {
        $id = $this->_request->getPost('id');
        $parentId = $this->_request->getPost('parentId', null);
        $parentId = $parentId ? $parentId : null; // null is root
        $position = $this->_request->getPost('position');
        
        if (! Validator_VoValidator::validateTreeMove($parentId, $position))
            return $this->response(3);
        
        $mm = new Model_MapMapper();
        $mm->moveNode($id, $parentId, $position);
        
        $this->response(1);
    }

    /**
     * Returns map details for edition
     */
    public function getMapAction()
    {
        $data = $this->_request->getPost('id', false);
        
        if (! $data)
            return $this->response(3);
        
        $mm = new Model_MapMapper();
        
        $node = $mm->fetchNode($data, true);
        
        $this->response($node ? 1 : 4, $node, false);
    }

    /**
     * Saves map details
     */
    public function saveMapAction()
    {
        $data = $this->_request->getPost();
        
        if (! $data)
            return $this->response(3);
        
        $nodeVo = new Vo_TreeNode($data);
        
        if (! Validator_VoValidator::validateMap($nodeVo))
            return $this->response(3);
        
        $mm = new Model_MapMapper();
        $mm->saveMap($nodeVo);
        
        $this->response(1);
    }

    /**
     * Deletes map
     */
    public function deleteMapAction()
    {
        $data = $this->_request->getPost('id', false);
        
        if (! $data)
            return $this->response(3);
        
        $mm = new Model_MapMapper();
        $mm->deleteNode($data);
        
        $this->response(1);
    }

    /**
     * Returns list of markers (for datatable)
     */
    public function getMarkersAction()
    {
        $data = $this->_request->getPost();
        if (! $data)
            return $this->response(3);
        
        $dm = new Model_DatatableMapper();
        
        $this->response(1, $dm->fetchMarkers($data), false); // data are
                                                                 // escaped
                                                                 // partially
    }

    /**
     * Returns marker details
     */
    public function getMarkerAction()
    {
        $id = $this->_request->getPost('id', false);
        if (! $id)
            return $this->response(3);
        
        $mm = new Model_MarkerMapper();
        $data = new stdClass();
        $data->marker = $mm->fetchOne($id);
        
        if (! $data->marker)
            return $this->response(4);
        
        $this->response(1, $data, false);
    }

    /**
     * Saves marker
     */
    public function saveMarkerAction()
    {
        $data = $this->_request->getPost();
        
        if (! $data)
            return $this->response(3);
        
        $vo = new Vo_Marker($data);
        
        if (! Validator_VoValidator::validateMarker($vo))
            return $this->response(3);
        
        $lm = new Model_MarkerMapper();
        
        $this->response(1, $lm->save($vo));
    }

    /**
     * Deletes marker
     */
    public function deleteMarkerAction()
    {
        $data = $this->_request->getPost('id', false);
        if (! $data)
            return $this->response(3);
        
        $mm = new Model_MarkerMapper();
        $this->response(1, $mm->delete($data), false);
    }

    /**
     * Retursn list of regions for specified map
     */
    public function getRegionsAction()
    {
        $mapId = $this->_request->getPost('id', false);
        if (! $mapId)
            return $this->response(3);
        
        $rm = new Model_RegionMapper();
        $this->response(1, $rm->fetchForMapSimple($mapId), true);
    }

    /**
     * Returns region details
     */
    public function getRegionAction()
    {
        $regionId = $this->_request->getPost('id', false);
        if (! $regionId)
            return $this->response(3);
        
        $rm = new Model_RegionMapper();
        $obj = $rm->fetchOne($regionId);
        
        $this->response($obj ? 1 : 4, $obj, false);
    }

    /**
     * Saves region
     */
    public function saveRegionAction()
    {
        $data = $this->_request->getPost();
        
        if (! $data)
            return $this->response(3);
        
        $vo = new Vo_Region($data);
        if (! Validator_VoValidator::validateRegion($vo))
            return $this->response(3);
        
        $mm = new Model_RegionMapper();
        $this->response(1, $mm->save($vo));
    }

    /**
     * Saves region position
     */
    public function saveRegionPositionAction()
    {
        $data = $this->_request->getPost();
        
        if (! $data)
            return $this->response(3);
        
        $vo = new Vo_Region($data);
        
        if (! Validator_VoValidator::validateRegionPosition($vo))
            return $this->response(3);
        
        $mm = new Model_RegionMapper();
        $mm->save($vo, true);
        $this->response(1);
    }

    /**
     * Deletes region
     */
    public function deleteRegionAction()
    {
        $data = $this->_request->getPost('id', false);
        if (! $data)
            return $this->response(3);
        
        $rm = new Model_RegionMapper();
        $this->response(1, $rm->delete($data), false);
    }

    /**
     * Returns labels list (for datatable)
     */
    public function getLabelsAction()
    {
        $data = $this->_request->getPost();
        if (! $data)
            return $this->response(3);
        
        $dm = new Model_DatatableMapper();
        
        $this->response(1, $dm->fetchLabels($data), false); // data are
                                                                // escaped
                                                                // partially
    }

    /**
     * Returns label details
     */
    public function getLabelAction()
    {
        $data = $this->_request->getPost('id', false);
        if (! $data)
            return $this->response(3);
        
        $lm = new Model_LabelMapper();
        $obj = $lm->fetchOne($data);
        
        $this->response($obj ? 1 : 4, $obj, false);
    }

    /**
     * Saves label
     */
    public function saveLabelAction()
    {
        $data = $this->_request->getPost();
        
        if (! $data)
            return $this->response(3);
        
        $vo = new Vo_Label($data);
        
        if (! Validator_VoValidator::validateLabel($vo))
            return $this->response(3);
        
        $lm = new Model_LabelMapper();
        $this->response(1, $lm->save($vo));
    }

    /**
     * Deletes label
     */
    public function deleteLabelAction()
    {
        $data = $this->_request->getPost('id', false);
        if (! $data)
            return $this->response(3);
        
        $mm = new Model_LabelMapper();
        $this->response(1, $mm->delete($data), false);
    }

    /**
     * Return marker types list
     */
    public function getMarkerTypesAction()
    {
        $mm = new Model_MarkerTypeMapper();
        $this->response(1, $mm->fetchAll(true), true);
    }

    /**
     * Returns marker type details
     */
    public function getMarkerTypeAction()
    {
        $id = $this->_request->getPost('id');
        $onlyEnabledParams = $this->_request->getPost('onlyEnabledParams');
        
        if (! $id)
            return $this->response(3);
        
        $mm = new Model_MarkerTypeMapper();
        $obj = $mm->fetchOne($id, $onlyEnabledParams);
        $this->response($obj ? 1 : 4, $obj, false); // do not escape, params
                                                        // table escaped on client
                                                        // side
    }

    /**
     * Saves marker type details
     */
    public function saveMarkerTypeAction()
    {
        $mtVo = new Vo_MarkerType($this->_request->getPost());
        unset($mtVo->changesData);
        
        $changesData = $this->_request->getPost('changesData', false);
        $editedParams = isset($changesData['edit']) ? $changesData['edit'] : false;
        $reordered = isset($changesData['reorder']) ? $changesData['reorder'] : false;
        
        if (! Validator_VoValidator::validateMarkerTypeVo($mtVo) ||
             ! Validator_VoValidator::validateMarkerTypeReorderData($reordered) ||
             ! Validator_VoValidator::validateMarkerTypeEditedData($editedParams))
                return $this->response(3);
        
        if (! Validator_VoValidator::markerTypeCssNameUnique($mtVo->cssName, $mtVo->id))
            return $this->response(2);
        
        $mm = new Model_MarkerTypeMapper();
        $mm->save($mtVo, $editedParams, $reordered);
        
        $fo = new Model_FileOperator();
        $fo->generateCss();
        
        $this->response(1);
    }

    /**
     * Deletes marker type
     */
    public function deleteMarkerTypeAction()
    {
        $data = $this->_request->getPost('id', false);
        if (! $data)
            return $this->response(3);
        
        $mm = new Model_MarkerTypeMapper();
        $mm->delete($data);
        
        $fo = new Model_FileOperator();
        $fo->generateCss();
        
        $this->response(1);
    }

    /**
     * Returns list of dictionaries
     */
    public function getDictionariesAction()
    {
        $mm = new Model_DictionaryMapper();
        $this->response(1, $mm->fetchAll(), true);
    }

    /**
     * Returns entries for specified dictionaries
     */
    public function getDictionariesEntriesAction()
    {
        $ids = $this->_request->getPost('ids', false);
        
        if (! $ids)
            return $this->response(3);
        
        $dm = new Model_DictionaryMapper();
        $res = $dm->fetchEntries($ids, false); // get with partition on
                                               // dictionaries
                                               
        // always return 1
        $this->response(1, $res, false); // special case do not escape
    }

    /**
     * Returns dictionary
     */
    public function getDictionaryAction()
    {
        $id = $this->_request->getPost('id', false);
        
        if (! $id)
            return $this->response(3);
        
        $dm = new Model_DictionaryMapper();
        $res = $dm->fetch($id);
        
        $this->response($res ? 1 : 4, $res, false); // special case do not
                                                        // escape
    }

    /**
     * Saves dictionary
     */
    public function saveDictionaryAction()
    {
        $dictVo = new Vo_Dictionary($this->_request->getPost());
        unset($dictVo->entriesData);
        
        $entriesData = $this->_request->getPost('entriesData', false);
        
        if (! Validator_VoValidator::validateDictionaryVo($dictVo) ||
             ! Validator_VoValidator::validateDictionaryEntriesData($entriesData))
                return $this->response(3);
            
            $mm = new Model_DictionaryMapper();
            $mm->save($dictVo, $entriesData);
            $this->response(1);
        }

        /**
         * Deletes dictionary
         */
        public function deleteDictionaryAction()
        {
            $data = $this->_request->getPost('id', false);
            if (! $data)
                return $this->response(3);
            
            $mm = new Model_DictionaryMapper();
            $this->response(1, $mm->delete($data), false);
        }

        /**
         * Returns settings
         */
        public function getSettingsAction()
        {
            $settings = new Model_SettingsMapper();
            $this->response(1, $settings->fetch(), false);
        }

        /**
         * Saves settings
         */
        public function saveSettingsAction()
        {
            $data = new Vo_Settings($this->_request->getPost());
            
            if (! Validator_VoValidator::validateSettingsVo($data))
                return $this->response(3);
            
            $settings = new Model_SettingsMapper();
            $settings->update($data);
            $this->response(1);
        }

        /**
         * Returns images
         */
        public function getImagesAction()
        {
            $fo = new Model_FileOperator();
            $this->response(1, $fo->getImages(), false);
        }

        /**
         * Returns icons
         */
        public function getIconsAction()
        {
            $fo = new Model_FileOperator();
            $this->response(1, $fo->getIcons(), false);
        }

        /**
         * Deletes icon
         */
        public function deleteIconAction()
        {
            $data = $this->_request->getPost('id', false);
            
            if (! $data)
                return $this->response(3);
            
            $fo = new Model_FileOperator();
            $fo->deleteIcon($data);
            $this->response(1);
        }

        /**
         * Deletes image
         */
        public function deleteImageAction()
        {
            $data = $this->_request->getPost('id', false);
            
            if (! $data)
                return $this->response(3);
            
            $fo = new Model_FileOperator();
            $fo->deleteImage($data);
            $this->response(1);
        }

        /**
         * Upload function for icons
         */
        public function uploadIconsAction()
        {
            $uo = new Model_UploadOperator();
            $fo = new Model_FileOperator();
            $res = $uo->handleUpload($fo->iconsPath);
            echo Zend_Json::encode($res); // special case
        }

        /**
         * Upload function for images
         */
        public function uploadImagesAction()
        {
            $uo = new Model_UploadOperator();
            $fo = new Model_FileOperator();
            $res = $uo->handleUpload($fo->imagesPath);
            echo Zend_Json::encode($res); // special case
        }

        /**
         * Upload map image function
         */
        public function uploadMapAction()
        {
            $data = (int) $this->_request->getParam('id', 0); // multipart
            
            if ($data < 1) {
                $res = array(
                    'error' => 'Wrong map id or increase post_max_size and upload_max_filesize.'
                );
            } else {
                $uo = new Model_UploadOperator();
                $fo = new Model_FileOperator();
                $fo->prepareMapFolder($data); // clear map folder or create it
                $res = $uo->handleUpload($fo->mapsPath . $data, true);
            }
            echo Zend_Json::encode($res);
        }
    }