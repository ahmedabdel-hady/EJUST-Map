<?php

/**
 * View helper used to create map breadcrumb
 * 
 * @author flexphperia
 *
 */
class Helper_MapBreadcrumb extends Zend_View_Helper_Abstract
{

    protected $_divider = '<span class="divider"><i class="icon-chevron-right"></i></span>';

    /**
     * Generates breadcrumb for map
     *
     * @param Vo_TreeNode $treeNode            
     * @param bool $addMapId
     *            add into ul data-id attribute, target map id or not
     * @param Vo_Region $regionVo            
     * @return string
     */
    public function mapBreadcrumb(Vo_TreeNode $treeNode, $addMapId = false, $linkable = false, $regionVo = false)
    {
        $parent = $treeNode;
        
        $parentNodes = array();
        
        while ($parent) {
            array_unshift($parentNodes, $parent);
            $parent = $parent->_parent;
        }
        
        $s = '<ul class="breadcrumb" ' . ($addMapId ? 'data-id="' . $treeNode->id . '"' : '') . '>';
        $len = count($parentNodes);
        for ($i = 0; $i < $len; $i ++) {
            $node = $parentNodes[$i];
            $s .= '<li>' . ($linkable ? '<a href="#"' . $this->_linkDataAttr($node) . '>' : '') .
                 $this->view->escape($node->name) . ($linkable ? '</a>' : '') .
                 ($i < $len - 1 || $regionVo ? $this->_divider : '') . '</li>';
        }
        
        if ($regionVo) {
            $regionLink = new stdClass();
            $regionLink->mapId = $regionVo->mapId;
            $regionLink->regionId = $regionVo->id;
            $s .= '<li>' .
                 ($linkable ? '<a href="#" data-cfm-region-link=\'' . Zend_Json::encode($regionLink) . '\' >' : '') .
                 $this->view->escape($regionVo->name) . ($linkable ? '</a>' : '') . '</li>';
        }
        
        return $s . '</ul>';
    }

    private function _linkDataAttr(Vo_TreeNode $node)
    {
        return 'data-cfm-map-link="' . $node->id . '"';
    }
}
