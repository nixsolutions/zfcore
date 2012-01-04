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
 * @category   Core
 * @package    Core_Translate
 */
class Core_Translate_ModularAdapter_Array extends Zend_Translate_Adapter_Array
{
    private $_routed = array();

    /**
     * Translates the given string
     * returns the translation
     *
     * @param  string|array       $messageId Translation string, or Array for plural translations
     * @param  string|Zend_Locale $locale    (optional) Locale/Language to use, identical with
     *                                       locale identifier,
     * @param null                $module
     * @see Zend_Locale for more information
     * @return string
     */
    public function translate($messageId, $locale = null, $module = null)
    {
        if (!$module) {
            $module = 'default';
        }
        if ($locale === null) {
            $locale = $this->_options['locale'];
        }

        $plural = null;
        $number = null;
        if (is_array( $messageId )) {
            if (count( $messageId ) > 2) {
                $number = array_pop( $messageId );
                if (!is_numeric( $number )) {
                    $plocale = $number;
                    $number = array_pop( $messageId );
                } else {
                    $plocale = 'en';
                }

                $plural = $messageId;
                $messageId = $messageId[0];
            } else {
                $messageId = $messageId[0];
            }
        }

        if (!Zend_Locale::isLocale( $locale, true, false )) {
            if (!Zend_Locale::isLocale( $locale, false, false )) {
                // language does not exist, return original string
                $this->_log( $messageId, $locale );
                // use rerouting when enabled
                if (!empty($this->_options['route'])) {
                    if (array_key_exists( $locale, $this->_options['route'] ) &&
                        !array_key_exists( $locale, $this->_routed )
                    ) {
                        $this->_routed[$locale] = true;
                        return $this->translate( $messageId, $this->_options['route'][$locale], $module );
                    }
                }

                $this->_routed = array();
                if ($plural === null) {
                    return $messageId;
                }

                $rule = Zend_Translate_Plural::getPlural( $number, $plocale );
                if (!isset($plural[$rule])) {
                    $rule = 0;
                }

                return $plural[$rule];
            }

            $locale = new Zend_Locale($locale);
        }

        $locale = (string)$locale;
        if ((is_string( $messageId ) || is_int( $messageId ))
            && (isset($this->_translate[$locale][$module][$messageId]) || isset($this->_translate[$locale]['default'][$messageId]))
        ) {

            // return original translation
            $result = $this->_getTranslatedMessage( $messageId, $locale, $module, $plural, $number );
            if (false !== $result) {
                return $result;
            }
        } else if (strlen( $locale ) != 2) {
            // faster than creating a new locale and separate the leading part
            $locale = substr( $locale, 0, -strlen( strrchr( $locale, '_' ) ) );

            if ((is_string( $messageId ) || is_int( $messageId ))
                && (isset($this->_translate[$locale][$module][$messageId]) || isset($this->_translate[$locale]['default'][$messageId]))
            ) {
                // return regionless translation (en_US -> en)
                $result = $this->_getTranslatedMessage( $messageId, $locale, $module, $plural, $number );
                if (false !== $result) {
                    return $result;
                }
            }
        }

        $this->_log( $messageId, $locale );
        // use rerouting when enabled
        if (!empty($this->_options['route'])) {
            if (array_key_exists( $locale, $this->_options['route'] ) &&
                !array_key_exists( $locale, $this->_routed )
            ) {
                $this->_routed[$locale] = true;
                return $this->translate( $messageId, $this->_options['route'][$locale], $module );
            }
        }

        $this->_routed = array();
        if ($plural === null) {
            return $messageId;
        }

        $rule = Zend_Translate_Plural::getPlural( $number, $plocale );
        if (!isset($plural[$rule])) {
            $rule = 0;
        }

        return $plural[$rule];
    }

    /**
     * Get translated message
     *
     * @param string  $messageId
     * @param string  $locale
     * @param string  $module
     * @param string  $plural
     * @param integer $number
     * @return string|false
     */
    protected function _getTranslatedMessage($messageId, $locale, $module, $plural, $number)
    {
        if ($plural === null) {
            $this->_routed = array();
            if (isset($this->_translate[$locale][$module][$messageId])) {
                return $this->_translate[$locale][$module][$messageId];
            }
            return $this->_translate[$locale]['default'][$messageId];
        }

        $rule = Zend_Translate_Plural::getPlural( $number, $locale );
        if (isset($this->_translate[$locale][$module][$plural[0]][$rule])) {
            $this->_routed = array();
            return $this->_translate[$locale][$module][$plural[0]][$rule];
        }
        if (isset($this->_translate[$locale]['default'][$plural[0]][$rule])) {
            $this->_routed = array();
            return $this->_translate[$locale]['default'][$plural[0]][$rule];
        }
        return false;
    }
}
