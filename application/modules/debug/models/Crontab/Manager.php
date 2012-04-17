<?php
/**
 * @category   Application
 * @package    Model
 * @subpackage Crontab
 *
 * @author     Anna Pavlova <pavlova.anna@nixsolutions.com>
 */
class Debug_Model_Crontab_Manager extends Core_Model_Manager
{
    private $_cronCommands = array(
        'read' => 'crontab -l',
        'save' => 'crontab ',
    );

    private $_selectOptionsMonth = array(
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

    private $_cronCols = array(
        'id',
        'minute',
        'hour',
        'dayOfMonth',
        'month',
        'dayOfWeek',
        'command'
    );

    const CRONTAB_FILE = '/tmp/crontab.txt';

    const CRONTAB_FILE_HEAD = 'SHELL=/bin/bash
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
        if ($key = array_search( $data['month'], $this->_selectOptionsMonth )) {
            $data['month'] = $key;
        }

        if ($key = array_search(
            $data['dayOfWeek'],
            $this->_selectOptionsDayOfWeek
        )
        ) {
            $data['dayOfWeek'] = $key;
        }

        return $data;
    }

    /**
     * OpenCrontabFile
     *
     * create array from Crontab File
     *
     * @internal param string $id
     * @return array of each file string
     */
    private function _openCrontabFile()
    {
        $command = $this->_cronCommands['read'];
        exec( $command, $output, $retval );
        if ($retval != 0) {
            return array();
        }
        return $output;
    }

    /**
     * _saveCrontabFile
     *
     * save the crontab file with given strings
     *
     * @param string $crontabStr
     * @internal param string $id
     * @return boolean
     */
    private function _saveCrontabFile($crontabStr = '')
    {
        if (empty($crontabStr)) {
            return false;
        }

        $file = self::CRONTAB_FILE;
        if (!$fp = @fopen( $file, 'w' )) {
            return false;
        }


        $crontabStr = self::CRONTAB_FILE_HEAD . $crontabStr;

        if (fwrite( $fp, $crontabStr ) === FALSE) {
            return false;
        }

        $command = $this->_cronCommands['save'] . $file;
        exec( $command, $output, $retval );

        $this->output = $output;

        if ($retval != 0) {
            return false;
        }

        return true;
    }

    /**
     * Delete
     *
     * @param integer $id
     * @return boolean
     */
    public function delete($id)
    {
        $crontabs = array();
        foreach ($this->createGritArray() as $line) {
            if ($id != $line['id']) {
                unset($line['id']);
                $crontabs[] = join( ' ', $line );
            }
        }

        return $this->_saveCrontabFile( join( PHP_EOL, $crontabs ) );
    }

    /**
     * createGritArray
     *
     * create array for Grit from Crantab file
     *
     * @return array of Crontab data for Grid
     *
     */
    public function createGritArray()
    {
        $crontabs = array();

        foreach ($this->_openCrontabFile() as $i => $line) {
            if (substr( $line, 0, 1 ) != '#') {
                $lineParts = explode( ' ', $line, self::CRONTAB_COLUMN_NUMBER );
                if (count( $lineParts ) == self::CRONTAB_COLUMN_NUMBER) {
                    $lineArr = array(
                        'id'         => ++$i,
                        'minute'     => $lineParts[0],
                        'hour'       => $lineParts[1],
                        'dayOfMonth' => $lineParts[2],
                        'month'      => $lineParts[3],
                        'dayOfWeek'  => $lineParts[4],
                        'command'    => $lineParts[5],
                    );

                    $crontabs[] = $lineArr;
                }
            }
        }
        return $crontabs;
    }

    /**
     * Get line by id
     *
     * @param integer $id
     * @return array
     */
    public function getLineById($id)
    {
        foreach ($this->createGritArray() as $line) {
            if ($id == $line['id']) {
                return $line;
            }
        }
        return array();
    }

    /**
     * Save
     *
     * @param array   $data
     * @param integer $id
     * @return bool
     *
     */
    public function save(array $data, $id = null)
    {
        $data = $this->_changeFormData( $data );

        $crontabs = array();

        foreach ($this->createGritArray() as $line) {
            // edit
            if ($id == $line['id']) {
                $line = $data;
            }

            unset($line['id']);
            $crontabs[] = join( ' ', $line );
        }
        // create
        if (!$id) {
            $crontabs[] = join( ' ', $data );
        }

        return $this->_saveCrontabFile( join( PHP_EOL, $crontabs ) );
    }
}