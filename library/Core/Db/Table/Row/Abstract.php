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
 * Class for SQL table row interface.
 *
 * @category   Core
 * @package    Core_Db
 * @subpackage Table
 *
 * @author     Anton Shevchuk <AntonShevchuk@gmail.com>
 * @link       http://anton.shevchuk.name
 *
 * @version    $Id: Abstract.php 146 2010-07-05 14:22:20Z AntonShevchuk $
 */
class Core_Db_Table_Row_Abstract
    extends Zend_Db_Table_Row_Abstract
{
    /**
     * Constructor
     *
     * @param  array $config OPTIONAL Array of user-specified config options.
     * @return \Core_Db_Table_Row_Abstract
     */
    public function __construct(array $config = array())
    {
        $this->_tableClass = get_class( $this ) . '_Table';
        parent::__construct( $config );
    }

    /**
     * Init Row
     *
     * @return void
     */
    public function init()
    {
        $cols = $this->getTable()->info( Zend_Db_Table::COLS );

        foreach ($cols as $col) {
            if (!isset($this->_data[$col])) {
                $this->_data[$col] = null;
            }
        }
    }

}