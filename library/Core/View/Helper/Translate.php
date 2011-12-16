<?php
/**
 * Core_View_Helper_AForm
 *
 * @author sm
 */
class Core_View_Helper_Translate extends Zend_View_Helper_Translate
{
    /**
     * Translate a message
     * You can give multiple params or an array of params.
     * If you want to output another locale just set it as last single parameter
     * Example 1: translate('%1\$s + %2\$s', $value1, $value2, $locale);
     * Example 2: translate('%1\$s + %2\$s', array($value1, $value2), $locale);
     *
     * @param  string $messageid Id of the message to be translated
     * @param  string $module name of module
     * @return string|Zend_View_Helper_Translate Translated message
     */
    public function translate($messageid = null, $module = null)
    {
        if ($messageid === null) {
            return $this;
        }

        $translate = $this->getTranslator();
        $options   = func_get_args();

        array_shift($options);
        $count  = count($options);
        $locale = null;
        if ($count > 0) {
            if (Zend_Locale::isLocale($options[($count - 1)], null, false) !== false) {
                $locale = array_pop($options);
            }
        }

        if ((count($options) === 1) and (is_array($options[0]) === true)) {
            $options = $options[0];
        }

        if ($translate !== null) {
            $messageid = $translate->translate($messageid, $locale, $module);
        }

        if (count($options) === 0) {
            return $messageid;
        }

        return vsprintf($messageid, $options);
    }
}
