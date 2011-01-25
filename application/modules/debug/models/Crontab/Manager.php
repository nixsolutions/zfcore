<?php
/**
 * 
 *
 * @category Application
 * @package Model
 * @subpackage Crontab
 *
 * @author Anna Pavlova <pavlova.anna@nixsolutions.com>
 *
 * @version  $Id$
 */
class Debug_Model_Crontab_Manager extends Core_Model_Manager
{
    private $_cronCommands          = array(
                                          'read' => 'crontab -l',
                                          'save' => 'crontab ',
                                      );

    private $_logsNames             = array(
                                          'access_log',
                                          'access.log',
                                          'error_log',
                                      );

    private $_selectOptionsMonth    = array(
                                          '1'  => 'January',
                                          '2'  => 'Fabuary',
                                          '3'  => 'March',
                                          '4'  => 'April',
                                          '5'  => 'May',
                                          '6'  => 'June',
                                          '7'  => 'July',
                                          '8'  => 'August',
                                          '9'  => 'September',
                                          '10' => 'October',
                                          '11' => 'November',
                                          '12' => 'December',
                                       );

    private $_selectOptionsDayOfWeek = array(
                                          '7'  => 'Sunday',
                                          '1'  => 'Monday',
                                          '2'  => 'Tuesday',
                                          '3'  => 'Wednesday',
                                          '4'  => 'Thursday',
                                          '5'  => 'Friday',
                                          '6'  => 'Saturday',
                                       );

    private $_cronCols               = array(
                                           'id',
                                           'minute',
                                           'hour',
                                           'dayOfMonth',
                                           'month',
                                           'dayOfWeek',
                                           'command'
                                       );

    private $_defaultOrderCol = 'command';

    const CRONTAB_FILE        = '/tmp/crontab.txt';

    const CRONTAB_FILE_HEAD   = 'SHELL=/bin/bash
PATH=/sbin:/bin:/usr/sbin:/usr/bin
MAILTO=root
HOME=/
';

    const CRONTAB_COLUMN_NUMBER = 6;

    /**
     * changeFormData
     *
     * change string data of Month and DayOfWeek to intiger eqivalent
     *
     * @param array $data
     * @return array $data
     *
     */
    public function _changeFormData($data = array())
    {
        if (!is_array($data)) {
            return $data;
        }

        if ($key = array_search($data['month'], $this->_selectOptionsMonth)) {
            $data['month'] = $key;
        }

        if ($key = array_search(
            $data['dayOfWeek'],
            $this->_selectOptionsDayOfWeek
        )) {
            $data['dayOfWeek'] = $key;
        }

        return $data;
    }

    /**
     * OpenCrontabFile
     *
     * create array from Crontab File
     *
     * @param string $id
     * @return array of each file string
     *
     */
    private function _openCrontabFile()
    {
        $command = $this->_cronCommands['read'];
        exec($command, $output, $retval);
        if ($retval != 0) {
            return null;
        }
        return $output;
    }

    /**
     * _saveCrontabFile
     *
     * save the crontab file with given strings
     *
     * @param string $id
     * @return null || true
     *
     */
    private function _saveCrontabFile($crontabStr = '')
    {

        if ($crontabStr == '') {
            return null;
        }

        $file = self::CRONTAB_FILE;
        if (!$fp = @fopen($file, 'w')) {
            return null;
        }

        $crontabStr = self::CRONTAB_FILE_HEAD . $crontabStr;

        if (fwrite($fp, $crontabStr) === FALSE) {
            return null;
        }

        $command = $this->_cronCommands['save'] . $file;
        exec($command, $output, $retval);
        if ($retval != 0) {
            return null;
        }

        return true;
    }

    /**
     * delete sesion item
     *
     * @param string $id
     * @return null || true
     *      
     */
    public function deleteCrontabLine($id = null)
    {
        if (is_null($id)) {
            return null;
        }

        $crontabStr = '';
        $lines = $this->createGritArray();
        if (!empty($lines['arr'])) {
            foreach ($lines['arr'] as $line) {
                if ($id != $line['id']) {
                     $crontabStr .= $line['minute']     . ' ' .
                                    $line['hour']       . ' ' .
                                    $line['dayOfMonth'] . ' ' .
                                    $line['month']      . ' ' .
                                    $line['dayOfWeek']  . ' ' .
                                    $line['command']    . '
';
                }
            }
        }

        return $this->_saveCrontabFile($crontabStr);
    }
    /**
     * createGritArray
     *
     * create array for Grit from Crantab file
     * 
     * @return null || array of Crontab data for Grid
     *
     */
    public function createGritArray($start = 0,
                                    $count = 15,
                                    $sort = false,
                                    $field = false,
                                    $filter = false)
    {
        $flagFilter = false;
        $desc        = true;
        $crontabs    = array();
        $tempArr     = array();
        $orderCol    = '';

        $cronLines = $this->_openCrontabFile();
        if (!is_array($cronLines)) {
            return null;
        }

        // sort data
        //   field  - ASC
        //   -field - DESC
        if ($sort
            && ltrim($sort, '-')
            && in_array(ltrim($sort, '-'), $this->_cronCols)
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
            && in_array($field, $this->_cronCols)
            && $filter
            && $filter != '*') {
            $flagFilter = true;
            $filter = str_replace('*', '(.*)', '/'. $filter .'/');
        }

        $i = 1;
        foreach ($cronLines as $line) {
            if (substr($line, 0, 1) != '#') {
                $lineParts = explode(' ', $line);
                if (!empty($lineParts)) {
                    if (count($lineParts) == self::CRONTAB_COLUMN_NUMBER) {
                        $lineArr = array(
                                           'id'         => $i,
                                           'minute'     => $lineParts[0],
                                           'hour'       => $lineParts[1],
                                           'dayOfMonth' => $lineParts[2],
                                           'month'      => $lineParts[3],
                                           'dayOfWeek'  => $lineParts[4],
                                           'command'    => $lineParts[5],
                                     );

                        if ($flagFilter) {
                            if (preg_match($filter, $lineArr[$field])) {
                                if ($orderCol != '') {
                                    $tempArr[$lineArr[$orderCol] .
                                             $lineArr['id']] = $lineArr;
                                } else {
                                    $tempArr[$lineArr[$this->_defaultOrderCol] .
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
                        $i++;
                    }
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
                $crontabs[$key] = $line;
            }
            $i ++;
        }
        unset($tempArr);

        return array('arr' => $crontabs, 'total' => $total);
    }

    /**
     * createCrontabFormArray
     *
     * create array from selected Crontab Line for Edit Form
     *
     * @param string $id
     * @return array
     */
    public function createCrontabFormArray($id = null)
    {
        if (is_null($id)) {
                return null;
        }

        $crontabLineArr = array();
        $lines = $this->createGritArray();
        if (!empty($lines['arr'])) {
            foreach ($lines['arr'] as $line) {
                if ($id == $line['id']) {
                    foreach ($this->_cronCols as $ind) {
                        $crontabLineArr[$ind] = $line[$ind];
                    }
                }
            }
        }
        return $crontabLineArr;
    }

    /**
     * createGritHead
     *
     * create Head for Grit from Crantab file Head
     *
     * @return null || array of Crontab data for Grid Head
     *
     */
    public function createGritHead()
    {
        $heads = array();
        $lines = $this->_openCrontabFile();
        if (!is_array($lines)) {
            return null;
        }

        foreach ($lines as $line) {
            if (!is_numeric(substr($line, 0, 1))) {
                        $heads[] = $line;
            }
        }
        return $heads;
    }

     /**
     * create new line in Crontab File
     *
     * @param array $data
     * @return null || true
     *
     */
    public function createCrontabLine($data = array())
    {
        if (!is_array($data)) {
            return null;
        }

        $crontabStr = '';
        $data = $this->_changeFormData($data);
        $lines = $this->createGritArray();
        if (!empty($lines['arr'])) {
            foreach ($lines['arr'] as $line) {
                 $crontabStr .= $line['minute']     . ' ' .
                                $line['hour']       . ' ' .
                                $line['dayOfMonth'] . ' ' .
                                $line['month']      . ' ' .
                                $line['dayOfWeek']  . ' ' .
                                $line['command']    . '
';
            }
        }
        $crontabStr .=  $data['minute']     . ' ' .
                        $data['hour']       . ' ' .
                        $data['dayOfMonth'] . ' ' .
                        $data['month']      . ' ' .
                        $data['dayOfWeek']  . ' ' .
                        $data['command']    . '
';
        return $this->_saveCrontabFile($crontabStr);
    }

    /**
     * editCrontabLine
     *
     * edit Crontab line by id
     *
     * @param string $id, array $data
     * @return null || true
     *
     */
    public function editCrontabLine($id = null, $data = array())
    {
        if (empty($id) || empty($data)) {
                return null;
        }

        $data = $this->_changeFormData($data);
        $crontabStr = '';
        $lines = $this->createGritArray();
        if (!empty($lines['arr'])) {
            foreach ($lines['arr'] as $line) {
                if ($id == $line['id']) {
                    $crontabStr .=  $data['minute']     . ' ' .
                                    $data['hour']       . ' ' .
                                    $data['dayOfMonth'] . ' ' .
                                    $data['month']      . ' ' .
                                    $data['dayOfWeek']  . ' ' .
                                    $data['command']    . '
';
                } else {
                     $crontabStr .= $line['minute']     . ' ' .
                                    $line['hour']       . ' ' .
                                    $line['dayOfMonth'] . ' ' .
                                    $line['month']      . ' ' .
                                    $line['dayOfWeek']  . ' ' .
                                    $line['command']    . '
';
                }
            }
        }
        
        return $this->_saveCrontabFile($crontabStr);
    }   
}