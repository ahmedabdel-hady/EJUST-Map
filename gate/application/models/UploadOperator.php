<?php

/**
 * Handles all uploads
 * 
 * @author flexphperia
 *
 */
class Model_UploadOperator extends Model_OperatorAbstract
{

    const IMAGES_MAX_WIDTH = 128;

    const IMAGES_MAX_HEIGHT = 128;

    const MAP_IMAGE_MIN_SIZE = 512;

    public $inputName = 'qqfile';

    /**
     * Stores uploded and saved image name
     * 
     * @var string
     */
    protected $uploadName;

    /**
     *
     * @var array
     */
    protected $_uploadedSize;

    /**
     * Process the upload.
     * 
     * @param string $uploadDirectory
     *            Target directory.
     * @param string $name
     *            Overwrites the name of the file.
     */
    public function handleUpload($uploadDirectory, $isMapImage = false)
    {
        // catch all fatal errors
        register_shutdown_function(array(
            $this,
            'fatalErrorHandler'
        ));
        
        if (! is_writable($uploadDirectory)) {
            return array(
                'error' => "Server error. Uploads directory isn't writable or executable."
            );
        }
        
        if (! isset($_SERVER['CONTENT_TYPE'])) {
            return array(
                'error' => "No files were uploaded."
            );
        } else 
            if (strpos(strtolower($_SERVER['CONTENT_TYPE']), 'multipart/') !== 0) {
                return array(
                    'error' => "Server error. Not a multipart request. Please set forceMultipart to default value (true)."
                );
            }
        
        if (! isset($_FILES[$this->inputName]))
            return array(
                'error' => "Server error. Increase post_max_size and upload_max_filesize."
            );
            
            // Get size and name
        $file = $_FILES[$this->inputName];
        $size = $file['size'];
        
        $name = $this->_getName();
        
        // Validate name
        if ($name === null || $name === '') {
            return array(
                'error' => 'File name empty.'
            );
        }
        
        // Validate file size
        if ($size == 0) {
            return array(
                'error' => 'File is empty. Check PHP upload limits.'
            );
        }
        
        $return = array();
        
        try {
            if (! $isMapImage) {
                // resizing and validation check that file is correct image
                $thumb = new Flexphperia_GdThumb($file['tmp_name'], array(
                    'jpegQuality' => 95
                ));
                $this->_uploadedSize = $thumb->getCurrentDimensions();
                // resize image
                if ($this->_uploadedSize['width'] > self::IMAGES_MAX_WIDTH ||
                     $this->_uploadedSize['height'] > self::IMAGES_MAX_HEIGHT) {
                        $thumb->resize(self::IMAGES_MAX_WIDTH, self::IMAGES_MAX_HEIGHT);
                        $this->_uploadedSize = $thumb->getCurrentDimensions();
                        $thumb->save($file['tmp_name']);
                    }
                } else {
                    $mapCreator = @new Flexphperia_OzDeepzoomImageCreator($file['tmp_name'], $uploadDirectory);
                    
                    if ($mapCreator->imageWidth < self::MAP_IMAGE_MIN_SIZE ||
                         $mapCreator->imageHeight < self::MAP_IMAGE_MIN_SIZE)
                            return array(
                                'error' => sprintf(
                                    'Map image must be min. %s x %s size. Previous map image was deleted.', 
                                    self::MAP_IMAGE_MIN_SIZE, self::MAP_IMAGE_MIN_SIZE)
                            );
                        
                        @$mapCreator->create();
                        
                        $return['success'] = true;
                        return $return;
                    }
                } catch (Exception $e) {
                    // something failed, maybe image is not image in reality
                    return $this->_logError($e->getMessage());
                }
                
                $target = $this->_getUniqueTargetPath($uploadDirectory, $name);
                
                if ($target) {
                    $this->uploadName = basename($target);
                    if (move_uploaded_file($file['tmp_name'], $target)) {
                        $imgSize = $thumb->getCurrentDimensions(); // get new
                                                                   // dimensions
                                                                   // after
                                                                   // resizing
                        
                        $return['success'] = true;
                        $return['uploadName'] = $this->uploadName; // name of
                                                                   // saved file
                                                                   // in folder
                        return $return;
                    }
                }
                
                return array(
                    'error' => 'Could not save uploaded file.' . 'The upload was cancelled, or server error encountered'
                );
            }

            /**
             * Returns a path to use with this upload.
             * Check that the name does not exist,
             * and appends a suffix otherwise.
             * 
             * @param string $uploadDirectory
             *            Target directory
             * @param string $filename
             *            The name of the file to use.
             */
            protected function _getUniqueTargetPath($uploadDirectory, $filename)
            {
                // Allow only one process at the time to get a unique file name,
                // otherwise
                // if multiple people would upload a file with the same name at
                // the same time
                // only the latest would be saved.
                
                if (function_exists('sem_acquire')) {
                    $lock = sem_get(ftok(__FILE__, 'u'));
                    sem_acquire($lock);
                }
                
                $pathinfo = pathinfo($filename);
                $base = $pathinfo['filename'];
                
                $base = preg_replace('/[^a-zA-Z0-9_]/', "_", $base); // remove
                                                                     // all not
                                                                     // allowed
                                                                     // characters
                
                $ext = strtolower(isset($pathinfo['extension']) ? $pathinfo['extension'] : '');
                $ext = $ext == '' ? $ext : '.' . $ext;
                
                // store size in file
                $unique = $base;
                $suffix = 0;
                $sizes = '-' . $this->_uploadedSize['width'] . '-' . $this->_uploadedSize['height'];
                
                // Get unique file name for the file, by appending random
                // suffix.
                while (file_exists($uploadDirectory . DIRECTORY_SEPARATOR . $unique . $sizes . $ext)) {
                    $suffix += rand(1, 999);
                    $unique = $base . '_' . $suffix;
                }
                
                // store sizes in file name
                $result = $uploadDirectory . $unique . $sizes . $ext;
                
                // Create an empty target file
                if (! touch($result)) {
                    // Failed
                    $result = false;
                }
                
                if (function_exists('sem_acquire')) {
                    sem_release($lock);
                }
                
                return $result;
            }

            /**
             * Log error into logger
             *
             * @param string $message            
             * @return string
             */
            protected function _logError($message)
            {
                // something failed, maybe image is not image in reality
                $registry = Zend_Registry::getInstance();
                $registry->bootstrap->log->err('File upload error: ' . $message);
                return array(
                    'error' => 'Something wrong with file. Check application.log file.'
                );
            }

            /**
             * Get the original filename
             */
            protected function _getName()
            {
                if (isset($_REQUEST['qqfilename']))
                    return $_REQUEST['qqfilename'];
                
                if (isset($_FILES[$this->inputName]))
                    return $_FILES[$this->inputName]['name'];
            }

            /**
             * Fatal error handler.
             * Called when fatal error willl be thrown while image uploading.
             * Maybe insufficient memory or something
             */
            public function fatalErrorHandler()
            {
                $error = error_get_last();
                if (($error['type'] === E_ERROR) || ($error['type'] === E_USER_ERROR))
                    echo json_encode(array(
                        'error' => $error['message']
                    )); // simple
                                                                           // php
                                                                           // native
                                                                           // json
                                                                           // response
            
            }
        }