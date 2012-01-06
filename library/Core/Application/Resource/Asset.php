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
 * Asset Resource
 *
 * @category   Core
 * @package    Core_Application
 * @subpackage Resource
 */
class Core_Application_Resource_Asset extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * @return array
     * @throws Core_Exception
     */
    public function init()
    {
        $options = $this->getOptions();

        if (sizeof($options) == 1 && !current($options)) {
            return false;
        }

        $assets = array();
        foreach ($options['packages'] as $package => $config) {

            /** use parent adapter options if package ones are empty */
            if (empty($config['adapter']) &&
                !empty($options['adapter'])
            ) {
                $config['adapter'] = $options['adapter'];
            }

            /** extend */
            if (!empty($config['extend'])) {
                $config['extend'] = (array)$config['extend'];
                foreach ($config['extend'] as &$extend) {
                    if (empty($assets[$extend])) {
                        throw new Core_Exception('Package "' . $extend . '" not found');
                    }

                    $extend = $assets[$extend];
                }
            }

            $assets[$package] = new Core_Asset($config);
        }

        Zend_Registry::set( 'assets', $assets );
        return $assets;
    }
}
