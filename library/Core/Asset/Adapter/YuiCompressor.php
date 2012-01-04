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
 * asset yui compressor adapter
 *
 * @category  Core
 * @package   Core_Asset
 * @subpackage Adapter
 */
class Core_Asset_Adapter_YuiCompressor extends Core_Asset_Adapter_Abstract
{
    /**
     * @var string
     */
    protected $_jarPath = null;

    /**
     * build javascript files
     *
     * @param array  $files
     * @param string $destination
     * @return void
     */
    public function buildJavascripts(array $files, $destination)
    {
        foreach ($files as $file) {
            $this->_compress( $file, $destination );
        }
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
        foreach ($files as $file) {
            $this->_compress( $file, $destination );
        }
    }

    /**
     * constructor
     *
     * @param array $options
     */
    public function __construct(array $options = null)
    {
        if (null !== $options) {
            $this->setOptions( $options );
        }
    }

    /**
     * set options
     *
     * @param array $options
     * @return Core_Asset
     */
    public function setOptions(array $options)
    {
        $methods = get_class_methods( $this );
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst( $key );
            if (in_array( $method, $methods )) {
                $this->$method( $value );
            }
        }
        return $this;
    }

    /**
     * set path to jar file
     *
     * @param $path
     * @return void
     */
    public function setJarPath($path)
    {
        $this->_jarPath = realpath( $path );
    }

    /**
     * compress
     *
     * @throws Core_Exception
     * @param string $input
     * @param string $output
     * @return void
     */
    protected function _compress($input, $output)
    {
        if (null === $this->_jarPath) {
            throw new Core_Exception('Path tp jar file is not set');
        }

        if (!is_file( $this->_jarPath )) {
            throw new Core_Exception('"' . $this->_jarPath . '" is not file');
        }

        if (!is_file( $input )) {
            throw new Core_Exception('Input file "' . $input . '" does not exist');
        }

        $cmd = sprintf( 'java -jar %s %s >> %s', $this->_jarPath, $input, $output );
        exec( $cmd );

        if (!is_file( $output )) {
            throw new Core_Exception('Output file "' . $output . '" does not exist');
        }
    }
}
