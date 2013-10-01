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
 * Class for SQL table rowset interface.
 *
 * @category   Core
 * @package    Core_Db
 * @subpackage Table
 *
 * @author     Oleksandr Vladymyrov <vladymyrov@nixsolutions.com>
 *
 * @version    $Id$
 */
class Core_Db_Table_Rowset_Abstract extends Zend_Db_Table_Rowset_Abstract
{

    /**
     * Setup to do on wakeup.
     * A de-serialized Rowset should not be assumed to have access to a live
     * database connection, so set _connected = false.
     *
     * @return void
     */
    public function __wakeup()
    {
        $this->_connected = false;
        $this->_table = $this->_getTableFromString($this->_tableClass);
    }

    /**
     * _getTableFromString
     *
     * @param string $tableName
     * @return Core_Db_Table_Abstract
     */
    protected function _getTableFromString($tableName)
    {
        return Core_Db_Table_Abstract::getTableFromString($tableName, $this->_table);
    }

}
