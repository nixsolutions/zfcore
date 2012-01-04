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
 * @category Core
 * @package  Core_Config
 * @subpackage Writer
 */
class Core_Config_Writer_Yaml extends Zend_Config_Writer_FileAbstract
{
    /**
     * What to call when we need to decode some YAML?
     *
     * @var callable
     */
    protected $_yamlEncoder = array(__CLASS__, 'encode');

    /**
     * Get callback for decoding YAML
     *
     * @return callable
     */
    public function getYamlEncoder()
    {
        return $this->_yamlEncoder;
    }

    /**
     * Set callback for decoding YAML
     *
     * @param  callable $yamlEncoder the decoder to set
     * @return Zend_Config_Yaml
     */
    public function setYamlEncoder($yamlEncoder)
    {
        if (!is_callable( $yamlEncoder )) {
            require_once 'Zend/Config/Exception.php';
            throw new Zend_Config_Exception('Invalid parameter to setYamlEncoder - must be callable');
        }

        $this->_yamlEncoder = $yamlEncoder;
        return $this;
    }

    /**
     * Render a Zend_Config into a YAML config string.
     *
     * @since 1.10
     * @return string
     */
    public function render()
    {
        $data = $this->_config->toArray();
        $sectionName = $this->_config->getSectionName();
        $extends = $this->_config->getExtends();

        if (is_string( $sectionName )) {
            $data = array($sectionName => $data);
        }

        foreach ($extends as $section => $parentSection) {
            $data[$section][Zend_Config_Yaml::EXTENDS_NAME] = $parentSection;
        }

        // Ensure that each "extends" section actually exists
        foreach ($data as $section => $sectionData) {
            if (is_array( $sectionData ) && isset($sectionData[Zend_Config_Yaml::EXTENDS_NAME])) {
                $sectionExtends = $sectionData[Zend_Config_Yaml::EXTENDS_NAME];
                if (!isset($data[$sectionExtends])) {
                    // Remove "extends" declaration if section does not exist
                    unset($data[$section][Zend_Config_Yaml::EXTENDS_NAME]);
                }
            }
        }

        return call_user_func( $this->getYamlEncoder(), $data );
    }

    /**
     * Very dumb YAML encoder
     *
     * Until we have Zend_Yaml...
     *
     * @param array $data YAML data
     * @return string
     */
    public static function encode($data)
    {
        return self::_encodeYaml( 0, $data );
    }

    /**
     * Service function for encoding YAML
     *
     * @param int   $indent Current indent level
     * @param array $data   Data to encode
     * @return string
     */
    protected static function _encodeYaml($indent, $data)
    {
        reset( $data );

        //make _extends be the first child
        if (isset($data[Zend_Config_Yaml::EXTENDS_NAME])) {
            $extends = $data[Zend_Config_Yaml::EXTENDS_NAME];
            unset($data[Zend_Config_Yaml::EXTENDS_NAME]);
            $data = array(Zend_Config_Yaml::EXTENDS_NAME => $extends) + $data;
        }

        $result = "";
        $numeric = is_numeric( key( $data ) );

        foreach ($data as $key => $value) {
            if (is_array( $value )) {
                $encoded = "\n" . self::_encodeYaml( $indent + 1, $value );
            } else {
                if (is_bool( $value )) {
                    $value = ($value) ? 'on' : 'off';
                }
                $encoded = (string)$value . "\n";

                if ($encoded) {
                    $encoded = " " . $encoded;
                }
            }

            $result .= str_repeat( "  ", $indent )
                . ($numeric ? "-" : "$key:")
                . $encoded;
        }
        return $result;
    }
}