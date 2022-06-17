<?php

/**
 * Class with static methods used to validate objects incoming from form app
 * It validates some basic things, like strings lenghts, etc. 
 * 
 * @author flexphperia
 *
 */
class Validator_VoValidator
{

    /**
     *
     * @var Zend_Validate_Between
     */
    protected static $_boolBitValidator;

    /**
     * Returns validator used to validate bit bool values (0,1)
     *
     * @return Zend_Validate_Between
     */
    protected static function boolBitValidator()
    {
        if (! self::$_boolBitValidator)
            self::$_boolBitValidator = new Zend_Validate_Between(array(
                'min' => 0,
                'max' => 1
            ));
        
        return self::$_boolBitValidator;
    }

    /**
     *
     * @var Zend_Validate_InArray
     */
    protected static $_zoomValidator;

    /**
     * Validdator used for validating zoom values
     *
     * @return Zend_Validate_InArray
     */
    protected static function zoomValidator()
    {
        if (! self::$_zoomValidator)
            self::$_zoomValidator = new Zend_Validate_InArray(array(
                'default',
                0,
                1,
                2,
                3,
                4,
                5,
                6
            ));
        
        return self::$_zoomValidator;
    }

    /**
     * Validate password (regex)
     *
     * @param string $pass            
     * @return boolean
     */
    public static function validatePass($pass)
    {
        // number and alphabet and minimum 6 chars
        $val = new Zend_Validate_Regex('/^(?=.*[0-9]+.*)(?=.*[a-zA-Z]+.*)[0-9a-zA-Z]{6,}$/');
        
        return $val->isValid($pass);
    }

    /**
     * Validates Vo_ChangePassword object (regex)
     *
     * @param Vo_ChangePassword $vo            
     * @return boolean
     */
    public static function validateChangePasswordVo(Vo_ChangePassword $vo)
    {
        if (! self::validatePass($vo->oldPassword) || ! self::validatePass($vo->newPassword1) ||
             ! self::validatePass($vo->newPassword2)) {
                return false;
            }
            
            if ($vo->newPassword1 !== $vo->newPassword2)
                return false;
            
            return true;
        }

        /**
         * Validates data passed to update elements positions
         *
         * @param array $labels            
         * @param array $markers            
         * @return boolean
         */
        public static function validateElementsPositions($labels, $markers)
        {
            if ($markers) {
                foreach ($markers as $markerPos) {
                    if (! isset($markerPos['x']) || ! isset($markerPos['y']))
                        return false;
                    if (! is_int((int) $markerPos['x']) || ! is_int((int) $markerPos['y']))
                        return false;
                }
            }
            if ($labels) {
                foreach ($labels as $labelPos) {
                    if (! isset($labelPos['x']) || ! isset($labelPos['y']))
                        return false;
                    if (! is_int((int) $labelPos['x']) || ! is_int((int) $labelPos['y']))
                        return false;
                }
            }
            return true;
        }

        /**
         * Validates new map parent position
         *
         * @param int $parentId            
         * @param int $position            
         * @return boolean
         */
        public static function validateTreeMove($parentId, $position)
        {
            if ($parentId)             // null = root position
            {
                $val = new Zend_Validate_Db_RecordExists(
                    array(
                        'table' => Model_MapMapper::getTableName(),
                        'field' => 'id'
                    ));
                
                if (! $val->isValid($parentId))
                    return false;
            }
            
            $position = (int) $position;
            
            $mm = new Model_MapMapper();
            if ($position > $mm->findNextChildPosition($parentId) || $position < 0)
                return false;
            
            return true;
        }

        /**
         * Validate region object
         *
         * @param Vo_Region $vo            
         * @return boolean
         */
        public static function validateRegion(Vo_Region $vo)
        {
            // mapId is not validated, it will be checked on db level (foreign
            // keys)
            $validators = array(
                'name' => new Zend_Validate_StringLength(2, 20, 'utf-8'),
                'zoom' => self::zoomValidator()
            );
            
            return self::_validate($validators, $vo);
        }

        /**
         * Validates label
         *
         * @param Vo_Label $vo            
         * @return boolean
         */
        public static function validateLabel(Vo_Label $vo)
        {
            // map will be validated on db level (foreign key check)
            $validators = array(
                'type' => new Zend_Validate_InArray(array(
                    'text',
                    'icon'
                )),
                'enabled' => self::boolBitValidator()
            );
            
            if (! self::_validate($validators, $vo))
                return false;
            
            if ($vo->type == 'text') {
                $val = new Zend_Validate_StringLength(2, 160, 'utf-8');
                if (! $val->isValid($vo->text))
                    return false;
            }
            
            if ($vo->linkRegion)             // check region
            {
                $rm = new Model_RegionMapper();
                if (! $rm->isRegionForMapExists($vo->linkMap, $vo->linkRegion))
                    return false;
            }
            
            return true;
        
        }

        /**
         * Validate marker
         *
         * @param Vo_Marker $vo            
         * @return boolean
         */
        public static function validateMarker(Vo_Marker $vo)
        {
            // markerTypeId and mapId is not validated, db foreign keys will do
            // that
            $validators = array(
                'title' => new Zend_Validate_StringLength(2, 60, 'utf-8'),
                'enabled' => self::boolBitValidator()
            );
            
            if (!self::_validate($validators, $vo))
                return false;
            
            if ($vo->region) {
                $rm = new Model_RegionMapper();
                if (! $rm->isRegionForMapExists($vo->map, $vo->region))
                    return false;
            }
            
            $mtm = new Model_MarkerTypeMapper();
            $typeParams = $mtm->fetchParams($vo->markerTypeId);
            
			 // no params defined for this type, all values must be empty
			if (!$typeParams){
				if (!$vo->param1Value && !$vo->param2Value && !$vo->param3Value && !$vo->param4Value &&
					 !$vo->param5Value)
						return true;
					else
						return false;
			}
			
			$textVal = new Zend_Validate_StringLength(2, 80, 'utf-8');
			$longTextVal = new Zend_Validate_StringLength(5, null, 'utf-8');
			$dm = new Model_DictionaryMapper();
			
			/* @var $paramVo Vo_MarkerTypeParam */
			foreach ($typeParams as $paramVo) {
				$varName = 'param' . $paramVo->number . 'Value';
				
				if ($vo->$varName == '' && $paramVo->type != 'link') //if value is empty and its not link if its link require empty separators
					continue;
				
				if (!$paramVo->enabled) // parameter is disabled so param
					return false;		// value should be empty
					
				
				if ($paramVo->type == 'dictionary') {
					if(!$dm->entryForDictExists($paramVo->typeValue, $vo->$varName))
						return false;
				} 
				elseif ($paramVo->type == 'text'){
					if (!$textVal->isValid($vo->$varName))
						return false;
				}
				elseif($paramVo->type == 'longText') {
					if (!$longTextVal->isValid($vo->$varName))
						return false;
				}	
				elseif($paramVo->type == 'link') {
					$arr = explode('||||',$vo->$varName);
					
					if(count($arr) != 3) //must contain 3 
						return false;
					
					$boolValidator = self::boolBitValidator();
					//check checkbox field
					if (!$boolValidator->isValid($arr[2]) )
						return false;
					
					$textFieldVal = $arr[0];
					$urlFieldVal = $arr[1];
					
					//if any one is filled check all
					if($textFieldVal != '' || $urlFieldVal != ''){
						if (!$textVal->isValid($textFieldVal) || !$textVal->isValid($urlFieldVal))
							return false;
					}
					
				}
			}
			
			return true;
		}

		/**
		 * Validate search data
		 *
		 * @param Vo_Search $vo            
		 * @return boolean
		 */
		public static function validateSearch(Vo_Search $vo)
		{
			$strVal = new Zend_Validate_StringLength(3, null, 'utf-8');
			// search in all types
			if (! $vo->markerTypeId)
				return $strVal->isValid($vo->title);
			
			$mtm = new Model_MarkerTypeMapper();
			$typeParams = $mtm->fetchParams($vo->markerTypeId);
			
			if (! $typeParams) // no params and were here
				return false;
			
			$allEmpty = true;
			if ($vo->title) {
				if (! $strVal->isValid($vo->title))
					return false;
				
				$allEmpty = false;
			}
			/* @var $paramVo Vo_MarkerTypeParam */
			foreach ($typeParams as $paramVo) {
				$varName = 'param' . $paramVo->number . 'Value';
				if (! $vo->$varName || ! $paramVo->enabled || ! $paramVo->searchable)
					continue;
				
				if ($paramVo->type == 'dictionary') {
					$allEmpty = false;
				} else {
					if (! $strVal->isValid($vo->$varName))
						return false;
					
					$allEmpty = false;
				}
			}
			
			if ($allEmpty)
				return false;
			
			return true;
		}

            /**
             * Validate map details
             *
             * @param Vo_TreeNode $vo            
             * @return boolean
             */
            public static function validateMap(Vo_TreeNode $vo)
            {
                $validators = array(
                    'name' => new Zend_Validate_StringLength(2, 30, 'utf-8'),
                    'enabled' => self::boolBitValidator(),
                    'showLegend' => self::boolBitValidator(),
                    'zoom' => self::zoomValidator()
                );
                
                return self::_validate($validators, $vo);
            }

            /**
             * Validate region position
             *
             * @param Vo_Region $vo            
             * @return boolean
             */
            public static function validateRegionPosition(Vo_Region $vo)
            {
                $validators = array(
                    'zoom' => self::zoomValidator()
                );
                
                return self::_validate($validators, $vo);
            }

            /**
             * Validate settings
             *
             * @param Vo_Settings $vo            
             * @return boolean
             */
            public static function validateSettingsVo(Vo_Settings $vo)
            {
                $validators = array(
                    'panelOpened' => self::boolBitValidator(),
                    'disableViewTab' => self::boolBitValidator()
                );
                
                // check is marker type exist
                if ($vo->defaultMarkerType)
                    $validators['defaultMarkerType'] = new Zend_Validate_Db_RecordExists(
                        array(
                            'table' => Model_MarkerTypeMapper::getTableName(),
                            'field' => 'id'
                        ));
                
                return self::_validate($validators, $vo);
            }

            /**
             * Validate marker tye object
             *
             * @param Vo_MarkerType $vo            
             * @return boolean
             */
            public static function validateMarkerTypeVo(Vo_MarkerType $vo)
            {
                $hexRegVal = new Zend_Validate_Regex('/^#([0-9a-f]{1,2}){3}$/i');
                
                $validators = array(
                    'name' => new Zend_Validate_StringLength(2, 30, 'utf-8'),
                    'cssName' => array(
                        new Zend_Validate_Regex('/^([0-9a-z-]+)$/i'),
                        new Zend_Validate_StringLength(2, 30, 'utf-8')
                    ),
                    'defaultIcon' => array(
                        'allowEmpty' => false
                    ), // only
                                                                      // required
                                                                      // dont
                                                                      // validate
                                                                      // icon
                    'markerColor' => $hexRegVal,
                    'markerHoveredColor' => $hexRegVal,
                    'showOnLegend' => self::boolBitValidator()
                );
                
                if ($vo->id) {
                    $validators['id'] = new Zend_Validate_Db_RecordExists(
                        array(
                            'table' => Model_MarkerTypeMapper::getTableName(),
                            'field' => 'id'
                        ));
                }
                
                return self::_validate($validators, $vo);
            }

            /**
             * Validates that css name is unique accross marker type css names
             *
             * @param string $cssName            
             * @param id $mtId            
             * @return boolean
             */
            public static function markerTypeCssNameUnique($cssName, $mtId)
            {
                $v = new Zend_Validate_Db_NoRecordExists(
                    array(
                        'table' => Model_MarkerTypeMapper::getTableName(),
                        'field' => 'cssName',
                        'exclude' => array(
                            'field' => 'id',
                            'value' => $mtId
                        )
                    ));
                
                return $v->isValid($cssName);
            }

            /**
             * Validate arker type edited data (params)
             *
             * @param unknown_type $data            
             * @return boolean
             */
            public static function validateMarkerTypeEditedData($data)
            {
                if (! $data)
                    return true; // might be empty
                
                if (! is_array($data)) {
                    return false;
                } else {
                    foreach ($data as $value) {
                        if (! self::validateMarkerTypeParamVo(new Vo_MarkerTypeParam($value)))
                            return false;
                    }
                }
                
                return true;
            }

            /**
             * Validate marker type reordered params data
             *
             * @param unknown_type $data            
             * @return boolean
             */
            public static function validateMarkerTypeReorderData($data)
            {
                if (! $data)
                    return true; // might be empty
                
                if (! is_array($data))
                    return false;
                    
                    // validate each object
                foreach ($data as $value) {
                    if (! isset($value['number']) || ! isset($value['position']))
                        return false;
                    
                    $number = (int) $value['number'];
                    if ($number > Model_MarkerTypeMapper::PARAMS_NUM || $number < 1)
                        return false;
                    
                    $position = (int) $value['position'];
                    if ($position > Model_MarkerTypeMapper::PARAMS_NUM - 1 || $position < 0)
                        return false;
                
                }
                return true;
            }

            /**
             * Validate param object
             *
             * @param Vo_MarkerTypeParam $vo            
             * @return boolean
             */
            public static function validateMarkerTypeParamVo(Vo_MarkerTypeParam $vo)
            {
                $validators = array(
                    'number' => new Zend_Validate_Between(array(
                        'min' => 1,
                        'max' => Model_MarkerTypeMapper::PARAMS_NUM
                    )),
                    'type' => new Zend_Validate_InArray(array(
                        'text',
                        'longText',
                        'dictionary',
                        'link'
                    )),
                    'label' => new Zend_Validate_StringLength(2, 30, 'utf-8'),
                    'enabled' => self::boolBitValidator(),
                    'showLabel' => self::boolBitValidator(),
                    'searchable' => self::boolBitValidator(),
                    'alwaysVisible' => self::boolBitValidator()
                );
                
                // if is disabled - skip label validator coz label field might be empty value
                if (! (int) $vo->enabled)
                    unset($validators['label']);
                
                if ($vo->type == 'dictionary') {
                    if (! $vo->typeValue)
                        return false;
                        // validate that dictionary id exists
                    $v = new Zend_Validate_Db_RecordExists(
                        array(
                            'table' => Model_DictionaryMapper::getTableName(),
                            'field' => 'id'
                        ));
                    if (! $v->isValid($vo->typeValue))
                        return false;
                }
                
                if ($vo->type == 'longText') {
                    if ((int) $vo->showLabel !== 1)
                        return false;
                }
                
                return self::_validate($validators, $vo);
            }

            /**
             * Validates dictionary object properties
             *
             * @param Vo_Dictionary $vo            
             * @return boolean
             */
            public static function validateDictionaryVo(Vo_Dictionary $vo)
            {
                $validators = array(
                    'name' => new Zend_Validate_StringLength(2, 60, 'utf-8')
                );
                
                if ($vo->id) {
                    $validators['id'] = new Zend_Validate_Db_RecordExists(
                        array(
                            'table' => Model_DictionaryMapper::getTableName(),
                            'field' => 'id'
                        ));
                }
                
                return self::_validate($validators, $vo);
            }

            /**
             * Used to validate array with all changes in dictionary entries
             * (deletions, addons, editions)
             *
             * @param array $data            
             * @return boolean
             */
            public static function validateDictionaryEntriesData($data)
            {
                if (! $data)
                    return true; // might be empty
                
                if (isset($data['add']) && $data['add']) {
                    if (! self::_validateDictionaryEntriesPart($data['add']))
                        return false;
                }
                if (isset($data['remove']) && $data['remove']) {
                    if (! self::_validateDictionaryEntriesPart($data['remove']))
                        return false;
                }
                if (isset($data['edit']) && $data['edit']) {
                    if (! self::_validateDictionaryEntriesPart($data['edit']))
                        return false;
                }
                
                return true;
            }

            /**
             * Used as loop in validating dictionary entries editions
             *
             * @param
             *            $obj
             * @return boolean
             */
            private static function _validateDictionaryEntriesPart($obj)
            {
                $validator = new Zend_Validate_StringLength(2, 80, 'utf-8');
                
                foreach ($obj as $id => $objVal) {
                    if (! isset($objVal['value']) || ! isset($objVal['id']))
                        return false;
                    
                    if (! $validator->isValid($objVal['value']))
                        return false;
                }
                return true;
            }

            /**
             * This function is used to validate passed data with
             * Zend_Filter_Input class
             *
             * @param array $validators            
             * @param array $vo            
             * @return boolean
             */
            private static function _validate($validators, $vo)
            {
                // create Zend_Filter_Input with validators array and null as
                // filter array
                $input = new Zend_Filter_Input(null, $validators, (array) $vo);
                
                // sets data to validate
                $input->setData((array) $vo);
                
                // options - validator chain terminates after the first
                // validator fails in a rule
                $input->setOptions(
                    array(
                        Zend_Filter_Input::BREAK_CHAIN => true,
                        Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_REQUIRED
                    ));
                
                // return validation result
                if ($input->hasInvalid() || $input->hasMissing())
                    return false;
                else
                    return true;
            }
        }
