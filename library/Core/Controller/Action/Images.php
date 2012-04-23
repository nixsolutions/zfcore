<?php
/**
 * Copyright (c) 2012 by PHP Team of NIX Solutions Ltd
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * Core_Controller_Action_Images
 *
 * @uses       Zend_Controller_Action
 * @category   Core
 * @package    Core_Controller
 * @subpackage Action
 */
class Core_Controller_Action_Images extends Core_Controller_Action
{
    protected $_uploadPath = '/uploads';
    protected $_uploadDir  = 'images';
    protected $_thumbDir   = '.thumbs';
    protected $_thumbWidth = 120;
    protected $_thumbHeight = 90;
    protected $_thumbQuality = 100;

    /**
     * return upload dir
     *
     * @return string
     */
    protected function _getUploadDir()
    {
        return $this->_uploadDir;
    }
    /**
     * list all images
     */
    public function listAction()
    {
        $images = glob(PUBLIC_PATH . $this->_uploadPath .DS. $this->_getUploadDir() .DS. '*.*');
        $data = array();
        foreach ($images as $image) {
            $thumb = $this->_createThumb($image);
            $src = pathinfo($image, PATHINFO_BASENAME);
            $data[] = array(
                'image' => $this->_uploadPath .'/'. $this->_getUploadDir() .'/'. $src,
                'thumb' => $thumb
            );
        }

        // Send the JSON response:
        $this->_helper->json($data);
    }

    /**
     * Index
     */
    public function uploadAction()
    {
        // disable layouts for this action:
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost()) {
            try {
                $destination = PUBLIC_PATH . $this->_uploadPath .DS. $this->_getUploadDir();
                $publicPath = $this->_uploadPath .DS. $this->_getUploadDir();

                /* Check destination folder */
                if (!is_dir($destination)) {
                    if (is_writable(PUBLIC_PATH . $this->_uploadPath)) {
                        mkdir($destination, 0777, true);
                    } else {
                        throw new Exception("Uploads directory is not writable");
                    }
                }

                /* Uploading Document File on Server */
                $upload = new Zend_File_Transfer_Adapter_Http();
                try {
                    // upload received file(s)
                    $upload->receive();
                } catch (Zend_File_Transfer_Exception $e) {
                    $e->getMessage();
                }

                // you MUST use following functions for knowing about uploaded file
                // Returns the file name for 'doc_path' named file element
                $filePath = $upload->getFileName('file');

                // Returns the mimetype for the 'file' form element
                $mimeType = $upload->getMimeType('file');

                // mimeType validation
                switch ($mimeType) {
                    case 'image/jpg':
                    case 'image/jpeg':
                    case 'image/pjpeg':
                        $ext = 'jpg';
                        break;
                    case 'image/png':
                        $ext = 'png';
                        break;
                    case 'image/gif':
                        $ext = 'gif';
                        break;
                    default:
                        throw new Exception('Wrong mimetype of uploaded file. Received "'.$mimeType.'"');
                        break;
                }

                // prepare filename
                $name = pathinfo($filePath, PATHINFO_FILENAME);
                $name = strtolower($name);
                $name = preg_replace('/[^a-z0-9_-]/', '-', $name);

                // rename uploaded file
                $renameFile = $name .'.'. $ext;
                $counter = 0;
                while (file_exists($destination .'/'. $renameFile)) {
                    $counter++;
                    $renameFile = $name .'-'. $counter .'.'. $ext;
                }

                $fullFilePath = $destination.'/'.$renameFile;

                // rename uploaded file using Zend Framework
                $filterFileRename = new Zend_Filter_File_Rename(array('target' => $fullFilePath, 'overwrite' => true));

                $filterFileRename -> filter($filePath);

                $this->_helper->viewRenderer->setNoRender(true);

                // create thumb
                $this->_createThumb($fullFilePath);

                echo "<img src='$publicPath/$renameFile' alt='' />";
            } catch (Exception $e) {
                $this->_forwardError($e->getMessage());
            }
        } else {
            $this->_forwardError('Internal Error of Uploads controller');
        }
    }

    /**
     * create thumb for image
     *
     * @param string $file path to original image
     * @return boolean
     */
    protected function _createThumb($file)
    {
        // get orgignal image size
        list($width, $height) = getimagesize($file);

        $tWidth = $this->_thumbWidth;
        $tHeight = $this->_thumbWidth;
        $fullPath = PUBLIC_PATH . $this->_uploadPath .DS. $this->_getUploadDir() .DS. $this->_thumbDir;
        $path = $this->_uploadPath .DS. $this->_getUploadDir() .DS. $this->_thumbDir;

        $name = pathinfo($file, PATHINFO_FILENAME);
        $ext  = pathinfo($file, PATHINFO_EXTENSION);

        $thumb =
                 $name   .'_'.
                 $tWidth  .'x'.
                 $tHeight . '.' .
                 $ext;

        // if already exists - return path to file
        if (is_file($fullPath .DS. $thumb)) {
            return $path .'/'. $thumb;
        }

        // try to create directory for thumbnails
        if (!is_dir($fullPath)) {
            if (is_writable(PUBLIC_PATH . $this->_uploadPath .DS. $this->_getUploadDir())) {
                mkdir($fullPath);
            } else {
                throw new Exception("Uploads directory is not writable");
            }
        }

        if (($width  > $tWidth) or
            ($height > $tHeight)) {

            $tHeight = min(
                $tHeight,
                $height/$width*$tWidth
            );

            $tWidth  = min(
                $tWidth,
                $width/$height*$tHeight
            );

            $tHeight = $height/$width*$tWidth;

            // switch statement for image extension
            switch (strtolower($ext)) {
                case 'jpg':
                case 'jpeg':
                    $oImage = imagecreatefromjpeg($file);
                    break;
                case 'gif':
                    $oImage = imagecreatefromgif($file);
                    break;
                case 'png':
                    $oImage = imagecreatefrompng($file);
                    break;
                default:
                    throw new Exception("Image file has wrong extension");
                    break;
            }

            $oThumb = imagecreatetruecolor($tWidth, $tHeight);

            imagecopyresampled(
                $oThumb, $oImage, 0, 0, 0, 0,
                $tWidth, $tHeight,
                $width, $height
            );

            imagejpeg($oThumb, $fullPath.DS.$thumb, $this->_thumbQuality);

            imagedestroy($oThumb);
            imagedestroy($oImage);
        } else {
            copy($file, $fullPath.DS.$thumb);
        }

        return $path .'/'. $thumb;
    }
}