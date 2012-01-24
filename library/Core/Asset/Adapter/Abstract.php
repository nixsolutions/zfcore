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
 * asset abstract adapter
 *
 * @category  Core
 * @package   Core_Asset
 * @subpackage Adapter
 */
abstract class Core_Asset_Adapter_Abstract
{
    /**
     * build javascript files
     *
     * @abstract
     * @param array  $files
     * @param string $destination
     * @return void
     */
    abstract public function buildJavascripts(array $files, $destination);

    /**
     * build stylesheet files
     *
     * @abstract
     * @param array  $files
     * @param string $destination
     * @return void
     */
    abstract public function buildStylesheets(array $files, $destination);

    /**
     * combine files
     *
     * @param array  $files
     * @param string $destination
     * @return void
     */
    protected function _combine(array $files, $destination)
    {
        $content = '';
        foreach ($files as $file) {
            if (is_file($file)) {
                $content .= file_get_contents($file) . "\n";
            }
        }
        file_put_contents($destination, $content);
    }
}
