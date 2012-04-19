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
 * Concrete class for generating debug dumps related to the output source.
 *
 * @category   Core
 * @package    Core_Debug
 *
 * @author     Anton Shevchuk <AntonShevchuk@gmail.com>
 * @link       http://anton.shevchuk.name
 */
class Core_Debug extends Zend_Debug
{
    protected static $_time;

    protected static $_memory = 0;

    protected static $_memoryPeak = 0;

    /**
     * @var Zend_Wildfire_Plugin_FirePhp_TableMessage
     */
    protected static $_timer = null;

    protected static $_vars = null;

    /**
     * Stores enabled state of the Debug.
     *
     * @var boolean
     */
    protected static $_enabled = false;

    /**
     * Enable or disable the Debug.  If $enable is false, the Debug
     * is disabled.
     *
     * @param  boolean $enable
     * @return Zend_Db_Profiler Provides a fluent interface
     */
    public static function setEnabled($enable)
    {
        self::$_enabled = (boolean)$enable;

        if (self::$_enabled && (!self::$_timer or !self::$_vars)) {
            self::$_timer = new Zend_Wildfire_Plugin_FirePhp_TableMessage('Timer');
            self::$_timer->setBuffered(true);
            self::$_timer->setHeader(
                array(
                    'Time (sec)',
                    'Total (sec)',
                    'Memory (Kb)',
                    'Total (Kb)',
                    'Comment',
                    'File'
                )
            );
            self::$_timer->setOption('includeLineNumbers', false);
        }
    }

    /**
     * dump
     *
     * @return string
     */
    public static function debug()
    {
        $backtrace = debug_backtrace();

        // get variable name
        $arrLines = file($backtrace[0]["file"]);
        $code = $arrLines[($backtrace[0]["line"] - 1)];
        $arrMatches = array();
        // find call to Core_Debug::debug()
        preg_match('/\b\s*Core_Debug::debug\s*\(\s*(.+)\s*\);\s*/i', $code, $arrMatches);

        $varName = isset($arrMatches[1]) ? $arrMatches[1] : '???';

        $trace = array();

        foreach ($backtrace as $rec) {
            if (isset($rec['function'])) {

                $t['call'] = '';
                if (isset($rec['class'])) {
                    $t['call'] .= $rec['class'] . $rec['type'] . $rec['function'];
                } else {
                    $t['call'] .= $rec['function'];
                }
                $t['call'] .= '(';
                if (sizeof($rec['args'])) {
                    foreach ($rec['args'] as $arg) {
                        if (is_object($arg)) {
                            $t['call'] .= get_class($arg);
                        } else {
                            $arg = str_replace("\n", ' ', (string)$arg);
                            $t['call'] .= '"' . (strlen($arg) <= 30 ? $arg : substr($arg, 0, 25) . '[...]') . '"';
                        }
                        $t['call'] .= ', ';
                    }
                    $t['call'] = substr($t['call'], 0, -2);
                }
                $t['call'] .= ")";
            }
            $t['file'] = @$rec['file'] . ':' . @$rec['line'];
            $trace[] = $t;
        }

        $debug = new Zend_Wildfire_Plugin_FirePhp_TableMessage('Debug');
        $debug->setBuffered(true);
        $debug->setHeader(array('Value and BackTrace'));
        $debug->setOption('includeLineNumbers', false);


        foreach (func_get_args() as $var) {
            $debug->addRow(array($var));
        }
        $debug->addRow(array($trace));

        $where = basename($backtrace[0]["file"]) . ':' . $backtrace[0]["line"];

        $debug->setLabel("Debug: {$varName} ({$where})");
        Zend_Wildfire_Plugin_FirePhp::getInstance()->send($debug);
    }

    /**
     * getGenerateTime
     *
     * @param   string $aComment
     * @return  string time
     */
    public static function getGenerateTime($aComment = "")
    {
        if (!self::$_enabled) return;

        $back = debug_backtrace();

        $_time = microtime(true);

        list ($mTotal, $mSec) = self::getMemoryUsage();

        if (!isset(self::$_time) && defined('START_TIMER')) {
            self::$_time["start"] = START_TIMER;
            self::$_time["section"] = START_TIMER;
        }

        if (!isset(self::$_time)) {
            self::$_time["start"] = $_time;
            self::$_time["section"] = $_time;

            self::$_timer->addRow(
                array(
                    sprintf("%01.4f", 0),
                    sprintf("%01.4f", 0),
                    $mSec, $mTotal,
                    $aComment,
                    basename(@$back[0]["file"]) . ':' . @$back[0]["line"],
                )
            );
        } else {


            $start = self::$_time["section"];
            self::$_time["section"] = $_time;

            self::$_timer->addRow(
                array(
                    sprintf("%01.4f", round(self::$_time["section"] - $start, 4)),
                    sprintf("%01.4f", round(self::$_time["section"] - self::$_time["start"], 4)),
                    $mSec, $mTotal,
                    $aComment,
                    basename(@$back[0]["file"]) . ':' . @$back[0]["line"],
                )
            );
        }

        self::updateMessageLabel();

        Zend_Wildfire_Plugin_FirePhp::getInstance()->send(self::$_timer);
    }

    /**
     * getMemoryUsage
     *
     * @return  array
     */
    protected static function getMemoryUsage()
    {
        if (!function_exists('memory_get_usage')) {
            if (substr(PHP_OS, 0, 3) == 'WIN') {
                $output = array();
                exec('tasklist /FI "PID eq ' . getmypid() . '" /FO LIST', $output);
                $_memory = preg_replace('/[\D]/', '', $output[5]) * 1024;
            } else {
                $pid = getmypid();
                exec("ps -eo%mem,rss,pid | grep $pid", $output);
                $output = explode("  ", $output[0]);
                $_memory = @$output[1] * 1024;
            }
        } else {
            $_memory = memory_get_usage();
        }
        $_memorySection = $_memory - self::$_memory;
        $_memoryTotal = sprintf("%08d", $_memory);
        $_memorySection = sprintf("%08d", $_memorySection);

        self::$_memory = $_memory;

        return array($_memoryTotal, $_memorySection);
    }

    /**
     * get formated memory usage string
     *
     * @return  string
     */
    protected static function getMemoryPeak()
    {
        if (function_exists('memory_get_peak_usage')) {
            self::$_memoryPeak = sprintf("%07s", memory_get_peak_usage());
        }

        return self::$_memoryPeak;
    }

    /**
     * Update the label of the message holding the profile info.
     *
     * @return void
     */
    protected static function updateMessageLabel()
    {
        self::$_timer->setLabel(
            sprintf(
                'Timer (%s sec @  %s Kb)',
                round(self::$_time["section"] - self::$_time["start"], 4),
                number_format(self::$_memory / 1024, 2, '.', ' ')
            )
        );
    }
}
