<?php

/**
 * This object represents map, tree node
 * 
 * @author flexphperia
 *
 */
class Vo_TreeNode extends Flexphperia_VoBase
{

    public $id;

    public $name;

    public $enabled;

    public $showLegend;

    public $zoom;

    public $_children = array();

    public $_parent; // parent treenode
    public $_parentId;

    public $_position;

    public $_noMap;

    public function __construct($data = null)
    {
        parent::__construct($data);
        
        // cast to integers, beeter performance than setters
        $this->id = (int) $this->id;
        $this->enabled = (int) $this->enabled;
        $this->showLegend = (int) $this->showLegend;
    }

    /**
     * Prepares object for js tree object
     */
    public function toJStree()
    {
        $this->data = $this->name;
        
        $this->metadata = new stdClass();
        $this->metadata->id = $this->id;
        $this->metadata->enabled = $this->enabled;
        $this->metadata->noMap = $this->_noMap;
        
        $this->attr = new stdClass();
        
        if ($this->metadata->noMap)
            $this->attr->rel = 'noMap';
        
        if (! $this->metadata->enabled)
            $this->attr->class = 'disabled';
        
        if (count($this->_children)) {
            $this->children = $this->_children;
        }
        
        unset($this->id);
        unset($this->name);
        unset($this->enabled);
        unset($this->showLegend);
        unset($this->zoom);
    }

    /**
     * Creates object ready to describe map with parents in array (used in html
     * forms)
     * Recursive function.
     *
     * @param bool $skipParenting
     *            - used intenally in this function
     */
    public function toMapObj($skipParenting = false)
    {
        if (! $skipParenting) {
            $parent = $this->_parent; // parent
            $parents = array();
            
            while ($parent) {
                $parent->toMapObj(true);
                array_unshift($parents, $parent);
                $parent = $parent->_parent; // new parrent, up in tree
            }
            $this->parents = $parents;
        }
        
        unset($this->enabled);
        unset($this->showLegend);
        unset($this->zoom);
    }
}