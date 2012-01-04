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
 * asset simple adapter
 *
 * @category  Core
 * @package   Core_Asset
 * @subpackage Adapter
 */
class Core_Asset_Adapter_Simple extends Core_Asset_Adapter_Abstract
{
    /**
     * build javascript files
     *
     * @param array  $files
     * @param string $destination
     * @return void
     */
    public function buildJavascripts(array $files, $destination)
    {
        $this->_combine( $files, $destination );
    }

    /**
     * build stylesheet files
     *
     * @param array  $files
     * @param string $destination
     * @return void
     */
    public function buildStylesheets(array $files, $destination)
    {
        $this->_combine( $files, $destination );
    }
}
