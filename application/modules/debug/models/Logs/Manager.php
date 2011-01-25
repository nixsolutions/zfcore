<?php
/**
 * Debug_Model_Logs_Manager
 *
 * @category Application
 * @package Model
 * @subpackage Option
 *
 * @author Anna Pavlova <pavlova.anna@nixsolutions.com>
 *
 * @version  $Id$
 */
class Debug_Model_Logs_Manager extends Core_Model_Manager
{
    private $_logsDir   = array (
                                '0' => '/var/log/apache',
                                '1' => '/var/log/apache2',
                                '2' => '/var/log/httpd',
                          );

    private $_logsNames = array (
                                'access_log',
                                'access.log',
                                'error_log',
                          );

    private $_logsCols  = array('id', 'name', 'path', 'size');

    private $_defaultOrderCol = 'name';

    const FIELD_DELIMITER = '@';

    const DIR_DELIMITER = '/';

    /**
     * create array from Logs Files in system
     *
     * @return array of logs in system
     *
     */
    public function createLogsArray($start = 0,
                                    $count = 15,
                                    $sort = false,
                                    $field = false,
                                    $filter = false)
    {
        $flagFilter = false;
        $desc        = true;
        $logs        = array();
        $tempArr     = array();
        $orderCol    = '';

        // sort data
        //   field  - ASC
        //   -field - DESC
        if ($sort
            && ltrim($sort, '-')
            && in_array(ltrim($sort, '-'), $this->_logsCols)
            ) {
            if (strpos($sort, '-') === 0) {
                $orderCol = ltrim($sort, '-');
            } else {
                $orderCol = $sort;
                $desc = false;
            }
        }

        // Use  filter
        if ($field
            && in_array($field, $this->_logsCols)
            && $filter
            && $filter != '*') {
            $flagFilter = true;
            $filter = str_replace('*', '(.*)', '/'. $filter .'/');
        }


        foreach ($this->_logsDir as $dind => $dir) {
            if (is_dir($dir)) {
                if ($dh = @opendir($dir)) {
                    while (($file = readdir($dh)) !== false) {
                        foreach ($this->_logsNames as $name) {
                            if (!(strpos($file, $name) === false)) {
                                $lineArr =
                                    array(
                                        'id'   => $dind .
                                                  self::FIELD_DELIMITER .
                                                  $file,
                                        'name' => $file,
                                        'path' => $dir,
                                        'size' => filesize(
                                            $dir .
                                            self::DIR_DELIMITER .
                                            $file
                                        ),
                                    );

                            if ($flagFilter) {
                                if (preg_match($filter, $lineArr[$field])) {
                                    if ($orderCol != '') {
                                        $tempArr[$lineArr[$orderCol] .
                                                 $lineArr['id']] = $lineArr;
                                    } else {
                                        $tempArr[$lineArr[$this->
                                                          _defaultOrderCol] .
                                                  $lineArr['id']] = $lineArr;
                                    }
                                }
                            } else {
                                if ($orderCol != '') {
                                    $tempArr[$lineArr[$orderCol] .
                                             $lineArr['id']] = $lineArr;
                                } else {
                                    $tempArr[$lineArr[$this->_defaultOrderCol] .
                                             $lineArr['id']] = $lineArr;
                                }
                            }

                            unset($lineArr);
                            }
                        }
                    }
                    closedir($dh);
                }
            }
        }

        if ($desc) {
            ksort($tempArr);
        } else {
            krsort($tempArr);
        }

        $total = count($tempArr);
        $i = 0;
        foreach ($tempArr as $key => $line) {
            if ($i >= $start && $i < $start+$count) {
                $logs[$key] = $line;
            }
            $i ++;
        }
        unset($tempArr);

        return array('arr' => $logs, 'total' => $total);
    }

    /**
     * create array from File Data
     *
     * @param string $id, int $num
     * @return array of each file string
     *
     */
    public function createFileArray($id, $num)
    {
        if (!is_int($num)) {
            return null;
        }
        
        $idArray = explode(self::FIELD_DELIMITER, $id);
        if (empty($idArray[0]) || empty ($idArray[1])) {
            return null;
        }
        $dirKey  = $idArray[0];
        $file = $idArray[1];
        $dirs = $this->_logsDir;

        $fileArray = gzfile($dirs[$dirKey] . self::DIR_DELIMITER . $file);
        if (empty($fileArray)) {
            return null;
        }
        
        end($fileArray);
        $fileArrayCut[] = current($fileArray);

        $i = 1;
        while ($i < $num) {
            $fileArrayCut[] = prev($fileArray);
            $i ++;
        }

        return array('arr' => $fileArrayCut, 'name' => $file);
    }

    /**
     * create array from File Data for ajax add action
     *
     * @param string $id, string $beginLogString, int $numUp, $numDown,
     *        string $direction
     * @return array of each file string
     *
     */
    public function createAddFileArray($id, $beginLogString, $length, $direction)
    {
        if (!is_numeric($length)) {
            return null;
        }

        $idArray = explode(self::FIELD_DELIMITER, $id);
        if (empty($idArray[0]) || empty ($idArray[0])) {
            return null;
        }
        $dirKey  = $idArray[0];
        $file = $idArray[1];
        $dirs = $this->_logsDir;

        $fileArray = gzfile($dirs[$dirKey] . self::DIR_DELIMITER . $file);
        $fileArray = array_reverse($fileArray);
        $key = array_search($beginLogString . "\n", $fileArray);
        $addNumber = Debug_LogsController::ADD_NUMBER;
        if ($direction == 'end') {
            $begin = $key + $length;
            $end =   $begin + $addNumber;
            $length = $addNumber;
            if (!array_key_exists($end, $fileArray)) {
                $length = count($fileArray) - $begin;
            }
            return array_slice($fileArray, $begin, $length);
        } elseif ($direction == 'begin') {
            $begin = $key - $length;
            $length = $addNumber;
            if (!array_key_exists($begin, $fileArray)) {
                $length = $addNumber + $begin;
                $begin = 0;
            }
            return array_reverse(array_slice($fileArray, $begin, $length));
        } else {
            return null;
        }
    }
}