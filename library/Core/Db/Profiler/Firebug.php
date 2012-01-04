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
 * Writes DB events as log messages to the Firebug Console via FirePHP.
 *
 * @category   Core
 * @package    Core_Db
 * @subpackage Profiler
 *
 * @author     Anton Shevchuk <AntonShevchuk@gmail.com>
 * @link       http://anton.shevchuk.name
 */
class Core_Db_Profiler_Firebug extends Zend_Db_Profiler_Firebug
{
    /**
     * Starts a query.
     *
     * @param string  $queryText
     * @param integer $queryType
     * @return integer|null
     */
    public function queryStart($queryText, $queryType = null)
    {
        $result = parent::queryStart( $queryText, $queryType );

        $backtrace = debug_backtrace();
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
                if (sizeof( $rec['args'] )) {
                    foreach ($rec['args'] as $arg) {
                        if (is_object( $arg )) {
                            $t['call'] .= get_class( $arg );
                        } else {
                            $arg = str_replace( "\n", ' ', (string)$arg );
                            $t['call'] .= '"' . (strlen( $arg ) <= 30 ? $arg : substr( $arg, 0, 25 ) . '[...]') . '"';
                        }
                        $t['call'] .= ', ';
                    }
                    $t['call'] = substr( $t['call'], 0, -2 );
                }
                $t['call'] .= ")";
            }
            $t['file'] = @$rec['file'] . ':' . @$rec['line'];
            $trace[] = $t;
        }
        $this->getLastQueryProfile()->bindParam( 'trace', $trace );

        return $result;
    }
}