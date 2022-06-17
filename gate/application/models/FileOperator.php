<?php

/**
 * File operator, used for all file operations.
 * 
 * @author flexphperia
 *
 */
class Model_FileOperator extends Model_OperatorAbstract
{

    public $mapsPath;

    public $iconsPath;

    public $imagesPath;

    function __construct()
    {
        parent::__construct();
        
        $this->mapsPath = $this->_config->uploadsPath . DIRECTORY_SEPARATOR . 'maps' . DIRECTORY_SEPARATOR;
        $this->iconsPath = $this->_config->uploadsPath . DIRECTORY_SEPARATOR . 'icons' . DIRECTORY_SEPARATOR;
        $this->imagesPath = $this->_config->uploadsPath . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR;
        $this->cssFilePath = 'data' . DIRECTORY_SEPARATOR . 'generated' . DIRECTORY_SEPARATOR . 'types.css';
    }

    /**
     * Genereates css file with marker type colors.
     * 
     * @throws Exception
     */
    public function generateCss()
    {
        if (! is_writable($this->cssFilePath))
            throw new Exception('types.css file not writable');
            
            // get all types
        $mtm = new Model_MarkerTypeMapper();
        $types = $mtm->fetchAll(false, array(), false, false, true);
        
        if (! $types) {
            file_put_contents($this->cssFilePath, '');
            return;
        }
        
        $this->_view->partialLoop()->setObjectKey('vo');
        $res = $this->_view->partialLoop('cssEntry.phtml', $types);
        
        file_put_contents($this->cssFilePath, $res); // save it
    }

    /**
     * Parses file name to return object with image details
     *
     * @param name $iconName            
     * @return stdClass
     */
    public function getIconObject($iconName)
    {
        $o = new stdClass();
        $o->id = $iconName;
        $o->url = 'uploads/icons/' . $iconName;
        $this->_findSize($o);
        return $o;
    }

    /**
     * Parses file name to return object with image details
     *
     * @param name $iconName            
     * @return stdClass
     */
    public function getImageObject($imageName)
    {
        $o = new stdClass();
        $o->id = $imageName;
        $o->url = 'uploads/images/' . $imageName;
        $this->_findSize($o);
        return $o;
    }

    /**
     * Searches for size in file name
     *
     * @param stdClass $o            
     * @throws Exception
     */
    private function _findSize($o)
    {
        $pattern = '/-[0-9-]+/';
        preg_match($pattern, $o->id, $matches);
        if (! $matches)
            throw new Exception('Wrong image or icon name, can\'t find size');
        $sizes = explode('-', substr($matches[0], 1));
        $o->width = $sizes[0];
        $o->height = $sizes[1];
    }

    /**
     * Get array of icons : {id, url}
     *
     * @return array
     */
    public function getIcons()
    {
        return $this->_getImgFiles('icons');
    }

    /**
     * Get array of images : {id, url}
     *
     * @return array
     */
    public function getImages()
    {
        return $this->_getImgFiles('images');
    }

    /**
     * Delete image file
     *
     * @param string $imageName            
     */
    public function deleteImage($imageName)
    {
        return $this->_deleteFile($imageName, 'images');
    }

    /**
     * Delete icon file
     *
     * @param string $imageName            
     */
    public function deleteIcon($imageName)
    {
        return $this->_deleteFile($imageName, 'icons');
    }

    public function prepareMapFolder($mapId)
    {
        $this->deleteMap($mapId);
        
        @mkdir($this->mapsPath . $mapId);
    }

    /**
     * Search for descriptor.txt in map folder and returns array with two keys
     * (width, height)
     *
     * @param int $mapId            
     * @return boolean array 0 - width, key 1 - height)
     */
    public function getMapSize($mapId)
    {
        $file = @file_get_contents($this->mapsPath . $mapId . DIRECTORY_SEPARATOR . 'descriptor.txt');
        
        if ($file === false)
            return false;
        
        $arr = explode('|', $file);
        
        if (count($arr) < 2)
            return false;
        
        $res = array();
        $res['width'] = (int) $arr[0];
        $res['height'] = (int) $arr[1];
        
        if ($res['width'] < 1 || $res['height'] < 1)
            return false;
        
        return ! $res ? null : $res;
    }

    /**
     * Deletes all map tile images for map and map id folder
     *
     * @param int $mapId            
     * @return boolean
     */
    public function deleteMap($mapId)
    {
        if (! $mapId) // prevents deleting all maps
            return false;
        
        return $this->_rmdirr($this->mapsPath . $mapId);
    }

    /**
     * Delete a file, or a folder and its contents
     *
     * @author Aidan Lister <aidan@php.net>
     * @version 1.0.2
     * @param string $dirname
     *            Directory to delete
     * @return bool Returns TRUE on success, FALSE on failure
     */
    private function _rmdirr($dirname)
    {
        // Sanity check
        if (! file_exists($dirname)) {
            return false;
        }
        
        // Simple delete for a file
        if (is_file($dirname)) {
            return unlink($dirname);
        }
        
        // Loop through the folder
        $dir = dir($dirname);
        while (false !== $entry = $dir->read()) {
            // Skip pointers
            if ($entry == '.' || $entry == '..')
                continue;
                // Recurse
            $this->_rmdirr("$dirname/$entry");
        }
        
        // Clean up
        $dir->close();
        
        return rmdir($dirname);
    }

    /**
     * Deletes file
     *
     * @param string $fileName            
     * @param string $type            
     */
    private function _deleteFile($fileName, $type)
    {
        if (! $type || strlen($fileName) <= 4)
            return;
        
        $path = $type == 'icons' ? $this->iconsPath : $this->imagesPath;
        
        return @unlink(realpath($path . $fileName));
    }

    /**
     * Returns list of image files
     *
     * @param string $type            
     * @return array
     */
    private function _getImgFiles($type)
    {
        $path = $type == 'icons' ? $this->iconsPath : $this->imagesPath;
        // get all image files with a .jpg extension.
        $images = glob($path . "*.{png,jpg,gif}", GLOB_BRACE);
        
        if ($images) {
            sort($images);
            $len = count($images);
            for ($i = 0; $i < $len; $i ++) {
                $img = substr($images[$i], strrpos($images[$i], DIRECTORY_SEPARATOR) + 1);
                $images[$i] = $type == 'icons' ? $this->getIconObject($img) : $this->getImageObject($img);
            }
        }
        return $images;
    }
}