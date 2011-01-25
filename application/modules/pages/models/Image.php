<?php
/**
 * Model working with files - create thumbs, getlist, delete, upload
 *
 * @category Application
 * @package  Model
 * 
 * @version  $Id: Image.php 58 2010-02-17 14:13:18Z AntonShevchuk $
 */
class Pages_Model_Image
{
    /**
     * Default options of model
     * @var array
     */
    protected $_options = array(
       'maxSize'      => 1048576, // 1Mb
       'uploadDir'    => 'uploads',
       'thumbDir'     => '.thumbs',
       'thumbQuality' => 100,
       'thumbWidth'   => 120,
       'thumbHeight'  => 90,
       'extensions'   => array('jpeg', 'jpg', 'gif', 'png'),
       'cssClasses'   => array('image-1', 'image-2', 'image-3'),
       'thumbSizes'   => array(1 => array('width'  => 160,
                                          'height' => 120),
                               2 => array('width'  => 64,
                                          'height' => 64)),
       'errors'       => array('default'  => 'Error while uploading',
                               'filename' => 'Wrong file name',
                               'filetype' => 'Wrong file type',
                               'size'     => 'File size is too big',
                               'directory'=> 'Wrong directory',
                               'thumb'    => 'Error thumb creation'),
    );
    
    /**
     * Occured errors put here
     *
     * @var array
     */
    protected $_errors = array();
    
    /**
     * Constructor of Image Manager
     * @param $options
     * @return void
     */
    public function __construct($options = array())
    {
        $this->_options = array_merge($this->_options, $options);
    }
    
    /**
     * Create thumb
     *
     * @param string $file
     * @param integer $size
     * @return bool
     */
    public function createThumb($file, $size)
    {
        $file = ltrim($file, '/'); // remove start slash
        
        if (($file = $this->getPath($file)) &&
            isset($this->_options['thumbSizes'][$size])) {
             
            $image = pathinfo($file);
            list($width, $height) = getimagesize($file);
            $image['width']  = $width;
            $image['height'] = $height;
            
            return
                $this->_createViewThumb(
                    $image,
                    $this->_options['thumbSizes'][$size]['width'],
                    $this->_options['thumbSizes'][$size]['height']
                );
        }

        return false;
    }
    
    /**
     * Return safe realpath
     * 
     * @param string $path
     * @return string|false
     */
    public function getPath($path)
    {
        $path     = realpath(APPLICATION_PATH.'/../public/'.$path);
        $safePath = realpath(APPLICATION_PATH.'/../public/'.$this->getDefaultDir());
        
        // not allowed to use hidden directories
        if (preg_match('/\/\./', $path)) {
            return false;
        }
        
        // if path begings with upload path
        if (strpos($path, $safePath) === 0) {
            if (is_file($path)) {
                return $path;
            } else {
                return $path . '/';
            }
        } else {
            return false;
        }
    }
    
    /**
     * Get directory tree
     *
     * @todo watch comments
     * 
     * @param string $path
     * @return array|false
     */
    public function getDirectory($path)
    {
        if (!$realPath = $this->getPath($path)) {
            $path     = $this->getDefaultDir();
            $realPath = $this->getPath($path);
        }
        
        $files       = array();
        $directories = array();

        $dir = scandir($realPath);
        
        // filter
        $dir = array_diff($dir, array('.', '..', '.svn', $this->_options['thumbDir']));
        
        foreach ($dir as $entry) {
            if (is_dir($realPath . $entry)) {
                 $directories[] = array(
                     'path'    => $path .'/'. $entry, 
                     'entry'   => $entry,
                     'isEmpty' => $this->_isEmptyDir($realPath . $entry)
                 );
            } elseif (is_file($realPath . $entry)) {
                $file = pathinfo($realPath . $entry);
               
                if (!in_array($file['extension'], $this->_options['extensions'])) {
                    continue;
                }
                
                list($width, $height) = getImageSize($realPath . $entry);
                
                $file['thumb']     = '/' . $path .
                                     '/' . $this->_options['thumbDir'] . 
                                     '/' . $file['filename'] .
                                     '_' . $this->_options['thumbWidth'] . 
                                     'x' . $this->_options['thumbHeight'] .
                                     '.' . $file['extension'];
                                      
                $file['relative']  = '/'. $path .'/'. $entry;
                $file['width']     = $width;
                $file['height']    = $height;
                
                $files[ $entry ]  = $file;
                
                $this->_createViewThumb($file);
            }
        }
        
        return array($directories, $files);
    }
    
    /**
     * Delete image from directory
     *
     * @param string $path
     * @param string $name
     * @return bool
     */
    public function delete($path, $name) 
    {
        $thumbDir = $this->_options['thumbDir'];

        if ($path = $this->getPath($path)) {
            // check if exists thumb for image
            if (is_dir($path . $thumbDir)) {
                $dir = scandir($path . $thumbDir);
                $f = pathinfo($path . $name);
                $f['filename'] = preg_replace(
                    array(
                        '/(\()/',
                        '/(\))/',
                        '/(\_)/',
                        '/(\-)/'
                    ),
                    '\\\$1',
                    $f['filename']
                );
                foreach ($dir as $entry) {
                    if (preg_match(
                        '/' . $f['filename'] . '\_\d+x\d+\.' . $f['extension'] . '/',
                        $entry,
                        $thumb
                    )) {
                        @unlink($path . $thumbDir . '/' . $thumb['0']);
                    }
                }
            } else {
                throw new Core_Exception($this->_options['errors']['directory']);
            }
            
            if (is_file($path . $name)) {
                return @unlink($path . $name);
            } else {
                throw new Core_Exception($this->_options['errors']['filename']);
            }
        } else {
            throw new Core_Exception($this->_options['errors']['directory']);
        }
        return false;
    }
    
    /**
     * Upload new image
     *
     * @param  string $_sDir
     * @return void
     */
    public function upload($dir)
    {
        if ($path = $this->getPath($dir)) {
            foreach ($_FILES as $file) {
                $this->_receiveFiles($file, $path);
            }
        } else {
            throw new Core_Exception($this->_options['errors']['directory']);
        }
    }
    
    /**
     * Get options for image manager form
     * 
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }
    
    /**
     * Check if errors occured
     *
     * @return bool 
     */
    public function isErrors()
    {
        return in_array(true, $this->_errors);
    }
    
    /**
     * Return relative path to default upload directory
     *
     * @return string
     */
    public function getDefaultDir()
    {
        return $this->_options['uploadDir'];
    }
    
    /**
     * Create previev for image
     *
     * @param array $image
     * @param integer $thumbWidth
     * @param integer $thumbHeight
     * @return bool
     */
    private function _createViewThumb(&$image, $thumbWidth = null, $thumbHeight = null)
    {
        $image['thumb_width']  = $thumbWidth  ? $thumbWidth  : $this->_options['thumbWidth'];
        $image['thumb_height'] = $thumbHeight ? $thumbHeight : $this->_options['thumbHeight'];
        
        $thumb = $image['dirname']            . '/' . 
                 $this->_options['thumbDir']  . '/' . 
                 $image['filename']           . '_' . 
                 $image['thumb_width']        . 'x' . 
                 $image['thumb_height']       . '.' .
                 $image['extension'];
                 
        if (!is_dir($image['dirname'] .'/'. $this->_options['thumbDir'])) {
            mkdir($image['dirname'] .'/'. $this->_options['thumbDir']);
        } elseif (is_file($thumb)) {
            return true;
        }
        
        if (($image['width']  > $image['thumb_width']) or 
            ($image['height'] > $image['thumb_height'])) {

            $image['thumb_height'] = min(
                $image['thumb_height'],
                $image['height']/$image['width']*$image['thumb_width']
            );
                
            $image['thumb_width']  = min(
                $image['thumb_width'],
                $image['width']/$image['height']*$image['thumb_height']
            );
                
            $image['thumb_height'] = $image['height']/$image['width']*$image['thumb_width'];
            
//            $oImage = imagecreatefromgd($image['dirname'] . 
//                                        '/'. 
//                                        $image['basename']);
//            $oImage = imagecreatefromgd2($image['dirname'] . 
//                                         '/'. 
//                                         $image['basename']);

            // switch statement for $image['']
            switch (strtolower($image['extension'])) {
                case 'jpg':
                case 'jpeg':
                    $oImage = imagecreatefromjpeg($image['dirname'] .'/'. $image['basename']);
                    break;
                case 'gif':
                    $oImage = imagecreatefromgif($image['dirname'] .'/'. $image['basename']);
                case 'png':
                    $oImage = imagecreatefrompng($image['dirname'] .'/'. $image['basename']);
                    break;
                default:
                    break;
            }
            
            $oThumb = imagecreatetruecolor($image['thumb_width'], $image['thumb_height']);
            
            imagecopyresampled(
                $oThumb, $oImage, 0, 0, 0, 0,
                $image['thumb_width'], $image['thumb_height'],
                $image['width'], $image['height']
            );
                               
            imagejpeg($oThumb, $thumb, $this->_options['thumbQuality']);
            
            imagedestroy($oThumb);
            imagedestroy($oImage);
        } else {
            copy($image['dirname'] .'/'. $image['basename'], $thumb);
        }
        
        return true;
    }
    
    /**
     * Receive image
     * 
     * @param array $file
     * @param string $path
     * @return void
     */
    private function _receiveFiles($file, $path)
    {
        if (isset($file['size']) && 
            ($file['size'] <= $this->_options['maxSize'])) {
            if (($file["error"] == 0) &&
                ($f = $this->_getFileName($file['name']))) {
                
                 if ((substr($file['type'], 0, 5) == 'image') && 
                      in_array($f['ext'], $this->_options['extensions'])) {

                        $file['name'] = $path . $f['file'] . '.' . $f['ext'];
                        if (is_file($file['name'])) {
                            $counter = 0;
                            do {
                                $file['name'] = $path.$f['file'] . 
                                                '(' . 
                                                ++$counter . 
                                                ').' . 
                                                $f['ext'];
                                                
                            } while (is_file($file['name']));
                        }
                        move_uploaded_file($file['tmp_name'], $file['name']);
                    
                 } else {
                    $this->_errors[] = $this->_options['errors']['filetype'];
                 }
            } else {
                $this->_errors[] = $this->_options['errors']['default'];
            }
        } else {
            $this->_errors[] = $this->_options['errors']['size'];
        }
    }
    
    /**
     * Returns filename and extension
     *
     * @param string $value
     * @return array|false
     */
    private function _getFileName($value)
    {
        if (preg_match('/(.*?)\.(\w+)$/', $value, $file)) {
            
           $file['1'] = preg_replace('/([^a-z0-9\_\-]+|\_{2,})/', '_', strtolower($file['1']));
            
           return array('file' => $file['1'],
                        'ext'  => strtolower($file['2']));
        }
        return false;
    }
    
    /**
     * Check if current dir is empty
     * 
     * @param string $dir
     * @return bool
     */
    private function _isEmptyDir($dir)
    {
        $items = glob($dir . '/[a-z0-9]*.*');
        if ($items['0']) {
           return false;
        }
        return true;
    }
}