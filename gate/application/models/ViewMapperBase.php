<?php

/**
 * @author flexphperia
 *
 */
class Model_ViewMapperBase extends Flexphperia_MapperBase
{

    /**
     * Array of used in fetched map types, stored to generate legend
     *
     * @var array
     */
    protected $_usedTypes;

    /**
     * 1.
     * Fetches types.
     * 2. Types loop to find dictionaries
     * 3. get dictionary entries and assign it into view variable used in
     * subclasses
     *
     * @param array $typesNeeded            
     */
    protected function _findTypesAndDictionaries($typesNeeded)
    {
        $mtm = new Model_MarkerTypeMapper();
        $this->_usedTypes = $mtm->fetchAll(false, $typesNeeded, true); // only
                                                                       // enabled
                                                                       // params
        
        $neededDictionaries = array(); // needed dict ids
        /* @var $typeVo Vo_MarkerType */
        // marker types looop to find dictionaries
        foreach ($this->_usedTypes as $typeVo) {
            if (! $typeVo->params)
                continue;
                /* @var $paramVo Vo_MarkerTypeParam */
            foreach ($typeVo->params as $paramVo) {
                // collect dictionary id
                if ($paramVo->type == 'dictionary')
                    $neededDictionaries[$paramVo->typeValue] = true;
            }
        }
        
        $neededDictionaries = array_keys($neededDictionaries);
        
        // get dictionary entries and assign it into view variable
        if ($neededDictionaries) {
            $dm = new Model_DictionaryMapper();
            $dictionariesEntries = $dm->fetchEntries($neededDictionaries);
            $this->_view->assign('dictEntries', $dictionariesEntries);
        }
    
    }
}