<?php
/**
 * Debug_Model_File_Manager
 *
 * @category Application
 * @package Model
 * @subpackage File
 *
 * @author Anna Pavlova <pavlova.anna@nixsolutions.com>
 *
 * @version  $Id$
 */
class Debug_Model_File_Manager extends Core_Model_Manager
{
    const FIELD_DELIMITER = '@';

    const DIR_DELIMITER = '/';

    /**
     * _detectMimeType
     *           
     * detect the mime type of a file
     *
     * @param  string $file file path
     * @return string Mimetype of given file
     */
    protected function _detectMimeType($file)
    {
        if (!file_exists($file)) {
            return null;
        }

        if (class_exists('finfo', false)) {
            $const = defined('FILEINFO_MIME_TYPE') ?
                FILEINFO_MIME_TYPE : FILEINFO_MIME;
            $mime = @finfo_open($const);
            if (!empty($mime)) {
                $result = finfo_file($mime, $file);
            }

            unset($mime);
        }

        if (empty($result) && (function_exists('mime_content_type')
            && ini_get('mime_magic.magicfile'))) {
            $result = mime_content_type($file);
        }

        if (empty($result)) {
            $result = 'application/octet-stream';
        }

        return $result;
    }

    /**
     *  _getFileNameFromPath
     *
     * get file name from full path
     *
     * @param string $path
     * @return string
     */
    private function _getFileNameFromPath($path)
    {
            $fileArray = explode(self::FIELD_DELIMITER, $path);
            if (is_array($fileArray)) {
                $count = count($fileArray)-1;
                return $fileArray[$count];
            } else {
                return null;
            }
    }

    /**
     *  _checkBranchChildren
     *         
     * check for childern in Branch
     *
     * @param string $root
     * @return true | false
     */
    private function _checkBranchChildren($root)
    {
        $i = 0;
        if (is_dir($root)) {
            if ($dh = @opendir($root)) {
                while (($file = readdir($dh)) !== false) {
                    if ( $file != '.svn' && $file != '.' && $file != '..' ) {
                        $i++;
                    }
                }
                closedir($dh);
            }
        }
        return ($i > 0) ? true : false;
    }

    /**
     *  _getBranchChildren
     *
     * get array of childern in Branch
     *
     * @param string $root
     * @return array
     */
    private function _getBranchChildren($root)
    {
        $items = array();
        $nods  = array();
        if (is_dir($root)) {
            if ($dh = @opendir($root)) {
                while (($file = readdir($dh)) !== false) {
                    if ($file != '.svn' && $file != '.' && $file != '..') {
                        $path     = $root . self::DIR_DELIMITER . $file;
                        $filetype = filetype($path);
                        $items[$filetype . $file] =
                            array(
                                'id'    => str_replace(
                                    self::DIR_DELIMITER,
                                    self::FIELD_DELIMITER,
                                    $path
                                ),
                                'label' => $file,
                                'path'  => $path,
                                'type'  => $filetype,
                                '$ref'  => str_replace(
                                    self::DIR_DELIMITER,
                                    self::FIELD_DELIMITER,
                                    $path
                                ),
                            );
                    }
                }
                closedir($dh);
                ksort($items);
                $i = 0;
                foreach ($items as $item) {
                    $nods[$i] = $item;
                    if ($this->_checkBranchChildren($item['path'])) {
                        $nods[$i]['children'] = true;
                    } elseif ($item["type"] == 'dir') {
                        $nods[$i]["children"] = true;
                    }
                    $i ++;
                }
            }
        }
        return $nods;
    }

    /**
     * create lazy tree array for File System
     *
     * @param string $root
     * @return array
     */
    public function createLazyTreeArray($root = null)
    {
        $nods  = array();
        if ($root === null) {
            $root =  APPLICATION_PATH . '/..'  ;
            $nods = $this->_getBranchChildren($root);
        } else {
            $fileName = $this->_getFileNameFromPath($root);
            $path = str_replace(
                self::FIELD_DELIMITER,
                self::DIR_DELIMITER,
                $root
            );
            if (is_dir($path)) {
                $nods = array(
                            'id' =>    $root,
                            'label' => $fileName,
                            'path' =>  $path,
                            'type' =>  filetype($path),
                        );
                $nods['children'] = $this->_getBranchChildren($nods['path']);
            }
        }
        return $nods;
    }

    /**
     * createFileArray
     *         
     * create string with File Data
     *
     * @return string
     *
     */
    public function createFileArray($id = null)
    {
        $fileInfoArray = array();
        $fileInfoArray["name"] = $this->_getFileNameFromPath($id);
        $file = str_replace(self::FIELD_DELIMITER, self::DIR_DELIMITER, $id);
        if (!file_exists($file)) {
            return null;
        }

        $pathArray = pathinfo($file);        
        $mime = $this->_detectMimeType($file);

        $handle = fopen($file, "r");
        $content = fread($handle, filesize($file));
        
        $fileInfoArray["mime"]    = $mime;
        $fileInfoArray["path"]    = $pathArray['dirname'];
        $fileInfoArray["content"] = $content;
        return $fileInfoArray;
    }
}